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
    $_SESSION = [];
    session_destroy();
}
