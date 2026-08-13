<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->prepare('SELECT status, updated_at FROM platform_status WHERE id = 1 LIMIT 1');
            $stmt->execute();
            $row = $stmt->fetch();

            if (!$row) {
                $status = 'on';
            } else {
                $status = (string) $row['status'];
            }

            json_response(['success' => true, 'status' => $status]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch platform status'], 500);
        }
        break;

    case 'PUT':
        require_once __DIR__ . '/../includes/admin_auth.php';
        validate_admin_session();

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        $status = isset($input['status']) ? (string) $input['status'] : null;

        if (!in_array($status, ['on', 'off'], true)) {
            json_response(['success' => false, 'message' => 'Invalid status. Expected "on" or "off".'], 422);
        }

        try {
            $stmt = $pdo->prepare('
                INSERT INTO platform_status (id, status)
                VALUES (1, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status)
            ');
            $stmt->execute([$status]);
            json_response(['success' => true, 'status' => $status], 200);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to update platform status'], 500);
        }
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}
