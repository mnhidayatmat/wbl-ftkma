@extends('layouts.app')

@section('title', 'FYP Overview')

@section('content')
<div class="min-h-screen bg-umpsa-soft-gray dark:bg-gray-900 -mx-10 -my-6 px-10 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">FYP Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Final Year Project - Semester 7</p>
        </div>

        <!-- Content Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">FYP Module</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">This module is currently under development.</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">Your FYP information, evaluations, and progress will be displayed here.</p>
            </div>
        </div>
    </div>
</div>
@endsection

