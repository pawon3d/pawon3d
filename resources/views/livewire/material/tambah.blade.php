<div>
    <style>
        /* Custom toggle switch styles */
        input[type="checkbox"]:checked~div {
            background-color: #56C568;
        }

        input[type="checkbox"]:checked~div>div {
            transform: translateX(20px);
        }
    </style>

    <div class="mb-4 flex items-center gap-4">
        <a href="{{ route('bahan-baku') }}"
            class="px-6 py-2.5 bg-[#313131] text-white rounded-2xl shadow-sm flex items-center gap-1.5 hover:bg-[#252324] transition-colors">
            <flux:icon.arrow-left variant="mini" class="size-4" wire:navigate />
            <span class="font-semibold text-base">Kembali</span>
        </a>
        <h1 class="text-xl font-semibold text-[#666666]">Tambah Barang Persediaan</h1>
    </div>


    <x-alert.info>
        Tambah barang ke dalam persediaan. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan
        benar dan tepat. Informasi akan ditampilkan untuk belanja persediaan, bagian dari resep produk, dan
        menetapkan harga produk produksi.
    </x-alert.info>

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex md:flex-row flex-col gap-[130px]">
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
            <select class="js-example-basic-multiple" wire:model.live="category_ids" multiple="multiple">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
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

    @if ($material_details[0]['unit_id'] != null && $material_details[0]['unit_id'] != '')
        <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
            <div class="w-full flex flex-col gap-4">
                <h3 class="text-lg font-medium text-[#666666]">Satuan Lainnya</h3>
                <div class="w-full flex items-center justify-between gap-4">
                    <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed max-w-[780px]">
                        Tambah satuan lainya untuk mengubah satuan utama menjadi satuan lain yang lebih kecil atau
                        besar. Satuan lain digunakan untuk menentukan jumlah rinci bahan baku yang akan digunakan dalam
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
                                <th class="text-left px-6 font-bold text-sm text-[#F8F4E1] min-w-[235px]">Satuan Ukur
                                </th>
                                <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Besar Satuan
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
                                <td class="px-6 text-right text-[#666666] font-medium">1 {{ $main_unit_alias ?? '' }}
                                </td>
                                <td class="px-6 text-right text-[#666666] font-medium">1 {{ $main_unit_alias ?? '' }}
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
                                        </div>
                                    </td>
                                    <td class="px-6 text-right text-[#666666] font-medium">1
                                        {{ $detail['unit'] ?? '' }}</td>
                                    <td class="px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <input type="number"
                                                wire:model.number.live="material_details.{{ $index }}.quantity"
                                                placeholder="0" min="0"
                                                class="w-full max-w-[190px] px-2.5 py-1.5 bg-[#FAFAFA] border border-[#ADADAD] rounded-md text-right text-[#666666] font-medium focus:outline-none focus:border-[#74512D]" />
                                            @if ($main_unit_alias)
                                                <span class="text-[#959595] font-medium">{{ $main_unit_alias }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 text-center">
                                        <button type="button" wire:click.prevent="removeUnit({{ $index }})"
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
                    Belanja persediaan untuk mendapatkan jumlah persediaan dan tanggal expired (merah expired, kuning
                    hampir expired, hijau belum expired).
                </p>
                <input type="text" value="Belum Ada Persediaan" disabled
                    class="w-full px-5 py-2.5 bg-[#EAEAEA] border border-[#D4D4D4] rounded-2xl text-base font-medium text-[#666666] cursor-not-allowed" />
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
                                <th class="text-left px-6 font-bold text-sm text-[#F8F4E1] min-w-[235px]">Satuan Ukur
                                </th>
                                <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Besar Satuan
                                </th>
                                <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Harga Satuan
                                </th>
                                <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Jumlah
                                    Persediaan</th>
                                <th class="text-right px-6 font-bold text-sm text-[#F8F4E1] min-w-[150px]">Jumlah Harga
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

    <div class="mt-8 bg-[#FAFAFA] shadow-sm rounded-2xl p-8">
        <div class="w-full flex flex-wrap gap-8 items-center justify-between">
            <div class="flex-1 min-w-[300px] max-w-[445px] flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Tampilan Barang</h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="is_active" class="sr-only peer"
                            {{ $is_active ? 'checked' : '' }}>
                        <div
                            class="w-[45px] h-[25px] bg-[#525252] peer-checked:bg-[#56C568] rounded-full peer transition-all duration-200 relative">
                            <div
                                class="absolute top-[2px] left-[2px] bg-[#FAFAFA] w-[21px] h-[21px] rounded-full transition-transform duration-200 peer-checked:translate-x-[20px]">
                            </div>
                        </div>
                    </label>
                </div>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Aktifkan opsi ini jika barang ingin ditampilkan, dijual, atau dijadikan bahan produksi.
                </p>
            </div>
            <div class="flex-1 min-w-[300px] max-w-[445px] flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <h3 class="text-lg font-medium text-[#666666]">Jual Langsung dari Persediaan</h3>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="is_recipe" class="sr-only peer"
                            {{ $is_recipe ? 'checked' : '' }}>
                        <div
                            class="w-[45px] h-[25px] bg-[#525252] peer-checked:bg-[#56C568] rounded-full peer transition-all duration-200 relative">
                            <div
                                class="absolute top-[2px] left-[2px] bg-[#FAFAFA] w-[21px] h-[21px] rounded-full transition-transform duration-200 peer-checked:translate-x-[20px]">
                            </div>
                        </div>
                    </label>
                </div>
                <p class="text-sm font-normal text-[#666666] text-justify leading-relaxed">
                    Aktifkan opsi ini jika barang dijual langsung dari persediaan.
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-16 gap-8">
        <a href="{{ route('bahan-baku') }}"
            class="px-6 py-2.5 bg-[#C4C4C4] text-[#333333] font-semibold text-base rounded-2xl shadow-sm flex items-center gap-1.5 hover:bg-[#B0B0B0] transition-colors">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
            Batal
        </a>
        <button type="button" wire:click.prevent="store" wire:loading.attr="disabled"
            class="px-6 py-2.5 bg-[#3F4E4F] text-[#F8F4E1] font-semibold text-base rounded-2xl shadow-sm flex items-center gap-1.5 hover:bg-[#354243] transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z">
                </path>
            </svg>
            Simpan
        </button>
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
