<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Daftar Pelanggan</h1>
        <div class="flex gap-2 items-center">
            <!-- Tombol Riwayat Pembaruan -->
            <button type="button" wire:click="riwayatPembaruan"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Riwayat Pembaruan
            </button>
        </div>
    </div>

    <div class="flex items-center bg-white shadow-lg rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. In semper nisi proin malesuada. Vehicula vestibulum consequat
                volutpat vel sagittis mi interdum. Tellus egestas lorem arcu sed auctor vestibulum mauris id fames. Amet
                enim magna mi nisl magna.
            </p>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 shadow mt-4">
        <div class="flex justify-between items-center">
            <!-- Search Input -->
            <div class="p-4 flex">
                <input wire:model.live="search" placeholder="Cari pekerja..."
                    class="w-lg px-4 py-2 border border-accent rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <flux:button :loading="false" class="ml-2" variant="ghost">
                    <flux:icon.funnel variant="mini" />
                    <span>Filter</span>
                </flux:button>
            </div>
            <div class="flex gap-2 items-center">
                <flux:button icon="plus" variant="primary" wire:click="showModalTambah" type="button">
                    Tambah Pelanggan
                </flux:button>
            </div>
        </div>
        <div class="flex justify-between items-center mb-2">
            <div class="p-4 flex">
                <flux:dropdown>
                    <flux:button variant="ghost">
                        @if ($filterStatus)
                            {{ $filterStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                        @else
                            Semua Pelanggan
                        @endif
                        ({{ $customers->total() }})
                        <flux:icon.chevron-down variant="mini" />
                    </flux:button>
                    <flux:menu>
                        <flux:menu.radio.group wire:model.live="filterStatus">
                            <flux:menu.radio value="">Semua Kategori</flux:menu.radio>
                            <flux:menu.radio value="aktif">Aktif</flux:menu.radio>
                            <flux:menu.radio value="nonaktif">Tidak Aktif</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>
            </div>
            <div class="flex gap-2 items-center">
                <flux:dropdown>
                    <flux:button variant="ghost">
                        Urutkan Pelanggan
                        <flux:icon.chevron-down variant="mini" />

                    </flux:button>

                    <flux:menu>
                        <flux:menu.radio.group wire:model="sortByCategory">
                            <flux:menu.radio value="name">Nama</flux:menu.radio>
                            <flux:menu.radio value="status">Status</flux:menu.radio>
                            <flux:menu.radio value="product" checked>Jenis Produk</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>

        <div class="bg-white rounded-xl border-0">
            <!-- Table -->
            <div class="overflow-x-auto rounded-xl">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('phone')">
                                Nomor Telepon
                                {{ $sortDirection === 'asc' && $sortField === 'phone' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('name')">
                                Nama Pelanggan
                                {{ $sortDirection === 'asc' && $sortField === 'name' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('lastTransaction')">
                                Transaksi Terbaru
                                {{ $sortDirection === 'asc' && $sortField === 'lastTransaction' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('totalTransaction')">
                                Total Transaksi
                                <span>{{ $sortField === 'totalTransaction' && $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('points')">
                                Saldo Poin
                                <span>{{ $sortField === 'points' && $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($customers as $customer)
                            <tr wire:click="showCustomerDetail('{{ $customer->id }}')"
                                class="hover:bg-gray-100 cursor-pointer transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $customer->phone }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $customer->name }}
                                </td>
                                <td class="px-6 py-4 text-left space-x-2 whitespace-nowrap">
                                    {{ $customer->transactions()->latest()->first()
                                        ? \Carbon\Carbon::parse($customer->transactions()->latest()->first()->created_at)->translatedFormat('d
                                                                    F Y H:i')
                                        : '-' }}
                                </td>
                                <td class="px-6 py-4 text-left space-x-2 whitespace-nowrap">
                                    {{ $customer->transactions()->count() ?? 0 }}
                                </td>
                                <td class="px-6 py-4 text-left space-x-2 whitespace-nowrap">
                                    {{ $customer->points ?? 0 }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Pelanggan</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach ($activityLogs as $log)
                    <div class="border-b py-2">
                        <div class="text-sm font-medium">{{ $log->description }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $log->causer->name ?? 'System' }} -
                            {{ $log->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </flux:modal>

    <flux:modal name="tambah-customer" class="w-full max-w-md" wire:model="customerModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Pelanggan</flux:heading>
            </div>
            <div class="space-y-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" id="phone" wire:model="phone" placeholder="Contoh: 08123456789"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required />
                    @error('phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="name" wire:model="name" placeholder="Contoh: Fani"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required />
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button type="button" icon="x-mark">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="button" icon="save" variant="primary" wire:click="addCustomer">Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="rincian-customer" class="w-full max-w-2xl" wire:model="customerDetailModal">
        <div class="space-y-6 mt-8">
            <div class="flex items-center justify-between flex-row">
                <flux:heading size="lg">Rincian Pelanggan</flux:heading>
                <flux:button icon="plus" variant="primary" wire:click="showModalTambahPoin" type="button">

                    Tambah Poin
                </flux:button>
            </div>
            <div class="space-y-4 flex md:flex-row flex-col gap-4">
                <div class="flex flex-col gap-4 md:w-1/2 w-full">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" id="phone" wire:model="phone" placeholder="Contoh: 08123456789"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required />
                        @error('phone')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" id="name" wire:model="name" placeholder="Contoh: Fani"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required />
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-xs text-gray-500">Transaksi Terbaru</p>
                        <p class="text-xs text-gray-500">
                            {{ $lastTransaction
                                ? \Carbon\Carbon::parse($lastTransaction)->translatedFormat('d
                                                        F Y H:i')
                                : '-' }}
                        </p>
                    </div>
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-xs text-gray-500">Total Transaksi</p>
                        <p class="text-xs text-gray-500">{{ $totalTransaction ?? 0 }}</p>
                    </div>
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-xs text-gray-500">Total Pembayaran</p>
                        <p class="text-xs text-gray-500">Rp0</p>
                    </div>
                    <div class="flex flex-row justify-between items-center">
                        <p class="text-xs text-gray-500">Saldo Poin</p>
                        <p class="text-xs text-gray-500">0</p>
                    </div>
                </div>
                <div class="flex flex-col gap-4 md:w-1/2 w-full">
                    <flux:label>Riwayat Poin</flux:label>
                    <div class="overflow-y-auto max-h-64 bg-white rounded-lg shadow p-4">
                        @if (!$histories)
                            <p class="text-gray-500 text-sm">Tidak ada riwayat poin.</p>
                        @else
                            <div class="bg-white rounded-xl border-0">
                                <!-- Table -->
                                <div class="overflow-x-auto overflow-y-auto rounded-xl">
                                    <table class="w-full text-xs text-gray-500">
                                        <thead class="bg-gray-200">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider cursor-pointer whitespace-nowrap"
                                                    wire:click="sortBy('action_id')">
                                                    ID Aksi
                                                    {{ $sortDirection === 'asc' && $sortField === 'action_id' ? '↑' : '↓' }}
                                                </th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider cursor-pointer whitespace-nowrap"
                                                    wire:click="sortBy('action')">
                                                    Aksi
                                                    {{ $sortDirection === 'asc' && $sortField === 'action' ? '↑' : '↓' }}
                                                </th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 tracking-wider cursor-pointer whitespace-nowrap"
                                                    wire:click="sortBy('points')">
                                                    Poin
                                                    {{ $sortDirection === 'asc' && $sortField === 'points' ? '↑' : '↓' }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($histories as $history)
                                                <tr>
                                                    <td class="px-3 py-2 whitespace-nowrap">{{ $history->action_id }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap">{{ $history->action }}
                                                    </td>
                                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                                        {{ $history->points ?? 0 }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3"
                                                        class="px-3 py-2 text-center text-sm text-gray-500">Tidak
                                                        ada data.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2 gap-4">
                <flux:modal.trigger name="delete-category" class="mr-4">
                    <flux:button variant="ghost" icon="trash" />
                </flux:modal.trigger>

                <flux:modal name="delete-category" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Hapus Pelanggan</flux:heading>

                            <flux:text class="mt-2">
                                <p>Apakah Anda yakin ingin menghapus pelanggan ini?</p>
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
                <flux:modal.close>
                    <flux:button type="button" icon="x-mark">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="button" icon="save" variant="primary" wire:click="update">Simpan
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="tambah-poin" class="w-full max-w-md" wire:model="addPointsModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Poin</flux:heading>
            </div>
            <div class="space-y-4">
                <div class="mb-5 w-full">
                    <flux:label class="mb-2">Bukti Story Instagram (5 Poin)</flux:label>
                    <div class="flex flex-row items-center gap-4 mt-3">
                        <label
                            class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2) w-1/4 text-xs text-center">
                            Unggah Bukti
                            <input type="file" wire:model.live="ig_image"
                                accept="image/jpeg, image/png, image/jpg" class="hidden" />
                        </label>

                        @if ($ig_image)
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="{{ is_string($ig_image) ? basename($ig_image) : $ig_image->getClientOriginalName() }}"
                                readonly wire:loading.remove wire:target="ig_image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="ig_image">
                        @else
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="File Belum Dipilih" readonly wire:loading.remove wire:target="ig_image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="ig_image">
                        @endif
                    </div>
                </div>
                <flux:error name="ig_image" />
                <div class="mb-5 w-full">
                    <flux:label class="mb-2">Bukti Rating Gmaps (10 Poin)</flux:label>
                    <div class="flex flex-row items-center gap-4 mt-3">
                        <label
                            class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2) w-1/4 text-xs text-center">
                            Unggah Bukti
                            <input type="file" wire:model.live="gmaps_image"
                                accept="image/jpeg, image/png, image/jpg" class="hidden" />
                        </label>

                        @if ($gmaps_image)
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="{{ is_string($gmaps_image) ? basename($gmaps_image) : $gmaps_image->getClientOriginalName() }}"
                                readonly wire:loading.remove wire:target="gmaps_image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="gmaps_image">
                        @else
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="File Belum Dipilih" readonly wire:loading.remove wire:target="gmaps_image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="gmaps_image">
                        @endif
                    </div>
                </div>
                <flux:error name="gmaps_image" />
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button type="button" icon="x-mark">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="button" icon="arrow-up-tray" variant="primary" wire:click="addPoints">Unggah
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
