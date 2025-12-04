<div class="space-y-5">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold text-[#333333]">Laporan Produksi</h1>
        <button
            onclick="window.open('{{ route('laporan-produksi.pdf') }}?filterPeriod={{ $filterPeriod }}&selectedDate={{ $selectedDate }}&customStartDate={{ $customStartDate }}&customEndDate={{ $customEndDate }}&selectedWorker={{ $selectedWorker }}&selectedMethod={{ $selectedMethod }}', '_blank')"
            class="flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-5 py-2.5 rounded-[15px] transition-colors">
            <flux:icon icon="printer" class="size-5" />
            <span class="text-sm font-medium">Cetak Informasi</span>
        </button>
    </div>

    {{-- Filters Row: Date Calendar, Worker, Method --}}
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

                {{-- Calendar Grid --}}
                @php $weeks = array_chunk($this->calendar, 7, true); @endphp
                <div class="space-y-2">
                    @foreach ($weeks as $weekIndex => $week)
                        @php
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
                                    $weekKeys = array_keys($week);
                                    $startKey = $weekKeys[$startPos] ?? null;
                                    $endKey = $weekKeys[$endPos] ?? null;
                                    $startIsEndpoint = $startKey ? $week[$startKey]['isRangeStart'] ?? false : false;
                                    $endIsEndpoint = $endKey ? $week[$endKey]['isRangeEnd'] ?? false : false;
                                    $borderRadius = '';
                                    if ($startIsEndpoint && $endIsEndpoint && $startPos == $endPos) {
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

        {{-- Worker Dropdown --}}
        <div class="flex-1">
            <flux:select wire:model.live="selectedWorker"
                class="!bg-[#fafafa] !border-[#adadad] !rounded-[15px] !px-5 !py-2.5 !text-[#666666]" searchable>
                <option value="semua">Semua Pekerja</option>
                @foreach (\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </flux:select>
        </div>

        {{-- Method Dropdown --}}
        <div class="flex-1">
            <flux:select wire:model.live="selectedMethod"
                class="!bg-[#fafafa] !border-[#adadad] !rounded-[15px] !px-5 !py-2.5 !text-[#666666]">
                <option value="semua">Semua Metode Penjualan</option>
                <option value="pesanan-reguler">Pesanan Reguler</option>
                <option value="pesanan-kotak">Pesanan Kotak</option>
                <option value="siap-beli">Siap Saji</option>
            </flux:select>
        </div>
    </div>

    {{-- Statistics Cards --}}
    {{-- Top Card - Produksi --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
        <div class="flex items-start justify-between">
            <div class="flex flex-col gap-[15px]">
                <p class="text-base font-medium text-[#333333]/70">Produksi</p>
                <h3 class="text-2xl font-bold text-[#333333]">{{ number_format($totalProduction, 0, ',', '.') }} pcs
                </h3>
            </div>
            <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                {{-- Cake/Cupcake Icon --}}
                <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 6c1.11 0 2-.9 2-2 0-.38-.1-.73-.29-1.03L12 0l-1.71 2.97c-.19.3-.29.65-.29 1.03 0 1.1.9 2 2 2zm4.6 9.99l-1.07-1.07-1.08 1.07c-1.3 1.3-3.58 1.31-4.89 0l-1.07-1.07-1.09 1.07C6.75 16.64 5.88 17 4.96 17c-.73 0-1.4-.23-1.96-.61V21c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-4.61c-.56.38-1.23.61-1.96.61-.92 0-1.79-.36-2.44-1.01zM18 9h-5V7h-2v2H6c-1.66 0-3 1.34-3 3v1.54c0 1.08.88 1.96 1.96 1.96.52 0 1.02-.2 1.38-.57l2.14-2.13 2.13 2.13c.74.74 2.03.74 2.77 0l2.14-2.13 2.13 2.13c.37.37.86.57 1.38.57 1.08 0 1.96-.88 1.96-1.96V12C21 10.34 19.66 9 18 9z" />
                </svg>
            </div>
        </div>
        <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
            <span class="{{ $diffStats['totalProduction']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $diffStats['totalProduction']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['totalProduction']['percentage'] }}%
            </span>
            <span class="{{ $diffStats['totalProduction']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                ({{ $diffStats['totalProduction']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['totalProduction']['diff'], 0, ',', '.') }})
            </span>
        </div>
    </div>

    {{-- Bottom 2 Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Produksi Berhasil --}}
        <div
            class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px] min-h-[120px]">
            <div class="flex items-start justify-between">
                <div class="flex flex-col gap-[15px]">
                    <p class="text-base font-medium text-[#333333]/70">Produksi Berhasil</p>
                    <h3 class="text-2xl font-bold text-[#333333]">{{ number_format($successProduction, 0, ',', '.') }}
                        pcs</h3>
                </div>
                <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
                    {{-- Cake Icon --}}
                    <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 6c1.11 0 2-.9 2-2 0-.38-.1-.73-.29-1.03L12 0l-1.71 2.97c-.19.3-.29.65-.29 1.03 0 1.1.9 2 2 2zm4.6 9.99l-1.07-1.07-1.08 1.07c-1.3 1.3-3.58 1.31-4.89 0l-1.07-1.07-1.09 1.07C6.75 16.64 5.88 17 4.96 17c-.73 0-1.4-.23-1.96-.61V21c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-4.61c-.56.38-1.23.61-1.96.61-.92 0-1.79-.36-2.44-1.01zM18 9h-5V7h-2v2H6c-1.66 0-3 1.34-3 3v1.54c0 1.08.88 1.96 1.96 1.96.52 0 1.02-.2 1.38-.57l2.14-2.13 2.13 2.13c.74.74 2.03.74 2.77 0l2.14-2.13 2.13 2.13c.37.37.86.57 1.38.57 1.08 0 1.96-.88 1.96-1.96V12C21 10.34 19.66 9 18 9z" />
                    </svg>
                    <div
                        class="absolute bottom-[6px] left-[6px] bg-[#56C568] rounded-full size-[23px] flex items-center justify-center border-2 border-[#fafafa]">
                        <flux:icon icon="check" class="size-[11px] text-white" />
                    </div>
                </div>
            </div>
            <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
                <span class="{{ $diffStats['successProduction']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $diffStats['successProduction']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['successProduction']['percentage'] }}%
                </span>
                <span class="{{ $diffStats['successProduction']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ({{ $diffStats['successProduction']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['successProduction']['diff'], 0, ',', '.') }})
                </span>
            </div>
        </div>

        {{-- Produksi Gagal --}}
        <div
            class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px] min-h-[120px]">
            <div class="flex items-start justify-between">
                <div class="flex flex-col gap-[15px]">
                    <p class="text-base font-medium text-[#333333]/70">Produksi Gagal</p>
                    <h3 class="text-2xl font-bold text-[#333333]">{{ number_format($failedProduction, 0, ',', '.') }}
                        pcs</h3>
                </div>
                <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
                    {{-- Cake Icon --}}
                    <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 6c1.11 0 2-.9 2-2 0-.38-.1-.73-.29-1.03L12 0l-1.71 2.97c-.19.3-.29.65-.29 1.03 0 1.1.9 2 2 2zm4.6 9.99l-1.07-1.07-1.08 1.07c-1.3 1.3-3.58 1.31-4.89 0l-1.07-1.07-1.09 1.07C6.75 16.64 5.88 17 4.96 17c-.73 0-1.4-.23-1.96-.61V21c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-4.61c-.56.38-1.23.61-1.96.61-.92 0-1.79-.36-2.44-1.01zM18 9h-5V7h-2v2H6c-1.66 0-3 1.34-3 3v1.54c0 1.08.88 1.96 1.96 1.96.52 0 1.02-.2 1.38-.57l2.14-2.13 2.13 2.13c.74.74 2.03.74 2.77 0l2.14-2.13 2.13 2.13c.37.37.86.57 1.38.57 1.08 0 1.96-.88 1.96-1.96V12C21 10.34 19.66 9 18 9z" />
                    </svg>
                    <div
                        class="absolute bottom-[6px] left-[6px] bg-[#EB5757] rounded-full size-[23px] flex items-center justify-center border-2 border-[#fafafa]">
                        <flux:icon icon="x-mark" class="size-[11px] text-white" />
                    </div>
                </div>
            </div>
            <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
                <span class="{{ $diffStats['failedProduction']['diff'] <= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $diffStats['failedProduction']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['failedProduction']['percentage'] }}%
                </span>
                <span class="{{ $diffStats['failedProduction']['diff'] <= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ({{ $diffStats['failedProduction']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['failedProduction']['diff'], 0, ',', '.') }})
                </span>
            </div>
        </div>
    </div>

    {{-- Charts Row: Top 10 + Highest/Lowest --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Top 10 Chart --}}
        <div
            class="lg:col-span-2 bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px]">
            <p class="font-medium text-[#333333]/70 mb-4">10 Produksi Tertinggi</p>
            <div class="relative" style="min-height: 320px;">
                @if (empty($topProductionsChartData['labels']) || count($topProductionsChartData['labels']) == 0)
                    <div class="absolute inset-0 flex items-center justify-center bg-[#fafafa]/80 z-10 rounded-lg">
                        <div class="text-center text-[#666666]">
                            <flux:icon icon="chart-bar" class="size-10 mx-auto mb-2" />
                            <p class="font-medium">Tidak ada data untuk rentang ini</p>
                            <p class="text-sm">Ubah filter untuk melihat data</p>
                        </div>
                    </div>
                @endif
                <canvas id="topProductionsChart" class="w-full"></canvas>
            </div>
        </div>

        {{-- Highest & Lowest Cards --}}
        <div class="flex flex-col gap-5">
            {{-- Produksi Tertinggi --}}
            <div
                class="flex-1 bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-[10px]">
                        <p class="text-base font-medium text-[#333333]/70">Produksi Tertinggi</p>
                        <h3 class="text-2xl font-bold text-[#333333]">
                            {{ number_format($bestProduction['total'] ?? 0, 0, ',', '.') }} pcs
                        </h3>
                    </div>
                    <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
                        <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 6c1.11 0 2-.9 2-2 0-.38-.1-.73-.29-1.03L12 0l-1.71 2.97c-.19.3-.29.65-.29 1.03 0 1.1.9 2 2 2zm4.6 9.99l-1.07-1.07-1.08 1.07c-1.3 1.3-3.58 1.31-4.89 0l-1.07-1.07-1.09 1.07C6.75 16.64 5.88 17 4.96 17c-.73 0-1.4-.23-1.96-.61V21c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-4.61c-.56.38-1.23.61-1.96.61-.92 0-1.79-.36-2.44-1.01zM18 9h-5V7h-2v2H6c-1.66 0-3 1.34-3 3v1.54c0 1.08.88 1.96 1.96 1.96.52 0 1.02-.2 1.38-.57l2.14-2.13 2.13 2.13c.74.74 2.03.74 2.77 0l2.14-2.13 2.13 2.13c.37.37.86.57 1.38.57 1.08 0 1.96-.88 1.96-1.96V12C21 10.34 19.66 9 18 9z" />
                        </svg>
                        <div
                            class="absolute bottom-[6px] left-[6px] bg-[#56C568] rounded-full size-[23px] flex items-center justify-center border-2 border-[#fafafa]">
                            <flux:icon icon="arrow-up" class="size-[11px] text-white" />
                        </div>
                    </div>
                </div>
                <p class="text-base text-[#333333]/70 mt-2">{{ $bestProduction['name'] ?? '-' }}</p>
                <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                    <span class="{{ $diffStats['best']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diffStats['best']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['best']['percentage'] }}%
                    </span>
                    <span class="{{ $diffStats['best']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $diffStats['best']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['best']['diff'], 0, ',', '.') }})
                    </span>
                </div>
            </div>

            {{-- Produksi Terendah --}}
            <div
                class="flex-1 bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-[10px]">
                        <p class="text-base font-medium text-[#333333]/70">Produksi Terendah</p>
                        <h3 class="text-2xl font-bold text-[#333333]">
                            {{ number_format($worstProduction['total'] ?? 0, 0, ',', '.') }} pcs
                        </h3>
                    </div>
                    <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
                        <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 6c1.11 0 2-.9 2-2 0-.38-.1-.73-.29-1.03L12 0l-1.71 2.97c-.19.3-.29.65-.29 1.03 0 1.1.9 2 2 2zm4.6 9.99l-1.07-1.07-1.08 1.07c-1.3 1.3-3.58 1.31-4.89 0l-1.07-1.07-1.09 1.07C6.75 16.64 5.88 17 4.96 17c-.73 0-1.4-.23-1.96-.61V21c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-4.61c-.56.38-1.23.61-1.96.61-.92 0-1.79-.36-2.44-1.01zM18 9h-5V7h-2v2H6c-1.66 0-3 1.34-3 3v1.54c0 1.08.88 1.96 1.96 1.96.52 0 1.02-.2 1.38-.57l2.14-2.13 2.13 2.13c.74.74 2.03.74 2.77 0l2.14-2.13 2.13 2.13c.37.37.86.57 1.38.57 1.08 0 1.96-.88 1.96-1.96V12C21 10.34 19.66 9 18 9z" />
                        </svg>
                        <div
                            class="absolute bottom-[6px] left-[6px] bg-[#EB5757] rounded-full size-[23px] flex items-center justify-center border-2 border-[#fafafa]">
                            <flux:icon icon="arrow-down" class="size-[11px] text-white" />
                        </div>
                    </div>
                </div>
                <p class="text-base text-[#333333]/70 mt-2">{{ $worstProduction['name'] ?? '-' }}</p>
                <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                    <span class="{{ $diffStats['worst']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diffStats['worst']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['worst']['percentage'] }}%
                    </span>
                    <span class="{{ $diffStats['worst']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $diffStats['worst']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['worst']['diff'], 0, ',', '.') }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Pie Chart: Metode Produksi --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px]">
        <p class="font-medium text-[#333333]/70 mb-4">Metode Produksi Teratas</p>
        @if (count($productionChartData['labels']) == 0 || count($productionChartData['data']) == 0)
            <p class="text-[#666666] text-center py-8">Tidak ada data untuk ditampilkan</p>
        @else
            <div class="flex justify-center" style="height: 320px;">
                <canvas id="productionMethodChart"></canvas>
            </div>
        @endif
    </div>

    {{-- Table: Produksi Produk --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px]">
        <div class="flex items-center justify-between mb-5">
            <p class="font-medium text-[#333333]/70">Produksi Produk</p>
            <div class="flex items-center gap-4">
                <div class="flex items-center bg-white border border-[#d4d4d4] rounded-[20px] px-4 py-1 min-w-[250px]">
                    <flux:icon icon="magnifying-glass" class="size-5 text-[#959595]" />
                    <input type="text" placeholder="Cari Produk"
                        class="ml-2 py-2 text-sm text-[#959595] bg-transparent border-none focus:outline-none w-full"
                        wire:model.live.debounce.300ms="search" />
                </div>
                <button class="flex items-center gap-1 text-[#666666]">
                    <flux:icon icon="funnel" class="size-5" />
                    <span class="text-sm font-medium">Filter</span>
                </button>
            </div>
        </div>

        <x-table.paginated :headers="$tableHeaders" :paginator="$paginator" emptyMessage="Tidak ada data produksi"
            headerBg="#3f4e4f" headerText="#f8f4e1">
            @foreach ($paginator as $product)
                <tr class="border-b border-[#d4d4d4] hover:bg-gray-50 transition-colors">
                    <td class="py-5 px-6 text-[#333333]">{{ $product->name }}</td>
                    <td class="py-5 px-6 text-left text-[#333333]">{{ number_format($product->total, 0, ',', '.') }}
                    </td>
                    <td class="py-5 px-6 text-left text-[#333333]">
                        {{ number_format($product->success, 0, ',', '.') }}</td>
                    <td class="py-5 px-6 text-left text-[#333333]">{{ number_format($product->fail, 0, ',', '.') }}
                    </td>
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
            let topProductionsChart, paymentChart;

            function initTopProductsChart(labels, data) {
                if (topProductionsChart) topProductionsChart.destroy();

                const ctx = document.getElementById('topProductionsChart');
                if (!ctx) return;

                topProductionsChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Produk (Pcs)',
                            data: data,
                            backgroundColor: '#3f4e4f',
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'rect',
                                    padding: 20
                                }
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'end',
                                color: '#333',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: '#e5e5e5'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }

            function initPieChart(canvasId, labels, data, chartRef, labelText) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return null;

                if (chartRef && chartRef.destroy) {
                    chartRef.destroy();
                }

                const ctx = canvas.getContext('2d');
                return new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: ['#74512D', '#3f4e4f', '#D4A574'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                formatter: (value, ctx) => {
                                    return ctx.chart.data.labels[ctx.dataIndex] + '\n' + value;
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            }

            // Listen for Livewire chart update events
            Livewire.on('update-charts', (args) => {
                const data = Array.isArray(args) ? args[0] : args;

                if (data.topProductionsChartData) {
                    initTopProductsChart(data.topProductionsChartData.labels, data.topProductionsChartData.data);
                }
                if (data.productionChartData) {
                    paymentChart = initPieChart('productionMethodChart', data.productionChartData.labels, data
                        .productionChartData.data, paymentChart, 'Metode Produksi');
                }
            });

            // Initialize charts on component load
            initTopProductsChart(
                @json($topProductionsChartData['labels']),
                @json($topProductionsChartData['data']),
            );
            paymentChart = initPieChart(
                'productionMethodChart',
                @json($productionChartData['labels']),
                @json($productionChartData['data']),
                null,
                'Metode Produksi'
            );
        </script>
    @endscript
</div>
