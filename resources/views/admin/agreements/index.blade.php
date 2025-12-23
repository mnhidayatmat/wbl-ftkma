@extends('layouts.app')

@section('title', 'Company Agreements')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Company Agreements</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage MoU, MoA, and LOI records</p>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="flex gap-2">
                <a href="{{ route('admin.agreements.import') }}" 
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import Excel
                </a>
                <a href="{{ route('admin.agreements.create') }}" 
                   class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Agreement
                </a>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_mou_active'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active MoU</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['total_moa_active'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active MoA</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['total_loi_active'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active LOI</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['total_active'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Active</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $stats['total_expired'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Expired</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['expiring_3_months'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Expiring 3mo</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
                <div class="text-2xl font-bold text-amber-600">{{ $stats['expiring_6_months'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Expiring 6mo</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
            <form action="{{ route('admin.agreements.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search title, ref no, company..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Types</option>
                        <option value="MoU" {{ request('type') == 'MoU' ? 'selected' : '' }}>MoU</option>
                        <option value="MoA" {{ request('type') == 'MoA' ? 'selected' : '' }}>MoA</option>
                        <option value="LOI" {{ request('type') == 'LOI' ? 'selected' : '' }}>LOI</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Expired" {{ request('status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                        <option value="Terminated" {{ request('status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div>
                    <select name="expiring" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">Expiring Filter</option>
                        <option value="3" {{ request('expiring') == '3' ? 'selected' : '' }}>Expiring in 3 months</option>
                        <option value="6" {{ request('expiring') == '6' ? 'selected' : '' }}>Expiring in 6 months</option>
                        <option value="12" {{ request('expiring') == '12' ? 'selected' : '' }}>Expiring in 12 months</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#0084C5] text-white rounded-lg hover:bg-[#003A6C] transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.agreements.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Agreements Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title / Ref No</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($agreements as $agreement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $agreement->agreement_type == 'MoU' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $agreement->agreement_type == 'MoA' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                    {{ $agreement->agreement_type == 'LOI' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}">
                                    {{ $agreement->agreement_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $agreement->company->company_name ?? 'N/A' }}
                                </div>
                                @if($agreement->faculty || $agreement->programme)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $agreement->faculty }} {{ $agreement->programme ? '/ ' . $agreement->programme : '' }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $agreement->agreement_title ?: 'No title' }}
                                </div>
                                @if($agreement->reference_no)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Ref: {{ $agreement->reference_no }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $agreement->start_date ? $agreement->start_date->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    to {{ $agreement->end_date ? $agreement->end_date->format('d/m/Y') : '-' }}
                                </div>
                                @if($agreement->isExpiringSoon())
                                <div class="text-xs text-yellow-600 font-semibold mt-1">
                                    ⚠️ {{ $agreement->days_until_expiry }} days left
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $agreement->status == 'Active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $agreement->status == 'Expired' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    {{ $agreement->status == 'Terminated' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                    {{ $agreement->status == 'Pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                    {{ $agreement->status == 'Draft' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                    {{ $agreement->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
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
                                    <form action="{{ route('admin.agreements.destroy', $agreement) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this agreement?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                                title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">No agreements found</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.agreements.create') }}" class="text-[#0084C5] hover:underline">Add your first agreement</a>
                                        or
                                        <a href="{{ route('admin.agreements.import') }}" class="text-[#0084C5] hover:underline">import from Excel</a>
                                        @else
                                        No agreements have been recorded yet.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($agreements->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $agreements->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

