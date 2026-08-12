<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

function is_admin_logged_in(): bool
{
    start_app_session();
    if (empty($_SESSION['admin_authenticated']) || empty($_SESSION['admin_id'])) {
        return false;
    }
    $last = isset($_SESSION['admin_last_activity']) ? (int) $_SESSION['admin_last_activity'] : 0;
    if ($last <= 0 || (time() - $last) > SESSION_LIFETIME) {
        unset(
            $_SESSION['admin_authenticated'],
            $_SESSION['admin_id'],
            $_SESSION['admin_username'],
            $_SESSION['admin_last_activity']
        );
        return false;
    }
    return true;
}

function admin_auth_is_api_request(): bool
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return str_contains($uri, '/api/');
}

function require_admin_login(): void
{
    start_app_session();
    if (!is_admin_logged_in()) {
        if (admin_auth_is_api_request()) {
            json_response([
                'success' => false,
                'message' => 'Session expired',
                'redirect' => '/admin/login.php',
            ], 401);
        }
        header('Location: /admin/login.php');
        exit;
    }
    $_SESSION['admin_last_activity'] = time();
}

function admin_login_with_credentials(string $username, string $password): bool
{
    $username = trim($username);
    $password = (string) $password;

    if ($username === '' || $password === '') {
        return false;
    }

    $stmt = get_db()->prepare('SELECT id, username, password FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }

    // Reference pattern uses direct password comparison; we still use hash_equals for timing safety.
    $valid = hash_equals((string) $row['password'], $password);
    if (!$valid) {
        return false;
    }

    start_app_session();
    $_SESSION['admin_authenticated'] = true;
    $_SESSION['admin_id'] = (int) $row['id'];
    $_SESSION['admin_username'] = (string) $row['username'];
    $_SESSION['admin_last_activity'] = time();

    // Update last login
    $upd = get_db()->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?');
    $upd->execute([(int) $row['id']]);

    return true;
}

function admin_logout_user(): void
{
    start_app_session();
    $_SESSION = [];
    session_destroy();
}

/**
 * Admin-only API guard.
 * Returns admin ID when valid, otherwise sends a JSON 401 and exits.
 */
function validate_admin_session(): int
{
    start_app_session();

    if (empty($_SESSION['admin_authenticated']) || empty($_SESSION['admin_id'])) {
        json_response([
            'success' => false,
            'message' => 'Not authenticated',
            'redirect' => '/admin/login.php',
        ], 401);
    }

    $last = isset($_SESSION['admin_last_activity']) ? (int) $_SESSION['admin_last_activity'] : 0;
    if ($last <= 0 || (time() - $last) > SESSION_LIFETIME) {
        unset(
            $_SESSION['admin_authenticated'],
            $_SESSION['admin_id'],
            $_SESSION['admin_username'],
            $_SESSION['admin_last_activity']
        );
        json_response([
            'success' => false,
            'message' => 'Session expired',
            'redirect' => '/admin/login.php',
        ], 401);
    }

    $_SESSION['admin_last_activity'] = time();
    return (int) $_SESSION['admin_id'];
}

