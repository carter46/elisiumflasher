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

function tracker_settings_bst_payload(PDO $pdo): array
{
    $secret = (string) (tracker_app_setting_get($pdo, 'bst_client_secret') ?? '');
    return [
        'tracker_url' => (string) (tracker_app_setting_get($pdo, 'bst_tracker_url') ?? ''),
        'integration_id' => (string) (tracker_app_setting_get($pdo, 'bst_integration_id') ?? ''),
        'client_id' => (string) (tracker_app_setting_get($pdo, 'bst_client_id') ?? ''),
        'client_secret' => $secret !== '' ? '********' : '',
        'client_secret_set' => $secret !== '',
    ];
}

function tracker_settings_smtp_payload(PDO $pdo): array
{
    $cfg = tracker_smtp_config($pdo);
    return [
        'host' => $cfg['host'],
        'port' => $cfg['port'],
        'user' => $cfg['user'],
        'pass' => $cfg['pass'] !== '' ? '********' : '',
        'pass_set' => $cfg['pass'] !== '',
        'encryption' => $cfg['encryption'],
        'from_email' => $cfg['from_email'],
        'from_name' => $cfg['from_name'],
    ];
}

switch ($method) {
    case 'GET':
        validate_admin_session();
        json_response([
            'success' => true,
            'tracker_api_key' => tracker_api_key_ensure($pdo),
            'bst' => tracker_settings_bst_payload($pdo),
            'smtp' => tracker_settings_smtp_payload($pdo),
        ]);
        break;

    case 'PUT':
        validate_admin_session();
        $input = tracker_json_body();

        if (!empty($input['rotate_api_key'])) {
            $key = bin2hex(random_bytes(24));
            tracker_app_setting_set($pdo, 'tracker_api_key', $key);
        }

        if (isset($input['bst']) && is_array($input['bst'])) {
            $bst = $input['bst'];
            $url = array_key_exists('tracker_url', $bst)
                ? tracker_normalize_hub_url((string) $bst['tracker_url'])
                : (string) (tracker_app_setting_get($pdo, 'bst_tracker_url') ?? '');
            $integrationId = array_key_exists('integration_id', $bst)
                ? trim((string) $bst['integration_id'])
                : (string) (tracker_app_setting_get($pdo, 'bst_integration_id') ?? '');
            $clientId = array_key_exists('client_id', $bst)
                ? trim((string) $bst['client_id'])
                : (string) (tracker_app_setting_get($pdo, 'bst_client_id') ?? '');
            $existingSecret = (string) (tracker_app_setting_get($pdo, 'bst_client_secret') ?? '');
            $secretIn = array_key_exists('client_secret', $bst) ? trim((string) $bst['client_secret']) : '';
            if ($secretIn === '********') {
                $secretIn = '';
            }
            $effectiveSecret = $secretIn !== '' ? $secretIn : $existingSecret;

            $any = ($url !== '' || $integrationId !== '' || $clientId !== '' || $effectiveSecret !== '');
            $all = ($url !== '' && $integrationId !== '' && $clientId !== '' && $effectiveSecret !== '');
            if ($any && !$all) {
                json_response([
                    'success' => false,
                    'message' => 'Enter Tracker URL, Integration ID, Client ID, and Client secret together (or clear all).',
                ], 422);
            }
            if ($url !== '' && !preg_match('#^https?://#i', $url)) {
                json_response(['success' => false, 'message' => 'Tracker URL must start with http:// or https://'], 422);
            }

            tracker_app_setting_set($pdo, 'bst_tracker_url', $url);
            tracker_app_setting_set($pdo, 'bst_integration_id', $integrationId);
            tracker_app_setting_set($pdo, 'bst_client_id', $clientId);
            if ($secretIn !== '') {
                tracker_app_setting_set($pdo, 'bst_client_secret', $secretIn);
            } elseif (!$any) {
                tracker_app_setting_set($pdo, 'bst_client_secret', '');
            }
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

        json_response([
            'success' => true,
            'message' => 'Tracker settings saved',
            'tracker_api_key' => tracker_api_key_ensure($pdo),
            'bst' => tracker_settings_bst_payload($pdo),
            'smtp' => tracker_settings_smtp_payload($pdo),
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
        if ($action === 'test_hub') {
            $result = tracker_ping_hub($pdo);
            json_response([
                'success' => true,
                'ok' => !empty($result['ok']),
                'message' => (string) ($result['message'] ?? ''),
                'http' => (int) ($result['http'] ?? 0),
            ]);
        }
        json_response(['success' => false, 'message' => 'Unknown action'], 422);
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}
