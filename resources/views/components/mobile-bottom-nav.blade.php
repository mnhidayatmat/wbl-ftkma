@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    $user = Auth::user();
    $isAdmin = $user && ($user->role === 'admin' || $user->hasRole('admin'));
    $isStudent = $user && $user->isStudent();

    // Determine active tab based on current route
    $currentRoute = Route::currentRouteName() ?? '';

    // Default active tab
    $activeTab = 'home';

    if ($isStudent) {
        // Student-specific route matching
        if (str_starts_with($currentRoute, 'student.placement') || str_starts_with($currentRoute, 'placement.student')) {
            $activeTab = 'placement';
        } elseif (str_starts_with($currentRoute, 'student.') && (
            str_contains($currentRoute, 'ppe') ||
            str_contains($currentRoute, 'fyp') ||
            str_contains($currentRoute, 'ip') ||
            str_contains($currentRoute, 'osh') ||
            str_contains($currentRoute, 'li') ||
            str_contains($currentRoute, 'submissions')
        )) {
            $activeTab = 'academic';
        } elseif (str_starts_with($currentRoute, 'students.profile') || str_starts_with($currentRoute, 'student.resume')) {
            $activeTab = 'profile';
        } elseif ($currentRoute === 'dashboard') {
            $activeTab = 'home';
        }
    } else {
        // Admin/Staff route matching
        if (str_starts_with($currentRoute, 'admin.students') || str_starts_with($currentRoute, 'students')) {
            $activeTab = 'students';
        } elseif (str_starts_with($currentRoute, 'admin.companies') || str_starts_with($currentRoute, 'companies') || str_starts_with($currentRoute, 'admin.agreements')) {
            $activeTab = 'companies';
        } elseif (str_starts_with($currentRoute, 'academic.ppe') || str_starts_with($currentRoute, 'academic.fyp') || str_starts_with($currentRoute, 'academic.ip') || str_starts_with($currentRoute, 'academic.osh') || str_starts_with($currentRoute, 'academic.li') || str_starts_with($currentRoute, 'placement') || str_starts_with($currentRoute, 'recruitment')) {
            $activeTab = 'academic';
        } elseif ($currentRoute === 'dashboard' || $currentRoute === 'admin.dashboard') {
            $activeTab = 'home';
        }
    }
@endphp

@if($isAdmin || $isStudent)
<!-- Mobile Bottom Navigation - Only visible on mobile (<1024px) -->
<div id="mobile-bottom-nav"
     x-data="mobileBottomNav()"
     x-cloak
     class="lg:hidden fixed bottom-0 left-0 right-0 z-[9998] bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]"
     style="padding-bottom: env(safe-area-inset-bottom, 0px);">

    <!-- Navigation Items -->
    <nav class="flex items-center justify-around h-16">
        @if($isStudent)
            {{-- Student Navigation --}}
            <!-- Home/Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group {{ $activeTab === 'home' ? 'text-[#003A6C] dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-1" fill="{{ $activeTab === 'home' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $activeTab === 'home' ? '0' : '1.5' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-[10px] font-medium">Home</span>
                @if($activeTab === 'home')
                    <span class="absolute bottom-1 w-1 h-1 bg-[#003A6C] dark:bg-blue-400 rounded-full"></span>
                @endif
            </a>

            <!-- Placement -->
            <a href="{{ route('student.placement.index') }}"
               class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group {{ $activeTab === 'placement' ? 'text-[#003A6C] dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-1" fill="{{ $activeTab === 'placement' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $activeTab === 'placement' ? '0' : '1.5' }}" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span class="text-[10px] font-medium">Placement</span>
                @if($activeTab === 'placement')
                    <span class="absolute bottom-1 w-1 h-1 bg-[#003A6C] dark:bg-blue-400 rounded-full"></span>
                @endif
            </a>

            <!-- Academic Modules -->
            <button @click="openAcademicSheet()"
                    type="button"
                    class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group {{ $activeTab === 'academic' ? 'text-[#003A6C] dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-1" fill="{{ $activeTab === 'academic' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $activeTab === 'academic' ? '0' : '1.5' }}" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="text-[10px] font-medium">Academic</span>
                @if($activeTab === 'academic')
                    <span class="absolute bottom-1 w-1 h-1 bg-[#003A6C] dark:bg-blue-400 rounded-full"></span>
                @endif
            </button>

            <!-- Profile -->
            <a href="{{ route('students.profile.show') }}"
               class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group {{ $activeTab === 'profile' ? 'text-[#003A6C] dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-1" fill="{{ $activeTab === 'profile' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $activeTab === 'profile' ? '0' : '1.5' }}" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-[10px] font-medium">Profile</span>
                @if($activeTab === 'profile')
                    <span class="absolute bottom-1 w-1 h-1 bg-[#003A6C] dark:bg-blue-400 rounded-full"></span>
                @endif
            </a>

            <!-- Menu -->
            <button @click="openMenu()"
                    type="button"
                    class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group text-gray-500 dark:text-gray-400">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <span class="text-[10px] font-medium">More</span>
            </button>
        @else
            {{-- Admin Navigation --}}
            <!-- Home -->
            <a href="{{ route('dashboard') }}"
               class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group {{ $activeTab === 'home' ? 'text-[#003A6C] dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-1" fill="{{ $activeTab === 'home' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $activeTab === 'home' ? '0' : '1.5' }}" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-[10px] font-medium">Home</span>
                @if($activeTab === 'home')
                    <span class="absolute bottom-1 w-1 h-1 bg-[#003A6C] dark:bg-blue-400 rounded-full"></span>
                @endif
            </a>

            <!-- Students -->
            <a href="{{ route('admin.students.index') }}"
               class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group {{ $activeTab === 'students' ? 'text-[#003A6C] dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-1" fill="{{ $activeTab === 'students' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $activeTab === 'students' ? '0' : '1.5' }}" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="text-[10px] font-medium">Students</span>
                @if($activeTab === 'students')
                    <span class="absolute bottom-1 w-1 h-1 bg-[#003A6C] dark:bg-blue-400 rounded-full"></span>
                @endif
            </a>

            <!-- Companies -->
            <a href="{{ route('admin.companies.index') }}"
               class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group {{ $activeTab === 'companies' ? 'text-[#003A6C] dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-1" fill="{{ $activeTab === 'companies' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $activeTab === 'companies' ? '0' : '1.5' }}" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="text-[10px] font-medium">Companies</span>
                @if($activeTab === 'companies')
                    <span class="absolute bottom-1 w-1 h-1 bg-[#003A6C] dark:bg-blue-400 rounded-full"></span>
                @endif
            </a>

            <!-- Academic -->
            <button @click="openAcademicSheet()"
                    type="button"
                    class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group {{ $activeTab === 'academic' ? 'text-[#003A6C] dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                <svg class="w-6 h-6 mb-1" fill="{{ $activeTab === 'academic' ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $activeTab === 'academic' ? '0' : '1.5' }}" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="text-[10px] font-medium">Academic</span>
                @if($activeTab === 'academic')
                    <span class="absolute bottom-1 w-1 h-1 bg-[#003A6C] dark:bg-blue-400 rounded-full"></span>
                @endif
            </button>

            <!-- Menu -->
            <button @click="openMenu()"
                    type="button"
                    class="flex flex-col items-center justify-center flex-1 h-full py-2 transition-colors relative group text-gray-500 dark:text-gray-400">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <span class="text-[10px] font-medium">Menu</span>
            </button>
        @endif
    </nav>

    <!-- Academic Modules Sheet -->
    <div x-show="academicSheetOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeAcademicSheet()"
         class="fixed inset-0 bg-black/50 z-[10000]"
         style="display: none;">
    </div>
    <div x-show="academicSheetOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         @click.stop
         class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 rounded-t-2xl z-[10001] max-h-[70vh] overflow-y-auto"
         style="display: none; padding-bottom: env(safe-area-inset-bottom, 0px);">

        <!-- Handle bar -->
        <div class="flex justify-center pt-3 pb-2">
            <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
        </div>

        <div class="px-4 pb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Academic Modules</h3>

            @if($isStudent)
                {{-- Student Academic Modules - Submissions --}}
                <!-- Module Grid -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <!-- PPE -->
                    <a href="{{ route('student.ppe.submissions') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">PPE</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">PPE</span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Submissions</span>
                    </a>

                    <!-- FYP -->
                    <a href="{{ route('student.fyp.submissions') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">FYP</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">FYP</span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Submissions</span>
                    </a>

                    <!-- IP -->
                    <a href="{{ route('student.ip.submissions') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">IP</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">IP</span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Submissions</span>
                    </a>

                    <!-- OSH -->
                    <a href="{{ route('student.osh.submissions') }}" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">OSH</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">OSH</span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Submissions</span>
                    </a>

                    <!-- LI -->
                    <a href="{{ route('student.li.submissions') }}" class="flex flex-col items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">LI</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">LI</span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">Submissions</span>
                    </a>
                </div>

                <!-- Student Quick Links -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">Resources</p>
                    <div class="space-y-2">
                        <a href="{{ route('reference-samples.student') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reference Samples</span>
                        </a>
                    </div>
                </div>
            @else
                {{-- Admin Academic Modules --}}
                <!-- Module Grid -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <!-- PPE -->
                    <a href="{{ route('academic.ppe.assessments.index') }}" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">PPE</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">PPE</span>
                    </a>

                    <!-- FYP -->
                    <a href="{{ route('academic.fyp.assessments.index') }}" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">FYP</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">FYP</span>
                    </a>

                    <!-- IP -->
                    <a href="{{ route('academic.ip.assessments.index') }}" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">IP</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">IP</span>
                    </a>

                    <!-- OSH -->
                    <a href="{{ route('academic.osh.assessments.index') }}" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">OSH</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">OSH</span>
                    </a>

                    <!-- LI -->
                    <a href="{{ route('academic.li.assessments.index') }}" class="flex flex-col items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center mb-2">
                            <span class="text-white font-bold text-sm">LI</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">LI</span>
                    </a>
                </div>

                <!-- Quick Actions -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">Quick Actions</p>
                    <div class="space-y-2">
                        <a href="{{ route('placement.index') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Placements</span>
                        </a>
                        <a href="{{ route('recruitment.pool.index') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Recruitment Pool</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Menu Sheet -->
    <div x-show="menuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeMenu()"
         class="fixed inset-0 bg-black/50 z-[10000]"
         style="display: none;">
    </div>
    <div x-show="menuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         @click.stop
         class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 rounded-t-2xl z-[10001] max-h-[85vh] overflow-y-auto"
         style="display: none; padding-bottom: env(safe-area-inset-bottom, 0px);">

        <!-- Handle bar -->
        <div class="flex justify-center pt-3 pb-2">
            <div class="w-10 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
        </div>

        <div class="px-4 pb-6">
            <!-- User Profile Header -->
            <div class="flex items-center p-4 bg-gradient-to-r from-[#003A6C] to-[#0084C5] rounded-xl mb-6">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-lg">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-white font-semibold truncate">{{ Auth::user()->name ?? 'User' }}</h4>
                    <p class="text-white/70 text-sm truncate">{{ Auth::user()->email ?? '' }}</p>
                    @if($isStudent)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-white/20 text-white mt-1">Student</span>
                    @endif
                </div>
            </div>

            @if($isStudent)
                {{-- Student Menu --}}
                <!-- Quick Access Section -->
                <div class="mb-6">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider font-medium">Quick Access</p>
                    <div class="space-y-1">
                        <a href="{{ route('student.resume.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Resume Inspection</span>
                        </a>
                        <a href="{{ route('workplace-issues.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Workplace Issues</span>
                        </a>
                        <a href="{{ route('reference-samples.student') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reference Samples</span>
                        </a>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="mb-6">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider font-medium">Settings</p>
                    <div class="space-y-1">
                        <a href="{{ route('students.profile.show') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">My Profile</span>
                        </a>
                        <button @click="toggleTheme()" type="button" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dark Mode</span>
                            </div>
                            <div class="relative">
                                <div class="w-10 h-6 bg-gray-200 dark:bg-[#003A6C] rounded-full transition-colors"></div>
                                <div class="absolute top-1 left-1 dark:left-5 w-4 h-4 bg-white rounded-full shadow transition-all"></div>
                            </div>
                        </button>
                    </div>
                </div>
            @else
                {{-- Admin Menu --}}
                <!-- Management Section -->
                <div class="mb-6">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider font-medium">Management</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.groups.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Groups</span>
                        </a>
                        <a href="{{ route('admin.programmes.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Programmes</span>
                        </a>
                        <a href="{{ route('admin.assessments.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Assessments</span>
                        </a>
                    </div>
                </div>

                <!-- Administration Section -->
                <div class="mb-6">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider font-medium">Administration</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.users.roles.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">User Roles</span>
                        </a>
                        <a href="{{ route('admin.permissions.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Permissions</span>
                        </a>
                        <a href="{{ route('admin.documents.sal') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Document Templates</span>
                        </a>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="mb-6">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider font-medium">Settings</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.profile.edit') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">My Profile</span>
                        </a>
                        <button @click="toggleTheme()" type="button" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dark Mode</span>
                            </div>
                            <div class="relative">
                                <div class="w-10 h-6 bg-gray-200 dark:bg-[#003A6C] rounded-full transition-colors"></div>
                                <div class="absolute top-1 left-1 dark:left-5 w-4 h-4 bg-white rounded-full shadow transition-all"></div>
                            </div>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center p-3 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="text-sm font-medium text-red-600 dark:text-red-400">Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function mobileBottomNav() {
        return {
            menuOpen: false,
            academicSheetOpen: false,

            openMenu() {
                this.academicSheetOpen = false;
                this.menuOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeMenu() {
                this.menuOpen = false;
                document.body.style.overflow = '';
            },

            openAcademicSheet() {
                this.menuOpen = false;
                this.academicSheetOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeAcademicSheet() {
                this.academicSheetOpen = false;
                document.body.style.overflow = '';
            },

            toggleTheme() {
                const html = document.documentElement;
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        };
    }
</script>
@endif
