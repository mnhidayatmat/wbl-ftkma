@extends('layouts.app')

@section('title', 'View Project Proposal')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('academic.fyp.proposals.index') }}"
                   class="text-[#0084C5] hover:text-[#003A6C] text-sm font-medium mb-2 inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Proposals
                </a>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Project Proposal Review</h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    @if($proposal->status === 'draft') bg-gray-100 text-gray-800
                    @elseif($proposal->status === 'submitted') bg-yellow-100 text-yellow-800
                    @elseif($proposal->status === 'approved') bg-green-100 text-green-800
                    @elseif($proposal->status === 'rejected') bg-red-100 text-red-800
                    @endif">
                    {{ $proposal->status_label }}
                </span>
                <a href="{{ route('academic.fyp.proposals.pdf', $proposal) }}"
                   target="_blank"
                   class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Export PDF
                </a>
            </div>
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

        <!-- Proposal Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6">
            <!-- Header Section with Student Info -->
            <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <!-- Student Photo -->
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                            @if($proposal->student->image_path)
                                <img src="{{ asset('storage/' . $proposal->student->image_path) }}"
                                     alt="{{ $proposal->student->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="text-center mt-2">
                            <p class="text-white font-semibold text-sm">{{ $proposal->student->name }}</p>
                            <p class="text-blue-200 text-xs">{{ $proposal->student->matric_no }}</p>
                        </div>
                    </div>

                    <!-- Student Details -->
                    <div class="flex-grow text-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Industry Coach</p>
                                <p class="font-semibold">
                                    {{ $proposal->student->industryCoach->name ?? 'Not Assigned' }}
                                    @if($proposal->student->industryCoach && $proposal->student->industryCoach->position)
                                        <span class="text-blue-200 text-sm">({{ $proposal->student->industryCoach->position }})</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Academic Tutor</p>
                                <p class="font-semibold">{{ $proposal->student->academicTutor->name ?? 'Not Assigned' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Company</p>
                                <p class="font-semibold">{{ $proposal->student->company->company_name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Title -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xs font-bold text-[#003A6C] dark:text-[#0084C5] uppercase tracking-wider mb-2">Project Title</h2>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $proposal->project_title }}</p>
            </div>

            <!-- Proposal Items Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-[#003A6C]">
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider w-[5%]">#</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider w-[30%]">Problem Statement</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider w-[30%]">Objective</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider w-[35%]">Methodology</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($proposal->proposal_items ?? [] as $index => $item)
                        <tr class="bg-[#FFFDE7] dark:bg-gray-700/50">
                            <td class="px-4 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $item['problem_statement'] ?? '' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $item['objective'] ?? '' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $item['methodology'] ?? '' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No proposal items found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Submission Info -->
            <div class="p-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Submitted At</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $proposal->submitted_at ? $proposal->submitted_at->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>
                    @if($proposal->approved_at)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Approved At</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $proposal->approved_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Approved By</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $proposal->approver->name ?? '-' }}
                        </p>
                    </div>
                    @endif
                </div>
                @if($proposal->remarks)
                <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Remarks:</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $proposal->remarks }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons for Pending Review -->
        @if($proposal->status === 'submitted')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-[#003A6C] dark:text-[#0084C5] mb-4">Review Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Approve -->
                <div class="border border-green-200 dark:border-green-800 rounded-lg p-4 bg-green-50 dark:bg-green-900/20">
                    <form action="{{ route('academic.fyp.proposals.approve', $proposal) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-green-800 dark:text-green-300 mb-2">Approve Proposal</label>
                            <textarea name="remarks"
                                      rows="3"
                                      placeholder="Add any comments (optional)..."
                                      class="w-full px-3 py-2 text-sm border border-green-300 dark:border-green-700 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-800 dark:text-white"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Approve Proposal
                        </button>
                    </form>
                </div>

                <!-- Reject / Request Revision -->
                <div class="border border-red-200 dark:border-red-800 rounded-lg p-4 bg-red-50 dark:bg-red-900/20">
                    <form action="{{ route('academic.fyp.proposals.reject', $proposal) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Request Revision</label>
                            <textarea name="remarks"
                                      rows="3"
                                      required
                                      placeholder="Explain what needs to be revised..."
                                      class="w-full px-3 py-2 text-sm border border-red-300 dark:border-red-700 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-800 dark:text-white"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Request Revision
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
