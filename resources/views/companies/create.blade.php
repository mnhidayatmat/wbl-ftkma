@extends('layouts.app')

@section('title', 'Add Company & Agreement')

@push('styles')
<style>
    /* Elegant Gradient Header */
    .form-hero {
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

    /* Form Input Styling */
    .form-input {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Section Header */
    .section-header {
        position: relative;
        padding-left: 1rem;
    }

    .section-header::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #667eea, #764ba2);
        border-radius: 2px;
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

    /* Radio Button Styling */
    .radio-card {
        transition: all 0.3s ease;
    }

    .radio-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .radio-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Elegant Header Section -->
    <div class="form-hero relative overflow-hidden">
        <div class="wave-pattern absolute inset-0"></div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-white">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm icon-float">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Add Company & Agreement</h1>
                            <p class="text-blue-100 mt-1">Create a new company with its agreement (MoU/MoA/LOI)</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.companies.index') }}"
                   class="px-4 py-2.5 btn-secondary text-white font-semibold rounded-xl transition-all duration-300 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Companies
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 pb-8">
        @if($errors->any())
        <div class="mb-6 glass-card bg-red-50/90 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-red-100 dark:bg-red-800/50 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="font-semibold">Please correct the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-sm ml-11 space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- SECTION 1: Company Information -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="section-header text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Company Information
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Duplicate Detection Alert Placeholder -->
                    <div id="duplicate-alert" class="hidden mb-4"></div>

                    <!-- Company Name -->
                    <div class="mb-5">
                        <label for="company_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                               class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white @error('company_name') border-red-500 @enderror"
                               placeholder="Enter company name" autocomplete="off">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Search Results Dropdown -->
                        <div id="search-results" class="hidden mt-2 glass-card border border-gray-300 dark:border-gray-600 rounded-xl shadow-lg max-h-64 overflow-y-auto">
                        </div>

                        <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Start typing to check for existing companies
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Industry Type -->
                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Industry Type</label>
                            <select name="category" id="category" class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white" onchange="handleCategoryChange(this)">
                                <option value="">Select Industry Type</option>
                                <option value="Oil and Gas" {{ old('category') === 'Oil and Gas' ? 'selected' : '' }}>Oil and Gas</option>
                                <option value="Design" {{ old('category') === 'Design' ? 'selected' : '' }}>Design</option>
                                <option value="Automotive" {{ old('category') === 'Automotive' ? 'selected' : '' }}>Automotive</option>
                                <option value="Manufacturing" {{ old('category') === 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="Construction" {{ old('category') === 'Construction' ? 'selected' : '' }}>Construction</option>
                                <option value="Information Technology" {{ old('category') === 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                                <option value="Telecommunications" {{ old('category') === 'Telecommunications' ? 'selected' : '' }}>Telecommunications</option>
                                <option value="Healthcare" {{ old('category') === 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                <option value="Education" {{ old('category') === 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Finance" {{ old('category') === 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Retail" {{ old('category') === 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Food and Beverage" {{ old('category') === 'Food and Beverage' ? 'selected' : '' }}>Food and Beverage</option>
                                <option value="Other" {{ old('category') && !in_array(old('category'), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? 'selected' : '' }}>Other</option>
                            </select>
                            <div id="category_other_container" style="display: {{ old('category') && !in_array(old('category'), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? 'block' : 'none' }};" class="mt-2">
                                <input type="text" name="category_other" id="category_other" value="{{ old('category') && !in_array(old('category'), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? old('category') : '' }}" placeholder="Specify industry type" class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                        <!-- PIC Name -->
                        <div>
                            <label for="pic_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Person in Charge (PIC) Name
                            </label>
                            <input type="text" name="pic_name" id="pic_name" value="{{ old('pic_name') }}"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter PIC name">
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                            <select name="position" id="position" class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white" onchange="handlePositionChange(this)">
                                <option value="">Select Position</option>
                                <option value="HR" {{ old('position') === 'HR' ? 'selected' : '' }}>HR</option>
                                <option value="Manager" {{ old('position') === 'Manager' ? 'selected' : '' }}>Manager</option>
                                <option value="Director" {{ old('position') === 'Director' ? 'selected' : '' }}>Director</option>
                                <option value="Other" {{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', '']) ? 'selected' : '' }}>Other</option>
                            </select>
                            <div id="position_other_container" style="display: {{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', '']) ? 'block' : 'none' }};" class="mt-2">
                                <input type="text" name="position_other" id="position_other" value="{{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', '']) ? old('position') : '' }}" placeholder="Specify position" class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                If position is HR, IC users from the same company will be automatically linked.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white"
                                   placeholder="contact@company.com">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white"
                                   placeholder="+60 12-345 6789">
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mt-5">
                        <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company Address</label>
                        <textarea name="address" id="address" rows="4"
                                  placeholder="No. Street Name&#10;Postcode City&#10;State"
                                  class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white resize-none">{{ old('address') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This address will be used in official documents like SAL.</p>
                    </div>

                    <!-- Website -->
                    <div class="mt-5">
                        <label for="website" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Website</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 text-sm text-gray-500 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-600 dark:to-gray-700 dark:text-gray-300 border-2 border-r-0 border-gray-200 dark:border-gray-600 rounded-l-xl">https://</span>
                            <input type="text" name="website" id="website" value="{{ old('website') }}"
                                   placeholder="www.example.com"
                                   class="form-input flex-1 px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-r-xl dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Agreement Details -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 bg-gradient-to-r from-purple-500/10 to-fuchsia-500/10 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="section-header text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Agreement Details
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Agreement Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            Agreement Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="radio-card cursor-pointer p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-blue-400 {{ old('agreement_type', 'MoU') == 'MoU' ? 'selected border-blue-500' : '' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="agreement_type" value="MoU"
                                           {{ old('agreement_type', 'MoU') == 'MoU' ? 'checked' : '' }}
                                           class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500" required>
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white">MoU</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Memorandum of Understanding</p>
                                    </div>
                                </div>
                            </label>
                            <label class="radio-card cursor-pointer p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-purple-400 {{ old('agreement_type') == 'MoA' ? 'selected border-purple-500' : '' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="agreement_type" value="MoA"
                                           {{ old('agreement_type') == 'MoA' ? 'checked' : '' }}
                                           class="w-5 h-5 text-purple-600 border-gray-300 focus:ring-purple-500" required>
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white">MoA</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Memorandum of Agreement</p>
                                    </div>
                                </div>
                            </label>
                            <label class="radio-card cursor-pointer p-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-orange-400 {{ old('agreement_type') == 'LOI' ? 'selected border-orange-500' : '' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="agreement_type" value="LOI"
                                           {{ old('agreement_type') == 'LOI' ? 'checked' : '' }}
                                           class="w-5 h-5 text-orange-600 border-gray-300 focus:ring-orange-500" required>
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white">LOI</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Letter of Intent</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Agreement Title -->
                        <div>
                            <label for="agreement_title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Agreement Title</label>
                            <input type="text" name="agreement_title" id="agreement_title" value="{{ old('agreement_title') }}"
                                   placeholder="e.g., Industrial Training Collaboration"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Reference Number -->
                        <div>
                            <label for="reference_no" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference Number</label>
                            <input type="text" name="reference_no" id="reference_no" value="{{ old('reference_no') }}"
                                   placeholder="e.g., MOU/2024/001"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5">
                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="signed_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Signed Date</label>
                            <input type="date" name="signed_date" id="signed_date" value="{{ old('signed_date') }}"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mt-5">
                        <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                            <option value="Not Started" {{ old('status', 'Not Started') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                            <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Expired" {{ old('status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                            <option value="Terminated" {{ old('status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                        <!-- Faculty -->
                        <div>
                            <label for="faculty" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Faculty</label>
                            <input type="text" name="faculty" id="faculty" value="{{ old('faculty') }}"
                                   placeholder="e.g., Faculty of Technology"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Programme -->
                        <div>
                            <label for="programme" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Programme</label>
                            <input type="text" name="programme" id="programme" value="{{ old('programme') }}"
                                   placeholder="e.g., Bachelor of Computer Science"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Staff PIC -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                        <div>
                            <label for="staff_pic_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Staff PIC Name</label>
                            <input type="text" name="staff_pic_name" id="staff_pic_name" value="{{ old('staff_pic_name') }}"
                                   placeholder="e.g., Dr. Ahmad Bin Ali"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label for="staff_pic_phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Staff PIC Phone</label>
                            <input type="text" name="staff_pic_phone" id="staff_pic_phone" value="{{ old('staff_pic_phone') }}"
                                   placeholder="e.g., +60123456789"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="mt-5">
                        <label for="remarks" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                                  placeholder="Additional notes or comments..."
                                  class="form-input w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white resize-none">{{ old('remarks') }}</textarea>
                    </div>

                    <!-- Document Upload -->
                    <div class="mt-5">
                        <label for="document" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Upload Document (PDF)</label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-indigo-400 transition-colors">
                            <input type="file" name="document" id="document" accept=".pdf" class="hidden">
                            <label for="document" class="cursor-pointer">
                                <div class="p-3 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-xl inline-block mb-3">
                                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">PDF (max. 10MB)</p>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.companies.index') }}"
                   class="px-6 py-3 btn-secondary text-white font-semibold rounded-xl transition-all duration-300 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-3 btn-gradient text-white font-semibold rounded-xl transition-all duration-300 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Company & Agreement
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Position "Other" field toggle
    function handlePositionChange(select) {
        const otherContainer = document.getElementById('position_other_container');
        const otherInput = document.getElementById('position_other');

        if (select.value === 'Other') {
            otherContainer.style.display = 'block';
            otherInput.required = true;
        } else {
            otherContainer.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    }

    // Category "Other" field toggle
    function handleCategoryChange(select) {
        const otherContainer = document.getElementById('category_other_container');
        const otherInput = document.getElementById('category_other');

        if (select.value === 'Other') {
            otherContainer.style.display = 'block';
            otherInput.required = true;
        } else {
            otherContainer.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    }

    // Radio card selection styling
    document.querySelectorAll('input[name="agreement_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.radio-card').forEach(card => {
                card.classList.remove('selected', 'border-blue-500', 'border-purple-500', 'border-orange-500');
            });
            if (this.checked) {
                const colors = { 'MoU': 'border-blue-500', 'MoA': 'border-purple-500', 'LOI': 'border-orange-500' };
                this.closest('.radio-card').classList.add('selected', colors[this.value]);
            }
        });
    });

    // Company name duplicate detection (AJAX search)
    let searchTimeout;
    const companyNameInput = document.getElementById('company_name');
    const searchResults = document.getElementById('search-results');

    companyNameInput.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('admin.companies.search') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        searchResults.innerHTML = `
                            <div class="p-4 text-sm text-green-600 dark:text-green-400 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                No existing companies found. Safe to proceed.
                            </div>
                        `;
                        searchResults.classList.remove('hidden');
                    } else {
                        let html = `
                            <div class="p-3 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-b border-amber-200 dark:border-amber-700">
                                <p class="text-xs font-semibold text-amber-800 dark:text-amber-200 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Found ${data.length} similar ${data.length === 1 ? 'company' : 'companies'}. Please verify before creating:
                                </p>
                            </div>
                        `;

                        data.forEach(company => {
                            html += `
                                <div class="p-4 hover:bg-gradient-to-r hover:from-gray-50 hover:to-slate-50 dark:hover:from-gray-700 dark:hover:to-gray-600 border-b border-gray-200 dark:border-gray-600 last:border-b-0 transition-colors">
                                    <div class="font-semibold text-sm text-gray-900 dark:text-white">${company.company_name}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-300 mt-1 space-y-0.5">
                                        <div>PIC: ${company.pic_name || 'N/A'}</div>
                                        <div>Email: ${company.email || 'N/A'}</div>
                                        <div>Phone: ${company.phone || 'N/A'}</div>
                                    </div>
                                    <a href="{{ route('admin.companies.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-2 inline-flex items-center gap-1">
                                        View Company
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            `;
                        });

                        searchResults.innerHTML = html;
                        searchResults.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.classList.add('hidden');
                });
        }, 300);
    });

    document.addEventListener('click', function(event) {
        if (!companyNameInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.classList.add('hidden');
        }
    });

    companyNameInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && searchResults.innerHTML) {
            searchResults.classList.remove('hidden');
        }
    });
</script>
@endsection
