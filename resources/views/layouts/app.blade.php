<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SSMS') – Student Subject Management System</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:       #1a3c6e;
            --primary-light: #2756a8;
            --accent:        #f4a623;
            --sidebar-w:     260px;
            --sidebar-bg:    #0f2447;
            --body-bg:       #f0f4f8;
        }

        * { font-family: 'Inter', sans-serif; }
        body { background: var(--body-bg); overflow-x: hidden; }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #162d55 100%);
            position: fixed;
            top: 0; left: 0;
            transition: width .25s ease;
            z-index: 1040;
            display: flex; flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.4rem 1.2rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand .brand-logo {
            width: 40px; height: 40px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #fff; font-weight: 700;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-text { color: #fff; font-weight: 700; font-size: .95rem; line-height: 1.2; }
        .sidebar-brand .brand-sub  { color: rgba(255,255,255,.5); font-size: .7rem; }

        /* User mini-card in sidebar */
        .sidebar-user {
            padding: .8rem 1rem;
            margin: .8rem;
            background: rgba(255,255,255,.07);
            border-radius: 10px;
            display: flex; align-items: center; gap: .7rem;
        }
        .sidebar-user img {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }
        .sidebar-user .u-name { color: #fff; font-size: .82rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user .u-role { color: rgba(255,255,255,.45); font-size: .7rem; }

        /* Nav links */
        .sidebar-nav { padding: .5rem 0; flex: 1; }
        .nav-section-label {
            color: rgba(255,255,255,.35);
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 1rem 1.2rem .4rem;
        }
        .nav-link-item {
            display: flex; align-items: center; gap: .75rem;
            color: rgba(255,255,255,.7);
            padding: .62rem 1.2rem;
            border-radius: 8px;
            margin: .1rem .6rem;
            text-decoration: none;
            font-size: .875rem;
            transition: all .18s;
        }
        .nav-link-item i { font-size: 1rem; flex-shrink: 0; }
        .nav-link-item:hover {
            background: rgba(255,255,255,.1);
            color: #fff;
        }
        .nav-link-item.active {
            background: var(--primary-light);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(39,86,168,.4);
        }
        .nav-link-item.active i { color: var(--accent); }

        /* ── FIX: Main content must not bleed height into chart resize loop ── */
        #main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            transition: margin .25s ease;
            /* Prevents Chart.js ResizeObserver from reading a growing parent height */
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .8rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1030;
            flex-shrink: 0; /* ← FIX: topbar must never shrink/grow */
        }
        .topbar .page-heading { font-size: 1.1rem; font-weight: 700; color: var(--primary); }
        .topbar .breadcrumb { font-size: .75rem; margin: 0; }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.4rem;
            border: none;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.09); }
        .stat-card .icon-box {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .stat-card .stat-value { font-size: 1.9rem; font-weight: 700; color: var(--primary); }
        .stat-card .stat-label { color: #64748b; font-size: .82rem; }

        /* ── FIX: content-area must have stable block layout, not flex ── */
        .content-area {
            padding: 1.5rem;
            flex: 1;          /* fills remaining height inside #main-content */
            min-height: 0;    /* ← FIX: prevents flex child from forcing parent taller */
            overflow-y: auto; /* ← FIX: scroll happens here, not on the body */
        }

        /* Table styling */
        .table-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            overflow: hidden;
        }
        .table-card .table-header {
            padding: 1.2rem 1.4rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .6rem;
        }
        .table-card .table-title { font-weight: 700; color: var(--primary); font-size: 1rem; }
        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 2px solid #e2e8f0;
            padding: .85rem 1rem;
        }
        .table tbody td { padding: .85rem 1rem; vertical-align: middle; font-size: .875rem; color: #374151; }
        .table tbody tr:hover { background: #f8fafc; }

        /* Badge */
        .semester-badge {
            font-size: .72rem;
            padding: .3em .7em;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Forms */
        .form-label { font-weight: 600; font-size: .82rem; color: #374151; }
        .form-control, .form-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: .875rem;
            padding: .55rem .85rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(39,86,168,.12);
        }
        .form-control.is-invalid { border-color: #dc3545; }

        /* Buttons */
        .btn-primary {
            background: var(--primary-light);
            border-color: var(--primary-light);
        }
        .btn-primary:hover { background: var(--primary); border-color: var(--primary); }
        .btn { border-radius: 8px; font-size: .875rem; font-weight: 500; }

        /* Toast container */
        .toast-container { z-index: 9999; }

        /* Modals */
        .modal-header { background: var(--primary); color: #fff; border-radius: .4rem .4rem 0 0; }
        .modal-title { font-weight: 700; font-size: 1rem; }
        .modal-header .btn-close { filter: invert(1); }

        /* Profile pic */
        .profile-avatar {
            width: 110px; height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-light);
            box-shadow: 0 4px 16px rgba(0,0,0,.15);
        }

        /* Responsive sidebar */
        @media (max-width: 768px) {
            #sidebar {
                width: var(--sidebar-w);
                transform: translateX(calc(-1 * var(--sidebar-w)));
            }
            #sidebar.open { transform: translateX(0); }
            #main-content { margin-left: 0; }
            .sidebar-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.45);
                z-index: 1039;
            }
            .sidebar-overlay.show { display: block; }
        }

        /* Auth pages */
        .auth-wrapper {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, #2756a8 50%, #1a4fa0 100%);
            padding: 1rem;
        }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            width: 100%; max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
        }
        .auth-logo {
            width: 64px; height: 64px;
            background: var(--primary);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; color: #fff; margin: 0 auto 1rem;
        }
    </style>
    @yield('styles')
</head>
<body>

{{-- Sidebar overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

{{-- Sidebar --}}
<nav id="sidebar">
    <div class="sidebar-brand d-flex align-items-center gap-2">
        <div class="brand-logo">S</div>
        <div>
            <div class="brand-text">SSMS</div>
            <div class="brand-sub">Subject Management</div>
        </div>
    </div>

    {{-- User info --}}
    <div class="sidebar-user">
        <img src="{{ Auth::user()->profile_picture && Auth::user()->profile_picture !== 'default.png'
            ? asset('uploads/profiles/' . Auth::user()->profile_picture)
            : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=f4a623&color=fff&size=80' }}"
            alt="Avatar">
        <div>
            <div class="u-name">{{ Auth::user()->name }}</div>
            <div class="u-role">Logged In</div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section-label">Management</div>
        <a href="{{ route('subjects.index') }}" class="nav-link-item {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
            <i class="bi bi-book"></i> Subject List
        </a>
        <a href="{{ route('users.index') }}" class="nav-link-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Users
        </a>

        <div class="nav-section-label">Account</div>
        <a href="{{ route('profile.index') }}" class="nav-link-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> My Profile
        </a>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="nav-link-item border-0 w-100 text-start"
                style="background:none; cursor:pointer; color:rgba(255,255,255,.7);">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</nav>

{{-- Main content --}}
<div id="main-content">
    {{-- Topbar --}}
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <div class="page-heading">@yield('page-title', 'Dashboard')</div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary-subtle text-primary fw-semibold d-none d-sm-inline-flex">
                <i class="bi bi-person me-1"></i> {{ Auth::user()->name }}
            </span>
        </div>
    </div>

    {{-- Content --}}
    <div class="content-area">
        @yield('content')
    </div>
</div>

{{-- Toast Container --}}
<div class="toast-container position-fixed top-0 end-0 p-3">
    @if (session('toast_success'))
    <div class="toast align-items-center text-bg-success border-0 show" role="alert" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body fw-semibold">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('toast_success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
    @if (session('toast_error'))
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body fw-semibold">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('toast_error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toast auto-init
    document.querySelectorAll('.toast').forEach(el => {
        new bootstrap.Toast(el, { delay: 4000 }).show();
    });

    // Sidebar toggle (mobile)
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }

    // CSRF for fetch requests
    window.csrfToken = '{{ csrf_token() }}';
</script>
@yield('scripts')
</body>
</html>