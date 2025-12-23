<div class="space-y-6">
    @if(auth()->user()->isAdmin() || auth()->user()->isLecturer())
    <!-- Add Note Form -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6" x-data="{ showForm: false }">
        <button @click="showForm = !showForm" class="w-full flex items-center justify-between px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
            <span>Add Follow-Up Note</span>
            <svg class="w-5 h-5" :class="{ 'rotate-180': showForm }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <form x-show="showForm" x-transition method="POST" action="{{ route('companies.notes.store', $company) }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Note *</label>
                <textarea name="note" rows="4" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Follow-Up Type *</label>
                    <select name="follow_up_type" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                        <option value="">Select Type</option>
                        <option value="Email">Email</option>
                        <option value="Call">Call</option>
                        <option value="Meeting">Meeting</option>
                        <option value="Reminder sent">Reminder sent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Next Action Date</label>
                    <input type="date" name="next_action_date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                Add Note
            </button>
        </form>
    </div>
                @endif

    <!-- Notes Timeline -->
    <div class="space-y-4">
        @forelse($company->notes as $note)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 border-l-4 border-[#0084C5]">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $note->follow_up_type }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $note->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        By: {{ $note->creator->name ?? 'Unknown' }}
                    </p>
                </div>
                @if(auth()->user()->isAdmin() || auth()->user()->isLecturer())
                @if($note->created_by === auth()->id() || auth()->user()->isAdmin())
                <form action="{{ route('companies.notes.destroy', [$company, $note]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this note?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">Delete</button>
                </form>
                @endif
                @endif
            </div>
            <p class="text-gray-900 dark:text-white mb-2">{{ $note->note }}</p>
            @if($note->next_action_date)
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                <strong>Next Action:</strong> {{ $note->next_action_date->format('d M Y') }}
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            No follow-up notes found. @if(auth()->user()->isAdmin() || auth()->user()->isLecturer())Add a note to track communication.@endif
        </div>
        @endforelse
    </div>
</div>

