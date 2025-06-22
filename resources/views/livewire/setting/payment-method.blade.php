<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <a href="{{ route('pengaturan') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Metode Pembayaran</h1>
        </div>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Bibendum sit in habitant id. Quis aenean placerat aliquet
                laoreet ac arcu posuere leo in. Ultricies consequat quis sollicitudin etiam. Luctus feugiat ac orci
                netus dolor sapien.
            </p>
        </div>
    </div>

    <div class="flex justify-between items-center mt-8">
        <div class="p-4 flex items-end">
            <p class="text-lg text-gray-500">
                Daftar Metode
            </p>
        </div>
        <div class="flex gap-2 items-center">
            <flux:button type="button" variant="primary" wire:click="openModal" icon="plus">
                Tambah Metode
            </flux:button>
        </div>
    </div>

    <div class="bg-white rounded-xl border mt-4">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('bank_name')">Sumber Pembayaran
                            {{ $sortDirection === 'asc' && $sortField === 'bank_name' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('group')">
                            Metode Pembayaran
                            {{ $sortDirection === 'asc' && $sortField === 'group' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('account_number')">
                            Nomor Tujuan
                            {{ $sortDirection === 'asc' && $sortField === 'account_number' ? '↑' : '↓' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('account_name')">
                            Atas Nama
                            {{ $sortDirection === 'asc' && $sortField === 'account_name' ? '↑' : '↓' }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($paymentChannels as $channel)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 cursor-pointer"
                                wire:click="openModal(true,'{{ $channel->id }}')">
                                {{ $channel->bank_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                {{ $channel->group }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $channel->account_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $channel->account_name }}
                            </td>
                        </tr>
                    @endforeach
                    @if (!$paymentChannels)
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada metode pembayaran yang tersedia.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $paymentChannels->links() }}
        </div>
    </div>

    <flux:modal name="channel" class="w-full max-w-md" wire:model="showModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $edit ? 'Rincian' : 'Tambah' }} Metode Pembayaran</flux:heading>
            </div>
            <div class="space-y-4">
                <div>
                    <label for="group" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                    <flux:select name="group" wire:model.defer="group" id="group" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="tunai">Tunai</option>
                        <option value="transfer bank">Transfer Bank</option>
                        <option value="dompet digital">Dompet Digital</option>
                        <option value="qris">QRIS</option>
                    </flux:select>
                    @error('group')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="bankName" class="block text-sm font-medium text-gray-700">Sumber Pembayaran</label>
                    <input type="text" id="bankName" wire:model.lazy="bankName" placeholder="Contoh: BCA, Mandiri"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                    @error('bankName')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="accountNumber" class="block text-sm font-medium text-gray-700">Nomor Tujuan</label>
                    <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        wire:model.defer="accountNumber" id="accountNumber" placeholder="Nomor Rekening" />
                </div>
                @error('accountNumber')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror

                <div>
                    <label for="accountName" class="block text-sm font-medium text-gray-700">Atas Nama</label>
                    <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        wire:model.defer="accountName" id="accountName" placeholder="Contoh: Sulastri" />
                </div>
                <div class="mb-5 w-full">
                    <p class="mb-2 text-sm text-gray-500">
                        Unggah Gambar QRIS (Opsional)
                    </p>
                    <div class="flex flex-row items-center gap-4">
                        <label
                            class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2) w-1/4">
                            Pilih File
                            <input type="file" wire:model.live="qrisImage"
                                accept="image/jpeg, image/png, image/jpg, application/pdf" class="hidden" />
                        </label>

                        @if ($qrisImage)
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="{{ is_string($qrisImage) ? basename($qrisImage) : $qrisImage->getClientOriginalName() }}"
                                readonly wire:loading.remove wire:target="qrisImage">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="qrisImage">
                        @else
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="File Belum Dipilih" readonly wire:loading.remove wire:target="qrisImage">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="qrisImage">
                        @endif

                    </div>
                </div>
                <flux:error name="qrisImage" />
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                @if ($edit)
                    <flux:modal.trigger name="delete-channel" class="mr-4">
                        <flux:button variant="ghost" icon="trash" />
                    </flux:modal.trigger>
                    <flux:modal name="delete-channel" class="min-w-[22rem]">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">Hapus Metode Pembayaran</flux:heading>

                                <flux:text class="mt-2">
                                    <p>Apakah Anda yakin ingin menghapus metode pembayaran ini?</p>
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
                <flux:button type="button" icon="save" variant="primary" wire:click="save">Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
