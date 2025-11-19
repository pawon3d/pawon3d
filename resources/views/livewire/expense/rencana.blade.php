<div>
    <!-- Header with Back Button and Title -->
    <div class="mb-6 flex gap-4 items-center">
        <a href="{{ route('belanja') }}"
            class="bg-[#313131] hover:bg-[#252324] text-white px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
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
            <a href="{{ route('belanja.tambah') }}"
                class="inline-flex items-center gap-1.5 justify-center px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] bg-[#74512d] text-[#f6f6f6] font-montserrat font-semibold text-[16px] hover:bg-[#5a3d22] transition ease-in-out duration-150"
                wire:navigate>
                <flux:icon.plus class="size-5" />
                Tambah Belanja
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-t-[15px] mb-5">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[#3f4e4f]">
                        <tr>
                            <th class="px-6 py-[21px] text-left text-[14px] font-bold text-[#f8f4e1] cursor-pointer min-w-[170px]"
                                wire:click="sortBy('expense_number')">
                                <div class="flex gap-1.5 items-center">
                                    <div class="flex flex-col leading-normal">
                                        <span>ID</span>
                                        <span>Belanja</span>
                                    </div>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </div>
                            </th>
                            <th class="px-6 py-[21px] text-left text-[14px] font-bold text-[#f8f4e1] cursor-pointer max-w-[150px]"
                                wire:click="sortBy('expense_date')">
                                <div class="flex gap-1.5 items-center">
                                    <div class="flex flex-col leading-normal">
                                        <span>Tanggal</span>
                                        <span>Belanja</span>
                                    </div>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </div>
                            </th>
                            <th class="px-6 py-[21px] text-left text-[14px] font-bold text-[#f8f4e1] cursor-pointer"
                                wire:click="sortBy('supplier_name')">
                                <div class="flex gap-1.5 items-center">
                                    <span class="leading-normal">Toko Persediaan</span>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </div>
                            </th>
                            <th class="px-6 py-[21px] text-right text-[14px] font-bold text-[#f8f4e1] cursor-pointer max-w-[160px] min-w-[100px]"
                                wire:click="sortBy('grand_total_expect')">
                                <div class="flex gap-1.5 items-center justify-end">
                                    <span class="leading-normal">Total Harga</span>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </div>
                            </th>
                            <th class="px-6 py-[21px] text-right text-[14px] font-bold text-[#f8f4e1] cursor-pointer max-w-[160px] min-w-[100px]"
                                wire:click="sortBy('grand_total_actual')">
                                <div class="flex gap-1.5 items-center justify-end">
                                    <div class="flex flex-col leading-normal text-right">
                                        <span>Total Harga</span>
                                        <span>(Bayar)</span>
                                    </div>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </div>
                            </th>
                            <th class="px-6 py-[21px] text-left text-[14px] font-bold text-[#f8f4e1] cursor-pointer w-[160px]"
                                wire:click="sortBy('status')">
                                <div class="flex gap-1.5 items-center">
                                    <span class="leading-normal">Status</span>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#fafafa] divide-y divide-[#d4d4d4]">
                        @forelse ($plannedExpenses as $expense)
                            <tr class="h-[60px]">
                                <td class="px-6 py-0">
                                    <a href="{{ route('belanja.rincian', $expense->id) }}"
                                        class="font-montserrat font-medium text-[14px] text-[#666666] hover:underline cursor-pointer overflow-ellipsis overflow-hidden block">
                                        {{ $expense->expense_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-0">
                                    <span
                                        class="font-montserrat font-medium text-[14px] text-[#666666] overflow-ellipsis overflow-hidden block">
                                        {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '-' }}
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
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <p class="font-montserrat font-semibold text-[16px] text-[#666666]">Belum Ada
                                        Rencana Belanja</p>
                                    <p class="font-montserrat font-normal text-[14px] text-[#959595] mt-2">Tekan tombol
                                        "Tambah Belanja" untuk membuat rencana belanja baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center">
            <div class="flex gap-1.5 font-montserrat font-medium text-[14px] text-[#666666] opacity-70">
                <span>Menampilkan</span>
                <span>{{ $plannedExpenses->firstItem() ?? 0 }}</span>
                <span>hingga</span>
                <span>{{ $plannedExpenses->lastItem() ?? 0 }}</span>
                <span>dari</span>
                <span>{{ $plannedExpenses->total() }}</span>
                <span>baris data</span>
            </div>
            <div class="flex gap-2.5 items-center">
                @if ($plannedExpenses->onFirstPage())
                    <button disabled
                        class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-[5px] cursor-not-allowed opacity-50">
                        <flux:icon.chevron-left class="size-[17px] text-[#666666]" />
                    </button>
                @else
                    <button wire:click="previousPage"
                        class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-[5px] hover:bg-gray-100 cursor-pointer">
                        <flux:icon.chevron-left class="size-[17px] text-[#666666]" />
                    </button>
                @endif

                <div class="min-w-[30px] px-2.5 py-1.5 bg-[#666666] rounded-[5px]">
                    <p class="font-montserrat font-medium text-[14px] text-[#fafafa] text-center">
                        {{ $plannedExpenses->currentPage() }}</p>
                </div>

                @if ($plannedExpenses->hasMorePages())
                    <button wire:click="nextPage"
                        class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-[5px] hover:bg-gray-100 cursor-pointer">
                        <flux:icon.chevron-right class="size-[17px] text-[#666666]" />
                    </button>
                @else
                    <button disabled
                        class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-[5px] cursor-not-allowed opacity-50">
                        <flux:icon.chevron-right class="size-[17px] text-[#666666]" />
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
