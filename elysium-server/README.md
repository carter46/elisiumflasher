# ELesium Flasher PHP App

## Setup

1. Import `database.sql` in phpMyAdmin.
2. Update `client_keys.client_key` with your real key.
3. Update Paystack settings in `app_settings`:
   - `paystack_test_secret_key`
   - `paystack_live_secret_key`
   - `paystack_use_live` (`0` for test, `1` for live)
4. Set DB credentials in `config.php` (or `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` env vars).
5. Run with XAMPP/Apache or PHP built-in server:
   - `php -S localhost:8000`
6. Open `http://localhost:8000/login.php`.

## App Flow

- `login.php` -> `transfer_selection.php`
- `transfer_selection.php` -> `local_dashboard.php` or `international_dashboard.php`
- Local transfer resolves account name via `api/resolve_account.php`
- Submit local transfer -> `local_transfer_processing.php`
- Local/International dashboard visible content now loads from MySQL via `api/dashboard_content.php`

## API Endpoints

- `POST /api/auth.php` with `{ "action": "login", "client_key": "..." }`
- `POST /api/auth.php` with `{ "action": "logout" }`
- `POST /api/auth.php` with `{ "action": "check" }`
- `POST /api/resolve_account.php` with `{ "account_number": "...", "bank_code": "..." }`
