<?php
require_once __DIR__ . '/auth.php';
startSession();

// Detect environment to point to correct frontend
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', 'localhost:8000', 'localhost:8001', '127.0.0.1', 'sdcolorslab.test']) || php_sapi_name() === 'cli';
$frontendUrl = $isLocal ? 'http://localhost:3000' : 'https://sdcolourslab.in';
?>
<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle ?? 'SD Colours Photobook Lab') ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc ?? 'Your Fast & Professional Photobook Printing Partner in Rourkela.') ?>" />
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
    /* Smooth glassmorphic background utility */
    .glass-card {
      background: rgba(28, 28, 31, 0.8);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
  </style>
</head>
<body class="h-full flex flex-col bg-darkbg text-zinc-100 antialiased">

  <!-- Minimal Header -->
  <header class="w-full bg-secondary/90 backdrop-blur-md border-b border-white/5 fixed top-0 left-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <a href="<?= $frontendUrl ?>" class="flex items-center">
        <img src="/images/logo.png" alt="SD Colours Logo" class="h-10 sm:h-12 w-auto brightness-110" />
      </a>
      <a href="<?= $frontendUrl ?>" class="flex items-center gap-1.5 border border-primary/30 bg-white/5 backdrop-blur-md px-4 py-1.5 rounded-full text-white text-xs font-semibold hover:bg-primary/15 hover:border-primary transition-all duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-primary">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Back to Home
      </a>
    </div>
  </header>

  <!-- Offset content for sticky header -->
  <div class="pt-16 flex-grow flex flex-col justify-between">
