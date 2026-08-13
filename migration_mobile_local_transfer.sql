-- Forward-only: mobile companion sessions/devices + bank code on local txs.
-- Does NOT modify wallet_pin values or format.

-- Ensure REVERSED is allowed (idempotent if already applied).
ALTER TABLE local_transactions
  MODIFY COLUMN status ENUM('SUCCESSFUL','FAILED','PENDING','REVERSED') NOT NULL DEFAULT 'SUCCESSFUL';

-- Store NUBAN bank_code from create payload for reliable mobile bank gating.
-- Safe if column already exists: run once; ignore duplicate-column error if re-run fails.
ALTER TABLE local_transactions
  ADD COLUMN beneficiary_bank_code VARCHAR(20) NULL DEFAULT NULL AFTER beneficiary_bank;

ALTER TABLE local_transactions
  ADD INDEX idx_lt_beneficiary_bank_code (beneficiary_bank_code);

ALTER TABLE local_transactions
  ADD INDEX idx_lt_beneficiary_account_status (beneficiary_account, status);

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

-- Ensure wallet PIN / local status setting rows exist (do not overwrite values).
INSERT INTO app_settings (setting_key, setting_value)
SELECT 'wallet_pin', NULL
WHERE NOT EXISTS (
  SELECT 1 FROM app_settings WHERE setting_key = 'wallet_pin'
);

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'wallet_pin_enc', NULL
WHERE NOT EXISTS (
  SELECT 1 FROM app_settings WHERE setting_key = 'wallet_pin_enc'
);

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_transfer_status', 'successful'
WHERE NOT EXISTS (
  SELECT 1 FROM app_settings WHERE setting_key = 'local_transfer_status'
);

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_link_wallet_status', 'on'
WHERE NOT EXISTS (
  SELECT 1 FROM app_settings WHERE setting_key = 'local_link_wallet_status'
);
