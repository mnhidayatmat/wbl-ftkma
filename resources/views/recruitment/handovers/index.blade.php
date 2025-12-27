@extends('layouts.app')

@section('title', 'Recruitment Handover History')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold heading-umpsa">Recruitment Handover History</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Track all recruitment packages sent to companies</p>
        </div>
        <div>
            <a href="{{ route('recruitment.pool.index') }}" class="px-4 py-2 bg-umpsa-primary hover:bg-umpsa-secondary text-white font-semibold rounded-lg transition-colors inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Recruitment Pool
            </a>
        </div>
    </div>

    <!-- Handovers Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Company</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Recruiter Emails</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Students</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sent By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Filters Applied</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($handovers as $handover)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $handover->created_at->format('M d, Y') }}
                                <div class="text-xs text-gray-500">{{ $handover->created_at->format('H:i A') }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 font-medium">
                                {{ $handover->company->company_name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                <div class="max-w-xs truncate" title="{{ $handover->recruiter_emails_string }}">
                                    {{ $handover->recruiter_emails_string }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full font-semibold">
                                    {{ $handover->student_count }} students
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $handover->handedOverBy->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                @if($handover->filters_applied && count($handover->filters_applied) > 0)
                                    <button onclick="showFilters({{ $handover->id }})" class="text-umpsa-primary hover:text-umpsa-secondary">
                                        View Filters
                                    </button>
                                    <div id="filters-{{ $handover->id }}" class="hidden mt-2 p-2 bg-gray-50 dark:bg-gray-700 rounded text-xs space-y-1">
                                        @foreach($handover->filters_applied as $key => $value)
                                            <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">No filters</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="showStudents({{ $handover->id }})" class="text-umpsa-primary hover:text-umpsa-secondary font-medium">
                                    View Students
                                </button>
                            </td>
                        </tr>

                        <!-- Student Details Row (Hidden by default) -->
                        <tr id="students-{{ $handover->id }}" class="hidden bg-gray-50 dark:bg-gray-900">
                            <td colspan="7" class="px-4 py-4">
                                <div class="max-w-4xl">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Students in this handover:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($handover->students() as $student)
                                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded p-3">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $student->name }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $student->matric_no }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-500">{{ $student->programme }}</div>
                                                @if($student->cgpa)
                                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">CGPA: {{ number_format($student->cgpa, 2) }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($handover->message)
                                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded">
                                            <h5 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">Message to Recruiter:</h5>
                                            <p class="text-sm text-blue-800 dark:text-blue-300">{{ $handover->message }}</p>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No recruitment handovers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($handovers->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $handovers->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function showStudents(handoverId) {
    const row = document.getElementById('students-' + handoverId);
    row.classList.toggle('hidden');
}

function showFilters(handoverId) {
    const div = document.getElementById('filters-' + handoverId);
    div.classList.toggle('hidden');
}
</script>
@endpush
