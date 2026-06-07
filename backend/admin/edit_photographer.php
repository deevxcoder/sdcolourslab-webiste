<?php
$pageTitle = 'Edit Photographer – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();
$error = '';
$success = '';

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$userId) {
    header('Location: /admin/photographers.php');
    exit;
}

// Fetch user details
$stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'photographer'");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: /admin/photographers.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_photographer'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $studioName = trim($_POST['studio_name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $status = in_array($_POST['status'], ['pending', 'approved', 'rejected']) ? $_POST['status'] : 'pending';

    // Simple validation
    if (empty($name) || empty($email)) {
        $error = 'Name and Email are required fields.';
    } else {
        try {
            // Check if email already exists for another user
            $checkEmail = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkEmail->execute([$email, $userId]);
            if ($checkEmail->fetch()) {
                $error = 'The email address is already in use by another account.';
            } else {
                // Update user in DB
                $updateStmt = $db->prepare("
                    UPDATE users 
                    SET name = ?, email = ?, phone = ?, studio_name = ?, city = ?, status = ? 
                    WHERE id = ? AND role = 'photographer'
                ");
                $updateStmt->execute([$name, $email, $phone, $studioName, $city, $status, $userId]);
                
                // Refresh data
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                $success = 'Photographer details updated successfully.';
            }
        } catch (Exception $e) {
            $error = 'Database update error: ' . $e->getMessage();
        }
    }
}

require_once '../includes/admin_header.php';
?>

<div class="mb-8 flex justify-between items-center">
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Edit Photographer Details</h1>
    <p class="text-zinc-400 text-sm mt-1">Modify photographer information, status, and studio details.</p>
  </div>
  <div>
    <a href="/admin/photographers.php" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-bold">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
      </svg>
      Back to Photographers
    </a>
  </div>
</div>

<?php if ($error): ?>
  <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
    </svg>
    <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    <?= htmlspecialchars($success) ?>
  </div>
<?php endif; ?>

<div class="max-w-2xl bg-secondary/40 border border-white/5 rounded-2xl p-6 sm:p-8 shadow-xl">
  <form method="POST" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Full Name *</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($user['name']) ?>" 
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Email Address *</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>" 
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Mobile Number</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Studio Name</label>
        <input type="text" name="studio_name" value="<?= htmlspecialchars($user['studio_name'] ?? '') ?>" 
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">City</label>
        <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" 
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">Portal Status *</label>
        <select name="status" 
                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-sm appearance-none cursor-pointer">
          <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?> class="bg-secondary text-white">Pending Approval</option>
          <option value="approved" <?= $user['status'] === 'approved' ? 'selected' : '' ?> class="bg-secondary text-white">Approved</option>
          <option value="rejected" <?= $user['status'] === 'rejected' ? 'selected' : '' ?> class="bg-secondary text-white">Rejected</option>
        </select>
      </div>
    </div>

    <div class="pt-4 border-t border-white/5 flex justify-end gap-3">
      <a href="/admin/photographers.php" 
         class="inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-zinc-300 hover:bg-white/10 hover:text-white transition-all">
        Cancel
      </a>
      <button type="submit" name="update_photographer" value="1"
              class="inline-flex items-center gap-1.5 text-xs font-bold px-5 py-2.5 rounded-xl bg-primary text-secondary hover:bg-primary/95 hover:scale-[1.02] active:scale-[0.98] transition-all cursor-pointer">
        Save Changes
      </button>
    </div>
  </form>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
