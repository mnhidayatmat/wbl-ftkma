@extends('layouts.app')

@section('title', 'Placement Tracking')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Student Profile Not Found</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Please complete your student profile first to access placement tracking.</p>
            <a href="{{ route('students.profile.create') }}" class="inline-block px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                Create Profile
            </a>
        </div>
    </div>
</div>
@endsection

