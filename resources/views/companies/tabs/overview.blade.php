<div class="space-y-6">
    <!-- Company Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $company->company_name }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Category</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $company->category ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Industry Type</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $company->industry_type ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Person in Charge</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $company->pic_name }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Position</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $company->position ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $company->email }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Phone</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $company->phone }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Website</label>
            <p class="text-lg text-gray-900 dark:text-white">
                @if($company->website)
                    <a href="{{ $company->website }}" target="_blank" class="text-[#0084C5] hover:underline">{{ $company->website }}</a>
                @else
                    N/A
                @endif
            </p>
        </div>
        @if($company->address)
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Address</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $company->address }}</p>
        </div>
        @endif
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Students</div>
            <div class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mt-2">{{ $company->students->count() }}</div>
        </div>
        @if($company->position === 'HR' || strtolower($company->position ?? '') === 'hr')
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 border-l-4 border-[#00A86B]">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Industry Coaches (IC)</div>
            <div class="text-3xl font-bold text-[#00A86B] mt-2">{{ $company->industry_coaches_count ?? 0 }}</div>
            @if($company->industry_coaches_count > 0)
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    @foreach($company->industryCoaches as $ic)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ $ic->name }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 border-l-4 {{ $company->mou && $company->mou->status === 'Signed' ? 'border-green-500' : ($company->mou && $company->mou->status === 'Expired' ? 'border-red-500' : 'border-yellow-500') }}">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">MoU Status</div>
            <div class="mt-2">
                @if($company->mou)
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
                    @if($company->mou->isExpired())
                        <span class="ml-2 text-xs text-red-600 dark:text-red-400">(Expired)</span>
                    @endif
                @else
                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        Not Initiated
                    </span>
                @endif
            </div>
        </div>
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 border-l-4 border-purple-500">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total MoAs</div>
            <div class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mt-2">{{ $company->moas->count() }}</div>
        </div>
    </div>

    <!-- MoU Expiry Warning -->
    @if($company->mou && $company->mou->end_date && $company->mou->end_date->diffInDays(now()) <= 30 && $company->mou->end_date > now())
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mt-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                <strong>MoU Expiry Warning:</strong> The MoU will expire on {{ $company->mou->end_date->format('d M Y') }} ({{ $company->mou->end_date->diffInDays(now()) }} days remaining).
            </p>
        </div>
    </div>
    @endif
</div>

