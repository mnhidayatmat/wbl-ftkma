@extends('layouts.auth')

@section('title', 'Forgot Password')

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

    <!-- Right Side - Forgot Password Form (1/3 width) -->
    <div class="w-full lg:w-1/3 flex items-center justify-center bg-white dark:bg-gray-900 px-8 py-12">
        <div class="w-full max-w-sm space-y-6">
            <!-- Welcome Message -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2" style="font-family: 'Inter', 'Segoe UI', sans-serif;">
                    Forgot Password?
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm" style="font-family: 'Inter', sans-serif;">
                    No worries! Enter your email address and we'll send you a link to reset your password.
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
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Forgot Password Form -->
            <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
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
                        autofocus
                    >
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-umpsa-teal hover:bg-umpsa-royal-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-umpsa-teal transition-colors"
                        style="font-family: 'Inter', sans-serif;"
                    >
                        Send Password Reset Link
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
@endsection

