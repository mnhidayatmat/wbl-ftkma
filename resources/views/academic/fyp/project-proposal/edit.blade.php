@extends('layouts.app')

@section('title', 'FYP Project Proposal')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">FYP Project Proposal</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Complete your Final Year Project proposal details below</p>
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

        <!-- Status Banner -->
        @if($proposal->status !== 'draft')
        <div class="mb-6 p-4 rounded-lg border-l-4
            @if($proposal->status === 'submitted') bg-yellow-50 border-yellow-400 text-yellow-800
            @elseif($proposal->status === 'approved') bg-green-50 border-green-400 text-green-800
            @elseif($proposal->status === 'rejected') bg-red-50 border-red-400 text-red-800
            @endif">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @if($proposal->status === 'submitted')
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    @elseif($proposal->status === 'approved')
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    @elseif($proposal->status === 'rejected')
                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    @endif
                </div>
                <div class="ml-3">
                    <p class="font-semibold">{{ $proposal->status_label }}</p>
                    @if($proposal->remarks)
                    <p class="text-sm mt-1">{{ $proposal->remarks }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Proposal Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <!-- Header Section with Student Info -->
            <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <!-- Student Photo -->
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                            @if($student->image_path)
                                <img src="{{ asset('storage/' . $student->image_path) }}"
                                     alt="{{ $student->name }}"
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
                            <p class="text-white font-semibold text-sm">{{ $student->name }}</p>
                            <p class="text-blue-200 text-xs">{{ $student->matric_no }}</p>
                        </div>
                    </div>

                    <!-- Student Details -->
                    <div class="flex-grow text-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Industry Coach</p>
                                <p class="font-semibold">
                                    {{ $student->industryCoach->name ?? 'Not Assigned' }}
                                    @if($student->industryCoach && $student->industryCoach->position)
                                        <span class="text-blue-200 text-sm">({{ $student->industryCoach->position }})</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Academic Tutor</p>
                                <p class="font-semibold">{{ $student->academicTutor->name ?? 'Not Assigned' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-blue-200 text-xs uppercase tracking-wider mb-1">Company</p>
                                <p class="font-semibold">{{ $student->company->company_name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <form action="{{ route('academic.fyp.project-proposal.update') }}" method="POST" id="proposalForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    <!-- Project Title -->
                    <div>
                        <label class="block text-sm font-bold text-[#003A6C] dark:text-[#0084C5] uppercase tracking-wider mb-2">
                            Project Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="project_title"
                               value="{{ old('project_title', $proposal->project_title) }}"
                               required
                               placeholder="Enter your project title..."
                               class="w-full px-4 py-3 text-lg font-semibold border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white transition-all"
                               {{ !$proposal->isEditable() ? 'disabled' : '' }}>
                    </div>

                    <!-- Proposal Items Table -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-sm font-bold text-[#003A6C] dark:text-[#0084C5] uppercase tracking-wider">
                                Problem Statements, Objectives & Methodology <span class="text-red-500">*</span>
                            </label>
                            @if($proposal->isEditable())
                            <button type="button"
                                    onclick="addProposalItem()"
                                    class="px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Row
                            </button>
                            @endif
                        </div>

                        <div class="overflow-x-auto border-2 border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-[#003A6C]">
                                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider w-[5%]">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider w-[30%]">Problem Statement</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider w-[30%]">Objective</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider w-[30%]">Methodology</th>
                                        @if($proposal->isEditable())
                                        <th class="px-4 py-3 text-center text-xs font-bold text-white uppercase tracking-wider w-[5%]">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="proposalItemsContainer" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $items = old('proposal_items', $proposal->proposal_items ?? []);
                                        if (empty($items)) {
                                            $items = [['problem_statement' => '', 'objective' => '', 'methodology' => '']];
                                        }
                                    @endphp
                                    @foreach($items as $index => $item)
                                    <tr class="proposal-item bg-[#FFFDE7] dark:bg-gray-700/50 hover:bg-[#FFF9C4] dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300 item-number">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <textarea name="proposal_items[{{ $index }}][problem_statement]"
                                                      rows="4"
                                                      required
                                                      placeholder="Describe the problem..."
                                                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white resize-none"
                                                      {{ !$proposal->isEditable() ? 'disabled' : '' }}>{{ $item['problem_statement'] ?? '' }}</textarea>
                                        </td>
                                        <td class="px-4 py-3">
                                            <textarea name="proposal_items[{{ $index }}][objective]"
                                                      rows="4"
                                                      required
                                                      placeholder="State the objective..."
                                                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white resize-none"
                                                      {{ !$proposal->isEditable() ? 'disabled' : '' }}>{{ $item['objective'] ?? '' }}</textarea>
                                        </td>
                                        <td class="px-4 py-3">
                                            <textarea name="proposal_items[{{ $index }}][methodology]"
                                                      rows="4"
                                                      required
                                                      placeholder="Describe the methodology..."
                                                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white resize-none"
                                                      {{ !$proposal->isEditable() ? 'disabled' : '' }}>{{ $item['methodology'] ?? '' }}</textarea>
                                        </td>
                                        @if($proposal->isEditable())
                                        <td class="px-4 py-3 text-center">
                                            <button type="button"
                                                    onclick="removeProposalItem(this)"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($proposal->isEditable())
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-semibold">Note:</span> Save your proposal as draft before submitting for review.
                        </p>
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                    name="action"
                                    value="save"
                                    class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Save Draft
                            </button>
                            <button type="button"
                                    onclick="confirmSubmit()"
                                    class="px-6 py-3 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Submit for Review
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('academic.fyp.proposals.pdf', $proposal) }}"
                           target="_blank"
                           class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Download PDF
                        </a>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Submit Proposal?</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Once submitted, you won't be able to edit your proposal until it's reviewed. Are you sure you want to proceed?
            </p>
            <div class="flex items-center justify-center gap-3">
                <button type="button"
                        onclick="closeModal()"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    Cancel
                </button>
                <form action="{{ route('academic.fyp.project-proposal.submit') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                        Yes, Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let itemIndex = {{ count($items) }};

function addProposalItem() {
    const container = document.getElementById('proposalItemsContainer');
    const newRow = document.createElement('tr');
    newRow.className = 'proposal-item bg-[#FFFDE7] dark:bg-gray-700/50 hover:bg-[#FFF9C4] dark:hover:bg-gray-700 transition-colors';

    newRow.innerHTML = `
        <td class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300 item-number">${itemIndex + 1}</td>
        <td class="px-4 py-3">
            <textarea name="proposal_items[${itemIndex}][problem_statement]"
                      rows="4"
                      required
                      placeholder="Describe the problem..."
                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white resize-none"></textarea>
        </td>
        <td class="px-4 py-3">
            <textarea name="proposal_items[${itemIndex}][objective]"
                      rows="4"
                      required
                      placeholder="State the objective..."
                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white resize-none"></textarea>
        </td>
        <td class="px-4 py-3">
            <textarea name="proposal_items[${itemIndex}][methodology]"
                      rows="4"
                      required
                      placeholder="Describe the methodology..."
                      class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-800 dark:text-white resize-none"></textarea>
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button"
                    onclick="removeProposalItem(this)"
                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </td>
    `;

    container.appendChild(newRow);
    itemIndex++;
    updateItemNumbers();
}

function removeProposalItem(button) {
    const rows = document.querySelectorAll('.proposal-item');
    if (rows.length <= 1) {
        alert('You must have at least one item in your proposal.');
        return;
    }

    if (confirm('Are you sure you want to remove this item?')) {
        button.closest('tr').remove();
        updateItemNumbers();
    }
}

function updateItemNumbers() {
    const rows = document.querySelectorAll('.proposal-item');
    rows.forEach((row, index) => {
        const numberCell = row.querySelector('.item-number');
        if (numberCell) {
            numberCell.textContent = index + 1;
        }

        // Update input names
        const textareas = row.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            const name = textarea.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                textarea.setAttribute('name', newName);
            }
        });
    });
}

function confirmSubmit() {
    // Validate form first
    const form = document.getElementById('proposalForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Save draft first, then show modal
    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(response => {
        document.getElementById('submitModal').classList.remove('hidden');
        document.getElementById('submitModal').classList.add('flex');
    }).catch(error => {
        console.error('Error:', error);
    });
}

function closeModal() {
    document.getElementById('submitModal').classList.add('hidden');
    document.getElementById('submitModal').classList.remove('flex');
}

// Close modal on outside click
document.getElementById('submitModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
