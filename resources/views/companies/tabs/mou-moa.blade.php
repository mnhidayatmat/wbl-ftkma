<div class="space-y-8">
    <!-- MoU Section -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-[#003A6C] dark:text-white">Memorandum of Understanding (MoU)</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">General partnership agreement framework</p>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div x-data="{ showForm: false }" class="mb-6">
            <!-- MoU Button -->
            <div class="flex justify-end mb-4">
                <button
                    @click="showForm = !showForm"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span x-text="showForm ? 'Cancel' : '{{ $company->mou ? "Update MoU" : "Create MoU" }}'"></span>
                </button>
            </div>

            <!-- MoU Form -->
            <div
                x-show="showForm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6"
                style="display: none;"
            >
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $company->mou ? 'Update' : 'Create' }} MoU</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage the Memorandum of Understanding details</p>
                </div>

                <form method="POST" action="{{ route('admin.companies.mou.store', $company) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                                <option value="Not Initiated" {{ $company->mou && $company->mou->status === 'Not Initiated' ? 'selected' : '' }}>Not Initiated</option>
                                <option value="In Progress" {{ $company->mou && $company->mou->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Signed" {{ $company->mou && $company->mou->status === 'Signed' ? 'selected' : '' }}>Signed</option>
                                <option value="Expired" {{ $company->mou && $company->mou->status === 'Expired' ? 'selected' : '' }}>Expired</option>
                                <option value="Not Responding" {{ $company->mou && $company->mou->status === 'Not Responding' ? 'selected' : '' }}>Not Responding</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Signed Date</label>
                            <input type="date" name="signed_date" value="{{ $company->mou?->signed_date?->format('Y-m-d') ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ $company->mou?->start_date?->format('Y-m-d') ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ $company->mou?->end_date?->format('Y-m-d') ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">MoU Document (PDF)</label>
                        <input type="file" name="file" accept=".pdf" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0084C5] file:text-white hover:file:bg-[#003A6C]">
                        @if($company->mou && $company->mou->file_path)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Current file: {{ basename($company->mou->file_path) }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                        <textarea name="remarks" rows="3" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors resize-none">{{ $company->mou?->remarks ?? '' }}</textarea>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $company->mou ? 'Update' : 'Create' }} MoU
                        </button>
                        <button type="button" @click="showForm = false" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($company->mou)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Status</label>
                    @php
                        $badgeColor = match($company->mou->status) {
                            'Signed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'In Progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'Not Responding' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            'Not Initiated' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            'Expired' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $badgeColor }}">
                        {{ $company->mou->status }}
                    </span>
                </div>
                @if($company->mou->file_path)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Document</label>
                    <a href="{{ asset('storage/' . $company->mou->file_path) }}" target="_blank" class="inline-flex items-center gap-2 text-[#0084C5] hover:text-[#003A6C] dark:text-[#00AEEF] font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View MoU Document
                    </a>
                </div>
                @endif
                @if($company->mou->start_date)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Start Date</label>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $company->mou->start_date->format('d M Y') }}</p>
                </div>
                @endif
                @if($company->mou->end_date)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">End Date</label>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $company->mou->end_date->format('d M Y') }}</p>
                </div>
                @endif
                @if($company->mou->remarks)
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Remarks</label>
                    <p class="text-gray-900 dark:text-white">{{ $company->mou->remarks }}</p>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-1">No MoU record yet</p>
            @if(auth()->user()->isAdmin())
            <p class="text-gray-400 dark:text-gray-500 text-sm">Click "Create MoU" to get started</p>
            @endif
        </div>
        @endif
    </div>

    <!-- MoA Section -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-[#003A6C] dark:text-white">Memorandum of Agreement (MoA)</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Specific program or student-based agreements</p>
            </div>
            @if(auth()->user()->isAdmin())
            <button
                onclick="document.getElementById('moa-form').scrollIntoView({ behavior: 'smooth' }); document.getElementById('moa-form').querySelector('button').click();"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add MoA
            </button>
            @endif
        </div>

        @if(auth()->user()->isAdmin())
        <div id="moa-form" x-data="{ showForm: false }" class="mb-6">
            <div class="flex justify-end mb-4">
                <button
                    @click="showForm = !showForm"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span x-text="showForm ? 'Cancel' : 'Add New MoA'"></span>
                </button>
            </div>

            <div
                x-show="showForm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6"
                style="display: none;"
            >
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Create New MoA</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new Memorandum of Agreement</p>
                </div>

                <form method="POST" action="{{ route('admin.companies.moa.store', $company) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                MoA Type <span class="text-red-500">*</span>
                            </label>
                            <select name="moa_type" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                                <option value="Programme-based">Programme-based</option>
                                <option value="Student-based">Student-based</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Code</label>
                            <select name="course_code" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                                <option value="">Select Course</option>
                                <option value="PPE">PPE</option>
                                <option value="IP">IP</option>
                                <option value="OSH">OSH</option>
                                <option value="FYP">FYP</option>
                                <option value="LI">LI</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                                <option value="Draft">Draft</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Signed">Signed</option>
                                <option value="Expired">Expired</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Signed Date</label>
                            <input type="date" name="signed_date" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                            <input type="date" name="start_date" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                            <input type="date" name="end_date" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">MoA Document (PDF)</label>
                        <input type="file" name="file" accept=".pdf" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0084C5] file:text-white hover:file:bg-[#003A6C]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Linked Students (for Student-based MoA)</label>
                        <select name="student_ids[]" multiple class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors" size="5">
                            @foreach($company->students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->matric_no }})</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple students</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                        <textarea name="remarks" rows="3" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors resize-none"></textarea>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create MoA
                        </button>
                        <button type="button" @click="showForm = false" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- MoA List -->
        <div class="space-y-4">
            @forelse($company->moas as $moa)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $moa->moa_type }}</h4>
                        @if($moa->course_code)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Course: <span class="font-medium">{{ $moa->course_code }}</span></p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $badgeColor = match($moa->status) {
                                'Signed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'In Progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'Draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'Expired' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $badgeColor }}">
                            {{ $moa->status }}
                        </span>
                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('admin.companies.moa.destroy', [$company, $moa]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this MoA?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-red-600 hover:text-white hover:bg-red-600 dark:text-red-400 dark:hover:bg-red-600 dark:hover:text-white rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
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
                        <a href="{{ asset('storage/' . $moa->file_path) }}" target="_blank" class="inline-flex items-center gap-2 text-[#0084C5] hover:text-[#003A6C] dark:text-[#00AEEF] font-medium transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            View MoA Document
                        </a>
                    </div>
                    @endif
                    @if($moa->students->count() > 0)
                    <div class="md:col-span-2">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">Linked Students:</span>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($moa->students as $student)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $student->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-1">No MoA records yet</p>
                @if(auth()->user()->isAdmin())
                <p class="text-gray-400 dark:text-gray-500 text-sm">Click "Add MoA" to create the first agreement</p>
                @endif
            </div>
            @endforelse
        </div>
    </div>
</div>
