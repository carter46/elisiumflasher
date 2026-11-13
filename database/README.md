# Database

| File | Use when |
|------|----------|
| `schema.sql` | **New / empty** database |
| `upgrade.sql` | **Existing** Flasher DB (safe to re-run) |

Flasher is **local-only**. International tables are not created; `upgrade.sql` drops them if present.

Tracker / mobile columns are also applied automatically by PHP on first API hit; these SQL files remain the documented install path for phpMyAdmin.
