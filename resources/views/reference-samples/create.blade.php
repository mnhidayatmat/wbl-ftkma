@extends('layouts.app')

@section('title', 'Upload Reference Sample')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
        <a href="{{ route('reference-samples.index') }}" class="hover:text-[#0084C5]">Reference Samples</a>
        <span>›</span>
        <span>Upload New</span>
    </div>
    <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Upload Reference Sample</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Upload a sample file for students to reference</p>
</div>

<div class="max-w-3xl">
    <form action="{{ route('reference-samples.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Title -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sample Information</h3>

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-[#0084C5] focus:ring focus:ring-[#0084C5] focus:ring-opacity-50"
                        placeholder="e.g., Resume Structure, Poster Layout">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-[#0084C5] focus:ring focus:ring-[#0084C5] focus:ring-opacity-50">
                        <option value="">Select Category</option>
                        <option value="resume" {{ old('category') === 'resume' ? 'selected' : '' }}>📄 Resume</option>
                        <option value="poster" {{ old('category') === 'poster' ? 'selected' : '' }}>🖼️ Poster</option>
                        <option value="achievement" {{ old('category') === 'achievement' ? 'selected' : '' }}>🏆 Achievement</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>📁 Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description (Optional)
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-[#0084C5] focus:ring focus:ring-[#0084C5] focus:ring-opacity-50"
                        placeholder="Brief description of this sample...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum 1000 characters</p>
                </div>
            </div>
        </div>

        <!-- File Upload -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">File Upload</h3>

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Sample File <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-[#0084C5] transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="file" class="relative cursor-pointer rounded-md font-medium text-[#0084C5] hover:text-[#003A6C] focus-within:outline-none">
                                <span>Upload a file</span>
                                <input id="file" name="file" type="file" class="sr-only" required accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            PDF, DOC, DOCX, PPT, PPTX, JPG, PNG up to 10MB
                        </p>
                    </div>
                </div>
                @error('file')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Settings</h3>

            <div class="space-y-4">
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Display Order
                    </label>
                    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', 0) }}" min="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-[#0084C5] focus:ring focus:ring-[#0084C5] focus:ring-opacity-50"
                        placeholder="0">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first (0 = default)</p>
                    @error('display_order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="h-4 w-4 text-[#0084C5] focus:ring-[#0084C5] border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Make this sample active immediately (students can download it)
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('reference-samples.index') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit" id="submitBtn" class="px-6 py-2 bg-[#003A6C] hover:bg-[#0084C5] text-white font-semibold rounded-lg transition-colors">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Upload Sample
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const fileInput = document.getElementById('file');
    const dropZone = fileInput.closest('.border-dashed');
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        handleFileSelect(e.target.files);
    });

    // Drag and drop handlers
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-[#0084C5]', 'bg-blue-50', 'dark:bg-blue-900/10');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-[#0084C5]', 'bg-blue-50', 'dark:bg-blue-900/10');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-[#0084C5]', 'bg-blue-50', 'dark:bg-blue-900/10');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files);
        }
    });

    // Handle file selection
    function handleFileSelect(files) {
        if (files && files.length > 0) {
            const fileName = files[0].name;
            const fileSize = (files[0].size / 1024 / 1024).toFixed(2);
            console.log(`Selected: ${fileName} (${fileSize} MB)`);

            // Optional: Show selected file name in the UI
            const fileNameDisplay = dropZone.querySelector('p.pl-1');
            if (fileNameDisplay) {
                fileNameDisplay.textContent = `Selected: ${fileName} (${fileSize} MB)`;
                fileNameDisplay.classList.add('text-[#0084C5]', 'font-medium');
            }
        }
    }

    // Form submission handler with debugging
    form.addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        console.log('File input files:', fileInput.files);
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);

        // Validate file is selected
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert('Please select a file to upload');
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Uploading...';

        console.log('Form is being submitted...');
    });

    console.log('Upload form initialized');
</script>
@endpush
@endsection
