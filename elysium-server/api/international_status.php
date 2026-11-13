<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$pdo = get_db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    validate_admin_session();
    $row = $pdo->query('SELECT status, updated_at FROM international_status WHERE id = 1 LIMIT 1')->fetch();
    if (!$row) {
        json_response(['success' => false, 'message' => 'International status not initialized'], 500);
    }
    json_response(['success' => true, 'status' => $row]);
}

if ($method === 'PUT') {
    validate_admin_session();
    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    $status = strtolower(trim((string) ($input['status'] ?? '')));
    $allowed = ['none', 'pending', 'processing', 'failed', 'reversed', 'network_error'];
    if (!in_array($status, $allowed, true)) {
        json_response(['success' => false, 'message' => 'Invalid status'], 422);
    }
    $stmt = $pdo->prepare('UPDATE international_status SET status = ? WHERE id = 1');
    $stmt->execute([$status]);
    $row = $pdo->query('SELECT status, updated_at FROM international_status WHERE id = 1 LIMIT 1')->fetch();
    json_response(['success' => true, 'status' => $row]);
}

json_response(['success' => false, 'message' => 'Method not allowed'], 405);

