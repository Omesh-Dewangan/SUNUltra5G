<!DOCTYPE html>
<html lang="en" id="html-tag">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SUNUltra 5G - Inventory')</title>
    
    <!-- CSS & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: linear-gradient(180deg, #18181b 0%, #000000 100%);
            --header-bg: #ffffff;
            --content-bg: #f8fafc;
            --primary-blue: #3b82f6;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        [data-theme="dark"] {
            --header-bg: #1e293b;
            --content-bg: #0f172a;
            --text-dark: #f1f5f9;
            --text-muted: #94a3b8;
            --card-bg: #1e293b;
            --border-color: #334155;
            --sidebar-bg: linear-gradient(180deg, #0a0a0a 0%, #000000 100%);
        }

        [data-theme="dark"] .bg-light,
        [data-theme="dark"] .bg-white,
        [data-theme="dark"] .bg-light-subtle {
            background-color: var(--card-bg) !important;
        }
        
        [data-theme="dark"] .text-dark {
            color: var(--text-dark) !important;
        }

        [data-theme="dark"] .text-muted,
        [data-theme="dark"] .text-secondary {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select,
        [data-theme="dark"] .input-group-text {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: var(--text-dark) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .breadcrumb-item.active {
            color: var(--text-dark) !important;
        }
        
        [data-theme="dark"] .breadcrumb {
            background-color: transparent !important;
        }

        [data-theme="dark"] .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.7;
        }

        [data-theme="dark"] .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-dark);
            --bs-table-border-color: var(--border-color);
            --bs-table-hover-bg: rgba(255, 255, 255, 0.05);
            --bs-table-striped-bg: rgba(255, 255, 255, 0.02);
            color: var(--text-dark);
        }

        [data-theme="dark"] .card,
        [data-theme="dark"] .data-card {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-dark) !important;
        }

        [data-theme="dark"] .card-header,
        [data-theme="dark"] .card-footer {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-color: var(--border-color) !important;
            color: var(--text-dark) !important;
        }

        [data-theme="dark"] .modal-content {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-dark) !important;
        }

        [data-theme="dark"] .modal-header,
        [data-theme="dark"] .modal-footer {
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .dropdown-menu {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
        }

        [data-theme="dark"] .dropdown-item {
            color: var(--text-dark) !important;
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        [data-theme="dark"] select option {
            background-color: #1e293b;
            color: #f1f5f9;
        }

        /* Custom Light Backgrounds in Dark Mode */
        [data-theme="dark"] .bg-success-light { background-color: rgba(34, 197, 94, 0.15) !important; color: #4ade80 !important; }
        [data-theme="dark"] .bg-danger-light { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
        [data-theme="dark"] .bg-primary-light { background-color: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
        [data-theme="dark"] .bg-warning-light { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
        [data-theme="dark"] .bg-info-light { background-color: rgba(6, 182, 212, 0.15) !important; color: #22d3ee !important; }

        /* Select2 Dark Mode */
        [data-theme="dark"] .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        [data-theme="dark"] .select2-results__option {
            color: #f1f5f9 !important;
        }
        [data-theme="dark"] .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-blue) !important;
            color: white !important;
        }
        [data-theme="dark"] .select2-search__field {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        [data-theme="dark"] .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: #334155 !important;
        }
        [data-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f1f5f9 !important;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--content-bg);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #94a3b8;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        [data-theme="dark"] .bg-light {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .card-header {
            background-color: transparent !important;
        }
        
        [data-theme="dark"] thead.bg-light th {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: var(--text-muted) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .back-btn-minimal:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        [data-theme="dark"] .btn-white {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: var(--border-color) !important;
            color: var(--text-dark) !important;
        }

        [data-theme="dark"] .text-bg-light {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: var(--text-dark) !important;
            border-color: var(--border-color) !important;
        }

        .nav-links {
            padding: 0 10px;
            flex: 1;
        }

        .menu-label {
            padding: 24px 20px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #fbbf24;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.08);
            transform: translateX(4px);
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0) 100%);
            color: #fbbf24;
            border-left: 4px solid #f59e0b;
            font-weight: 600;
        }

        .nav-link.active i { color: #fbbf24; }

        /* Main Content Styles */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            background-color: var(--content-bg);
            position: relative;
        }

        /* Responsive Sidebar Logic */
        #sidebar-toggle { display: none; }
        @media (max-width: 768px) {
            #sidebar-toggle { display: block !important; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .sidebar-overlay.show { display: block; }
        }

        .top-header {
            height: 64px;
            background-color: var(--header-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid var(--border-color);
            width: 100%;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        @media (max-width: 576px) {
            .top-header { padding: 0 12px; }
            .top-header .gap-3, .top-header .gap-15 { gap: 8px !important; }
        }

        .content-area {
            padding: 24px;
            flex: 1;
        }

        /* Header Components */
        .search-container {
            position: relative;
            max-width: 400px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 35px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 14px;
            background: var(--content-bg);
            color: var(--text-dark);
        }

        /* Dropdowns */
        .dropdown { position: static; }
        .dropdown-menu {
            position: fixed;
            top: 64px;
            right: 16px;
            width: 240px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            margin-top: 4px;
            display: none;
            z-index: 9999;
            padding: 8px 0;
        }
        .dropdown-menu.show { display: block; }
        #notif-menu {
            width: 300px;
            max-height: 420px;
            overflow-y: auto;
            right: 60px;
        }
        .dropdown-item {
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        .dropdown-item:hover { background: rgba(0,0,0,0.05); }

        /* Mobile Adjustments */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 10px 0 30px rgba(0,0,0,0.2); }
            .main-wrapper { margin-left: 0; }
            .search-container { display: none; }
            .sidebar-toggle { display: block !important; }
        }

        @media (min-width: 1025px) {
            .sidebar-toggle { display: none !important; }
        }

        /* Visibility Utilities */
        @media (max-width: 1024px) {
            .hide-mobile { display: none !important; }
            .show-mobile { display: block !important; }
        }
        @media (min-width: 1025px) {
            .hide-desktop { display: none !important; }
            .show-desktop { display: block !important; }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 950;
        }
        .sidebar-overlay.show { display: block; }

        /* Stats Utility */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
        }
        .stat-label { font-size: 13px; color: var(--text-muted); font-weight: 500; }
        .stat-value { font-size: 24px; font-weight: 700; display: block; margin-top: 4px; }

        /* Page Layout & Header */
        .content-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 28px; gap: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        @media (max-width: 768px) {
            .content-header { flex-direction: column; align-items: stretch; gap: 15px; border-bottom: none; padding-bottom: 0; }
            .content-header .btn { width: 100%; justify-content: center; order: 2; }
            .content-header > div { order: 1; margin-bottom: 5px; }
        }
        .page-title { font-size: 26px; font-weight: 800; color: var(--text-dark); margin: 0; letter-spacing: -0.5px; }
        .page-subtitle { font-size: 14px; color: var(--text-muted); margin: 2px 0 0 0; }

        .breadcrumb-item { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--primary-blue); font-weight: 700; margin-bottom: 4px; display: block; }

        .btn {
            padding: 10px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: all 0.2s; border: none; display: inline-flex; align-items: center; gap: 8px;
            justify-content: center;
        }
        .btn-primary { background: #22c55e; color: white; }
        .btn-primary:hover { background: #16a34a; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3); }
        
        .btn-secondary { 
            background: transparent; border: 1px solid #e2e8f0; color: #64748b; 
        }
        .btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }
        
        .back-btn-minimal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            transition: all 0.2s;
            text-decoration: none !important;
        }
        .back-btn-minimal:hover { background: rgba(0,0,0,0.1); color: var(--primary-blue) !important; transform: translateX(-2px); }

        .btn-success-light {
            background: rgba(34, 197, 94, 0.1); color: #16a34a; border: none;
            padding: 6px 12px; font-size: 12px; border-radius: 6px;
        }

        /* Custom Table Styles */
        .custom-table {
            width: 100%; border-collapse: separate; border-spacing: 0;
        }
        .custom-table th {
            background: rgba(241, 245, 249, 0.8); padding: 12px 16px;
            font-size: 12px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px; color: #475569; text-align: left;
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }
        .custom-table td {
            padding: 16px; border-bottom: 1px solid var(--border-color);
            font-size: 14px; color: var(--text-dark);
        }
        @media (max-width: 576px) {
            .custom-table th, .custom-table td { padding: 12px 10px; font-size: 13px; }
        }
        .custom-table tr:last-child td { border-bottom: none; }
        .custom-table tr:hover { background: rgba(59, 130, 246, 0.02); }
        .font-bold { font-weight: 700; }
        .text-muted { color: #64748b; font-size: 13px; }

        .data-card {
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 24px;
        }
        @media (max-width: 576px) {
            .data-card { padding: 16px; }
            .h3 { font-size: 1.25rem; }
            .btn { padding: 8px 16px; font-size: 13px; }
        }
        .card-title { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; }

        /* Utility & Grid Classes */
        .grid-2 { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 20px; 
        }
        @media (min-width: 768px) {
            .grid-2 { grid-template-columns: 1fr 1fr; } /* 2 columns for tablet/desktop */
        }
        .mt-3 { margin-top: 1rem; }
        .mb-3 { margin-bottom: 1rem; }
        .d-flex { display: flex; }
        .flex-column { flex-direction: column; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .me-1 { margin-right: 0.25rem; }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            display: none; justify-content: center; align-items: flex-start;
            padding: 20px 10px;
            z-index: 2000;
            overflow-y: auto;
        }
        .modal-content {
            background: var(--card-bg);
            border-radius: 16px;
            width: 96%;
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        @media (min-width: 768px) {
            .modal-overlay { align-items: center; padding: 20px; }
            .modal-content { width: 90%; }
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-body { 
            padding: 24px; 
            overflow-y: auto;
            max-height: calc(95vh - 140px); /* Adjust for header and footer */
        }
        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-color);
            display: flex; justify-content: flex-end; gap: 12px;
        }
        .close-modal { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted); }

        /* Action Buttons */
        .action-btn-group { display: flex; gap: 8px; }
        .btn-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border-color);
            background: transparent; cursor: pointer; transition: all 0.2s;
        }
        .btn-icon:hover { background: rgba(59, 130, 246, 0.1); border-color: #3b82f6; color: #3b82f6; }
        .btn-icon.btn-outline-danger:hover { background: rgba(239, 68, 68, 0.1); border-color: #ef4444; color: #ef4444; }

        /* Form Select */
        .form-select {
            width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0;
            border-radius: 10px; background: white; color: #1e293b;
            font-size: 14px; transition: all 0.2s; box-sizing: border-box;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            cursor: pointer;
        }
        .form-select:focus { 
            outline: none; 
            border-color: #3b82f6; 
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08); 
        }
        /* Badges */
        .badge {
            padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;
            display: inline-block;
        }
        .bg-primary { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .bg-info { background: rgba(6, 182, 212, 0.1); color: #0891b2; }
        .bg-secondary { background: rgba(100, 116, 139, 0.1); color: #64748b; }
        .bg-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; }

        .card-header {
            display: flex; justify-content: flex-start; align-items: center;
            margin-bottom: 24px; gap: 20px;
        }
        .search-box {
            position: relative; width: 100%; max-width: 500px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            border-radius: 8px;
        }
        .search-box i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }
        .search-box input {
            width: 100%; padding: 12px 16px 12px 44px; border: 1px solid var(--border-color);
            border-radius: 8px; background: var(--card-bg); color: var(--text-dark);
            font-size: 14px; transition: all 0.2s;
        }
        .search-box input:focus {
            outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .filter-box { width: 220px; } /* Slightly wider filter box */

        /* Form Controls */
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #475569; margin-bottom: 8px;
        }
        .form-control {
            width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0;
            border-radius: 10px; background: white; color: #1e293b;
            font-size: 14px; transition: all 0.2s; box-sizing: border-box;
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control:focus {
            outline: none; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
        }
        .text-danger { color: #ef4444; }
        textarea.form-control { resize: vertical; }

        /* Select2 Premium Overrides */
        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            height: 46px !important;
            padding: 8px 12px !important;
            background-color: white !important;
            transition: all 0.2s !important;
        }
        .select2-container--default .select2-selection--single:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 10px !important;
        }
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
            overflow: hidden !important;
            margin-top: 5px !important;
        }
        .select2-search--dropdown .select2-search__field {
            padding: 10px 14px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            outline: none !important;
        }
        .select2-results__option {
            padding: 10px 16px !important;
            font-size: 14px !important;
            color: #475569 !important;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }
        /* Animations */
        @keyframes pulsate-red {
            0% { background-color: rgba(239, 68, 68, 1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { background-color: rgba(239, 68, 68, 0.8); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { background-color: rgba(239, 68, 68, 1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .pulsate-red { animation: pulsate-red 2s infinite; }
        
        .extra-small { font-size: 11px; }
        .btn-primary-light { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .btn-danger-light { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        
        .stat-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
        }
    </script>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('assets/images/logo-white.svg') }}" alt="Logo" style="height: 50px; width: auto;">
        </div>

        <div class="nav-links">
            <div class="menu-label">Analytics Overview</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>

            @if(auth()->user()->hasPermission('manage_masters') || auth()->user()->hasRole('super_admin'))
                <div class="menu-label">Configuration</div>
                <a href="{{ route('master.categories') }}" class="nav-link {{ request()->routeIs('master.categories') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Category Master
                </a>
                <a href="{{ route('master.units') }}" class="nav-link {{ request()->routeIs('master.units') ? 'active' : '' }}">
                    <i class="fas fa-ruler-combined"></i> Unit Master
                </a>
                <a href="{{ route('master.products') }}" class="nav-link {{ request()->routeIs('master.products') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Product Master
                </a>
            @endif

            <div class="menu-label">Inventory & Ops</div>
            @if(auth()->user()->hasPermission('manage_inventory') || auth()->user()->hasRole('super_admin'))
                <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i> Stock Control
                </a>
                <a href="{{ route('inventory.audit.index') }}" class="nav-link {{ request()->routeIs('inventory.audit.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i> Stock Audit
                </a>
            @endif
            @if(auth()->user()->hasPermission('manage_orders') || auth()->user()->hasRole('super_admin'))
                <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}"><i class="fas fa-file-invoice"></i> Sales Orders</a>
            @endif
            @if(auth()->user()->hasPermission('manage_dealers') || auth()->user()->hasRole('super_admin'))
                <a href="{{ route('dealers.index') }}" class="nav-link {{ request()->routeIs('dealers.*') ? 'active' : '' }}"><i class="fas fa-users-gear"></i> Dealers</a>
            @endif

            @if(auth()->user()->hasRole('super_admin'))
                <div class="menu-label">System Control</div>
                <a href="{{ route('rbac.users') }}" class="nav-link {{ request()->routeIs('rbac.users') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i> User Access
                </a>
                <a href="{{ route('rbac.roles') }}" class="nav-link {{ request()->routeIs('rbac.roles') ? 'active' : '' }}">
                    <i class="fas fa-user-lock"></i> Security Roles
                </a>
            @endif
        </div>

        <!-- Admin Footer -->
        <div style="margin-top: auto; padding: 20px; border-top: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.2);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                <div style="width: 35px; height: 35px; border-radius: 8px; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div style="overflow: hidden;">
                    <div style="font-size: 13px; font-weight: 600; color: #f1f5f9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 11px; color: #64748b; text-transform: capitalize;">{{ Auth::user()->roles->first()->name ?? 'Admin' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05); color: #ef4444; font-size: 12px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="top-header">
            <div style="display: flex; align-items: center; flex: 1;">
                <button id="sidebar-toggle" class="sidebar-toggle show-mobile" style="background: none; border: none; font-size: 20px; margin-right: 15px; cursor: pointer; color: var(--text-muted);">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-container show-desktop">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px;"></i>
                    <input type="text" class="search-input" placeholder="Search analytics, users...">
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 15px;">
                <div id="theme-toggle" style="cursor: pointer; padding: 8px; border-radius: 8px; background: var(--content-bg); border: 1px solid var(--border-color); color: var(--text-muted); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-moon"></i>
                </div>
                
                <div class="dropdown">
                    <div id="notif-toggle" style="position: relative; cursor: pointer;">
                        <i class="fas fa-bell" style="color: var(--text-muted); font-size: 18px;"></i>
                        @if(isset($globalLowStockCount) && $globalLowStockCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 3px 6px;">
                                {{ $globalLowStockCount }}
                            </span>
                        @endif
                    </div>
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0" id="notif-menu" style="width: 300px; max-height: 400px; overflow-y: auto;">
                        <div class="px-3 py-2 fw-bold border-bottom d-flex justify-content-between align-items-center">
                            <span>Notifications</span>
                            @if(isset($globalLowStockCount) && $globalLowStockCount > 0)
                                <span class="badge bg-danger-light text-danger extra-small">{{ $globalLowStockCount }} Critical</span>
                            @endif
                        </div>
                        
                        @if(isset($globalLowStockItems) && $globalLowStockItems->count() > 0)
                            @foreach($globalLowStockItems as $item)
                                <a href="{{ route('inventory.index') }}" class="dropdown-item py-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-danger-light text-danger rounded-circle p-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-exclamation-triangle small"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <div class="fw-bold small text-dark">{{ $item->name }}</div>
                                            <div class="extra-small text-muted">Stock: {{ $item->stock_quantity }} (Low)</div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="p-4 text-center text-muted small">
                                <i class="fas fa-check-circle text-success fs-3 mb-2 d-block opacity-25"></i>
                                No new notifications
                            </div>
                        @endif
                        
                        <a href="{{ route('dashboard') }}" class="dropdown-item text-center extra-small fw-bold text-primary py-2">View Dashboard</a>
                    </div>
                </div>

                <div class="dropdown" style="padding-left: 15px; border-left: 1px solid var(--border-color);">
                    <div id="profile-toggle" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="show-desktop">
                            <span style="font-size: 14px; font-weight: 600;">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                    <div class="dropdown-menu" id="profile-menu">
                        <a href="{{ route('profile') }}" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="dropdown-item" style="color:#ef4444;"><i class="fas fa-power-off"></i> Logout</button></form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            @yield('content')
        </main>
        
        <footer class="text-center py-4 text-muted small mt-auto" style="border-top: 1px solid var(--border-color);">
            <div class="container-fluid">
                Developed by <span class="fw-bold" style="color: var(--primary-blue);">OMESH DEWANGAN</span>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar toggle
            $('#sidebar-toggle, #sidebar-overlay').on('click', function() {
                $('#sidebar').toggleClass('show');
                $('#sidebar-overlay').toggleClass('show');
            });

            // Dropdowns
            $('#notif-toggle').on('click', function(e) { e.stopPropagation(); $('#notif-menu').toggleClass('show'); $('#profile-menu').removeClass('show'); });
            $('#profile-toggle').on('click', function(e) { e.stopPropagation(); $('#profile-menu').toggleClass('show'); $('#notif-menu').removeClass('show'); });
            // Stop clicks INSIDE dropdown menus from closing them
            $('.dropdown-menu').on('click', function(e) { e.stopPropagation(); });
            $(document).on('click', function() { $('.dropdown-menu').removeClass('show'); });

            // Theme toggle
            if (localStorage.getItem('theme') === 'dark') {
                $('#html-tag, body').attr('data-theme', 'dark');
                $('#theme-toggle i').removeClass('fa-moon').addClass('fa-sun');
            }

            $('#theme-toggle').on('click', function() {
                const html = $('#html-tag');
                const isDark = html.attr('data-theme') === 'dark';
                const newTheme = isDark ? 'light' : 'dark';
                
                html.attr('data-theme', newTheme);
                $('body').attr('data-theme', newTheme); // Keep it on body too for some CSS selectors
                localStorage.setItem('theme', newTheme);
                
                $(this).find('i').toggleClass('fa-moon fa-sun');
            });

            // Select2
            if ($.fn.select2) { $('.select2').select2({ width: '100%' }); }

            // Disable autocomplete globally
            $('form, input, select, textarea').attr('autocomplete', 'off');

            // Global Live Search Functionality
            $('.live-search-input').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                var tableId = $(this).data('table');
                var $rows = $('#' + tableId + ' tbody tr');
                
                $rows.each(function() {
                    // Check if this row is already hidden by another filter (like the category filter in stock audit)
                    // Wait, if we use toggle(), it might override other filters.
                    // Instead, we just check text and add/remove a class or hide/show.
                    var rowText = $(this).text().toLowerCase();
                    if (rowText.indexOf(value) > -1) {
                        $(this).removeClass('d-none-search');
                    } else {
                        $(this).addClass('d-none-search');
                    }
                });
            });
        });
        
        // CSS for the hidden class
        $("<style type='text/css'> .d-none-search { display: none !important; } </style>").appendTo("head");
    </script>
    @yield('scripts')
</body>
</html>
