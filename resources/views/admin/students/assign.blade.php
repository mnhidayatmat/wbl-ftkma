@extends('layouts.app')

@section('title', 'Assign Students to Courses')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-10">
        <div class="mb-6">
            <h1 class="text-2xl font-bold heading-umpsa">Student Course Assignment</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Assign students to lecturers for different courses</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Course Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-4 overflow-x-auto" aria-label="Tabs">
                @foreach($courses as $code => $name)
                    <a href="{{ route('admin.students.assign', ['course' => $code]) }}" 
                       class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm {{ ($activeCourse ?? 'FYP') === $code ? 'border-[#0084C5] text-[#0084C5]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        {{ $name }}
                    </a>
                @endforeach
            </nav>
        </div>

        <!-- Course Assignment Content -->
        @include('admin.students.assign-course', ['courseCode' => $activeCourse ?? 'FYP', 'courseName' => $courses[$activeCourse ?? 'FYP']])
    </div>
</div>
@endsection
