@extends('layouts.app')

@section('title', 'Student Placement Tracking')

@section('content')
<div>
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Student Placement Tracking</h1>
            <p class="text-gray-600 dark:text-gray-400">
                Manage and track student placement before and after WBL hiring.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Students</div>
                <div class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mt-2">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Resume Recommended</div>
                <div class="text-3xl font-bold text-green-600 mt-2">{{ $stats['resume_recommended'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-yellow-500">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending SAL Release</div>
                <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['pending_sal'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border-l-4 border-purple-500">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">SAL Released</div>
                <div class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['sal_released'] }}</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <form method="GET" action="{{ route('placement.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Group Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Group</label>
                    <select name="group" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }} {{ $group->isCompleted() ? '(Completed)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Group Status Filter (Admin & Coordinator only) -->
                @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Group Status</label>
                    <select name="group_status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('group_status') == 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="completed" {{ request('group_status') == 'completed' ? 'selected' : '' }}>Completed Only</option>
                    </select>
                </div>
                @endif

                <!-- Resume Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Resume Status</label>
                    <select name="resume_status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="NOT_STARTED" {{ request('resume_status') == 'NOT_STARTED' ? 'selected' : '' }}>Not Started</option>
                        <option value="PENDING" {{ request('resume_status') == 'PENDING' ? 'selected' : '' }}>Pending Review</option>
                        <option value="RECOMMENDED" {{ request('resume_status') == 'RECOMMENDED' ? 'selected' : '' }}>Resume Recommended</option>
                        <option value="REVISION_REQUIRED" {{ request('resume_status') == 'REVISION_REQUIRED' ? 'selected' : '' }}>Revision Required</option>
                    </select>
                </div>

                <!-- Placement Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Placement Status</label>
                    <select name="placement_status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="NOT_APPLIED" {{ request('placement_status') == 'NOT_APPLIED' ? 'selected' : '' }}>Not Applied</option>
                        <option value="SAL_RELEASED" {{ request('placement_status') == 'SAL_RELEASED' ? 'selected' : '' }}>SAL Released</option>
                        <option value="APPLIED" {{ request('placement_status') == 'APPLIED' ? 'selected' : '' }}>Applied</option>
                        <option value="INTERVIEWED" {{ request('placement_status') == 'INTERVIEWED' ? 'selected' : '' }}>Interviewed</option>
                        <option value="OFFER_RECEIVED" {{ request('placement_status') == 'OFFER_RECEIVED' ? 'selected' : '' }}>Offer Received</option>
                        <option value="ACCEPTED" {{ request('placement_status') == 'ACCEPTED' ? 'selected' : '' }}>Accepted</option>
                        <option value="CONFIRMED" {{ request('placement_status') == 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                        <option value="SCL_RELEASED" {{ request('placement_status') == 'SCL_RELEASED' ? 'selected' : '' }}>SCL Released</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name or Matric No" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Filter Buttons -->
                <div class="md:col-span-2 lg:col-span-4 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Apply Filters
                    </button>
                    <a href="{{ route('placement.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <!-- Desktop/Tablet Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C] dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Group</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Resume Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Interview Attended</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Offered Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Accepted Offer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">SAL Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $student)
                            @php
                                $resumeInspection = $student->resumeInspection;
                                $tracking = $student->placementTracking;
                                $isInCompletedGroup = $student->group && $student->group->isCompleted();
                                
                                // Determine resume status with proper badge colors
                                $resumeStatus = 'Not Started';
                                $resumeStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'; // Grey = Not started
                                if ($resumeInspection) {
                                    if (empty($resumeInspection->resume_file_path)) {
                                        $resumeStatus = 'Not Started';
                                        $resumeStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                    } elseif ($resumeInspection->status === 'PENDING') {
                                        $resumeStatus = 'Submitted';
                                        $resumeStatusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'; // Blue = In progress
                                    } elseif ($resumeInspection->status === 'PASSED') {
                                        $resumeStatus = 'Resume Recommended';
                                        $resumeStatusColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'; // Green = Completed
                                    } elseif ($resumeInspection->status === 'REVISION_REQUIRED') {
                                        $resumeStatus = 'Resume Recommended'; // Keep as recommended for tracking
                                        $resumeStatusColor = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
                                    } elseif ($resumeInspection->status === 'FAILED') {
                                        $resumeStatus = 'Rejected';
                                        $resumeStatusColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'; // Red = Problem/Rejected
                                    }
                                }
                                
                                // Interview attended status
                                $interviewedCompanies = $tracking ? $tracking->companyApplications->filter(function($app) {
                                    return $app->interviewed === true;
                                }) : collect();
                                $hasInterviewed = $interviewedCompanies->count() > 0;
                                
                                // Accepted offer status
                                $acceptedOfferStatus = 'No Offer';
                                $acceptedOfferColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                $acceptedCompanyName = null;
                                if ($tracking) {
                                    if ($tracking->status === 'ACCEPTED' || ($tracking->status === 'ACCEPTED' && $tracking->confirmation_proof_path) || $tracking->status === 'CONFIRMED') {
                                        if ($student->company) {
                                            $acceptedOfferStatus = $student->company->company_name;
                                            $acceptedOfferColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                            $acceptedCompanyName = $student->company->company_name;
                                        } else {
                                            $acceptedOfferStatus = 'Accepted';
                                            $acceptedOfferColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                        }
                                    } elseif ($tracking->status === 'OFFER_RECEIVED') {
                                        $acceptedOfferStatus = 'Pending Decision';
                                        $acceptedOfferColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                    } elseif ($tracking->status === 'INTERVIEWED') {
                                        $acceptedOfferStatus = 'No Offer';
                                        $acceptedOfferColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                    }
                                }
                                
                                // SAL Status
                                $salStatus = 'Not Released';
                                $salStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                $salDate = null;
                                if ($tracking && $tracking->sal_file_path) {
                                    $salStatus = 'Released';
                                    $salStatusColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                    $salDate = $tracking->sal_released_at;
                                } elseif ($resumeInspection && $resumeInspection->status === 'PASSED' && (!$tracking || $tracking->status === 'NOT_APPLIED')) {
                                    $salStatus = 'Pending';
                                    $salStatusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                }
                                
                                // Check if student is eligible for SAL release
                                $canReleaseSal = $resumeInspection && 
                                                $resumeInspection->status === 'PASSED' && 
                                                $tracking && 
                                                $tracking->status === 'NOT_APPLIED';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $isInCompletedGroup ? 'opacity-60 bg-gray-50 dark:bg-gray-800/50' : '' }}">
                                <!-- Student Column -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $student->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 hidden lg:block">{{ $student->matric_no }}</div>
                                </td>
                                
                                <!-- Group Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($student->group)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $student->group->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                
                                <!-- Resume Status Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $resumeStatusColor }}">
                                        {{ $resumeStatus }}
                                    </span>
                                </td>
                                
                                <!-- Interview Attended Column -->
                                <td class="px-6 py-4">
                                    @if($hasInterviewed)
                                        <div class="flex flex-col gap-1">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Yes
                                            </span>
                                            @if($interviewedCompanies->count() > 1)
                                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ $interviewedCompanies->count() }} interviews
                                                </span>
                                            @elseif($interviewedCompanies->first() && $interviewedCompanies->first()->interviewed_at)
                                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                                    {{ $interviewedCompanies->first()->interviewed_at->format('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            No
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Offered Company Column -->
                                <td class="px-6 py-4">
                                    @if($student->company)
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $student->company->company_name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">–</span>
                                    @endif
                                </td>
                                
                                <!-- Accepted Offer Column -->
                                <td class="px-6 py-4">
                                    @if($acceptedCompanyName)
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $acceptedCompanyName }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $acceptedOfferColor }}">
                                            {{ $acceptedOfferStatus }}
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- SAL Status Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $salStatusColor }}">
                                            {{ $salStatus }}
                                        </span>
                                        @if($salDate)
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $salDate->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Actions Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-2">
                                        <!-- Primary Action: View Steps -->
                                        @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                                            <a href="{{ route('placement.student.view', $student) }}" 
                                               class="px-3 py-1.5 bg-[#0084C5] hover:bg-[#003A6C] text-white text-xs font-semibold rounded-lg transition-colors text-center">
                                                View Steps
                                            </a>
                                        @endif
                                        
                                        <!-- Secondary Action: View Student Profile Details -->
                                        <a href="{{ route('students.show', $student) }}" 
                                           class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-xs font-semibold rounded-lg transition-colors text-center">
                                            View Details
                                        </a>
                                        
                                        <!-- Admin-only Actions -->
                                        @if(!$isInCompletedGroup && (auth()->user()->isAdmin() || auth()->user()->isCoordinator()))
                                            @if($canReleaseSal)
                                                <form action="{{ route('placement.student.sal.release', $student) }}" method="POST" class="inline" onsubmit="return confirm('Release SAL for {{ $student->name }}?');">
                                                    @csrf
                                                    <button type="submit" class="w-full px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                        Release SAL
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($tracking && $tracking->sal_file_path)
                                                <a href="{{ route('placement.student.sal.download', $student) }}" 
                                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors text-center">
                                                    Download SAL
                                                </a>
                                            @endif
                                        @endif
                                        
                                        @if($isInCompletedGroup)
                                            <span class="px-3 py-1.5 bg-gray-300 text-gray-500 text-xs font-semibold rounded-lg text-center cursor-not-allowed" title="Completed cohort - actions disabled">
                                                Read Only
                                            </span>
                                        @endif
                                        
                                        <!-- Reset Action (Admin only) -->
                                        @if(auth()->user()->isAdmin() && !$isInCompletedGroup)
                                            <form action="{{ route('placement.student.reset', $student) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reset placement tracking for {{ $student->name }}? This will delete all SAL/SCL files, company applications, and reset status to initial state. This action cannot be undone.');">
                                                @csrf
                                                <button type="submit" class="w-full px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg transition-colors" title="Reset Placement Tracking">
                                                    Reset
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No students found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($students as $student)
                    @php
                        $resumeInspection = $student->resumeInspection;
                        $tracking = $student->placementTracking;
                        $isInCompletedGroup = $student->group && $student->group->isCompleted();
                        
                        // Determine resume status with proper badge colors
                        $resumeStatus = 'Not Started';
                        $resumeStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        if ($resumeInspection) {
                            if (empty($resumeInspection->resume_file_path)) {
                                $resumeStatus = 'Not Started';
                                $resumeStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            } elseif ($resumeInspection->status === 'PENDING') {
                                $resumeStatus = 'Submitted';
                                $resumeStatusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                            } elseif ($resumeInspection->status === 'PASSED') {
                                $resumeStatus = 'Resume Recommended';
                                $resumeStatusColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                            } elseif ($resumeInspection->status === 'REVISION_REQUIRED') {
                                $resumeStatus = 'Resume Recommended';
                                $resumeStatusColor = 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
                            } elseif ($resumeInspection->status === 'FAILED') {
                                $resumeStatus = 'Rejected';
                                $resumeStatusColor = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                            }
                        }
                        
                        // Interview attended status
                        $interviewedCompanies = $tracking ? $tracking->companyApplications->filter(function($app) {
                            return $app->interviewed === true;
                        }) : collect();
                        $hasInterviewed = $interviewedCompanies->count() > 0;
                        
                        // Accepted offer status
                        $acceptedOfferStatus = 'No Offer';
                        $acceptedOfferColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        $acceptedCompanyName = null;
                        if ($tracking) {
                            if ($tracking->status === 'ACCEPTED' || ($tracking->status === 'ACCEPTED' && $tracking->confirmation_proof_path) || $tracking->status === 'CONFIRMED') {
                                if ($student->company) {
                                    $acceptedOfferStatus = $student->company->company_name;
                                    $acceptedOfferColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                    $acceptedCompanyName = $student->company->company_name;
                                } else {
                                    $acceptedOfferStatus = 'Accepted';
                                    $acceptedOfferColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                }
                            } elseif ($tracking->status === 'OFFER_RECEIVED') {
                                $acceptedOfferStatus = 'Pending Decision';
                                $acceptedOfferColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                            } elseif ($tracking->status === 'INTERVIEWED') {
                                $acceptedOfferStatus = 'No Offer';
                                $acceptedOfferColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            }
                        }
                        
                        // SAL Status
                        $salStatus = 'Not Released';
                        $salStatusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        $salDate = null;
                        if ($tracking && $tracking->sal_file_path) {
                            $salStatus = 'Released';
                            $salStatusColor = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                            $salDate = $tracking->sal_released_at;
                        } elseif ($resumeInspection && $resumeInspection->status === 'PASSED' && (!$tracking || $tracking->status === 'NOT_APPLIED')) {
                            $salStatus = 'Pending';
                            $salStatusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                        }
                        
                        // Check if student is eligible for SAL release
                        $canReleaseSal = $resumeInspection && 
                                        $resumeInspection->status === 'PASSED' && 
                                        $tracking && 
                                        $tracking->status === 'NOT_APPLIED';
                    @endphp
                    <div class="p-4 {{ $isInCompletedGroup ? 'opacity-60 bg-gray-50 dark:bg-gray-800/50' : '' }}">
                        <!-- Student + Group Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg">{{ $student->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $student->matric_no }}</div>
                            </div>
                            @if($student->group)
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $student->group->name }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Status Flow Vertically -->
                        <div class="space-y-3 mb-4">
                            <!-- Resume Status -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Resume Status:</span>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $resumeStatusColor }}">
                                    {{ $resumeStatus }}
                                </span>
                            </div>
                            
                            <!-- Interview Attended -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Interview Attended:</span>
                                @if($hasInterviewed)
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Yes
                                        </span>
                                        @if($interviewedCompanies->count() > 1)
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $interviewedCompanies->count() }} interviews
                                            </span>
                                        @elseif($interviewedCompanies->first() && $interviewedCompanies->first()->interviewed_at)
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $interviewedCompanies->first()->interviewed_at->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        No
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Offered Company -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Offered Company:</span>
                                @if($student->company)
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $student->company->company_name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">–</span>
                                @endif
                            </div>
                            
                            <!-- Accepted Offer -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Accepted Offer:</span>
                                @if($acceptedCompanyName)
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $acceptedCompanyName }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $acceptedOfferColor }}">
                                        {{ $acceptedOfferStatus }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- SAL Status -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">SAL Status:</span>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $salStatusColor }}">
                                        {{ $salStatus }}
                                    </span>
                                    @if($salDate)
                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $salDate->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions at Bottom -->
                        <div class="flex flex-col gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                            @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                                <a href="{{ route('placement.student.view', $student) }}" 
                                   class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors text-center">
                                    View Steps
                                </a>
                            @endif
                            
                            <!-- View Student Profile Details -->
                            <a href="{{ route('students.show', $student) }}" 
                               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg transition-colors text-center">
                                View Details
                            </a>
                            
                            @if(!$isInCompletedGroup && (auth()->user()->isAdmin() || auth()->user()->isCoordinator()))
                                @if($canReleaseSal)
                                    <form action="{{ route('placement.student.sal.release', $student) }}" method="POST" onsubmit="return confirm('Release SAL for {{ $student->name }}?');">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                            Release SAL
                                        </button>
                                    </form>
                                @endif
                                
                                @if($tracking && $tracking->sal_file_path)
                                    <a href="{{ route('placement.student.sal.download', $student) }}" 
                                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors text-center">
                                        Download SAL
                                    </a>
                                @endif
                            @endif
                            
                            @if($isInCompletedGroup)
                                <span class="px-4 py-2 bg-gray-300 text-gray-500 text-sm font-semibold rounded-lg text-center cursor-not-allowed">
                                    Read Only
                                </span>
                            @endif
                            
                            <!-- Reset Action (Admin only) -->
                            @if(auth()->user()->isAdmin() && !$isInCompletedGroup)
                                <form action="{{ route('placement.student.reset', $student) }}" method="POST" onsubmit="return confirm('Are you sure you want to reset placement tracking for {{ $student->name }}? This will delete all SAL/SCL files, company applications, and reset status to initial state. This action cannot be undone.');">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                        Reset Placement
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                        No students found matching your filters.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Bulk Actions (Admin & Coordinator only) -->
        @if((auth()->user()->isAdmin() || auth()->user()->isCoordinator()) && $stats['pending_sal'] > 0)
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Bulk Actions</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Release SAL for all students with "Resume Recommended" status who haven't received SAL yet.
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $stats['pending_sal'] }} student(s) eligible for SAL release.
                    </p>
                </div>
                <form action="{{ route('placement.bulk.sal.release') }}" method="POST" onsubmit="return confirm('Release SAL for all eligible students? This action cannot be undone.');">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                        Bulk Release SAL
                    </button>
                </form>
            </div>
        </div>
        @endif
</div>
@endsection
