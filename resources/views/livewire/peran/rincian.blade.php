<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('role') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Rincian Peran</h1>
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
    <div class="w-full mt-8 mb-4 flex flex-col gap-4">
        <flux:input type="text" wire:model.defer='roleName' placeholder="Nama Peran" label="Nama Peran" />

        <flux:label>Pilih Hak Akses</flux:label>
        <p class="text-sm text-gray-500">
            Aktifkan satu atau beberapa hak akses untuk menampilkan dan memilih hak yang ingin diberikan.
        </p>

        <div class="flex items-center justify-between w-full flex-row gap-4">
            <label class="flex flex-col mb-5 gap-4 cursor-pointer w-full">
                <div class="flex flex-row items-center justify-between w-full">
                    <span class="text-lg font-medium">Inventori</span>
                    <input type="checkbox" value="Inventori" wire:model="permissions" class="sr-only peer">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300  rounded-full peer  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all  peer-checked:bg-green-500">
                    </div>
                </div>
                <span class="text-sm text-gray-500">Bagian yang mengelola barang persediaan.</span>
            </label>
        </div>
        <div class="flex items-center justify-between w-full flex-row gap-4">
            <label class="flex flex-col mb-5 gap-4 cursor-pointer w-full">
                <div class="flex flex-row items-center justify-between w-full">
                    <span class="text-lg font-medium">Produksi</span>
                    <input type="checkbox" value="Produksi" wire:model="permissions" class="sr-only peer">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300  rounded-full peer  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all  peer-checked:bg-green-500">
                    </div>
                </div>
                <span class="text-sm text-gray-500">Bagian yang mengelola produksi produk.</span>
            </label>
        </div>
        <div class="flex items-center justify-between w-full flex-row gap-4">
            <label class="flex flex-col mb-5 gap-4 cursor-pointer w-full">
                <div class="flex flex-row items-center justify-between w-full">
                    <span class="text-lg font-medium">Kasir</span>
                    <input type="checkbox" value="Kasir" wire:model="permissions" class="sr-only peer">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300  rounded-full peer  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all  peer-checked:bg-green-500">
                    </div>
                </div>
                <span class="text-sm text-gray-500">Bagian yang melayani transaksi penjualan kepada pelanggan.</span>
            </label>
        </div>
        <div class="flex items-center justify-between w-full flex-row gap-4">
            <label class="flex flex-col mb-5 gap-4 cursor-pointer w-full">
                <div class="flex flex-row items-center justify-between w-full">
                    <span class="text-lg font-medium">Manajemen Sistem</span>
                    <input type="checkbox" value="Manajemen Sistem" wire:model="permissions" class="sr-only peer">
                    <div
                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300  rounded-full peer  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all  peer-checked:bg-green-500">
                    </div>
                </div>
                <span class="text-sm text-gray-500">Pengelola utama sistem yang bertanggung jawab atas seluruh
                    operasional.</span>
            </label>
        </div>
    </div>

    <div class="flex flex-col gap-4 mt-4 w-full">
        <flux:label>Daftar Pekerja</flux:label>
        <p class="text-sm text-gray-500">
            Daftar pekerja yang memiliki peran ini. Anda dapat mengelola pekerja di halaman Pekerja.
        </p>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pekerja
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nomor Telepon
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->phone ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center px-6 py-4">Tidak ada pekerja yang memiliki peran ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end gap-8 mt-4">
        {{-- delete button --}}
        <flux:button wire:click="deleteRole" icon="trash" variant="danger" />
        <flux:button href="{{ route('role') }}" icon="x-mark">Batal</flux:button>
        <flux:button wire:click="updateRole" icon="archive-box" variant="primary">Simpan</flux:button>
    </div>

</div>
