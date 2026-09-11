<?php
declare(strict_types=1);

/**
 * Admin-only APK download. Streams private file — not a public static URL.
 * Expo artifact links expire; this serves the published site copy.
 */
require_once __DIR__ . '/../includes/admin_auth.php';

validate_admin_session();

$dir = defined('MOBILE_APK_PRIVATE_DIR')
    ? MOBILE_APK_PRIVATE_DIR
    : (__DIR__ . '/private_mobile');
$filename = defined('MOBILE_APK_FILENAME')
    ? MOBILE_APK_FILENAME
    : 'banking-companion.apk';
$path = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR)
    . DIRECTORY_SEPARATOR
    . $filename;

if (!is_readable($path) || filesize($path) < 5 * 1024 * 1024) {
    json_response([
        'success' => false,
        'message' => 'No published APK available yet. Build with EAS, then run publish:from-eas and deploy api/private_mobile/.',
    ], 404);
}

header('Content-Type: application/vnd.android.package-archive');
header('Content-Length: ' . (string) filesize($path));
header('Content-Disposition: attachment; filename="banking-companion.apk"');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');
header('Pragma: no-cache');

readfile($path);
exit;
