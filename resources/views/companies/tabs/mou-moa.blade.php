<div class="space-y-8">
    <!-- MoU Section -->
    <div>
        <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Memorandum of Understanding (MoU)</h3>
        
        @if(auth()->user()->isAdmin())
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6" x-data="{ showForm: false }">
            <button @click="showForm = !showForm" class="w-full flex items-center justify-between px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                <span>{{ $company->mou ? 'Update MoU' : 'Create MoU' }}</span>
                <svg class="w-5 h-5" :class="{ 'rotate-180': showForm }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <form x-show="showForm" x-transition method="POST" action="{{ route('companies.mou.store', $company) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                        <select name="status" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                            <option value="Not Initiated" {{ $company->mou && $company->mou->status === 'Not Initiated' ? 'selected' : '' }}>Not Initiated</option>
                            <option value="In Progress" {{ $company->mou && $company->mou->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Signed" {{ $company->mou && $company->mou->status === 'Signed' ? 'selected' : '' }}>Signed</option>
                            <option value="Expired" {{ $company->mou && $company->mou->status === 'Expired' ? 'selected' : '' }}>Expired</option>
                            <option value="Not Responding" {{ $company->mou && $company->mou->status === 'Not Responding' ? 'selected' : '' }}>Not Responding</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Signed Date</label>
                        <input type="date" name="signed_date" value="{{ $company->mou?->signed_date?->format('Y-m-d') ?? '' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ $company->mou?->start_date?->format('Y-m-d') ?? '' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ $company->mou?->end_date?->format('Y-m-d') ?? '' }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">MoU Document (PDF)</label>
                    <input type="file" name="file" accept=".pdf" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                    @if($company->mou && $company->mou->file_path)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Current file: {{ basename($company->mou->file_path) }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">{{ $company->mou?->remarks ?? '' }}</textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                    {{ $company->mou ? 'Update' : 'Create' }} MoU
                </button>
            </form>
        </div>
                @endif

        @if($company->mou)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    @php
                        $badgeColor = match($company->mou->status) {
                            'Signed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'In Progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'Not Responding' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            'Not Initiated' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                            'Expired' => 'bg-black text-white dark:bg-gray-900 dark:text-gray-100',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $badgeColor }}">
                        {{ $company->mou->status }}
                    </span>
                </div>
                @if($company->mou->file_path)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Document</label>
                    <a href="{{ asset('storage/' . $company->mou->file_path) }}" target="_blank" class="text-[#0084C5] hover:underline">View MoU Document</a>
                </div>
                @endif
                @if($company->mou->start_date)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <p class="text-gray-900 dark:text-white">{{ $company->mou->start_date->format('d M Y') }}</p>
                </div>
                @endif
                @if($company->mou->end_date)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <p class="text-gray-900 dark:text-white">{{ $company->mou->end_date->format('d M Y') }}</p>
                </div>
                @endif
                @if($company->mou->remarks)
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                    <p class="text-gray-900 dark:text-white">{{ $company->mou->remarks }}</p>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
            No MoU record found. @if(auth()->user()->isAdmin())Create one to get started.@endif
        </div>
        @endif
    </div>

    <!-- MoA Section -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Memorandum of Agreement (MoA)</h3>
            @if(auth()->user()->isAdmin())
            <button onclick="document.getElementById('moa-form').scrollIntoView({ behavior: 'smooth' })" class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                Add MoA
            </button>
            @endif
        </div>

        @if(auth()->user()->isAdmin())
        <div id="moa-form" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6" x-data="{ showForm: false }">
            <button @click="showForm = !showForm" class="w-full flex items-center justify-between px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                <span>Add New MoA</span>
                <svg class="w-5 h-5" :class="{ 'rotate-180': showForm }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <form x-show="showForm" x-transition method="POST" action="{{ route('companies.moas.store', $company) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">MoA Type *</label>
                        <select name="moa_type" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                            <option value="Programme-based">Programme-based</option>
                            <option value="Student-based">Student-based</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Course Code</label>
                        <select name="course_code" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                            <option value="">Select Course</option>
                            <option value="PPE">PPE</option>
                            <option value="IP">IP</option>
                            <option value="OSH">OSH</option>
                            <option value="FYP">FYP</option>
                            <option value="LI">LI</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                        <select name="status" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                            <option value="Draft">Draft</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Signed">Signed</option>
                            <option value="Expired">Expired</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Signed Date</label>
                        <input type="date" name="signed_date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input type="date" name="start_date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                        <input type="date" name="end_date" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">MoA Document (PDF)</label>
                    <input type="file" name="file" accept=".pdf" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Linked Students (for Student-based MoA)</label>
                    <select name="student_ids[]" multiple class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white" size="5">
                        @foreach($company->students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->matric_no }})</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple students</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                    <textarea name="remarks" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                    Create MoA
                </button>
            </form>
        </div>
                @endif

        <!-- MoA List -->
        <div class="space-y-4">
            @forelse($company->moas as $moa)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $moa->moa_type }}</h4>
                        @if($moa->course_code)
                            <p class="text-sm text-gray-600 dark:text-gray-400">Course: {{ $moa->course_code }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $badgeColor = match($moa->status) {
                                'Signed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'In Progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'Draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                'Expired' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $badgeColor }}">
                            {{ $moa->status }}
                        </span>
                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('companies.moas.destroy', [$company, $moa]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this MoA?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">Delete</button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    @if($moa->start_date)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Start Date:</span>
                        <span class="text-gray-600 dark:text-gray-400 ml-2">{{ $moa->start_date->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($moa->end_date)
                    <div>
                        <span class="font-semibold text-gray-700 dark:text-gray-300">End Date:</span>
                        <span class="text-gray-600 dark:text-gray-400 ml-2">{{ $moa->end_date->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($moa->file_path)
                    <div class="md:col-span-2">
                        <a href="{{ asset('storage/' . $moa->file_path) }}" target="_blank" class="text-[#0084C5] hover:underline">View MoA Document</a>
                    </div>
                    @endif
                    @if($moa->students->count() > 0)
                    <div class="md:col-span-2">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Linked Students:</span>
                        <span class="text-gray-600 dark:text-gray-400 ml-2">
                            {{ $moa->students->pluck('name')->join(', ') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
                No MoA records found. @if(auth()->user()->isAdmin())Add one to get started.@endif
            </div>
            @endforelse
        </div>
    </div>
</div>

