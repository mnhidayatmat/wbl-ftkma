@extends('layouts.app')

@section('title', isset($isStudentView) && $isStudentView ? 'My Placement Tracking' : 'Student Placement Tracking - ' . $student->name)

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">
                    @if(isset($isStudentView) && $isStudentView)
                        My Placement Tracking
                    @else
                        Student Placement Tracking
                    @endif
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    @if(isset($isStudentView) && $isStudentView)
                        Track your industrial placement status and progress
                    @else
                        View placement status and progress for <strong>{{ $student->name }}</strong> ({{ $student->matric_no }})
                    @endif
                </p>
            </div>
            @if(!isset($isStudentView) || !$isStudentView)
                <a href="{{ route('placement.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                    ← Back to Students
                </a>
            @endif
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

        <!-- Completed Group Notice -->
        @if(isset($isInCompletedGroup) && $isInCompletedGroup)
            <div class="mb-6 bg-gray-100 dark:bg-gray-700 border-l-4 border-gray-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 mb-1">WBL Group Completed</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Your WBL group has been completed and archived. You have <strong>read-only access</strong> to view your placement information. 
                            You cannot update your status or upload new documents. All data remains available for historical records and reporting.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Info Banner -->
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        <strong>Keep your status updated!</strong> Administrators can see your status updates in real-time. 
                        Please update your placement status as you progress through the application and placement process.
                    </p>
                </div>
            </div>
        </div>

        <!-- Resume Inspection Warning -->
        @if(!$canApply)
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">
                        Resume Inspection Required
                    </p>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                        You must pass the resume inspection before you can apply to companies. 
                        <a href="{{ route('student.resume.index') }}" class="underline font-semibold">Go to Resume Inspection</a>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Section A: Placement Status Overview (Enhanced Progress Journey) -->
        @php
            $totalSteps = count($statuses);
            $progressPercentage = ($currentStep / $totalSteps) * 100;
            $motivationalMessages = [
                1 => ['message' => '🚀 Your journey begins here!', 'subtitle' => 'Every great career starts with a single step.'],
                2 => ['message' => '📋 You\'re making progress!', 'subtitle' => 'Keep moving forward, you\'ve got this!'],
                3 => ['message' => '📤 Applications sent!', 'subtitle' => 'You\'re putting yourself out there - that\'s brave!'],
                4 => ['message' => '💼 Interview time!', 'subtitle' => 'Show them what you\'re made of!'],
                5 => ['message' => '🎉 Offer received!', 'subtitle' => 'Congratulations! Your hard work is paying off!'],
                6 => ['message' => '✨ Almost there!', 'subtitle' => 'You\'re so close to your goal!'],
                7 => ['message' => '🏆 Journey complete!', 'subtitle' => 'You did it! Time to celebrate your success!'],
            ];
            $currentMotivation = $motivationalMessages[$currentStep] ?? $motivationalMessages[1];
        @endphp
        
        <div class="bg-gradient-to-br from-white via-blue-50/30 to-purple-50/30 dark:from-gray-800 dark:via-blue-900/20 dark:to-purple-900/20 rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 p-8 mb-6 relative overflow-hidden">
            <!-- Decorative Background Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-200/20 to-purple-200/20 dark:from-blue-800/10 dark:to-purple-800/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-green-200/20 to-blue-200/20 dark:from-green-800/10 dark:to-blue-800/10 rounded-full blur-3xl -ml-24 -mb-24"></div>
            
            <div class="relative z-10">
                <!-- Header with Progress -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold bg-gradient-to-r from-[#003A6C] to-[#0084C5] dark:from-[#0084C5] dark:to-[#00A86B] bg-clip-text text-transparent mb-2">
                                Your Placement Journey
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $currentMotivation['message'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $currentMotivation['subtitle'] }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold bg-gradient-to-r from-[#0084C5] to-[#00A86B] bg-clip-text text-transparent">
                                {{ round($progressPercentage) }}%
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Complete</p>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-[#0084C5] via-[#00A86B] to-[#00A86B] rounded-full transition-all duration-1000 ease-out relative overflow-hidden" 
                             style="width: {{ $progressPercentage }}%">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Desktop: Enhanced Horizontal Timeline -->
                <div class="hidden md:block">
                    <div class="flex items-center justify-between relative">
                        @foreach($statuses as $statusKey => $statusInfo)
                            @php
                                $step = $statusInfo['step'];
                                $isCompleted = $currentStep > $step;
                                $isCurrent = $currentStep == $step;
                                $isPending = $currentStep < $step;
                                
                                // Icons for each step
                                $stepIcons = [
                                    1 => '📄',
                                    2 => '📋',
                                    3 => '📤',
                                    4 => '💼',
                                    5 => '🎉',
                                    6 => '✨',
                                    7 => '🏆',
                                ];
                                $stepIcon = $stepIcons[$step] ?? '📍';
                            @endphp
                            <div class="flex-1 flex items-center relative z-10">
                                <div class="flex flex-col items-center flex-1 group cursor-pointer">
                                    <!-- Step Circle with Animation -->
                                    <div class="relative mb-3">
                                        @if($isCompleted)
                                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#00A86B] to-[#008855] text-white flex items-center justify-center shadow-lg transform transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl">
                                                <svg class="w-8 h-8 animate-checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <div class="absolute -inset-2 bg-[#00A86B]/20 rounded-full animate-ping opacity-75"></div>
                                        @elseif($isCurrent)
                                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#0084C5] to-[#0066A3] text-white flex items-center justify-center shadow-xl ring-4 ring-[#0084C5]/30 ring-offset-2 ring-offset-white dark:ring-offset-gray-800 transform transition-all duration-300 group-hover:scale-110 animate-pulse">
                                                <span class="text-2xl">{{ $stepIcon }}</span>
                                            </div>
                                            <div class="absolute -inset-3 bg-[#0084C5]/20 rounded-full animate-pulse"></div>
                                        @else
                                            <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-400 flex items-center justify-center shadow-md transform transition-all duration-300 group-hover:scale-105">
                                                <span class="text-2xl opacity-50">{{ $stepIcon }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Step Label with Fixed Height Container -->
                                    <div class="text-center max-w-[120px] min-h-[60px] flex flex-col justify-center">
                                        <p class="text-sm font-bold {{ $isCurrent ? 'text-[#0084C5] dark:text-[#0084C5]' : ($isCompleted ? 'text-[#00A86B] dark:text-[#00A86B]' : 'text-gray-400 dark:text-gray-500') }} transition-colors mb-1">
                                            {{ $statusInfo['label'] }}
                                        </p>
                                        @if($statusInfo['date'])
                                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                                                {{ $statusInfo['date']->format('d M Y') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 dark:text-gray-600 italic">—</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Connection Line -->
                                @if(!$loop->last)
                                    <div class="flex-1 h-1.5 mx-3 relative">
                                        <div class="absolute inset-0 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                                        <div class="absolute inset-0 bg-gradient-to-r from-[#00A86B] to-[#0084C5] rounded-full transition-all duration-1000 {{ $isCompleted ? 'w-full' : 'w-0' }}"></div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Mobile: Enhanced Vertical Timeline -->
                <div class="md:hidden">
                    <div class="space-y-6">
                        @foreach($statuses as $statusKey => $statusInfo)
                            @php
                                $step = $statusInfo['step'];
                                $isCompleted = $currentStep > $step;
                                $isCurrent = $currentStep == $step;
                                $isPending = $currentStep < $step;
                                
                                $stepIcons = [
                                    1 => '📄',
                                    2 => '📋',
                                    3 => '📤',
                                    4 => '💼',
                                    5 => '🎉',
                                    6 => '✨',
                                    7 => '🏆',
                                ];
                                $stepIcon = $stepIcons[$step] ?? '📍';
                            @endphp
                            <div class="flex items-start group">
                                <div class="flex-shrink-0 relative">
                                    @if($isCompleted)
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#00A86B] to-[#008855] text-white flex items-center justify-center shadow-lg transform transition-all duration-300">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @elseif($isCurrent)
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#0084C5] to-[#0066A3] text-white flex items-center justify-center shadow-xl ring-4 ring-[#0084C5]/30 transform transition-all duration-300 animate-pulse">
                                            <span class="text-2xl">{{ $stepIcon }}</span>
                                        </div>
                                    @else
                                        <div class="w-14 h-14 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-400 flex items-center justify-center shadow-md">
                                            <span class="text-2xl opacity-50">{{ $stepIcon }}</span>
                                        </div>
                                    @endif
                                    @if(!$loop->last)
                                        <div class="w-0.5 h-16 mx-auto mt-3 {{ $isCompleted ? 'bg-gradient-to-b from-[#00A86B] to-[#0084C5]' : 'bg-gray-200 dark:bg-gray-700' }} rounded-full"></div>
                                    @endif
                                </div>
                                <div class="ml-5 flex-1 pt-1 min-h-[60px] flex flex-col justify-center">
                                    <p class="text-base font-bold {{ $isCurrent ? 'text-[#0084C5]' : ($isCompleted ? 'text-[#00A86B]' : 'text-gray-400') }} mb-1">
                                        {{ $statusInfo['label'] }}
                                    </p>
                                    @if($statusInfo['date'])
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                            {{ $statusInfo['date']->format('d M Y') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400 dark:text-gray-600 italic">—</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add CSS for animations -->
        <style>
            @keyframes shimmer {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            .animate-shimmer {
                animation: shimmer 2s infinite;
            }
            @keyframes checkmark {
                0% { transform: scale(0); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
            .animate-checkmark {
                animation: checkmark 0.5s ease-out;
            }
        </style>

            <!-- Current Status Display (Enhanced) -->
            <div class="mt-8 pt-8 border-t-2 border-gray-200/50 dark:border-gray-700/50">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-4 uppercase tracking-wide">Current Status</label>
                    @php
                        $currentStatusLabel = $tracking->status === 'NOT_APPLIED' ? ($step1Label ?? 'Resume Recommended') : $tracking->status_display;
                        
                        // Determine icon and styling based on status
                        if ($tracking->status === 'NOT_APPLIED') {
                            $statusLabel = $step1Label ?? 'Resume Recommended';
                            if ($statusLabel === 'Not started Resume Preparation') {
                                $icon = '📄';
                                $bgColor = 'bg-gray-50 dark:bg-gray-800';
                                $borderColor = 'border-gray-200 dark:border-gray-700';
                                $textColor = 'text-gray-700 dark:text-gray-300';
                                $iconBg = 'bg-gray-100 dark:bg-gray-700';
                            } elseif ($statusLabel === 'Pending Review') {
                                $icon = '⏳';
                                $bgColor = 'bg-yellow-50 dark:bg-yellow-900/20';
                                $borderColor = 'border-yellow-200 dark:border-yellow-800';
                                $textColor = 'text-yellow-800 dark:text-yellow-200';
                                $iconBg = 'bg-yellow-100 dark:bg-yellow-900/40';
                            } else { // Resume Recommended
                                $icon = '✅';
                                $bgColor = 'bg-green-50 dark:bg-green-900/20';
                                $borderColor = 'border-green-200 dark:border-green-800';
                                $textColor = 'text-green-800 dark:text-green-200';
                                $iconBg = 'bg-green-100 dark:bg-green-900/40';
                            }
                        } else {
                            // For other statuses
                            $statusForIcon = $tracking->status;
                            
                            $icon = match($statusForIcon) {
                                'SAL_RELEASED' => '📋',
                                'APPLIED' => '📤',
                                'INTERVIEWED' => '💼',
                                'OFFER_RECEIVED' => '🎉',
                                'ACCEPTED' => $tracking->confirmation_proof_path ? '✨' : '🤝',
                                'SCL_RELEASED' => '📜',
                                default => '📊',
                            };
                            $bgColor = 'bg-blue-50 dark:bg-blue-900/20';
                            $borderColor = 'border-blue-200 dark:border-blue-800';
                            $textColor = 'text-blue-800 dark:text-blue-200';
                            $iconBg = 'bg-blue-100 dark:bg-blue-900/40';
                        }
                    @endphp
                    
                    <div class="relative overflow-hidden rounded-2xl border-2 {{ $borderColor }} {{ $bgColor }} transition-all duration-500 hover:shadow-2xl hover:scale-[1.02] transform">
                        <!-- Animated Background Pattern -->
                        <div class="absolute inset-0 opacity-5">
                            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 24px 24px;"></div>
                        </div>
                        
                        <div class="relative flex items-center gap-5 px-8 py-6">
                            <!-- Icon/Emoticon with Animation -->
                            <div class="flex-shrink-0">
                                <div class="w-20 h-20 rounded-2xl {{ $iconBg }} flex items-center justify-center text-4xl shadow-lg transform transition-all duration-300 hover:scale-110 hover:rotate-6">
                                    <span class="animate-bounce-slow">{{ $icon }}</span>
                                </div>
                            </div>
                            
                            <!-- Status Text -->
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-2xl font-extrabold {{ $textColor }} tracking-tight">
                                        {{ $currentStatusLabel }}
                                    </h3>
                                    @if($tracking->status !== 'NOT_APPLIED' && $tracking->status !== 'SCL_RELEASED')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-white/50 dark:bg-gray-800/50 {{ $textColor }} animate-pulse">
                                            Active
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm {{ $textColor }} opacity-80 font-medium leading-relaxed">
                                    @if($tracking->status === 'NOT_APPLIED')
                                        Step 1 status is determined by your resume inspection status
                                    @elseif($tracking->status === 'SAL_RELEASED')
                                        Your SAL has been released. You can now update your status as you apply for placements.
                                    @elseif(!$canApply)
                                        You must pass resume inspection before applying
                                    @else
                                        Your placement progress status
                                    @endif
                                </p>
                            </div>
                            
                            <!-- Decorative Sparkle Elements -->
                            <div class="flex-shrink-0 flex flex-col gap-2">
                                <div class="w-3 h-3 rounded-full {{ $iconBg }} opacity-60 animate-pulse"></div>
                                <div class="w-2 h-2 rounded-full {{ $iconBg }} opacity-40 animate-pulse delay-150"></div>
                                <div class="w-1.5 h-1.5 rounded-full {{ $iconBg }} opacity-30 animate-pulse delay-300"></div>
                            </div>
                        </div>
                    </div>
                    
                    <style>
                        @keyframes bounce-slow {
                            0%, 100% { transform: translateY(0); }
                            50% { transform: translateY(-5px); }
                        }
                        .animate-bounce-slow {
                            animation: bounce-slow 3s ease-in-out infinite;
                        }
                    </style>
                    
                    <!-- Info Note -->
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 flex items-start gap-1.5">
                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>
                            @if($tracking->status === 'NOT_APPLIED')
                                This status is automatically updated based on your resume inspection. You cannot edit it manually.
                            @elseif($tracking->status === 'SAL_RELEASED')
                                Your SAL has been released! You can now update your placement status as you progress through the application process.
                            @elseif(!$canApply)
                                Complete your resume inspection to proceed with placement applications.
                            @else
                                Keep your status updated as you progress through the placement process.
                            @endif
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Quick Action: Next Step Button (Enhanced) -->
            @if($tracking->status === 'SAL_RELEASED' && (isset($isStudentView) && $isStudentView) && (!isset($readOnly) || !$readOnly))
            <div class="mt-8 pt-8 border-t-2 border-gray-200/50 dark:border-gray-700/50">
                <div class="relative overflow-hidden bg-gradient-to-br from-green-50 via-emerald-50 to-blue-50 dark:from-green-900/30 dark:via-emerald-900/20 dark:to-blue-900/20 rounded-2xl p-8 border-2 border-green-300/50 dark:border-green-700/50 shadow-xl">
                    <!-- Decorative Elements -->
                    <div class="absolute top-0 right-0 w-40 h-40 bg-green-200/20 dark:bg-green-800/10 rounded-full blur-2xl -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-200/20 dark:bg-blue-800/10 rounded-full blur-2xl -ml-16 -mb-16"></div>
                    
                    <div class="relative flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex-1 text-center md:text-left">
                            <div class="flex items-center justify-center md:justify-start gap-3 mb-3">
                                <span class="text-4xl animate-bounce">🚀</span>
                                <h3 class="text-2xl font-extrabold bg-gradient-to-r from-green-700 to-blue-700 dark:from-green-400 dark:to-blue-400 bg-clip-text text-transparent">
                                    Ready to Apply?
                                </h3>
                            </div>
                            <p class="text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                                Your SAL has been released! You can now proceed to the next step and mark yourself as <strong>"Applied"</strong> when you start applying for placements.
                            </p>
                        </div>
                        <form action="{{ route('student.placement.status.update') }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="status" value="APPLIED">
                            <button type="submit" class="group relative px-10 py-4 bg-gradient-to-r from-green-600 via-emerald-600 to-green-600 hover:from-green-700 hover:via-emerald-700 hover:to-green-700 text-white font-bold rounded-xl shadow-2xl transition-all duration-300 transform hover:scale-110 hover:shadow-green-500/50 flex items-center gap-3 overflow-hidden">
                                <span class="relative z-10 flex items-center gap-3">
                                    <span>Move to Step 3: Applied</span>
                                    <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- SECTION 1: APPLIED STATUS SUMMARY (Enhanced) -->
            @if($tracking->status === 'APPLIED' && (isset($isStudentView) && $isStudentView))
            <div class="mt-8 pt-8 border-t-2 border-gray-200/50 dark:border-gray-700/50">
                <div class="relative overflow-hidden bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 dark:from-green-900/30 dark:via-emerald-900/20 dark:to-teal-900/20 rounded-2xl p-8 border-2 border-green-300/50 dark:border-green-700/50 shadow-xl">
                    <!-- Decorative Background -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-green-200/10 dark:bg-green-800/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
                    
                    <div class="relative">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform">
                                <span class="text-3xl animate-bounce-slow">📤</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">Status: Applied</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Track your job application progress</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Total Companies Applied -->
                            <div class="group relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl p-6 shadow-lg border border-green-200/50 dark:border-green-800/50 hover:shadow-xl hover:scale-105 transition-all duration-300">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 block mb-1">Total Companies Applied</span>
                                        <p class="text-4xl font-extrabold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">{{ $tracking->companies_applied_count ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Last Update Date -->
                            <div class="group relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl p-6 shadow-lg border border-purple-200/50 dark:border-purple-800/50 hover:shadow-xl hover:scale-105 transition-all duration-300">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 block mb-1">Last Updated</span>
                                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                            {{ $tracking->updated_at->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: APPLICATION ACTIVITY - SIMPLE COMPANY LIST -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Companies Applied</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            List all companies you have applied to with their deadlines and application method.
                        </p>
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('addCompanyForm').classList.toggle('hidden')"
                            class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Company
                    </button>
                </div>

                <!-- Add Company Form (Hidden by default) -->
                <div id="addCompanyForm" class="hidden bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
                    <form action="{{ route('student.placement.company.add') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="placement_tracking_id" value="{{ $tracking->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="company_name" required
                                       placeholder="e.g., Petronas"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                @error('company_name')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Application Deadline
                                </label>
                                <input type="date" name="application_deadline"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                @error('application_deadline')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div x-data="{ applicationMethod: '' }">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Application Method <span class="text-red-500">*</span>
                                </label>
                                <select name="application_method" required
                                        x-model="applicationMethod"
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                    <option value="">Select method</option>
                                    <option value="through_coordinator">Through Coordinator</option>
                                    <option value="job_portal">Job Portal</option>
                                    <option value="company_website">Company Website</option>
                                    <option value="email">Email</option>
                                    <option value="career_fair">Career Fair</option>
                                    <option value="referral">Referral</option>
                                    <option value="other">Other (Please specify)</option>
                                </select>
                                @error('application_method')
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                @enderror
                                
                                <!-- Other specification field (shown when "Other" is selected) -->
                                <div x-show="applicationMethod === 'other'" x-transition class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Please specify <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="application_method_other" 
                                           x-bind:required="applicationMethod === 'other'"
                                           placeholder="e.g., LinkedIn, Company representative, etc."
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                    @error('application_method_other')
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                    <div class="flex gap-3">
                            <button type="submit" class="px-6 py-2 bg-[#00A86B] hover:bg-[#008855] text-white font-semibold rounded-lg transition-colors">
                                Add Company
                        </button>
                            <button type="button" 
                                    onclick="document.getElementById('addCompanyForm').classList.add('hidden')"
                                    class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-colors">
                                Cancel
                        </button>
                    </div>
                </form>
            </div>

                <!-- Companies List -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    @if($tracking->companyApplications && $tracking->companyApplications->count() > 0)
                        <div class="space-y-3">
                            @foreach($tracking->companyApplications as $application)
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 gap-3">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $application->company_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Deadline</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ $application->application_deadline ? $application->application_deadline->format('d M Y') : 'Not set' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Method</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $application->application_method_display }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 md:ml-4">
                                        @if($tracking->status === 'APPLIED')
                                            <form action="{{ route('student.placement.company.got-interview', $application) }}" method="POST" 
                                                  onsubmit="return confirm('Mark yourself as interviewed for {{ $application->company_name }}? This will update your status to \"Interviewed\".');"
                                                  class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-sm hover:shadow-md">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Got Interview
                                                </button>
                                            </form>
            @endif
                                        <form action="{{ route('student.placement.company.delete', $application) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to remove this company?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-2 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
        </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No companies added yet. Click "Add Company" to get started.</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Next Step Button (Enhanced) -->
            @if($tracking->hasApplicationData() && (isset($isStudentView) && $isStudentView))
            <div class="mt-8 pt-8 border-t-2 border-gray-200/50 dark:border-gray-700/50">
                <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/30 dark:via-indigo-900/20 dark:to-purple-900/20 rounded-2xl p-8 border-2 border-blue-300/50 dark:border-blue-700/50 shadow-xl">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-blue-200/20 dark:bg-blue-800/10 rounded-full blur-2xl -mr-20 -mt-20"></div>
                    
                    <div class="relative flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex-1 text-center md:text-left">
                            <div class="flex items-center justify-center md:justify-start gap-3 mb-3">
                                <span class="text-4xl animate-bounce">🎯</span>
                                <h3 class="text-2xl font-extrabold bg-gradient-to-r from-blue-700 to-purple-700 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent">
                                    Ready for Next Step?
                                </h3>
                            </div>
                            <p class="text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                                Your application data is complete! You can now proceed to <strong>"Interviewed"</strong> status when you receive interview invitations.
                            </p>
                        </div>
                        @if(!isset($readOnly) || !$readOnly)
                        <form action="{{ route('student.placement.status.update') }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="status" value="INTERVIEWED">
                            <button type="submit" class="group relative px-10 py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-2xl transition-all duration-300 transform hover:scale-110 hover:shadow-blue-500/50 flex items-center gap-3 overflow-hidden">
                                <span class="relative z-10 flex items-center gap-3">
                                    <span>Move to Step 4: Interviewed</span>
                                    <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </span>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            </button>
                        </form>
                        @else
                        <button type="button" disabled class="px-10 py-4 bg-gray-400 text-white font-bold rounded-xl cursor-not-allowed flex items-center gap-3">
                            <span>Move to Step 4: Interviewed</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- APPLIED STATUS SUMMARY (Read-only view for Admin) -->
            @if($tracking->status === 'APPLIED' && (!isset($isStudentView) || !$isStudentView))
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border-2 border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                            <span class="text-2xl">📤</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Status: Applied</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Student has applied for placements</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-green-100 dark:border-green-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Total Companies Applied</span>
                                    <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $tracking->companies_applied_count ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-purple-100 dark:border-purple-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Last Updated</span>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $tracking->updated_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Companies List (Read-only for Admin) -->
            @if($tracking->companyApplications && $tracking->companyApplications->count() > 0)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Companies Applied</h3>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <div class="space-y-3">
                        @foreach($tracking->companyApplications as $application)
                            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $application->company_name }}</p>
                        </div>
                        <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Deadline</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ $application->application_deadline ? $application->application_deadline->format('d M Y') : 'Not set' }}
                                        </p>
                        </div>
                        <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Method</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $application->application_method_display }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                        </div>
                        @endif
            @endif

            <!-- SECTION: INTERVIEWED STATUS SUMMARY -->
            @if($tracking->status === 'INTERVIEWED')
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border-2 border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                            <span class="text-2xl">💼</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Status: Interviewed</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Track your interview progress</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Interview Date -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-blue-100 dark:border-blue-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Interview Date</span>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $tracking->interviewed_at ? $tracking->interviewed_at->format('d M Y') : 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Last Update Date -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-purple-100 dark:border-purple-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Last Updated</span>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $tracking->updated_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Step Button for INTERVIEWED -->
            @if(isset($isStudentView) && $isStudentView)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 border-2 border-purple-200 dark:border-purple-800">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Received an Offer? 🎉</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                If you've received a job offer, you can proceed to "Offer Received" status.
                            </p>
                        </div>
                        @if(!isset($readOnly) || !$readOnly)
                        <form action="{{ route('student.placement.status.update') }}" method="POST" class="ml-4">
                            @csrf
                            <input type="hidden" name="status" value="OFFER_RECEIVED">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center gap-2">
                                <span>Move to Step 5: Offer Received</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </button>
                        </form>
                        @else
                        <button type="button" disabled class="px-8 py-3 bg-gray-400 text-white font-bold rounded-lg cursor-not-allowed flex items-center gap-2">
                            <span>Move to Step 5: Offer Received</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @endif

            <!-- SECTION: OFFER RECEIVED STATUS SUMMARY -->
            @if($tracking->status === 'OFFER_RECEIVED')
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 border-2 border-purple-200 dark:border-purple-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                            <span class="text-2xl">🎉</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Status: Offer Received</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Congratulations! You've received a job offer</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Offer Received Date -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-purple-100 dark:border-purple-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Offer Received Date</span>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $tracking->offer_received_at ? $tracking->offer_received_at->format('d M Y') : 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Last Update Date -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-purple-100 dark:border-purple-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Last Updated</span>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $tracking->updated_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accept Offer Button for OFFER_RECEIVED -->
            @if(isset($isStudentView) && $isStudentView)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border-2 border-green-200 dark:border-green-800">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Accept the Offer? ✅</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                If you've decided to accept the job offer, click the button below to proceed to "Accepted" status (Step 6). After accepting, you'll be able to upload confirmation proof.
                            </p>
                        </div>
                        @if(!isset($readOnly) || !$readOnly)
                        <form action="{{ route('student.placement.status.update') }}" method="POST" class="ml-4">
                            @csrf
                            <input type="hidden" name="status" value="ACCEPTED">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center gap-2">
                                <span>Accept the Offer</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                        </form>
                        @else
                        <button type="button" disabled class="px-8 py-3 bg-gray-400 text-white font-bold rounded-lg cursor-not-allowed flex items-center gap-2">
                            <span>Accept the Offer</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @endif

            <!-- SECTION: ACCEPTED STATUS SUMMARY (Step 6) -->
            @if($tracking->status === 'ACCEPTED')
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                @php
                    $hasProof = !empty($tracking->confirmation_proof_path);
                    $statusTitle = 'Accepted';
                    $statusMessage = $hasProof ? 'Your placement has been confirmed!' : 'Congratulations! You\'ve accepted the job offer';
                    $iconEmoji = $hasProof ? '✨' : '✅';
                @endphp
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border-2 border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                            <span class="text-2xl">{{ $iconEmoji }}</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Status: {{ $statusTitle }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $statusMessage }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Accepted Date -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-green-100 dark:border-green-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Accepted Date</span>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $tracking->accepted_at ? $tracking->accepted_at->format('d M Y') : 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if($hasProof)
                        <!-- Confirmation Proof Uploaded Date -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-emerald-100 dark:border-emerald-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Proof Uploaded</span>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $tracking->confirmed_at ? $tracking->confirmed_at->format('d M Y') : ($tracking->accepted_at ? $tracking->accepted_at->format('d M Y') : 'Not set') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @else
                        <!-- Last Update Date -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-green-100 dark:border-green-900/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Last Updated</span>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $tracking->updated_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($hasProof)
                    <!-- Proof Status -->
                    <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg p-5 shadow-sm border border-emerald-100 dark:border-emerald-900/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400 block">Proof Status</span>
                                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                                    Uploaded ✓
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- IMPORTANT WARNING: Do Not Accept Other Offers (Student only) -->
            @if(isset($isStudentView) && $isStudentView)
            <div class="mt-6 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-600 rounded-lg p-5 shadow-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-bold text-red-900 dark:text-red-200 mb-2 flex items-center gap-2">
                            <span>⚠️ Important Reminder</span>
                        </h3>
                        <div class="text-sm text-red-800 dark:text-red-200 space-y-2">
                            <p class="font-semibold">
                                <strong>You have accepted a job offer. This is a binding commitment.</strong>
                            </p>
                            <p>
                                <strong class="text-base">Please do NOT accept any other offers once you have accepted this offer.</strong>
                            </p>
                            <p class="text-xs mt-3 italic">
                                Think wisely and carefully consider your decision. Accepting multiple offers can have serious consequences including:
                            </p>
                            <ul class="list-disc list-inside mt-2 space-y-1 text-xs ml-2">
                                <li>Breach of professional ethics and trust</li>
                                <li>Potential legal implications</li>
                                <li>Damage to your professional reputation</li>
                                <li>Negative impact on future career opportunities</li>
                                <li>Harm to the university's relationship with employers</li>
                            </ul>
                            <p class="mt-3 font-semibold text-base">
                                Your acceptance is final. Please honor your commitment.
                            </p>
                        </div>
                    </div>
                </div>
                        </div>
                        @endif

            <!-- Confirmation Proof Upload Section (Student only) -->
            @if(isset($isStudentView) && $isStudentView)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-blue-200 dark:border-blue-800 p-6">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                    </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Submit Confirmation of Acceptance</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                Please upload your acknowledgment of acceptance or proof document to confirm your placement.
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                <strong>Accepted formats:</strong> PDF, JPG, JPEG, PNG (Max: 5MB)
                            </p>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                            </div>
                        </div>
                @endif

                    <form action="{{ route('student.placement.proof.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        
                        <!-- File Upload Area -->
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Upload Document <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-xl hover:border-blue-400 dark:hover:border-blue-500 transition-colors bg-gray-50 dark:bg-gray-700/50">
                                <div class="space-y-3 text-center w-full">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 005.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm leading-6 text-gray-600 dark:text-gray-400">
                                        <label for="proof" class="relative cursor-pointer rounded-md font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-2">
                                            <span>Upload a file</span>
                                            <input id="proof" name="proof" type="file" required accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-500 dark:text-gray-500">
                                        PDF, PNG, JPG, JPEG up to 5MB
                                    </p>
                                    <div id="fileName" class="hidden mt-2 text-sm text-blue-600 dark:text-blue-400 font-medium"></div>
                                </div>
            </div>

                            @error('proof')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- JavaScript for file name display -->
                        <script>
                            document.getElementById('proof').addEventListener('change', function(e) {
                                const fileNameDiv = document.getElementById('fileName');
                                if (e.target.files.length > 0) {
                                    const fileName = e.target.files[0].name;
                                    const fileSize = (e.target.files[0].size / 1024 / 1024).toFixed(2); // Size in MB
                                    fileNameDiv.innerHTML = `<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Selected: ${fileName} (${fileSize} MB)`;
                                    fileNameDiv.classList.remove('hidden');
                                } else {
                                    fileNameDiv.classList.add('hidden');
                                }
                            });
                        </script>

                        <!-- Information Box -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-blue-800 dark:text-blue-200">
                                    <p class="font-semibold mb-1">What to upload?</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Acceptance letter or email from the company</li>
                                        <li>Signed acknowledgment document</li>
                                        <li>Offer acceptance confirmation</li>
                                        <li>Any official proof of acceptance</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <span>Submit Confirmation</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            @endif

            <!-- CONFIRMED section removed - now combined with ACCEPTED (Step 6) -->
        </div>

        <!-- Section B: Placement Information (Combined) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-5 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Placement Information
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: Placement Details -->
                <div class="space-y-4">
                    <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Placement Details</h3>
                <div class="space-y-3">
                    @if($student->group)
                    <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">WBL Group</label>
                                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $student->group->name }}</p>
                    </div>
                    <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Training Period</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                            {{ $student->group->start_date->format('d M Y') }} - {{ $student->group->end_date->format('d M Y') }}
                        </p>
                    </div>
                    @endif
                    @if($student->programme)
                    <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Programme</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $student->programme }}</p>
                    </div>
                    @endif
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Placement Status</label>
                                <p class="mt-1">
                                    <span class="px-3 py-1.5 text-sm font-semibold rounded-full {{ $tracking->status_badge_color }}">
                                        {{ $tracking->status_display }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Supervisors -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Supervisors</h3>
                        <div class="space-y-3">
                    @if($student->industryCoach)
                    <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Industry Coach (IC)</label>
                                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $student->industryCoach->name }}</p>
                        @if($student->industryCoach->email)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $student->industryCoach->email }}</p>
                        @endif
                    </div>
                    @endif
                    @if($student->academicTutor)
                    <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Academic Tutor (AT)</label>
                                <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $student->academicTutor->name }}</p>
                    </div>
                    @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Company Information -->
                <div class="space-y-4">
                    <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Company Information</h3>
                        @if($student->company)
                            <div class="space-y-3">
                    <div>
                                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Company Name</label>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white mt-1">{{ $student->company->company_name }}</p>
                    </div>
                                @if($student->company->category)
                                <div>
                                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Industry Sector</label>
                                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $student->company->category }}</p>
                </div>
                                @endif
                                @if($student->company->address)
                                <div>
                                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Address</label>
                                    <p class="text-sm text-gray-900 dark:text-white mt-1 leading-relaxed">{{ $student->company->address }}</p>
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No company assigned yet.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Company Contact -->
                    @if($student->company)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Contact Details</h3>
                        <div class="space-y-3">
                            @if($student->company->pic_name)
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">PIC / HR Name</label>
                                <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $student->company->pic_name }}</p>
                            </div>
                            @endif
                            @if($student->company->email)
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Email</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1 break-all">{{ $student->company->email }}</p>
                            </div>
                            @endif
                            @if($student->company->phone)
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Phone</label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $student->company->phone }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section C: MoU / MoA Status -->
        @if($student->company)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">MoU / MoA Status</h2>
            <div class="space-y-4">
                @if($student->company->mou)
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">MoU Status</label>
                        <div class="mt-1">
                            @php
                                $mouStatus = $student->company->mou->status;
                                $badgeColor = match($mouStatus) {
                                    'Signed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'In Progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'Not Responding' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'Not Initiated' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                    'Expired' => 'bg-black text-white dark:bg-gray-900 dark:text-gray-100',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $badgeColor }}">
                                {{ $mouStatus }}
                            </span>
                        </div>
                        @if($student->company->mou->start_date)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Effective: {{ $student->company->mou->start_date->format('d M Y') }}
                            @if($student->company->mou->end_date)
                                - {{ $student->company->mou->end_date->format('d M Y') }}
                            @endif
                        </p>
                        @endif
                    </div>
                @else
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">MoU Status</label>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Not Available</p>
                    </div>
                @endif

                @if($student->company->moas->isNotEmpty())
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">MoA Status</label>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $student->company->moas->count() }} MoA(s) on file</p>
                    </div>
                @endif

                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-xs text-blue-800 dark:text-blue-200">
                        <strong>Note:</strong> MoU/MoA is managed by the faculty. Please contact coordinator for clarification.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Section D: Important Documents -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Important Documents</h2>
            <div class="space-y-3">
                @if($tracking->sal_file_path)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Student Application Letter (SAL)</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Released: {{ $tracking->sal_released_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('placement.student.sal.download', $student) }}" 
                       class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                        Download
                    </a>
                </div>
                @else
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400">SAL not yet released</p>
                </div>
                @endif

                @if($tracking->scl_file_path)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Student Confirmation Letter (SCL)</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Released: {{ $tracking->scl_released_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('placement.student.scl.download', $student) }}" 
                       class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                        Download
                    </a>
                </div>
                @else
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400">SCL not yet released</p>
                </div>
                @endif

                @if($tracking->confirmation_proof_path)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Confirmation Proof</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Uploaded</p>
                        </div>
                    </div>
                    @if(isset($isStudentView) && $isStudentView)
                        <a href="{{ route('student.placement.proof.view') }}" 
                           target="_blank"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg transition-colors">
                            View
                        </a>
                    @else
                        <a href="{{ route('placement.student.proof.view', $student) }}" 
                           target="_blank"
                           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg transition-colors">
                            View
                        </a>
                    @endif
                </div>
                @else
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No confirmation proof uploaded</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Section E: Activity Timeline (Optional) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Activity Timeline</h2>
            <div class="space-y-4">
                @if($tracking->sal_released_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-[#00A86B] mt-2"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">SAL Released</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tracking->sal_released_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
                @endif

                @if($student->company)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-[#0084C5] mt-2"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Company Assigned</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->company->company_name }}</p>
                    </div>
                </div>
                @endif

                @if($student->industryCoach)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-[#0084C5] mt-2"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Industry Coach Assigned</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->industryCoach->name }}</p>
                    </div>
                </div>
                @endif

                @if($tracking->scl_released_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-[#00A86B] mt-2"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">SCL Released</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tracking->scl_released_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
                @endif

                @if($tracking->updated_at)
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-2 h-2 rounded-full bg-gray-400 mt-2"></div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Last Updated</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tracking->updated_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
</div>
@endsection

