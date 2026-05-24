<?php
$pageTitle = 'Admin Dashboard – SD Colours';
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$db = getDB();

$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
$totalPhotographers = $db->query("SELECT COUNT(*) FROM users WHERE role='photographer'")->fetchColumn();
$pendingPhotographers = $db->query("SELECT COUNT(*) FROM users WHERE role='photographer' AND status='pending'")->fetchColumn();
$totalRevenue = $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$totalProducts = $db->query("SELECT COUNT(*) FROM products WHERE active=1")->fetchColumn();

$recentOrders = $db->query("SELECT o.*, u.name as photographer_name FROM orders o JOIN users u ON o.photographer_id=u.id ORDER BY o.created_at DESC LIMIT 8")->fetchAll();

require_once '../includes/admin_header.php';
?>

<div class="mb-8">
  <h1 class="text-3xl font-extrabold text-white tracking-tight">Dashboard Overview</h1>
  <p class="text-zinc-400 text-sm mt-1">Real-time statistics & activity overview for SD Colours Photobook Lab.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <!-- Orders Card -->
  <div class="bg-secondary/60 border border-white/10 rounded-2xl p-6 flex items-center gap-5 relative overflow-hidden group hover:border-primary/30 transition-colors shadow-lg">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-cyan-400 opacity-80"></div>
    <div class="text-4xl p-4 bg-white/5 rounded-2xl group-hover:scale-110 transition-transform">📋</div>
    <div>
      <div class="text-3xl font-black text-white"><?= $totalOrders ?></div>
      <div class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">Total Orders</div>
      <?php if ($pendingOrders): ?>
        <span class="text-blue-400 text-xs font-bold mt-2 inline-flex items-center gap-1 bg-blue-500/10 px-2 py-1 rounded-md">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
          <?= $pendingOrders ?> pending
        </span>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- Photographers Card -->
  <div class="bg-secondary/60 border border-white/10 rounded-2xl p-6 flex items-center gap-5 relative overflow-hidden group hover:border-primary/30 transition-colors shadow-lg">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-pink-500 opacity-80"></div>
    <div class="text-4xl p-4 bg-white/5 rounded-2xl group-hover:scale-110 transition-transform">👨‍📷</div>
    <div>
      <div class="text-3xl font-black text-white"><?= $totalPhotographers ?></div>
      <div class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">Photographers</div>
      <?php if ($pendingPhotographers): ?>
        <span class="text-purple-400 text-xs font-bold mt-2 inline-flex items-center gap-1 bg-purple-500/10 px-2 py-1 rounded-md">
          <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
          <?= $pendingPhotographers ?> awaiting approval
        </span>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- Revenue Card -->
  <div class="bg-secondary/60 border border-white/10 rounded-2xl p-6 flex items-center gap-5 relative overflow-hidden group hover:border-primary/30 transition-colors shadow-lg">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-primary-dark opacity-80"></div>
    <div class="text-4xl p-4 bg-white/5 rounded-2xl group-hover:scale-110 transition-transform">💰</div>
    <div>
      <div class="text-3xl font-black text-primary">₹<?= number_format($totalRevenue) ?></div>
      <div class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">Total Revenue</div>
    </div>
  </div>
</div>

<!-- Quick Link Actions -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
  <a href="/admin/orders.php" class="bg-secondary/40 hover:bg-secondary/80 border border-white/10 hover:border-primary/50 rounded-2xl p-6 flex flex-col items-center justify-center gap-3 transition-all duration-300 group shadow-lg">
    <div class="text-3xl group-hover:scale-110 transition-transform">📋</div>
    <div class="text-sm font-bold text-zinc-300 group-hover:text-primary">Manage Orders</div>
  </a>
  <a href="/admin/photographers.php" class="bg-secondary/40 hover:bg-secondary/80 border border-white/10 hover:border-primary/50 rounded-2xl p-6 flex flex-col items-center justify-center gap-3 transition-all duration-300 group shadow-lg relative">
    <div class="text-3xl group-hover:scale-110 transition-transform">👨‍📷</div>
    <div class="text-sm font-bold text-zinc-300 group-hover:text-primary flex items-center gap-2">
      Photographers
      <?php if ($pendingPhotographers): ?>
        <span class="bg-red-500 text-white rounded-full text-xs px-2 py-0.5 font-bold animate-bounce"><?= $pendingPhotographers ?></span>
      <?php endif; ?>
    </div>
  </a>
  <a href="/admin/products.php" class="bg-secondary/40 hover:bg-secondary/80 border border-white/10 hover:border-primary/50 rounded-2xl p-6 flex flex-col items-center justify-center gap-3 transition-all duration-300 group shadow-lg">
    <div class="text-3xl group-hover:scale-110 transition-transform">📦</div>
    <div class="text-sm font-bold text-zinc-300 group-hover:text-primary">Products Catalog</div>
  </a>
  <a href="/admin/products.php?action=add" class="bg-primary/10 hover:bg-primary/20 border border-primary/30 hover:border-primary/80 rounded-2xl p-6 flex flex-col items-center justify-center gap-3 transition-all duration-300 group shadow-lg">
    <div class="text-3xl group-hover:scale-110 transition-transform">➕</div>
    <div class="text-sm font-bold text-primary">Add Product</div>
  </a>
</div>

<!-- Recent Orders Table Section -->
<div class="bg-secondary/60 border border-white/10 rounded-2xl overflow-hidden shadow-2xl backdrop-blur-sm">
  <div class="px-8 py-5 border-b border-white/10 flex justify-between items-center bg-white/[0.02]">
    <h2 class="text-lg font-bold text-white flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
      </svg>
      Recent Orders
    </h2>
    <a href="/admin/orders.php" class="text-sm text-primary hover:text-white bg-primary/10 hover:bg-primary/30 border border-primary/20 px-4 py-1.5 rounded-lg font-bold flex items-center gap-2 transition-colors">
      View All 
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
      </svg>
    </a>
  </div>
  
  <?php if ($recentOrders): ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm text-left text-zinc-300">
        <thead class="text-xs uppercase bg-secondary/80 text-zinc-400 font-bold border-b border-white/10">
          <tr>
            <th scope="col" class="px-8 py-4">Order ID</th>
            <th scope="col" class="px-8 py-4">Photographer</th>
            <th scope="col" class="px-8 py-4">Date</th>
            <th scope="col" class="px-8 py-4">Total Amount</th>
            <th scope="col" class="px-8 py-4">Status</th>
            <th scope="col" class="px-8 py-4 text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $o): ?>
            <tr class="border-b border-white/5 hover:bg-white/[0.04] transition-colors">
              <td class="px-8 py-5 font-bold text-white">#<?= $o['id'] ?></td>
              <td class="px-8 py-5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-xs font-bold text-white uppercase">
                  <?= substr($o['photographer_name'], 0, 1) ?>
                </div>
                <?= htmlspecialchars($o['photographer_name']) ?>
              </td>
              <td class="px-8 py-5 text-zinc-400"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
              <td class="px-8 py-5 font-bold text-primary text-base">₹<?= number_format($o['total']) ?></td>
              <td class="px-8 py-5">
                <?php
                  $statusClasses = [
                    'pending' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                    'processing' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                    'shipped' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                    'delivered' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                    'cancelled' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                  ];
                  $badgeClass = $statusClasses[$o['status']] ?? 'bg-zinc-500/10 text-zinc-400';
                ?>
                <span class="inline-flex px-3 py-1.5 rounded-md text-xs font-bold uppercase tracking-wider <?= $badgeClass ?>">
                  <?= ucfirst($o['status']) ?>
                </span>
              </td>
              <td class="px-8 py-5 text-right">
                <a href="/admin/orders.php?id=<?= $o['id'] ?>" class="text-xs bg-white/5 hover:bg-primary text-white font-bold px-4 py-2.5 rounded-lg transition-colors inline-block border border-white/10 hover:border-primary">
                  Manage
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="p-12 text-center text-zinc-500 flex flex-col items-center">
      <div class="text-5xl mb-4 opacity-50">📂</div>
      <div class="text-lg font-bold text-zinc-400">No orders yet</div>
      <div class="text-sm mt-1">When customers place orders, they will appear here.</div>
    </div>
  <?php endif; ?>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
