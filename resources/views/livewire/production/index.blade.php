<div>
    <!-- Header -->
    <div class="flex items-end justify-between mb-7">
        <h1 class="text-3xl font-bold">Produksi</h1>
        <div class="flex gap-2 items-center">
            <button wire:click="openAddModal" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produksi
            </button>
        </div>
    </div>

    <div class="flex items-center space-x-4 mb-4">
        <flux:input icon="magnifying-glass" type="text" wire:model.live="search" placeholder="Cari produk..." />

        <flux:select wire:model.live="typeFilter" class="px-4 py-2 border rounded-lg">
            <flux:select.option value="all">Semua Tipe</flux:select.option>
            <flux:select.option value="siap beli">Siap Beli</flux:select.option>
            <flux:select.option value="pesanan">Pesanan</flux:select.option>
        </flux:select>
    </div>
    <!-- Tabel Produksi -->
    <div class="bg-white rounded-xl border">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Produksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Produksi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($productions as $production)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $production->product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $production->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $production->count }}x</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">{{ $production->status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $production->time }} menit</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button wire:click="openDetailModal('{{ $production->id }}')" class="px-3 py-1 border rounded-md hover:bg-gray-100">
                                Detail
                            </button>
                            <button wire:click="openEditModal('{{ $production->id }}')" class="px-3 py-1 border rounded-md hover:bg-gray-100">
                                Edit
                            </button>
                            <button wire:click="confirmDelete('{{ $production->id }}')" class="px-3 py-1 border rounded-md text-red-600 hover:bg-red-50">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $productions->links() }}
        </div>
    </div>

    <!-- Modal Tambah Produksi -->
    <flux:modal name="tambah-produksi" class="w-full max-w-lg" wire:model="openAdd">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Produksi</flux:heading>
            </div>
            <div class="flex gap-4 mb-4">
                <button type="button" wire:click="$set('activeTab', 'pesanan')" class="tab-button inline-flex items-center px-4 py-2 {{ $activeTab === 'pesanan' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600' }}">
                    Pesanan
                </button>
                <button type="button" wire:click="$set('activeTab', 'siap_beli')" class="tab-button inline-flex items-center px-4 py-2 {{ $activeTab === 'siap_beli' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600' }}">
                    Siap Beli
                </button>
            </div>
            <form wire:submit.prevent="addProduction" class="space-y-4">
                @if($activeTab === 'pesanan')
                <!-- Pilih Transaksi -->
                <flux:select label="Pilih Transaksi" type="select" wire:model.live="transaction_id">
                    <option value="">Pilih Transaksi</option>
                    @foreach($transactions->filter(function($t) {
                    return $t->type === 'pesanan' && $t->schedule === now()->format('d-m-Y');
                    }) as $transaction)
                    <option value="{{ $transaction->id }}">
                        {{ $transaction->schedule}} - {{ count($transaction->details) }} produk
                    </option>
                    @endforeach
                </flux:select>

                <!-- Pilih Detail Transaksi -->
                @if($transaction_id)
                <flux:select label="Pilih Detail Transaksi" type="select" wire:model.live="transaction_detail_id">
                    <option value="">Pilih Detail</option>
                    @php
                    $selectedTransaction = $transactions->firstWhere('id', $transaction_id);
                    @endphp
                    @if($selectedTransaction)
                    @foreach($selectedTransaction->details as $detail)
                    <option value="{{ $detail->id }}">
                        {{ $detail->product->name }} - Jumlah: {{ $detail->quantity }}
                    </option>
                    @endforeach
                    @endif
                </flux:select>
                @endif

                <!-- Field Produksi -->
                <flux:input readonly label="Jumlah Produksi" placeholder="Masukkan jumlah produksi" type="number" wire:model="count" />
                <flux:input label="Status Produksi" readonly placeholder="Masukkan status produksi" type="text" wire:model="status" />
                <div class="relative">
                    <flux:input label="Waktu Produksi" placeholder="Masukkan waktu produksi" type="number" wire:model="time" />
                    <span class="absolute inset-y-0 right-0 flex items-center mt-6 pr-3 text-gray-400">
                        Menit
                    </span>
                </div>
                <flux:input readonly label="Jumlah Hasil Produksi" placeholder="Masukkan jumlah hasil produksi" type="number" wire:model.live="quantity" />
                @else
                <!-- Field untuk Siap Beli -->
                <flux:select label="Pilih Produk" type="select" wire:model.live="product_id">
                    <option value="">Pilih Produk</option>
                    @foreach($readyProducts as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </flux:select>

                <!-- Quantity input user -->
                <flux:input label="Quantity" type="number" wire:model.live="quantity" placeholder="Masukkan quantity" />

                <!-- Jumlah Produksi -->
                <flux:input label="Jumlah Produksi" readonly type="number" wire:model="count" placeholder="Masukkan jumlah produksi" />

                <!-- Status & Waktu Produksi -->
                <flux:input label="Status Produksi" placeholder="Masukkan status produksi" type="text" wire:model="status" />
                <div class="relative">
                    <flux:input label="Waktu Produksi" placeholder="Masukkan waktu produksi" type="number" wire:model="time" />
                    <span class="absolute inset-y-0 right-0 flex items-center mt-6 pr-3 text-gray-400">
                        Menit
                    </span>
                </div>
                @endif

                <!-- Tampilkan List Komposisi Produk -->
                @if(!empty($compositionList))
                <div class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Komposisi Produk</label>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis Bahan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($compositionList as $composition)
                                <tr>
                                    <td class="px-4 py-2 text-sm">
                                        {{ $composition['material_id'] ? 'Bahan Baku' : 'Bahan Olahan' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($composition['material_id'])
                                        {{ \App\Models\Material::find($composition['material_id'])->name ?? '-' }}
                                        @else
                                        {{ \App\Models\ProcessedMaterial::find($composition['processed_material_id'])->name ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        {{ $composition['material_id'] ? $composition['material_quantity'] : $composition['processed_material_quantity'] }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        {{ $composition['material_unit'] ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Modal Edit Produksi -->
    <flux:modal name="edit-produksi" class="w-full max-w-lg" wire:model="openEdit">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Produksi</flux:heading>
            </div>
            <div class="flex gap-4 mb-4">
                <button type="button" wire:click="$set('activeTab', 'pesanan')" class="tab-button inline-flex items-center px-4 py-2 {{ $activeTab === 'pesanan' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600' }}">
                    Pesanan
                </button>
                <button type="button" wire:click="$set('activeTab', 'siap_beli')" class="tab-button inline-flex items-center px-4 py-2 {{ $activeTab === 'siap_beli' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-600' }}">
                    Siap Beli
                </button>
            </div>
            <form wire:submit.prevent="editProduction" class="space-y-4">
                <!-- Pilih Transaksi -->
                @if($activeTab === 'pesanan')
                <!-- Pilih Transaksi -->
                <flux:select label="Pilih Transaksi" type="select" wire:model.live="transaction_id">
                    <option value="">Pilih Transaksi</option>
                    @foreach($transactions->filter(function($t) {
                    return $t->type === 'pesanan' && $t->schedule === now()->format('d-m-Y');
                    }) as $transaction)
                    <option value="{{ $transaction->id }}">
                        {{ $transaction->schedule}} - {{ count($transaction->details) }} produk
                    </option>
                    @endforeach
                </flux:select>

                <!-- Pilih Detail Transaksi -->
                @if($transaction_id)
                <flux:select label="Pilih Detail Transaksi" type="select" wire:model.live="transaction_detail_id">
                    <option value="">Pilih Detail</option>
                    @php
                    $selectedTransaction = $transactions->firstWhere('id', $transaction_id);
                    @endphp
                    @if($selectedTransaction)
                    @foreach($selectedTransaction->details as $detail)
                    <option value="{{ $detail->id }}">
                        {{ $detail->product->name }} - Jumlah: {{ $detail->quantity }}
                    </option>
                    @endforeach
                    @endif
                </flux:select>
                @endif

                <!-- Field Produksi -->
                <flux:input readonly label="Jumlah Produksi" placeholder="Masukkan jumlah produksi" type="number" wire:model="count" />
                <flux:input label="Status Produksi" readonly placeholder="Masukkan status produksi" type="text" wire:model="status" />
                <div class="relative">
                    <flux:input label="Waktu Produksi" placeholder="Masukkan waktu produksi" type="number" wire:model="time" />
                    <span class="absolute inset-y-0 right-0 flex items-center mt-6 pr-3 text-gray-400">
                        Menit
                    </span>
                </div>
                <flux:input readonly label="Jumlah Hasil Produksi" placeholder="Masukkan jumlah hasil produksi" type="number" wire:model.live="quantity" />
                @else
                <!-- Field untuk Siap Beli -->
                <flux:select label="Pilih Produk" type="select" wire:model.live="product_id">
                    <option value="">Pilih Produk</option>
                    @foreach($readyProducts as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </flux:select>

                <!-- Quantity input user -->
                <flux:input label="Quantity" type="number" wire:model.live="quantity" placeholder="Masukkan quantity" />

                <!-- Jumlah Produksi -->
                <flux:input label="Jumlah Produksi" readonly type="number" wire:model="count" placeholder="Masukkan jumlah produksi" />

                <!-- Status & Waktu Produksi -->
                <flux:input label="Status Produksi" placeholder="Masukkan status produksi" type="text" wire:model="status" />
                <div class="relative">
                    <flux:input label="Waktu Produksi" placeholder="Masukkan waktu produksi" type="number" wire:model="time" />
                    <span class="absolute inset-y-0 right-0 flex items-center mt-6 pr-3 text-gray-400">
                        Menit
                    </span>
                </div>
                @endif
                <!-- Tampilkan List Komposisi Produk -->
                @if(!empty($compositionList))
                <div class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Komposisi Produk</label>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis Bahan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($compositionList as $composition)
                                <tr>
                                    <td class="px-4 py-2 text-sm">
                                        {{ $composition['material_id'] ? 'Bahan Baku' : 'Bahan Olahan' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($composition['material_id'])
                                        {{ \App\Models\Material::find($composition['material_id'])->name ?? '-' }}
                                        @else
                                        {{ \App\Models\ProcessedMaterial::find($composition['processed_material_id'])->name ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        {{ $composition['material_id'] ? $composition['material_quantity'] : $composition['processed_material_quantity'] }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        {{ $composition['material_unit'] ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:modal name="detail-produksi" class="w-full max-w-lg" wire:model="showDetailModal">
        @if($detailData)
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">Detail Produksi</flux:heading>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Produk</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $detailData->product->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Produksi</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $detailData->count }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $detailData->status }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Waktu Produksi</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $detailData->time }}</p>
                </div>
            </div>

            @if($detailData->product->product_compositions)
            <div class="border-t pt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Komposisi Produk</label>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis Bahan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($detailData->product->product_compositions as $composition)
                            <tr>
                                <td class="px-4 py-2 text-sm">
                                    {{ $composition->material_id ? 'Bahan Baku' : 'Bahan Olahan' }}
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    @if($composition->material_id)
                                    {{ \App\Models\Material::find($composition->material_id)->name ?? '-' }}
                                    @else
                                    {{ \App\Models\ProcessedMaterial::find($composition->processed_material_id)->name ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    {{ $composition->material_id ? $composition->material_quantity : $composition->processed_material_quantity }}
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    {{ $composition->material_unit ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="flex justify-between gap-2 mt-6">
                <flux:button type="button" wire:click="$set('showDetailModal', false)" class="btn-secondary">Tutup</flux:button>
                @if($detailData->status !== 'sedang dibuat')
                <flux:button type="button" wire:click="startProduction" class="btn-primary">Mulai</flux:button>
                @else
                <flux:button type="button" wire:click="cancelProduction" class="btn-secondary">Batalkan Produksi</flux:button>
                @endif
            </div>
        </div>
        @endif
    </flux:modal>
</div>
