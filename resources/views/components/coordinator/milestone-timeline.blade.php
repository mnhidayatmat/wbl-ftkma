@props(['module', 'atWindow' => null, 'icWindow' => null, 'groups' => null])

@php
    $moduleLabels = [
        'ppe' => ['name' => 'PPE', 'at' => 'Academic Tutor', 'ic' => 'Industry Coach'],
        'fyp' => ['name' => 'FYP', 'at' => 'Academic Tutor', 'ic' => 'Industry Coach'],
        'ip' => ['name' => 'IP', 'at' => 'Academic Tutor', 'ic' => 'Industry Coach'],
        'osh' => ['name' => 'OSH', 'at' => 'Academic Tutor', 'ic' => 'Industry Coach'],
        'li' => ['name' => 'LI', 'at' => 'Supervisor LI', 'ic' => 'Industry Coach'],
    ];
    $labels = $moduleLabels[$module] ?? $moduleLabels['ppe'];

    // Status colors for evaluation windows
    $statusColors = [
        'open' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
        'closed' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
        'upcoming' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
        'disabled' => 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200',
    ];

    // Get current active group for date context
    $activeGroup = $groups ? $groups->first() : null;

    // Calculate timeline progress
    $now = now();
    $timelineStart = $activeGroup?->start_date ?? $now->copy()->startOfMonth();
    $timelineEnd = $activeGroup?->end_date ?? $now->copy()->addMonths(6);
    $totalDays = $timelineStart->diffInDays($timelineEnd) ?: 1;
    $elapsedDays = $timelineStart->diffInDays($now);
    $progressPercent = min(100, max(0, ($elapsedDays / $totalDays) * 100));
@endphp

<!-- Project Milestone Timeline -->
<div class="mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-[#003A6C] to-[#0084C5] px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">Project Milestone Timeline</h2>
                        <p class="text-blue-100 text-sm">{{ $labels['name'] }} Module Assessment Schedule</p>
                    </div>
                </div>
                @if($activeGroup)
                <div class="text-right hidden sm:block">
                    <p class="text-white text-sm font-medium">{{ $activeGroup->name }}</p>
                    <p class="text-blue-100 text-xs">
                        {{ $activeGroup->start_date?->format('d M Y') ?? 'TBD' }} - {{ $activeGroup->end_date?->format('d M Y') ?? 'TBD' }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        <div class="p-6">
            <!-- Overall Progress Bar -->
            @if($activeGroup && $activeGroup->start_date && $activeGroup->end_date)
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Progress</span>
                    <span class="text-sm font-semibold text-[#003A6C] dark:text-blue-400">{{ round($progressPercent) }}% Complete</span>
                </div>
                <div class="relative h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-[#003A6C] to-[#0084C5] rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    <!-- Today marker -->
                    <div class="absolute top-1/2 -translate-y-1/2 w-3 h-3 bg-white border-2 border-[#00AEEF] rounded-full shadow-md transition-all duration-500" style="left: calc({{ $progressPercent }}% - 6px)"></div>
                </div>
                <div class="flex justify-between mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ $activeGroup->start_date->format('d M Y') }}</span>
                    <span>Today: {{ $now->format('d M Y') }}</span>
                    <span>{{ $activeGroup->end_date->format('d M Y') }}</span>
                </div>
            </div>
            @endif

            <!-- Assessment Windows Timeline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- AT/Lecturer Window -->
                <div class="relative bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $labels['at'] }} Evaluation</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">AT Contribution Assessment</p>
                            </div>
                        </div>
                        @if($atWindow)
                            @php
                                $atStatus = $atWindow->status ?? 'closed';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColors[$atStatus] ?? $statusColors['closed'] }}">
                                @if($atStatus === 'open')
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                @endif
                                {{ ucfirst($atStatus) }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                Not Set
                            </span>
                        @endif
                    </div>

                    @if($atWindow && ($atWindow->start_at || $atWindow->end_at))
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">
                                    {{ $atWindow->start_at?->format('d M Y') ?? 'Not set' }}
                                    <span class="text-gray-400 mx-1">→</span>
                                    {{ $atWindow->end_at?->format('d M Y') ?? 'Not set' }}
                                </span>
                            </div>
                            @if($atWindow->start_at && $atWindow->end_at)
                                @php
                                    $atTotalDays = $atWindow->start_at->diffInDays($atWindow->end_at) ?: 1;
                                    $atElapsed = max(0, $atWindow->start_at->diffInDays($now));
                                    $atProgress = min(100, max(0, ($atElapsed / $atTotalDays) * 100));
                                    if ($now < $atWindow->start_at) $atProgress = 0;
                                    if ($now > $atWindow->end_at) $atProgress = 100;
                                @endphp
                                <div class="mt-2 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-500 rounded-full transition-all duration-500" style="width: {{ $atProgress }}%"></div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">No schedule configured</p>
                        </div>
                    @endif
                </div>

                <!-- IC Window -->
                <div class="relative bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $labels['ic'] }} Evaluation</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">IC Contribution Assessment</p>
                            </div>
                        </div>
                        @if($icWindow)
                            @php
                                $icStatus = $icWindow->status ?? 'closed';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColors[$icStatus] ?? $statusColors['closed'] }}">
                                @if($icStatus === 'open')
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                @endif
                                {{ ucfirst($icStatus) }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                Not Set
                            </span>
                        @endif
                    </div>

                    @if($icWindow && ($icWindow->start_at || $icWindow->end_at))
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">
                                    {{ $icWindow->start_at?->format('d M Y') ?? 'Not set' }}
                                    <span class="text-gray-400 mx-1">→</span>
                                    {{ $icWindow->end_at?->format('d M Y') ?? 'Not set' }}
                                </span>
                            </div>
                            @if($icWindow->start_at && $icWindow->end_at)
                                @php
                                    $icTotalDays = $icWindow->start_at->diffInDays($icWindow->end_at) ?: 1;
                                    $icElapsed = max(0, $icWindow->start_at->diffInDays($now));
                                    $icProgress = min(100, max(0, ($icElapsed / $icTotalDays) * 100));
                                    if ($now < $icWindow->start_at) $icProgress = 0;
                                    if ($now > $icWindow->end_at) $icProgress = 100;
                                @endphp
                                <div class="mt-2 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="h-full bg-teal-500 rounded-full transition-all duration-500" style="width: {{ $icProgress }}%"></div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">No schedule configured</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Link to Schedule Management -->
            @php
                $scheduleRoutes = [
                    'ppe' => 'academic.ppe.schedule.index',
                    'fyp' => 'academic.fyp.schedule.index',
                    'ip' => 'academic.ip.schedule.index',
                    'osh' => 'academic.osh.schedule.index',
                    'li' => 'academic.li.schedule.index',
                ];
                $scheduleRoute = $scheduleRoutes[$module] ?? null;
            @endphp
            @if($scheduleRoute && Route::has($scheduleRoute))
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <a href="{{ route($scheduleRoute) }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#0084C5] hover:text-[#003A6C] dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    <span>Manage Assessment Schedule</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
