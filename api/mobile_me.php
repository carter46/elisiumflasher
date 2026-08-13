<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/mobile_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    mobile_json_err('Method not allowed', 405);
}

$pdo = get_db();

try {
    $session = mobile_require_eligible_session($pdo);
    $bankCode = $session['bank_code'];
    $accountNumber = $session['account_number'];

    $balance = mobile_sum_successful_balance($pdo, $bankCode, $accountNumber);
    $count = mobile_count_successful($pdo, $bankCode, $accountNumber);
    $recentRows = mobile_fetch_successful_rows($pdo, $bankCode, $accountNumber, 10, 0);
    $recent = [];
    foreach ($recentRows as $row) {
        $recent[] = mobile_receipt_dto($row, $bankCode);
    }

    mobile_json_ok([
        'bank_code' => $bankCode,
        'account_number' => $accountNumber,
        'account_name' => $session['account_name'],
        'balance' => $balance,
        'balance_rule' => 'sum_successful_credits',
        'transaction_count' => $count,
        'recent_transactions' => $recent,
    ]);
} catch (Throwable $e) {
    mobile_json_err('Failed to load profile', 500);
}
