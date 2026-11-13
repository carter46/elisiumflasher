<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../includes/tracker_api_auth.php';
require_once __DIR__ . '/../includes/tracker_mail.php';
require_once __DIR__ . '/../includes/tracker_store.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();
tracker_flasher_ensure_schema($pdo);

$smtpKeys = [
    'smtp_host',
    'smtp_port',
    'smtp_user',
    'smtp_pass',
    'smtp_encryption',
    'smtp_from_email',
    'smtp_from_name',
];

switch ($method) {
    case 'GET':
        validate_admin_session();
        $cfg = tracker_smtp_config($pdo);
        json_response([
            'success' => true,
            'tracker_api_key' => tracker_api_key_ensure($pdo),
            'smtp' => [
                'host' => $cfg['host'],
                'port' => $cfg['port'],
                'user' => $cfg['user'],
                'pass' => $cfg['pass'] !== '' ? '********' : '',
                'pass_set' => $cfg['pass'] !== '',
                'encryption' => $cfg['encryption'],
                'from_email' => $cfg['from_email'],
                'from_name' => $cfg['from_name'],
            ],
        ]);
        break;

    case 'PUT':
        validate_admin_session();
        $input = tracker_json_body();

        if (!empty($input['rotate_api_key'])) {
            $key = bin2hex(random_bytes(24));
            tracker_app_setting_set($pdo, 'tracker_api_key', $key);
        }

        if (isset($input['smtp']) && is_array($input['smtp'])) {
            $smtp = $input['smtp'];
            $map = [
                'host' => 'smtp_host',
                'port' => 'smtp_port',
                'user' => 'smtp_user',
                'encryption' => 'smtp_encryption',
                'from_email' => 'smtp_from_email',
                'from_name' => 'smtp_from_name',
            ];
            foreach ($map as $inKey => $settingKey) {
                if (array_key_exists($inKey, $smtp)) {
                    tracker_app_setting_set($pdo, $settingKey, trim((string) $smtp[$inKey]));
                }
            }
            if (array_key_exists('pass', $smtp)) {
                $pass = (string) $smtp['pass'];
                if ($pass !== '' && $pass !== '********') {
                    tracker_app_setting_set($pdo, 'smtp_pass', $pass);
                }
            }
        }

        $cfg = tracker_smtp_config($pdo);
        json_response([
            'success' => true,
            'message' => 'Tracker settings saved',
            'tracker_api_key' => tracker_api_key_ensure($pdo),
            'smtp' => [
                'host' => $cfg['host'],
                'port' => $cfg['port'],
                'user' => $cfg['user'],
                'pass' => $cfg['pass'] !== '' ? '********' : '',
                'pass_set' => $cfg['pass'] !== '',
                'encryption' => $cfg['encryption'],
                'from_email' => $cfg['from_email'],
                'from_name' => $cfg['from_name'],
            ],
        ]);
        break;

    case 'POST':
        validate_admin_session();
        $input = tracker_json_body();
        $action = (string) ($input['action'] ?? '');
        if ($action === 'test_mail') {
            $to = trim((string) ($input['email'] ?? ''));
            if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                json_response(['success' => false, 'message' => 'Valid email required'], 422);
            }
            $ok = tracker_send_mail(
                $pdo,
                $to,
                'Elysium Tracker SMTP test',
                "SMTP test from Elysium at " . date('c') . "\n"
            );
            json_response([
                'success' => $ok,
                'message' => $ok ? 'Test email accepted by mail transport' : 'Failed to send test email — check SMTP settings',
            ], $ok ? 200 : 500);
        }
        json_response(['success' => false, 'message' => 'Unknown action'], 422);
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}
