<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('pengaturan') }}" wire:navigate
            class="px-6 py-2.5 bg-[#313131] rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold">
            <flux:icon.arrow-left class="size-5" />
            Kembali
        </a>
        <h1 class="text-xl font-semibold text-[#666666]">Metode Pembayaran</h1>
    </div>

    <!-- Info Box -->

    <x-alert.info>
        <p class="text-sm leading-relaxed">
            Metode Pembayaran. Pembayaran dapat dilakukan dengan cara tunai dan non tunai. Berbagai metode
            pembayaran dibuat untuk mempermudah proses pembayaran.
        </p>
    </x-alert.info>

    <!-- Table Section -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <p class="text-base font-medium text-[#666666]">Daftar Metode</p>
            <flux:button type="button" wire:click="openModal" icon="plus"
                class="!bg-[#74512D] hover:!bg-[#5d4024] !text-[#F8F4E1] !px-6 !py-2.5 !rounded-[15px] !font-semibold">
                Tambah Metode
            </flux:button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#3F4E4F]">
                        <th class="px-6 py-5 text-left text-sm font-bold text-[#F8F4E1] cursor-pointer"
                            wire:click="sortBy('bank_name')">
                            Sumber Pembayaran
                            @if ($sortField === 'bank_name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold text-[#F8F4E1] cursor-pointer"
                            wire:click="sortBy('type')">
                            Media Pembayaran
                            @if ($sortField === 'type')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold text-[#F8F4E1] cursor-pointer"
                            wire:click="sortBy('group')">
                            Metode Pembayaran
                            @if ($sortField === 'group')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold text-[#F8F4E1] cursor-pointer"
                            wire:click="sortBy('account_number')">
                            Nomor Tujuan
                            @if ($sortField === 'account_number')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-5 text-left text-sm font-bold text-[#F8F4E1] cursor-pointer"
                            wire:click="sortBy('account_name')">
                            Atas Nama
                            @if ($sortField === 'account_name')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paymentChannels as $channel)
                        <tr class="border-b border-[#d4d4d4] hover:bg-gray-50 cursor-pointer"
                            wire:click="openModal(true,'{{ $channel->id }}')">
                            <td class="px-6 py-4 text-sm font-medium text-[#666666]">
                                {{ $channel->bank_name }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-[#666666] capitalize">
                                {{ $channel->type }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-[#666666] capitalize">
                                {{ $channel->group }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-[#666666]">
                                {{ $channel->account_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-[#666666]">
                                {{ $channel->account_name ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-[#666666]">
                                Tidak ada metode pembayaran yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($paymentChannels->hasPages())
            <div class="mt-4">
                {{ $paymentChannels->links() }}
            </div>
        @endif
    </div>

    <!-- Modal -->
    <flux:modal name="channel" class="w-full max-w-lg" wire:model="showModal">
        <div class="space-y-6 p-6">
            <div>
                <flux:heading size="lg" class="text-[#333333]">
                    {{ $edit ? 'Rincian' : 'Tambah' }} Metode Pembayaran
                </flux:heading>
            </div>

            <div class="space-y-5">
                <!-- Sumber Pembayaran -->
                <div>
                    <label class="block text-sm font-medium text-[#666666] mb-2">Sumber Pembayaran</label>
                    <input type="text" wire:model.defer="bankName" placeholder="Contoh : Bank Mandiri (Mandiri)"
                        class="w-full px-4 py-2.5 border border-[#d4d4d4] rounded-[10px] text-sm text-[#333333] placeholder:text-[#adadad] focus:outline-none focus:ring-2 focus:ring-[#74512D]" />
                    @error('bankName')
                        <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-sm font-medium text-[#666666] mb-2">Metode Pembayaran</label>
                    <select wire:model.live="group"
                        class="w-full px-4 py-2.5 border border-[#d4d4d4] rounded-[10px] text-sm text-[#333333] focus:outline-none focus:ring-2 focus:ring-[#74512D]">
                        <option value="" hidden>Pilih Metode Pembayaran</option>
                        <option value="tunai">Tunai</option>
                        <option value="non-tunai">Non-Tunai</option>
                    </select>
                    @error('group')
                        <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Media Pembayaran -->
                <div>
                    <label class="block text-sm font-medium text-[#666666] mb-2">Media Pembayaran</label>
                    <select wire:model.defer="type"
                        class="w-full px-4 py-2.5 border border-[#d4d4d4] rounded-[10px] text-sm text-[#333333] focus:outline-none focus:ring-2 focus:ring-[#74512D]">
                        <option value="" hidden>Pilih Media Pembayaran</option>
                        @if ($this->group == 'tunai')
                            <option value="tunai">Tunai</option>
                        @else
                            <option value="transfer">Transfer Bank</option>
                            <option value="dompet digital">Dompet Digital</option>
                            <option value="qris">QRIS</option>
                        @endif
                    </select>
                    @error('type')
                        <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nomor Tujuan -->
                <div>
                    <label class="block text-sm font-medium text-[#666666] mb-2">Nomor Tujuan</label>
                    <input type="text" wire:model.defer="accountNumber" placeholder="098765432109"
                        class="w-full px-4 py-2.5 border border-[#d4d4d4] rounded-[10px] text-sm text-[#333333] placeholder:text-[#adadad] focus:outline-none focus:ring-2 focus:ring-[#74512D]" />
                    @error('accountNumber')
                        <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Atas Nama -->
                <div>
                    <label class="block text-sm font-medium text-[#666666] mb-2">Atas Nama</label>
                    <input type="text" wire:model.defer="accountName" placeholder="RARA"
                        class="w-full px-4 py-2.5 border border-[#d4d4d4] rounded-[10px] text-sm text-[#333333] placeholder:text-[#adadad] focus:outline-none focus:ring-2 focus:ring-[#74512D]" />
                    @error('accountName')
                        <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Upload File -->
                <div>
                    <label class="block text-sm font-medium text-[#666666] mb-2">Unggah File (Jika ada QR
                        Code)</label>
                    <div class="flex items-center gap-3">
                        <label
                            class="px-6 py-2.5 bg-[#74512D] hover:bg-[#5d4024] text-[#F8F4E1] rounded-[10px] font-semibold text-sm cursor-pointer inline-flex items-center gap-2">
                            Pilih File
                            <input type="file" wire:model="qrisImage" accept="image/*" class="hidden" />
                        </label>

                        <input type="text"
                            class="flex-1 px-4 py-2.5 border border-[#d4d4d4] rounded-[10px] text-sm text-[#666666] bg-[#fafafa]"
                            value="{{ $qrisImage ? (is_string($qrisImage) ? basename($qrisImage) : $qrisImage->getClientOriginalName()) : 'File Belum Dipilih' }}"
                            readonly wire:loading.remove wire:target="qrisImage">

                        <input type="text"
                            class="flex-1 px-4 py-2.5 border border-[#d4d4d4] rounded-[10px] text-sm text-[#666666] bg-[#fafafa]"
                            value="Mengupload File..." readonly wire:loading wire:target="qrisImage">

                        @if ($edit && $qrisImage)
                            <div class="flex gap-2">
                                <button type="button" wire:click="previewImage"
                                    class="p-2 bg-[#525252] hover:bg-[#404040] rounded-full">
                                    <flux:icon.eye class="size-5 text-white" />
                                </button>
                                <button type="button" wire:click="downloadImage"
                                    class="p-2 bg-[#27ae60] hover:bg-[#229954] rounded-full">
                                    <flux:icon.arrow-down-tray class="size-5 text-white" />
                                </button>
                                <button type="button" wire:click="deleteImage"
                                    class="p-2 bg-[#eb5757] hover:bg-[#c0392b] rounded-full">
                                    <flux:icon.trash class="size-5 text-white" />
                                </button>
                            </div>
                        @endif
                    </div>
                    @error('qrisImage')
                        <span class="text-sm text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-4">
                @if ($edit)
                    <button type="button" wire:click="confirmDelete"
                        class="p-2 bg-[#eb5757] hover:bg-[#c0392b] rounded-full">
                        <flux:icon.trash class="size-5 text-white" />
                    </button>
                @else
                    <div></div>
                @endif

                <div class="flex gap-3">
                    <flux:button type="button" icon="x-mark" wire:click="$set('showModal', false)"
                        class="!bg-[#adadad] hover:!bg-[#8c8c8c] !text-white !px-6 !py-2.5 !rounded-[10px] !font-semibold">
                        Batal
                    </flux:button>
                    <flux:button type="button" icon="check" wire:click="save"
                        class="!bg-[#3F4E4F] hover:!bg-[#2d3738] !text-[#F8F4E1] !px-6 !py-2.5 !rounded-[10px] !font-semibold">
                        Simpan
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>

    <!-- Image Preview Modal -->
    <flux:modal name="preview-image" class="w-full max-w-4xl" wire:model="showPreviewModal">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <flux:heading size="lg" class="text-[#333333]">Preview Gambar QRIS</flux:heading>
            </div>

            <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-center">
                @if ($previewImageUrl)
                    <img src="{{ $previewImageUrl }}" alt="QRIS Preview"
                        class="max-w-full max-h-[70vh] object-contain rounded">
                @endif
            </div>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-channel" class="w-full max-w-md" wire:model="showDeleteModal">
        <div class="space-y-6 p-6">
            <div>
                <flux:heading size="lg" class="text-[#333333]">Hapus Metode Pembayaran</flux:heading>
                <p class="mt-2 text-sm text-[#666666]">
                    Apakah Anda yakin ingin menghapus metode pembayaran ini?
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button type="button" wire:click="$set('showDeleteModal', false)"
                    class="!bg-[#adadad] hover:!bg-[#8c8c8c] !text-white !px-6 !py-2.5 !rounded-[10px] !font-semibold">
                    Batal
                </flux:button>
                <flux:button type="button" wire:click="delete"
                    class="!bg-[#eb5757] hover:!bg-[#c0392b] !text-white !px-6 !py-2.5 !rounded-[10px] !font-semibold">
                    Hapus
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
