@php
use Illuminate\Support\Facades\Route;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#003A6C">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="WBL System">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-72x72.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icons/icon-72x72.png">
    <link rel="shortcut icon" href="/images/icons/icon-72x72.png">

    <title>{{ config('app.name', 'WBL Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Set default theme to light mode (override system preference)
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                // Default: light mode (even if system prefers dark)
                document.documentElement.classList.remove('dark');
                if (!savedTheme) {
                    localStorage.setItem('theme', 'light');
                }
            }
        })();
    </script>
    <style>
        /* ============================================
           GLOBAL LAYOUT SYSTEM - CONSISTENT PADDING
           ============================================ */
        :root {
            /* Desktop (≥1024px): 24px side padding */
            --app-padding-x: 24px;
            --app-padding-y: 24px;
        }
        
        /* Tablet (768px - 1023px): 20px side padding */
        @media (max-width: 1023px) {
            :root {
                --app-padding-x: 20px;
                --app-padding-y: 20px;
            }
        }
        
        /* Mobile (<768px): 16px side padding */
        @media (max-width: 767px) {
            :root {
                --app-padding-x: 16px;
                --app-padding-y: 16px;
            }
        }
        
        /* Global App Container - Applied to main content */
        .app-content-container {
            padding-left: var(--app-padding-x);
            padding-right: var(--app-padding-x);
            padding-top: var(--app-padding-y);
            padding-bottom: var(--app-padding-y);
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }
        
        /* Override nested containers to prevent double padding */
        .app-content-container .max-w-7xl,
        .app-content-container .max-w-6xl,
        .app-content-container .max-w-5xl,
        .app-content-container .max-w-4xl {
            max-width: 100%;
            padding-left: 0;
            padding-right: 0;
            margin-left: 0;
            margin-right: 0;
        }
        
        /* Remove extra horizontal padding from nested page wrappers */
        .app-content-container > div > .px-4,
        .app-content-container > div > .px-6,
        .app-content-container > div > .px-8 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Global overflow prevention */
        html, body {
            overflow-x: hidden;
            width: 100%;
            max-width: 100%;
        }

        /* Ensure touch targets are at least 44px for mobile */
        @media (max-width: 768px) {
            button, a {
                min-height: 44px;
            }
        }

        /* Sidebar Overlay base - controlled by media queries */
        #sidebar-overlay {
            display: none;
        }

        /* Alpine.js cloak - hide until Alpine initializes */
        [x-cloak] {
            display: none !important;
        }

        /* Sidebar base styles */
        #sidebar {
            z-index: 70;
        }

        /* Main content wrapper - flex-based layout */
        .main-content-wrapper {
            flex: 1;
            min-width: 0; /* CRITICAL: prevents flex overflow */
            overflow-x: hidden;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Prevent body scroll when sidebar is open on mobile/tablet */
        body.sidebar-open {
            overflow: hidden !important;
            position: fixed;
            width: 100%;
            height: 100%;
        }

        /* Ensure main content doesn't overflow */
        main {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        /* ============================================
           DESKTOP (≥1024px) - Sidebar visible, static
           ============================================ */
        @media (min-width: 1024px) {
            #sidebar {
                position: relative !important;
                transform: none !important;
                left: auto !important;
                top: auto !important;
                height: auto !important;
                min-height: 100vh;
                z-index: auto !important;
                transition: width 0.3s ease-in-out;
            }
            
            #sidebar-overlay {
                display: none !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }
            
            .main-content-wrapper {
                margin-left: 0 !important;
            }
        }

        /* ============================================
           MOBILE & TABLET (<1024px) - Sidebar as off-canvas overlay
           ============================================ */
        @media (max-width: 1023px) {
            /* Sidebar - Fixed position, hidden by default */
            #sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                width: 260px !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 1001 !important;
                will-change: transform;
                overflow-y: auto;
            }
            
            /* When sidebar is open - slide in */
            #sidebar.sidebar-mobile-open {
                transform: translateX(0) !important;
            }
            
            /* Main content - full width, no margin */
            .main-content-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
            
            /* Overlay - show element but hidden state */
            #sidebar-overlay {
                display: block !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                background: rgba(0, 0, 0, 0.5) !important;
                z-index: 1000 !important;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
                pointer-events: none;
            }
            
            /* Overlay when active */
            #sidebar-overlay.overlay-visible {
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-[#F5F7FA] dark:bg-gray-900 transition-colors duration-200" 
      x-data="sidebarController()"
      x-init="init()"
      :class="{ 'sidebar-open': sidebarOpen && !isDesktop() }">
    <div class="min-h-screen flex">
        <!-- Sidebar Overlay (Mobile & Tablet only) -->
        <div 
            id="sidebar-overlay" 
            @click="closeSidebar()"
            @keydown.escape.window="closeSidebar()"
            :class="{ 'overlay-visible': sidebarOpen && !isDesktop() }"
            class="lg:hidden"
        ></div>

        <!-- Sidebar - Materio Style -->
        <aside 
            id="sidebar" 
            class="bg-white dark:bg-gray-800 border-r border-[#E5E7EB] dark:border-gray-700 min-h-screen flex-shrink-0"
            :class="{
                'w-64': isDesktop() && sidebarExpanded,
                'w-20': isDesktop() && !sidebarExpanded,
                'sidebar-mobile-open': sidebarOpen && !isDesktop()
            }"
            @click.stop
        >
            <div class="p-4 sm:p-5 flex items-center justify-between border-b border-[#E5E7EB] dark:border-gray-700">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <!-- UMPSA Logo -->
                    <img 
                        src="{{ asset('images/logos/UMPSA_logo.png') }}" 
                        alt="Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)"
                        class="h-8 w-auto object-contain flex-shrink-0"
                        style="max-height: 32px; width: auto; object-fit: contain; vertical-align: middle;"
                        title="Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)"
                    >
                    <h1 x-show="(isDesktop() && sidebarExpanded) || (!isDesktop() && sidebarOpen)" 
                        class="text-lg md:text-xl font-bold text-[#003A6C] dark:text-white transition-opacity duration-300 leading-tight whitespace-nowrap">
                        WBL Management
                    </h1>
                </div>
                 <!-- Collapse/Expand Toggle Button (Desktop only - for mini sidebar) -->
                 <button 
                     @click="toggleSidebar()" 
                     class="hidden lg:flex text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg p-2 transition-colors min-h-[44px] min-w-[44px] items-center justify-center"
                     :title="sidebarExpanded ? 'Collapse Sidebar' : 'Expand Sidebar'"
                 >
                     <!-- Collapse/Expand Icon -->
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                     </svg>
                 </button>
                 <!-- Close Button (Mobile & Tablet only - inside sidebar) -->
                 <button 
                     @click="closeSidebar()" 
                     class="lg:hidden text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg p-2 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center"
                     title="Close Sidebar"
                 >
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                     </svg>
                 </button>
            </div>
            @include('layouts.app_sidebar_new')
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-0 main-content-wrapper">
            <!-- Topbar - Light Materio Style -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-[#E5E7EB] dark:border-gray-700 sticky top-0 z-50">
                <div class="px-4 sm:px-6 py-3 md:py-4 flex justify-between items-center">
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <!-- Hamburger Menu Button (Mobile & Tablet - hidden on desktop lg:) -->
                        <button 
                            @click.stop="toggleMobile()" 
                            type="button"
                            class="lg:hidden text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center relative z-50"
                            aria-label="Toggle sidebar"
                            x-bind:aria-expanded="sidebarOpen"
                        >
                            <!-- Hamburger Icon -->
                            <svg x-show="!sidebarOpen" 
                                 x-cloak
                                 class="w-6 h-6" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <!-- Close Icon -->
                            <svg x-show="sidebarOpen" 
                                 x-cloak
                                 class="w-6 h-6" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <!-- UMPSA Logo (Mobile) -->
                        <img 
                            src="{{ asset('images/logos/UMPSA_logo.png') }}" 
                            alt="Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)"
                            class="h-7 w-auto object-contain flex-shrink-0 lg:hidden"
                            style="max-height: 28px; width: auto; object-fit: contain; vertical-align: middle;"
                            title="Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)"
                        >
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200 truncate">@yield('title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-4" x-data="{ userMenuOpen: false }">
                        <!-- Search Bar (Materio Style) -->
                        <div class="hidden md:flex items-center" x-data="{ 
                            query: '', 
                            results: [], 
                            showResults: false,
                            loading: false,
                            searchTimeout: null,
                            async performSearch() {
                                if (this.query.length < 2) {
                                    this.results = [];
                                    this.showResults = false;
                                    return;
                                }
                                
                                this.loading = true;
                                clearTimeout(this.searchTimeout);
                                
                                this.searchTimeout = setTimeout(async () => {
                                    try {
                                        const response = await fetch(`{{ route('search') }}?q=${encodeURIComponent(this.query)}`);
                                        const data = await response.json();
                                        this.results = data.results || [];
                                        this.showResults = this.results.length > 0;
                                    } catch (error) {
                                        console.error('Search error:', error);
                                        this.results = [];
                                        this.showResults = false;
                                    } finally {
                                        this.loading = false;
                                    }
                                }, 300);
                            }
                        }" @click.away="showResults = false">
                            <div class="relative w-64">
                                <input 
                                    type="text" 
                                    x-model="query"
                                    @input="performSearch()"
                                    @focus="if (results.length > 0) showResults = true"
                                    placeholder="Search (⌘K)" 
                                    class="pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0084C5] focus:border-transparent w-full bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                
                                <!-- Loading Spinner -->
                                <div x-show="loading" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                    <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                
                                <!-- Search Results Dropdown -->
                                <div x-show="showResults && results.length > 0" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto">
                                    <template x-for="(result, index) in results" :key="result.id + '-' + result.type + '-' + index">
                                        <a :href="result.url" 
                                           @click.stop="showResults = false"
                                           class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0 cursor-pointer block no-underline">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#E6F4EF] dark:bg-gray-700 flex items-center justify-center">
                                                <svg x-show="result.icon === 'user'" class="w-4 h-4 text-[#00A86B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <svg x-show="result.icon === 'building'" class="w-4 h-4 text-[#00A86B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <svg x-show="result.icon === 'user-circle'" class="w-4 h-4 text-[#00A86B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" x-text="result.title"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="result.subtitle"></p>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </template>
                                    <div x-show="results.length === 0 && query.length >= 2 && !loading" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                        No results found
                                    </div>
                                </div>
                            </div>
                        </div>
                        @auth
                        <!-- Role Switcher (Only show if user has multiple roles) -->
                        @php
                            $user = Auth::user();
                            $userRoles = $user->roles()->get();
                            $activeRole = $user->getActiveRole();
                            $activeRoleDisplay = $userRoles->firstWhere('name', $activeRole)?->display_name ?? ($activeRole ? ucfirst($activeRole) : 'Select Role');
                        @endphp
                        @if($userRoles->count() > 1)
                        <div class="hidden md:block relative" x-data="{ roleMenuOpen: false }">
                            <button 
                                @click="roleMenuOpen = !roleMenuOpen" 
                                class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors min-h-[44px]"
                            >
                                <span class="text-xs text-gray-500 dark:text-gray-400">Acting as:</span>
                                <span class="text-[#00A86B] font-semibold">{{ $activeRoleDisplay }}</span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div 
                                x-show="roleMenuOpen" 
                                @click.away="roleMenuOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50 border border-gray-200 dark:border-gray-700"
                            >
                                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Switch Role</p>
                                </div>
                                @foreach($userRoles as $role)
                                <form method="POST" action="{{ route('role.switch') }}" class="block">
                                    @csrf
                                    <input type="hidden" name="role" value="{{ $role->name }}">
                                    <button 
                                        type="submit"
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-between {{ $activeRole === $role->name ? 'bg-[#E6F4EF] dark:bg-gray-700 text-[#00A86B] font-medium' : 'text-gray-700 dark:text-gray-300' }}"
                                    >
                                        <span>{{ $role->display_name }}</span>
                                        @if($activeRole === $role->name)
                                        <svg class="w-4 h-4 text-[#00A86B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        @endif
                                    </button>
                                </form>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @endauth
                        <!-- Dark Mode Toggle -->
                        <button onclick="toggleTheme()" data-theme-toggle class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors duration-200 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Toggle theme">
                            <!-- Sun Icon (Light Mode) -->
                            <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <!-- Moon Icon (Dark Mode) -->
                            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                        @auth
                            <!-- Desktop: Show user info and logout -->
                            <div class="hidden md:flex items-center space-x-4">
                                <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 hover:text-gray-800 transition-colors">
                                        <div class="w-8 h-8 rounded-full bg-[#00A86B] flex items-center justify-center text-white text-sm font-medium">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    </button>
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50 border border-gray-200 dark:border-gray-700">
                                        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                @php
                                                    $user = Auth::user();
                                                    $activeRole = $user->getActiveRole();
                                                    $activeRoleDisplay = $user->roles()->firstWhere('name', $activeRole)?->display_name ?? ($activeRole ? ucfirst($activeRole) : 'No Role');
                                                @endphp
                                                Acting as: <span class="text-[#00A86B] font-semibold">{{ $activeRoleDisplay }}</span>
                                            </p>
                                        </div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Mobile: Dropdown menu -->
                            <div class="md:hidden relative">
                                <button @click="userMenuOpen = !userMenuOpen" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </button>
                                <div x-show="userMenuOpen" 
                                     @click.away="userMenuOpen = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200">
                                    <div class="px-4 py-2 border-b border-gray-200">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center space-x-2 sm:space-x-4">
                                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-[#003A6C] transition-colors min-h-[44px] px-3 flex items-center">Login</a>
                                <a href="{{ route('register') }}" class="text-sm text-white bg-[#00A86B] hover:bg-[#008F5C] rounded-lg px-4 py-2 transition-colors min-h-[44px] flex items-center">Register</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Content - Light Grey Background with Global Padding -->
            <main class="flex-1 bg-[#F5F7FA] dark:bg-gray-900 transition-colors duration-200 overflow-x-hidden max-w-full app-content-container">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 dark:border-green-400 text-green-800 dark:text-green-300 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-400 text-red-800 dark:text-red-300 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 bg-umpsa-yellow dark:bg-umpsa-yellow/80 border-l-4 border-umpsa-deep-blue dark:border-umpsa-deep-blue text-umpsa-deep-blue dark:text-umpsa-deep-blue px-4 py-3 rounded">
                        {{ session('warning') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Robust Sidebar Controller - Single Source of Truth
        function sidebarController() {
            return {
                // Desktop: sidebar expanded state (true = expanded, false = mini)
                sidebarExpanded: true,
                // Mobile/Tablet: sidebar open state
                sidebarOpen: false,
                // Track if we're on mobile/tablet
                isMobile: false,
                
                // Check if we're on mobile (< 768px)
                isMobileBreakpoint() {
                    return window.innerWidth < 768;
                },
                
                // Check if we're on tablet (768px - 1023px)
                isTabletBreakpoint() {
                    return window.innerWidth >= 768 && window.innerWidth < 1024;
                },
                
                // Check if we're on desktop (>= 1024px)
                isDesktop() {
                    return window.innerWidth >= 1024;
                },
                
                // Toggle sidebar (different behavior per breakpoint)
                toggleSidebar() {
                    if (this.isDesktop()) {
                        // Desktop: toggle expanded/mini
                        this.sidebarExpanded = !this.sidebarExpanded;
                    } else {
                        // Mobile/Tablet: toggle open/close
                        this.toggleMobile();
                    }
                },
                
                // Toggle mobile sidebar
                toggleMobile() {
                    if (this.isDesktop()) return;
                    this.sidebarOpen = !this.sidebarOpen;
                    this.updateBodyScroll();
                },
                
                // Close sidebar (mobile/tablet only)
                closeSidebar() {
                    this.sidebarOpen = false;
                    this.updateBodyScroll();
                },
                
                // Update body scroll state
                updateBodyScroll() {
                    if (this.sidebarOpen && !this.isDesktop()) {
                        document.body.classList.add('sidebar-open');
                        document.body.style.overflow = 'hidden';
                        document.body.style.position = 'fixed';
                        document.body.style.width = '100%';
                    } else {
                        document.body.classList.remove('sidebar-open');
                        document.body.style.overflow = '';
                        document.body.style.position = '';
                        document.body.style.width = '';
                        document.body.style.height = '';
                    }
                },
                
                // Initialize on mount
                init() {
                    // Use matchMedia for consistent breakpoint detection
                    const mq = window.matchMedia('(max-width: 1023px)');
                    
                    const applyBreakpoint = () => {
                        this.isMobile = mq.matches;
                        
                        if (this.isMobile) {
                            // Mobile/Tablet: ensure sidebar is closed
                            this.sidebarOpen = false;
                            this.updateBodyScroll();
                        } else {
                            // Desktop: clear mobile state
                            this.sidebarOpen = false;
                            this.updateBodyScroll();
                        }
                    };
                    
                    // Apply on init
                    applyBreakpoint();
                    
                    // Listen for breakpoint changes
                    mq.addEventListener('change', applyBreakpoint);
                    
                    // Close sidebar on navigation (prevents stuck states)
                    window.addEventListener('popstate', () => {
                        if (!this.isDesktop()) {
                            this.closeSidebar();
                        }
                    });
                    
                    // Close on Escape key
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.sidebarOpen && !this.isDesktop()) {
                            this.closeSidebar();
                        }
                    });
                }
            };
        }

        // Dark Mode Toggle Function
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            if (newTheme === 'dark') {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        }

        // Keyboard shortcut for search (⌘K or Ctrl+K)
        document.addEventListener('keydown', function(event) {
            // Check for Cmd+K (Mac) or Ctrl+K (Windows/Linux)
            if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
                event.preventDefault();
                const searchInput = document.querySelector('input[placeholder*="Search"]');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });
    </script>
    @stack('scripts')

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered:', registration.scope);
                    })
                    .catch((error) => {
                        console.log('SW registration failed:', error);
                    });
            });
        }
    </script>

    <!-- PWA Install Prompt -->
    <div id="pwa-install-prompt" class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg p-4 z-[9999] transform translate-y-full transition-transform duration-300 ease-out" style="display: none;">
        <div class="max-w-lg mx-auto flex items-center gap-4">
            <img src="/images/icons/icon-72x72.png" alt="WBL App" class="w-12 h-12 rounded-xl shadow">
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Install WBL System</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" id="pwa-install-description">Add to your home screen for quick access</p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button id="pwa-install-dismiss" class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Later
                </button>
                <button id="pwa-install-button" class="px-4 py-2 text-sm font-medium text-white bg-[#003A6C] hover:bg-[#002a4f] rounded-lg transition-colors">
                    Install
                </button>
            </div>
        </div>
    </div>

    <!-- iOS Install Instructions Modal -->
    <div id="ios-install-modal" class="fixed inset-0 bg-black/50 z-[10000] flex items-end justify-center" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-t-2xl w-full max-w-lg p-6 transform transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Install WBL System</h3>
                <button id="ios-modal-close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#003A6C] text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                    <p class="text-gray-700 dark:text-gray-300">Tap the <span class="inline-flex items-center"><svg class="w-5 h-5 mx-1 text-[#007AFF]" fill="currentColor" viewBox="0 0 24 24"><path d="M16 5l-1.42 1.42-1.59-1.59V16h-1.98V4.83L9.42 6.42 8 5l4-4 4 4zm4 5v11c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2V10c0-1.1.9-2 2-2h3v2H6v11h12V10h-3V8h3c1.1 0 2 .9 2 2z"/></svg></span> Share button</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#003A6C] text-white rounded-full flex items-center justify-center text-sm font-medium">2</div>
                    <p class="text-gray-700 dark:text-gray-300">Scroll down and tap <strong>"Add to Home Screen"</strong></p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#003A6C] text-white rounded-full flex items-center justify-center text-sm font-medium">3</div>
                    <p class="text-gray-700 dark:text-gray-300">Tap <strong>"Add"</strong> to install</p>
                </div>
            </div>
            <button id="ios-modal-ok" class="w-full mt-6 px-4 py-3 text-sm font-medium text-white bg-[#003A6C] hover:bg-[#002a4f] rounded-lg transition-colors">
                Got it!
            </button>
        </div>
    </div>

    <script>
        (function() {
            // Check if running as installed PWA (standalone mode)
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
            if (isStandalone) {
                // User is using the installed app - no need to prompt
                return;
            }

            const promptEl = document.getElementById('pwa-install-prompt');
            const installBtn = document.getElementById('pwa-install-button');
            const dismissBtn = document.getElementById('pwa-install-dismiss');
            const description = document.getElementById('pwa-install-description');
            const iosModal = document.getElementById('ios-install-modal');
            const iosModalClose = document.getElementById('ios-modal-close');
            const iosModalOk = document.getElementById('ios-modal-ok');

            let deferredPrompt = null;

            // Detect iOS
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            const isSafari = /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent);

            // Detect mobile
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            // Check if user dismissed prompt recently (within 7 days)
            function wasRecentlyDismissed() {
                const dismissedAt = localStorage.getItem('pwa-prompt-dismissed');
                if (dismissedAt && (Date.now() - parseInt(dismissedAt)) < 7 * 24 * 60 * 60 * 1000) {
                    return true;
                }
                return false;
            }

            function showPrompt() {
                if (!promptEl || wasRecentlyDismissed()) return;
                promptEl.style.display = 'block';
                setTimeout(() => {
                    promptEl.classList.remove('translate-y-full');
                }, 10);
            }

            function hidePrompt() {
                if (!promptEl) return;
                promptEl.classList.add('translate-y-full');
                setTimeout(() => {
                    promptEl.style.display = 'none';
                }, 300);
            }

            function showIOSModal() {
                if (!iosModal) return;
                iosModal.style.display = 'flex';
            }

            function hideIOSModal() {
                if (!iosModal) return;
                iosModal.style.display = 'none';
            }

            // Listen for beforeinstallprompt (Android/Chrome)
            // This event ONLY fires when the app is NOT installed
            // If user uninstalls and visits again, this event will fire again
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;

                // Clear any previous installed flag since browser says it's installable
                // This handles the case where user uninstalled the app
                localStorage.removeItem('pwa-installed');

                // Show prompt after short delay
                setTimeout(showPrompt, 2000);
            });

            // For iOS Safari - check if should show prompt
            // iOS doesn't have beforeinstallprompt, so we use a different approach
            if (isIOS && isSafari && isMobile) {
                // Check if iOS user marked as "installed" (followed instructions)
                const iosInstalled = localStorage.getItem('pwa-ios-installed');

                if (!iosInstalled && !wasRecentlyDismissed()) {
                    description.textContent = 'Tap Install to see how to add this app';
                    setTimeout(showPrompt, 3000);
                }
            }

            // Install button click
            if (installBtn) {
                installBtn.addEventListener('click', async () => {
                    if (deferredPrompt) {
                        // Android/Chrome - trigger native prompt
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        if (outcome === 'accepted') {
                            localStorage.setItem('pwa-installed', 'true');
                        }
                        deferredPrompt = null;
                        hidePrompt();
                    } else if (isIOS) {
                        // iOS - show instructions modal
                        hidePrompt();
                        showIOSModal();
                    }
                });
            }

            // Dismiss button click
            if (dismissBtn) {
                dismissBtn.addEventListener('click', () => {
                    localStorage.setItem('pwa-prompt-dismissed', Date.now().toString());
                    hidePrompt();
                });
            }

            // iOS modal close
            if (iosModalClose) {
                iosModalClose.addEventListener('click', hideIOSModal);
            }
            if (iosModalOk) {
                iosModalOk.addEventListener('click', () => {
                    // Mark iOS as "installed" when user acknowledges instructions
                    localStorage.setItem('pwa-ios-installed', 'true');
                    localStorage.setItem('pwa-ios-installed-at', Date.now().toString());
                    hideIOSModal();
                });
            }

            // Close iOS modal on backdrop click
            if (iosModal) {
                iosModal.addEventListener('click', (e) => {
                    if (e.target === iosModal) {
                        localStorage.setItem('pwa-prompt-dismissed', Date.now().toString());
                        hideIOSModal();
                    }
                });
            }

            // Listen for successful install
            window.addEventListener('appinstalled', () => {
                localStorage.setItem('pwa-installed', 'true');
                hidePrompt();
                console.log('PWA installed successfully');
            });

            // Check periodically if iOS app was uninstalled
            // Reset iOS installed flag after 30 days to re-prompt if they deleted it
            if (isIOS) {
                const iosInstalledAt = localStorage.getItem('pwa-ios-installed-at');
                if (iosInstalledAt && (Date.now() - parseInt(iosInstalledAt)) > 30 * 24 * 60 * 60 * 1000) {
                    localStorage.removeItem('pwa-ios-installed');
                    localStorage.removeItem('pwa-ios-installed-at');
                }
            }
        })();
    </script>
</body>
</html>
