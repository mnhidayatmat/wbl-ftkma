@extends('layouts.app')

@section('title', 'PPE Overview')

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
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">PPE Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Professional Practice & Ethics</p>
        </div>

        <!-- Content Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">PPE Module</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">This module is currently under development.</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">Your PPE evaluations, marks, and progress will be displayed here.</p>
            </div>
        </div>
    </div>
</div>
@endsection

