@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-white">My Profile</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">View and manage your account information</p>
            </div>
            <a href="{{ route('admin.profile.edit') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Profile
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Profile Information Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-center gap-6 mb-8">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-[#003A6C] to-[#0084C5] flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $user->name }}</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $role->display_name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Email Address</label>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $user->email }}</p>
                    </div>

                    <!-- Member Since -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Member Since</label>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $user->created_at->format('F d, Y') }}</p>
                    </div>

                    <!-- Last Updated -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Last Updated</label>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $user->updated_at->format('F d, Y, H:i') }}</p>
                    </div>

                    <!-- Active Role -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Active Role</label>
                        <p class="text-gray-900 dark:text-white font-medium">{{ ucfirst($user->getActiveRole()) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles & Permissions Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Roles & Permissions</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($user->roles as $role)
                    <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $role->display_name }}</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $role->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
