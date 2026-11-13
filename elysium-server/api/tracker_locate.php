<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_once __DIR__ . '/../includes/tracker_uetr.php';
require_once __DIR__ . '/../includes/tracker_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$pdo = get_db();
tracker_require_api_key($pdo);
tracker_server_ensure_schema($pdo);

$input = $_SERVER['REQUEST_METHOD'] === 'POST' ? tracker_json_body() : $_GET;
$uetr = tracker_uetr_normalize((string) ($input['uetr'] ?? ''));

if (!tracker_uetr_is_valid($uetr)) {
    json_response(['success' => true, 'found' => false]);
}

$found = tracker_find_any_by_uetr($pdo, $uetr);
if (!$found) {
    json_response(['success' => true, 'found' => false]);
}

json_response([
    'success' => true,
    'found' => true,
    'site_id' => 'server',
    'rail' => $found['rail'],
]);
