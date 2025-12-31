@extends('layouts.app')

@section('title', 'Edit Company')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold heading-umpsa">Edit Company</h1>
</div>

<div class="card-umpsa p-6">
    <form action="{{ route('admin.companies.update', $company) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="company_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Company Name</label>
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $company->company_name) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('company_name') border-red-500 @enderror" required>
            @error('company_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="category" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Industry Type</label>
            <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('category') border-red-500 @enderror" onchange="handleCategoryChange(this)">
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
            <div id="category_other_container" style="display: {{ old('category', $company->category) && !in_array(old('category', $company->category), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? 'block' : 'none' }};" class="mt-2">
                <input type="text" name="category_other" id="category_other" value="{{ old('category', $company->category) && !in_array(old('category', $company->category), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? old('category', $company->category) : '' }}" placeholder="Specify industry type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
            </div>
            @error('category')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="pic_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Person in Charge (PIC) Name</label>
            <input type="text" name="pic_name" id="pic_name" value="{{ old('pic_name', $company->pic_name) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('pic_name') border-red-500 @enderror">
            @error('pic_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="position" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Position</label>
            <select name="position" id="position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('position') border-red-500 @enderror" onchange="handlePositionChange(this)">
                <option value="">Select Position</option>
                <option value="HR" {{ old('position', $company->position) === 'HR' ? 'selected' : '' }}>HR</option>
                <option value="Manager" {{ old('position', $company->position) === 'Manager' ? 'selected' : '' }}>Manager</option>
                <option value="Director" {{ old('position', $company->position) === 'Director' ? 'selected' : '' }}>Director</option>
                <option value="Other" {{ old('position', $company->position) && !in_array(old('position', $company->position), ['HR', 'Manager', 'Director', '']) ? 'selected' : '' }}>Other</option>
            </select>
            <div id="position_other_container" style="display: {{ old('position', $company->position) && !in_array(old('position', $company->position), ['HR', 'Manager', 'Director', '']) ? 'block' : 'none' }};" class="mt-2">
                <input type="text" name="position_other" id="position_other" value="{{ old('position', $company->position) && !in_array(old('position', $company->position), ['HR', 'Manager', 'Director', '']) ? old('position', $company->position) : '' }}" placeholder="Specify position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
            </div>
            @error('position')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if($company->position === 'HR' || strtolower($company->position ?? '') === 'hr')
                <p class="mt-1 text-xs text-[#00A86B]">
                    <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    This company has {{ $company->industry_coaches_count ?? 0 }} Industry Coach(es) linked.
                </p>
            @endif
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="phone" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('phone') border-red-500 @enderror">
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="website" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Website</label>
            <div class="flex">
                <span class="inline-flex items-center px-3 text-sm text-gray-500 bg-gray-100 dark:bg-gray-600 dark:text-gray-300 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l-md">https://</span>
                <input type="text" name="website" id="website" value="{{ old('website', str_replace(['https://', 'http://'], '', $company->website ?? '')) }}" placeholder="www.example.com" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-r-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('website') border-red-500 @enderror">
            </div>
            @error('website')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="address" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Company Address</label>
            <textarea name="address" id="address" rows="5" placeholder="No. Street Name&#10;Postcode City&#10;State" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal placeholder-gray-400 dark:placeholder-gray-500 @error('address') border-red-500 @enderror">{{ old('address', $company->address) }}</textarea>
            @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This address will be used in official documents like SAL.</p>
        </div>

        <!-- MoU Template Variables Section -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-4 cursor-pointer" onclick="toggleMouSection()">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200">MoU Template Variables</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Default values for MoU document generation</p>
                </div>
                <svg id="mouChevron" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="mouSection" class="space-y-4 hidden">
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-4">
                    <p class="text-sm text-amber-700 dark:text-amber-300">
                        <strong>Note:</strong> These values will be used when generating MoU documents. You can also override them per-company in the Agreements tab.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Company Number -->
                    <div>
                        <label for="mou_company_number" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                            MoU Reference Number
                            <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${company_number}</code>
                        </label>
                        <input type="text" name="mou_company_number" id="mou_company_number" value="{{ old('mou_company_number', $company->mou_company_number) }}" placeholder="e.g., MOU/UMPSA/2025/001" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                    </div>

                    <!-- Company Shortname -->
                    <div>
                        <label for="mou_company_shortname" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                            Company Short Name
                            <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${company_shortname}</code>
                        </label>
                        <input type="text" name="mou_company_shortname" id="mou_company_shortname" value="{{ old('mou_company_shortname', $company->mou_company_shortname) }}" placeholder="e.g., TMJ" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                    </div>

                    <!-- Signed Behalf Name -->
                    <div>
                        <label for="mou_signed_behalf_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                            Signed Behalf Name
                            <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${signed_behalf_name}</code>
                        </label>
                        <input type="text" name="mou_signed_behalf_name" id="mou_signed_behalf_name" value="{{ old('mou_signed_behalf_name', $company->mou_signed_behalf_name) }}" placeholder="Higher position person name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                    </div>

                    <!-- Signed Behalf Position -->
                    <div>
                        <label for="mou_signed_behalf_position" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                            Signed Behalf Position
                            <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${signed_behalf_position}</code>
                        </label>
                        <input type="text" name="mou_signed_behalf_position" id="mou_signed_behalf_position" value="{{ old('mou_signed_behalf_position', $company->mou_signed_behalf_position) }}" placeholder="e.g., Chief Executive Officer" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                    </div>

                    <!-- Witness Name -->
                    <div>
                        <label for="mou_witness_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                            Witness Name
                            <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${witness_name}</code>
                        </label>
                        <input type="text" name="mou_witness_name" id="mou_witness_name" value="{{ old('mou_witness_name', $company->mou_witness_name) }}" placeholder="Witness full name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                    </div>

                    <!-- Witness Position -->
                    <div>
                        <label for="mou_witness_position" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                            Witness Position
                            <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${witness_position}</code>
                        </label>
                        <input type="text" name="mou_witness_position" id="mou_witness_position" value="{{ old('mou_witness_position', $company->mou_witness_position) }}" placeholder="e.g., General Manager" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                    </div>
                </div>

                <!-- UMPSA Staff Section -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">UMPSA Staff & Leadership</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- UMPSA Liaison Officer -->
                        <div>
                            <label for="mou_liaison_officer" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                                UMPSA Liaison Officer
                                <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${liaison_officer}</code>
                            </label>
                            <input type="text" name="mou_liaison_officer" id="mou_liaison_officer" value="{{ old('mou_liaison_officer', $company->mou_liaison_officer) }}" placeholder="Staff handling this MoU" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                        </div>

                        <!-- Vice Chancellor -->
                        <div>
                            <label for="mou_vc_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                                Vice Chancellor
                                <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${vc_name}</code>
                            </label>
                            <input type="text" name="mou_vc_name" id="mou_vc_name" value="{{ old('mou_vc_name', $company->mou_vc_name ?? 'Professor Dr. Yatimah Alias') }}" placeholder="Professor Dr. Yatimah Alias" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                        </div>

                        <!-- Deputy Vice Chancellor -->
                        <div>
                            <label for="mou_dvc_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">
                                Deputy VC (A&I)
                                <code class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded text-orange-600">${dvc_name}</code>
                            </label>
                            <input type="text" name="mou_dvc_name" id="mou_dvc_name" value="{{ old('mou_dvc_name', $company->mou_dvc_name ?? 'Professor Dato Ir. Ts. Dr. Ahmad Ziad Sulaiman') }}" placeholder="Professor Dato Ir. Ts. Dr. Ahmad Ziad Sulaiman" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 mt-6">
            <a href="{{ route('admin.companies.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">Cancel</a>
            <button type="submit" class="btn-umpsa-primary">Update Company</button>
        </div>
    </form>
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
