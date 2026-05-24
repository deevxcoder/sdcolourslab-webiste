<?php
$pageTitle = 'My Dashboard – SD Colours';
require_once '../includes/db.php';
require_once '../includes/photographer_header.php';

$db = getDB();
$userId = $_SESSION['user_id'];

$ordersStmt = $db->prepare("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id=oi.order_id WHERE o.photographer_id=? GROUP BY o.id ORDER BY o.created_at DESC LIMIT 5");
$ordersStmt->execute([$userId]);
$recentOrders = $ordersStmt->fetchAll();

$totalOrders = $db->prepare("SELECT COUNT(*) FROM orders WHERE photographer_id=?");
$totalOrders->execute([$userId]);
$orderCount = $totalOrders->fetchColumn();

$totalSpent = $db->prepare("SELECT COALESCE(SUM(total),0) FROM orders WHERE photographer_id=? AND status != 'cancelled'");
$totalSpent->execute([$userId]);
$spent = $totalSpent->fetchColumn();
?>

<!-- ═══ MOBILE LAYOUT ═══ -->
<div class="md:hidden pt-14 pb-20 mobile-nav-safe">
  <!-- Hero greeting -->
  <div class="px-4 pt-5 pb-4">
    <p class="text-zinc-400 text-xs font-semibold uppercase tracking-wider mb-1">Good <?= date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') ?> 👋</p>
    <h1 class="text-2xl font-black text-white leading-tight"><?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></h1>
    <p class="text-primary text-sm font-semibold mt-0.5"><?= htmlspecialchars($user['studio_name'] ?: 'Photographer') ?></p>
  </div>

  <!-- Stats Row -->
  <div class="px-4 grid grid-cols-3 gap-2.5 mb-5">
    <div class="bg-darkcard border border-white/5 rounded-2xl p-3 text-center">
      <div class="text-2xl font-black text-white"><?= $orderCount ?></div>
      <div class="text-zinc-500 text-[10px] uppercase tracking-wider font-bold mt-0.5">Orders</div>
    </div>
    <div class="bg-darkcard border border-white/5 rounded-2xl p-3 text-center">
      <div class="text-2xl font-black text-primary">₹<?= number_format($spent/1000, 0) ?>K</div>
      <div class="text-zinc-500 text-[10px] uppercase tracking-wider font-bold mt-0.5">Spent</div>
    </div>
    <div class="bg-darkcard border border-white/5 rounded-2xl p-3 text-center">
      <div class="text-2xl font-black text-white"><?= $cartCount ?></div>
      <div class="text-zinc-500 text-[10px] uppercase tracking-wider font-bold mt-0.5">In Cart</div>
    </div>
  </div>

  <!-- Big CTA -->
  <div class="px-4 mb-5">
    <a href="/photographer/shop.php" class="flex items-center justify-between bg-primary rounded-2xl px-5 py-4 touch-btn shadow-xl shadow-primary/20">
      <div>
        <div class="text-secondary font-black text-lg leading-tight">Browse & Order</div>
        <div class="text-secondary/70 text-xs mt-0.5">Albums · Combos · Frames</div>
      </div>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-7 h-7 text-secondary/80">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
      </svg>
    </a>
  </div>

  <!-- Quick Action Grid -->
  <div class="px-4 grid grid-cols-2 gap-3 mb-5">
    <a href="/photographer/cart.php" class="bg-darkcard border border-white/5 rounded-2xl p-4 flex flex-col gap-2 touch-btn active:bg-white/5">
      <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-amber-400">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272" />
        </svg>
      </div>
      <span class="text-sm font-bold text-white">My Cart</span>
      <span class="text-xs text-zinc-500"><?= $cartCount ?> item<?= $cartCount !== 1 ? 's' : '' ?></span>
    </a>
    <a href="/photographer/orders.php" class="bg-darkcard border border-white/5 rounded-2xl p-4 flex flex-col gap-2 touch-btn active:bg-white/5">
      <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-400">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
        </svg>
      </div>
      <span class="text-sm font-bold text-white">My Orders</span>
      <span class="text-xs text-zinc-500"><?= $orderCount ?> total</span>
    </a>
  </div>

  <!-- Downloads & Support (Mobile) -->
  <div class="px-4 mb-5 space-y-3">
    <!-- Help & Support -->
    <div class="bg-darkcard border border-white/5 rounded-2xl p-4">
      <h3 class="text-white font-bold text-xs uppercase tracking-wider mb-1 text-primary">Help &amp; Support</h3>
      <p class="text-zinc-500 text-xs mb-3">Call or message us on WhatsApp for any help.</p>
      <div class="flex gap-2">
        <a href="tel:+918895838987" class="flex-1 flex items-center justify-center gap-1.5 bg-zinc-800 border border-white/10 text-white py-2 px-3 rounded-xl text-xs font-bold touch-btn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-primary">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.183-4.161-6.985-6.985l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
          </svg>
          Call Us
        </a>
        <a href="https://wa.me/918895838987" target="_blank" rel="noopener noreferrer" class="flex-1 flex items-center justify-center gap-1.5 bg-green-500/10 border border-green-500/20 text-green-400 py-2 px-3 rounded-xl text-xs font-bold touch-btn">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" class="w-4 h-4">
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L3 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
          </svg>
          WhatsApp
        </a>
      </div>
    </div>

    <!-- Pricing Lists PDF Download -->
    <div class="bg-darkcard border border-white/5 rounded-2xl p-4">
      <h3 class="text-white font-bold text-xs uppercase tracking-wider mb-1 text-primary">Price Lists PDF</h3>
      <p class="text-zinc-500 text-xs mb-3">Download offline copies of our price catalogs.</p>
      <div class="grid grid-cols-2 gap-2">
        <a href="/price_list.pdf" download class="flex flex-col items-center justify-center p-3 rounded-xl bg-zinc-800/50 border border-white/5 hover:border-primary/40 touch-btn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary mb-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
          </svg>
          <span class="text-[10px] font-bold text-white truncate w-full text-center">Price List</span>
          <span class="text-[8px] text-zinc-600 mt-0.5">8.0 MB</span>
        </a>
        <a href="/combo_price_list.pdf" download class="flex flex-col items-center justify-center p-3 rounded-xl bg-zinc-800/50 border border-white/5 hover:border-primary/40 touch-btn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary mb-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
          </svg>
          <span class="text-[10px] font-bold text-white truncate w-full text-center">Combo List</span>
          <span class="text-[8px] text-zinc-600 mt-0.5">62.2 MB</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Recent Orders -->
  <div class="px-4">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider">Recent Orders</h2>
      <a href="/photographer/orders.php" class="text-primary text-xs font-bold">View All →</a>
    </div>

    <?php if ($recentOrders): ?>
    <div class="space-y-3">
      <?php foreach ($recentOrders as $o):
        $statusMap = [
          'pending' => ['bg-yellow-500/10 text-yellow-400', 'Pending'],
          'processing' => ['bg-blue-500/10 text-blue-400', 'Processing'],
          'shipped' => ['bg-purple-500/10 text-purple-400', 'Shipped'],
          'delivered' => ['bg-green-500/10 text-green-400', 'Delivered'],
          'cancelled' => ['bg-red-500/10 text-red-400', 'Cancelled'],
        ];
        [$sc, $sl] = $statusMap[$o['status']] ?? ['bg-zinc-500/10 text-zinc-400', ucfirst($o['status'])];
      ?>
      <a href="/photographer/orders.php?id=<?= $o['id'] ?>" class="bg-darkcard border border-white/5 rounded-2xl p-4 flex items-center gap-4 touch-btn active:bg-white/5 block">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
          <span class="text-primary text-xs font-black">#<?= $o['id'] ?></span>
        </div>
        <div class="flex-grow min-w-0">
          <div class="text-white font-bold text-sm truncate"><?= $o['item_count'] ?> item<?= $o['item_count'] != 1 ? 's' : '' ?></div>
          <div class="text-zinc-500 text-xs mt-0.5"><?= date('d M Y', strtotime($o['created_at'])) ?></div>
        </div>
        <div class="text-right flex-shrink-0">
          <div class="text-white font-bold text-sm">₹<?= number_format($o['total']) ?></div>
          <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold <?= $sc ?> mt-1"><?= $sl ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="bg-darkcard border border-white/5 rounded-2xl py-12 text-center">
      <div class="text-4xl mb-3">📦</div>
      <p class="text-zinc-400 text-sm mb-4">No orders yet</p>
      <a href="/photographer/shop.php" class="inline-block bg-primary text-secondary text-xs font-bold px-6 py-3 rounded-xl touch-btn">Start Ordering</a>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ═══ DESKTOP LAYOUT ═══ -->
<div class="hidden md:block">
  <div class="mb-8">
    <h1 class="text-2xl font-bold text-white mb-1">Welcome back, <?= htmlspecialchars($user['name']) ?>! 👋</h1>
    <p class="text-zinc-400 text-sm"><?= htmlspecialchars($user['studio_name'] ?: 'SD Colours Photographer Portal') ?></p>
  </div>
  <div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-darkcard border border-white/5 rounded-2xl p-5 text-center">
      <div class="text-3xl font-black text-white mb-1"><?= $orderCount ?></div>
      <div class="text-zinc-400 text-xs uppercase tracking-wider font-semibold">Total Orders</div>
    </div>
    <div class="bg-darkcard border border-white/5 rounded-2xl p-5 text-center">
      <div class="text-3xl font-black text-primary mb-1">₹<?= number_format($spent) ?></div>
      <div class="text-zinc-400 text-xs uppercase tracking-wider font-semibold">Total Spent</div>
    </div>
    <div class="bg-darkcard border border-white/5 rounded-2xl p-5 text-center">
      <div class="text-3xl font-black text-white mb-1"><?= $cartCount ?></div>
      <div class="text-zinc-400 text-xs uppercase tracking-wider font-semibold">Items in Cart</div>
    </div>
  </div>
  <div class="grid grid-cols-3 gap-4 mb-8">
    <a href="/photographer/shop.php" class="bg-primary text-secondary rounded-2xl p-5 flex flex-col items-center gap-3 hover:bg-primary-dark transition-all font-bold shadow-lg shadow-primary/10">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
      <span class="text-sm">Browse & Order</span>
    </a>
    <a href="/photographer/cart.php" class="bg-darkcard border border-white/10 rounded-2xl p-5 flex flex-col items-center gap-3 hover:border-primary/40 hover:bg-white/5 transition-all font-semibold">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-primary"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
      <span class="text-sm text-white">My Cart<?= $cartCount > 0 ? " ($cartCount)" : '' ?></span>
    </a>
    <a href="/photographer/orders.php" class="bg-darkcard border border-white/10 rounded-2xl p-5 flex flex-col items-center gap-3 hover:border-primary/40 hover:bg-white/5 transition-all font-semibold">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-primary"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
      <span class="text-sm text-white">My Orders</span>
    </a>
  </div>

  <!-- Downloads & Support (Desktop) -->
  <div class="grid grid-cols-2 gap-4 mb-8">
    <!-- Help & Support -->
    <div class="bg-darkcard border border-white/5 rounded-2xl p-6 flex flex-col justify-between">
      <div>
        <h2 class="text-sm font-bold text-white uppercase tracking-wider mb-2">Help &amp; Support</h2>
        <p class="text-zinc-400 text-xs leading-relaxed mb-4">Have any questions about photobooks, custom sizes, or delivery schedules? Get in touch with the SD Colours Lab team directly via Call or WhatsApp.</p>
      </div>
      <div class="flex gap-3">
        <a href="tel:+918895838987" class="flex-1 flex items-center justify-center gap-2 bg-zinc-800 border border-white/10 hover:border-primary/45 hover:bg-white/5 text-white py-3 px-4 rounded-xl text-sm font-bold transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-primary">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.183-4.161-6.985-6.985l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
          </svg>
          Call +91 88958 38987
        </a>
        <a href="https://wa.me/918895838987" target="_blank" rel="noopener noreferrer" class="flex-1 flex items-center justify-center gap-2 bg-green-500/10 border border-green-500/20 hover:border-green-500/40 text-green-400 py-3 px-4 rounded-xl text-sm font-bold transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" class="w-4 h-4">
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L3 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
          </svg>
          Chat on WhatsApp
        </a>
      </div>
    </div>

    <!-- Pricing Lists PDF Download -->
    <div class="bg-darkcard border border-white/5 rounded-2xl p-6 flex flex-col justify-between">
      <div>
        <h2 class="text-sm font-bold text-white uppercase tracking-wider mb-2">Price Lists &amp; Catalogs</h2>
        <p class="text-zinc-400 text-xs leading-relaxed mb-4">Download individual pricing lists for albums, prints, and combination packs offline for easy reference.</p>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <a href="/price_list.pdf" download class="flex items-center gap-3 p-4 rounded-xl bg-zinc-800 border border-white/10 hover:border-primary/45 hover:bg-white/5 transition-all">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
          </div>
          <div>
            <div class="text-sm font-bold text-white">General Price List</div>
            <div class="text-xs text-zinc-500 mt-0.5">PDF · 8.0 MB</div>
          </div>
        </a>
        <a href="/combo_price_list.pdf" download class="flex items-center gap-3 p-4 rounded-xl bg-zinc-800 border border-white/10 hover:border-primary/45 hover:bg-white/5 transition-all">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
          </div>
          <div>
            <div class="text-sm font-bold text-white">Combo Price List</div>
            <div class="text-xs text-zinc-500 mt-0.5">PDF · 62.2 MB</div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="bg-darkcard border border-white/5 rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center">
      <h2 class="text-sm font-bold text-white uppercase tracking-wider">Recent Orders</h2>
      <a href="/photographer/orders.php" class="text-primary text-xs font-bold hover:underline">View All →</a>
    </div>
    <?php if ($recentOrders): ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="border-b border-white/5">
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Order #</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Date</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Items</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Total</th>
          <th class="px-6 py-3 text-left text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-white/5">
          <?php foreach ($recentOrders as $o):
            $statusColors = ['pending'=>'bg-yellow-500/10 text-yellow-400','processing'=>'bg-blue-500/10 text-blue-400','shipped'=>'bg-purple-500/10 text-purple-400','delivered'=>'bg-green-500/10 text-green-400','cancelled'=>'bg-red-500/10 text-red-400'];
            $sc = $statusColors[$o['status']] ?? 'bg-zinc-500/10 text-zinc-400';
          ?>
          <tr class="hover:bg-white/2 transition-colors">
            <td class="px-6 py-4"><a href="/photographer/orders.php?id=<?= $o['id'] ?>" class="text-primary font-bold hover:underline">#<?= $o['id'] ?></a></td>
            <td class="px-6 py-4 text-zinc-400"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td class="px-6 py-4 text-zinc-300"><?= $o['item_count'] ?> item<?= $o['item_count'] != 1 ? 's' : '' ?></td>
            <td class="px-6 py-4 text-white font-semibold">₹<?= number_format($o['total']) ?></td>
            <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold <?= $sc ?>"><?= ucfirst($o['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="py-16 text-center">
      <div class="text-5xl mb-4">📦</div>
      <p class="text-zinc-400 text-sm">No orders yet.</p>
      <a href="/photographer/shop.php" class="inline-block mt-4 bg-primary text-secondary text-xs font-bold px-6 py-2.5 rounded-xl hover:bg-primary-dark transition-all">Browse Products</a>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once '../includes/photographer_footer.php'; ?>
