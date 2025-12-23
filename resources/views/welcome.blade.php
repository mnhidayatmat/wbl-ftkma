@extends('layouts.auth')

@section('title', 'Welcome')

@section('content')
<div class="min-h-screen flex flex-col">
    <!-- Hero Section -->
    <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-4xl w-full text-center space-y-8">
            <!-- UMPSA Logo -->
            <div class="flex justify-center mb-8">
                <img 
                    src="{{ asset('images/logos/UMPSA_logo.png') }}" 
                    alt="Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)"
                    class="h-24 sm:h-32 w-auto object-contain"
                >
            </div>

            <!-- Main Title -->
            <div class="space-y-4">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-gray-100" style="font-family: 'Inter', 'Segoe UI', sans-serif; letter-spacing: -0.02em;">
                    Work-Based Learning (WBL)
                </h1>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-[#003A6C] dark:text-[#0084C5]">
                    Management System
                </h2>
                <p class="text-lg sm:text-xl text-gray-700 dark:text-gray-300 font-medium mt-4" style="font-family: 'Inter', sans-serif;">
                    Faculty of Mechanical and Automotive Engineering Technology
                </p>
            </div>

            <!-- Description -->
            <div class="max-w-2xl mx-auto mt-8">
                <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                    A comprehensive platform for managing Work-Based Learning programs, 
                    student placements, assessments, and academic progress tracking.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-12">
                <a 
                    href="{{ route('login') }}" 
                    class="w-full sm:w-auto px-8 py-3 bg-[#003A6C] hover:bg-[#0084C5] text-white font-semibold rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 flex items-center justify-center gap-2"
                    style="font-family: 'Inter', sans-serif;"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Sign In
                </a>
                <a 
                    href="{{ route('register') }}" 
                    class="w-full sm:w-auto px-8 py-3 bg-white dark:bg-gray-800 border-2 border-[#003A6C] dark:border-[#0084C5] text-[#003A6C] dark:text-[#0084C5] font-semibold rounded-lg shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 flex items-center justify-center gap-2"
                    style="font-family: 'Inter', sans-serif;"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Create Account
                </a>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 max-w-4xl mx-auto">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-[#003A6C]/10 dark:bg-[#0084C5]/20 rounded-lg flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-6 h-6 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Student Management</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Track student progress, assignments, and evaluations</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-[#003A6C]/10 dark:bg-[#0084C5]/20 rounded-lg flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-6 h-6 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Company Placements</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Manage industry partnerships and student placements</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-[#003A6C]/10 dark:bg-[#0084C5]/20 rounded-lg flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-6 h-6 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Progress Tracking</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Monitor academic performance and assessment results</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                © {{ date('Y') }} Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA). All rights reserved.
            </p>
        </div>
    </footer>
</div>
@endsection
