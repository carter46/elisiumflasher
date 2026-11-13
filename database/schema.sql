-- =============================================================================
-- Elysium Flasher — fresh database schema (local-only)
-- =============================================================================
-- Import into an empty MySQL/MariaDB database (phpMyAdmin → Import).
-- Then set real credentials in Admin / app_settings / client_keys.
--
-- Existing databases: use database/upgrade.sql instead (do not re-import this).
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

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
  uetr CHAR(36) NULL,
  amount DECIMAL(18,2) NOT NULL,
  currency VARCHAR(10) NOT NULL DEFAULT 'NGN',
  beneficiary_name VARCHAR(150) NOT NULL,
  beneficiary_bank VARCHAR(120) NOT NULL,
  beneficiary_bank_code VARCHAR(20) NULL DEFAULT NULL,
  beneficiary_account VARCHAR(30) NOT NULL,
  sender_account VARCHAR(30) NOT NULL,
  sender_name VARCHAR(150) NOT NULL,
  purpose VARCHAR(180) NULL,
  status ENUM('SUCCESSFUL','FAILED','PENDING','REVERSED','COMPLETED') NOT NULL DEFAULT 'SUCCESSFUL',
  tracker_status VARCHAR(16) NOT NULL DEFAULT 'PDNG',
  tracker_status_updated_at TIMESTAMP NULL DEFAULT NULL,
  direction ENUM('debit','credit') NOT NULL DEFAULT 'debit',
  transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_local_transactions_uetr (uetr),
  KEY idx_lt_beneficiary_bank_code (beneficiary_bank_code),
  KEY idx_lt_beneficiary_account_status (beneficiary_account, status)
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
-- Seeds (no production secrets — replace after import)
-- ---------------------------------------------------------------------------

INSERT INTO platform_status (id, status)
SELECT 1, 'on' WHERE NOT EXISTS (SELECT 1 FROM platform_status WHERE id = 1);

INSERT INTO local_dashboard_profile (account_type, account_name, account_number, balance, masked_pan, tier_label)
SELECT 'Local Savings Account', 'Demo Account', '0000000000', 1000000.00, '**** 0000', 'Standard'
WHERE NOT EXISTS (SELECT 1 FROM local_dashboard_profile);

-- password = ChangeMe123!  (bcrypt) — change immediately in Admin → Profile
INSERT INTO admin_users (username, email, password)
SELECT 'admin', 'admin@example.com', '$2y$10$/J1w.5eZQdn6MTRQCS9JlusXaWz6y.XwRKBphfuaprGcwVSZqLDUW'
WHERE NOT EXISTS (SELECT 1 FROM admin_users WHERE username = 'admin');

INSERT INTO client_keys (client_key, is_active)
SELECT 'CHANGE_ME_CLIENT_KEY', 1
WHERE NOT EXISTS (SELECT 1 FROM client_keys LIMIT 1);

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_resolve_enabled', '1'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_resolve_enabled');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'flutterwave_resolve_enabled', '0'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'flutterwave_resolve_enabled');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_use_live', '0'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_use_live');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_test_secret_key', ''
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_test_secret_key');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_live_secret_key', ''
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_live_secret_key');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_transfer_status', 'successful'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'local_transfer_status');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_link_wallet_status', 'on'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'local_link_wallet_status');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'wallet_pin', NULL
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'wallet_pin');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'wallet_pin_enc', NULL
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'wallet_pin_enc');
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_tracker_status', 'PDNG'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'local_tracker_status');
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

INSERT INTO bank_status (bank_code, bank_name, status) SELECT '033', 'UBA', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '033');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '011', 'First Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '011');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '044', 'Access Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '044');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '057', 'Zenith Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '057');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '058', 'Guaranty Trust Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '058');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '301', 'Jaiz Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '301');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '070', 'Fidelity Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '070');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '030', 'Heritage Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '030');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '082', 'Keystone Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '082');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '232', 'Sterling Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '232');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '032', 'Union Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '032');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '215', 'Unity Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '215');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '035', 'Wema Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '035');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '50211', 'Kuda Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '50211');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '50515', 'Moniepoint', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '50515');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '999992', 'OPay', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '999992');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '100033', 'PalmPay', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '100033');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '221', 'Stanbic IBTC Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '221');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '076', 'Polaris Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '076');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '214', 'FCMB', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '214');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '050', 'Ecobank Nigeria', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '050');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '068', 'Standard Chartered Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '068');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '023', 'Citibank Nigeria', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '023');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '101', 'Providus Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '101');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '565', 'Carbon', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '565');
INSERT INTO bank_status (bank_code, bank_name, status) SELECT '566', 'VFD Microfinance Bank', 'full_logs' WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '566');

INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Access Bank PLC', '044', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '044');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Zenith Bank', '057', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '057');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Guaranty Trust Bank', '058', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '058');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'United Bank for Africa', '033', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '033');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'First Bank of Nigeria', '011', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '011');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Fidelity Bank', '070', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '070');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Union Bank of Nigeria', '032', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '032');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Sterling Bank', '232', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '232');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Stanbic IBTC Bank', '221', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '221');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Wema Bank', '035', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '035');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Polaris Bank', '076', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '076');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Keystone Bank', '082', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '082');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Heritage Bank', '030', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '030');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Jaiz Bank', '301', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '301');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Unity Bank', '215', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '215');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'FCMB', '214', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '214');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Ecobank Nigeria', '050', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '050');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Standard Chartered Bank', '068', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '068');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Citibank Nigeria', '023', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '023');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Providus Bank', '101', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '101');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Kuda Bank', '50211', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '50211');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Moniepoint', '50515', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '50515');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'OPay', '999992', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '999992');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'PalmPay', '100033', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '100033');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'VFD Microfinance Bank', '566', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '566');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active) SELECT 'Carbon', '565', 1 WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_code = '565');
