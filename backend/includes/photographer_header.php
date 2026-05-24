<?php
require_once __DIR__ . '/auth.php';
startSession();
requirePhotographer();

$currentPage = basename($_SERVER['PHP_SELF']);
$user        = getCurrentUser();
$db          = getDB();
$userId      = $_SESSION['user_id'];
$cartCount   = getCartCount();

$isLocal     = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost','localhost:8000','localhost:8001','127.0.0.1']) || php_sapi_name() === 'cli';
$frontendUrl = $isLocal ? 'http://localhost:3000' : 'https://sdcolourslab.in';

function isNavActive($p, $cur) {
    return $p === $cur ? 'bg-primary text-secondary font-bold' : 'text-zinc-300 hover:bg-white/5 hover:text-white';
}
function mobileTab($p, $cur) {
    return $p === $cur ? 'text-primary' : 'text-zinc-500';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  <meta name="theme-color" content="#171717" />
  <title><?= htmlspecialchars($pageTitle ?? 'Photographer Portal – SD Colours') ?></title>
  <link rel="icon" href="/images/logo.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary:  { DEFAULT: '#cca353', dark: '#b58c42' },
            secondary:'#171717',
            darkbg:   '#0f0f11',
            darkcard: '#1c1c1f',
          },
          fontFamily: { sans: ['Inter','sans-serif'] },
        }
      }
    }
  </script>
  <style>
    * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background: #0f0f11; color: #f4f4f5; }
    .hide-scroll { scrollbar-width: none; }
    .hide-scroll::-webkit-scrollbar { display: none; }
    .touch-btn:active { transform: scale(0.97); }
    .glass { background: rgba(23,23,23,0.92); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
  </style>
</head>
<body>

<!-- ══════════════════════════════════════════
  MOBILE ONLY — Fixed top bar
══════════════════════════════════════════ -->
<header class="md:hidden fixed top-0 left-0 right-0 z-50 glass border-b border-white/5 h-14 flex items-center justify-between px-4">
  <a href="/photographer/index.php">
    <img src="/images/logo.png" alt="SD" class="h-7 w-auto brightness-110" />
  </a>
  <span class="text-xs text-zinc-400 font-semibold truncate max-w-[160px]">
    <?= htmlspecialchars($user['studio_name'] ?: $user['name']) ?>
  </span>
  <a href="/photographer/cart.php" class="relative p-1.5">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-zinc-300">
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>
    </svg>
    <?php if ($cartCount > 0): ?>
    <span class="absolute top-0 right-0 bg-primary text-secondary text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center"><?= $cartCount ?></span>
    <?php endif; ?>
  </a>
</header>

<!-- MOBILE ONLY — Bottom tab navigation -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 glass border-t border-white/5" style="padding-bottom: env(safe-area-inset-bottom)">
  <div class="flex h-16">
    <a href="/photographer/index.php" class="flex-1 flex flex-col items-center justify-center gap-0.5 touch-btn <?= mobileTab('index.php',$currentPage) ?>">
      <svg xmlns="http://www.w3.org/2000/svg" fill="<?= $currentPage==='index.php'?'currentColor':'none' ?>" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
      </svg>
      <span class="text-[10px] font-semibold">Home</span>
    </a>
    <a href="/photographer/shop.php" class="flex-1 flex flex-col items-center justify-center gap-0.5 touch-btn <?= mobileTab('shop.php',$currentPage) ?>">
      <svg xmlns="http://www.w3.org/2000/svg" fill="<?= $currentPage==='shop.php'?'currentColor':'none' ?>" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/>
      </svg>
      <span class="text-[10px] font-semibold">Shop</span>
    </a>
    <!-- Cart centre FAB -->
    <a href="/photographer/cart.php" class="flex-1 flex flex-col items-center justify-center gap-0.5 touch-btn relative <?= mobileTab('cart.php',$currentPage) ?>">
      <div class="<?= $currentPage==='cart.php'?'bg-primary':'bg-darkcard border border-white/10' ?> w-12 h-12 rounded-2xl flex items-center justify-center -mt-5 shadow-xl shadow-black/50">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 <?= $currentPage==='cart.php'?'text-secondary':'text-primary' ?>">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>
        </svg>
      </div>
      <?php if ($cartCount > 0): ?>
      <span class="absolute top-0.5 right-3 bg-red-500 text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center"><?= $cartCount ?></span>
      <?php endif; ?>
      <span class="text-[10px] font-semibold"><?= $cartCount > 0 ? "Cart ($cartCount)" : 'Cart' ?></span>
    </a>
    <a href="/photographer/orders.php" class="flex-1 flex flex-col items-center justify-center gap-0.5 touch-btn <?= mobileTab('orders.php',$currentPage) ?>">
      <svg xmlns="http://www.w3.org/2000/svg" fill="<?= $currentPage==='orders.php'?'currentColor':'none' ?>" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
      </svg>
      <span class="text-[10px] font-semibold">Orders</span>
    </a>
    <button onclick="toggleProfile()" class="flex-1 flex flex-col items-center justify-center gap-0.5 touch-btn text-zinc-500">
      <div class="w-6 h-6 rounded-full bg-primary/20 border border-primary/40 flex items-center justify-center">
        <span class="text-primary text-[10px] font-black"><?= strtoupper(substr($user['name'],0,1)) ?></span>
      </div>
      <span class="text-[10px] font-semibold">Me</span>
    </button>
  </div>
</nav>

<!-- MOBILE ONLY — Profile bottom sheet -->
<div id="profile-sheet" class="md:hidden fixed inset-0 z-[60] pointer-events-none opacity-0 transition-opacity duration-200">
  <div class="absolute inset-0 bg-black/60" onclick="toggleProfile()"></div>
  <div class="absolute bottom-0 left-0 right-0 glass border-t border-white/10 rounded-t-3xl p-6 space-y-4 transform translate-y-full transition-transform duration-300" id="profile-panel">
    <div class="w-10 h-1 bg-white/20 rounded-full mx-auto"></div>
    <div class="flex items-center gap-4">
      <div class="w-14 h-14 rounded-2xl bg-primary/20 border border-primary/30 flex items-center justify-center">
        <span class="text-primary text-2xl font-black"><?= strtoupper(substr($user['name'],0,1)) ?></span>
      </div>
      <div>
        <p class="font-bold text-white"><?= htmlspecialchars($user['name']) ?></p>
        <p class="text-primary text-xs font-semibold"><?= htmlspecialchars($user['studio_name'] ?: '') ?></p>
        <p class="text-zinc-500 text-xs"><?= htmlspecialchars($user['city'] ?: '') ?></p>
      </div>
    </div>
    <div class="space-y-2 pt-2 border-t border-white/5">
      <a href="<?= $frontendUrl ?>" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl bg-white/5 text-sm font-semibold text-zinc-300 touch-btn">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
        View Main Website
      </a>
      <a href="/logout.php" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl bg-red-500/10 text-sm font-semibold text-red-400 touch-btn">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"/></svg>
        Sign Out
      </a>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
  MAIN LAYOUT WRAPPER
  KEY FIX: outer div is always flex (not hidden md:flex)
  Only the <aside> sidebar uses hidden md:flex
  This ensures mobile content is always visible
══════════════════════════════════════════ -->
<div class="flex min-h-screen">
  <!-- Sidebar: hidden on mobile, visible on desktop -->
  <aside class="hidden md:flex w-64 bg-secondary border-r border-white/5 flex-col justify-between py-6 px-4 sticky top-0 h-screen overflow-y-auto flex-shrink-0">
    <div class="space-y-6">
      <a href="/photographer/index.php" class="flex items-center px-2"><img src="/images/logo.png" alt="SD Colours" class="h-10 w-auto brightness-110" /></a>
      <div class="px-3"><span class="text-[10px] font-bold uppercase tracking-widest text-primary/60">Photographer Portal</span></div>
      <nav class="space-y-1">
        <a href="/photographer/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all <?= isNavActive('index.php',$currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg> Dashboard
        </a>
        <a href="/photographer/shop.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all <?= isNavActive('shop.php',$currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016 2.993 2.993 0 0 0 2.25-1.016 3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg> Browse &amp; Order
        </a>
        <a href="/photographer/cart.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all <?= isNavActive('cart.php',$currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg> My Cart
          <?php if ($cartCount > 0): ?><span class="ml-auto bg-primary text-secondary text-xs font-bold px-2 py-0.5 rounded-full"><?= $cartCount ?></span><?php endif; ?>
        </a>
        <a href="/photographer/orders.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all <?= isNavActive('orders.php',$currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg> My Orders
        </a>
      </nav>
    </div>
    <div class="space-y-4 pt-6 border-t border-white/5">
      <div class="px-3">
        <p class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Signed In As</p>
        <p class="text-sm text-white font-bold truncate"><?= htmlspecialchars($user['name']) ?></p>
        <p class="text-xs text-primary truncate"><?= htmlspecialchars($user['studio_name'] ?: $user['email']) ?></p>
      </div>
      <div class="space-y-1">
        <a href="<?= $frontendUrl ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-zinc-400 hover:bg-white/5 hover:text-white transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg> View Main Site
        </a>
        <a href="/logout.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-400 hover:bg-red-500/10 transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"/></svg> Logout
        </a>
      </div>
    </div>
  </aside>
  <!-- Content area: always visible on mobile AND desktop -->
  <div class="flex-grow flex flex-col min-w-0">
    <!-- Desktop top bar (hidden on mobile since mobile has its own fixed header) -->
    <header class="hidden md:flex h-16 border-b border-white/5 items-center justify-between px-8 bg-secondary/30 flex-shrink-0">
      <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-wider">Photographer Portal</h2>
      <span class="text-xs text-zinc-500">Welcome, <strong class="text-primary"><?= htmlspecialchars($user['name']) ?></strong></span>
    </header>
    <div class="flex-grow md:p-6 lg:p-8 overflow-y-auto">
<!-- DESKTOP CONTENT AREA OPENS -->
