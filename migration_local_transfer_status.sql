-- Forward-only: global Local transfer statuses (any bank) + REVERSED tx status.

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_link_wallet_status', 'on'
WHERE NOT EXISTS (
  SELECT 1 FROM app_settings WHERE setting_key = 'local_link_wallet_status'
);

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'local_transfer_status', 'successful'
WHERE NOT EXISTS (
  SELECT 1 FROM app_settings WHERE setting_key = 'local_transfer_status'
);

-- Allow REVERSED on local transactions (MySQL). Safe if already present.
ALTER TABLE local_transactions
  MODIFY COLUMN status ENUM('SUCCESSFUL','FAILED','PENDING','REVERSED') NOT NULL DEFAULT 'SUCCESSFUL';
