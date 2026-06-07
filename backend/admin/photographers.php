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
    header('Location: /admin/photographers.php?updated=1&filter=' . ($_POST['current_filter'] ?? 'all'));
    exit;
}

$filter = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$where = "WHERE role='photographer'";
$params = [];

if ($filter !== 'all') {
    $where .= " AND status=?";
    $params[] = $filter;
}

if ($search !== '') {
    $where .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR studio_name LIKE ? OR city LIKE ?)";
    $searchWild = "%$search%";
    $params[] = $searchWild;
    $params[] = $searchWild;
    $params[] = $searchWild;
    $params[] = $searchWild;
    $params[] = $searchWild;
}

$stmt = $db->prepare("
    SELECT u.*,
        (SELECT COUNT(*) FROM orders WHERE photographer_id=u.id) as order_count,
        (SELECT COALESCE(SUM(total),0) FROM orders WHERE photographer_id=u.id AND status!='cancelled') as total_spent,
        (SELECT COUNT(*) FROM orders WHERE photographer_id=u.id AND status='pending') as pending_orders
    FROM users u $where ORDER BY u.created_at DESC
");
$stmt->execute($params);
$photographers = $stmt->fetchAll();

// Tab counts
$counts = $db->query("
    SELECT 
        COUNT(*) as total,
        SUM(status='pending') as pending,
        SUM(status='approved') as approved,
        SUM(status='rejected') as rejected
    FROM users WHERE role='photographer'
")->fetch();

require_once '../includes/admin_header.php';
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
  <div>
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Manage Photographers</h1>
    <p class="text-zinc-400 text-sm mt-1">Review accounts, approve or reject registrations, and manage portal access.</p>
  </div>
  <a href="/admin/index.php" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-bold">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
    </svg>
    Back to Dashboard
  </a>
</div>

<?php if (isset($_GET['updated'])): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm font-semibold mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    Photographer status updated successfully.
  </div>
<?php endif; ?>

<!-- Filter Tabs with counts & Search Bar -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
  <!-- Tabs -->
  <div class="flex gap-2 flex-wrap">
    <?php
    $tabs = [
      'all'      => ['label' => 'All',             'count' => $counts['total']],
      'pending'  => ['label' => 'Pending Approval','count' => $counts['pending']],
      'approved' => ['label' => 'Approved',        'count' => $counts['approved']],
      'rejected' => ['label' => 'Rejected',        'count' => $counts['rejected']],
    ];
    foreach ($tabs as $k => $tab):
      $isActive = $filter === $k;
      $cls = $isActive
        ? 'bg-primary text-secondary font-extrabold border-primary'
        : 'bg-white/5 border-white/10 text-zinc-300 hover:bg-white/10 hover:border-white/20';
    ?>
      <a href="/admin/photographers.php?status=<?= $k ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>" class="px-4 py-2 border rounded-full text-xs font-semibold transition-all duration-200 flex items-center gap-1.5 <?= $cls ?>">
        <?= $tab['label'] ?>
        <span class="<?= $isActive ? 'bg-secondary/30 text-secondary' : 'bg-white/10 text-zinc-400' ?> text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?= (int)$tab['count'] ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Search Bar -->
  <form method="GET" action="/admin/photographers.php" class="relative w-full md:w-80">
    <input type="hidden" name="status" value="<?= htmlspecialchars($filter) ?>">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, studio, phone, city..."
           class="w-full bg-white/5 border border-white/10 rounded-xl pl-9 pr-8 py-2.5 text-white placeholder-zinc-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-200 text-xs font-semibold">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-500">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.608 10.608Z" />
      </svg>
    </div>
    <?php if ($search !== ''): ?>
      <a href="/admin/photographers.php?status=<?= htmlspecialchars($filter) ?>" class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-white cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
      </a>
    <?php endif; ?>
  </form>
</div>

<?php if ($photographers): ?>
  <div class="space-y-4">
    <?php foreach ($photographers as $p): ?>
      <?php
        $statusColors = [
          'pending'  => ['dot' => 'bg-amber-400', 'badge' => 'bg-amber-500/10 text-amber-400 border-amber-500/20'],
          'approved' => ['dot' => 'bg-emerald-400','badge' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'],
          'rejected' => ['dot' => 'bg-red-400',   'badge' => 'bg-red-500/10 text-red-400 border-red-500/20'],
        ];
        $sc = $statusColors[$p['status']] ?? ['dot' => 'bg-zinc-400', 'badge' => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20'];
        $initial = strtoupper(substr($p['name'], 0, 1));
        $waText  = rawurlencode("Hi " . $p['name'] . "! Your SD Colours Lab photographer portal account has been approved. You can now log in at http://127.0.0.1:8000/login.php");
      ?>
      <div class="bg-secondary/40 border border-white/5 rounded-2xl p-5 md:p-6 shadow-xl hover:bg-secondary/50 transition-colors">
        
        <!-- Top row: identity + status badge -->
        <div class="flex flex-col md:flex-row md:items-start gap-5">
          
          <!-- Avatar + Info -->
          <div class="flex items-start gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-amber-500 flex items-center justify-center text-secondary font-extrabold text-xl flex-shrink-0 shadow-lg shadow-primary/20">
              <?= $initial ?>
            </div>
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-bold text-white text-base leading-tight"><?= htmlspecialchars($p['name']) ?></h3>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?= $sc['badge'] ?>">
                  <span class="w-1.5 h-1.5 rounded-full <?= $sc['dot'] ?>"></span>
                  <?= ucfirst($p['status']) ?>
                </span>
                <?php if ($p['pending_orders'] > 0): ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    <?= $p['pending_orders'] ?> pending order<?= $p['pending_orders'] > 1 ? 's' : '' ?>
                  </span>
                <?php endif; ?>
              </div>
              <p class="text-xs text-zinc-400 mt-0.5">
                <?= htmlspecialchars($p['email']) ?>
                <?= $p['phone'] ? ' &bull; ' . htmlspecialchars($p['phone']) : '' ?>
              </p>
              <?php if ($p['studio_name']): ?>
                <p class="text-xs text-primary font-semibold mt-0.5">
                  <?= htmlspecialchars($p['studio_name']) ?><?= $p['city'] ? ', ' . htmlspecialchars($p['city']) : '' ?>
                </p>
              <?php endif; ?>
              <p class="text-[10px] text-zinc-600 mt-1">Joined: <?= date('d M Y', strtotime($p['created_at'])) ?></p>
            </div>
          </div>

          <!-- Stats pills -->
          <div class="flex gap-6 md:gap-8 border-t border-white/5 pt-4 md:pt-0 md:border-t-0 md:flex-shrink-0">
            <div class="text-center">
              <div class="text-xl font-black text-white"><?= (int)$p['order_count'] ?></div>
              <div class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider mt-0.5">Orders</div>
            </div>
            <div class="text-center">
              <div class="text-xl font-black text-primary">₹<?= number_format($p['total_spent']) ?></div>
              <div class="text-[10px] font-semibold text-zinc-500 uppercase tracking-wider mt-0.5">Spent</div>
            </div>
          </div>
        </div>

        <!-- Bottom row: action buttons -->
        <div class="mt-5 pt-4 border-t border-white/5 flex flex-wrap items-center gap-2">

          <!-- ── View Orders ── -->
          <a href="/admin/orders.php?photographer_id=<?= $p['id'] ?>"
             class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-zinc-300 hover:bg-white/10 hover:text-white transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-primary">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            View Orders
          </a>

          <!-- ── Create Manual Order ── -->
          <a href="/admin/create_order.php?photographer_id=<?= $p['id'] ?>&studio=<?= urlencode($p['studio_name'] ?? $p['name']) ?>&phone=<?= urlencode($p['phone'] ?? '') ?>"
             class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-2 rounded-xl bg-primary/10 border border-primary/20 text-primary hover:bg-primary/20 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create Manual Order
          </a>

          <!-- ── Edit Photographer ── -->
          <a href="/admin/edit_photographer.php?id=<?= $p['id'] ?>"
             class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-zinc-300 hover:bg-white/10 hover:text-white transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-amber-500">
              <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
            </svg>
            Edit Info
          </a>

          <!-- ── WhatsApp (approved only) ── -->
          <?php if ($p['status'] === 'approved' && $p['phone']): ?>
            <a href="https://wa.me/91<?= preg_replace('/\D/','',$p['phone']) ?>?text=<?= $waText ?>"
               target="_blank"
               class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/20 transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
              Notify Approved
            </a>
          <?php endif; ?>

          <!-- Divider -->
          <div class="h-5 w-px bg-white/10 mx-1 hidden sm:block"></div>

          <!-- ── Status change form — only show buttons that CHANGE state ── -->
          <form method="POST" class="flex gap-2 flex-wrap items-center">
            <input type="hidden" name="user_id" value="<?= $p['id'] ?>">
            <input type="hidden" name="current_filter" value="<?= htmlspecialchars($filter) ?>">
            <input type="hidden" name="status" value="">

            <?php if ($p['status'] !== 'approved'): ?>
              <button type="submit" name="update_status" value="1"
                      onclick="this.form.status.value='approved'"
                      class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-2 rounded-xl bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/25 border border-emerald-500/20 transition-all cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Approve
              </button>
            <?php endif; ?>

            <?php if ($p['status'] !== 'rejected'): ?>
              <button type="submit" name="update_status" value="1"
                      onclick="this.form.status.value='rejected'"
                      class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-2 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/25 border border-red-500/20 transition-all cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Reject
              </button>
            <?php endif; ?>

            <?php if ($p['status'] === 'approved' || $p['status'] === 'rejected'): ?>
              <button type="submit" name="update_status" value="1"
                      onclick="this.form.status.value='pending'"
                      class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-2 rounded-xl bg-amber-500/10 text-amber-400 hover:bg-amber-500/25 border border-amber-500/20 transition-all cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Reset Pending
              </button>
            <?php endif; ?>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php else: ?>
  <div class="bg-secondary/40 border border-white/5 rounded-2xl p-16 text-center shadow-xl">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-zinc-600 mx-auto mb-3">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
    </svg>
    <p class="text-zinc-500 font-semibold">No photographers found for this filter.</p>
    <a href="/admin/photographers.php?status=all" class="mt-3 inline-block text-xs text-primary hover:underline font-bold">View All</a>
  </div>
<?php endif; ?>

<?php require_once '../includes/admin_footer.php'; ?>
