@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold heading-umpsa">Edit Student</h1>
</div>

<div class="card-umpsa p-6">
    <form action="{{ route('admin.students.update', $student) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="matric_no" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Matric Number</label>
            <input type="text" name="matric_no" id="matric_no" value="{{ old('matric_no', $student->matric_no) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('matric_no') border-red-500 @enderror" required>
            @error('matric_no')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="programme" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Programme</label>
            <input type="text" name="programme" id="programme" value="{{ old('programme', $student->programme) }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('programme') border-red-500 @enderror" required>
            @error('programme')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="group_id" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Group</label>
            <select name="group_id" id="group_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('group_id') border-red-500 @enderror" required>
                <option value="">Select a group</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id', $student->group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
            @error('group_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="company_id" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Company (Optional)</label>
            <select name="company_id" id="company_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('company_id') border-red-500 @enderror">
                <option value="">Select a company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $student->company_id) == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                @endforeach
            </select>
            @error('company_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @can('manage-settings')
        <div class="mb-4">
            <label for="at_id" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Academic Tutor (Optional)</label>
            <select name="at_id" id="at_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('at_id') border-red-500 @enderror">
                <option value="">No Academic Tutor Assigned</option>
                @foreach($lecturers as $lecturer)
                    <option value="{{ $lecturer->id }}" {{ old('at_id', $student->at_id) == $lecturer->id ? 'selected' : '' }}>{{ $lecturer->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Assign an Academic Tutor (Lecturer) to this student for AT evaluation</p>
            @error('at_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="ic_id" class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-2">Industry Coach (Optional)</label>
            <select name="ic_id" id="ic_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-umpsa-teal focus:border-umpsa-teal @error('ic_id') border-red-500 @enderror">
                <option value="">No Industry Coach Assigned</option>
                @foreach($industryCoaches as $ic)
                    <option value="{{ $ic->id }}" {{ old('ic_id', $student->ic_id) == $ic->id ? 'selected' : '' }}>{{ $ic->name }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Assign an Industry Coach to this student for IC evaluation</p>
            @error('ic_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        @endcan

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">Cancel</a>
            <button type="submit" class="btn-umpsa-primary">Update Student</button>
        </div>
    </form>
</div>
@endsection
