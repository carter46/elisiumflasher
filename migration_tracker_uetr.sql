-- =============================================================================
-- Elysium Flasher — Institutional Tracker: UETR + tracker_status + OTP + settings
-- Safe-ish re-run: uses IF NOT EXISTS / ignore duplicate column patterns where possible.
-- Run once on the Flasher database after backup.
-- =============================================================================

-- Local transactions: UETR + tracker status
ALTER TABLE local_transactions
  ADD COLUMN uetr CHAR(36) NULL AFTER reference,
  ADD COLUMN tracker_status VARCHAR(16) NOT NULL DEFAULT 'PDNG' AFTER status,
  ADD COLUMN tracker_status_updated_at TIMESTAMP NULL DEFAULT NULL AFTER tracker_status;

ALTER TABLE local_transactions
  ADD UNIQUE KEY uq_local_transactions_uetr (uetr);

-- OTP challenges
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

-- App settings seeds
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_tracker_status', 'PDNG'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'local_tracker_status');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'tracker_api_key', LOWER(CONCAT(
  HEX(RANDOM_BYTES(8)), HEX(RANDOM_BYTES(8)), HEX(RANDOM_BYTES(8))
))
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'tracker_api_key');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_host', ''
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_host');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_port', '587'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_port');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_user', ''
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_user');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_pass', ''
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_pass');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_encryption', 'tls'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_encryption');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_from_email', ''
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_from_email');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'smtp_from_name', 'Elysium Tracker'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'smtp_from_name');

-- Backfill UETRs for existing rows (MySQL 8+ / MariaDB with RANDOM_BYTES).
-- Re-run safely: only fills NULL uetr values.
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
