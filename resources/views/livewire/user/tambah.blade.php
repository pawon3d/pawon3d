<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('user') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Pekerja</h1>
    </div>
    <div class="flex items-center bg-white shadow-lg rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Bibendum sit in habitant id. Quis aenean placerat aliquet
                laoreet ac arcu posuere leo in. Ultricies consequat quis sollicitudin etiam. Luctus feugiat ac orci
                netus dolor sapien.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-8 mt-4 bg-white p-4 rounded-lg shadow">
        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Unggah Foto</flux:label>
            <p class="text-sm text-gray-500">
                Pilih foto pekerja yang ingin diunggah dan sesuai dengan nama yang akan ditambah.
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
            <flux:label>Nama</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan nama lengkap sesuai nama di KTP.
            </p>
            <flux:input placeholder="Nama Lengkap" wire:model.defer="name" />
            <flux:error name="name" />
            <flux:label>Jenis Kelamin</flux:label>
            <p class="text-sm text-gray-500">
                Pilih jenis kelamin.
            </p>
            <flux:select placeholder="Pilih Jenis Kelamin" wire:model.defer="gender">
                <option value="" hidden>Pilih Jenis Kelamin</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </flux:select>
            <flux:error name="gender" />
            <flux:label>Email</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan alamat email aktif.
            </p>
            <flux:input placeholder="namaemail@gmail.com" wire:model.defer="email" />
            <flux:error name="email" />
            <flux:callout icon="information-circle" color="blue" class="mt-2">
                <flux:callout.heading>Aktivasi via Email</flux:callout.heading>
                <flux:callout.text>
                    Setelah disimpan, sistem akan mengirim email undangan ke alamat ini.
                    Pekerja akan mengatur kata sandi sendiri melalui tautan di email tersebut.
                </flux:callout.text>
            </flux:callout>
            <flux:label>Nomor Telepon</flux:label>
            <p class="text-sm text-gray-500">
                Masukkan nomor telepon aktif (WhatsApp).
            </p>
            <flux:input placeholder="08xxxxxx" wire:model.defer="phone" />
            <flux:error name="phone" />
            <flux:label>Peran</flux:label>
            <p class="text-sm text-gray-500">
                Pilih peran pekerja yang akan ditambahkan.
            </p>
            <flux:select placeholder="Pilih Peran" wire:model.defer="role">
                <option value="" hidden>Pilih Peran</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </flux:select>
            <flux:error name="role" />

        </div>
    </div>

    <div class="flex justify-end gap-2 mt-4">
        <flux:button href="{{ route('user') }}" icon="x-mark">Batal</flux:button>
        <flux:button wire:click="createUser" icon="save" variant="primary">Simpan</flux:button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('pin-inputs');
            if (!container) return;

            const inputs = container.querySelectorAll('input[type="text"], input[type="password"]');
            inputs.forEach((el, idx, arr) => {
                el.addEventListener('input', () => {
                    if (el.value.length === 1 && idx < arr.length - 1) {
                        arr[idx + 1].focus();
                    }
                });

                el.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && el.value === '' && idx > 0) {
                        arr[idx - 1].focus();
                    }
                });
            });
        });
    </script>


</div>
