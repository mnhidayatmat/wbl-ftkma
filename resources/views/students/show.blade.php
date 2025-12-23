@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold heading-umpsa">Student Details</h1>
    <div>
        <a href="{{ route('students.edit', $student) }}" class="btn-umpsa-primary mr-2">Edit</a>
        <a href="{{ route('students.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition-colors">Back</a>
    </div>
</div>

<div class="card-umpsa p-6">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Name</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->name }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Matric Number</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->matric_no }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Programme</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->programme }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Group</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->group->name ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Company</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->company->company_name ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Academic Tutor (AT)</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->academicTutor->name ?? 'Not Assigned' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Industry Coach (IC)</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->industryCoach->name ?? 'Not Assigned' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Created At</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
</div>
@endsection
