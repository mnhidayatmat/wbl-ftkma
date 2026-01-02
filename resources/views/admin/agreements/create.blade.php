@extends('layouts.app')

@section('title', 'Add Agreement')

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
        background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 25%, #7c3aed 50%, #8b5cf6 75%, #4c1d95 100%);
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
        background: linear-gradient(180deg, #8b5cf6, #6d28d9);
        border-radius: 2px;
    }

    .form-input {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }

    .form-input:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
        transform: translateY(-1px);
    }

    .form-select {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }

    .form-select:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
    }

    .radio-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid #e5e7eb;
    }

    .radio-card:hover {
        border-color: #c4b5fd;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1);
    }

    .radio-card.selected {
        border-color: #8b5cf6;
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.05), rgba(109, 40, 217, 0.05));
    }

    .radio-card.selected .radio-indicator {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        border-color: #8b5cf6;
    }

    .radio-card.selected .radio-indicator::after {
        content: '';
        position: absolute;
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .radio-indicator {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        position: relative;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .btn-gradient-primary {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
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
        border-color: #8b5cf6;
        background: rgba(139, 92, 246, 0.05);
        transform: translateY(-2px);
    }

    .file-upload-area {
        border: 2px dashed #d1d5db;
        transition: all 0.3s ease;
    }

    .file-upload-area:hover {
        border-color: #8b5cf6;
        background: rgba(139, 92, 246, 0.02);
    }

    .file-upload-area.dragover {
        border-color: #8b5cf6;
        background: rgba(139, 92, 246, 0.05);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .status-badge:hover {
        transform: translateY(-1px);
    }

    .status-badge.active {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-purple-50/30">
    <!-- Elegant Hero Header -->
    <div class="form-hero py-8 mb-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('admin.agreements.index') }}" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Agreements
                </a>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center" style="animation: iconFloat 3s ease-in-out infinite;">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">Add New Agreement</h1>
                    <p class="text-white/80">Create a new MoU, MoA, or LOI record</p>
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

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('admin.agreements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Agreement Details Section -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="section-header">
                        <h2 class="text-lg font-bold text-gray-800">Agreement Details</h2>
                        <p class="text-sm text-gray-500 mt-1">Select company and agreement type</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Company Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Company <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <select name="company_id" required
                                    class="form-select w-full pl-12 pr-4 py-3 rounded-xl bg-white text-gray-900 appearance-none @error('company_id') border-red-500 @enderror">
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $selectedCompanyId) == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('company_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agreement Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Agreement Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-4" x-data="{ selectedType: '{{ old('agreement_type') }}' }">
                            @foreach(\App\Models\CompanyAgreement::AGREEMENT_TYPES as $type => $label)
                            <label class="radio-card rounded-xl p-4 text-center"
                                   :class="{ 'selected': selectedType === '{{ $type }}' }"
                                   @click="selectedType = '{{ $type }}'">
                                <input type="radio" name="agreement_type" value="{{ $type }}"
                                       x-model="selectedType"
                                       class="hidden" required>
                                <div class="flex flex-col items-center gap-2">
                                    <div class="radio-indicator"></div>
                                    <div>
                                        <span class="block font-bold text-gray-800">{{ $type }}</span>
                                        <span class="text-xs text-gray-500">{{ $label }}</span>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('agreement_type')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agreement Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Agreement Title</label>
                        <input type="text" name="agreement_title" value="{{ old('agreement_title') }}"
                               placeholder="e.g., Industrial Training Collaboration"
                               class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('agreement_title') border-red-500 @enderror">
                        @error('agreement_title')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reference Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                            </div>
                            <input type="text" name="reference_no" value="{{ old('reference_no') }}"
                                   placeholder="e.g., MOU/2024/001"
                                   class="form-input w-full pl-12 pr-4 py-3 rounded-xl bg-white text-gray-900 @error('reference_no') border-red-500 @enderror">
                        </div>
                        @error('reference_no')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dates & Status Section -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="section-header">
                        <h2 class="text-lg font-bold text-gray-800">Dates & Status</h2>
                        <p class="text-sm text-gray-500 mt-1">Agreement timeline and current status</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Dates Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Start Date
                            </label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                End Date
                            </label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}"
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Signed Date
                            </label>
                            <input type="date" name="signed_date" value="{{ old('signed_date') }}"
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('signed_date') border-red-500 @enderror">
                            @error('signed_date')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="status" required
                                    class="form-select w-full px-4 py-3 rounded-xl bg-white text-gray-900 appearance-none @error('status') border-red-500 @enderror">
                                @foreach(\App\Models\CompanyAgreement::STATUS_OPTIONS as $status => $label)
                                <option value="{{ $status }}" {{ old('status', 'Draft') == $status ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('status')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Details Section -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="section-header">
                        <h2 class="text-lg font-bold text-gray-800">Additional Details</h2>
                        <p class="text-sm text-gray-500 mt-1">Faculty, programme, and contact information</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Faculty & Programme -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Faculty</label>
                            <input type="text" name="faculty" value="{{ old('faculty') }}"
                                   placeholder="e.g., Faculty of Technology"
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('faculty') border-red-500 @enderror">
                            @error('faculty')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Programme</label>
                            <input type="text" name="programme" value="{{ old('programme') }}"
                                   placeholder="e.g., Bachelor of Computer Science"
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 @error('programme') border-red-500 @enderror">
                            @error('programme')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Staff PIC -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Staff PIC Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text" name="staff_pic_name" value="{{ old('staff_pic_name') }}"
                                       placeholder="e.g., Dr. Ahmad Bin Ali"
                                       class="form-input w-full pl-12 pr-4 py-3 rounded-xl bg-white text-gray-900 @error('staff_pic_name') border-red-500 @enderror">
                            </div>
                            @error('staff_pic_name')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Staff PIC Phone</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="text" name="staff_pic_phone" value="{{ old('staff_pic_phone') }}"
                                       placeholder="e.g., +60123456789"
                                       class="form-input w-full pl-12 pr-4 py-3 rounded-xl bg-white text-gray-900 @error('staff_pic_phone') border-red-500 @enderror">
                            </div>
                            @error('staff_pic_phone')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Remarks</label>
                        <textarea name="remarks" rows="3"
                                  placeholder="Additional notes or comments..."
                                  class="form-input w-full px-4 py-3 rounded-xl bg-white text-gray-900 resize-none @error('remarks') border-red-500 @enderror">{{ old('remarks') }}</textarea>
                        @error('remarks')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Document Upload Section -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="section-header">
                        <h2 class="text-lg font-bold text-gray-800">Document Upload</h2>
                        <p class="text-sm text-gray-500 mt-1">Attach the signed agreement document</p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="file-upload-area rounded-xl p-8 text-center" id="dropZone">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-gray-700 font-medium mb-1">Drop your PDF file here, or <label for="document" class="text-purple-600 hover:text-purple-700 cursor-pointer underline">browse</label></p>
                            <p class="text-sm text-gray-500">Maximum file size: 10MB</p>
                            <input type="file" name="document" id="document" accept=".pdf" class="hidden">
                            <p id="fileName" class="text-sm text-purple-600 font-medium mt-3 hidden"></p>
                        </div>
                    </div>
                    @error('document')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Minute of Meeting (MoM) Section -->
            <div class="glass-card rounded-2xl overflow-hidden" x-data="{ momMentioned: {{ old('mom_mentioned') ? 'true' : 'false' }} }">
                <div class="p-6 border-b border-gray-100">
                    <div class="section-header">
                        <h2 class="text-lg font-bold text-gray-800">Minute of Meeting (MoM)</h2>
                        <p class="text-sm text-gray-500 mt-1">Record if this company was discussed in a meeting</p>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- MoM Checkbox -->
                    <div class="flex items-start gap-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="mom_mentioned" value="1"
                                   x-model="momMentioned"
                                   class="sr-only peer"
                                   {{ old('mom_mentioned') ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">Company mentioned in Minute of Meeting</p>
                            <p class="text-sm text-gray-500 mt-1">Enable this if the company was discussed and approved in a faculty/university meeting</p>
                        </div>
                    </div>

                    <!-- MoM Details (shown when checkbox is checked) -->
                    <div x-show="momMentioned" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6 pt-4 border-t border-gray-100">
                        <!-- MoM Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Meeting Date
                            </label>
                            <input type="date" name="mom_date" value="{{ old('mom_date') }}"
                                   class="form-input w-full md:w-1/2 px-4 py-3 rounded-xl bg-white text-gray-900 @error('mom_date') border-red-500 @enderror">
                            @error('mom_date')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- MoM Document Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                MoM Document (PDF)
                            </label>
                            <div class="file-upload-area rounded-xl p-6 text-center" id="momDropZone">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-1">Drop MoM document here, or <label for="mom_document" class="text-purple-600 hover:text-purple-700 cursor-pointer underline">browse</label></p>
                                    <p class="text-xs text-gray-400">PDF only, max 10MB</p>
                                    <input type="file" name="mom_document" id="mom_document" accept=".pdf" class="hidden">
                                    <p id="momFileName" class="text-sm text-purple-600 font-medium mt-2 hidden"></p>
                                </div>
                            </div>
                            @error('mom_document')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('admin.agreements.index') }}"
                   class="btn-outline px-6 py-3 rounded-xl text-gray-700 font-semibold">
                    Cancel
                </a>
                <button type="submit"
                        class="btn-gradient-primary px-8 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Agreement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // File upload handling
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('document');
    const fileNameDisplay = document.getElementById('fileName');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'application/pdf') {
            fileInput.files = files;
            showFileName(files[0].name);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            showFileName(e.target.files[0].name);
        }
    });

    function showFileName(name) {
        fileNameDisplay.textContent = 'Selected: ' + name;
        fileNameDisplay.classList.remove('hidden');
    }

    // MoM file upload handling
    const momDropZone = document.getElementById('momDropZone');
    const momFileInput = document.getElementById('mom_document');
    const momFileNameDisplay = document.getElementById('momFileName');

    if (momDropZone) {
        momDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            momDropZone.classList.add('dragover');
        });

        momDropZone.addEventListener('dragleave', () => {
            momDropZone.classList.remove('dragover');
        });

        momDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            momDropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type === 'application/pdf') {
                momFileInput.files = files;
                showMomFileName(files[0].name);
            }
        });

        momFileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                showMomFileName(e.target.files[0].name);
            }
        });
    }

    function showMomFileName(name) {
        momFileNameDisplay.textContent = 'Selected: ' + name;
        momFileNameDisplay.classList.remove('hidden');
    }
</script>
@endsection
