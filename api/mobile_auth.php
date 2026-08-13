<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/mobile_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mobile_json_err('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true);
if (!is_array($input)) {
    mobile_json_err('Invalid JSON body', 400);
}

$action = strtolower(trim((string) ($input['action'] ?? 'login')));
$pdo = get_db();

try {
    if ($action === 'login') {
        $bankCode = strtoupper(trim((string) ($input['bank_code'] ?? '')));
        $accountNumber = mobile_normalize_account((string) ($input['account_number'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if (!mobile_is_valid_bank_code($bankCode)) {
            mobile_json_err('Invalid bank_code', 422);
        }
        if ($accountNumber === '' || strlen($accountNumber) < 10) {
            mobile_json_err('Invalid account_number', 422);
        }
        if ($password === '') {
            mobile_json_err('Password is required', 422);
        }

        $hash = mobile_wallet_pin_hash($pdo);
        if ($hash === null) {
            mobile_json_err('Wallet PIN not configured', 409);
        }
        if (!password_verify($password, $hash)) {
            mobile_json_err('Invalid credentials', 401);
        }

        $identity = mobile_resolve_beneficiary_identity($pdo, $bankCode, $accountNumber);
        if ($identity === null) {
            mobile_json_err('No successful transfers found for this bank account', 403);
        }

        $session = mobile_create_session(
            $pdo,
            $bankCode,
            $identity['account_number'],
            $identity['account_name']
        );
        $balance = mobile_sum_successful_balance($pdo, $bankCode, $identity['account_number']);

        mobile_json_ok([
            'token' => $session['token'],
            'expires_at' => $session['expires_at'],
            'bank_code' => $session['bank_code'],
            'account_number' => $session['account_number'],
            'account_name' => $session['account_name'],
            'balance' => $balance,
        ]);
    }

    if ($action === 'logout') {
        $token = mobile_bearer_token_from_request();
        if ($token !== null && $token !== '') {
            mobile_delete_session($pdo, $token);
        }
        mobile_json_ok(null);
    }

    if ($action === 'check') {
        $session = mobile_require_session($pdo);
        $balance = mobile_sum_successful_balance(
            $pdo,
            $session['bank_code'],
            $session['account_number']
        );
        mobile_json_ok([
            'bank_code' => $session['bank_code'],
            'account_number' => $session['account_number'],
            'account_name' => $session['account_name'],
            'balance' => $balance,
        ]);
    }

    mobile_json_err('Invalid action', 400);
} catch (Throwable $e) {
    mobile_json_err('Authentication failed', 500);
}
