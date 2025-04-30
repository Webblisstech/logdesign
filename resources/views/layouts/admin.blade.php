<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin Dashboard - Oprime Style')</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<!-- Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
<!-- SweetAlert2 -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f4f6f9;
      overflow-x: hidden;
    }

    /* SIDEBAR */
    .sidebar {
      background-color: #0d1b47;
      min-height: 100vh;
      color: white;
      padding: 1.5rem 1rem;
      position: fixed;
      top: 0;
      left: 0;
      width: 220px;
      transition: transform 0.3s ease;
      z-index: 2000;
    }

    .sidebar a {
      color: #d1d5db;
      text-decoration: none;
      display: block;
      margin-bottom: 1rem;
      font-weight: 500;
      padding: 0.5rem;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
      color: #ffffff;
      background-color: rgba(255, 255, 255, 0.1);
    }

    /* TOPBAR */
    .topbar {
      background-color: #06133b;
      padding: 1rem;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    /* CONTENT */
    .content {
      margin-left: 220px;
      padding: 2rem;
      transition: all 0.3s ease;
    }

    /* OVERLAY */
    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1500;
      cursor: pointer;
    }

    /* MOBILE BEHAVIOR */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        width: 260px;
      }

      .content {
        margin-left: 0;
        padding: 1rem;
        transform: translateX(0);
        transition: all 0.3s ease;
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .content.sidebar-open {
        transform: translateX(260px);
      }

      .overlay.show {
        display: block;
      }

      .topbar span:first-child {
        display: none;
      }
    }

    .profile-img {
      width: 30px;
      height: 30px;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>

  @stack('styles')
</head>

<body>

  <!-- Sidebar -->
  <div id="sidebar" class="sidebar">
    <h4 class="mb-4">Admin Panel</h4>
    <a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
    <a href="{{ route('admin.categories') }}"><i class="fas fa-th-large me-2"></i>Categories</a>
    <!-- <a href="#"><i class="fas fa-tags me-2"></i>Coupons</a> -->
    <a href="{{ route('admin.products') }}"><i class="fas fa-boxes me-2"></i>Products</a>
    <a href="{{ route('admin.users') }}"><i class="fas fa-users me-2"></i>Manage Users</a>
    <a href="{{ route('admin.orders') }}"><i class="fas fa-credit-card me-2"></i>Orders</a>
    <a href="{{ route('admin.transactions') }}"><i class="fas fa-wallet me-2"></i>Transactions</a>
    <!-- <a href="#"><i class="fas fa-life-ring me-2"></i>Support Ticket</a>
    <a href="#"><i class="fas fa-chart-line me-2"></i>Report</a>
    <a href="#"><i class="fas fa-user-plus me-2"></i>Subscribers</a> -->
    <hr>
    <a href="{{ route('admin.settings') }}"><i class="fas fa-cog me-2"></i>General Setting</a>
    <!-- <a href="#"><i class="fas fa-tools me-2"></i>System Configuration</a>
    <a href="#"><i class="fas fa-image me-2"></i>Logo & Favicon</a>
    <a href="#"><i class="fas fa-share-alt me-2"></i>Social Credentials</a>
    <a href="#"><i class="fas fa-plug me-2"></i>Extensions</a>
    <a href="#"><i class="fas fa-language me-2"></i>Language</a> -->
  </div>

  <!-- Overlay -->
  <div class="overlay" onclick="toggleSidebar()"></div>

  <!-- Main Content -->
  <div id="main-content" class="content">
    <!-- Topbar -->
    <div class="topbar">
      <button class="btn btn-outline-light d-md-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
      </button>
      <span class="d-none d-md-inline">Search here...</span>
      <div class="d-flex align-items-center gap-2">
        <img src="https://via.placeholder.com/30" class="profile-img" alt="Admin">
        <span class="d-none d-sm-inline">Admin</span>
      </div>
    </div>

    <div class="container-fluid mt-4">
      @yield('content')
    </div>
  </div>
<!-- jQuery CDN -->

<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-VvDr1o4EFQvZ+EYoM6OgF7u3FS5S2HR1OVi7P2J+7oI="
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('main-content');
        const overlay = document.querySelector('.overlay');

        sidebar.classList.toggle('show');
        content.classList.toggle('sidebar-open');
        overlay.classList.toggle('show');
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('main-content').classList.remove('sidebar-open');
            document.querySelector('.overlay').classList.remove('show');
        }
    });

    document.addEventListener('click', (event) => {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('main-content');
        const overlay = document.querySelector('.overlay');
        const toggleBtn = document.querySelector('.btn-outline-light');

        if (window.innerWidth <= 768 &&
            !sidebar.contains(event.target) &&
            !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('show');
            content.classList.remove('sidebar-open');
            overlay.classList.remove('show');
        }
    });
</script>

@stack('scripts')


  @stack('scripts')
</body>

</html>