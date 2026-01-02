@extends('layouts.app')

@section('title', 'Edit Programme')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('admin.programmes.index') }}" class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Programmes
            </a>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Edit Programme</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Update programme details</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <form action="{{ route('admin.programmes.update', $programme) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <!-- Programme Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Programme Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $programme->name) }}" required
                               placeholder="e.g., Bachelor of Mechanical Engineering Technology (Automotive) with Honours"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Short Code & Active Status Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                        <div>
                            <label for="short_code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Short Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="short_code" id="short_code" value="{{ old('short_code', $programme->short_code) }}" required maxlength="10"
                                   placeholder="e.g., BTA, BTD, BTG"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white uppercase">
                        </div>
                        <div class="flex items-center h-[42px]">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ $programme->is_active ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Active (visible for student selection)
                            </label>
                        </div>
                    </div>

                    <!-- WBL Coordinator Section -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mt-2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                WBL Coordinator
                            </h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">For SAL Template</span>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label for="wbl_coordinator_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                <input type="text" name="wbl_coordinator_name" id="wbl_coordinator_name" value="{{ old('wbl_coordinator_name', $programme->wbl_coordinator_name) }}"
                                       placeholder="e.g., Ts. Dr. Ahmad bin Abu"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="wbl_coordinator_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" name="wbl_coordinator_email" id="wbl_coordinator_email" value="{{ old('wbl_coordinator_email', $programme->wbl_coordinator_email) }}"
                                       placeholder="e.g., ahmad@umpsa.edu.my"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="wbl_coordinator_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <input type="tel" name="wbl_coordinator_phone" id="wbl_coordinator_phone" value="{{ old('wbl_coordinator_phone', $programme->wbl_coordinator_phone) }}" maxlength="20"
                                       placeholder="e.g., 09-5492000"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Student Count Info -->
                    @php $studentCount = $programme->students()->count(); @endphp
                    @if($studentCount > 0)
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm text-blue-700 dark:text-blue-300">
                                    <strong>{{ $studentCount }}</strong> student(s) are enrolled in this programme.
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-8 flex items-center justify-end gap-4">
                    <a href="{{ route('admin.programmes.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Update Programme
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
