<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('produk') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Produk</h1>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">Form ini digunakan untuk menambahkan produk ke dalam metode penjualan
                terpilih. Unggah gambar, isi nama dan deskripsi produk, atur tampilan atau keterlihatan produk, aktifkan
                rekomendasi jika perlu, dan tambahkan resep produk untuk mencatat komposisi bahan sebagai acuan produksi
                atau pilih produk dari persediaan.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Unggah Gambar Produk</flux:label>
            <p class="text-sm text-gray-500">Pilih gambar produk yang ingin diunggah dan sesuai dengan nama produk yang
                akan ditambahkan.</p>

            <div class="flex flex-col items-center w-full max-w-md mx-auto space-y-4">
                <!-- Dropzone Area -->
                <div class="relative w-full h-48 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                    wire:ignore
                    ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                    ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');" ondrop="handleDrop(event)"
                    id="dropzone-container">

                    <label for="dropzone-file" class="w-full h-full cursor-pointer flex items-center justify-center">
                        <div id="preview-container" class="w-full h-full">
                            @if ($previewImage)
                                <!-- Image Preview -->
                                <img src="{{ $previewImage }}" alt="Preview" class="object-cover w-full h-full"
                                    id="image-preview" />
                            @else
                                <!-- Default Content -->
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
                    </label>
                </div>

                <!-- Hidden File Input -->
                <input id="dropzone-file" type="file" wire:model="product_image" class="hidden"
                    accept="image/jpeg, image/png, image/jpg" onchange="previewImage(this)" />

                <!-- Upload Button -->
                <flux:button variant="primary" type="button" onclick="document.getElementById('dropzone-file').click()"
                    class="w-full">
                    Pilih Gambar
                </flux:button>

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


        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Nama Produk</flux:label>
            <p class="text-sm text-gray-500">Masukkan nama produk yang ingin dijual, seperti “Kue Lapis Legit 24x24”,
                "Kue Apem 30pcs", “Risol 1pcs” atau “Air Mineral”.</p>
            <flux:input placeholder="Masukkan nama produk" wire:model.defer="name" />
            <flux:error name="name" />
            <flux:label>Deskripsi Produk</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan deskripsi produk yang ingin dijual, seperti penjelasan produk seperti apa dan apa saja ciri
                khas atau daya tarik dari produk.
            </p>
            <flux:input placeholder="Ketik deskripsi produk" wire:model.defer="description" />
            <flux:error name="description" />
        </div>

    </div>

    <div class="w-full mt-8">
        <flux:label>Kategori Produk</flux:label>
        <p class="text-sm text-gray-500 mb-4">Ketik nama kategori yang ingin dikaitkan dan sesuai dengan ciri-ciri
            produk
            seperti
            rasa, bentuk, cara masak, dan lain sebagainya.</p>
        <select class="js-example-basic-multiple" wire:model.live="category_ids" multiple="multiple">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Resep Produk</flux:label>
            <flux:switch wire:model.live="is_recipe" class="data-checked:bg-green-500" />
        </div>
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <p class="text-sm text-gray-500">
                Aktifkan opsi
                <span class="font-semibold">Resep Produk</span>
                jika produk diolah menggunakan bahan persediaan. Jika
                <span class="font-semibold">Tidak Ada Resep</span>
                maka pilih produk yang akan dijual dari persediaan tanpa perlu diolah atau beli jadi dari orang lain
                seperti
                “Air Mineral” dan “Risol”.
            </p>
            @if ($is_recipe)
                <flux:button icon="plus" type="button" variant="primary" wire:click="addComposition">Tambah Bahan
                </flux:button>
            @endif
        </div>
        @if ($is_recipe)

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="text-left px-6 py-3">Bahan Baku</th>
                            <th class="text-right px-6 py-3">Jumlah</th>
                            <th class="text-right px-6 py-3">Satuan</th>
                            <th class="text-right px-6 py-3">Jumlah Harga</th>
                            <th class="text-right px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product_compositions as $index => $composition)
                            <tr>
                                <td class="px-6 py-3">
                                    <select class="w-full border-0 focus:outline-none focus:ring-0 rounded-none"
                                        wire:model="product_compositions.{{ $index }}.material_id"
                                        wire:change="setMaterial({{ $index }}, $event.target.value)">
                                        <option value="" class="text-gray-700">- Pilih Bahan Baku -</option>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->id }}" class="text-gray-700">
                                                {{ $material->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <input type="number" placeholder="0" min="0"
                                        wire:model.number.live="product_compositions.{{ $index }}.material_quantity"
                                        class="w-full border-gray-300 focus:border-gray-300 focus:outline-none focus:ring-0 rounded text-right" />
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <select class="w-full border-0 focus:outline-none focus:ring-0 rounded-none"
                                        wire:model="product_compositions.{{ $index }}.unit_id"
                                        wire:change="setUnit({{ $index }}, $event.target.value)">
                                        @php
                                            $material = $materials->firstWhere('id', $composition['material_id']);
                                            $units = $material?->material_details
                                                ->map(function ($detail) {
                                                    return $detail->unit;
                                                })
                                                ->filter();
                                        @endphp
                                        <option value="" class="text-gray-700">- Pilih Satuan Ukur -</option>
                                        @foreach ($units ?? [] as $unit)
                                            <option value="{{ $unit->id }}" class="text-gray-700">
                                                {{ $unit->name }} ({{ $unit->alias }})
                                            </option>
                                        @endforeach

                                    </select>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-gray-700">
                                        Rp.{{ number_format($composition['material_price'] * $composition['material_quantity'], 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <flux:button icon="trash" type="button" variant="danger"
                                        wire:click.prevent="removeComposition({{ $index }})" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <td class="px-6 py-3" colspan="3">
                                <span class="text-gray-700">Total Harga</span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-gray-700">
                                    {{-- jumlahkan total harga dari $composition['material_price'] *
                                $composition['material_quantity'] --}}
                                    Rp.{{ number_format(
                                        array_sum(
                                            array_map(function ($composition) {
                                                return $composition['material_price'] * $composition['material_quantity'];
                                            }, $product_compositions),
                                        ),
                                        0,
                                        ',',
                                        '.',
                                    ) }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Biaya lainnya --}}

            <div class="w-full flex items-center justify-start gap-4 flex-row">
                <flux:label>Biaya Lainnya</flux:label>
                <flux:switch wire:model.live="is_other" class="data-checked:bg-green-500" />
            </div>
            <div class="w-full flex items-center justify-between gap-4 flex-row">
                <p class="text-sm text-gray-500">
                    Aktifkan opsi
                    <span class="font-semibold">Biaya Lainnya</span>
                    jika produk diolah menggunakan biaya diluar bahan persediaan seperti biaya tenaga manusia, listrik,
                    gas,
                    dan air.
                </p>
                @if ($is_other)
                    <flux:button icon="plus" type="button" variant="primary" wire:click="addOther">Tambah Biaya
                        Lainnya
                    </flux:button>
                @endif
            </div>

            @if ($is_other)
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="text-left px-6 py-3">Biaya Lain</th>
                                <th class="text-right px-6 py-3">Total Harga</th>
                                <th class="text-right px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($other_costs as $index => $other)
                                <tr>
                                    <td class="px-6 py-3">
                                        <input type="text" placeholder="Ketik Biaya Lainnya..."
                                            class="w-full border-0 focus:border-0 focus:outline-none focus:ring-0 rounded-none"
                                            wire:model.defer="other_costs.{{ $index }}.name" />
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <input type="number" placeholder="0" min="0"
                                            wire:model.number.live="other_costs.{{ $index }}.price"
                                            class="border-gray-300 focus:border-gray-300 focus:outline-none focus:ring-0 rounded text-right" />
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <flux:button icon="trash" type="button" variant="danger"
                                            wire:click.prevent="removeOther({{ $index }})" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <td class="px-6 py-3" colspan="1">
                                    <span class="text-gray-700">Total Harga</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-gray-700">
                                        Rp.{{ number_format(
                                            array_sum(
                                                array_map(function ($other) {
                                                    return $other['price'];
                                                }, $other_costs),
                                            ),
                                            0,
                                            ',',
                                            '.',
                                        ) }}
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            {{-- Satu Resep untuk Banyak Buah --}}
            <div class="w-full flex items-center justify-start gap-4 flex-row">
                <flux:label>Jumlah Produk yang dihasilkan dari Satu Resep</flux:label>
            </div>
            <div class="w-full flex items-center justify-start gap-4 flex-row">
                <p class="text-sm text-gray-500">
                    Masukkan jumlah produk dari satu resep. Masukkan jumlah “1” apabila produk dijual dalam bentuk
                    loyang
                    atau unit besar (Bolu Pandan atau Brownies Kukus) sedangkan masukkan jumlah lebih dari satu apabila
                    hasil dari satu resep dijual dalam bentuk unit kecil atau perpotong (Kue Apem atau Kue Pedamaran).
                </p>
            </div>
            <div class="w-full flex items-center justify-start gap-4 flex-row">
                <flux:input placeholder="0" min="0" wire:model.number.live="pcs" type="number" />
            </div>

            <div class="w-full flex items-start justify-start gap-4 flex-col">
                <flux:label>Jumlah Waktu Expired Produk</flux:label>
                <p class="text-sm text-gray-500">
                    Masukkan jarak waktu expired produk pasca produksi baik ketika disimpan di suhu ruangan (20–25°C),
                    dingin (4–8°C), dan beku (-18°C atau lebih rendah).
                </p>
            </div>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="text-left px-6 py-3">Suhu Penyimpanan</th>
                            <th class="text-right px-6 py-3">Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-6 py-3">
                                <span class="text-gray-700">
                                    Suhu Ruangan (20–25°C)
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <input type="number" placeholder="0" min="0"
                                    wire:model.number="suhu_ruangan"
                                    class="border-gray-300 focus:border-gray-300 focus:outline-none focus:ring-0 rounded text-right" />
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3">
                                <span class="text-gray-700">
                                    Suhu Dingin (4–8°C)
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <input type="number" placeholder="0" min="0" wire:model.number="suhu_dingin"
                                    class="border-gray-300 focus:border-gray-300 focus:outline-none focus:ring-0 rounded text-right" />
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3">
                                <span class="text-gray-700">
                                    Suhu Beku (-18°C atau lebih rendah)
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <input type="number" placeholder="0" min="0" wire:model.number="suhu_beku"
                                    class="border-gray-300 focus:border-gray-300 focus:outline-none focus:ring-0 rounded text-right" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            @php
                $materials = \App\Models\Material::where('is_recipe', true)->get();
            @endphp
            @foreach ($product_compositions as $index => $composition)
                <div class="w-full flex items-center justify-start gap-4 flex-row">
                    <flux:select placeholder="- Pilih Produk dari Persediaan -"
                        wire:model="product_compositions.{{ $index }}.material_id"
                        wire:change="setSoloMaterial({{ $index }}, $event.target.value)">
                        @foreach ($materials as $material)
                            <flux:select.option value="{{ $material->id }}" class="text-gray-700">
                                {{ $material->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            @endforeach
            @if ($product_compositions[0]['material_id'] != '' && $product_compositions[0]['material_id'] != null)
                <div class="w-full flex items-start justify-start gap-4 flex-col">
                    <flux:label>Jumlah dan Expired Persediaan</flux:label>
                    <p class="text-sm text-gray-500">
                        Belanja barang persediaan untuk mendapatkan jumlah persediaan dan tanggal expired (merah
                        expired, kuning
                        hampir expired, hijau belum expired).
                    </p>
                </div>
                @php
                    $material = \App\Models\Material::find($product_compositions[0]['material_id']);
                @endphp
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="text-left px-6 py-3">Batch</th>
                                <th class="text-right px-6 py-3">Jumlah Persediaan</th>
                                <th class="text-right px-6 py-3">Jumlah Persediaan (Utama)</th>
                                <th class="text-right px-6 py-3">Tanggal Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($material->batches as $b)
                                <tr>
                                    <td class="px-6 py-3">
                                        <span class="text-gray-700">
                                            {{ $b->batch_number ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-gray-700">
                                            {{ $b->batch_quantity ?? 0 }} {{ $b->unit->alias ?? '' }}
                                        </span>
                                    </td>
                                    @php
                                        $detail = \App\Models\MaterialDetail::where('material_id', $material->id)
                                            ->where('unit_id', $b->unit_id)
                                            ->first();
                                        $quantity_main = $b->batch_quantity * $detail->quantity;
                                        $quantity_main_total = 0;
                                        $main_unit_alias = $detail->where('is_main', true)->first()?->unit->alias ?? '';
                                        $quantity_main_total += $quantity_main;
                                    @endphp
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-gray-700">
                                            {{ $quantity_main ?? 0 }} {{ $main_unit_alias ?? '' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-gray-700">
                                            {{ $b->date ? \Carbon\Carbon::parse($b->date)->format('d / m / Y') : '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <td class="px-6 py-3" colspan="2">
                                    <span class="text-gray-700">Total</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-gray-700">
                                        {{ $quantity_main_total . ' ' . ($main_unit_alias ?? '') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        @endif
    </div>

    <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
        <div class="md:w-1/2 mt-8 flex items-center flex-col gap-4">
            <div class="w-full">
                <flux:label>Modal dan Harga Jual Produk</flux:label>
                <p class="text-sm text-gray-500">Modal otomatis akan dihitung berdasarkan total bahan baku dan atau
                    biaya
                    lainnya. Tetapkan harga jual produk dengan pertimbangan modal yang telah dikeluarkan. Harga jual
                    tidak boleh
                    lebih rendah dari modal.</p>
            </div>

            <div class="w-full flex flex-col gap-8 mt-4">
                <div class="flex flex-row justify-between items-center gap-4">
                    <flux:label class="w-3/4">Modal {{ $pcs > 1 ? 'Utuh' : '' }}</flux:label>
                    <p class="w-1/4 text-right text-sm p-2">Rp.{{ $capital }}</p>
                </div>
                @if ($pcs > 1)
                    <div class="flex flex-row justify-between items-center gap-4">
                        <flux:label class="w-3/4">Modal Per Buah</flux:label>
                        <p class="w-1/4 text-right text-sm p-2">Rp.{{ number_format($pcs_capital, 2, ',', '.') }}</p>
                    </div>
                @endif
                <div class="flex flex-col gap-4">
                    <div class="flex flex-row justify-between items-center gap-4">
                        <flux:label class="w-3/4">Harga Jual {{ $pcs > 1 ? '/ Unit' : '' }}</flux:label>
                        <input placeholder="Rp.0" wire:model.live="price"
                            class="w-1/4 text-right text-sm bg-gray-50 rounded-lg p-2 border border-gray-500" />
                    </div>
                    <flux:error name="price" class="flex justify-end" />
                </div>
            </div>
        </div>
        <div class="md:w-1/2 mt-8 md:ml-8 flex items-center flex-col gap-4">
            <div class="w-full md:ml-8">
                <flux:label>Metode Penjualan</flux:label>
                <p class="text-sm text-gray-500 mb-4">
                    Pilih satu atau banyak metode penjualan.
                </p>
                <flux:checkbox.group wire:model.live="selectedMethods">
                    <flux:checkbox label="Siap Saji" value="siap-beli" />
                    <flux:checkbox label="Pesanan Reguler" value="pesanan-reguler" />
                    <flux:checkbox label="Pesanan Kotak" value="pesanan-kotak" />
                </flux:checkbox.group>
            </div>
        </div>
    </div>

    <div class="w-full mt-8 flex items-center justify-start gap-4 flex-row">
        <div class="w-full flex flex-col gap-4 mt-4">
            <div class="w-full flex items-center justify-start gap-4 flex-row">
                <p class="text-lg font-semibold">Tampilkan Produk</p>
                <flux:switch wire:model.live="is_active" class="data-checked:bg-green-500" />
            </div>
            <p class="text-sm text-gray-500 w-full">
                Aktifkan opsi ini jika produk ingin ditampilkan dan dapat beli atau dipesan.
            </p>
        </div>
        <div class="w-full flex flex-col gap-4 mt-4">
            <div class="w-full flex items-center justify-start gap-4 flex-row">
                <p class="text-lg font-semibold">Rekomendasi Produk</p>
                <flux:switch wire:model.live="is_recommended" class="data-checked:bg-green-500" />
            </div>
            <p class="text-sm text-gray-500 w-full">
                Aktifkan opsi ini jika produk ingin direkomendasikan untuk dibeli atau dipesan.
            </p>
        </div>
    </div>

    <div class="flex justify-end mt-16">
        <a href="{{ route('produk') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="bookmark-square" type="button" variant="primary" wire:click.prevent="store">Simpan
        </flux:button>
    </div>

    @script
        <script type="text/javascript">
            document.addEventListener('livewire:initialized', function() {
                function loadJavascript() {
                    $('.js-example-basic-multiple').select2({
                        placeholder: "Pilih kategori produk",
                        width: '100%',
                    }).on("change", function() {
                        $wire.set("category_ids", $(this).val());
                    });
                }
                loadJavascript();

                Livewire.hook("morphed", () => {
                    loadJavascript();
                })
            });
        </script>
    @endscript

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
