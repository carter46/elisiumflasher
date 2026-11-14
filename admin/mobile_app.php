<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_auth.php';
require_admin_login();

$dir = defined('MOBILE_APK_PRIVATE_DIR')
    ? MOBILE_APK_PRIVATE_DIR
    : (__DIR__ . '/../api/private_mobile');
$apkName = defined('MOBILE_APK_FILENAME') ? MOBILE_APK_FILENAME : 'banking-companion.apk';
$metaName = defined('MOBILE_APK_META_FILENAME') ? MOBILE_APK_META_FILENAME : 'apk-meta.json';
$apkPath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR)
    . DIRECTORY_SEPARATOR
    . $apkName;
$metaPath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR)
    . DIRECTORY_SEPARATOR
    . $metaName;

$available = is_readable($apkPath) && filesize($apkPath) >= (5 * 1024 * 1024);
$version = null;
$builtAt = null;
$fileSize = $available ? filesize($apkPath) : null;
$easBuildId = null;

if (is_readable($metaPath)) {
    $decoded = json_decode((string) file_get_contents($metaPath), true);
    if (is_array($decoded)) {
        $version = isset($decoded['version']) ? (string) $decoded['version'] : null;
        $builtAt = isset($decoded['built_at']) ? (string) $decoded['built_at'] : null;
        $easBuildId = isset($decoded['eas_build_id']) ? (string) $decoded['eas_build_id'] : null;
        if (!empty($decoded['file_size'])) {
            $fileSize = (int) $decoded['file_size'];
        }
    }
}

$sizeLabel = $fileSize !== null
    ? number_format($fileSize / (1024 * 1024), 1) . ' MB'
    : '—';
$builtLabel = '—';
if ($builtAt) {
    $ts = strtotime($builtAt);
    $builtLabel = $ts !== false ? date('Y-m-d H:i', $ts) . ' UTC' : $builtAt;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex"/>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mobile App | Elysium Server</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .material-symbols-outlined {
      font-family: 'Material Symbols Outlined';
      font-weight: normal;
      font-style: normal;
      line-height: 1;
      display: inline-block;
      white-space: nowrap;
      -webkit-font-smoothing: antialiased;
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
  </style>
</head>
<body class="min-h-screen bg-[#f5f5f5] p-6">
  <div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
      <a href="/admin/index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:underline">
        <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle;">arrow_back</span>
        Back
      </a>
      <h1 class="text-2xl font-semibold text-slate-900">Mobile App</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <div class="flex items-start justify-between gap-4 mb-4">
        <div>
          <h2 class="text-lg font-semibold text-slate-900">Banking Companion APK</h2>
          <p class="text-sm text-slate-600 mt-1">
            Durable download from this server. Expo artifact links expire; after each EAS build,
            publish the APK into <code class="text-xs bg-slate-100 px-1 rounded">api/private_mobile/</code>
            and deploy.
          </p>
        </div>
        <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">smartphone</span>
        </div>
      </div>

      <dl class="space-y-2 text-sm mb-6">
        <div class="flex justify-between gap-3">
          <dt class="text-slate-500">Status</dt>
          <dd class="font-semibold <?= $available ? 'text-emerald-700' : 'text-amber-700' ?>">
            <?= $available ? 'Published' : 'Not published yet' ?>
          </dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-slate-500">Version</dt>
          <dd class="font-semibold text-slate-900"><?= htmlspecialchars($version ?: '—', ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-slate-500">Built</dt>
          <dd class="font-semibold text-slate-900"><?= htmlspecialchars($builtLabel, ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <div class="flex justify-between gap-3">
          <dt class="text-slate-500">Size</dt>
          <dd class="font-semibold text-slate-900"><?= htmlspecialchars($sizeLabel, ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php if ($easBuildId): ?>
        <div class="flex justify-between gap-3">
          <dt class="text-slate-500">EAS build</dt>
          <dd class="font-mono text-xs text-slate-700 text-right break-all"><?= htmlspecialchars($easBuildId, ENT_QUOTES, 'UTF-8') ?></dd>
        </div>
        <?php endif; ?>
      </dl>

      <?php if ($available): ?>
      <a
        href="/api/mobile_apk_download.php"
        class="inline-flex w-full items-center justify-center gap-2 px-4 py-3 rounded-xl bg-[#0a1f44] text-white font-semibold hover:opacity-95"
      >
        <span class="material-symbols-outlined text-[20px]">download</span>
        Download Mobile App (APK)
      </a>
      <?php else: ?>
      <button
        type="button"
        disabled
        class="inline-flex w-full items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-300 text-slate-600 font-semibold cursor-not-allowed"
      >
        <span class="material-symbols-outlined text-[20px]">download</span>
        Download Mobile App (APK)
      </button>
      <p class="text-xs text-amber-800 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 mt-3">
        No APK published yet. From <code>Mobile_app</code> run
        <code>npm run build:android:apk:eas</code> or
        <code>npm run publish:from-eas -- -Latest</code>, then deploy
        <code>api/private_mobile/</code> to the server.
      </p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
