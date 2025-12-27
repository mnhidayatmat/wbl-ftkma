@extends('layouts.app')

@section('title', 'OSH Assessment Schedule')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">OSH Assessment Schedule</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage assessment windows and control when evaluations can be submitted</p>
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

        @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Assessment Window Control -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Assessment Window Control</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Lecturer Evaluation Window -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lecturer Evaluation (40%)</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Applies to: <strong>{{ $lecturerWindow->selected_assessments_display }}</strong>
                            </p>
                        </div>
                        @if($lecturerWindow->status === 'open')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                Open
                            </span>
                        @elseif($lecturerWindow->status === 'closed')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                Closed
                            </span>
                        @elseif($lecturerWindow->status === 'upcoming')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                Upcoming
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                Disabled
                            </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('academic.osh.schedule.update-window') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="evaluator_role" value="lecturer">
                        
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   name="is_enabled"
                                   id="lecturer_enabled"
                                   value="1"
                                   {{ $lecturerWindow->is_enabled ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                            <label for="lecturer_enabled" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Enable Lecturer Evaluation
                            </label>
                        </div>

                        <!-- Assessment Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assessments
                                <span class="text-xs text-gray-500 ml-1">(Leave empty to apply to all assessments)</span>
                            </label>
                            <select name="assessment_ids[]"
                                    multiple
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"
                                    size="5">
                                @foreach($lecturerAssessments as $assessment)
                                    <option value="{{ $assessment->id }}"
                                            {{ $lecturerWindow->assessments->contains($assessment->id) ? 'selected' : '' }}>
                                        {{ $assessment->assessment_name }} ({{ $assessment->assessment_type }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Hold Ctrl (Windows) or Cmd (Mac) to select multiple assessments.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date & Time</label>
                            <input type="datetime-local"
                                   name="start_at"
                                   value="{{ $lecturerWindow->start_at ? $lecturerWindow->start_at->format('Y-m-d\TH:i') : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date & Time</label>
                            <input type="datetime-local" 
                                   name="end_at" 
                                   value="{{ $lecturerWindow->end_at ? $lecturerWindow->end_at->format('Y-m-d\TH:i') : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                            <textarea name="notes" 
                                      rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">{{ $lecturerWindow->notes }}</textarea>
                        </div>

                        <button type="submit" 
                                class="w-full px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                            Save Lecturer Window
                        </button>
                    </form>
                </div>

                <!-- IC Evaluation Window -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Industry Coach Evaluation (60%)</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                Applies to: <strong>{{ $icWindow->selected_assessments_display }}</strong>
                            </p>
                        </div>
                        @if($icWindow->status === 'open')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                Open
                            </span>
                        @elseif($icWindow->status === 'closed')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                Closed
                            </span>
                        @elseif($icWindow->status === 'upcoming')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                Upcoming
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                Disabled
                            </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('academic.osh.schedule.update-window') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="evaluator_role" value="ic">
                        
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   name="is_enabled"
                                   id="ic_enabled"
                                   value="1"
                                   {{ $icWindow->is_enabled ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#0084C5] border-gray-300 rounded focus:ring-[#0084C5]">
                            <label for="ic_enabled" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Enable IC Evaluation
                            </label>
                        </div>

                        <!-- Assessment Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assessments
                                <span class="text-xs text-gray-500 ml-1">(Leave empty to apply to all assessments)</span>
                            </label>
                            <select name="assessment_ids[]"
                                    multiple
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white"
                                    size="5">
                                @foreach($icAssessments as $assessment)
                                    <option value="{{ $assessment->id }}"
                                            {{ $icWindow->assessments->contains($assessment->id) ? 'selected' : '' }}>
                                        {{ $assessment->assessment_name }} ({{ $assessment->assessment_type }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Hold Ctrl (Windows) or Cmd (Mac) to select multiple assessments.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date & Time</label>
                            <input type="datetime-local"
                                   name="start_at"
                                   value="{{ $icWindow->start_at ? $icWindow->start_at->format('Y-m-d\TH:i') : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date & Time</label>
                            <input type="datetime-local" 
                                   name="end_at" 
                                   value="{{ $icWindow->end_at ? $icWindow->end_at->format('Y-m-d\TH:i') : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                            <textarea name="notes" 
                                      rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">{{ $icWindow->notes }}</textarea>
                        </div>

                        <button type="submit" 
                                class="w-full px-4 py-2 bg-[#00AEEF] hover:bg-[#0084C5] text-white font-semibold rounded-lg transition-colors">
                            Save IC Window
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Information Card -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">How Assessment Windows Work</h3>
                    <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                        <li>• <strong>Open:</strong> Evaluators can submit marks</li>
                        <li>• <strong>Closed:</strong> Evaluation period has ended, no new submissions allowed</li>
                        <li>• <strong>Upcoming:</strong> Window is scheduled but not yet open</li>
                        <li>• <strong>Disabled:</strong> Window is turned off, no evaluations allowed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
