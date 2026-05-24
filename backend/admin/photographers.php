<?php
$pageTitle = 'Manage Photographers – Admin';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $userId = (int)$_POST['user_id'];
    $status = in_array($_POST['status'], ['pending','approved','rejected']) ? $_POST['status'] : 'pending';
    $stmt = $db->prepare("UPDATE users SET status=? WHERE id=? AND role='photographer'");
    $stmt->execute([$status, $userId]);
    header('Location: /admin/photographers.php?updated=1');
    exit;
}

$filter = $_GET['status'] ?? 'all';
$where = $filter !== 'all' ? "WHERE role='photographer' AND status=?" : "WHERE role='photographer'";
$params = $filter !== 'all' ? [$filter] : [];
$stmt = $db->prepare("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE photographer_id=u.id) as order_count, (SELECT COALESCE(SUM(total),0) FROM orders WHERE photographer_id=u.id AND status!='cancelled') as total_spent FROM users u $where ORDER BY u.created_at DESC");
$stmt->execute($params);
$photographers = $stmt->fetchAll();
require_once '../includes/admin_header.php';
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Manage Photographers</h1>
    <p class="text-zinc-400 text-sm mt-1">Approve, reject, or suspend photographer portal accounts.</p>
  </div>
  <div>
    <a href="/admin/index.php" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-bold">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
      </svg>
      Back to Dashboard
    </a>
  </div>
</div>

<?php if (isset($_GET['updated'])): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    Status updated successfully.
  </div>
<?php endif; ?>

<div class="flex gap-2 flex-wrap mb-6">
  <?php foreach (['all'=>'All','pending'=>'Pending Approval','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v): ?>
    <?php
      $isActive = $filter===$k;
      $tabClass = $isActive 
        ? 'bg-primary text-secondary font-bold border-primary' 
        : 'bg-white/5 border-white/10 text-zinc-300 hover:bg-white/10 hover:border-white/20';
    ?>
    <a href="/admin/photographers.php?status=<?= $k ?>" class="px-4 py-2 border rounded-full text-xs font-semibold transition-all duration-200 <?= $tabClass ?>">
      <?= $v ?>
    </a>
  <?php endforeach; ?>
</div>

<?php if ($photographers): ?>
  <div class="space-y-4">
    <?php foreach ($photographers as $p): ?>
      <div class="bg-secondary/40 border border-white/5 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-xl hover:bg-secondary/50 transition-colors">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-secondary font-extrabold text-lg flex-shrink-0">
            <?= strtoupper(substr($p['name'], 0, 1)) ?>
          </div>
          <div>
            <h3 class="font-bold text-white text-base"><?= htmlspecialchars($p['name']) ?></h3>
            <p class="text-xs text-zinc-400 mt-0.5">
              <?= htmlspecialchars($p['email']) ?> <?= $p['phone'] ? '• ' . htmlspecialchars($p['phone']) : '' ?>
            </p>
            <?php if ($p['studio_name']): ?>
              <p class="text-xs text-primary font-semibold mt-1">
                <?= htmlspecialchars($p['studio_name']) ?><?= $p['city'] ? ', ' . htmlspecialchars($p['city']) : '' ?>
              </p>
            <?php endif; ?>
            <p class="text-[10px] text-zinc-500 mt-1">Joined: <?= date('d M Y', strtotime($p['created_at'])) ?></p>
          </div>
        </div>
        
        <!-- Stats -->
        <div class="flex gap-8 border-t border-white/5 pt-4 md:pt-0 md:border-t-0">
          <div class="text-center md:text-left">
            <div class="text-lg font-black text-white"><?= $p['order_count'] ?></div>
            <div class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider">Orders</div>
          </div>
          <div class="text-center md:text-left">
            <div class="text-lg font-black text-primary">₹<?= number_format($p['total_spent']) ?></div>
            <div class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider">Spent</div>
          </div>
        </div>
        
        <!-- Status Badge & Form Actions -->
        <div class="flex flex-wrap items-center gap-4">
          <?php
            $statusBadges = [
              'pending' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
              'approved' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
              'rejected' => 'bg-red-500/10 text-red-400 border border-red-500/20',
            ];
            $badgeClass = $statusBadges[$p['status']] ?? 'bg-zinc-500/10 text-zinc-400';
          ?>
          <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold <?= $badgeClass ?>">
            <?= ucfirst($p['status']) ?>
          </span>
          
          <form method="POST" class="flex gap-2 flex-wrap">
            <input type="hidden" name="user_id" value="<?= $p['id'] ?>">
            <?php if ($p['status'] !== 'approved'): ?>
              <button type="submit" name="update_status" value="1" onclick="this.form.status.value='approved'" class="text-xs bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 font-bold px-3 py-2 rounded-xl transition-colors cursor-pointer">
                Approve
              </button>
            <?php endif; ?>
            <?php if ($p['status'] !== 'rejected'): ?>
              <button type="submit" name="update_status" value="1" onclick="this.form.status.value='rejected'" class="text-xs bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 font-bold px-3 py-2 rounded-xl transition-colors cursor-pointer">
                Reject
              </button>
            <?php endif; ?>
            <?php if ($p['status'] !== 'pending'): ?>
              <button type="submit" name="update_status" value="1" onclick="this.form.status.value='pending'" class="text-xs bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 font-bold px-3 py-2 rounded-xl transition-colors cursor-pointer">
                Set Pending
              </button>
            <?php endif; ?>
            <input type="hidden" name="status" value="">
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <div class="bg-secondary/40 border border-white/5 rounded-2xl p-12 text-center text-zinc-500 shadow-xl">
    No photographers found.
  </div>
<?php endif; ?>

<?php require_once '../includes/admin_footer.php'; ?>
