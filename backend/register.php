<?php
$pageTitle = 'Register – SD Colours Photobook Lab';
require_once 'includes/auth.php';
require_once 'includes/db.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/index.php' : '/photographer/index.php'));
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $studio = trim($_POST['studio_name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            $db = getDB();
            $check = $db->prepare('SELECT id FROM users WHERE email = ?');
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('INSERT INTO users (name, email, password_hash, phone, studio_name, city, role, status) VALUES (?, ?, ?, ?, ?, ?, \'photographer\', \'pending\')');
                $stmt->execute([$name, $email, $hash, $phone, $studio, $city]);
                $success = 'Registration successful! Your account is pending admin approval. You will be able to login once approved.';
            }
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}

require_once 'includes/auth_header.php';
?>

<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
  <div class="glass-card max-w-lg w-full space-y-8 p-8 sm:p-10 rounded-2xl shadow-2xl">
    <div>
      <h1 class="text-center text-3xl font-extrabold text-white tracking-tight">Photographer Registration</h1>
      <p class="mt-2 text-center text-sm text-zinc-400">Create your account and start ordering prints online</p>
    </div>

    <?php if ($error): ?>
      <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm text-center">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="space-y-6">
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 rounded-xl p-4 text-sm text-center">
          <?= htmlspecialchars($success) ?>
        </div>
        <div class="text-center">
          <a href="/login.php" class="inline-flex items-center gap-1.5 text-primary hover:underline font-semibold text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Login
          </a>
        </div>
      </div>
    <?php else: ?>
      <form class="space-y-4" method="POST">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Full Name *</label>
          <input type="text" name="name" required placeholder="Your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Email Address *</label>
          <input type="email" name="email" required placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Phone Number</label>
            <input type="tel" name="phone" placeholder="+91 XXXXX XXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">City</label>
            <input type="text" name="city" placeholder="Your city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Studio / Business Name</label>
          <input type="text" name="studio_name" placeholder="Your photography studio name" value="<?= htmlspecialchars($_POST['studio_name'] ?? '') ?>"
                 class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Password *</label>
            <input type="password" name="password" required placeholder="Min. 6 characters"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
          </div>
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Confirm Password *</label>
            <input type="password" name="confirm_password" required placeholder="Repeat password"
                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
          </div>
        </div>

        <div class="pt-2">
          <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-secondary font-bold py-3.5 px-4 rounded-xl shadow-lg transition-all duration-200 text-sm cursor-pointer">
            Create Account
          </button>
        </div>
      </form>

      <div class="text-center text-sm text-zinc-400 pt-2">
        Already have an account? 
        <a href="/login.php" class="text-primary hover:underline font-semibold ml-1">Sign In</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once 'includes/auth_footer.php'; ?>
