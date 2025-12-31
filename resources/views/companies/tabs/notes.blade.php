<div class="space-y-6">
    @if(auth()->user()->isAdmin() || auth()->user()->isLecturer() || auth()->user()->isWblCoordinator())
    <!-- Add Note Section -->
    <div x-data="{ showForm: false }">
        <!-- Add Note Button -->
        <div class="flex justify-end mb-4">
            <button
                @click="showForm = !showForm"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span x-text="showForm ? 'Cancel' : 'Add Follow-Up Note'"></span>
            </button>
        </div>

        <!-- Add Note Form -->
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Follow-Up Note</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Record communication and schedule follow-up actions</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.companies.notes.store', $company) }}" class="space-y-6">
                @csrf

                <!-- Note Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Note <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="note"
                        rows="4"
                        required
                        placeholder="Enter details about the communication or follow-up..."
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors resize-none"
                    ></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Follow-Up Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Follow-Up Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="follow_up_type"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                            <option value="">Select type</option>
                            <option value="Email">Email</option>
                            <option value="Call">Call</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Reminder sent">Reminder sent</option>
                        </select>
                    </div>

                    <!-- Next Action Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Next Action Date
                        </label>
                        <input
                            type="date"
                            name="next_action_date"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                        >
                    </div>
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
                        Save Note
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

    <!-- Notes Timeline -->
    <div class="space-y-4">
        @forelse($company->notes as $note)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-[#0084C5] p-6 hover:shadow-md transition-shadow" x-data="{ editMode: false }">
            <!-- Display Mode -->
            <div x-show="!editMode">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            @php
                                $iconBg = match($note->follow_up_type) {
                                    'Email' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                    'Call' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                                    'Meeting' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
                                    'Reminder sent' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
                                    default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                };
                            @endphp
                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $iconBg }}">
                                @if($note->follow_up_type === 'Email')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                @elseif($note->follow_up_type === 'Call')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                @elseif($note->follow_up_type === 'Meeting')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $iconBg }}">
                                    {{ $note->follow_up_type }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $note->created_at->format('d M Y, H:i') }} • By {{ $note->creator->name ?? 'Unknown' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->isAdmin() || auth()->user()->isLecturer() || auth()->user()->isWblCoordinator())
                    @if($note->created_by === auth()->id() || auth()->user()->isAdmin())
                    <div class="flex items-center gap-2">
                        <!-- Edit Button -->
                        <button
                            @click="editMode = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[#0084C5] hover:text-white hover:bg-[#0084C5] dark:text-[#00AEEF] dark:hover:bg-[#0084C5] dark:hover:text-white rounded-lg transition-all duration-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        <!-- Delete Button -->
                        <form action="{{ route('admin.companies.notes.destroy', [$company, $note]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this note?')">
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
                    </div>
                    @endif
                    @endif
                </div>

                <p class="text-gray-900 dark:text-white leading-relaxed mb-3">{{ $note->note }}</p>

                @if($note->next_action_date)
                <div class="flex items-start justify-between gap-3 p-3 rounded-lg border
                    {{ $note->isOverdue() ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-900/30' : 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-900/30' }}">
                    <div class="flex items-center gap-2 flex-1">
                        <svg class="w-5 h-5 {{ $note->isOverdue() ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm">
                            <span class="font-medium {{ $note->isOverdue() ? 'text-red-900 dark:text-red-300' : 'text-amber-900 dark:text-amber-300' }}">
                                {{ $note->isOverdue() ? 'Overdue Action:' : 'Next Action:' }}
                            </span>
                            <span class="{{ $note->isOverdue() ? 'text-red-700 dark:text-red-400' : 'text-amber-700 dark:text-amber-400' }} ml-1">
                                {{ $note->next_action_date->format('d M Y') }}
                                @if($note->getDaysUntilDue() !== null)
                                    @if($note->isOverdue())
                                        ({{ abs($note->getDaysUntilDue()) }} days overdue)
                                    @else
                                        (in {{ $note->getDaysUntilDue() }} days)
                                    @endif
                                @endif
                            </span>
                            @if($note->action_status === 'completed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 ml-2">
                                    ✓ Completed
                                </span>
                            @elseif($note->action_status === 'dismissed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 ml-2">
                                    Dismissed
                                </span>
                            @endif
                        </div>
                    </div>
                    @if(auth()->user()->isAdmin() || auth()->user()->isLecturer() || auth()->user()->isWblCoordinator())
                    @if($note->action_status === 'pending')
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.companies.notes.update-status', [$company, $note]) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action_status" value="completed">
                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700 rounded transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Complete
                            </button>
                        </form>
                        <form action="{{ route('admin.companies.notes.update-status', [$company, $note]) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action_status" value="dismissed">
                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Dismiss
                            </button>
                        </form>
                    </div>
                    @endif
                    @endif
                </div>
                @endif
            </div>

            <!-- Edit Mode -->
            <div x-show="editMode" style="display: none;">
                <form method="POST" action="{{ route('admin.companies.notes.update', [$company, $note]) }}">
                    @csrf
                    @method('PATCH')

                    <!-- Header -->
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex-shrink-0 w-10 h-10 bg-[#0084C5]/10 dark:bg-[#0084C5]/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Follow-Up Note</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Update communication details and follow-up information</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Note Content Section -->
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                <svg class="w-4 h-4 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Note Content
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="note"
                                rows="5"
                                required
                                placeholder="Enter details about the communication or follow-up..."
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-800 dark:text-white transition-colors resize-none"
                            >{{ $note->note }}</textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Provide detailed information about the communication or follow-up action</p>
                        </div>

                        <!-- Follow-Up Details Section -->
                        <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <h4 class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                <svg class="w-4 h-4 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Follow-Up Details
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Follow-Up Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Communication Type <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        name="follow_up_type"
                                        required
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-800 dark:text-white transition-colors"
                                    >
                                        <option value="">Select type</option>
                                        <option value="Email" {{ $note->follow_up_type === 'Email' ? 'selected' : '' }}>📧 Email</option>
                                        <option value="Call" {{ $note->follow_up_type === 'Call' ? 'selected' : '' }}>📞 Phone Call</option>
                                        <option value="Meeting" {{ $note->follow_up_type === 'Meeting' ? 'selected' : '' }}>🤝 Meeting</option>
                                        <option value="Reminder sent" {{ $note->follow_up_type === 'Reminder sent' ? 'selected' : '' }}>🔔 Reminder Sent</option>
                                    </select>
                                </div>

                                <!-- Next Action Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Next Action Date
                                    </label>
                                    <input
                                        type="date"
                                        name="next_action_date"
                                        value="{{ $note->next_action_date ? $note->next_action_date->format('Y-m-d') : '' }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-800 dark:text-white transition-colors"
                                    >
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Schedule next follow-up action</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Note
                        </button>
                        <button
                            type="button"
                            @click="editMode = false"
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-1">No follow-up notes yet</p>
            @if(auth()->user()->isAdmin() || auth()->user()->isLecturer() || auth()->user()->isWblCoordinator())
            <p class="text-gray-400 dark:text-gray-500 text-sm">Click "Add Follow-Up Note" to record communication</p>
            @endif
        </div>
        @endforelse
    </div>
</div>
