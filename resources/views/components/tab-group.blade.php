@props(['tabs', 'activeTab' => null, 'baseUrl' => '#'])

<div class="mb-6">
    <!-- Desktop Tabs -->
    <div class="hidden md:flex space-x-2 border-b border-gray-200 dark:border-gray-700">
        @foreach($tabs as $tab)
            <a 
                href="{{ $baseUrl }}{{ $tab['value'] ? '?group=' . $tab['value'] : '' }}"
                class="px-6 py-3 text-sm font-semibold rounded-t-lg transition-all duration-200 {{ 
                    $activeTab == $tab['value'] 
                        ? 'bg-umpsa-primary text-white border-b-2 border-umpsa-primary' 
                        : 'text-umpsa-primary hover:bg-umpsa-primary/10 dark:hover:bg-umpsa-primary/20 border-b-2 border-transparent hover:border-umpsa-primary/30' 
                }}"
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    <!-- Mobile Tabs (Horizontal Scroll) -->
    <div class="md:hidden overflow-x-auto pb-2 -mx-4 px-4">
        <div class="flex space-x-2 min-w-max">
            @foreach($tabs as $tab)
                <a 
                    href="{{ $baseUrl }}{{ $tab['value'] ? '?group=' . $tab['value'] : '' }}"
                    class="px-4 py-2 text-sm font-semibold whitespace-nowrap rounded-lg transition-all duration-200 {{ 
                        $activeTab == $tab['value'] 
                            ? 'bg-umpsa-primary text-white shadow-md' 
                            : 'bg-white dark:bg-gray-800 text-umpsa-primary border border-gray-200 dark:border-gray-700 hover:bg-umpsa-primary/10 dark:hover:bg-umpsa-primary/20' 
                    }}"
                >
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>

