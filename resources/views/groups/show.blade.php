@extends('layouts.app')

@section('title', 'Group Details')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-[#003A6C] dark:text-[#0084C5]">Group Details</h1>
        @if($group->isCompleted())
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">This group has been completed and archived.</p>
        @endif
    </div>
    <div>
        @if(auth()->user()->isAdmin())
            @if($group->isActive())
                <form action="{{ route('admin.groups.mark-completed', $group) }}" method="POST" class="inline mr-2" onsubmit="return confirm('Mark this group as completed?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-lg transition-colors">
                        Close Group
                    </button>
                </form>
            @else
                <form action="{{ route('admin.groups.reopen', $group) }}" method="POST" class="inline mr-2" onsubmit="return confirm('Reopen this group?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                        Reopen Group
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.groups.edit', $group) }}" class="btn-umpsa-primary mr-2">Edit</a>
        @endif
        <a href="{{ route('admin.groups.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded transition-colors">Back</a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Group Name</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $group->name }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $group->status_badge_color }}">
                {{ $group->status_display }}
            </span>
            @if($group->completed_at)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Completed: {{ $group->completed_at->format('d M Y') }}</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $group->start_date->format('d M Y') }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">End Date</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $group->end_date->format('d M Y') }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Total Students</label>
            <p class="text-lg text-gray-900 dark:text-white">{{ $group->students->count() }}</p>
        </div>
    </div>
</div>

@if($group->students->count() > 0)
    <div class="card-umpsa overflow-hidden">
        <div class="card-umpsa-header">
            <h2 class="text-xl font-semibold">Students in this Group</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-umpsa-deep-blue uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-umpsa-deep-blue uppercase tracking-wider">Matric No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-umpsa-deep-blue uppercase tracking-wider">Programme</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-umpsa-deep-blue uppercase tracking-wider">Company</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($group->students as $student)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-umpsa-deep-blue">{{ $student->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->matric_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->programme }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->company->company_name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
