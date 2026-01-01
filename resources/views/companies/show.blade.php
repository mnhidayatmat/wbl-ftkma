@extends('layouts.app')

@section('title', 'Company Details')

@push('styles')
<style>
    /* Elegant Gradient Header */
    .company-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 25%, #3b82a0 50%, #4a9eb8 75%, #1e3a5f 100%);
        background-size: 400% 400%;
        animation: elegantGradient 20s ease infinite;
    }

    @keyframes elegantGradient {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Wave Pattern Overlay */
    .wave-pattern {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.05' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
        background-size: cover;
        background-position: bottom;
    }

    /* Glass Card Effect */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .glass-card {
        background: rgba(31, 41, 55, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Icon Float Animation */
    @keyframes iconFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }

    .icon-float {
        animation: iconFloat 3s ease-in-out infinite;
    }

    /* Tab Styling */
    .tab-button {
        position: relative;
        transition: all 0.3s ease;
    }

    .tab-button::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .tab-button.active::after {
        transform: scaleX(1);
    }

    .tab-button:hover:not(.active) {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
    }

    /* Button Gradient */
    .btn-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #475569 0%, #64748b 100%);
        transform: translateY(-2px);
    }

    /* Card hover effects */
    .info-card {
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Elegant Header Section -->
    <div class="company-hero relative overflow-hidden">
        <div class="wave-pattern absolute inset-0"></div>
        <div class="relative max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-white">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm icon-float">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">{{ $company->company_name }}</h1>
                            <p class="text-blue-100 mt-1">Company Management & Agreement Tracking</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if(auth()->user()->isAdmin() || auth()->user()->isWblCoordinator())
                    <a href="{{ route('admin.companies.edit', $company) }}"
                       class="px-4 py-2.5 btn-gradient text-white font-semibold rounded-xl transition-all duration-300 inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Company
                    </a>
                    @endif
                    <a href="{{ route('admin.companies.index') }}"
                       class="px-4 py-2.5 btn-secondary text-white font-semibold rounded-xl transition-all duration-300 inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 -mt-4 pb-8">
        @if(session('success'))
        <div class="mb-4 glass-card bg-green-50/90 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-800/50 rounded-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            {{ session('success') }}
        </div>
        @endif

        <!-- Tab Navigation -->
        <div class="glass-card rounded-2xl shadow-xl mb-6 overflow-hidden" x-data="{ activeTab: '{{ $tab }}' }">
            <div class="border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-slate-50 to-gray-50 dark:from-gray-800 dark:to-gray-800">
                <nav class="flex -mb-px overflow-x-auto">
                    <button @click="activeTab = 'overview'"
                            :class="activeTab === 'overview' ? 'active text-indigo-600 dark:text-indigo-400 border-indigo-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-transparent'"
                            class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Overview
                    </button>
                    <button @click="activeTab = 'students'"
                            :class="activeTab === 'students' ? 'active text-indigo-600 dark:text-indigo-400 border-indigo-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-transparent'"
                            class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Students
                    </button>
                    <button @click="activeTab = 'agreements'"
                            :class="activeTab === 'agreements' ? 'active text-indigo-600 dark:text-indigo-400 border-indigo-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-transparent'"
                            class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Agreements
                    </button>
                    <button @click="activeTab = 'notes'"
                            :class="activeTab === 'notes' ? 'active text-indigo-600 dark:text-indigo-400 border-indigo-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-transparent'"
                            class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Follow-Up Notes
                    </button>
                    <button @click="activeTab = 'documents'"
                            :class="activeTab === 'documents' ? 'active text-indigo-600 dark:text-indigo-400 border-indigo-500' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 border-transparent'"
                            class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-semibold text-sm transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Documents
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6 bg-gradient-to-br from-white to-slate-50 dark:from-gray-800 dark:to-gray-900">
                <!-- Overview Tab -->
                <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                    @include('companies.tabs.overview', ['company' => $company])
                </div>

                <!-- Students Tab -->
                <div x-show="activeTab === 'students'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                    @include('companies.tabs.students', ['company' => $company])
                </div>

                <!-- Agreements Tab -->
                <div x-show="activeTab === 'agreements'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                    @include('companies.tabs.agreements', ['company' => $company])
                </div>

                <!-- Follow-Up Notes Tab -->
                <div x-show="activeTab === 'notes'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                    @include('companies.tabs.notes', ['company' => $company])
                </div>

                <!-- Documents Tab -->
                <div x-show="activeTab === 'documents'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
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
