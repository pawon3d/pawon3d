<div>
    <div class="mb-4 flex items-center gap-4">
        <a href="{{ route('pengaturan') }}"
            class="px-6 py-2 bg-[#313131] rounded-[15px] flex items-center text-white shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] hover:bg-[#252324]">
            <flux:icon.arrow-left variant="mini" class="mr-2 size-5" />
            Kembali
        </a>
        <h1 class="text-xl font-semibold text-[#666666]">Profil Anda</h1>
    </div>


    <x-alert.info>
        <p class="text-sm font-semibold leading-normal">
            Lorem ipsum dolor sit amet consectetur. Sed pharetra netus gravida non curabitur fermentum etiam. Lorem
            orci auctor adipiscing vel blandit. In in integer viverra proin risus eu eleifend.
        </p>
    </x-alert.info>

    <div
        class="w-full flex md:flex-row flex-col gap-[130px] bg-[#fafafa] p-[30px] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
        <div class="flex flex-col gap-4 min-w-[300px]">
            <flux:label class="text-[#666666] text-base font-medium">Foto Profil</flux:label>

            <div class="flex flex-col items-center w-full space-y-5">
                <!-- Dropzone Area -->
                <div class="relative w-[300px] h-[170px] border-2 border-dashed border-black rounded-[15px] bg-[#fafafa] hover:bg-gray-50 transition-colors duration-200 overflow-hidden"
                    wire:ignore
                    ondragover="event.preventDefault(); this.classList.add('border-[#74512d]', 'bg-gray-50');"
                    ondragleave="this.classList.remove('border-[#74512d]', 'bg-gray-50');" ondrop="handleDrop(event)"
                    id="dropzone-container">

                    <label for="dropzone-file" class="w-full h-full cursor-pointer flex items-center justify-center">
                        <div id="preview-container" class="w-full h-full">
                            @if ($previewImage)
                                <!-- Image Preview -->
                                <img src="{{ $previewImage }}" alt="Preview"
                                    class="object-cover w-full h-full rounded-[15px]" id="image-preview" />
                            @else
                                <!-- Default Content -->
                                <div class="flex flex-col items-center justify-center p-4 text-center h-full">
                                    <flux:icon icon="arrow-up-tray" class="w-8 h-8 mb-2 text-gray-400" />
                                    <p class="mb-2 text-lg font-semibold text-gray-600">Unggah Gambar</p>
                                    <p class="mb-2 text-xs text-gray-600">
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
                    class="w-full bg-[#74512d] hover:bg-[#5d3e1f] text-white rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
                    Pilih Foto
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


        <div class="flex-1 flex flex-col gap-[30px] min-w-[370px]">
            <div class="flex flex-col gap-4">
                <flux:label class="text-[#666666] text-base font-medium">Nama</flux:label>
                <flux:input placeholder="Nama Lengkap" wire:model.defer="name" disabled />
                <flux:error name="name" />
            </div>

            <div class="flex flex-col gap-4">
                <flux:label class="text-[#666666] text-base font-medium">Jenis Kelamin</flux:label>
                <flux:select wire:model.defer="gender" disabled>
                    <option value="" disabled>Pilih Jenis Kelamin</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </flux:select>
            </div>

            <div class="flex flex-col gap-4">
                <flux:label class="text-[#666666] text-base font-medium">Email</flux:label>
                <flux:input placeholder="namaemail@gmail.com" wire:model.defer="email" disabled />
                <flux:error name="email" />
            </div>

            <div class="flex flex-col gap-4">
                <flux:label class="text-[#666666] text-base font-medium">No. Telepon</flux:label>
                <flux:input placeholder="08xxxxxx" wire:model.defer="phone" disabled />
                <flux:error name="phone" />
            </div>

            <div class="flex flex-col gap-4">
                <flux:label class="text-[#666666] text-base font-medium">Kata Sandi</flux:label>
                <flux:input placeholder="Password" wire:model="password" type="password" viewable />
                <flux:error name="password" />
            </div>

            <div class="flex flex-col gap-4">
                <flux:label class="text-[#666666] text-base font-medium">Peran</flux:label>
                <flux:input placeholder="Pilih Peran" wire:model.defer="role" disabled />
                <flux:error name="role" />
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-[50px]">
        <flux:button wire:click="updateUser" variant="primary" icon="bookmark">
            Simpan Perubahan
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
