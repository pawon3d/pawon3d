<div>
    <div class="flex items-end justify-between mb-7">
        <h1 class="text-3xl font-bold">Pengaturan Toko</h1>
    </div>
    <div class="space-y-6">
        <form wire:submit.prevent='save' class="space-y-4" enctype="multipart/form-data">

            <flux:input label="Nama Toko" placeholder="Nama Toko" type="text" wire:model="storeName" />

            <div class="flex flex-col md:flex-row gap-4 justify-between">
                <flux:input label="Gambar Halaman Depan" type="file" wire:model="heroImage" />
                @if ($previewHeroImage)
                <img src="{{ $previewHeroImage }}" alt="{{ $storeName }}" class="w-32 h-32 object-cover rounded-lg" />
                @endif
            </div>
            <flux:input label="Judul Besar" placeholder="Judul Besar" type="text" wire:model="heroTitle" />
            <flux:textarea label="Sub Judul" placeholder="Sub Judul" wire:model="heroSubtitle" />
            <flux:input label="Kontak" placeholder="Nomor Telepon" type="text" wire:model="contact" />
            <flux:textarea label="Alamat" placeholder="Alamat" wire:model="address" />
            <div class="flex flex-col md:flex-row gap-4 justify-between">
                <flux:input label="Logo" type="file" wire:model="logo" />
                @if ($previewLogo)
                <img src="{{ $previewLogo }}" alt="{{ $storeName }}" class="w-32 h-32 object-cover rounded-lg" />
                @endif
            </div>


            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </div>
</div>