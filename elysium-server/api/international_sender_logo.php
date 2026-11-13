<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/receipt_logo_helpers.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$pdo = get_db();

if ($method === 'POST') {
    validate_admin_session();

    if (isset($_POST['action']) && $_POST['action'] === 'clear') {
        elysium_clear_receipt_logo($pdo);
        $row = $pdo->query('SELECT receipt_logo_path, updated_at FROM international_sender_settings WHERE id = 1 LIMIT 1')->fetch();
        json_response([
            'success' => true,
            'message' => 'Receipt logo cleared',
            'receipt_logo_path' => $row['receipt_logo_path'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
        ]);
    }

    if (!isset($_FILES['logo']) || !is_uploaded_file($_FILES['logo']['tmp_name'])) {
        json_response(['success' => false, 'message' => 'No file uploaded'], 422);
    }

    $f = $_FILES['logo'];
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        json_response(['success' => false, 'message' => 'Upload failed'], 422);
    }

    if (($f['size'] ?? 0) > 2 * 1024 * 1024) {
        json_response(['success' => false, 'message' => 'File too large (max 2MB)'], 422);
    }

    $mime = 'application/octet-stream';
    if (class_exists('finfo')) {
        $fi = new finfo(FILEINFO_MIME_TYPE);
        $mime = $fi->file($f['tmp_name']) ?: $mime;
    } elseif (function_exists('mime_content_type')) {
        $mime = mime_content_type($f['tmp_name']) ?: $mime;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($allowed[$mime])) {
        json_response(['success' => false, 'message' => 'Only JPEG, PNG, WebP, or GIF allowed'], 422);
    }

    $dir = dirname(__DIR__) . '/uploads/receipt_logos';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    elysium_delete_stored_receipt_logo_file($pdo);

    $name = 'receipt_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        json_response(['success' => false, 'message' => 'Could not save file'], 500);
    }

    $publicPath = '/uploads/receipt_logos/' . $name;
    $upd = $pdo->prepare('UPDATE international_sender_settings SET receipt_logo_path = ?, updated_at = NOW() WHERE id = 1');
    $upd->execute([$publicPath]);

    $row = $pdo->query('SELECT receipt_logo_path, updated_at FROM international_sender_settings WHERE id = 1 LIMIT 1')->fetch();
    json_response([
        'success' => true,
        'receipt_logo_path' => $row['receipt_logo_path'] ?? null,
        'updated_at' => $row['updated_at'] ?? null,
    ]);
}

if ($method === 'DELETE') {
    validate_admin_session();
    elysium_clear_receipt_logo($pdo);
    $row = $pdo->query('SELECT updated_at FROM international_sender_settings WHERE id = 1 LIMIT 1')->fetch();
    json_response([
        'success' => true,
        'message' => 'Receipt logo cleared',
        'updated_at' => $row['updated_at'] ?? null,
    ]);
}

json_response(['success' => false, 'message' => 'Method not allowed'], 405);
