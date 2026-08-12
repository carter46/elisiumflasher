<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = get_db();

// Helper for persisting app_settings rows.
function upsert_setting(PDO $pdo, string $key, ?string $value): void
{
    $stmt = $pdo->prepare('
        INSERT INTO app_settings (setting_key, setting_value)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ');
    $stmt->execute([$key, $value]);
}

/** Readable mask for admin UI (never send full secrets in GET). */
function mask_api_secret(?string $secret): ?string
{
    if ($secret === null || $secret === '') {
        return null;
    }
    $s = (string) $secret;
    $len = strlen($s);
    if ($len <= 10) {
        return str_repeat('•', min(12, max(4, $len)));
    }

    return substr($s, 0, 8) . str_repeat('•', 6) . substr($s, -4);
}

/**
 * @return array<string, string|null>
 */
function load_app_settings_map(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT setting_key, setting_value FROM app_settings');
    $rows = $stmt->fetchAll();
    $map = [];
    foreach ($rows as $r) {
        $map[(string) $r['setting_key']] = $r['setting_value'];
    }

    return $map;
}

switch ($method) {
    case 'GET':
        validate_admin_session();
        try {
            $map = load_app_settings_map($pdo);

            $useLive = ((string) ($map['paystack_use_live'] ?? '0')) === '1';

            // Support older key `paystack_secret_key` as fallback.
            $testPublic = (string) ($map['paystack_test_public_key'] ?? '');
            $livePublic = (string) ($map['paystack_live_public_key'] ?? '');
            $testSecret = (string) ($map['paystack_test_secret_key'] ?? '');
            $liveSecret = (string) ($map['paystack_live_secret_key'] ?? '');
            $legacySecret = (string) ($map['paystack_secret_key'] ?? '');

            $activePublic = $useLive ? $livePublic : $testPublic;
            $activeSecret = $useLive ? ($liveSecret !== '' ? $liveSecret : $legacySecret) : ($testSecret !== '' ? $testSecret : $legacySecret);

            $fwTest = (string) ($map['flutterwave_test_secret_key'] ?? '');
            $fwLive = (string) ($map['flutterwave_live_secret_key'] ?? '');
            $fwTestPublic = (string) ($map['flutterwave_test_public_key'] ?? '');
            $fwLivePublic = (string) ($map['flutterwave_live_public_key'] ?? '');
            $paystackResolveOn = ((string) ($map['paystack_resolve_enabled'] ?? '1')) === '1';
            $flutterwaveResolveOn = ((string) ($map['flutterwave_resolve_enabled'] ?? '0')) === '1';

            json_response([
                'success' => true,
                'test_public_key' => $testPublic !== '' ? $testPublic : null,
                'live_public_key' => $livePublic !== '' ? $livePublic : null,
                'use_live' => $useLive,
                'active_public_key' => $activePublic !== '' ? $activePublic : null,
                'active_secret_masked' => mask_api_secret($activeSecret !== '' ? $activeSecret : null),
                'paystack_resolve_enabled' => $paystackResolveOn,
                'flutterwave_resolve_enabled' => $flutterwaveResolveOn,
                'flutterwave_test_public_key' => $fwTestPublic !== '' ? $fwTestPublic : null,
                'flutterwave_live_public_key' => $fwLivePublic !== '' ? $fwLivePublic : null,
                'has_paystack_test_secret' => $testSecret !== '',
                'has_paystack_live_secret' => $liveSecret !== '' || $legacySecret !== '',
                'has_flutterwave_test_secret' => $fwTest !== '',
                'has_flutterwave_live_secret' => $fwLive !== '',
                'paystack_test_secret_masked' => mask_api_secret($testSecret !== '' ? $testSecret : null),
                'paystack_live_secret_masked' => mask_api_secret($liveSecret !== '' ? $liveSecret : ($legacySecret !== '' ? $legacySecret : null)),
                'flutterwave_test_secret_masked' => mask_api_secret($fwTest !== '' ? $fwTest : null),
                'flutterwave_live_secret_masked' => mask_api_secret($fwLive !== '' ? $fwLive : null),
            ]);
        } catch (Throwable $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch payment settings'], 500);
        }
        break;

    case 'POST':
        // Admin-only: test a secret key by making a real request to Paystack or Flutterwave.
        validate_admin_session();

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);
        $provider = isset($input['provider']) ? strtolower(trim((string) $input['provider'])) : 'paystack';
        $apiKey = isset($input['api_key']) ? trim((string) $input['api_key']) : '';
        $useStored = !empty($input['use_stored_secret']);
        $slot = isset($input['secret_slot']) ? trim((string) $input['secret_slot']) : '';

        if ($apiKey === '' && $useStored && $slot !== '') {
            $map = load_app_settings_map($pdo);
            switch ($slot) {
                case 'paystack_test':
                    $apiKey = trim((string) ($map['paystack_test_secret_key'] ?? ''));
                    break;
                case 'paystack_live':
                    $live = trim((string) ($map['paystack_live_secret_key'] ?? ''));
                    $apiKey = $live !== '' ? $live : trim((string) ($map['paystack_secret_key'] ?? ''));
                    break;
                case 'flutterwave_test':
                    $apiKey = trim((string) ($map['flutterwave_test_secret_key'] ?? ''));
                    break;
                case 'flutterwave_live':
                    $apiKey = trim((string) ($map['flutterwave_live_secret_key'] ?? ''));
                    break;
                default:
                    $apiKey = '';
            }
        }

        if ($apiKey === '') {
            json_response(['success' => false, 'message' => 'Paste a secret key to test, or save one first and use Test with an empty field.'], 422);
        }

        if ($provider === 'flutterwave') {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.flutterwave.com/v3/banks/NG',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json',
                    'Cache-Control: no-cache',
                ],
                CURLOPT_TIMEOUT => 12,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                json_response(['success' => true, 'valid' => false, 'message' => 'Connection error while reaching Flutterwave: ' . $curlError], 200);
            }

            if ($httpCode === 200) {
                $decoded = json_decode((string) $response, true);
                $ok = is_array($decoded) && (($decoded['status'] ?? '') === 'success' || !empty($decoded['data']));
                json_response([
                    'success' => true,
                    'valid' => $ok,
                    'message' => $ok ? 'Flutterwave secret key is valid and authorized' : ($decoded['message'] ?? 'Unexpected Flutterwave response'),
                ], 200);
            }

            if ($httpCode === 401) {
                json_response(['success' => true, 'valid' => false, 'message' => 'Invalid or unauthorized Flutterwave secret key'], 200);
            }

            json_response(['success' => true, 'valid' => false, 'message' => 'Flutterwave returned HTTP ' . $httpCode], 200);
            break;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.paystack.co/bank',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Cache-Control: no-cache',
            ],
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            json_response(['success' => true, 'valid' => false, 'message' => 'Connection error while reaching Paystack: ' . $curlError], 200);
        }

        if ($httpCode === 200) {
            json_response(['success' => true, 'valid' => true, 'message' => 'API key is valid and authorized'], 200);
        }

        if ($httpCode === 401) {
            json_response(['success' => true, 'valid' => false, 'message' => 'Invalid or unauthorized Paystack secret key'], 200);
        }

        if ($httpCode === 403) {
            json_response(['success' => true, 'valid' => false, 'message' => 'API key forbidden - check key permissions'], 200);
        }

        json_response(['success' => true, 'valid' => false, 'message' => 'Paystack returned HTTP ' . $httpCode], 200);
        break;

    case 'PUT':
        // Admin-only: update stored keys.
        validate_admin_session();

        $input = json_decode(file_get_contents('php://input') ?: '{}', true);

        $validAny = false;

        // Build update map explicitly to avoid mistakes.
        $update = [];
        if (array_key_exists('test_public_key', $input)) {
            $update['paystack_test_public_key'] = isset($input['test_public_key']) && trim((string) $input['test_public_key']) !== '' ? (string) $input['test_public_key'] : null;
            $validAny = true;
        }
        if (array_key_exists('test_secret_key', $input)) {
            $update['paystack_test_secret_key'] = isset($input['test_secret_key']) && trim((string) $input['test_secret_key']) !== '' ? (string) $input['test_secret_key'] : null;
            $validAny = true;
        }
        if (array_key_exists('live_public_key', $input)) {
            $update['paystack_live_public_key'] = isset($input['live_public_key']) && trim((string) $input['live_public_key']) !== '' ? (string) $input['live_public_key'] : null;
            $validAny = true;
        }
        if (array_key_exists('live_secret_key', $input)) {
            $update['paystack_live_secret_key'] = isset($input['live_secret_key']) && trim((string) $input['live_secret_key']) !== '' ? (string) $input['live_secret_key'] : null;
            $validAny = true;
        }
        if (array_key_exists('use_live', $input)) {
            $useVal = ($input['use_live'] === true || $input['use_live'] === 1 || $input['use_live'] === '1') ? '1' : '0';
            $update['paystack_use_live'] = $useVal;
            $validAny = true;
        }
        if (array_key_exists('paystack_resolve_enabled', $input)) {
            $v = $input['paystack_resolve_enabled'];
            $update['paystack_resolve_enabled'] = ($v === true || $v === 1 || $v === '1') ? '1' : '0';
            $validAny = true;
        }
        if (array_key_exists('flutterwave_resolve_enabled', $input)) {
            $v = $input['flutterwave_resolve_enabled'];
            $update['flutterwave_resolve_enabled'] = ($v === true || $v === 1 || $v === '1') ? '1' : '0';
            $validAny = true;
        }
        if (array_key_exists('flutterwave_test_secret_key', $input)) {
            $update['flutterwave_test_secret_key'] = isset($input['flutterwave_test_secret_key']) && trim((string) $input['flutterwave_test_secret_key']) !== '' ? (string) $input['flutterwave_test_secret_key'] : null;
            $validAny = true;
        }
        if (array_key_exists('flutterwave_live_secret_key', $input)) {
            $update['flutterwave_live_secret_key'] = isset($input['flutterwave_live_secret_key']) && trim((string) $input['flutterwave_live_secret_key']) !== '' ? (string) $input['flutterwave_live_secret_key'] : null;
            $validAny = true;
        }
        if (array_key_exists('flutterwave_test_public_key', $input)) {
            $update['flutterwave_test_public_key'] = isset($input['flutterwave_test_public_key']) && trim((string) $input['flutterwave_test_public_key']) !== '' ? (string) $input['flutterwave_test_public_key'] : null;
            $validAny = true;
        }
        if (array_key_exists('flutterwave_live_public_key', $input)) {
            $update['flutterwave_live_public_key'] = isset($input['flutterwave_live_public_key']) && trim((string) $input['flutterwave_live_public_key']) !== '' ? (string) $input['flutterwave_live_public_key'] : null;
            $validAny = true;
        }

        if (!$validAny) {
            json_response(['success' => false, 'message' => 'No update fields provided'], 422);
        }

        try {
            $pdo->beginTransaction();
            foreach ($update as $k => $v) {
                upsert_setting($pdo, $k, $v);
            }
            $pdo->commit();
            json_response(['success' => true, 'message' => 'Payment settings updated successfully']);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_response(['success' => false, 'message' => 'Failed to update payment settings'], 500);
        }
        break;

    default:
        json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

