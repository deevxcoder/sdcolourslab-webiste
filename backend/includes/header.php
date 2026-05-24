<?php
require_once __DIR__ . '/auth.php';
startSession();
$currentPage = basename($_SERVER['PHP_SELF']);
$loggedIn = isLoggedIn();
$role = $_SESSION['role'] ?? '';
$cartCount = $loggedIn && $role === 'photographer' ? getCartCount() : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle ?? 'SD Colours Photobook Lab') ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc ?? 'Your Fast & Professional Photobook Printing Partner in Rourkela.') ?>" />
  <link rel="icon" href="/images/logo.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/style.css" />
  <style>
    .cart-badge { background: #e74c3c; color: #fff; border-radius: 50%; font-size: 0.65rem; font-weight: 700; padding: 1px 5px; position: absolute; top: -6px; right: -8px; min-width: 16px; text-align: center; }
    .nav-cart { position: relative; display: inline-flex; align-items: center; text-decoration: none; color: var(--text); font-weight: 600; font-size: .875rem; }
    .user-menu { display: flex; align-items: center; gap: 0.75rem; }
    .btn-dash { background: var(--primary); color: #fff; padding: .4rem 1rem; border-radius: 6px; font-size: .8rem; font-weight: 700; text-decoration: none; }
    .btn-logout { background: #f3f4f6; color: #374151; padding: .4rem 1rem; border-radius: 6px; font-size: .8rem; font-weight: 700; text-decoration: none; }
    @media (max-width: 1023px) { .user-menu { display: none; } }
  </style>
</head>
<body>

  <header id="site-header">
    <nav class="nav-inner">
      <a href="/index.php" class="logo"><img src="/images/logo.png" alt="SD Colours Photobook Lab Logo" /></a>
      <ul class="nav-links">
        <li><a href="/index.php" <?= $currentPage === 'index.php' ? 'class="active"' : '' ?>>Home</a></li>
        <li><a href="/products.php" <?= $currentPage === 'products.php' ? 'class="active"' : '' ?>>Products</a></li>
        <li><a href="/pricing.php" <?= $currentPage === 'pricing.php' ? 'class="active"' : '' ?>>Pricing</a></li>
        <li><a href="/gallery.php" <?= $currentPage === 'gallery.php' ? 'class="active"' : '' ?>>Gallery</a></li>
        <li><a href="/about.php" <?= $currentPage === 'about.php' ? 'class="active"' : '' ?>>About</a></li>
        <li><a href="/contact.php" <?= $currentPage === 'contact.php' ? 'class="active"' : '' ?>>Contact</a></li>
      </ul>
      <?php if ($loggedIn): ?>
        <div class="user-menu">
          <?php if ($role === 'photographer'): ?>
            <a href="/photographer/cart.php" class="nav-cart">
              🛒 Cart
              <?php if ($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
            </a>
            <a href="/photographer/index.php" class="btn-dash"><span class="hidden sm:inline">My </span>Dashboard</a>
          <?php elseif ($role === 'admin'): ?>
            <a href="/admin/index.php" class="btn-dash">Admin Panel</a>
          <?php endif; ?>
          <a href="/logout.php" class="btn-logout">Logout</a>
        </div>
      <?php else: ?>
        <div class="user-menu">
          <a href="/login.php" class="btn-logout">Login</a>
          <a href="/register.php" class="btn-dash">Register</a>
        </div>
      <?php endif; ?>
      <?php if ($loggedIn && $role === 'photographer'): ?>
      <a href="/photographer/cart.php" class="mobile-cart-btn" aria-label="Cart">
        🛒
        <?php if ($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
      </a>
      <?php endif; ?>
      <button class="hamburger" id="hamburger-btn" aria-label="Open menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </nav>
  </header>

  <div class="mobile-menu" id="mobile-menu">
    <div class="mobile-overlay" id="mobile-overlay"></div>
    <div class="mobile-drawer">
      <div class="drawer-header">
        <img src="/images/logo.png" alt="SD Colours Logo" />
        <button class="drawer-close" id="mobile-close">&times;</button>
      </div>
      <ul class="drawer-nav">
        <li><a href="/index.php">Home</a></li>
        <li><a href="/products.php">Products</a></li>
        <li><a href="/pricing.php">Pricing</a></li>
        <li><a href="/gallery.php">Gallery</a></li>
        <li><a href="/about.php">About</a></li>
        <li><a href="/contact.php">Contact</a></li>
        <?php if ($loggedIn): ?>
          <?php if ($role === 'photographer'): ?>
            <li><a href="/photographer/index.php">Dashboard</a></li>
            <li><a href="/photographer/cart.php">Cart (<?= $cartCount ?>)</a></li>
          <?php elseif ($role === 'admin'): ?>
            <li><a href="/admin/index.php">Admin Panel</a></li>
          <?php endif; ?>
          <li><a href="/logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="/login.php">Login</a></li>
          <li><a href="/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
