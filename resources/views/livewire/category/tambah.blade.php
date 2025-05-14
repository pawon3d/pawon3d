<div>
    <div class="mb-4 flex items-center">
        <a href="{{ route('kategori') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
            <flux:icon.arrow-left variant="mini" class="mr-2" />
            Kembali
        </a>
        <h1 class="text-2xl">Tambah Kategori Produk</h1>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="exclamation-triangle" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">Form ini digunakan untuk mengelompokkan produk agar lebih mudah
                ditemukan tim kerja maupun pembeli, seperti "Kue Tradisional", "Kue Goreng", “Kue Manis” atau “Minuman”.
                Kemudian Anda dapat memilih apakah kategori ini dapat terlihat atau tidak oleh tim kerja maupun pembeli.
            </p>
        </div>
    </div>

    <div class="w-1/2 flex flex-col gap-4 mt-4">
        <flux:label>Nama Kategori</flux:label>
        <p class="text-sm text-gray-500">Masukkan nama kategori produk sesuai ciri khas profuk yang dijual, seperti "Kue
            Tradisional", "Kue Goreng", “Kue Manis” atau “Minuman”.</p>
        <flux:input placeholder="Masukkan nama kategori" wire:model.defer="name" />
        <flux:error name="name" />
        <flux:label>Tampil Kategori</flux:label>
        <p class="text-sm text-gray-500">
            Aktifkan opsi ini jika kategori produk ingin ditampilkan dan dapat digunakan oleh produk.
        </p>
        <flux:switch wire:model.live="is_active" class="{{ $is_active ? 'data-checked:bg-green-500' : '' }}" />


    </div>
    <div class="flex justify-end mt-8">
        <a href="{{ route('kategori') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="bookmark-square" type="button" variant="primary" wire:click.prevent="store">Simpan
        </flux:button>
    </div>
</div>