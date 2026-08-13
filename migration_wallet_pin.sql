-- Forward-only: ensure wallet PIN setting rows exist.
-- wallet_pin: password_hash for verification
-- wallet_pin_enc: encrypted copy for admin clipboard only (never displayed)

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
