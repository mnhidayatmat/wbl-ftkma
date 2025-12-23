@extends('layouts.app')

@section('title', 'Moderate Student - ' . $student->name)

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <a href="{{ route('academic.ppe.moderation.index') }}" 
               class="inline-flex items-center text-[#0084C5] hover:text-[#003A6C] transition-colors mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Moderation
            </a>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Moderate Student: {{ $student->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Apply moderation adjustments to student scores</p>
        </div>

        <!-- Student Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Matric No</div>
                    <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $student->matric_no }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Group</div>
                    <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $student->group->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Programme</div>
                    <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $student->programme ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Company</div>
                    <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $student->company->name ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Original Scores Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Original Scores</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Lecturer Score</div>
                    <div class="text-2xl font-bold text-[#0084C5]">{{ number_format($lecturerTotal, 2) }}% / 40%</div>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">IC Score</div>
                    <div class="text-2xl font-bold text-[#00AEEF]">{{ number_format($icTotal, 2) }}% / 60%</div>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Final Score</div>
                    <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">{{ number_format($finalScore, 2) }}% / 100%</div>
                </div>
            </div>
        </div>

        <!-- Moderation Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Apply Moderation</h2>

            @if($moderation)
            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <div class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">Existing Moderation Found</div>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            This student has been moderated. Current adjusted score: <strong>{{ number_format($moderation->adjusted_final_score, 2) }}%</strong>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('academic.ppe.moderation.store', $student) }}" x-data="{ adjustmentType: '{{ $moderation->adjustment_type ?? 'percentage' }}' }">
                @csrf

                <!-- Adjustment Type -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Adjustment Type <span class="text-red-500">*</span>
                    </label>
                    <select name="adjustment_type" 
                            x-model="adjustmentType"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <option value="percentage" {{ ($moderation->adjustment_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage Adjustment</option>
                        <option value="manual_override" {{ ($moderation->adjustment_type ?? '') === 'manual_override' ? 'selected' : '' }}>Manual Override</option>
                    </select>
                </div>

                <!-- Percentage Adjustment -->
                <div x-show="adjustmentType === 'percentage'" class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Adjustment Percentage <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" 
                               name="adjustment_percentage" 
                               value="{{ $moderation->adjustment_percentage ?? 0 }}"
                               step="0.01"
                               min="-100"
                               max="100"
                               required
                               x-show="adjustmentType === 'percentage'"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <span class="text-sm text-gray-600 dark:text-gray-400">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Enter positive value to increase, negative to decrease. Example: +5% or -10%
                    </p>
                </div>

                <!-- Manual Override -->
                <div x-show="adjustmentType === 'manual_override'" class="mb-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Adjusted Lecturer Score (out of 40%)
                        </label>
                        <input type="number" 
                               name="adjusted_lecturer_score" 
                               value="{{ $moderation->adjusted_lecturer_score ?? $lecturerTotal }}"
                               step="0.01"
                               min="0"
                               max="40"
                               x-show="adjustmentType === 'manual_override'"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Adjusted IC Score (out of 60%)
                        </label>
                        <input type="number" 
                               name="adjusted_ic_score" 
                               value="{{ $moderation->adjusted_ic_score ?? $icTotal }}"
                               step="0.01"
                               min="0"
                               max="60"
                               x-show="adjustmentType === 'manual_override'"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Adjusted Final Score (out of 100%)
                        </label>
                        <input type="number" 
                               name="adjusted_final_score" 
                               value="{{ $moderation->adjusted_final_score ?? $finalScore }}"
                               step="0.01"
                               min="0"
                               max="100"
                               x-show="adjustmentType === 'manual_override'"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <!-- Justification -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Justification <span class="text-red-500">*</span>
                    </label>
                    <textarea name="justification" 
                              rows="4"
                              required
                              minlength="10"
                              maxlength="1000"
                              placeholder="Provide a detailed justification for this moderation adjustment..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">{{ $moderation->justification ?? '' }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Minimum 10 characters. This justification will be logged for audit purposes.
                    </p>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Additional Notes (Optional)
                    </label>
                    <textarea name="notes" 
                              rows="3"
                              maxlength="1000"
                              placeholder="Any additional notes or comments..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">{{ $moderation->notes ?? '' }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('academic.ppe.moderation.index') }}" 
                       class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        {{ $moderation ? 'Update Moderation' : 'Apply Moderation' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

