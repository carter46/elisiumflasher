<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/elysium_client.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    tracker_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

tracker_start_session();
$input = tracker_body();
$email = strtolower(trim((string) ($input['email'] ?? '')));
$uetr = strtolower(trim((string) ($input['uetr'] ?? ($_SESSION['tracker_uetr'] ?? ''))));

if (!tracker_uetr_valid($uetr)) {
    tracker_json(['success' => false, 'message' => 'Locate a valid UETR first'], 422);
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    tracker_json(['success' => false, 'message' => 'Valid email is required'], 422);
}

$match = $_SESSION['tracker_match'] ?? null;
if (!is_array($match) || ($match['site_id'] ?? '') === '') {
    tracker_json(['success' => false, 'message' => 'No matched site in session — locate UETR again'], 409);
}
if (strtolower((string) ($_SESSION['tracker_uetr'] ?? '')) !== $uetr) {
    tracker_json(['success' => false, 'message' => 'UETR does not match located session'], 409);
}

$site = tracker_site_by_id((string) $match['site_id']);
if (!$site) {
    tracker_json(['success' => false, 'message' => 'Matched site is no longer configured'], 500);
}

$resp = elysium_post($site, '/api/tracker_otp_request.php', [
    'uetr' => $uetr,
    'email' => $email,
]);

$data = $resp['data'] ?? [];
if (!$resp['ok'] || empty($data['success'])) {
    tracker_json([
        'success' => false,
        'message' => $data['message'] ?? 'OTP request failed',
    ], $resp['http'] >= 400 ? $resp['http'] : 502);
}

$_SESSION['tracker_email'] = $email;
$_SESSION['tracker_unlocked'] = false;
unset($_SESSION['tracker_unlock_token'], $_SESSION['tracker_payload']);

$out = [
    'success' => true,
    'message' => $data['message'] ?? 'OTP sent',
    'mail_sent' => !empty($data['mail_sent']),
];
// Surface debug OTP only when Elysium returns it (SMTP not configured).
if (!empty($data['debug_otp'])) {
    $out['debug_otp'] = $data['debug_otp'];
}
tracker_json($out);
