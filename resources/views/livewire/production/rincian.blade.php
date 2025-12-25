<div class="px-4 sm:px-[30px] py-4 sm:py-[30px]" style="background: #eaeaea; min-height: 100vh;">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-[30px] gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto sm:gap-[15px]">
            <flux:button variant="secondary" icon="arrow-left" href="{{ route('produksi') }}" wire:navigate
                class="w-full sm:w-auto bg-[#313131] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center justify-center gap-[5px] px-[25px] py-[10px] no-underline">
                <span
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f6f6f6; white-space: nowrap;">Kembali</span>
            </flux:button>
            <h1 class="text-[18px] sm:text-[20px] font-semibold text-[#666666] text-center sm:text-left"
                style="font-family: 'Montserrat', sans-serif;">
                Rincian Produksi</h1>
        </div>
        <div class="w-full sm:w-auto">
            <flux:button variant="secondary" wire:click="riwayatPembaruan" class="w-full">
                Riwayat Pembaruan
            </flux:button>
        </div>
    </div>

    {{-- Main Content Container --}}
    <div class="flex flex-col gap-[30px] sm:gap-[50px] items-end">
        <div class="flex flex-col gap-[30px] w-full">
            {{-- Production Information Card --}}
            <div
                class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-4 sm:px-[30px] py-6 sm:py-[25px] flex flex-col gap-[30px]">
                {{-- Production Number and Status --}}
                <div class="flex flex-col sm:flex-row items-center justify-between w-full gap-4">
                    <p class="text-[24px] sm:text-[30px] font-medium text-[#666666] text-center sm:text-left"
                        style="font-family: 'Montserrat', sans-serif;">
                        {{ $production->production_number }}</p>
                    @php
                        $statusColors = [
                            'Belum Diproses' => ['bg' => '#adadad', 'text' => '#fafafa'],
                            'Sedang Diproses' => ['bg' => '#ffc400', 'text' => '#fafafa'],
                            'Selesai' => ['bg' => '#56c568', 'text' => '#fafafa'],
                            'Gagal' => ['bg' => '#ff0000', 'text' => '#fafafa'],
                            'Draft' => ['bg' => '#cccccc', 'text' => '#666666'],
                        ];
                        $statusColor = $statusColors[$status] ?? ['bg' => '#adadad', 'text' => '#fafafa'];
                    @endphp
                    <div class="rounded-[30px] px-[20px] py-[8px]" style="background-color: {{ $statusColor['bg'] }};">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; color: {{ $statusColor['text'] }}; white-space: nowrap;">
                            {{ $status }}</p>
                    </div>
                </div>

                {{-- Date and User Information --}}
                <div class="flex flex-col xl:flex-row xl:items-center w-full gap-6 sm:gap-[34px]">
                    <div class="flex flex-col md:flex-row md:items-start gap-6 md:gap-[34px]">
                        @if ($production->method == 'siap-beli')
                            {{-- Tanggal Pembuatan Rencana --}}
                            <div class="flex flex-col gap-[5px]">
                                <p class="font-medium text-[16px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                                    Tanggal Pembuatan Rencana</p>
                                <div class="flex items-center gap-[10px] font-normal text-[16px] text-[#666666]"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    @if ($date)
                                        <span>{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</span>
                                        <span>{{ \Carbon\Carbon::parse($date)->format('H:i') }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                            {{-- Tanggal Pelaksanaan Produksi --}}
                            <div class="flex flex-col gap-[5px]">
                                <p class="font-medium text-[16px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                                    Tanggal Pelaksanaan Produksi</p>
                                <div class="flex items-center gap-[10px] font-normal text-[16px] text-[#666666]"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    @if ($production->start_date)
                                        <span>{{ \Carbon\Carbon::parse($production->start_date)->translatedFormat('d M Y') }}</span>
                                        <span>{{ $production->time ? \Carbon\Carbon::parse($production->time)->format('H:i') : '00:00' }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Tanggal Produksi --}}
                            <div class="flex flex-col gap-[5px]">
                                <p class="font-medium text-[16px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                                    Tanggal Produksi</p>
                                <div class="flex items-center gap-[10px] font-normal text-[16px] text-[#666666]"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    @if ($production->start_date)
                                        <span>{{ \Carbon\Carbon::parse($production->start_date)->translatedFormat('d M Y') }}</span>
                                        <span>{{ $production->time ? \Carbon\Carbon::parse($production->time)->format('H:i') : '00:00' }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                            {{-- Tanggal Ambil Pesanan --}}
                            <div class="flex flex-col gap-[5px]">
                                <p class="font-medium text-[16px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                                    Tanggal Ambil Pesanan</p>
                                <div class="flex items-center gap-[10px] font-normal text-[16px] text-[#666666]"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    @if ($production->transaction && $production->transaction->date)
                                        <span>{{ \Carbon\Carbon::parse($production->transaction->date)->translatedFormat('d M Y') }}</span>
                                        <span>{{ $production->transaction->time ? \Carbon\Carbon::parse($production->transaction->time)->format('H:i') : '00:00' }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        {{-- Tanggal Produksi Selesai --}}
                        <div class="flex flex-col gap-[5px] text-[16px] text-[#666666]"
                            style="font-family: 'Montserrat', sans-serif;">
                            <p class="font-medium">Tanggal Produksi Selesai</p>
                            <p class="font-normal">
                                @if ($end_date)
                                    {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col md:flex-row items-start md:items-center md:justify-end gap-6 md:gap-[34px]">
                        @if ($production->method != 'siap-beli')
                            {{-- ID Pesanan --}}
                            <div class="flex flex-col gap-[5px] md:items-end w-full md:w-auto">
                                <p class="font-medium text-[16px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                                    ID Pesanan</p>
                                <p class="font-normal text-[16px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                                    {{ $production->transaction->invoice_number ?? '-' }}</p>
                            </div>
                        @endif
                        {{-- Koki --}}
                        <div class="flex flex-col gap-[5px] md:items-end w-full md:w-auto"
                            style="font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666;">
                            <p class="font-medium">Koki</p>
                            <p class="font-normal">
                                {{ $production->workers->count() > 0 ? $production->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="flex flex-col gap-[10px] sm:gap-[5px] w-full">
                    <div class="relative w-full h-[18px] sm:h-[45px]">
                        <div class="absolute inset-0 bg-[#eaeaea] rounded-[5px]"></div>
                        <div class="absolute inset-0 bg-[#49aa59] rounded-[5px]"
                            style="width: {{ number_format($percentage, 0) }}%;"></div>
                    </div>
                    <div class="flex items-center justify-center w-full">
                        <p class="font-medium text-[12px] sm:text-[14px] text-[#525252]"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ number_format($percentage, 0) }}% ({{ $total_quantity_get }} <span
                                class="font-normal">dari</span> {{ $total_quantity_plan }})
                        </p>
                    </div>
                </div>

                {{-- Catatan Pesanan (only for non siap-beli) --}}
                @if ($production->method != 'siap-beli')
                    <div class="flex flex-col gap-[20px]">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666;">
                            Catatan Pesanan</p>
                        <div class="bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] h-[120px]">
                            <p
                                style="font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666; text-align: justify;">
                                {{ $production->transaction->note ?? 'Tidak ada catatan' }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Product Table Card --}}
            <div
                class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-4 sm:px-[30px] py-6 sm:py-[25px] flex flex-col gap-[30px]">
                <div class="flex flex-col gap-[20px] w-full">
                    <p
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666;">
                        Daftar Produk</p>

                    <div class="overflow-x-auto w-full rounded-t-[15px]">
                        <div class="flex flex-col min-w-[900px]">
                        {{-- Table Header --}}
                        <div class="flex items-center w-full rounded-t-[15px] overflow-hidden">
                            <div class="flex-1 bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; white-space: nowrap;">
                                    Produk</p>
                            </div>
                            <div
                                class="w-[135px] bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">
                                    Jumlah<br />Pesanan</p>
                            </div>
                            <div
                                class="w-[135px] bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">
                                    Selisih<br />Didapatkan</p>
                            </div>
                            <div
                                class="w-[135px] bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">
                                    Jumlah<br />Didapatkan</p>
                            </div>
                            <div
                                class="w-[165px] bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">
                                    Pcs<br />Lebih</p>
                            </div>
                            <div
                                class="w-[115px] bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">
                                    Pcs<br />Gagal</p>
                            </div>
                            <div
                                class="w-[115px] bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">
                                    Pcs<br />Lebih</p>
                            </div>
                        </div>

                        {{-- Table Body --}}
                        @foreach ($production_details as $detail)
                            <div class="flex items-center w-full">
                                <div
                                    class="flex-1 bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] h-[60px] flex items-center">
                                    <p
                                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%;">
                                        {{ $detail->product->name ?? 'Produk Tidak Ditemukan' }}</p>
                                </div>
                                <div
                                    class="w-[135px] bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] h-[60px] flex items-center justify-end">
                                    <p
                                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                        {{ $detail->quantity_plan }}</p>
                                </div>
                                <div
                                    class="w-[135px] bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] h-[60px] flex items-center justify-end">
                                    <p
                                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                        @if ($detail->quantity_get > $detail->quantity_plan)
                                            +{{ $detail->quantity_get - $detail->quantity_plan }}
                                        @else
                                            {{ $detail->quantity_get - $detail->quantity_plan }}
                                        @endif
                                    </p>
                                </div>
                                <div
                                    class="w-[135px] bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] h-[60px] flex items-center justify-end">
                                    <p
                                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                        {{ $detail->quantity_get }}</p>
                                </div>
                                <div
                                    class="w-[165px] bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] h-[60px] flex items-center justify-end">
                                    <p
                                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                        {{ $detail->cycle }}</p>
                                </div>
                                <div
                                    class="w-[115px] bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] h-[60px] flex items-center justify-end">
                                    <p
                                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                        {{ $detail->quantity_fail }}</p>
                                </div>
                                <div
                                    class="w-[115px] bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] h-[60px] flex items-center justify-end">
                                    <p
                                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                        @if ($detail->quantity_get > $detail->quantity_plan)
                                            {{ $detail->quantity_get - $detail->quantity_plan }}
                                        @else
                                            0
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        {{-- Table Footer --}}
                        <div class="flex items-center w-full h-[60px] overflow-hidden">
                            <div class="flex-1 bg-[#eaeaea] px-[25px] h-full flex items-center">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; white-space: nowrap;">
                                    Total</p>
                            </div>
                            <div class="w-[135px] bg-[#eaeaea] px-[25px] h-full flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                    {{ $production_details->sum('quantity_plan') }}</p>
                            </div>
                            <div class="w-[135px] bg-[#eaeaea] px-[25px] h-full flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                    @php $totalDiff = $production_details->sum(fn($d) => $d->quantity_get - $d->quantity_plan); @endphp
                                    @if ($totalDiff > 0)
                                        +{{ $totalDiff }}
                                    @else
                                        {{ $totalDiff }}
                                    @endif
                                </p>
                            </div>
                            <div class="w-[135px] bg-[#eaeaea] px-[25px] h-full flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                    {{ $production_details->sum('quantity_get') }}</p>
                            </div>
                            <div class="w-[165px] bg-[#eaeaea] px-[25px] h-full flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                    {{ $production_details->sum('cycle') }}</p>
                            </div>
                            <div class="w-[115px] bg-[#eaeaea] px-[25px] h-full flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                    {{ $production_details->sum('quantity_fail') }}</p>
                            </div>
                            <div class="w-[115px] bg-[#eaeaea] px-[25px] h-full flex items-center justify-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right; white-space: nowrap;">
                                    @php $totalExcess = $production_details->sum(fn($d) => max(0, $d->quantity_get - $d->quantity_plan)); @endphp
                                    {{ $totalExcess }}
                                </p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                {{-- Catatan Produksi Section --}}
                <div class="flex flex-col gap-4 w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full gap-4">
                        <p class="font-medium text-[16px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                            Catatan Produksi</p>
                        <button wire:click="buatCatatan"
                            class="w-full sm:w-auto bg-[#666666] text-[#f6f6f6] px-[25px] py-[10px] rounded-[15px] border-none cursor-pointer shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center justify-center gap-[5px]">
                            <flux:icon icon="pencil-square" class="size-5 text-[#f6f6f6]" />
                            <span class="font-['Montserrat'] font-semibold text-[16px]">Buat Catatan</span>
                        </button>
                    </div>
                    <div
                        class="bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] min-h-[120px] w-full">
                        <p class="font-normal text-[16px] text-[#666666] text-justify"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ $production->note ?: 'Tidak ada catatan' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        @if ($is_start && !$is_finish)
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-[30px] items-center justify-end w-full">
                <flux:button variant="outline" icon="check-circle" wire:click="finish"
                    class="w-full sm:w-auto bg-[#fafafa] border border-[#3f4e4f] rounded-[15px] flex items-center justify-center gap-[5px] px-[25px] py-[10px]">
                    <span class="font-semibold text-[16px] text-[#3f4e4f]" style="font-family: 'Montserrat', sans-serif;">Selesaikan Produksi</span>
                </flux:button>
                @if ($total_quantity_get < $total_quantity_plan)
                    <flux:button href="{{ route('produksi.mulai', $production->id) }}" variant="secondary"
                        icon="clipboard-document-list" wire:navigate
                        class="w-full sm:w-auto bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center justify-center gap-[5px] px-[25px] py-[10px] no-underline">
                        <span class="font-semibold text-[16px] text-[#f8f4e1]" style="font-family: 'Montserrat', sans-serif;">Dapatkan Produk</span>
                    </flux:button>
                @endif
            </div>
        @elseif (!$is_start && !$is_finish)
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-[30px] items-center justify-end w-full">
                <button wire:click="confirmDelete"
                    class="w-full sm:w-auto bg-[#ff0000] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center justify-center gap-[5px] px-[25px] py-[10px] border-none cursor-pointer">
                    <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3 5H17M8 9V15M12 9V15M4 5L5 17C5 17.5 5.5 18 6 18H14C14.5 18 15 17.5 15 17L16 5M7 5V3C7 2.5 7.5 2 8 2H12C12.5 2 13 2.5 13 3V5"
                            stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="sm:hidden font-semibold text-[16px] text-white" style="font-family: 'Montserrat', sans-serif;">Hapus Rencana</span>
                </button>
                @if ($production->method != 'siap-beli' && $total_quantity_get <= 0)
                    <a href="{{ route('produksi.edit-produksi-pesanan', $production->id) }}" wire:navigate
                        class="w-full sm:w-auto bg-[#666666] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center justify-center gap-[5px] px-[25px] py-[10px] no-underline">
                        <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 17H17M14 4L16 6L10 12H8V10L14 4Z" stroke="#ffffff" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="font-semibold text-[16px] text-[#f6f6f6]" style="font-family: 'Montserrat', sans-serif;">Ubah Rencana</span>
                    </a>
                @endif
                <button wire:click="start"
                    class="w-full sm:w-auto bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center justify-center gap-[5px] px-[25px] py-[10px] border-none cursor-pointer">
                    <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2C10 2 6 4 6 8V11L4 13V14H16V13L14 11V8C14 4 10 2 10 2Z" fill="#f8f4e1" />
                    </svg>
                    <span class="font-semibold text-[16px] text-[#f8f4e1]" style="font-family: 'Montserrat', sans-serif;">Mulai Produksi</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Modal Riwayat Pembaruan --}}
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <h1 size="lg">Riwayat Pembaruan {{ $production->production_number }}</h1>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach ($activityLogs as $log)
                    <div class="border-b py-2">
                        <div class="text-sm font-medium">{{ $log->description }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $log->causer->name ?? 'System' }} -
                            {{ $log->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </flux:modal>

    {{-- Modal Buat Catatan --}}
    <flux:modal name="catatan-produksi" class="w-full max-w-2xl" wire:model="showNoteModal">
        <form wire:submit.prevent="simpanCatatan">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Catatan Produksi</flux:heading>
                    <p class="mt-1 text-sm text-gray-500">
                        Tambahkan atau edit catatan untuk produksi {{ $production->production_number }}
                    </p>
                </div>

                <div>
                    <flux:label for="noteInput">Catatan</flux:label>
                    <flux:textarea wire:model="noteInput" id="noteInput" rows="6"
                        placeholder="Masukkan catatan produksi di sini..." class="mt-1" />
                    @error('noteInput')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showNoteModal', false)">
                        Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Simpan Catatan
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
