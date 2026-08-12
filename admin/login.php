<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_auth.php';

if (is_admin_logged_in()) {
    header('Location: /admin/index.php');
    exit;
}

?>

<!doctype html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Elysium Server</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            'on-surface': '#131b2e',
            'on-surface-variant': '#767586',
          },
          fontFamily: {
            headline: ['Inter', 'sans-serif'],
            body: ['Inter', 'sans-serif'],
          },
        },
      },
    };
  </script>
  <style>
    .material-symbols-outlined {
      font-family: 'Material Symbols Outlined';
      font-weight: normal;
      font-style: normal;
      line-height: 1;
      letter-spacing: normal;
      text-transform: none;
      display: inline-block;
      white-space: nowrap;
      -webkit-font-smoothing: antialiased;
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .login-card {
      background: #ffffff;
      box-shadow: 0 16px 48px rgba(19, 27, 46, 0.1);
    }
    .login-input-wrap {
      border: 2px solid #0f0f0f;
      border-radius: 0.75rem;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .login-input-wrap:focus-within {
      border-color: #000000;
      box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.12);
    }
    body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
  </style>
</head>
<body class="font-body text-on-surface min-h-screen flex items-center justify-center p-6 bg-[#d4d4d4] relative overflow-hidden">
<main class="w-full max-w-md relative z-10">
  <div class="login-card border border-slate-300 rounded-2xl p-10 flex flex-col gap-8">
    <div class="flex flex-col items-center gap-2">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-slate-900 text-3xl" style="font-variation-settings: 'FILL' 1;">shield</span>
        <h1 class="font-headline text-2xl font-extrabold tracking-tighter text-on-surface">Elysium Server</h1>
      </div>
      <p class="text-slate-600 font-medium text-sm">Key Log Version : 4.09</p>
    </div>

    <div id="errorBox" class="hidden rounded-lg border border-red-300 bg-red-50 text-red-700 px-4 py-3 text-sm"></div>

    <form id="loginForm" class="flex flex-col gap-6">
      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-on-surface-variant tracking-wider uppercase pl-1" for="username">Username</label>
        <div class="login-input-wrap relative flex items-center px-3">
          <input id="username" name="username" type="text" required autocomplete="username"
            class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface placeholder:text-slate-400 rounded-lg">
        </div>
      </div>

      <div class="flex flex-col gap-1.5">
        <label class="text-xs font-semibold text-on-surface-variant tracking-wider uppercase pl-1" for="password">Password</label>
        <div class="login-input-wrap relative flex items-center px-3">
          <input id="password" name="password" type="password" required autocomplete="current-password"
            class="w-full bg-white border-none focus:ring-0 py-3 px-1 text-on-surface placeholder:text-slate-400 rounded-lg"
            placeholder="••••••••">
        </div>
      </div>

      <button type="submit" class="w-full bg-black text-gray-400 py-4 rounded-xl font-semibold hover:bg-zinc-900 active:scale-[0.99] transition-all duration-200 disabled:opacity-60">
        Login
      </button>

      <a href="/" class="text-center text-sm text-slate-600 hover:text-slate-900 transition-colors">Back to User Login</a>
    </form>
  </div>
</main>

<script>
  (function () {
    const form = document.getElementById('loginForm');
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const errorBox = document.getElementById('errorBox');
    const btn = form.querySelector('button[type="submit"]');

    function showError(msg) {
      errorBox.textContent = msg || 'Login failed';
      errorBox.classList.remove('hidden');
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      errorBox.classList.add('hidden');
      btn.disabled = true;
      try {
        const res = await fetch('/api/admin_auth.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ action: 'login', username: username.value, password: password.value })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
          if (res.status >= 500) {
            showError(data.message || 'Server error while logging in (HTTP ' + res.status + ')');
          } else {
            showError(data.message || 'Invalid login credentials');
          }
          return;
        }
        window.location.href = '/admin/index.php';
      } catch (err) {
        showError('Network error');
      } finally {
        btn.disabled = false;
      }
    });
  })();
</script>
</body>
</html>
