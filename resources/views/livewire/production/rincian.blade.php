<div>
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-[30px]">
        <div class="flex items-center gap-[15px]">
            <flux:button variant="secondary" icon="arrow-left" href="{{ route('produksi') }}" wire:navigate
                class="bg-[#313131] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-[5px] px-[25px] py-[10px] no-underline">
                <span
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f6f6f6; white-space: nowrap;">Kembali</span>
            </flux:button>
            <h1
                style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; white-space: nowrap;">
                Rincian Produksi</h1>
        </div>
        <flux:button variant="secondary" wire:click="riwayatPembaruan">
            Riwayat Pembaruan
        </flux:button>
    </div>

    {{-- Main Content Container --}}
    <div class="flex flex-col gap-[50px] items-end">
        <div class="flex flex-col gap-[30px] w-full">
            {{-- Production Information Card --}}
            <div
                class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] flex flex-col gap-[30px]">
                {{-- Production Number and Status --}}
                <div class="flex items-center justify-between w-full">
                    <p
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 30px; color: #666666; white-space: nowrap;">
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
                <div class="flex items-center w-full">
                    <div class="flex items-center gap-[34px]">
                        @if ($production->method == 'siap-beli')
                            {{-- Tanggal Pembuatan Rencana --}}
                            <div class="flex flex-col gap-[5px]">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; white-space: nowrap;">
                                    Tanggal Pembuatan Rencana</p>
                                <div class="flex items-start gap-[10px]"
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666;">
                                    @if ($date)
                                        <p>{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</p>
                                        <p>{{ \Carbon\Carbon::parse($date)->format('H:i') }}</p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                            {{-- Tanggal Pelaksanaan Produksi --}}
                            <div class="flex flex-col gap-[5px]">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; white-space: nowrap;">
                                    Tanggal Pelaksanaan Produksi</p>
                                <div class="flex items-start gap-[10px]"
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666;">
                                    @if ($production->start_date)
                                        <p>{{ \Carbon\Carbon::parse($production->start_date)->translatedFormat('d M Y') }}
                                        </p>
                                        <p>{{ $production->time ? \Carbon\Carbon::parse($production->time)->format('H:i') : '00:00' }}
                                        </p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Tanggal Produksi --}}
                            <div class="flex flex-col gap-[5px]">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; white-space: nowrap;">
                                    Tanggal Produksi</p>
                                <div class="flex items-start gap-[10px]"
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666;">
                                    @if ($production->start_date)
                                        <p>{{ \Carbon\Carbon::parse($production->start_date)->translatedFormat('d M Y') }}
                                        </p>
                                        <p>{{ $production->time ? \Carbon\Carbon::parse($production->time)->format('H:i') : '00:00' }}
                                        </p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                            {{-- Tanggal Ambil Pesanan --}}
                            <div class="flex flex-col gap-[5px]">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; white-space: nowrap;">
                                    Tanggal Ambil Pesanan</p>
                                <div class="flex items-start gap-[10px]"
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666;">
                                    @if ($production->transaction && $production->transaction->date)
                                        <p>{{ \Carbon\Carbon::parse($production->transaction->date)->translatedFormat('d M Y') }}
                                        </p>
                                        <p>{{ $production->transaction->time ? \Carbon\Carbon::parse($production->transaction->time)->format('H:i') : '00:00' }}
                                        </p>
                                    @else
                                        <p>-</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        {{-- Tanggal Produksi Selesai --}}
                        <div class="flex flex-col gap-[5px]"
                            style="font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666;">
                            <p style="font-weight: 500; white-space: nowrap;">Tanggal Produksi Selesai</p>
                            <p style="font-weight: 400; white-space: nowrap;">
                                @if ($end_date)
                                    {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex-1 flex items-center justify-end gap-[34px]">
                        @if ($production->method != 'siap-beli')
                            {{-- ID Pesanan --}}
                            <div class="flex flex-col gap-[5px] items-end">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; white-space: nowrap;">
                                    ID Pesanan</p>
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666; white-space: nowrap;">
                                    {{ $production->transaction->invoice_number ?? '-' }}</p>
                            </div>
                        @endif
                        {{-- Koki --}}
                        <div class="flex flex-col gap-[5px] items-end"
                            style="font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666;">
                            <p style="font-weight: 500; white-space: nowrap;">Koki</p>
                            <p style="font-weight: 400; white-space: nowrap;">
                                {{ $production->workers->count() > 0 ? $production->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="flex flex-col gap-[5px] h-[45px] justify-center w-full">
                    <div class="relative w-full h-full">
                        <div class="absolute inset-0 bg-[#eaeaea] rounded-[5px]"></div>
                        <div class="absolute inset-0 bg-[#49aa59] rounded-[5px]"
                            style="width: {{ number_format($percentage, 0) }}%;"></div>
                    </div>
                    <div class="flex items-center justify-center w-full">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #525252; white-space: nowrap;">
                            {{ number_format($percentage, 0) }}% ({{ $total_quantity_get }} <span
                                style="font-weight: 400;">dari</span> {{ $total_quantity_plan }})
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
                class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] flex flex-col gap-[30px]">
                <div class="flex flex-col gap-[20px] w-full">
                    <p
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666;">
                        Daftar Produk</p>

                    <div class="flex flex-col w-full">
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

                {{-- Catatan Produksi Section --}}
                <div class="flex flex-col gap-[20px] w-full">
                    <div class="flex items-center justify-between w-full">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666;">
                            Catatan Produksi</p>
                        <button wire:click="buatCatatan"
                            class="bg-[#666666] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-[5px] px-[25px] py-[10px]">
                            <flux:icon.pencil class="size-6 text-[#f6f6f6]" />
                            <span
                                style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f6f6f6; white-space: nowrap;">Buat
                                Catatan</span>
                        </button>
                    </div>
                    <div
                        class="bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] h-[120px] w-full">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666; text-align: justify;">
                            {{ $production->note ?: 'Tidak ada catatan' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        @if ($is_start && !$is_finish)
            <div class="flex flex-wrap gap-[30px] items-center justify-end w-full">
                <flux:button variant="outline" icon="check-circle" wire:click="finish"
                    class="bg-[#fafafa] border border-[#3f4e4f] rounded-[15px] flex items-center gap-[5px] px-[25px] py-[10px]">
                    <span
                        style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #3f4e4f; white-space: nowrap;">Selesaikan
                        Produksi</span>
                </flux:button>
                @if ($total_quantity_get < $total_quantity_plan)
                    <flux:button href="{{ route('produksi.mulai', $production->id) }}" variant="secondary"
                        icon="clipboard-document-list" wire:navigate
                        class="bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-[5px] px-[25px] py-[10px] no-underline">
                        <span
                            style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f8f4e1; white-space: nowrap;">Dapatkan
                            Produk</span>
                    </flux:button>
                @endif
            </div>
        @elseif (!$is_start && !$is_finish)
            <div class="flex gap-[30px] items-center justify-end w-full">
                <button wire:click="confirmDelete"
                    class="bg-[#ff0000] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-[5px] px-[25px] py-[10px]">
                    <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3 5H17M8 9V15M12 9V15M4 5L5 17C5 17.5 5.5 18 6 18H14C14.5 18 15 17.5 15 17L16 5M7 5V3C7 2.5 7.5 2 8 2H12C12.5 2 13 2.5 13 3V5"
                            stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                @if ($production->method != 'siap-beli' && $total_quantity_get <= 0)
                    <a href="{{ route('produksi.edit-produksi-pesanan', $production->id) }}" wire:navigate
                        class="bg-[#666666] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-[5px] px-[25px] py-[10px] no-underline">
                        <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 17H17M14 4L16 6L10 12H8V10L14 4Z" stroke="#ffffff" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span
                            style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f6f6f6; white-space: nowrap;">Ubah
                            Rencana Produksi</span>
                    </a>
                @endif
                <button wire:click="start"
                    class="bg-[#3f4e4f] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-[5px] px-[25px] py-[10px]">
                    <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2C10 2 6 4 6 8V11L4 13V14H16V13L14 11V8C14 4 10 2 10 2Z" fill="#f8f4e1" />
                    </svg>
                    <span
                        style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f8f4e1; white-space: nowrap;">Mulai
                        Produksi</span>
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
