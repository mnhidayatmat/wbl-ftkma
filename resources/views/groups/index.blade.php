@extends('layouts.app')

@section('title', 'Groups')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Group Control</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage group lifecycle: Activate or close groups to control visibility for all users</p>
        </div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.groups.create') }}" class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add New Group
    </a>
    @endif
</div>

    <!-- Info Banner: How Group Control Works -->
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-semibold text-blue-800 dark:text-blue-200 mb-1">How Group Control Works</p>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    <strong>Active Groups:</strong> Visible to all users (students, lecturers, coordinators). Students can upload documents and update their status.
                    <br>
                    <strong>Completed Groups:</strong> Hidden from lecturers/AT/IC/supervisors. Students in completed groups have read-only access. Only Admin and Coordinator can view completed groups for reporting purposes.
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Groups</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Active Groups</p>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['active'] ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Completed Groups</p>
            <p class="text-2xl font-bold text-gray-600 dark:text-gray-400 mt-1">{{ $stats['completed'] ?? 0 }}</p>
        </div>
    </div>
    @endif

    <!-- Status Filter (Admin & Coordinator only) -->
    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Status:</span>
            <a href="{{ route('admin.groups.index') }}" 
               class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ !request('status') ? 'bg-[#0084C5] text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                All
            </a>
            <a href="{{ route('admin.groups.index', ['status' => 'ACTIVE']) }}" 
               class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ request('status') === 'ACTIVE' ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                Active
            </a>
            <a href="{{ route('admin.groups.index', ['status' => 'COMPLETED']) }}" 
               class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ request('status') === 'COMPLETED' ? 'bg-gray-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                Completed
            </a>
        </div>
    </div>
    @endif
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

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Start Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">End Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Students</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($groups as $group)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $group->isCompleted() ? 'opacity-75' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $group->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $group->start_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $group->end_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $group->students_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $group->status_badge_color }}">
                                {{ $group->status_display }}
                            </span>
                            @if($group->completed_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Completed: {{ $group->completed_at->format('d M Y') }}
                                </p>
                            @endif
                        </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.groups.show', $group) }}" 
                                   class="text-[#0084C5] hover:text-[#003A6C] transition-colors">
                                    View
                                </a>
                        @if(auth()->user()->isAdmin())
                                    @if($group->isActive())
                                        <form action="{{ route('admin.groups.mark-completed', $group) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to mark this group as COMPLETED?\\n\\nThis will:\\n- Archive the group\\n- Disable student access\\n- Data will remain available for reporting\\n\\nThis action can be reversed by reopening the group.');">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-orange-600 hover:text-orange-800 transition-colors"
                                                    title="Close WBL Group">
                                                Close Group
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.groups.reopen', $group) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to reopen this group? Students will regain full access.');">
                            @csrf
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-800 transition-colors"
                                                    title="Reopen Group">
                                                Reopen
                                            </button>
                        </form>
                        @endif
                                    <a href="{{ route('admin.groups.edit', $group) }}" 
                                       class="text-[#0084C5] hover:text-[#003A6C] transition-colors">
                                        Edit
                                    </a>
                                @endif
                            </div>
                    </td>
                </tr>
            @empty
                <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No groups found.
                        </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <!-- Pagination -->
    @if($groups->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
    {{ $groups->links() }}
        </div>
    @endif
</div>
@endsection
