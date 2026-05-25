<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pageTitle = 'Login – SD Colours Photobook Lab';
require_once 'includes/auth.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/index.php' : '/photographer/index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = loginUser(trim($_POST['email'] ?? ''), $_POST['password'] ?? '');
    if (isset($result['success'])) {
        header('Location: ' . ($result['role'] === 'admin' ? '/admin/index.php' : '/photographer/index.php'));
        exit;
    }
    $error = $result['error'];
}

require_once 'includes/auth_header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
  <div class="glass-card max-w-md w-full space-y-8 p-8 sm:p-10 rounded-2xl shadow-2xl">
    <div>
      <h1 class="text-center text-3xl font-extrabold text-white tracking-tight">Welcome Back</h1>
      <p class="mt-2 text-center text-sm text-zinc-400">Login to your SD Colours photographer account</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form class="mt-8 space-y-6" method="POST">
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Email Address</label>
          <input type="email" name="email" required placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Password</label>
          <input type="password" name="password" required placeholder="••••••••"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>
      </div>

      <div>
        <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-secondary font-bold py-3.5 px-4 rounded-xl shadow-lg transition-all duration-200 text-sm cursor-pointer">
          Sign In
        </button>
      </div>
    </form>

    <div class="text-center text-sm text-zinc-400 pt-2">
      Don't have an account? 
      <a href="/register.php" class="text-primary hover:underline font-semibold ml-1">Register as Photographer</a>
    </div>

    <div class="text-center border-t border-white/5 pt-4">
      <small class="text-zinc-500 block text-xs">Admin Demo Credentials:</small>
      <code class="text-primary text-[11px] block mt-1">admin@sdcolours.com / admin123</code>
    </div>
  </div>
</div>

<?php require_once 'includes/auth_footer.php'; ?>
