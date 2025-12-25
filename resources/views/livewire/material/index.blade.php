<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h1 class="text-xl font-semibold text-[#666666]">Daftar Barang Persediaan</h1>
        <div class="flex flex-col sm:flex-row gap-2.5 items-center w-full sm:w-auto">
            <flux:button variant="secondary" wire:click="riwayatPembaruan" class="w-full sm:w-auto">
                Riwayat Pembaruan
            </flux:button>
        </div>
    </div>

    <x-alert.info>
        Persediaan. Persediaan adalah tempat mengatur bahan produksi dan barang jual langsung seperi air mineral
        kemasan. Persediaan digunakan untuk menentukan harga jual produk, sehingga modal suatu produk dapat ditentukan
        dengan tepat.
    </x-alert.info>

    <div class="mt-4 bg-white shadow-lg rounded-lg p-4">
        <div class="flex flex-col xl:flex-row justify-between xl:items-center gap-4 mb-6">
            <!-- Search Input -->
            <div class="flex flex-col sm:flex-row gap-4 sm:items-center flex-1 w-full">
                <div
                    class="flex-1 bg-white border border-[#666666] rounded-full px-4 py-0 min-h-[46px] flex items-center">
                    <flux:icon.magnifying-glass class="size-[20px] text-[#666666] shrink-0" />
                    <input wire:model.live="search" placeholder="Cari Barang Persediaan..."
                        class="flex-1 px-2.5 py-2.5 font-montserrat font-medium text-[16px] text-[#959595] border-0 focus:outline-none focus:ring-0 bg-transparent" />
                </div>
                <div class="flex items-center gap-1 cursor-pointer justify-center">
                    <flux:icon.funnel class="size-[20px] text-[#666666]" />
                    <span class="font-montserrat font-medium text-[16px] text-[#666666] px-1 py-2.5">Filter</span>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 w-full xl:w-auto">
                <flux:button type="button" variant="primary" href="{{ route('satuan-ukur') }}" icon="lamp-ceiling"
                    wire:navigate class="w-full sm:w-auto">
                    Satuan Ukur</flux:button>
                <flux:button type="button" variant="primary" href="{{ route('kategori-persediaan') }}" icon="shapes"
                    wire:navigate class="w-full sm:w-auto">
                    Kategori
                </flux:button>
                <flux:button type="button" variant="primary" icon="plus" href="{{ route('bahan-baku.tambah') }}"
                    wire:navigate class="w-full sm:w-auto">
                    Tambah Persediaan
                </flux:button>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
            <div class="flex gap-2 items-center sm:px-4">
                <flux:text class="text-base font-medium text-[#666666]">
                    Jumlah Persediaan: {{ $materials->total() }}
                </flux:text>
            </div>
            <div class="flex gap-4 items-center sm:pr-4 justify-between sm:justify-end">
                <span class="text-sm font-medium text-[#666666]">Tampilan:</span>

                <div class="flex gap-2">
                    <!-- Grid View -->
                    <div class="relative">
                        <input type="radio" name="viewMode" id="grid-view" value="grid" wire:model.live="viewMode"
                            class="absolute opacity-0 w-0 h-0">
                        <label for="grid-view" class="cursor-pointer">
                            <flux:icon icon="squares-2x2"
                                class="{{ $viewMode === 'grid' ? 'text-gray-100 bg-[#74512D]' : 'text-gray-800 bg-white' }} rounded-xl border border-[#74512D] hover:text-gray-100 hover:bg-[#74512D] transition-colors size-8" />
                        </label>
                    </div>

                    <!-- List View -->
                    <div class="relative">
                        <input type="radio" name="viewMode" id="list-view" value="list" wire:model.live="viewMode"
                            class="absolute opacity-0 w-0 h-0">
                        <label for="list-view" class="cursor-pointer">
                            <flux:icon icon="list-bullet"
                                class="{{ $viewMode === 'list' ? 'text-gray-100 bg-[#74512D]' : 'text-gray-800 bg-white' }} rounded-xl border border-[#74512D] hover:text-gray-100 hover:bg-[#74512D] transition-colors size-8" />
                        </label>
                    </div>
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
                                    {{ $batches->isNotEmpty() ? $quantity_main_total . ' ' . ($mainDetail->unit->alias ?? '') : 'Tidak Tersedia' }}
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
                                    Tgl Exp:
                                    {{ $nextBatch ? \Carbon\Carbon::parse($nextBatch->date)->translatedFormat('d M Y') : '-' }}
                                </p>
                            </div>
                            <flux:button class="w-full" variant="subtle" type="button"
                                href="{{ route('bahan-baku.edit', $material->id) }}" wire:navigate>
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
            <x-table.paginated :headers="[
                ['label' => 'Barang Persediaan', 'sortable' => true, 'sort-by' => 'name'],
                ['label' => 'Aktif', 'sortable' => true, 'sort-by' => 'is_active'],
                ['label' => 'Jumlah', 'align' => 'right'],
                ['label' => 'Expired', 'sortable' => true, 'sort-by' => 'expiry_date'],
                ['label' => 'Status', 'sortable' => true, 'sort-by' => 'status', 'align' => 'right'],
            ]" :paginator="$materials" headerBg="#3f4e4f" headerText="#f8f4e1"
                bodyBg="#fafafa" bodyText="#666666"
                emptyMessage="Belum ada barang persediaan. Tekan tombol 'Tambah Persediaan' untuk menambahkan persediaan.">
                @foreach ($materials as $material)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('bahan-baku.edit', $material->id) }}"
                                class="text-sm text-[#666666] font-medium montserrat-medium hover:underline"
                                wire:navigate>
                                {{ $material->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#666666] font-medium montserrat-medium whitespace-nowrap">
                            {{ $material->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-[#666666] font-medium montserrat-medium text-right">
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
                            <span>{{ $batches->isNotEmpty() ? $quantity_main_total : '0' }}</span>
                            <span
                                class="ml-1">{{ $batches->isNotEmpty() && $mainDetail ? $mainDetail->unit->alias : '' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#666666] font-medium montserrat-medium">
                            @php
                                // Ambil semua batch
                                $batches = $material->batches;

                                // Filter batch yang belum kadaluarsa dan urutkan dari yang paling dekat
                                $nextBatch = $batches
                                    ->filter(fn($batch) => \Carbon\Carbon::parse($batch->date)->isFuture())
                                    ->sortBy(fn($batch) => \Carbon\Carbon::parse($batch->date))
                                    ->first();
                            @endphp
                            {{ $nextBatch ? \Carbon\Carbon::parse($nextBatch->date)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-[#666666] font-medium montserrat-medium text-right">
                            {{ $material->status ?? 'Kosong' }}
                        </td>
                    </tr>
                @endforeach
            </x-table.paginated>
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
                                    wire:click='sortBy("status")'>Status Persediaan
                                    {{ $sortDirection === 'asc' && $sortField === 'status' ? '↑' : '↓' }}
                                </th>
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
                                        {{ $batches->isNotEmpty() ? $quantity_main_total . ' ' . ($mainDetail->unit->alias ?? '') : 'Tidak Tersedia' }}
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
                                        Tgl Exp:
                                        {{ $nextBatch ? \Carbon\Carbon::parse($nextBatch->date)->translatedFormat('d M Y') : '-' }}
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
