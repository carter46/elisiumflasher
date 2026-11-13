# ELesium Flasher PHP App

## Setup

1. Import `if0_40085505_elysium.sql` (or your baseline dump) in phpMyAdmin.
2. After deploying code that removes international transfer, run `migration_remove_international.sql` on the same database.
3. Update `client_keys.client_key` with your real key.
4. Update Paystack settings in `app_settings`:
   - `paystack_test_secret_key`
   - `paystack_live_secret_key`
   - `paystack_use_live` (`0` for test, `1` for live)
5. Set DB credentials in `config.php` (or `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` env vars).
6. Run with XAMPP/Apache or PHP built-in server:
   - `php -S localhost:8000`
7. Open `http://localhost:8000/`.

## App Flow

- `index.php` (login) -> `transfer_selection.php`
- `transfer_selection.php` (Port / Server / Encryption / Server IP / Currency) -> `local_dashboard.php`
- Local transfer is Nigeria-only; currency is Naira (`NGN`) or Dollars (`USD`)
- Account resolve via `api/resolve_account.php`
- Successful transfer -> `local_transfer_success.php`
- Local dashboard content loads from MySQL via `api/dashboard_content.php?page=local`

## API Endpoints

- `POST /api/auth.php` with `{ "action": "login", "client_key": "..." }`
- `POST /api/auth.php` with `{ "action": "logout" }`
- `POST /api/auth.php` with `{ "action": "check" }`
- `POST /api/resolve_account.php` with `{ "account_number": "...", "bank_code": "..." }`

## Institutional Tracker

Local transfers now generate a UUID-v4 **UETR** and a separate **tracker_status** (independent of receipt status).

1. Run `migration_tracker_uetr.sql` (or rely on auto-schema ensure on first API hit).
2. Admin → **Tracker settings**: copy API key + configure SMTP for OTP email.
3. Admin → **Bank status**: set default tracker status; override per tx in Account settings.
4. Configure [`institutional_transaction_tracker/config/sites.php`](institutional_transaction_tracker/config/sites.php) (or `sites.local.php`) with Flasher/Server base URLs + API keys.
5. Serve the tracker folder separately: `php -S localhost:8080 -t institutional_transaction_tracker`

Elysium Server (dual local + international) lives under `elysium-server/` with the same S2S contract.
