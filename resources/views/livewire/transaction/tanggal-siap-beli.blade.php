<div class="flex flex-col gap-[30px] px-4 sm:px-0">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-[15px]">
        <flux:button href="{{ route('transaksi.siap-beli') }}" wire:navigate
            class="w-full sm:w-auto !bg-[#313131] !text-white !px-[25px] !py-[10px] !rounded-[15px] !shadow-[0px_2px_3px_rgba(0,0,0,0.1)] flex items-center justify-center gap-[5px]">
            <flux:icon icon="arrow-left" class="size-[16px]" />
            Kembali
        </flux:button>
        <h1 class="font-medium text-[20px] text-[#333333] text-center sm:text-left">
            {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
        </h1>
    </div>

    <!-- Content Section -->
    <div class="bg-[#fafafa] rounded-[15px] flex flex-col gap-[20px] p-4 sm:p-[30px]">
        <!-- Search and Filter Section -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-[15px] w-full">
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-[15px] flex-1 w-full">
                <div
                    class="bg-white border border-[#666666] rounded-[20px] px-[15px] py-0 flex items-center gap-3 flex-1 w-full">
                    <flux:icon icon="magnifying-glass" class="size-[16px] text-[#666666]" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Produk"
                        class="border-0 focus:ring-0 text-[16px] text-[#959595] py-[10px] flex-1 bg-transparent" />
                </div>
                <div
                    class="flex items-center gap-[5px] text-[#666666] cursor-pointer hover:opacity-70 transition-opacity justify-center">
                    <flux:icon icon="adjustments-horizontal" class="size-[25px]" />
                    <span class="text-[16px] font-medium">Filter</span>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <div class="min-w-[800px]">
                <x-table.paginated :headers="[
                    ['label' => 'Produk', 'sortable' => true, 'sort-by' => 'product_name'],
                    ['label' => 'ID Produksi', 'sortable' => true, 'sort-by' => 'production_id', 'align' => 'right'],
                    ['label' => 'Jumlah Produksi', 'sortable' => true, 'sort-by' => 'total_production', 'align' => 'right'],
                    ['label' => 'Terjual', 'sortable' => true, 'sort-by' => 'total_sold', 'align' => 'right'],
                    ['label' => 'Tersisa', 'sortable' => true, 'sort-by' => 'total_remaining', 'align' => 'right'],
                ]" :paginator="$products" headerBg="#3f4e4f" headerText="#f8f4e1" bodyBg="#fafafa"
                    bodyText="#666666" emptyMessage="Tidak ada produk yang ditemukan.">
                    @foreach ($products as $product)
                        <tr class="border-b border-[#d4d4d4] hover:bg-gray-100 transition-colors">
                            <td class="px-[25px] py-[15px] text-[#666666] font-medium text-[14px]">
                                {{ $product['product_name'] }}
                            </td>
                            <td class="px-[25px] py-[15px] text-right text-[#666666] font-medium text-[14px]">
                                {{ $product['production_id'] }}
                            </td>
                            <td class="px-[25px] py-[15px] text-right text-[#666666] font-medium text-[14px]">
                                {{ $product['total_production'] }}
                            </td>
                            <td class="px-[25px] py-[15px] text-right text-[#666666] font-medium text-[14px]">
                                {{ $product['total_sold'] }}
                            </td>
                            <td class="px-[25px] py-[15px] text-right text-[#666666] font-medium text-[14px]">
                                {{ $product['total_remaining'] }}
                            </td>
                        </tr>
                    @endforeach
                </x-table.paginated>
            </div>
        </div>
    </div>
</div>
