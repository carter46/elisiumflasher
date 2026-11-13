<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/elysium_client.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    tracker_json(['success' => false, 'message' => 'Method not allowed'], 405);
}

tracker_start_session();
$input = tracker_body();
$uetr = strtolower(trim((string) ($input['uetr'] ?? '')));

if (!tracker_uetr_valid($uetr)) {
    tracker_json(['success' => false, 'found' => false, 'message' => 'Enter a valid 36-character UETR (UUID v4)'], 422);
}

$result = elysium_locate_fanout($uetr);
$matches = $result['matches'];
$errors = $result['errors'];

$authFails = array_filter($errors, static fn ($e) => ($e['code'] ?? '') === 'auth_failed');
$notConfigured = array_filter($errors, static fn ($e) => ($e['code'] ?? '') === 'not_configured');

if (count($matches) === 0) {
    unset($_SESSION['tracker_match'], $_SESSION['tracker_uetr']);
    if ($notConfigured) {
        tracker_json([
            'success' => false,
            'found' => false,
            'message' => 'No Elysium sites are configured with API keys on this tracker.',
        ], 503);
    }
    if (count($authFails) > 0 && count($authFails) === count($errors) && count($errors) > 0) {
        tracker_json([
            'success' => false,
            'found' => false,
            'message' => 'Tracker could not authenticate with configured Elysium sites. Check API keys.',
        ], 502);
    }
    tracker_json([
        'success' => true,
        'found' => false,
        'message' => 'No matching transaction on configured sites',
    ]);
}

if (count($matches) > 1) {
    unset($_SESSION['tracker_match'], $_SESSION['tracker_uetr']);
    tracker_json([
        'success' => false,
        'found' => false,
        'conflict' => true,
        'message' => 'Multiple sites reported this UETR. Contact support.',
    ], 409);
}

$match = $matches[0];
session_regenerate_id(true);
$_SESSION['tracker_uetr'] = $uetr;
$_SESSION['tracker_match'] = $match;
$_SESSION['tracker_unlocked'] = false;
unset($_SESSION['tracker_unlock_token'], $_SESSION['tracker_payload']);

tracker_json([
    'success' => true,
    'found' => true,
    'site_id' => $match['site_id'],
    'site_name' => $match['name'],
    'rail' => $match['rail'],
    'uetr' => $uetr,
]);
