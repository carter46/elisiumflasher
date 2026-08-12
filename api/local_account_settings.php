<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();

switch ($method) {
    case 'GET':
        validate_admin_session();

        try {
            $stmt = $pdo->query('SELECT account_name, account_number, balance FROM local_dashboard_profile ORDER BY id DESC LIMIT 1');
            $row = $stmt->fetch();
            if (!$row) {
                json_response(['success' => false, 'message' => 'Local account profile not found'], 404);
            }
            json_response(['success' => true, 'account' => $row]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch local account settings'], 500);
        }
        break;

    case 'PUT':
        validate_admin_session();

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);

        $updates = [];
        $params = [];

        if (array_key_exists('account_name', $input)) {
            $name = trim((string) ($input['account_name'] ?? ''));
            if ($name === '') {
                json_response(['success' => false, 'message' => 'account_name cannot be empty'], 422);
            }
            $updates[] = 'account_name = ?';
            $params[] = $name;
        }

        if (array_key_exists('account_number', $input)) {
            $acct = trim((string) ($input['account_number'] ?? ''));
            if ($acct === '') {
                json_response(['success' => false, 'message' => 'account_number cannot be empty'], 422);
            }
            $updates[] = 'account_number = ?';
            $params[] = $acct;
        }

        if (array_key_exists('balance', $input)) {
            $balance = $input['balance'];
            $balanceNum = is_numeric($balance) ? (float) $balance : null;
            if ($balanceNum === null || $balanceNum < 0) {
                json_response(['success' => false, 'message' => 'balance must be a valid non-negative number'], 422);
            }
            $updates[] = 'balance = ?';
            $params[] = $balanceNum;
        }

        if (!$updates) {
            json_response(['success' => false, 'message' => 'No valid fields to update'], 422);
        }

        try {
            $pdo->beginTransaction();

            $idStmt = $pdo->query('SELECT id FROM local_dashboard_profile ORDER BY id DESC LIMIT 1');
            $idRow = $idStmt->fetch();
            if (!$idRow) {
                $pdo->rollBack();
                json_response(['success' => false, 'message' => 'Local account profile not found'], 404);
            }
            $id = (int) $idRow['id'];

            $sql = 'UPDATE local_dashboard_profile SET ' . implode(', ', $updates) . ' WHERE id = ?';
            $params[] = $id;
            $upd = $pdo->prepare($sql);
            $upd->execute($params);

            $stmt = $pdo->prepare('SELECT account_name, account_number, balance FROM local_dashboard_profile WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $row = $stmt->fetch();

            $pdo->commit();
            json_response(['success' => true, 'account' => $row]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_response(['success' => false, 'message' => 'Failed to update local account settings'], 500);
        }
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

