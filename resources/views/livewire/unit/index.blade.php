<div>
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('bahan-baku') }}"
                class="bg-[#313131] hover:bg-[#252324] px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold text-base transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
            <h1 class="text-xl font-semibold text-[#666666]">Kelola Satuan ukur</h1>
        </div>
        <div class="flex gap-2.5">
            <button type="button" wire:click="riwayatPembaruan"
                class="bg-[#525252] hover:bg-[#666666] border border-[#666666] px-6 py-2.5 rounded-[15px] font-medium text-sm text-white transition-colors cursor-pointer">
                Riwayat Pembaruan
            </button>
        </div>
    </div>

    <!-- Info Box -->
    <x-alert.info>
        Satuan Ukur. Satuan ukur adalah bentuk adalah standar baku yang digunakan untuk mengukur besaran fisik
        seperti volume, massa, atau jumlah, sehingga memastikan keakuratan pengukuran dan memberikan nilai
        pembanding standar dalam persediaan.
    </x-alert.info>

    <!-- Main Content Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-8 py-6">
        <!-- Search and Add Button -->
        <div class="flex justify-between items-center mb-7">
            <div class="flex items-center gap-4 flex-1">
                <div
                    class="flex items-center bg-white border border-[#666666] rounded-full px-4 py-0 w-full max-w-[545px]">
                    <svg class="w-[30px] h-[30px] text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                    <input wire:model.live="search" placeholder="Cari Satuan."
                        class="flex-1 px-2.5 py-2.5 focus:outline-none text-[#959595] text-base font-medium border-none" />
                </div>
                <button class="flex items-center gap-2 text-[#666666] font-medium text-base">
                    <svg class="w-[25px] h-[25px]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                            clip-rule="evenodd" />
                    </svg>
                    Filter
                </button>
            </div>
            <button wire:click="showAddModal"
                class="bg-[#74512d] hover:bg-[#5d4024] text-[#f6f6f6] font-semibold text-base px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Tambah Satuan
            </button>
        </div>

        <!-- Table -->
        <div class="rounded-[15px] overflow-hidden border border-[#d4d4d4]">
            <!-- Table Header -->
            <div
                class="bg-[#3f4e4f] flex items-center px-6 py-5 text-left text-sm font-bold text-[#f8f4e1] cursor-pointer">
                <div class="flex-1 max-w-[350px] min-w-[100px] px-6 py-5 flex items-center gap-1.5 cursor-pointer"
                    wire:click="sortBy('name')">
                    <div class="flex items-center gap-1">
                        Nama Satuan
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 14 14">
                            <path d="M7 2L11 6H3L7 2Z" opacity="0.5" />
                            <path d="M7 12L3 8H11L7 12Z" opacity="0.5" />
                        </svg>
                    </div>

                </div>
                <div class="flex-1 max-w-[350px] min-w-[100px] px-6 py-5 flex items-center gap-1.5 cursor-pointer"
                    wire:click="sortBy('alias')">
                    <div class="flex items-center gap-1">
                        Singkatan
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 14 14">
                            <path d="M7 2L11 6H3L7 2Z" opacity="0.5" />
                            <path d="M7 12L3 8H11L7 12Z" opacity="0.5" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1 max-w-[350px] min-w-[100px] px-6 py-5 flex items-center gap-1.5 cursor-pointer"
                    wire:click="sortBy('group')">
                    <div class="flex items-center gap-1">
                        Kelompok Satuan
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 14 14">
                            <path d="M7 2L11 6H3L7 2Z" opacity="0.5" />
                            <path d="M7 12L3 8H11L7 12Z" opacity="0.5" />
                        </svg>
                    </div>

                </div>
                <div class="flex-1 min-w-[100px] px-6 py-5 flex items-center justify-end gap-1.5 cursor-pointer"
                    wire:click="sortBy('material_details_count')">
                    <div class="flex items-center gap-1 text-right">
                        Jumlah Penggunaan
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 14 14">
                            <path d="M7 2L11 6H3L7 2Z" opacity="0.5" />
                            <path d="M7 12L3 8H11L7 12Z" opacity="0.5" />
                        </svg>
                    </div>

                </div>
            </div>

            <!-- Table Body -->
            <div class="overflow-x-auto overflow-y-auto max-h-[600px]">
                @forelse($units as $unit)
                    <button wire:click="edit('{{ $unit->id }}')"
                        class="flex items-center w-full hover:bg-gray-50 transition-colors text-left cursor-pointer">
                        <div
                            class="flex-1 max-w-[350px] min-w-[100px] px-6 py-5 bg-[#fafafa] border-b border-[#d4d4d4]">
                            <p class="font-medium text-sm text-[#666666] truncate">{{ $unit->name }}</p>
                        </div>
                        <div
                            class="flex-1 max-w-[350px] min-w-[100px] px-6 py-5 bg-[#fafafa] border-b border-[#d4d4d4]">
                            <p class="font-medium text-sm text-[#666666] truncate">{{ $unit->alias }}</p>
                        </div>
                        <div
                            class="flex-1 max-w-[350px] min-w-[100px] px-6 py-5 bg-[#fafafa] border-b border-[#d4d4d4]">
                            <p class="font-medium text-sm text-[#666666] truncate">
                                @if ($unit->group === 'Berat')
                                    Berat / Massa
                                @elseif ($unit->group === 'Volume')
                                    Volume
                                @elseif ($unit->group === 'Jumlah')
                                    Jumlah
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div
                            class="flex-1 min-w-[100px] px-6 py-5 bg-[#fafafa] border-b border-[#d4d4d4] flex justify-end">
                            <p class="font-medium text-sm text-[#666666] text-right">
                                {{ $unit->material_details_count }}
                            </p>
                        </div>
                    </button>
                @empty
                    <div class="px-6 py-8 text-center">
                        <p class="text-sm text-[#666666]">Tidak ada data.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-5">
            <p class="text-sm font-medium text-[#666666] opacity-70">
                Menampilkan {{ $units->firstItem() ?? 0 }} hingga {{ $units->lastItem() ?? 0 }} dari
                {{ $units->total() }} baris data
            </p>
            <div class="flex items-center gap-2.5">
                @if ($units->onFirstPage())
                    <button disabled
                        class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-md opacity-50 cursor-not-allowed">
                        <svg class="w-[17px] h-[17px] text-[#666666]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @else
                    <button wire:click="previousPage"
                        class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-md hover:bg-gray-100">
                        <svg class="w-[17px] h-[17px] text-[#666666]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @endif

                <div class="min-w-[30px] px-2.5 py-1.5 bg-[#666666] rounded-md text-center">
                    <span class="text-sm font-medium text-[#fafafa]">{{ $units->currentPage() }}</span>
                </div>

                @if ($units->hasMorePages())
                    <button wire:click="nextPage"
                        class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-md hover:bg-gray-100">
                        <svg class="w-[17px] h-[17px] text-[#666666]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @else
                    <button disabled
                        class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-md opacity-50 cursor-not-allowed">
                        <svg class="w-[17px] h-[17px] text-[#666666]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Tambah Satuan -->
    <flux:modal name="tambah-satuan" class="w-full max-w-[560px]" wire:model="showModal">
        <div class="bg-[#fafafa] rounded-[15px] px-8 py-9 space-y-12">
            <div class="space-y-8">
                <h2 class="text-xl font-medium text-[#333333]">Tambah Satuan Baru</h2>

                <div class="space-y-5">
                    <!-- Nama Satuan -->
                    <div class="space-y-2.5">
                        <label for="name" class="block text-lg font-medium text-[#333333]">Nama Satuan</label>
                        <input type="text" id="name" wire:model.lazy="name" placeholder="Contoh : Gram"
                            class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] text-base text-[#959595] focus:outline-none focus:border-[#666666]" />
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Kelompok Satuan -->
                    <div class="space-y-2.5">
                        <label for="group" class="block text-lg font-medium text-[#333333]">Kelompok Satuan</label>
                        <div class="relative">
                            <select id="group" wire:model.lazy="group"
                                class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] text-base text-[#959595] focus:outline-none focus:border-[#666666] appearance-none">
                                <option value="">Pilih Kelompok Satuan</option>
                                <option value="Berat">Berat / Massa</option>
                                <option value="Volume">Volume</option>
                                <option value="Jumlah">Jumlah</option>
                            </select>
                        </div>
                        @error('group')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Singkatan -->
                    <div class="space-y-2.5">
                        <label for="alias" class="block text-lg font-medium text-[#333333]">Singkatan</label>
                        <input type="text" id="alias" wire:model.lazy="alias" placeholder="Contoh : gr"
                            class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] text-base text-[#959595] focus:outline-none focus:border-[#666666]" />
                        @error('alias')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2.5">
                <flux:modal.close>
                    <button type="button"
                        class="bg-[#c4c4c4] hover:bg-[#b0b0b0] text-[#333333] font-semibold text-base px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        Batal
                    </button>
                </flux:modal.close>
                <button type="button" wire:click="store"
                    class="bg-[#3f4e4f] hover:bg-[#2f3e3f] text-[#f8f4e1] font-semibold text-base px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Rincian Satuan -->
    <flux:modal name="rincian-satuan" class="w-full max-w-[560px]" wire:model="showEditModal">
        <div class="bg-[#fafafa] rounded-[15px] px-8 py-9 space-y-12">
            <div class="space-y-8">
                <h2 class="text-xl font-medium text-[#333333]">Rincian Satuan Ukur</h2>

                <div class="space-y-10">
                    <div class="space-y-5">
                        <!-- Nama Satuan -->
                        <div class="space-y-2.5">
                            <label for="edit_name" class="block text-lg font-medium text-[#333333]">Nama
                                Satuan</label>
                            <input type="text" id="edit_name" wire:model.lazy="name"
                                class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] focus:outline-none focus:border-[#666666]" />
                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Kelompok Satuan -->
                        <div class="space-y-2.5">
                            <label for="edit_group" class="block text-lg font-medium text-[#333333]">Kelompok
                                Satuan</label>
                            <div class="relative">
                                <select id="edit_group" wire:model.lazy="group"
                                    class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] focus:outline-none focus:border-[#666666] appearance-none">
                                    <option value="">Pilih Kelompok Satuan</option>
                                    <option value="Berat">Berat / Massa</option>
                                    <option value="Volume">Volume</option>
                                    <option value="Jumlah">Jumlah</option>
                                </select>
                            </div>
                            @error('group')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Singkatan -->
                        <div class="space-y-2.5">
                            <label for="edit_alias" class="block text-lg font-medium text-[#333333]">Singkatan</label>
                            <input type="text" id="edit_alias" wire:model.lazy="alias"
                                class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] focus:outline-none focus:border-[#666666]" />
                            @error('alias')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Jumlah Penggunaan -->
                    <div class="flex items-center justify-between">
                        <label class="text-base font-medium text-[#333333]">Jumlah Penggunaan</label>
                        <button type="button" wire:click="showUsageModal"
                            class="flex items-center gap-1 text-base font-medium text-[#333333] hover:text-[#666666] transition-colors cursor-pointer">
                            <span>{{ $materials }}</span>
                            <svg class="w-6 h-6 rotate-180 scale-y-[-1]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between w-full">
                <flux:modal.trigger name="delete-unit">

                    <button type="button"
                        class="w-10 h-10 bg-[#eb5757] hover:bg-[#d64545] rounded-[15px] flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-[#f8f4e1]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </flux:modal.trigger>
                <div class="flex gap-2.5">
                    <flux:modal.close>
                        <button type="button"
                            class="bg-[#c4c4c4] hover:bg-[#b0b0b0] text-[#333333] font-semibold text-base px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Batal
                        </button>
                    </flux:modal.close>
                    <button type="button" wire:click="update"
                        class="bg-[#3f4e4f] hover:bg-[#2f3e3f] text-[#f8f4e1] font-semibold text-base px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                        </svg>
                        Simpan Pembaruan
                    </button>
                </div>
            </div>
        </div>

        <flux:modal name="delete-unit" class="w-full max-w-md">
            <div class="bg-white rounded-[15px] p-8">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-medium text-[#333333]">Hapus Kategori</h2>
                        <p class="mt-2 text-[#666666]">Apakah Anda yakin ingin menghapus kategori ini?</p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <button type="button"
                                class="px-6 py-2.5 bg-[#c4c4c4] hover:bg-[#b0b0b0] text-[#333333] rounded-[15px] font-semibold text-base transition">
                                Batal
                            </button>
                        </flux:modal.close>
                        <button type="button" wire:click="delete"
                            class="px-6 py-2.5 bg-[#eb5757] hover:bg-[#d64545] text-white rounded-[15px] font-semibold text-base transition">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </flux:modal>
    </flux:modal>

    <!-- Modal Jumlah Penggunaan -->
    <flux:modal name="usage-modal" class="w-full max-w-[700px]" wire:model="showUsageModal">
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-8 py-6 space-y-8">
            <div class="space-y-5">
                <h3 class="text-lg font-medium text-[#333333]">Daftar Persediaan</h3>

                <!-- Search -->
                <div class="flex items-center bg-white border border-[#666666] rounded-full px-4 py-0">
                    <svg class="w-[30px] h-[30px] text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                    <input wire:model.live="usageSearch" placeholder="Cari Persediaan"
                        class="flex-1 px-2.5 py-2.5 focus:outline-none text-[#959595] text-base font-medium border-none" />
                </div>

                <!-- Table -->
                <div class="space-y-5">
                    <div class="rounded-[15px] overflow-hidden">
                        <!-- Table Header -->
                        <div class="bg-[#3f4e4f] flex items-center">
                            <div class="flex-1 min-w-[270px] px-6 py-5 flex items-center gap-1.5">
                                <span class="font-bold text-sm text-[#f8f4e1]">Barang Persediaan</span>
                                <button class="w-3.5 h-3.5 flex-shrink-0 relative">
                                    <svg class="absolute top-[1px] w-[15px] h-[10px] rotate-90 scale-y-[-1]"
                                        viewBox="0 0 15 10" fill="none">
                                        <path d="M1 1L7.5 8.5L14 1" stroke="#f8f4e1" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <svg class="absolute bottom-[1px] w-[15px] h-[10px] -rotate-90"
                                        viewBox="0 0 15 10" fill="none">
                                        <path d="M1 1L7.5 8.5L14 1" stroke="#f8f4e1" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                            <div
                                class="flex-1 max-w-[200px] min-w-[100px] px-6 py-5 flex items-center justify-end gap-1.5">
                                <span class="font-bold text-sm text-[#f8f4e1] text-right">Satuan Utama</span>
                                <button class="w-3.5 h-3.5 flex-shrink-0 relative">
                                    <svg class="absolute top-[1px] w-[15px] h-[10px] rotate-90 scale-y-[-1]"
                                        viewBox="0 0 15 10" fill="none">
                                        <path d="M1 1L7.5 8.5L14 1" stroke="#f8f4e1" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <svg class="absolute bottom-[1px] w-[15px] h-[10px] -rotate-90"
                                        viewBox="0 0 15 10" fill="none">
                                        <path d="M1 1L7.5 8.5L14 1" stroke="#f8f4e1" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                            <div
                                class="flex-1 max-w-[200px] min-w-[100px] px-6 py-5 flex items-center justify-end gap-1.5">
                                <span class="font-bold text-sm text-[#f8f4e1] text-right">Satuan Lainnya</span>
                                <button class="w-3.5 h-3.5 flex-shrink-0 relative">
                                    <svg class="absolute top-[1px] w-[15px] h-[10px] rotate-90 scale-y-[-1]"
                                        viewBox="0 0 15 10" fill="none">
                                        <path d="M1 1L7.5 8.5L14 1" stroke="#f8f4e1" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <svg class="absolute bottom-[1px] w-[15px] h-[10px] -rotate-90"
                                        viewBox="0 0 15 10" fill="none">
                                        <path d="M1 1L7.5 8.5L14 1" stroke="#f8f4e1" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                            <div class="w-[72px] px-6 py-5"></div>
                        </div>

                        <!-- Table Body -->
                        <div class="max-h-[300px] overflow-y-auto pb-2.5">
                            @if ($usageMaterials && $usageMaterials->count() > 0)
                                @foreach ($usageMaterials as $material)
                                    <div class="bg-[#fafafa] flex items-center border-b border-[#d4d4d4]">
                                        <div class="flex-1 min-w-[270px] px-6 py-5">
                                            <p class="font-medium text-sm text-[#666666] truncate">
                                                {{ $material->name }}
                                            </p>
                                        </div>
                                        <div class="flex-1 max-w-[200px] min-w-[100px] px-6 py-5 text-right">
                                            <p class="font-medium text-sm text-[#666666]">
                                                {{ $material->material_details->where('unit_id', $unit_id)->first() ? $material->material_details->where('unit_id', $unit_id)->first()->unit->alias : '-' }}
                                            </p>
                                        </div>
                                        <div class="flex-1 max-w-[200px] min-w-[100px] px-6 py-5 text-right">
                                            <p class="font-medium text-sm text-[#666666]">-</p>
                                        </div>
                                        <div class="w-[72px] px-6 py-5 flex items-center justify-center">
                                            <button class="text-[#666666] hover:text-[#eb5757] transition-colors">
                                                <svg class="w-[22px] h-[22px]" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-6 py-8 text-center">
                                    <p class="text-sm text-[#666666]">Tidak ada data persediaan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pagination Info -->
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-medium text-[#666666] opacity-70">
                            Menampilkan 1 hingga {{ $usageMaterials ? $usageMaterials->count() : 0 }} dari
                            {{ $usageMaterials ? $usageMaterials->count() : 0 }} baris data
                        </p>
                        <div class="flex items-center gap-2.5">
                            <button disabled
                                class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-md opacity-50 cursor-not-allowed">
                                <svg class="w-[17px] h-[17px] text-[#666666]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <div class="min-w-[30px] px-2.5 py-1.5 bg-[#666666] rounded-md text-center">
                                <span class="text-sm font-medium text-[#fafafa]">1</span>
                            </div>
                            <button disabled
                                class="min-w-[30px] px-2.5 py-1.5 bg-[#fafafa] border border-[#666666] rounded-md opacity-50 cursor-not-allowed">
                                <svg class="w-[17px] h-[17px] text-[#666666]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider and Close Info -->
            <div class="space-y-5 pt-5">
                <div class="h-px bg-[#666666]"></div>
                <p class="text-sm font-medium text-[#666666] opacity-70 text-center">
                    Tekan bagian luar untuk menutup halaman
                </p>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Satuan Ukur</flux:heading>
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
