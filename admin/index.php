<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_auth.php';
require_admin_login();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Elysium Server</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .admin-card:hover { transform: translateY(-2px); }
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
  <div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between gap-4 mb-6">
      <h1 class="text-2xl font-semibold text-slate-900">Admin Dashboard</h1>
      <button id="logoutBtn" class="px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-sm font-semibold">
        Logout
      </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <a href="/admin/local_account_settings.php" class="admin-card bg-white rounded-2xl shadow-sm border border-white/60 p-6 flex flex-col items-center text-center cursor-pointer">
        <div class="w-14 h-14 rounded-full bg-[#d61b1b] flex items-center justify-center mb-3">
          <span class="material-symbols-outlined text-white text-4xl" style="font-variation-settings:'FILL' 1;">settings</span>
        </div>
        <h2 class="text-lg font-semibold text-slate-900">Local account settings</h2>
        <p class="text-xs text-slate-600 mt-2">Manage local account name, number, and balance</p>
      </a>

      <a href="/admin/bank_status.php" class="admin-card bg-white rounded-2xl shadow-sm border border-white/60 p-6 flex flex-col items-center text-center cursor-pointer">
        <div class="w-14 h-14 rounded-full bg-[#f59e0b] flex items-center justify-center mb-3">
          <span class="material-symbols-outlined text-white text-4xl" style="font-variation-settings:'FILL' 1;">notification_important</span>
        </div>
        <h2 class="text-lg font-semibold text-slate-900">Bank status</h2>
        <p class="text-xs text-slate-600 mt-2">Platform switch and local transfer bank mode</p>
      </a>

      <a href="/admin/paystack_settings.php" class="admin-card bg-white rounded-2xl shadow-sm border border-white/60 p-6 flex flex-col items-center text-center cursor-pointer">
        <div class="w-14 h-14 rounded-full bg-[#0ea5e9] flex items-center justify-center mb-3">
          <span class="material-symbols-outlined text-white text-4xl" style="font-variation-settings:'FILL' 1;">credit_card</span>
        </div>
        <h2 class="text-lg font-semibold text-slate-900">Payment &amp; account resolve</h2>
        <p class="text-xs text-slate-600 mt-2">Paystack and Flutterwave keys and resolve toggles</p>
      </a>

      <a href="/admin/license_keys.php" class="admin-card bg-white rounded-2xl shadow-sm border border-white/60 p-6 flex flex-col items-center text-center cursor-pointer">
        <div class="w-14 h-14 rounded-full bg-[#10b981] flex items-center justify-center mb-3">
          <span class="material-symbols-outlined text-white text-4xl" style="font-variation-settings:'FILL' 1;">key</span>
        </div>
        <h2 class="text-lg font-semibold text-slate-900">License Key Management</h2>
        <p class="text-xs text-slate-600 mt-2">Generate client login keys</p>
      </a>

      <a href="/admin/profile.php" class="admin-card bg-white rounded-2xl shadow-sm border border-white/60 p-6 flex flex-col items-center text-center cursor-pointer sm:col-span-2">
        <div class="w-14 h-14 rounded-full bg-[#d61b1b] flex items-center justify-center mb-3">
          <span class="material-symbols-outlined text-white text-4xl" style="font-variation-settings:'FILL' 1;">account_circle</span>
        </div>
        <h2 class="text-lg font-semibold text-slate-900">Profile</h2>
        <p class="text-xs text-slate-600 mt-2">Update password and email</p>
      </a>
    </div>
  </div>

  <script>
    (function () {
      const btn = document.getElementById('logoutBtn');
      btn.addEventListener('click', async () => {
        try {
          await fetch('/api/admin_auth.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'logout'})
          });
        } finally {
          window.location.href = '/admin/login.php';
        }
      });
    })();
  </script>
</body>
</html>

