<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Student Interview Booking') }} - Admin - {{ $title ?? 'Dashboard' }}</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Sidebar transition */
        .sidebar {
            transition: width 0.3s ease, padding 0.3s ease;
        }
        .sidebar.collapsed {
            width: 5rem;
        }
        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .logout-text {
            display: none;
        }
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }
        .sidebar.collapsed .logo-text {
            display: none;
        }
        .sidebar.collapsed .logo-icon {
            margin-right: 0;
        }
        
        /* Content area transition */
        .content-area {
            transition: margin-left 0.3s ease;
        }
        .content-area.sidebar-collapsed {
            margin-left: 5rem;
        }
        @media (min-width: 1024px) {
            .content-area {
                margin-left: 16rem;
            }
        }
        
        /* Top bar transition */
        .top-bar {
            transition: left 0.3s ease, width 0.3s ease;
        }
        @media (min-width: 1024px) {
            .top-bar {
                left: 16rem;
                width: calc(100% - 16rem);
            }
            .top-bar.sidebar-collapsed {
                left: 5rem;
                width: calc(100% - 5rem);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen" id="app">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-primary-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0" aria-label="Sidebar">
            <div class="flex h-16 items-center justify-center border-b border-primary-800 px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center text-white">
                    <i class="fas fa-graduation-cap logo-icon mr-2 text-xl"></i>
                    <span class="logo-text sidebar-text text-xl font-bold">Admin Panel</span>
                </a>
            </div>
            
            <nav class="mt-6 px-4" aria-label="Main navigation">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }} nav-link flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                            <span class="sidebar-text">Dashboard</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.applications.index') }}" class="{{ request()->routeIs('admin.applications.*') ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }} nav-link flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-file-alt mr-3 w-5 text-center"></i>
                            <span class="sidebar-text">Applications</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.schedule.index') }}" class="{{ request()->routeIs('admin.schedule.*') ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }} nav-link flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-calendar-alt mr-3 w-5 text-center"></i>
                            <span class="sidebar-text">Schedule</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.tracks.index') }}" class="{{ request()->routeIs('admin.tracks.*') ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }} nav-link flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-tags mr-3 w-5 text-center"></i>
                            <span class="sidebar-text">Tracks</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.board-members.index') }}" class="{{ request()->routeIs('admin.board-members.*') ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }} nav-link flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-users mr-3 w-5 text-center"></i>
                            <span class="sidebar-text">Board Members</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.strengths.index') }}" class="{{ request()->routeIs('admin.strengths.*') ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }} nav-link flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-check-circle mr-3 w-5 text-center"></i>
                            <span class="sidebar-text">Strengths</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.weaknesses.index') }}" class="{{ request()->routeIs('admin.weaknesses.*') ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }} nav-link flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-times-circle mr-3 w-5 text-center"></i>
                            <span class="sidebar-text">Weaknesses</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'bg-primary-800 text-white' : 'text-primary-100 hover:bg-primary-800 hover:text-white' }} nav-link flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-chart-bar mr-3 w-5 text-center"></i>
                            <span class="sidebar-text">Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-primary-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-primary-100 hover:bg-primary-800 hover:text-white rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                        <span class="logout-text sidebar-text">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 content-area" id="contentArea">
            <!-- Top Bar -->
            <header class="sticky top-0 z-40 bg-white shadow-sm top-bar" id="topBar">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button id="sidebarToggle" class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100" aria-label="Toggle sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <button id="sidebarCollapseToggle" class="hidden lg:flex p-2 rounded-md text-gray-500 hover:bg-gray-100 ml-2" aria-label="Collapse sidebar">
                            <i class="fas fa-chevron-left" id="collapseIcon"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-900 ml-3">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-user mr-1"></i> {{ auth()->user()->name }}
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center justify-between" role="alert">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center justify-between" role="alert">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg" role="alert">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const contentArea = document.getElementById('contentArea');
            const topBar = document.getElementById('topBar');
            const collapseToggle = document.getElementById('sidebarCollapseToggle');
            const collapseIcon = document.getElementById('collapseIcon');
            const mobileToggle = document.getElementById('sidebarToggle');

            // Check localStorage for desktop sidebar state
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                contentArea.classList.add('sidebar-collapsed');
                topBar.classList.add('sidebar-collapsed');

                if (collapseIcon) {
                    collapseIcon.classList.replace(
                        'fa-chevron-left',
                        'fa-chevron-right'
                    );
                }
            }

            // ==========================================
            // DESKTOP COLLAPSE TOGGLE
            // ==========================================
            if (collapseToggle) {
                collapseToggle.addEventListener('click', function() {

                    // Only use desktop collapse behavior
                    if (window.innerWidth >= 1024) {

                        sidebar.classList.toggle('collapsed');
                        contentArea.classList.toggle('sidebar-collapsed');
                        topBar.classList.toggle('sidebar-collapsed');

                        const isNowCollapsed =
                            sidebar.classList.contains('collapsed');

                        localStorage.setItem(
                            'sidebarCollapsed',
                            isNowCollapsed
                        );

                        if (collapseIcon) {
                            if (isNowCollapsed) {
                                collapseIcon.classList.replace(
                                    'fa-chevron-left',
                                    'fa-chevron-right'
                                );
                            } else {
                                collapseIcon.classList.replace(
                                    'fa-chevron-right',
                                    'fa-chevron-left'
                                );
                            }
                        }
                    }
                });
            }

            // ==========================================
            // MOBILE TOGGLE
            // ==========================================
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {

                    sidebar.classList.toggle('translate-x-0');
                    sidebar.classList.toggle('-translate-x-full');
                });
            }

            // ==========================================
            // AUTOMATIC MOBILE SIDEBAR STATE
            // ==========================================
            function handleResponsiveSidebar() {

                if (window.innerWidth < 1024) {

                    // Automatically hide sidebar on phone/tablet
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');

                } else {

                    // Remove mobile classes when returning to desktop
                    sidebar.classList.remove('-translate-x-full');

                    // Restore desktop collapsed state
                    const isCollapsed =
                        localStorage.getItem('sidebarCollapsed') === 'true';

                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        contentArea.classList.add('sidebar-collapsed');
                        topBar.classList.add('sidebar-collapsed');

                        if (collapseIcon) {
                            collapseIcon.classList.remove('fa-chevron-left');
                            collapseIcon.classList.add('fa-chevron-right');
                        }
                    } else {
                        sidebar.classList.remove('collapsed');
                        contentArea.classList.remove('sidebar-collapsed');
                        topBar.classList.remove('sidebar-collapsed');

                        if (collapseIcon) {
                            collapseIcon.classList.remove('fa-chevron-right');
                            collapseIcon.classList.add('fa-chevron-left');
                        }
                    }
                }
            }

            // Run when page loads
            handleResponsiveSidebar();

            // Run when screen size changes
            window.addEventListener('resize', handleResponsiveSidebar);


            // ==========================================
            // CLOSE MOBILE SIDEBAR WHEN CLICKING OUTSIDE
            // ==========================================
            document.addEventListener('click', function(e) {

                if (window.innerWidth < 1024) {

                    if (
                        !sidebar.contains(e.target) &&
                        (!mobileToggle || !mobileToggle.contains(e.target))
                    ) {
                        sidebar.classList.remove('translate-x-0');
                        sidebar.classList.add('-translate-x-full');
                    }
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>