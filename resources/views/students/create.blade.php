@extends('layouts.app')

@section('title', 'Create Student')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold heading-umpsa">Create New Student</h1>
</div>

<div class="card-umpsa p-6">
    <form action="{{ route('admin.students.store') }}" method="POST">
        @csrf

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

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">Cancel</a>
            <button type="submit" class="btn-umpsa-primary">Create Student</button>
        </div>
    </form>
</div>
@endsection
