<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_once __DIR__ . '/../includes/tracker_uetr.php';
require_once __DIR__ . '/../includes/tracker_otp.php';
require_once __DIR__ . '/../includes/tracker_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$pdo = get_db();
tracker_require_api_key($pdo);
tracker_flasher_ensure_schema($pdo);

$input = tracker_json_body();
$uetr = tracker_uetr_normalize((string) ($input['uetr'] ?? ''));
$email = strtolower(trim((string) ($input['email'] ?? '')));

if (!tracker_uetr_is_valid($uetr)) {
    json_response(['success' => false, 'message' => 'Invalid UETR'], 422);
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Valid email is required'], 422);
}

$tx = tracker_find_local_by_uetr($pdo, $uetr);
if (!$tx) {
    json_response(['success' => false, 'message' => 'Transaction not found'], 404);
}

try {
    $challenge = tracker_otp_create_challenge($pdo, $uetr, $email);
    $sent = tracker_otp_send_email($pdo, $email, $uetr, $challenge['code']);
    $out = [
        'success' => true,
        'message' => $sent
            ? 'OTP sent'
            : 'OTP generated. Configure SMTP on Elysium to deliver email.',
        'mail_sent' => $sent,
    ];
    // Only expose OTP when explicitly enabled for local simulation.
    $debug = strtolower((string) (getenv('TRACKER_DEBUG_OTP') ?: ''));
    if (!$sent && ($debug === '1' || $debug === 'true')) {
        $out['debug_otp'] = $challenge['code'];
    }
    json_response($out);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => 'Failed to create OTP challenge'], 500);
}
