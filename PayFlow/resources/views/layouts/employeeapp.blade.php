<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title','employee.dashboard') — PayFlow</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/applayout.css') }}">

  <style>
    body.dark-mode {
      background-color: #0f0f0f !important;
      color: #f1f1f1 !important;
    }
    body.dark-mode .card,
    body.dark-mode .modal-content {
      background-color: #1a1a1a !important;
      color: #ffffff !important;
      border-color: #2c2c2c !important;
    }
    body.dark-mode label, body.dark-mode h1, body.dark-mode h2, body.dark-mode h3,
    body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
      color: #f1f1f1 !important;
    }
    body.dark-mode .text-muted, body.dark-mode small { color: #bbbbbb !important; }
    body.dark-mode .form-control, body.dark-mode .form-select {
      background-color: #2a2a2a !important;
      color: #ffffff !important;
      border-color: #444 !important;
    }
    body.dark-mode .form-control::placeholder { color: #aaaaaa !important; }
    body.dark-mode .btn-primary {
      background-color: #0d6efd !important;
      border-color: #0d6efd !important;
      color: #fff !important;
    }
    body.dark-mode .btn-success {
      background-color: #198754 !important;
      border-color: #198754 !important;
      color: #fff !important;
    }
    body.dark-mode .form-check-input:checked {
      background-color: #0d6efd !important;
      border-color: #0d6efd !important;
    }
    body.dark-mode option {
      background-color: #1a1a1a;
      color: #f1f1f1;
    }
    body.dark-mode .form-control:focus {
      border-color: #0d6efd !important;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    body.dark-mode .card h5, body.dark-mode .card-title { color: #ffffff !important; }

    .layout {
      display: flex;
      height: 100vh;
      overflow: hidden;
    }
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      overflow-y: auto;
      background-color: #0b1d39;
      z-index: 1000;
      transition: transform 0.3s ease-in-out;
    }
    main {
      margin-left: 250px;
      flex-grow: 1;
      height: 100vh;
      overflow-y: auto;
      background-color: #f8f9fa;
      transition: margin-left 0.3s ease-in-out;
    }
    .sidebar::-webkit-scrollbar { width: 6px; }
    .sidebar::-webkit-scrollbar-thumb {
      background-color: rgba(255, 255, 255, 0.3);
      border-radius: 4px;
    }
    .sidebar::-webkit-scrollbar-thumb:hover { background-color: rgba(255, 255, 255, 0.5); }

    /* Hamburger menu button */
    .menu-btn {
      display: none;
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1100;
      font-size: 24px;
      color: #fff;
      background-color: #0b1d39;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
    }

    /* Responsive for small screens */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.active {
        transform: translateX(0);
      }
      main {
        margin-left: 0;
      }
      .menu-btn {
        display: block;
      }
    }

  </style>
</head>
<body>
  <button class="menu-btn" id="menuBtn"><i class="bi bi-list"></i></button>

  <div class="layout">
    <aside class="sidebar" role="navigation" aria-label="Main sidebar" id="sidebar">
      <div class="brand">
        <div class="logo">PF</div>
        <div class="name">PayFlow</div>
      </div>

      <nav>
        <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-house-door-fill"></i></span><span>Dashboard</span>
        </a>
        <a href="{{ route('employee.profile') }}" class="nav-link {{ request()->routeIs('employee.profile') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-people-fill"></i></span><span>Profile</span>
        </a>
        <a href="{{ route('employee.request') }}" class="nav-link {{ request()->routeIs('employee.request') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-receipt"></i></span><span>Leave Request</span>
        </a>
        <a href="{{ route('employee.settings') }}" class="nav-link {{ request()->routeIs('employee.settings') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-gear-fill"></i></span><span>Settings</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        @if(auth()->guard('employee')->check())
          @php
              $employee = auth()->guard('employee')->user();
          @endphp

          <div class="profile-container">
              <div class="profile" id="profileButton">
                  <img src="{{ $employee->profile_picture && file_exists(storage_path('app/public/' . $employee->profile_picture)) 
                              ? asset('storage/' . $employee->profile_picture) 
                              : asset('images/default-profile.png') }}" alt="Profile photo">
                  <div>
                      <div style="font-size:14px">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                      <div style="font-size:12px;color:var(--muted)">{{ $employee->email }}</div>
                  </div>
              </div>

              <div class="dropdown-menu" id="profileMenu">
                  <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="btn btn-danger btn-sm w-100">Log out</button>
                  </form>
              </div>
          </div>
        @endif

        <div class="text-center mt-3">
            © {{ date('Y') }} PayFlow
        </div>
      </div>
    </aside>

    <main>
      <header>
        <h1 class="h4 fw-bold">@yield('title','')</h1>
      </header>
      <div class="p-4">
        @yield('content')
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Profile dropdown toggle
    document.addEventListener('click', function(e) {
      const profileButton = document.getElementById('profileButton');
      const menu = document.getElementById('profileMenu');

      if (!profileButton || !menu) return;

      if (profileButton.contains(e.target)) {
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
      } else {
        menu.style.display = 'none';
      }
    });

    // Sidebar toggle for small screens
    const menuBtn = document.getElementById('menuBtn');
  const sidebar = document.getElementById('sidebar');

  menuBtn.addEventListener('click', (e) => {
    sidebar.classList.add('active'); // show sidebar
    menuBtn.style.display = 'none';  // hide button
  });

  // Click outside sidebar to close it
  document.addEventListener('click', function(e) {
    if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
      if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
        sidebar.classList.remove('active'); // hide sidebar
        menuBtn.style.display = 'block';     // show button again
      }
    }
  });

    // DateTime update (existing)
    function updateDateTime() {
      const now = new Date();
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
      const formatted = now.toLocaleString('en-US', options);
      const el = document.getElementById('currentDateTime');
      if(el) el.textContent = formatted;
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);
  </script>
</body>
</html>
