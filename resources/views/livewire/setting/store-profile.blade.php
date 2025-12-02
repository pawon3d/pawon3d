<div>
    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('pengaturan') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Profil Usaha</h1>
        </div>
    </div>

    {{-- Info Banner --}}
    <x-alert.info>
        <p>
            Lengkapi profil usaha Anda untuk memberikan informasi yang jelas kepada pelanggan. Pastikan semua data
            yang diisi akurat dan terkini.
        </p>
    </x-alert.info>

    {{-- Section 1: Informasi Profil Usaha --}}
    <div class="w-full mt-8 bg-white shadow-lg rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-6">
            Informasi Profil Usaha
        </h4>

        {{-- Text Fields --}}
        <div class="flex flex-col gap-4">
            <flux:field>
                <flux:label>Nama Usaha</flux:label>
                <flux:input placeholder="Contoh: Pawon3D" wire:model="name" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Tagline Usaha</flux:label>
                <flux:input placeholder="Contoh: Kue Rumahan Lezat, Sehangat Pelukan Ibu" wire:model="tagline" />
                <flux:error name="tagline" />
            </flux:field>

            <flux:field>
                <flux:label>Jenis Usaha</flux:label>
                <flux:input placeholder="Contoh: Usaha Mikro Kecil (UMK)" wire:model="type" />
                <flux:error name="type" />
            </flux:field>

            <flux:field>
                <flux:label>Jenis Produksi</flux:label>
                <flux:input placeholder="Contoh: Kue" wire:model="product" />
                <flux:error name="product" />
            </flux:field>

            <flux:field>
                <flux:label>Deskripsi Usaha</flux:label>
                <flux:textarea rows="3"
                    placeholder="Contoh: Pawon3D hadir sebagai destinasi kuliner kue pilihan yang mengusung konsep unik. Perpaduan antara adorasi tradisional Nusantara dengan sentuhan gaya modern dan inovasi rasa."
                    wire:model="description" />
                <flux:error name="description" />
            </flux:field>
        </div>

        {{-- Image Uploads: Logo, Banner, Contoh Produk --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            {{-- Logo Usaha --}}
            <div class="flex flex-col gap-4">
                <flux:field>
                    <flux:label>Logo Usaha</flux:label>
                </flux:field>
                <div class="flex flex-col w-full space-y-4">
                    <div class="relative w-full h-40 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                        wire:ignore
                        ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                        ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');"
                        ondrop="handleDropLogo(event)" id="dropzone-logo-container">

                        <label for="dropzone-logo-file"
                            class="w-full h-full cursor-pointer flex items-center justify-center">
                            <div id="preview-logo-container" class="w-full h-full">
                                @if ($previewLogoImage)
                                    <img src="{{ $previewLogoImage }}" alt="Preview" class="object-cover w-full h-full"
                                        id="logo-image-preview" />
                                @else
                                    <div class="flex flex-col items-center justify-center p-4 text-center h-full">
                                        <flux:icon icon="arrow-up-tray" class="w-6 h-6 mb-2 text-gray-400" />
                                        <p class="mb-1 text-sm font-semibold text-gray-600">Unggah Foto</p>
                                        <p class="text-xs text-gray-500">Ukuran foto tidak lebih dari <span
                                                class="font-semibold">2mb</span></p>
                                        <p class="text-xs text-gray-500"><span class="font-semibold">PNG</span></p>
                                    </div>
                                @endif
                            </div>
                        </label>
                    </div>

                    <input id="dropzone-logo-file" type="file" wire:model="logo" class="hidden" accept="image/png"
                        onchange="previewLogoImage(this)" />

                    <flux:button variant="primary" type="button"
                        onclick="document.getElementById('dropzone-logo-file').click()" class="w-full">
                        Pilih Foto
                    </flux:button>

                    @error('logo')
                        <div class="w-full p-2 text-sm text-red-700 bg-red-100 rounded-lg">{{ $message }}</div>
                    @enderror

                    <div wire:loading wire:target="logo"
                        class="w-full p-2 text-sm text-blue-700 bg-blue-100 rounded-lg">
                        Mengupload gambar...
                    </div>
                </div>
            </div>

            {{-- Banner Utama --}}
            <div class="flex flex-col gap-4">
                <flux:field>
                    <flux:label>Banner Utama</flux:label>
                </flux:field>
                <div class="flex flex-col w-full space-y-4">
                    <div class="relative w-full h-40 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                        wire:ignore
                        ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                        ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');"
                        ondrop="handleDropBanner(event)" id="dropzone-banner-container">

                        <label for="dropzone-banner-file"
                            class="w-full h-full cursor-pointer flex items-center justify-center">
                            <div id="preview-banner-container" class="w-full h-full">
                                @if ($previewBannerImage)
                                    <img src="{{ $previewBannerImage }}" alt="Preview"
                                        class="object-cover w-full h-full" id="banner-image-preview" />
                                @else
                                    <div class="flex flex-col items-center justify-center p-4 text-center h-full">
                                        <flux:icon icon="arrow-up-tray" class="w-6 h-6 mb-2 text-gray-400" />
                                        <p class="mb-1 text-sm font-semibold text-gray-600">Unggah Foto</p>
                                        <p class="text-xs text-gray-500">Ukuran foto tidak lebih dari <span
                                                class="font-semibold">2mb</span></p>
                                        <p class="text-xs text-gray-500"><span class="font-semibold">JPG</span> atau
                                            <span class="font-semibold">PNG</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </label>
                    </div>

                    <input id="dropzone-banner-file" type="file" wire:model="banner" class="hidden"
                        accept="image/jpeg, image/png, image/jpg" onchange="previewBannerImage(this)" />

                    <flux:button variant="primary" type="button"
                        onclick="document.getElementById('dropzone-banner-file').click()" class="w-full">
                        Pilih Foto
                    </flux:button>

                    @error('banner')
                        <div class="w-full p-2 text-sm text-red-700 bg-red-100 rounded-lg">{{ $message }}</div>
                    @enderror

                    <div wire:loading wire:target="banner"
                        class="w-full p-2 text-sm text-blue-700 bg-blue-100 rounded-lg">
                        Mengupload gambar...
                    </div>
                </div>
            </div>

            {{-- Contoh Produk --}}
            <div class="flex flex-col gap-4">
                <flux:field>
                    <flux:label>Contoh Produk</flux:label>
                </flux:field>
                <div class="flex flex-col w-full space-y-4">
                    <div class="relative w-full h-40 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                        wire:ignore
                        ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                        ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');"
                        ondrop="handleDropProductImage(event)" id="dropzone-product-container">

                        <label for="dropzone-product-file"
                            class="w-full h-full cursor-pointer flex items-center justify-center">
                            <div id="preview-product-container" class="w-full h-full">
                                @if ($previewProductImage)
                                    <img src="{{ $previewProductImage }}" alt="Preview"
                                        class="object-cover w-full h-full" id="product-image-preview" />
                                @else
                                    <div class="flex flex-col items-center justify-center p-4 text-center h-full">
                                        <flux:icon icon="arrow-up-tray" class="w-6 h-6 mb-2 text-gray-400" />
                                        <p class="mb-1 text-sm font-semibold text-gray-600">Unggah Foto</p>
                                        <p class="text-xs text-gray-500">Ukuran foto tidak lebih dari <span
                                                class="font-semibold">2mb</span></p>
                                        <p class="text-xs text-gray-500"><span class="font-semibold">JPG</span> atau
                                            <span class="font-semibold">PNG</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </label>
                    </div>

                    <input id="dropzone-product-file" type="file" wire:model="productImage" class="hidden"
                        accept="image/jpeg, image/png, image/jpg" onchange="previewProductImage(this)" />

                    <flux:button variant="primary" type="button"
                        onclick="document.getElementById('dropzone-product-file').click()" class="w-full">
                        Pilih Foto
                    </flux:button>

                    @error('productImage')
                        <div class="w-full p-2 text-sm text-red-700 bg-red-100 rounded-lg">{{ $message }}</div>
                    @enderror

                    <div wire:loading wire:target="productImage"
                        class="w-full p-2 text-sm text-blue-700 bg-blue-100 rounded-lg">
                        Mengupload gambar...
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Alamat dan Kontak --}}
    <div class="w-full mt-8 bg-white shadow-lg rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-6">
            Informasi Alamat dan Kontak Usaha
        </h4>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Left Column: Foto Tempat Usaha --}}
            <div class="flex flex-col gap-4">
                <flux:field>
                    <flux:label>Foto Tempat Usaha</flux:label>
                </flux:field>
                <div class="flex flex-col w-full space-y-4">
                    <div class="relative w-full h-48 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                        wire:ignore
                        ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                        ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');"
                        ondrop="handleDropBuilding(event)" id="dropzone-building-container">

                        <label for="dropzone-building-file"
                            class="w-full h-full cursor-pointer flex items-center justify-center">
                            <div id="preview-building-container" class="w-full h-full">
                                @if ($previewBuildingImage)
                                    <img src="{{ $previewBuildingImage }}" alt="Preview"
                                        class="object-cover w-full h-full" id="building-image-preview" />
                                @else
                                    <div class="flex flex-col items-center justify-center p-4 text-center h-full">
                                        <flux:icon icon="arrow-up-tray" class="w-6 h-6 mb-2 text-gray-400" />
                                        <p class="mb-1 text-sm font-semibold text-gray-600">Unggah Foto</p>
                                        <p class="text-xs text-gray-500">Ukuran foto tidak lebih dari <span
                                                class="font-semibold">2mb</span></p>
                                        <p class="text-xs text-gray-500"><span class="font-semibold">JPG</span> atau
                                            <span class="font-semibold">PNG</span>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </label>
                    </div>

                    <input id="dropzone-building-file" type="file" wire:model="building" class="hidden"
                        accept="image/jpeg, image/png, image/jpg" onchange="previewBuildingImage(this)" />

                    <flux:button variant="primary" type="button"
                        onclick="document.getElementById('dropzone-building-file').click()" class="w-full">
                        Pilih Foto
                    </flux:button>

                    @error('building')
                        <div class="w-full p-2 text-sm text-red-700 bg-red-100 rounded-lg">{{ $message }}</div>
                    @enderror

                    <div wire:loading wire:target="building"
                        class="w-full p-2 text-sm text-blue-700 bg-blue-100 rounded-lg">
                        Mengupload gambar...
                    </div>
                </div>
            </div>

            {{-- Right Column: Location, Address, Contact, Email, Website --}}
            <div class="flex flex-col gap-4">
                <flux:field>
                    <flux:label>Titik Google Maps</flux:label>
                    <flux:input placeholder="Contoh: https://maps.app.goo.gl/socTAnFbJXJ3mUFw9"
                        wire:model="location" />
                    <flux:error name="location" />
                </flux:field>

                <flux:field>
                    <flux:label>Alamat</flux:label>
                    <flux:textarea rows="2"
                        placeholder="Contoh: Jl. Jenderal Sudirman KM.3, RT.25 RW.07, Kel. Muara Bulian, Kec. Muara Bulian, Kab. Batang Hari, Jambi"
                        wire:model="address" />
                    <flux:error name="address" />
                </flux:field>

                <flux:field>
                    <flux:label>No. Telepon</flux:label>
                    <flux:input placeholder="Contoh: 08123456789" wire:model="contact" />
                    <flux:error name="contact" />
                </flux:field>

                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input type="email" placeholder="Contoh: tokokue@gmail.com" wire:model="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label>Website</flux:label>
                    <flux:input placeholder="Contoh: www.pawon3d.my.id" wire:model="website" />
                    <flux:error name="website" />
                </flux:field>
            </div>
        </div>
    </div>

    {{-- Section 3: Sosial Media --}}
    <div class="w-full mt-8 bg-white shadow-lg rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-6">
            Informasi Sosial Media
        </h4>

        <div class="flex flex-col gap-4">
            <flux:field>
                <flux:label>Instagram</flux:label>
                <flux:input placeholder="Contoh: @pawon3d" wire:model="social_instagram" />
                <flux:error name="social_instagram" />
            </flux:field>

            <flux:field>
                <flux:label>Facebook</flux:label>
                <flux:input placeholder="Contoh: @pawon3d" wire:model="social_facebook" />
                <flux:error name="social_facebook" />
            </flux:field>

            <flux:field>
                <flux:label>WhatsApp</flux:label>
                <flux:input placeholder="Contoh: 08123456789" wire:model="social_whatsapp" />
                <flux:error name="social_whatsapp" />
            </flux:field>
        </div>
    </div>

    {{-- Section 4: Legalitas Usaha --}}
    <div class="w-full mt-8 bg-white shadow-lg rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-6">
            Informasi Legalitas Usaha
        </h4>

        <div class="flex flex-row items-center justify-between mb-4">
            <flux:text class="text-gray-600">Daftar Dokumen</flux:text>
            <flux:button type="button" variant="primary" icon="plus" wire:click='addModal'>
                Tambah Dokumen
            </flux:button>
        </div>

        <x-table.paginated :paginator="$storeDocuments" :headers="[
            ['label' => 'Dokumen Legalitas', 'class' => 'bg-[#3F4E4F] text-white'],
            ['label' => 'Nomor Dokumen', 'class' => 'bg-[#3F4E4F] text-white'],
            ['label' => 'Tanggal Terbit', 'class' => 'bg-[#3F4E4F] text-white'],
            ['label' => 'Berlaku Hingga', 'class' => 'bg-[#3F4E4F] text-white'],
        ]"
            emptyMessage="Tidak ada dokumen legalitas yang tersedia.">
            @foreach ($storeDocuments as $document)
                <tr class="hover:bg-gray-100 border-b border-gray-200" wire:key="document-{{ $document->id }}">
                    <td class="px-6 py-4 cursor-pointer hover:text-black"
                        wire:click="editModal('{{ $document->id }}')">
                        {{ $document->document_name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $document->document_number ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $document->valid_from ? \Carbon\Carbon::parse($document->valid_from)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $document->valid_until ? \Carbon\Carbon::parse($document->valid_until)->format('d/m/Y') : 'Tidak Terbatas' }}
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end gap-4 mt-6">
        <flux:button href="{{ route('pengaturan') }}" icon="x-mark">Batal</flux:button>
        <flux:button wire:click="updateStore" icon="save" variant="primary">Simpan</flux:button>
    </div>

    {{-- Document Modal --}}
    <flux:modal name="document" class="w-full max-w-md" wire:model="showModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $edit ? 'Rincian' : 'Tambah' }} Dokumen</flux:heading>
            </div>
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Nama Dokumen</flux:label>
                    <flux:input wire:model="documentName" placeholder="Masukkan nama dokumen" />
                    <flux:error name="documentName" />
                </flux:field>

                <flux:field>
                    <flux:label>Nomor Dokumen</flux:label>
                    <flux:input wire:model="documentNumber" placeholder="Masukkan nomor dokumen" />
                    <flux:error name="documentNumber" />
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Terbit</flux:label>
                    <input type="date" class="tanggal" onclick="this.showPicker()"
                        data-date="{{ $validFrom ? \Carbon\Carbon::parse($validFrom)->format('d/m/Y') : 'dd/mm/yyyy' }}"
                        wire:model.live="validFrom" id="validFrom" />
                    <flux:error name="validFrom" />
                </flux:field>

                <flux:field>
                    <flux:label>Tanggal Berlaku Sampai</flux:label>
                    <input type="date" class="tanggal" onclick="this.showPicker()"
                        data-date="{{ $validUntil ? \Carbon\Carbon::parse($validUntil)->format('d/m/Y') : 'dd/mm/yyyy' }}"
                        wire:model.live="validUntil" id="validUntil" />
                    <flux:error name="validUntil" />
                </flux:field>

                <div class="mb-5 w-full">
                    <flux:label class="mb-2">File Dokumen</flux:label>
                    <div class="flex flex-row items-center gap-4">
                        <label
                            class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-[#74512D] hover:bg-[color-mix(in_oklab,_#74512D,_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 shadow w-1/4">
                            Pilih File
                            <input type="file" wire:model.live="documentFile"
                                accept="image/jpeg, image/png, image/jpg, application/pdf" class="hidden" />
                        </label>

                        @if ($documentFile)
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="{{ is_string($documentFile) ? basename($documentFile) : $documentFile->getClientOriginalName() }}"
                                readonly wire:loading.remove wire:target="documentFile">
                        @else
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="File Belum Dipilih" readonly wire:loading.remove wire:target="documentFile">
                        @endif
                        <input type="text"
                            class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100 hidden"
                            value="Mengupload File..." readonly wire:loading.class.remove="hidden"
                            wire:target="documentFile">
                    </div>
                    <flux:error name="documentFile" />
                </div>
            </div>

            <div class="flex justify-end gap-2">
                @if ($edit)
                    <flux:modal.trigger name="delete-document" class="mr-auto">
                        <flux:button variant="ghost" icon="trash" />
                    </flux:modal.trigger>
                @endif
                <flux:button type="button" icon="x-mark" wire:click="$set('showModal', false)">Batal</flux:button>
                @if ($edit)
                    <flux:button type="button" icon="save" variant="primary" wire:click="updateDocument">Simpan
                    </flux:button>
                @else
                    <flux:button type="button" icon="save" variant="primary" wire:click="storeDocument">Simpan
                    </flux:button>
                @endif
            </div>
        </div>
    </flux:modal>

    {{-- Delete Document Modal --}}
    <flux:modal name="delete-document" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Dokumen</flux:heading>
                <flux:text class="mt-2">
                    Apakah Anda yakin ingin menghapus dokumen ini?
                </flux:text>
            </div>
            <div class="flex gap-2 justify-end">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="button" variant="danger" wire:click="delete">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- JavaScript for Image Previews --}}
    <script>
        function handleDropLogo(event) {
            event.preventDefault();
            const container = event.currentTarget;
            container.classList.remove('border-blue-500', 'bg-gray-100');
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('dropzone-logo-file');
                input.files = files;
                previewLogoImage(input);
                input.dispatchEvent(new Event('change'));
            }
        }

        function previewLogoImage(input) {
            const previewContainer = document.getElementById('preview-logo-container');
            const defaultContent = previewContainer.querySelector('.flex-col');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewImg = document.getElementById('logo-image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'logo-image-preview';
                        previewImg.className = 'object-cover w-full h-full';
                        previewContainer.appendChild(previewImg);
                    }
                    previewImg.src = e.target.result;
                    if (defaultContent) defaultContent.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleDropBanner(event) {
            event.preventDefault();
            const container = event.currentTarget;
            container.classList.remove('border-blue-500', 'bg-gray-100');
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('dropzone-banner-file');
                input.files = files;
                previewBannerImage(input);
                input.dispatchEvent(new Event('change'));
            }
        }

        function previewBannerImage(input) {
            const previewContainer = document.getElementById('preview-banner-container');
            const defaultContent = previewContainer.querySelector('.flex-col');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewImg = document.getElementById('banner-image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'banner-image-preview';
                        previewImg.className = 'object-cover w-full h-full';
                        previewContainer.appendChild(previewImg);
                    }
                    previewImg.src = e.target.result;
                    if (defaultContent) defaultContent.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleDropProductImage(event) {
            event.preventDefault();
            const container = event.currentTarget;
            container.classList.remove('border-blue-500', 'bg-gray-100');
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('dropzone-product-file');
                input.files = files;
                previewProductImageHandler(input);
                input.dispatchEvent(new Event('change'));
            }
        }

        function previewProductImage(input) {
            previewProductImageHandler(input);
        }

        function previewProductImageHandler(input) {
            const previewContainer = document.getElementById('preview-product-container');
            const defaultContent = previewContainer.querySelector('.flex-col');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewImg = document.getElementById('product-image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'product-image-preview';
                        previewImg.className = 'object-cover w-full h-full';
                        previewContainer.appendChild(previewImg);
                    }
                    previewImg.src = e.target.result;
                    if (defaultContent) defaultContent.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleDropBuilding(event) {
            event.preventDefault();
            const container = event.currentTarget;
            container.classList.remove('border-blue-500', 'bg-gray-100');
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('dropzone-building-file');
                input.files = files;
                previewBuildingImage(input);
                input.dispatchEvent(new Event('change'));
            }
        }

        function previewBuildingImage(input) {
            const previewContainer = document.getElementById('preview-building-container');
            const defaultContent = previewContainer.querySelector('.flex-col');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewImg = document.getElementById('building-image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'building-image-preview';
                        previewImg.className = 'object-cover w-full h-full';
                        previewContainer.appendChild(previewImg);
                    }
                    previewImg.src = e.target.result;
                    if (defaultContent) defaultContent.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    @section('css')
        <style>
            .tanggal {
                position: relative;
                width: 100%;
                height: 2.5rem;
                padding: 0.5rem 2.5rem 0.5rem 0.75rem;
                color: transparent;
                background-color: #f9fafb;
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                font-size: 0.875rem;
                outline: none;
            }

            .tanggal:before {
                position: absolute;
                top: 50%;
                left: 0.75rem;
                transform: translateY(-50%);
                content: attr(data-date);
                display: inline-block;
                color: #111827;
                pointer-events: none;
                font-size: 0.875rem;
            }

            .tanggal::-webkit-datetime-edit,
            .tanggal::-webkit-inner-spin-button,
            .tanggal::-webkit-clear-button {
                display: none;
            }

            .tanggal::-webkit-calendar-picker-indicator {
                position: absolute;
                top: 50%;
                right: 0.75rem;
                transform: translateY(-50%);
                opacity: 1;
                color: #6b7280;
                cursor: pointer;
            }
        </style>
    @endsection
</div>
