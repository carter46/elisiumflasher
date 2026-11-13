<?php
declare(strict_types=1);

function tracker_config(): array
{
    static $cfg = null;
    if ($cfg !== null) {
        return $cfg;
    }
    $local = __DIR__ . '/../config/sites.local.php';
    $path = is_file($local) ? $local : (__DIR__ . '/../config/sites.php');
    $cfg = is_file($path) ? require $path : ['sites' => [], 'otp' => []];
    if (!is_array($cfg)) {
        $cfg = ['sites' => [], 'otp' => []];
    }
    return $cfg;
}

function tracker_enabled_sites(): array
{
    $sites = tracker_config()['sites'] ?? [];
    $out = [];
    foreach ($sites as $site) {
        if (!is_array($site)) {
            continue;
        }
        if (empty($site['enabled'])) {
            continue;
        }
        $base = trim((string) ($site['base_url'] ?? ''));
        $key = trim((string) ($site['api_key'] ?? ''));
        if ($base === '' || $key === '') {
            continue;
        }
        $out[] = $site;
    }
    return $out;
}

function tracker_site_by_id(string $id): ?array
{
    foreach (tracker_enabled_sites() as $site) {
        if ((string) ($site['id'] ?? '') === $id) {
            return $site;
        }
    }
    return null;
}

function tracker_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function tracker_body(): array
{
    $raw = file_get_contents('php://input') ?: '{}';
    $input = json_decode($raw, true);
    return is_array($input) ? $input : [];
}

function tracker_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('INSTITUTIONAL_TRACKER_SESSION');
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function tracker_uetr_valid(string $uetr): bool
{
    return (bool) preg_match(
        '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
        trim($uetr)
    );
}
