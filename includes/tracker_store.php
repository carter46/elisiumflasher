<?php
declare(strict_types=1);

require_once __DIR__ . '/tracker_status.php';
require_once __DIR__ . '/tracker_uetr.php';
require_once __DIR__ . '/tracker_api_auth.php';

/**
 * Best-effort schema ensure for Flasher (local only).
 */
function tracker_flasher_ensure_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    try {
        $cols = $pdo->query('SHOW COLUMNS FROM local_transactions')->fetchAll(PDO::FETCH_COLUMN);
        $cols = array_map('strtolower', $cols ?: []);
        if (!in_array('uetr', $cols, true)) {
            $pdo->exec("ALTER TABLE local_transactions ADD COLUMN uetr CHAR(36) NULL AFTER reference");
        }
        if (!in_array('tracker_status', $cols, true)) {
            $pdo->exec("ALTER TABLE local_transactions ADD COLUMN tracker_status VARCHAR(16) NOT NULL DEFAULT 'PDNG' AFTER status");
        }
        if (!in_array('tracker_status_updated_at', $cols, true)) {
            $pdo->exec("ALTER TABLE local_transactions ADD COLUMN tracker_status_updated_at TIMESTAMP NULL DEFAULT NULL AFTER tracker_status");
        }
        // Unique index if missing
        $idx = $pdo->query("SHOW INDEX FROM local_transactions WHERE Key_name = 'uq_local_transactions_uetr'")->fetch();
        if (!$idx) {
            try {
                $pdo->exec('ALTER TABLE local_transactions ADD UNIQUE KEY uq_local_transactions_uetr (uetr)');
            } catch (Throwable $e) {
                // ignore if duplicates temporarily block
            }
        }
    } catch (Throwable $e) {
        // table may not exist during early boot
    }

    tracker_api_key_ensure($pdo);
    if (tracker_app_setting_get($pdo, 'local_tracker_status') === null) {
        tracker_app_setting_set($pdo, 'local_tracker_status', 'PDNG');
    }

    // Backfill null UETRs one-by-one (safe across MySQL versions)
    try {
        // Batch backfill; repeat within a time budget for larger legacy tables.
        $deadline = microtime(true) + 1.5;
        do {
            $missing = $pdo->query("SELECT id FROM local_transactions WHERE uetr IS NULL OR uetr = '' LIMIT 500")->fetchAll();
            if (!$missing) {
                break;
            }
            foreach ($missing as $row) {
                $uetr = tracker_uetr_generate_unique($pdo, 'local_transactions', 'uetr');
                $pdo->prepare('UPDATE local_transactions SET uetr = ? WHERE id = ? AND (uetr IS NULL OR uetr = \'\')')
                    ->execute([$uetr, (int) $row['id']]);
            }
        } while (microtime(true) < $deadline);
    } catch (Throwable $e) {
        // ignore
    }
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
