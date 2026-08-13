<?php
declare(strict_types=1);

/**
 * Phase A smoke tests for mobile companion helpers + optional live DB checks.
 *
 * Usage:
 *   php scripts/test_mobile_phase_a.php
 *   php scripts/test_mobile_phase_a.php --live
 *
 * --live requires DB connectivity and migration_mobile_local_transfer.sql applied.
 */

require_once __DIR__ . '/../includes/mobile_auth.php';

$failures = 0;

function assert_true(bool $cond, string $label): void
{
    global $failures;
    if ($cond) {
        echo "[PASS] {$label}\n";
        return;
    }
    $failures++;
    echo "[FAIL] {$label}\n";
}

echo "=== Mobile Phase A helper tests ===\n";

assert_true(mobile_normalize_account(' 012-345-6789 ') === '0123456789', 'normalize account strips non-digits');
assert_true(mobile_is_valid_bank_code('zenith'), 'ZENITH bank code valid');
assert_true(!mobile_is_valid_bank_code('GTBank'), 'unknown bank code rejected');

assert_true(
    mobile_bank_matches('ACCESS', '044', 'Anything'),
    'ACCESS matches by stored code 044'
);
assert_true(
    !mobile_bank_matches('ACCESS', '057', 'Access Bank PLC'),
    'ACCESS rejects wrong stored code even if name says Access'
);
assert_true(
    mobile_bank_matches('ACCESS', null, 'Access Bank PLC'),
    'ACCESS matches legacy name when code null'
);
assert_true(
    mobile_bank_matches('ZENITH', '', 'Zenith Bank'),
    'ZENITH matches legacy name when code empty'
);
assert_true(
    mobile_bank_matches('UBA', null, 'United Bank for Africa'),
    'UBA matches United Bank for Africa'
);
assert_true(
    mobile_bank_matches('FIRST', '011', 'First Bank of Nigeria'),
    'FIRST matches code 011'
);
assert_true(
    !mobile_bank_matches('ZENITH', null, 'Access Bank PLC'),
    'ZENITH does not match Access name'
);

$dto = mobile_receipt_dto([
    'id' => 42,
    'reference' => 'LOC123',
    'amount' => '1500.50',
    'currency' => 'NGN',
    'status' => 'SUCCESSFUL',
    'purpose' => 'RENT',
    'transaction_date' => '2026-08-13 10:00:00',
    'beneficiary_name' => 'Ada Lovelace',
    'beneficiary_bank' => 'Zenith Bank',
    'beneficiary_account' => '1311795728',
    'sender_name' => 'Sender',
    'sender_account' => '1022090307',
    'direction' => 'debit',
], 'ZENITH');

assert_true($dto['transaction_id'] === 'local_transactions:42', 'receipt DTO transaction_id format');
assert_true($dto['direction'] === 'credit', 'receipt DTO maps beneficiary direction to credit');
assert_true($dto['status'] === 'SUCCESSFUL', 'receipt DTO status SUCCESSFUL');
assert_true($dto['bank_code'] === 'ZENITH', 'receipt DTO uses session bank_code');
assert_true(mobile_parse_receipt_id('local_transactions:99') === 99, 'parse compound receipt id');
assert_true(mobile_parse_receipt_id('99') === 99, 'parse numeric receipt id');

$live = in_array('--live', $argv ?? [], true);
if (!$live) {
    echo "\n(Skip live DB tests — pass --live to run against configured database)\n";
    echo $failures === 0 ? "\nAll helper tests passed.\n" : "\n{$failures} helper test(s) failed.\n";
    exit($failures === 0 ? 0 : 1);
}

echo "\n=== Live DB tests ===\n";
$pdo = get_db();

// Schema checks
try {
    $pdo->query('SELECT beneficiary_bank_code FROM local_transactions LIMIT 0');
    assert_true(true, 'column local_transactions.beneficiary_bank_code exists');
} catch (Throwable $e) {
    assert_true(false, 'column local_transactions.beneficiary_bank_code exists');
}

try {
    $pdo->query('SELECT 1 FROM mobile_sessions LIMIT 0');
    assert_true(true, 'table mobile_sessions exists');
} catch (Throwable $e) {
    assert_true(false, 'table mobile_sessions exists');
}

try {
    $pdo->query('SELECT 1 FROM mobile_devices LIMIT 0');
    assert_true(true, 'table mobile_devices exists');
} catch (Throwable $e) {
    assert_true(false, 'table mobile_devices exists');
}

// Ownership filter: only SUCCESSFUL
$probeAccount = '9999999999';
$probeBank = 'ZENITH';

// Clean any leftover probe rows from prior runs
try {
    $pdo->prepare("DELETE FROM local_transactions WHERE beneficiary_account = ? AND reference LIKE 'MOBTEST%'")
        ->execute([$probeAccount]);
} catch (Throwable $e) {
    // ignore
}

$hasCodeCol = true;
try {
    $pdo->query('SELECT beneficiary_bank_code FROM local_transactions LIMIT 0');
} catch (Throwable $e) {
    $hasCodeCol = false;
}

$insertSql = $hasCodeCol
    ? 'INSERT INTO local_transactions (
        reference, amount, currency, beneficiary_name, beneficiary_bank, beneficiary_bank_code,
        beneficiary_account, sender_account, sender_name, purpose, status, direction, transaction_date
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
    : 'INSERT INTO local_transactions (
        reference, amount, currency, beneficiary_name, beneficiary_bank,
        beneficiary_account, sender_account, sender_name, purpose, status, direction, transaction_date
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';

$mk = static function (string $ref, string $status, float $amount) use ($pdo, $insertSql, $hasCodeCol, $probeAccount): void {
    if ($hasCodeCol) {
        $pdo->prepare($insertSql)->execute([
            $ref, $amount, 'NGN', 'Mobile Test User', 'Zenith Bank', '057',
            $probeAccount, '0000000000', 'Sender Test', 'probe', $status, 'debit',
        ]);
    } else {
        $pdo->prepare($insertSql)->execute([
            $ref, $amount, 'NGN', 'Mobile Test User', 'Zenith Bank',
            $probeAccount, '0000000000', 'Sender Test', 'probe', $status, 'debit',
        ]);
    }
};

try {
    $mk('MOBTEST-OK-1', 'SUCCESSFUL', 1000);
    $mk('MOBTEST-FAIL', 'FAILED', 2000);
    $mk('MOBTEST-PEND', 'PENDING', 3000);
    try {
        $mk('MOBTEST-REV', 'REVERSED', 4000);
    } catch (Throwable $e) {
        echo "[WARN] REVERSED insert failed — apply migration status ENUM first\n";
    }

    $count = mobile_count_successful($pdo, $probeBank, $probeAccount);
    assert_true($count === 1, 'ownership count includes only SUCCESSFUL (got ' . $count . ')');

    $bal = mobile_sum_successful_balance($pdo, $probeBank, $probeAccount);
    assert_true(abs($bal - 1000.0) < 0.001, 'balance sums SUCCESSFUL only (got ' . $bal . ')');

    $rows = mobile_fetch_successful_rows($pdo, $probeBank, $probeAccount, 50, 0);
    assert_true(count($rows) === 1, 'history returns only SUCCESSFUL rows');
    assert_true(strtoupper((string) ($rows[0]['status'] ?? '')) === 'SUCCESSFUL', 'returned row is SUCCESSFUL');

    // Wrong bank gate
    $wrong = mobile_count_successful($pdo, 'UBA', $probeAccount);
    assert_true($wrong === 0, 'UBA session cannot see Zenith SUCCESSFUL credit');

    // Wallet PIN configured?
    $hash = mobile_wallet_pin_hash($pdo);
    assert_true($hash !== null && $hash !== '', 'wallet_pin hash present (do not modify)');

    // Session create + load
    $session = mobile_create_session($pdo, $probeBank, $probeAccount, 'Mobile Test User');
    $loaded = mobile_load_session($pdo, $session['token']);
    assert_true($loaded !== null && $loaded['account_number'] === $probeAccount, 'Bearer session create/load works');
    mobile_delete_session($pdo, $session['token']);
} finally {
    $pdo->prepare("DELETE FROM local_transactions WHERE beneficiary_account = ? AND reference LIKE 'MOBTEST%'")
        ->execute([$probeAccount]);
    $pdo->prepare("DELETE FROM mobile_sessions WHERE account_number = ?")->execute([$probeAccount]);
}

echo $failures === 0 ? "\nAll tests passed.\n" : "\n{$failures} test(s) failed.\n";
exit($failures === 0 ? 0 : 1);
