<div class="space-y-5">
    {{-- Back button and title --}}
    <div class="flex items-center gap-[15px]">
        <flux:button variant="secondary" icon="arrow-left" href="{{ route('laporan-kasir') }}" wire:navigate>
            Kembali
        </flux:button>
        <h1 class="text-[20px] font-semibold text-[#666666]">Unduh Laporan Kasir</h1>
    </div>

    {{-- Form Container --}}
    <div class="bg-[#fafafa] rounded-[15px] px-[30px] py-[25px]">
        <div class="flex flex-col gap-[30px] pb-[50px]">
            {{-- Report Content Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Pilih Isi Laporan Kasir <span class="text-[#eb5757]">*</span>
                </label>
                <flux:select wire:model.live="reportContent"
                    class="!bg-[#fafafa] !border-[#d4d4d4] !rounded-[15px] !px-[20px] !py-[10px]">
                    <option value="" hidden>Pilih Isi Laporan Kasir</option>
                    <option value="sesi">Sesi Penjualan</option>
                    <option value="customer">Pelanggan Baru</option>
                    <option value="transaksi">Transaksi Pembayaran</option>
                    <option value="terjual">Produk Terjual</option>
                    <option value="keuangan">Keuangan</option>
                </flux:select>
            </div>

            {{-- Date Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Tanggal Laporan <span class="text-[#eb5757]">*</span>
                </label>
                <div class="relative" x-data="{ open: @entangle('showCalendar') }" @click.away="open = false">
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
                                    <input type="text" wire:model="customStartDate" placeholder="01 Dec 2025"
                                        x-data="{ displayValue: '' }" x-init="if ($wire.customStartDate) {
                                            const date = new Date($wire.customStartDate);
                                            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                            displayValue = date.getDate().toString().padStart(2, '0') + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
                                            $el.value = displayValue;
                                        }
                                        new Pikaday({
                                            field: $el,
                                            format: 'DD MMM YYYY',
                                            toString(date) {
                                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                return date.getDate().toString().padStart(2, '0') + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
                                            },
                                            parse(dateString) {
                                                const parts = dateString.split(' ');
                                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                const monthIndex = months.indexOf(parts[1]);
                                                return new Date(parts[2], monthIndex, parts[0]);
                                            },
                                            onSelect: function(date) {
                                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                displayValue = date.getDate().toString().padStart(2, '0') + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
                                                $el.value = displayValue;
                                                $wire.set('customStartDate', date.getFullYear() + '-' +
                                                    (date.getMonth() + 1).toString().padStart(2, '0') + '-' +
                                                    date.getDate().toString().padStart(2, '0'));
                                            }
                                        });"
                                        class="w-full px-4 py-2 border border-[#d4d4d4] rounded-[10px] text-[#666666] focus:outline-none focus:ring-2 focus:ring-[#3f4e4f]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#666666] mb-2">Ke</label>
                                    <input type="text" wire:model="customEndDate" placeholder="31 Dec 2025"
                                        x-data="{ displayValue: '' }" x-init="if ($wire.customEndDate) {
                                            const date = new Date($wire.customEndDate);
                                            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                            displayValue = date.getDate().toString().padStart(2, '0') + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
                                            $el.value = displayValue;
                                        }
                                        new Pikaday({
                                            field: $el,
                                            format: 'DD MMM YYYY',
                                            toString(date) {
                                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                return date.getDate().toString().padStart(2, '0') + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
                                            },
                                            parse(dateString) {
                                                const parts = dateString.split(' ');
                                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                const monthIndex = months.indexOf(parts[1]);
                                                return new Date(parts[2], monthIndex, parts[0]);
                                            },
                                            onSelect: function(date) {
                                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                displayValue = date.getDate().toString().padStart(2, '0') + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
                                                $el.value = displayValue;
                                                $wire.set('customEndDate', date.getFullYear() + '-' +
                                                    (date.getMonth() + 1).toString().padStart(2, '0') + '-' +
                                                    date.getDate().toString().padStart(2, '0'));
                                            }
                                        });"
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
                            @php
                                $start = \Carbon\Carbon::parse($currentMonth)->startOfMonth()->startOfWeek();
                                $end = \Carbon\Carbon::parse($currentMonth)->endOfMonth()->endOfWeek();
                                $days = [];
                                for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                                    $days[] = $date->copy();
                                }
                            @endphp

                            <div class="grid grid-cols-7 gap-2">
                                @foreach ($days as $day)
                                    @php
                                        $isToday = $day->isToday();
                                        $isCurrentMonth = $day->isSameMonth(\Carbon\Carbon::parse($currentMonth));
                                        $isSelected = $day->isSameDay(\Carbon\Carbon::parse($selectedDate));
                                    @endphp
                                    <button type="button" wire:click="selectDate('{{ $day->toDateString() }}')"
                                        class="size-[35px] rounded-[6px] text-sm flex items-center justify-center transition-colors
                                        {{ $isSelected
                                            ? 'bg-[#3f4e4f] text-white font-semibold'
                                            : ($isToday
                                                ? 'bg-blue-100 text-blue-600 font-medium'
                                                : ($isCurrentMonth
                                                    ? 'text-[#666666] hover:bg-gray-100'
                                                    : 'text-[#d4d4d4]')) }}">
                                        {{ $day->format('j') }}
                                    </button>
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
                    class="!bg-[#fafafa] !border-[#adadad] !rounded-[15px] !px-[20px] !py-[10px]">
                    <option value="semua">Semua Pekerja</option>
                    @foreach ($workers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            {{-- Sale Method Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Metode Penjualan <span class="text-[#eb5757]">*</span>
                </label>
                <flux:select wire:model.live="selectedMethod"
                    class="!bg-[#fafafa] !border-[#adadad] !rounded-[15px] !px-[20px] !py-[10px]">
                    <option value="semua">Semua Metode Penjualan</option>
                    <option value="pesanan-reguler">Pesanan Reguler</option>
                    <option value="pesanan-kotak">Pesanan Kotak</option>
                    <option value="siap-beli">Siap Saji</option>
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Export Buttons --}}
    <div class="flex gap-[10px] justify-end">
        <flux:button
            onclick="window.open('{{ route('laporan-kasir.pdf') }}?reportContent={{ $reportContent }}&filterPeriod={{ $filterPeriod }}&selectedDate={{ $selectedDate }}&customStartDate={{ $customStartDate }}&customEndDate={{ $customEndDate }}&selectedWorker={{ $selectedWorker }}&selectedMethod={{ $selectedMethod }}', '_blank')"
            class="!bg-[#eb5757] hover:!bg-[#d94444] !text-[#f6f6f6] !rounded-[15px] !px-[25px] !py-[10px] !font-semibold !shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon icon="document" class="size-5" />
            Unduh PDF
        </flux:button>
        <flux:button
            onclick="window.open('{{ route('laporan-kasir.excel') }}?reportContent={{ $reportContent }}&filterPeriod={{ $filterPeriod }}&selectedDate={{ $selectedDate }}&customStartDate={{ $customStartDate }}&customEndDate={{ $customEndDate }}&selectedWorker={{ $selectedWorker }}&selectedMethod={{ $selectedMethod }}', '_blank')"
            class="!bg-[#56c568] hover:!bg-[#48b05a] !text-[#f6f6f6] !rounded-[15px] !px-[25px] !py-[10px] !font-semibold !shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon icon="table-cells" class="size-5" />
            Unduh Excel
        </flux:button>
    </div>
</div>
