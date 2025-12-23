@extends('layouts.app')

@section('title', 'IP Overview')

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
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">IP Overview</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Internship Preparation</p>
        </div>

        <!-- Content Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.255M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">IP Module</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">This module is currently under development.</p>
                <p class="text-sm text-gray-500 dark:text-gray-500">Your IP evaluations, marks, and progress will be displayed here.</p>
            </div>
        </div>
    </div>
</div>
@endsection

