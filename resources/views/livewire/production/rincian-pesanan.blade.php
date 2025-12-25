<div class="px-4 sm:px-[30px] py-4 sm:py-[30px]" style="background: #eaeaea; min-height: 100vh;">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-[30px] gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto sm:gap-[15px]">
            <flux:button variant="secondary" icon="arrow-left"
                href="{{ route('produksi.pesanan', ['method' => $transaction->method]) }}" wire:navigate
                class="w-full sm:w-auto flex items-center justify-center gap-[5px] px-[25px] py-[10px] bg-[#313131] rounded-[15px] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] no-underline">
                <span
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f6f6f6; white-space: nowrap;">Kembali</span>
            </flux:button>
            <h1 class="text-center sm:text-left"
                style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin: 0;">
                Rincian Pesanan</h1>
        </div>
    </div>

    {{-- Main Content Container --}}
    <div class="flex flex-col gap-[30px] sm:gap-[50px] items-end">
        <div class="flex flex-col gap-[30px] w-full">
            {{-- Order Information Card --}}
            <div
                class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] p-4 sm:p-[30px] flex flex-col gap-[30px]">
                {{-- Invoice Number and Status --}}
                <div class="flex flex-col sm:flex-row items-center justify-between w-full gap-4">
                    <p class="text-center sm:text-left"
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: clamp(20px, 5vw, 30px); color: #666666; margin: 0; word-break: break-word;">
                        {{ $transaction->invoice_number }}</p>
                    @php
                        $statusColors = [
                            'Belum Diproses' => ['bg' => '#adadad', 'text' => '#fafafa'],
                            'Sedang Diproses' => ['bg' => '#ffa500', 'text' => '#fafafa'],
                            'Selesai' => ['bg' => '#56c568', 'text' => '#fafafa'],
                            'Gagal' => ['bg' => '#ff0000', 'text' => '#fafafa'],
                            'Draft' => ['bg' => '#cccccc', 'text' => '#666666'],
                        ];
                        $statusColor = $statusColors[$transaction->status] ?? ['bg' => '#adadad', 'text' => '#fafafa'];
                    @endphp
                    <div style="border-radius: 30px; padding: 8px 20px; background-color: {{ $statusColor['bg'] }};">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; color: {{ $statusColor['text'] }}; white-space: nowrap; margin: 0;">
                            {{ $transaction->status }}</p>
                    </div>
                </div>

                {{-- Date and User Information --}}
                <div class="flex flex-col lg:flex-row gap-6 w-full justify-between">
                    <div class="flex flex-col md:flex-row flex-wrap gap-6 md:gap-[30px] w-full">
                        {{-- Tanggal Pesanan Masuk --}}
                        <div class="flex flex-col gap-[5px] min-w-[200px] flex-1">
                            <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                                Tanggal Pesanan Masuk</p>
                            <div class="flex flex-wrap gap-[10px] font-['Montserrat'] font-normal text-[16px] text-[#666666]">
                                @if ($transaction->start_date)
                                    <p class="m-0">
                                        {{ \Carbon\Carbon::parse($transaction->start_date)->translatedFormat('d M Y') }}
                                    </p>
                                    <p class="m-0">
                                        {{ \Carbon\Carbon::parse($transaction->start_date)->format('H:i') }}</p>
                                @else
                                    <p class="m-0">-</p>
                                @endif
                            </div>
                        </div>
                        {{-- Tanggal Ambil Pesanan --}}
                        <div class="flex flex-col gap-[5px] min-w-[200px] flex-1">
                            <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                                Tanggal Ambil Pesanan</p>
                            <div class="flex flex-wrap gap-[10px] font-['Montserrat'] font-normal text-[16px] text-[#666666]">
                                @if ($transaction->date)
                                    <p class="m-0">
                                        {{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d M Y') }}</p>
                                    <p class="m-0">
                                        {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '00:00' }}
                                    </p>
                                @else
                                    <p class="m-0">-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Pembeli and Kasir --}}
                    <div class="flex flex-col sm:flex-row flex-wrap gap-6 sm:gap-[30px] w-full justify-between lg:justify-end lg:text-right">
                        <div class="flex flex-col gap-[5px] font-['Montserrat'] text-[16px] text-[#666666] min-w-[150px]">
                            <p class="font-medium m-0">Pembeli</p>
                            <p class="font-normal m-0 break-words">
                                {{ $transaction->name ?? '-' }}</p>
                        </div>
                        <div class="flex flex-col gap-[5px] font-['Montserrat'] text-[16px] text-[#666666] min-w-[150px]">
                            <p class="font-medium m-0">Kasir</p>
                            <p class="font-normal m-0 break-words">
                                {{ $transaction->user->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Catatan Pesanan Section --}}
                <div class="flex flex-col gap-[20px]">
                    <p class="font-['Montserrat'] font-medium text-[18px] text-[#666666] m-0">
                        Catatan Pesanan</p>
                    <div class="bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] p-4 sm:px-[20px] sm:py-[10px] min-h-[120px]">
                        <p class="font-['Montserrat'] font-normal text-[16px] text-[#666666] text-justify m-0 break-words">
                            {{ $transaction->note ?: 'Tidak ada catatan' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Product Table Card --}}
            <div
                class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] p-4 sm:p-[30px] flex flex-col gap-[20px]">
                <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                    Daftar Produk</p>

                <div style="width: 100%; overflow-x: auto;">
                    <div style="display: flex; flex-direction: column; min-width: 600px;">
                        {{-- Table Header --}}
                        <div
                            style="display: flex; align-items: center; width: 100%; border-radius: 15px 15px 0 0; overflow: hidden;">
                            <div
                                style="flex: 1; background: #3f4e4f; padding: 21px 25px; height: 60px; display: flex; align-items: center;">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; white-space: nowrap; margin: 0;">
                                    Produk</p>
                            </div>
                            <div
                                style="width: 180px; background: #3f4e4f; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end;">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; white-space: nowrap; text-align: right; margin: 0;">
                                    Jumlah Pesanan</p>
                            </div>
                        </div>

                        {{-- Table Body --}}
                        <div style="background: #fafafa; display: flex; flex-direction: column; width: 100%;">
                            @foreach ($details as $detail)
                                <div style="display: flex; align-items: center; width: 100%;">
                                    <div
                                        style="flex: 1; background: #fafafa; border-bottom: 1px solid #d4d4d4; padding: 0 25px; height: 60px; display: flex; align-items: center;">
                                        <p
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%; margin: 0;">
                                            {{ $detail->product->name ?? 'Produk Tidak Ditemukan' }}</p>
                                    </div>
                                    <div
                                        style="width: 180px; background: #fafafa; border-bottom: 1px solid #d4d4d4; padding: 0 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end;">
                                        <p
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; white-space: nowrap; margin: 0;">
                                            {{ $detail->quantity ?? 0 }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Table Footer --}}
                        <div
                            style="display: flex; align-items: center; width: 100%; height: 60px; border-radius: 0 0 15px 15px; overflow: hidden;">
                            <div
                                style="flex: 1; background: #eaeaea; padding: 0 25px; height: 100%; display: flex; align-items: center;">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; white-space: nowrap; margin: 0;">
                                    Total</p>
                            </div>
                            <div
                                style="width: 180px; background: #eaeaea; padding: 0 25px; height: 100%; display: flex; align-items: center; justify-content: flex-end;">
                                <p
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right; white-space: nowrap; margin: 0;">
                                    {{ $details->sum('quantity') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mulai Produksi Button --}}
        @if (empty($transaction->production))
            <flux:button wire:click="start" variant="secondary" icon="chef-hat" class="w-full sm:w-auto">
                Mulai Produksi
            </flux:button>
        @endif
    </div>

    {{-- Modal Riwayat Pembaruan --}}
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <h1 size="lg">Riwayat Pembaruan {{ $transaction->invoice_number }}</h1>
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
</div>
