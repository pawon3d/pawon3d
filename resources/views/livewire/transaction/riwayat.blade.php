<div class="px-4 sm:px-0">
    {{-- Header with Back Button, Title and Cetak Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-[32px] mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-center w-full sm:w-auto">
            <a href="{{ route('transaksi') }}"
                class="w-full sm:w-auto bg-[#313131] flex items-center justify-center gap-[5px] px-[25px] py-[10px] rounded-[15px] shadow-sm hover:bg-[#252324] transition-colors"
                wire:navigate style="font-family: 'Montserrat', sans-serif;">
                <flux:icon icon="arrow-left" class="size-5 text-[#f8f4e1]" />
                <span class="font-semibold text-[16px] text-[#f8f4e1]">Kembali</span>
            </a>
            <p class="font-semibold text-[20px] text-[#666666] text-center sm:text-left" style="font-family: 'Montserrat', sans-serif;">
                Riwayat {{ $methodName }}
            </p>
        </div>
        {{-- 
        <div class="flex-1 flex justify-end">
            <flux:button variant="secondary" wire:click="cetakInformasi" icon="printer">
                Cetak Informasi
            </flux:button>
        </div> --}}
    </div>

    {{-- Content Container --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-4 sm:px-[30px] sm:py-[25px]">
        {{-- Search Bar --}}
        <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-[20px] gap-4">
            <div class="flex-1 flex flex-col sm:flex-row gap-[15px] items-center w-full">
                {{-- Search Input --}}
                <div class="flex-1 bg-white border border-[#666666] rounded-[20px] px-[15px] py-0 flex items-center w-full">
                    <flux:icon icon="magnifying-glass" class="size-[30px] text-[#666666]" />
                    <input wire:model.live="search" placeholder="Cari Pesanan" type="text"
                        class="flex-1 px-[10px] py-[10px] font-medium text-[16px] text-[#959595] border-0 focus:ring-0 focus:outline-none bg-transparent"
                        style="font-family: 'Montserrat', sans-serif;" />
                </div>

                {{-- Filter Button --}}
                <div class="flex items-center gap-[5px] cursor-pointer justify-center">
                    <flux:icon icon="funnel" class="size-[25px] text-[#666666]" />
                    <span class="font-medium text-[16px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                        Filter
                    </span>
                </div>
            </div>
        </div>

        {{-- Table --}}
        @php
            $headers = [
                [
                    'label' => 'ID Transaksi',
                    'sortable' => true,
                    'sort-by' => 'invoice_number',
                    'field' => 'invoice_number',
                ],
            ];

            if ($method == 'siap-beli') {
                $headers[] = [
                    'label' => 'Tanggal Beli',
                    'sortable' => true,
                    'sort-by' => 'date',
                    'field' => 'date',
                ];
            } else {
                $headers[] = [
                    'label' => 'Tanggal Ambil',
                    'sortable' => true,
                    'sort-by' => 'date',
                    'field' => 'date',
                ];
            }

            $headers[] = [
                'label' => 'Daftar Produk',
                'sortable' => true,
                'sort-by' => 'product_name',
                'field' => 'product_name',
            ];

            $headers[] = [
                'label' => 'Pembeli',
                'sortable' => true,
                'sort-by' => 'name',
                'field' => 'name',
            ];

            $headers[] = [
                'label' => 'Kasir',
                'sortable' => true,
                'sort-by' => 'user_name',
                'field' => 'user_name',
            ];

            $headers[] = [
                'label' => 'Status Bayar',
                'sortable' => true,
                'sort-by' => 'payment_status',
                'field' => 'payment_status',
            ];

            if ($method != 'siap-beli') {
                $headers[] = [
                    'label' => 'Status Pesanan',
                    'sortable' => true,
                    'sort-by' => 'status',
                    'field' => 'status',
                ];
            }
        @endphp

        <div class="overflow-x-auto">
            <div class="min-w-[1000px]">
                <x-table.paginated :headers="$headers" :paginator="$transactions" emptyMessage="Belum ada riwayat transaksi."
                    headerBg="#3f4e4f" headerText="#f8f4e1" bodyBg="#fafafa" bodyText="#666666"
                    wrapperClass="rounded-[15px] border border-[#d4d4d4]">
            @foreach ($transactions as $transaction)
                <tr class="border-b border-[#d4d4d4] hover:bg-[#f0f0f0] transition-colors">
                    {{-- ID Transaksi --}}
                    <td class="px-6 py-4">
                        <a href="{{ route('transaksi.rincian-pesanan', $transaction->id) }}"
                            class="font-medium text-[14px] text-[#666666] hover:underline"
                            style="font-family: 'Montserrat', sans-serif;" wire:navigate>
                            {{ $transaction->invoice_number }}
                        </a>
                    </td>

                    {{-- Tanggal --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-[10px] items-center">
                            <p class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                @if ($method == 'siap-beli')
                                    {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->translatedFormat('d F Y') : '-' }}
                                @else
                                    {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->translatedFormat('d F Y') : '-' }}
                                @endif
                            </p>
                            <p class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                @if ($method == 'siap-beli')
                                    {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('H:i') : '-' }}
                                @else
                                    {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '-' }}
                                @endif
                            </p>
                        </div>
                    </td>

                    {{-- Daftar Produk --}}
                    <td class="px-6 py-4">
                        <p class="font-medium text-[14px] text-[#666666] truncate max-w-md"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ $transaction->details->count() > 0 ? $transaction->details->map(fn($d) => $d->product?->name)->filter()->implode(', ') : 'Tidak ada produk' }}
                        </p>
                    </td>

                    {{-- Pembeli --}}
                    <td class="px-6 py-4">
                        <p class="font-medium text-[14px] text-[#666666]"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ $transaction->name ?? '-' }}
                        </p>
                    </td>

                    {{-- Kasir --}}
                    <td class="px-6 py-4">
                        <p class="font-medium text-[14px] text-[#666666]"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ ucfirst($transaction->user->name) }}
                        </p>
                    </td>

                    {{-- Status Pembayaran --}}
                    <td class="px-6 py-4">
                        @php
                            $paymentStatus = strtolower($transaction->payment_status);
                            if ($paymentStatus === 'lunas') {
                                $bgColor = '#56c568';
                                $textColor = '#fafafa';
                                $label = 'Lunas';
                            } elseif (in_array($paymentStatus, ['belum lunas', 'belum dibayar'])) {
                                $bgColor = '#ffc400';
                                $textColor = '#fafafa';
                                $label = 'Belum Lunas';
                            } elseif ($paymentStatus === 'batal') {
                                $bgColor = '#eb5757';
                                $textColor = '#fafafa';
                                $label = 'Batal';
                            } elseif ($paymentStatus === 'refund') {
                                $bgColor = '#eb5757';
                                $textColor = '#fafafa';
                                $label = 'Refund';
                            } else {
                                $bgColor = '#fafafa';
                                $textColor = '#666666';
                                $label = ucfirst($transaction->payment_status);
                            }
                        @endphp
                        <div class="flex items-center justify-center">
                            <div class="px-[15px] py-[5px] rounded-[15px] min-w-[90px] flex items-center justify-center"
                                style="background-color: {{ $bgColor }}; {{ $bgColor === '#fafafa' ? 'border: 1px solid #666666;' : '' }}">
                                <p class="font-bold text-[12px] text-center"
                                    style="font-family: 'Montserrat', sans-serif; color: {{ $textColor }};">
                                    {{ $label }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Status Pesanan (only for non siap-beli) --}}
                    @if ($method != 'siap-beli')
                        <td class="px-6 py-4">
                            @php
                                $orderStatus = strtolower($transaction->status);
                                if (in_array($orderStatus, ['selesai', 'dapat diambil'])) {
                                    $bgColor = '#56c568';
                                    $textColor = '#fafafa';
                                    $label = 'Selesai';
                                } elseif (in_array($orderStatus, ['batal', 'cancelled'])) {
                                    $bgColor = '#eb5757';
                                    $textColor = '#fafafa';
                                    $label = 'Batal';
                                } elseif ($orderStatus === 'refund') {
                                    $bgColor = '#eb5757';
                                    $textColor = '#fafafa';
                                    $label = 'Refund';
                                } else {
                                    $bgColor = '#fafafa';
                                    $textColor = '#666666';
                                    $label = ucfirst($transaction->status);
                                }
                            @endphp
                            <div class="flex items-center justify-center">
                                <div class="px-[15px] py-[5px] rounded-[15px] min-w-[90px] flex items-center justify-center"
                                    style="background-color: {{ $bgColor }}; {{ in_array($bgColor, ['#fafafa', '#f6f6f6']) ? 'border: 1px solid #666666;' : '' }}">
                                    <p class="font-bold text-[12px] text-center"
                                        style="font-family: 'Montserrat', sans-serif; color: {{ $textColor }};">
                                        {{ $label }}
                                    </p>
                                </div>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
                </x-table.paginated>
            </div>
        </div>
    </div>
</div>
