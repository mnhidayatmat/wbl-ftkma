@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold heading-umpsa">Student Details</h1>
    <div>
        <a href="{{ route('admin.students.edit', $student) }}" class="btn-umpsa-primary mr-2">Edit</a>
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition-colors">Back</a>
    </div>
</div>

<div class="card-umpsa p-6 mb-6">
    <h2 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-4 border-b-2 border-umpsa-teal pb-2">Personal Information</h2>

    <!-- Student Photo -->
    @if($student->image_path)
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('storage/' . $student->image_path) }}" alt="{{ $student->name }}" class="w-48 h-48 object-cover rounded-lg border-4 border-umpsa-primary shadow-lg">
        </div>
    @endif

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
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">IC Number</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->ic_number ?? 'Not provided' }}</p>
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

<div class="card-umpsa p-6">
    <h2 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-4 border-b-2 border-umpsa-teal pb-2">Emergency Contact Information</h2>
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Parent/Guardian Name</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->parent_name ?? 'Not provided' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Parent/Guardian Phone Number</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->parent_phone_number ?? 'Not provided' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Next of Kin</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->next_of_kin ?? 'Not provided' }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Next of Kin Phone Number</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->next_of_kin_phone_number ?? 'Not provided' }}</p>
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-semibold text-umpsa-deep-blue dark:text-gray-300 mb-1">Home Address</label>
            <p class="text-lg text-gray-900 dark:text-gray-200">{{ $student->home_address ?? 'Not provided' }}</p>
        </div>
    </div>
</div>
@endsection
