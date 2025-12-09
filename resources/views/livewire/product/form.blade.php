<div>
    <style>
        /* Custom checkbox styling for green rounded checkboxes */
        input[type="checkbox"].custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ADADAD;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
            position: relative;
            background-color: white;
            transition: all 0.2s ease;
        }

        input[type="checkbox"].custom-checkbox:checked {
            background-color: #22c55e;
            border-color: #1c422a;
        }

        input[type="checkbox"].custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        input[type="checkbox"].custom-checkbox:focus {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }
    </style>

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('produk') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">{{ $product_id ? 'Rincian' : 'Tambah' }} Produk</h1>
        </div>
        @if ($product_id)
            <div class="flex gap-2 items-center">
                <flux:button variant="secondary" wire:click="riwayatPembaruan">Riwayat Pembaruan</flux:button>
            </div>
        @endif
    </div>
    <x-alert.info>
        Form ini digunakan untuk {{ $product_id ? 'mengubah' : 'menambahkan' }} produk. Lengkapi
        informasi yang diminta,
        pastikan informasi yang dimasukan benar dan tepat. Informasi akan ditampilkan untuk dipilih dalam proses
        produksi, penyiapan inventori, dan panjualan oleh kasir.
    </x-alert.info>

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
            <div class="md:w-1/2 flex flex-col gap-4">
                <h3 class="text-lg font-medium text-[#666666]">Foto Produk</h3>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Pilih gambar produk yang ingin diunggah.
                </p>

                <div class="flex flex-col items-center w-full max-w-[300px] space-y-5">
                    <!-- Dropzone Area -->
                    <div class="relative w-full h-[170px] border-2 border-dashed border-black rounded-2xl bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                        wire:ignore
                        ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                        ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');"
                        ondrop="handleDrop(event)" id="dropzone-container">

                        <label for="dropzone-file"
                            class="w-full h-full cursor-pointer flex items-center justify-center">
                            <div id="preview-container" class="w-full h-full">
                                @if ($previewImage)
                                    <!-- Image Preview -->
                                    <img src="{{ $previewImage }}" alt="Preview" class="object-cover w-full h-full"
                                        id="image-preview" />
                                @else
                                    <!-- Default Content -->
                                    <div class="flex flex-col items-center justify-center p-4 text-center">
                                        <flux:icon icon="arrow-up-tray" class="w-8 h-8 mb-4 text-gray-400" />
                                        <p class="mb-2 text-base font-semibold text-gray-600">Unggah Gambar</p>
                                        <p class="mb-1 text-xs text-gray-600 mt-4">
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
                        </label>
                    </div>

                    <!-- Hidden File Input -->
                    <input id="dropzone-file" type="file" wire:model="product_image" class="hidden"
                        accept="image/jpeg, image/png, image/jpg" onchange="previewImage(this)" />

                    <!-- Upload Button -->
                    <button type="button" onclick="document.getElementById('dropzone-file').click()"
                        class="w-full bg-[#74512D] text-[#F8F4E1] font-semibold text-base px-6 py-2.5 rounded-2xl shadow-sm hover:bg-[#654520] transition-colors">
                        Pilih Gambar
                    </button>

                    <!-- Error Message -->
                    @error('product_image')
                        <div class="w-full p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- Loading Indicator -->
                    <div wire:loading wire:target="product_image"
                        class="w-full p-3 text-sm text-blue-700 bg-blue-100 rounded-lg">
                        Mengupload gambar...
                    </div>
                </div>
            </div>


            <div class="md:w-1/2 flex flex-col gap-8">
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Nama Produk</h3>
                    <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                        Masukkan nama produk yang ingin disimpan, seperti "Bolu Pandan Loyang", "Kue Apem", atau "Air
                        Mineral Gelas 240ml"
                    </p>
                    <input type="text" wire:model.defer="name" placeholder="Bolu Pandan Loyang"
                        class="w-full px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl text-base font-normal text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors" />
                    <flux:error name="name" />
                </div>
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Deskripsi Produk</h3>
                    <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                        Masukkan deskripsi produk yang ingin disimpan, seperti penjelasan apa saja ciri khas dan
                        kegunaan dari produk.
                    </p>
                    <textarea wire:model.defer="description" rows="4"
                        placeholder="Bolu pandan adalah kue bolu dengan aroma dan rasa pandan..."
                        class="w-full px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl text-base font-normal text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors"></textarea>
                    <flux:error name="description" />
                </div>
            </div>
        </div>
        <div class="w-full mt-10 flex flex-col gap-4">
            <h3 class="text-lg font-medium text-[#666666]">Kategori Produk</h3>
            <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                Masukkan nama kategori yang ingin dikaitkan dan sesuai dengan ciri-ciri produk seperti rasa, bentuk,
                kegunaan, dan lain sebagainya.
            </p>
            <x-form.multi-select :options="$categoryOptions->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray()" :selected="$category_ids ?? []" name="category_ids"
                placeholder="Pilih kategori produk" />
        </div>
    </div>

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex flex-col gap-4">
            <div class="flex items-center justify-between pb-4 border-b border-dashed border-slate-200">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-[#666666]">Resep Produk</h3>
                    <p class="mt-2 text-sm font-normal text-[#666666] text-justify leading-relaxed">
                        Aktifkan <span class="font-bold">Resep Produk</span> jika produk memiliki resep. Jika tidak,
                        maka pilih barang dari persediaan untuk dijual kembali, seperti Air Mineral.
                    </p>
                </div>
                <flux:switch wire:model.live="is_recipe" class="data-checked:bg-green-500"
                    :checked="$is_recipe ? true : false" />
            </div>

            @if ($is_recipe)
                {{-- Recipe Mode: Product Compositions --}}
                <div class="flex justify-end gap-4">
                    <flux:button type="button" wire:click="showUnit" variant="primary" icon="shapes">
                        Satuan Ukur
                    </flux:button>
                    <flux:button type="button" wire:click="addComposition" variant="primary" icon="plus">
                        Tambah Bahan Baku
                    </flux:button>
                </div>

                <div class="w-full overflow-x-auto rounded-2xl shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-[#3F4E4F] h-[60px]">
                                <th class="text-left px-6 font-bold text-sm text-[#F8F4E1] min-w-[235px]">Bahan Baku
                                </th>
                                <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Jumlah yang
                                    Digunakan
                                </th>
                                <th class="text-left px-6 font-bold text-sm text-[#F8F4E1] min-w-[200px]">Satuan Ukur
                                </th>
                                <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Jumlah
                                    Harga
                                </th>
                                <th class="w-[72px]"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product_compositions as $index => $composition)
                                @php
                                    $material = $recipeMaterials->firstWhere('id', $composition['material_id']);
                                    $units = $material?->material_details->map(fn($detail) => $detail->unit)->filter();
                                @endphp
                                <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                    <td class="px-6">
                                        <div class="flex items-center gap-2">
                                            <select
                                                wire:model.live="product_compositions.{{ $index }}.material_id"
                                                wire:change="setMaterial({{ $index }}, $event.target.value)"
                                                class="flex-1 bg-transparent border-0 border-b border-[#D4D4D4] focus:border-[#74512D] focus:outline-none focus:ring-0 text-[#666666] font-medium appearance-none pr-8">
                                                <option value="">- Pilih Bahan Baku -</option>
                                                @foreach ($recipeMaterials as $mat)
                                                    <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td class="px-6 text-right">
                                        <input type="number" min="0" step="0.01"
                                            wire:model.live="product_compositions.{{ $index }}.material_quantity"
                                            placeholder="0"
                                            class="w-full max-w-[190px] px-2.5 py-1.5 bg-[#FAFAFA] border border-[#ADADAD] rounded-md text-right text-[#666666] font-medium focus:outline-none focus:border-[#74512D]" />
                                    </td>
                                    <td class="px-6">
                                        <select wire:model.live="product_compositions.{{ $index }}.unit_id"
                                            wire:change="setUnit({{ $index }}, $event.target.value)"
                                            class="w-full bg-transparent border-0 border-b border-[#D4D4D4] focus:border-[#74512D] focus:outline-none focus:ring-0 text-[#666666] font-medium appearance-none pr-8">
                                            <option value="">- Pilih Satuan -</option>
                                            @if ($units)
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}
                                                        ({{ $unit->alias }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>
                                    <td class="px-6 text-right text-[#666666] font-medium">
                                        Rp{{ number_format(($composition['material_price'] ?? 0) * ($composition['material_quantity'] ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 text-center">
                                        <button type="button"
                                            wire:click.prevent="removeComposition({{ $index }})"
                                            class="inline-flex items-center justify-center w-[22px] h-[22px] text-[#666666] hover:text-red-600 transition-colors">
                                            <svg class="w-3 h-4" fill="currentColor" viewBox="0 0 12 16">
                                                <path
                                                    d="M11 2H8.5L7.5 1H4.5L3.5 2H1V4H11V2ZM2 14C2 15.1 2.9 16 4 16H8C9.1 16 10 15.1 10 14V5H2V14Z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="h-[60px] bg-[#EAEAEA] border-b border-[#D4D4D4]">
                                <td colspan="3" class="px-6 font-bold text-sm text-[#666666]">Total Harga Bahan
                                    Baku
                                </td>
                                <td class="px-6 text-right font-bold text-sm text-[#666666]">
                                    Rp{{ number_format(collect($product_compositions)->sum(fn($c) => ($c['material_price'] ?? 0) * ($c['material_quantity'] ?? 0)), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                {{-- Non-Recipe Mode: Ready Material Selection --}}
                <div class="pt-4 space-y-4">
                    <p class="text-sm font-normal text-[#666666]">
                        Pilih barang jadi yang siap dijual tanpa proses produksi.
                    </p>
                    @foreach ($product_compositions as $index => $composition)
                        <select wire:model.live="product_compositions.{{ $index }}.material_id"
                            wire:change="setSoloMaterial({{ $index }}, $event.target.value)"
                            class="w-full px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl text-base font-normal text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors appearance-none pr-12">
                            <option value="">- Pilih Barang Persediaan -</option>
                            @foreach ($readyMaterials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                    @endforeach

                    @if ($soloInventory)
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-[#666666] mb-4">Jumlah dan Expired Persediaan</h3>
                            <p class="text-sm font-normal text-[#666666] mb-4">
                                Belanja persediaan untuk mendapatkan jumlah persediaan dan tanggal expired (merah
                                expired, kuning
                                hampir expired, hijau belum expired).
                            </p>
                            <div class="w-full overflow-x-auto rounded-2xl shadow-sm">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-[#3F4E4F] h-[60px]">
                                            <th class="text-left px-6 font-bold text-sm text-[#F8F4E1]">Batch</th>
                                            <th class="text-right px-6 font-bold text-sm text-[#F8F4E1]">Jumlah
                                                Persediaan</th>
                                            <th class="text-right px-6 font-bold text-sm text-[#F8F4E1]">Jumlah
                                                Persediaan (Utama)</th>
                                            <th class="text-right px-6 font-bold text-sm text-[#F8F4E1]">Tanggal
                                                Expired</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($soloInventory['batches'] as $batch)
                                            <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                                <td class="px-6 text-[#666666] font-medium">{{ $batch['number'] }}
                                                </td>
                                                <td class="px-6 text-right text-[#666666] font-medium">
                                                    {{ $batch['quantity'] }}
                                                    {{ $batch['unit_alias'] }}</td>
                                                <td class="px-6 text-right text-[#666666] font-medium">
                                                    {{ $batch['main_quantity'] }}
                                                    {{ $soloInventory['main_unit_alias'] }}</td>
                                                <td class="px-6 text-right text-[#666666] font-medium">
                                                    {{ \Carbon\Carbon::parse($batch['date'])->format('d  M  Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4"
                                                    class="px-6 py-4 text-center text-[#959595] font-medium">Tidak ada
                                                    data batch</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($soloInventory['batches']->isNotEmpty())
                                        <tfoot>
                                            <tr class="h-[60px] bg-[#EAEAEA]">
                                                <td colspan="2" class="px-6 font-bold text-sm text-[#666666]">Total
                                                </td>
                                                <td class="px-6 text-right font-bold text-sm text-[#666666]">
                                                    {{ $soloInventory['total_main'] }}
                                                    {{ $soloInventory['main_unit_alias'] }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if ($is_recipe)
        <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
            <div class="w-full flex flex-col gap-4">
                <div class="flex items-center justify-between pb-4 border-b border-dashed border-slate-200">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-[#666666]">Biaya Produksi</h3>
                        <p class="mt-2 text-sm font-normal text-[#666666] text-justify leading-relaxed">
                            Aktifkan opsi <span class="font-bold">Biaya Produksi</span> jika produk diolah
                            menggunakan biaya diluar bahan persediaan seperti biaya tenaga kerja, listrik,
                            gas, dan air.
                        </p>
                    </div>
                    <flux:switch wire:model.live="is_other" class="data-checked:bg-green-500"
                        :checked="$is_other ? true : false" />
                </div>

                @if ($is_other)
                    <div class="flex justify-end gap-4">
                        <flux:button variant="primary" icon="bolt" href="{{ route('jenis-biaya') }}"
                            wire:navigate>
                            Jenis Biaya Produksi
                        </flux:button>
                        <flux:button type="button" wire:click="addOther" variant="primary" icon="plus">
                            Tambah Biaya Produksi
                        </flux:button>
                    </div>

                    <div class="w-full overflow-x-auto rounded-2xl shadow-sm">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#3F4E4F] h-[60px]">
                                    <th class="text-left px-6 font-bold text-sm text-[#F8F4E1] min-w-[235px]">Jenis
                                        Biaya
                                    </th>
                                    <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Jumlah
                                        Harga
                                    </th>
                                    <th class="w-[72px]"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($other_costs as $index => $other)
                                    <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                        <td class="px-6">
                                            <div class="flex items-center gap-2">
                                                <select
                                                    wire:model.defer="other_costs.{{ $index }}.type_cost_id"
                                                    class="flex-1 bg-transparent border-0 border-b border-[#D4D4D4] focus:border-[#74512D] focus:outline-none focus:ring-0 text-[#666666] font-medium appearance-none pr-8">
                                                    <option value="">- Pilih Jenis Biaya -</option>
                                                    @foreach ($typeCosts as $typeCost)
                                                        <option value="{{ $typeCost->id }}">{{ $typeCost->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td class="px-6 text-right">
                                            <input type="number" min="0"
                                                wire:model.live="other_costs.{{ $index }}.price"
                                                placeholder="0"
                                                class="w-full max-w-[190px] px-2.5 py-1.5 bg-[#FAFAFA] border border-[#ADADAD] rounded-md text-right text-[#666666] font-medium focus:outline-none focus:border-[#74512D]" />
                                        </td>
                                        <td class="px-6 text-center">
                                            <button type="button"
                                                wire:click.prevent="removeOther({{ $index }})"
                                                class="inline-flex items-center justify-center w-[22px] h-[22px] text-[#666666] hover:text-red-600 transition-colors">
                                                <svg class="w-3 h-4" fill="currentColor" viewBox="0 0 12 16">
                                                    <path
                                                        d="M11 2H8.5L7.5 1H4.5L3.5 2H1V4H11V2ZM2 14C2 15.1 2.9 16 4 16H8C9.1 16 10 15.1 10 14V5H2V14Z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="h-[60px] bg-[#EAEAEA] border-b border-[#D4D4D4]">
                                    <td class="px-6 font-bold text-sm text-[#666666]">Total Biaya Produksi</td>
                                    <td class="px-6 text-right font-bold text-sm text-[#666666]">
                                        Rp{{ number_format(collect($other_costs)->sum('price'), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
            <div class="w-full flex flex-col gap-4">
                <h3 class="text-lg font-medium text-[#666666]">Jumlah Produk yang Dihasilkan dari Satu Resep</h3>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Masukkan jumlah produk dari satu resep. Masukkan jumlah "1" jika resep hanya
                    menghasilkan 1 dan masukkan jumlah lebih banyak apabila hasil resep lebih dari 1.
                </p>
                <input type="number" min="1" wire:model.live="pcs" placeholder="1"
                    class="w-full px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl text-base font-medium text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors" />
                <flux:error name="pcs" />
            </div>
        </div>

        <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
            <div class="w-full flex flex-col gap-4">
                <h3 class="text-lg font-medium text-[#666666]">Expired Produk</h3>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Masukkan expired produk pasca produksi baik ketika disimpan di suhu ruangan (20–25°C),
                    dingin (4–8°C), dan beku (-18°C atau lebih rendah).
                </p>
                <div class="w-full overflow-x-auto rounded-2xl shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-[#3F4E4F] h-[60px]">
                                <th class="text-left px-6 font-bold text-sm text-[#F8F4E1]">Suhu Penyimpanan</th>
                                <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] w-[200px]">Hari</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                <td class="px-6 text-[#666666] font-medium">Suhu Ruangan <span
                                        class="font-normal">(20–25°C)</span></td>
                                <td class="px-6 text-right">
                                    <input type="number" min="0" wire:model.defer="suhu_ruangan"
                                        placeholder="0"
                                        class="w-full max-w-[190px] px-2.5 py-1.5 bg-[#FAFAFA] border border-[#ADADAD] rounded-md text-right text-[#666666] font-medium focus:outline-none focus:border-[#74512D]" />
                                </td>
                            </tr>
                            <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                <td class="px-6 text-[#666666] font-medium">Suhu Dingin <span
                                        class="font-normal">(4–8°C)</span></td>
                                <td class="px-6 text-right">
                                    <input type="number" min="0" wire:model.defer="suhu_dingin"
                                        placeholder="0"
                                        class="w-full max-w-[190px] px-2.5 py-1.5 bg-[#FAFAFA] border border-[#ADADAD] rounded-md text-right text-[#666666] font-medium focus:outline-none focus:border-[#74512D]" />
                                </td>
                            </tr>
                            <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                <td class="px-6 text-[#666666] font-medium">Suhu Beku <span class="font-normal">(-18°C
                                        atau lebih rendah)</span></td>
                                <td class="px-6 text-right">
                                    <input type="number" min="0" wire:model.defer="suhu_beku"
                                        placeholder="0"
                                        class="w-full max-w-[190px] px-2.5 py-1.5 bg-[#FAFAFA] border border-[#ADADAD] rounded-md text-right text-[#666666] font-medium focus:outline-none focus:border-[#74512D]" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex md:flex-row flex-col gap-8">
            {{-- Modal Section - Left --}}
            <div class="md:w-1/2 flex flex-col gap-4">
                <h3 class="text-lg font-medium text-[#666666]">Modal dan Harga Jual Produk</h3>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Modal ini berasal dari modal barang yang dipilih atau total pengeluaran produksi. Tentukan harga
                    jual
                    yang sesuai.
                </p>
                <div class="w-full overflow-x-auto">
                    <table class="w-full text-sm">
                        <tbody class="bg-[#FAFAFA]">
                            @if ($pcs > 1)
                                <tr class="h-[60px]">
                                    <td class="px-6 text-[#666666] font-medium">Modal per Resep</td>
                                    <td class="px-6 text-right text-[#666666] font-bold">
                                        Rp{{ number_format($capital, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="h-[60px]">
                                    <td class="px-6 text-[#666666] font-medium">Modal per PCS</td>
                                    <td class="px-6 text-right text-[#666666] font-bold">
                                        Rp{{ number_format($pcs_capital, 0, ',', '.') }}</td>
                                </tr>
                            @else
                                <tr class="h-[60px]">
                                    <td class="px-6 text-[#666666] font-medium">Total Modal</td>
                                    <td class="px-6 text-right text-[#666666] font-bold">
                                        Rp{{ number_format($capital, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="h-[60px]">
                                <td class="px-6 text-[#666666] font-medium">Harga Jual</td>
                                <td class="px-6 text-right">
                                    <input type="number" min="0" wire:model.live="price" placeholder="0"
                                        class="w-full max-w-[300px] px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-xl text-right text-base font-medium text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <flux:error name="price" />
            </div>

            {{-- Metode Penjualan Section - Right --}}
            <div class="md:w-1/2 flex flex-col gap-4">
                <h3 class="text-lg font-medium text-[#666666]">Metode Penjualan</h3>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Pilih satu atau banyak metode penjualan untuk produk.
                </p>
                <div class="flex flex-col gap-4">
                    @foreach (['pesanan-reguler' => 'Pesanan Reguler', 'pesanan-kotak' => 'Pesanan Kotak', 'siap-beli' => 'Siap Saji'] as $value => $label)
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" value="{{ $value }}" wire:model.live="selectedMethods"
                                class="text-[#56C568] focus:ring-[#56C568] rounded-full border-accent" />
                            <span class="text-base text-[#666666]">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <flux:error name="selectedMethods" />
            </div>
        </div>
    </div>

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex flex-wrap gap-8 items-center justify-between">
            <div class="flex-1 min-w-[300px] max-w-[445px] flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Tampilan Produk</h3>
                    <flux:switch wire:model.live="is_active" class="data-checked:bg-green-500"
                        :checked="$is_active ? true : false" />
                </div>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Aktifkan opsi ini jika produk ingin ditampilkan dan dapat dijual.
                </p>
            </div>
            <div class="flex-1 min-w-[300px] max-w-[445px] flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Rekomendasikan Produk</h3>
                    <flux:switch wire:model.live="is_recommended" class="data-checked:bg-green-500"
                        :checked="$is_recommended ? true : false" />
                </div>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Aktifkan opsi ini jika produk ingin direkomendasikan kepada pelanggan.
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-between flex-row items-center">
        @if ($product_id)
            <flux:button icon="trash" type="button" variant="danger" wire:click="confirmDelete()">
                Hapus Produk
            </flux:button>
        @else
            <div></div>
        @endif
        <div class="flex justify-end gap-4 mt-8">
            <a href="{{ route('produk') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
                <flux:icon.x-mark class="w-4 h-4 mr-2" />
                Batal
            </a>
            <flux:button icon="bookmark-square" type="button" variant="secondary" wire:click.prevent="save">
                {{ $product_id ? 'Perbarui' : 'Simpan' }}
            </flux:button>
        </div>
    </div>

    @if ($product_id)
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
    @endif

    <script>
        function handleDrop(event) {
            event.preventDefault();
            const container = event.currentTarget;
            container.classList.remove('border-blue-500', 'bg-gray-100');

            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('dropzone-file');
                input.files = files;
                previewImage(input);
                input.dispatchEvent(new Event('change'));
            }
        }

        function previewImage(input) {
            const previewContainer = document.getElementById('preview-container');
            const defaultContent = previewContainer.querySelector('.flex-col');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Update preview image
                    let previewImg = document.getElementById('image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'image-preview';
                        previewImg.className = 'object-cover w-full h-full';
                        previewContainer.appendChild(previewImg);
                    }
                    previewImg.src = e.target.result;

                    // Sembunyikan konten default
                    if (defaultContent) defaultContent.style.display = 'none';
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</div>
