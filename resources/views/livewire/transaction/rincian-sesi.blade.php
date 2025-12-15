<div class="flex flex-col gap-[30px] px-[30px] py-[30px]">
    {{-- Header with Back Button, Session Info, and View Details Button --}}
    <div class="flex items-center justify-between h-[40px]">
        <div class="flex items-center gap-[15px]">
            {{-- Back Button --}}
            <flux:button variant="secondary" href="{{ route('transaksi.riwayat-sesi') }}" wire:navigate
                class="flex items-center justify-center gap-[5px] px-[25px] py-[10px] bg-[#313131] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
                <flux:icon.arrow-left class="w-[20px] h-[20px] text-[#f8f4e1]" />
                <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f8f4e1] leading-[1]">Kembali</span>
            </flux:button>

            {{-- Session Info --}}
            <p class="font-['Montserrat'] font-medium text-[20px] text-[#333333] leading-[1] whitespace-nowrap">
                Sesi {{ $shift->shift_number }}
            </p>
            <p class="font-['Montserrat'] font-medium text-[20px] text-[#333333] leading-[1]">:</p>
            <p class="font-['Montserrat'] font-medium text-[20px] text-[#333333] leading-[1] whitespace-nowrap">
                {{ $shift->openedBy->name }}
            </p>
        </div>

        {{-- View Details Button --}}
        <button wire:click="$dispatch('openDetailShiftModal', { shiftId: '{{ $shift->id }}' })"
            class="flex items-center justify-center gap-[5px] px-[25px] py-[10px] bg-[#313131] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon.cashier class="w-[20px] h-[20px] text-white" />
            <span class="font-['Montserrat'] font-semibold text-[16px] text-[#f6f6f6] leading-[1]">Lihat Rincian
                Sesi</span>
        </button>
    </div>

    {{-- Main Content Card --}}
    <div class="bg-[#fafafa] rounded-[15px] px-[30px] py-[25px] flex flex-col gap-[30px]">
        {{-- Search and Filter --}}
        <div class="flex items-center gap-[30px]">
            <div class="flex-1 flex items-center gap-[15px]">
                {{-- Search Input --}}
                <div class="flex-1 flex items-center gap-0 bg-white border border-[#666666] rounded-[20px] px-[15px]">
                    <flux:icon.magnifying-glass class="w-[30px] h-[30px] text-[#666666]" />
                    <input type="text" wire:model.live="search" placeholder="Cari Struk"
                        class="flex-1 px-[10px] py-[10px] font-['Montserrat'] font-medium text-[16px] text-[#666666] placeholder-[#959595] border-0 focus:ring-0 focus:outline-none leading-[1]">
                </div>

                {{-- Filter Button --}}
                <div class="flex items-center gap-0">
                    <flux:icon.funnel class="w-[25px] h-[25px] text-[#666666]" />
                    <button class="px-[5px] py-[10px]">
                        <span
                            class="font-['Montserrat'] font-medium text-[16px] text-[#666666] leading-[1]">Filter</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <x-table.paginated :headers="[
            ['label' => 'ID Struk', 'sortable' => true, 'sort-by' => 'receipt_number', 'class' => 'w-[160px]'],
            ['label' => 'ID Transaksi', 'sortable' => true, 'sort-by' => 'invoice_number', 'class' => 'w-[180px]'],
            ['label' => 'Jenis Penjualan', 'sortable' => true, 'sort-by' => 'method', 'class' => 'w-[180px]'],
            ['label' => 'Pembeli', 'sortable' => true, 'sort-by' => 'name', 'class' => 'w-[180px]'],
            ['label' => 'Status Bayar', 'sortable' => true, 'sort-by' => 'payment_status'],
            ['label' => 'Tanggal Cetak', 'sortable' => true, 'sort-by' => 'created_at', 'class' => 'w-[190px]'],
        ]" :paginator="$transactions" emptyMessage="Tidak ada data transaksi."
            headerBg="#3f4e4f" headerText="#f8f4e1" bodyBg="#fafafa" bodyText="#666666"
            wrapperClass="rounded-[15px] border border-[#d4d4d4]">
            @foreach ($transactions as $transaction)
                <tr class="border-b border-[#d4d4d4] hover:bg-[#f0f0f0] transition-colors">
                    {{-- ID Struk --}}
                    <td class="px-6 py-4">
                        @if ($transaction->payments->count() > 0 || $transaction->refund)
                            <div class="flex flex-col gap-[5px]">
                                @foreach ($transaction->payments as $payment)
                                    <p wire:click="showStrukModal('{{ $payment->id }}')"
                                        class="font-medium text-[14px] text-[#666666] truncate cursor-pointer hover:text-[#3f4e4f] hover:underline"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        {{ $payment->receipt_number }}
                                    </p>
                                @endforeach
                                @if ($transaction->refund)
                                    <p wire:click="showRefundStrukModal('{{ $transaction->id }}')"
                                        class="font-medium text-[14px] text-[#eb5757] truncate cursor-pointer hover:text-[#c44545] hover:underline"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        Refund
                                    </p>
                                @endif
                            </div>
                        @else
                            <span class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">-</span>
                        @endif
                    </td>

                    {{-- ID Transaksi --}}
                    <td class="px-6 py-4">
                        <a href="{{ route('transaksi.rincian-pesanan', ['id' => $transaction->id]) }}"
                            class="hover:underline" wire:navigate>
                            <span class="font-medium text-[14px] text-[#666666] truncate"
                                style="font-family: 'Montserrat', sans-serif;">
                                {{ $transaction->invoice_number }}
                            </span>
                        </a>
                    </td>

                    {{-- Jenis Penjualan --}}
                    <td class="px-6 py-4">
                        <span class="font-medium text-[14px] text-[#666666]"
                            style="font-family: 'Montserrat', sans-serif;">
                            @if ($transaction->method == 'pesanan-reguler')
                                Pesanan Reguler
                            @elseif($transaction->method == 'siap-beli')
                                Siap Saji
                            @elseif($transaction->method == 'pesanan-kotak')
                                Pesanan Kotak
                            @else
                                {{ $transaction->method }}
                            @endif
                        </span>
                    </td>

                    {{-- Pembeli --}}
                    <td class="px-6 py-4">
                        <span class="font-medium text-[14px] text-[#666666] truncate"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ $transaction->name ?? 'Umum' }}
                        </span>
                    </td>

                    {{-- Status Bayar --}}
                    <td class="px-6 py-4">
                        @if ($transaction->payment_status == 'Lunas')
                            <div
                                class="bg-[#56c568] rounded-[15px] px-[15px] py-[5px] min-w-[90px] inline-flex items-center justify-center">
                                <span class="font-bold text-[12px] text-[#fafafa]"
                                    style="font-family: 'Montserrat', sans-serif;">Lunas</span>
                            </div>
                        @elseif ($transaction->payment_status == 'Belum Lunas')
                            <div
                                class="bg-[#ffc400] rounded-[15px] px-[15px] py-[5px] min-w-[90px] inline-flex items-center justify-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="font-bold text-[12px] text-[#fafafa] leading-[1.2]"
                                        style="font-family: 'Montserrat', sans-serif;">Belum</span>
                                    <span class="font-bold text-[12px] text-[#fafafa] leading-[1.2]"
                                        style="font-family: 'Montserrat', sans-serif;">Lunas</span>
                                </div>
                            </div>
                        @elseif ($transaction->payment_status == 'Refund')
                            <div
                                class="bg-[#ff4d4d] rounded-[15px] px-[15px] py-[5px] min-w-[90px] inline-flex items-center justify-center">
                                <span class="font-bold text-[12px] text-[#fafafa]"
                                    style="font-family: 'Montserrat', sans-serif;">Refund</span>
                            </div>
                        @endif
                    </td>

                    {{-- Tanggal Cetak --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center flex-row flex-nowrap gap-[10px]">
                            <span class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                {{ \Carbon\Carbon::parse($transaction->created_at)->translatedFormat('d F Y') }}
                            </span>
                            <span class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i') }}
                            </span>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    {{-- Modal Struk --}}
    @if ($showStruk && $selectedPayment && $selectedTransaction)
        <div class="fixed inset-0 top-0 bottom-0 overflow-y-scroll bg-gray-100/95 z-50" wire:ignore.self>
            <div class="w-full px-4">
                <div class="relative min-h-screen pb-32">
                    <div class="fixed top-2 right-4 z-50">
                        <flux:button type="button" icon="x-mark" wire:click="closeStrukModal" variant="ghost" />
                    </div>

                    <div class="w-full max-w-[280px] mt-8 mb-4 mx-auto bg-[#fafafa] border-[0.5px] border-[#666666] rounded-[15px] shadow-md"
                        style="font-family: Montserrat, sans-serif;">
                        <div class="flex flex-col gap-[15px] items-center px-[20px] pt-[30px] pb-[100px]">

                            {{-- Header Info --}}
                            <div class="flex flex-col items-center justify-center w-full">
                                <div class="flex items-start justify-between pb-[15px] w-full">
                                    <div class="flex-1 flex flex-col gap-[12px] items-center">
                                        {{-- Logo Pawon3D --}}
                                        <div class="flex flex-col justify-center leading-[50px] text-center text-black"
                                            style="font-family: Pacifico, cursive; font-size: 0;">
                                            <p class="whitespace-pre">
                                                <span
                                                    style="font-size: 32px;">{{ $storeProfile->name != '' ? $storeProfile->name : 'Pawon3D' }}</span>
                                            </p>
                                        </div>
                                        {{-- Alamat --}}
                                        <div class="flex flex-col gap-[4px] items-center text-center text-black"
                                            style="font-size: 10px; line-height: 1.2;">
                                            <div class="flex flex-col justify-center">
                                                <p class="mb-0">
                                                    {{ $storeProfile->address != '' ? $storeProfile->address : 'Jl. Jenderal Sudirman Km.3 RT.25 RW.07 Kel. Muara Bulian, Kec.Muara Bulian, Kab.Batang Hari, Jambi, 36613' }}
                                                </p>
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <p class="whitespace-pre">
                                                    {{ $storeProfile->contact != '' ? $storeProfile->contact : '081122334455' }}
                                                </p>
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <p class="whitespace-pre">{{ config('app.url') }}</p>
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
                                            <p class="break-all">{{ $selectedTransaction->invoice_number }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Tanggal Bayar</p>
                                        </div>
                                        <div class="font-medium text-right">
                                            <p>
                                                {{ $selectedPayment->paid_at ? \Carbon\Carbon::parse($selectedPayment->paid_at)->translatedFormat('d M Y H:i') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Status Bayar</p>
                                        </div>
                                        <div class="font-medium text-right"
                                            style="color: {{ $selectedTransaction->payment_status == 'Lunas' ? '#56c568' : '#ffc400' }};">
                                            <p>{{ $selectedTransaction->payment_status }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Kasir</p>
                                        </div>
                                        <div class="font-medium text-right">
                                            <p class="break-words">{{ $selectedTransaction->user->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Daftar Produk --}}
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    @foreach ($selectedTransaction->details as $detail)
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
                                            <div class="flex gap-[4px] items-center w-full font-normal text-black">
                                                <p>{{ $detail->quantity }}</p>
                                                <p>x</p>
                                                <p>Rp{{ number_format($detail->product->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Total --}}
                                @php
                                    $subtotal = $selectedTransaction->details->sum(function ($detail) {
                                        return $detail->quantity * $detail->product->price;
                                    });
                                    $totalItems = $selectedTransaction->details->sum('quantity');
                                    $pointsDiscount = $selectedTransaction->points_discount ?? 0;
                                    $pointsUsed = $selectedTransaction->points_used ?? 0;
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
                                    @if ($pointsUsed > 0)
                                        <div class="flex items-center justify-between w-full text-black">
                                            <div class="flex gap-[4px] items-center font-normal">
                                                <p class="whitespace-nowrap">Tukar {{ $pointsUsed }} Poin</p>
                                            </div>
                                            <p class="font-medium whitespace-nowrap">
                                                -Rp{{ number_format($pointsDiscount, 0, ',', '.') }}</p>
                                        </div>
                                    @endif
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Total Tagihan</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp{{ number_format($subtotal - $pointsDiscount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pembayaran --}}
                                @php
                                    $allPayments = $selectedTransaction->payments;
                                    $totalPaid = $allPayments->sum('paid_amount');
                                    $remainingAmount = $subtotal - $totalPaid;

                                    // Info untuk pembayaran yang dipilih (struk ini)
                                    $method = $selectedPayment->payment_method
                                        ? ucfirst($selectedPayment->payment_method)
                                        : '-';
                                    $bank = $selectedPayment->channel->bank_name ?? null;

                                    // Tentukan tipe pembayaran untuk struk ini
                                    $paymentCount = $allPayments->count();
                                    $currentPaymentIndex = $allPayments->search(
                                        fn($p) => $p->id === $selectedPayment->id,
                                    );

                                    if ($paymentCount == 1) {
                                        $tipe = 'Lunas';
                                    } else {
                                        $tipe = $currentPaymentIndex == $paymentCount - 1 ? 'Lunas' : 'Uang Muka';
                                    }

                                    $paymentLabel = "($tipe) $method" . ($bank ? " - $bank" : '');

                                    // Hitung sisa tagihan setelah pembayaran ini
                                    $paidBeforeThis = $allPayments
                                        ->filter(fn($p, $idx) => $idx <= $currentPaymentIndex)
                                        ->sum('paid_amount');
                                    $remainingAfterThis = $subtotal - $paidBeforeThis;
                                @endphp
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Total Bayar</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp{{ number_format($selectedPayment->paid_amount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between gap-2 w-full text-black font-normal">
                                        <div class="break-words flex-1">
                                            <p>{{ $paymentLabel }}</p>
                                        </div>
                                        <div class="whitespace-nowrap">
                                            <p>Rp{{ number_format($selectedPayment->paid_amount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full"
                                        style="color: {{ $remainingAfterThis > 0 ? '#eb5757' : '#000000' }};">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Sisa Tagihan</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp{{ number_format($remainingAfterThis, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer Info --}}
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>ID Struk</p>
                                        </div>
                                        <div class="font-medium">
                                            <p>{{ $selectedPayment->receipt_number }}</p>
                                        </div>
                                    </div>
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
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Struk Refund --}}
    @if ($showRefundStruk && $selectedRefundTransaction)
        <div class="fixed inset-0 top-0 bottom-0 overflow-y-scroll bg-gray-100/95 z-50" wire:ignore.self>
            <div class="w-full px-4">
                <div class="relative min-h-screen pb-32">
                    <div class="fixed top-2 right-4 z-50">
                        <flux:button type="button" icon="x-mark" wire:click="closeRefundStrukModal"
                            variant="ghost" />
                    </div>

                    <div class="w-full max-w-[280px] mt-8 mb-4 mx-auto bg-[#fafafa] border-[0.5px] border-[#666666] rounded-[15px] shadow-md"
                        style="font-family: Montserrat, sans-serif;">
                        <div class="flex flex-col gap-[15px] items-center px-[20px] pt-[30px] pb-[100px]">

                            {{-- Header Info --}}
                            <div class="flex flex-col items-center justify-center w-full">
                                <div class="flex items-start justify-between pb-[15px] w-full">
                                    <div class="flex-1 flex flex-col gap-[12px] items-center">
                                        {{-- Logo Pawon3D --}}
                                        <div class="flex flex-col justify-center leading-[50px] text-center text-black"
                                            style="font-family: Pacifico, cursive; font-size: 0;">
                                            <p class="whitespace-pre">
                                                <span
                                                    style="font-size: 32px;">{{ $storeProfile->name != '' ? $storeProfile->name : 'Pawon3D' }}</span>
                                            </p>
                                        </div>
                                        {{-- Alamat --}}
                                        <div class="flex flex-col gap-[4px] items-center text-center text-black"
                                            style="font-size: 10px; line-height: 1.2;">
                                            <div class="flex flex-col justify-center">
                                                <p class="mb-0">
                                                    {{ $storeProfile->address != '' ? $storeProfile->address : 'Jl. Jenderal Sudirman Km.3 RT.25 RW.07 Kel. Muara Bulian, Kec.Muara Bulian, Kab.Batang Hari, Jambi, 36613' }}
                                                </p>
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <p class="whitespace-pre">
                                                    {{ $storeProfile->contact != '' ? $storeProfile->contact : '081122334455' }}
                                                </p>
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <p class="whitespace-pre">{{ config('app.url') }}</p>
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
                                            <p class="break-all">{{ $selectedRefundTransaction->invoice_number }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Tanggal Refund</p>
                                        </div>
                                        <div class="font-medium text-right">
                                            <p>
                                                {{ $selectedRefundTransaction->refund && $selectedRefundTransaction->refund->refunded_at ? \Carbon\Carbon::parse($selectedRefundTransaction->refund->refunded_at)->translatedFormat('d M Y H:i') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Status Bayar</p>
                                        </div>
                                        <div class="font-medium text-right" style="color: #eb5757;">
                                            <p>Refund</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Kasir</p>
                                        </div>
                                        <div class="font-medium text-right">
                                            <p class="break-words">{{ $selectedRefundTransaction->user->name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if ($selectedRefundTransaction->refund && $selectedRefundTransaction->refund->reason)
                                        <div class="flex items-start justify-between w-full text-black">
                                            <div class="font-normal whitespace-nowrap">
                                                <p>Alasan Refund</p>
                                            </div>
                                            <div class="font-medium text-right">
                                                <p class="break-words">
                                                    {{ $selectedRefundTransaction->refund->reason }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Daftar Produk --}}
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    @foreach ($selectedRefundTransaction->details as $detail)
                                        <div class="flex flex-col gap-[6px] items-start w-full">
                                            <div class="flex items-start justify-between gap-2 w-full text-black">
                                                <div class="font-normal flex-1">
                                                    <p class="break-words">{{ $detail->product->name }}</p>
                                                </div>
                                                <div class="font-medium whitespace-nowrap">
                                                    <p>Rp{{ number_format($detail->price * $detail->quantity, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex gap-[4px] items-center w-full font-normal text-black">
                                                <p>{{ $detail->quantity }}</p>
                                                <p>x</p>
                                                <p>Rp{{ number_format($detail->price, 0, ',', '.') }}</p>
                                            </div>
                                            @if ($detail->refund_quantity > 0)
                                                <div class="flex items-start justify-between gap-2 w-full"
                                                    style="color: #eb5757;">
                                                    <div class="font-normal flex-1">
                                                        <p class="break-words">Refund {{ $detail->refund_quantity }}
                                                        </p>
                                                    </div>
                                                    <div class="font-medium whitespace-nowrap">
                                                        <p>-Rp{{ number_format($detail->price * $detail->refund_quantity, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Total --}}
                                @php
                                    $subtotal = $selectedRefundTransaction->details->sum(function ($detail) {
                                        return $detail->quantity * $detail->price;
                                    });
                                    $totalItems = $selectedRefundTransaction->details->sum('quantity');
                                    $totalRefund = $selectedRefundTransaction->refund
                                        ? $selectedRefundTransaction->refund->total_amount
                                        : 0;
                                    $pointsDiscount = $selectedRefundTransaction->points_discount ?? 0;
                                    $pointsUsed = $selectedRefundTransaction->points_used ?? 0;
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
                                    @if ($pointsUsed > 0)
                                        <div class="flex items-center justify-between w-full text-black">
                                            <div class="flex gap-[4px] items-center font-normal">
                                                <p class="whitespace-nowrap">Tukar {{ $pointsUsed }} Poin</p>
                                            </div>
                                            <p class="font-medium whitespace-nowrap">
                                                -Rp{{ number_format($pointsDiscount, 0, ',', '.') }}</p>
                                        </div>
                                    @endif
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Total Tagihan</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp{{ number_format($subtotal - $pointsDiscount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pembayaran --}}
                                @php
                                    $allPayments = $selectedRefundTransaction->payments;
                                    $totalPaid = $allPayments->sum('paid_amount');
                                    $refund = $selectedRefundTransaction->refund;
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

                                    {{-- Refund Entry (di atas) --}}
                                    @if ($refund)
                                        @php
                                            $refundMethodLabel =
                                                $refund->refund_method == 'tunai'
                                                    ? 'Tunai'
                                                    : 'Transfer' .
                                                        ($refund->channel ? ' - ' . $refund->channel->bank_name : '');
                                        @endphp
                                        <div class="flex items-start justify-between gap-2 w-full font-normal"
                                            style="color: #eb5757;">
                                            <div class="break-words flex-1">
                                                <p>(Refund) {{ $refundMethodLabel }}</p>
                                            </div>
                                            <div class="whitespace-nowrap font-medium">
                                                <p>-Rp{{ number_format($refund->total_amount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Semua Pembayaran --}}
                                    @foreach ($allPayments as $index => $payment)
                                        @php
                                            $method = $payment->payment_method
                                                ? ucfirst($payment->payment_method)
                                                : '-';
                                            $bank = $payment->channel->bank_name ?? null;

                                            $paymentCount = $allPayments->count();
                                            if ($paymentCount == 1) {
                                                $tipe = 'Lunas';
                                            } else {
                                                $tipe = $index == $paymentCount - 1 ? 'Lunas' : 'Uang Muka';
                                            }

                                            $paymentLabel = "($tipe) $method" . ($bank ? " - $bank" : '');
                                        @endphp
                                        <div
                                            class="flex items-start justify-between gap-2 w-full text-black font-normal">
                                            <div class="break-words flex-1">
                                                <p>{{ $paymentLabel }}</p>
                                            </div>
                                            <div class="whitespace-nowrap">
                                                <p>Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="flex items-start justify-between w-full" style="color: #666666;">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>Sisa Tagihan</p>
                                        </div>
                                        <div class="font-medium whitespace-nowrap">
                                            <p>Rp0</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer Info --}}
                                <div class="border-b border-dashed border-black flex flex-col gap-[10px] items-center justify-center py-[12px] w-full"
                                    style="font-size: 10px; line-height: 1.3;">
                                    <div class="flex items-start justify-between w-full text-black">
                                        <div class="font-normal whitespace-nowrap">
                                            <p>ID Transaksi</p>
                                        </div>
                                        <div class="font-medium">
                                            <p>{{ $selectedRefundTransaction->invoice_number }}</p>
                                        </div>
                                    </div>
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
                                    <p>Terima Kasih Atas Pengertian Anda</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detail Rincian Sesi -->
    <flux:modal name="detail-shift-modal" class="w-full max-w-[490px] h-full" wire:model="showDetailShiftModal">
        <div class="bg-[#fafafa] rounded-[15px] flex flex-col">
            <div class="flex flex-col gap-[30px]">
                {{-- Header --}}
                <div class="flex items-center justify-center">
                    <h2 class="text-[#666666] font-bold text-[20px]"
                        style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                        Laporan Sesi
                    </h2>
                </div>

                {{-- Content Area (Scrollable) --}}
                <div class="flex flex-col gap-[30px] h-[500px]">
                    <div class="flex-1 overflow-y-auto px-[30px] py-[40px]">
                        {{-- Info Sesi --}}
                        <div class="flex flex-col gap-[15px]">
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    No. Sesi
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    {{ $detailShiftNumber }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tanggal Buka
                                </span>
                                <div class="flex items-center gap-[10px] text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    @if ($detailShiftStartTime)
                                        <span>{{ \Carbon\Carbon::parse($detailShiftStartTime)->translatedFormat('d F Y') }}</span>
                                        <span>{{ \Carbon\Carbon::parse($detailShiftStartTime)->format('H:i') }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tanggal Tutup
                                </span>
                                <div class="flex items-center gap-[10px] text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    @if ($detailShiftEndTime)
                                        <span>{{ \Carbon\Carbon::parse($detailShiftEndTime)->translatedFormat('d F Y') }}</span>
                                        <span>{{ \Carbon\Carbon::parse($detailShiftEndTime)->format('H:i') }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Kasir
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    {{ $detailShiftOpenedBy }}
                                </span>
                            </div>
                        </div>

                        {{-- Section: Tunai --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tunai
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Awal
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailInitialCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailReceivedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailRefundCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Diharapkan
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailExpectedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Sebenarnya
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailFinalCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Selisih Jumlah
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailFinalCash - $detailExpectedCash, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Section: Terima Pembayaran --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Terima Pembayaran
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailReceivedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] flex items-center gap-2 font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Transfer
                                    <flux:button icon="information-circle" iconVariant="outline" variant="ghost"
                                        type="button" wire:click="showNonCashDetails('{{ $shiftId }}')" />
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailReceivedNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Section: Pendapatan --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan Kotor
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailReceivedCash + $detailReceivedNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailRefundCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Non Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailRefundNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Potongan Harga
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailDiscountToday, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan Bersih
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($detailReceivedCash + $detailReceivedNonCash - $detailRefundTotal - $detailDiscountToday, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer: Action Button (Sticky) --}}
            <div class="px-[30px] w-full">
                <flux:modal.close class="w-full">
                    <button type="button"
                        class="w-full bg-[#3f4e4f] rounded-[15px] px-[25px] py-[10px] flex items-center justify-center gap-[5px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] hover:bg-[#2f3e3f] transition-colors">
                        <flux:icon icon="arrow-long-left" variant="solid" class="size-5 text-[#f8f4e1]" />
                        <span class="text-[#f8f4e1] font-semibold text-[16px]"
                            style="font-family: 'Montserrat', sans-serif;">
                            Tutup
                        </span>
                    </button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Non Cash Details -->
    <flux:modal name="non-cash-details-modal" class="w-full max-w-lg max-h-[90vh] flex flex-col"
        wire:model="showNonCashDetailsModal">
        <div class="flex flex-col flex-1 min-h-0">
            <div class="p-4">
                <flux:heading size="lg">Pembayaran Non Tunai</flux:heading>
            </div>
            <div class="flex-1 overflow-y-auto px-4 pb-2">
                @if (count($nonCashDetails) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">ID Struk</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">Bank</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-500">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($nonCashDetails as $detail)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-900">{{ $detail['receipt_number'] }}</td>
                                        <td class="px-3 py-2 text-gray-500">{{ $detail['bank_name'] }}</td>
                                        <td class="px-3 py-2 text-right text-gray-900">
                                            Rp{{ number_format($detail['paid_amount'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-center text-gray-500 py-8">Tidak ada pembayaran non tunai</p>
                @endif
            </div>

            <div class="p-4 border-t mt-auto">
                <flux:modal.close class="w-full">
                    <flux:button type="button" variant="secondary" icon="arrow-long-left" class="w-full">
                        Kembali
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>
