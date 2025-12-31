<div class="space-y-6">
    <!-- MoU Template Section -->
    @if(auth()->user()->isAdmin())
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">MoU Template</h3>
                <p class="text-sm text-blue-600 dark:text-blue-400">Generate Memorandum of Understanding document</p>
            </div>
        </div>

        <form action="{{ route('admin.companies.mou-template.save', $company) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Manual Input Variables -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Manual Input Variables
                    </h4>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Company Number</label>
                        <input type="text" name="mou_company_number" value="{{ old('mou_company_number', $company->mou_company_number) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="e.g., MOU/UMPSA/2025/001">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Company Shortname</label>
                        <input type="text" name="mou_company_shortname" value="{{ old('mou_company_shortname', $company->mou_company_shortname) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="e.g., TMJ">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Signed Behalf Name</label>
                        <input type="text" name="mou_signed_behalf_name" value="{{ old('mou_signed_behalf_name', $company->mou_signed_behalf_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="Higher position person name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Signed Behalf Position</label>
                        <input type="text" name="mou_signed_behalf_position" value="{{ old('mou_signed_behalf_position', $company->mou_signed_behalf_position) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="e.g., Chief Executive Officer">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Witness Name</label>
                        <input type="text" name="mou_witness_name" value="{{ old('mou_witness_name', $company->mou_witness_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="Witness full name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Witness Position</label>
                        <input type="text" name="mou_witness_position" value="{{ old('mou_witness_position', $company->mou_witness_position) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="e.g., General Manager">
                    </div>
                </div>

                <!-- Auto-populated Variables (Read-only display) -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Auto-populated Variables
                    </h4>

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">${company_name}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $company->company_name ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">${hr_name}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $company->pic_name ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">${hr_phone}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $company->phone ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 dark:text-gray-400">${hr_email}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $company->email ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between items-start text-sm">
                            <span class="text-gray-500 dark:text-gray-400">${company_address}</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-right max-w-xs">{{ $company->address ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 text-sm">
                        <p class="text-amber-700 dark:text-amber-300">
                            <strong>Note:</strong> Auto-populated values come from Company Details.
                            <a href="{{ route('admin.companies.edit', $company) }}" class="underline">Edit company</a> to update these values.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-blue-200 dark:border-blue-700">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Variables
                </button>

                <a href="{{ route('admin.companies.mou-template.preview', $company) }}" target="_blank"
                   class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Preview PDF
                </a>
        </form>

                <form action="{{ route('admin.companies.mou-template.generate', $company) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Generate MoU
                    </button>
                </form>

                @if($company->mou_generated_path)
                <a href="{{ route('admin.companies.mou-template.download', $company) }}"
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download DOCX
                </a>
                @endif
            </div>

            @if($company->mou_generated_at)
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Last generated: {{ $company->mou_generated_at->format('d M Y, H:i') }}
            </p>
            @endif
    </div>
    @endif

    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5]">Company Agreements</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Unified MoU, MoA, and LOI records</p>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.agreements.create', ['company_id' => $company->id]) }}"
           class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Agreement
        </a>
        @endif
    </div>

    <!-- Summary Cards -->
    @php
        $agreements = $company->agreements;
        $activeMou = $agreements->where('agreement_type', 'MoU')->where('status', 'Active')->count();
        $activeMoa = $agreements->where('agreement_type', 'MoA')->where('status', 'Active')->count();
        $activeLoi = $agreements->where('agreement_type', 'LOI')->where('status', 'Active')->count();
        $expiringSoon = $agreements->filter(fn($a) => $a->isExpiringSoon())->count();
    @endphp
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $activeMou }}</div>
            <div class="text-xs text-blue-700 dark:text-blue-400">Active MoU</div>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $activeMoa }}</div>
            <div class="text-xs text-purple-700 dark:text-purple-400">Active MoA</div>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $activeLoi }}</div>
            <div class="text-xs text-orange-700 dark:text-orange-400">Active LOI</div>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $expiringSoon }}</div>
            <div class="text-xs text-yellow-700 dark:text-yellow-400">Expiring Soon</div>
        </div>
    </div>

    <!-- Agreements List -->
    @if($agreements->count() > 0)
    <div class="space-y-4">
        @foreach($agreements as $agreement)
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="flex flex-col sm:flex-row justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $agreement->agreement_type == 'MoU' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $agreement->agreement_type == 'MoA' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                            {{ $agreement->agreement_type == 'LOI' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}">
                            {{ $agreement->agreement_type }}
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $agreement->status == 'Active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $agreement->status == 'Expired' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                            {{ $agreement->status == 'Terminated' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                            {{ $agreement->status == 'Pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $agreement->status == 'Draft' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                            {{ $agreement->status }}
                        </span>
                        @if($agreement->isExpiringSoon())
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            ⚠️ {{ $agreement->days_until_expiry }} days left
                        </span>
                        @endif
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white">
                        {{ $agreement->agreement_title ?: 'Untitled Agreement' }}
                    </h4>
                    @if($agreement->reference_no)
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ref: {{ $agreement->reference_no }}</p>
                    @endif
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        @if($agreement->start_date && $agreement->end_date)
                        <span>{{ $agreement->start_date->format('d M Y') }} - {{ $agreement->end_date->format('d M Y') }}</span>
                        @elseif($agreement->start_date)
                        <span>From {{ $agreement->start_date->format('d M Y') }}</span>
                        @endif
                        @if($agreement->faculty || $agreement->programme)
                        <span class="ml-4">{{ $agreement->faculty }} {{ $agreement->programme ? '/ ' . $agreement->programme : '' }}</span>
                        @endif
                    </div>
                    @if($agreement->staff_pic_name || $agreement->staff_pic_phone)
                    <div class="mt-2 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-medium">Staff PIC:</span>
                        @if($agreement->staff_pic_name)
                        <span>{{ $agreement->staff_pic_name }}</span>
                        @endif
                        @if($agreement->staff_pic_phone)
                        <span class="ml-2">{{ $agreement->staff_pic_phone }}</span>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if($agreement->document_path)
                    <a href="{{ Storage::url($agreement->document_path) }}" 
                       target="_blank"
                       class="p-2 text-green-600 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors"
                       title="View PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </a>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.agreements.edit', $agreement) }}" 
                       class="p-2 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                       title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">No agreements found</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.agreements.create', ['company_id' => $company->id]) }}" class="text-[#0084C5] hover:underline">Add the first agreement</a>
            for this company.
            @else
            No agreements have been recorded for this company yet.
            @endif
        </p>
    </div>
    @endif

    <!-- Link to Full Agreements Management -->
    @if(auth()->user()->isAdmin())
    <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('admin.agreements.index', ['company_id' => $company->id]) }}" 
           class="text-[#0084C5] hover:underline text-sm">
            View all agreements in management panel →
        </a>
    </div>
    @endif
</div>

