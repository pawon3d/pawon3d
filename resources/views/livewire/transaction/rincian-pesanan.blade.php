<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <a href="{{ route('transaksi') }}" wire:navigate
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl">Rincian Pesanan</h1>
        </div>
        <div class="flex gap-2 items-center">
            {{-- <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button> --}}

            <!-- Tombol Riwayat Pembaruan -->
            <flux:button variant="secondary" wire:click="riwayatPembaruan">
                Riwayat Pembaruan
            </flux:button>
        </div>
    </div>
    {{-- <div class="flex items-center border bg-white border-gray-500 rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Viverra erat aenean mauris adipiscing nibh. Nullam adipiscing
                dignissim consequat volutpat augue. Auctor euismod arcu at euismod. Odio cras proin eget facilisis vitae
                at. Non at vitae lorem nec quis urna.
            </p>
        </div>
    </div> --}}

    <div class="w-full flex flex-col gap-4 mt-4 bg-white border-gray-300 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold"
                style="font-family: Montserrat, sans-serif; line-height: 1; font-weight: 500;">
                {{ $transaction->invoice_number }}</h1>
            @php
                $statusBgColor = match ($transaction->status) {
                    'Selesai' => '#56c568',
                    'Gagal', 'Batal' => '#eb5757',
                    default => '#ffc400',
                };
                $statusText = match ($transaction->status) {
                    'Gagal' => 'Batal',
                    default => $transaction->status,
                };
            @endphp
            <span class="px-6 py-2 text-white font-medium rounded-full"
                style="background-color: {{ $statusBgColor }}; font-family: Montserrat, sans-serif; font-size: 18px; line-height: 1; border-radius: 30px;">
                {{ $statusText }}
            </span>
        </div>
        @if ($transaction->method == 'siap-beli')
            <div class="w-full flex items-center justify-between flex-row">
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Tanggal Pembelian</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d-m-Y H:i') : '-' }}
                    </p>
                </div>
                <div class="flex flex-col gap-1 items-end">
                    <flux:heading class="text-lg font-semibold">Kasir</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->user->name ?? '-' }}
                    </p>
                </div>
            </div>
        @else
            <div class="w-full">
                <div class="flex flex-wrap gap-8" style="font-family: Montserrat, sans-serif;">
                    <!-- Kolom 1 -->
                    <div class="flex flex-col gap-4 flex-1 min-w-[200px]">
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold" style="line-height: 1; color: #666666; font-size: 14px;">Tanggal
                                Pesanan Masuk</p>
                            <div class="flex flex-row items-center gap-4">
                                <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                    {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d-m-Y') : '-' }}
                                </p>
                                <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                    {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold" style="line-height: 1; color: #666666; font-size: 14px;">Pembeli
                            </p>
                            <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                {{ $transaction->name ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Kolom 2 -->
                    <div class="flex flex-col gap-4 flex-1 min-w-[200px]">
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold" style="line-height: 1; color: #666666; font-size: 14px;">Tanggal
                                Ambil Pesanan</p>
                            <div class="flex flex-row items-center gap-4">
                                <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                    {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') : '-' }}
                                </p>
                                <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                    {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold" style="line-height: 1; color: #666666; font-size: 14px;">No.
                                Telepon Pembeli</p>
                            <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                {{ $transaction->phone ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Kolom 3 -->
                    <div class="flex flex-col gap-4 flex-1 min-w-[200px]">
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold" style="line-height: 1; color: #666666; font-size: 14px;">Tanggal
                                Pesanan Selesai</p>
                            <div class="flex flex-row items-center gap-4">
                                <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                    {{ $transaction->end_date ? \Carbon\Carbon::parse($transaction->end_date)->format('d-m-Y') : '-' }}
                                </p>
                                <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                    {{ $transaction->end_date ? \Carbon\Carbon::parse($transaction->end_date)->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold" style="line-height: 1; color: #666666; font-size: 14px;">Kasir</p>
                            <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                {{ $transaction->user->name ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Kolom 4 -->
                    <div class="flex flex-col gap-4 flex-1 min-w-[200px]">
                        <div class="flex flex-col gap-1">
                            @php
                                $paymentStatusColor = match ($transaction->payment_status) {
                                    'Lunas' => '#56c568',
                                    'Refund' => '#eb5757',
                                    'Belum Lunas' => '#ffc400',
                                    default => '#666666',
                                };
                            @endphp
                            <p class="font-semibold" style="line-height: 1; color: #666666; font-size: 14px;">Status
                                Pembayaran</p>
                            <p class="text-sm font-semibold"
                                style="line-height: 1; color: {{ $paymentStatusColor }}; font-size: 14px;">
                                {{ $transaction->payment_status ?? '-' }}
                            </p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p class="font-semibold" style="line-height: 1; color: #666666; font-size: 14px;">Koki</p>
                            <p class="text-sm" style="line-height: 1; color: #666666; font-size: 14px;">
                                @if (!empty($transaction->production) && !empty($transaction->production->workers))
                                    {{ $transaction->production->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-y-4 my-4 flex-col"
                    style="gap: 15px; margin-top: 30px; margin-bottom: 30px;">
                    <div class="w-full h-4 bg-gray-200 rounded"
                        style="background-color: #eaeaea; height: 16px; border-radius: 5px; overflow: hidden;">
                        <div class="h-full"
                            style="width: {{ number_format($percentage, 0) }}%; background-color: #3f4e4f; border-radius: 5px;">
                        </div>
                    </div>
                    <span class="text-xs"
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666;">
                        {{ number_format($percentage, 0) }}% ({{ $total_quantity_get }} dari
                        {{ $total_quantity_plan }})
                    </span>
                </div>

                <div class="flex flex-col gap-5 mt-4 w-full" style="margin-top: 30px; gap: 20px;">
                    <div class="flex items-center justify-between w-full">
                        <p class="font-medium"
                            style="font-family: Montserrat, sans-serif; line-height: 1; color: #666666; font-size: 16px; font-weight: 500;">
                            Catatan Pesanan</p>
                        <button type="button" wire:click="showNoteModal"
                            class="inline-flex items-center gap-1 px-6 py-2 rounded-lg font-semibold transition-colors"
                            style="background-color: #666666; color: #f6f6f6; font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; border-radius: 15px; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1); padding: 10px 25px;">
                            <flux:icon.pencil class="size-3" />
                            Buat Catatan
                        </button>
                    </div>
                    <div class="w-full bg-gray-100 border border-gray-300 rounded-lg p-5"
                        style="background-color: #eaeaea; border: 1px solid #d4d4d4; border-radius: 15px; padding: 10px 20px; min-height: 120px;">
                        <p class="font-normal text-justify"
                            style="font-family: Montserrat, sans-serif; line-height: 1; color: #666666; font-size: 16px;">
                            {{ $transaction->note ?? 'Tidak ada catatan' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="w-full mt-8 flex items-center flex-col gap-4 bg-white border-gray-300 rounded-lg p-4"
        style="background-color: #fafafa; border-radius: 15px; padding: 25px; margin-top: 30px; gap: 15px;">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <p class="font-semibold"
                style="font-family: Montserrat, sans-serif; line-height: 1; color: #666666; font-size: 18px; font-weight: 600;">
                Daftar Produk</p>
        </div>
        <div class="relative overflow-x-auto w-full"
            style="border-radius: 15px; overflow: hidden; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1);">
            <table class="w-full text-sm" style="font-family: Montserrat, sans-serif;">
                <thead style="background-color: #3f4e4f; color: #f8f4e1;">
                    <tr>
                        <th class="px-6 py-3 text-left"
                            style="padding: 15px 20px; font-weight: 600; font-size: 14px; line-height: 1;">Produk</th>
                        <th class="px-6 py-3 text-right"
                            style="padding: 15px 20px; font-weight: 600; font-size: 14px; line-height: 1;">Jumlah
                            Pesanan</th>
                        @if ($transaction->method != 'siap-beli')
                            <th class="px-6 py-3 text-right"
                                style="padding: 15px 20px; font-weight: 600; font-size: 14px; line-height: 1;">Selisih
                                Didapatkan</th>
                            <th class="px-6 py-3 text-right"
                                style="padding: 15px 20px; font-weight: 600; font-size: 14px; line-height: 1;">Jumlah
                                Didapatkan</th>
                        @endif
                        <th class="px-6 py-3 text-right"
                            style="padding: 15px 20px; font-weight: 600; font-size: 14px; line-height: 1;">Harga Satuan
                        </th>
                        <th class="px-6 py-3 text-right"
                            style="padding: 15px 20px; font-weight: 600; font-size: 14px; line-height: 1;">Harga Jumlah
                        </th>
                    </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                    @foreach ($details as $id => $detail)
                        <tr style="border-bottom: 1px solid #d4d4d4;">
                            <td class="px-6 py-3" style="padding: 15px 20px;">
                                <span style="font-size: 14px; line-height: 1; color: #666666;">
                                    {{ $detail['name'] ?? 'Produk Tidak Ditemukan' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                                <span style="font-size: 14px; line-height: 1; color: #666666;">
                                    {{ $detail['quantity'] }}
                                </span>
                            </td>
                            @if ($transaction->method != 'siap-beli')
                                @php
                                    $productionDetails = $transaction->production?->details ?? collect();
                                    $prodDetail = $productionDetails->firstWhere('product_id', $detail['product_id']);
                                    $qty_get =
                                        $prodDetail?->quantity_get > $detail['quantity']
                                            ? $detail['quantity']
                                            : $prodDetail?->quantity_get;
                                    $selisih = ($qty_get ?? 0) - $detail['quantity'];
                                    $selisihColor = $selisih < 0 ? '#eb5757' : '#666666';
                                @endphp
                                <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                                    <span style="font-size: 14px; line-height: 1; color: {{ $selisihColor }};">
                                        {{ $selisih }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                                    <span style="font-size: 14px; line-height: 1; color: #666666;">
                                        {{ $qty_get ?? 0 }}
                                    </span>
                                </td>
                            @endif
                            <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                                <span style="font-size: 14px; line-height: 1; color: #666666;">
                                    Rp{{ number_format($detail['price'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                                <span style="font-size: 14px; line-height: 1; color: #666666;">
                                    Rp{{ number_format($detail['quantity'] * $detail['price'], 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot style="background-color: #eaeaea;">
                    <tr>
                        <td class="px-6 py-3" style="padding: 15px 20px;">
                            <span
                                style="font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">Total</span>
                        </td>
                        <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                            <span style="font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">
                                {{ $total_quantity_plan }}
                            </span>
                        </td>
                        @if ($transaction->method != 'siap-beli')
                            @php
                                $totalSelisih = $total_quantity_get - $total_quantity_plan;
                                $totalSelisihColor = $totalSelisih < 0 ? '#eb5757' : '#666666';
                            @endphp
                            <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                                <span
                                    style="font-size: 14px; line-height: 1; color: {{ $totalSelisihColor }}; font-weight: 600;">
                                    {{ $totalSelisih }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                                <span style="font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">
                                    {{ $total_quantity_get }}
                                </span>
                            </td>
                        @endif
                        <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                            <span style="font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right" style="padding: 15px 20px;">
                            <span style="font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">
                                Rp{{ number_format(collect($details)->sum(function ($detail) {return $detail['quantity'] * $detail['price'];}),0,',','.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @if (!empty($transaction->production))
            <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4 w-full" style="margin-top: 30px;">
                <p class="font-semibold"
                    style="font-family: Montserrat, sans-serif; line-height: 1; color: #666666; font-size: 14px;">
                    Catatan Produksi</p>
                <flux:textarea rows="4" class="bg-gray-300" disabled
                    style="font-family: Montserrat, sans-serif; background-color: #fafafa; border: 1px solid #d4d4d4; border-radius: 15px; width: 100%;">
                    {{ $transaction->production->note }}</flux:textarea>
            </div>
        @endif
    </div>



    <div class="w-full flex items-start text-start space-x-2 gap-3 flex-col mt-8 mb-2 bg-white border-gray-300 rounded-lg p-4"
        style="background-color: #fafafa; border-radius: 15px; padding: 25px; margin-top: 30px; gap: 15px;">
        <p class="font-semibold"
            style="font-family: Montserrat, sans-serif; line-height: 1; color: #666666; font-size: 18px; font-weight: 600;">
            Pembayaran</p>
        <div class="w-full mb-8 flex flex-col rounded-lg bg-white border border-gray-200 p-1 shadow-sm"
            style="border-radius: 15px; overflow: hidden; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1);">
            <div class="w-full flex flex-col"
                style="background-color: #ffffff; border-radius: 15px 15px 0 0; padding: 20px;">
                <div class="flex flex-row justify-between w-full" style="padding: 10px 0;">
                    <p style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666;">
                        Subtotal {{ count($details) }} Produk</p>
                    <p style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666;">
                        Rp{{ number_format($totalAmount, 0, ',', '.') }}
                    </p>
                </div>
                @if ($transaction->points_used > 0)
                    <div class="flex flex-row justify-between w-full" style="padding: 10px 0;">
                        <p
                            style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #27ae60;">
                            Tukar {{ number_format($transaction->points_used, 0, ',', '.') }} Poin</p>
                        <p
                            style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #27ae60;">
                            -Rp{{ number_format($transaction->points_discount, 0, ',', '.') }}
                        </p>
                    </div>
                @endif
                <div class="flex flex-row justify-between w-full"
                    style="padding: 10px 0; border-top: 1px solid #d4d4d4; margin-top: 10px; padding-top: 20px;">
                    <p
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">
                        Total Tagihan</p>
                    <p
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">
                        Rp{{ number_format($totalAmount - $transaction->points_discount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="w-full flex flex-col"
                style="background-color: #fafafa; border-radius: 0 0 15px 15px; padding: 20px;">
                <div class="flex flex-row justify-between w-full" style="padding: 10px 0;">
                    <p
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">
                        Total Bayar</p>
                    <p
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: #666666; font-weight: 600;">
                        @if ($transaction->payment_status == 'Lunas')
                            Rp{{ number_format($totalAmount, 0, ',', '.') }}
                        @else
                            Rp{{ !empty($totalPayment) ? number_format($totalPayment, 0, ',', '.') : '0' }}
                        @endif
                    </p>
                </div>
                {{-- Refund row (tampilkan di paling atas jika ada refund) --}}
                @if ($transaction->refund)
                    @php
                        $refund = $transaction->refund;
                        $refundDate = $refund->refunded_at
                            ? \Carbon\Carbon::parse($refund->refunded_at)->translatedFormat('d F Y')
                            : '-';
                        $refundTime = $refund->refunded_at
                            ? \Carbon\Carbon::parse($refund->refunded_at)->format('H:i')
                            : '-';
                        $refundMethodLabel =
                            $refund->refund_method == 'tunai'
                                ? 'Tunai'
                                : 'Transfer' . ($refund->channel ? ' - ' . $refund->channel->bank_name : '');
                    @endphp
                    <div class="flex flex-row justify-between items-center w-full"
                        style="padding: 15px 0; border-top: 1px solid #d4d4d4; font-family: Montserrat, sans-serif;">
                        <div class="flex items-center gap-3">
                            <p style="font-size: 14px; line-height: 1; color: #666666;">{{ $refundDate }}</p>
                            <p style="font-size: 14px; line-height: 1; color: #666666;">{{ $refundTime }}</p>
                            <p style="font-size: 14px; line-height: 1; color: #eb5757; font-weight: 500;">Refund</p>
                            <p style="font-size: 14px; line-height: 1; color: #666666;">{{ $refundMethodLabel }}</p>

                            @if ($refund->refund_method == 'transfer' && $refund->proof_image)
                                <div class="flex flex-row gap-2 items-center">
                                    <flux:button icon="eye" iconVariant="micro" variant="ghost"
                                        wire:click="showRefundModal" />
                                </div>
                            @endif
                        </div>
                        <p style="font-size: 14px; line-height: 1; color: #eb5757; font-weight: 500;">
                            -Rp{{ number_format($refund->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                @endif

                @if ($payments && $payments->count())
                    @foreach ($payments as $index => $payment)
                        @php
                            $paidAt = $payment->paid_at
                                ? \Carbon\Carbon::parse($payment->paid_at)->translatedFormat('d F Y')
                                : '-';
                            $time = $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('H:i') : '-';

                            // Tentukan tipe pembayaran (latest = terbaru di bawah)
                            // Index 0 = pembayaran terakhir/terbaru, semakin besar index semakin lama
                            $jumlahPembayaran = $payments->count();
                            $posisiDariAwal = $jumlahPembayaran - $index; // 1 = pertama, 2 = kedua, dst

                            if ($posisiDariAwal == 1 && $payment->paid_amount < $totalAmount) {
                                $tipe = 'Uang Muka';
                            } elseif ($posisiDariAwal == $jumlahPembayaran) {
                                $tipe = 'Lunas';
                            } else {
                                $tipe = 'Uang Muka';
                            }

                            $method = $payment->payment_method ? ucfirst($payment->payment_method) : '-';
                            $bank = $payment->channel->bank_name ?? null;
                            $label = $method . ($bank ? ' ' . ucfirst($bank) : '');
                        @endphp

                        <div class="flex flex-row justify-between items-center w-full"
                            style="padding: 15px 0; border-top: 1px solid #d4d4d4; font-family: Montserrat, sans-serif;">
                            <div class="flex items-center gap-3">
                                <p style="font-size: 14px; line-height: 1; color: #666666;">{{ $paidAt }}</p>
                                <p style="font-size: 14px; line-height: 1; color: #666666;">{{ $time }}</p>
                                <p style="font-size: 14px; line-height: 1; color: #666666;">{{ $tipe }}</p>
                                <p style="font-size: 14px; line-height: 1; color: #666666;">{{ $label }}</p>

                                @if ($payment->payment_method && $payment->payment_method !== 'tunai')
                                    <div class="flex flex-row gap-2 items-center">
                                        @if (!empty($payment->image))
                                            <flux:button icon="eye" iconVariant="micro" variant="ghost"
                                                wire:click="showImageModal('{{ $payment->id }}')" />
                                            <flux:button icon="arrow-down-tray" iconVariant="micro" variant="ghost"
                                                wire:click="downloadImage('{{ $payment->id }}')" />
                                            <flux:button icon="arrow-up-tray" iconVariant="micro" variant="ghost"
                                                wire:click="showUploadModal('{{ $payment->id }}')" />
                                        @else
                                            <flux:button icon="arrow-up-tray" iconVariant="micro" variant="ghost"
                                                wire:click="showUploadModal('{{ $payment->id }}')" />
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <p style="font-size: 14px; line-height: 1; color: #666666;">
                                Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                @endif

                <div class="flex flex-row justify-between w-full"
                    style="padding: 15px 0; border-top: 1px solid #d4d4d4; margin-top: 10px;">
                    @php
                        $sisaTagihanColor = $remainingAmount > 0 ? '#eb5757' : '#666666';
                    @endphp
                    <p
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: {{ $sisaTagihanColor }}; font-weight: 600;">
                        Sisa Tagihan</p>
                    <p
                        style="font-family: Montserrat, sans-serif; font-size: 14px; line-height: 1; color: {{ $sisaTagihanColor }}; font-weight: 600;">
                        Rp{{ number_format($remainingAmount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        @if ($transaction->payment_status != 'Lunas' && $transactionStatus && $customer)
            {{-- Tukar Poin Section --}}
            <div class="w-full flex flex-col gap-4 bg-white border-gray-300 rounded-lg p-4"
                style="background-color: #fafafa; border-radius: 15px; padding: 25px; margin-top: 30px;">
                <div class="flex flex-col gap-[15px]">
                    <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666]" style="line-height: 1;">
                        Tukar Poin
                    </p>
                    <div class="flex items-start justify-between w-full">
                        <p class="font-['Montserrat'] font-normal text-[14px] text-[#666666] text-justify"
                            style="line-height: 1;">Tukar poin untuk menerima potongan harga. Poin (1 poin = Rp 100)
                            yang dapat
                            ditukarkan adalah kelipatan 10 poin.</p>
                        <div class="flex items-center gap-[2px] font-['Montserrat'] font-normal text-[14px] text-[#666666]"
                            style="line-height: 1;">
                            <span>{{ number_format($availablePoints, 0, ',', '.') }}</span>
                            <span>Poin</span>
                        </div>
                    </div>
                    <div class="flex gap-3 items-end">
                        <div class="flex-1">
                            <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px]">
                                <input type="number" wire:model.live="pointsUsed" placeholder="0" min="0"
                                    step="10" {{ $availablePoints == 0 ? 'disabled' : '' }}
                                    class="w-full font-['Montserrat'] font-normal text-[16px] text-[#959595] bg-transparent border-none focus:outline-none focus:ring-0 p-0"
                                    style="line-height: 1;" />
                            </div>
                        </div>
                        <button type="button" wire:click="applyPoints"
                            class="bg-[#3f4e4f] hover:bg-[#2d3738] px-6 py-2.5 rounded-[15px] text-white font-semibold"
                            style="font-family: Montserrat, sans-serif;">
                            Terapkan
                        </button>
                    </div>
                    @if ($pointsUsed > 0)
                        <p class="font-['Montserrat'] font-normal text-[14px] text-[#27ae60]" style="line-height: 1;">
                            Diskon: Rp{{ number_format($pointsUsed * 100, 0, ',', '.') }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        @if ($transaction->payment_status != 'Lunas' && $transactionStatus)
            <div class="w-full flex flex-col gap-4">
                <flux:label>Metode Pembayaran</flux:label>
                <p class="text-sm text-gray-500">
                    Pilih Metode Pembayaran (Tunai, Transfer, atau QRIS). Jika Bukan Tunai maka akan diminta bukti
                    pembayaran
                    berupa
                    gambar (.jpg dan .png)
                </p>
                <flux:select wire:model.live="paymentMethod" class="mt-2" placeholder="Pilih Metode Pembayaran">
                    <flux:select.option value="tunai" class="text-gray-700">Tunai</flux:select.option>
                    <flux:select.option value="transfer" class="text-gray-700">Transfer</flux:select.option>
                </flux:select>
                <flux:error name="paymentMethod" />

                @if ($paymentMethod == 'transfer')
                    <div class="mt-2 flex flex-row gap-2 w-full">
                        <div class="w-1/4">
                            <flux:select wire:model.live="paymentChannelId" placeholder="Pilih Bank Tujuan">
                                @foreach ($paymentChannels as $channel)
                                    <flux:select.option value="{{ $channel->id }}" class="text-gray-700">
                                        {{ $channel->bank_name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="paymentChannelId" />
                        </div>
                        <div class="w-3/4">
                            <flux:input wire:model="paymentAccount" placeholder="Masukkan Nomor Rekening" readonly />
                            <flux:error name="paymentAccount" />
                        </div>
                    </div>
                @endif

                <flux:label>Nominal Pembayaran</flux:label>
                <p class="text-sm text-gray-500">
                    Masukkan atau pilih nominal pembayaran tagihan. Untuk uang muka dilakukan dengan minimal 50% atau
                    setengah
                    dari
                    Total Tagihan.
                </p>
                <div class="flex flex-row gap-2 w-full">
                    <div class="flex flex-col gap-2 w-full">
                        @if ($paymentMethod == 'tunai')
                            <span class="text-xs text-gray-500">
                                Nominal Uang Yang Diterima
                            </span>
                        @endif
                        <flux:input placeholder="Masukkan Nominal Pembayaran..."
                            wire:model.number.live="paidAmount" />
                        <flux:error name="paidAmount" />
                    </div>
                    @if ($paymentMethod == 'tunai')
                        <div class="flex flex-col gap-2 w-full">
                            <span class="text-xs text-gray-500">
                                Nominal Uang Kembalian
                            </span>
                            <flux:input placeholder="Kembalian"
                                value="Rp{{ number_format($changeAmount, 0, ',', '.') }}" readonly />
                        </div>
                    @endif
                </div>

                @if ($paymentMethod == 'transfer')
                    <div class="mb-5 w-full">
                        <div class="flex flex-row items-center gap-4">
                            <label
                                class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2)">
                                Pilih Bukti Pembayaran
                                <input type="file" wire:model.live="image"
                                    accept="image/jpeg, image/png, image/jpg" class="hidden" />
                            </label>

                            @if ($image)
                                <input type="text"
                                    class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                    value="{{ is_string($image) ? basename($image) : $image->getClientOriginalName() }}"
                                    readonly wire:loading.remove wire:target="image">
                                <input type="text"
                                    class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                    value="Mengupload gambar..." readonly wire:loading wire:target="image">
                            @else
                                <input type="text"
                                    class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                    value="Belum Ada Bukti Pembayaran" readonly wire:loading.remove
                                    wire:target="image">
                                <input type="text"
                                    class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                    value="Mengupload gambar..." readonly wire:loading wire:target="image">
                            @endif

                        </div>
                    </div>
                    <flux:error name="image" />
                @endif
            </div>
        @endif
    </div>





    <div class="flex justify-end mt-16 gap-4">
        @if ($transaction->status == 'Draft')
            <flux:button icon="trash" type="button" variant="danger" loading="false"
                wire:click.prevent="delete">
                Hapus Pesanan
            </flux:button>
        @endif
        @if (
            ($transaction->status == 'Belum Diproses' || $transaction->status == 'Draft') &&
                $transaction->payment_status != 'Lunas')
            <flux:button icon="pencil-square" type="button" href="{{ route('transaksi.edit', $transaction->id) }}"
                wire:navigate>
                Ubah Daftar Pesanan
            </flux:button>
        @elseif ($transaction->status == 'Belum Diproses' && $transaction->payment_status == 'Lunas')
            <flux:button icon="check-circle" type="button" wire:click.prevent='finish'>
                Selesaikan Pesanan
            </flux:button>
        @elseif ($transaction->status == 'Sedang Diproses' || $transaction->status == 'Dapat Diambil')
            <flux:button icon="check-circle" type="button" wire:click.prevent='finish'>
                Selesaikan Pesanan
            </flux:button>
        @endif
        @if ($transaction->status == 'Gagal' || $transaction->status == 'Selesai')
            <button type="button" wire:click='showRefundModal'
                class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-medium transition-colors"
                style="background-color: #eb5757; color: white; font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; border-radius: 20px;">
                <flux:icon.receipt-refund variant="solid" class="size-5" />
                Refund Pesanan
            </button>
            <button type="button" wire:click.prevent="$set('showStruk', true)"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-medium transition-colors"
                style="background-color: #3f4e4f; color: white; font-family: Montserrat, sans-serif; font-size: 16px; line-height: 1; border-radius: 20px;">
                <flux:icon.printer class="size-5" />
                Cetak Struk Pesanan
            </button>
        @elseif($transaction->payment_status != 'Lunas')
            <flux:button icon="shopping-cart" type="button" variant="primary" wire:click.prevent="pay">
                Bayar dan
                @if ($transaction->status == 'Draft')
                    Buat
                @else
                    Ambil
                @endif
                Pesanan
            </flux:button>
        @endif
    </div>

    @if ($showStruk)
        <div class="fixed inset-0 top-0 bottom-0 overflow-y-scroll bg-gray-100/95 z-50" wire:ignore.self>
            <div class="w-full px-4">
                <div class="relative min-h-screen pb-32">
                    <div class="fixed top-2 right-4 z-50">
                        <flux:button type="button" icon="x-mark" wire:click.prevent="kembali" variant="ghost" />
                    </div>

                    <div class="mx-auto mt-20 max-w-sm text-center fade-slide-up" id="success-content">
                        <div class="state-container">
                            <span id="state" class="state-span active">
                                <svg id="state-svg" width="120" height="120" viewBox="0 0 120 120">
                                    <circle id="extra-outer-circle" cx="60" cy="60" r="0"
                                        fill="#B9EBC6" opacity="0" />
                                    <circle id="outer-circle" cx="60" cy="60" r="0" fill="#72CF81"
                                        opacity="0" />
                                    <circle id="mid-circle" cx="60" cy="60" r="0" fill="#48A457"
                                        opacity="0" />
                                    <circle class="pulse-circle" cx="60" cy="60" r="30"
                                        fill="#398345" />
                                    <path class="checkmark-path" d="M43 64L55 76L87 38" stroke="white"
                                        stroke-width="4" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>

                        <p class="text-lg font-bold">Pembayaran Berhasil</p>
                        <p class="text-lg">{{ \Carbon\Carbon::now()->format('d M Y, H:i:s') }} WIB</p>
                    </div>

                    <div class="w-full max-w-[280px] mt-8 mb-4 mx-auto bg-[#fafafa] border-[0.5px] border-[#666666] rounded-[15px] shadow-md fade-slide-up"
                        id="receipt" style="font-family: Montserrat, sans-serif;">
                        <div class="flex flex-col gap-[15px] items-center px-[20px] pt-[30px] pb-[100px]">

                            {{-- Header Info --}}
                            <div class="flex flex-col items-center justify-center w-full">
                                <div class="flex items-start justify-between pb-[15px] w-full">
                                    <div class="flex-1 flex flex-col gap-[12px] items-center">
                                        {{-- Logo Pawon3D --}}
                                        <div class="flex flex-col justify-center leading-[50px] text-center text-black"
                                            style="font-family: Pacifico, cursive; font-size: 0;">
                                            <p class="whitespace-pre">
                                                <span style="font-size: 32px;">Pawon</span><span
                                                    style="font-size: 34px; letter-spacing: -2.8px;">3</span><span
                                                    style="font-size: 32px; letter-spacing: -2.6px;">D</span>
                                            </p>
                                        </div>
                                        {{-- Alamat --}}
                                        <div class="flex flex-col gap-[4px] items-center text-center text-black"
                                            style="font-size: 10px; line-height: 1.2;">
                                            <div class="flex flex-col justify-center">
                                                <p class="mb-0">Jl. Jenderal Sudirman Km.3 RT.25 RW.07</p>
                                                <p class="mb-0">Kel. Muara Bulian, Kec.Muara Bulian,</p>
                                                <p>Kab.Batang Hari, Jambi, 36613</p>
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <p class="whitespace-pre">081122334455</p>
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <p class="whitespace-pre">www.pawon3d.my.id</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Info Transaksi --}}
                                <div class="border-t border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal">
                                            <p class="whitespace-nowrap">ID Transaksi</p>
                                        </div>
                                        <div class="font-medium text-right">
                                            <p class="break-all">{{ $transaction->invoice_number }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Tanggal</p>
                                        </div>
                                        <div class="font-medium text-right">
                                            <p>
                                                {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->translatedFormat('d M Y H:i') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Status Bayar</p>
                                        </div>
                                        @php
                                            $statusColor = match ($transaction->payment_status) {
                                                'Lunas' => '#56c568',
                                                'Refund' => '#eb5757',
                                                'Belum Lunas' => '#ffc400',
                                                default => '#666666',
                                            };
                                        @endphp
                                        <div class="font-medium text-right" style="color: {{ $statusColor }};">
                                            <p>{{ $transaction->payment_status ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Kasir</p>
                                        </div>
                                        <div class="font-medium text-right">
                                            <p class="break-words">{{ $transaction->user->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Daftar Produk --}}
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    @foreach ($transaction->details as $detail)
                                        <div class="flex flex-col gap-[6px] items-start w-full">
                                            <div class="flex items-start justify-between gap-2 w-full text-black">
                                                <div class="font-normal flex-1">
                                                    <p class="break-words">{{ $detail->product->name }}</p>
                                                </div>
                                                <div class="font-medium whitespace-nowrap">
                                                    <p>Rp{{ number_format($detail->product->price * $detail->quantity, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex gap-[4px] items-center w-full justify-between">
                                                <div class="flex gap-[4px] items-center font-normal text-black">
                                                    <p>{{ $detail->quantity }}</p>
                                                    <p>x</p>
                                                    <p>Rp{{ number_format($detail->product->price, 0, ',', '.') }}</p>
                                                    @if ($detail->refund_quantity > 0)
                                                        <p style="color: #eb5757;">Refund
                                                            {{ $detail->refund_quantity }}</p>
                                                    @endif
                                                </div>
                                                @if ($detail->refund_quantity > 0)
                                                    <div class="font-medium" style="color: #eb5757;">
                                                        <p>-Rp{{ number_format($detail->product->price * $detail->refund_quantity, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Total --}}
                                @php
                                    $subtotal = $totalAmount;
                                    $totalItems = $transaction->details->sum('quantity');
                                @endphp
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="flex gap-[4px] items-center font-normal">
                                            <p class="whitespace-nowrap">Subtotal {{ $totalItems }} Produk</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    @if ($transaction->points_used > 0)
                                        <div class="flex items-center justify-between w-full text-green-600">
                                            <div class="flex gap-[4px] items-center font-normal">
                                                <p class="whitespace-nowrap">Tukar
                                                    {{ number_format($transaction->points_used, 0, ',', '.') }} Poin
                                                </p>
                                            </div>
                                            <p class="font-medium whitespace-nowrap">
                                                -Rp{{ number_format($transaction->points_discount, 0, ',', '.') }}</p>
                                        </div>
                                    @endif
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Total Tagihan</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp{{ number_format($subtotal - $transaction->points_discount, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pembayaran --}}
                                @php
                                    $allPayments = $payments ?? collect();
                                    $totalPaid = $allPayments->sum('paid_amount');
                                    $transactionRefund = $transaction->refund;
                                @endphp
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Total Bayar</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp{{ number_format($totalPaid, 0, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    {{-- Refund Payment Display --}}
                                    @if ($transactionRefund)
                                        <div class="flex items-start justify-between gap-2 w-full font-normal"
                                            style="color: #eb5757;">
                                            <div class="break-words flex-1">
                                                <p>(Refund)
                                                    {{ ucfirst($transactionRefund->refund_method) }}{{ $transactionRefund->channel ? ' - ' . $transactionRefund->channel->bank_name : '' }}
                                                </p>
                                            </div>
                                            <div class="whitespace-nowrap">
                                                <p>Rp{{ number_format($transactionRefund->total_amount, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($allPayments && $allPayments->count())
                                        @foreach ($allPayments as $payment)
                                            @php
                                                $method = $payment->payment_method
                                                    ? ucfirst($payment->payment_method)
                                                    : '-';
                                                $bank = $payment->channel->bank_name ?? null;

                                                // Tentukan tipe pembayaran
                                                $paymentCount = $allPayments->count();
                                                $paymentIndex = $allPayments->search(fn($p) => $p->id === $payment->id);

                                                if ($paymentCount == 1) {
                                                    $tipe = 'Lunas';
                                                } else {
                                                    $tipe = $paymentIndex == $paymentCount - 1 ? 'Lunas' : 'Uang Muka';
                                                }

                                                $label = "($tipe) $method" . ($bank ? " - $bank" : '');
                                            @endphp
                                            <div
                                                class="flex items-start justify-between gap-2 w-full text-black font-normal">
                                                <div class="break-words flex-1">
                                                    <p>{{ $label }}</p>
                                                </div>
                                                <div class="whitespace-nowrap">
                                                    <p>Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="flex items-start justify-between w-full"
                                        style="color: {{ $remainingAmount > 0 ? '#eb5757' : '#000000' }};">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Sisa Tagihan</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp{{ number_format($remainingAmount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer Info --}}
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Tanggal Cetak</p>
                                        </div>
                                        <div class="font-medium text-right">
                                            <p>{{ now()->translatedFormat('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pesan Footer --}}
                                <div class="flex flex-col justify-center w-full text-center text-black px-2"
                                    style="font-size: 9px; line-height: 1.4; font-weight: 400;">
                                    <p>Mohon Cek Kembali Uang Kembalian Sebelum Meninggalkan Kasir</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="fixed bottom-0 left-4 right-4 z-51">
                        <div class="max-w-md mt-8 mb-4 mx-auto border-gray-300 bg-white text-sm rounded-lg shadow-md p-4 font-sans text-center flex flex-col gap-4 fade-slide-up"
                            id="buttons">
                            <input type="text" wire:model="phoneNumber"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="contoh: 08123456789" />
                            <div class="grid grid-cols-2 gap-4">
                                <flux:button type="button" wire:click.prevent="kembali" class="w-full">
                                    Kembali
                                </flux:button>
                                <flux:button type="button" variant="primary" wire:click="send" class="w-full">
                                    Kirim Struk
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .state-container {
                position: relative;
                width: 100px;
                height: 100px;
                margin: 0 auto 2rem;
            }

            .state-span {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                opacity: 0;
                transition:
                    opacity 0.6s cubic-bezier(0.22, 0.61, 0.36, 1),
                    transform 0.6s cubic-bezier(0.22, 0.61, 0.36, 1);
                pointer-events: none;
                filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
            }

            .state-span.active {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
                z-index: 10;
            }

            .checkmark-path {
                stroke-dasharray: 50;
                stroke-dashoffset: 50;
                animation: drawCheck 0.5s forwards 0.3s;
            }

            @keyframes drawCheck {
                to {
                    stroke-dashoffset: 0;
                }
            }

            .pulse-circle {
                transform-origin: center;
                animation: pulse 1.5s cubic-bezier(0.22, 0.61, 0.36, 1) infinite;
            }

            @keyframes pulse {
                0% {
                    transform: scale(1);
                    opacity: 1;
                }

                50% {
                    transform: scale(1.05);
                    opacity: 0.9;
                }

                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }

            #extra-outer-circle,
            #outer-circle,
            #mid-circle {
                transition: r 0.8s ease, opacity 0.8s ease;
            }

            .fade-slide-up {
                opacity: 0;
                transform: translateY(30px);
                transition: all 0.6s ease-out;
            }

            .fade-slide-up.show {
                opacity: 1;
                transform: translateY(0);
            }
        </style>

        @script
            <script>
                window.addEventListener('livewire:init', () => {
                    Livewire.on('showStrukChanged', (show) => {
                        if (show) initStrukAnimations();
                    });
                });

                if (@js($showStruk)) {
                    setTimeout(initStrukAnimations, 100);
                }

                function initStrukAnimations() {
                    if (window.strukAnimationInterval) {
                        clearInterval(window.strukAnimationInterval);
                    }

                    const getSafeElement = (id) => document.getElementById(id) || {
                        setAttribute: () => {}
                    };

                    const extraOuter = getSafeElement("extra-outer-circle");
                    const outer = getSafeElement("outer-circle");
                    const mid = getSafeElement("mid-circle");

                    const states = [{
                            r1: 0,
                            r2: 0,
                            r3: 0,
                            o: 0
                        },
                        {
                            r1: 40,
                            r2: 0,
                            r3: 0,
                            o: 0.4
                        },
                        {
                            r1: 50,
                            r2: 40,
                            r3: 0,
                            o: 0.5
                        },
                        {
                            r1: 60,
                            r2: 50,
                            r3: 40,
                            o: 0.6
                        }
                    ];

                    let current = 0;

                    function updateState() {
                        const state = states[current];
                        extraOuter.setAttribute("r", state.r1);
                        extraOuter.setAttribute("opacity", state.o);
                        outer.setAttribute("r", state.r2);
                        outer.setAttribute("opacity", state.o);
                        mid.setAttribute("r", state.r3);
                        mid.setAttribute("opacity", state.o);
                        current = (current + 1) % states.length;
                    }

                    updateState();
                    setTimeout(updateState, 1000);
                    window.strukAnimationInterval = setInterval(updateState, 2000);

                    const animateWithDelay = (element, delay) => {
                        if (!element) return;
                        element.classList.remove('show');
                        setTimeout(() => element.classList.add('show'), delay);
                    };

                    animateWithDelay(document.getElementById('success-content'), 400);
                    animateWithDelay(document.getElementById('receipt'), 2000);
                    animateWithDelay(document.getElementById('buttons'), 3000);
                }
            </script>
        @endscript
    @endif


    {{-- <flux:modal class="w-full max-w-xs" wire:model="showPrintModal">
        @if ($transaction)
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }


                #printArea,
                #printArea * {
                    visibility: visible;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }

                #printArea {
                    size: 72mm 100vh;
                    margin: 0;
                    padding: 0;
                    font-size: 10px;
                }
            }
        </style>
        <div id="printArea" class="p-4">

            <div class="text-center">
                <h2 class="text-lg font-bold">Struk Transaksi</h2>
                <p class="text-xs">Tanggal: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
            </div>

            <div class="mt-4">
                <p class="text-xs"><strong>Total:</strong> Rp {{ number_format($transaction->total_amount) }}</p>
                <p class="text-xs"><strong>Status Pembayaran:</strong> {{ $transaction->payment_status }}</p>
                <p class="text-xs"><strong>Tipe:</strong>
                    @if ($transaction->method == 'pesanan-reguler')
                    Pesanan Reguler
                    @elseif('pesanan-kotak')
                    Pesanan Kotak
                    @else
                    Siap Saji
                    @endif
                </p>
            </div>

            <div class="mt-4 border-t pt-2">
                <table class="w-full text-xs">
                    <thead>
                        <tr>
                            <th class="text-left">Produk</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaction->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name }}</td>
                            <td class="text-right">{{ $detail->quantity }}</td>
                            <td class="text-right">Rp {{ number_format($detail->price) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs">Terima kasih telah berbelanja</p>
            </div>
        </div>
        <div class="flex justify-between gap-2 mt-6">
            <flux:button type="button" wire:click="$set('showPrintModal', false)" class="btn-secondary">
                Tutup
            </flux:button>
            <flux:button type="button" onclick="return cetakStruk('{{ route('transaksi.cetak', $transaction->id) }}')"
                class="px-4 py-2 border rounded-md btn-primary">
                Cetak
            </flux:button>
        </div>
        <div class="flex justify-end mt-4">
            <flux:button type="button" wire:click="send" class="w-full">
                Kirim Struk via WhatsApp
            </flux:button>
        </div>
        @endif
    </flux:modal> --}}

    <flux:modal class="w-full max-w-xs" wire:model="showImage">
        @if (!empty($paymentImage) && $showImage)
            <div class="text-center mt-4">
                <flux:heading class="text-sm text-gray-500">Bukti Pembayaran</flux:heading>
            </div>
            <div class="flex justify-center">
                <img src="{{ asset('storage/' . $paymentImage) }}" alt="Bukti Pembayaran"
                    class="w-full h-auto max-h-96 object-cover rounded-lg">
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <flux:button type="button" wire:click="$set('showImage', false)">
                    Tutup
                </flux:button>
            </div>
        @else
            <div class="text-center mt-4">
                <flux:heading class="text-sm text-gray-500">Bukti Pembayaran Belum Diupload</flux:heading>
            </div>
        @endif
    </flux:modal>

    <flux:modal name="upload-image" class="w-full max-w-xs" wire:model="uploadModal">
        <div class="flex flex-col items-center justify-center gap-4 mt-4">
            <div class="flex flex-col w-full max-w-xs space-y-4">
                <!-- Dropzone Area -->
                <div class="relative w-full h-48 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                    id="dropzone-container">

                    <label for="dropzone-file" class="w-full h-full cursor-pointer flex items-center justify-center">
                        <div id="preview-container" class="w-full h-full">
                            @if ($previewUploadImage)
                                <!-- Image Preview -->
                                <img src="{{ $previewUploadImage }}" alt="Preview"
                                    class="object-fill w-full h-full" id="image-preview" />
                            @else
                                <!-- Default Content -->
                                <div class="flex flex-col items-center justify-center p-4 text-center">
                                    <flux:icon icon="arrow-up-tray" class="w-8 h-8 mb-6 text-gray-400" />
                                    <p class="mb-2 text-lg font-semibold text-gray-600">Unggah Gambar</p>
                                    <p class="mb-2 text-xs text-gray-600 mt-4">
                                        Ukuran gambar tidak lebih dari
                                        <span class="font-semibold">2mb</span>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Pastikan gambar dalam format
                                        <span class="font-semibold">JPG </span> atau
                                        <span class="font-semibold">PNG</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </label>
                </div>

                <!-- Hidden File Input -->
                <input id="dropzone-file" type="file" wire:model="uploadImage" class="hidden"
                    accept="image/jpeg, image/png, image/jpg" />

                <!-- Upload Button -->
                <flux:button variant="primary" type="button"
                    onclick="document.getElementById('dropzone-file').click()" class="w-full">
                    Pilih Gambar
                </flux:button>

                <!-- Error Message -->
                @error('uploadImage')
                    <div class="w-full p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Loading Indicator -->
                <div wire:loading wire:target="uploadImage"
                    class="w-full p-3 text-sm text-blue-700 bg-blue-100 rounded-lg">
                    Mengupload gambar...
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-end space-x-2">
            <flux:modal.close>
                <flux:button type="button" icon="x-mark">Batal</flux:button>
            </flux:modal.close>
            <flux:button icon="save" variant="primary" type="button" wire:click.prevent="uploadImageStore">
                Simpan
            </flux:button>
        </div>
    </flux:modal>

    <flux:modal class="w-full max-w-[490px]" name="refund" wire:model="refundModal">
        <div style="font-family: Montserrat, sans-serif;">
            {{-- Header with Transaction Info --}}
            <div class="flex flex-col gap-[10px] pb-[20px] pt-[29px] px-[30px]" style="background-color: #fafafa;">
                <div class="flex items-center justify-between w-full"
                    style="font-size: 16px; line-height: 1; color: #666666;">
                    <p style="font-weight: 500;">ID Transaksi</p>
                    <p style="font-weight: 400;">{{ $transaction->invoice_number }}</p>
                </div>
                <div class="flex items-center justify-between w-full"
                    style="font-size: 16px; line-height: 1; color: #666666;">
                    <p style="font-weight: 500;">Total Bayar</p>
                    <p style="font-weight: 500;">Rp{{ number_format($totalAmount, 0, ',', '.') }}</p>
                </div>
                @if ($isRefundReadOnly && $transaction->refund)
                    <div class="flex items-center justify-between w-full"
                        style="font-size: 16px; line-height: 1; color: #666666;">
                        <p style="font-weight: 500;">Tanggal Refund</p>
                        <p style="font-weight: 400;">
                            {{ \Carbon\Carbon::parse($transaction->refund->refunded_at)->format('d M Y, H:i') }}</p>
                    </div>
                @endif
            </div>

            <div class="flex flex-col max-h-[60vh] overflow-y-auto">
                {{-- Section 1: Reason & Proof --}}
                <div class="flex flex-col gap-[15px] px-[30px] py-[25px] border-t"
                    style="background-color: #fafafa; border-color: #d4d4d4;">
                    <div class="flex flex-col gap-[10px]" style="font-size: 16px; line-height: 1; color: #666666;">
                        <p style="font-weight: 500;">Alasan Refund @if (!$isRefundReadOnly)
                                <span style="color: #eb5757;"> *</span>
                            @endif
                        </p>
                    </div>
                    <flux:select wire:model="refundReason" placeholder="Pilih Alasan" :disabled="$isRefundReadOnly">
                        <flux:select.option value="Gosong">Gosong</flux:select.option>
                        <flux:select.option value="Produk Rusak">Produk Rusak</flux:select.option>
                        <flux:select.option value="Salah Pesanan">Salah Pesanan</flux:select.option>
                        <flux:select.option value="Kadaluarsa">Kadaluarsa</flux:select.option>
                        <flux:select.option value="Tidak Sesuai Pesanan">Tidak Sesuai Pesanan</flux:select.option>
                        <flux:select.option value="Lainnya">Lainnya</flux:select.option>
                    </flux:select>
                    @if (!$isRefundReadOnly)
                        <flux:error name="refundReason" />
                    @endif

                    @if (!$isRefundReadOnly)
                        <div class="flex gap-[10px] items-center w-full">
                            <label class="cursor-pointer px-[30px] py-[10px] rounded-[15px]"
                                style="background-color: #74512d; color: #ffffff; font-size: 16px; font-weight: 500; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1);">
                                Pilih File
                                <input type="file" wire:model="refundProofImage" accept="image/*"
                                    class="hidden" />
                            </label>
                            <div class="flex-1 px-[20px] py-[10px] rounded-[15px] border"
                                style="background-color: @if ($refundProofImage) #eaeaea @else #f6f6f6 @endif; border-color: #d4d4d4; font-size: 16px; color: @if ($refundProofImage) #666666 @else #959595 @endif;">
                                @if ($refundProofImage)
                                    {{ $refundProofImage->getClientOriginalName() }}
                                @else
                                    File Belum Dipilih
                                @endif
                            </div>
                        </div>
                        <flux:error name="refundProofImage" />
                    @elseif($transaction->refund && $transaction->refund->proof_image)
                        <div class="flex flex-col gap-[10px]">
                            <p style="font-size: 14px; font-weight: 500; color: #666666;">Bukti Refund:</p>
                            <img src="{{ Storage::url($transaction->refund->proof_image) }}" alt="Bukti Refund"
                                class="w-full max-h-[150px] object-contain rounded-[10px] border"
                                style="border-color: #d4d4d4;">
                        </div>
                    @endif
                </div>

                {{-- Section 2: Refund Method --}}
                <div class="flex flex-col gap-[15px] px-[30px] py-[25px] border-t"
                    style="background-color: #fafafa; border-color: #d4d4d4;">
                    <div class="flex flex-col gap-[10px]" style="font-size: 16px; line-height: 1; color: #666666;">
                        <p style="font-weight: 500;">Metode Refund@if (!$isRefundReadOnly)
                                <span style="color: #eb5757;"> *</span>
                            @endif
                        </p>
                    </div>

                    <flux:select wire:model.live="refundMethod" placeholder="Pilih Metode Refund"
                        :disabled="$isRefundReadOnly">
                        <flux:select.option value="tunai">Tunai</flux:select.option>
                        <flux:select.option value="transfer">Non Tunai</flux:select.option>
                    </flux:select>
                    @if (!$isRefundReadOnly)
                        <flux:error name="refundMethod" />
                    @endif

                    @if ($refundMethod == 'transfer')
                        <flux:select wire:model.live="refundPaymentChannel" placeholder="Pilih Bank"
                            :disabled="$isRefundReadOnly">
                            @foreach ($paymentChannels as $channel)
                                <flux:select.option value="{{ $channel->id }}">{{ $channel->bank_name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        @if (!$isRefundReadOnly)
                            <flux:error name="refundPaymentChannel" />
                        @endif

                        <flux:input wire:model="refundAccountNumber" placeholder="Masukkan Nomor Rekening"
                            :disabled="$isRefundReadOnly" />
                        @if (!$isRefundReadOnly)
                            <flux:error name="refundAccountNumber" />
                        @endif
                    @endif
                </div>

                {{-- Section 3: Product Selection --}}
                <div class="flex flex-col gap-[15px] px-[30px] py-[25px] border-t"
                    style="background-color: #fafafa; border-color: #d4d4d4;">
                    <div class="flex flex-col gap-[10px]" style="font-size: 16px; line-height: 1; color: #666666;">
                        <p style="font-weight: 500;">Produk Refund@if (!$isRefundReadOnly)
                                <span style="color: #eb5757;"> *</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col gap-[10px]">
                        @foreach ($details as $id => $item)
                            <div class="flex items-start justify-between py-[10px] border-b"
                                style="border-color: #e5e5e5;">
                                <div class="flex flex-col gap-[8px]">
                                    <p style="font-size: 16px; font-weight: 500; color: #666666;">
                                        {{ $item['name'] }}</p>
                                    <div class="flex gap-[10px] items-center">
                                        <div class="flex gap-[5px] items-center"
                                            style="font-size: 14px; color: #666666;">
                                            <p>{{ $item['quantity'] }}</p>
                                            <p>x</p>
                                            <p>Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                                        </div>
                                        @if ($item['refund_quantity'] > 0)
                                            <div class="flex gap-[5px] items-center"
                                                style="font-size: 14px; color: #eb5757;">
                                                <p>Refund</p>
                                                <p>{{ $item['refund_quantity'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col gap-[8px] items-end">
                                    @if ($item['refund_quantity'] > 0)
                                        <div class="flex gap-[5px] items-center"
                                            style="font-size: 14px; font-weight: 500; color: #eb5757;">
                                            <p>-Rp{{ number_format($item['refund_quantity'] * $item['price'], 0, ',', '.') }}
                                            </p>
                                        </div>
                                    @endif
                                    @if (!$isRefundReadOnly)
                                        <div class="flex gap-[10px] items-center">
                                            <button wire:click="decrementItem('{{ $id }}')"
                                                class="flex items-center justify-center w-[28px] h-[28px] rounded-full border-[1.5px]"
                                                style="border-color: #74512d;">
                                                <flux:icon.minus class="size-[16px]" style="color: #74512d;" />
                                            </button>
                                            <div class="flex items-center justify-center px-[8px] py-[2px] w-[32px] rounded-[8px] border"
                                                style="border-color: rgba(116, 81, 45, 0.2); font-size: 14px; font-weight: 600; color: #666666;">
                                                {{ $item['refund_quantity'] }}
                                            </div>
                                            <button wire:click="incrementItem('{{ $id }}')"
                                                class="flex items-center justify-center w-[28px] h-[28px] rounded-full border-[1.5px]"
                                                style="border-color: #74512d;">
                                                <flux:icon.plus class="size-[16px]" style="color: #74512d;" />
                                            </button>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center px-[8px] py-[2px] rounded-[8px]"
                                            style="background-color: #f0f0f0; font-size: 14px; font-weight: 600; color: #666666;">
                                            {{ $item['refund_quantity'] }} item
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer with Total & Buttons --}}
            <div class="flex flex-col gap-[20px] items-end px-[30px] py-[20px] border-t"
                style="background-color: #fafafa; border-color: #d4d4d4;">
                <div class="flex items-center justify-between w-full"
                    style="font-size: 16px; font-weight: 500; color: #eb5757;">
                    <div class="flex gap-[5px] items-center">
                        <p>Total</p>
                        <p>{{ collect($details)->sum('refund_quantity') }}</p>
                        <p>Refund</p>
                    </div>
                    <p>Rp{{ number_format($refundTotal, 0, ',', '.') }}</p>
                </div>

                <div class="flex gap-[10px] items-center">
                    <flux:modal.close>
                        <button type="button"
                            class="flex gap-[5px] items-center justify-center px-[25px] py-[10px] rounded-[15px]"
                            style="background-color: #c4c4c4; color: #333333; font-size: 16px; font-weight: 600; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1);">
                            <flux:icon.x-mark class="size-5" />
                            {{ $isRefundReadOnly ? 'Tutup' : 'Batal' }}
                        </button>
                    </flux:modal.close>

                    @if (!$isRefundReadOnly)
                        <button wire:click="refundStore" type="button"
                            class="flex gap-[5px] items-center justify-center px-[25px] py-[10px] rounded-[15px]"
                            style="background-color: #3f4e4f; color: #f8f4e1; font-size: 16px; font-weight: 600; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.1);">
                            <flux:icon.receipt-refund class="size-5" />
                            Refund
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </flux:modal>

    <flux:modal class="w-full max-w-md" name="note" wire:model="noteModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Catatan Pesanan</flux:heading>
                <p class="text-sm text-gray-500 mt-2">Tambahkan atau ubah catatan untuk pesanan ini</p>
            </div>
            <div>
                <flux:label>Catatan</flux:label>
                <flux:textarea wire:model="note" rows="6" placeholder="Masukkan catatan pesanan..."
                    class="w-full" maxlength="500"></flux:textarea>
                <flux:error name="note" />
                <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button type="button" icon="x-mark">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="button" icon="check" variant="primary" wire:click="saveNote">
                    Simpan Catatan
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @script
        <script>
            window.addEventListener('open-wa', event => {
                window.open(event.detail[0].url, '_blank');
            });
        </script>
    @endscript

    @section('css')
        <style>
            .text-color-white {
                color: #ffffff !important;
            }

            .text-position-center {
                text-align: center !important;
            }

            .text-size-xs {
                font-size: 0.75rem !important;
            }

            .text-size-sm {
                font-size: 0.875rem !important;
            }
        </style>
    @endsection


</div>
