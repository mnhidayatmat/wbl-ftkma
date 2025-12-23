@extends('layouts.app')

@section('title', 'Create Company')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold heading-umpsa">Create New Company</h1>
</div>

<div class="card-umpsa p-6">
    <form action="{{ route('companies.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="company_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Company Name</label>
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('company_name') border-red-500 @enderror" required>
            @error('company_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="category" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Category</label>
            <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('category') border-red-500 @enderror" onchange="handleCategoryChange(this)">
                <option value="">Select Category</option>
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
                <input type="text" name="category_other" id="category_other" value="{{ old('category') && !in_array(old('category'), ['Oil and Gas', 'Design', 'Automotive', 'Manufacturing', 'Construction', 'Information Technology', 'Telecommunications', 'Healthcare', 'Education', 'Finance', 'Retail', 'Food and Beverage', '']) ? old('category') : '' }}" placeholder="Specify category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
            </div>
            @error('category')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="pic_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Person in Charge (PIC) Name</label>
            <input type="text" name="pic_name" id="pic_name" value="{{ old('pic_name') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('pic_name') border-red-500 @enderror" required>
            @error('pic_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="position" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Position</label>
            <select name="position" id="position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('position') border-red-500 @enderror" onchange="handlePositionChange(this)">
                <option value="">Select Position</option>
                <option value="HR" {{ old('position') === 'HR' ? 'selected' : '' }}>HR</option>
                <option value="Manager" {{ old('position') === 'Manager' ? 'selected' : '' }}>Manager</option>
                <option value="Director" {{ old('position') === 'Director' ? 'selected' : '' }}>Director</option>
                <option value="Supervisor" {{ old('position') === 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                <option value="Other" {{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', 'Supervisor', '']) ? 'selected' : '' }}>Other</option>
            </select>
            <div id="position_other_container" style="display: {{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', 'Supervisor', '']) ? 'block' : 'none' }};" class="mt-2">
                <input type="text" name="position_other" id="position_other" value="{{ old('position') && !in_array(old('position'), ['HR', 'Manager', 'Director', 'Supervisor', '']) ? old('position') : '' }}" placeholder="Specify position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
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

        <div class="mb-4">
            <label for="email" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('email') border-red-500 @enderror" required>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="phone" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('phone') border-red-500 @enderror" required>
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('companies.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">Cancel</a>
            <button type="submit" class="btn-umpsa-primary">Create Company</button>
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
</script>
@endsection
