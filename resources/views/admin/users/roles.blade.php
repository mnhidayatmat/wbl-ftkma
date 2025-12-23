@extends('layouts.app')

@section('title', 'User Roles Management')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">User Roles</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage roles for all users</p>
            </div>
            <a href="{{ route('admin.permissions.index') }}" 
               class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                User Access Control
            </a>
        </div>
    </div>

    <!-- Statistics Card -->
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400 px-4 py-3 rounded-lg">
            <p class="text-sm">{{ session('info') }}</p>
        </div>
    @endif

    <!-- Filters and Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Search -->
            <div class="flex flex-col md:flex-row gap-4 flex-1">
                <form method="GET" action="{{ route('admin.users.roles.index') }}" class="flex-1">
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Search by name or email..."
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-gray-100"
                        >
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-[#0084C5] text-white rounded-lg hover:bg-[#006BA3] transition-colors"
                        >
                            Search
                        </button>
                        @if(request('search'))
                            <a 
                                href="{{ route('admin.users.roles.index') }}"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                            >
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Roles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @if($user->roles->count() > 0)
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                {{ $role->display_name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400">
                                            {{ ucfirst($user->role ?? 'No Role') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                <!-- Edit Roles Button -->
                                <button 
                                    type="button"
                                    onclick="openEditRolesModal({{ $user->id }}, [{{ $user->roles->pluck('id')->implode(',') }}])"
                                    class="px-3 py-1 bg-[#00A86B] text-white rounded-lg hover:bg-[#008F5C] transition-colors text-xs"
                                >
                                    Edit Roles
                                </button>
                                    
                                    <!-- Delete User Button -->
                                    @if($user->id !== auth()->id())
                                        @php
                                            $hasStudentProfile = $user->student()->exists();
                                            $assignedAsIc = $user->assignedStudents()->exists();
                                            $assignedAsAt = $user->assignedStudentsAsAt()->exists();
                                            $warningMessage = 'Are you sure you want to delete the user account "' . $user->name . '"?\n\n';
                                            if ($hasStudentProfile || $assignedAsIc || $assignedAsAt) {
                                                $warningMessage .= "WARNING: This will also:\n";
                                                if ($hasStudentProfile) {
                                                    $warningMessage .= "• Delete the associated student profile\n";
                                                }
                                                if ($assignedAsIc) {
                                                    $warningMessage .= "• Remove Industry Coach assignments\n";
                                                }
                                                if ($assignedAsAt) {
                                                    $warningMessage .= "• Remove Academic Tutor assignments\n";
                                                }
                                                $warningMessage .= "\n";
                                            }
                                            $warningMessage .= 'This action cannot be undone.';
                                        @endphp
                                        <form 
                                            action="{{ route('admin.users.roles.destroy', $user) }}" 
                                            method="POST" 
                                            class="inline"
                                            onsubmit="return confirm('{{ addslashes($warningMessage) }}')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit"
                                                class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs flex items-center gap-1"
                                                title="Delete User Account"
                                            >
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="px-3 py-1 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-lg text-xs cursor-not-allowed" title="Cannot delete your own account">
                                            Delete
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Edit Roles Modal -->
<div id="editRolesModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit User Roles</h3>
                <button onclick="closeEditRolesModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="editRolesForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Roles
                    </label>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($allRoles as $role)
                            <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="roles[]" 
                                    value="{{ $role->id }}"
                                    class="rounded border-gray-300 text-[#00A86B] focus:ring-[#00A86B]"
                                >
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role->display_name }}</span>
                                @if($role->description)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">- {{ $role->description }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button 
                        type="button"
                        onclick="closeEditRolesModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-[#00A86B] text-white rounded-lg hover:bg-[#008F5C] transition-colors"
                    >
                        Save Roles
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditRolesModal(userId, currentRoleIds) {
        const modal = document.getElementById('editRolesModal');
        const form = document.getElementById('editRolesForm');
        
        // Set form action
        form.action = `/admin/users/roles/${userId}/update-roles`;
        
        // Clear all checkboxes
        form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Check current roles
        currentRoleIds.forEach(roleId => {
            const checkbox = form.querySelector(`input[value="${roleId}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
        
        modal.style.display = 'flex';
    }
    
    function closeEditRolesModal() {
        const modal = document.getElementById('editRolesModal');
        modal.style.display = 'none';
    }
    
    // Close modal when clicking outside
    document.getElementById('editRolesModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditRolesModal();
        }
    });
</script>
@endsection

