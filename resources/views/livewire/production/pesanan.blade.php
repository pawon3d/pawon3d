<div>
    {{-- Header: Back Button + Title --}}
    <div class="flex items-center gap-4 mb-5">
        <a href="{{ route('produksi') }}"
            class="inline-flex items-center gap-[5px] px-[25px] py-[10px] bg-[#313131] text-white rounded-[15px] shadow-sm hover:bg-[#252324] transition font-['Montserrat'] font-semibold text-[16px]"
            wire:navigate>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali</span>
        </a>
        <h1 class="font-['Montserrat'] font-semibold text-[20px] text-[#666666]">Antrian {{ $methodName }}</h1>
    </div>

    {{-- Container with Background --}}
    <div class="bg-[#fafafa] rounded-[15px] p-[30px]">
        {{-- Search Bar + Filter Button --}}
        <div class="flex items-center gap-4 mb-4 w-full">
            <!-- Search Bar -->
            <div class="flex items-center border border-[#666666] rounded-[20px] bg-white px-4 py-2 w-full">
                <svg class="w-[30px] h-[30px] text-[#666666]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input wire:model.live="search" type="text" placeholder="Cari Produksi"
                    class="ml-2 flex-1 border-0 focus:ring-0 text-[16px] font-medium text-[#666666] placeholder:text-[#959595] w-full" />
            </div>

            <!-- Filter -->
            <div class="flex items-center gap-1 text-[#666666]">
                <svg class="w-[25px] h-[25px]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                </svg>
                <span class="text-[16px] font-medium px-1">Filter</span>
            </div>
        </div>

        {{-- Table --}}
        <x-table.paginated :paginator="$transactions" :headers="[
            ['label' => 'ID Transaksi', 'sortable' => true, 'sort-by' => 'invoice_number'],
            ['label' => 'Tanggal Ambil', 'sortable' => true, 'sort-by' => 'date'],
            ['label' => 'Daftar Produk'],
            ['label' => 'Pembeli', 'sortable' => true, 'sort-by' => 'customer_name'],
            ['label' => 'Kasir', 'sortable' => true, 'sort-by' => 'user_name'],
            ['label' => 'Status Pesanan', 'sortable' => true, 'sort-by' => 'status'],
        ]" headerBg="#3f4e4f" headerText="#f8f4e1" bodyBg="#fafafa"
            bodyText="#666666" wrapperClass="rounded-[15px] border-0 overflow-hidden">
            @foreach ($transactions as $transaction)
                @php
                    // Determine which date field to use based on method
                    $pickupDate = $method === 'siap-beli' ? $transaction->start_date : $transaction->date;
                    $pickupTime = $method !== 'siap-beli' ? $transaction->time : null;
                    $daysUntil = (int) \Carbon\Carbon::parse($pickupDate)->diffInDays(now(), false);
                    $countdownText = $daysUntil > 0 ? '(H+' . $daysUntil . ')' : '(H' . $daysUntil . ')';
                    $countdownColor = $daysUntil >= -3 ? '#eb5757' : '#3fa2f7';
                @endphp
                <tr class="border-b border-[#d4d4d4] hover:bg-[#f5f5f5]">
                    <td class="px-[25px] py-4">
                        <a href="{{ route('produksi.rincian-pesanan', $transaction->id) }}"
                            class="font-['Montserrat'] font-medium text-[14px] text-[#666666] hover:underline">
                            {{ $transaction->invoice_number }}
                        </a>
                    </td>

                    <td class="px-[25px] py-4">
                        <div
                            class="flex flex-col justify-center items-center gap-[5px] font-['Montserrat'] font-medium text-[14px]">
                            <div class="flex items-center gap-[10px] text-[#666666]">
                                <span>{{ \Carbon\Carbon::parse($pickupDate)->translatedFormat('d M Y') }}</span>
                                <span>{{ \Carbon\Carbon::parse($pickupTime)->format('H:i') }}</span>
                            </div>
                            <div class="font-semibold text-center" style="color: {{ $countdownColor }};">
                                {{ $countdownText }}
                            </div>
                        </div>
                    </td>

                    <td class="px-[25px] py-4">
                        @if ($transaction->details->count() > 0)
                            <div class="font-['Montserrat'] font-medium text-[14px] text-[#666666]">
                                {{ $transaction->details->map(fn($d) => $d->product?->name)->filter()->implode(', ') }}
                            </div>
                        @else
                            <span class="font-['Montserrat'] font-medium text-[14px] text-gray-400">Tidak ada
                                produk</span>
                        @endif
                    </td>

                    <td class="px-[25px] py-4">
                        <span
                            class="font-['Montserrat'] font-medium text-[14px] text-[#666666]">{{ $transaction->name ?? '-' }}</span>
                    </td>

                    <td class="px-[25px] py-4">
                        <span
                            class="font-['Montserrat'] font-medium text-[14px] text-[#666666]">{{ $transaction->user->name }}</span>
                    </td>

                    <td class="px-[25px] py-4">
                        <div
                            class="inline-flex flex-col items-center justify-center bg-[#adadad] rounded-[15px] px-[15px] py-[5px] min-h-[40px] min-w-[90px]">
                            <span
                                class="font-['Montserrat'] font-bold text-[12px] text-[#fafafa] leading-tight">Belum</span>
                            <span
                                class="font-['Montserrat'] font-bold text-[12px] text-[#fafafa] leading-tight">Diproses</span>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

</div>
