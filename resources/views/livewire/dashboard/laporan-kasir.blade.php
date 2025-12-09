<div class="space-y-5" wire:init="loadData">
    {{-- Loading Overlay --}}
    @if (!$readyToLoad)
        <div class="flex flex-col items-center justify-center py-20">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#7c3aed] mb-4"></div>
            <p class="text-[#666666] text-base">Memuat data laporan...</p>
        </div>
    @else
        {{-- Header --}}
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-semibold text-[#333333]">Laporan Kasir</h1>
            <flux:button variant="secondary" icon="printer"
                onclick="window.open('{{ route('laporan-kasir.pdf') }}?filterPeriod={{ $filterPeriod }}&selectedDate={{ $selectedDate }}&customStartDate={{ $customStartDate }}&customEndDate={{ $customEndDate }}&selectedWorker={{ $selectedWorker }}&selectedMethod={{ $selectedMethod }}', '_blank')">
                Cetak Informasi
            </flux:button>
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
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
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
                                        $startIsEndpoint = $startKey
                                            ? $week[$startKey]['isRangeStart'] ?? false
                                            : false;
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
                                                    <span title="{{ $info['transactionCount'] ?? 0 }} transaksi"
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

        {{-- Stats Cards Row 1: Sesi Penjualan + Pelanggan Baru --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Sesi Penjualan --}}
            <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-[15px]">
                        <p class="text-base font-medium text-[#333333]/70">Sesi Penjualan</p>
                        <h3 class="text-2xl font-bold text-[#333333]">{{ number_format($sessionCount, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                        <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M19 6h-3V5c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v1H5c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM10 5h4v1h-4V5zm9 14H5V8h14v11z" />
                            <path
                                d="M12 10c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm0 4.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" />
                        </svg>
                    </div>
                </div>
                <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
                    <span class="{{ $diffStats['sessionCount']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diffStats['sessionCount']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['sessionCount']['percentage'] }}%
                    </span>
                    <span class="{{ $diffStats['sessionCount']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $diffStats['sessionCount']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['sessionCount']['diff'], 0, ',', '.') }})
                    </span>
                </div>
            </div>

            {{-- Pelanggan Baru --}}
            <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-[15px]">
                        <p class="text-base font-medium text-[#333333]/70">Pelanggan Baru</p>
                        <h3 class="text-2xl font-bold text-[#333333]">{{ number_format($customerCount, 0, ',', '.') }}
                            orang</h3>
                    </div>
                    <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                        <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                        </svg>
                    </div>
                </div>
                <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
                    <span class="{{ $diffStats['customerCount']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diffStats['customerCount']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['customerCount']['percentage'] }}%
                    </span>
                    <span class="{{ $diffStats['customerCount']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $diffStats['customerCount']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['customerCount']['diff'], 0, ',', '.') }})
                    </span>
                </div>
            </div>
        </div>

        {{-- Transaksi Card (full width) --}}
        <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
            <div class="flex items-start justify-between">
                <div class="flex flex-col gap-[15px]">
                    <p class="text-base font-medium text-[#333333]/70">Transaksi</p>
                    <h3 class="text-2xl font-bold text-[#333333]">{{ number_format($transactionCount, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                    <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                    </svg>
                </div>
            </div>
            <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[15px]">
                <span class="{{ $diffStats['transactionCount']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $diffStats['transactionCount']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['transactionCount']['percentage'] }}%
                </span>
                <span class="{{ $diffStats['transactionCount']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    ({{ $diffStats['transactionCount']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['transactionCount']['diff'], 0, ',', '.') }})
                </span>
            </div>
        </div>

        {{-- Charts Row: 10 Produk Terlaris + Product Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- 10 Produk Terlaris Chart --}}
            <div
                class="lg:col-span-2 bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px]">
                <p class="font-medium text-[#333333]/70 mb-4">10 Produk Terlaris</p>
                <div class="relative" style="min-height: 320px;">
                    @if (empty($topProductsChartData['labels']) || count($topProductsChartData['labels']) == 0)
                        <div class="absolute inset-0 flex items-center justify-center bg-[#fafafa]/80 z-10 rounded-lg">
                            <div class="text-center text-[#666666]">
                                <flux:icon icon="chart-bar" class="size-10 mx-auto mb-2" />
                                <p class="font-medium">Tidak ada data</p>
                            </div>
                        </div>
                    @endif
                    <canvas id="topProductsChart" class="w-full"></canvas>
                </div>
            </div>

            {{-- Product Cards --}}
            <div class="flex flex-col gap-5">
                {{-- Produk Terjual --}}
                <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col gap-[10px]">
                            <p class="text-base font-medium text-[#333333]/70">Produk Terjual</p>
                            <h3 class="text-2xl font-bold text-[#333333]">
                                {{ number_format($productSold, 0, ',', '.') }}
                                pcs</h3>
                        </div>
                        <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                            <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 6c1.11 0 2-.9 2-2 0-.38-.1-.73-.29-1.03L12 0l-1.71 2.97c-.19.3-.29.65-.29 1.03 0 1.1.9 2 2 2zm4.6 9.99l-1.07-1.07-1.08 1.07c-1.3 1.3-3.58 1.31-4.89 0l-1.07-1.07-1.09 1.07C6.75 16.64 5.88 17 4.96 17c-.73 0-1.4-.23-1.96-.61V21c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-4.61c-.56.38-1.23.61-1.96.61-.92 0-1.79-.36-2.44-1.01zM18 9h-5V7h-2v2H6c-1.66 0-3 1.34-3 3v1.54c0 1.08.88 1.96 1.96 1.96.52 0 1.02-.2 1.38-.57l2.14-2.13 2.13 2.13c.74.74 2.03.74 2.77 0l2.14-2.13 2.13 2.13c.37.37.86.57 1.38.57 1.08 0 1.96-.88 1.96-1.96V12C21 10.34 19.66 9 18 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                        <span
                            class="{{ $diffStats['productSold']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $diffStats['productSold']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['productSold']['percentage'] }}%
                        </span>
                        <span
                            class="{{ $diffStats['productSold']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ({{ $diffStats['productSold']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['productSold']['diff'], 0, ',', '.') }})
                        </span>
                    </div>
                </div>

                {{-- Produk Terlaris --}}
                <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col gap-[10px]">
                            <p class="text-base font-medium text-[#333333]/70">Produk Terlaris</p>
                            <h3 class="text-2xl font-bold text-[#333333]">
                                {{ number_format($bestProduct['total'] ?? 0, 0, ',', '.') }} pcs</h3>
                        </div>
                        <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
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
                    <p class="text-base text-[#333333]/70 mt-2">{{ $bestProduct['name'] ?? '-' }}</p>
                    <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                        <span class="{{ $diffStats['best']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $diffStats['best']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['best']['percentage'] }}%
                        </span>
                        <span class="{{ $diffStats['best']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ({{ $diffStats['best']['diff'] >= 0 ? '+' : '' }}{{ number_format($diffStats['best']['diff'], 0, ',', '.') }})
                        </span>
                    </div>
                </div>

                {{-- Produk Tersepi --}}
                <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col gap-[10px]">
                            <p class="text-base font-medium text-[#333333]/70">Produk Tersepi</p>
                            <h3 class="text-2xl font-bold text-[#333333]">
                                {{ number_format($worstProduct['total'] ?? 0, 0, ',', '.') }} pcs</h3>
                        </div>
                        <div class="relative flex items-center justify-center size-[60px] rounded-[20px]">
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
                    <p class="text-base text-[#333333]/70 mt-2">{{ $worstProduct['name'] ?? '-' }}</p>
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

        {{-- Tabel Penjualan Produk --}}
        <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px]">
            <div class="flex items-center justify-between mb-5">
                <p class="font-medium text-[#333333]/70">Penjualan Produk</p>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center bg-white border border-[#d4d4d4] rounded-[20px] px-4 py-1 min-w-[250px]">
                        <flux:icon icon="magnifying-glass" class="size-5 text-[#959595]" />
                        <input type="text" placeholder="Cari Produk"
                            class="ml-2 py-2 text-sm text-[#959595] bg-transparent border-none focus:outline-none w-full"
                            wire:model.live.debounce.300ms="searchProduct" />
                    </div>
                    <button class="flex items-center gap-1 text-[#666666]">
                        <flux:icon icon="funnel" class="size-5" />
                        <span class="text-sm">Filter</span>
                    </button>
                </div>
            </div>

            <x-table.paginated :headers="$productSalesHeaders" :paginator="$productSalesPaginator" emptyMessage="Tidak ada data penjualan"
                headerBg="#3f4e4f" headerText="white">
                @foreach ($productSalesPaginator as $item)
                    <tr class="border-b border-[#e5e5e5] hover:bg-gray-50">
                        <td class="py-3 px-4 text-left text-[#333333]">{{ $item->name }}</td>
                        <td class="py-3 px-4 text-center text-[#333333]">
                            {{ number_format($item->produksi, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center text-[#333333]">{{ number_format($item->sold, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center text-[#333333]">
                            {{ number_format($item->unsold, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </x-table.paginated>
        </div>

        {{-- ==================== PART 3: Revenue Section ==================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Left Side: Revenue Cards --}}
            <div class="flex flex-col gap-5">
                {{-- Pendapatan Kotor --}}
                <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col gap-[10px]">
                            <p class="text-base font-medium text-[#333333]/70">Pendapatan Kotor</p>
                            <h3 class="text-2xl font-bold text-[#333333]">Rp
                                {{ number_format($grossRevenue, 0, ',', '.') }}</h3>
                        </div>
                        <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                            <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                        <span
                            class="{{ $diffStats['grossRevenue']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $diffStats['grossRevenue']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['grossRevenue']['percentage'] }}%
                        </span>
                        <span
                            class="{{ $diffStats['grossRevenue']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ({{ $diffStats['grossRevenue']['diff'] >= 0 ? '+' : '' }}Rp
                            {{ number_format($diffStats['grossRevenue']['diff'], 0, ',', '.') }})
                        </span>
                    </div>
                </div>

                {{-- Potongan Harga --}}
                <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col gap-[10px]">
                            <p class="text-base font-medium text-[#333333]/70">Potongan Harga</p>
                            <h3 class="text-2xl font-bold text-[#333333]">Rp
                                {{ number_format($discountTotal, 0, ',', '.') }}</h3>
                        </div>
                        <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                            <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7zm11.77 8.27L13 19.54l-4.27-4.27A2.52 2.52 0 0 1 8 13.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0c0 .69-.28 1.32-.73 1.77z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                        <span class="{{ $diffStats['discount']['diff'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $diffStats['discount']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['discount']['percentage'] }}%
                        </span>
                        <span class="{{ $diffStats['discount']['diff'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            ({{ $diffStats['discount']['diff'] >= 0 ? '+' : '' }}Rp
                            {{ number_format($diffStats['discount']['diff'], 0, ',', '.') }})
                        </span>
                    </div>
                </div>

                {{-- Refund --}}
                <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col gap-[10px]">
                            <p class="text-base font-medium text-[#333333]/70">Refund</p>
                            <h3 class="text-2xl font-bold text-[#333333]">Rp
                                {{ number_format($refundTotal, 0, ',', '.') }}</h3>
                        </div>
                        <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                            <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                        <span class="{{ $diffStats['refund']['diff'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $diffStats['refund']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['refund']['percentage'] }}%
                        </span>
                        <span class="{{ $diffStats['refund']['diff'] >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            ({{ $diffStats['refund']['diff'] >= 0 ? '+' : '' }}Rp
                            {{ number_format($diffStats['refund']['diff'], 0, ',', '.') }})
                        </span>
                    </div>
                </div>
            </div>

            {{-- Right Side: Line Chart --}}
            <div class="lg:col-span-2 bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] p-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="font-medium text-[#333333]/70">Grafik Data</p>
                    <flux:select wire:model.live="selectedChart" class="w-auto min-w-[160px]">
                        <flux:select.option value="gross">Pendapatan Kotor</flux:select.option>
                        <flux:select.option value="discount">Potongan Harga</flux:select.option>
                        <flux:select.option value="refund">Refund</flux:select.option>
                        <flux:select.option value="net">Pendapatan Bersih</flux:select.option>
                        <flux:select.option value="profit">Keuntungan</flux:select.option>
                    </flux:select>
                </div>
                <div class="relative" style="min-height: 280px;">
                    @if (empty($chartRevenue) || array_sum($chartRevenue) == 0)
                        <div class="absolute inset-0 flex items-center justify-center bg-[#fafafa]/80 z-10 rounded-lg">
                            <div class="text-center text-[#666666]">
                                <flux:icon icon="chart-bar" class="size-10 mx-auto mb-2" />
                                <p class="font-medium">Tidak ada data</p>
                            </div>
                        </div>
                    @endif
                    <canvas id="revenueLineChart" class="w-full"></canvas>
                </div>
            </div>
        </div>

        {{-- ==================== PART 4: Pendapatan Cards + Pie Charts ==================== --}}
        {{-- Pendapatan Bersih & Keuntungan Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Pendapatan Bersih --}}
            <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-[10px]">
                        <p class="text-base font-medium text-[#333333]/70">Pendapatan Bersih</p>
                        <h3 class="text-2xl font-bold text-[#333333]">Rp
                            {{ number_format($netRevenue, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                        <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                        </svg>
                    </div>
                </div>
                <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                    <span class="{{ $diffStats['netRevenue']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diffStats['netRevenue']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['netRevenue']['percentage'] }}%
                    </span>
                    <span class="{{ $diffStats['netRevenue']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $diffStats['netRevenue']['diff'] >= 0 ? '+' : '' }}Rp
                        {{ number_format($diffStats['netRevenue']['diff'], 0, ',', '.') }})
                    </span>
                </div>
            </div>

            {{-- Keuntungan --}}
            <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[15px]">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col gap-[10px]">
                        <p class="text-base font-medium text-[#333333]/70">Keuntungan</p>
                        <h3 class="text-2xl font-bold text-[#333333]">Rp {{ number_format($profit, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="flex items-center justify-center size-[60px] rounded-[20px]">
                        <svg class="size-[38px] text-[#74512D]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                        </svg>
                    </div>
                </div>
                <div class="flex gap-[10px] text-base text-[#333333]/70 mt-[10px]">
                    <span class="{{ $diffStats['profit']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $diffStats['profit']['diff'] >= 0 ? '+' : '' }}{{ $diffStats['profit']['percentage'] }}%
                    </span>
                    <span class="{{ $diffStats['profit']['diff'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $diffStats['profit']['diff'] >= 0 ? '+' : '' }}Rp
                        {{ number_format($diffStats['profit']['diff'], 0, ',', '.') }})
                    </span>
                </div>
            </div>
        </div>

        {{-- Pie Charts: Metode Penjualan & Metode Pembayaran --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Metode Penjualan Teratas --}}
            <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] p-6">
                <p class="font-medium text-[#333333]/70 mb-4">Metode Penjualan Teratas</p>
                <div class="relative flex items-center justify-center">
                    @if (empty($salesChartData['labels']) || count($salesChartData['labels']) == 0)
                        <div class="absolute inset-0 flex items-center justify-center bg-[#fafafa]/80 z-10 rounded-lg">
                            <div class="text-center text-[#666666]">
                                <flux:icon icon="chart-pie" class="size-10 mx-auto mb-2" />
                                <p class="font-medium">Tidak ada data</p>
                            </div>
                        </div>
                    @endif
                    <div class="w-[280px] h-[280px]">
                        <canvas id="salesMethodChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Metode Pembayaran Teratas --}}
            <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] p-6">
                <p class="font-medium text-[#333333]/70 mb-4">Metode Pembayaran Teratas</p>
                <div class="relative flex items-center justify-center">
                    @if (empty($paymentChartData['labels']) || count($paymentChartData['labels']) == 0)
                        <div class="absolute inset-0 flex items-center justify-center bg-[#fafafa]/80 z-10 rounded-lg">
                            <div class="text-center text-[#666666]">
                                <flux:icon icon="chart-pie" class="size-10 mx-auto mb-2" />
                                <p class="font-medium">Tidak ada data</p>
                            </div>
                        </div>
                    @endif
                    <div class="w-[280px] h-[280px]">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== PART 5 & 6: Tabel Rincian Penjualan ==================== --}}
        <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
                <h2 class="text-lg font-semibold text-[#333333]">Rincian Penjualan</h2>
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center bg-white border border-[#d4d4d4] rounded-[20px] px-4 py-1 min-w-[250px]">
                        <flux:icon icon="magnifying-glass" class="size-5 text-[#959595]" />
                        <input type="text" placeholder="Cari..."
                            class="ml-2 py-2 text-sm text-[#959595] bg-transparent border-none focus:outline-none w-full"
                            wire:model.live.debounce.300ms="searchReport" />
                    </div>
                    <button class="flex items-center gap-1 text-[#666666]">
                        <flux:icon icon="funnel" class="size-5" />
                        <span class="text-sm">Filter</span>
                    </button>
                </div>
            </div>

            <x-table.paginated :headers="$monthlyReportsHeaders" :paginator="$monthlyReportsPaginator" emptyMessage="Tidak ada data rincian penjualan"
                headerBg="#3f4e4f" headerText="white">
                @foreach ($monthlyReportsPaginator as $item)
                    <tr class="border-b border-[#e5e5e5] hover:bg-gray-50">
                        <td class="py-3 px-4 text-left text-[#333333]">{{ $item->waktu }}</td>
                        <td class="py-3 px-4 text-center text-[#333333]">Rp
                            {{ number_format($item->pendapatanKotor, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center text-[#333333]">Rp
                            {{ number_format($item->refund, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center text-[#333333]">Rp
                            {{ number_format($item->potonganHarga, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center text-[#333333]">Rp
                            {{ number_format($item->pendapatanBersih, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center text-[#333333]">Rp
                            {{ number_format($item->modal, 0, ',', '.') }}</td>
                        <td
                            class="py-3 px-4 text-center {{ $item->keuntungan >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($item->keuntungan, 0, ',', '.') }}
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
                let topProductsChart, revenueLineChart, salesMethodChart, paymentMethodChart;

                const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                function initTopProductsChart(labels, data) {
                    if (topProductsChart) topProductsChart.destroy();
                    const ctx = document.getElementById('topProductsChart');
                    if (!ctx) return;
                    topProductsChart = new Chart(ctx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah Produk (Pcs)',
                                data: data,
                                backgroundColor: '#3f4e4f',
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

                function initRevenueLineChart(data) {
                    if (revenueLineChart) revenueLineChart.destroy();
                    const ctx = document.getElementById('revenueLineChart');
                    if (!ctx) return;
                    revenueLineChart = new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: monthLabels,
                            datasets: [{
                                label: 'Pendapatan',
                                data: data,
                                borderColor: '#666666',
                                backgroundColor: 'rgba(76, 175, 80, 0.85)',
                                fill: true,
                                tension: 0,
                                pointBackgroundColor: '#666666',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: 'rgba(0,0,0,0.7)',
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    position: 'left',
                                    ticks: {
                                        color: 'rgba(0,0,0,0.7)',
                                        font: {
                                            size: 12
                                        },
                                        callback: function(value) {
                                            if (value >= 1000000) {
                                                return 'Rp' + (value / 1000000).toLocaleString('id-ID') + '.000.000';
                                            } else if (value >= 1000) {
                                                return 'Rp' + (value / 1000).toLocaleString('id-ID') + '.000';
                                            }
                                            return 'Rp' + value.toLocaleString('id-ID');
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                }
                            }
                        }
                    });
                }

                function initSalesMethodChart(labels, data) {
                    if (salesMethodChart) salesMethodChart.destroy();
                    const ctx = document.getElementById('salesMethodChart');
                    if (!ctx) return;
                    salesMethodChart = new Chart(ctx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: ['#3f4e4f', '#74512D', '#7c3aed', '#22c55e'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                            return context.label + ': Rp ' + context.raw.toLocaleString('id-ID') +
                                                ' (' + percentage + '%)';
                                        }
                                    }
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    },
                                    formatter: (value, ctx) => {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
                                        return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage + '%';
                                    }
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                }

                function initPaymentMethodChart(labels, data) {
                    if (paymentMethodChart) paymentMethodChart.destroy();
                    const ctx = document.getElementById('paymentMethodChart');
                    if (!ctx) return;
                    paymentMethodChart = new Chart(ctx.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                            return context.label + ': Rp ' + context.raw.toLocaleString('id-ID') +
                                                ' (' + percentage + '%)';
                                        }
                                    }
                                },
                                datalabels: {
                                    color: '#fff',
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    },
                                    formatter: (value, ctx) => {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
                                        return ctx.chart.data.labels[ctx.dataIndex] + '\n' + percentage + '%';
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
                    if (data.topProductsChartData) initTopProductsChart(data.topProductsChartData.labels, data
                        .topProductsChartData.data);
                    if (data.chartRevenue) initRevenueLineChart(data.chartRevenue);
                    if (data.salesChartData) initSalesMethodChart(data.salesChartData.labels, data.salesChartData.data);
                    if (data.paymentChartData) initPaymentMethodChart(data.paymentChartData.labels, data.paymentChartData
                        .data);
                });

                // Initialize charts on component load
                initTopProductsChart(@json($topProductsChartData['labels']), @json($topProductsChartData['data']));
                initRevenueLineChart(@json($chartRevenue));
                initSalesMethodChart(@json($salesChartData['labels'] ?? []), @json($salesChartData['data'] ?? []));
                initPaymentMethodChart(@json($paymentChartData['labels'] ?? []), @json($paymentChartData['data'] ?? []));
            </script>
        @endscript
    @endif
</div>
