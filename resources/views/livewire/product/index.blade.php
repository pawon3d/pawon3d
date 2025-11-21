<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Kelola Produk</h1>
        <div class="flex gap-3">
            <button type="button" wire:click="riwayatPembaruan"
                class="bg-[#525252] border border-[#666666] text-white px-6 py-2.5 rounded-[15px] hover:bg-[#666666] transition-colors">
                <span class="font-montserrat font-medium text-[14px]">Riwayat Pembaruan</span>
            </button>
        </div>
    </div>

    <x-alert.info class="space-y-3">
        <p class="font-semibold text-[14px] leading-[1.4]">
            Pilih salah satu metode penjualan seperti Pesanan Reguler, Pesanan Kotak, atau Siap Saji untuk
            menampilkan daftar produk. Tambah produk dengan menekan tombol “Tambah Produk”.
        </p>
        <ul class="list-disc space-y-2 text-[14px] pl-5">
            <li><span class="font-semibold">Pesanan Reguler</span> untuk produk pesanan berbentuk loyangan
                atau paketan.</li>
            <li><span class="font-semibold">Pesanan Kotak</span> untuk produk kombinasi snack box.</li>
            <li><span class="font-semibold">Siap Saji</span> untuk produk yang tersedia di rak penjualan secara
                ecer.</li>
        </ul>
    </x-alert.info>

    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-8 py-6 flex flex-col gap-6 border border-[#ececec]">
        @php
            $tabOptions = [
                ['value' => 'pesanan-reguler', 'label' => 'Pesanan Reguler', 'icon' => 'cake'],
                ['value' => 'pesanan-kotak', 'label' => 'Pesanan Kotak', 'icon' => 'cube'],
                ['value' => 'siap-beli', 'label' => 'Siap Saji', 'icon' => 'shopping-bag'],
            ];
        @endphp
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            @foreach ($tabOptions as $tab)
                @php
                    $isActive = $method === $tab['value'];
                @endphp
                <button type="button" wire:click="$set('method', '{{ $tab['value'] }}')"
                    class="rounded-[15px] border transition-all duration-200 flex flex-col items-center gap-2 px-6 py-4 {{ $isActive ? 'bg-white border-[#74512d] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]' : 'bg-[#fafafa] border-[#e4e4e4] hover:border-[#74512d]' }}"
                    aria-pressed="{{ $isActive ? 'true' : 'false' }}">
                    <flux:icon :icon="$tab['icon']" class="size-8 text-[#6c7068]" />
                    <span class="font-montserrat font-medium text-[16px] text-center text-[#6c7068]">
                        {{ $tab['label'] }}
                    </span>
                    @if ($isActive)
                        <span class="mt-1 block h-[4px] w-12 rounded-full bg-[#74512d]"></span>
                    @endif
                </button>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-[240px]">
                <div
                    class="flex items-center gap-2 bg-white border border-[#666666] rounded-[20px] px-4 h-[40px] flex-1">
                    <flux:icon.magnifying-glass class="size-5 text-[#666666]" />
                    <input wire:model.live="search" placeholder="Cari Produk"
                        class="flex-1 bg-transparent border-0 font-montserrat font-medium text-[16px] text-[#959595] focus:outline-none focus:ring-0" />
                </div>
                <button type="button"
                    class="flex items-center gap-2 text-[#666666] font-montserrat font-medium text-[16px] px-3">
                    <flux:icon.funnel variant="mini" class="size-5" />
                    Filter
                </button>
            </div>
            <div class="flex items-center gap-3">
                <flux:button type="button" variant="primary" href="{{ route('kategori') }}" icon="shapes">
                    Kategori
                </flux:button>
                <flux:button variant="primary" icon="plus" href="{{ route('produk.tambah') }}">
                    Tambah Produk
                </flux:button>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 font-montserrat font-medium text-[16px] text-[#666666]">
                <span>Jumlah Produk :</span>
                <span>{{ $products->total() }}</span>
            </div>
            <div class="flex items-center gap-3 text-[#666666]">
                <span class="font-montserrat text-[16px]">Tampilan Produk:</span>
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="$set('viewMode', 'grid')"
                        class="size-10 rounded-[15px] border flex items-center justify-center {{ $viewMode === 'grid' ? 'bg-[#74512d] text-white border-[#74512d]' : 'bg-white text-[#666666] border-[#959595]' }}">
                        <flux:icon.squares-2x2 class="size-5" />
                    </button>
                    <button type="button" wire:click="$set('viewMode', 'list')"
                        class="size-10 rounded-[15px] border flex items-center justify-center {{ $viewMode === 'list' ? 'bg-[#74512d] text-white border-[#74512d]' : 'bg-white text-[#666666] border-[#959595]' }}">
                        <flux:icon.list-bullet class="size-5" />
                    </button>
                </div>
            </div>
        </div>

        @if ($viewMode === 'list')
            @if ($products->isEmpty())
                <div
                    class="w-full rounded-[15px] bg-[#eaeaea] p-10 text-center flex flex-col items-center justify-center gap-2">
                    <flux:icon.cube class="size-10 text-[#666666]" />
                    <p class="font-montserrat font-semibold text-[18px] text-[#1e1e1e]">Belum Ada Produk</p>
                    <p class="font-montserrat text-[16px] text-[#666666]">Tekan tombol “Tambah Produk” untuk
                        menambahkan data.</p>
                </div>
            @else
                <div class="bg-white rounded-[15px] border border-[#e4e4e4] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-[#3f4e4f] h-[60px]">
                                    <th class="px-6 text-left">
                                        <button type="button" wire:click="sortBy('name')"
                                            class="flex items-center gap-2 text-left">
                                            <span
                                                class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Produk</span>
                                            <flux:icon.chevron-up-down class="size-4 text-[#f8f4e1]" />
                                        </button>
                                    </th>
                                    <th class="px-6 text-left">
                                        <button type="button" wire:click="sortBy('is_active')"
                                            class="flex items-center gap-2 text-left">
                                            <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Status
                                                Tampil</span>
                                            <flux:icon.chevron-up-down class="size-4 text-[#f8f4e1]" />
                                        </button>
                                    </th>
                                    <th class="px-6 text-left">
                                        <button type="button" wire:click="sortBy('is_recommended')"
                                            class="flex items-center gap-2 text-left">
                                            <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Status
                                                Rekomendasi</span>
                                            <flux:icon.chevron-up-down class="size-4 text-[#f8f4e1]" />
                                        </button>
                                    </th>
                                    <th class="px-6 text-right">
                                        <button type="button" wire:click="sortBy('price')"
                                            class="flex items-center gap-2 w-full justify-end">
                                            <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Harga
                                                Jual</span>
                                            <flux:icon.chevron-up-down class="size-4 text-[#f8f4e1]" />
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-[#fafafa]">
                                @foreach ($products as $product)
                                    <tr class="border-b border-[#d4d4d4] h-[60px]">
                                        <td class="px-6">
                                            <a href="{{ route('produk.edit', $product->id) }}"
                                                class="font-montserrat font-medium text-[14px] text-[#666666] hover:text-[#3f4e4f]">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td class="px-6">
                                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                                {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6">
                                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                                {{ $product->is_recommended ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 text-right">
                                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4">
                        {{ $products->links() }}
                    </div>
                </div>
            @endif
        @else
            @if ($products->isEmpty())
                <div
                    class="rounded-[20px] bg-[#eaeaea] p-8 text-center flex flex-col items-center justify-center gap-3">
                    <flux:icon.cube class="size-10 text-[#666666]" />
                    <p class="font-montserrat font-semibold text-[18px] text-[#1e1e1e]">Belum Ada Produk</p>
                    <p class="font-montserrat text-[16px] text-[#666666]">Tambah produk baru untuk melihat
                        kartu
                        ringkasannya di sini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach ($products as $product)
                        <div
                            class="bg-white rounded-[20px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] border border-[#e4e4e4] overflow-hidden">
                            <div class="h-[140px] bg-[#eaeaea] relative">
                                @if ($product->product_image)
                                    <img src="{{ asset('storage/' . $product->product_image) }}"
                                        alt="{{ $product->name }}"
                                        class="absolute inset-0 w-full h-full object-cover" />
                                @else
                                    <img src="{{ asset('img/no-img.jpg') }}" alt="Gambar Produk"
                                        class="absolute inset-0 w-full h-full object-cover" />
                                @endif
                                <div class="absolute top-3 left-3 flex gap-2">
                                    @if ($product->is_recommended)
                                        <span
                                            class="bg-[#74512d] text-white text-xs px-2 py-0.5 rounded-full font-montserrat">Promo</span>
                                    @endif
                                    <span
                                        class="bg-[#3f4e4f] text-white text-xs px-2 py-0.5 rounded-full font-montserrat">
                                        {{ $product->is_active ? 'Tampil' : 'Disembunyikan' }}
                                    </span>
                                </div>
                            </div>
                            <div class="px-5 py-4 space-y-2 text-center">
                                <p class="font-montserrat font-semibold text-[16px] text-[#666666]">
                                    {{ $product->name }}</p>
                                <p class="font-montserrat text-[14px] text-[#959595]">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="px-5 pb-5">
                                <flux:button class="w-full" variant="subtle" type="button"
                                    href="{{ route('produk.edit', $product->id) }}">
                                    Lihat
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Produk</flux:heading>
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
