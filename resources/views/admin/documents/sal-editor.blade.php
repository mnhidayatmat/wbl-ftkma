@extends('layouts.app')

@section('title', 'Edit SAL Template')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">SAL Template Editor</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Visually customize the Student Application Letter template</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.documents.sal') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                Cancel
            </a>
            <a href="{{ route('admin.documents.sal.preview') }}" target="_blank"
               class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Preview PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.documents.sal.update') }}" method="POST" id="templateForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel: Editor -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                        Letter Header
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Letter Title</label>
                            <input type="text" name="title" value="{{ old('title', $template->title) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="STUDENT APPLICATION LETTER (SAL)">
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtitle (Optional)</label>
                            <input type="text" name="subtitle" value="{{ old('subtitle', $template->subtitle) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Work-Based Learning Program">
                        </div>
                    </div>
                </div>

                <!-- Salutation -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                        Salutation
                    </h3>
                    <input type="text" name="salutation" value="{{ old('salutation', $template->salutation) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                           placeholder="To Whom It May Concern,">
                </div>

                <!-- Body Content -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Letter Body
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Use variables like <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded text-blue-600">@{{student_name}}</code> to insert dynamic data. Use <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">&lt;strong&gt;</code> tags for bold text.
                    </p>
                    <textarea name="body_content" rows="12"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white font-mono text-sm"
                              placeholder="Enter the letter body content...">{{ old('body_content', $template->body_content) }}</textarea>
                    @error('body_content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Closing & Signature -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Closing & Signature
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Closing Text</label>
                            <input type="text" name="closing_text" value="{{ old('closing_text', $template->closing_text) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Yours sincerely,">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Signatory Name</label>
                                <input type="text" name="signatory_name" value="{{ old('signatory_name', $template->signatory_name) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="Dr. Ahmad">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title/Position</label>
                                <input type="text" name="signatory_title" value="{{ old('signatory_title', $template->signatory_title) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="WBL Coordinator">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department/Organization</label>
                                <input type="text" name="signatory_department" value="{{ old('signatory_department', $template->signatory_department) }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                       placeholder="UMPSA">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Page Settings
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Font Size (pt)</label>
                            <select name="settings[font_size]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @foreach(['10', '11', '12', '13', '14'] as $size)
                                    <option value="{{ $size }}" {{ ($template->settings['font_size'] ?? '12') == $size ? 'selected' : '' }}>{{ $size }}pt</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Line Height</label>
                            <select name="settings[line_height]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @foreach(['1.4', '1.5', '1.6', '1.8', '2.0'] as $height)
                                    <option value="{{ $height }}" {{ ($template->settings['line_height'] ?? '1.6') == $height ? 'selected' : '' }}>{{ $height }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Margin (mm)</label>
                            <select name="settings[margin_top]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @foreach(['15', '20', '25', '30'] as $margin)
                                    <option value="{{ $margin }}" {{ ($template->settings['margin_top'] ?? '25') == $margin ? 'selected' : '' }}>{{ $margin }}mm</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Format</label>
                            <select name="settings[date_format]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="d F Y" {{ ($template->settings['date_format'] ?? 'd F Y') == 'd F Y' ? 'selected' : '' }}>{{ now()->format('d F Y') }}</option>
                                <option value="d/m/Y" {{ ($template->settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>{{ now()->format('d/m/Y') }}</option>
                                <option value="d-m-Y" {{ ($template->settings['date_format'] ?? '') == 'd-m-Y' ? 'selected' : '' }}>{{ now()->format('d-m-Y') }}</option>
                                <option value="F d, Y" {{ ($template->settings['date_format'] ?? '') == 'F d, Y' ? 'selected' : '' }}>{{ now()->format('F d, Y') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 mt-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="settings[show_logo]" value="1"
                                   {{ ($template->settings['show_logo'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Show Logo</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="settings[show_date]" value="1"
                                   {{ ($template->settings['show_date'] ?? true) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Show Date</span>
                        </label>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex items-center justify-between">
                    <form action="{{ route('admin.documents.sal.reset') }}" method="POST" class="inline"
                          onsubmit="return confirm('Are you sure you want to reset the template to defaults? This cannot be undone.')">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                            Reset to Defaults
                        </button>
                    </form>
                    <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Save Template
                    </button>
                </div>
            </div>

            <!-- Right Panel: Variables & Preview -->
            <div class="space-y-6">
                <!-- Available Variables -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Available Variables
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">Click to copy variable to clipboard</p>
                    <div class="space-y-2">
                        @foreach($variables as $var => $description)
                            <button type="button" onclick="copyVariable('{{ $var }}')"
                                    class="w-full text-left px-3 py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors group">
                                <code class="text-blue-600 dark:text-blue-400 text-sm group-hover:text-blue-700">{{ $var }}</code>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $description }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Live Preview Info -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800">
                    <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Preview Tips
                    </h3>
                    <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                        <li>Click "Preview PDF" to see the final output</li>
                        <li>Variables will be replaced with sample data</li>
                        <li>Use <code class="bg-blue-100 dark:bg-blue-800/50 px-1 rounded">&lt;strong&gt;</code> for bold text</li>
                        <li>Use <code class="bg-blue-100 dark:bg-blue-800/50 px-1 rounded">&lt;em&gt;</code> for italic text</li>
                        <li>Press Enter twice for paragraph breaks</li>
                    </ul>
                </div>

                @if($template->updated_at && $template->updatedByUser)
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Last updated {{ $template->updated_at->diffForHumans() }}
                            by {{ $template->updatedByUser->name }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
function copyVariable(variable) {
    navigator.clipboard.writeText(variable).then(() => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.textContent = 'Copied: ' + variable;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}
</script>
@endsection
