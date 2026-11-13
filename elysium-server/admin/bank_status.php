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
            When OFF, users cannot continue to <strong>any transfer</strong> (local or international). When ON, transfers follow the bank/international mode settings below.
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
      <div class="flex items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-slate-700">public</span>
            <h2 class="text-lg font-semibold text-slate-900">International Transfer Status</h2>
          </div>
          <p class="text-sm text-slate-600 max-w-xl">Controls what the user sees after the SWIFT connecting modal completes for international transfers.</p>
          <p class="text-xs text-slate-500 mt-2">Last updated: <span id="intlStatusUpdatedAt">—</span></p>
        </div>
        <div class="flex items-center gap-3">
          <select id="intlStatusSelect" class="border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold">
            <option value="pending">pending</option>
            <option value="none">none (allow success)</option>
            <option value="processing">processing</option>
            <option value="failed">failed</option>
            <option value="reversed">reversed</option>
            <option value="network_error">network error</option>
          </select>
          <button id="saveIntlStatusBtn" class="px-4 py-2 rounded-xl bg-[#0a1f44] text-white font-semibold">Save</button>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-4">
      <div class="flex items-center gap-2 mb-2">
        <span class="material-symbols-outlined text-slate-700">travel_explore</span>
        <h2 class="text-lg font-semibold text-slate-900">Institutional Tracker defaults</h2>
      </div>
      <p class="text-sm text-slate-600 mb-4">Initial tracker status for new transfers. Separate from receipt outcome. Override per transaction in account settings.</p>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">Local tracker default</label>
          <select id="localTrackerStatus" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm"></select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1">International tracker default</label>
          <select id="intlTrackerStatus" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm"></select>
        </div>
      </div>
      <div class="flex justify-end mt-4">
        <button id="saveTrackerDefaultsBtn" class="px-4 py-2 rounded-xl bg-[#0a1f44] text-white font-semibold text-sm">Save tracker defaults</button>
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
      const intlStatusSelect = document.getElementById('intlStatusSelect');
      const intlStatusUpdatedAt = document.getElementById('intlStatusUpdatedAt');
      const saveIntlStatusBtn = document.getElementById('saveIntlStatusBtn');

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
          ? 'Platform is available for all transfers (normal behavior)'
          : 'Platform is under maintenance (all transfers blocked)';
      }

      // International status functions
      async function loadIntlStatus() {
        const res = await fetch('/api/international_status.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load international status');
        let current = String((data.status && data.status.status) || 'pending').toLowerCase();

        // Auto-migrate legacy "none" default to pending so admin default matches current logic.
        if (current === 'none') {
          const res2 = await fetch('/api/international_status.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ status: 'pending' })
          });
          const data2 = await res2.json().catch(() => ({}));
          if (!res2.ok || !data2.success) throw new Error(data2.message || 'Failed to normalize international status');
          current = 'pending';
          intlStatusUpdatedAt.textContent = (data2.status && data2.status.updated_at) || '—';
        } else {
          intlStatusUpdatedAt.textContent = data.status.updated_at || '—';
        }
        intlStatusSelect.value = current || 'pending';
      }

      saveIntlStatusBtn.addEventListener('click', async () => {
        try {
          saveIntlStatusBtn.disabled = true;
          const res = await fetch('/api/international_status.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ status: intlStatusSelect.value })
          });
          const data = await res.json().catch(() => ({}));
          if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save');
          intlStatusUpdatedAt.textContent = data.status.updated_at || '—';
          showMessage('International status saved.', 'success');
        } catch (err) {
          showMessage(err.message || 'Failed to save', 'error');
        } finally {
          saveIntlStatusBtn.disabled = false;
        }
      });

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

      function fillTrackerSelect(el, options, selected) {
        const list = Array.isArray(options) && options.length ? options : [{ code: 'PDNG', label: 'Pending / In Progress' }];
        el.innerHTML = list.map((o) => '<option value="' + o.code + '">' + o.code + ' — ' + o.label + '</option>').join('');
        el.value = selected || 'PDNG';
      }

      async function loadTrackerDefaults() {
        const res = await fetch('/api/tracker_defaults.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load tracker defaults');
        fillTrackerSelect(document.getElementById('localTrackerStatus'), data.tracker_status_options, data.local_tracker_status);
        fillTrackerSelect(document.getElementById('intlTrackerStatus'), data.tracker_status_options, data.international_tracker_status);
      }

      document.getElementById('saveTrackerDefaultsBtn').addEventListener('click', async () => {
        try {
          const res = await fetch('/api/tracker_defaults.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              local_tracker_status: document.getElementById('localTrackerStatus').value,
              international_tracker_status: document.getElementById('intlTrackerStatus').value,
            }),
          });
          const data = await res.json().catch(() => ({}));
          if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save tracker defaults');
          showMessage('Tracker defaults saved.', 'success');
        } catch (err) {
          showMessage(err.message || 'Failed to save tracker defaults', 'error');
        }
      });

      (async () => {
        try {
          await loadPlatformStatus();
          await loadBankStatuses();
          await loadIntlStatus();
          await loadTrackerDefaults();
          renderBanks();
        } catch (err) {
          showMessage(err.message || 'Failed to load bank status data', 'error');
        }
      })();
    })();
  </script>
</body>
</html>

