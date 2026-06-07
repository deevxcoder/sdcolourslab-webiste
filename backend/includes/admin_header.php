<?php
require_once __DIR__ . '/auth.php';
startSession();
requireAdmin();

$currentPage = basename($_SERVER['PHP_SELF']);

// Detect environment to point to correct frontend
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:8000', 'localhost:8001', '127.0.0.1', 'sdcolorslab.test']) || php_sapi_name() === 'cli';
$frontendUrl = $isLocal ? 'http://localhost:3000' : 'https://sdcolourslab.in';

// Helper to determine active state of navigation links
function isNavActive($pageName, $currentPage) {
    return $currentPage === $pageName ? 'bg-primary/10 border-l-4 border-primary text-primary font-bold' : 'text-zinc-300 hover:bg-white/5 hover:text-white border-l-4 border-transparent';
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle ?? 'Admin Panel – SD Colours') ?></title>
  <link rel="icon" href="/images/logo.png" />
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '#cca353',
              dark: '#b58c42',
            },
            secondary: '#171717',
            accent: '#f8f4eb',
            darkbg: '#0f0f11',
            darkcard: '#1c1c1f',
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .glass-card {
      background: rgba(28, 28, 31, 0.8);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
    /* Premium custom scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: transparent;
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.2);
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
  </style>
</head>
<body class="h-screen overflow-hidden bg-darkbg text-zinc-100 flex flex-col md:flex-row antialiased">

  <!-- Mobile Top Bar -->
  <header class="md:hidden w-full bg-secondary border-b border-white/5 h-16 flex items-center justify-between px-4 fixed top-0 left-0 z-50">
    <a href="/admin/index.php" class="flex items-center">
      <img src="/images/logo.png" alt="SD Colours Logo" class="h-8 w-auto brightness-110" />
    </a>
    <button id="sidebar-toggle" class="text-zinc-400 hover:text-white p-2 cursor-pointer">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>
  </header>

  <!-- Sidebar Nav -->
  <aside id="sidebar-nav" class="fixed inset-y-0 left-0 z-40 w-64 bg-secondary border-r border-white/5 flex flex-col justify-between py-6 px-4 transform -translate-x-full transition-transform duration-300 md:translate-x-0 md:static md:h-screen flex-shrink-0 overflow-y-auto scrollbar-hide">
    
    <!-- Sidebar Header -->
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <a href="/admin/index.php" class="flex items-center px-2">
          <img src="/images/logo.png" alt="SD Colours Logo" class="h-10 w-auto brightness-110" />
        </a>
        <button id="sidebar-close" class="md:hidden text-zinc-400 hover:text-white p-1 cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Nav items -->
      <nav class="space-y-1">
        <a href="/admin/index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= isNavActive('index.php', $currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
          </svg>
          Dashboard
        </a>
        
        <a href="/admin/orders.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= isNavActive('orders.php', $currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
          </svg>
          Manage Orders
        </a>
        
        <a href="/admin/photographers.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= isNavActive('photographers.php', $currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
          </svg>
          Photographers
        </a>
        
        <a href="/admin/products.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= isNavActive('products.php', $currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
          </svg>
          Manage Products
        </a>
        
        <a href="/admin/settings.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= isNavActive('settings.php', $currentPage) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          </svg>
          Settings
        </a>
      </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="space-y-4 pt-6 border-t border-white/5">
      <div class="px-3">
        <p class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Signed In As</p>
        <p class="text-sm text-white font-bold truncate">Administrator</p>
      </div>
      <div class="space-y-1">
        <a href="<?= $frontendUrl ?>" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-zinc-400 hover:bg-white/5 hover:text-white transition-all duration-200">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
          </svg>
          View Main Site
        </a>
        <a href="/logout.php" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-400 hover:bg-red-500/10 transition-all duration-200">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
          </svg>
          Logout
        </a>
      </div>
    </div>
  </aside>

  <!-- Mobile Overlay -->
  <div id="mobile-overlay" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm hidden md:hidden"></div>

  <!-- Content Container -->
  <main class="flex-1 h-screen overflow-y-auto flex flex-col pt-16 md:pt-0 scrollbar-hide scroll-smooth">
    <!-- Top Nav Bar (Desktop) -->
    <header class="hidden md:flex h-16 border-b border-white/5 items-center justify-between px-8 bg-secondary/80 backdrop-blur-md sticky top-0 z-30">
      <div class="flex items-center gap-4">
          <h2 class="text-sm font-semibold text-white tracking-wider flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-primary">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
            <?= htmlspecialchars($pageTitle ?? 'Control Center') ?>
          </h2>
      </div>
      <div class="flex items-center gap-6">
        <span class="text-zinc-500 text-xs px-2 py-1 rounded-md border border-white/5 bg-white/5">
          Env: <strong class="text-primary"><?= $isLocal ? 'Development' : 'Production' ?></strong>
        </span>
        <div class="flex items-center gap-3 pl-6 border-l border-white/5 cursor-pointer hover:bg-white/5 p-1 rounded-lg transition-colors">
            <div class="w-8 h-8 rounded-full bg-primary/20 border border-primary/50 flex items-center justify-center text-primary font-bold text-sm">
                A
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-white leading-none">Admin</span>
                <span class="text-[10px] text-zinc-500">Superuser</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-zinc-500 ml-1">
              <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex-grow p-4 sm:p-6 lg:p-8">
