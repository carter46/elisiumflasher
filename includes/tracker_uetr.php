<?php
declare(strict_types=1);

/**
 * UUID v4 UETR helpers (36-char hyphenated form).
 */

function tracker_uetr_is_valid(string $uetr): bool
{
    return (bool) preg_match(
        '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
        trim($uetr)
    );
}

function tracker_uetr_normalize(string $uetr): string
{
    return strtolower(trim($uetr));
}

function tracker_uetr_generate(): string
{
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    $hex = bin2hex($bytes);
    return sprintf(
        '%s-%s-%s-%s-%s',
        substr($hex, 0, 8),
        substr($hex, 8, 4),
        substr($hex, 12, 4),
        substr($hex, 16, 4),
        substr($hex, 20, 12)
    );
}

/**
 * Generate a UETR that does not already exist in the given table.column.
 */
function tracker_uetr_generate_unique(PDO $pdo, string $table, string $column = 'uetr', int $maxAttempts = 8): string
{
    $table = preg_replace('/[^a-z0-9_]/i', '', $table) ?: 'local_transactions';
    $column = preg_replace('/[^a-z0-9_]/i', '', $column) ?: 'uetr';
    for ($i = 0; $i < $maxAttempts; $i++) {
        $uetr = tracker_uetr_generate();
        $stmt = $pdo->prepare("SELECT 1 FROM {$table} WHERE {$column} = ? LIMIT 1");
        $stmt->execute([$uetr]);
        if (!$stmt->fetch()) {
            return $uetr;
        }
    }
    throw new RuntimeException('Unable to allocate unique UETR');
}

function tracker_mask_account(?string $value, int $visible = 4): string
{
    $digits = preg_replace('/\s+/', '', (string) $value);
    if ($digits === '') {
        return '--';
    }
    $len = strlen($digits);
    if ($len <= $visible) {
        return str_repeat('*', max(0, $len - 1)) . substr($digits, -1);
    }
    return str_repeat('*', $len - $visible) . substr($digits, -$visible);
}
