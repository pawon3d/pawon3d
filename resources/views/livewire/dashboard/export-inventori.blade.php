<div class="space-y-5">
    {{-- Back button and title --}}
    <div class="flex items-center gap-[15px]">
        <flux:button variant="secondary" icon="arrow-left" href="{{ route('laporan-inventori') }}" wire:navigate>
            Kembali
        </flux:button>
        <h1 class="text-[20px] font-semibold text-[#666666]">Unduh Laporan Inventori</h1>
    </div>

    {{-- Form Container --}}
    <div class="bg-[#fafafa] rounded-[15px] px-[30px] py-[25px]">
        <div class="flex flex-col gap-[30px] pb-[50px]">
            {{-- Report Content Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Pilih Isi Laporan Inventori <span class="text-[#eb5757]">*</span>
                </label>
                <flux:select wire:model.live="reportContent"
                    class="!bg-[#fafafa] !border-[#d4d4d4] !rounded-[15px] !px-[20px] !py-[10px]">
                    <option value="">Pilih Isi Laporan Inventori</option>
                    <option value="belanja">Sesi Belanja</option>
                    <option value="persediaan">Nilai Persediaan</option>
                    <option value="alur">Alur Persediaan</option>
                </flux:select>
            </div>

            {{-- Date Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Tanggal Laporan <span class="text-[#eb5757]">*</span>
                </label>

                {{-- Custom Calendar Dropdown --}}
                <div class="relative" x-data="{ open: @entangle('showCalendar') }" @click.away="open = false">
                    <button type="button" @click="open = !open"
                        class="w-full bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] flex items-center justify-between cursor-pointer hover:border-[#666666] transition-colors">
                        <span class="text-[#666666] text-base">
                            @if ($filterPeriod === 'Custom')
                                @if (!empty($customStartDate) && !empty($customEndDate))
                                    {{ \Carbon\Carbon::parse($customStartDate)->translatedFormat('d F Y') }} —
                                    {{ \Carbon\Carbon::parse($customEndDate)->translatedFormat('d F Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($customStartDate ?? $selectedDate)->translatedFormat('d F Y') }}
                                @endif
                            @elseif ($filterPeriod === 'Tahun')
                                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('Y') }}
                            @elseif ($filterPeriod === 'Bulan')
                                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('F Y') }}
                            @elseif ($filterPeriod === 'Minggu')
                                @php
                                    $startOfWeek = \Carbon\Carbon::parse($selectedDate)->startOfWeek();
                                    $endOfWeek = \Carbon\Carbon::parse($selectedDate)->endOfWeek();
                                @endphp
                                {{ $startOfWeek->translatedFormat('d F Y') }} -
                                {{ $endOfWeek->translatedFormat('d F Y') }}
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

                        @if ($filterPeriod === 'Custom')
                            {{-- Custom Date Range Picker --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#666666] mb-2">Dari</label>
                                    <input type="date" wire:model.live="customStartDate"
                                        class="w-full px-4 py-2 border border-[#d4d4d4] rounded-[10px] text-[#666666] focus:outline-none focus:ring-2 focus:ring-[#3f4e4f]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#666666] mb-2">Ke</label>
                                    <input type="date" wire:model.live="customEndDate"
                                        class="w-full px-4 py-2 border border-[#d4d4d4] rounded-[10px] text-[#666666] focus:outline-none focus:ring-2 focus:ring-[#3f4e4f]">
                                </div>
                            </div>
                        @elseif ($filterPeriod === 'Tahun')
                            {{-- Year Grid --}}
                            <div class="text-center mb-4">
                                <p class="text-base font-medium text-[#666666]">
                                    {{ \Carbon\Carbon::parse($currentMonth)->year }}
                                </p>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                @for ($year = \Carbon\Carbon::parse($currentMonth)->year; $year >= \Carbon\Carbon::parse($currentMonth)->year - 11; $year--)
                                    <button type="button" wire:click="selectYear({{ $year }})"
                                        class="px-4 py-3 text-sm rounded-[10px] transition-colors
                                        {{ \Carbon\Carbon::parse($selectedDate)->year == $year
                                            ? 'bg-[#3f4e4f] text-white font-medium'
                                            : 'bg-[#fafafa] text-[#666666] border border-[#d4d4d4] hover:bg-gray-100' }}">
                                        {{ $year }}
                                    </button>
                                @endfor
                            </div>
                        @elseif ($filterPeriod === 'Bulan')
                            {{-- Month Grid for Bulan --}}
                            <div class="flex items-center justify-between mb-4">
                                <button type="button" wire:click="previousYear"
                                    class="size-[34px] flex items-center justify-center border border-[#d4d4d4] rounded-[5px] bg-white hover:bg-gray-50 transition-colors">
                                    <flux:icon icon="chevron-left" class="size-3 text-[#666666]" />
                                </button>
                                <div class="text-center">
                                    <p class="text-base font-medium text-[#666666]">
                                        {{ \Carbon\Carbon::parse($currentMonth)->year }}
                                    </p>
                                </div>
                                <button type="button" wire:click="nextYear"
                                    class="size-[34px] flex items-center justify-center border border-[#d4d4d4] rounded-[5px] bg-white hover:bg-gray-50 transition-colors">
                                    <flux:icon icon="chevron-right" class="size-3 text-[#666666]" />
                                </button>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $monthName)
                                    @php
                                        $monthNumber = $index + 1;
                                        $isSelected =
                                            \Carbon\Carbon::parse($selectedDate)->month == $monthNumber &&
                                            \Carbon\Carbon::parse($selectedDate)->year ==
                                                \Carbon\Carbon::parse($currentMonth)->year;
                                    @endphp
                                    <button type="button" wire:click="selectMonth({{ $monthNumber }})"
                                        class="px-4 py-3 text-sm rounded-[10px] transition-colors
                                        {{ $isSelected
                                            ? 'bg-[#3f4e4f] text-white font-medium'
                                            : 'bg-[#fafafa] text-[#666666] border border-[#d4d4d4] hover:bg-gray-100' }}">
                                        {{ $monthName }}
                                    </button>
                                @endforeach
                            </div>
                        @else
                            {{-- Day Calendar Grid (Hari & Minggu) --}}
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
                                    <div class="grid grid-cols-7 gap-x-5 gap-y-4">
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
                                                }
                                            @endphp

                                            <button type="button" wire:click="selectDate('{{ $date }}')"
                                                class="{{ $base }} {{ $bgClass }} {{ $textClass }} {{ $radiusClass }} {{ !$info['isSelected'] ? 'hover:bg-gray-100' : '' }}">
                                                <div class="flex flex-col items-center">
                                                    <span>{{ $info['day'] }}</span>
                                                    <div class="mt-1 flex items-center gap-1">
                                                        @if (!empty($info['hasExpense']))
                                                            <span title="{{ $info['expenseCount'] ?? 0 }} sesi belanja"
                                                                class="w-1.5 h-1.5 rounded-full bg-[#f59e0b]"></span>
                                                        @endif
                                                        @if (!empty($info['hasData']))
                                                            <span title="{{ $info['productionCount'] ?? 0 }} produksi"
                                                                class="w-1.5 h-1.5 rounded-full bg-[#4caf50]"></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Worker Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Pekerja <span class="text-[#eb5757]">*</span>
                </label>
                <flux:select wire:model.live="selectedWorker"
                    class="!bg-[#fafafa] !border-[#d4d4d4] !rounded-[15px] !px-[20px] !py-[10px]">
                    @can('manajemen.pembayaran.kelola')
                        <option value="semua">Semua Pekerja</option>
                    @endcan
                    @can('manajemen.pembayaran.kelola')
                        @foreach ($workers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    @else
                        <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>
                    @endcan
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Export Buttons --}}
    <div class="flex gap-[10px] justify-end">
        <flux:button
            onclick="window.open('{{ route('laporan-inventori.pdf') }}?reportContent={{ $reportContent }}&filterPeriod={{ $filterPeriod }}&selectedDate={{ $selectedDate }}&customStartDate={{ $customStartDate }}&customEndDate={{ $customEndDate }}&selectedWorker={{ $selectedWorker }}', '_blank')"
            class="!bg-[#eb5757] hover:!bg-[#d94444] !text-[#f6f6f6] !rounded-[15px] !px-[25px] !py-[10px] !font-semibold !shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon icon="document" class="size-5" />
            Unduh PDF
        </flux:button>
        <flux:button
            onclick="window.open('{{ route('laporan-inventori.excel') }}?reportContent={{ $reportContent }}&filterPeriod={{ $filterPeriod }}&selectedDate={{ $selectedDate }}&customStartDate={{ $customStartDate }}&customEndDate={{ $customEndDate }}&selectedWorker={{ $selectedWorker }}', '_blank')"
            class="!bg-[#56c568] hover:!bg-[#48b05a] !text-[#f6f6f6] !rounded-[15px] !px-[25px] !py-[10px] !font-semibold !shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon icon="table-cells" class="size-5" />
            Unduh Excel
        </flux:button>
    </div>
</div>
