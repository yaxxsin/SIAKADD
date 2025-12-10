<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Dark Mode Flash Prevention -->
        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --siakad-dark: #1B3C53;
                --siakad-primary: #234C6A;
                --siakad-secondary: #456882;
                --siakad-light: #E3E3E3;
                --bg-body: #FAFBFC;
                --bg-card: #FFFFFF;
                --bg-sidebar: #FFFFFF;
                --text-primary: #1B3C53;
                --text-secondary: #456882;
                --border-color: #E3E3E3;
            }
            
            .dark {
                --bg-body: #0F172A;
                --bg-card: #1E293B;
                --bg-sidebar: #1E293B;
                --text-primary: #F1F5F9;
                --text-secondary: #94A3B8;
                --border-color: #334155;
            }
            
            body { 
                font-family: 'Inter', system-ui, sans-serif;
                -webkit-font-smoothing: antialiased;
                background-color: var(--bg-body);
                color: var(--text-primary);
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            /* Sidebar Links */
            .sidebar-link {
                transition: all 0.15s ease;
                position: relative;
            }
            .sidebar-link:hover {
                background-color: rgba(35, 76, 106, 0.08);
            }
            .sidebar-link.active {
                background-color: var(--siakad-primary);
                color: white;
            }
            .sidebar-link.active:hover {
                background-color: var(--siakad-dark);
            }

            /* Cards */
            .card-saas {
                background: var(--bg-card);
                border: 1px solid var(--border-color);
                border-radius: 12px;
                transition: all 0.2s ease;
            }
            .card-saas:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }
            .dark .card-saas:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            }

            /* Dark Mode Text Overrides */
            .dark .text-siakad-dark {
                color: var(--text-primary) !important;
            }
            .dark .text-siakad-secondary {
                color: var(--text-secondary) !important;
            }
            .dark .text-siakad-primary {
                color: #60A5FA !important;
            }
            .dark .border-siakad-light {
                border-color: var(--border-color) !important;
            }
            .dark .bg-siakad-light {
                background-color: #334155 !important;
            }

            /* Tables */
            .table-saas tbody tr {
                transition: background-color 0.15s ease;
            }
            .table-saas tbody tr:hover {
                background-color: rgba(35, 76, 106, 0.04);
            }

            /* Buttons */
            .btn-primary-saas {
                background-color: var(--siakad-primary);
                color: white;
                transition: all 0.15s ease;
            }
            .btn-primary-saas:hover {
                background-color: var(--siakad-dark);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(27, 60, 83, 0.3);
            }

            .btn-ghost-saas {
                background-color: transparent;
                color: var(--siakad-primary);
                border: 1px solid var(--siakad-light);
                transition: all 0.15s ease;
            }
            .btn-ghost-saas:hover {
                background-color: rgba(35, 76, 106, 0.08);
                border-color: var(--siakad-secondary);
            }

            /* Inputs */
            .input-saas {
                border: 1px solid var(--siakad-light);
                border-radius: 8px;
                transition: all 0.15s ease;
            }
            .input-saas:focus {
                border-color: var(--siakad-primary);
                box-shadow: 0 0 0 3px rgba(35, 76, 106, 0.1);
                outline: none;
            }

            /* Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: var(--siakad-light);
                border-radius: 3px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: var(--siakad-secondary);
            }

            /* Animations */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-4px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: fadeIn 0.2s ease-out;
            }

            /* Sidebar Collapse */
            .sidebar-collapsed .sidebar-text {
                display: none;
            }
            .sidebar-collapsed .sidebar-logo-text {
                display: none;
            }
            .sidebar-collapsed .sidebar-section-title {
                display: none;
            }
            .sidebar-collapsed .sidebar-user-info {
                display: none;
            }
            .sidebar-collapsed .sidebar-link {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            .sidebar-collapsed .sidebar-link svg {
                margin: 0;
            }
            .sidebar-collapsed .user-section {
                justify-content: center;
            }
            .sidebar-toggle-icon {
                transition: transform 0.2s ease;
            }
            .sidebar-collapsed .sidebar-toggle-icon {
                transform: rotate(180deg);
            }
            .sidebar-collapsed .logo-section {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            .sidebar-collapsed .toggle-btn {
                display: none;
            }

            /* Prevent transition on page load */
            [x-cloak] { display: none !important; }
            .no-transition * {
                transition: none !important;
            }
        </style>
        <script>
            function toggleDarkMode() {
                const isDark = document.documentElement.classList.toggle('dark');
                document.body.classList.toggle('dark', isDark);
                localStorage.setItem('darkMode', isDark);
                // Toggle icons
                const moonIcon = document.getElementById('moonIcon');
                const sunIcon = document.getElementById('sunIcon');
                if (moonIcon && sunIcon) {
                    moonIcon.classList.toggle('hidden', isDark);
                    sunIcon.classList.toggle('hidden', !isDark);
                }
            }
        </script>
    </head>
    <body class="antialiased no-transition" 
          x-data="{ sidebarOpen: true }" 
          :class="{ 'sidebar-collapsed': !sidebarOpen }" 
          x-init="setTimeout(() => document.body.classList.remove('no-transition'), 100)">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-64 fixed h-full z-30 transition-all duration-300" :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }" style="background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color);">
                <!-- Logo -->
                <div class="h-16 flex items-center justify-between px-4 logo-section" style="border-bottom: 1px solid var(--border-color);">

                    <div class="flex items-center gap-3 overflow-hidden">
                        <button @click="if(!sidebarOpen) sidebarOpen = true" :class="!sidebarOpen ? 'cursor-pointer hover:bg-siakad-primary/80' : 'cursor-default'" class="w-9 h-9 rounded-lg bg-siakad-primary flex items-center justify-center flex-shrink-0 transition">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </button>
                        <div class="sidebar-logo-text">
                            <h1 class="text-base font-semibold" style="color: var(--text-primary);">{{ config('app.name') }}</h1>
                            <p class="text-[11px] tracking-wide" style="color: var(--text-secondary);">Academic Management</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg transition flex-shrink-0 toggle-btn" style="color: var(--text-secondary);">
                        <svg class="w-4 h-4 sidebar-toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="p-3 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 180px);">

                    @if(Auth::user()->role === 'admin')
                    <!-- Admin Panel -->
                    <a href="{{ url('admin/dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span class="sidebar-text">Admin Panel</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-semibold text-siakad-secondary/60 uppercase tracking-widest sidebar-section-title">Master Data</p>
                    </div>
                    <a href="{{ url('admin/fakultas') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/fakultas*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="sidebar-text">Fakultas</span>
                    </a>
                    <a href="{{ url('admin/prodi') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/prodi*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="sidebar-text">Program Studi</span>
                    </a>
                    <a href="{{ url('admin/mata-kuliah') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/mata-kuliah*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="sidebar-text">Mata Kuliah</span>
                    </a>
                    <a href="{{ url('admin/kelas') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/kelas*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                        <span class="sidebar-text">Kelas</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-semibold text-siakad-secondary/60 uppercase tracking-widest sidebar-section-title">Manajemen</p>
                    </div>
                    <a href="{{ url('admin/mahasiswa') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/mahasiswa*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="sidebar-text">Mahasiswa</span>
                    </a>
                    <a href="{{ url('admin/dosen') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/dosen*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="sidebar-text">Dosen</span>
                    </a>
                    <a href="{{ url('admin/ruangan') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/ruangan*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="sidebar-text">Ruangan</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-semibold text-siakad-secondary/60 uppercase tracking-widest sidebar-section-title">Akademik</p>
                    </div>
                    <a href="{{ url('admin/krs-approval') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/krs-approval*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="sidebar-text">Approval KRS</span>
                        @php $pendingCount = \App\Models\Krs::where('status', 'pending')->count(); @endphp
                        @if($pendingCount > 0)
                        <span class="ml-auto px-2 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a href="{{ url('admin/skripsi') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/skripsi*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="sidebar-text">Skripsi / TA</span>
                    </a>
                    <a href="{{ url('admin/kp') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/kp*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="sidebar-text">Kerja Praktek</span>
                    </a>
                    <a href="{{ url('admin/kehadiran-dosen') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('admin/kehadiran-dosen*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="sidebar-text">Kehadiran Dosen</span>
                    </a>
                    @endif

                    @if(Auth::user()->role === 'mahasiswa')
                    <a href="{{ url('mahasiswa/dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('mahasiswa/dashboard') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span class="sidebar-text">Portal Akademik</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-semibold text-siakad-secondary/60 uppercase tracking-widest sidebar-section-title">Akademik</p>
                    </div>
                    <a href="{{ url('mahasiswa/krs') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('mahasiswa/krs*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span class="sidebar-text">Pengisian KRS</span>
                        @php $krsStatus = \App\Models\Krs::where('mahasiswa_id', Auth::user()->mahasiswa?->id)->latest()->first(); @endphp
                        @if(!$krsStatus || $krsStatus->status === 'draft')
                        <span class="ml-auto px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-700 rounded-full">Baru</span>
                        @endif
                    </a>
                    <a href="{{ url('mahasiswa/transkrip') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('mahasiswa/transkrip*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span class="sidebar-text">Riwayat Kuliah</span>
                    </a>
                    <a href="{{ url('mahasiswa/presensi') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('mahasiswa/presensi*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <span class="sidebar-text">Presensi</span>
                    </a>
                    <a href="{{ url('mahasiswa/jadwal') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('mahasiswa/jadwal*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="sidebar-text">Jadwal Kuliah</span>
                    </a>
                    <a href="{{ url('mahasiswa/khs') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('mahasiswa/khs*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="sidebar-text">KHS / Nilai</span>
                    </a>
                    <a href="{{ url('mahasiswa/skripsi') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('mahasiswa/skripsi*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="sidebar-text">Skripsi / TA</span>
                    </a>
                    <a href="{{ url('mahasiswa/kp') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('mahasiswa/kp*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="sidebar-text">Kerja Praktek</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-semibold text-siakad-secondary/60 uppercase tracking-widest sidebar-section-title">Data</p>
                    </div>
                    <a href="{{ route('mahasiswa.biodata.index') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="sidebar-text">Biodata</span>
                    </a>
                    @endif

                    @if(Auth::user()->role === 'dosen')
                    <a href="{{ url('dosen/dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('dosen/dashboard') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span class="sidebar-text">Dashboard</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-semibold text-siakad-secondary/60 uppercase tracking-widest sidebar-section-title">Bimbingan</p>
                    </div>
                    <a href="{{ url('dosen/bimbingan') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('dosen/bimbingan') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="sidebar-text">Mahasiswa Bimbingan</span>
                    </a>
                    <a href="{{ url('dosen/bimbingan/krs-approval') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('dosen/bimbingan/krs*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="sidebar-text">Approval KRS</span>
                        @php 
                            $dosen = Auth::user()->dosen;
                            $pendingKrs = $dosen ? \App\Models\Krs::whereIn('mahasiswa_id', $dosen->mahasiswaBimbingan()->pluck('id'))->where('status', 'pending')->count() : 0;
                        @endphp
                        @if($pendingKrs > 0)
                        <span class="ml-auto px-2 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded-full">{{ $pendingKrs }}</span>
                        @endif
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-semibold text-siakad-secondary/60 uppercase tracking-widest sidebar-section-title">Pengajaran</p>
                    </div>
                    <a href="{{ url('dosen/penilaian') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('dosen/penilaian*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span class="sidebar-text">Input Nilai</span>
                    </a>
                    <a href="{{ url('dosen/presensi') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('dosen/presensi*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <span class="sidebar-text">Presensi</span>
                    </a>
                    <a href="{{ url('dosen/skripsi') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('dosen/skripsi*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="sidebar-text">Bimbingan Skripsi</span>
                    </a>
                    <a href="{{ url('dosen/kp') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('dosen/kp*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="sidebar-text">Bimbingan KP</span>
                    </a>
                    <a href="{{ url('dosen/kehadiran') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-siakad-secondary text-sm font-medium {{ request()->is('dosen/kehadiran*') ? 'active' : '' }}">
                        <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="sidebar-text">Kehadiran Saya</span>
                    </a>
                    @endif
                </nav>

                <!-- User Info -->
                <div class="absolute bottom-0 left-0 right-0 p-3" style="border-top: 1px solid var(--border-color); background-color: var(--bg-sidebar);">
                    <div class="flex items-center gap-3 user-section">
                        <div class="w-9 h-9 rounded-lg bg-siakad-primary flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0 sidebar-user-info">
                            <p class="text-sm font-medium truncate" style="color: var(--text-primary);">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] capitalize" style="color: var(--text-secondary);">{{ Auth::user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="sidebar-user-info">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg transition-colors" style="color: var(--text-secondary);" title="Logout">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 ml-64 transition-all duration-300" :class="{ 'ml-64': sidebarOpen, 'ml-20': !sidebarOpen }">
                <!-- Top Header -->
                <header class="h-16 flex items-center justify-between px-8 sticky top-0 z-20" style="background-color: var(--bg-card); border-bottom: 1px solid var(--border-color);">
                    <div>
                        @isset($header)
                        <h1 class="text-lg font-semibold" style="color: var(--text-primary);">{{ $header }}</h1>
                        @endisset
                    </div>
                    <div class="flex items-center gap-4">
                        @if(Auth::user()->role === 'mahasiswa')
                        <!-- Dark Mode Toggle -->
                        <button id="darkModeToggle" onclick="toggleDarkMode()" class="p-2 rounded-lg transition-colors hover:bg-siakad-light/50" style="color: var(--text-secondary);" title="Toggle Dark Mode">
                            <svg id="moonIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            <svg id="sunIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </button>
                        <script>
                            // Sync icon state on load
                            if (localStorage.getItem('darkMode') === 'true') {
                                document.getElementById('moonIcon')?.classList.add('hidden');
                                document.getElementById('sunIcon')?.classList.remove('hidden');
                            }
                        </script>
                        @endif
                        
                        <!-- Notifications Bell -->
                        @php $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->unread()->count(); @endphp
                        <div x-data="{ notifOpen: false }" class="relative">
                            <button @click="notifOpen = !notifOpen" class="p-2 rounded-lg transition-colors hover:bg-siakad-light/50 relative" style="color: var(--text-secondary);" title="Notifikasi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 text-[10px] font-bold bg-red-500 text-white rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </button>
                            
                            <!-- Notification Dropdown -->
                            <div x-show="notifOpen" @click.away="notifOpen = false" x-transition class="absolute right-0 mt-2 w-80 rounded-xl shadow-xl z-50 overflow-hidden animate-fade-in" style="background-color: var(--bg-card); border: 1px solid var(--border-color);">
                                <div class="px-4 py-3 flex items-center justify-between" style="border-bottom: 1px solid var(--border-color);">
                                    <h3 class="font-semibold" style="color: var(--text-primary);">Notifikasi</h3>
                                    @if($unreadCount > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 hover:underline">Tandai semua dibaca</button>
                                    </form>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @php $notifications = \App\Models\Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->limit(10)->get(); @endphp
                                    @forelse($notifications as $notif)
                                    <a href="{{ route('notifications.index') }}" class="block px-4 py-3 hover:bg-siakad-light/30 transition {{ !$notif->is_read ? 'bg-indigo-50/50' : '' }}" style="border-bottom: 1px solid var(--border-color);">
                                        <div class="flex gap-3">
                                            <span class="text-lg">{{ $notif->icon }}</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium truncate {{ !$notif->is_read ? 'text-indigo-700' : '' }}" style="color: {{ $notif->is_read ? 'var(--text-primary)' : '' }};">{{ $notif->title }}</p>
                                                <p class="text-xs mt-0.5 truncate" style="color: var(--text-secondary);">{{ Str::limit($notif->message, 50) }}</p>
                                                <p class="text-[10px] mt-1" style="color: var(--text-secondary);">{{ $notif->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="px-4 py-8 text-center">
                                        <p class="text-sm" style="color: var(--text-secondary);">Tidak ada notifikasi</p>
                                    </div>
                                    @endforelse
                                </div>
                                @if($notifications->isNotEmpty())
                                <a href="{{ route('notifications.index') }}" class="block px-4 py-3 text-center text-sm text-indigo-600 hover:bg-siakad-light/30 transition">Lihat Semua</a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-sm font-medium" style="color: var(--text-primary);">{{ Auth::user()->name }}</p>
                            <p class="text-[11px]" style="color: var(--text-secondary);">{{ now()->format('l, d M Y') }}</p>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-8">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm font-medium animate-fade-in">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm font-medium animate-fade-in">
                        {{ session('error') }}
                    </div>
                    @endif

                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="px-8 py-4" style="border-top: 1px solid var(--border-color); background-color: var(--bg-card);">
                    <div class="flex items-center justify-between text-[11px]" style="color: var(--text-secondary);">
                        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        <p>v1.0.0</p>
                    </div>
                </footer>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
