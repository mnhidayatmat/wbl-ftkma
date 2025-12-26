@extends('layouts.app')

@section('title', 'Companies')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Companies</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Manage industry partners and MoU/MoA tracking</p>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.companies.create') }}" class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                Add New Company
            </a>
            @endif
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Companies Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Company Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">PIC Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Phone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Students</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Agreement Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($companies as $company)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $company->company_name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $company->pic_name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $company->email }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $company->phone }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $company->students_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    // Check for active agreement first (unified system)
                                    $activeAgreement = $company->agreements->where('status', 'Active')->first();
                                    // If no active, get the most recent one
                                    $agreement = $activeAgreement ?? $company->agreements->first();

                                    // Fall back to old MoU if no unified agreements exist
                                    if (!$agreement && $company->mou) {
                                        $legacyStatus = $company->mou->status;
                                        $badgeColor = match($legacyStatus) {
                                            'Signed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'In Progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'Not Responding' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            'Not Initiated' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                            'Expired' => 'bg-black text-white dark:bg-gray-900 dark:text-gray-100',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    } elseif ($agreement) {
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
                                    }
                                @endphp

                                @if($agreement)
                                    <div class="flex flex-col gap-1">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $typeColor }}">
                                            {{ $agreement->agreement_type }}
                                        </span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                            {{ $agreement->status }}
                                        </span>
                                    </div>
                                @elseif($company->mou)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                        MoU: {{ $legacyStatus }}
                                    </span>
                                    @if($company->mou->isExpired())
                                        <span class="ml-1 text-xs text-red-600 dark:text-red-400">⚠</span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        Not Initiated
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.companies.show', $company) }}" class="text-[#0084C5] hover:text-[#003A6C] dark:text-[#00AEEF] dark:hover:text-[#0084C5] mr-3 transition-colors">
                                    View
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.companies.edit', $company) }}" class="text-[#0084C5] hover:text-[#003A6C] dark:text-[#00AEEF] dark:hover:text-[#0084C5] mr-3 transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this company?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 transition-colors">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No companies found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($companies->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $companies->links() }}
            </div>
            @endif
        </div>

        <!-- Summary Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Companies</div>
                <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $companies->total() }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">MoU Signed</div>
                <div class="text-2xl font-bold text-green-600">
                    {{ $companies->filter(fn($c) => $c->mou && $c->mou->status === 'Signed')->count() }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">MoU In Progress</div>
                <div class="text-2xl font-bold text-yellow-600">
                    {{ $companies->filter(fn($c) => $c->mou && $c->mou->status === 'In Progress')->count() }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Students</div>
                <div class="text-2xl font-bold text-[#00AEEF]">
                    {{ $companies->sum('students_count') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
