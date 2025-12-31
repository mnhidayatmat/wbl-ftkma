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

    @if($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded-lg">
            <p class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
        $interviewedCount = $tracking->companyApplications ? $tracking->companyApplications->where('interviewed', true)->count() : 0;
        $offersCount = $tracking->companyApplications ? $tracking->companyApplications->where('offer_received', true)->count() : 0;
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

                @php
                    // Define clean step labels with icons
                    $journeySteps = [
                        1 => ['icon' => '📄', 'label' => 'Resume Preparation'],
                        2 => ['icon' => '📋', 'label' => 'SAL Released'],
                        3 => ['icon' => '📤', 'label' => 'Applications Sent'],
                        4 => ['icon' => '💼', 'label' => 'Interviews'],
                        5 => ['icon' => '🎉', 'label' => 'Offer Received'],
                        6 => ['icon' => '✨', 'label' => 'Offer Accepted'],
                        7 => ['icon' => '📜', 'label' => 'SCL Released'],
                    ];
                @endphp

                {{-- Mobile: Vertical Timeline --}}
                <div class="block md:hidden space-y-3">
                    @foreach($journeySteps as $stepNum => $stepInfo)
                        @php
                            $isActive = $stepNum === $currentStep;
                            $isCompleted = $stepNum < $currentStep;
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center shadow-sm
                                {{ $isCompleted ? 'bg-green-500 text-white' : ($isActive ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700') }}">
                                <span class="text-lg">{{ $isCompleted ? '✓' : $stepInfo['icon'] }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold {{ $isActive ? 'text-blue-600 dark:text-blue-400' : ($isCompleted ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400') }}">
                                    {{ $stepInfo['label'] }}
                                </p>
                                @if($isActive)
                                    <span class="text-xs text-blue-500 dark:text-blue-400 font-medium">Current Stage</span>
                                @elseif($isCompleted)
                                    <span class="text-xs text-green-500 dark:text-green-400">Completed</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Desktop: Horizontal Timeline --}}
                <div class="hidden md:flex items-center justify-between relative">
                    {{-- Progress Line --}}
                    <div class="absolute top-8 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-700" style="z-index: 0;"></div>
                    <div class="absolute top-8 left-0 h-1 bg-gradient-to-r from-blue-500 to-green-500 transition-all duration-500"
                         style="width: {{ $progressPercentage }}%; z-index: 1;"></div>

                    @foreach($journeySteps as $stepNum => $stepInfo)
                        @php
                            $isActive = $stepNum === $currentStep;
                            $isCompleted = $stepNum < $currentStep;
                        @endphp
                        <div class="flex flex-col items-center relative z-10" style="flex: 1;">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-2 shadow-md transition-all
                                {{ $isCompleted ? 'bg-green-500 text-white' : ($isActive ? 'bg-blue-500 text-white ring-4 ring-blue-200 scale-110' : 'bg-gray-200 dark:bg-gray-700') }}">
                                <span class="text-2xl">{{ $isCompleted ? '✓' : $stepInfo['icon'] }}</span>
                            </div>
                            <p class="text-xs text-center font-semibold px-1 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : ($isCompleted ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-500') }}"
                               style="max-width: 100px;">
                                {{ $stepInfo['label'] }}
                            </p>
                            @if($isActive)
                                <span class="text-xs text-blue-500 dark:text-blue-400 font-bold mt-1">Active</span>
                            @endif
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
                        <input type="hidden" name="placement_tracking_id" value="{{ $tracking->id }}">

                        <div class="space-y-4">
                            {{-- Company Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Company Name <span class="text-red-500">*</span></label>
                                <input type="text" name="company_name" required placeholder="Enter company name"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Company will be officially registered when you accept an offer</p>
                            </div>

                            {{-- Application Details --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Application Deadline</label>
                                    <input type="date" name="application_deadline"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Application Method <span class="text-red-500">*</span></label>
                                    <select name="application_method" id="applicationMethodSelect" required
                                            onchange="toggleOtherMethodInput()"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Method</option>
                                        <option value="through_coordinator">Through Coordinator</option>
                                        <option value="job_portal">Job Portal</option>
                                        <option value="company_website">Company Website</option>
                                        <option value="email">Email</option>
                                        <option value="career_fair">Career Fair</option>
                                        <option value="referral">Referral</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Other Method Input --}}
                            <div id="otherMethodContainer" class="hidden">
                                <input type="text" name="application_method_other" id="otherMethodInput" placeholder="Please specify the application method *"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg">Add Application</button>
                                <button type="button" onclick="document.getElementById('addCompanyForm').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-semibold rounded-lg">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
                <script>
                    function toggleOtherMethodInput() {
                        const select = document.getElementById('applicationMethodSelect');
                        const container = document.getElementById('otherMethodContainer');
                        const input = document.getElementById('otherMethodInput');
                        if (select.value === 'other') {
                            container.classList.remove('hidden');
                            input.required = true;
                        } else {
                            container.classList.add('hidden');
                            input.required = false;
                            input.value = '';
                        }
                    }
                </script>

                {{-- Companies Grid --}}
                @if($tracking->companyApplications && $tracking->companyApplications->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($tracking->companyApplications as $application)
                            <div class="border-2 {{ $application->interviewed ? 'border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700' }} rounded-lg p-4 hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        @if($application->interviewed)
                                            <span class="text-green-600 dark:text-green-400">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            </span>
                                        @endif
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $application->company_name }}</h4>
                                    </div>
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
                                    @if($application->interviewed && $application->interview_date)
                                        <p class="text-green-600 dark:text-green-400">
                                            <span class="font-medium">Interview Date:</span>
                                            {{ $application->interview_date->format('d M Y') }}
                                        </p>
                                    @endif
                                </div>

                                {{-- APPLIED stage: Show "Got Interview" button --}}
                                @if($tracking->status === 'APPLIED' && (!isset($readOnly) || !$readOnly))
                                    <form action="{{ route('student.placement.company.got-interview', $application) }}" method="POST"
                                          onsubmit="return confirm('Mark as interviewed for {{ $application->company_name }}?');" class="mt-3">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-colors">
                                            Got Interview
                                        </button>
                                    </form>
                                @endif

                                {{-- INTERVIEWED stage: Show checkbox and date input --}}
                                @if($tracking->status === 'INTERVIEWED' && (!isset($readOnly) || !$readOnly))
                                    <form action="{{ route('student.placement.company.update-interview', $application) }}" method="POST" class="mt-3 space-y-2" id="interviewForm_{{ $application->id }}">
                                        @csrf
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="interviewed" id="interviewed_{{ $application->id }}" value="1"
                                                   {{ $application->interviewed ? 'checked' : '' }}
                                                   onchange="toggleInterviewDate_{{ $application->id }}(this)"
                                                   class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="interviewed_{{ $application->id }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Got Interview
                                            </label>
                                        </div>
                                        <div id="interviewDateContainer_{{ $application->id }}" class="{{ $application->interviewed ? '' : 'hidden' }}">
                                            <div class="flex items-center gap-2">
                                                <label class="text-xs text-gray-600 dark:text-gray-400">Interview Date <span class="text-red-500">*</span>:</label>
                                                <input type="date" name="interview_date" id="interviewDate_{{ $application->id }}"
                                                       value="{{ $application->interview_date ? $application->interview_date->format('Y-m-d') : '' }}"
                                                       {{ $application->interviewed ? 'required' : '' }}
                                                       class="px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                                <input type="hidden" name="interviewed" value="1">
                                                <button type="submit" class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                    <script>
                                        function toggleInterviewDate_{{ $application->id }}(checkbox) {
                                            const container = document.getElementById('interviewDateContainer_{{ $application->id }}');
                                            const dateInput = document.getElementById('interviewDate_{{ $application->id }}');
                                            const form = document.getElementById('interviewForm_{{ $application->id }}');
                                            if (checkbox.checked) {
                                                container.classList.remove('hidden');
                                                dateInput.required = true;
                                            } else {
                                                container.classList.add('hidden');
                                                dateInput.required = false;
                                                dateInput.value = '';
                                                form.submit();
                                            }
                                        }
                                    </script>
                                @endif

                                {{-- Follow-up Section (Available in both APPLIED and INTERVIEWED stages) --}}
                                @if(($tracking->status === 'APPLIED' || $tracking->status === 'INTERVIEWED') && (!isset($readOnly) || !$readOnly))
                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <button type="button" onclick="toggleFollowUp_{{ $application->id }}()"
                                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $application->follow_up_date ? 'Edit Follow-up' : 'Add Follow-up' }}
                                            @if($application->follow_up_date)
                                                <span class="text-orange-500">({{ $application->follow_up_date->format('d M') }})</span>
                                            @endif
                                        </button>
                                        <div id="followUpContainer_{{ $application->id }}" class="hidden mt-2">
                                            <form action="{{ route('student.placement.company.update-follow-up', $application) }}" method="POST" class="space-y-2">
                                                @csrf
                                                <div>
                                                    <label class="text-xs text-gray-600 dark:text-gray-400">Follow-up Date:</label>
                                                    <input type="date" name="follow_up_date"
                                                           value="{{ $application->follow_up_date ? $application->follow_up_date->format('Y-m-d') : '' }}"
                                                           class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-600 dark:text-gray-400">Notes:</label>
                                                    <textarea name="follow_up_notes" rows="2" placeholder="e.g., Call HR, Send email reminder..."
                                                              class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">{{ $application->follow_up_notes }}</textarea>
                                                </div>
                                                <button type="submit" class="w-full px-2 py-1 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold rounded">
                                                    Save Follow-up
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <script>
                                        function toggleFollowUp_{{ $application->id }}() {
                                            const container = document.getElementById('followUpContainer_{{ $application->id }}');
                                            container.classList.toggle('hidden');
                                        }
                                    </script>
                                @endif

                                {{-- Show follow-up info if set (read-only display) --}}
                                @if($application->follow_up_date && (isset($readOnly) && $readOnly))
                                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <p class="text-xs text-orange-600 dark:text-orange-400">
                                            <span class="font-medium">Follow-up:</span> {{ $application->follow_up_date->format('d M Y') }}
                                            @if($application->follow_up_notes)
                                                - {{ $application->follow_up_notes }}
                                            @endif
                                        </p>
                                    </div>
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

            {{-- QUICK ACTIONS (Enhanced & Contextual) --}}
            @if((!isset($readOnly) || !$readOnly) && isset($isStudentView) && $isStudentView)
            <div class="bg-gradient-to-br from-white to-blue-50/30 dark:from-gray-800 dark:to-blue-900/10 rounded-xl shadow-lg p-5 border-2 border-blue-100 dark:border-blue-900/50">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Quick Actions
                    </h3>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-2 py-1 rounded-full">
                        Step {{ $currentStep }} of 7
                    </span>
                </div>

                {{-- Mini Progress Bar --}}
                <div class="mb-4 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                    <div class="bg-gradient-to-r from-blue-500 to-green-500 h-1.5 rounded-full transition-all duration-500"
                         style="width: {{ $progressPercentage }}%"></div>
                </div>

                <div class="space-y-4">
                    @if($tracking->status === 'NOT_APPLIED')
                        {{-- Resume Preparation Stage --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border-l-4 border-blue-500">
                            <p class="text-xs font-bold text-blue-800 dark:text-blue-200 mb-1">📍 Current Stage</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Complete resume inspection to proceed</p>
                        </div>

                        @if(!$canApply)
                            <div class="space-y-2">
                                <a href="{{ route('student.resume.index') }}"
                                   class="block px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-center text-sm shadow-md">
                                    📄 Go to Resume Inspection
                                </a>
                                <p class="text-xs text-gray-600 dark:text-gray-400 text-center">Submit your resume and posters for approval</p>
                            </div>

                            {{-- Checklist --}}
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 text-xs">
                                <p class="font-semibold text-gray-700 dark:text-gray-300 mb-2">📋 Required Steps:</p>
                                <ul class="space-y-1 text-gray-600 dark:text-gray-400">
                                    <li class="flex items-start gap-2">
                                        <span class="text-red-500">○</span>
                                        <span>Upload merged PDF (Resume + Posters)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="text-red-500">○</span>
                                        <span>Wait for coordinator approval</span>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div>
                                        <p class="text-sm font-semibold text-green-800 dark:text-green-200">Resume Approved!</p>
                                        <p class="text-xs text-green-700 dark:text-green-300 mt-1">Waiting for admin to release SAL</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border-l-4 border-yellow-500">
                                <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200 mb-1">💡 What's Next?</p>
                                <p class="text-xs text-yellow-700 dark:text-yellow-300">Once SAL is released, you can start applying to companies</p>
                            </div>
                        @endif

                    @elseif($tracking->status === 'SAL_RELEASED')
                        {{-- SAL Released - Ready to Apply --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border-l-4 border-blue-500">
                            <p class="text-xs font-bold text-blue-800 dark:text-blue-200 mb-1">📍 Current Stage</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Start applying to companies</p>
                        </div>

                        @if($tracking->sal_file_path)
                            <div class="space-y-2">
                                <a href="{{ route('student.placement.download-sal') }}" target="_blank"
                                   class="block px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-center text-sm shadow-md">
                                    📋 Download SAL
                                </a>
                                <p class="text-xs text-gray-600 dark:text-gray-400 text-center">Student Application Letter for companies</p>
                            </div>
                        @endif

                        <div class="space-y-3">
                            <form action="{{ route('student.placement.status.update') }}" method="POST" class="w-full space-y-3">
                                @csrf
                                <input type="hidden" name="status" value="APPLIED">

                                <button type="submit"
                                        class="w-full px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-sm shadow-md">
                                    📤 I've Sent My Applications
                                </button>
                            </form>
                            <p class="text-xs text-gray-600 dark:text-gray-400 text-center">Proceed to Applications Sent stage</p>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border-l-4 border-yellow-500">
                            <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200 mb-1">💡 Pro Tip</p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-300">Apply to 5-10 companies to increase your chances</p>
                        </div>

                    @elseif($tracking->status === 'APPLIED')
                        {{-- Applied - Managing Applications --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border-l-4 border-blue-500">
                            <p class="text-xs font-bold text-blue-800 dark:text-blue-200 mb-1">📍 Current Stage</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Track applications & wait for interviews</p>
                        </div>

                        {{-- Quick Stats --}}
                        <div class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg p-3 border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-purple-600 dark:text-purple-400">Companies Applied</p>
                                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $totalApplications }}</p>
                                </div>
                                <svg class="w-10 h-10 text-purple-300 dark:text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <form action="{{ route('student.placement.status.update') }}" method="POST" class="w-full">
                                @csrf
                                <input type="hidden" name="status" value="INTERVIEWED">
                                <button type="submit"
                                        class="w-full px-4 py-3 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-sm shadow-md">
                                    💼 I've Been Interviewed
                                </button>
                            </form>
                            <p class="text-xs text-gray-600 dark:text-gray-400 text-center">Proceed to Interviews stage</p>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border-l-4 border-yellow-500">
                            <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200 mb-1">💡 Pro Tip</p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-300">Follow up with companies 3-5 days after applying</p>
                        </div>

                        {{-- Go Back Button --}}
                        <form action="{{ route('student.placement.status.update') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="status" value="SAL_RELEASED">
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all text-sm"
                                    onclick="return confirm('Are you sure you want to go back to SAL Released stage?')">
                                ← Go Back to SAL Released
                            </button>
                        </form>

                    @elseif($tracking->status === 'INTERVIEWED')
                        {{-- Interviewed - Waiting for Offers --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border-l-4 border-blue-500">
                            <p class="text-xs font-bold text-blue-800 dark:text-blue-200 mb-1">📍 Current Stage</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Wait for offers or continue applying</p>
                        </div>

                        {{-- Quick Stats --}}
                        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-lg p-3 border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-purple-600 dark:text-purple-400">Interviews Done</p>
                                    <p class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $interviewedCount }}</p>
                                </div>
                                <svg class="w-10 h-10 text-purple-300 dark:text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <form action="{{ route('student.placement.status.update') }}" method="POST" class="w-full">
                                @csrf
                                <input type="hidden" name="status" value="OFFER_RECEIVED">
                                <button type="submit"
                                        class="w-full px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-sm shadow-md">
                                    🎉 I've Received an Offer
                                </button>
                            </form>
                            <p class="text-xs text-gray-600 dark:text-gray-400 text-center">Proceed to mark which companies gave you offers</p>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border-l-4 border-yellow-500">
                            <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200 mb-1">💡 Pro Tips</p>
                            <ul class="text-xs text-yellow-700 dark:text-yellow-300 list-disc list-inside space-y-1">
                                <li>Send thank-you emails after interviews!</li>
                                <li>Keep frequent follow-up with companies you've applied to</li>
                                <li>Continue following up even after interviews</li>
                                <li>Follow up 1 week after application or interview if no response</li>
                            </ul>
                        </div>

                        {{-- Go Back Button --}}
                        <form action="{{ route('student.placement.status.update') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="status" value="APPLIED">
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all text-sm"
                                    onclick="return confirm('Are you sure you want to go back to Applications Sent stage?')">
                                ← Go Back to Applications Sent
                            </button>
                        </form>

                    @elseif($tracking->status === 'OFFER_RECEIVED')
                        {{-- Offer Received - Need to Accept --}}
                        @php
                            $companiesWithOffers = $tracking->companyApplications ? $tracking->companyApplications->where('offer_received', true) : collect();
                            $acceptedCompany = $companiesWithOffers->where('is_accepted', true)->first();
                            $otherOfferCompanies = $acceptedCompany ? $companiesWithOffers->where('id', '!=', $acceptedCompany->id) : collect();
                            $allOthersDeclined = $otherOfferCompanies->count() === 0 || $otherOfferCompanies->every(fn($c) => $c->decline_sent);
                            $canProceed = $acceptedCompany && $allOthersDeclined;
                        @endphp

                        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 border-l-4 border-orange-500">
                            <p class="text-xs font-bold text-orange-800 dark:text-orange-200 mb-1">🎉 Congratulations on your offer(s)!</p>
                            <p class="text-sm text-orange-700 dark:text-orange-300">Select ONE company to accept, then confirm you've declined others</p>
                        </div>

                        {{-- CRITICAL WARNING --}}
                        <div class="bg-red-100 dark:bg-red-900/30 rounded-lg p-4 border-2 border-red-500">
                            <div class="flex items-start gap-2">
                                <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-red-800 dark:text-red-200">IMPORTANT: Accept Only ONE Company!</p>
                                    <p class="text-xs text-red-700 dark:text-red-300 mt-1">Accepting multiple companies is <strong>strictly prohibited</strong> and will damage our university's reputation with industry partners.</p>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 1: Mark companies with offers --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="bg-blue-50 dark:bg-blue-900/20 px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200">Step 1: Mark Companies That Gave You Offers</h4>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @if($tracking->companyApplications && $tracking->companyApplications->count() > 0)
                                    @foreach($tracking->companyApplications as $application)
                                        <div class="p-3 {{ $application->offer_received ? 'bg-orange-50 dark:bg-orange-900/10' : '' }}">
                                            <form action="{{ route('student.placement.company.update-offer', $application) }}" method="POST" class="flex items-center gap-3">
                                                @csrf
                                                <input type="hidden" name="action" value="toggle_offer">
                                                <label class="flex items-center gap-2 cursor-pointer flex-1">
                                                    <input type="checkbox" name="offer_received" value="1"
                                                           {{ $application->offer_received ? 'checked' : '' }}
                                                           onchange="this.form.submit()"
                                                           class="w-4 h-4 text-orange-500 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->company_name }}</span>
                                                    @if($application->interviewed)
                                                        <span class="text-xs text-green-600 dark:text-green-400">(Interviewed)</span>
                                                    @endif
                                                </label>
                                                @if($application->offer_received)
                                                    <span class="text-xs bg-orange-100 text-orange-700 dark:bg-orange-800 dark:text-orange-200 px-2 py-0.5 rounded">🎁 Offer</span>
                                                @endif
                                            </form>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                        <p class="text-sm">No companies in your application list.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($companiesWithOffers->count() > 0)
                            {{-- STEP 2: Select ONE company to accept --}}
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="bg-green-50 dark:bg-green-900/20 px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                    <h4 class="text-sm font-semibold text-green-800 dark:text-green-200">Step 2: Select ONE Company to Accept</h4>
                                    <p class="text-xs text-green-600 dark:text-green-400">Choose the company you will accept the offer from</p>
                                </div>
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($companiesWithOffers as $application)
                                        <div class="p-3 {{ $application->is_accepted ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                            <form action="{{ route('student.placement.company.update-offer', $application) }}" method="POST" class="flex items-center gap-3">
                                                @csrf
                                                <input type="hidden" name="action" value="select_accept">
                                                <label class="flex items-center gap-2 cursor-pointer flex-1">
                                                    <input type="radio" name="is_accepted" value="1"
                                                           {{ $application->is_accepted ? 'checked' : '' }}
                                                           onchange="this.form.submit()"
                                                           class="w-4 h-4 text-green-500 bg-gray-100 border-gray-300 focus:ring-green-500">
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->company_name }}</span>
                                                </label>
                                                @if($application->is_accepted)
                                                    <span class="text-xs bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200 px-2 py-0.5 rounded font-semibold">✓ Will Accept</span>
                                                @endif
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- STEP 3: Confirm decline sent for other companies --}}
                            @if($acceptedCompany && $otherOfferCompanies->count() > 0)
                                <div class="bg-white dark:bg-gray-800 rounded-lg border-2 border-red-300 dark:border-red-700 overflow-hidden">
                                    <div class="bg-red-50 dark:bg-red-900/20 px-4 py-2 border-b border-red-200 dark:border-red-700">
                                        <h4 class="text-sm font-semibold text-red-800 dark:text-red-200">Step 3: Confirm You Have Declined Other Offers</h4>
                                        <p class="text-xs text-red-600 dark:text-red-400">You MUST send decline emails/messages to these companies before proceeding</p>
                                    </div>
                                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($otherOfferCompanies as $application)
                                            <div class="p-4 {{ $application->decline_sent ? 'bg-gray-50 dark:bg-gray-700/50' : 'bg-red-50 dark:bg-red-900/10' }}">
                                                <div class="flex items-start gap-3">
                                                    <form action="{{ route('student.placement.company.update-offer', $application) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        <input type="hidden" name="action" value="mark_decline_sent">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <label class="flex items-center gap-2 cursor-pointer">
                                                                <input type="checkbox" name="decline_sent" value="1"
                                                                       {{ $application->decline_sent ? 'checked' : '' }}
                                                                       onchange="this.form.submit()"
                                                                       class="w-5 h-5 text-red-500 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
                                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->company_name }}</span>
                                                            </label>
                                                            @if($application->decline_sent)
                                                                <span class="text-xs bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-200 px-2 py-0.5 rounded">✓ Decline Sent</span>
                                                            @else
                                                                <span class="text-xs bg-red-200 text-red-700 dark:bg-red-800 dark:text-red-200 px-2 py-0.5 rounded animate-pulse">⚠ Pending</span>
                                                            @endif
                                                        </div>
                                                        @if(!$application->decline_sent)
                                                            <p class="text-xs text-red-600 dark:text-red-400 ml-7">Tick this box ONLY after you have sent a polite decline message to this company</p>
                                                        @endif
                                                    </form>
                                                </div>

                                                {{-- Decline notes section --}}
                                                <div class="mt-3 ml-7">
                                                    <form action="{{ route('student.placement.company.update-offer', $application) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="save_decline_notes">
                                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                            Your decline message (for your reference):
                                                        </label>
                                                        <textarea name="decline_notes" rows="2"
                                                                  placeholder="E.g., 'Dear [Name], Thank you for the offer. After careful consideration, I have decided to accept another position. I appreciate the opportunity...'"
                                                                  class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-red-500 focus:border-red-500 placeholder:text-gray-400">{{ $application->decline_notes }}</textarea>
                                                        <button type="submit" class="mt-2 px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-xs rounded transition-colors">
                                                            Save Notes
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Accept Offer Button --}}
                        @if($canProceed)
                            <div class="space-y-2">
                                <form action="{{ route('student.placement.status.update') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="status" value="ACCEPTED">
                                    <button type="submit"
                                            class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-sm shadow-md"
                                            onclick="return confirm('Final Confirmation:\n\nYou are accepting offer from: {{ $acceptedCompany->company_name }}\n\nHave you sent decline messages to all other companies?\n\nThis action cannot be undone.')">
                                        ✨ Confirm: Accept Offer from {{ $acceptedCompany->company_name }}
                                    </button>
                                </form>
                            </div>
                        @elseif($companiesWithOffers->count() > 0 && !$acceptedCompany)
                            <div class="bg-yellow-100 dark:bg-yellow-900/30 rounded-lg p-3 text-center border border-yellow-300">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">👆 Please select ONE company to accept in Step 2</p>
                            </div>
                        @elseif($acceptedCompany && !$allOthersDeclined)
                            <div class="bg-red-100 dark:bg-red-900/30 rounded-lg p-3 text-center border border-red-300">
                                <p class="text-sm text-red-800 dark:text-red-200">⚠️ Please confirm you have sent decline messages to ALL other companies in Step 3</p>
                            </div>
                        @else
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 text-center">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Mark companies that gave you offers in Step 1</p>
                            </div>
                        @endif

                        @if($tracking->sal_file_path)
                            <a href="{{ route('student.placement.download-sal') }}" target="_blank"
                               class="block px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-center text-sm shadow-md">
                                📋 Download SAL
                            </a>
                        @endif

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border-l-4 border-yellow-500">
                            <p class="text-xs font-semibold text-yellow-800 dark:text-yellow-200 mb-1">💡 Work Ethics - Declining Offers Professionally</p>
                            <ul class="text-xs text-yellow-700 dark:text-yellow-300 list-disc list-inside space-y-1">
                                <li>Always respond promptly - don't leave companies waiting</li>
                                <li>Be gracious and thank them for the opportunity</li>
                                <li>Keep it brief but professional</li>
                                <li>Don't burn bridges - you may work with them in future</li>
                            </ul>
                        </div>

                        {{-- Go Back Button --}}
                        <form action="{{ route('student.placement.status.update') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="status" value="INTERVIEWED">
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all text-sm"
                                    onclick="return confirm('Are you sure you want to go back to Interviews stage?')">
                                ← Go Back to Interviews
                            </button>
                        </form>

                    @elseif($tracking->status === 'ACCEPTED')
                        {{-- Accepted - Waiting for SCL --}}
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border-l-4 border-green-500">
                            <p class="text-xs font-bold text-green-800 dark:text-green-200 mb-1">✅ Offer Accepted!</p>
                            <p class="text-sm text-green-700 dark:text-green-300">Waiting for SCL release</p>
                        </div>

                        @if($tracking->confirmation_proof_path)
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div>
                                        <p class="text-sm font-semibold text-green-800 dark:text-green-200">Proof Uploaded</p>
                                        <p class="text-xs text-green-700 dark:text-green-300 mt-1">Acceptance proof submitted</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="space-y-3">
                                <form action="{{ route('student.placement.proof.upload') }}" method="POST" enctype="multipart/form-data" class="w-full space-y-2">
                                    @csrf
                                    <label class="block">
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Select acceptance proof file</span>
                                        <input type="file" name="proof_file" required accept=".pdf,.jpg,.jpeg,.png"
                                               class="w-full text-sm text-gray-500 dark:text-gray-400
                                                      file:mr-2 file:py-2 file:px-3
                                                      file:rounded-lg file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-blue-50 file:text-blue-700
                                                      hover:file:bg-blue-100
                                                      dark:file:bg-blue-900/30 dark:file:text-blue-300">
                                    </label>
                                    <button type="submit"
                                            class="w-full px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-sm shadow-md">
                                        📎 Upload Acceptance Proof
                                    </button>
                                </form>
                                <p class="text-xs text-gray-600 dark:text-gray-400 text-center">Required for SCL processing (PDF, JPG, PNG)</p>
                            </div>
                        @endif

                        @if($tracking->sal_file_path)
                            <a href="{{ route('student.placement.download-sal') }}" target="_blank"
                               class="block px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-center text-sm shadow-md">
                                📋 Download SAL
                            </a>
                        @endif

                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">Waiting...</p>
                                    <p class="text-xs text-blue-700 dark:text-blue-300">Admin will release your SCL soon</p>
                                </div>
                            </div>
                        </div>

                        {{-- Go Back Button --}}
                        <form action="{{ route('student.placement.status.update') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="status" value="OFFER_RECEIVED">
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-all text-sm"
                                    onclick="return confirm('Are you sure you want to go back to Offer Received stage?')">
                                ← Go Back to Offer Received
                            </button>
                        </form>

                    @elseif($tracking->status === 'SCL_RELEASED')
                        {{-- SCL Released - Journey Complete --}}
                        <div class="bg-gradient-to-r from-green-400 to-blue-500 rounded-lg p-4 text-white text-center">
                            <p class="text-3xl mb-2">🎉</p>
                            <p class="font-bold text-lg">Journey Complete!</p>
                            <p class="text-xs opacity-90 mt-1">Congratulations on your placement</p>
                        </div>

                        @if($tracking->scl_file_path)
                            <div class="space-y-2">
                                <a href="{{ route('student.placement.download-scl') }}" target="_blank"
                                   class="block px-4 py-3 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-center text-sm shadow-md">
                                    📜 Download SCL
                                </a>
                                <p class="text-xs text-gray-600 dark:text-gray-400 text-center">Student Confirmation Letter</p>
                            </div>
                        @endif

                        @if($tracking->sal_file_path)
                            <a href="{{ route('student.placement.download-sal') }}" target="_blank"
                               class="block px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-all hover:scale-[1.02] text-center text-sm shadow-md">
                                📋 Download SAL
                            </a>
                        @endif

                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border-l-4 border-blue-500">
                            <p class="text-xs font-semibold text-blue-800 dark:text-blue-200 mb-1">📚 What's Next?</p>
                            <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                                <li>• Prepare for your internship</li>
                                <li>• Review company guidelines</li>
                                <li>• Stay in touch with coordinator</li>
                            </ul>
                        </div>
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
                    @if($tracking->sal_file_path)
                        <a href="{{ route('student.placement.download-sal') }}" target="_blank"
                           class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors cursor-pointer border border-green-200 dark:border-green-800">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-sm font-medium text-green-700 dark:text-green-300">SAL</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200 px-2 py-1 rounded-full">Download</span>
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </div>
                        </a>
                    @else
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">SAL</span>
                            </div>
                            <span class="text-xs bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full">Pending</span>
                        </div>
                    @endif

                    @if($tracking->scl_file_path)
                        <a href="{{ route('student.placement.download-scl') }}" target="_blank"
                           class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors cursor-pointer border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-sm font-medium text-purple-700 dark:text-purple-300">SCL</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200 px-2 py-1 rounded-full">Download</span>
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </div>
                        </a>
                    @else
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">SCL</span>
                            </div>
                            <span class="text-xs bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full">Pending</span>
                        </div>
                    @endif
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
