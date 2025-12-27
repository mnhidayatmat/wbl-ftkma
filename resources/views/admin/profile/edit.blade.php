@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-white">Edit Profile</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Update your account information</p>
            </div>
            <a href="{{ route('admin.profile.show') }}"
               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                Back
            </a>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Profile Information Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Information</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update your account's profile information and email address</p>
            </div>
            <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                    <a href="{{ route('admin.profile.show') }}"
                       class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Update Password Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Update Password</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ensure your account is using a long, random password to stay secure</p>
            </div>
            <form method="POST" action="{{ route('admin.profile.password.update') }}" class="p-6">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
