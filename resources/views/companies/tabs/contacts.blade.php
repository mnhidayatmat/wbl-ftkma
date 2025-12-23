<div class="space-y-6">
    @if(auth()->user()->isAdmin())
    <!-- Add Contact Form -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6" x-data="{ showForm: false }">
        <button @click="showForm = !showForm" class="w-full flex items-center justify-between px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
            <span>Add New Contact</span>
            <svg class="w-5 h-5" :class="{ 'rotate-180': showForm }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <form x-show="showForm" x-transition method="POST" action="{{ route('companies.contacts.store', $company) }}" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role *</label>
                    <select name="role" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                        <option value="">Select Role</option>
                        <option value="HR">HR</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Manager">Manager</option>
                        <option value="Industry Coach">Industry Coach</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                    <input type="text" name="phone" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                </div>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_primary" id="is_primary" class="rounded border-gray-300 text-[#0084C5] focus:ring-[#0084C5]">
                <label for="is_primary" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as Primary Contact</label>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                Add Contact
            </button>
        </form>
    </div>
                @endif

    <!-- Contacts List -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-[#003A6C]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                    @if(auth()->user()->isAdmin())
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($company->contacts as $contact)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $contact->name }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                        {{ $contact->role }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                        {{ $contact->email ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                        {{ $contact->phone ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($contact->is_primary)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Primary
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                Secondary
                            </span>
                        @endif
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <form action="{{ route('companies.contacts.destroy', [$company, $contact]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this contact?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600">Delete</button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isAdmin() ? '6' : '5' }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No contacts found. @if(auth()->user()->isAdmin())Add a contact to get started.@endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

