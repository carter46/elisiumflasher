-- =============================================================================
-- Elysium Flasher — upgrade existing databases to current local-only schema
-- =============================================================================
-- Safe to re-run (idempotent). Select your DB in phpMyAdmin, then Import.
-- Fresh empty DB: import database/schema.sql instead.
--
-- Replaces the old root migration_*.sql clutter:
--   migration.sql, migration_full_legacy.sql, if0_40085505_elysium.sql,
--   migration_remove_international.sql, migration_local_transfer_status.sql,
--   migration_wallet_pin.sql, migration_transaction_completed.sql,
--   migration_mobile_local_transfer.sql, migration_tracker_uetr.sql
-- =============================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- 1) Drop international tables (Flasher is local-only)
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS international_transactions;
DROP TABLE IF EXISTS international_dashboard_profile;
DROP TABLE IF EXISTS international_recent_transfers;
DROP TABLE IF EXISTS international_sender_settings;
DROP TABLE IF EXISTS international_status;
DROP TABLE IF EXISTS international_banks;

-- ---------------------------------------------------------------------------
-- 2) Core tables if missing
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS client_keys (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_key VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS app_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS local_dashboard_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  account_type VARCHAR(100) NOT NULL,
  account_name VARCHAR(150) NOT NULL,
  account_number VARCHAR(30) NOT NULL DEFAULT '',
  balance DECIMAL(18,2) NOT NULL,
  masked_pan VARCHAR(30) NOT NULL,
  tier_label VARCHAR(50) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(255) NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS platform_status (
  id INT PRIMARY KEY,
  status ENUM('on','off') NOT NULL DEFAULT 'on',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bank_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bank_code VARCHAR(20) UNIQUE NOT NULL,
  bank_name VARCHAR(100) NOT NULL,
  status ENUM('full_logs','weak_logs','pending_request','post_no_debit','fixed_account') DEFAULT 'full_logs',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS local_transfer_banks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bank_name VARCHAR(120) NOT NULL UNIQUE,
  bank_code VARCHAR(20) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS local_recent_transfers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient_name VARCHAR(150) NOT NULL,
  subtitle VARCHAR(180) NOT NULL,
  amount DECIMAL(18,2) NOT NULL,
  direction ENUM('debit','credit') NOT NULL DEFAULT 'debit',
  status_label VARCHAR(30) NOT NULL DEFAULT 'Success',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS local_frequent_recipients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient_name VARCHAR(150) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS mobile_devices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bank_code VARCHAR(16) NOT NULL,
  account_number VARCHAR(30) NOT NULL,
  fcm_token VARCHAR(512) NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_mobile_devices_identity (bank_code, account_number),
  KEY idx_mobile_devices_token (fcm_token(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- 3) Conditional columns / indexes on local_dashboard_profile + local_transactions
-- ---------------------------------------------------------------------------

SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'local_dashboard_profile' AND COLUMN_NAME = 'account_number');
SET @sql := IF(@col = 0, 'ALTER TABLE local_dashboard_profile ADD COLUMN account_number VARCHAR(30) NOT NULL DEFAULT '''' AFTER account_name', 'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

ALTER TABLE local_transactions
  MODIFY COLUMN status ENUM('SUCCESSFUL','FAILED','PENDING','REVERSED','COMPLETED') NOT NULL DEFAULT 'SUCCESSFUL';

SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'local_transactions' AND COLUMN_NAME = 'beneficiary_bank_code');
SET @sql := IF(@col = 0, 'ALTER TABLE local_transactions ADD COLUMN beneficiary_bank_code VARCHAR(20) NULL DEFAULT NULL AFTER beneficiary_bank', 'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'local_transactions' AND COLUMN_NAME = 'uetr');
SET @sql := IF(@col = 0, 'ALTER TABLE local_transactions ADD COLUMN uetr CHAR(36) NULL AFTER reference', 'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'local_transactions' AND COLUMN_NAME = 'tracker_status');
SET @sql := IF(@col = 0, 'ALTER TABLE local_transactions ADD COLUMN tracker_status VARCHAR(16) NOT NULL DEFAULT ''PDNG'' AFTER status', 'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @col := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'local_transactions' AND COLUMN_NAME = 'tracker_status_updated_at');
SET @sql := IF(@col = 0, 'ALTER TABLE local_transactions ADD COLUMN tracker_status_updated_at TIMESTAMP NULL DEFAULT NULL AFTER tracker_status', 'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @idx := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'local_transactions' AND INDEX_NAME = 'uq_local_transactions_uetr');
SET @sql := IF(@idx = 0, 'ALTER TABLE local_transactions ADD UNIQUE KEY uq_local_transactions_uetr (uetr)', 'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @idx := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'local_transactions' AND INDEX_NAME = 'idx_lt_beneficiary_bank_code');
SET @sql := IF(@idx = 0, 'ALTER TABLE local_transactions ADD INDEX idx_lt_beneficiary_bank_code (beneficiary_bank_code)', 'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @idx := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'local_transactions' AND INDEX_NAME = 'idx_lt_beneficiary_account_status');
SET @sql := IF(@idx = 0, 'ALTER TABLE local_transactions ADD INDEX idx_lt_beneficiary_account_status (beneficiary_account, status)', 'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ---------------------------------------------------------------------------
-- 4) Settings seeds (do not overwrite existing values)
-- ---------------------------------------------------------------------------
INSERT INTO platform_status (id, status)
SELECT 1, 'on' WHERE NOT EXISTS (SELECT 1 FROM platform_status WHERE id = 1);

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_resolve_enabled', '1' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_resolve_enabled');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'flutterwave_resolve_enabled', '0' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'flutterwave_resolve_enabled');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_transfer_status', 'successful' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'local_transfer_status');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_link_wallet_status', 'on' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'local_link_wallet_status');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'wallet_pin', NULL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'wallet_pin');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'wallet_pin_enc', NULL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'wallet_pin_enc');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_tracker_status', 'PDNG' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'local_tracker_status');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'tracker_api_key', LOWER(CONCAT(HEX(RANDOM_BYTES(8)), HEX(RANDOM_BYTES(8)), HEX(RANDOM_BYTES(8))))
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'tracker_api_key');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_host', '' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_host');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_port', '587' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_port');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_user', '' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_user');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_pass', '' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_pass');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_encryption', 'tls' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_encryption');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_from_email', '' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_from_email');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_from_name', 'Elysium Tracker' WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_from_name');

-- ---------------------------------------------------------------------------
-- 5) Backfill tracker fields on existing rows
-- ---------------------------------------------------------------------------
UPDATE local_transactions
SET uetr = LOWER(CONCAT(
  SUBSTR(HEX(RANDOM_BYTES(4)), 1, 8), '-',
  SUBSTR(HEX(RANDOM_BYTES(2)), 1, 4), '-',
  '4', SUBSTR(HEX(RANDOM_BYTES(2)), 1, 3), '-',
  SUBSTR('89ab', 1 + (CRC32(CONCAT(id, reference)) % 4), 1), SUBSTR(HEX(RANDOM_BYTES(2)), 1, 3), '-',
  SUBSTR(HEX(RANDOM_BYTES(6)), 1, 12)
))
WHERE uetr IS NULL OR uetr = '';

UPDATE local_transactions
SET tracker_status = 'PDNG'
WHERE tracker_status IS NULL OR tracker_status = '';
