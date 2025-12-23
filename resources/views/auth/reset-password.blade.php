@extends('layouts.auth')

@section('title', 'Reset Password')

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

    <!-- Right Side - Reset Password Form (1/3 width) -->
    <div class="w-full lg:w-1/3 flex items-center justify-center bg-white dark:bg-gray-900 px-8 py-12">
        <div class="w-full max-w-sm space-y-6">
            <!-- Welcome Message -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2" style="font-family: 'Inter', 'Segoe UI', sans-serif;">
                    Reset Password
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm" style="font-family: 'Inter', sans-serif;">
                    Please enter your new password below.
                </p>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Reset Password Form -->
            <form class="space-y-5" action="{{ route('password.store') }}" method="POST">
                @csrf

                <!-- Token (hidden) -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                        value="{{ old('email', $request->email) }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal dark:bg-gray-800 dark:text-gray-100 transition-colors text-sm"
                        placeholder="Enter your email"
                        style="font-family: 'Inter', sans-serif;"
                        readonly
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" style="font-family: 'Inter', sans-serif;">
                        New Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="new-password" 
                            required 
                            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal dark:bg-gray-800 dark:text-gray-100 transition-colors text-sm"
                            placeholder="Enter your new password"
                            style="font-family: 'Inter', sans-serif;"
                            autofocus
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

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" style="font-family: 'Inter', sans-serif;">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            autocomplete="new-password" 
                            required 
                            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal dark:bg-gray-800 dark:text-gray-100 transition-colors text-sm"
                            placeholder="Confirm your new password"
                            style="font-family: 'Inter', sans-serif;"
                        >
                        <button 
                            type="button"
                            onclick="togglePasswordConfirmation()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
                            aria-label="Toggle password visibility"
                        >
                            <svg id="eye-icon-confirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="eye-off-icon-confirm" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-umpsa-teal hover:bg-umpsa-royal-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-umpsa-teal transition-colors"
                        style="font-family: 'Inter', sans-serif;"
                    >
                        Reset Password
                    </button>
                </div>

                <!-- Back to Login Link -->
                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-umpsa-teal hover:text-umpsa-royal-blue transition-colors" style="font-family: 'Inter', sans-serif;">
                        ← Back to Login
                    </a>
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

function togglePasswordConfirmation() {
    const passwordInput = document.getElementById('password_confirmation');
    const eyeIcon = document.getElementById('eye-icon-confirm');
    const eyeOffIcon = document.getElementById('eye-off-icon-confirm');
    
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

