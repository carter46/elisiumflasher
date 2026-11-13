<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/elysium_client.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    tracker_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

tracker_start_session();
$input = tracker_body();
$otp = trim((string) ($input['otp'] ?? ''));
$email = strtolower(trim((string) ($input['email'] ?? ($_SESSION['tracker_email'] ?? ''))));
$uetr = strtolower(trim((string) ($input['uetr'] ?? ($_SESSION['tracker_uetr'] ?? ''))));

if (!tracker_uetr_valid($uetr) || $email === '' || $otp === '') {
    tracker_json(['success' => false, 'message' => 'UETR, email, and OTP are required'], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    tracker_json(['success' => false, 'message' => 'Valid email is required'], 422);
}
if (strtolower((string) ($_SESSION['tracker_uetr'] ?? '')) !== $uetr) {
    tracker_json(['success' => false, 'message' => 'UETR does not match located session'], 409);
}

$match = $_SESSION['tracker_match'] ?? null;
if (!is_array($match)) {
    tracker_json(['success' => false, 'message' => 'No matched site — locate UETR again'], 409);
}

$site = tracker_site_by_id((string) $match['site_id']);
if (!$site) {
    tracker_json(['success' => false, 'message' => 'Matched site is no longer configured'], 500);
}

$resp = elysium_post($site, '/api/tracker_otp_verify.php', [
    'uetr' => $uetr,
    'email' => $email,
    'otp' => $otp,
]);
$data = $resp['data'] ?? [];
if (!$resp['ok'] || empty($data['success'])) {
    tracker_json([
        'success' => false,
        'message' => $data['message'] ?? 'OTP verification failed',
    ], $resp['http'] >= 400 ? $resp['http'] : 502);
}

$token = (string) ($data['unlock_token'] ?? '');
$_SESSION['tracker_unlock_token'] = $token;
$_SESSION['tracker_unlocked'] = true;
$_SESSION['tracker_payload'] = $data;
$_SESSION['tracker_email'] = $email;

// Prefer fresh lookup when token present
if ($token !== '') {
    $lookup = elysium_post($site, '/api/tracker_lookup.php', [
        'uetr' => $uetr,
        'unlock_token' => $token,
    ]);
    if ($lookup['ok'] && !empty($lookup['data']['success'])) {
        $_SESSION['tracker_payload'] = $lookup['data'];
        $data = $lookup['data'];
    }
}

unset($data['unlock_token'], $data['debug_otp']);
$data['site_id'] = $match['site_id'];
$data['site_name'] = $match['name'] ?? $match['site_id'];
tracker_json($data);
