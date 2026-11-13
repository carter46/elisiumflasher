<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_once __DIR__ . '/../includes/tracker_uetr.php';
require_once __DIR__ . '/../includes/tracker_otp.php';
require_once __DIR__ . '/../includes/tracker_store.php';
require_once __DIR__ . '/../includes/tracker_payload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$pdo = get_db();
tracker_require_api_key($pdo);
tracker_server_ensure_schema($pdo);

$input = $_SERVER['REQUEST_METHOD'] === 'POST' ? tracker_json_body() : $_GET;
$uetr = tracker_uetr_normalize((string) ($input['uetr'] ?? ''));
$token = trim((string) ($input['unlock_token'] ?? ''));

if (!tracker_uetr_is_valid($uetr)) {
    json_response(['success' => false, 'message' => 'Invalid UETR'], 422);
}
if ($token === '' || !tracker_otp_unlock_valid($pdo, $uetr, $token)) {
    json_response(['success' => false, 'message' => 'Unlock token required or expired'], 401);
}

$found = tracker_find_any_by_uetr($pdo, $uetr);
if (!$found) {
    json_response(['success' => false, 'found' => false, 'message' => 'Transaction not found'], 404);
}

json_response(tracker_build_payload($found['tx'], $found['rail']));
