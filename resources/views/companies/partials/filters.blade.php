{{-- Filter component for Companies and Agreements tabs --}}
@php
    $isAgreementsView = ($type ?? 'companies') === 'agreements';
    $routeName = 'admin.companies.index';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
    <form action="{{ route($routeName) }}" method="GET" class="grid grid-cols-1 md:grid-cols-{{ $isAgreementsView ? '5' : '6' }} gap-4">
        @if($isAgreementsView)
            {{-- Hidden field to maintain view parameter for agreements tab --}}
            <input type="hidden" name="view" value="agreements">
        @endif

        {{-- Search Input --}}
        <div>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="{{ $isAgreementsView ? 'Search title, ref no, company...' : 'Search company, PIC, email...' }}"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
        </div>

        @if($isAgreementsView)
            {{-- Agreements Tab Filters --}}
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
        @else
            {{-- Companies Tab Filters --}}
            <div>
                <select name="agreement_type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Agreement Types</option>
                    <option value="MoU" {{ request('agreement_type') == 'MoU' ? 'selected' : '' }}>With MoU</option>
                    <option value="MoA" {{ request('agreement_type') == 'MoA' ? 'selected' : '' }}>With MoA</option>
                    <option value="LOI" {{ request('agreement_type') == 'LOI' ? 'selected' : '' }}>With LOI</option>
                    <option value="none" {{ request('agreement_type') == 'none' ? 'selected' : '' }}>No Agreements</option>
                </select>
            </div>
            <div>
                <select name="agreement_status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Agreement Status</option>
                    <option value="Active" {{ request('agreement_status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Expired" {{ request('agreement_status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                    <option value="Pending" {{ request('agreement_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                    <option value="">All Categories</option>
                    <option value="Oil and Gas" {{ request('category') == 'Oil and Gas' ? 'selected' : '' }}>Oil and Gas</option>
                    <option value="Design" {{ request('category') == 'Design' ? 'selected' : '' }}>Design</option>
                    <option value="Automotive" {{ request('category') == 'Automotive' ? 'selected' : '' }}>Automotive</option>
                    <option value="IT" {{ request('category') == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="Manufacturing" {{ request('category') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                    <option value="Construction" {{ request('category') == 'Construction' ? 'selected' : '' }}>Construction</option>
                    <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
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
        @endif

        {{-- Apply and Clear Buttons --}}
        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-[#0084C5] text-white rounded-lg hover:bg-[#003A6C] transition-colors">
                Filter
            </button>
            <a href="{{ route($routeName) }}{{ $isAgreementsView ? '?view=agreements' : '' }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                Clear
            </a>
        </div>
    </form>
</div>
