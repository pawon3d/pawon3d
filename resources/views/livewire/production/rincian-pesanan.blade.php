<div style="padding: 0 15px;">
    {{-- Header Section --}}
    <div
        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('produksi.pesanan', ['method' => $transaction->method]) }}" wire:navigate
                style="background: #313131; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px; padding: 10px 25px; text-decoration: none;">
                <svg style="width: 20px; height: 20px; min-width: 20px;" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 16L6 10L12 4" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f6f6f6; white-space: nowrap;">Kembali</span>
            </a>
            <h1
                style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin: 0;">
                Rincian Pesanan</h1>
        </div>
    </div>

    {{-- Main Content Container --}}
    <div style="display: flex; flex-direction: column; gap: 50px; align-items: flex-end;">
        <div style="display: flex; flex-direction: column; gap: 30px; width: 100%;">
            {{-- Order Information Card --}}
            <div
                style="background: #fafafa; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); padding: 25px 30px; display: flex; flex-direction: column; gap: 30px;">
                {{-- Invoice Number and Status --}}
                <div
                    style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 15px;">
                    <p
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
                <div style="display: flex; flex-direction: row; gap: 20px; width: 100%;" class="justify-between">
                    <div style="display: flex; flex-wrap: wrap; gap: 30px; width: 100%;">
                        {{-- Tanggal Pesanan Masuk --}}
                        <div style="display: flex; flex-direction: column; gap: 5px; min-width: 200px; flex: 1;">
                            <p
                                style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                                Tanggal Pesanan Masuk</p>
                            <div
                                style="display: flex; flex-wrap: wrap; gap: 10px; font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666;">
                                @if ($transaction->start_date)
                                    <p style="margin: 0;">
                                        {{ \Carbon\Carbon::parse($transaction->start_date)->translatedFormat('d M Y') }}
                                    </p>
                                    <p style="margin: 0;">
                                        {{ \Carbon\Carbon::parse($transaction->start_date)->format('H:i') }}</p>
                                @else
                                    <p style="margin: 0;">-</p>
                                @endif
                            </div>
                        </div>
                        {{-- Tanggal Ambil Pesanan --}}
                        <div style="display: flex; flex-direction: column; gap: 5px; min-width: 200px; flex: 1;">
                            <p
                                style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                                Tanggal Ambil Pesanan</p>
                            <div
                                style="display: flex; flex-wrap: wrap; gap: 10px; font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666;">
                                @if ($transaction->date)
                                    <p style="margin: 0;">
                                        {{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('d M Y') }}</p>
                                    <p style="margin: 0;">
                                        {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '00:00' }}
                                    </p>
                                @else
                                    <p style="margin: 0;">-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Pembeli and Kasir --}}
                    <div style="display: flex; flex-wrap: wrap; gap: 30px; width: 100%;" class="justify-end text-right">
                        <div
                            style="display: flex; flex-direction: column; gap: 5px; font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666; min-width: 150px;">
                            <p style="font-weight: 500; margin: 0;">Pembeli</p>
                            <p style="font-weight: 400; margin: 0; word-break: break-word;">
                                {{ $transaction->name ?? '-' }}</p>
                        </div>
                        <div
                            style="display: flex; flex-direction: column; gap: 5px; font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666; min-width: 150px;">
                            <p style="font-weight: 500; margin: 0;">Kasir</p>
                            <p style="font-weight: 400; margin: 0; word-break: break-word;">
                                {{ $transaction->user->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Catatan Pesanan Section --}}
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <p
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 18px; color: #666666; margin: 0;">
                        Catatan Pesanan</p>
                    <div
                        style="background: #eaeaea; border: 1px solid #d4d4d4; border-radius: 15px; padding: 10px 20px; min-height: 120px;">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 400; font-size: 16px; color: #666666; text-align: justify; margin: 0; word-wrap: break-word;">
                            {{ $transaction->note ?: 'Tidak ada catatan' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Product Table Card --}}
            <div
                style="background: #fafafa; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); padding: 25px 30px; display: flex; flex-direction: column; gap: 20px;">
                <p
                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
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
            <flux:button wire:click="start" variant="secondary" icon="cake">
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
