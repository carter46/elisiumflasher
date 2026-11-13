<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/tracker_status.php';
require_once __DIR__ . '/../includes/tracker_store.php';

validate_admin_session();

$pdo = get_db();
tracker_server_ensure_schema($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $limit = isset($_GET['limit']) ? max(1, min(200, (int) $_GET['limit'])) : 50;
    $offset = isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : 0;
    $stmt = $pdo->prepare('
        SELECT id, reference, uetr, beneficiary_iban, beneficiary_name, swift_code, bank_name,
               amount, currency, message_type, delivery_date, status, tracker_status,
               tracker_status_updated_at, created_at
        FROM international_transactions
        ORDER BY id DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    json_response([
        'success' => true,
        'transactions' => $rows,
        'limit' => $limit,
        'offset' => $offset,
        'tracker_status_options' => array_values(tracker_status_catalog()),
    ]);
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    if (!is_array($input)) {
        json_response(['success' => false, 'message' => 'Invalid JSON'], 400);
    }
    $id = isset($input['id']) ? (int) $input['id'] : 0;
    $hasReceipt = array_key_exists('status', $input) && trim((string) $input['status']) !== '';
    $hasTracker = array_key_exists('tracker_status', $input) && trim((string) $input['tracker_status']) !== '';
    $allowed = ['SUCCESSFUL', 'PENDING', 'PROCESSING', 'FAILED', 'REVERSED', 'NETWORK_ERROR'];
    if ($id <= 0) {
        json_response(['success' => false, 'message' => 'Invalid id'], 422);
    }
    if (!$hasReceipt && !$hasTracker) {
        json_response(['success' => false, 'message' => 'No status fields provided'], 422);
    }

    $tracker = null;
    $status = null;
    if ($hasTracker) {
        $tracker = tracker_status_normalize((string) $input['tracker_status']);
        if (!in_array($tracker, tracker_status_codes(), true)) {
            json_response(['success' => false, 'message' => 'Invalid tracker status'], 422);
        }
    }
    if ($hasReceipt) {
        $status = strtoupper(trim((string) $input['status']));
        if (!in_array($status, $allowed, true)) {
            json_response(['success' => false, 'message' => 'Invalid status'], 422);
        }
    }

    try {
        $pdo->beginTransaction();
        if ($hasTracker && $tracker !== null) {
            $pdo->prepare('UPDATE international_transactions SET tracker_status = ?, tracker_status_updated_at = NOW() WHERE id = ?')
                ->execute([$tracker, $id]);
        }
        if ($hasReceipt && $status !== null) {
            $pdo->prepare('UPDATE international_transactions SET status = ? WHERE id = ?')->execute([$status, $id]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        json_response(['success' => false, 'message' => 'Failed to update transaction'], 500);
    }

    $row = $pdo->prepare('SELECT id, reference, uetr, status, tracker_status FROM international_transactions WHERE id = ? LIMIT 1');
    $row->execute([$id]);
    $updated = $row->fetch();
    if (!$updated) {
        json_response(['success' => false, 'message' => 'Not found'], 404);
    }
    json_response(['success' => true, 'transaction' => $updated]);
}

if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        json_response(['success' => false, 'message' => 'Invalid id'], 422);
    }
    $stmt = $pdo->prepare('DELETE FROM international_transactions WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        json_response(['success' => false, 'message' => 'Not found'], 404);
    }
    json_response(['success' => true, 'message' => 'Transaction deleted']);
}

json_response(['success' => false, 'message' => 'Method not allowed'], 405);
