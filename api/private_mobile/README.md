Private APK store — not publicly URL-accessible.

Official flow:
1. Expo/EAS preview APK build (`npm run build:android:apk:eas` from Mobile_app)
2. Script downloads the artifact → validates → publishes here
3. Deploy/upload `api/private_mobile/` to production
4. Admin downloads via `/admin/mobile_app.php` → `api/mobile_apk_download.php`

Files:
- `banking-companion.apk` — durable copy (Expo links expire)
- `apk-meta.json` — version / built_at / file_size / eas_build_id

Do not rely on temporary Expo download URLs for admin installs.
