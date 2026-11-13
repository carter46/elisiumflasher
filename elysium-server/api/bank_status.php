<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();

// Used when returning default bank_name for unknown bank_code.
$bankNames = [
    '033' => 'UBA',
    '011' => 'First Bank',
    '044' => 'Access Bank',
    '070' => 'Fidelity Bank',
    '058' => 'Guaranty Trust Bank',
    '030' => 'Heritage Bank',
    '301' => 'Jaiz Bank',
    '082' => 'Keystone Bank',
    '232' => 'Sterling Bank',
    '032' => 'Union Bank',
    '215' => 'Unity Bank',
    '035' => 'Wema Bank',
    '057' => 'Zenith Bank',
    '50211' => 'Kuda Bank',
    '50515' => 'Moniepoint',
    '999992' => 'OPay',
    '100033' => 'PalmPay',
];

$allowedStatuses = ['full_logs', 'weak_logs', 'pending_request', 'post_no_debit', 'fixed_account'];

switch ($method) {
    case 'GET':
        try {
            $bankCode = isset($_GET['bank_code']) ? trim((string) $_GET['bank_code']) : '';

            if ($bankCode !== '') {
                $stmt = $pdo->prepare('SELECT bank_code, bank_name, status, updated_at FROM bank_status WHERE bank_code = ? LIMIT 1');
                $stmt->execute([$bankCode]);
                $row = $stmt->fetch();

                if (!$row) {
                    $defaultName = $bankNames[$bankCode] ?? 'Unknown Bank';
                    $ins = $pdo->prepare('INSERT INTO bank_status (bank_code, bank_name, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE bank_name = VALUES(bank_name), status = VALUES(status)');
                    $ins->execute([$bankCode, $defaultName, 'full_logs']);
                    $stmt->execute([$bankCode]);
                    $row = $stmt->fetch();
                }

                json_response(['success' => true, 'bank_status' => $row]);
            }

            $stmt = $pdo->query('SELECT bank_code, bank_name, status, updated_at FROM bank_status ORDER BY bank_name');
            $statuses = $stmt->fetchAll();
            json_response(['success' => true, 'bank_statuses' => $statuses]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch bank status'], 500);
        }
        break;

    case 'PUT':
        validate_admin_session();

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);

        $statuses = $input['statuses'] ?? null;
        if (!is_array($statuses)) {
            // Allow single-object payload: { bank_code, status }
            if (isset($input['bank_code']) && isset($input['status'])) {
                $statuses = [[
                    'bank_code' => (string) $input['bank_code'],
                    'status' => (string) $input['status'],
                ]];
            } else {
                json_response(['success' => false, 'message' => 'Invalid input: expected `statuses[]` or `{bank_code,status}`'], 422);
            }
        }

        $pdo->beginTransaction();
        try {
            $upsert = $pdo->prepare('
                INSERT INTO bank_status (bank_code, bank_name, status)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    bank_name = VALUES(bank_name),
                    status = VALUES(status),
                    updated_at = CURRENT_TIMESTAMP
            ');

            foreach ($statuses as $s) {
                if (!is_array($s)) {
                    continue;
                }
                $bankCode = trim((string) ($s['bank_code'] ?? ''));
                $status = (string) ($s['status'] ?? '');
                if ($bankCode === '' || !in_array($status, $allowedStatuses, true)) {
                    continue;
                }
                $bankName = $bankNames[$bankCode] ?? (string) ($s['bank_name'] ?? 'Unknown Bank');
                $upsert->execute([$bankCode, $bankName, $status]);
            }

            $stmt = $pdo->query('SELECT bank_code, bank_name, status, updated_at FROM bank_status ORDER BY bank_name');
            $updated = $stmt->fetchAll();
            $pdo->commit();

            json_response(['success' => true, 'bank_statuses' => $updated]);
        } catch (Throwable $e) {
            $pdo->rollBack();
            json_response(['success' => false, 'message' => 'Failed to update bank statuses'], 500);
        }
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

