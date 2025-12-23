@extends('layouts.app')

@section('title', 'Resume & Poster Submission')

@section('content')
<div>
    <!-- 1. PAGE HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">Resume & Poster Submission</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-4">Before WBL Placement • Approval Required</p>
            
            <!-- Info Alert Banner -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="font-semibold text-blue-800 dark:text-blue-200">Only ONE combined PDF is accepted. Resume + Posters must be merged.</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6">
                <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-lg mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                    </div>
                </div>

                @if(session('submission_feedback'))
                    @php $feedback = session('submission_feedback'); @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-green-200 dark:border-green-800 p-5 mb-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5]">Submission Confirmed</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Document submitted successfully</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">File</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $feedback['file_name'] }}">{{ $feedback['file_name'] }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Size</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $feedback['file_size'] }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Submitted</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($feedback['submitted_at'])->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Status</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pending</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg">
                <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
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
                            Your WBL group has been completed and archived. You have <strong>read-only access</strong> to view your resume inspection information. 
                            You cannot upload or modify documents. All data remains available for historical records.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg">
                <p class="font-semibold text-red-800 dark:text-red-200 mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-1 text-sm text-red-700 dark:text-red-300">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- STATUS CARD - Prominent Display -->
        @if($inspection->resume_file_path)
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 {{ $inspection->isPassed() ? 'border-green-300 dark:border-green-700' : ($inspection->isRevisionRequired() ? 'border-orange-300 dark:border-orange-700' : 'border-yellow-300 dark:border-yellow-700') }} overflow-hidden">
                <!-- Status Header -->
                <div class="bg-gradient-to-r {{ $inspection->isPassed() ? 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/30' : ($inspection->isRevisionRequired() ? 'from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-900/30' : 'from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-900/30') }} px-6 py-4 border-b {{ $inspection->isPassed() ? 'border-green-200 dark:border-green-800' : ($inspection->isRevisionRequired() ? 'border-orange-200 dark:border-orange-800' : 'border-yellow-200 dark:border-yellow-800') }}">
                    <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                            <div class="w-12 h-12 {{ $inspection->isPassed() ? 'bg-green-100 dark:bg-green-900/30' : ($inspection->isRevisionRequired() ? 'bg-orange-100 dark:bg-orange-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30') }} rounded-full flex items-center justify-center">
                                @if($inspection->isPassed())
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($inspection->isRevisionRequired())
                                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $inspection->status_display }}</h2>
                                @if($inspection->isPassed())
                                    <p class="text-sm text-green-700 dark:text-green-300 font-medium">You can now apply for WBL placements</p>
                                    @if($inspection->approved_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Approved on {{ $inspection->approved_at->format('d M Y, h:i A') }}</p>
                                    @elseif($inspection->reviewed_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reviewed on {{ $inspection->reviewed_at->format('d M Y, h:i A') }}</p>
                                    @endif
                                @elseif($inspection->isRevisionRequired())
                                    <p class="text-sm text-orange-700 dark:text-orange-300 font-medium">Please review comments and resubmit</p>
                                    @if($inspection->reviewed_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Reviewed on {{ $inspection->reviewed_at->format('d M Y, h:i A') }}</p>
                                    @endif
                                @else
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 font-medium">Awaiting coordinator review</p>
                                    @if($inspection->created_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Submitted on {{ $inspection->created_at->format('d M Y, h:i A') }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $inspection->status_badge_color }}">
                    {{ $inspection->status_display }}
                </span>
                            @if($inspection->isPassed() && $inspection->approved_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $inspection->approved_at->format('d M Y') }}</p>
                            @elseif($inspection->isRevisionRequired() && $inspection->reviewed_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $inspection->reviewed_at->format('d M Y') }}</p>
                            @elseif($inspection->isPending() && $inspection->created_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $inspection->created_at->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Status Content -->
                <div class="p-6">
                    @if($inspection->isPending())
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-500">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-1">What to Expect</p>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">Review typically takes 3-5 business days. You'll be notified when the coordinator completes the review.</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Submitted:</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium ml-2">{{ $inspection->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Waiting:</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium ml-2">{{ $inspection->created_at->diffInDays(now()) }} day(s)</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($inspection->coordinator_comment)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Coordinator Feedback</p>
                @if($inspection->reviewed_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $inspection->reviewed_at->format('d M Y, h:i A') }}</p>
                                    @endif
                                </div>
                                @if($inspection->reviewer)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">by {{ $inspection->reviewer->name }}</p>
                                @endif
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-l-4 border-blue-500">
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $inspection->coordinator_comment }}</p>
                            </div>

                            @if($inspection->student_reply)
                                <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border-l-4 border-green-500">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Your Reply</p>
                                    @if($inspection->student_replied_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $inspection->student_replied_at->format('d M Y, h:i A') }}</p>
                                    @endif
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $inspection->student_reply }}</p>
                                </div>
                            @endif

                            <div class="mt-4">
                                <form action="{{ route('student.resume.reply') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <textarea name="reply" rows="3" required 
                                              placeholder="Type your reply to the coordinator's comment..."
                                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white text-sm"></textarea>
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Maximum 2000 characters</p>
                                        <button type="submit" class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                                            {{ $inspection->student_reply ? 'Update Reply' : 'Submit Reply' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- 2. STEP-BY-STEP INSTRUCTION CARD -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6" x-data="{ open: false }">
            <div class="flex items-center justify-between mb-4 cursor-pointer" @click="open = !open">
                <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Step-by-Step Instructions
                </h2>
                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            
            <div x-show="open" x-transition class="space-y-5 pt-4 border-t border-gray-200 dark:border-gray-700">
                <!-- STEP 1 -->
                <div class="border-l-4 border-[#0084C5] pl-4">
                    <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-2 flex items-center">
                        <span class="bg-[#0084C5] text-white rounded-full w-7 h-7 flex items-center justify-center text-xs font-bold mr-3">1</span>
                        Document Format
                    </h3>
                    <ul class="space-y-1.5 text-sm text-gray-700 dark:text-gray-300 ml-10">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Only <strong>ONE (1) PDF file</strong> is accepted</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Resume must come <strong>FIRST</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Followed by: <strong>PD3 → PD4 → PD5</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-red-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span><strong>Word, image, or ZIP files are NOT accepted</strong></span>
                        </li>
                    </ul>
                </div>

                <!-- STEP 2 -->
                <div class="border-l-4 border-[#0084C5] pl-4">
                    <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-2 flex items-center">
                        <span class="bg-[#0084C5] text-white rounded-full w-7 h-7 flex items-center justify-center text-xs font-bold mr-3">2</span>
                        Resume Guidelines (1–2 pages)
                    </h3>
                    <div class="ml-10 space-y-2 text-sm">
                        <p class="text-gray-700 dark:text-gray-300 font-medium">Must include:</p>
                        <ul class="space-y-1 text-gray-700 dark:text-gray-300 list-disc list-inside ml-4">
                            <li>Full Name, Matric Number, Programme</li>
                            <li>Contact Details, Skills & Software</li>
                        </ul>
                        <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded text-xs text-gray-600 dark:text-gray-400 mt-2">
                            <strong>Tip:</strong> Use concise layout, bullet points. Highlight achievements, not just activities.
                        </div>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div class="border-l-4 border-[#0084C5] pl-4">
                    <h3 class="text-lg font-semibold text-[#003A6C] dark:text-[#0084C5] mb-2 flex items-center">
                        <span class="bg-[#0084C5] text-white rounded-full w-7 h-7 flex items-center justify-center text-xs font-bold mr-3">3</span>
                        Poster Portfolio (PD3, PD4, PD5)
                    </h3>
                    <div class="ml-10 space-y-2 text-sm">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-2 rounded text-xs">
                            <p class="font-semibold text-yellow-800 dark:text-yellow-200">⚠️ POSTER projects only (not normal portfolio)</p>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Page limits:</strong> PD3 (1-2), PD4 (1-2), PD5 (1-2) = Max 6 pages total</p>
                        <p class="text-gray-700 dark:text-gray-300"><strong>Each poster must show:</strong> Project title, Problem statement, Concept, Key visuals, Tools used, Your contribution, Final outcome</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. ACHIEVEMENT & CONTRIBUTION HIGHLIGHT -->
        <div class="bg-gradient-to-r from-[#0084C5] to-[#003A6C] rounded-xl shadow-md p-5 mb-6 text-white">
            <h2 class="text-lg font-bold mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                Focus on WHAT YOU DID and WHAT YOU ACHIEVED
            </h2>
            <div class="grid md:grid-cols-2 gap-3">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <p class="text-xs font-semibold mb-1.5 flex items-center">
                        <svg class="w-4 h-4 text-green-300 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        GOOD:
                    </p>
                    <ul class="space-y-1 text-xs text-white/90 list-disc list-inside ml-4">
                        <li>Designed product concept based on user feedback</li>
                        <li>Developed prototype for Product Design 4</li>
                        <li>Applied CAD modelling and rendering</li>
                    </ul>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                    <p class="text-xs font-semibold mb-1.5 flex items-center">
                        <svg class="w-4 h-4 text-red-300 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        AVOID:
                    </p>
                    <ul class="space-y-1 text-xs text-white/90 list-disc list-inside ml-4">
                        <li>Did group project</li>
                        <li>Participated in class</li>
                        <li>Worked with team</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 4. SAMPLE REFERENCES -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 mb-6" x-data="{ open: false }">
            <div class="flex items-center justify-between mb-4 cursor-pointer" @click="open = !open">
                <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Reference Samples
                </h2>
                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div x-show="open" x-transition class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-2 rounded mb-4 text-xs">
                    <p class="text-yellow-800 dark:text-yellow-200 font-medium">⚠️ For reference only. Do NOT copy directly.</p>
                </div>
                <div class="grid md:grid-cols-3 gap-3">
                    @foreach([1 => 'Good Resume Structure', 2 => 'Good Poster Layout', 3 => 'Strong Achievement Example'] as $num => $title)
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 text-[#0084C5] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200">Sample {{ $num }}</h3>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $title }}</p>
                            <a href="{{ route('student.resume.sample', ['sample' => $num]) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-[#0084C5] hover:bg-[#003A6C] text-white rounded text-xs font-medium transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- COMPLIANCE CHECKLIST (Disabled for completed groups) -->
        @if(!isset($isInCompletedGroup) || !$isInCompletedGroup)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 mb-6 border-l-4 border-yellow-500" x-data="{ open: true }">
            <div class="flex items-center justify-between mb-4 cursor-pointer" @click="open = !open">
                <div>
                    <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Compliance Declaration Checklist
                    </h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Confirm ALL items before submitting</p>
                </div>
                <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div x-show="open" x-transition>
                <form id="checklistForm" action="{{ route('student.resume.save-checklist') }}" method="POST" x-data="checklistForm()">
                    @csrf
                    <div class="space-y-3 text-sm">
                        @foreach([
                            'checklist_merged_pdf' => 'I have merged Resume and Poster Projects (PD3, PD4, PD5) into ONE (1) single PDF file.',
                            'checklist_document_order' => 'Document order is: Resume → Poster PD3 → Poster PD4 → Poster PD5.',
                            'checklist_resume_concise' => 'My Resume is concise (1–2 pages) and uses space efficiently.',
                            'checklist_achievements_highlighted' => 'I have highlighted my ACHIEVEMENTS and CONTRIBUTIONS, not just activities.',
                            'checklist_poster_includes_required' => 'Each Poster includes: Project title, Problem statement, Concept, Key visuals, Tools used, My contribution.',
                            'checklist_poster_pages_limit' => 'Total poster pages do NOT exceed 6 pages.',
                            'checklist_own_work_ready' => 'This document is my own work and ready for coordinator review.',
                        ] as $field => $label)
                            <div class="flex items-start">
                                <input type="checkbox" 
                                       name="{{ $field }}" 
                                       value="1"
                                       x-model="checklist.{{ str_replace('checklist_', '', $field) }}"
                                       @change="updateChecklistStatus()"
                                       class="mt-1 w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5] focus:ring-2"
                                       {{ $inspection->$field ? 'checked' : '' }}>
                                <label class="ml-2.5 text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 p-2.5 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded text-xs" x-show="!allChecked" x-transition>
                        <p class="text-yellow-800 dark:text-yellow-200 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Please complete all declarations before submitting.
                        </p>
                    </div>

                    <div class="mt-4">
                        <button type="submit" 
                                class="px-5 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                                :disabled="!allChecked">
                            Save Checklist
                        </button>
                    </div>
                </form>

                @if($inspection->checklist_confirmed_at)
                    <div class="mt-3 p-2.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded text-xs">
                        <p class="text-green-800 dark:text-green-200 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Confirmed on {{ $inspection->checklist_confirmed_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- UPLOAD SECTION (Disabled for completed groups) -->
        @if(!isset($isInCompletedGroup) || !$isInCompletedGroup)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 mb-6">
            <h2 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Upload Document
            </h2>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-4 text-sm">
                <p class="font-semibold text-blue-800 dark:text-blue-200 mb-1">Upload Rules:</p>
                <ul class="list-disc list-inside space-y-0.5 text-blue-700 dark:text-blue-300 text-xs ml-1">
                    <li>PDF only • Max 15MB</li>
                    <li>Resume + Posters (PD3, PD4, PD5) merged in order</li>
                </ul>
            </div>

            <form action="{{ route('student.resume.upload-document') }}" method="POST" enctype="multipart/form-data" class="space-y-4" 
                  x-data="{ checklistComplete: {{ $inspection->isChecklistComplete() ? 'true' : 'false' }} }"
                  @checklist-status-changed.window="checklistComplete = $event.detail.complete">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select PDF File</label>
                    <input type="file" name="document" accept=".pdf" required 
                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-[#0084C5] file:text-white
                                  hover:file:bg-[#003A6C]
                                  cursor-pointer
                                  {{ $errors->has('document') ? 'border-red-500' : '' }}">
                    @error('document')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-2.5 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded text-xs" x-show="!checklistComplete" x-transition>
                    <p class="text-yellow-800 dark:text-yellow-200 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Complete all compliance checklist items above first.
                    </p>
                </div>
                
                <button type="submit" 
                        :disabled="!checklistComplete"
                        class="w-full md:w-auto px-6 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:bg-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    {{ $inspection->resume_file_path ? 'Replace Document' : 'Upload / Submit Document' }}
                </button>
            </form>
            
            @if($inspection->resume_file_path)
                <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                            <div>
                                <p class="text-sm font-semibold text-green-800 dark:text-green-200">Document uploaded</p>
                                <p class="text-xs text-green-600 dark:text-green-400 mt-0.5 truncate max-w-xs">{{ basename($inspection->resume_file_path) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('student.resume.view-document', $inspection) }}" target="_blank"
                               class="text-xs text-[#0084C5] hover:text-[#003A6C] font-medium px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                View
                            </a>
                            <a href="{{ route('student.resume.download-document', $inspection) }}" 
                               class="text-xs text-[#0084C5] hover:text-[#003A6C] font-medium px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                Download
                            </a>
                            @if($inspection->status === 'PENDING')
                            <form action="{{ route('student.resume.delete-document') }}" method="POST" 
                                      onsubmit="return confirm('Are you sure? You will need to upload a new document.');"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endif
</div>

@endsection

@push('scripts')
<script>
function checklistForm() {
    return {
        checklist: {
            merged_pdf: {{ $inspection->checklist_merged_pdf ? 'true' : 'false' }},
            document_order: {{ $inspection->checklist_document_order ? 'true' : 'false' }},
            resume_concise: {{ $inspection->checklist_resume_concise ? 'true' : 'false' }},
            achievements_highlighted: {{ $inspection->checklist_achievements_highlighted ? 'true' : 'false' }},
            poster_includes_required: {{ $inspection->checklist_poster_includes_required ? 'true' : 'false' }},
            poster_pages_limit: {{ $inspection->checklist_poster_pages_limit ? 'true' : 'false' }},
            own_work_ready: {{ $inspection->checklist_own_work_ready ? 'true' : 'false' }},
        },
        get allChecked() {
            return this.checklist.merged_pdf &&
                   this.checklist.document_order &&
                   this.checklist.resume_concise &&
                   this.checklist.achievements_highlighted &&
                   this.checklist.poster_includes_required &&
                   this.checklist.poster_pages_limit &&
                   this.checklist.own_work_ready;
        },
        updateChecklistStatus() {
            window.dispatchEvent(new CustomEvent('checklist-status-changed', { 
                detail: { complete: this.allChecked } 
            }));
        }
    }
}
</script>
@endpush
