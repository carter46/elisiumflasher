-- =============================================================================
-- Elysium Flasher - forward migration: remove international transfer
-- =============================================================================
-- Target database: if0_40085505_elysium
--
-- Run this in phpMyAdmin AFTER deploying application code that no longer
-- queries international_* tables (local transfer Nigeria-only).
--
-- Safe for re-run: uses DROP TABLE IF EXISTS.
-- Does NOT modify local_*, platform_status, bank_status, app_settings, or auth.
--
-- Destructive: permanently removes international transfer history and seeds.
-- Backup the database before importing if you need that data.
--
-- Historical files left unchanged:
--   migration.sql, migration_full_legacy.sql, if0_40085505_elysium.sql
-- =============================================================================

DROP TABLE IF EXISTS international_transactions;
DROP TABLE IF EXISTS international_dashboard_profile;
DROP TABLE IF EXISTS international_recent_transfers;
DROP TABLE IF EXISTS international_sender_settings;
DROP TABLE IF EXISTS international_status;
DROP TABLE IF EXISTS international_banks;
