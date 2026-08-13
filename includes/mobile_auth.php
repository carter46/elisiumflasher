<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

/** Mobile bank codes supported by Mobile_app. */
const MOBILE_BANK_CODES = ['UBA', 'FIRST', 'ZENITH', 'ACCESS'];

/** Session lifetime for Bearer tokens (30 days). */
const MOBILE_SESSION_TTL_SECONDS = 60 * 60 * 24 * 30;

/**
 * @return array{codes: string[], name_needles: string[]}
 */
function mobile_bank_map(string $mobileCode): array
{
    $code = strtoupper(trim($mobileCode));
    return match ($code) {
        'UBA' => [
            'codes' => ['033'],
            'name_needles' => ['uba', 'united bank for africa'],
        ],
        'FIRST' => [
            'codes' => ['011'],
            'name_needles' => ['first bank', 'firstbank', 'fbn'],
        ],
        'ZENITH' => [
            'codes' => ['057'],
            'name_needles' => ['zenith'],
        ],
        'ACCESS' => [
            'codes' => ['044'],
            'name_needles' => ['access'],
        ],
        default => [
            'codes' => [],
            'name_needles' => [],
        ],
    };
}

function mobile_normalize_account(string $account): string
{
    return preg_replace('/\D/', '', $account) ?? '';
}

function mobile_is_valid_bank_code(string $bankCode): bool
{
    return in_array(strtoupper(trim($bankCode)), MOBILE_BANK_CODES, true);
}

function mobile_bank_matches(string $mobileCode, ?string $beneficiaryBankCode, ?string $beneficiaryBankName): bool
{
    $map = mobile_bank_map($mobileCode);
    if ($map['codes'] === [] && $map['name_needles'] === []) {
        return false;
    }

    $storedCode = strtoupper(trim((string) ($beneficiaryBankCode ?? '')));
    if ($storedCode !== '') {
        foreach ($map['codes'] as $c) {
            if ($storedCode === strtoupper($c)) {
                return true;
            }
        }
        // Explicit code that does not match → reject (do not fall through to loose name match).
        return false;
    }

    $name = strtolower(trim((string) ($beneficiaryBankName ?? '')));
    if ($name === '') {
        return false;
    }
    foreach ($map['name_needles'] as $needle) {
        if ($needle !== '' && str_contains($name, $needle)) {
            return true;
        }
    }
    return false;
}

function mobile_wallet_pin_hash(PDO $pdo): ?string
{
    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute(['wallet_pin']);
    $row = $stmt->fetch();
    if (!$row || $row['setting_value'] === null) {
        return null;
    }
    $hash = trim((string) $row['setting_value']);
    return $hash !== '' ? $hash : null;
}

/**
 * Same key material as api/wallet_pin.php — used only to verify the admin-set PIN.
 * Does not modify wallet_pin / wallet_pin_enc storage.
 */
function mobile_wallet_pin_enc_key(): string
{
    $material = (defined('DB_PASS') ? (string) DB_PASS : '')
        . '|'
        . (defined('DB_NAME') ? (string) DB_NAME : '')
        . '|elysium_wallet_pin_v1';
    return hash('sha256', $material, true);
}

function mobile_wallet_pin_decrypt_enc(string $blob): ?string
{
    $raw = base64_decode($blob, true);
    if ($raw === false || strlen($raw) < 17) {
        return null;
    }
    $iv = substr($raw, 0, 16);
    $cipher = substr($raw, 16);
    $plain = openssl_decrypt($cipher, 'AES-256-CBC', mobile_wallet_pin_enc_key(), OPENSSL_RAW_DATA, $iv);
    if ($plain === false || !preg_match('/^\d{6}$/', $plain)) {
        return null;
    }
    return $plain;
}

/**
 * Reuse existing Wallet PIN storage: password_verify against app_settings.wallet_pin.
 * Falls back to wallet_pin_enc plaintext compare when hash verify fails (same admin PIN).
 * Does not modify PIN storage or create an alternate credential.
 */
function mobile_verify_wallet_pin(PDO $pdo, string $password): bool
{
    $password = trim($password);
    if ($password === '') {
        return false;
    }

    $hash = mobile_wallet_pin_hash($pdo);
    if ($hash !== null && password_verify($password, $hash)) {
        return true;
    }

    $stmt = $pdo->prepare('SELECT setting_value FROM app_settings WHERE setting_key = ? LIMIT 1');
    $stmt->execute(['wallet_pin_enc']);
    $row = $stmt->fetch();
    if (!$row || $row['setting_value'] === null) {
        return false;
    }
    $plain = mobile_wallet_pin_decrypt_enc(trim((string) $row['setting_value']));
    return $plain !== null && hash_equals($plain, $password);
}

/**
 * Admin Local Account Settings (local_dashboard_profile) — source of truth for sender on receipts.
 *
 * @return array{account_name: string, account_number: string}
 */
function mobile_local_sender_profile(PDO $pdo): array
{
    static $cached = null;
    if (is_array($cached)) {
        return $cached;
    }
    try {
        $row = $pdo->query(
            'SELECT account_name, account_number FROM local_dashboard_profile ORDER BY id DESC LIMIT 1'
        )->fetch();
    } catch (Throwable $e) {
        $row = false;
    }
    $cached = [
        'account_name' => $row ? trim((string) ($row['account_name'] ?? '')) : '',
        'account_number' => $row
            ? mobile_normalize_account((string) ($row['account_number'] ?? ''))
            : '',
    ];
    return $cached;
}

function mobile_sql_digits_expr(string $column): string
{
    // Strip non-digits in SQL for MySQL/MariaDB comparisons.
    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE({$column}, ' ', ''), '-', ''), '/', ''), '.', ''), '+', '')";
}

/**
 * Ownership filter fragments for SUCCESSFUL beneficiary credits.
 *
 * @return array{0: string, 1: list<mixed>} SQL AND-clause + bound params
 */
function mobile_ownership_sql(string $bankCode, string $accountNumber): array
{
    $map = mobile_bank_map($bankCode);
    $digitsCol = mobile_sql_digits_expr('beneficiary_account');
    $params = [$accountNumber];

    $codePlaceholders = [];
    foreach ($map['codes'] as $c) {
        $codePlaceholders[] = '?';
        $params[] = $c;
    }
    $codeSql = $codePlaceholders !== []
        ? 'UPPER(TRIM(COALESCE(beneficiary_bank_code, \'\'))) IN (' . implode(',', $codePlaceholders) . ')'
        : '0=1';

    $nameClauses = [];
    foreach ($map['name_needles'] as $needle) {
        $nameClauses[] = 'LOWER(beneficiary_bank) LIKE ?';
        $params[] = '%' . strtolower($needle) . '%';
    }
    $nameSql = $nameClauses !== [] ? '(' . implode(' OR ', $nameClauses) . ')' : '0=1';

    // Prefer stored bank code when present; else fall back to name patterns for legacy rows.
    $bankSql = "(
        (beneficiary_bank_code IS NOT NULL AND TRIM(beneficiary_bank_code) <> '' AND {$codeSql})
        OR
        ((beneficiary_bank_code IS NULL OR TRIM(beneficiary_bank_code) = '') AND {$nameSql})
    )";

    $sql = "status IN ('SUCCESSFUL','COMPLETED') AND {$digitsCol} = ? AND {$bankSql}";
    return [$sql, $params];
}

/**
 * @return list<array<string, mixed>>
 */
function mobile_fetch_successful_rows(PDO $pdo, string $bankCode, string $accountNumber, ?int $limit = null, int $offset = 0): array
{
    [$where, $params] = mobile_ownership_sql($bankCode, $accountNumber);
    $sql = "SELECT * FROM local_transactions WHERE {$where} ORDER BY transaction_date DESC";
    if ($limit !== null) {
        $sql .= ' LIMIT ? OFFSET ?';
    }
    $stmt = $pdo->prepare($sql);
    $i = 1;
    foreach ($params as $p) {
        $stmt->bindValue($i++, $p);
    }
    if ($limit !== null) {
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, max(0, $offset), PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll() ?: [];
}

function mobile_count_successful(PDO $pdo, string $bankCode, string $accountNumber): int
{
    [$where, $params] = mobile_ownership_sql($bankCode, $accountNumber);
    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM local_transactions WHERE {$where}");
    $stmt->execute($params);
    $row = $stmt->fetch();
    return (int) ($row['c'] ?? 0);
}

function mobile_sum_successful_balance(PDO $pdo, string $bankCode, string $accountNumber): float
{
    [$where, $params] = mobile_ownership_sql($bankCode, $accountNumber);
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS bal FROM local_transactions WHERE {$where}");
    $stmt->execute($params);
    $row = $stmt->fetch();
    return (float) ($row['bal'] ?? 0);
}

/**
 * Latest SUCCESSFUL beneficiary identity for login, or null if none.
 *
 * @return array{account_name: string, account_number: string}|null
 */
function mobile_resolve_beneficiary_identity(PDO $pdo, string $bankCode, string $accountNumber): ?array
{
    $rows = mobile_fetch_successful_rows($pdo, $bankCode, $accountNumber, 1, 0);
    if ($rows === []) {
        return null;
    }
    $row = $rows[0];
    return [
        'account_name' => (string) ($row['beneficiary_name'] ?? ''),
        'account_number' => mobile_normalize_account((string) ($row['beneficiary_account'] ?? $accountNumber)),
    ];
}

/**
 * Map local_transactions row → ReceiptDto expected by Mobile_app.
 * Sender falls back to admin Local Account Settings when tx fields are blank.
 *
 * @param array<string, mixed> $row
 * @return array<string, mixed>
 */
function mobile_receipt_dto(array $row, string $sessionBankCode, ?PDO $pdo = null): array
{
    $id = (int) ($row['id'] ?? 0);
    $reference = isset($row['reference']) ? (string) $row['reference'] : null;

    $senderName = isset($row['sender_name']) ? trim((string) $row['sender_name']) : '';
    $senderAccount = isset($row['sender_account'])
        ? mobile_normalize_account((string) $row['sender_account'])
        : '';
    if ($pdo !== null && ($senderName === '' || $senderAccount === '')) {
        $profile = mobile_local_sender_profile($pdo);
        if ($senderName === '' && $profile['account_name'] !== '') {
            $senderName = $profile['account_name'];
        }
        if ($senderAccount === '' && $profile['account_number'] !== '') {
            $senderAccount = $profile['account_number'];
        }
    }

    $purpose = isset($row['purpose']) ? trim((string) $row['purpose']) : '';

    return [
        'transaction_id' => 'local_transactions:' . $id,
        'source_table' => 'local_transactions',
        'source_id' => $id,
        'bank_code' => strtoupper($sessionBankCode),
        'reference' => $reference,
        'session_id' => $reference,
        'reference_id' => $reference,
        'amount' => (float) ($row['amount'] ?? 0),
        'currency' => (string) ($row['currency'] ?? 'NGN'),
        'status' => strtoupper((string) ($row['status'] ?? 'SUCCESSFUL')),
        'purpose' => $purpose !== '' ? $purpose : null,
        'transaction_date' => isset($row['transaction_date']) ? (string) $row['transaction_date'] : null,
        'beneficiary_name' => isset($row['beneficiary_name']) ? (string) $row['beneficiary_name'] : null,
        'beneficiary_bank' => isset($row['beneficiary_bank']) ? (string) $row['beneficiary_bank'] : null,
        'beneficiary_account' => isset($row['beneficiary_account'])
            ? mobile_normalize_account((string) $row['beneficiary_account'])
            : null,
        'sender_name' => $senderName !== '' ? $senderName : null,
        'sender_account' => $senderAccount !== '' ? $senderAccount : null,
        'sender_bank' => null,
        // Beneficiary view: credit (stored web direction remains debit for sender).
        'direction' => 'credit',
    ];
}

function mobile_bearer_token_from_request(): ?string
{
    $header = '';
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $header = (string) $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $header = (string) $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        foreach ($headers as $k => $v) {
            if (strtolower((string) $k) === 'authorization') {
                $header = (string) $v;
                break;
            }
        }
    }

    if ($header === '') {
        return null;
    }
    if (preg_match('/^\s*Bearer\s+(\S+)\s*$/i', $header, $m)) {
        return $m[1];
    }
    return null;
}

/**
 * @return array{id:int, token:string, bank_code:string, account_number:string, account_name:string, expires_at:string}|null
 */
function mobile_load_session(PDO $pdo, ?string $token = null): ?array
{
    $token = $token ?? mobile_bearer_token_from_request();
    if ($token === null || $token === '') {
        return null;
    }

    $stmt = $pdo->prepare('
        SELECT id, token, bank_code, account_number, account_name, expires_at
        FROM mobile_sessions
        WHERE token = ?
        LIMIT 1
    ');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    $expiresAt = strtotime((string) $row['expires_at']);
    if ($expiresAt === false || $expiresAt < time()) {
        $del = $pdo->prepare('DELETE FROM mobile_sessions WHERE id = ?');
        $del->execute([(int) $row['id']]);
        return null;
    }

    return [
        'id' => (int) $row['id'],
        'token' => (string) $row['token'],
        'bank_code' => strtoupper((string) $row['bank_code']),
        'account_number' => mobile_normalize_account((string) $row['account_number']),
        'account_name' => (string) $row['account_name'],
        'expires_at' => (string) $row['expires_at'],
    ];
}

/**
 * @return array{id:int, token:string, bank_code:string, account_number:string, account_name:string, expires_at:string}
 */
function mobile_require_session(PDO $pdo): array
{
    $session = mobile_load_session($pdo);
    if ($session === null) {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    return $session;
}

/**
 * Require a valid Bearer session AND at least one SUCCESSFUL/COMPLETED owned transfer.
 * Policy (documented): if eligibility is lost after login (e.g. only tx flipped to FAILED),
 * revoke the session and return 401 so mobile matches login rules.
 *
 * @return array{id:int, token:string, bank_code:string, account_number:string, account_name:string, expires_at:string}
 */
function mobile_require_eligible_session(PDO $pdo): array
{
    $session = mobile_require_session($pdo);
    $count = mobile_count_successful($pdo, $session['bank_code'], $session['account_number']);
    if ($count <= 0) {
        mobile_delete_session($pdo, $session['token']);
        mobile_delete_devices_for_account($pdo, $session['bank_code'], $session['account_number']);
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    return $session;
}

function mobile_create_session(
    PDO $pdo,
    string $bankCode,
    string $accountNumber,
    string $accountName
): array {
    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + MOBILE_SESSION_TTL_SECONDS);

    $stmt = $pdo->prepare('
        INSERT INTO mobile_sessions (token, bank_code, account_number, account_name, expires_at)
        VALUES (?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $token,
        strtoupper($bankCode),
        mobile_normalize_account($accountNumber),
        $accountName,
        $expiresAt,
    ]);

    return [
        'token' => $token,
        'expires_at' => $expiresAt,
        'bank_code' => strtoupper($bankCode),
        'account_number' => mobile_normalize_account($accountNumber),
        'account_name' => $accountName,
    ];
}

function mobile_delete_session(PDO $pdo, string $token): void
{
    $stmt = $pdo->prepare('DELETE FROM mobile_sessions WHERE token = ?');
    $stmt->execute([$token]);
}

function mobile_delete_devices_for_account(PDO $pdo, string $bankCode, string $accountNumber): void
{
    $stmt = $pdo->prepare('DELETE FROM mobile_devices WHERE bank_code = ? AND account_number = ?');
    $stmt->execute([strtoupper($bankCode), mobile_normalize_account($accountNumber)]);
}

/**
 * Parse receipt id: "local_transactions:123" or bare numeric id.
 */
function mobile_parse_receipt_id(string $id): ?int
{
    $id = trim($id);
    if ($id === '') {
        return null;
    }
    if (preg_match('/^local_transactions:(\d+)$/i', $id, $m)) {
        return (int) $m[1];
    }
    if (ctype_digit($id)) {
        return (int) $id;
    }
    return null;
}

/**
 * @return array<string, mixed>|null
 */
function mobile_fetch_owned_transaction(PDO $pdo, string $bankCode, string $accountNumber, int $sourceId): ?array
{
    [$where, $params] = mobile_ownership_sql($bankCode, $accountNumber);
    $sql = "SELECT * FROM local_transactions WHERE id = ? AND {$where} LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge([$sourceId], $params));
    $row = $stmt->fetch();
    return $row ?: null;
}

function mobile_json_ok(mixed $data, int $status = 200): void
{
    json_response(['success' => true, 'data' => $data], $status);
}

function mobile_json_err(string $message, int $status = 400): void
{
    json_response(['success' => false, 'message' => $message], $status);
}
