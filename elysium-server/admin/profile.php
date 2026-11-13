<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

require_admin_login();
$admin_id = validate_admin_session();

$pdo = get_db();
$message = null; // ['type'=>'success'|'error','text'=>...]

// Handle updates (server-side to keep profile wiring simple).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

    $updates = [];
    $params = [];

    if ($email !== '') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = ['type' => 'error', 'text' => 'Invalid email format'];
        } else {
            $updates[] = 'email = ?';
            $params[] = $email;
        }
    }

    if ($password !== '') {
        $updates[] = 'password = ?';
        $params[] = $password;
    }

    if (!$updates) {
        $message = ['type' => 'error', 'text' => 'Provide at least an email or a password to update'];
    } else {
        try {
            $params[] = $admin_id;
            $sql = 'UPDATE admin_users SET ' . implode(', ', $updates) . ' WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $message = ['type' => 'success', 'text' => 'Profile updated successfully'];
        } catch (Throwable $e) {
            $message = ['type' => 'error', 'text' => 'Failed to update profile'];
        }
    }
}

// Load profile
$stmt = $pdo->prepare('SELECT username, email FROM admin_users WHERE id = ? LIMIT 1');
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Profile | Elysium Server</title>
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
      <h1 class="text-2xl font-semibold text-slate-900">Profile Settings</h1>
    </div>

    <?php if ($message): ?>
      <div class="mb-4 rounded-xl border px-4 py-3 text-sm <?= $message['type']==='success' ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800' ?>">
        <?= htmlspecialchars($message['text'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-2xl shadow-sm border p-6 space-y-6">
      <div>
        <div class="text-sm font-semibold text-slate-700 mb-2">Username</div>
        <div class="text-lg font-semibold text-slate-900"><?= htmlspecialchars((string) ($admin['username'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
        <div class="text-xs text-slate-500 mt-1">Username cannot be changed.</div>
      </div>

      <div>
        <div class="text-sm font-semibold text-slate-700 mb-2">Email Address</div>
        <input type="email" name="email" placeholder="Enter new email" value="<?= htmlspecialchars((string) ($admin['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
          class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200">
      </div>

      <div>
        <div class="text-sm font-semibold text-slate-700 mb-2">Password</div>
        <input type="password" name="password" placeholder="Leave blank to keep current password"
          class="w-full border border-slate-200 rounded-xl px-3 py-2 outline-none focus:ring-4 focus:ring-indigo-200">
      </div>

      <div class="flex justify-end gap-3">
        <button type="button" onclick="window.location.href='/admin/index.php'" class="px-5 py-2 rounded-xl border border-slate-200 bg-white font-semibold text-sm">
          Cancel
        </button>
        <button type="submit" class="px-5 py-2 rounded-xl bg-[#d61b1b] text-white font-semibold text-sm">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</body>
</html>

