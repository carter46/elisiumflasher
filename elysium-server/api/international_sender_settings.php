<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/receipt_logo_helpers.php';

$pdo = get_db();
$method = $_SERVER['REQUEST_METHOD'];

// GET is allowed for authenticated users (receipt needs it).
// PUT is admin-only.

if ($method === 'GET') {
    start_app_session();
    $userOk = is_logged_in();
    $adminOk = is_admin_logged_in();
    if (!$userOk && !$adminOk) {
        json_response([
            'success' => false,
            'message' => 'Session expired',
            'redirect' => '/',
        ], 401);
    }
    if ($userOk) {
        $_SESSION['last_activity'] = time();
    }
    if ($adminOk) {
        $_SESSION['admin_last_activity'] = time();
    }
    $row = $pdo->query('
        SELECT sender_name, sender_bank, sender_country,
               sender_address_line1, sender_address_line2, sender_address_line3,
               sender_swift, sender_iban, default_delivery_date, receipt_logo_path, updated_at
        FROM international_sender_settings
        WHERE id = 1
        LIMIT 1
    ')->fetch();
    if (!$row) {
        json_response(['success' => false, 'message' => 'Sender settings not initialized'], 500);
    }
    json_response(['success' => true, 'sender' => $row]);
}

if ($method === 'PUT') {
    validate_admin_session();
    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    if (!is_array($input)) {
        json_response(['success' => false, 'message' => 'Invalid JSON'], 400);
    }

    $fields = [
        'sender_name', 'sender_bank', 'sender_country',
        'sender_address_line1', 'sender_address_line2', 'sender_address_line3',
        'sender_swift', 'sender_iban', 'default_delivery_date',
    ];

    $clearedLogo = false;
    if (!empty($input['clear_receipt_logo'])) {
        elysium_clear_receipt_logo($pdo);
        $clearedLogo = true;
        unset($input['clear_receipt_logo']);
    }

    $updates = [];
    $params = [];
    foreach ($fields as $f) {
        if (array_key_exists($f, $input)) {
            $val = trim((string) ($input[$f] ?? ''));
            if (in_array($f, ['sender_name', 'sender_bank', 'sender_country', 'sender_address_line1'], true) && $val === '') {
                json_response(['success' => false, 'message' => $f . ' cannot be empty'], 422);
            }
            $updates[] = $f . ' = ?';
            $params[] = $val === '' ? null : $val;
        }
    }

    if (!$updates && !$clearedLogo) {
        json_response(['success' => false, 'message' => 'No valid fields to update'], 422);
    }

    if ($updates) {
        $sql = 'UPDATE international_sender_settings SET ' . implode(', ', $updates) . ' WHERE id = 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    $row = $pdo->query('
        SELECT sender_name, sender_bank, sender_country,
               sender_address_line1, sender_address_line2, sender_address_line3,
               sender_swift, sender_iban, default_delivery_date, receipt_logo_path, updated_at
        FROM international_sender_settings
        WHERE id = 1
        LIMIT 1
    ')->fetch();
    json_response(['success' => true, 'sender' => $row]);
}

json_response(['success' => false, 'message' => 'Method not allowed'], 405);
