<div>
    <div class="flex gap-8 items-center h-10 mb-[30px]">
        <div class="flex gap-[15px] items-center">
            <flux:button type="button" variant="filled" icon="arrow-left" href="{{ route('belanja') }}" wire:navigate>
                Kembali
            </flux:button>
            <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Riwayat Belanja Persediaan</h1>
        </div>
    </div>

    <div class="bg-[#fafafa] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] rounded-[15px] px-[30px] py-[25px]">
        <div class="flex items-center mb-5">
            <div class="flex gap-[15px] items-center flex-1">
                <div class="flex items-center bg-white border border-[#666666] rounded-[20px] px-[15px] py-0 flex-1">
                    <flux:icon.magnifying-glass class="size-[16px] text-[#666666]" />
                    <input wire:model.live="search" placeholder="Cari Belanja"
                        class="px-2.5 py-2.5 border-0 focus:outline-none focus:ring-0 text-[16px] font-medium text-[#959595] bg-transparent flex-1" />
                </div>
                <div class="flex items-center cursor-pointer">
                    <flux:icon.funnel class="size-[25px] text-[#666666]" />
                    <p class="text-[16px] font-medium text-[#666666] px-1.5 py-2.5">Filter</p>
                </div>
            </div>
        </div>

        <x-table.paginated :paginator="$expenses" :headers="[
            [
                'label' => 'ID Belanja',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'expense_number',
                'class' => 'min-w-[170px]',
            ],
            [
                'label' => 'Tanggal Selesai',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'expense_date',
                'class' => 'max-w-[150px]',
            ],
            [
                'label' => 'Toko Persediaan',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'supplier_name',
                'class' => 'w-[192px]',
            ],
            [
                'label' => 'Total Harga',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'grand_total_expect',
                'class' => 'max-w-[160px]',
                'align' => 'right',
            ],
            [
                'label' => 'Total Harga (Bayar)',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'grand_total_actual',
                'class' => 'max-w-[160px]',
                'align' => 'right',
            ],
            [
                'label' => 'Status',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'status',
                'class' => 'max-w-[120px]',
            ],
            ['label' => 'Kemajuan Persediaan', 'class' => 'min-w-[100px]'],
        ]" header-bg="#3f4e4f" header-text="#f8f4e1"
            body-bg="#fafafa" body-text="#666666"
            empty-message="Belum Ada Riwayat Belanja. Tekan tombol 'Tambah belanja' di halaman utama untuk menambahkan belanja."
            wrapper-class="overflow-hidden rounded-[15px]">
            @foreach ($expenses as $expense)
                @php
                    $total_expect = $expense->expenseDetails->sum('quantity_expect');
                    $total_get = $expense->expenseDetails->sum('quantity_get');
                    $percentage = $total_expect > 0 ? ($total_get / $total_expect) * 100 : 0;
                    $status = $percentage >= 100 ? 'Selesai' : ($percentage > 0 ? 'Selesai' : 'Batal');
                @endphp
                <tr class="h-[60px] border-b border-[#d4d4d4]">
                    <td class="px-6 py-0">
                        <a href="{{ route('belanja.rincian', $expense->id) }}"
                            class="text-[14px] font-medium text-[#666666] hover:underline cursor-pointer overflow-ellipsis overflow-hidden block">
                            {{ $expense->expense_number }}
                        </a>
                    </td>
                    <td class="px-6 py-0">
                        <span class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                            {{ $expense->end_date ? \Carbon\Carbon::parse($expense->end_date)->format('d M Y') : '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-0">
                        <span class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                            {{ $expense->supplier->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-0 text-right">
                        <span class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                            Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-6 py-0 text-right">
                        <span class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                            Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-6 py-0">
                        @if ($status === 'Selesai')
                            <div
                                class="inline-flex items-center justify-center px-[15px] py-1.5 bg-[#56c568] rounded-[15px] min-h-[40px] min-w-[90px]">
                                <span class="text-[12px] font-bold text-[#fafafa] leading-normal">Selesai</span>
                            </div>
                        @else
                            <div
                                class="inline-flex items-center justify-center px-[15px] py-1.5 bg-[#eb5757] rounded-[15px] min-h-[40px] min-w-[90px]">
                                <span class="text-[12px] font-bold text-[#fafafa] leading-normal">Batal</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-0">
                        <div class="flex flex-col gap-1.5 w-full">
                            <div class="w-full h-[18px] bg-[#d4d4d4] rounded-[5px] relative overflow-hidden">
                                <div class="h-full bg-[rgba(86,197,104,0.8)] rounded-[5px] absolute top-0 left-0"
                                    style="width: {{ number_format($percentage, 0) }}%">
                                </div>
                            </div>
                            <span class="text-[12px] font-medium text-[#525252] text-center">
                                {{ number_format($percentage, 0) }}%
                            </span>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

</div>
