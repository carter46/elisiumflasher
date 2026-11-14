<?php
declare(strict_types=1);

/**
 * Health/ping for Bolt Switch Trace (Hub → this host).
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_once __DIR__ . '/../includes/tracker_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'ok' => false, 'message' => 'Method not allowed'], 405);
}

$pdo = get_db();
tracker_flasher_ensure_schema($pdo);
tracker_require_api_key($pdo);

json_response([
    'success' => true,
    'ok' => true,
    'message' => 'Elysium Flasher tracker endpoint reachable',
    'host' => 'elysium-flasher',
]);
