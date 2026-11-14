<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_once __DIR__ . '/../includes/tracker_uetr.php';
require_once __DIR__ . '/../includes/tracker_otp.php';
require_once __DIR__ . '/../includes/tracker_store.php';
require_once __DIR__ . '/../includes/tracker_payload.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
    }

    $pdo = get_db();
    tracker_require_api_key($pdo);
    tracker_flasher_ensure_schema($pdo);

    $input = tracker_json_body();
    $uetr = tracker_uetr_normalize((string) ($input['uetr'] ?? ''));
    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $otp = trim((string) ($input['otp'] ?? ''));

    if (!tracker_uetr_is_valid($uetr) || $email === '' || $otp === '') {
        json_response(['success' => false, 'message' => 'UETR, email, and OTP are required'], 422);
    }

    $tx = tracker_find_local_by_uetr($pdo, $uetr);
    if (!$tx) {
        json_response(['success' => false, 'message' => 'Transaction not found'], 404);
    }

    $token = tracker_otp_verify($pdo, $uetr, $email, $otp);
    if ($token === null) {
        json_response(['success' => false, 'message' => 'Invalid or expired OTP'], 401);
    }

    $payload = tracker_build_payload($tx, 'local');
    $payload['unlock_token'] = $token;
    $payload['message'] = 'OTP verified';
    json_response($payload);
} catch (Throwable $e) {
    error_log('tracker_otp_verify.php: ' . $e->getMessage());
    json_response([
        'success' => false,
        'message' => 'OTP verify error on Elysium Flasher: ' . $e->getMessage(),
    ], 500);
}
