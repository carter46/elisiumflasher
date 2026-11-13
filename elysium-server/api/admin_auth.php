<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
$action = (string) ($input['action'] ?? 'login');

if ($action === 'login') {
    $username = (string) ($input['username'] ?? '');
    $password = (string) ($input['password'] ?? '');

    if (!admin_login_with_credentials($username, $password)) {
        json_response(['success' => false, 'message' => 'Invalid username or password'], 401);
    }

    json_response(['success' => true, 'message' => 'Login successful']);
}

if ($action === 'logout') {
    admin_logout_user();
    json_response(['success' => true, 'message' => 'Logout successful']);
}

if ($action === 'check') {
    json_response(['success' => true, 'authenticated' => is_admin_logged_in()]);
}

json_response(['success' => false, 'message' => 'Invalid action'], 400);

