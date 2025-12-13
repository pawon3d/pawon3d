<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <flux:button variant="secondary" icon="arrow-left" href="{{ route('bahan-baku') }}"
                class="px-6 py-2.5 bg-[#313131] hover:bg-[#3a3a3a] text-white rounded-[15px] shadow-sm flex items-center gap-1.5 font-semibold text-base transition"
                wire:navigate>
                Kembali
            </flux:button>
            <h1 class="text-xl font-semibold text-[#666666]">Kelola Kategori Persediaan</h1>
        </div>
        <div class="flex gap-2 items-center">
            <flux:button variant="secondary" wire:click="riwayatPembaruan">
                Riwayat Pembaruan
            </flux:button>
        </div>
    </div>

    <x-alert.info>
        Kategori Persediaan. Lihat atau tambah kategori untuk mengelompokkan persediaan berdasarkan jenis, bentuk,
        rasa, dan fungsinya. Persediaan dapat memiliki banyak kategori dan kategori dapat terdiri dari banyak
        persediaan.
    </x-alert.info>

    <div class="bg-white shadow-lg rounded-[15px] p-7">
        <div class="flex justify-between items-center mb-7">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="bg-white border border-[#666666] rounded-[20px] px-4 py-2 flex items-center max-w-md">
                    <svg class="w-5 h-5 text-[#666666] mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <input wire:model.live="search" placeholder="Cari Kategori"
                        class="flex-1 outline-none text-[#959595] font-medium text-base" />
                </div>
            </div>
            <div class="flex gap-2 items-center">
                <flux:button variant="primary" icon="plus" type="button" wire:click="showAddModal"
                    class="px-6 py-2.5 bg-[#74512d] hover:bg-[#8a5f35] text-white rounded-[15px] shadow-sm flex items-center gap-2 font-semibold text-base transition">
                    Tambah Kategori
                </flux:button>
            </div>
        </div>

        <x-table.paginated :headers="[
            ['label' => 'Kategori Persediaan', 'sortable' => true, 'sort-by' => 'name'],
            ['label' => 'Status Tampil', 'sortable' => true, 'sort-by' => 'is_active'],
            ['label' => 'Jumlah Penggunaan', 'sortable' => true, 'sort-by' => 'details_count', 'align' => 'right'],
        ]" :paginator="$categories" headerBg="#3f4e4f" headerText="#f8f4e1" bodyBg="#fafafa"
            bodyText="#666666" emptyMessage="Tidak ada data.">
            @foreach ($categories as $category)
                <tr class="hover:bg-gray-50 cursor-pointer" wire:click="edit('{{ $category->id }}')">
                    <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                        {{ $category->name }}
                    </td>
                    <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                        {{ $category->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </td>
                    <td class="px-6 py-5 text-right text-[#666666] font-medium text-sm">
                        {{ $category->details_count }}
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    <flux:modal name="tambah-kategori" class="w-full max-w-lg" wire:model="showModal">
        <div class="bg-[#fafafa] rounded-[15px] p-8">
            <div class="space-y-8">
                <div>
                    <h2 class="text-xl font-medium text-[#333333]">Tambah Kategori Persediaan</h2>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2.5">
                        <label for="name" class="block text-lg font-medium text-[#333333]">Nama Kategori</label>
                        <input type="text" id="name" wire:model.lazy="name" placeholder="Contoh : Bahan Kering"
                            class="w-full px-5 py-2.5 border border-[#d4d4d4] rounded-[15px] bg-[#fafafa] text-[#666666] text-base focus:outline-none focus:border-[#adadad] focus:ring-0"
                            required />
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-lg font-medium text-[#666666]">Tampil Kategori</label>
                            <flux:switch wire:model.live="is_active" class="data-checked:bg-green-500" />
                        </div>
                        <p class="text-sm text-[#666666] leading-relaxed">
                            Aktifkan opsi ini jika kategori ingin ditampilkan dan digunakan.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2.5">
                    <flux:button variant="filled" icon="x-mark" type="button" wire:click="$set('showModal', false)"
                        class="px-6 py-2.5 bg-[#c4c4c4] hover:bg-[#b0b0b0] text-[#333333] rounded-[15px] shadow-sm flex items-center gap-2 font-semibold text-base transition">
                        Batal
                    </flux:button>
                    <flux:button icon="save" type="button" variant="secondary" wire:click="store">
                        Simpan
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="rincian-kategori" class="w-full max-w-lg" wire:model="showEditModal">
        <div class="bg-[#fafafa] rounded-[15px] p-8">
            <div class="space-y-8">
                <div>
                    <h2 class="text-xl font-medium text-[#333333]">Rincian Kategori Persediaan</h2>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2.5">
                        <label for="edit-name" class="block text-lg font-medium text-[#333333]">Nama Kategori</label>
                        <input type="text" id="edit-name" wire:model.lazy="name"
                            class="w-full px-5 py-2.5 border-[1.5px] border-[#adadad] rounded-[15px] bg-[#fafafa] text-[#666666] text-base focus:outline-none focus:border-[#8a8a8a] focus:ring-0"
                            required />
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="text-base font-medium text-[#333333]">Jumlah Penggunaan</label>
                        <button type="button" wire:click="showUsageModal"
                            class="flex items-center gap-1 text-base font-medium text-[#333333] hover:text-[#666666]">
                            <span>{{ $products ?? 0 }}</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M9.29 6.71a.996.996 0 000 1.41L13.17 12l-3.88 3.88a.996.996 0 101.41 1.41l4.59-4.59a.996.996 0 000-1.41L10.7 6.7c-.38-.38-1.02-.38-1.41.01z" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-lg font-medium text-[#666666]">Tampil Kategori</label>
                            <flux:switch wire:model.live="is_active" class="data-checked:bg-green-500" />
                        </div>
                        <p class="text-sm text-[#666666] leading-relaxed">
                            Aktifkan opsi ini jika kategori ingin ditampilkan dan digunakan.
                        </p>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <flux:modal.trigger name="delete-category">

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
                        <flux:button variant="filled" icon="x-mark" type="button"
                            wire:click="$set('showEditModal', false)"
                            class="px-6 py-2.5 bg-[#c4c4c4] hover:bg-[#b0b0b0] text-[#333333] rounded-[15px] shadow-sm flex items-center gap-2 font-semibold text-base transition">
                            Batal
                        </flux:button>
                        <flux:button icon="save" type="button" variant="secondary" wire:click="update">
                            Simpan Pembaruan
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <flux:modal name="delete-category" class="w-full max-w-md">
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
    <flux:modal name="jumlah-penggunaan" class="w-full max-w-lg">
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-7">
            <div class="space-y-5">
                <div class="space-y-5">
                    <h2 class="text-lg font-medium text-[#333333]">Daftar Persediaan</h2>

                    <!-- Search Bar -->
                    <div class="bg-white border border-[#666666] rounded-[20px] px-4 py-2 flex items-center">
                        <svg class="w-5 h-5 text-[#666666] mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <input wire:model.live="usageSearch" placeholder="Cari Persediaan"
                            class="flex-1 outline-none text-[#959595] font-medium text-base bg-transparent" />
                    </div>

                    <!-- Table -->
                    <x-list.paginated :items="$usageMaterials" :columns="[
                        [
                            'label' => 'Barang Persediaan',
                            'sortable' => true,
                            'sort-method' => 'sortUsageMaterials',
                        ],
                    ]" headerBg="#3f4e4f" headerText="#f8f4e1"
                        bodyBg="#fafafa" bodyText="#666666" emptyMessage="Tidak ada persediaan.">
                        @if ($usageMaterials && count($usageMaterials) > 0)
                            @foreach ($usageMaterials as $material)
                                <tr>
                                    <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                                        {{ $material->name }}
                                    </td>
                                    <td class="px-6 py-5 text-center w-[72px]">
                                        <button type="button" wire:click="removeFromCategory('{{ $material->id }}')"
                                            class="text-[#666666] hover:text-[#eb5757] transition">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        <x-slot name="actionColumn">
                            <th class="px-6 py-5 w-[72px]"></th>
                        </x-slot>
                    </x-list.paginated>
                </div>

                <!-- Bottom Section -->
                <div class="space-y-5 pt-5 border-t border-[#666666]">
                    <p class="text-sm font-medium text-[#666666] opacity-70 text-center">
                        Tekan bagian luar untuk menutup halaman
                    </p>
                </div>
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
