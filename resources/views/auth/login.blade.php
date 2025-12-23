@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex">
    <!-- Left Side - Branding (2/3 width) -->
    <div class="hidden lg:flex lg:w-2/3 bg-teal-50 dark:bg-teal-900/20 flex-col items-center justify-center px-12 py-12 border-r border-gray-200 dark:border-gray-700">
        <div class="text-center space-y-6 max-w-2xl">
            <!-- Main Title -->
            <div>
                <h1 class="text-5xl font-bold mb-3 text-gray-900 dark:text-gray-100" style="font-family: 'Inter', 'Segoe UI', sans-serif; letter-spacing: -0.02em;">
                    Work-Based Learning (WBL)
                </h1>
                <p class="text-xl text-gray-700 dark:text-gray-300 font-medium" style="font-family: 'Inter', sans-serif;">
                    Faculty of Mechanical and Automotive Engineering Technology
                </p>
            </div>

            <!-- University Logo -->
            <div class="flex justify-center my-10">
                <img 
                    src="{{ asset('images/logos/UMPSA_logo.png') }}" 
                    alt="UMPSA Logo"
                    class="w-80 h-auto object-contain"
                >
            </div>

        </div>
    </div>

    <!-- Right Side - Login Form (1/3 width) -->
    <div class="w-full lg:w-1/3 flex items-center justify-center bg-white dark:bg-gray-900 px-8 py-12">
        <div class="w-full max-w-sm space-y-6">
            <!-- Welcome Message -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2" style="font-family: 'Inter', 'Segoe UI', sans-serif;">
                    Welcome to WBL Management System!
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm" style="font-family: 'Inter', sans-serif;">
                    Please sign-in to your account and start the adventure
                </p>
            </div>

            <!-- Success Messages -->
            @if(session('status'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
                    <p class="text-sm">{{ session('status') }}</p>
                </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm space-y-2">
                        @foreach($errors->all() as $error)
                            <li>
                                @if(str_contains(strtolower($error), 'verify your email') || str_contains(strtolower($error), 'verification'))
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-start gap-2">
                                            <span class="inline-block">{{ $error }}</span>
                                        </div>
                                        @if(old('email'))
                                            <form method="POST" action="{{ route('verification.resend') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="email" value="{{ old('email') }}">
                                                <button type="submit" class="text-sm font-semibold underline hover:text-red-900 dark:hover:text-red-300 transition-colors focus:outline-none">
                                                    Click here to resend verification email
                                                </button>
                                            </form>
                                        @else
                                            <p class="text-xs text-red-600 dark:text-red-400">
                                                Please enter your email address above and try logging in again to resend the verification email.
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    {{ $error }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Login Form -->
            <form class="space-y-5" action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" style="font-family: 'Inter', sans-serif;">
                        Email
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal dark:bg-gray-800 dark:text-gray-100 transition-colors text-sm"
                        placeholder="Enter your email"
                        style="font-family: 'Inter', sans-serif;"
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" style="font-family: 'Inter', sans-serif;">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal dark:bg-gray-800 dark:text-gray-100 transition-colors text-sm"
                            placeholder="Enter your password"
                            style="font-family: 'Inter', sans-serif;"
                        >
                        <button 
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
                            aria-label="Toggle password visibility"
                        >
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-umpsa-teal focus:ring-umpsa-teal border-gray-300 dark:border-gray-600 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300" style="font-family: 'Inter', sans-serif;">
                            Remember me
                        </label>
                    </div>
                    <div>
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-umpsa-teal hover:text-umpsa-royal-blue transition-colors" style="font-family: 'Inter', sans-serif;">
                            Forgot Password?
                        </a>
                    </div>
                </div>

                <!-- Login Button -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-umpsa-teal hover:bg-umpsa-royal-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-umpsa-teal transition-colors"
                        style="font-family: 'Inter', sans-serif;"
                    >
                        Login
                    </button>
                </div>

                <!-- Register Link -->
                <div class="text-center pt-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400" style="font-family: 'Inter', sans-serif;">
                        New on our platform? 
                        <a href="{{ route('register') }}" class="font-medium text-umpsa-teal hover:text-umpsa-royal-blue transition-colors">
                            Create an account
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    const eyeOffIcon = document.getElementById('eye-off-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeOffIcon.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeOffIcon.classList.add('hidden');
    }
}
</script>
@endsection
