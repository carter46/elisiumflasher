<?php
declare(strict_types=1);

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'u502532383_elysiumflash');
define('DB_USER', getenv('DB_USER') ?: 'u502532383_elysiumflash');
define('DB_PASS', getenv('DB_PASS') ?: 'Secretpass0721//');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME', 'ELESIUM_FLASHER_SESSION');
define('SESSION_LIFETIME', 60 * 60 * 2); // 2 hours of inactivity (server-side)

function get_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    $fwd = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    if ($fwd === 'https') {
        return true;
    }
    $fwdSsl = (string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '');
    if ($fwdSsl === 'on') {
        return true;
    }

    return false;
}

function start_app_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name(SESSION_NAME);

    $secure = is_https_request();
    // Cookie lasts for the browser session. Inactivity is enforced server-side via SESSION_LIFETIME.
    // Using SESSION_LIFETIME as cookie max-age caused early logouts (cookie died even while using the app).
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function app_setting(string $key): ?string
{
    $stmt = get_db()->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? (string) $row['setting_value'] : null;
}

// Optional FCM (mobile companion push). Leave empty to disable. Never accept from admin UI.
if (!defined('FCM_PROJECT_ID')) {
    define('FCM_PROJECT_ID', getenv('FCM_PROJECT_ID') ?: '');
}
if (!defined('FCM_SERVICE_ACCOUNT_JSON')) {
    // Filesystem path to Firebase service-account JSON on the server.
    define('FCM_SERVICE_ACCOUNT_JSON', getenv('FCM_SERVICE_ACCOUNT_JSON') ?: '');
}
