<div class="space-y-6">
    <div class="mb-2 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('produk') }}"
                class="bg-[#313131] hover:bg-[#252324] text-white px-5 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-2 transition-colors">
                <flux:icon.arrow-left variant="mini" class="size-4" />
                <span class="font-montserrat font-semibold text-[16px]">Kembali</span>
            </a>
            <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Daftar Kategori Produk</h1>
        </div>
        <button type="button" wire:click="riwayatPembaruan"
            class="bg-[#525252] border border-[#666666] text-white px-6 py-2.5 rounded-[15px] hover:bg-[#666666] transition-colors">
            <span class="font-montserrat font-medium text-[14px]">Riwayat Pembaruan</span>
        </button>
    </div>

    <x-alert.info class="font-montserrat font-semibold text-[14px] leading-[1.4]">
        Kategori Produk. Lihat atau tambah kategori untuk mengelompokkan produk berdasarkan jenis, rasa, cara
        masak, dan lain sebagainya. Produk dapat memiliki banyak kategori dan kategori dapat terdiri dari banyak
        produk.
    </x-alert.info>


    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-8 py-6 flex flex-col gap-6 border border-[#ececec]">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex-1 min-w-[220px] max-w-[545px]">
                <div
                    class="flex items-center gap-2 bg-white border border-[#666666] rounded-[20px] px-4 h-[40px] w-full">
                    <flux:icon.magnifying-glass class="size-5 text-[#666666]" />
                    <input wire:model.live="search" placeholder="Cari Kategori"
                        class="flex-1 bg-transparent border-0 font-montserrat font-medium text-[16px] text-[#959595] focus:outline-none focus:ring-0" />
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" wire:click="showAddModal"
                    class="bg-[#74512d] hover:bg-[#5f4224] text-[#f6f6f6] px-6 py-2.5 rounded-[15px] font-montserrat font-semibold text-[16px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-2 transition-colors">
                    <flux:icon.plus class="size-5" />
                    Tambah Kategori
                </button>
            </div>
        </div>

        <div class="w-full rounded-[15px] overflow-hidden border border-[#e4e4e4] bg-white">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[#3f4e4f] h-[60px]">
                            <th class="text-left px-6 py-5">
                                <button type="button" wire:click="sortBy('name')"
                                    class="flex items-center gap-2 text-left w-full">
                                    <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Kategori
                                        Produk</span>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </button>
                            </th>
                            <th class="text-left px-6 py-5">
                                <button type="button" wire:click="sortBy('is_active')"
                                    class="flex items-center gap-2 text-left w-full">
                                    <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Status
                                        Tampil</span>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </button>
                            </th>
                            <th class="text-right px-6 py-5">
                                <button type="button" wire:click="sortBy('products_count')"
                                    class="flex items-center gap-2 w-full justify-end">
                                    <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Jumlah
                                        Penggunaan</span>
                                    <flux:icon.chevron-up-down class="size-3.5 text-[#f8f4e1]" />
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#fafafa]">
                        @forelse ($categories as $category)
                            <tr class="border-b border-[#d4d4d4] h-[60px] hover:bg-[#f0f0f0] transition-colors cursor-pointer"
                                wire:click="edit('{{ $category->id }}')">
                                <td class="px-6">
                                    <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                        {{ $category->name }}
                                    </span>
                                </td>
                                <td class="px-6">
                                    <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                        {{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="px-6 text-right">
                                    <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr class="h-[60px]">
                                <td colspan="3" class="px-6 text-center">
                                    <span class="font-montserrat font-medium text-[14px] text-[#666666]">Tidak ada
                                        data.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 px-6 py-4 md:flex-row md:items-center md:justify-between">
                <div class="font-montserrat font-medium text-[14px] text-[#666666] opacity-70">
                    @php
                        $first = $categories->firstItem() ?? 0;
                        $last = $categories->lastItem() ?? 0;
                        $total = $categories->total();
                    @endphp
                    <span>Menampilkan {{ $first }} hingga {{ $last }} dari {{ $total }} baris
                        data</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="previousPage" @disabled($categories->onFirstPage())
                        class="bg-[#fafafa] border border-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px] {{ $categories->onFirstPage() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#f0f0f0]' }}">
                        <flux:icon.chevron-left class="size-[17px] text-[#666666]" />
                    </button>
                    <div class="bg-[#666666] min-w-[30px] px-3 py-1 rounded-[5px] text-center">
                        <span
                            class="font-montserrat font-medium text-[14px] text-white">{{ $categories->currentPage() }}</span>
                    </div>
                    <button type="button" wire:click="nextPage" @disabled(!$categories->hasMorePages())
                        class="bg-[#fafafa] border border-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px] {{ $categories->hasMorePages() ? 'hover:bg-[#f0f0f0]' : 'opacity-50 cursor-not-allowed' }}">
                        <flux:icon.chevron-right class="size-[17px] text-[#666666]" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <flux:modal name="tambah-kategori" class="w-full max-w-lg" wire:model="showModal">
        <div class="bg-[#fafafa] rounded-[15px] p-8 space-y-8">
            <div class="space-y-2">
                <p class="font-montserrat font-medium text-[20px] text-[#333333]">Tambah Kategori Persediaan</p>
                <div class="space-y-3">
                    <div class="space-y-2">
                        <p class="font-montserrat font-medium text-[18px] text-[#333333]">Nama Kategori</p>
                        <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-5 py-2.5 flex items-center">
                            <input type="text" wire:model.lazy="name" placeholder="Contoh : Kue Tradisional"
                                class="flex-1 bg-transparent border-0 font-montserrat text-[16px] text-[#666666] placeholder:text-[#959595] focus:outline-none focus:ring-0" />
                        </div>
                        @error('name')
                            <span class="text-sm text-red-500 font-montserrat">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="font-montserrat font-medium text-[18px] text-[#666666]">Tampil Kategori</p>
                            <button type="button" wire:click="$toggle('is_active')"
                                class="relative h-[25px] w-[45px] rounded-full transition-colors duration-200 {{ $is_active ? 'bg-green-500' : 'bg-[#666666]' }}"
                                aria-pressed="{{ $is_active ? 'true' : 'false' }}">
                                <span
                                    class="absolute top-[3px] h-[19px] w-[19px] rounded-full bg-white transition-all duration-200 {{ $is_active ? 'left-[23px]' : 'left-[3px]' }}"></span>
                            </button>
                        </div>
                        <p class="font-montserrat text-[14px] text-[#666666]">Aktifkan opsi ini jika kategori ingin
                            ditampilkan dan digunakan.</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <button type="button"
                        class="bg-[#c4c4c4] text-[#333333] font-montserrat font-semibold text-[16px] px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
                        Batal
                    </button>
                </flux:modal.close>
                <button type="button" wire:click="store"
                    class="bg-[#3f4e4f] text-[#f8f4e1] font-montserrat font-semibold text-[16px] px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" class="size-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7.5V21h18V7.5L16.5 3h-9L3 7.5zM15 21v-7.5H9V21" />
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="rincian-kategori" class="w-full max-w-lg" wire:model="showEditModal">
        <div class="bg-[#fafafa] rounded-[15px] p-8 space-y-8">
            <div class="space-y-2">
                <p class="font-montserrat font-medium text-[20px] text-[#333333]">Rincian Kategori Persediaan</p>
                <div class="space-y-3">
                    <div class="space-y-2">
                        <p class="font-montserrat font-medium text-[18px] text-[#333333]">Nama Kategori</p>
                        <div class="bg-[#fafafa] border border-[#adadad] rounded-[15px] px-5 py-2.5 flex items-center">
                            <input type="text" wire:model.lazy="name"
                                class="flex-1 bg-transparent border-0 font-montserrat text-[16px] text-[#666666] focus:outline-none focus:ring-0" />
                        </div>
                        @error('name')
                            <span class="text-sm text-red-500 font-montserrat">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-montserrat font-medium text-[16px] text-[#333333]">Jumlah Penggunaan</p>
                            <p class="font-montserrat text-[16px] text-[#333333]">{{ $products ?? 0 }}</p>
                        </div>
                        <button type="button" @disabled(!$category_id)
                            @if ($category_id) wire:click="openUsageModal('{{ $category_id }}')" @endif
                            class="size-10 rounded-full flex items-center justify-center {{ $category_id ? 'bg-transparent' : 'opacity-40 cursor-not-allowed' }}">
                            <flux:icon.chevron-right class="size-4 text-[#333333]" />
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="font-montserrat font-medium text-[18px] text-[#666666]">Tampil Kategori</p>
                            <button type="button" wire:click="$toggle('is_active')"
                                class="relative h-[25px] w-[45px] rounded-full transition-colors duration-200 {{ $is_active ? 'bg-green-500' : 'bg-[#666666]' }}"
                                aria-pressed="{{ $is_active ? 'true' : 'false' }}">
                                <span
                                    class="absolute top-[3px] h-[19px] w-[19px] rounded-full bg-white transition-all duration-200 {{ $is_active ? 'left-[23px]' : 'left-[3px]' }}"></span>
                            </button>
                        </div>
                        <p class="font-montserrat text-[14px] text-[#666666]">Aktifkan opsi ini jika kategori ingin
                            ditampilkan dan digunakan.</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <flux:modal.trigger name="delete-category">
                    <button type="button"
                        class="bg-[#eb5757] rounded-[15px] p-3 text-white hover:bg-[#d64545] transition-colors">
                        <flux:icon.trash class="size-5" />
                    </button>
                </flux:modal.trigger>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="$set('showEditModal', false)"
                        class="bg-[#c4c4c4] text-[#333333] font-montserrat font-semibold text-[16px] px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
                        Batal
                    </button>
                    <button type="button" wire:click="update"
                        class="bg-[#3f4e4f] text-[#f8f4e1] font-montserrat font-semibold text-[16px] px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" class="size-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7.5V21h18V7.5L16.5 3h-9L3 7.5zM15 21v-7.5H9V21" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <flux:modal name="delete-category" class="w-full max-w-md">
            <div class="space-y-4 p-6">
                <flux:heading size="lg">Hapus Kategori</flux:heading>
                <p class="font-montserrat text-[14px] text-[#666666]">Apakah Anda yakin ingin menghapus kategori ini?
                </p>
                <div class="flex justify-end gap-2">
                    <flux:modal.close>
                        <button type="button"
                            class="px-4 py-2 rounded-[10px] bg-[#f5f5f5] text-[#333333] font-montserrat">Batal</button>
                    </flux:modal.close>
                    <button type="button" wire:click="delete"
                        class="px-4 py-2 rounded-[10px] bg-[#eb5757] text-white font-montserrat">Hapus</button>
                </div>
            </div>
        </flux:modal>
    </flux:modal>

    <flux:modal name="jumlah-penggunaan" class="w-full max-w-2xl" wire:model="showUsageModal">
        <div class="bg-[#fafafa] rounded-[15px] p-8 space-y-4">
            <p class="font-montserrat font-medium text-[18px] text-[#333333]">Daftar Produk</p>
            <div class="flex items-center gap-3">
                <div
                    class="flex-1 flex items-center gap-2 bg-white border border-[#666666] rounded-[20px] px-4 h-[40px]">
                    <flux:icon.magnifying-glass class="size-5 text-[#666666]" />
                    <input type="text" wire:model.live="usageSearch" placeholder="Cari Produk"
                        class="flex-1 bg-transparent border-0 font-montserrat text-[16px] text-[#959595] focus:outline-none focus:ring-0" />
                </div>
            </div>
            <div class="border border-[#d4d4d4] rounded-[15px] overflow-hidden">
                <div
                    class="bg-[#3f4e4f] text-[#f8f4e1] font-montserrat font-bold text-[14px] px-6 py-4 rounded-t-[15px]">
                    <div class="flex items-center justify-between">
                        <span>Produk</span>
                        <flux:icon.chevron-up-down class="size-4" />
                    </div>
                </div>
                <div class="divide-y divide-[#d4d4d4]">
                    @forelse ($usageProducts as $product)
                        <div class="flex items-center justify-between px-6 py-4">
                            <span class="font-montserrat text-[14px] text-[#666666]">{{ $product['name'] }}</span>
                            <span class="text-[#666666]">
                                <flux:icon.trash class="size-4" />
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-6 text-center font-montserrat text-[14px] text-[#666666]">
                            Tidak ada produk di kategori ini.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="font-montserrat text-[14px] text-[#666666] opacity-70">Menampilkan
                    {{ $usageSummary['from'] }} hingga {{ $usageSummary['to'] }} dari
                    {{ $usageSummary['total'] }} baris data</span>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="previousUsagePage" @disabled($usagePage === 1 || $usageSummary['total'] === 0)
                        class="bg-[#fafafa] border border-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px] {{ $usagePage === 1 || $usageSummary['total'] === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#f0f0f0]' }}">
                        <flux:icon.chevron-left class="size-[17px] text-[#666666]" />
                    </button>
                    <div class="bg-[#666666] min-w-[30px] px-3 py-1 rounded-[5px] text-center">
                        <span class="font-montserrat text-[14px] text-white">{{ $usagePage }}</span>
                    </div>
                    <button type="button" wire:click="nextUsagePage" @disabled($usagePage >= $usageSummary['pages'] || $usageSummary['total'] === 0)
                        class="bg-[#fafafa] border border-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px] {{ $usagePage >= $usageSummary['pages'] || $usageSummary['total'] === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#f0f0f0]' }}">
                        <flux:icon.chevron-right class="size-[17px] text-[#666666]" />
                    </button>
                </div>
            </div>
            <div class="border-t border-dotted border-[#666666] pt-3">
                <p class="font-montserrat text-[14px] text-[#666666] text-center opacity-70">Tekan bagian luar untuk
                    menutup halaman</p>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Kategori</flux:heading>
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
