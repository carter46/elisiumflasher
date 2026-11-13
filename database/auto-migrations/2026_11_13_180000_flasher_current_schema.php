<?php
declare(strict_types=1);

/**
 * Bring older Flasher databases up to the current local-only schema.
 * Mirrors database/upgrade.sql in PHP (idempotent helpers).
 */

return [
    'id' => '2026_11_13_flasher_current_schema',
    'description' => 'Flasher local-only schema: drop intl, mobile/tracker columns, OTP, settings',
    'up' => function (PDO $pdo): void {
        // 1) Flasher is local-only
        foreach ([
            'international_transactions',
            'international_dashboard_profile',
            'international_recent_transfers',
            'international_sender_settings',
            'international_status',
            'international_banks',
        ] as $table) {
            DatabaseAutoMigrate::execOrFail($pdo, "DROP TABLE IF EXISTS `{$table}`", "Drop {$table}");
        }

        // 2) Core tables
        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS client_keys (
              id INT AUTO_INCREMENT PRIMARY KEY,
              client_key VARCHAR(255) NOT NULL,
              is_active TINYINT(1) NOT NULL DEFAULT 1,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create client_keys');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS app_settings (
              id INT AUTO_INCREMENT PRIMARY KEY,
              setting_key VARCHAR(100) NOT NULL UNIQUE,
              setting_value TEXT NULL,
              updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create app_settings');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS local_dashboard_profile (
              id INT AUTO_INCREMENT PRIMARY KEY,
              account_type VARCHAR(100) NOT NULL,
              account_name VARCHAR(150) NOT NULL,
              account_number VARCHAR(30) NOT NULL DEFAULT '',
              balance DECIMAL(18,2) NOT NULL,
              masked_pan VARCHAR(30) NOT NULL,
              tier_label VARCHAR(50) NOT NULL,
              updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create local_dashboard_profile');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS admin_users (
              id INT AUTO_INCREMENT PRIMARY KEY,
              username VARCHAR(50) NOT NULL UNIQUE,
              email VARCHAR(255) NULL,
              password VARCHAR(255) NOT NULL,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              last_login DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create admin_users');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS platform_status (
              id INT PRIMARY KEY,
              status ENUM('on','off') NOT NULL DEFAULT 'on',
              updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create platform_status');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS bank_status (
              id INT AUTO_INCREMENT PRIMARY KEY,
              bank_code VARCHAR(20) UNIQUE NOT NULL,
              bank_name VARCHAR(100) NOT NULL,
              status ENUM('full_logs','weak_logs','pending_request','post_no_debit','fixed_account') DEFAULT 'full_logs',
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create bank_status');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS local_transactions (
              id INT AUTO_INCREMENT PRIMARY KEY,
              reference VARCHAR(100) NOT NULL UNIQUE,
              amount DECIMAL(18,2) NOT NULL,
              currency VARCHAR(10) NOT NULL DEFAULT 'NGN',
              beneficiary_name VARCHAR(150) NOT NULL,
              beneficiary_bank VARCHAR(120) NOT NULL,
              beneficiary_account VARCHAR(30) NOT NULL,
              sender_account VARCHAR(30) NOT NULL,
              sender_name VARCHAR(150) NOT NULL,
              purpose VARCHAR(180) NULL,
              status ENUM('SUCCESSFUL','FAILED','PENDING','REVERSED','COMPLETED') NOT NULL DEFAULT 'SUCCESSFUL',
              direction ENUM('debit','credit') NOT NULL DEFAULT 'debit',
              transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create local_transactions');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS local_transfer_banks (
              id INT AUTO_INCREMENT PRIMARY KEY,
              bank_name VARCHAR(120) NOT NULL UNIQUE,
              bank_code VARCHAR(20) NOT NULL,
              is_active TINYINT(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create local_transfer_banks');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS local_recent_transfers (
              id INT AUTO_INCREMENT PRIMARY KEY,
              recipient_name VARCHAR(150) NOT NULL,
              subtitle VARCHAR(180) NOT NULL,
              amount DECIMAL(18,2) NOT NULL,
              direction ENUM('debit','credit') NOT NULL DEFAULT 'debit',
              status_label VARCHAR(30) NOT NULL DEFAULT 'Success',
              sort_order INT NOT NULL DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create local_recent_transfers');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS local_frequent_recipients (
              id INT AUTO_INCREMENT PRIMARY KEY,
              recipient_name VARCHAR(150) NOT NULL,
              sort_order INT NOT NULL DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create local_frequent_recipients');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS mobile_sessions (
              id INT AUTO_INCREMENT PRIMARY KEY,
              token VARCHAR(128) NOT NULL,
              bank_code VARCHAR(16) NOT NULL,
              account_number VARCHAR(30) NOT NULL,
              account_name VARCHAR(150) NOT NULL,
              expires_at DATETIME NOT NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY uq_mobile_sessions_token (token),
              KEY idx_mobile_sessions_identity (bank_code, account_number),
              KEY idx_mobile_sessions_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create mobile_sessions');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS mobile_devices (
              id INT AUTO_INCREMENT PRIMARY KEY,
              bank_code VARCHAR(16) NOT NULL,
              account_number VARCHAR(30) NOT NULL,
              fcm_token VARCHAR(512) NOT NULL,
              updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY uq_mobile_devices_identity (bank_code, account_number),
              KEY idx_mobile_devices_token (fcm_token(191))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create mobile_devices');

        DatabaseAutoMigrate::execOrFail($pdo, "
            CREATE TABLE IF NOT EXISTS tracker_otp_challenges (
              id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              uetr CHAR(36) NOT NULL,
              email VARCHAR(190) NOT NULL,
              otp_hash VARCHAR(255) NOT NULL,
              unlock_token_hash VARCHAR(255) NULL,
              attempts INT UNSIGNED NOT NULL DEFAULT 0,
              expires_at DATETIME NOT NULL,
              unlocked_at DATETIME NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX idx_tracker_otp_uetr_email (uetr, email),
              INDEX idx_tracker_otp_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ", 'Create tracker_otp_challenges');

        // 3) Columns / indexes
        DatabaseAutoMigrate::ensureColumn(
            $pdo,
            'local_dashboard_profile',
            'account_number',
            "`account_number` VARCHAR(30) NOT NULL DEFAULT '' AFTER account_name"
        );

        DatabaseAutoMigrate::execOrFail(
            $pdo,
            "ALTER TABLE local_transactions
             MODIFY COLUMN status ENUM('SUCCESSFUL','FAILED','PENDING','REVERSED','COMPLETED') NOT NULL DEFAULT 'SUCCESSFUL'",
            'Expand local_transactions.status ENUM'
        );

        DatabaseAutoMigrate::ensureColumn(
            $pdo,
            'local_transactions',
            'beneficiary_bank_code',
            '`beneficiary_bank_code` VARCHAR(20) NULL DEFAULT NULL AFTER beneficiary_bank'
        );
        DatabaseAutoMigrate::ensureColumn(
            $pdo,
            'local_transactions',
            'uetr',
            '`uetr` CHAR(36) NULL AFTER reference'
        );
        DatabaseAutoMigrate::ensureColumn(
            $pdo,
            'local_transactions',
            'tracker_status',
            "`tracker_status` VARCHAR(16) NOT NULL DEFAULT 'PDNG' AFTER status"
        );
        DatabaseAutoMigrate::ensureColumn(
            $pdo,
            'local_transactions',
            'tracker_status_updated_at',
            '`tracker_status_updated_at` TIMESTAMP NULL DEFAULT NULL AFTER tracker_status'
        );

        DatabaseAutoMigrate::ensureIndex(
            $pdo,
            'local_transactions',
            'uq_local_transactions_uetr',
            'ALTER TABLE local_transactions ADD UNIQUE KEY uq_local_transactions_uetr (uetr)'
        );
        DatabaseAutoMigrate::ensureIndex(
            $pdo,
            'local_transactions',
            'idx_lt_beneficiary_bank_code',
            'ALTER TABLE local_transactions ADD INDEX idx_lt_beneficiary_bank_code (beneficiary_bank_code)'
        );
        DatabaseAutoMigrate::ensureIndex(
            $pdo,
            'local_transactions',
            'idx_lt_beneficiary_account_status',
            'ALTER TABLE local_transactions ADD INDEX idx_lt_beneficiary_account_status (beneficiary_account, status)'
        );

        // 4) Settings
        $pdo->exec("INSERT INTO platform_status (id, status) SELECT 1, 'on' WHERE NOT EXISTS (SELECT 1 FROM platform_status WHERE id = 1)");

        foreach ([
            'paystack_resolve_enabled' => '1',
            'flutterwave_resolve_enabled' => '0',
            'local_transfer_status' => 'successful',
            'local_link_wallet_status' => 'on',
            'local_tracker_status' => 'PDNG',
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_user' => '',
            'smtp_pass' => '',
            'smtp_encryption' => 'tls',
            'smtp_from_email' => '',
            'smtp_from_name' => 'Elysium Tracker',
        ] as $key => $val) {
            DatabaseAutoMigrate::ensureAppSetting($pdo, $key, $val);
        }
        DatabaseAutoMigrate::ensureAppSetting($pdo, 'wallet_pin', null);
        DatabaseAutoMigrate::ensureAppSetting($pdo, 'wallet_pin_enc', null);

        $stmt = $pdo->prepare("SELECT setting_value FROM app_settings WHERE setting_key = 'tracker_api_key' LIMIT 1");
        $stmt->execute();
        $existingKey = $stmt->fetchColumn();
        if ($existingKey === false || $existingKey === null || trim((string) $existingKey) === '') {
            $key = bin2hex(random_bytes(24));
            if ($existingKey === false) {
                DatabaseAutoMigrate::ensureAppSetting($pdo, 'tracker_api_key', $key);
            } else {
                $upd = $pdo->prepare("UPDATE app_settings SET setting_value = ? WHERE setting_key = 'tracker_api_key'");
                $upd->execute([$key]);
            }
        }

        // 5) Backfill tracker fields
        $pdo->exec("
            UPDATE local_transactions
            SET uetr = LOWER(CONCAT(
              SUBSTR(HEX(RANDOM_BYTES(4)), 1, 8), '-',
              SUBSTR(HEX(RANDOM_BYTES(2)), 1, 4), '-',
              '4', SUBSTR(HEX(RANDOM_BYTES(2)), 1, 3), '-',
              SUBSTR('89ab', 1 + (CRC32(CONCAT(id, reference)) % 4), 1), SUBSTR(HEX(RANDOM_BYTES(2)), 1, 3), '-',
              SUBSTR(HEX(RANDOM_BYTES(6)), 1, 12)
            ))
            WHERE uetr IS NULL OR uetr = ''
        ");
        $pdo->exec("UPDATE local_transactions SET tracker_status = 'PDNG' WHERE tracker_status IS NULL OR tracker_status = ''");
    },
];
