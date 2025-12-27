@extends('layouts.app')

@section('title', 'Reference Samples Management')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Reference Samples Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage reference files for students</p>
            </div>
            <a href="{{ route('reference-samples.create') }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Upload New Sample
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['total'] }}</p>
                    </div>
                    <div class="text-3xl">📋</div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Active</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                    </div>
                    <div class="text-3xl">✅</div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Inactive</p>
                        <p class="text-2xl font-bold text-gray-600">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="text-3xl">❌</div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Resumes</p>
                        <p class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['resume'] }}</p>
                    </div>
                    <div class="text-3xl">📄</div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Posters</p>
                        <p class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['poster'] }}</p>
                    </div>
                    <div class="text-3xl">🖼️</div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Achievements</p>
                        <p class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['achievement'] }}</p>
                    </div>
                    <div class="text-3xl">🏆</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
            <form method="GET" action="{{ route('reference-samples.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by title..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Category Filter -->
                <div>
                    <select name="category"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Categories</option>
                        <option value="resume" {{ request('category') == 'resume' ? 'selected' : '' }}>Resume</option>
                        <option value="poster" {{ request('category') == 'poster' ? 'selected' : '' }}>Poster</option>
                        <option value="achievement" {{ request('category') == 'achievement' ? 'selected' : '' }}>Achievement</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('reference-samples.index') }}"
                       class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Samples Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">File Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Uploaded By</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Downloads</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($samples as $sample)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-2">
                                    <span class="text-2xl">{{ $sample->category_icon }}</span>
                                    <div>
                                        <p class="text-sm font-semibold text-[#003A6C] dark:text-[#0084C5]">{{ $sample->title }}</p>
                                        @if($sample->description)
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($sample->description, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                                    {{ $sample->category_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="text-gray-900 dark:text-gray-200 font-medium">{{ $sample->file_name }}</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-xs">{{ $sample->file_size_formatted }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="text-gray-900 dark:text-gray-200">{{ $sample->uploader->name }}</p>
                                    <p class="text-gray-600 dark:text-gray-400 text-xs">{{ $sample->created_at->format('d M Y') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-200">{{ $sample->download_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('reference-samples.toggle-status', $sample) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $sample->is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 hover:bg-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }} transition-colors">
                                        {{ $sample->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('reference-samples.show', $sample) }}"
                                       class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('reference-samples.edit', $sample) }}"
                                       class="px-3 py-1 bg-[#0084C5] hover:bg-[#003A6C] text-white text-xs font-semibold rounded transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('reference-samples.destroy', $sample) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this sample? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">No reference samples found</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Get started by uploading your first sample</p>
                                    <a href="{{ route('reference-samples.create') }}"
                                       class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                                        Upload Sample
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($samples->hasPages())
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4">
                {{ $samples->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
