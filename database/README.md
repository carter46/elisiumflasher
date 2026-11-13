# Database

| File | Use when |
|------|----------|
| `schema.sql` | **New / empty** database |
| `upgrade.sql` | **Existing** Flasher DB (manual phpMyAdmin import; safe to re-run) |
| `auto-migrations/` | **Automatic** upgrades when an admin logs in or loads any admin page |

Flasher is **local-only**. International tables are not created; `upgrade.sql` / auto-migrate drops them if present.

## Auto-migrate (recommended for live older DBs)

Same pattern as Axion Trust Bank:

1. Admin logs in **or** opens any `/admin/*` page / admin API.
2. `includes/database_auto_migrate.php` runs once per request.
3. Pending files under `database/auto-migrations/*.php` apply and are recorded in `auto_migrations`.

To ship a future schema change, add a new dated PHP file in `auto-migrations/` (do not edit an already-applied id).
