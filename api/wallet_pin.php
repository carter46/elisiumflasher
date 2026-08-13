<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();

function wallet_pin_get_setting(PDO $pdo, string $key): ?string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $val = $row['setting_value'];
    if ($val === null) {
        return null;
    }
    $trimmed = trim((string) $val);
    return $trimmed !== '' ? $trimmed : null;
}

function wallet_pin_get_hash(PDO $pdo): ?string
{
    return wallet_pin_get_setting($pdo, 'wallet_pin');
}

function wallet_pin_is_configured(PDO $pdo): bool
{
    return wallet_pin_get_hash($pdo) !== null;
}

function wallet_pin_upsert_setting(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare('
        INSERT INTO app_settings (setting_key, setting_value)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ');
    $stmt->execute([$key, $value]);
}

function wallet_pin_enc_key(): string
{
    $material = (defined('DB_PASS') ? (string) DB_PASS : '')
        . '|'
        . (defined('DB_NAME') ? (string) DB_NAME : '')
        . '|elysium_wallet_pin_v1';
    return hash('sha256', $material, true);
}

function wallet_pin_encrypt(string $pin): string
{
    $iv = random_bytes(16);
    $cipher = openssl_encrypt($pin, 'AES-256-CBC', wallet_pin_enc_key(), OPENSSL_RAW_DATA, $iv);
    if ($cipher === false) {
        throw new RuntimeException('PIN encrypt failed');
    }
    return base64_encode($iv . $cipher);
}

function wallet_pin_decrypt(string $blob): ?string
{
    $raw = base64_decode($blob, true);
    if ($raw === false || strlen($raw) < 17) {
        return null;
    }
    $iv = substr($raw, 0, 16);
    $cipher = substr($raw, 16);
    $plain = openssl_decrypt($cipher, 'AES-256-CBC', wallet_pin_enc_key(), OPENSSL_RAW_DATA, $iv);
    if ($plain === false || !preg_match('/^\d{6}$/', $plain)) {
        return null;
    }
    return $plain;
}

function wallet_pin_copyable(PDO $pdo): bool
{
    $enc = wallet_pin_get_setting($pdo, 'wallet_pin_enc');
    return $enc !== null && wallet_pin_decrypt($enc) !== null;
}

switch ($method) {
    case 'GET':
        validate_admin_session();
        try {
            $configured = wallet_pin_is_configured($pdo);
            json_response([
                'success' => true,
                'configured' => $configured,
                'copyable' => $configured && wallet_pin_copyable($pdo),
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
                wallet_pin_upsert_setting($pdo, 'wallet_pin', $hash);
                wallet_pin_upsert_setting($pdo, 'wallet_pin_enc', wallet_pin_encrypt($pin));
                json_response([
                    'success' => true,
                    'configured' => true,
                    'copyable' => true,
                    'message' => 'Wallet PIN saved',
                ]);
            } catch (Throwable $e) {
                json_response(['success' => false, 'message' => 'Failed to save wallet PIN'], 500);
            }
        }

        if ($action === 'copy') {
            validate_admin_session();
            try {
                $enc = wallet_pin_get_setting($pdo, 'wallet_pin_enc');
                if ($enc === null) {
                    json_response([
                        'success' => false,
                        'message' => 'Saved PIN cannot be copied yet. Re-save the PIN to enable copy.',
                        'copyable' => false,
                    ], 409);
                }
                $pin = wallet_pin_decrypt($enc);
                if ($pin === null) {
                    json_response([
                        'success' => false,
                        'message' => 'Saved PIN cannot be copied yet. Re-save the PIN to enable copy.',
                        'copyable' => false,
                    ], 409);
                }
                json_response([
                    'success' => true,
                    'pin' => $pin,
                ]);
            } catch (Throwable $e) {
                json_response(['success' => false, 'message' => 'Failed to copy wallet PIN'], 500);
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
