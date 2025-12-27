<div class="space-y-6">
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

