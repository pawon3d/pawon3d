<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Daftar Barang Persediaan</h1>
        <div class="flex gap-2 items-center">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>

            <!-- Tombol Riwayat Pembaruan -->
            <button type="button" wire:click="riwayatPembaruan"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Riwayat Pembaruan
            </button>
        </div>
    </div>
    <div class="flex items-center border bg-white shadow-lg rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Augue lectus risus sed ultricies quis. Facilisi id tempus tortor
                aliquet tempus. Sagittis nec odio sed nisl arcu sed. Vulputate aliquam nibh adipiscing lacinia nisi
                vestibulum vitae. Auctor sagittis porttitor dolor hendrerit. Mi sollicitudin scelerisque purus
                ullamcorper. Gravida nunc facilisis et consectetur tortor purus eget consectetur nulla.
            </p>

        </div>
    </div>

    <div class="mt-4 bg-white shadow-lg rounded-lg p-4">
        <div class="flex justify-between items-center mb-4">
            <!-- Search Input -->
            <div class="p-4 flex">
                <input wire:model.live="search" placeholder="Cari..."
                    class="w-lg px-4 py-2 border border-accent rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <flux:button :loading="false" class="ml-2" variant="ghost">
                    <flux:icon.funnel variant="mini" />
                    <span>Filter</span>
                </flux:button>
            </div>
            <div class="flex gap-2 items-center">
                {{-- <a href="{{ route('bahan-baku.tambah') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Persediaan
                </a> --}}
                <flux:button type="button" variant="primary" href="{{ route('satuan-ukur') }}" icon="lamp-ceiling">
                    Daftar
                    Satuan Ukur</flux:button>
                <flux:button type="button" variant="primary" href="{{ route('kategori-persediaan') }}" icon="shapes">
                    Daftar Kategori
                </flux:button>
                <flux:button type="button" variant="primary" href="{{ route('bahan-baku.tambah') }}" icon="plus">Tambah
                    Persediaan
                </flux:button>
            </div>
        </div>
        <div class="flex justify-between items-center mb-4">
            <div class="flex gap-2 items-center">
                <flux:dropdown>
                    <flux:button variant="ghost">
                        @if ($filterStatus)
                        {{ $filterStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                        @else
                        Semua Kategori
                        @endif
                        ({{ $materials->total() }})
                        <flux:icon.chevron-down variant="mini" />
                    </flux:button>
                    <flux:menu>
                        <flux:menu.radio.group wire:model.live="filterStatus">
                            <flux:menu.radio value="">Semua Kategori</flux:menu.radio>
                            <flux:menu.radio value="aktif">Aktif</flux:menu.radio>
                            <flux:menu.radio value="nonaktif">Tidak Aktif</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>
                <flux:dropdown>
                    <flux:button variant="ghost">
                        Urutkan Barang
                        <flux:icon.chevron-down variant="mini" />

                    </flux:button>

                    <flux:menu>
                        <flux:menu.radio.group wire:model="sortByCategory">
                            <flux:menu.radio value="name">Nama</flux:menu.radio>
                            <flux:menu.radio value="status">Status</flux:menu.radio>
                            <flux:menu.radio value="material" checked>Jenis Produk</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>
            </div>
            <div class="flex gap-2 mr-4 items-center">
                <span class="text-sm mr-2">Tampilan Produk:</span>

                <!-- Grid View -->
                <div class="relative">
                    <input type="radio" name="viewMode" id="grid-view" value="grid" wire:model.live="viewMode"
                        class="absolute opacity-0 w-0 h-0">
                    <label for="grid-view" class="cursor-pointer">
                        <flux:icon icon="squares-2x2"
                            class="{{ $viewMode === 'grid' ? 'text-gray-100 bg-gray-600' : 'text-gray-800 bg-white' }} rounded-xl border border-gray-600 hover:text-gray-100 hover:bg-gray-600 transition-colors size-8" />
                    </label>
                </div>

                <!-- List View -->
                <div class="relative">
                    <input type="radio" name="viewMode" id="list-view" value="list" wire:model.live="viewMode"
                        class="absolute opacity-0 w-0 h-0">
                    <label for="list-view" class="cursor-pointer">
                        <flux:icon icon="list-bullet"
                            class="{{ $viewMode === 'list' ? 'text-gray-100 bg-gray-600' : 'text-gray-800 bg-white' }} rounded-xl border border-gray-600 hover:text-gray-100 hover:bg-gray-600 transition-colors size-8" />
                    </label>
                </div>
            </div>
        </div>

        @if ($viewMode === 'grid')
        {{-- grid view --}}
        <div class="bg-white">
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4 mt-4">
                @forelse($materials as $material)
                <div class="p-4 text-center">
                    {{-- <a href="{{ route('bahan-baku.edit', $material->id) }}"
                        class="hover:bg-gray-50 cursor-pointer">
                        --}}
                        <div class="flex justify-center mb-4">
                            @if ($material->image)
                            <img src="{{ asset('storage/' . $material->image) }}" alt="{{ $material->name }}"
                                class="w-full h-36 object-fill rounded-lg border border-gray-200" />
                            @else
                            <img src="{{ asset('img/no-img.jpg') }}" alt="Gambar Produk"
                                class="w-full h-36 object-fill rounded-lg border border-gray-200" />
                            @endif
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg montserrat-regular font-semibold mb-2">{{ $material->name }}</h3>
                            <p class="text-gray-600 mb-4 text-sm montserrat-regular">
                                @php
                                $quantity_main_total = 0;
                                $batches = $material->batches;

                                foreach ($batches as $b) {
                                $detail = \App\Models\MaterialDetail::where('material_id', $material->id)
                                ->where('unit_id', $b->unit_id)
                                ->first();

                                if ($detail) {
                                $quantity_main = ($b->batch_quantity ?? 0) * ($detail->quantity ?? 0);
                                $quantity_main_total += $quantity_main;
                                }
                                }

                                // Ambil satu detail utama untuk unit (jika ada)
                                $mainDetail = \App\Models\MaterialDetail::where('material_id', $material->id)
                                ->where('is_main', true)
                                ->first();
                                @endphp
                                {{ $batches->isNotEmpty() ? $quantity_main_total . ' ' . ($mainDetail->unit->alias ??
                                '') :
                                'Tidak Tersedia' }}
                            </p>
                            <p class="text-gray-600 mb-4 text-sm montserrat-regular">
                                {{ $material->status ?? 'Kosong' }}
                            </p>
                            @php
                            // Ambil semua batch
                            $batches = $material->batches;

                            // Filter batch yang belum kadaluarsa dan urutkan dari yang paling dekat
                            $nextBatch = $batches
                            ->filter(fn($batch) => \Carbon\Carbon::parse($batch->date)->isFuture())
                            ->sortBy(fn($batch) => \Carbon\Carbon::parse($batch->date))
                            ->first();
                            @endphp

                            <p class="text-gray-600 mb-4 text-sm montserrat-regular">
                                {{ $nextBatch ? \Carbon\Carbon::parse($nextBatch->date)->format('d / m / Y') : 'Belum
                                Ada
                                Tanggal' }}
                            </p>
                        </div>
                        <flux:button class="w-full" variant="primary" type="button"
                            href="{{ route('bahan-baku.edit', $material->id) }}">
                            Lihat
                        </flux:button>
                        {{--
                    </a> --}}
                </div>
                @empty
                <div
                    class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
                    <p class="text-gray-700 font-semibold">Belum ada persediaan.</p>
                    <p class="text-gray-700">Tekan tombol “Tambah Persediaan” untuk menambahkan persediaan.</p>
                </div>
                @endforelse
            </div>
            <div class="p-4">
                {{ $materials->links() }}
            </div>
        </div>
        @elseif ($viewMode === 'list')
        {{-- list view --}}
        @if ($materials->isEmpty())
        <div class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center">
            <p class="text-gray-700 font-semibold">Belum ada barang persediaan.</p>
            <p class="text-gray-700">Tekan tombol “Tambah Persediaan” untuk menambahkan persediaan.</p>
        </div>
        @else
        <div class="bg-white rounded-xl border">
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 cursor-pointer"
                                wire:click="sortBy('name')">Barang Persediaan
                                {{ $sortDirection === 'asc' && $sortField === 'name' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 cursor-pointer"
                                wire:click='sortBy("is_active")'>Status Tampil
                                {{ $sortDirection === 'asc' && $sortField === 'is_active' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Jumlah Persediaan
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 cursor-pointer"
                                wire:click='sortBy("expiry_date")'>Tanggal Expired
                                {{ $sortDirection === 'asc' && $sortField === 'expiry_date' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 cursor-pointer"
                                wire:click='sortBy("status")'>Status Persediaan {{ $sortDirection === 'asc' &&
                                $sortField
                                === 'status' ? '↑' : '↓' }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($materials as $material)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('bahan-baku.edit', $material->id) }}"
                                    class="hover:bg-gray-50 cursor-pointer">
                                    {{ $material->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 space-x-2 whitespace-nowrap">
                                {{ $material->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @php
                                $quantity_main_total = 0;
                                $batches = $material->batches;

                                foreach ($batches as $b) {
                                $detail = \App\Models\MaterialDetail::where(
                                'material_id',
                                $material->id,
                                )
                                ->where('unit_id', $b->unit_id)
                                ->first();

                                if ($detail) {
                                $quantity_main =
                                ($b->batch_quantity ?? 0) * ($detail->quantity ?? 0);
                                $quantity_main_total += $quantity_main;
                                }
                                }

                                // Ambil satu detail utama untuk unit (jika ada)
                                $mainDetail = \App\Models\MaterialDetail::where(
                                'material_id',
                                $material->id,
                                )
                                ->where('is_main', true)
                                ->first();
                                @endphp
                                {{ $batches->isNotEmpty() ? $quantity_main_total . ' ' . ($mainDetail->unit->alias ??
                                '') :
                                'Tidak Tersedia' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @php
                                // Ambil semua batch
                                $batches = $material->batches;

                                // Filter batch yang belum kadaluarsa dan urutkan dari yang paling dekat
                                $nextBatch = $batches
                                ->filter(fn($batch) => \Carbon\Carbon::parse($batch->date)->isFuture())
                                ->sortBy(fn($batch) => \Carbon\Carbon::parse($batch->date))
                                ->first();
                                @endphp


                                {{ $nextBatch ? \Carbon\Carbon::parse($nextBatch->date)->format('d / m / Y') : 'Belum
                                Ada Tanggal' }}

                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $material->status ?? 'Kosong' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $materials->links() }}
            </div>
        </div>
        @endif
        @endif
    </div>


    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Barang Persediaan</flux:heading>
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