@extends('layouts.app')

@section('title', isset($isStudentView) && $isStudentView ? 'My Placement Tracking' : 'Student Placement Tracking - ' . $student->name)

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- HEADER --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">
                {{ isset($isStudentView) && $isStudentView ? 'My Placement Tracking' : 'Student Placement Tracking' }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                {{ isset($isStudentView) && $isStudentView ? 'Track your industrial placement status and progress' : $student->name . ' (' . $student->matric_no . ')' }}
            </p>
        </div>
        @if(!isset($isStudentView) || !$isStudentView)
            <a href="{{ route('placement.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg transition-colors">
                ← Back to Students
            </a>
        @endif
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-3 rounded-lg">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded-lg">
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    @if(isset($isInCompletedGroup) && $isInCompletedGroup)
        <div class="mb-6 bg-gray-100 dark:bg-gray-700 border-l-4 border-gray-500 p-3 rounded-lg">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">WBL Group Completed - Read-only access</p>
        </div>
    @endif

    @if(!$canApply)
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded-lg">
            <p class="text-sm font-semibold text-red-800 dark:text-red-200">Resume Inspection Required</p>
            <p class="text-xs text-red-700 dark:text-red-300 mt-1">
                <a href="{{ route('student.resume.index') }}" class="underline">Go to Resume Inspection</a> to complete before applying
            </p>
        </div>
    @endif

    {{-- QUICK STATS GRID --}}
    @php
        $totalApplications = $tracking->companyApplications ? $tracking->companyApplications->count() : 0;
        $interviewedCount = $tracking->companyApplications ? $tracking->companyApplications->where('status', 'interviewed')->count() : 0;
        $offersCount = $tracking->status === 'OFFER_RECEIVED' || $tracking->status === 'ACCEPTED' || $tracking->status === 'SCL_RELEASED' ? 1 : 0;
        $progressPercentage = (($currentStep - 1) / (count($statuses) - 1)) * 100;
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Applications --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm opacity-90">Applications</span>
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <p class="text-3xl font-bold">{{ $totalApplications }}</p>
            <p class="text-xs opacity-75 mt-1">Companies applied</p>
        </div>

        {{-- Interviews --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm opacity-90">Interviews</span>
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <p class="text-3xl font-bold">{{ $interviewedCount }}</p>
            <p class="text-xs opacity-75 mt-1">Completed</p>
        </div>

        {{-- Offers --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm opacity-90">Offers</span>
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-3xl font-bold">{{ $offersCount }}</p>
            <p class="text-xs opacity-75 mt-1">Received</p>
        </div>

        {{-- Progress --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-5 text-white shadow-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm opacity-90">Progress</span>
                <svg class="w-5 h-5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <p class="text-3xl font-bold">{{ round($progressPercentage) }}%</p>
            <p class="text-xs opacity-75 mt-1">Complete</p>
        </div>
    </div>

    {{-- MAIN GRID LAYOUT --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT COLUMN (2/3 width) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- COMPACT HORIZONTAL TIMELINE --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
                <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Placement Journey</h3>

                {{-- Mobile: Vertical Timeline --}}
                <div class="block md:hidden space-y-3">
                    @foreach($statuses as $stepNum => $statusInfo)
                        @php
                            $isActive = $stepNum === $currentStep;
                            $isCompleted = $stepNum < $currentStep;
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                {{ $isCompleted ? 'bg-green-500 text-white' : ($isActive ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500') }}">
                                {{ $isCompleted ? '✓' : $stepNum }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $statusInfo['label'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Desktop: Horizontal Timeline --}}
                <div class="hidden md:flex items-center justify-between relative">
                    {{-- Progress Line --}}
                    <div class="absolute top-6 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-700" style="z-index: 0;"></div>
                    <div class="absolute top-6 left-0 h-1 bg-blue-500 transition-all duration-500"
                         style="width: {{ $progressPercentage }}%; z-index: 1;"></div>

                    @foreach($statuses as $stepNum => $statusInfo)
                        @php
                            $isActive = $stepNum === $currentStep;
                            $isCompleted = $stepNum < $currentStep;
                        @endphp
                        <div class="flex flex-col items-center relative z-10" style="flex: 1;">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-bold mb-2 shadow-md transition-all
                                {{ $isCompleted ? 'bg-green-500 text-white' : ($isActive ? 'bg-blue-500 text-white ring-4 ring-blue-200' : 'bg-gray-200 dark:bg-gray-700 text-gray-500') }}">
                                {{ $isCompleted ? '✓' : $stepNum }}
                            </div>
                            <p class="text-xs text-center font-medium {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $statusInfo['label'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- COMPANY APPLICATIONS GRID --}}
            @if($tracking->status === 'APPLIED' || $tracking->status === 'INTERVIEWED')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5]">Company Applications</h3>
                    @if((!isset($readOnly) || !$readOnly) && $canApply)
                        <button onclick="document.getElementById('addCompanyForm').classList.toggle('hidden')"
                                class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                            + Add Company
                        </button>
                    @endif
                </div>

                {{-- Add Company Form (Collapsible) --}}
                <div id="addCompanyForm" class="hidden mb-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <form action="{{ route('student.placement.company.add') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="company_name" required placeholder="Company Name *"
                                   class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <input type="date" name="application_deadline" placeholder="Deadline"
                                   class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <select name="application_method" required
                                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                <option value="">Application Method *</option>
                                <option value="job_portal">Job Portal</option>
                                <option value="company_website">Company Website</option>
                                <option value="email">Email</option>
                                <option value="career_fair">Career Fair</option>
                                <option value="referral">Referral</option>
                            </select>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg">Add</button>
                                <button type="button" onclick="document.getElementById('addCompanyForm').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-semibold rounded-lg">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Companies Grid --}}
                @if($tracking->companyApplications && $tracking->companyApplications->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($tracking->companyApplications as $application)
                            <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $application->company_name }}</h4>
                                    @if(!isset($readOnly) || !$readOnly)
                                        <form action="{{ route('student.placement.company.delete', $application) }}" method="POST"
                                              onsubmit="return confirm('Remove this company?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="space-y-1 text-sm">
                                    <p class="text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Deadline:</span>
                                        {{ $application->application_deadline ? $application->application_deadline->format('d M Y') : 'Not set' }}
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Method:</span>
                                        {{ $application->application_method_display }}
                                    </p>
                                </div>
                                @if($tracking->status === 'APPLIED' && (!isset($readOnly) || !$readOnly))
                                    <form action="{{ route('student.placement.company.got-interview', $application) }}" method="POST"
                                          onsubmit="return confirm('Mark as interviewed for {{ $application->company_name }}?');" class="mt-3">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                            Got Interview
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No companies added yet</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- QUICK ACTIONS --}}
            @if((!isset($readOnly) || !$readOnly) && isset($isStudentView) && $isStudentView)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
                <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @if($canApply && $tracking->status !== 'SCL_RELEASED')
                        <button onclick="document.getElementById('statusUpdateModal').classList.remove('hidden')"
                                class="px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors text-sm">
                            Update Status
                        </button>
                    @endif
                    @if($tracking->sal_path)
                        <a href="{{ route('student.placement.download-sal') }}" target="_blank"
                           class="px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors text-center text-sm">
                            Download SAL
                        </a>
                    @endif
                    @if($tracking->scl_path)
                        <a href="{{ route('student.placement.download-scl') }}" target="_blank"
                           class="px-4 py-3 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg transition-colors text-center text-sm">
                            Download SCL
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT COLUMN (1/3 width) --}}
        <div class="space-y-6">
            {{-- CURRENT STATUS CARD --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3 uppercase">Current Status</h3>
                @php
                    $statusIcon = match($tracking->status) {
                        'NOT_APPLIED' => '📄',
                        'SAL_RELEASED' => '📋',
                        'APPLIED' => '📤',
                        'INTERVIEWED' => '💼',
                        'OFFER_RECEIVED' => '🎉',
                        'ACCEPTED' => '✨',
                        'SCL_RELEASED' => '📜',
                        default => '📊',
                    };
                    $statusColor = match($tracking->status) {
                        'NOT_APPLIED' => 'bg-gray-100 border-gray-300 text-gray-800',
                        'SAL_RELEASED' => 'bg-blue-100 border-blue-300 text-blue-800',
                        'APPLIED' => 'bg-yellow-100 border-yellow-300 text-yellow-800',
                        'INTERVIEWED' => 'bg-purple-100 border-purple-300 text-purple-800',
                        'OFFER_RECEIVED' => 'bg-orange-100 border-orange-300 text-orange-800',
                        'ACCEPTED' => 'bg-green-100 border-green-300 text-green-800',
                        'SCL_RELEASED' => 'bg-indigo-100 border-indigo-300 text-indigo-800',
                        default => 'bg-gray-100 border-gray-300 text-gray-800',
                    };
                @endphp
                <div class="border-2 {{ $statusColor }} rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-3xl">{{ $statusIcon }}</span>
                        <div>
                            <p class="font-bold text-lg">{{ $tracking->status_display }}</p>
                            @if($tracking->updated_at)
                                <p class="text-xs opacity-75">Updated {{ $tracking->updated_at->diffForHumans() }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- NEXT STEPS CARD --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-5 border-2 border-blue-200 dark:border-blue-800">
                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Next Steps
                </h3>
                <div class="space-y-2 text-sm text-blue-900 dark:text-blue-200">
                    @if($tracking->status === 'NOT_APPLIED')
                        @if(!$canApply)
                            <p>✓ Complete resume inspection</p>
                            <p class="opacity-75">→ Wait for SAL release</p>
                        @else
                            <p>✓ Resume approved</p>
                            <p class="opacity-75">→ Wait for SAL release</p>
                        @endif
                    @elseif($tracking->status === 'SAL_RELEASED')
                        <p>✓ SAL released</p>
                        <p class="opacity-75">→ Start applying to companies</p>
                    @elseif($tracking->status === 'APPLIED')
                        <p>✓ Applications sent</p>
                        <p class="opacity-75">→ Wait for interview invitations</p>
                    @elseif($tracking->status === 'INTERVIEWED')
                        <p>✓ Interviews completed</p>
                        <p class="opacity-75">→ Wait for offer letters</p>
                    @elseif($tracking->status === 'OFFER_RECEIVED')
                        <p>✓ Offer received</p>
                        <p class="opacity-75">→ Accept offer & upload proof</p>
                    @elseif($tracking->status === 'ACCEPTED')
                        <p>✓ Offer accepted</p>
                        <p class="opacity-75">→ Wait for SCL release</p>
                    @elseif($tracking->status === 'SCL_RELEASED')
                        <p>✓ SCL released</p>
                        <p class="opacity-75">→ Journey complete! 🎉</p>
                    @endif
                </div>
            </div>

            {{-- DOCUMENTS CARD --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3 uppercase">Documents</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">SAL</span>
                        </div>
                        @if($tracking->sal_path)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Available</span>
                        @else
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">Pending</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">SCL</span>
                        </div>
                        @if($tracking->scl_path)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Available</span>
                        @else
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- TIPS CARD --}}
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-5 border-2 border-yellow-200 dark:border-yellow-800">
                <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    Pro Tips
                </h3>
                <ul class="text-xs text-yellow-800 dark:text-yellow-200 space-y-2">
                    <li>• Update your status regularly</li>
                    <li>• Keep company records organized</li>
                    <li>• Upload proof documents when needed</li>
                    <li>• Check with admin if you have questions</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- STATUS UPDATE MODAL --}}
    @if((!isset($readOnly) || !$readOnly) && isset($isStudentView) && $isStudentView && $canApply)
    <div id="statusUpdateModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">Update Placement Status</h3>
                <button onclick="document.getElementById('statusUpdateModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('student.placement.status.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Status</label>
                        <select name="status" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <option value="">Select status...</option>
                            <option value="SAL_RELEASED">SAL Released</option>
                            <option value="APPLIED">Applied</option>
                            <option value="INTERVIEWED">Interviewed</option>
                            <option value="OFFER_RECEIVED">Offer Received</option>
                            <option value="ACCEPTED">Accepted</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="document.getElementById('statusUpdateModal').classList.add('hidden')"
                                class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors">
                            Update Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
