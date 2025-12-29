@extends('layouts.app')

@section('title', 'Add Company & Agreement')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <a href="{{ route('admin.companies.index') }}" class="text-[#0084C5] hover:underline text-sm mb-2 inline-block">
                ← Back to Companies
            </a>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Add Company & Agreement</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Create a new company with its agreement (MoU/MoA/LOI)</p>
        </div>

        <!-- Global Error Display -->
        @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
            <h3 class="font-semibold mb-2">Please correct the following errors:</h3>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- SECTION 1: Company Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] border-b border-gray-200 dark:border-gray-700 pb-3 mb-6">Company Information</h2>

                <!-- Duplicate Detection Alert Placeholder -->
                <div id="duplicate-alert" class="hidden mb-4"></div>

                <!-- Company Name -->
                <div class="mb-4">
                    <label for="company_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Company Name
                    </label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('company_name') border-red-500 @enderror"
                           autocomplete="off">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Search Results Dropdown -->
                    <div id="search-results" class="hidden mt-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-64 overflow-y-auto">
                        <!-- Results will be populated here via JavaScript -->
                    </div>

                    <!-- Info message -->
                    <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Start typing to check for existing companies
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Industry Type -->
                    <div class="mb-4">
                        <label for="category" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Industry Type</label>
                        <select name="category" id="category" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('category') border-red-500 @enderror" onchange="handleCategoryChange(this)">
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
                            <input type="text" name="category_other" id="category_other" value="{{ old('category') && !in_array(old('category'), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? old('category') : '' }}" placeholder="Specify industry type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                        </div>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- PIC Name -->
                    <div class="mb-4">
                        <label for="pic_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Person in Charge (PIC) Name
                        </label>
                        <input type="text" name="pic_name" id="pic_name" value="{{ old('pic_name') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('pic_name') border-red-500 @enderror">
                        @error('pic_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Position -->
                    <div class="mb-4">
                        <label for="position" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                        <select name="position" id="position" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('position') border-red-500 @enderror" onchange="handlePositionChange(this)">
                            <option value="">Select Position</option>
                            <option value="HR" {{ old('position') === 'HR' ? 'selected' : '' }}>HR</option>
                            <option value="Manager" {{ old('position') === 'Manager' ? 'selected' : '' }}>Manager</option>
                            <option value="Director" {{ old('position') === 'Director' ? 'selected' : '' }}>Director</option>
                            <option value="Supervisor" {{ old('position') === 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="Other" {{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', 'Supervisor', '']) ? 'selected' : '' }}>Other</option>
                        </select>
                        <div id="position_other_container" style="display: {{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', 'Supervisor', '']) ? 'block' : 'none' }};" class="mt-2">
                            <input type="text" name="position_other" id="position_other" value="{{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', 'Supervisor', '']) ? old('position') : '' }}" placeholder="Specify position" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white">
                        </div>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            If position is HR, IC users from the same company will be automatically linked.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Phone
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea name="address" id="address" rows="2"
                              placeholder="Company address"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Website -->
                <div class="mb-4">
                    <label for="website" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Website</label>
                    <input type="url" name="website" id="website" value="{{ old('website') }}"
                           placeholder="https://example.com"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('website') border-red-500 @enderror">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- SECTION 2: Agreement Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] border-b border-gray-200 dark:border-gray-700 pb-3 mb-6">Agreement Details</h2>

                <!-- Agreement Type -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Agreement Type <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-6">
                        <label class="flex items-center">
                            <input type="radio" name="agreement_type" value="MoU"
                                   {{ old('agreement_type', 'MoU') == 'MoU' ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#0084C5] border-gray-300 focus:ring-[#0084C5]" required>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">MoU (Memorandum of Understanding)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="agreement_type" value="MoA"
                                   {{ old('agreement_type') == 'MoA' ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#0084C5] border-gray-300 focus:ring-[#0084C5]" required>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">MoA (Memorandum of Agreement)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="agreement_type" value="LOI"
                                   {{ old('agreement_type') == 'LOI' ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#0084C5] border-gray-300 focus:ring-[#0084C5]" required>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">LOI (Letter of Intent)</span>
                        </label>
                    </div>
                    @error('agreement_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Agreement Title -->
                    <div class="mb-4">
                        <label for="agreement_title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Agreement Title</label>
                        <input type="text" name="agreement_title" id="agreement_title" value="{{ old('agreement_title') }}"
                               placeholder="e.g., Industrial Training Collaboration"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('agreement_title') border-red-500 @enderror">
                        @error('agreement_title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div class="mb-4">
                        <label for="reference_no" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference Number</label>
                        <input type="text" name="reference_no" id="reference_no" value="{{ old('reference_no') }}"
                               placeholder="e.g., MOU/2024/001"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('reference_no') border-red-500 @enderror">
                        @error('reference_no')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="signed_date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Signed Date</label>
                        <input type="date" name="signed_date" id="signed_date" value="{{ old('signed_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('signed_date') border-red-500 @enderror">
                        @error('signed_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('status') border-red-500 @enderror">
                        <option value="Not Started" {{ old('status', 'Not Started') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                        <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Expired" {{ old('status') == 'Expired' ? 'selected' : '' }}>Expired</option>
                        <option value="Terminated" {{ old('status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Faculty -->
                    <div class="mb-4">
                        <label for="faculty" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Faculty</label>
                        <input type="text" name="faculty" id="faculty" value="{{ old('faculty') }}"
                               placeholder="e.g., Faculty of Technology"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('faculty') border-red-500 @enderror">
                        @error('faculty')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Programme -->
                    <div class="mb-4">
                        <label for="programme" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Programme</label>
                        <input type="text" name="programme" id="programme" value="{{ old('programme') }}"
                               placeholder="e.g., Bachelor of Computer Science"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('programme') border-red-500 @enderror">
                        @error('programme')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Staff PIC -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="staff_pic_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Staff PIC Name</label>
                        <input type="text" name="staff_pic_name" id="staff_pic_name" value="{{ old('staff_pic_name') }}"
                               placeholder="e.g., Dr. Ahmad Bin Ali"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('staff_pic_name') border-red-500 @enderror">
                        @error('staff_pic_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="staff_pic_phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Staff PIC Phone Number</label>
                        <input type="text" name="staff_pic_phone" id="staff_pic_phone" value="{{ old('staff_pic_phone') }}"
                               placeholder="e.g., +60123456789"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('staff_pic_phone') border-red-500 @enderror">
                        @error('staff_pic_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Remarks -->
                <div class="mb-4">
                    <label for="remarks" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="3"
                              placeholder="Additional notes or comments..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('remarks') border-red-500 @enderror">{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Upload -->
                <div class="mb-4">
                    <label for="document" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Upload Document (PDF)</label>
                    <input type="file" name="document" id="document" accept=".pdf"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white @error('document') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max file size: 10MB</p>
                    @error('document')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.companies.index') }}"
                   class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
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

    // Company name duplicate detection (AJAX search)
    let searchTimeout;
    const companyNameInput = document.getElementById('company_name');
    const searchResults = document.getElementById('search-results');

    companyNameInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // Hide results if query is too short
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
            return;
        }

        // Debounce the search request
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('admin.companies.search') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        searchResults.innerHTML = `
                            <div class="p-3 text-sm text-green-600 dark:text-green-400">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                No existing companies found. Safe to proceed.
                            </div>
                        `;
                        searchResults.classList.remove('hidden');
                    } else {
                        let html = `
                            <div class="p-2 bg-yellow-50 dark:bg-yellow-900 border-b border-yellow-200 dark:border-yellow-700">
                                <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200">
                                    ⚠ Found ${data.length} similar ${data.length === 1 ? 'company' : 'companies'}. Please verify before creating:
                                </p>
                            </div>
                        `;

                        data.forEach(company => {
                            html += `
                                <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                                    <div class="font-semibold text-sm text-gray-900 dark:text-white">${company.company_name}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                        <div>PIC: ${company.pic_name || 'N/A'}</div>
                                        <div>Email: ${company.email || 'N/A'}</div>
                                        <div>Phone: ${company.phone || 'N/A'}</div>
                                    </div>
                                    <a href="{{ route('admin.companies.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 inline-block">
                                        View Company →
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
        }, 300); // 300ms debounce
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(event) {
        if (!companyNameInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // Show results again when focusing on input if it has content
    companyNameInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && searchResults.innerHTML) {
            searchResults.classList.remove('hidden');
        }
    });
</script>
@endsection
