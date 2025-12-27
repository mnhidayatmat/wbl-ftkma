<div class="space-y-6">
    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Active Students Card -->
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Students</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $company->students->count() }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
        </div>

        <!-- Industry Coaches Card -->
        @if($company->position === 'HR' || strtolower($company->position ?? '') === 'hr')
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Industry Coaches</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $company->industry_coaches_count ?? 0 }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
        </div>
        @endif

        <!-- Agreement Status Card -->
        @php
            $activeAgreement = $company->agreements->where('status', 'Active')->first();
            $agreement = $activeAgreement ?? $company->agreements->first();
        @endphp
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Agreement Status</p>
                        @if($agreement)
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $typeColor = match($agreement->agreement_type) {
                                        'MoU' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                        'MoA' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                                        'LOI' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                    $statusColor = match($agreement->status) {
                                        'Active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                        'Pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        'Expired' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold {{ $typeColor }}">
                                    {{ $agreement->agreement_type }}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold {{ $statusColor }}">
                                    {{ $agreement->status }}
                                </span>
                            </div>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                Not Initiated
                            </span>
                        @endif
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            @if($agreement && $agreement->status === 'Active')
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
            @elseif($agreement && $agreement->status === 'Pending')
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-yellow-500 to-yellow-600"></div>
            @elseif($agreement && $agreement->status === 'Expired')
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
            @else
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-gray-400 to-gray-500"></div>
            @endif
        </div>

        <!-- Total Agreements Card -->
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Agreements</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $company->agreements->count() }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
        </div>
    </div>

    <!-- Company Information & Contact Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Company Information Section -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Company Information
                    </h3>
                </div>
                <!-- Content -->
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Company Name</dt>
                                <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ $company->company_name }}</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Category</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    @if($company->category)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                            {{ $company->category }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Not specified</span>
                                    @endif
                                </dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Industry Type</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ $company->industry_type ?? 'Not specified' }}</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Website</dt>
                                <dd class="mt-1 text-base">
                                    @if($company->website)
                                        <a href="{{ $company->website }}" target="_blank" class="text-[#0084C5] hover:text-[#003A6C] font-medium inline-flex items-center gap-1 transition-colors">
                                            {{ Str::limit($company->website, 30) }}
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Not specified</span>
                                    @endif
                                </dd>
                            </div>
                        </div>

                        @if($company->address)
                        <div class="md:col-span-2 flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Address</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white leading-relaxed">{{ $company->address }}</dd>
                            </div>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Primary Contact Section -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-[#0084C5] to-[#00AEEF] px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Primary Contact
                    </h3>
                </div>
                <!-- Content -->
                <div class="p-6">
                    @php
                        $primaryContact = $company->contacts->where('is_primary', true)->first() ?? $company->contacts->first();
                    @endphp
                    @if($primaryContact)
                        <!-- Contact Avatar -->
                        <div class="flex justify-center mb-5">
                            <div class="w-20 h-20 bg-gradient-to-br from-[#0084C5] to-[#003A6C] rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-2xl">
                                    {{ strtoupper(substr($primaryContact->name, 0, 2)) }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Name -->
                            <div class="text-center">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $primaryContact->name }}</p>
                                <span class="inline-flex items-center mt-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $primaryContact->role }}
                                </span>
                            </div>

                            <!-- Contact Details -->
                            @if($primaryContact->email)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</p>
                                    <a href="mailto:{{ $primaryContact->email }}" class="text-sm text-[#0084C5] hover:text-[#003A6C] font-medium truncate block">
                                        {{ $primaryContact->email }}
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($primaryContact->phone)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phone</p>
                                    <a href="tel:{{ $primaryContact->phone }}" class="text-sm text-[#0084C5] hover:text-[#003A6C] font-medium">
                                        {{ $primaryContact->phone }}
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <!-- Fallback to company PIC -->
                        <div class="flex justify-center mb-5">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white font-bold text-2xl">
                                    {{ $company->pic_name ? strtoupper(substr($company->pic_name, 0, 2)) : '?' }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="text-center">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $company->pic_name ?? 'No contact' }}</p>
                                @if($company->position)
                                <span class="inline-flex items-center mt-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $company->position }}
                                </span>
                                @endif
                            </div>

                            @if($company->email)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</p>
                                    <a href="mailto:{{ $company->email }}" class="text-sm text-[#0084C5] hover:text-[#003A6C] font-medium truncate block">
                                        {{ $company->email }}
                                    </a>
                                </div>
                            </div>
                            @endif

                            @if($company->phone)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phone</p>
                                    <a href="tel:{{ $company->phone }}" class="text-sm text-[#0084C5] hover:text-[#003A6C] font-medium">
                                        {{ $company->phone }}
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Contacts -->
    @if($company->contacts->where('is_primary', false)->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-700 to-gray-800 dark:from-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Additional Contacts
                <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-white/20 rounded-full">
                    {{ $company->contacts->where('is_primary', false)->count() }}
                </span>
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($company->contacts->where('is_primary', false) as $contact)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 hover:shadow-md transition-shadow border border-gray-200 dark:border-gray-600">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">
                                {{ strtoupper(substr($contact->name, 0, 2)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $contact->name }}</p>
                            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-md text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200">
                                {{ $contact->role }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3 space-y-2">
                        @if($contact->email)
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <a href="mailto:{{ $contact->email }}" class="text-[#0084C5] hover:text-[#003A6C] truncate">
                                {{ $contact->email }}
                            </a>
                        </div>
                        @endif
                        @if($contact->phone)
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <a href="tel:{{ $contact->phone }}" class="text-[#0084C5] hover:text-[#003A6C]">
                                {{ $contact->phone }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Industry Coaches & Supervised Students -->
    @if($company->industryCoaches->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-700 dark:to-emerald-700">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Industry Coaches & Supervised Students
                <span class="ml-2 px-2 py-0.5 text-xs font-semibold bg-white/20 rounded-full">
                    {{ $company->industryCoaches->count() }} IC{{ $company->industryCoaches->count() > 1 ? 's' : '' }}
                </span>
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                @foreach($company->industryCoaches as $ic)
                <div class="bg-gradient-to-r from-gray-50 to-green-50 dark:from-gray-700/50 dark:to-green-900/20 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                    <!-- IC Header -->
                    <div class="px-5 py-4 bg-white/80 dark:bg-gray-800/80 border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <!-- IC Avatar -->
                                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-white font-bold text-lg">
                                        {{ strtoupper(substr($ic->name, 0, 2)) }}
                                    </span>
                                </div>
                                <!-- IC Info -->
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $ic->name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            Industry Coach
                                        </span>
                                        @if($ic->email)
                                        <a href="mailto:{{ $ic->email }}" class="text-xs text-gray-600 dark:text-gray-400 hover:text-[#0084C5] dark:hover:text-[#00AEEF] transition-colors inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $ic->email }}
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Student Count Badge -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $ic->assignedStudents->count() }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Student{{ $ic->assignedStudents->count() !== 1 ? 's' : '' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supervised Students List -->
                    @if($ic->assignedStudents->count() > 0)
                    <div class="p-5">
                        <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Supervised Students ({{ $ic->assignedStudents->count() }})
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($ic->assignedStudents as $student)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600 hover:shadow-md hover:border-green-400 dark:hover:border-green-500 transition-all group">
                                <div class="flex items-start gap-3">
                                    <!-- Student Avatar -->
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-semibold text-xs">
                                            {{ strtoupper(substr($student->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <!-- Student Info -->
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm text-gray-900 dark:text-white truncate group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">
                                            {{ $student->name }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                            {{ $student->matric_no }}
                                        </p>
                                        @if($student->group)
                                        <span class="inline-flex items-center mt-1.5 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $student->group->name }}
                                        </span>
                                        @endif
                                        @if($student->programme)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate" title="{{ $student->programme }}">
                                            {{ Str::limit($student->programme, 30) }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                <!-- View Student Link -->
                                <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <a href="{{ route('admin.students.show', $student->id) }}"
                                       class="text-xs text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium inline-flex items-center gap-1 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="p-5 text-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        No students assigned to this Industry Coach yet.
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- MoU/Agreement Expiry Warning -->
    @if($agreement && $agreement->end_date && $agreement->end_date->isFuture() && $agreement->end_date->diffInDays(now()) <= 30)
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border-l-4 border-yellow-500 rounded-lg p-5 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-yellow-900 dark:text-yellow-200">Agreement Expiring Soon</h4>
                <p class="mt-1 text-sm text-yellow-800 dark:text-yellow-300">
                    The <strong>{{ $agreement->agreement_type }}</strong> agreement will expire on
                    <strong>{{ $agreement->end_date->format('d M Y') }}</strong>
                    ({{ $agreement->end_date->diffInDays(now()) }} days remaining). Please renew or update the agreement.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
