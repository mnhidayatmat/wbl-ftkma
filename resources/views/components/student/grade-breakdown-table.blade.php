@props(['marks', 'columns' => []])

@php
    // Default columns if not provided
    if (empty($columns)) {
        $columns = [
            ['key' => 'name', 'label' => 'Assessment'],
            ['key' => 'score', 'label' => 'Score'],
            ['key' => 'max', 'label' => 'Max'],
            ['key' => 'percentage', 'label' => 'Percentage'],
        ];
    }
@endphp

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                @foreach($columns as $column)
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $column['label'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($marks as $mark)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    @foreach($columns as $column)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            @if($column['key'] === 'percentage' && isset($mark->score) && isset($mark->max))
                                @php
                                    $percentage = $mark->max > 0 ? ($mark->score / $mark->max) * 100 : 0;
                                @endphp
                                <div class="flex items-center">
                                    <span class="mr-2">{{ number_format($percentage, 1) }}%</span>
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $percentage >= 70 ? 'bg-green-500' : ($percentage >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                             style="width: {{ min($percentage, 100) }}%"></div>
                                    </div>
                                </div>
                            @elseif($column['key'] === 'status')
                                @php
                                    $status = $mark->{$column['key']} ?? 'pending';
                                    $statusColors = [
                                        'graded' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            @else
                                {{ $mark->{$column['key']} ?? '-' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        No marks available yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
