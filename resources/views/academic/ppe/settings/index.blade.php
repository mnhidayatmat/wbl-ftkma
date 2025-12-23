@extends('layouts.app')

@section('title', 'PPE Assessment Control & Monitoring Panel')

@section('content')
<div class="py-6 overflow-x-hidden">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">PPE Assessment Control & Monitoring Panel</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage assessment windows, monitor progress, and send reminders</p>
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

        <!-- Section A: Assessment Window Control -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Assessment Window Control</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Lecturer Evaluation Window -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lecturer Evaluation (40%)</h3>
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

                    <form method="POST" action="{{ route('academic.ppe.settings.update-window') }}" class="space-y-4">
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
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Industry Coach Evaluation (60%)</h3>
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

                    <form method="POST" action="{{ route('academic.ppe.settings.update-window') }}" class="space-y-4">
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
                                class="w-full px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                            Save IC Window
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Section B: Evaluation Progress Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Evaluation Progress Overview</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Students -->
                <div class="bg-gradient-to-br from-[#003A6C] to-[#0084C5] rounded-lg p-5 text-white">
                    <div class="text-sm font-medium opacity-90 mb-1">Total Students</div>
                    <div class="text-3xl font-bold">{{ $totalStudents }}</div>
                </div>

                <!-- Lecturer Completed -->
                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Lecturer Completed</div>
                        <div class="text-lg font-bold text-[#0084C5]">{{ $lecturerCompleted }}</div>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                        <div class="bg-[#0084C5] h-3 rounded-full transition-all" style="width: {{ min($lecturerProgress, 100) }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($lecturerProgress, 1) }}%</div>
                </div>

                <!-- IC Completed -->
                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-600 dark:text-gray-400">IC Completed</div>
                        <div class="text-lg font-bold text-[#00AEEF]">{{ $icCompleted }}</div>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                        <div class="bg-[#00AEEF] h-3 rounded-full transition-all" style="width: {{ min($icProgress, 100) }}%"></div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($icProgress, 1) }}%</div>
                </div>

                <!-- Pending Evaluations -->
                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-5">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Pending Evaluations</div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Lecturer:</span>
                            <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ $pendingLecturer }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">IC:</span>
                            <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ $pendingIc }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section C: Reminder & Notification Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Reminder & Notification Settings</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Lecturer Reminders -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lecturer Reminders</h3>
                    
                    <form method="POST" action="{{ route('academic.ppe.settings.send-reminder') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="evaluator_role" value="lecturer">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reminder Frequency</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                <option>Manual</option>
                                <option>Every 3 days</option>
                                <option>Weekly</option>
                                <option>48 hours before deadline</option>
                            </select>
                        </div>

                        <button type="submit" 
                                class="w-full px-4 py-2 bg-[#0084C5] hover:bg-[#003A6C] text-white font-semibold rounded-lg transition-colors">
                            Send Reminder Now (Lecturer)
                        </button>
                    </form>
                </div>

                <!-- IC Reminders -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Industry Coach Reminders</h3>
                    
                    <form method="POST" action="{{ route('academic.ppe.settings.send-reminder') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="evaluator_role" value="ic">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reminder Frequency</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0084C5] focus:border-[#0084C5] dark:bg-gray-700 dark:text-white">
                                <option>Manual</option>
                                <option>Every 3 days</option>
                                <option>Weekly</option>
                                <option>48 hours before deadline</option>
                            </select>
                        </div>

                        <button type="submit" 
                                class="w-full px-4 py-2 bg-[#00AEEF] hover:bg-[#0084C5] text-white font-semibold rounded-lg transition-colors">
                            Send Reminder Now (IC)
    </button>
                    </form>
                </div>
            </div>
</div>

        <!-- Section D: Activity Log -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-[#003A6C] dark:text-[#0084C5] mb-4">Activity Log (Audit Trail)</h2>
            
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-[#003A6C]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date & Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Admin User</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Description</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($activityLogs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $log->action_label }}
                        </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                @if($log->evaluator_role)
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                        {{ ucfirst($log->evaluator_role) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                        </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $log->adminUser->name }}
                        </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $log->description }}
                        </td>
                    </tr>
                @empty
                    <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No activity logs found.
                            </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
    </div>
</div>
@endsection
