@extends('layouts.app')

@section('title', 'Resume & Poster Submission')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- HEADER -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-3 mb-3">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-[#003A6C] dark:text-[#0084C5]">Resume & Poster Submission</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Before WBL Placement • Approval Required</p>
            </div>
            @if($inspection && $inspection->resume_file_path)
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $inspection->status_badge_color }}">
                    {{ $inspection->status_display }}
                </span>
            @endif
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-3 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-semibold text-blue-800 dark:text-blue-200">Only ONE combined PDF: Resume + Posters (PD3, PD4, PD5) merged in order</p>
            </div>
        </div>
    </div>

    <!-- ALERTS -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded-lg">
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded-lg">
            <p class="font-semibold text-sm text-red-800 dark:text-red-200 mb-1">Errors:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs text-red-700 dark:text-red-300">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($isInCompletedGroup) && $isInCompletedGroup)
        <div class="mb-6 bg-gray-100 dark:bg-gray-700 border-l-4 border-gray-500 p-3 rounded-lg">
            <p class="font-semibold text-sm text-gray-800 dark:text-gray-200">WBL Group Completed - Read-only access</p>
            <p class="text-xs text-gray-700 dark:text-gray-300 mt-1">Your group is archived. You cannot upload or modify documents.</p>
        </div>
    @endif

    <!-- 2-COLUMN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT COLUMN (2/3 width) - Primary Actions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- CURRENT STATUS & FEEDBACK -->
            @if($inspection && $inspection->resume_file_path)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-2 {{ $inspection->isPassed() ? 'border-green-300' : ($inspection->isRevisionRequired() ? 'border-orange-300' : 'border-yellow-300') }} overflow-hidden">
                    <div class="px-5 py-4 bg-gradient-to-r {{ $inspection->isPassed() ? 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/30' : ($inspection->isRevisionRequired() ? 'from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-900/30' : 'from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-900/30') }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 {{ $inspection->isPassed() ? 'bg-green-100' : ($inspection->isRevisionRequired() ? 'bg-orange-100' : 'bg-yellow-100') }} rounded-full flex items-center justify-center flex-shrink-0">
                                @if($inspection->isPassed())
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @elseif($inspection->isRevisionRequired())
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                @else
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5]">{{ $inspection->status_display }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    @if($inspection->isPassed())
                                        Approved {{ $inspection->approved_at ? 'on ' . $inspection->approved_at->format('d M Y') : '' }}
                                    @elseif($inspection->isRevisionRequired())
                                        Reviewed {{ $inspection->reviewed_at ? 'on ' . $inspection->reviewed_at->format('d M Y') : '' }}
                                    @else
                                        Submitted {{ $inspection->created_at ? $inspection->created_at->diffForHumans() : '' }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($inspection->coordinator_comment || $inspection->isPending())
                        <div class="p-5 space-y-3">
                            @if($inspection->isPending())
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-sm">
                                    <p class="font-semibold text-blue-800 dark:text-blue-200 mb-1">What to Expect</p>
                                    <p class="text-xs text-blue-700 dark:text-blue-300">Review typically takes 3-5 business days.</p>
                                </div>
                            @endif

                            @if($inspection->coordinator_comment)
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Coordinator Feedback</p>
                                        @if($inspection->reviewer)
                                            <p class="text-xs text-gray-500">by {{ $inspection->reviewer->name }}</p>
                                        @endif
                                    </div>
                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-l-4 border-blue-500">
                                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $inspection->coordinator_comment }}</p>
                                    </div>

                                    @if($inspection->student_reply)
                                        <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border-l-4 border-green-500">
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Your Reply ({{ $inspection->student_replied_at ? $inspection->student_replied_at->format('d M Y') : '' }})</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $inspection->student_reply }}</p>
                                        </div>
                                    @endif

                                    <form action="{{ route('student.resume.reply') }}" method="POST" class="space-y-2">
                                        @csrf
                                        <textarea name="reply" rows="2" required placeholder="Reply to coordinator..." class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-700 dark:text-white"></textarea>
                                        <button type="submit" class="px-4 py-1.5 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors">
                                            {{ $inspection->student_reply ? 'Update Reply' : 'Submit Reply' }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <!-- REVIEW HISTORY -->
            @if(isset($history) && $history->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Review History
                        </h3>
                        <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full">{{ $history->count() }} {{ Str::plural('entry', $history->count()) }}</span>
                    </div>

                    <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                        @foreach($history as $entry)
                            @php
                                $isStudentAction = $entry->isStudentAction();
                                $borderColor = match(true) {
                                    $entry->action === 'APPROVED' => 'border-green-500',
                                    $entry->action === 'REVISION_REQUESTED' => 'border-orange-500',
                                    $entry->action === 'RESET' => 'border-red-500',
                                    $isStudentAction => 'border-purple-500',
                                    default => 'border-blue-500',
                                };
                                $bgColor = $isStudentAction ? 'bg-purple-50 dark:bg-purple-900/20' : 'bg-gray-50 dark:bg-gray-700/50';
                            @endphp
                            <div class="border-l-4 {{ $borderColor }} {{ $bgColor }} rounded-r-lg p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold {{ $entry->action_icon_color }}">
                                            @if($entry->action === 'APPROVED')
                                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            @elseif($entry->action === 'REVISION_REQUESTED')
                                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            @elseif($entry->action === 'RESET')
                                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            @elseif($isStudentAction)
                                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                            @endif
                                            {{ $entry->action_label }}
                                        </span>
                                        @if($isStudentAction)
                                            <span class="text-xs px-1.5 py-0.5 bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded">Student</span>
                                        @else
                                            <span class="text-xs px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded">Coordinator</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $entry->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">by {{ $entry->actor_name ?? 'Unknown' }}</p>
                                @if($entry->comment)
                                    <div class="bg-white dark:bg-gray-800 rounded p-2 mt-2 border {{ $isStudentAction ? 'border-purple-200 dark:border-purple-700' : 'border-gray-200 dark:border-gray-600' }}">
                                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $entry->comment }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- COMPLIANCE CHECKLIST (if not in completed group) -->
            @if(!isset($isInCompletedGroup) || !$isInCompletedGroup)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Compliance Checklist
                        </h3>
                        @if($inspection && $inspection->checklist_confirmed_at)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">✓ Confirmed</span>
                        @endif
                    </div>

                    <form action="{{ route('student.resume.save-checklist') }}" method="POST" x-data="checklistForm()">
                        @csrf
                        <div class="space-y-2 text-sm">
                            @foreach([
                                'checklist_merged_pdf' => 'Merged Resume + Posters into ONE PDF',
                                'checklist_document_order' => 'Correct order: Resume → PD3 → PD4 → PD5',
                                'checklist_resume_concise' => 'Resume is concise (1–2 pages)',
                                'checklist_achievements_highlighted' => 'Highlighted achievements & contributions',
                                'checklist_poster_includes_required' => 'Each poster includes required elements',
                                'checklist_poster_pages_limit' => 'Total poster pages ≤ 6 pages',
                                'checklist_own_work_ready' => 'Document is my own work, ready for review',
                            ] as $field => $label)
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" name="{{ $field }}" value="1"
                                           x-model="checklist.{{ str_replace('checklist_', '', $field) }}"
                                           @change="updateChecklistStatus()"
                                           class="mt-0.5 w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]"
                                           {{ $inspection && $inspection->$field ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300 group-hover:text-[#0084C5] text-sm">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <p class="text-xs text-gray-500" x-show="!allChecked">Complete all items to enable upload</p>
                            <button type="submit" :disabled="!allChecked"
                                    class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                Save Checklist
                            </button>
                        </div>
                    </form>
                </div>

                <!-- UPLOAD SECTION -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
                    <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Upload Document
                    </h3>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 rounded-lg p-2 mb-3 text-xs text-blue-700 dark:text-blue-300">
                        <strong>Rules:</strong> PDF only • Max 15MB • Resume + Posters merged
                    </div>

                    <form action="{{ route('student.resume.upload-document') }}" method="POST" enctype="multipart/form-data"
                          x-data="{ checklistComplete: {{ $inspection && $inspection->isChecklistComplete() ? 'true' : 'false' }} }"
                          @checklist-status-changed.window="checklistComplete = $event.detail.complete" class="space-y-3">
                        @csrf
                        <input type="file" name="document" accept=".pdf" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0084C5] file:text-white hover:file:bg-[#003A6C] cursor-pointer">

                        <button type="submit" :disabled="!checklistComplete"
                                class="w-full px-6 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            {{ $inspection && $inspection->resume_file_path ? 'Replace Document' : 'Upload Document' }}
                        </button>
                    </form>

                    @if($inspection && $inspection->resume_file_path)
                        <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm font-semibold text-green-800">Uploaded: {{ basename($inspection->resume_file_path) }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('student.resume.view-document', $inspection) }}" target="_blank" class="text-xs text-[#0084C5] hover:underline font-medium">View</a>
                                <a href="{{ route('student.resume.download-document', $inspection) }}" class="text-xs text-[#0084C5] hover:underline font-medium">Download</a>
                                @if($inspection->status === 'PENDING')
                                    <form action="{{ route('student.resume.delete-document') }}" method="POST" onsubmit="return confirm('Delete this document?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:underline font-medium">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN (1/3 width) - Quick Reference -->
        <div class="space-y-6">
            <!-- QUICK REFERENCE -->
            <div class="bg-gradient-to-br from-[#0084C5] to-[#003A6C] rounded-xl shadow-md p-5 text-white">
                <h3 class="text-lg font-bold mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Quick Reference
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="font-semibold mb-1">✓ Document Format</p>
                        <ul class="text-xs space-y-0.5 text-white/90">
                            <li>• ONE PDF file only</li>
                            <li>• Resume first, then PD3→PD4→PD5</li>
                            <li>• Max 15MB file size</li>
                        </ul>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="font-semibold mb-1">✓ Resume (1-2 pages)</p>
                        <ul class="text-xs space-y-0.5 text-white/90">
                            <li>• Name, matric, programme</li>
                            <li>• Contact & skills</li>
                            <li>• Highlight achievements</li>
                        </ul>
                    </div>
                    <div class="bg-white/10 rounded-lg p-3">
                        <p class="font-semibold mb-1">✓ Posters (Max 6 pages)</p>
                        <ul class="text-xs space-y-0.5 text-white/90">
                            <li>• PD3, PD4, PD5 (1-2 pages each)</li>
                            <li>• Show: Title, concept, visuals</li>
                            <li>• Your contribution & tools used</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- SAMPLE DOWNLOADS -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5">
                <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Reference Samples
                </h3>
                <div class="space-y-2">
                    @php
                        $allSamples = collect();
                        if(isset($referenceSamples)) {
                            $allSamples = $referenceSamples->flatten();
                        }
                    @endphp

                    @if($allSamples->isNotEmpty())
                        @foreach($allSamples as $sample)
                            <a href="{{ route('reference-samples.download', $sample) }}"
                               class="flex items-center justify-between p-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">{{ $sample->category_icon }}</span>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $sample->title }}</span>
                                        @if($sample->description)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($sample->description, 50) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400">{{ $sample->file_size_formatted }}</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                            </a>
                        @endforeach
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">⚠️ For reference only. Do NOT copy directly.</p>
                    @else
                        <div class="text-center py-4">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No reference samples available yet</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Check back later for examples</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- KEY TIPS -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
                <h4 class="text-sm font-bold text-yellow-800 dark:text-yellow-200 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    Pro Tips
                </h4>
                <ul class="text-xs text-yellow-800 dark:text-yellow-200 space-y-1">
                    <li>✓ Use concise, action-oriented language</li>
                    <li>✓ Show WHAT you achieved, not just activities</li>
                    <li>✓ Quantify results when possible (e.g., "improved by 30%")</li>
                    <li>✗ Avoid generic phrases like "participated in"</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function checklistForm() {
    return {
        checklist: {
            merged_pdf: {{ $inspection && $inspection->checklist_merged_pdf ? 'true' : 'false' }},
            document_order: {{ $inspection && $inspection->checklist_document_order ? 'true' : 'false' }},
            resume_concise: {{ $inspection && $inspection->checklist_resume_concise ? 'true' : 'false' }},
            achievements_highlighted: {{ $inspection && $inspection->checklist_achievements_highlighted ? 'true' : 'false' }},
            poster_includes_required: {{ $inspection && $inspection->checklist_poster_includes_required ? 'true' : 'false' }},
            poster_pages_limit: {{ $inspection && $inspection->checklist_poster_pages_limit ? 'true' : 'false' }},
            own_work_ready: {{ $inspection && $inspection->checklist_own_work_ready ? 'true' : 'false' }},
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
