<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Daftar Belanja Persediaan</h1>
        <flux:button type="button" wire:click="riwayatPembaruan" variant="filled">
            Riwayat Pembaruan
        </flux:button>
    </div>

    <x-alert.info>
        Belanja Persediaan. Belanja Persediaan digunakan untuk menentukan harga dari persediaan, sehingga modal
        suatu produk dapat ditentukan dengan tepat. Belanja dapat dilakukan dengan mendatangi toko atau memesan
        lewat telepon dan whatsapp.
    </x-alert.info>

    <div class="mt-4 bg-[#fafafa] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] rounded-[15px] px-[30px] py-[25px]">
        <div class="flex justify-between items-center mb-5">
            <!-- Search Input -->
            <div class="flex gap-[15px] items-center max-w-[350px]">
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
            <div class="flex gap-2.5 items-center">
                <flux:button type="button" variant="primary" icon="archive-box" href="{{ route('supplier') }}"
                    wire:navigate>
                    Toko Persediaan
                </flux:button>
                <flux:button type="button" variant="primary" icon="list-bullet" href="{{ route('belanja.rencana') }}"
                    wire:navigate>
                    Rencana Belanja
                </flux:button>
                <flux:button type="button" variant="primary" icon="clock" href="{{ route('belanja.riwayat') }}"
                    wire:navigate square>
                </flux:button>
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
                'label' => 'Tanggal Belanja',
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
            empty-message="Belum Ada Belanja. Tekan tombol 'Rencana Belanja' untuk membuat belanja baru."
            wrapper-class="overflow-hidden rounded-[15px]">
            @foreach ($expenses as $expense)
                <tr class="h-[60px] border-b border-[#d4d4d4]">
                    <td class="px-6 py-0">
                        <a href="{{ route('belanja.rincian', $expense->id) }}"
                            class="text-[14px] font-medium text-[#666666] hover:underline cursor-pointer overflow-ellipsis overflow-hidden block">
                            {{ $expense->expense_number }}
                        </a>
                    </td>
                    <td class="px-6 py-0">
                        <span class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                            {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '-' }}
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
                        <div
                            class="inline-flex items-center justify-center px-[15px] py-1.5 bg-[#ffc400] rounded-[15px] min-h-[40px] min-w-[90px]">
                            <div class="flex flex-col text-[12px] font-bold text-[#fafafa] leading-normal text-center">
                                <span>Sedang</span>
                                <span>Diproses</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-0">
                        @php
                            $total_expect = $expense->expenseDetails->sum('quantity_expect');
                            $total_get = $expense->expenseDetails->sum('quantity_get');
                            $percentage = $total_expect > 0 ? ($total_get / $total_expect) * 100 : 0;
                        @endphp

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

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Barang Persediaan</flux:heading>
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
