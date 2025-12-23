@extends('layouts.app')

@section('title', 'User Access Control')

@section('content')
<div class="py-6 overflow-x-hidden" x-data="permissionController()" x-init="init()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-4 text-sm">
            <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
                <li><a href="{{ route('admin.users.roles.index') }}" class="hover:text-[#0084C5] transition-colors">User Roles</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900 dark:text-white font-medium">User Access Control</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">User Access Control</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage permissions for each role</p>
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

        @if(!$selectedRole)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
            <p class="text-yellow-800 dark:text-yellow-300">No roles available. Please create roles first.</p>
        </div>
        @else
        <!-- Role Selector Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select Role</label>
                    <form method="GET" action="{{ route('admin.permissions.index') }}" class="flex items-center gap-4">
                        <select name="role_id" 
                                onchange="this.form.submit()"
                                class="w-64 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white font-medium">
                            @foreach($allRoles as $role)
                                <option value="{{ $role->id }}" {{ $selectedRole && $selectedRole->id == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @if($selectedRole && $selectedRole->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedRole->description }}</p>
                        @endif
                    </form>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Admin Role</div>
                    <div class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-sm font-medium">
                        Full Access (Locked)
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Permission Legend -->
        <div class="mb-6 flex items-center gap-6 text-sm">
            <span class="text-gray-600 dark:text-gray-400 font-medium">Permission Levels:</span>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Full</span>
                <span class="text-gray-500 dark:text-gray-400">Create, Read, Update, Delete</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold">View</span>
                <span class="text-gray-500 dark:text-gray-400">Read-only</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full text-xs font-semibold">None</span>
                <span class="text-gray-500 dark:text-gray-400">No access</span>
            </div>
        </div>

        <!-- Permission Matrix (Vertical Layout) -->
        <form id="permissionForm" method="POST" action="{{ route('admin.permissions.bulk-update') }}" @submit.prevent="savePermissions()">
            @csrf
            <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">
            
            @foreach($permissions as $moduleName => $modulePermissions)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <!-- Module Header with Bulk Actions -->
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">
                        {{ ucfirst(str_replace('_', ' ', $moduleName)) }}
                    </h2>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="bulkUpdateModule('{{ $moduleName }}', 'full')"
                                class="px-3 py-1.5 text-xs font-semibold bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            Grant Full
                        </button>
                        <button type="button" 
                                @click="bulkUpdateModule('{{ $moduleName }}', 'view')"
                                class="px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            View Only
                        </button>
                        <button type="button" 
                                @click="bulkUpdateModule('{{ $moduleName }}', 'none')"
                                class="px-3 py-1.5 text-xs font-semibold bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            No Access
                        </button>
                    </div>
                </div>

                <!-- Permissions List -->
                <div class="space-y-4">
                    @foreach($modulePermissions as $permission)
                    @php
                        $rolePermission = $rolePermissions->get($permission->id);
                        $currentLevel = $rolePermission ? $rolePermission->access_level : 'none';
                    @endphp
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $permission->display_name }}</div>
                            @if($permission->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $permission->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" 
                                       name="permissions[{{ $permission->id }}][access_level]"
                                       value="none"
                                       :checked="permissions['{{ $permission->id }}'] === 'none'"
                                       @change="permissions['{{ $permission->id }}'] = 'none'; markAsChanged()"
                                       class="w-4 h-4 text-[#0084C5] border-gray-300 focus:ring-[#0084C5]">
                                <span class="text-sm text-gray-600 dark:text-gray-400">None</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" 
                                       name="permissions[{{ $permission->id }}][access_level]"
                                       value="view"
                                       :checked="permissions['{{ $permission->id }}'] === 'view'"
                                       @change="permissions['{{ $permission->id }}'] = 'view'; markAsChanged()"
                                       class="w-4 h-4 text-[#0084C5] border-gray-300 focus:ring-[#0084C5]">
                                <span class="text-sm text-gray-600 dark:text-gray-400">View</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" 
                                       name="permissions[{{ $permission->id }}][access_level]"
                                       value="full"
                                       :checked="permissions['{{ $permission->id }}'] === 'full'"
                                       @change="permissions['{{ $permission->id }}'] = 'full'; markAsChanged()"
                                       class="w-4 h-4 text-[#0084C5] border-gray-300 focus:ring-[#0084C5]">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Full</span>
                            </label>
                            <input type="hidden" name="permissions[{{ $permission->id }}][permission_id]" value="{{ $permission->id }}">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <!-- Sticky Action Bar -->
            <div class="sticky bottom-0 z-10 mt-6" x-show="hasChanges">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>You have unsaved changes</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" 
                                    @click="resetChanges()"
                                    class="px-6 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    :disabled="saving"
                                    class="px-6 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <svg x-show="saving" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </div>
</div>

<script>
function permissionController() {
    return {
        permissions: {},
        hasChanges: false,
        saving: false,
        initialPermissions: {},

        init() {
            // Initialize permissions from current values
            @if($selectedRole)
            @foreach($permissions as $modulePermissions)
                @foreach($modulePermissions as $permission)
                    @php
                        $rolePermission = $rolePermissions->get($permission->id);
                        $currentLevel = $rolePermission ? $rolePermission->access_level : 'none';
                    @endphp
                    this.permissions['{{ $permission->id }}'] = '{{ $currentLevel }}';
                    this.initialPermissions['{{ $permission->id }}'] = '{{ $currentLevel }}';
                @endforeach
            @endforeach
            @endif
        },

        markAsChanged() {
            this.hasChanges = true;
        },

        resetChanges() {
            if (confirm('Are you sure you want to discard all changes?')) {
                Object.keys(this.permissions).forEach(key => {
                    this.permissions[key] = this.initialPermissions[key];
                });
                this.hasChanges = false;
                // Reset form radio buttons
                document.querySelectorAll('input[type="radio"]').forEach(radio => {
                    const permissionId = radio.name.match(/permissions\[(\d+)\]/)[1];
                    if (this.initialPermissions[permissionId] === radio.value) {
                        radio.checked = true;
                    }
                });
            }
        },

        async bulkUpdateModule(moduleName, accessLevel) {
            if (!confirm(`Set all permissions in "${moduleName}" to "${accessLevel}"?`)) {
                return;
            }

            try {
                const response = await fetch('{{ route("admin.permissions.bulk-update-module") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        role_id: {{ $selectedRole->id ?? 'null' }},
                        module_name: moduleName,
                        access_level: accessLevel
                    })
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Failed to update permissions');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        },

        async savePermissions() {
            this.saving = true;
            
            try {
                const form = document.getElementById('permissionForm');
                const formData = new FormData(form);
                
                const response = await fetch('{{ route("admin.permissions.bulk-update") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    this.hasChanges = false;
                    // Update initial permissions
                    Object.keys(this.permissions).forEach(key => {
                        this.initialPermissions[key] = this.permissions[key];
                    });
                    // Show success and reload
                    window.location.href = '{{ route("admin.permissions.index", ["role_id" => $selectedRole->id ?? null]) }}';
                } else {
                    alert('Failed to save permissions');
                    this.saving = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
                this.saving = false;
            }
        }
    }
}

// Prevent leaving page if unsaved changes
window.addEventListener('beforeunload', function(e) {
    const controller = Alpine.$data(document.querySelector('[x-data="permissionController()"]'));
    if (controller && controller.hasChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endsection
