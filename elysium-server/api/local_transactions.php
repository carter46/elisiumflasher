<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/tracker_status.php';
require_once __DIR__ . '/../includes/tracker_uetr.php';
require_once __DIR__ . '/../includes/tracker_store.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();
tracker_server_ensure_schema($pdo);

$allowedStatuses = ['SUCCESSFUL', 'FAILED', 'PENDING'];

switch ($method) {
    case 'GET':
        // Allow both admin and regular user to fetch transaction history
        start_app_session();
        $isAdmin = is_admin_logged_in();
        $isUser = is_logged_in();
        
        if (!$isAdmin && !$isUser) {
            json_response([
                'success' => false,
                'message' => 'Session expired',
                'redirect' => '/',
            ], 401);
        }
        
        // Update last activity for the appropriate session
        if ($isUser) {
            $_SESSION['last_activity'] = time();
        }
        if ($isAdmin) {
            $_SESSION['admin_last_activity'] = time();
        }

        if (isset($_GET['id']) && (int) $_GET['id'] > 0) {
            try {
                $stmt = $pdo->prepare('SELECT * FROM local_transactions WHERE id = ? LIMIT 1');
                $stmt->execute([(int) $_GET['id']]);
                $row = $stmt->fetch();
                if (!$row) {
                    json_response(['success' => false, 'message' => 'Transaction not found'], 404);
                }
                json_response(['success' => true, 'transaction' => $row]);
            } catch (Throwable $e) {
                json_response(['success' => false, 'message' => 'Failed to fetch transaction'], 500);
            }
        }

        if (isset($_GET['limit'])) {
            $limit = max(1, min(200, (int) $_GET['limit']));
        } else {
            $limit = 50;
        }
        $offset = isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : 0;

        try {
            $stmt = $pdo->query('SELECT COUNT(*) AS total FROM local_transactions');
            $total = $stmt->fetch()['total'] ?? 0;

            $stmt = $pdo->prepare('
                SELECT *
                FROM local_transactions
                ORDER BY transaction_date DESC
                LIMIT ? OFFSET ?
            ');
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();

            $transactions = $stmt->fetchAll();
            json_response([
                'success' => true,
                'transactions' => $transactions,
                'total' => (int) $total,
                'limit' => $limit,
                'offset' => $offset,
            ]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch transactions'], 500);
        }
        break;

    case 'POST':
        // Client authenticated transaction creation.
        require_login();

        $contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '');
        if (str_starts_with(strtolower($contentType), 'application/json') || $contentType === '') {
            $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        } else {
            $input = [];
        }

        $bankCode = trim((string) ($input['bank_code'] ?? ''));
        $bankName = trim((string) ($input['bank_name'] ?? ''));
        $beneficiaryAccount = preg_replace('/\D/', '', (string) ($input['beneficiary_account'] ?? ''));
        $beneficiaryName = trim((string) ($input['beneficiary_name'] ?? ''));
        $amount = $input['amount'] ?? null;
        $remark = trim((string) ($input['remark'] ?? ''));
        $currency = trim((string) ($input['currency'] ?? 'NGN'));

        if ($bankCode === '' || $bankName === '' || $beneficiaryAccount === '' || $beneficiaryAccount === '' || $beneficiaryName === '' || $amount === null) {
            json_response(['success' => false, 'message' => 'Missing required transaction fields'], 422);
        }

        $amountNum = is_numeric($amount) ? (float) $amount : null;
        if ($amountNum === null || $amountNum <= 0) {
            json_response(['success' => false, 'message' => 'Invalid amount'], 422);
        }

        if ($currency === '') {
            $currency = 'NGN';
        }

        // Optional: caller can provide reference, otherwise we generate one.
        $reference = trim((string) ($input['reference'] ?? ''));
        if ($reference === '') {
            $reference = (string) (1000000000 + random_int(0, 8999999999));
        }

        // Idempotency: if reference already exists, return existing transaction (no extra balance deduction).
        try {
            $check = $pdo->prepare('SELECT * FROM local_transactions WHERE reference = ? LIMIT 1');
            $check->execute([$reference]);
            $existing = $check->fetch();
            if ($existing) {
                json_response(['success' => true, 'transaction' => $existing, 'message' => 'Transaction already exists']);
            }
        } catch (Throwable $e) {
            // If the idempotency check fails, fall back to normal flow below.
        }

        try {
            $pdo->beginTransaction();

            // Check platform status (on/off)
            $stmt = $pdo->query("SELECT status FROM platform_status WHERE id = 1 LIMIT 1");
            $platform = $stmt->fetch();
            $platformStatus = $platform ? (string) $platform['status'] : 'on';

            if ($platformStatus !== 'on') {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Platform is currently under maintenance'], 409);
            }

            // Check per-bank status
            $stmt = $pdo->prepare('SELECT status FROM bank_status WHERE bank_code = ? LIMIT 1');
            $stmt->execute([$bankCode]);
            $bsRow = $stmt->fetch();
            $bankStatus = $bsRow ? (string) $bsRow['status'] : 'full_logs';

            if ($bankStatus !== 'full_logs') {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Bank status does not allow debit', 'bank_status' => $bankStatus], 409);
            }

            // Fetch sender account settings (latest)
            $acctStmt = $pdo->query('SELECT id, account_name, account_number, balance FROM local_dashboard_profile ORDER BY id DESC LIMIT 1');
            $acct = $acctStmt->fetch();
            if (!$acct) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Local account settings not found'], 404);
            }

            $accountId = (int) $acct['id'];
            $senderName = (string) $acct['account_name'];
            $senderAccount = (string) $acct['account_number'];
            $currentBalance = (float) $acct['balance'];

            if ($currentBalance < $amountNum) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Insufficient balance'], 409);
            }

            $trackerDefault = tracker_status_normalize(
                strtoupper((string) (tracker_app_setting_get($pdo, 'local_tracker_status', 'PDNG') ?? 'PDNG')),
                'PDNG'
            );
            $uetr = tracker_uetr_generate_unique($pdo, 'local_transactions', 'uetr');

            // Insert transaction
            $ins = $pdo->prepare('
                INSERT INTO local_transactions (
                    reference, uetr, amount, currency, beneficiary_name, beneficiary_bank,
                    beneficiary_account, sender_account, sender_name, purpose, status,
                    tracker_status, tracker_status_updated_at, direction, transaction_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())
            ');
            $ins->execute([
                $reference,
                $uetr,
                $amountNum,
                $currency,
                $beneficiaryName,
                $bankName,
                $beneficiaryAccount,
                $senderAccount,
                $senderName,
                $remark !== '' ? $remark : null,
                'SUCCESSFUL',
                $trackerDefault,
                'debit',
            ]);

            // Deduct balance atomically (only if the row still matches the latest id)
            $upd = $pdo->prepare('
                UPDATE local_dashboard_profile
                SET balance = balance - ?, updated_at = NOW()
                WHERE id = ? AND balance >= ?
            ');
            $upd->execute([$amountNum, $accountId, $amountNum]);

            if ($upd->rowCount() !== 1) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Balance update failed (concurrency or insufficient funds)'], 409);
            }

            $txId = (int) $pdo->lastInsertId();
            $txStmt = $pdo->prepare('SELECT * FROM local_transactions WHERE id = ? LIMIT 1');
            $txStmt->execute([$txId]);
            $tx = $txStmt->fetch();

            $pdo->commit();
            json_response([
                'success' => true,
                'transaction' => $tx,
                'updated_balance' => null, // optional to compute later
            ]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_response(['success' => false, 'message' => 'Failed to create transaction'], 500);
        }
        break;

    case 'DELETE':
        // Admin-only deletion and balance restore.
        validate_admin_session();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            json_response(['success' => false, 'message' => 'Transaction ID is required'], 422);
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT amount FROM local_transactions WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $tx = $stmt->fetch();
            if (!$tx) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            $del = $pdo->prepare('DELETE FROM local_transactions WHERE id = ?');
            $del->execute([$id]);

            $accountRow = $pdo->query('SELECT id FROM local_dashboard_profile ORDER BY id DESC LIMIT 1')->fetch();
            if (!$accountRow) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Local account settings not found'], 404);
            }
            $accountId = (int) $accountRow['id'];

            $upd = $pdo->prepare('
                UPDATE local_dashboard_profile
                SET balance = balance + ?, updated_at = NOW()
                WHERE id = ?
            ');
            $upd->execute([(float) $tx['amount'], $accountId]);

            $pdo->commit();
            json_response(['success' => true, 'message' => 'Transaction deleted successfully']);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_response(['success' => false, 'message' => 'Failed to delete transaction'], 500);
        }
        break;

    case 'PUT':
        validate_admin_session();
        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        if (!is_array($input)) {
            json_response(['success' => false, 'message' => 'Invalid JSON body'], 400);
        }
        $id = isset($input['id']) ? (int) $input['id'] : 0;
        $hasReceipt = array_key_exists('status', $input) && trim((string) $input['status']) !== '';
        $hasTracker = array_key_exists('tracker_status', $input) && trim((string) $input['tracker_status']) !== '';
        if ($id <= 0) {
            json_response(['success' => false, 'message' => 'Transaction ID is required'], 422);
        }
        if (!$hasReceipt && !$hasTracker) {
            json_response(['success' => false, 'message' => 'No status fields provided'], 422);
        }

        $stmt = $pdo->prepare('SELECT * FROM local_transactions WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $tx = $stmt->fetch();
        if (!$tx) {
            json_response(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        $tracker = null;
        $newStatus = null;
        if ($hasTracker) {
            $tracker = tracker_status_normalize((string) $input['tracker_status']);
            if (!in_array($tracker, tracker_status_codes(), true)) {
                json_response(['success' => false, 'message' => 'Invalid tracker status'], 422);
            }
        }
        if ($hasReceipt) {
            $newStatus = strtoupper(trim((string) $input['status']));
            if (!in_array($newStatus, $allowedStatuses, true)) {
                json_response(['success' => false, 'message' => 'Invalid status'], 422);
            }
        }

        try {
            $pdo->beginTransaction();
            if ($hasTracker && $tracker !== null) {
                $pdo->prepare('UPDATE local_transactions SET tracker_status = ?, tracker_status_updated_at = NOW() WHERE id = ?')
                    ->execute([$tracker, $id]);
            }
            if ($hasReceipt && $newStatus !== null) {
                $pdo->prepare('UPDATE local_transactions SET status = ? WHERE id = ?')->execute([$newStatus, $id]);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_response(['success' => false, 'message' => 'Failed to update transaction'], 500);
        }

        $txStmt = $pdo->prepare('SELECT * FROM local_transactions WHERE id = ? LIMIT 1');
        $txStmt->execute([$id]);
        json_response([
            'success' => true,
            'transaction' => $txStmt->fetch(),
            'message' => $hasTracker && !$hasReceipt ? 'Tracker status updated' : 'Transaction status updated',
        ]);

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

