<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - Student Interview Booking</title>
    
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
                        }
                    }
                }
            }
        }
    </script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full px-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="text-center mb-8">
                <a href="{{ route('recruitment') }}" class="inline-flex items-center text-2xl font-bold text-primary-600">
                    <img src="{{ asset('logo2.png') }}" alt="Student Interview Booking" class="h-10 w-auto">
                </a>
                <h1 class="mt-4 text-xl font-semibold text-gray-900">Hult Prize SAMS Login</h1>
                <p class="text-gray-500 mt-1">Enter your credentials to access the admin panel</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                @if (session('error'))
                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                        placeholder="admin@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                        placeholder="Enter your password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Remember me</span>
                    </label>
                </div>
                
                <button type="submit" class="w-full py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>