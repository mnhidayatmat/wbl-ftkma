@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Dashboard Content -->
<div class="min-h-screen">
    <!-- Group Filter (Admin & Coordinator only) -->
    @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
    <div class="mb-4 sm:mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter Groups:</span>
                <a href="{{ route('dashboard', ['group_filter' => 'all']) }}" 
                   class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ (!isset($groupFilter) || $groupFilter === 'all') ? 'bg-[#0084C5] text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                    All Groups
                </a>
                <a href="{{ route('dashboard', ['group_filter' => 'active']) }}" 
                   class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ (isset($groupFilter) && $groupFilter === 'active') ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                    Active Only
                </a>
                <a href="{{ route('dashboard', ['group_filter' => 'completed']) }}" 
                   class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ (isset($groupFilter) && $groupFilter === 'completed') ? 'bg-gray-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                    Completed Only
                </a>
            </div>
            @if(isset($groupFilter) && $groupFilter !== 'all')
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Showing {{ $groupFilter === 'active' ? 'Active' : 'Completed' }} groups only
                </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">
        <x-stat-card 
            title="Total Students"
            :value="number_format($stats['students'])"
            :change="$changes['students']['value']"
            :changeType="$changes['students']['type']"
            icon='<svg class="w-6 h-6 text-umpsa-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
            iconBg="bg-umpsa-accent/10"
        />
        
        <x-stat-card 
            title="Total Groups"
            :value="number_format($stats['groups'])"
            subtitle="{{ isset($stats['active_groups']) ? $stats['active_groups'] . ' Active, ' . $stats['completed_groups'] . ' Completed' : '' }}"
            :change="$changes['groups']['value']"
            :changeType="$changes['groups']['type']"
            icon='<svg class="w-6 h-6 text-umpsa-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
            iconBg="bg-umpsa-secondary/10"
        />
        
        <x-stat-card 
            title="Total Companies"
            :value="number_format($stats['companies'])"
            :change="$changes['companies']['value']"
            :changeType="$changes['companies']['type']"
            icon='<svg class="w-6 h-6 text-umpsa-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
            iconBg="bg-umpsa-primary/10"
        />
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">
        <!-- Students by Program Chart Card -->
        <div class="lg:col-span-2">
            <x-chart-card 
                title="Students by Program"
                chartId="studentsProgramChart"
                chartType="bar"
                :height="300"
            />
        </div>

        <!-- Donut Chart Card -->
        <div class="lg:col-span-1">
            <x-donut-card 
                title="Students by Company"
                chartId="studentsDonutChart"
                :height="300"
            />
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
        <!-- Program and Group Chart Card -->
        <div>
            <x-chart-card 
                title="Number of Students by Program and Group"
                chartId="studentsProgramGroupChart"
                chartType="bar"
                :height="300"
            />
        </div>

        <!-- Bar Chart Card -->
        <div>
            <x-chart-card 
                title="Students by Group"
                chartId="studentsBarChart"
                chartType="bar"
                :height="300"
            />
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // UMPSA Color Palette
    const UMPSA_COLORS = {
        primary: '#003A6C',
        secondary: '#0084C5',
        accent: '#00AEEF',
        darkNavy: '#002244',
        softGray: '#F4F7FC',
        neutralGray: '#E6ECF2',
        success: '#28A745',
        danger: '#DC3545'
    };

    // Detect dark mode
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Chart colors based on theme
    const chartColors = {
        text: isDarkMode ? '#9CA3AF' : '#6B7280',
        grid: isDarkMode ? '#374151' : '#E6ECF2',
        background: isDarkMode ? '#1F2937' : '#FFFFFF'
    };

    // Bar Chart - Students by Program
    const programCtx = document.getElementById('studentsProgramChart');
    if (programCtx) {
        new Chart(programCtx, {
            type: 'bar',
            data: {
                labels: @json($programChartData['labels']),
                datasets: [{
                    label: 'Students',
                    data: @json($programChartData['data']),
                    backgroundColor: [
                        UMPSA_COLORS.primary,
                        UMPSA_COLORS.secondary,
                        UMPSA_COLORS.accent,
                        UMPSA_COLORS.darkNavy,
                        UMPSA_COLORS.primary + '80',
                        UMPSA_COLORS.secondary + '80'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1F2937' : UMPSA_COLORS.primary,
                        titleColor: isDarkMode ? '#F9FAFB' : '#FFFFFF',
                        bodyColor: isDarkMode ? '#F9FAFB' : '#FFFFFF',
                        borderColor: isDarkMode ? '#374151' : UMPSA_COLORS.primary,
                        borderWidth: 1,
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Students: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text,
                            font: {
                                size: 12
                            },
                            stepSize: 1,
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Number of Students',
                            color: chartColors.text,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: chartColors.text,
                            font: {
                                size: 12
                            }
                        },
                        title: {
                            display: true,
                            text: 'Program',
                            color: chartColors.text,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    }

    // Grouped Bar Chart - Students by Program and Group
    const programGroupCtx = document.getElementById('studentsProgramGroupChart');
    if (programGroupCtx) {
        new Chart(programGroupCtx, {
            type: 'bar',
            data: {
                labels: @json($programGroupChartData['labels']),
                datasets: @json($programGroupChartData['datasets'])
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            color: chartColors.text,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1F2937' : UMPSA_COLORS.primary,
                        titleColor: isDarkMode ? '#F9FAFB' : '#FFFFFF',
                        bodyColor: isDarkMode ? '#F9FAFB' : '#FFFFFF',
                        borderColor: isDarkMode ? '#374151' : UMPSA_COLORS.primary,
                        borderWidth: 1,
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' students';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text,
                            font: {
                                size: 12
                            },
                            stepSize: 1,
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Number of Students',
                            color: chartColors.text,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: chartColors.text,
                            font: {
                                size: 12
                            }
                        },
                        title: {
                            display: true,
                            text: 'Groups',
                            color: chartColors.text,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    }

    // Bar Chart - Students by Group
    const barCtx = document.getElementById('studentsBarChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($barChartData['labels']),
                datasets: [{
                    label: 'Students',
                    data: @json($barChartData['data']),
                    backgroundColor: [
                        UMPSA_COLORS.accent,
                        UMPSA_COLORS.primary,
                        UMPSA_COLORS.secondary,
                        UMPSA_COLORS.accent + '80',
                        UMPSA_COLORS.primary + '80'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1F2937' : UMPSA_COLORS.primary,
                        titleColor: isDarkMode ? '#F9FAFB' : '#FFFFFF',
                        bodyColor: isDarkMode ? '#F9FAFB' : '#FFFFFF',
                        borderColor: isDarkMode ? '#374151' : UMPSA_COLORS.primary,
                        borderWidth: 1,
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text,
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: chartColors.text,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }

    // Donut Chart - Students by Company
    const donutCtx = document.getElementById('studentsDonutChart');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: @json($donutChartData['labels']),
                datasets: [{
                    data: @json($donutChartData['data']),
                    backgroundColor: [
                        UMPSA_COLORS.primary,
                        UMPSA_COLORS.secondary,
                        UMPSA_COLORS.accent,
                        UMPSA_COLORS.neutralGray,
                        UMPSA_COLORS.primary + '80'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            color: chartColors.text,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#1F2937' : UMPSA_COLORS.primary,
                        titleColor: isDarkMode ? '#F9FAFB' : '#FFFFFF',
                        bodyColor: isDarkMode ? '#F9FAFB' : '#FFFFFF',
                        borderColor: isDarkMode ? '#374151' : UMPSA_COLORS.primary,
                        borderWidth: 1,
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8
                    }
                },
                cutout: '60%'
            }
        });
    }
</script>
@endsection
