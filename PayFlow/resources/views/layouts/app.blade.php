<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title','Dashboard') — PayFlow</title>

  {{-- ✅ Bootstrap CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- ✅ Bootstrap Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- ✅ Your custom app layout CSS --}}
  <link rel="stylesheet" href="{{ asset('css/applayout.css') }}">

  <style>
/* 🌙 Improved Dark Mode Readability */
body.dark-mode {
    background-color: #0f0f0f !important;
    color: #f1f1f1 !important;
}

/* Cards, forms, and modals */
body.dark-mode .card,
body.dark-mode .modal-content {
    background-color: #1a1a1a !important;
    color: #ffffff !important;
    border-color: #2c2c2c !important;
}

/* Labels, headings, and muted text */
body.dark-mode label,
body.dark-mode h1,
body.dark-mode h2,
body.dark-mode h3,
body.dark-mode h4,
body.dark-mode h5,
body.dark-mode h6 {
    color: #f1f1f1 !important;
}

body.dark-mode .text-muted,
body.dark-mode small {
    color: #bbbbbb !important;
}

/* Input fields and dropdowns */
body.dark-mode .form-control,
body.dark-mode .form-select {
    background-color: #2a2a2a !important;
    color: #ffffff !important;
    border-color: #444 !important;
}

body.dark-mode .form-control::placeholder {
    color: #aaaaaa !important;
}

/* Buttons */
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

/* Switch toggles */
body.dark-mode .form-check-input:checked {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}

/* Dropdowns and selects */
body.dark-mode option {
    background-color: #1a1a1a;
    color: #f1f1f1;
}

/* Input focus state */
body.dark-mode .form-control:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

/* Card header or title contrast */
body.dark-mode .card h5,
body.dark-mode .card-title {
    color: #ffffff !important;
}

/* ✅ FIX: Prevent Sidebar from Scrolling */
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
}

main {
    margin-left: 250px;
    flex-grow: 1;
    height: 100vh;
    overflow-y: auto;
    background-color: #f8f9fa;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}
.sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.5);
}
  </style>

</head>
<body>
  <div class="layout">
    <aside class="sidebar" role="navigation" aria-label="Main sidebar">
      <div class="brand">
        <div class="logo">PF</div>
        <div class="name">PayFlow</div>
      </div>

      <nav>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-house-door-fill"></i></span><span>Dashboard</span>
        </a>
        <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-people-fill"></i></span><span>Employees</span>
        </a>
        <a href="{{ route('employeeschedule.index') }}" class="nav-link {{ request()->routeIs('employeeschedule.*') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-calendar-check-fill"></i></span><span>Scheduling</span>
        </a>
        <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-clock-fill"></i></span><span>Attendance</span>
        </a>
        <a href="{{ route('payrolldata') }}" class="nav-link {{ request()->routeIs('payrolldata') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-receipt"></i></span><span>Payroll</span>
        </a>
        <a href="{{ route('reports') }}" class="nav-link {{ request()->routeIs('reports') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-bar-chart-fill"></i></span><span>Reports</span>
        </a>
        <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
          <span class="icon"><i class="bi bi-gear-fill"></i></span><span>Settings</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        @if (Auth::check() )
        <div class="profile-container">
          <div class="profile" id="profileButton">
            <img src="{{ asset('images/profile.png') }}" alt="Profile photo">
            <div>
              <div style="font-size:14px">{{ Auth::user()->name }}</div>
              <div style="font-size:12px;color:var(--muted)">{{ Auth::user()->email }}</div>
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
      <header class="p-3 border-bottom">
        <h1 class="h4 fw-bold">@yield('title','DASHBOARD')</h1>
      </header>
      <div class="p-4">
        @yield('content')
      </div>
    </main>
  </div>

  {{-- ✅ Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
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
  </script>
</body>
</html>
