<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();

$allowedLink = ['on', 'off'];
$allowedTransfer = ['successful', 'failed', 'pending', 'reversed'];

function local_status_get(PDO $pdo, string $key, string $default): string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row || $row['setting_value'] === null || trim((string) $row['setting_value']) === '') {
        return $default;
    }
    return trim((string) $row['setting_value']);
}

function local_status_set(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare('
        INSERT INTO app_settings (setting_key, setting_value)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ');
    $stmt->execute([$key, $value]);
}

switch ($method) {
    case 'GET':
        // Logged-in clients and admins can read (needed for send/sync flow).
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
        if ($isUser) {
            $_SESSION['last_activity'] = time();
        }
        if ($isAdmin) {
            $_SESSION['admin_last_activity'] = time();
        }

        try {
            $link = local_status_get($pdo, 'local_link_wallet_status', 'on');
            $transfer = local_status_get($pdo, 'local_transfer_status', 'successful');
            if (!in_array($link, $allowedLink, true)) {
                $link = 'on';
            }
            if (!in_array($transfer, $allowedTransfer, true)) {
                $transfer = 'successful';
            }
            json_response([
                'success' => true,
                'link_wallet_status' => $link,
                'transfer_status' => $transfer,
            ]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch local transfer status'], 500);
        }
        break;

    case 'PUT':
        validate_admin_session();
        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        if (!is_array($input)) {
            json_response(['success' => false, 'message' => 'Invalid JSON body'], 400);
        }

        $link = isset($input['link_wallet_status'])
            ? strtolower(trim((string) $input['link_wallet_status']))
            : null;
        $transfer = isset($input['transfer_status'])
            ? strtolower(trim((string) $input['transfer_status']))
            : null;

        if ($link === null && $transfer === null) {
            json_response(['success' => false, 'message' => 'No status fields provided'], 422);
        }
        if ($link !== null && !in_array($link, $allowedLink, true)) {
            json_response(['success' => false, 'message' => 'Invalid link wallet status'], 422);
        }
        if ($transfer !== null && !in_array($transfer, $allowedTransfer, true)) {
            json_response(['success' => false, 'message' => 'Invalid transfer status'], 422);
        }

        try {
            if ($link !== null) {
                local_status_set($pdo, 'local_link_wallet_status', $link);
            }
            if ($transfer !== null) {
                local_status_set($pdo, 'local_transfer_status', $transfer);
            }
            json_response([
                'success' => true,
                'link_wallet_status' => local_status_get($pdo, 'local_link_wallet_status', 'on'),
                'transfer_status' => local_status_get($pdo, 'local_transfer_status', 'successful'),
                'message' => 'Local transfer status updated',
            ]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to update local transfer status'], 500);
        }
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}
