<div class="space-y-6">
    <!-- Company Information & Primary Contact -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Company Basic Info (2 columns) -->
        <div class="lg:col-span-2 bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
            <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Company Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Company Name</label>
                    <p class="text-base text-gray-900 dark:text-white font-medium">{{ $company->company_name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Category</label>
                    <p class="text-base text-gray-900 dark:text-white">{{ $company->category ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Industry Type</label>
                    <p class="text-base text-gray-900 dark:text-white">{{ $company->industry_type ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Website</label>
                    <p class="text-base text-gray-900 dark:text-white">
                        @if($company->website)
                            <a href="{{ $company->website }}" target="_blank" class="text-[#0084C5] hover:underline inline-flex items-center gap-1">
                                {{ $company->website }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                @if($company->address)
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Address</label>
                    <p class="text-base text-gray-900 dark:text-white">{{ $company->address }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Primary Contact (1 column) -->
        <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-700 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
            <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Primary Contact
            </h3>
            <div class="space-y-3">
                @php
                    $primaryContact = $company->contacts->where('is_primary', true)->first() ?? $company->contacts->first();
                @endphp
                @if($primaryContact)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Name</label>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $primaryContact->name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Role</label>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ $primaryContact->role }}
                        </span>
                    </div>
                    @if($primaryContact->email)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Email</label>
                        <a href="mailto:{{ $primaryContact->email }}" class="text-base text-[#0084C5] hover:underline break-all">
                            {{ $primaryContact->email }}
                        </a>
                    </div>
                    @endif
                    @if($primaryContact->phone)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Phone</label>
                        <a href="tel:{{ $primaryContact->phone }}" class="text-base text-[#0084C5] hover:underline">
                            {{ $primaryContact->phone }}
                        </a>
                    </div>
                    @endif
                @else
                    <!-- Fallback to company PIC if no contacts -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Name</label>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $company->pic_name ?? 'N/A' }}</p>
                    </div>
                    @if($company->position)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Position</label>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ $company->position }}
                        </span>
                    </div>
                    @endif
                    @if($company->email)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Email</label>
                        <a href="mailto:{{ $company->email }}" class="text-base text-[#0084C5] hover:underline break-all">
                            {{ $company->email }}
                        </a>
                    </div>
                    @endif
                    @if($company->phone)
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Phone</label>
                        <a href="tel:{{ $company->phone }}" class="text-base text-[#0084C5] hover:underline">
                            {{ $company->phone }}
                        </a>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Additional Contacts Section -->
    @if($company->contacts->count() > 1)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-[#003A6C] to-[#0084C5]">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Additional Contacts ({{ $company->contacts->count() - 1 }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Phone</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($company->contacts->where('is_primary', false) as $contact)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-[#0084C5] to-[#003A6C] rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold text-xs">
                                        {{ strtoupper(substr($contact->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="ml-3">
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
        @php
            $activeAgreement = $company->agreements->where('status', 'Active')->first();
            $agreement = $activeAgreement ?? $company->agreements->first();
            $borderColor = 'border-gray-500';
            if ($agreement) {
                $borderColor = match($agreement->status) {
                    'Active' => 'border-green-500',
                    'Expired' => 'border-red-500',
                    'Pending' => 'border-yellow-500',
                    default => 'border-gray-500',
                };
            } elseif ($company->mou) {
                $borderColor = match($company->mou->status) {
                    'Signed' => 'border-green-500',
                    'Expired' => 'border-red-500',
                    default => 'border-yellow-500',
                };
            }
        @endphp
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 border-l-4 {{ $borderColor }}">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Agreement Status</div>
            <div class="mt-2 space-y-1">
                @if($agreement)
                    @php
                        $badgeColor = match($agreement->status) {
                            'Active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'Expired' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            'Terminated' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                            'Draft' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                            default => 'bg-gray-100 text-gray-800',
                        };
                        $typeColor = match($agreement->agreement_type) {
                            'MoU' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'MoA' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                            'LOI' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                            default => 'bg-gray-50 text-gray-700',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $typeColor }}">
                        {{ $agreement->agreement_type }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $badgeColor }}">
                        {{ $agreement->status }}
                    </span>
                @elseif($company->mou)
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
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">MoU</span>
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
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Agreements</div>
            <div class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mt-2">{{ $company->agreements->count() }}</div>
        </div>
    </div>

    <!-- MoU Expiry Warning -->
    @if($company->mou && $company->mou->end_date && $company->mou->end_date->diffInDays(now()) <= 30 && $company->mou->end_date > now())
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
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
