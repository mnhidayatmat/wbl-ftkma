@php
    $user = auth()->user();
    $isLecturer = $user->isLecturer();
    $isAt = $user->isAt();
    $isSupervisorLi = $user->isSupervisorLi();
    $isIc = $user->isIndustry();
    $isStudent = $user->isStudent();
    $isAdmin = $user->isAdmin();
    $isCoordinator = $user->isCoordinator();
    
    $lecturerIsAt = $isLecturer && $isAt;
    $lecturerIsSupervisorLi = $isLecturer && $isSupervisorLi;
@endphp

<nav class="mt-4 space-y-1" :class="isSidebarCollapsed ? 'px-2' : 'px-3'" 
     x-data="{
         companiesMenuOpen: {{ request()->routeIs('companies.*') || request()->routeIs('admin.agreements.*') ? 'true' : 'false' }},
         ppeMenuOpen: {{ request()->routeIs('academic.ppe.*') || request()->routeIs('ppe.assign-students') ? 'true' : 'false' }},
         fypMenuOpen: {{ request()->routeIs('academic.fyp.*') || request()->routeIs('fyp.*') || request()->routeIs('fyp.assign-students') ? 'true' : 'false' }},
         ipMenuOpen: {{ request()->routeIs('academic.ip.*') || request()->routeIs('ip.assign-students') ? 'true' : 'false' }},
         oshMenuOpen: {{ request()->routeIs('academic.osh.*') || request()->routeIs('osh.assign-students') ? 'true' : 'false' }},
         liMenuOpen: {{ request()->routeIs('academic.li.*') || request()->routeIs('li.*') || request()->routeIs('li.assign-students') ? 'true' : 'false' }},
         // Helper to check if sidebar text should be visible
         // Access parent scope (body x-data) - sidebar is included in same scope
         get sidebarTextVisible() {
             try {
                 // Try to access parent scope
                 const bodyEl = document.body;
                 const parentData = window.Alpine?.$data(bodyEl);
                 if (parentData) {
                     const isDesktop = window.innerWidth >= 1024;
                     if (isDesktop) {
                         return parentData.sidebarExpanded !== false;
                     }
                     return parentData.sidebarOpen === true;
                 }
             } catch(e) {
                 // Silent fail - fallback to default behavior
             }
             // Fallback: check window width and assume expanded/open
             return window.innerWidth >= 1024;
         },
         get isSidebarCollapsed() {
             try {
                 const bodyEl = document.body;
                 const parentData = window.Alpine?.$data(bodyEl);
                 if (parentData) {
                     const isDesktop = window.innerWidth >= 1024;
                     if (isDesktop) {
                         return parentData.sidebarExpanded === false;
                     }
                     return parentData.sidebarOpen === false;
                 }
             } catch(e) {
                 // Silent fail - fallback to default behavior
             }
             return false;
         },
         toggleMenu(menuName) {
             // Check if the clicked menu is already open
             let isCurrentlyOpen = false;
            if (menuName === 'companies' && this.companiesMenuOpen) isCurrentlyOpen = true;
            if (menuName === 'ppe' && this.ppeMenuOpen) isCurrentlyOpen = true;
            if (menuName === 'fyp' && this.fypMenuOpen) isCurrentlyOpen = true;
            if (menuName === 'ip' && this.ipMenuOpen) isCurrentlyOpen = true;
            if (menuName === 'osh' && this.oshMenuOpen) isCurrentlyOpen = true;
            if (menuName === 'li' && this.liMenuOpen) isCurrentlyOpen = true;
            
            // Close all menus first
            this.companiesMenuOpen = false;
            this.ppeMenuOpen = false;
            this.fypMenuOpen = false;
            this.ipMenuOpen = false;
            this.oshMenuOpen = false;
            this.liMenuOpen = false;
            
            // If the clicked menu was not open, open it now
            if (!isCurrentlyOpen) {
                if (menuName === 'companies') this.companiesMenuOpen = true;
                if (menuName === 'ppe') this.ppeMenuOpen = true;
                if (menuName === 'fyp') this.fypMenuOpen = true;
                if (menuName === 'ip') this.ipMenuOpen = true;
                if (menuName === 'osh') this.oshMenuOpen = true;
                if (menuName === 'li') this.liMenuOpen = true;
            }
        },
         closeMobileSidebarOnClick(event) {
             // Close mobile/tablet sidebar when clicking actual links (not menu toggles)
             // Only close if clicking on an anchor tag (link) and on mobile/tablet
             if (event.target.closest('a') && window.innerWidth < 1024) {
                 const body = document.body;
                 const alpineData = Alpine.$data(body);
                 if (alpineData && alpineData.closeSidebar) {
                     // Small delay to allow navigation to start
                     setTimeout(() => {
                         alpineData.closeSidebar();
                     }, 100);
                 }
             }
         }
     }"
     @click="closeMobileSidebarOnClick($event)">
    
    <!-- Group #1: Dashboard (Hidden for Students - they have it in STUDENT section) -->
    @if(!$isStudent)
    <a href="{{ route('dashboard') }}" 
       class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('dashboard') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
       :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
       :title="isSidebarCollapsed ? 'Dashboard' : ''">
        <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
        </div>
        <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Dashboard</span>
    </a>


    <div class="relative">
        <button @click="toggleMenu('companies')"
                class="w-full flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('companies.*') || request()->routeIs('admin.agreements.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                :class="isSidebarCollapsed ? 'justify-center px-0' : 'justify-between px-2'"
                :title="isSidebarCollapsed ? 'Companies' : ''">
            <div class="flex items-center gap-1">
                <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Companies</span>
            </div>
            <svg x-show="sidebarTextVisible" 
                 class="w-4 h-4 transition-transform duration-200 flex-shrink-0" 
                 :class="companiesMenuOpen ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <!-- Companies Sub-menu -->
        <div x-show="companiesMenuOpen && sidebarTextVisible" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-1 ml-4 pl-4 border-l-2 border-gray-200 dark:border-gray-700 space-y-1">
            
            <!-- Company Directory -->
            <a href="{{ route('companies.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-all duration-200 min-h-[40px] {{ request()->routeIs('companies.*') ? 'text-[#0084C5] font-medium bg-[#0084C5]/5 border-l-2 border-[#00A86B] -ml-[2px] pl-[14px]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#0084C5]' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Company Directory
            </a>
            
            <!-- MoU/MoA/LOI -->
            <a href="{{ route('admin.agreements.index') }}" 
               class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-all duration-200 min-h-[40px] {{ request()->routeIs('admin.agreements.*') ? 'text-[#0084C5] font-medium bg-[#0084C5]/5 border-l-2 border-[#00A86B] -ml-[2px] pl-[14px]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-[#0084C5]' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                MoU / MoA / LOI
            </a>
        </div>
    </div>
    
    <a href="{{ route('admin.users.roles.index') }}" 
       class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('admin.users.roles.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
       :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
       :title="isSidebarCollapsed ? 'User Roles' : ''">
        <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">User Roles</span>
    </a>
    
    <!-- Group Control (Admin Only) - Manage group lifecycle and visibility -->
    <a href="{{ route('admin.groups.index') }}" 
       class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('admin.groups.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
       :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
       :title="isSidebarCollapsed ? 'Group Control' : ''">
        <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Group Control</span>
    </a>
    @endif

    <!-- Student Placement Tracking -->
    @if($isAdmin || $isCoordinator || $isLecturer || $isAt || $isSupervisorLi || $isIc)
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <p x-show="sidebarTextVisible" x-transition class="px-3 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 font-semibold" :class="isSidebarCollapsed ? 'text-center px-0' : ''">
            <span x-show="!isSidebarCollapsed">PLACEMENT</span>
            <span x-show="isSidebarCollapsed" class="block w-8 h-[1px] bg-gray-300 dark:bg-gray-600 mx-auto"></span>
        </p>
        
        @if($isAdmin || $isCoordinator)
        <a href="{{ route('coordinator.resume.index') }}" 
           class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('coordinator.resume.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
           :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
           :title="isSidebarCollapsed ? 'Resume Inspection' : ''">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Resume Inspection</span>
        </a>
        @endif
        
        <a href="{{ route('placement.index') }}" 
           class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('placement.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
           :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
           :title="isSidebarCollapsed ? 'Student Placement' : ''">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Student Placement</span>
        </a>
    </div>
    @endif

    <!-- Group #2: ACADEMIC MODULES (Hidden for Students - they have "My Courses" section) -->
    @if($isLecturer || $isAt || $isSupervisorLi || $isIc || $isAdmin)
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <p x-show="sidebarTextVisible" x-transition class="px-3 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 font-semibold" :class="isSidebarCollapsed ? 'text-center px-0' : ''">
            <span x-show="!isSidebarCollapsed">ACADEMIC MODULES</span>
            <span x-show="isSidebarCollapsed" class="block w-8 h-[1px] bg-gray-300 dark:bg-gray-600 mx-auto"></span>
        </p>
        
        <!-- FYP -->
        @if($isAt || ($isLecturer && $lecturerIsAt) || $isAdmin)
        <div class="mb-1">
            <button type="button"
                    @click="toggleMenu('fyp')"
                    class="w-full flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-h-[44px]"
                    :class="isSidebarCollapsed ? 'justify-center px-0' : 'justify-between px-2'"
                    :title="isSidebarCollapsed ? 'FYP' : ''">
                <div class="flex items-center gap-1">
                    <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">FYP</span>
                </div>
                <svg x-show="sidebarTextVisible" 
                     class="w-4 h-4 transition-transform duration-300 flex-shrink-0" 
                     :class="{ 'rotate-90': fypMenuOpen }" 
                     fill="none" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24" 
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div x-show="fypMenuOpen && sidebarTextVisible" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="ml-6 mt-1 space-y-1">
                @if($isAdmin)
                <a href="{{ route('fyp.assign-students') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('fyp.assign-students') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assign Students
                </a>
                <a href="{{ route('academic.fyp.assessments.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.assessments.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessments
                </a>
                <a href="{{ route('academic.fyp.schedule.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.schedule.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessment Schedule
                </a>
                @endif
                @if($isAdmin || $isCoordinator || $isLecturer)
                <a href="{{ route('academic.fyp.clo-plo.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.clo-plo.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    CLO–PLO Analysis
                </a>
                @endif
                @if($isAt || $isAdmin)
                <a href="{{ route('academic.fyp.lecturer.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.lecturer.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    AT Evaluation
                </a>
                @endif
                @if($isIc || $isAdmin)
                <a href="{{ route('academic.fyp.ic.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.ic.*') || request()->routeIs('academic.fyp.logbook.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    IC Evaluation
                </a>
                {{-- Logbook Evaluation is now accessed through IC Evaluation page --}}
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.fyp.progress.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.progress.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Evaluation Progress
                </a>
                @endif
                @if($isAt || $isAdmin)
                <a href="{{ route('academic.fyp.performance.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.performance.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Student Performance
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.fyp.moderation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.moderation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Moderation
                </a>
                <a href="{{ route('academic.fyp.finalisation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.finalisation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Result Finalisation
                </a>
                <a href="{{ route('academic.fyp.reports.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.reports.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Reports
                </a>
                <a href="{{ route('academic.fyp.proposals.index') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.proposals.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Project Proposals
                </a>
                <a href="{{ route('academic.fyp.audit.index') }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.fyp.audit.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Audit Log
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- IP -->
        @if($isLecturer || $isAdmin)
        <div class="mb-1">
            <button type="button"
                    @click="toggleMenu('ip')"
                    class="w-full flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-h-[44px]"
                    :class="isSidebarCollapsed ? 'justify-center px-0' : 'justify-between px-2'"
                    :title="isSidebarCollapsed ? 'IP' : ''">
                <div class="flex items-center gap-1">
                    <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.255M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">IP</span>
                </div>
                <svg x-show="sidebarTextVisible" 
                     class="w-4 h-4 transition-transform duration-300 flex-shrink-0" 
                     :class="{ 'rotate-90': ipMenuOpen }" 
                     fill="none" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24" 
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div x-show="ipMenuOpen && sidebarTextVisible" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="ml-6 mt-1 space-y-1">
                @if($isAdmin)
                <a href="{{ route('ip.assign-students') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('ip.assign-students') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assign Students
                </a>
                <a href="{{ route('academic.ip.assessments.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.assessments.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessments
                </a>
                <a href="{{ route('academic.ip.schedule.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.schedule.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessment Schedule
                </a>
                @endif
                @if($isAdmin || $isCoordinator || $isLecturer)
                <a href="{{ route('academic.ip.clo-plo.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.clo-plo.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    CLO–PLO Analysis
                </a>
                @endif
                @if($isLecturer || $isAdmin)
                <a href="{{ route('academic.ip.lecturer.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.lecturer.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Lecturer Evaluation
                </a>
                @endif
                @if($isIc || $isAdmin)
                <a href="{{ route('academic.ip.ic.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.ic.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    IC Evaluation
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.ip.progress.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.progress.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Evaluation Progress
                </a>
                @endif
                @if($isLecturer || $isAdmin)
                <a href="{{ route('academic.ip.performance.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.performance.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Student Performance
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.ip.moderation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.moderation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Moderation
                </a>
                <a href="{{ route('academic.ip.finalisation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.finalisation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Result Finalisation
                </a>
                <a href="{{ route('academic.ip.reports.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.reports.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Reports
                </a>
                <a href="{{ route('academic.ip.audit.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ip.audit.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Audit Log
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- OSH -->
        @if($isLecturer || $isAdmin)
        <div class="mb-1">
            <button type="button"
                    @click="toggleMenu('osh')"
                    class="w-full flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-h-[44px]"
                    :class="isSidebarCollapsed ? 'justify-center px-0' : 'justify-between px-2'"
                    :title="isSidebarCollapsed ? 'OSH' : ''">
                <div class="flex items-center gap-1">
                    <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">OSH</span>
                </div>
                <svg x-show="sidebarTextVisible" 
                     class="w-4 h-4 transition-transform duration-300 flex-shrink-0" 
                     :class="{ 'rotate-90': oshMenuOpen }" 
                     fill="none" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24" 
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div x-show="oshMenuOpen && sidebarTextVisible" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="ml-6 mt-1 space-y-1">
                @if($isAdmin)
                <a href="{{ route('osh.assign-students') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('osh.assign-students') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assign Students
                </a>
                <a href="{{ route('academic.osh.assessments.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.assessments.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessments
                </a>
                <a href="{{ route('academic.osh.schedule.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.schedule.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessment Schedule
                </a>
                @endif
                @if($isAdmin || $isCoordinator || $isLecturer)
                <a href="{{ route('academic.osh.clo-plo.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.clo-plo.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    CLO–PLO Analysis
                </a>
                @endif
                @if($isLecturer || $isAdmin)
                <a href="{{ route('academic.osh.lecturer.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.lecturer.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Lecturer Evaluation
                </a>
                @endif
                @if($isIc || $isAdmin)
                <a href="{{ route('academic.osh.ic.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.ic.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    IC Evaluation
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.osh.progress.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.progress.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Evaluation Progress
                </a>
                @endif
                @if($isLecturer || $isAdmin)
                <a href="{{ route('academic.osh.performance.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.performance.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Student Performance
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.osh.moderation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.moderation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Moderation
                </a>
                <a href="{{ route('academic.osh.finalisation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.finalisation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Result Finalisation
                </a>
                <a href="{{ route('academic.osh.reports.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.reports.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Reports
                </a>
                <a href="{{ route('academic.osh.audit.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.osh.audit.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Audit Log
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- PPE -->
        @if($isLecturer || $isIc || $isAdmin)
        <div class="mb-1">
            <!-- Parent Menu Item (Toggle Only - No Navigation) -->
            <button type="button"
                    @click="toggleMenu('ppe')"
                    class="w-full flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-h-[44px]"
                    :class="isSidebarCollapsed ? 'justify-center px-0' : 'justify-between px-2'"
                    :title="isSidebarCollapsed ? 'PPE' : ''">
                <div class="flex items-center gap-1">
                    <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">PPE</span>
                </div>
                <!-- Chevron Icon (Rotates on Expand) -->
                <svg x-show="sidebarTextVisible" 
                     class="w-4 h-4 transition-transform duration-300 flex-shrink-0" 
                     :class="{ 'rotate-90': ppeMenuOpen }" 
                     fill="none" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24" 
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div x-show="ppeMenuOpen && sidebarTextVisible" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="ml-6 mt-1 space-y-1">
                @if($isAdmin)
                <a href="{{ route('ppe.assign-students') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('ppe.assign-students') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assign Students
                </a>
                <a href="{{ route('academic.ppe.assessments.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.assessments.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessments
                </a>
                <a href="{{ route('academic.ppe.schedule.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.schedule.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessment Schedule
                </a>
                @endif
                @if($isAdmin || $isCoordinator || $isLecturer)
                <a href="{{ route('academic.ppe.clo-plo.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.clo-plo.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    CLO–PLO Analysis
                </a>
                @endif
                @if($isLecturer || $isAdmin)
                <a href="{{ route('academic.ppe.lecturer.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.lecturer.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Lecturer Evaluation
                </a>
                @endif
                @if($isIc || $isAdmin)
                <a href="{{ route('academic.ppe.ic.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.ic.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    IC Evaluation
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.ppe.progress.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.progress.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Evaluation Progress
                </a>
                @endif
                @if($isLecturer || $isAdmin)
                <a href="{{ route('academic.ppe.performance.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.performance.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Student Performance
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.ppe.moderation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.moderation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Moderation
                </a>
                <a href="{{ route('academic.ppe.finalisation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.finalisation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Result Finalisation
                </a>
                <a href="{{ route('academic.ppe.reports.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.reports.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Reports
                </a>
                <a href="{{ route('academic.ppe.audit.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.ppe.audit.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Audit Log
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- Industrial Training -->
        @if($isSupervisorLi || ($isLecturer && $lecturerIsSupervisorLi) || $isIc || $isAdmin)
        <div class="mb-1">
            <button type="button"
                    @click="toggleMenu('li')"
                    class="w-full flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-h-[44px]"
                    :class="isSidebarCollapsed ? 'justify-center px-0' : 'justify-between px-2'"
                    :title="isSidebarCollapsed ? 'Industrial Training' : ''">
                <div class="flex items-center gap-1">
                    <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Industrial Training</span>
                </div>
                <svg x-show="sidebarTextVisible" 
                     class="w-4 h-4 transition-transform duration-300 flex-shrink-0" 
                     :class="{ 'rotate-90': liMenuOpen }" 
                     fill="none" 
                     stroke="currentColor" 
                     viewBox="0 0 24 24" 
                     stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            <div x-show="liMenuOpen && sidebarTextVisible" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="ml-6 mt-1 space-y-1">
                @if($isAdmin)
                <a href="{{ route('li.assign-students') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('li.assign-students') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assign Students
                </a>
                <a href="{{ route('academic.li.assessments.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.assessments.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessments
                </a>
                <a href="{{ route('academic.li.schedule.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.schedule.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Assessment Schedule
                </a>
                @endif
                @if($isAdmin || $isCoordinator || $isLecturer)
                <a href="{{ route('academic.li.clo-plo.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.clo-plo.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    CLO–PLO Analysis
                </a>
                @endif
                @if($isSupervisorLi || ($isLecturer && $lecturerIsSupervisorLi) || $isAdmin)
                <a href="{{ route('academic.li.lecturer.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.lecturer.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Supervisor Evaluation
                </a>
                @endif
                @if($isIc || $isAdmin)
                <a href="{{ route('academic.li.ic.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.ic.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    IC Evaluation
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.li.progress.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.progress.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Evaluation Progress
                </a>
                @endif
                @if($isSupervisorLi || ($isLecturer && $lecturerIsSupervisorLi) || $isIc || $isAdmin)
                <a href="{{ route('academic.li.performance.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.performance.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Student Performance
                </a>
                @endif
                @if($isAdmin)
                <a href="{{ route('academic.li.moderation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.moderation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Moderation
                </a>
                <a href="{{ route('academic.li.finalisation.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.finalisation.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Result Finalisation
                </a>
                <a href="{{ route('academic.li.reports.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.reports.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Reports
                </a>
                <a href="{{ route('academic.li.audit.index') }}" 
                   class="block px-3 py-2 text-sm rounded-lg transition-all duration-300 min-h-[44px] flex items-center {{ request()->routeIs('academic.li.audit.*') ? 'text-[#0084C5] font-medium border-l-2 border-[#00A86B]' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    Audit Log
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Student Menu Section -->
    @if($isStudent)
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <p x-show="sidebarTextVisible" x-transition class="px-3 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 font-semibold" :class="isSidebarCollapsed ? 'text-center px-0' : ''">
            <span x-show="!isSidebarCollapsed">STUDENT</span>
            <span x-show="isSidebarCollapsed" class="block w-8 h-[1px] bg-gray-300 dark:bg-gray-600 mx-auto"></span>
        </p>
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('dashboard') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
           :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
           :title="isSidebarCollapsed ? 'Dashboard' : ''">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Dashboard</span>
        </a>
        
        <!-- Resume Inspection (Standalone) -->
        <a href="{{ route('student.resume.index') }}" 
           class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('student.resume.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
           :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
           :title="isSidebarCollapsed ? 'Resume Inspection' : ''">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Resume Inspection</span>
        </a>
        
        <!-- Placement Tracking -->
        <a href="{{ route('student.placement.index') }}" 
           class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('student.placement.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
           :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
           :title="isSidebarCollapsed ? 'Placement Tracking' : ''">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Placement Tracking</span>
        </a>
        
        <!-- My Courses (Collapsible Parent) -->
        <div x-data="{ coursesMenuOpen: {{ request()->routeIs('student.fyp.*') || request()->routeIs('student.ppe.*') || request()->routeIs('student.osh.*') || request()->routeIs('student.ip.*') || request()->routeIs('student.li.*') ? 'true' : 'false' }} }">
            <button @click="coursesMenuOpen = !coursesMenuOpen" 
                    class="flex items-center w-full gap-1 rounded-lg transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-h-[44px]"
                    :class="isSidebarCollapsed ? 'justify-center px-0' : 'justify-between px-2'"
                    :title="isSidebarCollapsed ? 'My Courses' : ''">
                <div class="flex items-center gap-1">
                    <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">My Courses</span>
                </div>
                <svg x-show="sidebarTextVisible" x-transition :class="{'rotate-180': coursesMenuOpen}" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Submenu Items -->
            <div x-show="coursesMenuOpen && sidebarTextVisible" x-transition class="ml-6 mt-2 space-y-1">
                <a href="{{ Route::has('student.fyp.overview') ? route('student.fyp.overview') : '#' }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 ease-in-out {{ request()->routeIs('student.fyp.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="text-sm">FYP</span>
                </a>
                <a href="{{ route('academic.fyp.project-proposal.edit') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 ease-in-out {{ request()->routeIs('academic.fyp.project-proposal.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="text-sm">FYP Project Proposal</span>
                </a>
                <a href="{{ Route::has('student.ip.overview') ? route('student.ip.overview') : '#' }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 ease-in-out {{ request()->routeIs('student.ip.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="text-sm">IP</span>
                </a>
                <a href="{{ Route::has('student.osh.overview') ? route('student.osh.overview') : '#' }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 ease-in-out {{ request()->routeIs('student.osh.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="text-sm">OSH</span>
                </a>
                <a href="{{ Route::has('student.ppe.overview') ? route('student.ppe.overview') : '#' }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 ease-in-out {{ request()->routeIs('student.ppe.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="text-sm">PPE</span>
                </a>
                <a href="{{ Route::has('student.li.overview') ? route('student.li.overview') : '#' }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-300 ease-in-out {{ request()->routeIs('student.li.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span class="text-sm">Industrial Training</span>
                </a>
            </div>
        </div>
        
        <!-- Profile -->
        <a href="{{ route('students.profile.show') }}" 
           class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('students.profile.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
           :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
           :title="isSidebarCollapsed ? 'Profile' : ''">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Profile</span>
        </a>
    </div>
    @endif

    <!-- Group #3: INDUSTRY COACH -->
    @if($isIc)
    <div class="mt-6 pt-6 border-t border-gray-200">
        <p x-show="sidebarTextVisible" x-transition class="px-3 text-xs uppercase tracking-wider text-gray-500 mb-3 font-semibold" :class="isSidebarCollapsed ? 'text-center px-0' : ''">
            <span x-show="!isSidebarCollapsed">INDUSTRY COACH</span>
            <span x-show="isSidebarCollapsed" class="block w-8 h-[1px] bg-gray-300 mx-auto"></span>
        </p>
        <a href="{{ route('industry.students.index') }}" 
           class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('industry.students.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
           :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
           :title="isSidebarCollapsed ? 'My Students' : ''">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">My Students</span>
        </a>
    </div>
    @endif


    <!-- Group #4: PROFILE SETTINGS (Hidden for Students - Profile is in STUDENT section) -->
    @if(!$isStudent)
    <div class="mt-6 pt-6 border-t border-gray-200">
        <p x-show="sidebarTextVisible" x-transition class="px-3 text-xs uppercase tracking-wider text-gray-500 mb-3 font-semibold" :class="isSidebarCollapsed ? 'text-center px-0' : ''">
            <span x-show="!isSidebarCollapsed">PROFILE SETTINGS</span>
            <span x-show="isSidebarCollapsed" class="block w-8 h-[1px] bg-gray-300 mx-auto"></span>
        </p>
        <a href="{{ route('students.profile.show') }}" 
           class="flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out min-h-[44px] {{ request()->routeIs('students.profile.*') ? 'bg-[#E6F4EF] dark:bg-gray-700/50 text-[#003A6C] dark:text-white border-l-[3px] border-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
           :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
           :title="isSidebarCollapsed ? 'My Profile' : ''">
            <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">My Profile</span>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-h-[44px]"
                    :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
                    :title="isSidebarCollapsed ? 'Logout' : ''">
                <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>
    @else
    <!-- Logout for Students (separate from PROFILE SETTINGS) -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center gap-1 rounded-lg transition-all duration-300 ease-in-out text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 min-h-[44px]"
                    :class="isSidebarCollapsed ? 'justify-center px-0' : 'px-2'"
                    :title="isSidebarCollapsed ? 'Logout' : ''">
                <div class="w-9 h-9 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <span x-show="sidebarTextVisible" x-transition class="text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>
    @endif
</nav>

