<div class="flex flex-col gap-[30px]">
    <!-- Header Section -->
    <div class="flex items-center gap-[15px]">
        <flux:button href="{{ route('transaksi') }}" wire:navigate
            class="!bg-[#313131] !text-white !px-[25px] !py-[10px] !rounded-[15px] !shadow-[0px_2px_3px_rgba(0,0,0,0.1)] flex items-center gap-[5px]">
            <flux:icon icon="arrow-left" class="size-[16px]" />
            Kembali
        </flux:button>
        <h1 class="font-semibold text-[20px] text-[#333333]">Daftar Produk Siap Saji</h1>
    </div>

    <!-- Content Section -->
    <div class="bg-[#fafafa] rounded-[15px] flex flex-col gap-[20px] p-[30px]">
        <!-- Search and Filter Section -->
        <div class="flex items-center justify-between gap-[15px] w-full">
            <div class="flex items-center gap-[15px] flex-1">
                <div
                    class="bg-white border border-[#666666] rounded-[20px] px-[15px] py-0 flex items-center gap-3 flex-1">
                    <flux:icon icon="magnifying-glass" class="size-[16px] text-[#666666]" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Tanggal"
                        class="border-0 focus:ring-0 text-[16px] text-[#959595] py-[10px] flex-1 bg-transparent" />
                </div>
                <div
                    class="flex items-center gap-[5px] text-[#666666] cursor-pointer hover:opacity-70 transition-opacity">
                    <flux:icon icon="adjustments-horizontal" class="size-[25px]" />
                    <span class="text-[16px] font-medium">Filter</span>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="flex flex-col overflow-hidden rounded-[15px] border border-[#d4d4d4]">
            <!-- Table Header -->
            <div class="bg-[#3f4e4f] flex">
                <div class="flex-1 px-[25px] py-[21px] min-w-[100px]">
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Tanggal Siap Saji</p>
                </div>
                <div class="w-[170px] px-[25px] py-[21px] text-right">
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Jenis</p>
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Produk</p>
                </div>
                <div class="w-[170px] px-[25px] py-[21px] text-right">
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Total</p>
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Produksi</p>
                </div>
                <div class="w-[170px] px-[25px] py-[21px] text-right">
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Total</p>
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Terjual</p>
                </div>
                <div class="w-[170px] px-[25px] py-[21px] text-right">
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Total</p>
                    <p class="font-bold text-[14px] text-[#f8f4e1]">Tersisa</p>
                </div>
            </div>

            <!-- Table Body -->
            <div class="bg-[#fafafa] flex flex-col overflow-y-auto">
                @forelse ($products as $item)
                    <div class="flex border-b border-[#d4d4d4] hover:bg-gray-100 transition-colors">
                        <div class="flex-1 px-[25px] py-[15px] min-w-[100px]">
                            <a href="{{ route('transaksi.tanggal-siap-beli', ['date' => $item['date']]) }}"
                                class="font-medium text-[14px] text-[#666666] hover:underline" wire:navigate>
                                {{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d F Y') }}</a>
                        </div>
                        <div class="w-[170px] px-[25px] py-[15px] text-right">
                            <p class="font-medium text-[14px] text-[#666666]">{{ $item['jenis_produk'] }}</p>
                        </div>
                        <div class="w-[170px] px-[25px] py-[15px] text-right">
                            <p class="font-medium text-[14px] text-[#666666]">{{ $item['total_produksi'] }}</p>
                        </div>
                        <div class="w-[170px] px-[25px] py-[15px] text-right">
                            <p class="font-medium text-[14px] text-[#666666]">{{ $item['total_terjual'] }}</p>
                        </div>
                        <div class="w-[170px] px-[25px] py-[15px] text-right">
                            <p class="font-medium text-[14px] text-[#666666]">{{ $item['total_tersisa'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-[25px] py-[40px] text-center">
                        <p class="font-medium text-[14px] text-[#666666]">Tidak ada produk yang ditemukan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="flex items-center justify-between">
            <p class="text-[14px] font-medium text-[#666666] opacity-70">
                Menampilkan {{ $products->firstItem() ?? 0 }} hingga {{ $products->lastItem() ?? 0 }} dari
                {{ $products->total() }} baris data
            </p>
            <div class="flex items-center gap-[10px]">
                <button wire:click="previousPage" {{ $products->onFirstPage() ? 'disabled' : '' }}
                    class="bg-white border border-[#666666] rounded-[5px] px-[10px] py-[5px] hover:opacity-70 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">
                    <flux:icon icon="chevron-left" class="size-[17px] text-[#666666]" />
                </button>

                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    <button wire:click="gotoPage({{ $page }})"
                        class="min-w-[30px] rounded-[5px] px-[10px] py-[5px] font-medium text-[14px] {{ $products->currentPage() == $page ? 'bg-[#666666] text-white' : 'bg-white border border-[#666666] text-[#666666] hover:opacity-70' }} transition-opacity">
                        {{ $page }}
                    </button>
                @endforeach

                <button wire:click="nextPage" {{ !$products->hasMorePages() ? 'disabled' : '' }}
                    class="bg-white border border-[#666666] rounded-[5px] px-[10px] py-[5px] hover:opacity-70 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">
                    <flux:icon icon="chevron-right" class="size-[17px] text-[#666666]" />
                </button>
            </div>
        </div>
    </div>
</div>
