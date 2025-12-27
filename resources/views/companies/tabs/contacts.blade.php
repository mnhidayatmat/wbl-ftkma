<div class="space-y-6">
    @if(auth()->user()->isAdmin())
    <!-- Add Contact Section -->
    <div x-data="{ showForm: false }">
        <!-- Add Contact Button -->
        <div class="flex justify-end mb-4">
            <button
                @click="showForm = !showForm"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span x-text="showForm ? 'Cancel' : 'Add Contact'"></span>
            </button>
        </div>

        <!-- Add Contact Form -->
        <div
            x-show="showForm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6"
            style="display: none;"
        >
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Contact</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new contact person for this company</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.companies.contacts.store', $company) }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="e.g., John Smith"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>

                    <!-- Role Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="role"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                            <option value="">Select a role</option>
                            <option value="HR">HR</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Manager">Manager</option>
                            <option value="Industry Coach">Industry Coach</option>
                        </select>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input
                            type="email"
                            name="email"
                            placeholder="john.smith@company.com"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input
                            type="text"
                            name="phone"
                            placeholder="+60 12-345 6789"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>
                </div>

                <!-- Primary Contact Checkbox -->
                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <input
                        type="checkbox"
                        name="is_primary"
                        id="is_primary"
                        value="1"
                        class="w-4 h-4 rounded border-gray-300 text-[#0084C5] focus:ring-[#0084C5] focus:ring-offset-0"
                    >
                    <label for="is_primary" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Set as Primary Contact
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">This will be the main point of contact for the company</span>
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Contact
                    </button>
                    <button
                        type="button"
                        @click="showForm = false"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Contacts List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gradient-to-r from-[#003A6C] to-[#0084C5]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($company->contacts as $contact)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-[#0084C5] to-[#003A6C] rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr($contact->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $contact->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $contact->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}" class="hover:text-[#0084C5] transition-colors">
                                    {{ $contact->email }}
                                </a>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            @if($contact->phone)
                                <a href="tel:{{ $contact->phone }}" class="hover:text-[#0084C5] transition-colors">
                                    {{ $contact->phone }}
                                </a>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($contact->is_primary)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Primary
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    Secondary
                                </span>
                            @endif
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form action="{{ route('admin.companies.contacts.destroy', [$company, $contact]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this contact?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-red-600 hover:text-white hover:bg-red-600 dark:text-red-400 dark:hover:bg-red-600 dark:hover:text-white rounded-lg transition-all duration-200"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? '6' : '5' }}" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-1">No contacts yet</p>
                                @if(auth()->user()->isAdmin())
                                <p class="text-gray-400 dark:text-gray-500 text-sm">Click "Add Contact" above to create the first contact</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
