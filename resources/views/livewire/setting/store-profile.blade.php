<div>
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
    <div class="flex items-center bg-white shadow-lg rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div>
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Bibendum sit in habitant id. Quis aenean placerat aliquet
                laoreet ac arcu posuere leo in. Ultricies consequat quis sollicitudin etiam. Luctus feugiat ac orci
                netus dolor sapien.
            </p>
        </div>
    </div>

    <div class="w-full mt-8 bg-white shadow-lg rounded-lg p-4">
        <div class="w-full mt-8 ">
            <h4 class="text-lg font-semibold text-gray-800">
                Informasi Umum
            </h4>
            <div class="w-full flex md:flex-row flex-col gap-8 mt-2">
                <div class="md:w-1/2 flex flex-col gap-4 mt-4">
                    <flux:label>Logo Usaha</flux:label>
                    <div class="flex flex-col w-full max-w-xs space-y-4">
                        <!-- Dropzone Area -->
                        <div class="relative w-full h-48 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                            wire:ignore
                            ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                            ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');"
                            ondrop="handleDropLogo(event)" id="dropzone-logo-container">

                            <label for="dropzone-logo-file"
                                class="w-full h-full cursor-pointer flex items-center justify-center">
                                <div id="preview-logo-container" class="w-full h-full">
                                    @if ($previewLogoImage)
                                    <!-- Image Preview -->
                                    <img src="{{ $previewLogoImage }}" alt="Preview" class="object-cover w-full h-full"
                                        id="logo-image-preview" />
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
                        <input id="dropzone-logo-file" type="file" wire:model="logo" class="hidden"
                            accept="image/jpeg, image/png, image/jpg" onchange="previewLogoImage(this)" />

                        <!-- Upload Button -->
                        <flux:button variant="primary" type="button"
                            onclick="document.getElementById('dropzone-logo-file').click()" class="w-full">
                            Pilih Gambar
                        </flux:button>

                        <!-- Error Message -->
                        @error('logo')
                        <div class="w-full p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                            {{ $message }}
                        </div>
                        @enderror

                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="logo"
                            class="w-full p-3 text-sm text-blue-700 bg-blue-100 rounded-lg">
                            Mengupload gambar...
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 flex flex-col gap-4 mt-4">
                    <flux:label>Nama Usaha</flux:label>
                    <flux:input placeholder="Contoh: Pawon3D" wire:model.defer="name" />
                    <flux:error name="name" />
                    <flux:label>Tagline Usaha</flux:label>
                    <flux:input placeholder="Contoh: Kue Rumahan Lezat, Sehangat Pelukan Ibu"
                        wire:model.defer="tagline" />
                    <flux:error name="tagline" />
                    <flux:label>Jenis Usaha</flux:label>
                    <flux:input placeholder="Contoh: Usaha Mikro Kecil (UMK)" wire:model.defer="type" />
                    <flux:error name="type" />
                </div>
            </div>
        </div>

        <div class="w-full mt-8 ">
            <div class="w-full flex md:flex-row flex-col gap-8 mt-2">
                <div class="md:w-1/2 flex flex-col gap-4 mt-4">
                    <flux:label>Banner Utama</flux:label>
                    <div class="flex flex-col w-full max-w-md space-y-4">
                        <!-- Dropzone Area -->
                        <div class="relative w-full h-48 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                            wire:ignore
                            ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                            ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');"
                            ondrop="handleDropBanner(event)" id="dropzone-banner-container">

                            <label for="dropzone-banner-file"
                                class="w-full h-full cursor-pointer flex items-center justify-center">
                                <div id="preview-banner-container" class="w-full h-full">
                                    @if ($previewBannerImage)
                                    <!-- Image Preview -->
                                    <img src="{{ $previewBannerImage }}" alt="Preview"
                                        class="object-cover w-full h-full" id="banner-image-preview" />
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
                        <input id="dropzone-banner-file" type="file" wire:model="banner" class="hidden"
                            accept="image/jpeg, image/png, image/jpg" onchange="previewBannerImage(this)" />

                        <!-- Upload Button -->
                        <flux:button variant="primary" type="button"
                            onclick="document.getElementById('dropzone-banner-file').click()" class="w-full">
                            Pilih Gambar
                        </flux:button>

                        <!-- Error Message -->
                        @error('banner')
                        <div class="w-full p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                            {{ $message }}
                        </div>
                        @enderror

                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="banner"
                            class="w-full p-3 text-sm text-blue-700 bg-blue-100 rounded-lg">
                            Mengupload gambar...
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 flex flex-col gap-4 mt-4">
                    <flux:label>Jenis Produksi</flux:label>
                    <flux:input placeholder="Contoh: Kue" wire:model.defer="product" />
                    <flux:error name="product" />
                    <flux:label>Deskripsi Usaha</flux:label>
                    <flux:textarea rows="7"
                        placeholder="Contoh: Pawon3D hadir sebagai destinasi kuliner kue pilihan yang mengusung konsep unik. Perpaduan antara resep tradisional Nusantara dengan sentuhan gaya modern dan inovasi rasa."
                        wire:model.defer="description" />
                    <flux:error name="description" />
                </div>
            </div>
        </div>

        <div class="w-full mt-8 ">
            <h4 class="text-lg font-semibold text-gray-800">
                Informasi Alamat dan Kontak Usaha
            </h4>
            <div class="w-full flex md:flex-row flex-col gap-8 mt-2">
                <div class="md:w-1/2 flex flex-col gap-4 mt-4">
                    <flux:label>Foto Tempat Usaha</flux:label>
                    <div class="flex flex-col w-full max-w-md space-y-4">
                        <!-- Dropzone Area -->
                        <div class="relative w-full h-48 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200 overflow-hidden"
                            wire:ignore
                            ondragover="event.preventDefault(); this.classList.add('border-blue-500', 'bg-gray-100');"
                            ondragleave="this.classList.remove('border-blue-500', 'bg-gray-100');"
                            ondrop="handleDropBuilding(event)" id="dropzone-building-container">

                            <label for="dropzone-building-file"
                                class="w-full h-full cursor-pointer flex items-center justify-center">
                                <div id="preview-building-container" class="w-full h-full">
                                    @if ($previewBuildingImage)
                                    <!-- Image Preview -->
                                    <img src="{{ $previewBuildingImage }}" alt="Preview"
                                        class="object-cover w-full h-full" id="building-image-preview" />
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
                        <input id="dropzone-building-file" type="file" wire:model="building" class="hidden"
                            accept="image/jpeg, image/png, image/jpg" onchange="previewBuildingImage(this)" />

                        <!-- Upload Button -->
                        <flux:button variant="primary" type="button"
                            onclick="document.getElementById('dropzone-building-file').click()" class="w-full">
                            Pilih Gambar
                        </flux:button>

                        <!-- Error Message -->
                        @error('building')
                        <div class="w-full p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                            {{ $message }}
                        </div>
                        @enderror

                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="building"
                            class="w-full p-3 text-sm text-blue-700 bg-blue-100 rounded-lg">
                            Mengupload gambar...
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 flex flex-col gap-4 mt-4">
                    <flux:label>Titik Lokasi (Google Maps)</flux:label>
                    <flux:input placeholder="Contoh :  https://maps.app.goo.gl/socTAnFbJXJ3mUFw9"
                        wire:model.defer="location" />
                    <flux:error name="location" />
                    <flux:label>Alamat</flux:label>
                    <flux:textarea rows="2"
                        placeholder="Contoh : Jl. Jenderal Sudirman KM.3, RT.25 RW.07, Kel. Muara Bulian, Kec. Muara Bulian, Kab. Batang Hari, Jambi"
                        wire:model.defer="address" />
                    <flux:error name="address" />
                    <flux:label>Nomor Telepon</flux:label>
                    <flux:input placeholder="Contoh :  08123456789" wire:model.defer="contact" />
                    <flux:error name="contact" />
                    <flux:label>Alamat Email</flux:label>
                    <flux:input placeholder="Contoh :  tokokue@gmail.com" wire:model.defer="email" />
                    <flux:error name="email" />
                    <flux:label>Website</flux:label>
                    <flux:input placeholder="Contoh :  www.pawon3d.my.id" wire:model.defer="website" />
                    <flux:error name="website" />
                </div>
            </div>
        </div>
    </div>
    <div class="w-full mt-8 bg-white shadow-lg rounded-lg p-4">
        <div class="w-full mt-8 ">
            <h4 class="text-lg font-semibold text-gray-800">
                Informasi Jam Operasional
            </h4>
            <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                <div class="border border-gray-300 rounded-lg p-4 flex flex-col gap-4 bg-white">
                    <div class="flex flex-row items-center justify-between">
                        <flux:label>Senin</flux:label>
                        <flux:switch wire:model.live="is_senin" :checked="$is_senin ? true : false"
                            class="data-checked:bg-green-500" />
                    </div>
                    <div class="flex flex-row w-full items-center justify-between">
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Buka</flux:label>
                            <div x-init="flatpickr($refs.open_senin, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                open_senin = dateStr;
                                @this.set('open_senin', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="open_senin" wire:model='open_senin' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Tutup</flux:label>
                            <div x-init="flatpickr($refs.close_senin, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                close_senin = dateStr;
                                @this.set('close_senin', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="close_senin" wire:model='close_senin' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-300 rounded-lg p-4 flex flex-col gap-4 bg-white">
                    <div class="flex flex-row items-center justify-between">
                        <flux:label>Selasa</flux:label>
                        <flux:switch wire:model.live="is_selasa" :checked="$is_selasa ? true : false"
                            class="data-checked:bg-green-500" />
                    </div>
                    <div class="flex flex-row w-full items-center justify-between">
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Buka</flux:label>
                            <div x-init="flatpickr($refs.open_selasa, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                open_selasa = dateStr;
                                @this.set('open_selasa', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="open_selasa" wire:model='open_selasa' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Tutup</flux:label>
                            <div x-init="flatpickr($refs.close_selasa, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                close_selasa = dateStr;
                                @this.set('close_selasa', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="close_selasa" wire:model='close_selasa' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-300 rounded-lg p-4 flex flex-col gap-4 bg-white">
                    <div class="flex flex-row items-center justify-between">
                        <flux:label>Rabu</flux:label>
                        <flux:switch wire:model.live="is_rabu" :checked="$is_rabu ? true : false"
                            class="data-checked:bg-green-500" />
                    </div>
                    <div class="flex flex-row w-full items-center justify-between">
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Buka</flux:label>
                            <div x-init="flatpickr($refs.open_rabu, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                open_rabu = dateStr;
                                @this.set('open_rabu', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="open_rabu" wire:model='open_rabu' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Tutup</flux:label>
                            <div x-init="flatpickr($refs.close_rabu, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                close_rabu = dateStr;
                                @this.set('close_rabu', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="close_rabu" wire:model='close_rabu' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                <div class="border border-gray-300 rounded-lg p-4 flex flex-col gap-4 bg-white">
                    <div class="flex flex-row items-center justify-between">
                        <flux:label>Kamis</flux:label>
                        <flux:switch wire:model.live="is_kamis" :checked="$is_kamis ? true : false"
                            class="data-checked:bg-green-500" />
                    </div>
                    <div class="flex flex-row w-full items-center justify-between">
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Buka</flux:label>
                            <div x-init="flatpickr($refs.open_kamis, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                open_kamis = dateStr;
                                @this.set('open_kamis', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="open_kamis" wire:model='open_kamis' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Tutup</flux:label>
                            <div x-init="flatpickr($refs.close_kamis, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                close_kamis = dateStr;
                                @this.set('close_kamis', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="close_kamis" wire:model='close_kamis' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-300 rounded-lg p-4 flex flex-col gap-4 bg-white">
                    <div class="flex flex-row items-center justify-between">
                        <flux:label>Jumat</flux:label>
                        <flux:switch wire:model.live="is_jumat" :checked="$is_jumat ? true : false"
                            class="data-checked:bg-green-500" />
                    </div>
                    <div class="flex flex-row w-full items-center justify-between">
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Buka</flux:label>
                            <div x-init="flatpickr($refs.open_jumat, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                open_jumat = dateStr;
                                @this.set('open_jumat', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="open_jumat" wire:model='open_jumat' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Tutup</flux:label>
                            <div x-init="flatpickr($refs.close_jumat, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                close_jumat = dateStr;
                                @this.set('close_jumat', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="close_jumat" wire:model='close_jumat' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-300 rounded-lg p-4 flex flex-col gap-4 bg-white">
                    <div class="flex flex-row items-center justify-between">
                        <flux:label>Sabtu</flux:label>
                        <flux:switch wire:model.live="is_sabtu" :checked="$is_sabtu ? true : false"
                            class="data-checked:bg-green-500" />
                    </div>
                    <div class="flex flex-row w-full items-center justify-between">
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Buka</flux:label>
                            <div x-init="flatpickr($refs.open_sabtu, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                open_sabtu = dateStr;
                                @this.set('open_sabtu', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="open_sabtu" wire:model='open_sabtu' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Tutup</flux:label>
                            <div x-init="flatpickr($refs.close_sabtu, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                close_sabtu = dateStr;
                                @this.set('close_sabtu', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="close_sabtu" wire:model='close_sabtu' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full grid grid-cols-1 gap-4 mt-2">
                <div class="border border-gray-300 rounded-lg p-4 flex flex-col gap-4 bg-white">
                    <div class="flex flex-row items-center justify-between">
                        <flux:label>Minggu</flux:label>
                        <flux:switch wire:model.live="is_minggu" :checked="$is_minggu ? true : false"
                            class="data-checked:bg-green-500" />
                    </div>
                    <div class="flex flex-row w-full items-center justify-between">
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Buka</flux:label>
                            <div x-init="flatpickr($refs.open_minggu, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                open_minggu = dateStr;
                                @this.set('open_minggu', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="open_minggu" wire:model='open_minggu' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                        <div class="flex flex-col w-1/3 gap-3 border border-gray-300 rounded-lg p-2">
                            <flux:label>Tutup</flux:label>
                            <div x-init="flatpickr($refs.close_minggu, {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: 'H:i',
                            time_24hr: true,
                            onChange: function(selectedDates, dateStr) {
                                close_minggu = dateStr;
                                @this.set('close_minggu', dateStr);
                            }
                        });" class="relative">
                                <input x-ref="close_minggu" wire:model='close_minggu' type="text"
                                    class="w-full text-gray-600 text-sm font-semibold border-0 focus:outline-none focus:ring-0 cursor-pointer"
                                    placeholder="hh:mm" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="w-full mt-8 bg-white shadow-lg rounded-lg p-4">
        <div class="w-full mt-8 ">
            <h4 class="text-lg font-semibold text-gray-800">
                Informasi Sosial Media
            </h4>
            <div class="w-full flex flex-col gap-8 mt-2">
                <div class="w-full flex flex-col gap-4 mt-4">
                    <flux:label>Instagram</flux:label>
                    <flux:input placeholder="Contoh : @pawon3d" wire:model.defer="social_instagram" />
                    <flux:error name="social_instagram" />
                    <flux:label>Facebook</flux:label>
                    <flux:input placeholder="Contoh : @pawon3d" wire:model.defer="social_facebook" />
                    <flux:error name="social_facebook" />
                    <flux:label>Whatsapp</flux:label>
                    <flux:input placeholder="Contoh : 08123456789" wire:model.defer="social_whatsapp" />
                    <flux:error name="social_whatsapp" />
                </div>
            </div>
        </div>
    </div>
    <div class="w-full mt-8 bg-white shadow-lg rounded-lg p-4">
        <div class="w-full mt-8 ">
            <h4 class="text-lg font-semibold text-gray-800">
                Informasi Legalitas Usaha
            </h4>
            <div class="flex flex-row items-center justify-between mt-4">
                <flux:label>Daftar Dokumen</flux:label>
                <flux:button type="button" variant="primary" icon="plus" wire:click='addModal'>Tambah Dokumen
                </flux:button>
            </div>
            <div class="bg-white rounded-xl border shadow-sm mt-4">
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-6 py-3 font-semibold cursor-pointer" wire:click="sortBy('document_name')">
                                    Dokumen Legalitas
                                    <span>{{ $sortDirection === 'asc' && $sortField === 'document_name' ? '↑' : '↓'
                                        }}</span>
                                </th>
                                <th class="px-6 py-3 font-semibold cursor-pointer"
                                    wire:click="sortBy('document_number')">
                                    Nomor Dokumen
                                    <span>{{ $sortDirection === 'asc' && $sortField === 'document_number' ? '↑' : '↓'
                                        }}</span>
                                </th>
                                <th class="px-6 py-3 font-semibold cursor-pointer" wire:click='sortBy("valid_from")'>
                                    Tanggal Terbit
                                    <span>{{ $sortDirection === 'asc' && $sortField === 'valid_from' ? '↑' : '↓'
                                        }}</span>
                                </th>
                                <th class="px-6 py-3 font-semibold cursor-pointer" wire:click='sortBy("valid_until")'>
                                    Berlaku Hingga
                                    <span>{{ $sortDirection === 'asc' && $sortField === 'valid_until' ? '↑' : '↓'
                                        }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-900">
                            @foreach ($storeDocuments as $document)
                            <tr class="hover:bg-gray-100">
                                <td class="px-6 py-4 hover:text-black cursor-pointer"
                                    wire:click="editModal('{{ $document->id }}')">
                                    {{ $document->document_name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $document->document_number }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $document->valid_from ?
                                    \Carbon\Carbon::parse($document->valid_from)->format('d/m/Y')
                                    : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $document->valid_until ?
                                    \Carbon\Carbon::parse($document->valid_until)->format('d/m/Y') : 'Tidak Terbatas' }}
                                </td>
                            </tr>
                            @endforeach
                            @if (!$storeDocuments)
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada dokumen legalitas yang tersedia.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-end gap-8 mt-4">
        <flux:button href="{{ route('pengaturan') }}" icon="x-mark">Batal</flux:button>
        <flux:button wire:click="updateStore" icon="save" variant="primary">Simpan</flux:button>
    </div>

    <flux:modal name="document" class="w-full max-w-md" wire:model="showModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $edit ? 'Rincian' : 'Tambah' }} Dokumen</flux:heading>
            </div>
            <div class="space-y-4">
                <div>
                    <label for="documentName" class="block text-sm font-medium text-gray-700">Nama Dokumen</label>
                    <input type="text" id="documentName" wire:model.lazy="documentName"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required />
                    @error('documentName')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="documentNumber" class="block text-sm font-medium text-gray-700">Nomor Dokumen</label>
                    <input type="text" id="documentNumber" wire:model.lazy="documentNumber"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                    @error('documentNumber')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="validFrom" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                    <input type="date" class="tanggal" onclick="this.showPicker()"
                        data-date="{{ $validFrom ? \Carbon\Carbon::parse($validFrom)->format('d/m/Y') : 'dd/mm/yyyy' }}"
                        wire:model.live="validFrom" id="validFrom" placeholder="dd/mm/yyyy" />
                </div>
                @error('validFrom')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror

                <div>
                    <label for="documentNumber" class="block text-sm font-medium text-gray-700">Tanggal Berlaku
                        Sampai</label>
                    <input type="date" class="tanggal" onclick="this.showPicker()"
                        data-date="{{ $validUntil ? \Carbon\Carbon::parse($validUntil)->format('d/m/Y') : 'dd/mm/yyyy' }}"
                        wire:model.live="validUntil" id="validUntil" placeholder="dd/mm/yyyy" />
                </div>
                <div class="mb-5 w-full">
                    <div class="flex flex-row items-center gap-4">
                        <label
                            class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2) w-1/4">
                            Pilih File
                            <input type="file" wire:model.live="documentFile"
                                accept="image/jpeg, image/png, image/jpg, application/pdf" class="hidden" />
                        </label>

                        @if ($documentFile)
                        <input type="text"
                            class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                            value="{{ is_string($documentFile) ? basename($documentFile) : $documentFile->getClientOriginalName() }}"
                            readonly wire:loading.remove wire:target="documentFile">
                        <input type="text"
                            class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                            value="Mengupload File..." readonly wire:loading wire:target="documentFile">
                        @else
                        <input type="text"
                            class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                            value="File Belum Dipilih" readonly wire:loading.remove wire:target="documentFile">
                        <input type="text"
                            class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                            value="Mengupload File..." readonly wire:loading wire:target="documentFile">
                        @endif

                    </div>
                </div>
                <flux:error name="documentFile" />
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                @if ($edit)
                <flux:modal.trigger name="delete-document" class="mr-4">
                    <flux:button variant="ghost" icon="trash" />
                </flux:modal.trigger>
                <flux:modal name="delete-document" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Hapus Dokumen</flux:heading>

                            <flux:text class="mt-2">
                                <p>Apakah Anda yakin ingin menghapus dokumen ini?</p>
                            </flux:text>
                        </div>

                        <div class="flex gap-2">
                            <flux:spacer />

                            <flux:modal.close>
                                <flux:button variant="ghost">Batal</flux:button>
                            </flux:modal.close>

                            <flux:button type="button" variant="danger" wire:click="delete">Hapus</flux:button>
                        </div>
                    </div>
                </flux:modal>
                @endif
                <flux:button type="button" icon="x-mark" wire:click="$set('showModal', false)">Batal
                </flux:button>
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
                    // Update preview image
                    let previewImg = document.getElementById('logo-image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'logo-image-preview';
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
    <script>
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
                    // Update preview image
                    let previewImg = document.getElementById('banner-image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'banner-image-preview';
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
    <script>
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
                    // Update preview image
                    let previewImg = document.getElementById('building-image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'building-image-preview';
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
    @section('css')
    <style>
        .tanggal {
            position: relative;
            width: 100%;
            height: 2.5rem;
            /* Sesuaikan tinggi input */
            padding: 0.5rem 2.5rem 0.5rem 0.75rem;
            /* Biar ada ruang untuk teks dan ikon */
            color: transparent;
            /* Sembunyikan teks aslinya */
            background-color: #f9fafb;
            /* gray-50 */
            border: 1px solid #d1d5db;
            /* gray-300 */
            border-radius: 0.5rem;
            /* rounded-lg */
            font-size: 0.875rem;
            /* text-sm */
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
            /* gray-900 */
            pointer-events: none;
            font-size: 0.875rem;
            /* text-sm */
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
            /* gray-500 */
            cursor: pointer;
        }
    </style>
    @endsection
</div>