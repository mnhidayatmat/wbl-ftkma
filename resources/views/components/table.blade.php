@props(['columns', 'sortable' => true])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-umpsa-teal dark:bg-umpsa-teal">
            <tr>
                @foreach($columns as $column)
                    <th 
                        scope="col" 
                        class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider {{ $column['sortable'] ?? false ? 'cursor-pointer hover:bg-umpsa-royal-blue transition-colors' : '' }}"
                        @if($column['sortable'] ?? false)
                            onclick="window.location.href='{{ $column['sortUrl'] ?? '#' }}'"
                        @endif
                    >
                        <div class="flex items-center space-x-1">
                            <span>{{ $column['label'] }}</span>
                            @if(($column['sortable'] ?? false) && isset($column['sortIcon']))
                                <span class="text-white/80">{!! $column['sortIcon'] !!}</span>
                            @endif
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>

