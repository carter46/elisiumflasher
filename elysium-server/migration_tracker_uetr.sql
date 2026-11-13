-- =============================================================================
-- Elysium Server — Institutional Tracker: UETR + tracker_status (local + intl)
-- =============================================================================

ALTER TABLE local_transactions
  ADD COLUMN uetr CHAR(36) NULL AFTER reference,
  ADD COLUMN tracker_status VARCHAR(16) NOT NULL DEFAULT 'PDNG' AFTER status,
  ADD COLUMN tracker_status_updated_at TIMESTAMP NULL DEFAULT NULL AFTER tracker_status;

ALTER TABLE international_transactions
  ADD COLUMN uetr CHAR(36) NULL AFTER reference,
  ADD COLUMN tracker_status VARCHAR(16) NOT NULL DEFAULT 'PDNG' AFTER status,
  ADD COLUMN tracker_status_updated_at TIMESTAMP NULL DEFAULT NULL AFTER tracker_status;

ALTER TABLE local_transactions ADD UNIQUE KEY uq_local_transactions_uetr (uetr);
ALTER TABLE international_transactions ADD UNIQUE KEY uq_international_transactions_uetr (uetr);

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

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_tracker_status', 'PDNG' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'local_tracker_status');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'international_tracker_status', 'PDNG' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'international_tracker_status');

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'tracker_api_key', LOWER(CONCAT(HEX(RANDOM_BYTES(8)), HEX(RANDOM_BYTES(8)), HEX(RANDOM_BYTES(8))))
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM app_settings WHERE setting_key = 'tracker_api_key');
