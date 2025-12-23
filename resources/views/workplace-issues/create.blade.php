@extends('layouts.app')

@section('title', 'Report Workplace Issue')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('workplace-issues.index') }}" class="text-umpsa-primary hover:text-umpsa-secondary flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Reports
        </a>
        <h1 class="text-2xl font-bold heading-umpsa">Report Workplace Issue</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Your safety and wellbeing are our priority. Please provide details about your concern.</p>
    </div>

    <!-- Information Alert -->
    <div class="card-umpsa p-4 mb-6 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500">
        <div class="flex gap-3">
            <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-gray-700 dark:text-gray-300">
                <p class="font-semibold mb-1">Confidential Reporting</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Your report will be reviewed by the WBL coordinator</li>
                    <li>All information is treated confidentially</li>
                    <li>You will be notified of updates and actions taken</li>
                    <li>For emergency situations, please contact campus security immediately</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Report Form -->
    <form action="{{ route('workplace-issues.store') }}" method="POST" enctype="multipart/form-data" class="card-umpsa p-6">
        @csrf

        <div class="space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Issue Title <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    maxlength="255"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('title') border-red-500 @enderror"
                    placeholder="Brief summary of the issue"
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Category <span class="text-red-500">*</span>
                </label>
                <select
                    id="category"
                    name="category"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('category') border-red-500 @enderror"
                    onchange="toggleCustomCategory(this.value)"
                >
                    <option value="">Select a category</option>
                    <option value="safety_health" {{ old('category') == 'safety_health' ? 'selected' : '' }}>Safety & Health Hazards</option>
                    <option value="harassment_discrimination" {{ old('category') == 'harassment_discrimination' ? 'selected' : '' }}>Harassment & Discrimination</option>
                    <option value="work_environment" {{ old('category') == 'work_environment' ? 'selected' : '' }}>Work Environment Issues</option>
                    <option value="supervision_guidance" {{ old('category') == 'supervision_guidance' ? 'selected' : '' }}>Supervision & Guidance Problems</option>
                    <option value="custom" {{ old('category') == 'custom' ? 'selected' : '' }}>Other (Please Specify)</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Custom Category -->
            <div id="custom-category-field" class="hidden">
                <label for="custom_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Specify Category <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="custom_category"
                    name="custom_category"
                    value="{{ old('custom_category') }}"
                    maxlength="255"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('custom_category') border-red-500 @enderror"
                    placeholder="Please describe the category"
                >
                @error('custom_category')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Severity -->
            <div>
                <label for="severity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Severity <span class="text-red-500">*</span>
                </label>
                <select
                    id="severity"
                    name="severity"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('severity') border-red-500 @enderror"
                >
                    <option value="">Select severity level</option>
                    <option value="low" {{ old('severity') == 'low' ? 'selected' : '' }}>Low - Minor concern, no immediate risk</option>
                    <option value="medium" {{ old('severity') == 'medium' ? 'selected' : '' }}>Medium - Moderate concern, needs attention</option>
                    <option value="high" {{ old('severity') == 'high' ? 'selected' : '' }}>High - Serious issue, requires prompt action</option>
                    <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>Critical - Urgent situation, immediate action required</option>
                </select>
                @error('severity')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    required
                    rows="6"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('description') border-red-500 @enderror"
                    placeholder="Please provide detailed information about the issue. Include what happened, when, where, and who was involved."
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Location (Optional)
                </label>
                <input
                    type="text"
                    id="location"
                    name="location"
                    value="{{ old('location') }}"
                    maxlength="255"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('location') border-red-500 @enderror"
                    placeholder="Where did this occur? (e.g., Office floor 3, Factory assembly line)"
                >
                @error('location')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Incident Date & Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="incident_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Incident Date (Optional)
                    </label>
                    <input
                        type="date"
                        id="incident_date"
                        name="incident_date"
                        value="{{ old('incident_date') }}"
                        max="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('incident_date') border-red-500 @enderror"
                    >
                    @error('incident_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="incident_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Incident Time (Optional)
                    </label>
                    <input
                        type="time"
                        id="incident_time"
                        name="incident_time"
                        value="{{ old('incident_time') }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('incident_time') border-red-500 @enderror"
                    >
                    @error('incident_time')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Attachments -->
            <div>
                <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Attachments (Optional)
                </label>
                <input
                    type="file"
                    id="attachments"
                    name="attachments[]"
                    multiple
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary @error('attachments.*') border-red-500 @enderror"
                >
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Accepted formats: JPG, PNG, PDF, DOC, DOCX. Maximum 5MB per file.
                </p>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-umpsa-primary">
                    Submit Report
                </button>
                <a href="{{ route('workplace-issues.index') }}" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors font-semibold">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleCustomCategory(category) {
    const customField = document.getElementById('custom-category-field');
    const customInput = document.getElementById('custom_category');

    if (category === 'custom') {
        customField.classList.remove('hidden');
        customInput.required = true;
    } else {
        customField.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const category = document.getElementById('category').value;
    toggleCustomCategory(category);
});
</script>
@endpush
@endsection
