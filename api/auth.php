<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
$action = $input['action'] ?? 'login';

if ($action === 'login') {
    $clientKey = (string) ($input['client_key'] ?? '');
    if (!login_with_client_key($clientKey)) {
        json_response(['success' => false, 'message' => 'Invalid license key'], 401);
    }
    json_response(['success' => true, 'message' => 'Login successful']);
}

if ($action === 'check') {
    $ok = is_logged_in();
    if ($ok) {
        $_SESSION['last_activity'] = time();
    }
    json_response(['success' => true, 'authenticated' => $ok]);
}

if ($action === 'logout') {
    logout_user();
    json_response(['success' => true, 'message' => 'Logout successful']);
}

json_response(['success' => false, 'message' => 'Invalid action'], 400);
