@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold heading-umpsa">Student Details</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.students.edit', $student) }}" class="btn-umpsa-primary">
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit
        </a>
        <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition-colors">
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Photo & Quick Info -->
    <div class="lg:col-span-1">
        <!-- Student Photo Card -->
        <div class="card-umpsa p-6 mb-6">
            <div class="flex flex-col items-center">
                @if($student->image_path)
                    <img src="{{ asset('storage/' . $student->image_path) }}" alt="{{ $student->name }}" class="w-48 h-48 object-cover rounded-lg border-4 border-umpsa-primary shadow-lg mb-4">
                @else
                    <div class="w-48 h-48 bg-gradient-to-br from-umpsa-primary to-umpsa-secondary rounded-lg border-4 border-umpsa-primary shadow-lg flex items-center justify-center mb-4">
                        <svg class="w-28 h-28 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
                <h3 class="text-xl font-bold text-umpsa-deep-blue dark:text-gray-200 text-center">{{ $student->name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $student->matric_no }}</p>
            </div>
        </div>

        <!-- Academic Info Card -->
        <div class="card-umpsa p-5 mb-6">
            <h3 class="text-md font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-umpsa-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Academic Details
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Programme</label>
                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ $student->programme ?? 'Not set' }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Group</label>
                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ $student->group->name ?? 'Not assigned' }}</p>
                </div>
                @if($student->cgpa)
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">CGPA</label>
                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ number_format($student->cgpa, 2) }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Placement Info Card -->
        <div class="card-umpsa p-5 mb-6">
            <h3 class="text-md font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-umpsa-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Placement
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Company</label>
                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ $student->company->company_name ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Academic Tutor (AT)</label>
                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ $student->academicTutor->name ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Industry Coach (IC)</label>
                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ $student->industryCoach->name ?? 'Not assigned' }}</p>
                </div>
                @if($student->academicAdvisor)
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Academic Advisor</label>
                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ $student->academicAdvisor->name }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Supervisors & Lecturers Card -->
        @if($student->courseAssignments->count() > 0)
        <div class="card-umpsa p-5">
            <h3 class="text-md font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2 text-umpsa-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Course Supervisors
            </h3>
            <div class="space-y-3">
                @foreach($student->courseAssignments as $assignment)
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                        @if($assignment->course_type === 'LI')
                            Supervisor LI
                        @elseif($assignment->course_type === 'PPE')
                            Lecturer PPE
                        @elseif($assignment->course_type === 'OSH')
                            Lecturer OSH
                        @elseif($assignment->course_type === 'IP')
                            Lecturer IP
                        @elseif($assignment->course_type === 'FYP')
                            Supervisor FYP
                        @else
                            {{ $assignment->course_type }}
                        @endif
                    </label>
                    <p class="text-sm text-gray-900 dark:text-gray-200">{{ $assignment->lecturer->name ?? 'Not assigned' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column - Detailed Information -->
    <div class="lg:col-span-2">
        <div class="grid grid-cols-1 gap-6">
            <!-- Personal Information Card -->
            <div class="card-umpsa p-6">
                <h3 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-4 flex items-center border-b-2 border-umpsa-teal pb-2">
                    <svg class="w-5 h-5 mr-2 text-umpsa-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Personal Information
                </h3>
                <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Full Name</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Matric Number</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->matric_no }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">IC Number</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->ic_number ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Mobile Phone</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->mobile_phone ?? 'Not provided' }}</p>
                    </div>
                    @if($student->home_address)
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Home Address</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->home_address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Emergency Contact Card -->
            <div class="card-umpsa p-6">
                <h3 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-4 flex items-center border-b-2 border-umpsa-teal pb-2">
                    <svg class="w-5 h-5 mr-2 text-umpsa-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    Emergency Contact
                </h3>
                <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Parent/Guardian Name</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->parent_name ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Parent/Guardian Phone</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->parent_phone_number ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Next of Kin</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->next_of_kin ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Next of Kin Phone</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->next_of_kin_phone_number ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Skills & Interests Card -->
            @if($student->skills || $student->interests || $student->background || $student->preferred_industry || $student->preferred_location)
            <div class="card-umpsa p-6">
                <h3 class="text-lg font-semibold text-umpsa-deep-blue dark:text-gray-200 mb-4 flex items-center border-b-2 border-umpsa-teal pb-2">
                    <svg class="w-5 h-5 mr-2 text-umpsa-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Skills & Career Preferences
                </h3>
                <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                    @if($student->skills)
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Skills</label>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach(is_array($student->skills) ? $student->skills : json_decode($student->skills, true) ?? [] as $skill)
                                <span class="px-3 py-1 bg-umpsa-teal/10 text-umpsa-teal dark:bg-umpsa-teal/20 dark:text-umpsa-accent text-xs font-medium rounded-full">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if($student->interests)
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Interests</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->interests }}</p>
                    </div>
                    @endif
                    @if($student->preferred_industry)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Preferred Industry</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->preferred_industry }}</p>
                    </div>
                    @endif
                    @if($student->preferred_location)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Preferred Location</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->preferred_location }}</p>
                    </div>
                    @endif
                    @if($student->background)
                    <div class="col-span-2">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Background</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200 mt-1">{{ $student->background }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Metadata Card -->
            <div class="card-umpsa p-5 bg-gray-50 dark:bg-gray-800/50">
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Created: {{ $student->created_at->format('d M Y, H:i') }}
                    </div>
                    @if($student->updated_at != $student->created_at)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Updated: {{ $student->updated_at->format('d M Y, H:i') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
