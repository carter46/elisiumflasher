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
  <title>Bank status | Elysium Server</title>
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
  <div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
      <a href="/admin/index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:underline">
        <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle;">arrow_back</span>
        Back
      </a>
      <h1 class="text-2xl font-semibold text-slate-900">Bank status</h1>
    </div>

    <div id="messageBox" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm"></div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-slate-700">priority_high</span>
            <h2 class="text-lg font-semibold text-slate-900">Platform Status</h2>
          </div>
          <p class="text-sm text-slate-600 max-w-xl">
            When OFF, users cannot continue to local transfer. When ON, transfers follow the bank mode settings below.
          </p>
          <p id="platformSummary" class="text-xs text-slate-500 mt-2"></p>
        </div>

        <div class="min-w-[240px]">
          <label class="block text-sm font-semibold text-slate-700 mb-2">Platform Status</label>
          <select id="platformStatus" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200">
            <option value="on">On</option>
            <option value="off">Off</option>
          </select>
        </div>
      </div>
      <div class="flex justify-end mt-5">
        <button id="savePlatformBtn" class="px-5 py-2 rounded-xl bg-[#0a1f44] text-white font-semibold">Save Platform</button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <div class="flex items-center gap-2 mb-4">
        <span class="material-symbols-outlined text-slate-700">account_balance</span>
        <h2 class="text-lg font-semibold text-slate-900">Local transfer bank</h2>
      </div>
      <p class="text-sm text-slate-600 mb-4 max-w-xl">This project uses a single local corridor (UBA). The status controls how transfers behave when that bank is selected.</p>
      <div id="bankCards"></div>
    </div>

    <div class="flex justify-end gap-3 mt-6">
      <a href="/admin/index.php" class="px-5 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm">Cancel</a>
      <button id="saveAllBtn" class="px-5 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold text-sm">Save bank status</button>
    </div>
  </div>

  <script>
    (function () {
      const messageBox = document.getElementById('messageBox');
      const platformStatusEl = document.getElementById('platformStatus');
      const platformSummary = document.getElementById('platformSummary');

      function showMessage(text, type) {
        messageBox.classList.remove('hidden');
        if (type === 'success') {
          messageBox.className = 'mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800';
        } else {
          messageBox.className = 'mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800';
        }
        messageBox.textContent = text || '';
      }

      const STATUS_OPTIONS = [
        { value: 'full_logs', label: 'Full Logs (Active)' },
        { value: 'weak_logs', label: 'Weak Logs' },
        { value: 'pending_request', label: 'Pending Request' },
        { value: 'post_no_debit', label: 'Post No Debit' },
        { value: 'fixed_account', label: 'Fixed Account' },
      ];

      const BANKS = [{ name: 'UBA', code: '033' }];

      let bankStatuses = {};

      function statusSummary() {
        platformSummary.textContent = platformStatusEl.value === 'on'
          ? 'Platform is available for local transfers (normal behavior)'
          : 'Platform is under maintenance (all transfers blocked)';
      }

      function renderBanks() {
        const wrap = document.getElementById('bankCards');
        wrap.innerHTML = '';

        BANKS.forEach((b) => {
          const row = document.createElement('div');
          row.className = 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4';
          row.innerHTML = `
            <div>
              <div class="text-sm font-semibold text-slate-900">${b.name}</div>
              <div class="text-xs text-slate-500 mt-1">Code ${b.code}</div>
            </div>
            <div class="w-full sm:w-64">
              <label class="block text-xs font-semibold text-slate-600 mb-1">Transfer mode</label>
              <select data-bank-code="${b.code}" class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200">
                ${STATUS_OPTIONS.map(o => `<option value="${o.value}">${o.label}</option>`).join('')}
              </select>
            </div>
          `;
          wrap.appendChild(row);
        });

        wrap.querySelectorAll('select[data-bank-code]').forEach((sel) => {
          const code = sel.getAttribute('data-bank-code');
          sel.value = bankStatuses[code] || 'full_logs';
          sel.addEventListener('change', () => {
            bankStatuses[code] = sel.value;
          });
        });
      }

      async function loadPlatformStatus() {
        const res = await fetch('/api/platform_status.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load platform status');

        platformStatusEl.value = data.status || 'on';
        statusSummary();
      }

      async function loadBankStatuses() {
        const res = await fetch('/api/bank_status.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load bank statuses');

        const list = Array.isArray(data.bank_statuses) ? data.bank_statuses : [];
        bankStatuses = {};
        list.forEach((s) => { bankStatuses[s.bank_code] = s.status; });
        const allowed = new Set(BANKS.map((b) => b.code));
        bankStatuses = Object.fromEntries(
          Object.entries(bankStatuses).filter(([code]) => allowed.has(code))
        );
        BANKS.forEach((b) => {
          if (!bankStatuses[b.code]) {
            bankStatuses[b.code] = 'full_logs';
          }
        });
      }

      async function savePlatform() {
        const res = await fetch('/api/platform_status.php', {
          method: 'PUT',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ status: platformStatusEl.value })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to update platform');
        showMessage('Platform status updated successfully!', 'success');
        statusSummary();
      }

      async function saveAll() {
        const statuses = BANKS.map((b) => ({
          bank_code: b.code,
          status: bankStatuses[b.code] || 'full_logs',
        }));
        const res = await fetch('/api/bank_status.php', {
          method: 'PUT',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ statuses })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to update bank statuses');
        showMessage('Bank status saved.', 'success');
      }

      document.getElementById('savePlatformBtn').addEventListener('click', async () => {
        try {
          showMessage('Saving...', 'success');
          await savePlatform();
        } catch (err) {
          showMessage(err.message || 'Failed to save platform status', 'error');
        }
      });

      document.getElementById('saveAllBtn').addEventListener('click', async () => {
        try {
          await saveAll();
        } catch (err) {
          showMessage(err.message || 'Failed to save bank statuses', 'error');
        }
      });

      platformStatusEl.addEventListener('change', statusSummary);

      (async () => {
        try {
          await loadPlatformStatus();
          await loadBankStatuses();
          renderBanks();
        } catch (err) {
          showMessage(err.message || 'Failed to load bank status data', 'error');
        }
      })();
    })();
  </script>
</body>
</html>
