<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/tracker_status.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_once __DIR__ . '/../includes/tracker_store.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();
tracker_flasher_ensure_schema($pdo);

if ($method === 'GET') {
    validate_admin_session();
    json_response([
        'success' => true,
        'local_tracker_status' => tracker_status_normalize(
            strtoupper((string) (tracker_app_setting_get($pdo, 'local_tracker_status', 'PDNG') ?? 'PDNG'))
        ),
        'tracker_status_options' => array_values(tracker_status_catalog()),
    ]);
}

if ($method === 'PUT') {
    validate_admin_session();
    $input = tracker_json_body();
    if (isset($input['local_tracker_status'])) {
        $code = tracker_status_normalize((string) $input['local_tracker_status']);
        tracker_app_setting_set($pdo, 'local_tracker_status', $code);
    }
    json_response([
        'success' => true,
        'message' => 'Tracker defaults saved',
        'local_tracker_status' => tracker_status_normalize(
            strtoupper((string) (tracker_app_setting_get($pdo, 'local_tracker_status', 'PDNG') ?? 'PDNG'))
        ),
    ]);
}

json_response(['success' => false, 'message' => 'Method not allowed'], 405);
