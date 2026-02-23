<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Material Control System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-900 to-blue-700 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-900 text-white p-8 text-center">
                <div class="mb-4">
                    <i class="fas fa-warehouse text-6xl"></i>
                </div>
                <h1 class="text-2xl font-bold">Material Control System</h1>
                <p class="text-blue-200 text-sm mt-2">Stamping Manufacturing</p>
            </div>

            <!-- Login Form -->
            <div class="p-8">
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="your@email.com">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="••••••••">
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>

                {{-- <!-- Demo Accounts Info -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-600 text-center mb-3 font-semibold">Demo Accounts:</p>
                    <div class="space-y-2 text-xs">
                        <div class="bg-purple-50 p-3 rounded">
                            <p class="font-semibold text-purple-800"><i class="fas fa-user-shield mr-1"></i> Admin</p>
                            <p class="text-gray-600">admin@materialcontrol.com / password</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded">
                            <p class="font-semibold text-blue-800"><i class="fas fa-user mr-1"></i> Staff</p>
                            <p class="text-gray-600">staff@materialcontrol.com / password</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded">
                            <p class="font-semibold text-green-800"><i class="fas fa-truck mr-1"></i> Supplier</p>
                            <p class="text-gray-600">supplier@steelindonesia.com / password</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="text-center mt-6 text-white text-sm">
            <p>&copy; 2026 Material Control System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
