<div>
    <div class="mb-6 flex gap-4 items-center">
        <a href="{{ route('supplier') }}"
            class="bg-[#313131] hover:bg-[#252324] text-white px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
            <flux:icon.arrow-left variant="mini" class="size-4" />
            <span class="font-montserrat font-semibold text-[16px]">Kembali</span>
        </a>
        <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Tambah Toko Persediaan</h1>
    </div>

    <x-alert.info>
        Tambah toko persediaan. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan tepat.
        Informasi akan ditampilkan untuk memilih toko sebelum belanja persediaan.
    </x-alert.info>

    <!-- First Section: Foto Toko + Basic Info -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-8 py-6 mb-8">
        <div class="flex flex-col lg:flex-row gap-[130px]">
            <!-- Left: Foto Toko -->
            <div class="flex flex-col gap-4">
                <h3 class="font-montserrat font-medium text-[18px] text-[#666666]">Foto Toko</h3>

                <!-- Image Upload Area -->
                <div class="relative w-[300px] h-[170px] border border-dashed border-[#d4d4d4] rounded-[5px] bg-white overflow-hidden"
                    wire:ignore ondragover="event.preventDefault(); this.classList.add('border-[#74512d]');"
                    ondragleave="this.classList.remove('border-[#74512d]');" ondrop="handleDrop(event)"
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
                                    <svg width="34" height="34" viewBox="0 0 34 34" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                        <path d="M17 22.6667V11.3333M17 11.3333L11.3333 17M17 11.3333L22.6667 17"
                                            stroke="#666666" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M29.75 22.6667V27.625C29.75 28.4538 29.4208 29.2487 28.8347 29.8347C28.2487 30.4208 27.4538 30.75 26.625 30.75H7.375C6.54619 30.75 5.75134 30.4208 5.16529 29.8347C4.57924 29.2487 4.25 28.4538 4.25 27.625V22.6667"
                                            stroke="#666666" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <p class="font-montserrat font-medium text-[16px] text-[#666666] mb-2">Unggah Gambar
                                    </p>
                                    <p class="font-montserrat font-normal text-[14px] text-[#666666]">
                                        Ukuran gambar tidak lebih dari <span class="font-semibold">2mb</span>
                                    </p>
                                    <p class="font-montserrat font-normal text-[14px] text-[#959595]">
                                        Pastikan gambar dalam format <span class="font-semibold">JPG</span> atau <span
                                            class="font-semibold">PNG</span>
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
                    class="w-[300px] h-[46px] bg-[#74512d] rounded-[5px] shadow-[0px_4px_4px_0px_rgba(0,0,0,0.25)] font-montserrat font-semibold text-[16px] text-white hover:bg-[#5d3f23] transition-colors">
                    Pilih Gambar
                </button>

                <!-- Error Message -->
                @error('image')
                    <div class="w-[300px] p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Loading Indicator -->
                <div wire:loading wire:target="image"
                    class="w-[300px] p-3 text-sm text-blue-700 bg-blue-100 rounded-lg">
                    Mengupload gambar...
                </div>
            </div>


            <!-- Right: Nama Toko & Deskripsi -->
            <div class="flex-1 flex flex-col gap-6">
                <!-- Nama Toko -->
                <div class="flex flex-col gap-2">
                    <label class="font-montserrat font-medium text-[16px] text-[#666666]">Nama Toko</label>
                    <p class="font-montserrat font-normal text-[14px] text-[#959595]">
                        Masukkan nama toko yang ingin disimpan, seperti "Toko Mawar", "Toko Sarini", atau "Minimarket
                        Emly".
                    </p>
                    <input type="text" wire:model.defer="name" placeholder="Ketik nama toko"
                        class="w-full h-[46px] px-4 bg-white border border-[#d4d4d4] rounded-[5px] font-montserrat font-normal text-[16px] text-[#333333] placeholder:text-[#c4c4c4] focus:outline-none focus:border-[#3f4e4f]" />
                    @error('name')
                        <p class="font-montserrat font-normal text-[14px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Toko -->
                <div class="flex flex-col gap-2">
                    <label class="font-montserrat font-medium text-[16px] text-[#666666]">Deskripsi Toko</label>
                    <p class="font-montserrat font-normal text-[14px] text-[#959595]">
                        Masukkan deskripsi toko yang ingin disimpan, seperti penjelasan apa saja ciri khas dan kegunaan
                        dari produk.
                    </p>
                    <input type="text" wire:model.defer="description" placeholder="Ketik deskripsi produk"
                        class="w-full h-[46px] px-4 bg-white border border-[#d4d4d4] rounded-[5px] font-montserrat font-normal text-[16px] text-[#333333] placeholder:text-[#c4c4c4] focus:outline-none focus:border-[#3f4e4f]" />
                    @error('description')
                        <p class="font-montserrat font-normal text-[14px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Second Section: Alamat Toko -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-8 py-6 mb-8">
        <div class="flex flex-col gap-4">
            <h3 class="font-montserrat font-medium text-[18px] text-[#666666]">Alamat Toko</h3>
            <p class="font-montserrat font-normal text-[14px] text-[#666666] text-justify">
                Masukkan alamat dari lokasi toko.
            </p>

            <p class="font-montserrat font-medium text-[14px] text-[#666666] mt-2">Nama Jalan</p>
            <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-5 py-2.5">
                <input type="text" wire:model.defer="street" placeholder="Contoh : Jl. Jenderal Sudirman Km.4"
                    class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 font-montserrat font-normal text-[16px] text-[#666666] placeholder-[#959595] p-0" />
            </div>

            <p class="font-montserrat font-medium text-[14px] text-[#666666] mt-2">Patokan Lokasi</p>
            <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-5 py-2.5">
                <input type="text" wire:model.defer="landmark" placeholder="Contoh : Depan gapura lorong BTN 2"
                    class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 font-montserrat font-normal text-[16px] text-[#666666] placeholder-[#959595] p-0" />
            </div>

            <p class="font-montserrat font-medium text-[14px] text-[#666666] mt-2">Titik Google Maps</p>
            <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-5 py-2.5">
                <input type="text" wire:model.defer="maps_link"
                    placeholder="Contoh : https://maps.app.goo.gl/jpbice8S3E5G88pW6"
                    class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 font-montserrat font-normal text-[16px] text-[#666666] placeholder-[#959595] p-0" />
            </div>
        </div>
    </div>

    <!-- Third Section: Contact Info -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-8 py-6 mb-12">
        <div class="flex flex-col gap-8">
            <div class="flex flex-col gap-4">
                <h3 class="font-montserrat font-medium text-[18px] text-[#666666]">Nama Kontak</h3>
                <p class="font-montserrat font-normal text-[14px] text-[#666666] text-justify">
                    Masukkan nama kontak untuk mengenali pemilik nomor telepon.
                </p>
                <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-5 py-2.5">
                    <input type="text" wire:model.defer="contact_name" placeholder="Contoh : Ririni"
                        class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 font-montserrat font-normal text-[16px] text-[#666666] placeholder-[#959595] p-0" />
                </div>
                @error('contact_name')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-4">
                <h3 class="font-montserrat font-medium text-[18px] text-[#666666]">No. Telepon</h3>
                <p class="font-montserrat font-normal text-[14px] text-[#666666] text-justify">
                    Masukkan nomor telepon untuk menghubungi pihak toko atau memesan barang.
                </p>
                <div class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-5 py-2.5">
                    <input type="text" wire:model.defer="phone" placeholder="Contoh : 0811223344"
                        class="w-full bg-transparent border-0 focus:outline-none focus:ring-0 font-montserrat font-normal text-[16px] text-[#666666] placeholder-[#959595] p-0" />
                </div>
                @error('phone')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-8">
        <a href="{{ route('supplier') }}"
            class="bg-[#c4c4c4] hover:bg-[#b0b0b0] text-[#333333] px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
            <flux:icon.x-mark class="size-5" />
            <span class="font-montserrat font-semibold text-[16px]">Batal</span>
        </a>
        <button type="button" wire:click.prevent="store"
            class="bg-[#3f4e4f] hover:bg-[#2f3e3f] text-[#f8f4e1] px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
            <flux:icon.bookmark-square class="size-5" />
            <span class="font-montserrat font-semibold text-[16px]">Simpan</span>
        </button>
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
