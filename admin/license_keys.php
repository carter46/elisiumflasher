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
  <title>License Key Management | Elysium Server</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
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
  <div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
      <a href="/admin/index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 hover:underline">
        <span class="material-symbols-outlined" style="font-size:18px; vertical-align:middle;">arrow_back</span>
        Back
      </a>
      <h1 class="text-2xl font-semibold text-slate-900">License Key Management</h1>
    </div>

    <div id="messageBox" class="hidden mb-4 rounded-xl border px-4 py-3 text-sm"></div>

    <div class="flex justify-end mb-4">
      <button id="generateBtn" class="px-5 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold text-sm">
        Generate New Client Key
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <div class="mb-4">
        <div class="text-sm font-semibold text-slate-900">Client Keys</div>
        <div class="text-xs text-slate-600 mt-1">Multi-active enabled: generating a new key does not deactivate existing active keys.</div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="border-b">
              <th class="text-left py-3 px-2 font-semibold text-slate-700">Key</th>
              <th class="text-left py-3 px-2 font-semibold text-slate-700">Status</th>
              <th class="text-left py-3 px-2 font-semibold text-slate-700">Created</th>
              <th class="text-right py-3 px-2 font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody id="keysBody"></tbody>
        </table>
      </div>
    </div>

    <div id="generatedBox" class="hidden mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="text-sm font-semibold text-amber-900 mb-1">New Client Key Generated</div>
          <div id="generatedKeyValue" class="mono text-xs break-all text-amber-900"></div>
        </div>
        <button id="copyKeyBtn" class="px-4 py-2 rounded-xl border border-amber-300 bg-white text-amber-900 font-semibold text-sm">Copy</button>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const messageBox = document.getElementById('messageBox');
      const keysBody = document.getElementById('keysBody');
      const generatedBox = document.getElementById('generatedBox');
      const generatedKeyValue = document.getElementById('generatedKeyValue');
      const copyKeyBtn = document.getElementById('copyKeyBtn');
      const generateBtn = document.getElementById('generateBtn');

      const setMessage = (text, type) => {
        messageBox.textContent = text || '';
        messageBox.className = type === 'success'
          ? 'mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 block'
          : 'mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 block';
        messageBox.classList.remove('hidden');
      };

      const formatKeyPreview = (k) => {
        if (!k) return '';
        if (k.length <= 28) return k;
        return k.substring(0, 20) + '...' + k.substring(k.length - 8);
      };

      async function copyText(text) {
        const value = String(text || '');
        if (!value) throw new Error('Nothing to copy');

        if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function' && window.isSecureContext) {
          await navigator.clipboard.writeText(value);
          return;
        }

        const ta = document.createElement('textarea');
        ta.value = value;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.top = '0';
        ta.style.left = '0';
        ta.style.width = '1px';
        ta.style.height = '1px';
        ta.style.padding = '0';
        ta.style.border = 'none';
        ta.style.outline = 'none';
        ta.style.boxShadow = 'none';
        ta.style.background = 'transparent';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        ta.setSelectionRange(0, value.length);
        const ok = document.execCommand('copy');
        document.body.removeChild(ta);
        if (!ok) throw new Error('Copy failed');
      }

      async function loadKeys() {
        const res = await fetch('/api/client_keys.php');
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load client keys');

        const keys = Array.isArray(data.keys) ? data.keys : [];
        keysBody.innerHTML = '';
        if (!keys.length) {
          keysBody.innerHTML = '<tr><td colspan="4" class="py-10 text-center text-slate-600">No keys found</td></tr>';
          return;
        }

        keys.forEach((k) => {
          const tr = document.createElement('tr');
          tr.className = 'border-b';
          const isActive = !!k.is_active;
          tr.innerHTML = `
            <td class="py-3 px-2">
              <div class="mono text-xs break-all">${formatKeyPreview(k.client_key)}</div>
              <div class="mono text-[10px] text-slate-500 mt-1">id: ${k.id}</div>
            </td>
            <td class="py-3 px-2">
              <span class="inline-flex items-center px-2.5 py-1 rounded-lg border ${isActive ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200'} text-xs font-semibold">
                ${isActive ? 'Active' : 'Inactive'}
              </span>
            </td>
            <td class="py-3 px-2 text-slate-600">${k.created_at ? new Date(k.created_at).toLocaleString() : ''}</td>
            <td class="py-3 px-2 text-right">
              <div class="flex justify-end gap-2">
                <button data-action="copy" data-key="${k.client_key}" class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-900 font-semibold text-xs">
                  Copy
                </button>
                <button data-action="toggle" data-id="${k.id}" class="px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-900 font-semibold text-xs">
                  ${isActive ? 'Disable' : 'Enable'}
                </button>
                <button data-action="delete" data-id="${k.id}" class="px-3 py-2 rounded-xl border border-red-200 bg-white text-red-600 font-semibold text-xs">
                  Delete
                </button>
              </div>
            </td>
          `;
          keysBody.appendChild(tr);
        });

        // Wire actions after render
        keysBody.querySelectorAll('button[data-action]').forEach((btn) => {
          const action = btn.getAttribute('data-action');
          const id = btn.getAttribute('data-id');
          const keyValue = btn.getAttribute('data-key');
          btn.addEventListener('click', async () => {
            try {
              if (action === 'copy') {
                await copyText(keyValue);
                setMessage('Key copied to clipboard', 'success');
                return;
              } else if (action === 'toggle') {
                const wantActive = btn.textContent.trim().toLowerCase().startsWith('enable');
                const res = await fetch('/api/client_keys.php', {
                  method: 'PUT',
                  headers: {'Content-Type': 'application/json'},
                  body: JSON.stringify({ id: Number(id), is_active: wantActive ? 1 : 0 })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to update key');
                setMessage('Client key updated successfully', 'success');
              } else if (action === 'delete') {
                if (!confirm('Delete this client key?')) return;
                const res = await fetch('/api/client_keys.php?id=' + encodeURIComponent(id), { method: 'DELETE' });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to delete key');
                setMessage('Client key deleted', 'success');
              }
              await loadKeys();
            } catch (err) {
              setMessage(err.message || 'Action failed', 'error');
            }
          });
        });
      }

      copyKeyBtn.addEventListener('click', async () => {
        try {
          await copyText(generatedKeyValue.textContent.trim());
          setMessage('Copied to clipboard', 'success');
        } catch (err) {
          setMessage(err.message || 'Copy failed', 'error');
        }
      });

      generateBtn.addEventListener('click', async () => {
        try {
          generateBtn.disabled = true;
          const res = await fetch('/api/client_keys.php', { method: 'POST' });
          const data = await res.json().catch(() => ({}));
          if (!res.ok || !data.success) throw new Error(data.message || 'Failed to generate key');
          generatedKeyValue.textContent = data.generated_key || '';
          generatedBox.classList.remove('hidden');
          generatedBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
          setMessage('New key generated. Copy it now.', 'success');
          await loadKeys();
        } catch (err) {
          setMessage(err.message || 'Failed to generate key', 'error');
        } finally {
          generateBtn.disabled = false;
        }
      });

      (async () => {
        try {
          await loadKeys();
        } catch (err) {
          setMessage(err.message || 'Failed to load keys', 'error');
        }
      })();
    })();
  </script>
</body>
</html>

