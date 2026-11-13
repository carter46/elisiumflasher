# Institutional Transaction Tracker

Public UETR lookup site that talks to one or more Elysium hosts over authenticated server-to-server APIs.

## Configure sites

1. On each Elysium host (Flasher and/or Server), open **Admin → Tracker settings**, copy the **Tracker API key**, and configure SMTP for OTP email.
2. Edit [`config/sites.php`](config/sites.php) or copy [`config/sites.local.php.example`](config/sites.local.php.example) to `config/sites.local.php` and set:

- `base_url` — full origin of that Elysium deploy (no trailing slash required)
- `api_key` — that host’s Tracker API key
- `enabled` — `true` to include in fan-out

Add a third site later by appending another array entry.

Env vars (optional): `TRACKER_BASE_FLASHER`, `TRACKER_KEY_FLASHER`, `TRACKER_BASE_SERVER`, `TRACKER_KEY_SERVER`.

## Flow

1. Visitor enters UETR  
2. Tracker fans out `tracker_locate` to all enabled sites (~5s loading UX)  
3. Visitor enters email → matched site sends OTP  
4. Visitor enters OTP → professional tracking payload rendered  

## Run locally

```bash
php -S localhost:8080 -t institutional_transaction_tracker
```

Open `http://localhost:8080/`.
