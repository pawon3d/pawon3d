@props([
    'headers' => [],
    'rows' => null,
    'footer' => null,
    'emptyMessage' => 'Belum ada data.',
    'headerBg' => 'bg-[#3F4E4F]',
    'headerText' => 'text-[#F8F4E1]',
    'bodyBg' => 'bg-white',
    'bodyText' => 'text-[#666666]',
    'footerBg' => 'bg-[#EAEAEA]',
    'footerText' => 'text-[#666666]',
])

<div class="overflow-hidden rounded-[15px] border border-[#FAFAFA]">
    <table class="w-full text-sm">
        {{-- Header --}}
        <thead class="{{ $headerBg }} text-sm font-bold uppercase {{ $headerText }}">
            <tr>
                @foreach ($headers as $header)
                    <th {{ $header['attributes'] ?? '' }} class="{{ $header['class'] ?? 'px-6 py-5 text-left' }}">
                        {{ $header['label'] ?? $header }}
                    </th>
                @endforeach
            </tr>
        </thead>

        {{-- Body --}}
        <tbody class="{{ $bodyBg }} {{ $bodyText }}">
            @if ($rows && !$rows->isEmpty())
                {{ $rows }}
            @else
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-4 text-center text-[#959595]">
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endif
        </tbody>

        {{-- Footer (Optional) --}}
        @if ($footer)
            <tfoot class="{{ $footerBg }} text-sm font-bold uppercase {{ $footerText }}">
                {{ $footer }}
            </tfoot>
        @endif
    </table>
</div>
