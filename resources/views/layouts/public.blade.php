<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Student Interview Booking') }} - {{ $title ?? 'Register' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            200: '#fbcfe8',
                            300: '#f9a8d4',
                            400: '#f472b6',
                            500: '#ec2088',
                            600: '#d91b78',
                            700: '#b91566',
                            800: '#991254',
                            900: '#831046',
                        },
                        secondary: {
                            50: '#f5f4fa',
                            100: '#e9e8f2',
                            200: '#d3d1e5',
                            300: '#b5b2d0',
                            400: '#918db8',
                            500: '#292563',
                            600: '#252158',
                            700: '#211d4d',
                            800: '#1c1941',
                            900: '#171436',
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="{{ route('recruitment') }}" class="text-xl font-bold text-primary-600">
                            <img src="{{ asset('logo2.png') }}" alt="Student Interview Booking" class="h-10 w-auto">
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('recruitment') }}" class="text-gray-600 hover:text-primary-600">
                            <i class="fas fa-home mr-1"></i> Home
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <footer class="bg-white border-t mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Hult Prize SAMS. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
    
    @stack('scripts')
</body>
</html>