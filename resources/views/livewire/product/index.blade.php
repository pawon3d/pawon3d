<div class="space-y-[30px]">
    <div class="flex items-center justify-between h-[40px]">
        <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Kelola Produk</h1>
        <flux:button type="button" variant="subtle" wire:click="riwayatPembaruan">
            Riwayat Pembaruan
        </flux:button>
    </div>


    <x-alert.info class="text-[#dcd7c9] text-justify">
        <p class="font-montserrat font-semibold text-[14px] mb-3 leading-normal">
            Pilih salah satu metode penjualan seperti Pesanan Reguler, Pesanan Kotak, atau Siap Saji untuk
            menampilkan daftar produk. Tambah produk dengan menekan tombol "<span class="font-bold">Tambah
                Produk</span>" untuk menambahkan produk.
        </p>
        <ul class="list-disc ml-6 space-y-2 text-[14px] leading-normal">
            <li><span class="font-bold">Pesanan Reguler</span> untuk produk pesanan yang bentuknya loyangan atau
                paketan.</li>
            <li><span class="font-bold">Pesanan Kotak</span> untuk produk pesanan yang bentuknya snack box dengan
                kombinasi banyak produk dalam satu kotak.</li>
            <li><span class="font-bold">Siap Saji</span> untuk produk yang ada di rak penjualan yang bentuknya per
                potong.</li>
        </ul>
    </x-alert.info>

    <div class="flex items-center">
        @php
            $tabs = [
                ['value' => 'pesanan-reguler', 'label' => 'Pesanan Reguler', 'icon' => 'cake'],
                ['value' => 'pesanan-kotak', 'label' => 'Pesanan Kotak', 'icon' => 'cube'],
                ['value' => 'siap-beli', 'label' => 'Siap Saji', 'icon' => 'shopping-bag'],
            ];
        @endphp

        @foreach ($tabs as $index => $tab)
            @php
                $isActive = $method === $tab['value'];
                $roundedClass =
                    $index === 0
                        ? 'rounded-tl-[15px] rounded-bl-[15px]'
                        : ($index === count($tabs) - 1
                            ? 'rounded-tr-[15px] rounded-br-[15px]'
                            : '');
            @endphp
            <button type="button" wire:click="$set('method', '{{ $tab['value'] }}')"
                class="flex-1 bg-[#fafafa] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] overflow-hidden px-[20px] py-[15px] h-[105px] flex flex-col items-center justify-center gap-[5px] {{ $roundedClass }}"
                aria-pressed="{{ $isActive ? 'true' : 'false' }}">
                <flux:icon :icon="$tab['icon']"
                    class="size-[34px] {{ $isActive ? 'text-[#74512d]' : 'text-[#6c7068]' }}" />
                <span
                    class="font-montserrat {{ $isActive ? 'font-bold text-[#74512d]' : 'font-medium text-[#6c7068] opacity-90' }} text-[16px] text-center">
                    {{ $tab['label'] }}
                </span>
                @if ($isActive)
                    <div class="h-[4px] w-full border-b-[4px] border-[#74512d]"></div>
                @endif
            </button>
        @endforeach
    </div>

    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-[30px] py-[25px] flex flex-col gap-[20px]">
        <div class="flex flex-col gap-[30px]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-[15px] flex-1">
                    <div class="bg-white border border-[#666666] rounded-[20px] px-[15px] flex items-center w-[450px]">
                        <flux:icon.magnifying-glass class="size-[30px] text-[#666666]" />
                        <input wire:model.live="search" placeholder="Cari Produk"
                            class="flex-1 bg-transparent border-0 font-montserrat font-medium text-[16px] text-[#959595] focus:outline-none focus:ring-0 px-[10px] py-[10px]" />
                    </div>
                    <div class="flex items-center cursor-pointer">
                        <flux:icon.funnel class="size-[25px] text-[#666666]" />
                        <p class="font-montserrat font-medium text-[16px] text-[#666666] px-[5px] py-[10px]">Filter</p>
                    </div>
                </div>
                <div class="flex items-center gap-[15px] justify-end flex-1">
                    <flux:button variant="primary" icon="shapes" href="{{ route('kategori') }}">
                        Kategori
                    </flux:button>
                    <flux:button variant="primary" icon="plus" href="{{ route('produk.tambah') }}">
                        Tambah Produk
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <p class="font-montserrat font-medium text-[16px] text-[#666666] px-[5px] py-[10px]">
                    Jumlah Produk
                </p>
                <p class="font-montserrat font-medium text-[16px] text-[#666666] px-0 py-[10px]">
                    :
                </p>
                <p class="font-montserrat font-medium text-[16px] text-[#666666] px-[5px] py-[10px]">
                    {{ $products->total() }}
                </p>
            </div>
            <div class="flex items-center gap-[20px] justify-center">
                <p class="font-montserrat font-medium text-[16px] text-[#666666] text-center">
                    Tampilan Produk:
                </p>
                <div class="flex items-center gap-[10px] cursor-pointer">
                    <button type="button" wire:click="$set('viewMode', 'grid')"
                        class="size-[40px] rounded-[15px] flex items-center justify-center overflow-hidden p-[10px] {{ $viewMode === 'grid' ? 'bg-[#74512d]' : 'bg-white border border-[#959595]' }}">
                        <flux:icon.squares-2x2
                            class="size-[22px] {{ $viewMode === 'grid' ? 'text-white' : 'text-[#666666]' }}" />
                    </button>
                    <button type="button" wire:click="$set('viewMode', 'list')"
                        class="size-[40px] rounded-[15px] flex flex-col items-center justify-center overflow-hidden p-[10px] {{ $viewMode === 'list' ? 'bg-[#74512d]' : 'bg-white border border-[#959595]' }}">
                        <flux:icon.list-bullet
                            class="size-[22px] {{ $viewMode === 'list' ? 'text-white' : 'text-[#666666]' }}" />
                    </button>
                </div>
            </div>
        </div>

        @if ($viewMode === 'list')
            <x-table.paginated :headers="[
                ['label' => 'Produk', 'sortable' => true, 'sort-by' => 'name'],
                ['label' => 'Status Tampil', 'sortable' => true, 'sort-by' => 'is_active', 'class' => 'w-[150px]'],
                [
                    'label' => 'Status Rekomendasi',
                    'sortable' => true,
                    'sort-by' => 'is_recommended',
                    'class' => 'w-[190px]',
                ],
                [
                    'label' => 'Harga Jual',
                    'sortable' => true,
                    'sort-by' => 'price',
                    'align' => 'right',
                    'class' => 'w-[150px]',
                ],
            ]" :paginator="$products" headerBg="#3f4e4f" headerText="#f8f4e1"
                bodyBg="#fafafa" bodyText="#666666"
                wrapperClass="rounded-[15px] overflow-hidden border-0 shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]"
                emptyMessage="Belum ada produk. Tambah produk baru untuk melihat datanya di sini.">
                @foreach ($products as $product)
                    <tr class="border-b border-[#d4d4d4] last:border-0 h-[60px]">
                        <td class="px-[25px] py-4">
                            <a href="{{ route('produk.edit', $product->id) }}"
                                class="font-montserrat font-medium text-[14px] text-[#666666] hover:text-[#74512d]">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-[25px] py-4">
                            <p class="font-montserrat font-medium text-[14px] text-[#666666]">
                                {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </p>
                        </td>
                        <td class="px-[25px] py-4">
                            <p class="font-montserrat font-medium text-[14px] text-[#666666]">
                                {{ $product->is_recommended ? 'Aktif' : 'Tidak Aktif' }}
                            </p>
                        </td>
                        <td class="px-[25px] py-4 text-right">
                            <p class="font-montserrat font-medium text-[14px] text-[#666666]">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </td>
                    </tr>
                @endforeach
            </x-table.paginated>
        @else
            @if ($products->isEmpty())
                <div
                    class="w-full rounded-[20px] bg-[#eaeaea] p-8 text-center flex flex-col items-center justify-center gap-3">
                    <flux:icon.cube class="size-10 text-[#666666]" />
                    <p class="font-montserrat font-semibold text-[18px] text-[#1e1e1e]">Belum Ada Produk</p>
                    <p class="font-montserrat text-[16px] text-[#666666]">Tambah produk baru untuk melihat
                        kartu ringkasannya di sini.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-[35px]">
                    @foreach ($products as $product)
                        <div class="flex flex-col gap-[20px] items-center pb-[25px]">
                            <div class="flex flex-col gap-[15px] items-center w-full">
                                <div
                                    class="h-[119px] w-[182px] relative rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] bg-[#eaeaea] mx-auto">
                                    @if ($product->product_image)
                                        <img src="{{ asset('storage/' . $product->product_image) }}"
                                            alt="{{ $product->name }}"
                                            class="absolute inset-0 w-full h-full object-cover rounded-[15px]" />
                                    @else
                                        <img src="{{ asset('img/no-img.jpg') }}" alt="Gambar Produk"
                                            class="absolute inset-0 w-full h-full object-cover rounded-[15px]" />
                                    @endif
                                </div>
                                <div class="flex flex-col gap-[30px] items-center w-full">
                                    <div class="flex flex-col gap-[10px] items-center min-h-[70px] w-full px-[15px]">
                                        <p
                                            class="font-montserrat font-medium text-[16px] text-[#666666] text-center w-full line-clamp-2">
                                            {{ $product->name }}
                                        </p>
                                    </div>
                                    <div
                                        class="flex items-center justify-center font-montserrat font-semibold text-[18px] text-[#666666] gap-1">
                                        <p>Rp</p>
                                        <p>{{ number_format($product->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('produk.edit', $product->id) }}"
                                class="bg-[#fafafa] border-[1.5px] border-[#74512d] rounded-[20px] w-full flex items-center justify-center px-[25px] py-[10px]">
                                <p class="font-montserrat font-bold text-[16px] text-[#74512d]">Lihat</p>
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination for Grid View --}}
                @if ($products->hasPages())
                    <div class="flex items-center justify-center mt-[20px]">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        @endif
    </div>

    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Produk</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse ($activityLogs as $log)
                    <div class="border-b py-2">
                        <div class="text-sm font-medium">{{ $log->description }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $log->causer->name ?? 'System' }} -
                            {{ $log->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">Tidak ada riwayat pembaruan</p>
                @endforelse
            </div>
        </div>
    </flux:modal>
</div>
