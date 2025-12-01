<div class="space-y-5">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold text-[#333333]">Laporan Inventori</h1>
        <button
            class="flex items-center gap-2 bg-[#3f4e4f] hover:bg-[#313131] text-[#f8f4e1] px-5 py-2.5 rounded-[15px] transition-colors">
            <flux:icon icon="printer" class="size-5" />
            <span class="text-sm font-medium">Cetak Informasi</span>
        </button>
    </div>

    {{-- Date and Worker Filter --}}
    <div class="flex gap-4">
        {{-- Custom Calendar Dropdown --}}
        <div class="flex-1 max-w-[500px] relative" x-data="{ open: @entangle('showCalendar') }" @click.away="open = false">
            <button type="button" @click="open = !open"
                class="w-full bg-[#fafafa] border border-[#adadad] rounded-[15px] px-[30px] py-[10px] flex items-center justify-between cursor-pointer hover:border-[#666666] transition-colors">
                <span class="text-[#666666] text-base">
                    @if ($filterPeriod === 'Custom')
                        @if (!empty($customStartDate) && !empty($customEndDate))
                            {{ \Carbon\Carbon::parse($customStartDate)->translatedFormat('d F Y') }} —
                            {{ \Carbon\Carbon::parse($customEndDate)->translatedFormat('d F Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($customStartDate ?? $selectedDate)->translatedFormat('d F Y') }}
                        @endif
                    @else
                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                    @endif
                </span>
                <flux:icon icon="calendar" class="size-5 text-[#959595]" />
            </button>

            {{-- Calendar Dropdown --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                class="absolute z-50 mt-2 left-0 bg-white border border-[#d4d4d4] rounded-[15px] shadow-lg p-5 w-[385px] overflow-hidden"
                @click.stop>

                {{-- Filter Period Buttons --}}
                <div class="flex gap-2 mb-4 overflow-x-scroll scroll-hide">
                    @foreach (['Hari', 'Minggu', 'Bulan', 'Tahun', 'Custom'] as $period)
                        <button type="button" wire:click="setFilterPeriod('{{ $period }}')"
                            class="px-[15px] py-[10px] text-sm rounded-[15px] transition-colors
                                {{ $filterPeriod === $period
                                    ? 'bg-[#313131] text-[#f8f4e1] font-medium shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]'
                                    : 'bg-[#fafafa] text-[#666666] border border-[#d4d4d4] hover:bg-gray-100' }}">
                            {{ $period }}
                        </button>
                    @endforeach
                </div>

                @if ($filterPeriod === 'Custom' && !empty($customStartDate))
                    <div class="flex justify-end mb-4">
                        <button type="button" wire:click="clearCustomRange"
                            class="text-sm px-3 py-2 rounded-[10px] bg-[#fff] border border-[#d4d4d4] text-[#666666] hover:bg-gray-50">
                            Hapus Rentang
                        </button>
                    </div>
                @endif

                {{-- Header Navigation --}}
                <div class="flex items-center justify-between mb-4">
                    <button type="button" wire:click="previousMonth"
                        class="size-[34px] flex items-center justify-center border border-[#d4d4d4] rounded-[5px] bg-white hover:bg-gray-50 transition-colors">
                        <flux:icon icon="chevron-left" class="size-3 text-[#666666]" />
                    </button>
                    <div class="text-center">
                        <p class="text-sm font-medium text-[#666666]">
                            {{ \Carbon\Carbon::parse($currentMonth)->translatedFormat('F') }}
                        </p>
                        <p class="text-sm font-medium text-[#666666]">
                            {{ \Carbon\Carbon::parse($currentMonth)->year }}
                        </p>
                    </div>
                    <button type="button" wire:click="nextMonth"
                        class="size-[34px] flex items-center justify-center border border-[#d4d4d4] rounded-[5px] bg-white hover:bg-gray-50 transition-colors">
                        <flux:icon icon="chevron-right" class="size-3 text-[#666666]" />
                    </button>
                </div>

                {{-- Day Names --}}
                <div class="grid grid-cols-7 gap-5 mb-4">
                    @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                        <div
                            class="w-[35px] text-center text-sm font-medium {{ in_array($day, ['Sab', 'Min']) ? 'text-[#eb5757]' : 'text-[#666666]' }}">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                {{-- Calendar Grid grouped by week so we can render continuous range highlights --}}
                @php $weeks = array_chunk($this->calendar, 7, true); @endphp
                <div class="space-y-2">
                    @foreach ($weeks as $weekIndex => $week)
                        @php
                            // find range positions within this week (0..6)
                            $positions = [];
                            $i = 0;
                            foreach ($week as $d => $inf) {
                                if (!empty($inf['inRange'])) {
                                    $positions[] = $i;
                                }
                                $i++;
                            }
                        @endphp

                        <div class="relative">
                            @if (!empty($positions))
                                @php
                                    $startPos = min($positions);
                                    $endPos = max($positions);
                                    $leftPct = ($startPos / 7) * 100;
                                    $widthPct = (($endPos - $startPos + 1) / 7) * 100;
                                    // determine rounding: check if the start cell is actual rangeStart, and end cell is rangeEnd
                                    $weekKeys = array_keys($week);
                                    $startKey = $weekKeys[$startPos] ?? null;
                                    $endKey = $weekKeys[$endPos] ?? null;
                                    $startIsEndpoint = $startKey ? $week[$startKey]['isRangeStart'] ?? false : false;
                                    $endIsEndpoint = $endKey ? $week[$endKey]['isRangeEnd'] ?? false : false;
                                    $borderRadius = '';
                                    if ($startIsEndpoint && $endIsEndpoint && $startPos == $endPos) {
                                        // single day range
                                        $borderRadius = 'border-radius: 6px;';
                                    } else {
                                        $borderLeft = $startIsEndpoint ? '6px' : '0';
                                        $borderRight = $endIsEndpoint ? '6px' : '0';
                                        $borderRadius = "border-top-left-radius: {$borderLeft}; border-bottom-left-radius: {$borderLeft}; border-top-right-radius: {$borderRight}; border-bottom-right-radius: {$borderRight};";
                                    }
                                @endphp
                                <div class="absolute top-0 left-0 h-[40px]"
                                    style="left: {{ $leftPct }}%; width: {{ $widthPct }}%; background: #e6f0ff; {{ $borderRadius }} pointer-events: none;">
                                </div>
                            @endif

                            <div class="grid grid-cols-7 gap-x-5 gap-y-4">
                                @php $i = 0; @endphp
                                @foreach ($week as $date => $info)
                                    @php
                                        // make buttons positioned above the overlay
                                        $base =
                                            'w-[35px] h-[40px] flex items-center justify-center text-sm transition-colors relative z-10';
                                        $textClass = '';
                                        $bgClass = '';
                                        $radiusClass = 'rounded-[5px]';

                                        if ($info['isSelected']) {
                                            $bgClass = 'bg-[#3f4e4f] text-white';
                                        } else {
                                            if (!$info['isCurrentMonth']) {
                                                $textClass = 'text-[#adadad]';
                                            } elseif ($info['isWeekend']) {
                                                $textClass = 'text-[#eb5757]';
                                            } else {
                                                $textClass = 'text-[#666666]';
                                            }
                                            // endpoints get a subtle rounding so numbers align with overlay
                                            if (!empty($info['isRangeStart'])) {
                                                $radiusClass = 'rounded-l-[5px]';
                                            } elseif (!empty($info['isRangeEnd'])) {
                                                $radiusClass = 'rounded-r-[5px]';
                                            } elseif (!empty($info['inRange'])) {
                                                $radiusClass = 'rounded-none';
                                            }
                                        }
                                    @endphp

                                    <button type="button" wire:click="selectDate('{{ $date }}')"
                                        class="{{ $base }} {{ $bgClass }} {{ $textClass }} {{ $radiusClass }} {{ !$info['isSelected'] ? 'hover:bg-gray-100' : '' }}">
                                        <div class="flex flex-col items-center">
                                            <span>{{ $info['day'] }}</span>
                                            @if (!empty($info['hasData']))
                                                <span title="{{ $info['productionCount'] ?? 0 }} produksi"
                                                    aria-label="{{ $info['productionCount'] ?? 0 }} produksi"
                                                    class="mt-1 w-1.5 h-1.5 rounded-full bg-[#4caf50] transition-all duration-200"></span>
                                            @endif
                                        </div>
                                    </button>

                                    @php $i++; @endphp
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex-1">
            <flux:select wire:model.live="selectedWorker"
                class="!bg-[#fafafa] !border-[#adadad] !rounded-[15px] !px-5 !py-2.5 !text-[#666666]" searchable>
                <option value="semua">Semua Pekerja</option>
                @foreach (\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Statistics Cards --}}
    {{-- Top Card - Sesi Belanja Persediaan --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
        <div class="flex items-start justify-between">
            <div class="flex flex-col gap-[15px]">
                <p class="text-base font-medium text-[#333333]/70">Sesi Belanja Persediaan</p>
                <h3 class="text-2xl font-bold text-[#333333]">{{ number_format($totalExpense, 0, ',', '.') }}</h3>
            </div>
            <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                <flux:icon icon="shopping-cart" class="size-[38px] text-[#74512D]" />
            </div>
        </div>
        <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
            <span class="{{ $diffStats['totalExpense']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $diffStats['totalExpense']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['totalExpense']['percentage'] }}%
            </span>
            <span class="{{ $diffStats['totalExpense']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ({{ $diffStats['totalExpense']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['totalExpense']['diff'] }})
            </span>
        </div>
    </div>

    {{-- Bottom 3 Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Nilai Persediaan --}}
        <div
            class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px] min-h-[120px]">
            <div class="flex items-start justify-between">
                <div class="flex flex-col gap-[15px]">
                    <p class="text-base font-medium text-[#333333]/70">Nilai Persediaan</p>
                    <h3 class="text-2xl font-bold text-[#333333]">Rp{{ number_format($grandTotal, 0, ',', '.') }}</h3>
                </div>
                <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                    <flux:icon icon="wallet" class="size-[26px] text-[#74512D]" />
                </div>
            </div>
            <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
                <span class="{{ $diffStats['grandTotal']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $diffStats['grandTotal']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['grandTotal']['percentage'] }}%
                </span>
                <span class="{{ $diffStats['grandTotal']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ({{ $diffStats['grandTotal']['diff'] >= 0 ? '+' : '' }}Rp{{ number_format($diffStats['grandTotal']['diff'], 0, ',', '.') }})
                </span>
            </div>
        </div>

        {{-- Nilai Persediaan Terpakai --}}
        <div
            class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px] min-h-[120px]">
            <div class="flex items-start justify-between">
                <div class="flex flex-col gap-[15px]">
                    <p class="text-base font-medium text-[#333333]/70">Nilai Persediaan Terpakai</p>
                    <h3 class="text-2xl font-bold text-[#333333]">Rp{{ number_format($usedGrandTotal, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
                    <svg class="size-[26px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M4 10v7h3v-7H4zm6 0v7h3v-7h-3zM2 22h19v-3H2v3zm14-12v7h3v-7h-3zm-4.5-9L2 6v2h19V6l-9.5-5z" />
                    </svg>
                    <div
                        class="absolute bottom-[6px] left-[6px] bg-[#f3f3f3] rounded-full size-[23px] flex items-center justify-center">
                        <flux:icon icon="minus" class="size-[11px] text-[#74512D]" />
                    </div>
                </div>
            </div>
            <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
                <span class="{{ $diffStats['usedGrandTotal']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $diffStats['usedGrandTotal']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['usedGrandTotal']['percentage'] }}%
                </span>
                <span class="{{ $diffStats['usedGrandTotal']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ({{ $diffStats['usedGrandTotal']['diff'] >= 0 ? '+' : '' }}Rp{{ number_format($diffStats['usedGrandTotal']['diff'], 0, ',', '.') }})
                </span>
            </div>
        </div>

        {{-- Nilai Persediaan Saat Ini --}}
        <div
            class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px] min-h-[120px]">
            <div class="flex items-start justify-between">
                <div class="flex flex-col gap-[15px]">
                    <p class="text-base font-medium text-[#333333]/70">Nilai Persediaan Saat Ini</p>
                    <h3 class="text-2xl font-bold text-[#333333]">
                        Rp{{ number_format($remainGrandTotal, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
                    <flux:icon icon="wallet" class="size-[26px] text-[#74512D]" />
                    <div
                        class="absolute bottom-[6px] left-[6px] bg-[#f3f3f3] rounded-full size-[23px] flex items-center justify-center">
                        <svg class="size-[11px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
                <span class="{{ $diffStats['remainGrandTotal']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $diffStats['remainGrandTotal']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['remainGrandTotal']['percentage'] }}%
                </span>
                <span class="{{ $diffStats['remainGrandTotal']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ({{ $diffStats['remainGrandTotal']['diff'] >= 0 ? '+' : '' }}Rp{{ number_format($diffStats['remainGrandTotal']['diff'], 0, ',', '.') }})
                </span>
            </div>
        </div>
    </div>

    {{-- Chart and Best/Worst Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Chart: 10 Persediaan Banyak Digunakan --}}
        <div
            class="lg:col-span-2 bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] p-[30px] relative">
            <h3 class="text-base font-semibold text-[#333333] mb-4">10 Persediaan Banyak Digunakan</h3>
            <div id="topMaterialChartEmpty"
                class="absolute inset-0 flex items-center justify-center bg-white/50 opacity-0 pointer-events-none transition-opacity duration-300">
                <div class="text-center text-[#666666]">
                    <svg class="mx-auto mb-2" width="40" height="40" viewBox="0 0 24 24" fill="none"
                        stroke="#666666" stroke-width="1.5">
                        <path d="M3 3h18v18H3z" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8 14s1.5-2 4-2 4 2 4 2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="font-medium">Tidak ada data untuk rentang ini</div>
                    <div class="text-sm">Ubah tanggal atau pekerja untuk melihat data</div>
                </div>
            </div>

            <div id="topMaterialChartContainer" class="relative">
                <canvas id="topMaterialChart" class="w-full" style="max-height: 400px;"></canvas>
            </div>
        </div>

        {{-- Side Cards --}}
        <div class="flex flex-col gap-5">
            {{-- Persediaan Banyak Digunakan --}}
            <div
                class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px] flex-1 min-h-[180px]">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-[15px]">
                        <p class="text-base font-medium text-[#333333]/70">Persediaan Banyak Digunakan</p>
                        <h3 class="text-2xl font-bold text-[#333333]">
                            Rp{{ number_format($bestMaterial['total'] ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
                        <svg class="size-[26px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M4 10v7h3v-7H4zm6 0v7h3v-7h-3zM2 22h19v-3H2v3zm14-12v7h3v-7h-3zm-4.5-9L2 6v2h19V6l-9.5-5z" />
                        </svg>
                        <div
                            class="absolute bottom-[6px] left-[6px] bg-[#e8f5e9] rounded-full size-[23px] flex items-center justify-center">
                            <flux:icon icon="chevron-up" class="size-[11px] text-[#4caf50]" />
                        </div>
                    </div>
                </div>
                @if (!empty($bestMaterial['total']) && $bestMaterial['total'] > 0)
                    <p class="text-base text-[#333333]/70 mt-[15px]">{{ $bestMaterial['name'] ?? '-' }}</p>
                @else
                    <p class="text-base text-[#333333]/70 mt-[15px]">-</p>
                @endif
                <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                    <span class="{{ $diffStats['best']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diffStats['best']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['best']['percentage'] }}%
                    </span>
                    <span class="{{ $diffStats['best']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $diffStats['best']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['best']['diff'] }})
                    </span>
                </div>
            </div>

            {{-- Persediaan Sedikit Digunakan --}}
            <div
                class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px] flex-1 min-h-[180px]">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-[15px]">
                        <p class="text-base font-medium text-[#333333]/70">Persediaan Sedikit Digunanakan</p>
                        <h3 class="text-2xl font-bold text-[#333333]">
                            Rp{{ number_format($worstMaterial['total'] ?? 0, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
                        <flux:icon icon="wallet" class="size-[26px] text-[#74512D]" />
                        <div
                            class="absolute bottom-[6px] left-[6px] bg-[#ffebee] rounded-full size-[23px] flex items-center justify-center">
                            <flux:icon icon="chevron-down" class="size-[11px] text-[#f44336]" />
                        </div>
                    </div>
                </div>
                @if (!empty($worstMaterial['total']) && $worstMaterial['total'] > 0)
                    <p class="text-base text-[#333333]/70 mt-[15px]">{{ $worstMaterial['name'] ?? '-' }}</p>
                @else
                    <p class="text-base text-[#333333]/70 mt-[15px]">-</p>
                @endif
                <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                    <span class="{{ $diffStats['worst']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diffStats['worst']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['worst']['percentage'] }}%
                    </span>
                    <span class="{{ $diffStats['worst']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $diffStats['worst']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['worst']['diff'] }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Persediaan Table --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px]">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-base font-semibold text-[#333333]">Persediaan</h3>
            <div class="flex gap-3 items-center">
                <div class="relative flex items-center">
                    <flux:icon icon="magnifying-glass" class="absolute left-[15px] text-gray-400 size-5" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Persediaan"
                        class="pl-[45px] pr-4 py-[10px] text-sm border border-[#d4d4d4] rounded-[10px] bg-white focus:ring-2 focus:ring-[#74512D] focus:border-transparent w-[200px]">
                </div>
                <button
                    class="flex items-center gap-[5px] px-4 py-[10px] text-[#666666] hover:bg-gray-100 rounded-[10px] transition-colors">
                    <flux:icon icon="funnel" class="size-[25px]" />
                    <span class="text-sm">Filter</span>
                </button>
            </div>
        </div>

        <x-table.paginated :headers="$tableHeaders" :paginator="$paginator" emptyMessage="Tidak ada data persediaan"
            headerBg="#3f4e4f" headerText="#f8f4e1">
            @foreach ($paginator as $item)
                <tr class="border-b border-[#d4d4d4] hover:bg-gray-50 transition-colors">
                    <td class="py-5 px-6 text-[#333333]">{{ $item->name }}</td>
                    <td class="py-5 px-6 text-right text-[#333333]">{{ $item->total }} {{ $item->total_alias }}</td>
                    <td class="py-5 px-6 text-right text-[#333333]">
                        Rp{{ number_format($item->total_price, 0, ',', '.') }}</td>
                    <td class="py-5 px-6 text-right text-[#333333]">-{{ $item->used }} {{ $item->used_alias }}</td>
                    <td class="py-5 px-6 text-right text-[#333333]">
                        -Rp{{ number_format($item->used_price, 0, ',', '.') }}</td>
                    <td class="py-5 px-6 text-right text-[#333333]">{{ $item->remain }} {{ $item->remain_alias }}
                    </td>
                    <td class="py-5 px-6 text-right text-[#333333]">
                        Rp{{ number_format($item->remain_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    @endassets

    @script
        <script>
            let topMaterialChart;

            window.initTopProductsChartInventori = function(labels, data) {
                if (topMaterialChart) topMaterialChart.destroy();

                const ctx = document.getElementById('topMaterialChart');
                const emptyEl = document.getElementById('topMaterialChartEmpty');
                if (!ctx) return;

                // If no labels/data, show empty state and don't render chart (with transition)
                if (!labels || !Array.isArray(labels) || labels.length === 0) {
                    if (emptyEl) {
                        emptyEl.classList.remove('opacity-0', 'pointer-events-none');
                        emptyEl.classList.add('opacity-100', 'pointer-events-auto');
                    }
                    ctx.style.display = 'none';
                    return;
                }

                // Has data: ensure canvas visible and empty state hidden (with transition)
                if (emptyEl) {
                    emptyEl.classList.remove('opacity-100', 'pointer-events-auto');
                    emptyEl.classList.add('opacity-0', 'pointer-events-none');
                }
                ctx.style.display = '';

                topMaterialChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Persediaan Digunakan',
                            data: data,
                            backgroundColor: '#74512D',
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Listen for Livewire chart update events using Livewire hook
            Livewire.on('update-charts', (args) => {
                const data = Array.isArray(args) ? args[0] : args;
                console.log('Chart update received:', data);
                if (data && data.topMaterialChartData) {
                    window.initTopProductsChartInventori(data.topMaterialChartData.labels, data.topMaterialChartData
                        .data);
                }
            });

            // Initialize chart on component load
            window.initTopProductsChartInventori(
                @json($topMaterialChartData['labels']),
                @json($topMaterialChartData['data']),
            );
        </script>
    @endscript
</div>
