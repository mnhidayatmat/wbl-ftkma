@extends('layouts.app')

@section('title', 'Company Details')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $company->company_name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Company Management & MoU/MoA Tracking</p>
            </div>
            <div class="flex gap-2">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.companies.edit', $company) }}" class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                    Edit Company
                </a>
                @endif
                <a href="{{ route('admin.companies.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                    Back
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md mb-6" x-data="{ activeTab: '{{ $tab }}' }">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex -mb-px overflow-x-auto">
                    <button @click="activeTab = 'overview'" 
                            :class="activeTab === 'overview' ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        Overview
                    </button>
                    <button @click="activeTab = 'contacts'" 
                            :class="activeTab === 'contacts' ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        Contacts
                    </button>
                    <button @click="activeTab = 'students'"
                            :class="activeTab === 'students' ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        Students
                    </button>
                    <button @click="activeTab = 'agreements'" 
                            :class="activeTab === 'agreements' ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        📄 Agreements
                    </button>
                    <button @click="activeTab = 'notes'" 
                            :class="activeTab === 'notes' ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        Follow-Up Notes
                    </button>
                    <button @click="activeTab = 'documents'" 
                            :class="activeTab === 'documents' ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                        Documents
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Overview Tab -->
                <div x-show="activeTab === 'overview'" x-transition>
                    @include('companies.tabs.overview', ['company' => $company])
                </div>

                <!-- Contacts Tab -->
                <div x-show="activeTab === 'contacts'" x-transition>
                    @include('companies.tabs.contacts', ['company' => $company])
                </div>

                <!-- Students Tab -->
                <div x-show="activeTab === 'students'" x-transition>
                    @include('companies.tabs.students', ['company' => $company])
                </div>

                <!-- Agreements Tab -->
                <div x-show="activeTab === 'agreements'" x-transition>
                    @include('companies.tabs.agreements', ['company' => $company])
                </div>

                <!-- Follow-Up Notes Tab -->
                <div x-show="activeTab === 'notes'" x-transition>
                    @include('companies.tabs.notes', ['company' => $company])
                </div>

                <!-- Documents Tab -->
                <div x-show="activeTab === 'documents'" x-transition>
                    @include('companies.tabs.documents', ['company' => $company])
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Sync Alpine.js tab with URL parameter
    document.addEventListener('alpine:init', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'overview';
        Alpine.store('activeTab', tab);
    });
</script>
@endsection
