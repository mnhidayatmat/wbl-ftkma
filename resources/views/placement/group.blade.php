@extends('layouts.app')

@section('title', 'Student Placement - ' . $group->name)

@section('content')
<div>
        <!-- Page Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">{{ $group->name }} - Placement Tracking</h1>
                <p class="text-gray-600 dark:text-gray-400">Manage student placement status and documents</p>
            </div>
            <a href="{{ route('placement.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                Back to Groups
            </a>
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-9 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Total</div>
                <div class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5] mt-1">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Resume Recommended</div>
                <div class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['not_applied'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">SAL Released</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['sal_released'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Applied</div>
                <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['applied'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Interviewed</div>
                <div class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['interviewed'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Offer</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['offer_received'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Accepted</div>
                <div class="text-2xl font-bold text-green-600 mt-1">{{ $stats['accepted'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">Confirmed</div>
                <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['confirmed'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4">
                <div class="text-xs font-medium text-gray-600 dark:text-gray-400">SCL Released</div>
                <div class="text-2xl font-bold text-teal-600 mt-1">{{ $stats['scl_released'] }}</div>
            </div>
        </div>

        <!-- Bulk Actions (Admin & Coordinator only) -->
        @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 mb-6">
            <form action="{{ route('placement.bulk.sal.release') }}" method="POST" onsubmit="return confirm('Release SAL for all students in this group?');">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    Bulk Release SAL for All Students
                </button>
            </form>
        </div>
        @endif

        <!-- Students Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C] dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">SAL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Proof</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">SCL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status Update</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Student Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($students as $student)
                            @php
                                $tracking = $student->placementTracking;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $student->matric_no }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($tracking)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $tracking->status_badge_color }}">
                                            {{ $tracking->status_display }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Resume Recommended</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($tracking && $tracking->sal_file_path)
                                        <a href="{{ route('placement.student.sal.download', $student) }}" class="text-blue-600 hover:text-blue-800">Download</a>
                                    @else
                                        @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                                            <form action="{{ route('placement.student.sal.release', $student) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-800">Release</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">Not released</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($tracking && $tracking->confirmation_proof_path)
                                        <span class="text-green-600">Uploaded</span>
                                    @else
                                        @if(auth()->user()->isStudent() && auth()->user()->id === $student->user_id)
                                            <form action="{{ route('placement.student.proof.upload', $student) }}" method="POST" enctype="multipart/form-data" class="inline">
                                                @csrf
                                                <input type="file" name="proof" accept=".pdf,.jpg,.jpeg,.png" required class="text-sm">
                                                <button type="submit" class="ml-2 text-blue-600 hover:text-blue-800">Upload</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">Not uploaded</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($tracking && $tracking->scl_file_path)
                                        <a href="{{ route('placement.student.scl.download', $student) }}" class="text-blue-600 hover:text-blue-800">Download</a>
                                    @else
                                        @if((auth()->user()->isAdmin() || auth()->user()->isCoordinator()) && $tracking && $tracking->status === 'CONFIRMED' && $tracking->confirmation_proof_path)
                                            <form action="{{ route('placement.student.scl.release', $student) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-800">Release</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">Not released</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                                        <select onchange="updateStatus(this, {{ $student->id }})" class="text-sm border rounded px-2 py-1">
                                            <option value="NOT_APPLIED" {{ $tracking && $tracking->status === 'NOT_APPLIED' ? 'selected' : '' }}>Resume Recommended</option>
                                            <option value="SAL_RELEASED" {{ $tracking && $tracking->status === 'SAL_RELEASED' ? 'selected' : '' }}>SAL Released</option>
                                            <option value="APPLIED" {{ $tracking && $tracking->status === 'APPLIED' ? 'selected' : '' }}>Applied</option>
                                            <option value="INTERVIEWED" {{ $tracking && $tracking->status === 'INTERVIEWED' ? 'selected' : '' }}>Interviewed</option>
                                            <option value="OFFER_RECEIVED" {{ $tracking && $tracking->status === 'OFFER_RECEIVED' ? 'selected' : '' }}>Offer Received</option>
                                            <option value="ACCEPTED" {{ $tracking && $tracking->status === 'ACCEPTED' ? 'selected' : '' }}>Accepted</option>
                                            <option value="CONFIRMED" {{ $tracking && $tracking->status === 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="SCL_RELEASED" {{ $tracking && $tracking->status === 'SCL_RELEASED' ? 'selected' : '' }}>SCL Released</option>
                                        </select>
                                    @elseif(auth()->user()->isStudent() && auth()->user()->id === $student->user_id)
                                        <a href="{{ route('student.placement.index') }}" class="text-blue-600 hover:text-blue-800 underline">
                                            Update Status
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($tracking && $tracking->notes)
                                        <div class="max-w-xs">
                                            <p class="text-xs truncate" title="{{ $tracking->notes }}">
                                                {{ Str::limit($tracking->notes, 50) }}
                                            </p>
                                            @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
                                                <button onclick="showNotesModal('{{ addslashes($tracking->notes) }}', '{{ addslashes($student->name) }}')" class="text-xs text-blue-600 hover:text-blue-800 mt-1">
                                                    View Full Notes
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">No notes</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($tracking && $tracking->updated_at)
                                        <div>
                                            <p class="text-xs">{{ $tracking->updated_at->format('d M Y') }}</p>
                                            <p class="text-xs text-gray-400">{{ $tracking->updated_at->format('h:i A') }}</p>
                                        </div>
                                        @if($tracking->updatedByUser)
                                            <p class="text-xs text-gray-400 mt-1">by {{ $tracking->updatedByUser->name }}</p>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
</div>

<!-- Notes Modal -->
<div id="notesModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2" id="notesModalTitle">Student Notes</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap" id="notesModalContent"></p>
            <div class="mt-4 flex justify-end">
                <button onclick="closeNotesModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(select, studentId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/placement/student/${studentId}/status`;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    const status = document.createElement('input');
    status.type = 'hidden';
    status.name = 'status';
    status.value = select.value;
    form.appendChild(status);
    
    document.body.appendChild(form);
    form.submit();
}

function showNotesModal(notes, studentName) {
    document.getElementById('notesModalTitle').textContent = `Notes for ${studentName}`;
    document.getElementById('notesModalContent').textContent = notes;
    document.getElementById('notesModal').classList.remove('hidden');
}

function closeNotesModal() {
    document.getElementById('notesModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('notesModal');
    if (event.target === modal) {
        closeNotesModal();
    }
}
</script>
@endsection

