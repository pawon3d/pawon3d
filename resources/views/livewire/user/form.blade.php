<div>
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('user') }}" wire:navigate
                class="bg-[#313131] hover:bg-[#252324] px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold text-base transition-colors"
                wire:navigate>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
            <h1 class="text-xl font-semibold text-[#666666]">
                {{ $this->isEditMode() ? 'Rincian Pekerja' : 'Tambah Pekerja' }}
            </h1>
        </div>
        @if ($this->isEditMode())
            <div class="flex gap-2.5">
                <flux:button variant="secondary" wire:click="riwayatPembaruan">
                    Riwayat Pembaruan
                </flux:button>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <x-alert.info>
        @if ($this->isEditMode())
            Anda dapat mengubah atau menghapus pekerja. Sesuaikan informasi jika terdapat perubahan, pastikan informasi
            yang dimasukan benar dan tepat. Informasi akan ditampilkan untuk mengetahui data diri pekerja dan kegiatan
            yang dilakukan.
        @else
            Tambah Pekerja. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan tepat.
            Informasi akan ditampilkan untuk mengetahui data diri pekerja dan kegiatan yang dilakukan.
        @endif
    </x-alert.info>

    <!-- Main Form Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-8 py-6 mt-5">
        <div class="flex flex-col lg:flex-row gap-[130px]">
            <!-- Left Column - Photo Upload -->
            <div class="flex flex-col gap-4 min-w-[300px]">
                <div class="flex flex-col gap-4">
                    <p class="text-base font-medium text-[#666666]">
                        {{ $this->isEditMode() ? 'Unggah Foto' : 'Foto Pekerja' }}
                    </p>
                    <p class="text-sm font-normal text-[#666666]">
                        Pilih foto pekerja yang ingin diunggah.
                    </p>
                </div>

                <div class="flex flex-col gap-5 items-start">
                    <!-- Dropzone Area -->
                    <div class="relative w-[300px] h-[170px] border border-dashed border-black rounded-[15px] overflow-hidden"
                        ondragover="event.preventDefault(); this.classList.add('border-[#74512d]');"
                        ondragleave="this.classList.remove('border-[#74512d]');" ondrop="handleDrop(event)"
                        id="dropzone-container">

                        <label for="dropzone-file"
                            class="w-full h-full cursor-pointer flex items-center justify-center">
                            <div id="preview-container" class="w-full h-full">
                                @if ($previewImage)
                                    <!-- Image Preview -->
                                    <img src="{{ $previewImage }}" alt="Preview" class="object-cover w-full h-full"
                                        id="image-preview" />
                                @else
                                    <!-- Default Content -->
                                    <div class="flex flex-col items-center justify-center p-5 text-center h-full">
                                        <svg class="w-[22px] h-[22px] mb-2.5 text-[#666666]" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="text-base font-medium text-[#666666] mb-2">Unggah Foto</p>
                                        <p class="text-sm text-[#666666]">
                                            Ukuran foto tidak lebih dari <span class="font-bold">2mb</span>
                                        </p>
                                        <p class="text-sm text-[#666666]">
                                            (<span class="font-bold">JPG</span> atau <span class="font-bold">PNG</span>)
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
                        class="w-[300px] bg-[#74512d] hover:bg-[#5d4024] text-[#f6f6f6] font-semibold text-base px-6 py-2.5 rounded-[15px] shadow-sm transition-colors cursor-pointer">
                        Pilih Foto
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
            </div>

            <!-- Right Column - Form Fields -->
            <div class="flex flex-col gap-[30px] flex-1">
                <!-- Nama -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">Nama</p>
                        <p class="text-sm font-normal text-[#666666]">
                            Masukkan nama lengkap sesuai nama di KTP.
                        </p>
                    </div>
                    <input type="text" wire:model="name" placeholder="Contoh: Ruru"
                        class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] placeholder:text-[#959595] focus:outline-none focus:border-[#666666]" />
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">Jenis Kelamin</p>
                        <p class="text-sm font-normal text-[#666666]">
                            Pilih jenis kelamin.
                        </p>
                    </div>
                    <select wire:model="gender"
                        class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] focus:outline-none focus:border-[#666666] appearance-none cursor-pointer">
                        <option value="" class="text-[#959595]">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    @error('gender')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">Email</p>
                        <p class="text-sm font-normal text-[#666666]">
                            Masukkan email aktif.
                        </p>
                    </div>
                    <input type="email" wire:model="email" placeholder="Contoh: namaemail@gmail.com"
                        @if ($this->isEditMode()) disabled @endif
                        class="w-full px-5 py-2.5 {{ $this->isEditMode() ? 'bg-[#eaeaea]' : 'bg-[#fafafa]' }} border border-[#d4d4d4] rounded-[15px] text-base text-[#666666] placeholder:text-[#959595] focus:outline-none focus:border-[#666666] disabled:cursor-not-allowed" />
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Kata Sandi -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">Kata Sandi</p>
                        <p class="text-sm font-normal text-[#666666]">
                            Masukkan kata sandi dengan kombinasi huruf dan angka (minimal 8 karakter)
                        </p>
                    </div>
                    <div class="relative" x-data="{ showPassword: false }">
                        <input :type="showPassword ? 'text' : 'password'" wire:model.live="password"
                            placeholder="{{ $this->isEditMode() ? '********' : 'Contoh: katas123' }}"
                            class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] placeholder:text-[#959595] focus:outline-none focus:border-[#666666] pr-12" />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[#666666]">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- No. Telepon -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">No. Telepon</p>
                        <p class="text-sm font-normal text-[#666666]">
                            Masukkan nomor telepon aktif (WhatsApp).
                        </p>
                    </div>
                    <input type="text" wire:model="phone" placeholder="Contoh: 081122334455"
                        class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] placeholder:text-[#959595] focus:outline-none focus:border-[#666666]" />
                    @error('phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Peran -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">Peran</p>
                        <p class="text-sm font-normal text-[#666666]">
                            {{ $this->isEditMode() ? 'Pilih peran yang akan dijalankan.' : 'Masukkan nomor telepon aktif (WhatsApp).' }}
                        </p>
                    </div>
                    <select wire:model="role"
                        class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] focus:outline-none focus:border-[#666666] appearance-none cursor-pointer">
                        <option value="" class="text-[#959595]">Pilih Peran</option>
                        @foreach ($roles as $roleItem)
                            <option value="{{ $roleItem->name }}">{{ $roleItem->name }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if ($this->isEditMode())
                    <!-- Status Pekerja -->
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-4">
                            <p class="text-base font-medium text-[#666666]">Status Pekerja</p>
                            <p class="text-sm font-normal text-[#666666]">
                                Pilih status pekerja seperti <span class="font-bold">Aktif</span> atau <span
                                    class="font-bold">Nonaktif/Dinonaktifkan.</span> Aktif jika pekerja masih bekerja
                                dan
                                nonaktif jika pekerja bermasalah, berhenti atau keluar dari pekerjaan.
                            </p>
                        </div>
                        <select wire:model="is_active"
                            class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] focus:outline-none focus:border-[#666666] appearance-none cursor-pointer">
                            <option value="" class="text-[#959595]">Pilih Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif/Dinonaktifkan</option>
                        </select>
                        @error('is_active')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex {{ $this->isEditMode() ? 'justify-between' : 'justify-end' }} items-center mt-12 gap-8">
        @if ($this->isEditMode())
            <!-- Delete Button (Only in Edit Mode) -->
            <button type="button" wire:click="confirmDelete"
                class="bg-[#eb5757] hover:bg-[#d64545] text-[#f8f4e1] font-semibold text-base px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                Hapus Pekerja
            </button>
        @endif

        <div class="flex gap-2.5 items-center">
            @if (!$this->isEditMode())
                <!-- Cancel Button (Only in Add Mode) -->
                <a href="{{ route('user') }}"
                    class="bg-[#c4c4c4] hover:bg-[#b0b0b0] text-[#333333] font-semibold text-base px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 transition-colors cursor-pointer"
                    wire:navigate>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Batal
                </a>
            @endif

            <!-- Save Button -->
            <flux:button type="button" variant="secondary" icon="save" wire:click="save">
                {{ $this->isEditMode() ? 'Simpan Perubahan' : 'Simpan' }}
            </flux:button>
        </div>
    </div>

    <!-- Modal Riwayat Pembaruan -->
    @if ($this->isEditMode())
        <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Riwayat Pembaruan Pekerja</flux:heading>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @forelse ($activityLogs as $log)
                        <div class="border-b py-3">
                            <div class="flex justify-between items-start">
                                <div class="text-sm font-medium text-[#666666]">{{ $log->description }}</div>
                                <div class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Oleh: {{ $log->causer->name ?? 'System' }}
                            </div>

                            {{-- Show changes for update event --}}
                            @if ($log->event === 'updated' && $log->properties->has('old') && $log->properties->has('attributes'))
                                <div class="mt-2 text-xs bg-gray-50 rounded-lg p-2">
                                    <div class="font-medium text-[#666666] mb-1">Perubahan:</div>
                                    @php
                                        $old = $log->properties->get('old', []);
                                        $new = $log->properties->get('attributes', []);
                                        $fieldLabels = [
                                            'name' => 'Nama',
                                            'email' => 'Email',
                                            'phone' => 'No. Telepon',
                                            'gender' => 'Jenis Kelamin',
                                            'image' => 'Foto',
                                            'password' => 'Kata Sandi',
                                            'role' => 'Peran',
                                            'is_active' => 'Status Aktif',
                                        ];
                                    @endphp
                                    @foreach ($new as $field => $newValue)
                                        @if (isset($fieldLabels[$field]))
                                            <div class="flex items-start gap-2 py-1">
                                                <span
                                                    class="font-medium text-[#666666] min-w-[100px]">{{ $fieldLabels[$field] }}:</span>
                                                @if ($field === 'password')
                                                    <span class="text-gray-500">Kata sandi diperbarui</span>
                                                @elseif ($field === 'image')
                                                    <span class="text-gray-500">Foto diperbarui</span>
                                                @else
                                                    <span
                                                        class="text-red-500 line-through">{{ $old[$field] ?? '-' }}</span>
                                                    <span class="text-gray-400">→</span>
                                                    <span class="text-green-600">{{ $newValue ?? '-' }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">
                            Belum ada riwayat pembaruan.
                        </div>
                    @endforelse
                </div>
            </div>
        </flux:modal>
    @endif
</div>

@script
    <script>
        function handleDrop(event) {
            event.preventDefault();
            const container = event.currentTarget;
            container.classList.remove('border-[#74512d]');

            const files = event.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('dropzone-file');
                input.files = files;
                previewImage(input);
                input.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            }
        }

        function previewImage(input) {
            const previewContainer = document.getElementById('preview-container');
            const defaultContent = previewContainer.querySelector('.flex-col');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    let previewImg = document.getElementById('image-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'image-preview';
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
@endscript
