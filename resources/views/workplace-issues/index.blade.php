@extends('layouts.app')

@section('title', 'Workplace Issue Reports')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold heading-umpsa">Workplace Issue Reports</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Report and track workplace safety and wellbeing concerns</p>
        </div>
        @if(Auth::user()->isStudent())
            <a href="{{ route('workplace-issues.create') }}" class="btn-umpsa-primary whitespace-nowrap">
                Report New Issue
            </a>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="card-umpsa p-4">
            <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">Total</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statistics['total'] }}</div>
        </div>
        <div class="card-umpsa p-4">
            <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">New</div>
            <div class="text-2xl font-bold text-purple-600">{{ $statistics['new'] }}</div>
        </div>
        <div class="card-umpsa p-4">
            <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">Under Review</div>
            <div class="text-2xl font-bold text-blue-600">{{ $statistics['under_review'] }}</div>
        </div>
        <div class="card-umpsa p-4">
            <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">In Progress</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $statistics['in_progress'] }}</div>
        </div>
        <div class="card-umpsa p-4">
            <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">Resolved</div>
            <div class="text-2xl font-bold text-green-600">{{ $statistics['resolved'] }}</div>
        </div>
        <div class="card-umpsa p-4">
            <div class="text-gray-500 dark:text-gray-400 text-sm mb-1">Closed</div>
            <div class="text-2xl font-bold text-gray-600">{{ $statistics['closed'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-umpsa p-4 mb-6">
        <form method="GET" action="{{ route('workplace-issues.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Title, description, student..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary"
                >
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <!-- Severity Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Severity</label>
                <select name="severity" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary">
                    <option value="">All Severities</option>
                    <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-umpsa-primary focus:border-umpsa-primary">
                    <option value="">All Categories</option>
                    <option value="safety_health" {{ request('category') == 'safety_health' ? 'selected' : '' }}>Safety & Health</option>
                    <option value="harassment_discrimination" {{ request('category') == 'harassment_discrimination' ? 'selected' : '' }}>Harassment & Discrimination</option>
                    <option value="work_environment" {{ request('category') == 'work_environment' ? 'selected' : '' }}>Work Environment</option>
                    <option value="supervision_guidance" {{ request('category') == 'supervision_guidance' ? 'selected' : '' }}>Supervision & Guidance</option>
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="btn-umpsa-primary">Apply Filters</button>
                @if(request()->hasAny(['search', 'status', 'severity', 'category']))
                    <a href="{{ route('workplace-issues.index') }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors font-semibold">
                        Clear Filters
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Reports List -->
<div class="space-y-4">
    @forelse($reports as $report)
        <div class="card-umpsa hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-start gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $report->title }}
                            </h3>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $report->status_badge_color }}">
                                {{ $report->status_display }}
                            </span>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $report->severity_badge_color }}">
                                {{ $report->severity_display }}
                            </span>
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                            {{ $report->description }}
                        </p>

                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                            @if(!Auth::user()->isStudent())
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ $report->student->name }} ({{ $report->student->matric_no }})</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span>{{ $report->category_display }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $report->submitted_at->format('d M Y, H:i') }}</span>
                            </div>
                            @if($report->attachments->count() > 0)
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <span>{{ $report->attachments->count() }} attachment(s)</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('workplace-issues.show', $report) }}" class="px-4 py-2 bg-umpsa-primary text-white rounded-lg hover:bg-umpsa-secondary transition-colors font-semibold whitespace-nowrap">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card-umpsa p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 mb-4">No workplace issue reports found</p>
            @if(Auth::user()->isStudent())
                <a href="{{ route('workplace-issues.create') }}" class="btn-umpsa-primary">
                    Report Your First Issue
                </a>
            @endif
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($reports->hasPages())
    <div class="mt-6">
        {{ $reports->links() }}
    </div>
@endif
@endsection
