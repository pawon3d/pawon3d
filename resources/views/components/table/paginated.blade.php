@props([
    'headers' => [],
    'rows' => [],
    'paginator' => null,
    'emptyMessage' => 'Tidak ada data.',
    'headerBg' => 'bg-gray-50 dark:bg-gray-800',
    'headerText' => 'text-gray-500 dark:text-gray-400',
    'bodyBg' => 'bg-white dark:bg-gray-900',
    'bodyText' => 'text-gray-900 dark:text-gray-100',
    'wrapperClass' => '',
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
@endphp

<div
    {{ $attributes->merge(['class' => $wrapperClass ?: 'w-full rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900']) }}>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="{{ $headerBgClass }}" style="{{ $headerBgStyle }}">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col"
                            class="px-6 py-5 text-left text-xs font-bold uppercase {{ $headerTextClass }} {{ $header['class'] ?? '' }}"
                            style="{{ $headerTextStyle }}">
                            @if (isset($header['sortable']) && $header['sortable'])
                                @php
                                    $sortMethod = $header['sort-method'] ?? 'sortBy';
                                    $sortField = $header['sort-by'] ?? '';
                                @endphp
                                <button type="button" wire:click="{{ $sortMethod }}('{{ $sortField }}')"
                                    class="flex items-center gap-2 {{ $header['align'] ?? '' == 'right' ? 'justify-end' : '' }} w-full">
                                    <span>{{ is_array($header) ? $header['label'] : $header }}</span>
                                    <flux:icon.chevron-up-down class="size-3.5" />
                                </button>
                            @else
                                <span class="{{ $header['align'] ?? '' == 'right' ? 'flex justify-end' : '' }}">
                                    {{ is_array($header) ? $header['label'] : $header }}
                                </span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="{{ $bodyBgClass }} {{ $bodyTextClass }}" style="{{ $bodyBgStyle }} {{ $bodyTextStyle }}"
                <tbody class="{{ $bodyBgClass }} {{ $bodyTextClass }}"
                style="{{ $bodyBgStyle }} {{ $bodyTextStyle }}">
                @if ($paginator && $paginator->count() > 0)
                    {{ $slot }}
                @else
                    <tr>
                        <td colspan="{{ count($headers) }}" class="px-6 py-4 text-center text-sm {{ $bodyTextClass }}"
                            style="{{ $bodyTextStyle }}">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if ($paginator && $paginator->total() > 0)
        <div class="flex flex-col gap-3 px-6 py-4 md:flex-row md:items-center md:justify-between">
            <div class="text-sm {{ $bodyTextClass }} opacity-70" style="{{ $bodyTextStyle }}">
                <span>Menampilkan {{ $paginator->firstItem() ?? 0 }} hingga {{ $paginator->lastItem() ?? 0 }} dari
                    {{ $paginator->total() }} baris data</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="previousPage" @disabled($paginator->onFirstPage())
                    class="bg-[#fafafa] border border-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px] {{ $paginator->onFirstPage() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#f0f0f0]' }}">
                    <flux:icon.chevron-left class="size-[17px] text-[#666666]" />
                </button>
                <div class="bg-[#666666] min-w-[30px] px-3 py-1 rounded-[5px] text-center">
                    <span class="text-sm text-white font-medium">{{ $paginator->currentPage() }}</span>
                </div>
                <button type="button" wire:click="nextPage" @disabled(!$paginator->hasMorePages())
                    class="bg-[#fafafa] border border-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px] {{ $paginator->hasMorePages() ? 'hover:bg-[#f0f0f0]' : 'opacity-50 cursor-not-allowed' }}">
                    <flux:icon.chevron-right class="size-[17px] text-[#666666]" />
                </button>
            </div>
        </div>
    @endif
</div>
