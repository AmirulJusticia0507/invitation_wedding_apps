<!-- header.php -->
<?php
if (!function_exists('h')) {
    function h($str, $flags = ENT_QUOTES) {
        return htmlspecialchars((string)($str ?? ''), $flags, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Admin Panel</title>
    <!-- Favicon -->
    <link rel="icon" href="images/amirulprojectsolutions.ico" type="image/x-icon">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 (CSS for modal & datatable compatibility) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Lightbox2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #020617 !important;
            color: #f8fafc !important;
            font-family: 'Inter', sans-serif !important;
            overflow-x: hidden;
        }

        /* Layout wrapper */
        .content-wrapper {
            margin-left: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            background-color: #020617;
        }
        @media (min-width: 1024px) {
            .content-wrapper {
                margin-left: 16rem !important; /* 64 = 256px = 16rem */
            }
        }

        /* Dark Table Styling */
        .table {
            --bs-table-bg: #0f172a !important;
            --bs-table-striped-bg: #1e293b !important;
            --bs-table-hover-bg: #334155 !important;
            --bs-table-color: #f1f5f9 !important;
            --bs-table-border-color: #334155 !important;
            border-color: #334155 !important;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .table thead th {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #334155 !important;
            padding: 0.75rem 1rem;
        }
        .table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            font-size: 0.875rem;
            border-color: #334155 !important;
        }

        /* Dark Modal */
        .modal-content {
            background-color: #0f172a !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
            border-radius: 1rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7) !important;
        }
        .modal-header {
            border-bottom: 1px solid #1e293b !important;
            padding: 1.25rem 1.5rem;
        }
        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        .modal-footer {
            border-top: 1px solid #1e293b !important;
            padding: 1.25rem 1.5rem;
        }

        /* Dark Form Controls */
        .form-control, .form-select {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
            border-radius: 0.75rem !important;
            padding: 0.6rem 0.85rem !important;
            font-size: 0.875rem !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: #1e293b !important;
            border-color: #ec4899 !important;
            box-shadow: 0 0 0 2px rgba(236,72,153,0.3) !important;
            color: #ffffff !important;
        }
        .form-control::placeholder {
            color: #64748b !important;
        }
        .input-group-text {
            background-color: #334155 !important;
            border: 1px solid #334155 !important;
            color: #cbd5e1 !important;
            border-radius: 0.75rem 0 0 0.75rem !important;
        }
        .input-group .form-control {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        /* DataTables Dark */
        .dataTables_wrapper {
            color: #94a3b8 !important;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            color: #94a3b8 !important;
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            color: #fff !important;
            border-radius: 0.5rem !important;
            padding: 0.35rem 0.75rem !important;
            margin-left: 0.5rem;
        }
        .dataTables_wrapper .dataTables_length select {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            color: #fff !important;
            border-radius: 0.5rem !important;
            padding: 0.35rem 0.75rem !important;
        }
        .page-item.active .page-link {
            background-color: #ec4899 !important;
            border-color: #ec4899 !important;
            color: #fff !important;
        }
        .page-link {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }
        .page-link:hover {
            background-color: #334155 !important;
            color: #fff !important;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

<!-- Topbar for Mobile Sidebar Toggle & Page Info -->
<div class="lg:hidden sticky top-0 z-30 bg-slate-900/90 backdrop-blur border-b border-slate-800 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-slate-300">
            <i class="fa-solid fa-bars text-sm"></i>
        </button>
        <span class="font-bold text-white text-sm">Wedding Admin</span>
    </div>
    <span class="text-xs text-pink-400 bg-pink-500/10 px-2.5 py-1 rounded-full border border-pink-500/20">Panel</span>
</div>
