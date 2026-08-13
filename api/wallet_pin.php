<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();

function wallet_pin_get_hash(PDO $pdo): ?string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute(['wallet_pin']);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $val = $row['setting_value'];
    if ($val === null) {
        return null;
    }
    $hash = trim((string) $val);
    return $hash !== '' ? $hash : null;
}

function wallet_pin_is_configured(PDO $pdo): bool
{
    return wallet_pin_get_hash($pdo) !== null;
}

function wallet_pin_upsert_hash(PDO $pdo, string $hash): void
{
    $stmt = $pdo->prepare('
        INSERT INTO app_settings (setting_key, setting_value)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ');
    $stmt->execute(['wallet_pin', $hash]);
}

switch ($method) {
    case 'GET':
        validate_admin_session();
        try {
            json_response([
                'success' => true,
                'configured' => wallet_pin_is_configured($pdo),
            ]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch wallet PIN status'], 500);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        if (!is_array($input)) {
            json_response(['success' => false, 'message' => 'Invalid JSON body'], 400);
        }
        $action = trim((string) ($input['action'] ?? ''));

        if ($action === 'save') {
            validate_admin_session();
            $pin = trim((string) ($input['pin'] ?? $input['new_pin'] ?? ''));
            $confirm = trim((string) ($input['confirm_pin'] ?? $input['confirm'] ?? ''));

            if (!preg_match('/^\d{6}$/', $pin)) {
                json_response(['success' => false, 'message' => 'PIN must be exactly 6 digits'], 422);
            }
            if ($pin !== $confirm) {
                json_response(['success' => false, 'message' => 'PIN confirmation does not match'], 422);
            }

            try {
                $hash = password_hash($pin, PASSWORD_DEFAULT);
                if ($hash === false) {
                    json_response(['success' => false, 'message' => 'Failed to hash PIN'], 500);
                }
                wallet_pin_upsert_hash($pdo, $hash);
                json_response([
                    'success' => true,
                    'configured' => true,
                    'message' => 'Wallet PIN saved',
                ]);
            } catch (Throwable $e) {
                json_response(['success' => false, 'message' => 'Failed to save wallet PIN'], 500);
            }
        }

        if ($action === 'validate') {
            require_login();
            $pin = trim((string) ($input['pin'] ?? ''));
            if (!preg_match('/^\d{6}$/', $pin)) {
                json_response(['success' => false, 'message' => 'PIN must be exactly 6 digits'], 422);
            }

            try {
                $hash = wallet_pin_get_hash($pdo);
                if ($hash === null) {
                    json_response(['success' => false, 'message' => 'Wallet PIN not configured'], 409);
                }
                if (!password_verify($pin, $hash)) {
                    json_response(['success' => false, 'message' => 'Incorrect PIN'], 401);
                }
                json_response(['success' => true, 'message' => 'PIN verified']);
            } catch (Throwable $e) {
                json_response(['success' => false, 'message' => 'Failed to validate PIN'], 500);
            }
        }

        json_response(['success' => false, 'message' => 'Invalid action'], 400);
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}
