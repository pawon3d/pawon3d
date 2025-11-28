<div>
    {{-- Header with Back Button and Title --}}
    <div class="flex gap-[32px] items-center mb-8">
        <div class="flex gap-[15px] items-center">
            <a href="{{ route('transaksi') }}"
                class="bg-[#313131] flex items-center justify-center gap-[5px] px-[25px] py-[10px] rounded-[15px] shadow-sm hover:bg-[#252324] transition-colors"
                wire:navigate style="font-family: 'Montserrat', sans-serif;">
                <flux:icon icon="arrow-left" class="size-5 text-[#f8f4e1]" />
                <span class="font-semibold text-[16px] text-[#f8f4e1]">Kembali</span>
            </a>
            <p class="font-semibold text-[20px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                Daftar {{ $methodName }}
            </p>
        </div>
    </div>

    {{-- Content Container --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-[30px] py-[25px]">
        {{-- Search Bar --}}
        <div class="flex justify-between items-center mb-[20px]">
            <div class="flex-1 flex gap-[15px] items-center">
                {{-- Search Input --}}
                <div class="flex-1 bg-white border border-[#666666] rounded-[20px] px-[15px] py-0 flex items-center">
                    <flux:icon icon="magnifying-glass" class="size-[30px] text-[#666666]" />
                    <input wire:model.live="search" placeholder="Cari Pesanan" type="text"
                        class="flex-1 px-[10px] py-[10px] font-medium text-[16px] text-[#959595] border-0 focus:ring-0 focus:outline-none bg-transparent"
                        style="font-family: 'Montserrat', sans-serif;" />
                </div>

                {{-- Filter Button --}}
                <div class="flex items-center gap-[5px] cursor-pointer">
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
                [
                    'label' => $method == 'siap-beli' ? 'Tanggal Pembelian' : 'Tanggal Ambil',
                    'sortable' => true,
                    'sort-by' => 'date',
                    'field' => 'date',
                ],
                [
                    'label' => 'Daftar Produk',
                    'sortable' => true,
                    'sort-by' => 'product_name',
                    'field' => 'product_name',
                ],
            ];

            if ($method != 'siap-beli') {
                $headers[] = [
                    'label' => 'Pembeli',
                    'sortable' => true,
                    'sort-by' => 'name',
                    'field' => 'name',
                ];
            }

            $headers[] = [
                'label' => 'Kasir',
                'sortable' => true,
                'sort-by' => 'user_name',
                'field' => 'user_name',
            ];

            if ($method != 'siap-beli') {
                $headers[] = [
                    'label' => 'Status Bayar',
                    'sortable' => true,
                    'sort-by' => 'payment_status',
                    'field' => 'payment_status',
                ];
                $headers[] = [
                    'label' => 'Status Pesanan',
                    'sortable' => true,
                    'sort-by' => 'status',
                    'field' => 'status',
                ];
            }
        @endphp

        <x-table.paginated :headers="$headers" :paginator="$transactions" emptyMessage="Belum ada pesanan." headerBg="#3f4e4f"
            headerText="#f8f4e1" bodyBg="#fafafa" bodyText="#666666"
            wrapperClass="rounded-[15px] border border-[#d4d4d4]">
            @foreach ($transactions as $transaction)
                <tr class="border-b border-[#d4d4d4] hover:bg-[#f0f0f0] transition-colors">
                    {{-- ID Transaksi --}}
                    <td class="px-6 py-4">
                        <a href="{{ route('transaksi.rincian-pesanan', $transaction->id) }}"
                            class="font-medium text-[14px] text-[#666666] hover:underline"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ $transaction->invoice_number }}
                        </a>
                    </td>

                    {{-- Tanggal --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-[5px]">
                            <div class="flex gap-[10px] items-center">
                                <p class="font-medium text-[14px] text-[#666666]"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    @if ($method == 'siap-beli')
                                        {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d M Y') : '-' }}
                                    @else
                                        {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d M Y') : '-' }}
                                    @endif
                                </p>
                                <p class="font-medium text-[14px] text-[#666666]"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    @if ($method == 'siap-beli')
                                        {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('H:i') : '-' }}
                                    @else
                                        {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('H:i') : '-' }}
                                    @endif
                                </p>
                            </div>
                            @php
                                $targetDate = $method == 'siap-beli' ? $transaction->start_date : $transaction->date;
                                $daysUntil = $targetDate
                                    ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($targetDate), false)
                                    : null;
                            @endphp
                            @if ($daysUntil !== null)
                                <p class="font-medium text-[14px] text-center {{ $daysUntil <= 2 ? 'text-[#eb5757]' : 'text-[#3fa2f7]' }}"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    (H{{ $daysUntil >= 0 ? '+' : '' }}{{ $daysUntil }})
                                </p>
                            @endif
                        </div>
                    </td>

                    {{-- Daftar Produk --}}
                    <td class="px-6 py-4">
                        <p class="font-medium text-[14px] text-[#666666] truncate max-w-md"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ $transaction->details->count() > 0 ? $transaction->details->map(fn($d) => $d->product?->name)->filter()->implode(', ') : 'Tidak ada produk' }}
                        </p>
                    </td>

                    {{-- Pembeli (hidden for siap-beli) --}}
                    @if ($method != 'siap-beli')
                        <td class="px-6 py-4">
                            <p class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                {{ $transaction->name ?? '-' }}
                            </p>
                        </td>
                    @endif

                    {{-- Kasir --}}
                    <td class="px-6 py-4">
                        <p class="font-medium text-[14px] text-[#666666]"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ ucfirst($transaction->user->name) }}
                        </p>
                    </td>

                    {{-- Status Pembayaran (hidden for siap-beli) --}}
                    @if ($method != 'siap-beli')
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
                    @endif

                    {{-- Status Pesanan (hidden for siap-beli) --}}
                    @if ($method != 'siap-beli')
                        <td class="px-6 py-4">
                            @php
                                $orderStatus = strtolower($transaction->status);
                                if (in_array($orderStatus, ['dapat diambil', 'selesai'])) {
                                    $bgColor = '#3fa2f7';
                                    $textColor = '#fafafa';
                                    $label = 'Dapat Diambil';
                                } elseif (in_array($orderStatus, ['sedang diproses', 'diproses'])) {
                                    $bgColor = '#ffc400';
                                    $textColor = '#fafafa';
                                    $label = 'Sedang Diproses';
                                } elseif (in_array($orderStatus, ['belum diproses', 'pending'])) {
                                    $bgColor = '#adadad';
                                    $textColor = '#fafafa';
                                    $label = 'Belum Diproses';
                                } elseif ($orderStatus === 'draft') {
                                    $bgColor = '#f6f6f6';
                                    $textColor = '#666666';
                                    $label = 'Draft';
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
