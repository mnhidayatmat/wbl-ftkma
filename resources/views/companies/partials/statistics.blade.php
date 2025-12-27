{{-- Statistics Dashboard for Companies and Agreements tabs --}}
@php
    $isAgreementsView = ($type ?? 'companies') === 'agreements';
@endphp

@if($isAgreementsView)
    {{-- Agreements Tab Statistics (7 cards) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_mou_active'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active MoU</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_moa_active'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active MoA</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $stats['total_loi_active'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active LOI</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['total_active'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Active</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['total_expired'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Expired</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['expiring_3_months'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Expiring 3mo</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-amber-600">{{ $stats['expiring_6_months'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Expiring 6mo</div>
        </div>
    </div>
@else
    {{-- Companies Tab Statistics (6 cards) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $stats['total_companies'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Companies</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['with_active_agreements'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">With Active Agreements</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['with_expiring_agreements'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">With Expiring Agreements</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['mou_count'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active MoU</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['moa_count'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Active MoA</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 text-center">
            <div class="text-2xl font-bold text-[#00AEEF]">{{ $stats['total_students'] ?? 0 }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Students</div>
        </div>
    </div>
@endif
