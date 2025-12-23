@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'My Profile')

@section('content')
<div class="min-h-screen bg-umpsa-soft-gray dark:bg-gray-900 -mx-4 sm:-mx-6 lg:-mx-10 -my-4 sm:-my-6 px-4 sm:px-6 lg:px-10 py-4 sm:py-6">
    <div class="max-w-5xl mx-auto">
        <!-- Top Profile Identity Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-4">
                <h1 class="text-xl sm:text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Student Profile</h1>
                <a href="{{ route('students.profile.edit', $student) }}" 
                   class="inline-flex items-center px-4 py-2 sm:py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors shadow-sm hover:shadow-md min-h-[44px] w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profile
                </a>
            </div>

            <div class="flex flex-col md:flex-row gap-4 sm:gap-6">
                <!-- Left: Profile Picture -->
                <div class="flex-shrink-0 flex justify-center md:justify-start">
                    <div class="relative">
                        @if($student->image_path)
                            <img src="{{ Storage::url($student->image_path) }}" 
                                 alt="{{ $student->name }}" 
                                 class="w-32 h-32 sm:w-40 sm:h-40 object-cover rounded-xl shadow-lg border-4 border-white dark:border-gray-700"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%23E6ECF2\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'14\'%3EImage%3C/text%3E%3C/svg%3E';">
                        @else
                            <div class="w-32 h-32 sm:w-40 sm:h-40 bg-gradient-to-br from-[#003A6C] to-[#0084C5] rounded-xl shadow-lg border-4 border-white dark:border-gray-700 flex items-center justify-center">
                                <svg class="w-16 h-16 sm:w-20 sm:h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <!-- Student Badge -->
                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#003A6C] text-white shadow-md">
                                Student
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Personal Information (Two Columns) -->
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Name</label>
                        <p class="text-base font-medium text-[#003A6C] dark:text-[#0084C5] mt-1">{{ $student->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Matric No</label>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-200 mt-1">{{ $student->matric_no }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Programme</label>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-200 mt-1">{{ $student->programme }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">CGPA</label>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-200 mt-1">
                            {{ $student->cgpa ? number_format($student->cgpa, 2) : 'Not Provided' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Group</label>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-200 mt-1">{{ $student->group->name ?? 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Company</label>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-200 mt-1">{{ $student->company->company_name ?? 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Mobile Phone</label>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-200 mt-1">
                            {{ $student->mobile_phone ?? 'Not Provided' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Email</label>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-200 mt-1">
                            {{ $student->user->email ?? auth()->user()->email }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500 dark:text-gray-400">Academic Advisor</label>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-200 mt-1">
                            {{ $student->academicAdvisor->name ?? 'Not Assigned' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Background Section (if available) -->
            @if($student->background)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 block">Background</label>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $student->background }}</p>
                </div>
            @endif
        </div>

        <!-- Course Assignments & Supervisory Roles Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Course Assignments & Supervisory Roles
            </h2>

            <!-- Group A: Supervisory Assignments -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Supervisors</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Industry Coach (IC) -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 rounded-lg bg-[#0084C5]/10 dark:bg-[#0084C5]/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.255M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Industry Coach</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-200 mt-1">
                                    {{ $student->industryCoach->name ?? 'Not Assigned' }}
                                </p>
                                @if($student->industryCoach)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $student->industryCoach->email ?? '' }}</p>
                                @endif
                            </div>
                            @if($student->industryCoach)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Academic Tutor (AT) - FYP -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 rounded-lg bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Academic Tutor (FYP)</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-200 mt-1">
                                    {{ $student->academicTutor->name ?? 'Not Assigned' }}
                                </p>
                                @if($student->academicTutor)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $student->academicTutor->email ?? '' }}</p>
                                @endif
                            </div>
                            @if($student->academicTutor)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Supervisor LI -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 rounded-lg bg-[#00AEEF]/10 dark:bg-[#00AEEF]/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#00AEEF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Supervisor LI</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-200 mt-1">
                                    @if(isset($courseAssignments) && $courseAssignments->has('Industrial Training') && $courseAssignments->get('Industrial Training')->lecturer)
                                        {{ $courseAssignments->get('Industrial Training')->lecturer->name }}
                                    @else
                                        Not Assigned
                                    @endif
                                </p>
                                @if(isset($courseAssignments) && $courseAssignments->has('Industrial Training') && $courseAssignments->get('Industrial Training')->lecturer)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $courseAssignments->get('Industrial Training')->lecturer->email ?? '' }}</p>
                                @endif
                            </div>
                            @if(isset($courseAssignments) && $courseAssignments->has('Industrial Training') && $courseAssignments->get('Industrial Training')->lecturer)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="my-6 border-t border-gray-200 dark:border-gray-700"></div>

            <!-- Group B: Academic Course Lecturers -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Course Lecturers</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- IP Lecturer -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 rounded-lg bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">IP Lecturer</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-200 mt-1">
                                    @if(isset($courseAssignments) && $courseAssignments->has('IP') && $courseAssignments->get('IP')->lecturer)
                                        {{ $courseAssignments->get('IP')->lecturer->name }}
                                    @else
                                        Not Assigned
                                    @endif
                                </p>
                                @if(isset($courseAssignments) && $courseAssignments->has('IP') && $courseAssignments->get('IP')->lecturer)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $courseAssignments->get('IP')->lecturer->email ?? '' }}</p>
                                @endif
                            </div>
                            @if(isset($courseAssignments) && $courseAssignments->has('IP') && $courseAssignments->get('IP')->lecturer)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- OSH Lecturer -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 rounded-lg bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">OSH Lecturer</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-200 mt-1">
                                    @if(isset($courseAssignments) && $courseAssignments->has('OSH') && $courseAssignments->get('OSH')->lecturer)
                                        {{ $courseAssignments->get('OSH')->lecturer->name }}
                                    @else
                                        Not Assigned
                                    @endif
                                </p>
                                @if(isset($courseAssignments) && $courseAssignments->has('OSH') && $courseAssignments->get('OSH')->lecturer)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $courseAssignments->get('OSH')->lecturer->email ?? '' }}</p>
                                @endif
                            </div>
                            @if(isset($courseAssignments) && $courseAssignments->has('OSH') && $courseAssignments->get('OSH')->lecturer)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- PPE Lecturer -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 rounded-lg bg-[#003A6C]/10 dark:bg-[#003A6C]/20 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">PPE Lecturer</label>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-200 mt-1">
                                    @if(isset($courseAssignments) && $courseAssignments->has('PPE') && $courseAssignments->get('PPE')->lecturer)
                                        {{ $courseAssignments->get('PPE')->lecturer->name }}
                                    @else
                                        Not Assigned
                                    @endif
                                </p>
                                @if(isset($courseAssignments) && $courseAssignments->has('PPE') && $courseAssignments->get('PPE')->lecturer)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $courseAssignments->get('PPE')->lecturer->email ?? '' }}</p>
                                @endif
                            </div>
                            @if(isset($courseAssignments) && $courseAssignments->has('PPE') && $courseAssignments->get('PPE')->lecturer)
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
