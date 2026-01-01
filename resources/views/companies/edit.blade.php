@extends('layouts.app')

@section('title', 'Edit Company')

@push('styles')
<style>
    @keyframes elegantGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }

    .form-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 25%, #3b82a0 50%, #4a9eb8 75%, #1e3a5f 100%);
        background-size: 400% 400%;
        animation: elegantGradient 20s ease infinite;
        position: relative;
        overflow: hidden;
    }

    .form-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M30 30c0-5.5 4.5-10 10-10s10 4.5 10 10-4.5 10-10 10-10-4.5-10-10zm-20 0c0-5.5 4.5-10 10-10s10 4.5 10 10-4.5 10-10 10-10-4.5-10-10z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        position: relative;
        padding-left: 1rem;
    }

    .section-header::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #667eea, #764ba2);
        border-radius: 2px;
    }

    .form-input {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        transform: translateY(-1px);
    }

    .form-select {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }

    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }

    .btn-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    .btn-gradient-primary::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-gradient-primary:hover::after {
        left: 100%;
    }

    .btn-outline {
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .btn-outline:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
        transform: translateY(-2px);
    }

    .mou-section-toggle {
        transition: all 0.3s ease;
    }

    .mou-section-toggle:hover {
        background: rgba(102, 126, 234, 0.05);
    }

    .template-code {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 0.65rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        color: #764ba2;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid rgba(118, 75, 162, 0.2);
    }

    .info-badge {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1));
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #059669;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50/30">
    <!-- Elegant Hero Header -->
    <div class="form-hero py-8 mb-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('admin.companies.index') }}" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Companies
                </a>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center" style="animation: iconFloat 3s ease-in-out infinite;">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">Edit Company</h1>
                    <p class="text-white/80">Update information for {{ $company->company_name }}</p>
                </div>
            </div>
        </div>
        <!-- Wave Decoration -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full h-8">
                <path d="M0 60V30C240 50 480 10 720 30C960 50 1200 10 1440 30V60H0Z" fill="white" fill-opacity="0.1"/>
                <path d="M0 60V40C240 55 480 25 720 40C960 55 1200 25 1440 40V60H0Z" fill="#f8fafc"/>
            </svg>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('admin.companies.update', $company) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Company Information Section -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="section-header">
                        <h2 class="text-lg font-bold text-gray-800">Company Information</h2>
                        <p class="text-sm text-gray-500 mt-1">Basic details about the company</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $company->company_name) }}"
                               class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('company_name') border-red-500 @enderror" required>
                        @error('company_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Industry Type -->
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Industry Type</label>
                        <select name="category" id="category"
                                class="form-select w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('category') border-red-500 @enderror"
                                onchange="handleCategoryChange(this)">
                            <option value="">Select Industry Type</option>
                            <option value="Oil and Gas" {{ old('category', $company->category) === 'Oil and Gas' ? 'selected' : '' }}>Oil and Gas</option>
                            <option value="Design" {{ old('category', $company->category) === 'Design' ? 'selected' : '' }}>Design</option>
                            <option value="Automotive" {{ old('category', $company->category) === 'Automotive' ? 'selected' : '' }}>Automotive</option>
                            <option value="Manufacturing" {{ old('category', $company->category) === 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                            <option value="Construction" {{ old('category', $company->category) === 'Construction' ? 'selected' : '' }}>Construction</option>
                            <option value="Information Technology" {{ old('category', $company->category) === 'Information Technology' ? 'selected' : '' }}>Information Technology</option>
                            <option value="Telecommunications" {{ old('category', $company->category) === 'Telecommunications' ? 'selected' : '' }}>Telecommunications</option>
                            <option value="Healthcare" {{ old('category', $company->category) === 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                            <option value="Education" {{ old('category', $company->category) === 'Education' ? 'selected' : '' }}>Education</option>
                            <option value="Finance" {{ old('category', $company->category) === 'Finance' ? 'selected' : '' }}>Finance</option>
                            <option value="Retail" {{ old('category', $company->category) === 'Retail' ? 'selected' : '' }}>Retail</option>
                            <option value="Food and Beverage" {{ old('category', $company->category) === 'Food and Beverage' ? 'selected' : '' }}>Food and Beverage</option>
                            <option value="Other" {{ old('category', $company->category) && !in_array(old('category', $company->category), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? 'selected' : '' }}>Other</option>
                        </select>
                        <div id="category_other_container" style="display: {{ old('category', $company->category) && !in_array(old('category', $company->category), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? 'block' : 'none' }};" class="mt-3">
                            <input type="text" name="category_other" id="category_other" value="{{ old('category', $company->category) && !in_array(old('category', $company->category), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? old('category', $company->category) : '' }}"
                                   placeholder="Specify industry type"
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                        </div>
                        @error('category')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PIC Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="pic_name" class="block text-sm font-semibold text-gray-700 mb-2">Person in Charge (PIC)</label>
                            <input type="text" name="pic_name" id="pic_name" value="{{ old('pic_name', $company->pic_name) }}"
                                   placeholder="Full name of contact person"
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('pic_name') border-red-500 @enderror">
                            @error('pic_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                            <select name="position" id="position"
                                    class="form-select w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('position') border-red-500 @enderror"
                                    onchange="handlePositionChange(this)">
                                <option value="">Select Position</option>
                                <option value="HR" {{ old('position', $company->position) === 'HR' ? 'selected' : '' }}>HR</option>
                                <option value="Manager" {{ old('position', $company->position) === 'Manager' ? 'selected' : '' }}>Manager</option>
                                <option value="Director" {{ old('position', $company->position) === 'Director' ? 'selected' : '' }}>Director</option>
                                <option value="Other" {{ old('position', $company->position) && !in_array(old('position', $company->position), ['HR', 'Manager', 'Director', '']) ? 'selected' : '' }}>Other</option>
                            </select>
                            <div id="position_other_container" style="display: {{ old('position', $company->position) && !in_array(old('position', $company->position), ['HR', 'Manager', 'Director', '']) ? 'block' : 'none' }};" class="mt-3">
                                <input type="text" name="position_other" id="position_other" value="{{ old('position', $company->position) && !in_array(old('position', $company->position), ['HR', 'Manager', 'Director', '']) ? old('position', $company->position) : '' }}"
                                       placeholder="Specify position"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                            </div>
                            @error('position')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if($company->position === 'HR' || strtolower($company->position ?? '') === 'hr')
                                <p class="mt-2 text-xs info-badge inline-flex items-center gap-1 px-3 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $company->industry_coaches_count ?? 0 }} Industry Coach(es) linked
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                                       placeholder="company@example.com"
                                       class="form-input w-full pl-12 pr-4 py-3 rounded-xl bg-white text-gray-900 @error('email') border-red-500 @enderror">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                                       placeholder="+60123456789"
                                       class="form-input w-full pl-12 pr-4 py-3 rounded-xl bg-white text-gray-900 @error('phone') border-red-500 @enderror">
                            </div>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 text-sm text-gray-500 bg-gray-100 border-2 border-r-0 border-gray-200 rounded-l-xl">
                                https://
                            </span>
                            <input type="text" name="website" id="website" value="{{ old('website', str_replace(['https://', 'http://'], '', $company->website ?? '')) }}"
                                   placeholder="www.example.com"
                                   class="form-input flex-1 px-4 py-3 rounded-r-xl rounded-l-none bg-white text-gray-900 @error('website') border-red-500 @enderror">
                        </div>
                        @error('website')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Company Address</label>
                        <div class="relative">
                            <div class="absolute top-3 left-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <textarea name="address" id="address" rows="4"
                                      placeholder="No. Street Name&#10;Postcode City&#10;State"
                                      class="form-input w-full pl-12 pr-4 py-3 rounded-xl bg-white text-gray-900 resize-none @error('address') border-red-500 @enderror">{{ old('address', $company->address) }}</textarea>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">This address will be used in official documents like SAL.</p>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- MoU Template Variables Section -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="mou-section-toggle p-6 cursor-pointer" onclick="toggleMouSection()">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-800">MoU Template Variables</h2>
                                <p class="text-sm text-gray-500">Default values for MoU document generation</p>
                            </div>
                        </div>
                        <svg id="mouChevron" class="w-5 h-5 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <div id="mouSection" class="hidden border-t border-gray-100">
                    <div class="p-6 space-y-6">
                        <!-- Info Notice -->
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-amber-700">
                                    These values will be used when generating MoU documents. You can also override them per-company in the Agreements tab.
                                </p>
                            </div>
                        </div>

                        <!-- Company MoU Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="mou_company_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                    MoU Reference Number
                                    <code class="template-code ml-2">${company_number}</code>
                                </label>
                                <input type="text" name="mou_company_number" id="mou_company_number" value="{{ old('mou_company_number', $company->mou_company_number) }}"
                                       placeholder="e.g., MOU/UMPSA/2025/001"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                            </div>

                            <div>
                                <label for="mou_company_shortname" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Company Short Name
                                    <code class="template-code ml-2">${company_shortname}</code>
                                </label>
                                <input type="text" name="mou_company_shortname" id="mou_company_shortname" value="{{ old('mou_company_shortname', $company->mou_company_shortname) }}"
                                       placeholder="e.g., TMJ"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                            </div>

                            <div>
                                <label for="mou_signed_behalf_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Signed Behalf Name
                                    <code class="template-code ml-2">${signed_behalf_name}</code>
                                </label>
                                <input type="text" name="mou_signed_behalf_name" id="mou_signed_behalf_name" value="{{ old('mou_signed_behalf_name', $company->mou_signed_behalf_name) }}"
                                       placeholder="Higher position person name"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                            </div>

                            <div>
                                <label for="mou_signed_behalf_position" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Signed Behalf Position
                                    <code class="template-code ml-2">${signed_behalf_position}</code>
                                </label>
                                <input type="text" name="mou_signed_behalf_position" id="mou_signed_behalf_position" value="{{ old('mou_signed_behalf_position', $company->mou_signed_behalf_position) }}"
                                       placeholder="e.g., Chief Executive Officer"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                            </div>

                            <div>
                                <label for="mou_witness_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Witness Name
                                    <code class="template-code ml-2">${witness_name}</code>
                                </label>
                                <input type="text" name="mou_witness_name" id="mou_witness_name" value="{{ old('mou_witness_name', $company->mou_witness_name) }}"
                                       placeholder="Witness full name"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                            </div>

                            <div>
                                <label for="mou_witness_position" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Witness Position
                                    <code class="template-code ml-2">${witness_position}</code>
                                </label>
                                <input type="text" name="mou_witness_position" id="mou_witness_position" value="{{ old('mou_witness_position', $company->mou_witness_position) }}"
                                       placeholder="e.g., General Manager"
                                       class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                            </div>
                        </div>

                        <!-- UMPSA Staff Section -->
                        <div class="pt-6 border-t border-gray-200">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">UMPSA Staff & Leadership</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="mou_liaison_officer" class="block text-sm font-semibold text-gray-700 mb-2">
                                        UMPSA Liaison Officer
                                        <code class="template-code ml-1">${liaison_officer}</code>
                                    </label>
                                    <input type="text" name="mou_liaison_officer" id="mou_liaison_officer" value="{{ old('mou_liaison_officer', $company->mou_liaison_officer) }}"
                                           placeholder="Staff handling this MoU"
                                           class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                                </div>

                                <div>
                                    <label for="mou_vc_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Vice Chancellor
                                        <code class="template-code ml-1">${vc_name}</code>
                                    </label>
                                    <input type="text" name="mou_vc_name" id="mou_vc_name" value="{{ old('mou_vc_name', $company->mou_vc_name ?? 'Professor Dr. Yatimah Alias') }}"
                                           placeholder="Professor Dr. Yatimah Alias"
                                           class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                                </div>

                                <div>
                                    <label for="mou_dvc_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Deputy VC (A&I)
                                        <code class="template-code ml-1">${dvc_name}</code>
                                    </label>
                                    <input type="text" name="mou_dvc_name" id="mou_dvc_name" value="{{ old('mou_dvc_name', $company->mou_dvc_name ?? 'Professor Dato Ir. Ts. Dr. Ahmad Ziad Sulaiman') }}"
                                           placeholder="Professor Dato Ir. Ts. Dr. Ahmad Ziad Sulaiman"
                                           class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('admin.companies.index') }}"
                   class="btn-outline px-6 py-3 rounded-xl text-gray-700 font-semibold">
                    Cancel
                </a>
                <button type="submit"
                        class="btn-gradient-primary px-8 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Company
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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

    function toggleMouSection() {
        const section = document.getElementById('mouSection');
        const chevron = document.getElementById('mouChevron');

        section.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }
</script>
@endsection
