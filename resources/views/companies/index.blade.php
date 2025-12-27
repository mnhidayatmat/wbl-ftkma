@extends('layouts.app')

@section('title', 'Companies & Agreements')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Compact Header -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Companies & Agreements</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unified management of industry partners and agreements</p>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="flex gap-2">
                <a href="{{ route('admin.companies.create') }}"
                   class="px-3 py-2 text-sm bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Company & Agreement
                </a>
                <a href="{{ route('admin.companies.import.form') }}"
                   class="px-3 py-2 text-sm bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Import Excel
                </a>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-3 py-2 rounded-lg text-sm">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-3 py-2 rounded-lg text-sm">
            {{ session('error') }}
        </div>
        @endif

        <!-- Compact Statistics -->
        @if(isset($stats))
        <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['total_companies'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Companies</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-xl font-bold text-blue-600">{{ $stats['mou_count'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Active MoU</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-xl font-bold text-purple-600">{{ $stats['moa_count'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Active MoA</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-xl font-bold text-green-600">{{ $stats['with_active_agreements'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">With Agreements</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-xl font-bold text-yellow-600">{{ $stats['with_expiring_agreements'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Expiring Soon</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 text-center border border-gray-200 dark:border-gray-700">
                <div class="text-xl font-bold text-[#00AEEF]">{{ $stats['total_students'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Students</div>
            </div>
        </div>
        @endif

        <!-- Compact Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 mb-4 border border-gray-200 dark:border-gray-700">
            <form action="{{ route('admin.companies.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                <select name="agreement_type" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Agreement Types</option>
                    <option value="MoU" {{ request('agreement_type') == 'MoU' ? 'selected' : '' }}>With MoU</option>
                    <option value="MoA" {{ request('agreement_type') == 'MoA' ? 'selected' : '' }}>With MoA</option>
                    <option value="LOI" {{ request('agreement_type') == 'LOI' ? 'selected' : '' }}>With LOI</option>
                    <option value="none" {{ request('agreement_type') == 'none' ? 'selected' : '' }}>No Agreements</option>
                </select>
                <select name="agreement_status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="Active" {{ request('agreement_status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Pending" {{ request('agreement_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Draft" {{ request('agreement_status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Expired" {{ request('agreement_status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                    <option value="Terminated" {{ request('agreement_status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
                <select name="category" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Categories</option>
                    <option value="Oil and Gas" {{ request('category') == 'Oil and Gas' ? 'selected' : '' }}>Oil and Gas</option>
                    <option value="Design" {{ request('category') == 'Design' ? 'selected' : '' }}>Design</option>
                    <option value="Automotive" {{ request('category') == 'Automotive' ? 'selected' : '' }}>Automotive</option>
                    <option value="IT" {{ request('category') == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="Manufacturing" {{ request('category') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                    <option value="Construction" {{ request('category') == 'Construction' ? 'selected' : '' }}>Construction</option>
                    <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <select name="expiring" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">Expiring Filter</option>
                    <option value="3" {{ request('expiring') == '3' ? 'selected' : '' }}>3 months</option>
                    <option value="6" {{ request('expiring') == '6' ? 'selected' : '' }}>6 months</option>
                    <option value="12" {{ request('expiring') == '12' ? 'selected' : '' }}>12 months</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-3 py-2 text-sm bg-[#0084C5] text-white rounded-lg hover:bg-[#003A6C] transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.companies.index') }}" class="px-3 py-2 text-sm bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Unified Companies & Agreements Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Company</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Contact Info</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-white uppercase">Students</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Agreements</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-white uppercase">Status</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-white uppercase">Documents</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-white uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($companies as $company)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-3 py-3">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $company->company_name }}</div>
                                @if($company->category)
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->category }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $company->pic_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->email }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $company->phone }}</div>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded-full bg-[#00AEEF]/10 text-[#00AEEF]">
                                    {{ $company->students_count }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                @php
                                    $mou = $company->agreements->where('agreement_type', 'MoU')->where('status', 'Active')->first();
                                    $moa = $company->agreements->where('agreement_type', 'MoA')->where('status', 'Active')->first();
                                    $loi = $company->agreements->where('agreement_type', 'LOI')->where('status', 'Active')->first();
                                    $hasAgreements = $mou || $moa || $loi;
                                @endphp

                                @if($hasAgreements)
                                    <div class="flex flex-wrap gap-1">
                                        @if($mou)
                                            <div class="group relative">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    MoU
                                                    @if($mou->isExpiringSoon())
                                                    <span class="text-yellow-600">⚠</span>
                                                    @endif
                                                </span>
                                                @if($mou->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-2 py-1 text-xs bg-gray-900 text-white rounded shadow-lg -top-8 left-0 whitespace-nowrap">
                                                    Expires: {{ $mou->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if($moa)
                                            <div class="group relative">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    MoA
                                                    @if($moa->isExpiringSoon())
                                                    <span class="text-yellow-600">⚠</span>
                                                    @endif
                                                </span>
                                                @if($moa->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-2 py-1 text-xs bg-gray-900 text-white rounded shadow-lg -top-8 left-0 whitespace-nowrap">
                                                    Expires: {{ $moa->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if($loi)
                                            <div class="group relative">
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                    LOI
                                                    @if($loi->isExpiringSoon())
                                                    <span class="text-yellow-600">⚠</span>
                                                    @endif
                                                </span>
                                                @if($loi->end_date)
                                                <div class="hidden group-hover:block absolute z-10 px-2 py-1 text-xs bg-gray-900 text-white rounded shadow-lg -top-8 left-0 whitespace-nowrap">
                                                    Expires: {{ $loi->end_date->format('d/m/Y') }}
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        No Active Agreements
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @php
                                    // Count agreements by status (priority order: Active > Pending > Draft > Expired > Terminated)
                                    $hasActive = $company->agreements->where('status', 'Active')->count() > 0;
                                    $hasPending = $company->agreements->where('status', 'Pending')->count() > 0;
                                    $hasDraft = $company->agreements->where('status', 'Draft')->count() > 0;
                                    $hasExpired = $company->agreements->where('status', 'Expired')->count() > 0;
                                    $hasTerminated = $company->agreements->where('status', 'Terminated')->count() > 0;
                                @endphp
                                @if($hasActive)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Active
                                    </span>
                                @elseif($hasPending)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Pending
                                    </span>
                                @elseif($hasDraft)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        Draft
                                    </span>
                                @elseif($hasExpired)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Expired
                                    </span>
                                @elseif($hasTerminated)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-700 text-white dark:bg-gray-600 dark:text-gray-100">
                                        Terminated
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        None
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center">
                                @php
                                    $agreementsWithDocs = $company->agreements->filter(fn($a) => $a->document_path);
                                @endphp
                                @if($agreementsWithDocs->count() > 0)
                                    <div class="flex items-center justify-center gap-1">
                                        @foreach($agreementsWithDocs->take(3) as $agreement)
                                        <a href="{{ Storage::url($agreement->document_path) }}"
                                           target="_blank"
                                           title="{{ $agreement->agreement_type }} - {{ $agreement->agreement_title }}"
                                           class="p-1.5 text-green-600 hover:bg-green-100 dark:hover:bg-green-900/30 rounded transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                        @endforeach
                                        @if($agreementsWithDocs->count() > 3)
                                        <span class="text-xs text-gray-500">+{{ $agreementsWithDocs->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.companies.show', $company) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#0084C5] hover:text-white hover:bg-[#0084C5] dark:text-[#00AEEF] dark:hover:bg-[#0084C5] dark:hover:text-white rounded-lg transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this company? This will also delete all related agreements, contacts, notes, and documents.')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 hover:text-white hover:bg-red-600 dark:text-red-400 dark:hover:bg-red-600 dark:hover:text-white rounded-lg transition-all duration-200"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-3 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No companies found</p>
                                    @if(request()->hasAny(['search', 'agreement_type', 'agreement_status', 'category', 'expiring']))
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Try adjusting your filters</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($companies->hasPages())
            <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 text-sm">
                {{ $companies->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
