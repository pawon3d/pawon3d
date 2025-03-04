<div>
    <div class="flex items-end justify-between mb-7">
        <h1 class="text-3xl font-bold">Bahan Baku Olahan</h1>
        <button wire:click="$set('showAddModal', true)" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Data
        </button>
    </div>

    <!-- Search Input -->
    <div class="mb-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Cari bahan olahan..." class="w-full max-w-sm px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
    </div>

    <!-- Table -->
    <div class="rounded-xl border bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Olahan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($processedMaterials as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button wire:click="showDetail('{{ $item->id }}')" class="px-3 py-1 border rounded-md hover:bg-gray-100">
                                Detail
                            </button>
                            <button wire:click="edit('{{ $item->id }}')" class="px-3 py-1 border rounded-md hover:bg-gray-100">
                                Edit
                            </button>
                            <button wire:click="confirmDelete('{{ $item->id }}')" class="px-3 py-1 border rounded-md text-red-600 hover:bg-red-50">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $processedMaterials->links() }}
        </div>
    </div>

    <!-- Add Modal -->
    <flux:modal name="tambah-bahan-olahan" class="w-full max-w-lg" wire:model="showAddModal">
        <form wire:submit.prevent="store" class="space-y-4">
            <div class="p-4 space-y-4">
                <div>
                    <flux:input label="Nama Olahan" wire:model="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <flux:input label="Jumlah" wire:model="quantity" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bahan Baku</label>
                    @foreach($processedMaterialDetails as $index => $detail)
                    <div class="grid grid-cols-12 gap-4 mb-2 items-center">
                        <!-- Kolom Pilih Bahan -->
                        <div class="col-span-5">
                            <flux:select wire:change="setMaterial({{ $index }}, $event.target.value)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <flux:select.option value="">Pilih Bahan Baku</flux:select.option>
                                @foreach($materials as $material)
                                <option value="{{ $material->id }}" {{ $detail['material_id'] == $material->id ? 'selected' : '' }}>
                                    {{ $material->name }}
                                </option>
                                @endforeach
                            </flux:select>
                            @error("processedMaterialDetails.{$index}.material_id")
                            <div class="text-red-500 text-sm mt-1">Material harus dipilih</div>
                            @enderror
                        </div>

                        <!-- Kolom Input Quantity -->
                        <div class="col-span-4 relative">
                            <flux:input type="number" wire:model="processedMaterialDetails.{{ $index }}.material_quantity" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500">
                                {{ $detail['material_unit'] }}
                            </span>
                        </div>

                        <!-- Kolom Tombol Hapus -->
                        <div class="col-span-3 text-right">
                            <button type="button" wire:click="removeDetailRow({{ $index }})" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach

                    <button type="button" wire:click="addDetailRow" class="mt-2 text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Bahan Baku
                    </button>
                </div>

            </div>

            <div class="flex justify-end gap-2 px-4 py-3 bg-gray-50">
                <button type="button" wire:click="$set('showAddModal', false)" class="px-4 py-2 border rounded-md">
                    Batal
                </button>
                <flux:button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal name="edit-bahan-olahan" class="w-full max-w-lg" wire:model="showEditModal">
        <form wire:submit.prevent="update" class="space-y-4">
            <div class="p-4 space-y-4">
                <div>
                    <flux:input label="Nama Olahan" wire:model="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <flux:input label="Jumlah" wire:model="quantity" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bahan Baku</label>
                    @foreach($processedMaterialDetails as $index => $detail)
                    <div class="grid grid-cols-12 gap-4 mb-2 items-center">
                        <!-- Kolom Pilih Bahan -->
                        <div class="col-span-5">
                            <flux:select wire:change="setMaterial({{ $index }}, $event.target.value)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <flux:select.option value="">Pilih Bahan Baku</flux:select.option>
                                @foreach($materials as $material)
                                <option value="{{ $material->id }}" {{ $detail['material_id'] == $material->id ? 'selected' : '' }}>
                                    {{ $material->name }}
                                </option>
                                @endforeach
                            </flux:select>
                            @error("processedMaterialDetails.{$index}.material_id")
                            <div class="text-red-500 text-sm mt-1">Material harus dipilih</div>
                            @enderror
                        </div>

                        <!-- Kolom Input Quantity -->
                        <div class="col-span-4 relative">
                            <flux:input type="number" wire:model="processedMaterialDetails.{{ $index }}.material_quantity" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500">
                                {{ $detail['material_unit'] }}
                            </span>
                        </div>

                        <!-- Kolom Tombol Hapus -->
                        <div class="col-span-3 text-right">
                            <button type="button" wire:click="removeDetailRow({{ $index }})" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach

                    <button type="button" wire:click="addDetailRow" class="mt-2 text-blue-600 hover:text-blue-800 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Bahan Baku
                    </button>
                </div>

            </div>

            <div class="flex justify-end gap-2 px-4 py-3 bg-gray-50">
                <button type="button" wire:click="$set('showAddModal', false)" class="px-4 py-2 border rounded-md">
                    Batal
                </button>
                <flux:button type="submit" class="px-4 py-2">
                    Simpan
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Detail Modal -->
    <flux:modal name="detail-bahan-olahan" class="w-full max-w-lg" wire:model="showDetailModal">
        @if($detailData)
        <div class="p-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Olahan</label>
                <p class="mt-1">{{ $detailData->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                <p class="mt-1">{{ $detailData->quantity }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bahan Baku</label>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detailData->processed_material_details as $detail)
                        <tr>
                            <td class="px-6 py-4">{{ $detail->material->name }}</td>
                            <td class="px-6 py-4">{{ $detail->material_quantity }}</td>
                            <td class="px-6 py-4">{{ $detail->material_unit }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="flex justify-end gap-2 px-4 py-3 bg-gray-50">
            <button type="button" wire:click="$set('showDetailModal', false)" class="px-4 py-2 border rounded-md">
                Tutup
            </button>
        </div>
    </flux:modal>
</div>
