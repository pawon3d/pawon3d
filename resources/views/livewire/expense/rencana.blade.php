<div>
    <!-- Header with Back Button and Title -->
    <div class="mb-6 flex gap-4 items-center">
        <a href="{{ route('belanja') }}"
            class="bg-[#313131] hover:bg-[#252324] text-white px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors"
            wire:navigate>
            <flux:icon.arrow-left variant="mini" class="size-4" />
            <span class="font-montserrat font-semibold text-[16px]">Kembali</span>
        </a>
        <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Rencana Belanja Persediaan</h1>
    </div>

    <!-- Info Box -->
    <x-alert.info>
        Rencana Belanja Persedian. Pilih dan mulai rencana belanja jika diperlukan. Belanja persediaan untuk
        memenuhi jumlah persediaan yang direncanakan.
    </x-alert.info>

    <!-- Main Content Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px]">
        <!-- Search Bar and Add Button -->
        <div class="flex justify-between items-center mb-5">
            <div class="flex gap-[15px] items-center w-[545px]">
                <div class="flex items-center bg-white border border-[#666666] rounded-[20px] px-[15px] py-0 flex-1">
                    <flux:icon.magnifying-glass class="size-[30px] text-[#666666] shrink-0" />
                    <input wire:model.live="search" placeholder="Cari Rencana Belanja"
                        class="px-2.5 py-2.5 border-0 focus:outline-none focus:ring-0 font-montserrat font-medium text-[16px] text-[#959595] bg-transparent flex-1" />
                </div>
                <div class="flex items-center cursor-pointer">
                    <flux:icon.funnel class="size-[25px] text-[#666666]" />
                    <p class="font-montserrat font-medium text-[16px] text-[#666666] px-1.5 py-2.5">Filter</p>
                </div>
            </div>
            <flux:button type="button" variant="primary" icon="plus" href="{{ route('belanja.tambah') }}"
                wire:navigate>
                Tambah Belanja
            </flux:button>
        </div>

        <!-- Table -->
        <x-table.paginated :paginator="$plannedExpenses" :headers="[
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
            ],
            [
                'label' => 'Total Harga',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'grand_total_expect',
                'class' => 'max-w-[160px] min-w-[100px]',
                'align' => 'right',
            ],
            [
                'label' => 'Total Harga (Bayar)',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'grand_total_actual',
                'class' => 'max-w-[160px] min-w-[100px]',
                'align' => 'right',
            ],
            [
                'label' => 'Status',
                'sortable' => true,
                'sort-method' => 'sortByColumn',
                'sort-by' => 'status',
                'class' => 'w-[160px]',
            ],
        ]" header-bg="#3f4e4f" header-text="#f8f4e1"
            body-bg="#fafafa" body-text="#666666"
            empty-message="Belum Ada Rencana Belanja. Tekan tombol 'Tambah Belanja' untuk membuat rencana belanja baru."
            wrapper-class="overflow-hidden rounded-[15px] mb-5">
            @foreach ($plannedExpenses as $expense)
                <tr class="h-[60px] border-b border-[#d4d4d4]">
                    <td class="px-6 py-0">
                        <a href="{{ route('belanja.rincian', $expense->id) }}"
                            class="font-montserrat font-medium text-[14px] text-[#666666] hover:underline cursor-pointer overflow-ellipsis overflow-hidden block"
                            wire:navigate>
                            {{ $expense->expense_number }}
                        </a>
                    </td>
                    <td class="px-6 py-0">
                        <span
                            class="font-montserrat font-medium text-[14px] text-[#666666] overflow-ellipsis overflow-hidden block">
                            {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->translatedFormat('d F Y') : '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-0">
                        <span
                            class="font-montserrat font-medium text-[14px] text-[#666666] overflow-ellipsis overflow-hidden block">
                            {{ $expense->supplier->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-0 text-right">
                        <span
                            class="font-montserrat font-medium text-[14px] text-[#666666] overflow-ellipsis overflow-hidden block">
                            Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-6 py-0 text-right">
                        <span
                            class="font-montserrat font-medium text-[14px] text-[#666666] overflow-ellipsis overflow-hidden block">
                            Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-6 py-0">
                        <div
                            class="inline-flex items-center justify-center px-[15px] py-1.5 bg-[#adadad] rounded-[15px] min-h-[40px] min-w-[90px]">
                            <div
                                class="flex flex-col font-montserrat font-bold text-[12px] text-[#fafafa] leading-normal text-center">
                                <span>Belum</span>
                                <span>Diproses</span>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>
</div>
