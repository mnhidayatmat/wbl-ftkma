<div class="space-y-6">
    @if(auth()->user()->isAdmin())
    <!-- Upload Document Form -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6" x-data="{ showForm: false }">
        <button @click="showForm = !showForm" class="w-full flex items-center justify-between px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
            <span>Upload Document</span>
            <svg class="w-5 h-5" :class="{ 'rotate-180': showForm }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <form x-show="showForm" x-transition method="POST" action="{{ route('companies.documents.store', $company) }}" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Document Type *</label>
                    <select name="document_type" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
                        <option value="">Select Type</option>
                        <option value="MoU">MoU</option>
                        <option value="MoA">MoA</option>
                        <option value="NDA">NDA</option>
                        <option value="Letter">Letter</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">File * (PDF, DOC, DOCX - Max 10MB)</label>
                <input type="file" name="file" accept=".pdf,.doc,.docx" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] dark:bg-gray-800 dark:text-white"></textarea>
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                Upload Document
            </button>
        </form>
    </div>
                @endif

    <!-- Documents List -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-[#003A6C]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">File Size</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Uploaded By</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($company->documents as $document)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                        {{ $document->title }}
                        @if($document->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($document->description, 50) }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $document->document_type }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                        {{ $document->formatted_file_size }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                        {{ $document->uploader->name ?? 'Unknown' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                        {{ $document->created_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        <a href="{{ route('companies.documents.download', [$company, $document]) }}" class="text-[#0084C5] hover:text-[#003A6C] dark:text-[#00AEEF] dark:hover:text-[#0084C5] mr-3">Download</a>
                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('companies.documents.destroy', [$company, $document]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No documents found. @if(auth()->user()->isAdmin())Upload a document to get started.@endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

