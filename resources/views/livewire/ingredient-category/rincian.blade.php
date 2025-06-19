<div>
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('kategori-persediaan') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Rincian Kategori Persediaan</h1>
        </div>
        <div class="flex gap-2 items-center">
            <flux:button variant="filled" wire:click="riwayatPembaruan">Riwayat Pembaruan</flux:button>
        </div>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">Form ini digunakan untuk mengubah nama kategori dan status tampil
                kategori. Kemudian Anda dapat melihat jumlah produk yang menggunakan kategori sebelum melakukan
                perubahan atau bahkan penghapusan.
            </p>
        </div>
    </div>

    <div class="w-full flex md:flex-row flex-col gap-8 mt-4">
        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Nama Kategori</flux:label>
            <p class="text-sm text-gray-500">Masukkan nama kategori persediaan sesuai ciri khas bahan yang digunakan,
                seperti "Bahan Kering", "Bahan Cair", “Bahan Pengembang” atau “Bahan Setengah Jadi”.</p>
            <flux:input placeholder="Masukkan nama kategori" wire:model.defer="name" />
            <flux:error name="name" />
            <flux:label>Tampil Kategori</flux:label>
            <p class="text-sm text-gray-500">
                Aktifkan opsi ini jika kategori persediaan ingin ditampilkan dan dapat digunakan oleh bahan.
            </p>
            <flux:switch wire:model.live="is_active" :checked="$is_active ? true : false"
                class="{{ $is_active ? 'data-checked:bg-green-500' : '' }}" />
        </div>
        <div class="md:w-1/2 flex flex-col gap-4 mt-4">
            <flux:label>Jumlah Produk</flux:label>
            <p class="text-sm text-gray-500">Jumlah bahan yang berada di dalam kategori yang sama.</p>
            <flux:input wire:model="products" readonly class="bg-gray-50" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-4 mt-8">
        <flux:button icon="trash" type="button" variant="danger" wire:click="confirmDelete()">
        </flux:button>
        <a href="{{ route('kategori-persediaan') }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="bookmark-square" type="button" variant="primary" wire:click.prevent="update">Simpan
        </flux:button>
    </div>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Kategori</flux:heading>
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
</div>
