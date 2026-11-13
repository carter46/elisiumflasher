<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

function is_logged_in(): bool
{
    start_app_session();
    if (empty($_SESSION['authenticated']) || empty($_SESSION['client_key'])) {
        return false;
    }
    $last = isset($_SESSION['last_activity']) ? (int) $_SESSION['last_activity'] : 0;
    if ($last <= 0 || (time() - $last) > SESSION_LIFETIME) {
        unset($_SESSION['authenticated'], $_SESSION['client_key'], $_SESSION['last_activity']);
        return false;
    }
    return true;
}

function auth_is_api_request(): bool
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return str_contains($uri, '/api/');
}

function require_login(): void
{
    start_app_session();
    if (!is_logged_in()) {
        if (auth_is_api_request()) {
            json_response([
                'success' => false,
                'message' => 'Session expired',
                'redirect' => '/',
            ], 401);
        }
        header('Location: /');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Allow either an authenticated client session or an admin session.
 * Used for receipt pages admins open from account settings.
 */
function require_user_or_admin_login(): void
{
    require_once __DIR__ . '/admin_auth.php';
    start_app_session();
    if (is_logged_in()) {
        $_SESSION['last_activity'] = time();
        return;
    }
    if (is_admin_logged_in()) {
        $_SESSION['admin_last_activity'] = time();
        return;
    }
    if (auth_is_api_request()) {
        json_response([
            'success' => false,
            'message' => 'Session expired',
            'redirect' => '/',
        ], 401);
    }
    header('Location: /');
    exit;
}

function preferred_transfer_paths(): array
{
    return ['/local_dashboard.php'];
}

function set_preferred_transfer_path(string $path): bool
{
    $path = trim($path);
    if (!in_array($path, preferred_transfer_paths(), true)) {
        return false;
    }
    start_app_session();
    $_SESSION['preferred_transfer_path'] = $path;
    return true;
}

function get_preferred_transfer_path(): ?string
{
    start_app_session();
    $path = isset($_SESSION['preferred_transfer_path']) ? (string) $_SESSION['preferred_transfer_path'] : '';
    if ($path === '' || !in_array($path, preferred_transfer_paths(), true)) {
        return null;
    }
    return $path;
}

function clear_preferred_transfer_path(): void
{
    start_app_session();
    unset($_SESSION['preferred_transfer_path']);
}

function login_with_client_key(string $clientKey): bool
{
    $clientKey = trim($clientKey);
    if ($clientKey === '') {
        return false;
    }

    // Multi-active key behavior: accept ANY active client_key.
    $stmt = get_db()->prepare('SELECT client_key FROM client_keys WHERE is_active = 1 AND client_key = ? LIMIT 1');
    $stmt->execute([$clientKey]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }

    $valid = hash_equals((string) $row['client_key'], $clientKey);
    if (!$valid) {
        return false;
    }

    start_app_session();
    $_SESSION['authenticated'] = true;
    $_SESSION['client_key'] = $clientKey;
    $_SESSION['last_activity'] = time();
    return true;
}

function logout_user(): void
{
    start_app_session();
    clear_preferred_transfer_path();
    $_SESSION = [];
    session_destroy();
}
