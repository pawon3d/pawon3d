@props([
    'items' => [],
    'emptyMessage' => 'Tidak ada data.',
    'columns' => [],
    'summary' => [
        'from' => 0,
        'to' => 0,
        'total' => 0,
        'pages' => 1,
    ],
    'currentPage' => 1,
    'previousMethod' => 'previousPage',
    'nextMethod' => 'nextPage',
    'headerBg' => 'bg-[#3f4e4f]',
    'headerText' => 'text-[#f8f4e1]',
    'bodyBg' => '#fafafa',
    'bodyText' => '#666666',
])

@php
    // Convert hex colors to inline styles if provided
    $headerBgStyle = str_starts_with($headerBg, '#') ? "background-color: {$headerBg};" : '';
    $headerTextStyle = str_starts_with($headerText, '#') ? "color: {$headerText};" : '';
    $bodyBgStyle = str_starts_with($bodyBg, '#') ? "background-color: {$bodyBg};" : '';
    $bodyTextStyle = str_starts_with($bodyText, '#') ? "color: {$bodyText};" : '';

    // Use as classes if not hex colors
    $headerBgClass = str_starts_with($headerBg, '#') ? '' : $headerBg;
    $headerTextClass = str_starts_with($headerText, '#') ? '' : $headerText;
    $bodyBgClass = str_starts_with($bodyBg, '#') ? '' : $bodyBg;
    $bodyTextClass = str_starts_with($bodyText, '#') ? '' : $bodyText;

    // Detect if using Laravel paginator
    $isPaginator = is_object($items) && method_exists($items, 'currentPage');

    // Auto-detect pagination info
    if ($isPaginator) {
        $currentPage = $items->currentPage();
        $summary = [
            'from' => $items->firstItem() ?? 0,
            'to' => $items->lastItem() ?? 0,
            'total' => $items->total() ?? 0,
            'pages' => $items->lastPage() ?? 1,
        ];
        // Use the paginator's page name for navigation methods
    $pageName = $items->getPageName();
    $previousMethod = 'previousPage';
    $nextMethod = 'nextPage';
}

$hasItems = isset($summary['total']) && $summary['total'] > 0;
$isFirstPage = $currentPage === 1 || !$hasItems;
$isLastPage = $currentPage >= ($summary['pages'] ?? 1) || !$hasItems;
@endphp

<div class="space-y-5">
    <div class="border rounded-[15px] overflow-hidden max-h-[180px]">
        <table class="min-w-full">
            <thead class="{{ $headerBgClass }}" style="{{ $headerBgStyle }}">
                <tr>
                    @foreach ($columns as $column)
                        <th class="px-6 py-5 text-left text-sm font-bold {{ $headerTextClass }}"
                            style="{{ $headerTextStyle }}">
                            @if (isset($column['sortable']) && $column['sortable'] && isset($column['sort-method']))
                                <button type="button" wire:click="{{ $column['sort-method'] }}"
                                    class="flex items-center gap-1">
                                    <span>{{ $column['label'] }}</span>
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 14 14">
                                        <path d="M7 2L11 6H3L7 2Z" opacity="0.5" />
                                        <path d="M7 12L3 8H11L7 12Z" opacity="0.5" />
                                    </svg>
                                </button>
                            @else
                                <div class="flex items-center gap-1">
                                    {{ $column['label'] }}
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 14 14">
                                        <path d="M7 2L11 6H3L7 2Z" opacity="0.5" />
                                        <path d="M7 12L3 8H11L7 12Z" opacity="0.5" />
                                    </svg>
                                </div>
                            @endif
                        </th>
                    @endforeach

                    {{ $actionColumn ?? '' }}
                </tr>
            </thead>
            <tbody class="{{ $bodyBgClass }} divide-y divide-[#d4d4d4]" style="{{ $bodyBgStyle }}">
                @if (count($items) > 0)
                    {{ $slot }}
                @else
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}"
                            class="px-6 py-5 text-center {{ $bodyTextClass }} font-medium text-sm"
                            style="{{ $bodyTextStyle }}">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="flex justify-between items-center">
        <p class="text-sm font-medium {{ $bodyTextClass }} opacity-70" style="{{ $bodyTextStyle }}">
            Menampilkan {{ $summary['from'] ?? 0 }} hingga
            {{ $summary['to'] ?? 0 }} dari {{ $summary['total'] ?? 0 }} baris
            data
        </p>
        <div class="flex gap-2.5 items-center">
            @if ($isPaginator)
                {{-- Use Livewire's built-in pagination for paginator objects --}}
                <button type="button" wire:click="previousPage('{{ $pageName }}')" @disabled($isFirstPage)
                    class="min-w-[30px] px-2.5 py-1 bg-[#fafafa] border border-[#666666] rounded-[5px] {{ $isFirstPage ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <svg class="w-4 h-4 text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div class="min-w-[30px] px-2.5 py-1 bg-[#666666] rounded-[5px] text-center">
                    <span class="text-sm font-medium text-[#fafafa]">{{ $currentPage }}</span>
                </div>
                <button type="button" wire:click="nextPage('{{ $pageName }}')" @disabled($isLastPage)
                    class="min-w-[30px] px-2.5 py-1 bg-[#fafafa] border border-[#666666] rounded-[5px] {{ $isLastPage ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <svg class="w-4 h-4 text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            @else
                {{-- Use custom methods for manual pagination --}}
                <button type="button" wire:click="{{ $previousMethod }}" @disabled($isFirstPage)
                    class="min-w-[30px] px-2.5 py-1 bg-[#fafafa] border border-[#666666] rounded-[5px] {{ $isFirstPage ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <svg class="w-4 h-4 text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div class="min-w-[30px] px-2.5 py-1 bg-[#666666] rounded-[5px] text-center">
                    <span class="text-sm font-medium text-[#fafafa]">{{ $currentPage }}</span>
                </div>
                <button type="button" wire:click="{{ $nextMethod }}" @disabled($isLastPage)
                    class="min-w-[30px] px-2.5 py-1 bg-[#fafafa] border border-[#666666] rounded-[5px] {{ $isLastPage ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <svg class="w-4 h-4 text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
