<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title></title>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- TailwindCSS (optional, remove if not using) -->
  <!-- <script src="https://cdn.tailwindcss.com"></script> -->

  <style>
    :root {
      --background-light: linear-gradient(135deg, #f5f7fa, #c3cfe2);
      --background-dark: linear-gradient(135deg, #1f1f1f, #2b2b2b);
      --text-light: #333;
      --text-dark: #eee;
      --header-light: rgba(255,255,255,0.8);
      --header-dark: rgba(0,0,0,0.7);
      --card-light: rgba(255,255,255,0.8);
      --card-dark: rgba(40,40,40,0.8);
    }

    body {
      font-family: 'Figtree', 'Segoe UI', sans-serif;
      background: var(--background-light);
      color: var(--text-light);
      transition: all 0.5s ease;
      overflow-x: hidden;
    }

    [data-theme="dark"] body {
      background: var(--background-dark);
      color: var(--text-dark);
    }

    /* Header */
    .header {
      backdrop-filter: blur(10px);
      background: var(--header-light);
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      padding: 15px 25px;
      position: fixed;
      width: 100%;
      z-index: 1050;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.5s ease;
    }

    [data-theme="dark"] .header {
      background: var(--header-dark);
      border-color: rgba(0,0,0,0.3);
    }

    .header .logo img {
      height: 40px;
    }

    .wallet-balance {
      background: rgba(13,110,253,0.8);
      backdrop-filter: blur(10px);
      padding: 8px 20px;
      border-radius: 30px;
      color: #fff;
      font-weight: bold;
      box-shadow: 0 4px 12px rgba(13,110,253,0.3);
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: -260px;
      width: 240px;
      height: 100%;
      background: var(--header-light);
      backdrop-filter: blur(16px);
      border-right: 1px solid rgba(255,255,255,0.2);
      padding-top: 90px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.05);
      transition: all 0.5s ease;
      opacity: 0;
      z-index: 1040;
    }

    [data-theme="dark"] .sidebar {
      background: var(--header-dark);
      border-color: rgba(0,0,0,0.2);
    }

    .sidebar.active {
      left: 0;
      opacity: 1;
    }

    .sidebar .nav-link {
      padding: 14px 20px;
      margin: 6px 15px;
      border-radius: 12px;
      color: inherit;
      font-weight: 500;
      display: flex;
      align-items: center;
      transition: 0.3s;
    }

    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      background: rgba(13,110,253,0.15);
      transform: scale(1.03);
      color: #0d6efd;
    }

    .sidebar .nav-link i {
      margin-right: 12px;
      font-size: 18px;
    }

    /* Main content */
    .main-content {
      margin-left: 0;
      padding: 110px 30px 30px;
      transition: margin 0.5s ease;
    }

    .main-content.shifted {
      margin-left: 260px;
    }

    /* Toggle Button */
    .toggle-btn {
      background: #0d6efd;
      border: none;
      padding: 8px 12px;
      border-radius: 10px;
      color: #fff;
      font-size: 22px;
      box-shadow: 0 4px 12px rgba(13,110,253,0.3);
    }

    .theme-toggle {
      margin-left: 20px;
      cursor: pointer;
      font-size: 22px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
      .main-content.shifted {
        margin-left: 0;
      }
    }
  </style>
</head>

<body>

<!-- Header -->
<div class="header">
  <div class="d-flex align-items-center">
    <button class="toggle-btn me-3" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>
    <a href="/" class="logo">
      <img src="https://osocialcare.com/assets/uploads/userda39a3ee5e6b4b0d3255bfef95601890afd80709/be4543b4dac3fc50f9cf00dd2a8a0add.png" alt="Logo">
    </a>
  </div>

  <div class="d-flex align-items-center">
    <div class="wallet-balance">
      ₦{{ number_format(auth()->user()->wallet ?? 0, 2) }}
    </div>
    <div class="theme-toggle ms-3" onclick="toggleTheme()" title="Toggle Theme">
      <i id="theme-icon" class="fas fa-sun"></i>
    </div>
  </div>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <nav class="nav flex-column">
    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/"><i class="fas fa-home"></i> Dashboard</a>
    <a class="nav-link {{ request()->routeIs('wallet.index') ? 'active' : '' }}" href="{{ route('wallet.index') }}"><i class="fas fa-wallet"></i> Wallet</a>
    <a class="nav-link {{ request()->routeIs('orders') ? 'active' : '' }}" href="{{ route('orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><i class="fas fa-user"></i> Profile</a>
    <a class="nav-link" href="#"><i class="fas fa-cog"></i> Settings</a>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 mx-3">
      @csrf
      <button type="submit" class="btn btn-outline-danger w-100">
        <i class="fas fa-sign-out-alt me-2"></i> Logout
      </button>
    </form>
  </nav>
</div>

<!-- Main Content -->
<div class="main-content" id="main-content">
  @isset($header)
    <div class="bg-white shadow rounded-4 p-5 mb-4" style="backdrop-filter: blur(8px); background: var(--card-light);">
      {{ $header }}
    </div>
  @endisset

  @yield('content')
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('active');
  document.getElementById('main-content').classList.toggle('shifted');
}

// Dark/Light Mode Switcher
function toggleTheme() {
  const htmlTag = document.documentElement;
  if (htmlTag.getAttribute('data-theme') === 'light') {
    htmlTag.setAttribute('data-theme', 'dark');
    document.getElementById('theme-icon').classList.replace('fa-sun', 'fa-moon');
  } else {
    htmlTag.setAttribute('data-theme', 'light');
    document.getElementById('theme-icon').classList.replace('fa-moon', 'fa-sun');
  }
}
</script>

@yield('scripts')

</body>
</html>
