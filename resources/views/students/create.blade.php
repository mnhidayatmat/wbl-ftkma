@extends('layouts.app')

@section('title', 'Create Student')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold heading-umpsa">Create New Student</h1>
</div>

<div class="card-umpsa p-6">
    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Student Image Upload -->
        <div class="mb-6">
            <label for="image" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Student Photo</label>
            <input type="file" name="image" id="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('image') border-red-500 @enderror">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload student photo (JPG, PNG, max 2MB)</p>
            @error('image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="matric_no" class="block text-sm font-semibold text-umpsa-deep-blue mb-2">Matric Number</label>
            <input type="text" name="matric_no" id="matric_no" value="{{ old('matric_no') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('matric_no') border-red-500 @enderror" required>
            @error('matric_no')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="programme" class="block text-sm font-semibold text-umpsa-deep-blue mb-2">Programme</label>
            <input type="text" name="programme" id="programme" value="{{ old('programme') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('programme') border-red-500 @enderror" required>
            @error('programme')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="group_id" class="block text-sm font-semibold text-umpsa-deep-blue mb-2">Group</label>
            <select name="group_id" id="group_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('group_id') border-red-500 @enderror" required>
                <option value="">Select a group</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
            @error('group_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="company_id" class="block text-sm font-semibold text-umpsa-deep-blue mb-2">Company (Optional)</label>
            <select name="company_id" id="company_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('company_id') border-red-500 @enderror">
                <option value="">Select a company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                @endforeach
            </select>
            @error('company_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="cgpa" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">CGPA (Optional)</label>
            <input type="number" name="cgpa" id="cgpa" step="0.01" min="0" max="4" value="{{ old('cgpa') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('cgpa') border-red-500 @enderror">
            @error('cgpa')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="ic_number" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">IC Number (Optional)</label>
            <input type="text" name="ic_number" id="ic_number" value="{{ old('ic_number') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('ic_number') border-red-500 @enderror" placeholder="e.g., 990101-01-1234">
            @error('ic_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Emergency Contact Information -->
        <div class="mb-6 mt-8">
            <h2 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-4 border-b-2 border-umpsa-teal pb-2">Emergency Contact Information</h2>
        </div>

        <div class="mb-4">
            <label for="parent_name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Parent/Guardian Name (Optional)</label>
            <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('parent_name') border-red-500 @enderror" placeholder="e.g., Ahmad bin Abdullah">
            @error('parent_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="parent_phone_number" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Parent/Guardian Phone Number (Optional)</label>
            <input type="text" name="parent_phone_number" id="parent_phone_number" value="{{ old('parent_phone_number') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('parent_phone_number') border-red-500 @enderror" placeholder="e.g., +60123456789">
            @error('parent_phone_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="next_of_kin" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Next of Kin (Optional)</label>
            <input type="text" name="next_of_kin" id="next_of_kin" value="{{ old('next_of_kin') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('next_of_kin') border-red-500 @enderror" placeholder="e.g., Fatimah binti Ahmad (Sister)">
            @error('next_of_kin')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="next_of_kin_phone_number" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Next of Kin Phone Number (Optional)</label>
            <input type="text" name="next_of_kin_phone_number" id="next_of_kin_phone_number" value="{{ old('next_of_kin_phone_number') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('next_of_kin_phone_number') border-red-500 @enderror" placeholder="e.g., +60123456789">
            @error('next_of_kin_phone_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="home_address" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Home Address (Optional)</label>
            <textarea name="home_address" id="home_address" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('home_address') border-red-500 @enderror" placeholder="e.g., No. 123, Jalan ABC, Taman DEF, 26000 Kuantan, Pahang">{{ old('home_address') }}</textarea>
            @error('home_address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Skills & Career Preferences -->
        <div class="mb-6 mt-8">
            <h2 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-4 border-b-2 border-umpsa-teal pb-2">Skills & Career Preferences</h2>
        </div>

        <div class="mb-4">
            <label for="skills" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Skills (Optional)</label>
            <div x-data="skillsInput({{ json_encode(old('skills', [])) }})">
                <div class="flex flex-wrap gap-2 mb-2" x-show="skills.length > 0">
                    <template x-for="(skill, index) in skills" :key="index">
                        <span class="inline-flex items-center px-3 py-1 bg-umpsa-teal text-white rounded-full text-sm">
                            <span x-text="skill"></span>
                            <button type="button" @click="removeSkill(index)" class="ml-2 text-white hover:text-gray-200">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </span>
                    </template>
                </div>
                <div class="flex gap-2">
                    <input type="text" x-model="newSkill" @keydown.enter.prevent="addSkill" placeholder="Type a skill and press Enter" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal">
                    <button type="button" @click="addSkill" class="px-4 py-2 bg-umpsa-teal text-white rounded-md hover:bg-umpsa-deep-blue transition-colors">Add</button>
                </div>
                <input type="hidden" name="skills" x-model="skillsJson">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Example: Laravel, PHP, JavaScript, React, Python</p>
            </div>
            @error('skills')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="interests" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Career Interests (Optional)</label>
            <textarea name="interests" id="interests" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('interests') border-red-500 @enderror" placeholder="What are you interested in? (e.g., Web Development, Data Science, Mobile Apps)">{{ old('interests') }}</textarea>
            @error('interests')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="preferred_industry" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Preferred Industry (Optional)</label>
            <input type="text" name="preferred_industry" id="preferred_industry" value="{{ old('preferred_industry') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('preferred_industry') border-red-500 @enderror" placeholder="e.g., Technology, Finance, Healthcare">
            @error('preferred_industry')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="preferred_location" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Preferred Work Location (Optional)</label>
            <input type="text" name="preferred_location" id="preferred_location" value="{{ old('preferred_location') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('preferred_location') border-red-500 @enderror" placeholder="e.g., Kuala Lumpur, Penang, Remote">
            @error('preferred_location')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">Cancel</a>
            <button type="submit" class="btn-umpsa-primary">Create Student</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function skillsInput(initialSkills = []) {
    return {
        skills: initialSkills,
        newSkill: '',
        skillsJson: JSON.stringify(initialSkills),

        addSkill() {
            const skill = this.newSkill.trim();
            if (skill && !this.skills.includes(skill)) {
                this.skills.push(skill);
                this.updateJson();
                this.newSkill = '';
            }
        },

        removeSkill(index) {
            this.skills.splice(index, 1);
            this.updateJson();
        },

        updateJson() {
            this.skillsJson = JSON.stringify(this.skills);
        }
    }
}
</script>
@endpush
@endsection
