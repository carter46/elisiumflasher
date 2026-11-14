<?php
declare(strict_types=1);

require_once __DIR__ . '/tracker_status.php';
require_once __DIR__ . '/tracker_uetr.php';
require_once __DIR__ . '/tracker_api_auth.php';

function tracker_table_has_column(PDO $pdo, string $table, string $column): bool
{
    static $cache = [];
    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    try {
        $tableSafe = preg_replace('/[^a-z0-9_]/i', '', $table);
        $cols = $pdo->query('SHOW COLUMNS FROM `' . $tableSafe . '`')->fetchAll(PDO::FETCH_COLUMN);
        $cols = array_map('strtolower', $cols ?: []);
        $cache[$key] = in_array(strtolower($column), $cols, true);
    } catch (Throwable $e) {
        $cache[$key] = false;
    }
    return $cache[$key];
}

function tracker_ensure_tx_columns(PDO $pdo, string $table): void
{
    $tableSafe = preg_replace('/[^a-z0-9_]/i', '', $table);
    if (!tracker_table_has_column($pdo, $tableSafe, 'uetr')) {
        try {
            $pdo->exec("ALTER TABLE `{$tableSafe}` ADD COLUMN uetr CHAR(36) NULL AFTER reference");
        } catch (Throwable $e) {
            // ignore
        }
    }
    if (!tracker_table_has_column($pdo, $tableSafe, 'tracker_status')) {
        try {
            $after = tracker_table_has_column($pdo, $tableSafe, 'status') ? ' AFTER status' : '';
            $pdo->exec("ALTER TABLE `{$tableSafe}` ADD COLUMN tracker_status VARCHAR(16) NOT NULL DEFAULT 'PDNG'{$after}");
        } catch (Throwable $e) {
            // ignore
        }
    }
    if (!tracker_table_has_column($pdo, $tableSafe, 'tracker_status_updated_at')) {
        try {
            $pdo->exec("ALTER TABLE `{$tableSafe}` ADD COLUMN tracker_status_updated_at TIMESTAMP NULL DEFAULT NULL AFTER tracker_status");
        } catch (Throwable $e) {
            // ignore
        }
    }
    try {
        $idxName = 'uq_' . $tableSafe . '_uetr';
        $idx = $pdo->query("SHOW INDEX FROM `{$tableSafe}` WHERE Key_name = " . $pdo->quote($idxName))->fetch();
        if (!$idx) {
            $pdo->exec("ALTER TABLE `{$tableSafe}` ADD UNIQUE KEY `{$idxName}` (uetr)");
        }
    } catch (Throwable $e) {
        // ignore
    }
}

function tracker_backfill_uetr(PDO $pdo, string $table): void
{
    $tableSafe = preg_replace('/[^a-z0-9_]/i', '', $table);
    try {
        $deadline = microtime(true) + 1.5;
        do {
            $missing = $pdo->query("SELECT id FROM `{$tableSafe}` WHERE uetr IS NULL OR uetr = '' LIMIT 500")->fetchAll();
            if (!$missing) {
                break;
            }
            foreach ($missing as $row) {
                $uetr = tracker_uetr_generate_unique($pdo, $tableSafe, 'uetr');
                $pdo->prepare("UPDATE `{$tableSafe}` SET uetr = ? WHERE id = ? AND (uetr IS NULL OR uetr = '')")
                    ->execute([$uetr, (int) $row['id']]);
            }
        } while (microtime(true) < $deadline);
    } catch (Throwable $e) {
        // ignore
    }
}

/**
 * Schema ensure for Elysium Flasher (local only).
 */
function tracker_flasher_ensure_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    tracker_ensure_tx_columns($pdo, 'local_transactions');
    tracker_api_key_ensure($pdo);

    if (tracker_app_setting_get($pdo, 'local_tracker_status') === null) {
        tracker_app_setting_set($pdo, 'local_tracker_status', 'PDNG');
    }

    tracker_backfill_uetr($pdo, 'local_transactions');
}

function tracker_find_local_by_uetr(PDO $pdo, string $uetr): ?array
{
    tracker_flasher_ensure_schema($pdo);
    $uetr = tracker_uetr_normalize($uetr);
    $stmt = $pdo->prepare('SELECT * FROM local_transactions WHERE uetr = ? LIMIT 1');
    $stmt->execute([$uetr]);
    $row = $stmt->fetch();
    return $row ?: null;
}
