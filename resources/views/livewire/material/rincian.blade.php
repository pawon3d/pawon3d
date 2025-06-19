<div>
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('bahan-baku') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Rincian Barang Persediaan</h1>
        </div>
        <div class="flex gap-2 items-center">
            <flux:button variant="filled" wire:click="riwayatPembaruan">Riwayat Pembaruan</flux:button>
        </div>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Form ini digunakan untuk menambahkan barang ke dalam persediaan. Lengkapi informasi yang diminta,
                pastikan informasi yang dimasukan benar dan tepat. Informasi akan ditampilkan dalam sistem sehingga tim
                dapat mengolah persediaan, produksi hingga penjualan produk.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Unggah Gambar Barang</flux:label>
            <p class="text-sm text-gray-500">
                Pilih gambar barang yang ingin diunggah dan sesuai dengan nama barang yang akan ditambahkan.
            </p>

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
                <input id="dropzone-file" type="file" wire:model="image" class="hidden"
                    accept="image/jpeg, image/png, image/jpg" onchange="previewImage(this)" />

                <!-- Upload Button -->
                <flux:button variant="primary" type="button" onclick="document.getElementById('dropzone-file').click()"
                    class="w-full">
                    Pilih Gambar
                </flux:button>

                <!-- Error Message -->
                @error('image')
                    <div class="w-full p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Loading Indicator -->
                <div wire:loading wire:target="image" class="w-full p-3 text-sm text-blue-700 bg-blue-100 rounded-lg">
                    Mengupload gambar...
                </div>
            </div>
        </div>


        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Nama Barang</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan nama barang yang ingin disimpan, seperti “Gula Pasir”, “Tepung Ketan”, Telur” atau “Air Mineral
                Gelas 240ml”
            </p>
            <flux:input placeholder="Ketik nama barang" wire:model.defer="name" />
            <flux:error name="name" />
            <flux:label>Deskripsi Barang</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan deskripsi barang yang ingin disimpan, seperti penjelasan apa saja ciri khas dan kegunaan dari
                produk.
            </p>
            <flux:input placeholder="Ketik deskripsi barang" wire:model.defer="description" />
            <flux:error name="description" />
        </div>

    </div>

    <div class="w-full mt-8">
        <flux:label>Kategori Barang</flux:label>
        <p class="text-sm text-gray-500 mb-4">
            Ketik nama kategori yang ingin dikaitkan dan sesuai dengan ciri-ciri produk seperti rasa, bentuk, kegunaan,
            dan lain sebagainya.
        </p>
        <select class="js-example-basic-multiple" wire:model.live="category_ids" multiple="multiple">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Satuan Ukur Utama</flux:label>
        </div>
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <p class="text-sm text-gray-500">
                Pilih satuan ukur utama untuk menampilkan menu
                <span class="font-semibold">Persediaan Barang, </span>
                <span class="font-semibold">Modal Barang, </span>
                <span class="font-semibold">Minimum Persediaan. </span>
                Satuan dapat berupa kilogram (kg), liter (L), hingga pieces (pcs)
            </p>
        </div>

        @foreach ($material_details as $index => $detail)
            @if ($index === 0)
                <div class="w-full flex items-center justify-start gap-4 flex-row">
                    <flux:select placeholder="- Pilih Produk dari Persediaan -"
                        wire:model="material_details.{{ $index }}.unit_id"
                        wire:change="setUnit({{ $index }}, $event.target.value)">
                        @foreach ($units as $unit)
                            <flux:select.option value="{{ $unit->id }}" class="text-gray-700">{{ $unit->name }}
                                ({{ $unit->alias }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            @endif
        @endforeach

    </div>
    @if ($material_details)
        @if ($material_details[0]['unit_id'] != null && $material_details[0]['unit_id'] != '')
            <div class="w-full mt-8 flex items-center flex-col gap-4">
                <div class="w-full flex items-center justify-start gap-4 flex-row">
                    <flux:label>Persediaan Barang</flux:label>
                </div>
                <div class="w-full flex items-center justify-start gap-4 flex-row">
                    <p class="text-sm text-gray-500">
                        Tambah satuan lainya untuk mengubah satuan utama menjadi satuan lain yang lebih kecil ataupun
                        lebih
                        besar. Satuan lain digunakan untuk menentukan jumlah rinci saat menambahkan bahan ke dalam resep
                        kue.
                    </p>
                    <flux:button icon="plus" type="button" variant="primary" wire:click="addUnit">Tambah Satuan
                        Lainnya
                    </flux:button>
                </div>


                <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="text-left px-6 py-3">Satuan Ukur</th>
                                <th class="px-6 py-3 text-right">Besar Satuan</th>
                                <th class="px-6 py-3 text-right">Besar Satuan (Utama)</th>
                                <th class="text-left px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-6 py-3">
                                    <span class="text-gray-700">
                                        {{ $main_unit_name ?? '-' }} ({{ $main_unit_alias ?? '' }})
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-gray-700">1 {{ $main_unit_alias ?? '' }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-gray-700">1 {{ $main_unit_alias ?? '' }}</span>
                                </td>
                                <td></td>
                            </tr>
                            @foreach ($material_details as $index => $detail)
                                @if ($index === 0)
                                    @continue
                                @endif
                                <tr>
                                    <td class="px-6 py-3">
                                        <select
                                            class="border-0 border-b border-b-gray-300 focus:border-b-blue-500 focus:outline-none focus:ring-0 rounded-none"
                                            wire:model="material_details.{{ $index }}.unit_id"
                                            wire:change="setUnit({{ $index }}, $event.target.value)">
                                            <option value="" class="text-gray-700">- Pilih Satuan -</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}" class="text-gray-700">
                                                    {{ $unit->name }} ({{ $unit->alias }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        1 {{ $detail['unit'] ?? '' }}
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <flux:input.group class="text-right">
                                            <input type="number"
                                                class="w-full text-right border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-0 rounded-l-md"
                                                placeholder="0" min="0"
                                                wire:model.number.live="material_details.{{ $index }}.quantity" />
                                            @if ($main_unit_alias)
                                                <flux:input.group.suffix>{{ $main_unit_alias }}
                                                </flux:input.group.suffix>
                                            @endif
                                        </flux:input.group>
                                    </td>

                                    <td class="px-6 py-3">
                                        <flux:button icon="trash" type="button" variant="danger"
                                            wire:click.prevent="removeUnit({{ $index }})" />
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                        <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <td class="px-6 py-3" colspan="2">
                                    <span class="text-gray-700">Total Persediaan</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <span class="text-gray-700">
                                        {{ $supply_quantity_main }} {{ $main_unit_alias ?? '' }}
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="w-full flex flex-col gap-4 mt-8">
                <flux:label>Jumlah dan Expired Persediaan</flux:label>
                <p class="text-sm text-gray-500">
                    Belanja barang persediaan untuk mendapatkan jumlah persediaan dan tanggal expired (merah expired,
                    kuning
                    hampir expired, hijau belum expired).
                </p>
                @if (!empty($material->batches) && $material->batches->count() > 0)
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="text-left px-6 py-3">Batch</th>
                                    <th class="text-right px-6 py-3">Jumlah Persediaan</th>
                                    <th class="text-right px-6 py-3">Jumlah Persediaan (Utama)</th>
                                    <th class="text-right px-6 py-3">Tanggal Expired</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $batches = $material->batches->sortBy('date');
                                @endphp
                                @foreach ($batches as $b)
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
                                            $quantity_main_total += $quantity_main;
                                        @endphp
                                        <td class="px-6 py-3 text-right">
                                            <span class="text-gray-700">
                                                {{ $quantity_main ?? 0 }} {{ $main_unit_alias ?? '' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <div class="relative w-full">
                                                <input type="text" class="rounded-md border-gray-300"
                                                    value="{{ \Carbon\Carbon::parse($b->date)->format('d / m / Y') }}"
                                                    disabled />
                                                <span
                                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                                    <flux:icon.calendar class="w-4 h-4" />
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot
                                class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
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
                @else
                    <flux:input type="text" class="w-full" placeholder="Belum Ada Persediaan" disabled />
                @endif
            </div>

            <div>
                <div class="w-full mt-8 flex flex-col gap-4">
                    <flux:label>Minimum dan Status Persediaan</flux:label>
                    <p class="text-sm text-gray-500">
                        Masukkan nilai minimum persediaan untuk pembaruan status persediaan. Status terdiri dari
                        Tersedia
                        (lebih
                        dari minimum dikali 2), Hampir Habis (kurang dari minimum dikali 2) , dan Habis (kurang dari
                        minimum).
                        Kemudian status Expired akan diambil dari tanggal expired terdekat.
                    </p>
                </div>
                <div class="w-full flex md:flex-row flex-col gap-8">
                    <div class="md:w-1/2 mt-4 flex items-center flex-col gap-4">
                        <div class="w-full flex flex-col gap-4">
                            <flux:input.group class="w-full">
                                @if ($main_unit_alias)
                                    <flux:input.group.prefix>{{ $main_unit_alias }}</flux:input.group.prefix>
                                @endif
                                <flux:input type="number" class="w-full" wire:model.number.defer="minimum" />
                            </flux:input.group>
                        </div>
                    </div>
                    <div class="md:w-1/2 mt-4 flex items-center flex-col gap-4">
                        <div class="w-full flex flex-col gap-4">
                            <flux:input wire:model.defer="status" class="w-full" disabled />
                            <flux:error name="status" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full mt-8 flex items-center flex-col gap-4">
                <div class="w-full flex items-center justify-start gap-4 flex-row">
                    <flux:label>Modal Barang</flux:label>
                </div>
                <div class="w-full flex items-center justify-start gap-4 flex-row">
                    <p class="text-sm text-gray-500">
                        Belanja barang persediaan untuk menentukan berapa harga modal. Modal otomatis akan dihitung
                        berdasarkan
                        harga belanja.
                    </p>
                </div>


                <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="text-left px-6 py-3">Satuan Ukur</th>
                                <th class="text-left px-6 py-3">Besar Satuan</th>
                                <th class="text-left px-6 py-3">Harga Satuan</th>
                                <th class="text-left px-6 py-3">Jumlah Persediaan</th>
                                <th class="text-left px-6 py-3">Jumlah Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($material_details as $index => $detail)
                                <tr>
                                    <td class="px-6 py-3">
                                        <span class="text-gray-700">
                                            {{ $detail['unit_name'] ?? '-' }} ({{ $detail['unit'] ?? '' }})
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-gray-700">

                                            1 {{ $detail['unit'] ?? '' }}
                                            ({{ $detail['quantity'] ?? 0 }}
                                            {{ $main_unit_alias ?? '' }})
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 flex items-center flex-row relative">
                                        <span class="text-gray-700">
                                            Rp{{ number_format($detail['supply_price'], 0, ',', '.') ?? 0 }}
                                        </span>
                                    </td>
                                    @php
                                        $batch = \App\Models\MaterialBatch::where('material_id', $material->id)
                                            ->where('unit_id', $detail['unit_id'])
                                            ->first();
                                    @endphp
                                    <td class="px-6 py-3">
                                        <span class="text-gray-700">
                                            {{ $batch->batch_quantity ?? 0 }} {{ $detail['unit'] ?? '' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-gray-700">
                                            Rp{{ number_format(($detail['supply_price'] ?? 0) * ($batch->batch_quantity ?? 0), 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                        <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <td class="px-6 py-3" colspan="3">
                                    <span class="text-gray-700">Total Harga</span>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-gray-700">
                                        {{ $quantity_main_total . ' ' . ($main_unit_alias ?? '') }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-gray-700">
                                        @php
                                            $price_total = 0;
                                            foreach ($material_details as $detail) {
                                                $batch = \App\Models\MaterialBatch::where('material_id', $material->id)
                                                    ->where('unit_id', $detail['unit_id'])
                                                    ->first();
                                                if ($batch) {
                                                    $price_total +=
                                                        ($detail['supply_price'] ?? 0) * ($batch->batch_quantity ?? 0);
                                                }
                                            }
                                        @endphp
                                        {{ $price_total ? 'Rp' . number_format($price_total, 0, ',', '.') : 'Rp0' }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    @endif

    <div class="w-full flex flex-row gap-8 mt-8">
        <div class="w-1/2 flex flex-col items-center gap-4">
            <div class="w-full flex items-center justify-start gap-4 flex-row">
                <flux:label>Jual Langsung dari Persediaan</flux:label>
                <flux:switch wire:model.live="is_recipe" class="data-checked:bg-green-500"
                    :checked="$is_recipe ? true : false" />
            </div>
            <p class="text-sm text-gray-500 mb-4">
                Aktifkan opsi ini jika barang tidak termasuk dalam daftar komponen resep atau akan dijual langsung dari
                persediaan.
            </p>
        </div>
        <div class="w-1/2 flex flex-col items-center gap-4">
            <div class="w-full flex items-center justify-start gap-4 flex-row">
                <flux:label>Tampilkan Barang</flux:label>
                <flux:switch wire:model.live="is_active" class="data-checked:bg-green-500"
                    :checked="$is_active ? true : false" />
            </div>
            <p class="text-sm text-gray-500 mb-4">
                Aktifkan opsi ini jika barang ingin ditampilkan serta digunakan sebagai aktivitas belanja persediaan,
                bahan
                baku, dan produk jualan.
            </p>
        </div>
    </div>

    <div class="flex justify-end gap-4 mt-8">
        <flux:button icon="trash" type="button" variant="danger" wire:click="confirmDelete()">
        </flux:button>
        <a href="{{ route('bahan-baku') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="bookmark-square" type="button" variant="primary" wire:click.prevent="update">Simpan
        </flux:button>
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
