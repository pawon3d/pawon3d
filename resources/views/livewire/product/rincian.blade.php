<div class="space-y-8">
    <header class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex justify-between flex-wrap items-center gap-4 w-full">
            <div class="flex items-center gap-4">
                <a href="{{ route('produk') }}"
                    class="inline-flex items-center gap-2 rounded-[15px] border border-[#313131] bg-[#313131] px-6 py-2.5 text-base font-semibold text-[#F6F6F6] shadow transition hover:bg-[#252324] wire:navigate">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
                <h1 class="text-xl font-semibold text-[#666666]">Rincian Produk</h1>
            </div>
            <div class="flex gap-2 items-center">
                <flux:button variant="filled" wire:click="riwayatPembaruan">Riwayat Pembaruan</flux:button>
            </div>
        </div>
    </header>

    {{-- Info Penting Banner --}}
    <x-alert.info>
        Anda dapat mengubah atau menghapus informasi produk. Sesuaikan informasi jika terdapat perubahan, pastikan
        informasi yang dimasukan benar dan tepat. Informasi akan ditampilkan untuk dipilih dalam proses produksi,
        penyiapan inventori, dan panjualan oleh kasir.
    </x-alert.info>


    <div class="space-y-8">
        {{-- Foto & Detail Produk --}}
        <section class="overflow-hidden rounded-[15px] border border-[#FAFAFA] bg-white p-8 shadow-sm">
            <div class="flex flex-wrap gap-8">
                {{-- Foto Produk --}}
                <div class="w-[300px] space-y-4">
                    <div>
                        <p class="text-base font-medium text-[#666666]">Foto Produk</p>
                        <p class="mt-2 text-sm text-[#666666]">Pilih gambar produk yang ingin diunggah.</p>
                    </div>
                    <div class="space-y-5">
                        <div class="aspect-[4/3] overflow-hidden rounded-[15px] border border-dashed border-black">
                            @if ($previewImage)
                                <img src="{{ $previewImage }}" alt="Preview" class="h-full w-full object-cover">
                            @else
                                <div class="flex flex-col items-center justify-center p-4 text-center">
                                    <flux:icon icon="arrow-up-tray" class="w-8 h-8 mb-6 text-gray-400" />
                                    <p class="mb-2 text-lg font-semibold text-gray-600">Unggah Gambar</p>
                                    <p class="mb-2 text-xs text-gray-600 mt-4">
                                        Ukuran gambar tidak lebih dari
                                        <span class="font-semibold">2mb</span>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Pastikan gambar dalam format
                                        <span class="font-semibold">JPG </span> atau
                                        <span class="font-semibold">PNG</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                        <input type="file" wire:model="product_image" class="hidden" id="product_image"
                            accept="image/jpeg,image/png">
                        <button type="button" onclick="document.getElementById('product_image').click()"
                            class="w-full rounded-[15px] bg-[#74512D] px-6 py-2.5 text-base font-semibold text-[#F6F6F6] shadow transition hover:bg-[#5d3f23]">
                            Pilih Gambar
                        </button>
                        @error('product_image')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if (session()->has('message'))
                            <p class="text-sm text-green-600">{{ session('message') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Nama & Deskripsi Produk --}}
                <div class="flex-1 space-y-8">
                    <div class="space-y-4">
                        <div>
                            <p class="text-base font-medium text-[#666666]">Nama Produk</p>
                            <p class="mt-2 text-sm text-[#666666]">Masukkan nama produk, seperti "Bolu Pandan Loyang",
                                "Kue Apem", atau "Air Mineral Gelas 240ml".</p>
                        </div>
                        <input type="text" wire:model.defer="name" placeholder="Air Mineral Gelas 250ml"
                            class="w-full rounded-[15px] border-[1.5px] border-[#ADADAD] bg-[#FAFAFA] px-5 py-2.5 text-base text-[#666666] focus:border-[#74512D] focus:outline-none">
                        @error('name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-base font-medium text-[#666666]">Deskripsi Produk</p>
                            <p class="mt-2 text-sm text-[#666666]">Masukkan deskripsi produk, seperti apa ciri khas atau
                                daya tarik dari produk.</p>
                        </div>
                        <textarea wire:model.defer="description" rows="4" placeholder="Deskripsi produk..."
                            class="w-full rounded-[15px] border-[1.5px] border-[#ADADAD] bg-[#FAFAFA] px-5 py-2.5 text-base text-[#666666] focus:border-[#74512D] focus:outline-none"></textarea>
                        @error('description')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Kategori Produk --}}
            <div class="mt-8 space-y-4">
                <div>
                    <p class="text-base font-medium text-[#666666]">Kategori Produk</p>
                    <p class="mt-2 text-sm text-[#666666]">Pilih satu atau banyak kategori untuk produk.</p>
                </div>
                <div class="flex flex-wrap gap-2.5 px-5 py-2.5 w-full" wire:ignore>
                    <select class="js-example-basic-multiple w-full" wire:model.live="category_ids" multiple>
                        @foreach ($categoryOptions as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- Resep Produk Section --}}
        <section class="overflow-hidden rounded-[15px] border border-[#FAFAFA] bg-white p-8 shadow-sm">
            <div class="flex items-center justify-between gap-6 pb-6">
                <div class="flex-1">
                    <p class="text-base font-medium text-[#666666]">Resep Produk</p>
                </div>
                <div class="flex items-center gap-3">
                    <label class="relative inline-block h-[25px] w-[45px] cursor-pointer">
                        <input type="checkbox" wire:model.live="is_recipe" class="peer sr-only">
                        <span
                            class="absolute inset-0 rounded-full transition {{ $is_recipe ? 'bg-[#56C568]' : 'bg-[#525252]' }}"></span>
                        <span
                            class="absolute top-1/2 h-[21px] w-[21px] -translate-y-1/2 rounded-full bg-[#FAFAFA] transition-all {{ $is_recipe ? 'right-[2px]' : 'left-[2px]' }}"></span>
                    </label>
                </div>
            </div>
            <div class="flex items-center justify-between gap-6 border-b border-dashed border-slate-200 pb-6">
                <div class="flex-1">
                    <p class="mt-2 text-sm text-[#666666]">
                        Aktifkan <span class="font-bold">Resep Produk</span> jika produk memiliki resep. Jika tidak,
                        maka pilih barang dari persediaan untuk dijual kembali, seperti Air Mineral.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if ($is_recipe)
                        <flux:button type="button" wire:click="showUnit" variant="primary" icon="shapes">
                            Satuan Ukur
                        </flux:button>
                        <flux:button type="button" wire:click="addComposition" variant="primary" icon="plus">
                            Tambah Bahan Baku
                        </flux:button>
                    @endif
                </div>
            </div>

            @if ($is_recipe)
                {{-- Recipe Mode --}}
                <div class="mt-6 space-y-6">
                    {{-- Komposisi Table --}}
                    <div class="overflow-hidden rounded-[15px] border border-[#FAFAFA]">
                        <table class="w-full text-sm">
                            <thead class="bg-[#3F4E4F] text-sm font-bold uppercase text-[#F8F4E1]">
                                <tr>
                                    <th class="px-6 py-5 text-left">Bahan Baku</th>
                                    <th class="px-6 py-5 text-right">Jumlah yang Digunakan</th>
                                    <th class="px-6 py-5 text-left">Satuan Ukur Utama</th>
                                    <th class="w-[200px] px-6 py-5 text-right">Jumlah Harga</th>
                                    <th class="w-[72px] px-6 py-5"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white text-[#666666]">
                                @foreach ($product_compositions as $index => $composition)
                                    @php
                                        $material = $recipeMaterials->firstWhere('id', $composition['material_id']);
                                        $units = $material?->material_details
                                            ->map(fn($detail) => $detail->unit)
                                            ->filter();
                                    @endphp
                                    <tr class="border-b border-[#D4D4D4]">
                                        <td class="px-6 py-4">
                                            <select
                                                wire:model.live="product_compositions.{{ $index }}.material_id"
                                                wire:change="setMaterial({{ $index }}, $event.target.value)"
                                                class="w-full appearance-none rounded-[5px] border border-[#ADADAD] bg-[#FAFAFA] px-3 py-2 text-sm font-medium text-[#666666]">
                                                <option value="">Pilih Barang Persediaan</option>
                                                @foreach ($recipeMaterials as $mat)
                                                    <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number" min="0" step="0.01"
                                                wire:model.live="product_compositions.{{ $index }}.material_quantity"
                                                class="w-full rounded-[5px] border border-[#ADADAD] bg-[#FAFAFA] px-3 py-1.5 text-right text-sm font-medium text-[#666666]">
                                        </td>
                                        <td class="px-6 py-4">
                                            <select wire:model.live="product_compositions.{{ $index }}.unit_id"
                                                wire:change="setUnit({{ $index }}, $event.target.value)"
                                                class="w-full appearance-none rounded-[5px] border border-[#ADADAD] bg-[#FAFAFA] px-3 py-2 text-sm font-medium text-[#666666]">
                                                <option value="">Pilih Satuan Ukur</option>
                                                @if ($units)
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}
                                                            ({{ $unit->alias }})
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold text-[#666666]">
                                            Rp{{ number_format(($composition['material_price'] ?? 0) * ($composition['material_quantity'] ?? 0), 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button"
                                                wire:click="removeComposition({{ $index }})"
                                                class="text-[#666666] hover:text-red-600">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-[#EAEAEA] text-sm font-bold uppercase text-[#666666]">
                                <tr>
                                    <td colspan="3" class="px-6 py-5 text-left">Total</td>
                                    <td class="px-6 py-5 text-right">
                                        Rp{{ number_format(collect($product_compositions)->sum(fn($c) => ($c['material_price'] ?? 0) * ($c['material_quantity'] ?? 0)), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Biaya Produksi --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-dashed border-slate-200 pb-4">
                            <div class="flex-1">
                                <p class="text-base font-medium text-[#666666]">Biaya Produksi</p>
                                <p class="mt-2 text-sm text-[#666666]">
                                    Aktifkan opsi <span class="font-bold">Biaya Produksi</span> jika produk diolah
                                    menggunakan biaya diluar bahan persediaan seperti biaya tenaga kerja, listrik, gas,
                                    dan air.
                                </p>
                            </div>
                            <label class="relative inline-block h-[25px] w-[45px] cursor-pointer">
                                <input type="checkbox" wire:model.live="is_other" class="peer sr-only">
                                <span
                                    class="absolute inset-0 rounded-full transition {{ $is_other ? 'bg-[#56C568]' : 'bg-[#525252]' }}"></span>
                                <span
                                    class="absolute top-1/2 h-[21px] w-[21px] -translate-y-1/2 rounded-full bg-[#FAFAFA] transition-all {{ $is_other ? 'right-[2px]' : 'left-[2px]' }}"></span>
                            </label>
                        </div>

                        @if ($is_other)
                            <div class="flex justify-end">
                                <button type="button" wire:click="addOther"
                                    class="flex items-center gap-2 rounded-[15px] bg-[#74512D] px-6 py-2.5 text-base font-semibold text-[#F6F6F6] shadow">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Biaya Produksi
                                </button>
                            </div>

                            <div class="overflow-hidden rounded-[15px] border border-[#FAFAFA]">
                                <table class="w-full text-sm">
                                    <thead class="bg-[#3F4E4F] text-sm font-bold uppercase text-[#F8F4E1]">
                                        <tr>
                                            <th class="px-6 py-5 text-left">Jenis Biaya</th>
                                            <th class="w-[240px] px-6 py-5 text-right">Jumlah Harga</th>
                                            <th class="w-[72px] px-6 py-5"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white text-[#666666]">
                                        @foreach ($other_costs as $index => $other)
                                            <tr class="border-b border-[#D4D4D4]">
                                                <td class="px-6 py-4">
                                                    <select
                                                        wire:model.defer="other_costs.{{ $index }}.type_cost_id"
                                                        class="w-full rounded-[5px] border border-[#ADADAD] bg-[#FAFAFA] px-3 py-2 text-sm font-medium text-[#666666]">
                                                        <option value="">Pilih Jenis Biaya</option>
                                                        @foreach ($typeCosts as $typeCost)
                                                            <option value="{{ $typeCost->id }}">{{ $typeCost->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <input type="number" min="0"
                                                        wire:model.live="other_costs.{{ $index }}.price"
                                                        placeholder="Rp0"
                                                        class="w-full rounded-[5px] border border-[#ADADAD] bg-[#FAFAFA] px-3 py-1.5 text-right text-sm font-medium text-[#666666]">
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <button type="button"
                                                        wire:click="removeOther({{ $index }})"
                                                        class="text-[#666666] hover:text-red-600">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-[#EAEAEA] text-sm font-bold uppercase text-[#666666]">
                                        <tr>
                                            <td class="px-6 py-5 text-left">Total</td>
                                            <td class="px-6 py-5 text-right">
                                                Rp{{ number_format(collect($other_costs)->sum('price'), 0, ',', '.') }}
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Expired Produk --}}
                    <div class="space-y-4">
                        <div>
                            <p class="text-base font-medium text-[#666666]">Expired produk</p>
                            <p class="mt-2 text-sm text-[#666666]">
                                Masukkan expired produk pasca produksi baik ketika disimpan di suhu ruangan (20–25°C),
                                dingin (4–8°C), dan beku (-18°C atau lebih rendah).
                            </p>
                        </div>
                        <div class="overflow-hidden rounded-[15px] border border-[#FAFAFA]">
                            <table class="w-full text-sm">
                                <thead class="bg-[#3F4E4F] text-sm font-bold uppercase text-[#F8F4E1]">
                                    <tr>
                                        <th class="px-6 py-5 text-left">Suhu Penyimpanan</th>
                                        <th class="w-[240px] px-6 py-5 text-right">Hari</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white text-[#666666]">
                                    <tr class="border-b border-[#D4D4D4]">
                                        <td class="px-6 py-4 font-medium">Suhu Ruangan <span
                                                class="font-normal">(20–25°C)</span></td>
                                        <td class="px-6 py-4">
                                            <input type="number" min="0" wire:model.defer="suhu_ruangan"
                                                class="w-full rounded-[5px] border border-[#ADADAD] bg-[#FAFAFA] px-3 py-1.5 text-right text-sm font-medium text-[#666666]">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-[#D4D4D4]">
                                        <td class="px-6 py-4 font-medium">Suhu Dingin <span
                                                class="font-normal">(4–8°C)</span></td>
                                        <td class="px-6 py-4">
                                            <input type="number" min="0" wire:model.defer="suhu_dingin"
                                                class="w-full rounded-[5px] border border-[#ADADAD] bg-[#FAFAFA] px-3 py-1.5 text-right text-sm font-medium text-[#666666]">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-[#D4D4D4]">
                                        <td class="px-6 py-4 font-medium">Suhu Beku <span class="font-normal">(-18°C
                                                atau lebih rendah)</span></td>
                                        <td class="px-6 py-4">
                                            <input type="number" min="0" wire:model.defer="suhu_beku"
                                                class="w-full rounded-[5px] border border-[#ADADAD] bg-[#FAFAFA] px-3 py-1.5 text-right text-sm font-medium text-[#666666]">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Jumlah Produk dari Satu Resep --}}
                    <div class="space-y-4">
                        <div>
                            <p class="text-base font-medium text-[#666666]">Jumlah Produk yang dihasilkan dari Satu
                                Resep</p>
                            <p class="mt-2 text-sm text-[#666666]">
                                Masukkan jumlah produk dari satu resep. Masukkan jumlah "1" jika resep hanya
                                menghasilkan 1 dan masukkan jumlah lebih banyak apabila hasil resep lebih dari 1.
                            </p>
                        </div>
                        <input type="number" min="1" wire:model.live="pcs" placeholder="1"
                            class="w-full rounded-[15px] border-[1.5px] border-[#ADADAD] bg-[#FAFAFA] px-5 py-2.5 text-base text-[#666666]">
                        @error('pcs')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @else
                {{-- Non-Recipe Mode --}}
                <div class="mt-6 space-y-6">
                    <div class="rounded-[15px] border border-[#D4D4D4] bg-[#FAFAFA] p-6">
                        <p class="text-base font-medium text-[#666666]">Produk dari Persediaan</p>
                        <p class="mt-2 text-sm text-[#666666]">Pilih barang jadi yang siap dijual tanpa proses
                            produksi.</p>
                        @foreach ($product_compositions as $index => $composition)
                            <select wire:model.live="product_compositions.{{ $index }}.material_id"
                                wire:change="setSoloMaterial({{ $index }}, $event.target.value)"
                                class="mt-4 w-full appearance-none rounded-[15px] border-[1.5px] border-[#ADADAD] bg-[#FAFAFA] px-5 py-2.5 text-base text-[#666666]">
                                <option value="">Pilih produk</option>
                                @foreach ($readyMaterials as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>

                    {{-- Jumlah & Masa Simpan --}}
                    <div class="rounded-[15px] border border-[#FAFAFA] bg-white p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-base font-medium text-[#666666]">Jumlah dan Expired Persediaan</p>
                                <p class="mt-2 text-sm text-[#666666]">Belanja persediaan untuk mendapatkan jumlah
                                    persediaan dan tanggal expired (merah expired, kuning hampir expired, hijau belum
                                    expired).</p>
                            </div>
                            @if ($soloInventory)
                                <span
                                    class="rounded-full bg-indigo-50 px-4 py-1 text-sm font-semibold text-indigo-600">
                                    {{ $soloInventory['material']->name }}
                                </span>
                            @endif
                        </div>
                        @if ($soloInventory)
                            <div class="mt-4 overflow-hidden rounded-[15px] border border-[#FAFAFA]">
                                <table class="w-full text-sm">
                                    <thead class="bg-[#3F4E4F] text-sm font-bold uppercase text-[#F8F4E1]">
                                        <tr>
                                            <th class="px-6 py-5 text-left">Batch</th>
                                            <th class="px-6 py-5 text-right">Jumlah Persediaan</th>
                                            <th class="px-6 py-5 text-right">Jumlah Persediaan (Utama)</th>
                                            <th class="px-6 py-5 text-right">Tanggal Expired</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white text-[#666666]">
                                        @forelse ($soloInventory['batches'] as $batch)
                                            <tr class="border-b border-[#D4D4D4]">
                                                <td class="px-6 py-4 font-medium">{{ $batch['number'] }}</td>
                                                <td class="px-6 py-4 text-right">{{ $batch['quantity'] }}
                                                    {{ $batch['unit_alias'] }}</td>
                                                <td class="px-6 py-4 text-right">{{ $batch['main_quantity'] }}
                                                    {{ $soloInventory['main_unit_alias'] }}</td>
                                                <td class="px-6 py-4 text-right">
                                                    {{ \Carbon\Carbon::parse($batch['date'])->format('d M Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 text-center text-[#959595]">Tidak
                                                    ada data batch</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($soloInventory['batches']->isNotEmpty())
                                        <tfoot class="bg-[#EAEAEA] text-sm font-bold uppercase text-[#666666]">
                                            <tr>
                                                <td class="px-6 py-5 text-left">Total</td>
                                                <td class="px-6 py-5 text-right"></td>
                                                <td class="px-6 py-5 text-right">{{ $soloInventory['total_main'] }}
                                                    {{ $soloInventory['main_unit_alias'] }}</td>
                                                <td class="px-6 py-5"></td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        @else
                            <p
                                class="mt-4 rounded-[15px] border border-dashed border-[#D4D4D4] bg-[#FAFAFA] px-5 py-4 text-sm text-[#666666]">
                                Pilih produk persediaan terlebih dahulu untuk melihat detail batch dan kadaluarsa.
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        {{-- Modal & Metode Penjualan --}}
        <section
            class="flex flex-wrap gap-20 overflow-hidden rounded-[15px] border border-[#FAFAFA] bg-white p-8 shadow-sm">
            {{-- Modal dan Harga --}}
            <div class="flex-1 space-y-4">
                <div>
                    <p class="text-base font-medium text-[#666666]">Modal dan Harga Jual Produk</p>
                    <p class="mt-2 text-sm text-[#666666]">Modal ini berasal dari modal barang yang dipilih atau total
                        pengeluaran produksi.</p>
                </div>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span
                            class="text-sm font-medium text-[#666666]">{{ $pcs > 1 ? 'Modal/Resep' : 'Modal' }}</span>
                        <span
                            class="text-base font-medium text-[#666666]">Rp{{ number_format($capital, 0, ',', '.') }}</span>
                    </div>
                    @if ($pcs > 1)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-[#666666]">Harga Jual/Resep</span>
                            <input type="number" min="0" wire:model.live="price" placeholder="Rp0"
                                class="w-1/2 rounded-[15px] border-[1.5px] border-[#ADADAD] bg-[#FAFAFA] px-5 py-2.5 text-right text-base font-medium text-[#666666]">
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-[#666666]">Harga Jual</span>
                            <input type="number" min="0" wire:model.live="price" placeholder="Rp0"
                                class="w-1/2 rounded-[15px] border-[1.5px] border-[#ADADAD] bg-[#FAFAFA] px-5 py-2.5 text-right text-base font-medium text-[#666666]">
                        </div>
                    @endif
                    @error('price')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Metode Penjualan --}}
            <div class="w-[350px] space-y-4">
                <div>
                    <p class="text-base font-medium text-[#666666]">Metode Penjualan</p>
                    <p class="mt-2 text-sm text-[#666666]">Pilih satu atau banyak metode penjualan.</p>
                </div>
                <div class="space-y-4">
                    @foreach (['siap-beli' => 'Siap Saji', 'pesanan-reguler' => 'Pesanan Reguler', 'pesanan-kotak' => 'Pesanan Kotak'] as $value => $label)
                        <label class="flex items-center gap-4">
                            <input type="checkbox" value="{{ $value }}" wire:model.live="selectedMethods"
                                class="h-[30px] w-[30px] rounded-full border-2 border-[#525252] text-[#56C568] focus:ring-[#56C568]">
                            <span class="text-base text-[#666666]">{{ $label }}</span>
                        </label>
                    @endforeach
                    @error('selectedMethods')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Rekomendasi & Tampilan --}}
        <section
            class="flex flex-wrap gap-8 overflow-hidden rounded-[15px] border border-[#FAFAFA] bg-white p-8 shadow-sm">
            <div class="flex-1 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <p class="text-base font-medium text-[#666666]">Rekomendasikan Produk</p>
                        <p class="mt-2 text-sm text-[#666666]">Aktifkan opsi ini jika produk ingin rekomendasikan
                            produk.</p>
                    </div>
                    <label class="relative inline-block h-[25px] w-[45px] cursor-pointer">
                        <input type="checkbox" wire:model.live="is_recommended" class="peer sr-only">
                        <span
                            class="absolute inset-0 rounded-full transition {{ $is_recommended ? 'bg-[#56C568]' : 'bg-[#525252]' }}"></span>
                        <span
                            class="absolute top-1/2 h-[21px] w-[21px] -translate-y-1/2 rounded-full bg-[#FAFAFA] transition-all {{ $is_recommended ? 'right-[2px]' : 'left-[2px]' }}"></span>
                    </label>
                </div>
            </div>

            <div class="flex-1 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex-1">
                        <p class="text-base font-medium text-[#666666]">Tampilan Produk</p>
                        <p class="mt-2 text-sm text-[#666666]">Aktifkan opsi ini jika produk ingin ditampilkan dan
                            dapat dijual.</p>
                    </div>
                    <label class="relative inline-block h-[25px] w-[45px] cursor-pointer">
                        <input type="checkbox" wire:model.live="is_active" class="peer sr-only">
                        <span
                            class="absolute inset-0 rounded-full transition {{ $is_active ? 'bg-[#56C568]' : 'bg-[#525252]' }}"></span>
                        <span
                            class="absolute top-1/2 h-[21px] w-[21px] -translate-y-1/2 rounded-full bg-[#FAFAFA] transition-all {{ $is_active ? 'right-[2px]' : 'left-[2px]' }}"></span>
                    </label>
                </div>
            </div>
        </section>

        {{-- Action Buttons --}}
        <div class="flex justify-between items-center gap-3">
            <div class="flex justify-start">
                <flux:button icon="trash" variant="danger" wire:click="confirmDelete">
                    Hapus Produk
                </flux:button>
            </div>
            <div class="flex justify-end gap-2.5">
                <a href="{{ route('produk') }}"
                    class="inline-flex items-center gap-2 rounded-[15px] bg-[#C4C4C4] px-6 py-2.5 text-base font-semibold text-[#333333] shadow transition hover:bg-[#b0b0b0]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
                <button type="button" wire:click="update" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-[15px] bg-[#3F4E4F] px-6 py-2.5 text-base font-semibold text-[#F8F4E1] shadow transition hover:bg-[#2f3d3e] cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Simpan
                    <div wire:loading wire:target="update" class="animate-spin">
                        <flux:icon.loading />
                    </div>
                </button>
            </div>
        </div>
    </div>


    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Produk</flux:heading>
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


    @script
        <script type="text/javascript">
            document.addEventListener('livewire:initialized', function() {
                function loadCategories() {
                    $('.js-example-basic-multiple').select2({
                        placeholder: 'Pilih kategori produk',
                        width: '100%'
                    }).on('change', function() {
                        $wire.set('category_ids', $(this).val());
                    });
                }

                loadCategories();

                Livewire.hook('morphed', () => {
                    loadCategories();
                });
            });
        </script>
    @endscript
</div>
