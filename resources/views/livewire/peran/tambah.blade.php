<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('role') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Peran</h1>
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

    <div class="flex justify-end gap-2 mt-4">
        <flux:button href="{{ route('role') }}" icon="x-mark">Batal</flux:button>
        <flux:button wire:click="createRole" icon="archive-box" variant="primary">Simpan</flux:button>
    </div>

</div>
