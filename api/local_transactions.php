<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/mobile_fcm.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();

$allowedStatuses = ['SUCCESSFUL', 'FAILED', 'PENDING', 'REVERSED', 'COMPLETED'];

function local_setting_value(PDO $pdo, string $key, string $default): string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row || $row['setting_value'] === null || trim((string) $row['setting_value']) === '') {
        return strtolower($default);
    }
    return strtolower(trim((string) $row['setting_value']));
}

/** Statuses that hold/deduct sender balance. */
function local_tx_is_debit_status(string $status): bool
{
    $s = strtoupper(trim($status));
    return in_array($s, ['SUCCESSFUL', 'PENDING', 'COMPLETED'], true);
}

function local_transactions_has_bank_code_column(PDO $pdo): bool
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    try {
        $pdo->query('SELECT beneficiary_bank_code FROM local_transactions LIMIT 0');
        $cached = true;
    } catch (Throwable $e) {
        $cached = false;
    }
    return $cached;
}

function local_profile_latest(PDO $pdo): ?array
{
    $row = $pdo->query('SELECT id, account_name, account_number, balance FROM local_dashboard_profile ORDER BY id DESC LIMIT 1')->fetch();
    return $row ?: null;
}

function local_debit_balance(PDO $pdo, int $accountId, float $amount): bool
{
    $upd = $pdo->prepare('
        UPDATE local_dashboard_profile
        SET balance = balance - ?, updated_at = NOW()
        WHERE id = ? AND balance >= ?
    ');
    $upd->execute([$amount, $accountId, $amount]);
    return $upd->rowCount() === 1;
}

function local_credit_balance(PDO $pdo, int $accountId, float $amount): void
{
    $upd = $pdo->prepare('
        UPDATE local_dashboard_profile
        SET balance = balance + ?, updated_at = NOW()
        WHERE id = ?
    ');
    $upd->execute([$amount, $accountId]);
}

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
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
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

        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
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

            // Global local transfer outcome (applies to any bank)
            $transferMode = local_setting_value($pdo, 'local_transfer_status', 'successful');
            $statusMap = [
                'successful' => 'SUCCESSFUL',
                'failed' => 'FAILED',
                'pending' => 'PENDING',
                'reversed' => 'REVERSED',
                'completed' => 'COMPLETED',
            ];
            $txStatus = $statusMap[$transferMode] ?? 'SUCCESSFUL';

            // Fetch sender account settings (latest)
            $acct = local_profile_latest($pdo);
            if (!$acct) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Local account settings not found'], 404);
            }

            $accountId = (int) $acct['id'];
            $senderName = (string) $acct['account_name'];
            $senderAccount = (string) $acct['account_number'];
            $currentBalance = (float) $acct['balance'];

            // Debit for SUCCESSFUL / PENDING / COMPLETED; not for FAILED / REVERSED
            $shouldDebit = local_tx_is_debit_status($txStatus);
            if ($shouldDebit && $currentBalance < $amountNum) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Insufficient balance'], 409);
            }

            // Insert transaction (persist NUBAN bank_code when column exists)
            if (local_transactions_has_bank_code_column($pdo)) {
                $ins = $pdo->prepare('
                    INSERT INTO local_transactions (
                        reference, amount, currency, beneficiary_name, beneficiary_bank,
                        beneficiary_bank_code, beneficiary_account, sender_account, sender_name, purpose, status,
                        direction, transaction_date
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ');
                $ins->execute([
                    $reference,
                    $amountNum,
                    $currency,
                    $beneficiaryName,
                    $bankName,
                    $bankCode !== '' ? $bankCode : null,
                    $beneficiaryAccount,
                    $senderAccount,
                    $senderName,
                    $remark !== '' ? $remark : null,
                    $txStatus,
                    'debit',
                ]);
            } else {
                $ins = $pdo->prepare('
                    INSERT INTO local_transactions (
                        reference, amount, currency, beneficiary_name, beneficiary_bank,
                        beneficiary_account, sender_account, sender_name, purpose, status,
                        direction, transaction_date
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ');
                $ins->execute([
                    $reference,
                    $amountNum,
                    $currency,
                    $beneficiaryName,
                    $bankName,
                    $beneficiaryAccount,
                    $senderAccount,
                    $senderName,
                    $remark !== '' ? $remark : null,
                    $txStatus,
                    'debit',
                ]);
            }

            if ($shouldDebit) {
                if (!local_debit_balance($pdo, $accountId, $amountNum)) {
                    $pdo->rollBack();
                    json_response(['success' => false, 'message' => 'Balance update failed (concurrency or insufficient funds)'], 409);
                }
            }

            $txId = (int) $pdo->lastInsertId();
            $txStmt = $pdo->prepare('SELECT * FROM local_transactions WHERE id = ? LIMIT 1');
            $txStmt->execute([$txId]);
            $tx = $txStmt->fetch();

            $pdo->commit();

            // Best-effort FCM for finalized mobile-visible credits only.
            if (is_array($tx)) {
                $st = strtoupper((string) ($tx['status'] ?? ''));
                if ($st === 'SUCCESSFUL' || $st === 'COMPLETED') {
                    try {
                        mobile_fcm_notify_successful_transfer($pdo, $tx);
                    } catch (Throwable $notifyErr) {
                        // Intentionally ignore — transfer already committed.
                    }
                }
            }

            json_response([
                'success' => true,
                'transaction' => $tx,
                'transfer_status' => $transferMode,
                'updated_balance' => null,
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

            $stmt = $pdo->prepare('SELECT amount, status FROM local_transactions WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $tx = $stmt->fetch();
            if (!$tx) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            $del = $pdo->prepare('DELETE FROM local_transactions WHERE id = ?');
            $del->execute([$id]);

            // Restore balance for any debit status (SUCCESSFUL / PENDING / COMPLETED)
            $restored = false;
            if (local_tx_is_debit_status((string) ($tx['status'] ?? ''))) {
                $accountRow = local_profile_latest($pdo);
                if (!$accountRow) {
                    $pdo->rollBack();
                    json_response(['success' => false, 'message' => 'Local account settings not found'], 404);
                }
                local_credit_balance($pdo, (int) $accountRow['id'], (float) $tx['amount']);
                $restored = true;
            }

            $pdo->commit();
            json_response([
                'success' => true,
                'message' => $restored
                    ? 'Transaction deleted successfully. Balance restored.'
                    : 'Transaction deleted successfully.',
                'balance_restored' => $restored,
            ]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_response(['success' => false, 'message' => 'Failed to delete transaction'], 500);
        }
        break;

    case 'PUT':
        // Admin-only: edit per-transaction status and adjust balance on debit transitions.
        validate_admin_session();

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        if (!is_array($input)) {
            json_response(['success' => false, 'message' => 'Invalid JSON body'], 400);
        }

        $id = isset($input['id']) ? (int) $input['id'] : (isset($_GET['id']) ? (int) $_GET['id'] : 0);
        $newStatus = strtoupper(trim((string) ($input['status'] ?? '')));
        if ($id <= 0) {
            json_response(['success' => false, 'message' => 'Transaction ID is required'], 422);
        }
        if (!in_array($newStatus, $allowedStatuses, true)) {
            json_response(['success' => false, 'message' => 'Invalid status'], 422);
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT * FROM local_transactions WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $tx = $stmt->fetch();
            if (!$tx) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            $oldStatus = strtoupper((string) ($tx['status'] ?? ''));
            if ($oldStatus === $newStatus) {
                $pdo->commit();
                json_response(['success' => true, 'transaction' => $tx, 'message' => 'Status unchanged']);
            }

            $wasDebit = local_tx_is_debit_status($oldStatus);
            $willDebit = local_tx_is_debit_status($newStatus);
            $amountNum = (float) ($tx['amount'] ?? 0);

            $acct = local_profile_latest($pdo);
            if (!$acct) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Local account settings not found'], 404);
            }
            $accountId = (int) $acct['id'];

            if ($wasDebit && !$willDebit) {
                local_credit_balance($pdo, $accountId, $amountNum);
            } elseif (!$wasDebit && $willDebit) {
                if (!local_debit_balance($pdo, $accountId, $amountNum)) {
                    $pdo->rollBack();
                    json_response(['success' => false, 'message' => 'Insufficient balance to apply this status'], 409);
                }
            }

            $upd = $pdo->prepare('UPDATE local_transactions SET status = ? WHERE id = ?');
            $upd->execute([$newStatus, $id]);

            $txStmt = $pdo->prepare('SELECT * FROM local_transactions WHERE id = ? LIMIT 1');
            $txStmt->execute([$id]);
            $updated = $txStmt->fetch();

            $pdo->commit();

            if (is_array($updated) && ($newStatus === 'SUCCESSFUL' || $newStatus === 'COMPLETED') && $oldStatus !== $newStatus) {
                try {
                    mobile_fcm_notify_successful_transfer($pdo, $updated);
                } catch (Throwable $notifyErr) {
                    // ignore
                }
            }

            json_response([
                'success' => true,
                'transaction' => $updated,
                'message' => 'Transaction status updated',
            ]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_response(['success' => false, 'message' => 'Failed to update transaction status'], 500);
        }
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

