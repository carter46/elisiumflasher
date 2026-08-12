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
            $stmt = $pdo->query('SELECT id, client_key, is_active, created_at FROM client_keys ORDER BY is_active DESC, created_at DESC');
            $keys = $stmt->fetchAll();
            json_response(['success' => true, 'keys' => $keys]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch client keys'], 500);
        }
        break;

    case 'POST':
        validate_admin_session();

        // Generate new key (multi-active behavior: keep existing active keys).
        try {
            $clientKey = bin2hex(random_bytes(32)); // 64 char hex

            $stmt = $pdo->prepare('INSERT INTO client_keys (client_key, is_active) VALUES (?, 1)');
            $stmt->execute([$clientKey]);

            $id = (int) $pdo->lastInsertId();
            json_response(['success' => true, 'generated_key' => $clientKey, 'id' => $id]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to generate client key'], 500);
        }
        break;

    case 'PUT':
        validate_admin_session();

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        $id = isset($input['id']) ? (int) $input['id'] : 0;
        $isActive = isset($input['is_active']) ? (int) $input['is_active'] : null;

        if ($id <= 0 || !in_array($isActive, [0, 1], true)) {
            json_response(['success' => false, 'message' => 'Invalid payload. Expected {id, is_active:0|1}'], 422);
        }

        try {
            $stmt = $pdo->prepare('UPDATE client_keys SET is_active = ? WHERE id = ?');
            $stmt->execute([$isActive, $id]);

            json_response(['success' => true, 'message' => 'Client key updated']);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to update client key'], 500);
        }
        break;

    case 'DELETE':
        validate_admin_session();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            json_response(['success' => false, 'message' => 'Client key id is required'], 422);
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM client_keys WHERE id = ?');
            $stmt->execute([$id]);
            json_response(['success' => true, 'message' => 'Client key deleted']);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to delete client key'], 500);
        }
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

