<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ setting('app_name', config('app.name', 'Hospitality CRM')) }} - @yield('title', 'Dashboard')</title>

    <!-- Favicon - Only show if custom favicon is uploaded -->
    @php
        $faviconFile = setting('app_favicon');
        $faviconPath = $faviconFile ? storage_path('app/public/settings/' . $faviconFile) : null;
        $faviconVersion = ($faviconPath && file_exists($faviconPath)) ? '?v=' . filemtime($faviconPath) : '';
        $faviconUrl = $faviconFile ? asset('storage/settings/' . $faviconFile . $faviconVersion) : null;
    @endphp
    
    @if($faviconUrl)
        <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconUrl }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            /* Dribbble-Inspired Color Palette */
            --primary-color: #6366F1;          /* Primary Indigo */
            --primary-dark: #4F46E5;           /* Darker indigo for hover */
            --primary-light: #818CF8;          /* Lighter indigo */
            --primary-subtle: #E0E7FF;         /* Very light indigo for backgrounds */
            
            /* Background Colors */
            --page-bg: #F8FAFC;                /* Off-white slate-50 */
            --card-bg: #FFFFFF;                /* Pure white */
            --sidebar-bg: #1E293B;             /* Dark slate */
            --header-bg: #FFFFFF;              /* White */
            
            /* Accent Colors */
            --success-color: #10B981;         /* Emerald */
            --warning-color: #F59E0B;         /* Amber */
            --danger-color: #EF4444;          /* Red */
            --info-color: #3B82F6;             /* Blue */
            
            /* Text Colors */
            --text-primary: #1E293B;           /* Slate-800 */
            --text-secondary: #64748B;         /* Slate-500 */
            --text-muted: #94A3B8;             /* Slate-400 */
            --text-white: #FFFFFF;
            
            /* Layout */
            --sidebar-width: 260px;
            --header-height: 64px;
            
            /* Spacing */
            --card-radius: 16px;
            --item-radius: 10px;
            
            /* Shadows (Soft only) */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--page-bg);
            color: var(--text-primary);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* =======================
           SIDEBAR STYLES
           ======================= */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--text-white);
            z-index: 1000;
            transition: transform 0.3s ease, width 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--text-white);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        /* Section Divider */
        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 0.75rem 1.25rem;
            border: none;
        }

        .nav-section {
            margin-bottom: 0.5rem;
        }

        .nav-section-header {
            padding: 0.75rem 1.5rem 0.5rem;
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: color 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .nav-section-header:hover {
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-section-header .chevron-icon {
            transition: transform 0.3s ease;
            opacity: 0.5;
        }

        .nav-section-header.collapsed .chevron-icon {
            transform: rotate(180deg);
        }

        /* Admin Section Styling */
        .nav-section.admin-section .nav-section-header {
            background: rgba(255, 248, 225, 0.1);
            color: #fbbf24;
            margin: 0 0.75rem;
            border-radius: var(--item-radius);
            padding: 0.625rem 1rem;
        }

        .nav-section.admin-section .nav-section-header:hover {
            background: rgba(255, 248, 225, 0.15);
            color: #fcd34d;
        }

        .nav-item {
            margin: 0.125rem 0.75rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.625rem 1rem;
            border-radius: var(--item-radius);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.75rem;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .nav-link:hover {
            color: var(--text-white);
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(2px);
        }

        .nav-link.active {
            color: var(--text-white);
            background: var(--primary-color);
        }

        .nav-link.active:hover {
            background: var(--primary-color);
            transform: translateX(2px);
        }

        .nav-link i {
            font-size: 1.125rem;
            width: 1.5rem;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link .badge {
            font-size: 0.6875rem;
            padding: 0.25rem 0.5rem;
            font-weight: 600;
            margin-left: auto;
            background: rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.9);
        }

        .nav-link.active .badge {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Admin Links */
        .nav-section.admin-section .nav-link.admin-link {
            background: rgba(255, 248, 225, 0.05);
        }

        .nav-section.admin-section .nav-link.admin-link:hover {
            background: rgba(255, 248, 225, 0.12);
        }

        /* Admin Link Wrapper for nested items */
        .admin-link-wrapper {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        /* Admin Sub Links (nested under Users) */
        .nav-section.admin-section .nav-link.admin-sub-link {
            font-size: 0.8125rem;
            padding: 0.5rem 1rem 0.5rem 2.75rem;
            color: rgba(255, 255, 255, 0.6);
            background: transparent;
        }

        .nav-section.admin-section .nav-link.admin-sub-link:hover {
            color: var(--text-white);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-section.admin-section .nav-link.admin-sub-link.active {
            color: var(--text-white);
            background: var(--primary-color);
        }

        /* =======================
           MAIN CONTENT
           ======================= */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .top-header {
            height: var(--header-height);
            background: var(--header-bg);
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .page-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .notification-btn:hover {
            background: var(--page-bg);
            color: var(--text-primary);
        }

        .notification-btn i {
            font-size: 1.25rem;
        }

        .notification-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--danger-color);
            color: white;
            font-size: 0.625rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem 0.75rem;
            border-radius: var(--item-radius);
            transition: background 0.2s ease;
        }

        .user-dropdown:hover {
            background: var(--page-bg);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--primary-subtle);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .content-wrapper {
            padding: 1.75rem;
        }

        /* =======================
           CARD STYLES
           ======================= */
        .card {
            border: none;
            border-radius: var(--card-radius);
            background: var(--card-bg);
            box-shadow: var(--shadow-md);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid #F1F5F9;
            font-weight: 600;
            padding: 1.25rem 1.5rem;
        }

        /* =======================
           STAT CARD
           ======================= */
        .stat-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            transition: all 0.2s ease;
            height: 100%;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: var(--primary-subtle);
            color: var(--primary-color);
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stat-card .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .stat-card .stat-change {
            font-size: 0.8125rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-weight: 500;
        }

        .stat-change.positive {
            color: var(--success-color);
        }

        .stat-change.negative {
            color: var(--danger-color);
        }

        .stat-change.neutral {
            color: var(--text-secondary);
        }

        /* =======================
           TABLE STYLES
           ======================= */
        .table-responsive {
            border-radius: var(--item-radius);
            overflow: hidden;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: #F8FAFC;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #E2E8F0;
            padding: 0.875rem 1rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .table tbody tr {
            transition: background 0.15s ease;
        }

        .table tbody tr:hover {
            background: #F8FAFC;
        }

        /* =======================
           BADGE STYLES
           ======================= */
        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active {
            background: #DCFCE7;
            color: #166534;
        }

        .badge-inactive {
            background: #FEE2E2;
            color: #991B1B;
        }

        .badge-pending {
            background: #FEF3C7;
            color: #92400E;
        }

        /* =======================
           FORM STYLES
           ======================= */
        .form-control, .form-select {
            border: 1px solid #E2E8F0;
            border-radius: var(--item-radius);
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            background: var(--card-bg);
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        /* =======================
           BUTTON STYLES
           ======================= */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: var(--item-radius);
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-secondary {
            background: #F1F5F9;
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: #E2E8F0;
            color: var(--text-primary);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #DC2626;
            color: white;
        }

        .btn-outline-primary {
            background: transparent;
            border: 1.5px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        /* =======================
           ALERT STYLES
           ======================= */
        .alert {
            border: none;
            border-radius: var(--item-radius);
            padding: 1rem 1.25rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background: #DCFCE7;
            color: #166534;
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        /* =======================
           MOBILE STYLES
           ======================= */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            padding: 0.5rem;
            border-radius: var(--item-radius);
            transition: background 0.2s ease;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background: var(--page-bg);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
        }

        /* =======================
           RESPONSIVE
           ======================= */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: flex;
            }

            .user-info {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .content-wrapper {
                padding: 1rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-card .stat-value {
                font-size: 1.5rem;
            }
        }

        /* =======================
           ANIMATIONS
           ======================= */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }

        /* Dropdown animations */
        .dropdown-menu {
            border: none;
            border-radius: var(--item-radius);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem;
            min-width: 200px;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            transition: all 0.15s ease;
        }

        .dropdown-item:hover {
            background: var(--page-bg);
        }

        .dropdown-item i {
            margin-right: 0.5rem;
            color: var(--text-secondary);
        }

        /* =======================
           BIRTHDAY CARD STYLES
           ======================= */
        .bg-success-light {
            background-color: rgba(16, 185, 129, 0.1) !important;
        }

        .bg-success-transparent {
            background-color: rgba(16, 185, 129, 0.08) !important;
        }

        .birthday-today-card {
            transition: all 0.2s ease;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .birthday-today-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .birthday-avatar {
            font-size: 1.25rem;
        }

        .birthday-today-section {
            padding-right: 1rem;
            border-right: 1px solid #E2E8F0;
        }

        @media (max-width: 991.98px) {
            .birthday-today-section {
                border-right: none;
                border-bottom: 1px solid #E2E8F0;
                padding-bottom: 1rem;
                padding-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        @auth
        <x-sidebar />
        @endauth

        <!-- Main Content -->
        <main class="main-content flex-grow-1">
            @auth
            <header class="top-header">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle d-lg-none me-3" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="user-menu">
                    <button class="notification-btn">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="dropdown">
                        <div class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                                <div class="user-role">{{ Auth::user()->getRoleNames()->first() ?? 'Member' }}</div>
                            </div>
                            <i class="bi bi-chevron-down" style="color: var(--text-muted); font-size: 0.75rem;"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Settings</a></li>
                            <li><hr class="dropdown-divider" style="margin: 0.5rem 0;"></li>
                            <li>
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            @endauth

            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Custom JS (includes axios with credentials configured) -->
    <script src="{{ asset('build/assets/app-DgMpcWWK.js') }}"></script>
</body>
</html>

