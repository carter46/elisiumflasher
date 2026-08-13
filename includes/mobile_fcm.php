<?php
declare(strict_types=1);

/**
 * Direct FCM HTTP v1 helpers for mobile companion.
 *
 * Requires in config.php (or env):
 *   FCM_PROJECT_ID
 *   FCM_SERVICE_ACCOUNT_JSON  — absolute/relative filesystem path to service-account JSON
 *
 * Never accepts Firebase secrets from the admin UI or request body.
 */

require_once __DIR__ . '/mobile_auth.php';

function mobile_fcm_project_id(): string
{
    if (defined('FCM_PROJECT_ID') && is_string(FCM_PROJECT_ID) && FCM_PROJECT_ID !== '') {
        return (string) FCM_PROJECT_ID;
    }
    $env = getenv('FCM_PROJECT_ID');
    return is_string($env) ? trim($env) : '';
}

function mobile_fcm_service_account_path(): string
{
    if (defined('FCM_SERVICE_ACCOUNT_JSON') && is_string(FCM_SERVICE_ACCOUNT_JSON) && FCM_SERVICE_ACCOUNT_JSON !== '') {
        return (string) FCM_SERVICE_ACCOUNT_JSON;
    }
    $env = getenv('FCM_SERVICE_ACCOUNT_JSON');
    return is_string($env) ? trim($env) : '';
}

function mobile_fcm_is_configured(): bool
{
    $project = mobile_fcm_project_id();
    $path = mobile_fcm_service_account_path();
    return $project !== '' && $path !== '' && is_readable($path);
}

/**
 * Map NUBAN / bank name on a local_transactions row to mobile BankCode.
 */
function mobile_fcm_mobile_bank_code_from_row(array $row): ?string
{
    foreach (MOBILE_BANK_CODES as $code) {
        if (mobile_bank_matches(
            $code,
            isset($row['beneficiary_bank_code']) ? (string) $row['beneficiary_bank_code'] : null,
            isset($row['beneficiary_bank']) ? (string) $row['beneficiary_bank'] : null
        )) {
            return $code;
        }
    }
    return null;
}

/**
 * @return array<string, mixed>|null
 */
function mobile_fcm_load_service_account(): ?array
{
    $path = mobile_fcm_service_account_path();
    if ($path === '' || !is_readable($path)) {
        return null;
    }
    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return null;
    }
    $json = json_decode($raw, true);
    return is_array($json) ? $json : null;
}

function mobile_fcm_base64url(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function mobile_fcm_access_token(): ?string
{
    static $cached = null;
    static $cachedExp = 0;

    if (is_string($cached) && $cached !== '' && time() < ($cachedExp - 60)) {
        return $cached;
    }

    $sa = mobile_fcm_load_service_account();
    if ($sa === null) {
        return null;
    }
    $clientEmail = (string) ($sa['client_email'] ?? '');
    $privateKey = (string) ($sa['private_key'] ?? '');
    $tokenUri = (string) ($sa['token_uri'] ?? 'https://oauth2.googleapis.com/token');
    if ($clientEmail === '' || $privateKey === '') {
        return null;
    }

    $now = time();
    $header = mobile_fcm_base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_UNESCAPED_SLASHES) ?: '{}');
    $claim = mobile_fcm_base64url(json_encode([
        'iss' => $clientEmail,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => $tokenUri,
        'iat' => $now,
        'exp' => $now + 3600,
    ], JSON_UNESCAPED_SLASHES) ?: '{}');
    $unsigned = $header . '.' . $claim;

    $signature = '';
    $ok = openssl_sign($unsigned, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    if (!$ok) {
        return null;
    }
    $jwt = $unsigned . '.' . mobile_fcm_base64url($signature);

    $ch = curl_init($tokenUri);
    if ($ch === false) {
        return null;
    }
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]),
        CURLOPT_TIMEOUT => 15,
    ]);
    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if (!is_string($body) || $status < 200 || $status >= 300) {
        return null;
    }
    $parsed = json_decode($body, true);
    $token = is_array($parsed) ? (string) ($parsed['access_token'] ?? '') : '';
    if ($token === '') {
        return null;
    }
    $cached = $token;
    $cachedExp = $now + (int) ($parsed['expires_in'] ?? 3600);
    return $cached;
}

/**
 * Send one FCM data+notification message. Returns true on HTTP success.
 */
function mobile_fcm_send(string $deviceToken, array $notification, array $data): bool
{
    if (!mobile_fcm_is_configured()) {
        return false;
    }
    $access = mobile_fcm_access_token();
    if ($access === null) {
        return false;
    }
    $projectId = mobile_fcm_project_id();
    $url = 'https://fcm.googleapis.com/v1/projects/' . rawurlencode($projectId) . '/messages:send';

    // FCM data payload values must be strings.
    $dataStr = [];
    foreach ($data as $k => $v) {
        $dataStr[(string) $k] = (string) $v;
    }

    $payload = [
        'message' => [
            'token' => $deviceToken,
            'notification' => $notification,
            'data' => $dataStr,
            'android' => [
                'priority' => 'HIGH',
            ],
        ],
    ];

    $ch = curl_init($url);
    if ($ch === false) {
        return false;
    }
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $access,
            'Content-Type: application/json; charset=utf-8',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT => 15,
    ]);
    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return is_string($body) && $status >= 200 && $status < 300;
}

/**
 * After a SUCCESSFUL local transfer commit: notify registered device if any.
 * No-ops when FCM is not configured or no device token. Never throws to caller path ideally.
 *
 * @param array<string, mixed> $tx local_transactions row
 */
function mobile_fcm_notify_successful_transfer(PDO $pdo, array $tx): void
{
    if (strtoupper((string) ($tx['status'] ?? '')) !== 'SUCCESSFUL'
        && strtoupper((string) ($tx['status'] ?? '')) !== 'COMPLETED') {
        return;
    }
    if (!mobile_fcm_is_configured()) {
        return;
    }

    $mobileBank = mobile_fcm_mobile_bank_code_from_row($tx);
    if ($mobileBank === null) {
        return;
    }
    $account = mobile_normalize_account((string) ($tx['beneficiary_account'] ?? ''));
    if ($account === '') {
        return;
    }

    $stmt = $pdo->prepare('
        SELECT fcm_token FROM mobile_devices
        WHERE bank_code = ? AND account_number = ?
        LIMIT 1
    ');
    $stmt->execute([$mobileBank, $account]);
    $dev = $stmt->fetch();
    if (!$dev) {
        return;
    }
    $token = trim((string) ($dev['fcm_token'] ?? ''));
    if ($token === '' || str_starts_with($token, 'ExponentPushToken[') || str_starts_with($token, 'ExpoPushToken[')) {
        return;
    }

    $txId = (int) ($tx['id'] ?? 0);
    $transactionId = 'local_transactions:' . $txId;
    $amount = number_format((float) ($tx['amount'] ?? 0), 2, '.', ',');
    $currency = (string) ($tx['currency'] ?? 'NGN');

    mobile_fcm_send(
        $token,
        [
            'title' => 'Credit received',
            'body' => "{$currency} {$amount} credited to your account",
        ],
        [
            'transaction_id' => $transactionId,
            'source_table' => 'local_transactions',
            'source_id' => (string) $txId,
            'open' => 'receipt',
            'type' => 'credit',
        ]
    );
}
