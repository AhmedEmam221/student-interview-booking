<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Student Interview Booking') }} - High Board Panel - {{ $title ?? 'Dashboard' }}</title>
    
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
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        highboard: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-orange-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0" aria-label="Sidebar">
            <div class="flex h-16 items-center justify-center border-b border-orange-800">
                <a href="{{ route('highboard.dashboard') }}" class="text-xl font-bold text-white">
                    <i class="fas fa-crown mr-2"></i>
                    High Board
                </a>
            </div>
            
            <nav class="mt-6 px-4" aria-label="Main navigation">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('highboard.dashboard') }}" class="{{ request()->routeIs('highboard.dashboard') ? 'bg-orange-800 text-white' : 'text-orange-100 hover:bg-orange-800 hover:text-white' }} flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-tachometer-alt mr-3 w-5 text-center"></i>
                            Dashboard
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('highboard.applications.index') }}" class="{{ request()->routeIs('highboard.applications.*') ? 'bg-orange-800 text-white' : 'text-orange-100 hover:bg-orange-800 hover:text-white' }} flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-file-alt mr-3 w-5 text-center"></i>
                            Applications
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('highboard.reports.index') }}" class="{{ request()->routeIs('highboard.reports.*') ? 'bg-orange-800 text-white' : 'text-orange-100 hover:bg-orange-800 hover:text-white' }} flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-chart-bar mr-3 w-5 text-center"></i>
                            Reports
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('highboard.schedule.index') }}" class="{{ request()->routeIs('highboard.schedule.*') ? 'bg-orange-800 text-white' : 'text-orange-100 hover:bg-orange-800 hover:text-white' }} flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-calendar-alt mr-3 w-5 text-center"></i>
                            Schedule
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('highboard.tracks.index') }}" class="{{ request()->routeIs('highboard.tracks.*') ? 'bg-orange-800 text-white' : 'text-orange-100 hover:bg-orange-800 hover:text-white' }} flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-tags mr-3 w-5 text-center"></i>
                            Subtracks
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('highboard.board-members.index') }}" class="{{ request()->routeIs('highboard.board-members.*') ? 'bg-orange-800 text-white' : 'text-orange-100 hover:bg-orange-800 hover:text-white' }} flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors">
                            <i class="fas fa-users mr-3 w-5 text-center"></i>
                            Board Members
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-orange-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-orange-100 hover:bg-orange-800 hover:text-white rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64">
            <!-- Top Bar -->
            <header class="sticky top-0 z-40 bg-white shadow-sm">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-xl font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <i class="fas fa-crown text-orange-600"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <span class="px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-800 rounded">High Board</span>
                        </div>
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
    
    @stack('scripts')
</body>
</html>