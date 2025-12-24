@extends('layouts.app')

@section('title', 'Manage Reference Samples')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Reference Samples Management</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload and manage reference samples for students</p>
    </div>
    <a href="{{ route('reference-samples.create') }}" class="btn-umpsa-primary">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Upload New Sample
    </a>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-[#003A6C]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Total Samples</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="text-[#003A6C]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Active</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['active'] }}</p>
            </div>
            <div class="text-green-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Inactive</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['inactive'] }}</p>
            </div>
            <div class="text-red-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Resume</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['resume'] }}</p>
            </div>
            <div class="text-blue-500">
                <span class="text-2xl">📄</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Poster</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['poster'] }}</p>
            </div>
            <div class="text-purple-500">
                <span class="text-2xl">🖼️</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Achievement</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['achievement'] }}</p>
            </div>
            <div class="text-yellow-500">
                <span class="text-2xl">🏆</span>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('reference-samples.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or description..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
            <select name="category" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Categories</option>
                <option value="resume" {{ request('category') === 'resume' ? 'selected' : '' }}>Resume</option>
                <option value="poster" {{ request('category') === 'poster' ? 'selected' : '' }}>Poster</option>
                <option value="achievement" {{ request('category') === 'achievement' ? 'selected' : '' }}>Achievement</option>
                <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-[#003A6C] hover:bg-[#0084C5] text-white rounded-lg transition-colors">
                Apply Filters
            </button>
            <a href="{{ route('reference-samples.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Reference Samples Table -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gradient-to-r from-[#003A6C] to-[#0084C5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Sample</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">File Info</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Downloads</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Uploaded By</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($samples as $sample)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $sample->category_icon }}</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $sample->title }}</p>
                                    @if($sample->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($sample->description, 50) }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $sample->category === 'resume' ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}
                                {{ $sample->category === 'poster' ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
                                {{ $sample->category === 'achievement' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
                                {{ $sample->category === 'other' ? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' : '' }}">
                                {{ $sample->category_display }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-gray-900 dark:text-white font-medium">{{ $sample->file_name }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                    {{ strtoupper($sample->file_extension) }} • {{ $sample->file_size_formatted }}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                {{ $sample->download_count }} times
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <form action="{{ route('reference-samples.toggle-status', $sample) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors
                                    {{ $sample->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-800 dark:text-red-100' }}">
                                    {{ $sample->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                            {{ $sample->display_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm">
                                <p class="text-gray-900 dark:text-white">{{ $sample->uploader->name }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs">{{ $sample->created_at->format('d M Y') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('reference-samples.download', $sample) }}" class="text-green-600 hover:text-green-900 dark:text-green-400" title="Download">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('reference-samples.edit', $sample) }}" class="text-[#0084C5] hover:text-[#003A6C]" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('reference-samples.destroy', $sample) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this sample?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No reference samples found</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload your first reference sample to get started.</p>
                                <a href="{{ route('reference-samples.create') }}" class="mt-4 btn-umpsa-primary">Upload New Sample</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($samples->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $samples->links() }}
        </div>
    @endif
</div>
@endsection
