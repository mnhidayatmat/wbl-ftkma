@extends('layouts.app')

@section('title', 'Add Agreement')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <a href="{{ route('admin.agreements.index') }}" class="text-[#0084C5] hover:underline text-sm mb-2 inline-block">
                ← Back to Agreements
            </a>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Add New Agreement</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Create a new MoU, MoA, or LOI record</p>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <form action="{{ route('admin.agreements.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <!-- Company Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Company <span class="text-red-500">*</span>
                    </label>
                    <select name="company_id" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('company_id') border-red-500 @enderror">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $selectedCompanyId) == $company->id ? 'selected' : '' }}>
                            {{ $company->company_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('company_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Agreement Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Agreement Type <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                        @foreach(\App\Models\CompanyAgreement::AGREEMENT_TYPES as $type => $label)
                        <label class="flex items-center">
                            <input type="radio" name="agreement_type" value="{{ $type }}" 
                                   {{ old('agreement_type') == $type ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#0084C5] border-gray-300 focus:ring-[#0084C5]" required>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $type }} ({{ $label }})</span>
                        </label>
                        @endforeach
                    </div>
                    @error('agreement_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Agreement Title -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Agreement Title
                    </label>
                    <input type="text" name="agreement_title" value="{{ old('agreement_title') }}"
                           placeholder="e.g., Industrial Training Collaboration"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('agreement_title') border-red-500 @enderror">
                    @error('agreement_title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reference Number -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Reference Number
                    </label>
                    <input type="text" name="reference_no" value="{{ old('reference_no') }}"
                           placeholder="e.g., MOU/2024/001"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('reference_no') border-red-500 @enderror">
                    @error('reference_no')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Start Date
                        </label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            End Date
                        </label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Signed Date
                        </label>
                        <input type="date" name="signed_date" value="{{ old('signed_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('signed_date') border-red-500 @enderror">
                        @error('signed_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('status') border-red-500 @enderror">
                        @foreach(\App\Models\CompanyAgreement::STATUS_OPTIONS as $status => $label)
                        <option value="{{ $status }}" {{ old('status', 'Draft') == $status ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Faculty & Programme -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Faculty
                        </label>
                        <input type="text" name="faculty" value="{{ old('faculty') }}"
                               placeholder="e.g., Faculty of Technology"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('faculty') border-red-500 @enderror">
                        @error('faculty')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Programme
                        </label>
                        <input type="text" name="programme" value="{{ old('programme') }}"
                               placeholder="e.g., Bachelor of Computer Science"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('programme') border-red-500 @enderror">
                        @error('programme')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Staff PIC -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Staff PIC Name
                        </label>
                        <input type="text" name="staff_pic_name" value="{{ old('staff_pic_name') }}"
                               placeholder="e.g., Dr. Ahmad Bin Ali"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('staff_pic_name') border-red-500 @enderror">
                        @error('staff_pic_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Staff PIC Phone Number
                        </label>
                        <input type="text" name="staff_pic_phone" value="{{ old('staff_pic_phone') }}"
                               placeholder="e.g., +60123456789"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('staff_pic_phone') border-red-500 @enderror">
                        @error('staff_pic_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Remarks -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Remarks
                    </label>
                    <textarea name="remarks" rows="3"
                              placeholder="Additional notes or comments..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('remarks') border-red-500 @enderror">{{ old('remarks') }}</textarea>
                    @error('remarks')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Upload -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Upload Document (PDF)
                    </label>
                    <input type="file" name="document" accept=".pdf"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('document') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max file size: 10MB</p>
                    @error('document')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.agreements.index') }}" 
                       class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Create Agreement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

