<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Ultra5G')</title>
    
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS/JS Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-blue: #0f172a;
            --accent-yellow: #fbbf24;
            --sidebar-bg: #0f172a;
            --header-bg: #ffffff;
            --content-bg: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --success-green: #22c55e;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --input-bg: #ffffff;
            --dropdown-bg: #ffffff;
        }

        [data-theme="dark"] {
            --header-bg: #1e293b;
            --content-bg: #0f172a;
            --text-dark: #f1f5f9;
            --text-muted: #94a3b8;
            --card-bg: #1e293b;
            --border-color: #334155;
            --input-bg: #334155;
            --dropdown-bg: #1e293b;
            --sidebar-bg: #000000;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--content-bg);
            color: var(--text-dark);
            margin: 0;
            display: flex;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 24px;
            font-size: 20px;
            font-weight: 700;
            background-color: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .nav-links {
            padding: 20px 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-link i {
            margin-right: 12px;
            width: 20px;
        }

        /* Main Content Styles */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        .top-header {
            height: 64px;
            background-color: var(--header-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .content-area {
            padding: 24px;
            flex: 1;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border-color);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin: 8px 0;
            color: var(--text-dark);
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tables & Lists */
        .data-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        /* Responsive Utilities */
        .lg\:block { display: none; }
        .lg\:hidden { display: block; }

        @media (min-width: 1025px) {
            .lg\:block { display: block !important; }
            .lg\:hidden { display: none !important; }
        }

        /* Responsive Sidebar & Layout */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
                width: 280px;
            }
            .sidebar.open {
                transform: translateX(0);
                box-shadow: 10px 0 30px rgba(0,0,0,0.2);
            }
            .main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 900;
            }
            .sidebar-overlay.show {
                display: block;
            }
            .content-area {
                padding: 15px;
            }
            .top-header {
                padding: 0 15px;
            }
        }
        .overflow-hidden { overflow: hidden; }

        /* Table Responsive Wrapper */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Responsive Grid Utility */
        .grid-2-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 640px) {
            .grid-2-col {
                grid-template-columns: 1fr;
            }
        }
        /* Dropdown Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 240px;
            background: var(--dropdown-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            margin-top: 12px;
            display: none;
            z-index: 2000;
            padding: 8px 0;
            animation: slideDown 0.2s ease-out;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f8fafc;
            color: var(--primary-blue);
        }

        .dropdown-item i {
            color: var(--text-muted);
            width: 18px;
            font-size: 14px;
        }

        .dropdown-header {
            padding: 8px 16px 12px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 8px;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header" style="padding: 15px 20px 5px; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <div style="margin-bottom: 0;">
                <img src="{{ asset('assets/images/logo-white.svg') }}" alt="Logo" style="height: 65px; width: auto; max-width: 100%;">
            </div>
        </div>
        <div class="nav-links" style="padding: 10px 10px;">
            <div style="padding: 5px 15px 10px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Main Menu</div>
            <a href="{{ route('dashboard') }}" class="nav-link active">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-box-open"></i> Inventory (LEDs)
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-drum-steelpan"></i> Wires & Cables
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-solar-panel"></i> Solar Products
            </a>
            
            <div style="padding: 20px 15px 10px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Administration</div>
            <a href="#" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Sales Orders
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-users-gear"></i> Dealer Network
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-pie"></i> Reports
            </a>

            @if(auth()->user()->hasRole('super_admin'))
                <div style="padding: 20px 15px 10px; font-size: 11px; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Security Management</div>
                <a href="{{ route('rbac.users') }}" class="nav-link {{ request()->routeIs('rbac.users') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i> User Roles
                </a>
                <a href="{{ route('rbac.roles') }}" class="nav-link {{ request()->routeIs('rbac.roles') ? 'active' : '' }}">
                    <i class="fas fa-key"></i> Roles & Permissions
                </a>
            @endif
            
            <div style="margin-top: auto; padding: 20px 10px;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; color: #f87171;">
                        <i class="fas fa-power-off"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="top-header">
            <div style="display: flex; align-items: center; flex: 1;">
                <button id="sidebar-toggle" style="background: none; border: none; font-size: 20px; margin-right: 15px; cursor: pointer; color: var(--text-muted);" class="lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-container" style="position: relative; max-width: 400px; width: 100%; display: none;" class="lg:block">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px;"></i>
                    <input type="text" placeholder="Search analytics, users..." style="width: 100%; padding: 8px 12px 8px 35px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px; background: var(--input-bg); color: var(--text-dark);">
                </div>
            </div>
            <div class="user-profile" style="display: flex; align-items: center; gap: 15px;">
                <!-- Theme Toggle -->
                <div id="theme-toggle" style="cursor: pointer; padding: 8px; border-radius: 8px; background: var(--input-bg); border: 1px solid var(--border-color); color: var(--text-muted); transition: all 0.2s; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-moon"></i>
                </div>
                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <div id="notif-dropdown-toggle" style="position: relative; cursor: pointer; padding: 5px;">
                        <i class="fas fa-bell" style="color: var(--text-muted); font-size: 18px;"></i>
                        <span style="position: absolute; top: 0; right: 0; width: 10px; height: 10px; background: #ef4444; border-radius: 50%; border: 2px solid white;"></span>
                    </div>
                    <div class="dropdown-menu" id="notif-dropdown">
                        <div class="dropdown-header">
                            <div style="font-weight: 700; font-size: 14px;">Notifications</div>
                        </div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-shopping-cart" style="color: #3b82f6;"></i>
                            <div>
                                <div style="font-weight: 600;">New Order #429</div>
                                <div style="font-size: 11px; color: var(--text-muted);">From Raipur Electricals</div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-exclamation-circle" style="color: #f59e0b;"></i>
                            <div>
                                <div style="font-weight: 600;">Low Stock Alert</div>
                                <div style="font-size: 11px; color: var(--text-muted);">9W LED Bulbs (12 left)</div>
                            </div>
                        </a>
                        <div style="padding: 10px; border-top: 1px solid #f1f5f9; text-align: center;">
                            <a href="#" style="font-size: 12px; color: var(--primary-blue); font-weight: 600; text-decoration: none;">View All Notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown" style="padding-left: 15px; border-left: 1px solid #e5e7eb;">
                    <div id="profile-dropdown-toggle" style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 5px; border-radius: 8px; transition: background 0.2s;">
                        <div id="header-user-initial" style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div style="display: none;" class="lg:block">
                            <div id="header-user-name" style="font-size: 14px; font-weight: 600; color: var(--text-dark);">{{ Auth::user()->name }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">Administrator <i class="fas fa-chevron-down" style="font-size: 8px; margin-left: 5px;"></i></div>
                        </div>
                    </div>
                    <div class="dropdown-menu" id="profile-dropdown">
                        <div class="dropdown-header">
                            <div id="profile-dropdown-name" style="font-weight: 700; font-size: 14px;">{{ Auth::user()->name }}</div>
                            <div id="profile-dropdown-email" style="font-size: 12px; color: var(--text-muted);">{{ Auth::user()->email }}</div>
                        </div>
                        <a href="{{ route('profile') }}" class="dropdown-item">
                            <i class="fas fa-user-circle"></i> My Profile
                        </a>
                        <a href="{{ route('profile') }}" class="dropdown-item">
                            <i class="fas fa-cog"></i> Account Settings
                        </a>
                        <div style="border-top: 1px solid #f1f5f9; margin-top: 8px; padding-top: 8px;">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item" style="color: #ef4444;">
                                    <i class="fas fa-power-off"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            @yield('content')
        </main>
    </div>

    <script>
        $(document).ready(function() {
            // Sidebar Toggle
            $('#sidebar-toggle, #sidebar-overlay').on('click', function() {
                $('#sidebar').toggleClass('open');
                $('#sidebar-overlay').toggleClass('show');
                $('body').toggleClass('overflow-hidden');
            });

            // Dropdown Toggles
            $('#notif-dropdown-toggle').on('click', function(e) {
                e.stopPropagation();
                $('#profile-dropdown').removeClass('show');
                $('#notif-dropdown').toggleClass('show');
            });

            $('#profile-dropdown-toggle').on('click', function(e) {
                e.stopPropagation();
                $('#notif-dropdown').removeClass('show');
                $('#profile-dropdown').toggleClass('show');
            });

            // Close dropdowns on click outside
            $(document).on('click', function() {
                $('.dropdown-menu').removeClass('show');
            });

            // Prevent closing when clicking inside dropdown
            $('.dropdown-menu').on('click', function(e) {
                e.stopPropagation();
            });

            // Theme Toggle Logic
            const themeToggle = $('#theme-toggle');
            const themeIcon = themeToggle.find('i');
            const currentTheme = localStorage.getItem('theme') || 'light';

            // Apply theme on load
            if (currentTheme === 'dark') {
                $('html').attr('data-theme', 'dark');
                themeIcon.removeClass('fa-moon').addClass('fa-sun');
            }

            themeToggle.on('click', function() {
                const isDark = $('html').attr('data-theme') === 'dark';
                
                if (isDark) {
                    $('html').removeAttr('data-theme');
                    localStorage.setItem('theme', 'light');
                    themeIcon.removeClass('fa-sun').addClass('fa-moon');
                } else {
                    $('html').attr('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                    themeIcon.removeClass('fa-moon').addClass('fa-sun');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
