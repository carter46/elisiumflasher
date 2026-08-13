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

        // Eligibility before PIN to avoid confirming PIN validity when no eligible txs exist.
        // Same generic 401 for bad PIN or no eligible beneficiary transfers.
        $identity = mobile_resolve_beneficiary_identity($pdo, $bankCode, $accountNumber);
        if ($identity === null || !password_verify($password, $hash)) {
            mobile_json_err('Invalid credentials', 401);
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
        $session = $token !== null && $token !== '' ? mobile_load_session($pdo, $token) : null;
        if ($session !== null) {
            mobile_delete_devices_for_account($pdo, $session['bank_code'], $session['account_number']);
            mobile_delete_session($pdo, $session['token']);
        } elseif ($token !== null && $token !== '') {
            mobile_delete_session($pdo, $token);
        }
        mobile_json_ok(null);
    }

    if ($action === 'check') {
        // Re-check eligibility; revoke session if no SUCCESSFUL/COMPLETED owned txs remain.
        $session = mobile_require_eligible_session($pdo);
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
