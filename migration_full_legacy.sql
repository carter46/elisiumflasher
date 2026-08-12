-- =============================================================================
-- ARCHIVE / REFERENCE ONLY — full historical migration (large, many seeds)
-- =============================================================================
-- The active lean migration for production updates is `migration.sql`.
-- Keep this file for reference or one-off recovery (e.g. re-seeding bank rows
-- with INSERT ... WHERE NOT EXISTS). Do not import blindly on live data if you
-- already applied an equivalent migration — prefer `migration.sql` for updates.
-- =============================================================================

-- Migration script for updating older databases to the current schema.
-- Safe to run multiple times: uses CREATE TABLE IF NOT EXISTS and conditional ALTER/INSERT.
-- In phpMyAdmin: click your database in the left sidebar, then Import (runs against the selected DB only).

-- 1) Core tables
CREATE TABLE IF NOT EXISTS client_keys (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_key VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS app_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS local_dashboard_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  account_type VARCHAR(100) NOT NULL,
  account_name VARCHAR(150) NOT NULL,
  balance DECIMAL(18,2) NOT NULL,
  masked_pan VARCHAR(30) NOT NULL,
  tier_label VARCHAR(50) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add local_dashboard_profile.account_number if missing
SET @col_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'local_dashboard_profile'
    AND COLUMN_NAME = 'account_number'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE local_dashboard_profile ADD COLUMN account_number VARCHAR(30) NOT NULL DEFAULT '''' AFTER account_name',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2) Admin + status control tables
CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(255) NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login DATETIME NULL
);

CREATE TABLE IF NOT EXISTS platform_status (
  id INT PRIMARY KEY,
  status ENUM('on','off') NOT NULL DEFAULT 'on',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bank_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bank_code VARCHAR(20) UNIQUE NOT NULL,
  bank_name VARCHAR(100) NOT NULL,
  status ENUM('full_logs', 'weak_logs', 'pending_request', 'post_no_debit', 'fixed_account') DEFAULT 'full_logs',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3) Local transfer banks table
CREATE TABLE IF NOT EXISTS local_transfer_banks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bank_name VARCHAR(120) NOT NULL UNIQUE,
  bank_code VARCHAR(20) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
);

-- Nigerian Banks (comprehensive list with proper CBN codes)
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Access Bank PLC', '044', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Access Bank PLC');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Zenith Bank', '057', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Zenith Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Guaranty Trust Bank', '058', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Guaranty Trust Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'United Bank for Africa', '033', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'United Bank for Africa');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'First Bank of Nigeria', '011', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'First Bank of Nigeria');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Fidelity Bank', '070', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Fidelity Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Union Bank of Nigeria', '032', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Union Bank of Nigeria');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Sterling Bank', '232', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Sterling Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Stanbic IBTC Bank', '221', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Stanbic IBTC Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Wema Bank', '035', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Wema Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Polaris Bank', '076', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Polaris Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Keystone Bank', '082', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Keystone Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Heritage Bank', '030', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Heritage Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Jaiz Bank', '301', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Jaiz Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Unity Bank', '215', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Unity Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'FCMB', '214', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'FCMB');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Ecobank Nigeria', '050', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Ecobank Nigeria');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Standard Chartered Bank', '068', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Standard Chartered Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Citibank Nigeria', '023', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Citibank Nigeria');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Globus Bank', '00103', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Globus Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'SunTrust Bank', '100', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'SunTrust Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Providus Bank', '101', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Providus Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Titan Trust Bank', '102', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Titan Trust Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Kuda Bank', '50211', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Kuda Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Moniepoint', '50515', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Moniepoint');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'OPay', '999992', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'OPay');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'PalmPay', '100033', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'PalmPay');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'VFD Microfinance Bank', '566', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'VFD Microfinance Bank');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Rubies MFB', '125', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Rubies MFB');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Sparkle MFB', '51310', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Sparkle MFB');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'Carbon', '565', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'Carbon');
INSERT INTO local_transfer_banks (bank_name, bank_code, is_active)
SELECT 'ALAT by WEMA', '035A', 1
WHERE NOT EXISTS (SELECT 1 FROM local_transfer_banks WHERE bank_name = 'ALAT by WEMA');

-- 4) Local transfer transaction history table
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
  status ENUM('SUCCESSFUL','FAILED','PENDING') NOT NULL DEFAULT 'SUCCESSFUL',
  direction ENUM('debit','credit') NOT NULL DEFAULT 'debit',
  transaction_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Local recent transfers (display data)
CREATE TABLE IF NOT EXISTS local_recent_transfers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient_name VARCHAR(150) NOT NULL,
  subtitle VARCHAR(180) NOT NULL,
  amount DECIMAL(18,2) NOT NULL,
  direction ENUM('debit','credit') NOT NULL DEFAULT 'debit',
  status_label VARCHAR(30) NOT NULL DEFAULT 'Success',
  sort_order INT NOT NULL DEFAULT 0
);

-- Local frequent recipients (display data)
CREATE TABLE IF NOT EXISTS local_frequent_recipients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient_name VARCHAR(150) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
);

-- International dashboard profile
CREATE TABLE IF NOT EXISTS international_dashboard_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  balance_usd DECIMAL(18,2) NOT NULL,
  vault_label VARCHAR(100) NOT NULL,
  masked_pan VARCHAR(30) NOT NULL,
  route_label VARCHAR(100) NOT NULL,
  route_description VARCHAR(200) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- International recent transfers (display data)
CREATE TABLE IF NOT EXISTS international_recent_transfers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient_name VARCHAR(150) NOT NULL,
  subtitle VARCHAR(180) NOT NULL,
  transfer_date VARCHAR(50) NOT NULL,
  amount_usd DECIMAL(18,2) NOT NULL,
  currency VARCHAR(3) NOT NULL DEFAULT 'USD',
  status_label VARCHAR(30) NOT NULL DEFAULT 'Completed',
  sort_order INT NOT NULL DEFAULT 0
);

-- Seed default local dashboard profile if not exists
INSERT INTO local_dashboard_profile (account_type, account_name, account_number, balance, masked_pan, tier_label)
SELECT 'Local Savings Account', 'Tunde O. Badmus', '1022090307', 1245000.00, '**** 4492', 'Premium Tier'
WHERE NOT EXISTS (SELECT 1 FROM local_dashboard_profile);

-- Seed default international dashboard profile if not exists
INSERT INTO international_dashboard_profile (balance_usd, vault_label, masked_pan, route_label, route_description)
SELECT 48250.40, 'Savings Vault - Platinum', '**** **** **** 8824', 'SWIFT Network', 'Delivery within 24-48 hours via premium rails.'
WHERE NOT EXISTS (SELECT 1 FROM international_dashboard_profile);

-- 4) Ensure default settings exist (non-destructive)
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_secret_key', 'sk_live_fc6a9d6fed91eadb4226db9b61408ab614c2533f'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_secret_key');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_test_secret_key', 'sk_test_change_me'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_test_secret_key');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_live_secret_key', 'sk_live_fc6a9d6fed91eadb4226db9b61408ab614c2533f'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_live_secret_key');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_use_live', '1'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_use_live');

-- Update existing Paystack keys to use the correct live key
UPDATE app_settings SET setting_value = 'sk_live_fc6a9d6fed91eadb4226db9b61408ab614c2533f' 
WHERE setting_key = 'paystack_secret_key' AND (setting_value = 'sk_test_change_me' OR setting_value IS NULL OR setting_value = '');

UPDATE app_settings SET setting_value = 'sk_live_fc6a9d6fed91eadb4226db9b61408ab614c2533f' 
WHERE setting_key = 'paystack_live_secret_key' AND (setting_value = 'sk_live_change_me' OR setting_value IS NULL OR setting_value = '');

UPDATE app_settings SET setting_value = '1' 
WHERE setting_key = 'paystack_use_live' AND setting_value = '0';

-- (Optional) public keys used by admin UI; safe placeholders
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_test_public_key', NULL
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_test_public_key');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_live_public_key', NULL
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_live_public_key');

-- 5) Seed platform + banks status rows
INSERT INTO platform_status (id, status)
SELECT 1, 'on'
WHERE NOT EXISTS (SELECT 1 FROM platform_status WHERE id = 1);

INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '033', 'UBA', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '033');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '011', 'First Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '011');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '044', 'Access Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '044');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '070', 'Fidelity Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '070');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '058', 'Guaranty Trust Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '058');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '030', 'Heritage Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '030');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '301', 'Jaiz Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '301');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '082', 'Keystone Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '082');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '232', 'Sterling Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '232');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '032', 'Union Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '032');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '215', 'Unity Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '215');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '035', 'Wema Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '035');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '057', 'Zenith Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '057');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '50211', 'Kuda Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '50211');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '50515', 'Moniepoint', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '50515');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '999992', 'OPay', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '999992');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '100033', 'PalmPay', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '100033');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '221', 'Stanbic IBTC Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '221');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '076', 'Polaris Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '076');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '214', 'FCMB', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '214');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '050', 'Ecobank Nigeria', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '050');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '068', 'Standard Chartered Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '068');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '023', 'Citibank Nigeria', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '023');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '101', 'Providus Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '101');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '565', 'Carbon', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '565');
INSERT INTO bank_status (bank_code, bank_name, status)
SELECT '566', 'VFD Microfinance Bank', 'full_logs'
WHERE NOT EXISTS (SELECT 1 FROM bank_status WHERE bank_code = '566');

-- 6) Remove legacy default admin (admin / admin123). Add your admin row manually in phpMyAdmin if none exists.
DELETE FROM admin_users WHERE username = 'admin' AND password = 'admin123';

-- 7) international_recent_transfers.currency (USD / EUR display)
SET @irt_curr := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_recent_transfers'
    AND COLUMN_NAME = 'currency'
);
SET @sql_irt := IF(
  @irt_curr = 0,
  'ALTER TABLE international_recent_transfers ADD COLUMN currency VARCHAR(3) NOT NULL DEFAULT ''USD'' AFTER amount_usd',
  'SELECT 1'
);
PREPARE stmt_irt FROM @sql_irt;
EXECUTE stmt_irt;
DEALLOCATE PREPARE stmt_irt;

-- 8) international_transactions (user history + receipt source)
CREATE TABLE IF NOT EXISTS international_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reference VARCHAR(100) NOT NULL UNIQUE,
  beneficiary_iban VARCHAR(60) NOT NULL,
  beneficiary_name VARCHAR(150) NOT NULL,
  beneficiary_address TEXT NOT NULL,
  swift_code VARCHAR(30) NOT NULL DEFAULT '',
  routing_number VARCHAR(9) NOT NULL DEFAULT '',
  bank_name VARCHAR(150) NOT NULL,
  country_code VARCHAR(2) NOT NULL DEFAULT '',
  country_name VARCHAR(100) NOT NULL DEFAULT '',
  amount DECIMAL(18,2) NOT NULL,
  currency VARCHAR(3) NOT NULL,
  message_type VARCHAR(80) NOT NULL,
  delivery_date DATE NOT NULL,
  status ENUM('SUCCESSFUL','PENDING','FAILED','REVERSED','NETWORK_ERROR') NOT NULL DEFAULT 'SUCCESSFUL',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Ensure international_transactions.status enum supports extended values
ALTER TABLE international_transactions
  MODIFY COLUMN status ENUM('SUCCESSFUL','PENDING','FAILED','REVERSED','NETWORK_ERROR') NOT NULL DEFAULT 'SUCCESSFUL';

-- Add routing_number column if missing
SET @irt_rn := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_transactions'
    AND COLUMN_NAME = 'routing_number'
);
SET @sql_irt_rn := IF(
  @irt_rn = 0,
  'ALTER TABLE international_transactions ADD COLUMN routing_number VARCHAR(9) NOT NULL DEFAULT '''' AFTER swift_code',
  'SELECT 1'
);
PREPARE stmt FROM @sql_irt_rn;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add country fields if missing
SET @it_cc := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_transactions'
    AND COLUMN_NAME = 'country_code'
);
SET @it_cn := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_transactions'
    AND COLUMN_NAME = 'country_name'
);
SET @sql_it_cc := IF(
  @it_cc = 0,
  'ALTER TABLE international_transactions ADD COLUMN country_code VARCHAR(2) NOT NULL DEFAULT '''' AFTER bank_name',
  'SELECT 1'
);
PREPARE stmt_it_cc FROM @sql_it_cc;
EXECUTE stmt_it_cc;
DEALLOCATE PREPARE stmt_it_cc;

SET @sql_it_cn := IF(
  @it_cn = 0,
  'ALTER TABLE international_transactions ADD COLUMN country_name VARCHAR(100) NOT NULL DEFAULT '''' AFTER country_code',
  'SELECT 1'
);
PREPARE stmt_it_cn FROM @sql_it_cn;
EXECUTE stmt_it_cn;
DEALLOCATE PREPARE stmt_it_cn;

-- Add beneficiary_address column if missing
SET @it_ba := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_transactions'
    AND COLUMN_NAME = 'beneficiary_address'
);
SET @sql_it_ba := IF(
  @it_ba = 0,
  'ALTER TABLE international_transactions ADD COLUMN beneficiary_address TEXT NOT NULL AFTER beneficiary_name',
  'SELECT 1'
);
PREPARE stmt_it_ba FROM @sql_it_ba;
EXECUTE stmt_it_ba;
DEALLOCATE PREPARE stmt_it_ba;

-- 9) Global international status control
CREATE TABLE IF NOT EXISTS international_status (
  id INT PRIMARY KEY,
  status ENUM('none','pending','failed','reversed','network_error') NOT NULL DEFAULT 'none',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO international_status (id, status)
SELECT 1, 'none'
WHERE NOT EXISTS (SELECT 1 FROM international_status WHERE id = 1);

-- 10) International banks directory (autocomplete)
CREATE TABLE IF NOT EXISTS international_banks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  country_code VARCHAR(2) NOT NULL,
  bank_name VARCHAR(180) NOT NULL,
  swift_prefix VARCHAR(20) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_country_bank (country_code, bank_name)
);

-- Seed 20 banks per supported country (UK, US, CA + 10 EU)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'HSBC UK', 'HBUK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='HSBC UK');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Barclays', 'BARC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Barclays');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Lloyds Bank', 'LOYD' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Lloyds Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'NatWest', 'NWBK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='NatWest');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Santander UK', 'ABBY' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Santander UK');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Standard Chartered UK', 'SCBL' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Standard Chartered UK');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'TSB Bank', 'TSBS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='TSB Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Nationwide Building Society', 'NAIA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Nationwide Building Society');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Halifax', 'HLFX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Halifax');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Bank of Scotland', 'BOFS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Bank of Scotland');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Monzo Bank', 'MONZ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Monzo Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Revolut Ltd', 'REVO' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Revolut Ltd');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Starling Bank', 'SRLG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Starling Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Metro Bank', 'MYMB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Metro Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Clydesdale Bank', 'CLYD' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Clydesdale Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Virgin Money UK', 'VMUK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Virgin Money UK');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Co-operative Bank', 'CPBK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Co-operative Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Close Brothers', 'CLOS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Close Brothers');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Coutts', 'COUT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Coutts');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GB', 'Weatherbys Bank', 'WEAT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GB' AND bank_name='Weatherbys Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'JPMorgan Chase Bank', 'CHAS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='JPMorgan Chase Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Bank of America', 'BOFA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Bank of America');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Citibank', 'CITI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Citibank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Wells Fargo Bank', 'WFBI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Wells Fargo Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Goldman Sachs Bank', 'GSUS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Goldman Sachs Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Morgan Stanley Bank', 'MSUS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Morgan Stanley Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'U.S. Bank', 'USBK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='U.S. Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'PNC Bank', 'PNCC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='PNC Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'TD Bank USA', 'NRTH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='TD Bank USA');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Capital One', 'NFBK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Capital One');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Charles Schwab Bank', 'CSCH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Charles Schwab Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'BBVA USA', 'BBVA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='BBVA USA');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Truist Bank', 'BRBT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Truist Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Fifth Third Bank', 'FTBC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Fifth Third Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'KeyBank', 'KEYB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='KeyBank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'BMO Harris Bank', 'HATR' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='BMO Harris Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Regions Bank', 'UPOB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Regions Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Ally Bank', 'ALLY' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Ally Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'Discover Bank', 'DCOV' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='Discover Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'US', 'First Republic Bank', 'FRBB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='US' AND bank_name='First Republic Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Royal Bank of Canada', 'ROYCCAT2' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Royal Bank of Canada');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'TD Canada Trust', 'TDOMCATTTOR' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='TD Canada Trust');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Scotiabank', 'NOSCCATT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Scotiabank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Bank of Montreal', 'BOFMCAM2' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Bank of Montreal');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'CIBC', 'CIBCCATT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='CIBC');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'National Bank of Canada', 'BNDCCAMM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='National Bank of Canada');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'HSBC Bank Canada', 'HKBCCATT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='HSBC Bank Canada');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Laurentian Bank of Canada', 'LAVACA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Laurentian Bank of Canada');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Canadian Western Bank', 'CWBCCATT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Canadian Western Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Desjardins', 'DJDACAT2' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Desjardins');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Tangerine Bank', 'INGDCAT2' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Tangerine Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Simplii Financial', 'CIBCCATT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Simplii Financial');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'EQ Bank', 'EQBKCA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='EQ Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Alterna Bank', 'ALTRCA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Alterna Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'ATB Financial', 'ATBFCA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='ATB Financial');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Servus Credit Union', 'SERVCA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Servus Credit Union');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Vancity', 'VANC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Vancity');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Coast Capital Savings', 'COASCA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Coast Capital Savings');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'Meridian Credit Union', 'MERICA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='Meridian Credit Union');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CA', 'FirstOntario Credit Union', 'FONTC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CA' AND bank_name='FirstOntario Credit Union');

-- EU seeds (20 banks total spread across 10 countries; 2 each to meet baseline directory size)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'FI', 'Nordea Bank Finland', 'NDEAFIHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='FI' AND bank_name='Nordea Bank Finland');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'FI', 'OP Financial Group', 'OKOYFIHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='FI' AND bank_name='OP Financial Group');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'National Bank of Greece', 'ETHNGRAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='National Bank of Greece');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Piraeus Bank', 'PIRBGRAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Piraeus Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'OTP Bank', 'OTPVHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='OTP Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'K&H Bank', 'OKHBHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='K&H Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'Ceska sporitelna', 'GIBACZPX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='Ceska sporitelna');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'CSOB', 'CEKOCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='CSOB');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'Banca Transilvania', 'BTRLRO22' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='Banca Transilvania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'BCR', 'RNCBROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='BCR');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BG', 'UniCredit Bulbank', 'UNCRBGSF' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BG' AND bank_name='UniCredit Bulbank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BG', 'DSK Bank', 'STSABGSF' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BG' AND bank_name='DSK Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HR', 'Zagrebacka banka', 'ZABAHR2X' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HR' AND bank_name='Zagrebacka banka');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HR', 'Privredna banka Zagreb', 'PBZGHR2X' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HR' AND bank_name='Privredna banka Zagreb');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SK', 'Slovenska sporitelna', 'GIBASKBX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SK' AND bank_name='Slovenska sporitelna');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SK', 'Tatra banka', 'TATRSKBX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SK' AND bank_name='Tatra banka');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SI', 'NLB d.d.', 'LJBASI2X' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SI' AND bank_name='NLB d.d.');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SI', 'Nova KBM', 'KBMASI2X' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SI' AND bank_name='Nova KBM');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'LT', 'Swedbank Lithuania', 'HABALT22' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='LT' AND bank_name='Swedbank Lithuania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'LT', 'SEB Lithuania', 'CBVILT2X' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='LT' AND bank_name='SEB Lithuania');

-- Switzerland (CH)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CH', 'UBS Switzerland AG', 'UBSWCHZH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CH' AND bank_name='UBS Switzerland AG');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CH', 'Credit Suisse', 'CRESCHZZ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CH' AND bank_name='Credit Suisse');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CH', 'Zuercher Kantonalbank', 'ZKBKCHZZ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CH' AND bank_name='Zuercher Kantonalbank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CH', 'Raiffeisen Switzerland', 'RAIFCH22' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CH' AND bank_name='Raiffeisen Switzerland');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CH', 'PostFinance', 'POFICHBE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CH' AND bank_name='PostFinance');

-- Germany (DE)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DE', 'Deutsche Bank', 'DEUTDEFF' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DE' AND bank_name='Deutsche Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DE', 'Commerzbank', 'COBADEFF' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DE' AND bank_name='Commerzbank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DE', 'DZ Bank', 'GENODEFF' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DE' AND bank_name='DZ Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DE', 'KfW Bankengruppe', 'KFWIDEFF' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DE' AND bank_name='KfW Bankengruppe');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DE', 'Landesbank Baden-Wuerttemberg', 'SOLADEST' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DE' AND bank_name='Landesbank Baden-Wuerttemberg');

-- Spain (ES)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ES', 'Banco Santander', 'BSCHESMM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ES' AND bank_name='Banco Santander');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ES', 'BBVA', 'BBVAESMM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ES' AND bank_name='BBVA');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ES', 'CaixaBank', 'CABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ES' AND bank_name='CaixaBank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ES', 'Banco Sabadell', 'BSABESBB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ES' AND bank_name='Banco Sabadell');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ES', 'Bankinter', 'BKBKESMM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ES' AND bank_name='Bankinter');

-- Italy (IT)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IT', 'Intesa Sanpaolo', 'BCITITMM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IT' AND bank_name='Intesa Sanpaolo');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IT', 'UniCredit', 'UNCRITMM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IT' AND bank_name='UniCredit');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IT', 'Banca Monte dei Paschi', 'PASCITM1' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IT' AND bank_name='Banca Monte dei Paschi');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IT', 'Mediobanca', 'MABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IT' AND bank_name='Mediobanca');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IT', 'Banco BPM', 'BAPPIT21' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IT' AND bank_name='Banco BPM');

-- France (FR)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'FR', 'BNP Paribas', 'BNPAFRPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='FR' AND bank_name='BNP Paribas');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'FR', 'Credit Agricole', 'AGRIFRPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='FR' AND bank_name='Credit Agricole');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'FR', 'Societe Generale', 'SOGEFRPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='FR' AND bank_name='Societe Generale');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'FR', 'Groupe BPCE', 'CEPAFRPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='FR' AND bank_name='Groupe BPCE');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'FR', 'Credit Mutuel', 'CMCIFRPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='FR' AND bank_name='Credit Mutuel');

-- Denmark (DK)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DK', 'Danske Bank', 'DABADKKK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DK' AND bank_name='Danske Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DK', 'Nordea Denmark', 'NDEADKKK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DK' AND bank_name='Nordea Denmark');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DK', 'Jyske Bank', 'JYBADKKK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DK' AND bank_name='Jyske Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DK', 'Nykredit Bank', 'NABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DK' AND bank_name='Nykredit Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'DK', 'Sydbank', 'SABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='DK' AND bank_name='Sydbank');

-- Austria (AT)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AT', 'Erste Group Bank', 'GIBAATWW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AT' AND bank_name='Erste Group Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AT', 'Raiffeisen Bank International', 'RZBAATWW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AT' AND bank_name='Raiffeisen Bank International');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AT', 'UniCredit Bank Austria', 'BKAUATWW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AT' AND bank_name='UniCredit Bank Austria');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AT', 'BAWAG PSK', 'BAWAATWW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AT' AND bank_name='BAWAG PSK');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AT', 'Oberbank', 'OBKLAT2L' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AT' AND bank_name='Oberbank');

-- Netherlands (NL)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NL', 'ING Bank', 'INGBNL2A' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NL' AND bank_name='ING Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NL', 'ABN AMRO', 'ABNANL2A' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NL' AND bank_name='ABN AMRO');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NL', 'Rabobank', 'RABONL2U' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NL' AND bank_name='Rabobank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NL', 'De Volksbank', 'SNSBNL2A' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NL' AND bank_name='De Volksbank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NL', 'Triodos Bank', 'TRIONL2U' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NL' AND bank_name='Triodos Bank');

-- Belgium (BE)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BE', 'KBC Bank', 'KREDBEBB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BE' AND bank_name='KBC Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BE', 'BNP Paribas Fortis', 'GEBABEBB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BE' AND bank_name='BNP Paribas Fortis');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BE', 'Belfius Bank', 'GKCCBEBB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BE' AND bank_name='Belfius Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BE', 'ING Belgium', 'BBRUBEBB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BE' AND bank_name='ING Belgium');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BE', 'Argenta', 'ARSPBE22' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BE' AND bank_name='Argenta');

-- Sweden (SE)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SE', 'Swedbank', 'SWEDSESS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SE' AND bank_name='Swedbank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SE', 'Handelsbanken', 'HANDSESS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SE' AND bank_name='Handelsbanken');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SE', 'SEB', 'ESSESESS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SE' AND bank_name='SEB');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SE', 'Nordea Sweden', 'NDEASESS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SE' AND bank_name='Nordea Sweden');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SE', 'Lansforsakringar Bank', 'ELLFSESS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SE' AND bank_name='Lansforsakringar Bank');

-- Norway (NO)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NO', 'DNB Bank', 'DNBANOKK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NO' AND bank_name='DNB Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NO', 'Nordea Norway', 'NDEANOKK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NO' AND bank_name='Nordea Norway');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NO', 'SpareBank 1', 'SPSONO22' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NO' AND bank_name='SpareBank 1');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NO', 'Handelsbanken Norway', 'HANDNOKK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NO' AND bank_name='Handelsbanken Norway');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NO', 'Sbanken', 'SBAKNOBB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NO' AND bank_name='Sbanken');

-- Portugal (PT)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PT', 'Caixa Geral de Depositos', 'CGDIPTPL' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PT' AND bank_name='Caixa Geral de Depositos');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PT', 'Millennium bcp', 'BCOMPTPL' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PT' AND bank_name='Millennium bcp');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PT', 'Novo Banco', 'BESCPTPL' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PT' AND bank_name='Novo Banco');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PT', 'Santander Totta', 'TOTAPTPL' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PT' AND bank_name='Santander Totta');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PT', 'BPI', 'BBPIPTPL' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PT' AND bank_name='BPI');

-- Ireland (IE)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IE', 'Bank of Ireland', 'BOFIIE2D' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IE' AND bank_name='Bank of Ireland');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IE', 'AIB', 'AABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IE' AND bank_name='AIB');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IE', 'Ulster Bank Ireland', 'ABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IE' AND bank_name='Ulster Bank Ireland');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IE', 'Permanent TSB', 'IPBSIE2D' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IE' AND bank_name='Permanent TSB');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'IE', 'KBC Bank Ireland', 'KABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='IE' AND bank_name='KBC Bank Ireland');

-- 12) African Banks for Local Transfers (15 countries, 5 banks each)

-- South Africa (ZA)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'Standard Bank', 'SBZAZAJJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='Standard Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'First National Bank', 'FIABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='First National Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'Absa Bank', 'ABSAZAJJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='Absa Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'Nedbank', 'NEDSZAJJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='Nedbank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'Capitec Bank', 'CABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='Capitec Bank');

-- Ghana (GH)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GH', 'GCB Bank', 'GHCBGHAC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GH' AND bank_name='GCB Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GH', 'Ecobank Ghana', 'EABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GH' AND bank_name='Ecobank Ghana');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GH', 'Stanbic Bank Ghana', 'SBICGHAC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GH' AND bank_name='Stanbic Bank Ghana');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GH', 'Fidelity Bank Ghana', 'FABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GH' AND bank_name='Fidelity Bank Ghana');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GH', 'CalBank', 'ACABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GH' AND bank_name='CalBank');

-- Egypt (EG)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'National Bank of Egypt', 'NBEGEGCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='National Bank of Egypt');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'Banque Misr', 'BMISEGCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='Banque Misr');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'Commercial International Bank', 'CIABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='Commercial International Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'QNB Alahli', 'QABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='QNB Alahli');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'Arab African International Bank', 'ARABEGCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='Arab African International Bank');

-- Kenya (KE)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'Kenya Commercial Bank', 'KCABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='Kenya Commercial Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'Equity Bank Kenya', 'EABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='Equity Bank Kenya');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'Co-operative Bank of Kenya', 'KCOOKENA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='Co-operative Bank of Kenya');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'Standard Chartered Kenya', 'SCBLKENX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='Standard Chartered Kenya');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'Absa Bank Kenya', 'BABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='Absa Bank Kenya');

-- Uganda (UG)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'UG', 'Stanbic Bank Uganda', 'SBICUGKA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='UG' AND bank_name='Stanbic Bank Uganda');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'UG', 'Standard Chartered Uganda', 'SCBLUGKA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='UG' AND bank_name='Standard Chartered Uganda');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'UG', 'Centenary Bank', 'CEABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='UG' AND bank_name='Centenary Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'UG', 'DFCU Bank', 'DFCUUGKA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='UG' AND bank_name='DFCU Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'UG', 'Absa Bank Uganda', 'BABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='UG' AND bank_name='Absa Bank Uganda');

-- Cameroon (CM)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CM', 'Afriland First Bank', 'ABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CM' AND bank_name='Afriland First Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CM', 'Societe Generale Cameroun', 'SGCMCMCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CM' AND bank_name='Societe Generale Cameroun');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CM', 'Ecobank Cameroon', 'EABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CM' AND bank_name='Ecobank Cameroon');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CM', 'UBA Cameroon', 'UNABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CM' AND bank_name='UBA Cameroon');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CM', 'BICEC', 'BICECMCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CM' AND bank_name='BICEC');

-- Tanzania (TZ)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TZ', 'CRDB Bank', 'COABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TZ' AND bank_name='CRDB Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TZ', 'NMB Bank', 'NMIBTZTZ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TZ' AND bank_name='NMB Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TZ', 'Stanbic Bank Tanzania', 'SBICTZTX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TZ' AND bank_name='Stanbic Bank Tanzania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TZ', 'Exim Bank Tanzania', 'EXABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TZ' AND bank_name='Exim Bank Tanzania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TZ', 'Equity Bank Tanzania', 'EABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TZ' AND bank_name='Equity Bank Tanzania');

-- Morocco (MA)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'MA', 'Attijariwafa Bank', 'BCMAMAMC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='MA' AND bank_name='Attijariwafa Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'MA', 'Banque Populaire du Maroc', 'BCPOMAMC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='MA' AND bank_name='Banque Populaire du Maroc');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'MA', 'BMCE Bank', 'BMABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='MA' AND bank_name='BMCE Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'MA', 'Societe Generale Maroc', 'SGMB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='MA' AND bank_name='Societe Generale Maroc');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'MA', 'CIH Bank', 'CIHMMAMC' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='MA' AND bank_name='CIH Bank');

-- Rwanda (RW)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RW', 'Bank of Kigali', 'BABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RW' AND bank_name='Bank of Kigali');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RW', 'Equity Bank Rwanda', 'EABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RW' AND bank_name='Equity Bank Rwanda');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RW', 'I&M Bank Rwanda', 'IMBORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RW' AND bank_name='I&M Bank Rwanda');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RW', 'Access Bank Rwanda', 'AABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RW' AND bank_name='Access Bank Rwanda');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RW', 'Ecobank Rwanda', 'EABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RW' AND bank_name='Ecobank Rwanda');

-- Senegal (SN)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SN', 'CBAO Groupe Attijariwafa', 'CBAOSNDA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SN' AND bank_name='CBAO Groupe Attijariwafa');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SN', 'Societe Generale Senegal', 'SGSNSNDA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SN' AND bank_name='Societe Generale Senegal');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SN', 'Ecobank Senegal', 'EABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SN' AND bank_name='Ecobank Senegal');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SN', 'Bank of Africa Senegal', 'BOABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SN' AND bank_name='Bank of Africa Senegal');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SN', 'UBA Senegal', 'UNABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SN' AND bank_name='UBA Senegal');

-- Ivory Coast (CI)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CI', 'Societe Generale Cote dIvoire', 'SGCICICX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CI' AND bank_name='Societe Generale Cote dIvoire');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CI', 'Ecobank Cote dIvoire', 'EABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CI' AND bank_name='Ecobank Cote dIvoire');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CI', 'NSIA Banque', 'NSIACIAB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CI' AND bank_name='NSIA Banque');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CI', 'Bank of Africa Cote dIvoire', 'AFRIBJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CI' AND bank_name='Bank of Africa Cote dIvoire');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CI', 'UBA Cote dIvoire', 'UNABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CI' AND bank_name='UBA Cote dIvoire');

-- Ethiopia (ET)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ET', 'Commercial Bank of Ethiopia', 'CBETETAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ET' AND bank_name='Commercial Bank of Ethiopia');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ET', 'Dashen Bank', 'ABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ET' AND bank_name='Dashen Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ET', 'Awash Bank', 'AABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ET' AND bank_name='Awash Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ET', 'Bank of Abyssinia', 'BOABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ET' AND bank_name='Bank of Abyssinia');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ET', 'United Bank Ethiopia', 'UNITETAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ET' AND bank_name='United Bank Ethiopia');

-- Botswana (BW)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BW', 'First National Bank Botswana', 'FIABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BW' AND bank_name='First National Bank Botswana');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BW', 'Standard Chartered Botswana', 'SCBLBWGX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BW' AND bank_name='Standard Chartered Botswana');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BW', 'Absa Bank Botswana', 'BABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BW' AND bank_name='Absa Bank Botswana');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BW', 'Stanbic Bank Botswana', 'SBICBWGX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BW' AND bank_name='Stanbic Bank Botswana');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BW', 'Bank of Botswana', 'BBABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BW' AND bank_name='Bank of Botswana');

-- Zambia (ZM)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZM', 'Zanaco', 'ABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZM' AND bank_name='Zanaco');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZM', 'Stanbic Bank Zambia', 'SBICZMLX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZM' AND bank_name='Stanbic Bank Zambia');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZM', 'Standard Chartered Zambia', 'SCBLZMLX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZM' AND bank_name='Standard Chartered Zambia');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZM', 'Absa Bank Zambia', 'BABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZM' AND bank_name='Absa Bank Zambia');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZM', 'First National Bank Zambia', 'FIABORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZM' AND bank_name='First National Bank Zambia');

-- Zimbabwe (ZW)
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZW', 'CBZ Bank', 'COBZZWHA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZW' AND bank_name='CBZ Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZW', 'Standard Chartered Zimbabwe', 'SCBLZWHX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZW' AND bank_name='Standard Chartered Zimbabwe');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZW', 'Stanbic Bank Zimbabwe', 'SBICZWHX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZW' AND bank_name='Stanbic Bank Zimbabwe');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZW', 'FBC Bank', 'FBCZWHHA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZW' AND bank_name='FBC Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZW', 'NMB Bank Zimbabwe', 'NMBORNES' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZW' AND bank_name='NMB Bank Zimbabwe');

-- 13) International sender settings (admin-managed, shown on receipt)
CREATE TABLE IF NOT EXISTS international_sender_settings (
  id INT PRIMARY KEY,
  sender_name VARCHAR(150) NOT NULL,
  sender_bank VARCHAR(150) NOT NULL,
  sender_country VARCHAR(100) NOT NULL,
  sender_address_line1 VARCHAR(120) NOT NULL,
  sender_address_line2 VARCHAR(120) NULL,
  sender_address_line3 VARCHAR(120) NULL,
  sender_swift VARCHAR(30) NULL,
  sender_iban VARCHAR(60) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add default_delivery_date column if missing (MUST be done before INSERT)
SET @iss_dd := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_sender_settings'
    AND COLUMN_NAME = 'default_delivery_date'
);
SET @sql_iss_dd := IF(
  @iss_dd = 0,
  'ALTER TABLE international_sender_settings ADD COLUMN default_delivery_date DATE NULL AFTER sender_iban',
  'SELECT 1'
);
PREPARE stmt_iss_dd FROM @sql_iss_dd;
EXECUTE stmt_iss_dd;
DEALLOCATE PREPARE stmt_iss_dd;

-- Insert default sender settings (without default_delivery_date first)
INSERT INTO international_sender_settings
  (id, sender_name, sender_bank, sender_country, sender_address_line1, sender_address_line2, sender_address_line3, sender_swift, sender_iban)
SELECT
  1, 'Elysium Treasury Desk', 'Elysium Clearing Bank', 'United Kingdom',
  '1 Swift Square', 'Canary Wharf', 'London, E14', 'ELYSGB2L', 'GB00ELYS00000000000000'
WHERE NOT EXISTS (SELECT 1 FROM international_sender_settings WHERE id = 1);

-- Set a default delivery date if none exists (3 days from now)
UPDATE international_sender_settings SET default_delivery_date = DATE_ADD(CURDATE(), INTERVAL 3 DAY) WHERE id = 1 AND default_delivery_date IS NULL;

-- Receipt logo (admin upload, optional — falls back to SWIFT asset in UI)
SET @iss_logo := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_sender_settings'
    AND COLUMN_NAME = 'receipt_logo_path'
);
SET @sql_iss_logo := IF(
  @iss_logo = 0,
  'ALTER TABLE international_sender_settings ADD COLUMN receipt_logo_path VARCHAR(255) NULL AFTER default_delivery_date',
  'SELECT 1'
);
PREPARE stmt_iss_logo FROM @sql_iss_logo;
EXECUTE stmt_iss_logo;
DEALLOCATE PREPARE stmt_iss_logo;

