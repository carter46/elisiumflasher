-- =============================================================================
-- Elysium Flasher - incremental migration (lean)
-- =============================================================================
-- Run this on an EXISTING database to add missing tables/columns. Safe to run
-- multiple times (idempotent): uses CREATE TABLE IF NOT EXISTS and conditional
-- ALTER only. It does NOT drop tables, truncate data, or mass-reseed banks.
--
-- * Brand-new empty database: import `database.sql` first (full schema + seeds).
-- * Historical one-off seed dump (banks, etc.): see `migration_full_legacy.sql`
--   (reference only - do not re-import unless you intend to add missing seed rows).
--
-- In phpMyAdmin: select your database, then Import this file.
-- =============================================================================

-- ---------------------------------------------------------------------------
-- 1) Tables (current shape - skipped if already present)
-- ---------------------------------------------------------------------------

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
  account_number VARCHAR(30) NOT NULL DEFAULT '',
  balance DECIMAL(18,2) NOT NULL,
  masked_pan VARCHAR(30) NOT NULL,
  tier_label VARCHAR(50) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

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

CREATE TABLE IF NOT EXISTS local_transfer_banks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bank_name VARCHAR(120) NOT NULL UNIQUE,
  bank_code VARCHAR(20) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS local_recent_transfers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient_name VARCHAR(150) NOT NULL,
  subtitle VARCHAR(180) NOT NULL,
  amount DECIMAL(18,2) NOT NULL,
  direction ENUM('debit','credit') NOT NULL DEFAULT 'debit',
  status_label VARCHAR(30) NOT NULL DEFAULT 'Success',
  sort_order INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS local_frequent_recipients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient_name VARCHAR(150) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS international_dashboard_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  balance_usd DECIMAL(18,2) NOT NULL,
  vault_label VARCHAR(100) NOT NULL,
  masked_pan VARCHAR(30) NOT NULL,
  route_label VARCHAR(100) NOT NULL,
  route_description VARCHAR(255) NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS international_recent_transfers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient_name VARCHAR(150) NOT NULL,
  subtitle VARCHAR(180) NOT NULL,
  transfer_date VARCHAR(80) NOT NULL,
  amount_usd DECIMAL(18,2) NOT NULL,
  currency VARCHAR(3) NOT NULL DEFAULT 'USD',
  status_label VARCHAR(30) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS international_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reference VARCHAR(100) NOT NULL UNIQUE,
  beneficiary_iban VARCHAR(60) NOT NULL,
  beneficiary_name VARCHAR(150) NOT NULL,
  beneficiary_address TEXT NOT NULL DEFAULT '',
  swift_code VARCHAR(30) NOT NULL DEFAULT '',
  routing_number VARCHAR(9) NOT NULL DEFAULT '',
  bank_name VARCHAR(150) NOT NULL,
  country_code VARCHAR(2) NOT NULL DEFAULT '',
  country_name VARCHAR(100) NOT NULL DEFAULT '',
  amount DECIMAL(18,2) NOT NULL,
  currency VARCHAR(3) NOT NULL,
  message_type VARCHAR(80) NOT NULL,
  delivery_date DATE NOT NULL,
  status ENUM('SUCCESSFUL','PENDING','PROCESSING','FAILED','REVERSED','NETWORK_ERROR') NOT NULL DEFAULT 'SUCCESSFUL',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS international_status (
  id INT PRIMARY KEY,
  status ENUM('none','pending','processing','failed','reversed','network_error') NOT NULL DEFAULT 'pending',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS international_banks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  country_code VARCHAR(2) NOT NULL,
  bank_name VARCHAR(180) NOT NULL,
  swift_prefix VARCHAR(20) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_country_bank (country_code, bank_name)
);

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
  default_delivery_date DATE NULL,
  receipt_logo_path VARCHAR(255) NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------------
-- 2) Columns added on older installs (safe if column already exists)
-- ---------------------------------------------------------------------------

SET @account_number_col_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'local_dashboard_profile'
    AND COLUMN_NAME = 'account_number'
);
SET @account_number_add_sql := IF(
  @account_number_col_exists = 0,
  'ALTER TABLE local_dashboard_profile ADD COLUMN account_number VARCHAR(30) NOT NULL DEFAULT '''' AFTER account_name',
  'SELECT 1'
);
PREPARE stmt FROM @account_number_add_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

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

ALTER TABLE international_transactions
  MODIFY COLUMN status ENUM('SUCCESSFUL','PENDING','PROCESSING','FAILED','REVERSED','NETWORK_ERROR') NOT NULL DEFAULT 'SUCCESSFUL';

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

SET @it_cc := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_transactions'
    AND COLUMN_NAME = 'country_code'
);
SET @sql_it_cc := IF(
  @it_cc = 0,
  'ALTER TABLE international_transactions ADD COLUMN country_code VARCHAR(2) NOT NULL DEFAULT '''' AFTER bank_name',
  'SELECT 1'
);
PREPARE stmt_it_cc FROM @sql_it_cc;
EXECUTE stmt_it_cc;
DEALLOCATE PREPARE stmt_it_cc;

SET @it_cn := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_transactions'
    AND COLUMN_NAME = 'country_name'
);
SET @sql_it_cn := IF(
  @it_cn = 0,
  'ALTER TABLE international_transactions ADD COLUMN country_name VARCHAR(100) NOT NULL DEFAULT '''' AFTER country_code',
  'SELECT 1'
);
PREPARE stmt_it_cn FROM @sql_it_cn;
EXECUTE stmt_it_cn;
DEALLOCATE PREPARE stmt_it_cn;

SET @it_ba := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'international_transactions'
    AND COLUMN_NAME = 'beneficiary_address'
);
SET @sql_it_ba := IF(
  @it_ba = 0,
  'ALTER TABLE international_transactions ADD COLUMN beneficiary_address TEXT NOT NULL DEFAULT '''' AFTER beneficiary_name',
  'SELECT 1'
);
PREPARE stmt_it_ba FROM @sql_it_ba;
EXECUTE stmt_it_ba;
DEALLOCATE PREPARE stmt_it_ba;

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

-- ---------------------------------------------------------------------------
-- 3) Minimal rows (only if missing - does not overwrite existing data)
-- ---------------------------------------------------------------------------

INSERT INTO platform_status (id, status)
SELECT 1, 'on'
WHERE NOT EXISTS (SELECT 1 FROM platform_status WHERE id = 1);

INSERT INTO international_status (id, status)
SELECT 1, 'pending'
WHERE NOT EXISTS (SELECT 1 FROM international_status WHERE id = 1);

-- Normalize legacy installs that still use "none" as default.
UPDATE international_status
SET status = 'pending'
WHERE id = 1 AND status = 'none';

INSERT INTO international_sender_settings
  (id, sender_name, sender_bank, sender_country, sender_address_line1, sender_address_line2, sender_address_line3, sender_swift, sender_iban)
SELECT
  1, 'Elysium Treasury Desk', 'Elysium Clearing Bank', 'United Kingdom',
  '1 Swift Square', 'Canary Wharf', 'London, E14', 'ELYSGB2L', 'GB00ELYS00000000000000'
WHERE NOT EXISTS (SELECT 1 FROM international_sender_settings WHERE id = 1);

UPDATE international_sender_settings
SET default_delivery_date = DATE_ADD(CURDATE(), INTERVAL 3 DAY)
WHERE id = 1 AND default_delivery_date IS NULL;

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'paystack_resolve_enabled', '1'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'paystack_resolve_enabled');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'flutterwave_resolve_enabled', '0'
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'flutterwave_resolve_enabled');

-- ---------------------------------------------------------------------------
-- 4) International expansion (country coverage + banks)
--    Adds 10 banks each for:
--    * Middle East: AE, SA, QA, KW, BH
--    * East Asia: JP, CN, KR, SG, HK
--    * Africa (intl): ZA, NG, EG, TN, KE
--    * Europe: PL, CZ, GR, HU, RO
-- ---------------------------------------------------------------------------
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'Emirates NBD', 'EBILAEAD' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='Emirates NBD');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'First Abu Dhabi Bank', 'NBADAEAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='First Abu Dhabi Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'Abu Dhabi Commercial Bank', 'ADCBAEAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='Abu Dhabi Commercial Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'Dubai Islamic Bank', 'DUIBAEAD' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='Dubai Islamic Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'Mashreq Bank', 'BOMLAEAD' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='Mashreq Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'Commercial Bank of Dubai', 'CBDUAEAD' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='Commercial Bank of Dubai');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'RAKBANK', 'NRAKAEAK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='RAKBANK');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'Sharjah Islamic Bank', 'NBSHAEAS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='Sharjah Islamic Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'United Arab Bank', 'UABAEAAX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='United Arab Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'AE', 'National Bank of Fujairah', 'NBFUAEAF' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='AE' AND bank_name='National Bank of Fujairah');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Saudi National Bank', 'NCBKSAJE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Saudi National Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Al Rajhi Bank', 'RJHISARI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Al Rajhi Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Riyad Bank', 'RIBLSARI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Riyad Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Saudi Awwal Bank', 'SABBSARI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Saudi Awwal Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Banque Saudi Fransi', 'BSFRSARI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Banque Saudi Fransi');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Arab National Bank', 'ARNBSARI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Arab National Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Bank AlJazira', 'BJAZSAJE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Bank AlJazira');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Bank AlBilad', 'ALBISARI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Bank AlBilad');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'The Saudi Investment Bank', 'SIBCSARI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='The Saudi Investment Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SA', 'Bank Alinma', 'INMASARI' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SA' AND bank_name='Bank Alinma');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Qatar National Bank', 'QNBAQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Qatar National Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Commercial Bank of Qatar', 'CBQAQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Commercial Bank of Qatar');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Doha Bank', 'DOHBQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Doha Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Qatar Islamic Bank', 'QISBQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Qatar Islamic Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Masraf Al Rayan', 'MABFQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Masraf Al Rayan');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Al Khaliji Bank', 'KLJIQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Al Khaliji Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Qatar International Islamic Bank', 'QIIBQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Qatar International Islamic Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Ahli Bank QSC', 'ABQQQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Ahli Bank QSC');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Dukhan Bank', 'QIIBQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Dukhan Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'QA', 'Lesha Bank', 'QINVQAQA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='QA' AND bank_name='Lesha Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'National Bank of Kuwait', 'NBOKKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='National Bank of Kuwait');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Kuwait Finance House', 'KFHOKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Kuwait Finance House');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Burgan Bank', 'BURGKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Burgan Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Gulf Bank', 'GULBKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Gulf Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Commercial Bank of Kuwait', 'COMBKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Commercial Bank of Kuwait');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Boubyan Bank', 'BBYNKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Boubyan Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Warba Bank', 'WARBKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Warba Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Kuwait International Bank', 'KIBBKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Kuwait International Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Ahli United Bank Kuwait', 'AUBBKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Ahli United Bank Kuwait');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KW', 'Industrial Bank of Kuwait', 'IBOKKWKW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KW' AND bank_name='Industrial Bank of Kuwait');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'National Bank of Bahrain', 'NBOBBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='National Bank of Bahrain');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Bank of Bahrain and Kuwait', 'BBKUBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Bank of Bahrain and Kuwait');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Ahli United Bank Bahrain', 'AUBBBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Ahli United Bank Bahrain');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Bahrain Islamic Bank', 'BISBBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Bahrain Islamic Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Kuwait Finance House Bahrain', 'KFHBBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Kuwait Finance House Bahrain');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Al Salam Bank Bahrain', 'ALSABHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Al Salam Bank Bahrain');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Khaleeji Commercial Bank', 'KHCBBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Khaleeji Commercial Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Ithmaar Bank', 'FIBHBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Ithmaar Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Arab Banking Corporation', 'ABCOBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Arab Banking Corporation');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'BH', 'Bahrain Development Bank', 'BDBBBHBM' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='BH' AND bank_name='Bahrain Development Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Mitsubishi UFJ Bank', 'BOTKJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Mitsubishi UFJ Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Sumitomo Mitsui Banking Corporation', 'SMBCJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Sumitomo Mitsui Banking Corporation');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Mizuho Bank', 'MHCBJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Mizuho Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Resona Bank', 'DIWAJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Resona Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Japan Post Bank', 'JPPSJPJ1' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Japan Post Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'SBI Shinsei Bank', 'LTCBJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='SBI Shinsei Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Aozora Bank', 'NCBTJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Aozora Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Fukuoka Bank', 'FKBKJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Fukuoka Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Shizuoka Bank', 'SHIZJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Shizuoka Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'JP', 'Chiba Bank', 'CHBAJPJT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='JP' AND bank_name='Chiba Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'Industrial and Commercial Bank of China', 'ICBKCNBJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='Industrial and Commercial Bank of China');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'China Construction Bank', 'PCBCCNBJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='China Construction Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'Agricultural Bank of China', 'ABOCCNBJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='Agricultural Bank of China');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'Bank of China', 'BKCHCNBJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='Bank of China');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'Bank of Communications', 'COMMCNSH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='Bank of Communications');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'China Merchants Bank', 'CMBCCNBS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='China Merchants Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'China CITIC Bank', 'CIBKCNBJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='China CITIC Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'China Minsheng Bank', 'MSBCCNBJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='China Minsheng Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'Ping An Bank', 'SZDBCNBS' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='Ping An Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CN', 'Shanghai Pudong Development Bank', 'SPDBCNSH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CN' AND bank_name='Shanghai Pudong Development Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'KB Kookmin Bank', 'CZNBKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='KB Kookmin Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'Shinhan Bank', 'SHBKKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='Shinhan Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'Woori Bank', 'HVBKKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='Woori Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'Hana Bank', 'KOEXKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='Hana Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'Industrial Bank of Korea', 'IBKOKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='Industrial Bank of Korea');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'NongHyup Bank', 'NACFKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='NongHyup Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'Korea Development Bank', 'KODBKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='Korea Development Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'Suhyup Bank', 'NFFCKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='Suhyup Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'Citi Korea', 'CITIKRSX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='Citi Korea');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KR', 'SC First Bank Korea', 'SCBLKRSE' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KR' AND bank_name='SC First Bank Korea');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'DBS Bank', 'DBSSSGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='DBS Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'OCBC Bank', 'OCBCSGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='OCBC Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'United Overseas Bank', 'UOVBSGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='United Overseas Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'Standard Chartered Singapore', 'SCBLSGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='Standard Chartered Singapore');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'Citibank Singapore', 'CITISGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='Citibank Singapore');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'HSBC Singapore', 'HSBCSGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='HSBC Singapore');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'Maybank Singapore', 'MBBESGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='Maybank Singapore');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'RHB Bank Singapore', 'RHBBSGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='RHB Bank Singapore');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'Bank of China Singapore', 'BKCHSGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='Bank of China Singapore');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'SG', 'State Bank of India Singapore', 'SBINSGSG' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='SG' AND bank_name='State Bank of India Singapore');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'HSBC Hong Kong', 'HSBCHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='HSBC Hong Kong');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'Bank of China Hong Kong', 'BKCHHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='Bank of China Hong Kong');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'Hang Seng Bank', 'HASEHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='Hang Seng Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'Standard Chartered Hong Kong', 'SCBLHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='Standard Chartered Hong Kong');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'Bank of East Asia', 'BEASHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='Bank of East Asia');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'DBS Hong Kong', 'DHBKHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='DBS Hong Kong');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'Citibank Hong Kong', 'CITIHKHX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='Citibank Hong Kong');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'OCBC Wing Hang Bank', 'WIHBHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='OCBC Wing Hang Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'ICBC Asia', 'UBHKHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='ICBC Asia');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HK', 'China CITIC Bank International', 'KWHKHKHH' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HK' AND bank_name='China CITIC Bank International');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'Investec Bank', 'IVESZAJJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='Investec Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'TymeBank', 'TYMEZAJJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='TymeBank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'African Bank', 'AFRCZAJJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='African Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'Sasfin Bank', 'SASFZAJJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='Sasfin Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'ZA', 'Bidvest Bank', 'BIDBZAJJ' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='ZA' AND bank_name='Bidvest Bank');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'Zenith Bank', 'ZEIBNGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='Zenith Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'Guaranty Trust Bank', 'GTBINGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='Guaranty Trust Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'First Bank of Nigeria', 'FBNINGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='First Bank of Nigeria');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'United Bank for Africa', 'UNAFNGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='United Bank for Africa');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'Access Bank', 'ABNGNGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='Access Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'Ecobank Nigeria', 'ECOCNGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='Ecobank Nigeria');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'Fidelity Bank Nigeria', 'FIDTNGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='Fidelity Bank Nigeria');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'Sterling Bank', 'NAMENGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='Sterling Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'Stanbic IBTC Bank', 'SBICNGLX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='Stanbic IBTC Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'NG', 'Union Bank of Nigeria', 'UBNINGLA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='NG' AND bank_name='Union Bank of Nigeria');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'AlexBank', 'ALEXEGCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='AlexBank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'Bank of Alexandria', 'ALEXEGCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='Bank of Alexandria');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'HSBC Egypt', 'HSBCEGCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='HSBC Egypt');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'Arab Bank Egypt', 'ARBKEGCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='Arab Bank Egypt');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'EG', 'Faisal Islamic Bank Egypt', 'FAITEGCX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='EG' AND bank_name='Faisal Islamic Bank Egypt');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Banque de Tunisie', 'BTBKTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Banque de Tunisie');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Banque Internationale Arabe de Tunisie', 'BIATTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Banque Internationale Arabe de Tunisie');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Banque Nationale Agricole', 'BNATTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Banque Nationale Agricole');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Amen Bank', 'CFCTTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Amen Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Attijari Bank Tunisie', 'BSTUTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Attijari Bank Tunisie');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Arab Tunisian Bank', 'ATBKTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Arab Tunisian Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Zitouna Bank', 'BZITTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Zitouna Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Union Bancaire pour le Commerce et lIndustrie', 'UBCITNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Union Bancaire pour le Commerce et lIndustrie');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Banque de lHabitat', 'BHBKTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Banque de lHabitat');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'TN', 'Societe Tunisienne de Banque', 'STBKTNTT' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='TN' AND bank_name='Societe Tunisienne de Banque');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'NCBA Bank Kenya', 'CBAFKENX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='NCBA Bank Kenya');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'Diamond Trust Bank Kenya', 'DTKEKENA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='Diamond Trust Bank Kenya');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'I&M Bank Kenya', 'IMBLKENA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='I&M Bank Kenya');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'Family Bank', 'FABLKENA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='Family Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'KE', 'NCBK Bank Kenya', 'NCBAKENX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='KE' AND bank_name='NCBK Bank Kenya');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'PKO Bank Polski', 'BPKOPLPW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='PKO Bank Polski');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'Bank Pekao', 'PKOPPLPW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='Bank Pekao');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'Santander Bank Polska', 'WBKPPLPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='Santander Bank Polska');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'mBank', 'BREXPLPW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='mBank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'ING Bank Slaski', 'INGBPLPW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='ING Bank Slaski');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'Bank Millennium', 'BIGBPLPW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='Bank Millennium');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'Alior Bank', 'ALBPPLPW' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='Alior Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'Bank Handlowy', 'CITIPLPX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='Bank Handlowy');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'BNP Paribas Bank Polska', 'PPABPLPK' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='BNP Paribas Bank Polska');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'PL', 'Credit Agricole Bank Polska', 'AGRIPLPR' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='PL' AND bank_name='Credit Agricole Bank Polska');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'Komercni banka', 'KOMBCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='Komercni banka');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'UniCredit Bank Czech Republic', 'BACXCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='UniCredit Bank Czech Republic');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'Moneta Money Bank', 'AGBACZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='Moneta Money Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'Raiffeisenbank Czech Republic', 'RZBCCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='Raiffeisenbank Czech Republic');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'Air Bank', 'AIRACZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='Air Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'Fio banka', 'FIOBCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='Fio banka');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'Trinity Bank', 'MPUBCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='Trinity Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'J&T Banka', 'JTBPCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='J&T Banka');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'PPF banka', 'PMBPCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='PPF banka');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'CZ', 'mBank Czech Republic', 'BREXCZPP' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='CZ' AND bank_name='mBank Czech Republic');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Eurobank', 'ERBKGRAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Eurobank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Alpha Bank Greece', 'CRBAGRAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Alpha Bank Greece');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Attica Bank', 'ATTIGRAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Attica Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Optima Bank', 'OPTMGRAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Optima Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Pancreta Bank', 'CHQBGRAX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Pancreta Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Viva Bank', 'VIVBGR21' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Viva Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'ProCredit Bank Greece', 'PRCBGRAA' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='ProCredit Bank Greece');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'CrediaBank', 'PBAGRAXX' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='CrediaBank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Hellenic Development Bank', 'EATEGR21' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Hellenic Development Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'GR', 'Cooperative Bank of Epirus', 'STSPGR21' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='GR' AND bank_name='Cooperative Bank of Epirus');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'Erste Bank Hungary', 'GIBAHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='Erste Bank Hungary');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'UniCredit Bank Hungary', 'BACXHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='UniCredit Bank Hungary');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'Raiffeisen Bank Hungary', 'UBRTHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='Raiffeisen Bank Hungary');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'MBH Bank', 'MKKBHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='MBH Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'CIB Bank', 'CIBHHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='CIB Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'KDB Bank Europe', 'KDBLHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='KDB Bank Europe');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'MagNet Bank', 'HBWEHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='MagNet Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'Granit Bank', 'GNBAHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='Granit Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'Polgari Bank', 'POLBHU22' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='Polgari Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'HU', 'Cetelem Bank Hungary', 'CETEHUHB' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='HU' AND bank_name='Cetelem Bank Hungary');

INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'BRD Groupe Societe Generale', 'BRDEROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='BRD Groupe Societe Generale');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'Raiffeisen Bank Romania', 'RZBRROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='Raiffeisen Bank Romania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'UniCredit Bank Romania', 'BACXROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='UniCredit Bank Romania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'CEC Bank', 'CECOROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='CEC Bank');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'ING Bank Romania', 'INGBROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='ING Bank Romania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'Alpha Bank Romania', 'BUCUROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='Alpha Bank Romania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'OTP Bank Romania', 'OTPVROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='OTP Bank Romania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'Garanti BBVA Romania', 'UGBIROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='Garanti BBVA Romania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'Vista Bank Romania', 'VISTROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='Vista Bank Romania');
INSERT INTO international_banks (country_code, bank_name, swift_prefix)
SELECT 'RO', 'Libra Internet Bank', 'BRELROBU' WHERE NOT EXISTS (SELECT 1 FROM international_banks WHERE country_code='RO' AND bank_name='Libra Internet Bank');

-- =============================================================================
-- Done. Optional: import `database.sql` on a fresh DB for full seed data.
-- =============================================================================
