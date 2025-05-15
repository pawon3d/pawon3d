<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('produk') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Produk</h1>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="exclamation-triangle" />
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

    <div class="flex justify-end mt-8">
        <a href="{{ route('produk') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="bookmark-square" type="button" variant="primary" wire:click.prevent="store">Simpan
        </flux:button>
    </div>

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