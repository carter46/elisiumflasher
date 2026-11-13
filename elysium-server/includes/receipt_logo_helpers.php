<?php
declare(strict_types=1);

function elysium_receipt_logo_disk_path(string $publicPath): ?string
{
    if ($publicPath === '' || strpos($publicPath, '..') !== false) {
        return null;
    }
    if (strpos($publicPath, '/uploads/receipt_logos/') !== 0) {
        return null;
    }
    $full = dirname(__DIR__) . $publicPath;
    return is_file($full) ? $full : null;
}

function elysium_delete_stored_receipt_logo_file(PDO $pdo): void
{
    $row = $pdo->query('SELECT receipt_logo_path FROM international_sender_settings WHERE id = 1 LIMIT 1')->fetch();
    if (!$row || empty($row['receipt_logo_path'])) {
        return;
    }
    $disk = elysium_receipt_logo_disk_path((string) $row['receipt_logo_path']);
    if ($disk) {
        @unlink($disk);
    }
}

function elysium_clear_receipt_logo(PDO $pdo): void
{
    elysium_delete_stored_receipt_logo_file($pdo);
    $pdo->exec('UPDATE international_sender_settings SET receipt_logo_path = NULL, updated_at = NOW() WHERE id = 1');
}
