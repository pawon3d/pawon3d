<div>
    <div class="flex gap-8 items-center h-10 mb-[30px]">
        <div class="flex gap-[15px] items-center">
            <a href="{{ route('belanja') }}"
                class="inline-flex items-center gap-1.5 justify-center px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] bg-[#313131] text-[#f6f6f6] text-[16px] font-semibold hover:bg-[#252324] transition ease-in-out duration-150"
                wire:navigate>
                <flux:icon.arrow-left class="size-5" />
                Kembali
            </a>
            <h1 class="text-[20px] font-semibold text-[#666666]">Riwayat Belanja Persediaan</h1>
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

        @if ($expenses->isEmpty())
            <div class="col-span-7 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
                <p class="text-gray-700 font-semibold">Belum Ada Riwayat Belanja.</p>
                <p class="text-gray-700">Tekan tombol “Tambah belanja” di halaman utama untuk menambahkan belanja.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-t-[15px]">
                <!-- Table -->
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
                                            <span>Selesai</span>
                                        </div>
                                        <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                    </div>
                                </th>
                                <th class="px-6 py-[21px] text-left text-[14px] font-bold text-[#f8f4e1] cursor-pointer w-[192px]"
                                    wire:click="sortBy('supplier_name')">
                                    <div class="flex gap-1.5 items-center">
                                        <span class="leading-normal">Toko Persediaan</span>
                                        <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                    </div>
                                </th>
                                <th class="px-6 py-[21px] text-right text-[14px] font-bold text-[#f8f4e1] cursor-pointer max-w-[160px]"
                                    wire:click="sortBy('grand_total_expect')">
                                    <div class="flex gap-1.5 items-center justify-end">
                                        <span class="leading-normal">Total Harga</span>
                                        <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                    </div>
                                </th>
                                <th class="px-6 py-[21px] text-right text-[14px] font-bold text-[#f8f4e1] cursor-pointer max-w-[160px]"
                                    wire:click="sortBy('grand_total_actual')">
                                    <div class="flex gap-1.5 items-center justify-end">
                                        <div class="flex flex-col leading-normal text-right">
                                            <span>Total Harga</span>
                                            <span>(Bayar)</span>
                                        </div>
                                        <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                    </div>
                                </th>
                                <th class="px-6 py-[21px] text-left text-[14px] font-bold text-[#f8f4e1] cursor-pointer max-w-[120px]"
                                    wire:click="sortBy('status')">
                                    <div class="flex gap-1.5 items-center">
                                        <span class="leading-normal">Status</span>
                                        <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                    </div>
                                </th>
                                <th class="px-6 py-[21px] text-left text-[14px] font-bold text-[#f8f4e1] min-w-[100px]">
                                    <div class="flex flex-col leading-normal">
                                        <span>Kemajuan</span>
                                        <span>Persediaan</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-[#fafafa] divide-y divide-[#d4d4d4]">
                            @foreach ($expenses as $expense)
                                @php
                                    $total_expect = $expense->expenseDetails->sum('quantity_expect');
                                    $total_get = $expense->expenseDetails->sum('quantity_get');
                                    $percentage = $total_expect > 0 ? ($total_get / $total_expect) * 100 : 0;
                                    $status = $percentage >= 100 ? 'Selesai' : ($percentage > 0 ? 'Selesai' : 'Batal');
                                @endphp
                                <tr class="h-[60px]">
                                    <td class="px-6 py-0">
                                        <a href="{{ route('belanja.rincian', $expense->id) }}"
                                            class="text-[14px] font-medium text-[#666666] hover:underline cursor-pointer overflow-ellipsis overflow-hidden block">
                                            {{ $expense->expense_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-0">
                                        <span
                                            class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                                            {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-0">
                                        <span
                                            class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                                            {{ $expense->supplier->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-0 text-right">
                                        <span
                                            class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                                            Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-0 text-right">
                                        <span
                                            class="text-[14px] font-medium text-[#666666] overflow-ellipsis overflow-hidden block">
                                            Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-0">
                                        @if ($status === 'Selesai')
                                            <div
                                                class="inline-flex items-center justify-center px-[15px] py-1.5 bg-[#56c568] rounded-[15px] min-h-[40px] min-w-[90px]">
                                                <span
                                                    class="text-[12px] font-bold text-[#fafafa] leading-normal">Selesai</span>
                                            </div>
                                        @else
                                            <div
                                                class="inline-flex items-center justify-center px-[15px] py-1.5 bg-[#eb5757] rounded-[15px] min-h-[40px] min-w-[90px]">
                                                <span
                                                    class="text-[12px] font-bold text-[#fafafa] leading-normal">Batal</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-0">
                                        <div class="flex flex-col gap-1.5 w-full">
                                            <div
                                                class="w-full h-[18px] bg-[#d4d4d4] rounded-[5px] relative overflow-hidden">
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
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-5">
                <div class="flex gap-1.5 text-[14px] font-medium text-[#666666] opacity-70">
                    <span>Menampilkan</span>
                    <span>{{ $expenses->firstItem() ?? 0 }}</span>
                    <span>hingga</span>
                    <span>{{ $expenses->lastItem() ?? 0 }}</span>
                    <span>dari</span>
                    <span>{{ $expenses->total() }}</span>
                    <span>baris data</span>
                </div>
                <div class="flex gap-2.5 items-center">
                    @if ($expenses->onFirstPage())
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
                        <p class="text-[14px] font-medium text-[#fafafa] text-center">{{ $expenses->currentPage() }}
                        </p>
                    </div>

                    @if ($expenses->hasMorePages())
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
        @endif
    </div>

</div>
