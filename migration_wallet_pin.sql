-- Forward-only: ensure wallet_pin setting row exists (empty until admin configures).
-- Stores password_hash only — never plaintext.

INSERT INTO app_settings (setting_key, setting_value)
SELECT 'wallet_pin', NULL
WHERE NOT EXISTS (
  SELECT 1 FROM app_settings WHERE setting_key = 'wallet_pin'
);
