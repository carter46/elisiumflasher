<?php
declare(strict_types=1);

/**
 * Idempotent database auto-migrator (Axion-style).
 * Runs pending PHP migrations from database/auto-migrations/ once per request
 * when an admin logs in or loads any admin page/API.
 *
 * Add files named like: 2026_11_13_180000_short_name.php returning:
 *   ['id' => '...', 'description' => '...', 'up' => function (PDO $pdo): void { ... }]
 */

require_once __DIR__ . '/../config.php';

class DatabaseAutoMigrate
{
    private static bool $ranThisRequest = false;

    private PDO $pdo;
    private string $dir;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? get_db();
        $this->dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'auto-migrations';
    }

    /**
     * @return array{ran:bool,applied:list<array>,failed:list<array>,skipped:int,errors:list<string>}
     */
    public function run(?int $appliedBy = null): array
    {
        if (self::$ranThisRequest) {
            return $_SESSION['auto_migration_last_result'] ?? [
                'ran' => false,
                'applied' => [],
                'failed' => [],
                'skipped' => 0,
                'errors' => [],
            ];
        }
        self::$ranThisRequest = true;

        if (session_status() !== PHP_SESSION_ACTIVE) {
            start_app_session();
        }

        $result = [
            'ran' => true,
            'applied' => [],
            'failed' => [],
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            $this->ensureTrackingTable();
            $migrations = $this->discoverMigrations();
            $appliedIds = $this->getSuccessfullyAppliedIds();

            foreach ($migrations as $migration) {
                $id = (string) $migration['id'];
                if (isset($appliedIds[$id])) {
                    $result['skipped']++;
                    continue;
                }

                try {
                    $up = $migration['up'];
                    if (!is_callable($up)) {
                        throw new RuntimeException('Migration up() is not callable');
                    }
                    $up($this->pdo);
                    $this->recordSuccess($id, (string) ($migration['description'] ?? $id), $appliedBy);
                    $result['applied'][] = [
                        'id' => $id,
                        'description' => (string) ($migration['description'] ?? $id),
                    ];
                } catch (Throwable $e) {
                    $msg = $e->getMessage();
                    $this->recordFailure($id, (string) ($migration['description'] ?? $id), $msg, $appliedBy);
                    $result['failed'][] = [
                        'id' => $id,
                        'description' => (string) ($migration['description'] ?? $id),
                        'error' => $msg,
                    ];
                    $result['errors'][] = "{$id}: {$msg}";
                    error_log("Auto-migration failed [{$id}]: {$msg}");
                }
            }
        } catch (Throwable $e) {
            $result['errors'][] = 'Migrator bootstrap failed: ' . $e->getMessage();
            error_log('DatabaseAutoMigrate bootstrap error: ' . $e->getMessage());
        }

        $_SESSION['auto_migration_last_result'] = $result;
        if (!empty($result['failed']) || !empty($result['errors'])) {
            $_SESSION['auto_migration_errors'] = $result['errors'];
        } else {
            unset($_SESSION['auto_migration_errors']);
        }
        if (!empty($result['applied'])) {
            $_SESSION['auto_migration_success'] = array_map(
                static fn (array $row): string => ($row['description'] ?? $row['id']) . ' (' . $row['id'] . ')',
                $result['applied']
            );
        }

        return $result;
    }

    private function ensureTrackingTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `auto_migrations` (
                `id` VARCHAR(191) NOT NULL,
                `description` VARCHAR(255) DEFAULT NULL,
                `status` ENUM('success','failed') NOT NULL DEFAULT 'success',
                `error_message` TEXT DEFAULT NULL,
                `applied_by` INT DEFAULT NULL,
                `applied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_status` (`status`),
                KEY `idx_applied_at` (`applied_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    /** @return array<string,true> */
    private function getSuccessfullyAppliedIds(): array
    {
        $ids = [];
        $stmt = $this->pdo->query("SELECT id FROM auto_migrations WHERE status = 'success'");
        foreach ($stmt->fetchAll() ?: [] as $row) {
            $ids[(string) $row['id']] = true;
        }
        return $ids;
    }

    /** @return list<array{id:string,description?:string,up:callable}> */
    private function discoverMigrations(): array
    {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0755, true);
        }

        $files = glob($this->dir . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files, SORT_STRING);

        $migrations = [];
        foreach ($files as $file) {
            if (basename($file) === 'index.php') {
                continue;
            }
            $data = include $file;
            if (!is_array($data) || empty($data['id']) || !isset($data['up'])) {
                throw new RuntimeException('Invalid migration file: ' . basename($file));
            }
            $migrations[] = $data;
        }
        return $migrations;
    }

    private function recordSuccess(string $id, string $description, ?int $appliedBy): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO auto_migrations (id, description, status, error_message, applied_by, applied_at)
            VALUES (?, ?, 'success', NULL, ?, NOW())
            ON DUPLICATE KEY UPDATE
                description = VALUES(description),
                status = 'success',
                error_message = NULL,
                applied_by = VALUES(applied_by),
                applied_at = NOW()
        ");
        $stmt->execute([$id, $description, $appliedBy]);
    }

    private function recordFailure(string $id, string $description, string $error, ?int $appliedBy): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO auto_migrations (id, description, status, error_message, applied_by, applied_at)
            VALUES (?, ?, 'failed', ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                description = VALUES(description),
                status = 'failed',
                error_message = VALUES(error_message),
                applied_by = VALUES(applied_by),
                updated_at = NOW()
        ");
        $stmt->execute([$id, $description, $error, $appliedBy]);
    }

    public static function tableExists(PDO $pdo, string $table): bool
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table) ?: '';
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS cnt
            FROM information_schema.tables
            WHERE table_schema = DATABASE() AND table_name = ?
        ");
        $stmt->execute([$table]);
        $row = $stmt->fetch();
        return !empty($row['cnt']);
    }

    public static function ensureColumn(PDO $pdo, string $table, string $column, string $definitionSql): bool
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table) ?: '';
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column) ?: '';
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS cnt
            FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = ?
              AND column_name = ?
        ");
        $stmt->execute([$table, $column]);
        $row = $stmt->fetch();
        if (!empty($row['cnt'])) {
            return false;
        }
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN {$definitionSql}");
        return true;
    }

    public static function ensureIndex(PDO $pdo, string $table, string $indexName, string $createSql): bool
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table) ?: '';
        $indexName = preg_replace('/[^a-zA-Z0-9_]/', '', $indexName) ?: '';
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS cnt
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
              AND table_name = ?
              AND index_name = ?
        ");
        $stmt->execute([$table, $indexName]);
        $row = $stmt->fetch();
        if (!empty($row['cnt'])) {
            return false;
        }
        $pdo->exec($createSql);
        return true;
    }

    public static function ensureAppSetting(PDO $pdo, string $key, ?string $value): bool
    {
        $stmt = $pdo->prepare('SELECT id FROM app_settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        if ($stmt->fetch()) {
            return false;
        }
        $ins = $pdo->prepare('INSERT INTO app_settings (setting_key, setting_value) VALUES (?, ?)');
        $ins->execute([$key, $value]);
        return true;
    }

    public static function execOrFail(PDO $pdo, string $sql, string $label = ''): void
    {
        try {
            $pdo->exec($sql);
        } catch (Throwable $e) {
            throw new RuntimeException(($label !== '' ? $label . ': ' : '') . $e->getMessage(), 0, $e);
        }
    }
}

/**
 * Run auto-migrations for the current admin session (once per request).
 */
function run_admin_database_auto_migrations(?int $adminUserId = null): ?array
{
    try {
        $adminUserId = $adminUserId ?? (isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : null);
        $migrator = new DatabaseAutoMigrate();
        return $migrator->run($adminUserId && $adminUserId > 0 ? $adminUserId : null);
    } catch (Throwable $e) {
        error_log('run_admin_database_auto_migrations: ' . $e->getMessage());
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['auto_migration_errors'] = ['Auto-migration runner failed: ' . $e->getMessage()];
        }
        return null;
    }
}
