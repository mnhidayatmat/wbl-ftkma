{{-- Student Submission Settings Partial --}}
{{-- Usage: @include('academic.assessments.partials.submission-settings', ['assessment' => $assessment ?? null]) --}}

@php
    $assessment = $assessment ?? null;
    $presets = \App\Models\Assessment::SUBMISSION_PRESETS;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6"
     x-data="{
         requiresSubmission: {{ old('requires_submission', $assessment?->requires_submission ?? false) ? 'true' : 'false' }},
         allowLateSubmission: {{ old('allow_late_submission', $assessment?->allow_late_submission ?? false) ? 'true' : 'false' }},
         requireDeclaration: {{ old('require_declaration', $assessment?->require_declaration ?? true) ? 'true' : 'false' }},
         selectedFileTypes: {{ json_encode(old('allowed_file_types', $assessment?->allowed_file_types ?? ['pdf', 'docx'])) }},
         presets: {{ json_encode($presets) }},

         toggleFileType(type) {
             const index = this.selectedFileTypes.indexOf(type);
             if (index > -1) {
                 this.selectedFileTypes.splice(index, 1);
             } else {
                 this.selectedFileTypes.push(type);
             }
         },

         applyPreset(assessmentType) {
             const preset = this.presets[assessmentType];
             if (preset) {
                 this.requiresSubmission = preset.requires_submission;
                 this.selectedFileTypes = [...preset.allowed_file_types];
                 if (document.querySelector('[name=max_file_size_mb]')) {
                     document.querySelector('[name=max_file_size_mb]').value = preset.max_file_size_mb;
                 }
             }
         }
     }"
     x-init="
         // Listen for assessment type changes to apply presets
         $watch('$store.assessmentType', (type) => { if (type) applyPreset(type); });
     ">

    {{-- Section Header --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="flex-shrink-0 w-10 h-10 bg-[#0084C5]/10 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Student Submission Settings</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Configure how students submit their work for this assessment</p>
        </div>
    </div>

    {{-- Main Toggle: Require Student Submission --}}
    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-6">
        <div>
            <label for="requires_submission" class="font-medium text-gray-900 dark:text-white">Require Student Submission</label>
            <p class="text-sm text-gray-500 dark:text-gray-400">Students must upload files for this assessment</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="requires_submission" value="0">
            <input type="checkbox"
                   name="requires_submission"
                   id="requires_submission"
                   value="1"
                   x-model="requiresSubmission"
                   class="sr-only peer">
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#0084C5]/20 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-[#0084C5]"></div>
        </label>
    </div>

    {{-- Conditional Settings (shown when toggle is ON) --}}
    <div x-show="requiresSubmission" x-collapse class="space-y-6">

        {{-- Deadline --}}
        <div>
            <label for="submission_deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Submission Deadline <span class="text-red-500">*</span>
            </label>
            <input type="datetime-local"
                   name="submission_deadline"
                   id="submission_deadline"
                   value="{{ old('submission_deadline', $assessment?->submission_deadline?->format('Y-m-d\TH:i')) }}"
                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white @error('submission_deadline') border-red-500 @enderror">
            @error('submission_deadline')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Allowed File Types --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                Allowed File Types
            </label>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                @php
                    $fileTypes = [
                        'pdf' => ['label' => 'PDF', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                        'docx' => ['label' => 'DOCX', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        'pptx' => ['label' => 'PPTX', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'],
                        'xlsx' => ['label' => 'XLSX', 'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                        'zip' => ['label' => 'ZIP', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
                        'png' => ['label' => 'Images', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ];
                @endphp

                @foreach($fileTypes as $type => $config)
                    <label class="relative cursor-pointer group">
                        <input type="checkbox"
                               name="allowed_file_types[]"
                               value="{{ $type }}"
                               x-bind:checked="selectedFileTypes.includes('{{ $type }}')"
                               @change="toggleFileType('{{ $type }}')"
                               class="sr-only peer">
                        <div class="flex flex-col items-center justify-center p-3 border-2 rounded-lg transition-all
                                    border-gray-200 dark:border-gray-600
                                    peer-checked:border-[#0084C5] peer-checked:bg-[#0084C5]/5
                                    hover:border-gray-300 dark:hover:border-gray-500
                                    group-hover:shadow-sm">
                            <svg class="w-6 h-6 mb-1 text-gray-400 peer-checked:text-[#0084C5]"
                                 :class="selectedFileTypes.includes('{{ $type }}') ? 'text-[#0084C5]' : 'text-gray-400'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
                            </svg>
                            <span class="text-xs font-medium"
                                  :class="selectedFileTypes.includes('{{ $type }}') ? 'text-[#0084C5]' : 'text-gray-600 dark:text-gray-400'">
                                {{ $config['label'] }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('allowed_file_types')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Max File Size & Max Attempts --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="max_file_size_mb" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Max File Size
                </label>
                <select name="max_file_size_mb"
                        id="max_file_size_mb"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                    @foreach([5, 10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ old('max_file_size_mb', $assessment?->max_file_size_mb ?? 10) == $size ? 'selected' : '' }}>
                            {{ $size }} MB
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="max_attempts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Max Submission Attempts
                </label>
                <select name="max_attempts"
                        id="max_attempts"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                    @foreach(range(1, 5) as $attempts)
                        <option value="{{ $attempts }}" {{ old('max_attempts', $assessment?->max_attempts ?? 1) == $attempts ? 'selected' : '' }}>
                            {{ $attempts }} {{ $attempts === 1 ? 'attempt' : 'attempts' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Late Submission Section --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-4">
                <div>
                    <label for="allow_late_submission" class="font-medium text-gray-900 dark:text-white">Allow Late Submission</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Accept submissions after the deadline with penalty</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="allow_late_submission" value="0">
                    <input type="checkbox"
                           name="allow_late_submission"
                           id="allow_late_submission"
                           value="1"
                           x-model="allowLateSubmission"
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#0084C5]/20 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-[#0084C5]"></div>
                </label>
            </div>

            {{-- Late Submission Settings --}}
            <div x-show="allowLateSubmission" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="late_penalty_per_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Penalty per Day (%)
                    </label>
                    <div class="relative">
                        <input type="number"
                               name="late_penalty_per_day"
                               id="late_penalty_per_day"
                               min="0"
                               max="100"
                               step="0.5"
                               value="{{ old('late_penalty_per_day', $assessment?->late_penalty_per_day ?? 5) }}"
                               class="w-full px-4 py-2.5 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deducted from final score per day late</p>
                </div>
                <div>
                    <label for="max_late_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Max Late Days Allowed
                    </label>
                    <select name="max_late_days"
                            id="max_late_days"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                        @foreach([1, 2, 3, 5, 7, 14, 30] as $days)
                            <option value="{{ $days }}" {{ old('max_late_days', $assessment?->max_late_days ?? 7) == $days ? 'selected' : '' }}>
                                {{ $days }} {{ $days === 1 ? 'day' : 'days' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Submissions rejected after this period</p>
                </div>
            </div>
        </div>

        {{-- Declaration Toggle --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div>
                    <label for="require_declaration" class="font-medium text-gray-900 dark:text-white">Require Academic Integrity Declaration</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Students must confirm this is their own original work</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="require_declaration" value="0">
                    <input type="checkbox"
                           name="require_declaration"
                           id="require_declaration"
                           value="1"
                           x-model="requireDeclaration"
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#0084C5]/20 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-[#0084C5]"></div>
                </label>
            </div>
        </div>

        {{-- Submission Instructions --}}
        <div>
            <label for="submission_instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Submission Instructions (Optional)
            </label>
            <textarea name="submission_instructions"
                      id="submission_instructions"
                      rows="3"
                      placeholder="Enter any special instructions for students regarding their submission..."
                      class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 resize-none">{{ old('submission_instructions', $assessment?->submission_instructions) }}</textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">These instructions will be displayed to students on the submission page</p>
        </div>
    </div>

    {{-- Info message when submission is disabled --}}
    <div x-show="!requiresSubmission" class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">Evaluation-Only Assessment</p>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">This assessment does not require students to submit any files. Evaluators will assess students directly using the rubric or scoring criteria.</p>
            </div>
        </div>
    </div>
</div>
