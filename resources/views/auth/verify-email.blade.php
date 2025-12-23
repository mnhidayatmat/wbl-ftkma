@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#F5F7FA] dark:bg-gray-900 px-4 py-12">
    <div class="max-w-md w-full">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8">
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-umpsa-teal/10 mb-4">
                    <svg class="h-8 w-8 text-umpsa-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    Verify Your Email Address
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
                </p>
            </div>

            <!-- Success Message -->
            @if(session('status') == 'verification-link-sent')
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
                    <p class="text-sm">A new verification link has been sent to your email address.</p>
                </div>
            @endif

            <!-- Resend Verification Email Form -->
            <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                @csrf
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-umpsa-teal hover:bg-umpsa-royal-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-umpsa-teal transition-colors"
                >
                    Resend Verification Email
                </button>
            </form>

            <!-- Logout Option -->
            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button 
                        type="submit" 
                        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

