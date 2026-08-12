<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
$accountNumber = preg_replace('/\D/', '', (string) ($input['account_number'] ?? ''));
$bankCode = trim((string) ($input['bank_code'] ?? ''));

if (strlen($accountNumber) !== 10 || $bankCode === '') {
    json_response(['success' => false, 'message' => 'Valid account number and bank code are required'], 422);
}

$useLive = app_setting('paystack_use_live') === '1';

$paystackResolveOn = app_setting('paystack_resolve_enabled');
if ($paystackResolveOn === null || $paystackResolveOn === '') {
    $paystackResolveOn = '1';
}
$paystackResolveOn = $paystackResolveOn === '1';

$flutterwaveResolveOn = app_setting('flutterwave_resolve_enabled') === '1';

if (!$paystackResolveOn && !$flutterwaveResolveOn) {
    json_response(['success' => false, 'message' => 'Account resolution is disabled. Enable Paystack or Flutterwave in admin payment settings.'], 400);
}

function resolve_paystack_secret(bool $useLive): ?string
{
    $secretKey = $useLive ? app_setting('paystack_live_secret_key') : app_setting('paystack_test_secret_key');
    if (!$secretKey) {
        $secretKey = app_setting('paystack_secret_key');
    }
    $s = $secretKey !== null ? trim($secretKey) : '';
    return $s !== '' ? $s : null;
}

function resolve_flutterwave_secret(bool $useLive): ?string
{
    $secretKey = $useLive ? app_setting('flutterwave_live_secret_key') : app_setting('flutterwave_test_secret_key');
    $s = $secretKey !== null ? trim((string) $secretKey) : '';
    return $s !== '' ? $s : null;
}

/**
 * @return array{success:bool, account_name?:string, account_number?:string, bank_id?:null|int, message?:string}
 */
function try_paystack_resolve(string $accountNumber, string $bankCode, string $secretKey): array
{
    $url = 'https://api.paystack.co/bank/resolve?account_number=' . rawurlencode($accountNumber) . '&bank_code=' . rawurlencode($bankCode);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $secretKey,
            'Cache-Control: no-cache',
        ],
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['success' => false, 'message' => 'Unable to reach Paystack'];
    }

    $data = json_decode((string) $response, true);
    if ($httpCode === 401) {
        return ['success' => false, 'message' => 'Invalid Paystack key (401)'];
    }
    if (!is_array($data)) {
        return ['success' => false, 'message' => 'Invalid response from Paystack'];
    }
    if ($httpCode !== 200 || empty($data['status']) || empty($data['data']['account_name'])) {
        return ['success' => false, 'message' => $data['message'] ?? 'Account resolution failed'];
    }

    return [
        'success' => true,
        'account_name' => $data['data']['account_name'],
        'account_number' => $data['data']['account_number'] ?? $accountNumber,
        'bank_id' => $data['data']['bank_id'] ?? null,
    ];
}

/**
 * @return array{success:bool, account_name?:string, account_number?:string, bank_id?:null, message?:string}
 */
function try_flutterwave_resolve(string $accountNumber, string $bankCode, string $secretKey): array
{
    $payload = json_encode([
        'account_number' => $accountNumber,
        'account_bank' => $bankCode,
    ]);
    if ($payload === false) {
        return ['success' => false, 'message' => 'Invalid request payload'];
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.flutterwave.com/v3/accounts/resolve',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $secretKey,
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        ],
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['success' => false, 'message' => 'Unable to reach Flutterwave'];
    }

    $data = json_decode((string) $response, true);
    if ($httpCode === 401) {
        return ['success' => false, 'message' => 'Invalid Flutterwave key (401)'];
    }

    $ok = is_array($data)
        && (($data['status'] ?? '') === 'success')
        && !empty($data['data']['account_name']);

    if (!$ok) {
        $msg = is_array($data) ? ($data['message'] ?? 'Account resolution failed') : 'Account resolution failed';

        return ['success' => false, 'message' => $msg];
    }

    return [
        'success' => true,
        'account_name' => $data['data']['account_name'],
        'account_number' => $data['data']['account_number'] ?? $accountNumber,
        'bank_id' => null,
    ];
}

$paystackSecret = resolve_paystack_secret($useLive);
$flutterwaveSecret = resolve_flutterwave_secret($useLive);

$lastMessage = 'Account resolution failed';

if ($paystackResolveOn && $paystackSecret !== null) {
    $r = try_paystack_resolve($accountNumber, $bankCode, $paystackSecret);
    if (!empty($r['success'])) {
        json_response([
            'success' => true,
            'data' => [
                'account_name' => $r['account_name'],
                'account_number' => $r['account_number'] ?? $accountNumber,
                'bank_id' => $r['bank_id'] ?? null,
            ],
        ]);
    }
    $lastMessage = $r['message'] ?? $lastMessage;
}

if ($flutterwaveResolveOn && $flutterwaveSecret !== null) {
    $r = try_flutterwave_resolve($accountNumber, $bankCode, $flutterwaveSecret);
    if (!empty($r['success'])) {
        json_response([
            'success' => true,
            'data' => [
                'account_name' => $r['account_name'],
                'account_number' => $r['account_number'] ?? $accountNumber,
                'bank_id' => $r['bank_id'] ?? null,
            ],
        ]);
    }
    $lastMessage = $r['message'] ?? $lastMessage;
}

if ($paystackResolveOn && $paystackSecret === null && $flutterwaveResolveOn && $flutterwaveSecret === null) {
    json_response(['success' => false, 'message' => 'No payment provider secret keys configured for account resolution'], 500);
}

if ($paystackResolveOn && !$flutterwaveResolveOn && $paystackSecret === null) {
    json_response(['success' => false, 'message' => 'Paystack secret key not configured'], 500);
}

if (!$paystackResolveOn && $flutterwaveResolveOn && $flutterwaveSecret === null) {
    json_response(['success' => false, 'message' => 'Flutterwave secret key not configured'], 500);
}

json_response(['success' => false, 'message' => $lastMessage], 422);
