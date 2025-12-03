<div>
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('bahan-baku') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">{{ $material_id ? 'Rincian' : 'Tambah' }} Barang Persediaan</h1>
        </div>
        @if ($material_id)
            <div class="flex gap-2 items-center">
                <flux:button variant="filled" wire:click="riwayatPembaruan">Riwayat Pembaruan</flux:button>
            </div>
        @endif
    </div>
    <x-alert.info>
        Form ini digunakan untuk {{ $material_id ? 'mengubah' : 'menambahkan' }} barang ke dalam persediaan. Lengkapi
        informasi yang diminta,
        pastikan informasi yang dimasukan benar dan tepat. Informasi akan ditampilkan dalam sistem sehingga tim dapat
        mengolah persediaan, produksi hingga penjualan produk.
    </x-alert.info>

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
            <div class="md:w-1/2 flex flex-col gap-4">
                <h3 class="text-lg font-medium text-[#666666]">Foto Persediaan</h3>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Pilih gambar persediaan yang ingin diunggah.
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
                    <input id="dropzone-file" type="file" wire:model="image" class="hidden"
                        accept="image/jpeg, image/png, image/jpg" onchange="previewImage(this)" />

                    <!-- Upload Button -->
                    <button type="button" onclick="document.getElementById('dropzone-file').click()"
                        class="w-full bg-[#74512D] text-[#F8F4E1] font-semibold text-base px-6 py-2.5 rounded-2xl shadow-sm hover:bg-[#654520] transition-colors">
                        Pilih Gambar
                    </button>

                    <!-- Error Message -->
                    @error('image')
                        <div class="w-full p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- Loading Indicator -->
                    <div wire:loading wire:target="image"
                        class="w-full p-3 text-sm text-blue-700 bg-blue-100 rounded-lg">
                        Mengupload gambar...
                    </div>
                </div>
            </div>


            <div class="md:w-1/2 flex flex-col gap-8">
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Nama Persediaan</h3>
                    <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                        Masukkan nama persediaan yang ingin disimpan, seperti "Gula Pasir", "Tepung Ketan", Telur" atau
                        "Air Mineral Gelas 240ml"
                    </p>
                    <input type="text" wire:model.defer="name" placeholder="Tepung Terigu Protein Sedang"
                        class="w-full px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl text-base font-normal text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors" />
                    <flux:error name="name" />
                </div>
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Deskripsi Barang</h3>
                    <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                        Masukkan deskripsi barang yang ingin disimpan, seperti penjelasan apa saja ciri khas dan
                        kegunaan dari produk.
                    </p>
                    <input type="text" wire:model.defer="description"
                        placeholder="Tepung terigu adalah jenis tepung yang terbuat dari biji gandum..."
                        class="w-full px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl text-base font-normal text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors" />
                    <flux:error name="description" />
                </div>
            </div>
        </div>
        <div class="w-full mt-10 flex flex-col gap-4">
            <h3 class="text-lg font-medium text-[#666666]">Kategori Barang</h3>
            <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                Masukkan nama kategori yang ingin dikaitkan dan sesuai dengan ciri-ciri produk seperti rasa, bentuk,
                kegunaan, dan lain sebagainya.
            </p>
            <x-form.multi-select :options="$categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray()" :selected="$category_ids ?? []" name="category_ids"
                placeholder="Pilih kategori produk" />
        </div>
    </div>

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex flex-col gap-4">
            <h3 class="text-lg font-medium text-[#666666]">Satuan Ukur Utama</h3>
            <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                Pilih satuan ukur utama untuk menampilkan menu <span class="font-bold">Tambah Satuan Lainnya</span>,
                <span class="font-bold">Jumlah dan Expired Persediaan, Modal Barang</span>, <span
                    class="font-bold">Status dan Minimal Persediaan.</span> Satuan dapat berupa kilogram (kg), liter
                (L), hingga pieces (pcs).
            </p>

            @foreach ($material_details as $index => $detail)
                @if ($index === 0)
                    <select wire:model="material_details.{{ $index }}.unit_id"
                        wire:change="setUnit({{ $index }}, $event.target.value)"
                        class="w-full px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl text-base font-normal text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors appearance-none pr-12">
                        <option value="">- Pilih Satuan -</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->alias }})</option>
                        @endforeach
                    </select>
                @endif
            @endforeach
        </div>
    </div>

    @if ($material_details)
        @if ($material_details[0]['unit_id'] != null && $material_details[0]['unit_id'] != '')
            <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
                <div class="w-full flex flex-col gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Satuan Lainnya</h3>
                    <div class="w-full flex items-center justify-between gap-4">
                        <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed max-w-[780px]">
                            Tambah satuan lainya untuk mengubah satuan utama menjadi satuan lain yang lebih kecil atau
                            besar. Satuan lain digunakan untuk menentukan jumlah rinci bahan baku yang akan digunakan
                            dalam
                            sebuah resep kue.
                        </p>
                        <button type="button" wire:click="addUnit"
                            class="bg-[#74512D] text-[#F8F4E1] font-semibold text-base px-6 py-2.5 rounded-2xl shadow-sm flex items-center gap-1.5 hover:bg-[#654520] transition-colors whitespace-nowrap">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Tambah Satuan Lainnya
                        </button>
                    </div>

                    <div class="w-full overflow-x-auto rounded-2xl shadow-sm">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#3F4E4F] h-[60px]">
                                    <th class="text-left px-6 font-bold text-sm text-[#F8F4E1] min-w-[235px]">Satuan
                                        Ukur
                                    </th>
                                    <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Besar
                                        Satuan
                                    </th>
                                    <th
                                        class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px] max-w-[240px]">
                                        Besar Satuan<br>(Utama)</th>
                                    <th class="w-[72px]"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                    <td class="px-6 text-[#666666] font-medium">{{ $main_unit_name ?? '-' }}
                                        ({{ $main_unit_alias ?? '' }})</td>
                                    <td class="px-6 text-right text-[#666666] font-medium">1
                                        {{ $main_unit_alias ?? '' }}
                                    </td>
                                    <td class="px-6 text-right text-[#666666] font-medium">1
                                        {{ $main_unit_alias ?? '' }}
                                    </td>
                                    <td></td>
                                </tr>
                                @foreach ($material_details as $index => $detail)
                                    @if ($index === 0)
                                        @continue
                                    @endif
                                    <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                        <td class="px-6">
                                            <div class="flex items-center gap-2">
                                                <select wire:model="material_details.{{ $index }}.unit_id"
                                                    wire:change="setUnit({{ $index }}, $event.target.value)"
                                                    class="flex-1 bg-transparent border-0 border-b border-[#D4D4D4] focus:border-[#74512D] focus:outline-none focus:ring-0 text-[#666666] font-medium appearance-none pr-8">
                                                    <option value="">- Pilih Satuan -</option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}
                                                            ({{ $unit->alias }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if (!empty($unitsWithAutoConversion[$index]))
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Auto
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 text-right text-[#666666] font-medium">1
                                            {{ $detail['unit'] ?? '' }}</td>
                                        <td class="px-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <input type="number"
                                                    wire:model.number.live="material_details.{{ $index }}.quantity"
                                                    placeholder="0" min="0"
                                                    @if (!empty($unitsWithAutoConversion[$index])) readonly @endif
                                                    class="w-full max-w-[190px] px-2.5 py-1.5 border border-[#ADADAD] rounded-md text-right text-[#666666] font-medium focus:outline-none focus:border-[#74512D] {{ !empty($unitsWithAutoConversion[$index]) ? 'bg-green-50 cursor-not-allowed' : 'bg-[#FAFAFA]' }}" />
                                                @if ($main_unit_alias)
                                                    <span
                                                        class="text-[#959595] font-medium">{{ $main_unit_alias }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 text-center">
                                            <button type="button"
                                                wire:click.prevent="removeUnit({{ $index }})"
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
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
                <div class="w-full flex flex-col gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Jumlah dan Expired Persediaan</h3>
                    <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                        Belanja persediaan untuk mendapatkan jumlah persediaan dan tanggal expired (merah expired,
                        kuning
                        hampir expired, hijau belum expired).
                    </p>
                    @if (!empty($material->batches) && $material->batches->count() > 0)
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead class="font-bold text-sm text-[#F8F4E1]">
                                    <tr class="bg-[#3F4E4F] h-[60px]">
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
                                                $detail = \App\Models\MaterialDetail::where(
                                                    'material_id',
                                                    $material->id,
                                                )
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
                        <input type="text" value="Belum Ada Persediaan" disabled
                            class="w-full px-5 py-2.5 bg-[#EAEAEA] border border-[#D4D4D4] rounded-2xl text-base font-medium text-[#666666] cursor-not-allowed" />
                    @endif
                </div>
            </div>
            <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
                <div class="w-full flex flex-col gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Status dan Minimal Persediaan</h3>
                    <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                        Masukkan nilai minimal persediaan untuk pembaruan status persediaan. Status terdiri dari <span
                            class="font-medium">Tersedia</span> (lebih dari minimal dikali 2), <span
                            class="font-medium">Hampir Habis</span> (kurang dari minimal dikali 2), dan <span
                            class="font-medium">Habis</span> (kurang dari minimal). Kemudian status <span
                            class="font-medium">Expired</span> akan diambil dari tanggal expired terdekat.
                    </p>
                    <div class="flex gap-4">
                        <input type="text" wire:model.defer="status" disabled value="Hampir Habis"
                            class="flex-1 px-5 py-2.5 bg-[#EAEAEA] border border-[#D4D4D4] rounded-2xl text-base font-medium text-[#666666] cursor-not-allowed" />
                        <input type="number" wire:model.number.defer="minimum" placeholder="5 kg"
                            class="flex-1 px-5 py-2.5 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl text-base font-medium text-[#666666] focus:outline-none focus:border-[#74512D] transition-colors" />
                    </div>
                    <flux:error name="minimum" />
                </div>
            </div>

            <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
                <div class="w-full flex flex-col gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Modal Barang</h3>
                    <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                        Belanja persediaan untuk menentukan berapa harga modal. Modal otomatis akan dihitung berdasarkan
                        harga belanja.
                    </p>

                    <div class="w-full overflow-x-auto rounded-2xl shadow-sm">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#3F4E4F] h-[60px]">
                                    <th class="text-left px-6 font-bold text-sm text-[#F8F4E1] min-w-[235px]">Satuan
                                        Ukur
                                    </th>
                                    <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Besar
                                        Satuan
                                    </th>
                                    <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Harga
                                        Satuan
                                    </th>
                                    <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Jumlah
                                        Persediaan</th>
                                    <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Jumlah
                                        Harga
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($material_details as $index => $detail)
                                    <tr class="h-[60px] border-b border-[#D4D4D4] bg-[#FAFAFA]">
                                        <td class="px-6 text-[#666666] font-medium">{{ $detail['unit_name'] ?? '-' }}
                                            ({{ $detail['unit'] ?? '' }})
                                        </td>
                                        <td class="px-6 text-right text-[#666666] font-medium">1
                                            {{ $detail['unit'] ?? '' }} ({{ $detail['quantity'] ?? 0 }}
                                            {{ $main_unit_alias ?? '' }})</td>
                                        <td class="px-6 text-right text-[#666666] font-medium">
                                            Rp{{ number_format($detail['supply_price'], 0, ',', '.') ?? 0 }}</td>
                                        <td class="px-6 text-right text-[#666666] font-medium">
                                            {{ $detail['supply_quantity'] }} {{ $detail['unit'] ?? '' }}</td>
                                        <td class="px-6 text-right text-[#666666] font-medium">
                                            Rp{{ number_format(($detail['supply_price'] ?? 0) * ($detail['supply_quantity'] ?? 0), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="h-[60px] bg-[#EAEAEA] border-b border-[#D4D4D4]">
                                    <td colspan="4" class="px-6 font-bold text-sm text-[#666666]">Total Harga</td>
                                    <td class="px-6 text-right font-bold text-sm text-[#666666]">
                                        {{ $supply_price_total ? 'Rp' . number_format($supply_price_total, 0, ',', '.') : 'Rp0' }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex flex-wrap gap-8 items-center justify-between">
            <div class="flex-1 min-w-[300px] max-w-[445px] flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Tampilan Barang</h3>
                    <flux:switch wire:model.live="is_active" class="data-checked:bg-green-500"
                        :checked="$is_active ? true : false" />
                </div>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Aktifkan opsi ini jika barang ingin ditampilkan, dijual, atau dijadikan bahan produksi.
                </p>
            </div>
            <div class="flex-1 min-w-[300px] max-w-[445px] flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Jual Langsung dari Persediaan</h3>
                    <flux:switch wire:model.live="is_recipe" class="data-checked:bg-green-500"
                        :checked="$is_recipe ? true : false" />
                </div>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Aktifkan opsi ini jika barang dijual langsung dari persediaan.
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-between flex-row items-center">
        @if ($material_id)
            <flux:button icon="trash" type="button" variant="danger" wire:click="confirmDelete()">
                Hapus Persediaan
            </flux:button>
        @else
            <div></div>
        @endif
        <div class="flex justify-end gap-4 mt-8">
            <a href="{{ route('bahan-baku') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
                <flux:icon.x-mark class="w-4 h-4 mr-2" />
                Batal
            </a>
            <flux:button icon="bookmark-square" type="button" variant="secondary" wire:click.prevent="save">
                Simpan
            </flux:button>
        </div>
    </div>

    @if ($material_id)
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
