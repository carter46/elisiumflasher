<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

function tracker_app_setting_get(PDO $pdo, string $key, ?string $default = null): ?string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row || $row['setting_value'] === null) {
        return $default;
    }
    $val = trim((string) $row['setting_value']);
    return $val === '' ? $default : $val;
}

function tracker_app_setting_set(PDO $pdo, string $key, string $value): void
{
    $stmt = $pdo->prepare('SELECT id FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if ($row) {
        $upd = $pdo->prepare('UPDATE app_settings SET setting_value = ? WHERE setting_key = ?');
        $upd->execute([$value, $key]);
        return;
    }
    $ins = $pdo->prepare('INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?)');
    $ins->execute([$key, $value]);
}

function tracker_api_key_ensure(PDO $pdo): string
{
    $existing = tracker_app_setting_get($pdo, 'tracker_api_key');
    if ($existing !== null && $existing !== '') {
        return $existing;
    }
    $key = bin2hex(random_bytes(24));
    tracker_app_setting_set($pdo, 'tracker_api_key', $key);
    return $key;
}

function tracker_request_api_key(): ?string
{
    $header = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    if (preg_match('/^\s*Bearer\s+(\S+)\s*$/i', $header, $m)) {
        return trim($m[1]);
    }
    $alt = (string) ($_SERVER['HTTP_X_TRACKER_API_KEY'] ?? '');
    if ($alt !== '') {
        return trim($alt);
    }
    return null;
}

/**
 * Preferred: Hub-issued client_secret from Bolt Switch Trace (bst_client_secret).
 * Fallback: legacy locally generated tracker_api_key.
 */
function tracker_expected_inbound_secret(PDO $pdo): string
{
    $hub = tracker_app_setting_get($pdo, 'bst_client_secret');
    if ($hub !== null && $hub !== '') {
        return $hub;
    }
    return tracker_api_key_ensure($pdo);
}

function tracker_require_api_key(PDO $pdo): void
{
    $expected = tracker_expected_inbound_secret($pdo);
    $provided = tracker_request_api_key();
    if ($provided === null || $provided === '' || !hash_equals($expected, $provided)) {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    // When Hub credentials are configured, integration + client ids are required and must match.
    $hubSecret = tracker_app_setting_get($pdo, 'bst_client_secret');
    if ($hubSecret !== null && $hubSecret !== '') {
        $expIntegration = trim((string) (tracker_app_setting_get($pdo, 'bst_integration_id') ?? ''));
        $expClient = trim((string) (tracker_app_setting_get($pdo, 'bst_client_id') ?? ''));
        $gotIntegration = trim((string) ($_SERVER['HTTP_X_TRACKER_INTEGRATION_ID'] ?? ''));
        $gotClient = trim((string) ($_SERVER['HTTP_X_TRACKER_CLIENT_ID'] ?? ''));
        if ($expIntegration === '' || $expClient === '') {
            json_response(['success' => false, 'message' => 'Hub credentials incomplete on this host'], 401);
        }
        if ($gotIntegration === '' || $gotClient === '') {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        if (!hash_equals($expIntegration, $gotIntegration) || !hash_equals($expClient, $gotClient)) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
    }
}

/** Normalize pasted Bolt Switch Trace base URL. */
function tracker_normalize_hub_url(string $url): string
{
    $url = trim($url);
    $url = preg_replace('#/index\.php$#i', '', $url) ?? $url;
    return rtrim($url, '/');
}

/**
 * InfinityFree (and similar) return a JS AES cookie wall instead of the API.
 * Solve __test cookie when the classic toNumbers/slowAES challenge is present.
 */
function tracker_solve_host_firewall_cookie(string $html): ?string
{
    if (!preg_match('/a=toNumbers\("([a-f0-9]+)"\)/i', $html, $ma)) {
        return null;
    }
    if (!preg_match('/b=toNumbers\("([a-f0-9]+)"\)/i', $html, $mb)) {
        return null;
    }
    if (!preg_match('/c=toNumbers\("([a-f0-9]+)"\)/i', $html, $mc)) {
        return null;
    }
    if (!function_exists('openssl_decrypt')) {
        return null;
    }
    $key = @hex2bin($ma[1]);
    $iv = @hex2bin($mb[1]);
    $cipher = @hex2bin($mc[1]);
    if ($key === false || $iv === false || $cipher === false) {
        return null;
    }
    $plain = openssl_decrypt($cipher, 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
    if ($plain === false || $plain === '') {
        $plain = openssl_decrypt($cipher, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }
    if ($plain === false || $plain === '') {
        return null;
    }
    // Cookie value is hex of decrypted bytes (InfinityFree slowAES.toHex).
    return '__test=' . bin2hex($plain);
}

/**
 * POST JSON to Tracer; retry once after solving host firewall cookie challenge.
 * @return array{ok:bool,http:int,raw:string,data:?array,error:?string,url:string}
 */
function tracker_hub_http_post(string $url, array $headers, string $body, ?string $cookie = null, bool $retried = false): array
{
    $ch = curl_init($url);
    $reqHeaders = $headers;
    if ($cookie !== null && $cookie !== '') {
        $reqHeaders[] = 'Cookie: ' . $cookie;
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER => $reqHeaders,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HEADER => true,
    ]);
    $rawFull = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $finalUrl = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);

    if ($errno || $rawFull === false) {
        return [
            'ok' => false,
            'http' => 0,
            'raw' => '',
            'data' => null,
            'error' => 'Network error: ' . ($err ?: 'curl failed'),
            'url' => $url,
        ];
    }

    $rawHeaders = substr((string) $rawFull, 0, $headerSize);
    $raw = substr((string) $rawFull, $headerSize);
    $data = json_decode((string) $raw, true);

    // Host firewall challenge: solve cookie and retry once.
    if (!$retried && !is_array($data) && str_contains((string) $raw, 'toNumbers')) {
        $solved = tracker_solve_host_firewall_cookie((string) $raw);
        if ($solved !== null) {
            if (preg_match('/^Set-Cookie:\s*(__test=[^;\r\n]+)/mi', $rawHeaders, $sm)) {
                $solved = $sm[1];
            }
            return tracker_hub_http_post($url, $headers, $body, $solved, true);
        }
    }

    return [
        'ok' => $http >= 200 && $http < 300 && is_array($data) && !empty($data['ok']),
        'http' => $http,
        'raw' => (string) $raw,
        'data' => is_array($data) ? $data : null,
        'error' => null,
        'url' => $finalUrl !== '' ? $finalUrl : $url,
    ];
}

/** Ping Bolt Switch Trace with stored Hub credentials. */
function tracker_ping_hub(PDO $pdo): array
{
    $hubUrl = tracker_normalize_hub_url((string) (tracker_app_setting_get($pdo, 'bst_tracker_url') ?? ''));
    $integrationId = (string) (tracker_app_setting_get($pdo, 'bst_integration_id') ?? '');
    $clientId = (string) (tracker_app_setting_get($pdo, 'bst_client_id') ?? '');
    $secret = (string) (tracker_app_setting_get($pdo, 'bst_client_secret') ?? '');

    if ($hubUrl === '' || $integrationId === '' || $clientId === '' || $secret === '') {
        return [
            'ok' => false,
            'message' => 'Save Tracker URL, Integration ID, Client ID, and Client secret first.',
            'http' => 0,
        ];
    }

    $url = $hubUrl . '/api/integration_health.php';
    $resp = tracker_hub_http_post(
        $url,
        [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $secret,
            'X-Tracker-Api-Key: ' . $secret,
            'X-Tracker-Integration-Id: ' . $integrationId,
            'X-Tracker-Client-Id: ' . $clientId,
        ],
        json_encode([
            'integration_id' => $integrationId,
            'client_id' => $clientId,
        ], JSON_UNESCAPED_UNICODE)
    );

    if ($resp['error'] !== null) {
        return ['ok' => false, 'message' => $resp['error'], 'http' => 0];
    }

    if (!is_array($resp['data'])) {
        $raw = (string) $resp['raw'];
        if (str_contains($raw, 'toNumbers') || str_contains($raw, 'slowAES')) {
            return [
                'ok' => false,
                'message' => 'Tracer host is blocking API calls with a browser cookie firewall (common on InfinityFree). '
                    . 'Move Tracer to hosting that allows server-to-server requests, or whitelist this server.',
                'http' => (int) $resp['http'],
                'url' => $resp['url'],
            ];
        }
        $snippet = trim(preg_replace('/\s+/', ' ', strip_tags($raw)) ?? '');
        $snippet = $snippet !== '' ? substr($snippet, 0, 120) : '(empty body)';
        return [
            'ok' => false,
            'message' => 'Invalid response from Tracer (HTTP ' . (int) $resp['http'] . '). '
                . 'Check Tracer URL is the site root that contains /api/integration_health.php. '
                . 'Preview: ' . $snippet,
            'http' => (int) $resp['http'],
            'url' => $resp['url'],
        ];
    }

    return [
        'ok' => !empty($resp['ok']),
        'message' => (string) ($resp['data']['message'] ?? (!empty($resp['ok']) ? 'Connected' : 'Rejected')),
        'http' => (int) $resp['http'],
    ];
}

function tracker_json_body(): array
{
    $raw = file_get_contents('php://input') ?: '{}';
    $input = json_decode($raw, true);
    return is_array($input) ? $input : [];
}
