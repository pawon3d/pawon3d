<div class="h-full">
    @if ($todayShiftStatus == 'open')
        {{-- Header untuk kondisi shift open --}}
        <div class="px-4 sm:px-0 flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-xl font-semibold text-[#666666] text-center sm:text-left" style="font-family: 'Montserrat', sans-serif;">Daftar Menu
            </h1>
            <div class="flex flex-col sm:flex-row gap-2 items-center w-full sm:w-auto">
                <button type="button" wire:click="$set('closeShiftModal', true)"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#252324] text-[#f6f6f6] rounded-[15px] shadow-sm hover:bg-[#1a1819] transition-colors"
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">
                    <flux:icon icon="cashier" class="size-5" />
                    <span>Tutup Sesi Penjualan</span>
                </button>
                <flux:button type="button" variant="secondary" href="{{ route('transaksi.riwayat-sesi') }}"
                    wire:navigate
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-[#252324] text-[#f6f6f6] rounded-[15px] shadow-sm hover:bg-[#1a1819] transition-colors">
                    <flux:icon icon="history" class="size-5" />
                </flux:button>
            </div>
        </div>

        {{-- Info Box --}}

        <x-alert.info
            style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; line-height: 1.5;">
            <p class="mb-2">
                Pilih salah satu metode penjualan seperti Pesanan Reguler, Pesanan Kotak, atau Siap Saji untuk
                menampilkan <strong style="font-weight: 700;">Daftar Menu</strong>.
            </p>
            <ul class="list-disc pl-5 space-y-1">
                <li><strong style="font-weight: 700;">Pesanan Reguler</strong> : produk pesanan dalam bentuk
                    loyangan atau paketan.</li>
                <li><strong style="font-weight: 700;">Pesanan Kotak</strong> : produk dalam bentuk snack box dengan
                    kombinasi banyak jenis dalam kotak.</li>
                <li><strong style="font-weight: 700;">Siap Saji</strong> : produk dalam bentuk per potong yang
                    dipajang di etalase toko.</li>
            </ul>
        </x-alert.info>

        {{-- Method Selection Tabs --}}
        <div class="px-4 sm:px-0 flex flex-col sm:flex-row sm:h-[120px] mb-8 gap-1 sm:gap-0">
            <button wire:click="$set('method', 'pesanan-reguler')"
                class="flex-1 bg-[#fafafa] flex flex-col items-center justify-center gap-1 rounded-[15px] sm:rounded-none sm:rounded-tl-[15px] sm:rounded-bl-[15px] shadow-sm transition-all py-4 sm:py-0
                    {{ $method === 'pesanan-reguler' ? 'border-b-[4px] border-[#74512d]' : '' }}">
                <flux:icon icon="cake" class="size-[34px] text-[#666666]" />
                <span
                    class="text-center text-[16px] {{ $method === 'pesanan-reguler' ? 'font-bold text-[#74512d]' : 'font-medium text-[#6c7068] opacity-90' }}"
                    style="font-family: 'Montserrat', sans-serif;">
                    Pesanan Reguler
                </span>
            </button>

            <button wire:click="$set('method', 'pesanan-kotak')"
                class="flex-1 bg-[#fafafa] flex flex-col items-center justify-center gap-1 sm:rounded-none shadow-sm transition-all py-4 sm:py-0
                    {{ $method === 'pesanan-kotak' ? 'border-b-[4px] border-[#74512d]' : '' }}">
                <flux:icon icon="cube" class="size-[30px] text-[#666666]" />
                <span
                    class="text-center text-[16px] {{ $method === 'pesanan-kotak' ? 'font-bold text-[#74512d]' : 'font-medium text-[#6c7068] opacity-90' }}"
                    style="font-family: 'Montserrat', sans-serif;">
                    Pesanan Kotak
                </span>
            </button>

            <button wire:click="$set('method', 'siap-beli')"
                class="flex-1 bg-[#fafafa] flex flex-col items-center justify-center gap-1 rounded-[15px] sm:rounded-none sm:rounded-tr-[15px] sm:rounded-br-[15px] shadow-sm transition-all py-4 sm:py-0
                    {{ $method === 'siap-beli' ? 'border-b-[4px] border-[#74512d]' : '' }}">
                <flux:icon icon="dessert" class="size-[30px] text-[#666666]" />
                <span
                    class="text-center text-[16px] {{ $method === 'siap-beli' ? 'font-bold text-[#74512d]' : 'font-medium text-[#6c7068] opacity-90' }}"
                    style="font-family: 'Montserrat', sans-serif;">
                    Siap Saji
                </span>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-4 sm:p-8">
            {{-- Search and Actions --}}
            <div class="flex flex-col lg:flex-row justify-between lg:items-center mb-8 gap-4 flex-wrap">
                <div class="flex flex-col sm:flex-row gap-4 items-center flex-1 w-full lg:max-w-[550px]">
                    <div
                        class="flex-1 bg-white border border-[#666666] rounded-[20px] px-4 py-2 flex items-center gap-3 w-full">
                        <flux:icon icon="magnifying-glass" class="size-[30px] text-[#666666]" />
                        <input type="text" wire:model.live="search" placeholder="Cari Produk"
                            class="flex-1 w-full border-0 focus:ring-0 text-[16px] text-[#959595]"
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500;" />
                    </div>
                    <button class="flex items-center gap-2 text-[#666666] hover:text-[#74512d] transition-colors justify-center">
                        <flux:icon icon="funnel" class="size-[25px]" />
                        <span class="text-[16px] font-medium"
                            style="font-family: 'Montserrat', sans-serif;">Filter</span>
                    </button>
                </div>

                <div class="flex gap-2 items-center flex-wrap justify-center w-full lg:w-auto">
                    @if ($method != 'siap-beli')
                        <flux:button type="button" variant="primary"
                            class="flex-1 sm:flex-none"
                            href="{{ route('transaksi.pesanan', ['method' => $method]) }}" icon="clipboard"
                            wire:navigate>
                            <span>Daftar Pesanan</span>
                        </flux:button>
                    @else
                        <flux:button type="button" variant="primary" href="{{ route('transaksi.siap-beli') }}"
                            class="flex-1 sm:flex-none"
                            icon="clipboard" wire:navigate>
                            <span>Daftar Produk</span>
                        </flux:button>
                    @endif
                    <flux:button type="button" variant="primary" icon="history"
                        class="flex-none"
                        href="{{ route('transaksi.riwayat', ['method' => $method]) }}" wire:navigate>
                    </flux:button>
                </div>
            </div>

            <div class="h-px bg-[#d4d4d4] mb-8"></div>

            {{-- Content Container with Product Grid and Cart Sidebar --}}
            <div class="flex flex-col xl:flex-row gap-[20px] @if (count($cart) > 0) justify-between @endif">
                {{-- Product Grid --}}
                <div class="@if (count($cart) > 0) flex-1 @else w-full @endif">
                    <div @class([
                        'grid gap-8 min-h-[500px] pr-2',
                        'grid-cols-2 lg:grid-cols-3' => count($cart) > 0,
                        'grid-cols-2 md:grid-cols-3 lg:grid-cols-5' => count($cart) === 0,
                    ])>
                        @forelse ($products as $product)
                            <div class="flex flex-col gap-5 pb-6">
                                {{-- Product Image --}}
                                <a href="{{ route('transaksi.rincian-produk', $product->id) }}"
                                    class="relative block hover:opacity-80 transition-opacity" wire:navigate>
                                    @if ($product->product_image)
                                        <img src="{{ asset('storage/' . $product->product_image) }}"
                                            alt="{{ $product->name }}"
                                            class="w-full h-[119px] object-cover rounded-[15px] shadow-md">
                                    @else
                                        <img src="{{ asset('img/no-img.jpg') }}" alt="Gambar Produk"
                                            class="w-full h-[119px] object-cover rounded-[15px] shadow-md bg-[#eaeaea]">
                                    @endif
                                </a>

                                {{-- Product Info --}}
                                <div class="flex flex-col gap-8 items-center px-4">
                                    <div class="min-h-[70px] w-full">
                                        <a href="{{ route('transaksi.rincian-produk', $product->id) }}"
                                            class="hover:text-[#74512d] transition-colors" wire:navigate>
                                            <p class="text-base font-medium text-[#666666] text-center line-clamp-2 mb-1 hover:text-[#74512d]"
                                                style="font-family: 'Montserrat', sans-serif;">
                                                {{ $product->name }}
                                            </p>
                                        </a>
                                        <p class="text-base font-medium text-[#666666] text-center"
                                            style="font-family: 'Montserrat', sans-serif;">
                                            @if ($method == 'siap-beli')
                                                stok : {{ $product->getAvailableStock() }}
                                            @else
                                                ({{ $product->pcs }} pcs)
                                            @endif
                                        </p>
                                    </div>

                                    <div class="text-lg font-semibold text-[#666666] text-center w-full"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        Rp{{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                </div>

                                {{-- Add Button or Quantity Controls --}}
                                @if (isset($cart[$product->id]))
                                    {{-- Quantity Controls when in cart --}}
                                    <div class="flex items-center justify-center gap-[11px] w-full">
                                        {{-- Minus Button --}}
                                        <button wire:click="decrementItem('{{ $product->id }}')"
                                            class="border-[1.5px] border-[#74512d] rounded-[30px] w-[35px] h-[35px] flex items-center justify-center hover:bg-[#74512d] transition-colors group">
                                            <flux:icon icon="minus"
                                                class="size-[18px] text-[#74512d] group-hover:text-white" />
                                        </button>

                                        {{-- Quantity Display --}}
                                        <div
                                            class="border-[1.5px] border-[#74512d] rounded-[10px] px-[15px] py-[6px] min-w-[45px] flex items-center justify-center">
                                            <p class="font-semibold text-[16px] text-[#74512d]"
                                                style="font-family: 'Montserrat', sans-serif;">
                                                {{ $cart[$product->id]['quantity'] }}
                                            </p>
                                        </div>

                                        {{-- Plus Button --}}
                                        <button wire:click="incrementItem('{{ $product->id }}')"
                                            class="border-[1.5px] border-[#74512d] rounded-[30px] w-[35px] h-[35px] flex items-center justify-center hover:bg-[#74512d] transition-colors group">
                                            <flux:icon icon="plus"
                                                class="size-[18px] text-[#74512d] group-hover:text-white" />
                                        </button>
                                    </div>
                                @else
                                    {{-- Add Button when not in cart --}}
                                    <button wire:click="addToCart('{{ $product->id }}')"
                                        class="w-full bg-white border-[1.5px] border-[#74512d] rounded-[20px] px-6 py-2.5 text-[#74512d] font-bold text-base hover:bg-[#74512d] hover:text-white transition-colors"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        Tambah
                                    </button>
                                @endif
                            </div>
                        @empty
                            <div @class([
                                'bg-[#eaeaea] rounded-[15px] p-8 text-center min-h-[385px] flex flex-col items-center justify-center',
                                'col-span-3' => count($cart) > 0,
                                'col-span-5' => count($cart) === 0,
                            ])>
                                <p class="text-[#666666] font-semibold text-base mb-2"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    Belum ada produk
                                </p>
                                <p class="text-[#666666] text-sm" style="font-family: 'Montserrat', sans-serif;">
                                    Pilih metode penjualan untuk melihat daftar produk
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Cart Sidebar - Only show when cart has items --}}
                @if (count($cart) > 0)
                    <div class="w-full xl:w-[420px] flex-shrink-0">
                        <div
                            class="bg-white border border-[#d4d4d4] rounded-[15px] h-full flex flex-col justify-between overflow-hidden">
                            {{-- Cart Items Section --}}
                            <div class="flex flex-col gap-[20px] py-[25px]">
                                {{-- Header --}}
                                <div class="px-[20px]">
                                    <p class="font-bold text-[16px] text-[#666666]"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        Daftar Pesanan
                                    </p>
                                </div>

                                {{-- Cart Items List --}}
                                <div class="max-h-[500px] overflow-y-auto px-[21px]">
                                    <div class="flex flex-col">
                                        @foreach ($cart as $itemId => $item)
                                            <div
                                                class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-[15px] border-b border-[#f0f0f0] gap-4 sm:gap-0">
                                                {{-- Left: Product Info --}}
                                                <div class="flex flex-col gap-[5px] w-full sm:w-auto">
                                                    {{-- Product Name --}}
                                                    <div class="flex items-center">
                                                        <p class="font-medium text-[16px] text-[#666666] truncate w-full sm:max-w-[180px]"
                                                            style="font-family: 'Montserrat', sans-serif;">
                                                            {{ $item['name'] }}
                                                        </p>
                                                    </div>

                                                    {{-- Quantity x Price --}}
                                                    <div class="flex items-center gap-[5px]">
                                                        <p class="font-normal text-[14px] text-[#959595]"
                                                            style="font-family: 'Montserrat', sans-serif;">
                                                            {{ $item['quantity'] }}
                                                        </p>
                                                        <p class="font-normal text-[14px] text-[#959595]"
                                                            style="font-family: 'Montserrat', sans-serif;">
                                                            x
                                                        </p>
                                                        <p class="font-normal text-[14px] text-[#959595]"
                                                            style="font-family: 'Montserrat', sans-serif;">
                                                            Rp{{ number_format($item['price'], 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>

                                                {{-- Right: Total & Controls --}}
                                                <div
                                                    class="flex flex-row sm:flex-col gap-[15px] sm:gap-[10px] items-center sm:items-end justify-between w-full sm:w-auto">
                                                    {{-- Total Price --}}
                                                    <div class="flex items-center gap-[5px]">
                                                        <p class="font-normal text-[16px] text-[#666666]"
                                                            style="font-family: 'Montserrat', sans-serif;">
                                                            =
                                                        </p>
                                                        <p class="font-medium text-[16px] text-[#666666]"
                                                            style="font-family: 'Montserrat', sans-serif;">
                                                            Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                        </p>
                                                    </div>

                                                    {{-- Controls: Delete, -, Quantity, + --}}
                                                    <div class="flex items-center gap-[15px] sm:gap-[25px]">
                                                        {{-- Delete Button --}}
                                                        <button wire:click="removeItem('{{ $itemId }}')"
                                                            class="bg-[#eb5757] rounded-[15px] p-0 w-[32px] h-[32px] flex items-center justify-center hover:bg-[#d94545] transition-colors order-2 sm:order-1">
                                                            <flux:icon icon="trash" class="size-3 text-[#f8f4e1]" />
                                                        </button>

                                                        {{-- Quantity Controls --}}
                                                        <div class="flex items-center gap-[10px] sm:gap-[11px] order-1 sm:order-2">
                                                            {{-- Minus Button --}}
                                                            <button wire:click="decrementItem('{{ $itemId }}')"
                                                                class="border-[1.5px] border-[#74512d] rounded-[30px] w-[30px] h-[30px] flex items-center justify-center hover:bg-[#74512d] hover:text-white transition-colors">
                                                                <flux:icon icon="minus"
                                                                    class="size-[18px] text-[#666666]" />
                                                            </button>

                                                            {{-- Quantity Display --}}
                                                            <div
                                                                class="border border-[rgba(116,81,45,0.2)] rounded-[10px] px-[10px] py-[3px] w-[36px] flex items-center justify-center">
                                                                <p class="font-semibold text-[16px] text-[#666666]"
                                                                    style="font-family: 'Montserrat', sans-serif;">
                                                                    {{ $item['quantity'] }}
                                                                </p>
                                                            </div>

                                                            {{-- Plus Button --}}
                                                            <button wire:click="incrementItem('{{ $itemId }}')"
                                                                class="border-[1.5px] border-[#74512d] rounded-[30px] w-[30px] h-[30px] flex items-center justify-center hover:bg-[#74512d] hover:text-white transition-colors">
                                                                <flux:icon icon="plus"
                                                                    class="size-[18px] text-[#666666]" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Footer: Summary & Buttons --}}
                            <div class="bg-white border-t border-[#d4d4d4] py-[20px] flex flex-col gap-[30px]">
                                {{-- Subtotal & Total --}}
                                <div class="flex flex-col gap-[15px]">
                                    {{-- Subtotal --}}
                                    <div class="flex items-center justify-between px-[20px]">
                                        <div class="flex items-center gap-[5px]">
                                            <p class="font-medium text-[16px] text-[#666666]"
                                                style="font-family: 'Montserrat', sans-serif;">
                                                Subtotal
                                            </p>
                                            <p class="font-medium text-[16px] text-[#666666]"
                                                style="font-family: 'Montserrat', sans-serif;">
                                                {{ array_sum(array_column($cart, 'quantity')) }}
                                            </p>
                                            <p class="font-medium text-[16px] text-[#666666]"
                                                style="font-family: 'Montserrat', sans-serif;">
                                                Produk
                                            </p>
                                        </div>
                                        <p class="font-medium text-[16px] text-[#666666]"
                                            style="font-family: 'Montserrat', sans-serif;">
                                            Rp{{ number_format($this->getTotalProperty(), 0, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- Total Tagihan --}}
                                    <div class="flex items-center justify-between px-[20px]">
                                        <p class="font-bold text-[16px] text-[#74512d]"
                                            style="font-family: 'Montserrat', sans-serif;">
                                            Total Tagihan
                                        </p>
                                        <p class="font-bold text-[16px] text-[#74512d]"
                                            style="font-family: 'Montserrat', sans-serif;">
                                            Rp{{ number_format($this->getTotalProperty(), 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex gap-[10px] px-[20px]">
                                    {{-- Cancel Button --}}
                                    <button wire:click="clearCart"
                                        class="flex-1 bg-[#c4c4c4] rounded-[15px] px-[25px] py-[10px] flex items-center justify-center gap-[5px] shadow-sm hover:bg-[#b0b0b0] transition-colors">
                                        <flux:icon icon="x-mark" class="size-5 text-[#333333]" />
                                        <span class="font-semibold text-[16px] text-[#333333]"
                                            style="font-family: 'Montserrat', sans-serif;">
                                            Batal
                                        </span>
                                    </button>

                                    {{-- Checkout Button --}}
                                    <flux:button variant="secondary" icon="shopping-cart" wire:click="checkout"
                                        wire:navigate>
                                        <span class="font-semibold text-[16px] text-[#f8f4e1]"
                                            style="font-family: 'Montserrat', sans-serif;">
                                            Checkout
                                        </span>
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Header untuk kondisi shift closed --}}
        <div class="px-4 sm:px-0 flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-xl font-semibold text-[#666666] text-center sm:text-left" style="font-family: 'Montserrat', sans-serif;">Daftar
                Menu
            </h1>
            <div class="flex flex-col sm:flex-row gap-2 items-center w-full sm:w-auto">
                <button type="button" wire:click="$set('openShiftModal', true)"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#252324] text-[#f6f6f6] rounded-[15px] shadow-sm hover:bg-[#1a1819] transition-colors"
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">
                    <flux:icon icon="cashier" class="size-5" />
                    <span>Buka Sesi Penjualan</span>
                </button>
                <flux:button type="button" variant="secondary" href="{{ route('transaksi.riwayat-sesi') }}"
                    wire:navigate
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-[#252324] text-[#f6f6f6] rounded-[15px] shadow-sm hover:bg-[#1a1819] transition-colors">
                    <flux:icon icon="history" class="size-5" />
                </flux:button>
            </div>
        </div>

        {{-- Info Box --}}
        <x-alert.info
            style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; line-height: 1.5;">
            <p class="mb-2">
                Pilih salah satu metode penjualan seperti Pesanan Reguler, Pesanan Kotak, atau Siap Saji untuk
                menampilkan <strong style="font-weight: 700;">Daftar Menu</strong>.
            </p>
            <ul class="list-disc pl-5 space-y-1">
                <li><strong style="font-weight: 700;">Pesanan Reguler</strong> : produk pesanan dalam bentuk
                    loyangan atau paketan.</li>
                <li><strong style="font-weight: 700;">Pesanan Kotak</strong> : produk dalam bentuk snack box
                    dengan
                    kombinasi banyak jenis dalam kotak.</li>
                <li><strong style="font-weight: 700;">Siap Saji</strong> : produk dalam bentuk per potong yang
                    dipajang di etalase toko.</li>
            </ul>
        </x-alert.info>

        {{-- Empty State --}}
        <div class="bg-[#eaeaea] rounded-[15px] p-4 sm:p-8 min-h-[400px] flex flex-col items-center justify-center gap-6">
            <flux:icon icon="cashier" class="size-[40px] text-[#666666]" />
            <div class="text-center">
                <h2 class="text-lg font-medium text-[#313131] mb-2" style="font-family: 'Montserrat', sans-serif;">
                    Sesi Penjualan Belum Dibuka
                </h2>
                <p class="text-base text-[#313131]" style="font-family: 'Montserrat', sans-serif;">
                    Buka Sesi Penjualan untuk melakukan Penjualan
                </p>
            </div>
        </div>
    @endif


    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Transaksi</flux:heading>
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

    <!-- Modal Buka Sesi Penjualan -->
    <flux:modal name="open-shift-modal" class="w-full max-w-md" wire:model="openShiftModal">
        <div class="space-y-6">
            <div class="p-4">
                <flux:heading size="lg">Mulai Sesi Penjualan</flux:heading>
                <flux:label class="mt-8 mb-2">Jumlah awal uang di Laci Kasir</flux:label>
                <input type="number" class="border border-gray-300 rounded-md p-2 w-full text-right"
                    wire:model.number="initialCash" placeholder="Rp0" />
            </div>
        </div>
        <div class="mt-6 flex justify-end space-x-2">
            <flux:modal.close>
                <flux:button type="button" icon="x-mark" variant="filled">Batal</flux:button>
            </flux:modal.close>
            <flux:button type="button" icon="play" iconVariant="solid" variant="secondary"
                wire:click="openShift">Buka
            </flux:button>
        </div>
    </flux:modal>

    <!-- Modal Tutup Sesi Penjualan -->
    <flux:modal name="close-shift-modal" class="w-full max-w-[490px] h-full" wire:model="closeShiftModal">
        <div class="bg-[#fafafa] rounded-[15px] flex flex-col">

            <div class="flex flex-col gap-[30px]">
                {{-- Header --}}
                <div class="flex items-center justify-center">
                    <h2 class="text-[#666666] font-bold text-[20px]"
                        style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                        Laporan Sesi
                    </h2>
                </div>
                {{-- Content Area (Scrollable) --}}
                <div class="flex flex-col gap-[30px] h-[500px]">
                    <div class="flex-1 overflow-y-auto px-4 py-8 sm:px-[30px] sm:py-[40px]">
                        {{-- Info Sesi --}}
                        <div class="flex flex-col gap-[15px]">
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    No. Sesi
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    {{ $todayShiftNumber }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tanggal Buka
                                </span>
                                <div class="flex items-center gap-[10px] text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    <span>{{ \Carbon\Carbon::parse($todayShiftStartTime)->format('d M Y') }}</span>
                                    <span>{{ \Carbon\Carbon::parse($todayShiftStartTime)->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Kasir
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    {{ $todayShiftOpenedBy }}
                                </span>
                            </div>
                        </div>

                        {{-- Section: Tunai --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tunai
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Awal
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($initialCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($refundCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Diharapkan
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($expectedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="flex-1 text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Sebenarnya
                                </span>
                                <div class="flex-1 max-w-[150px]">
                                    <input type="number"
                                        class="w-full h-[35px] bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] text-right text-[#959595] font-normal text-[16px]"
                                        style="font-family: 'Montserrat', sans-serif;" wire:model.number="finalCash"
                                        placeholder="Rp0" />
                                </div>
                            </div>
                        </div>

                        {{-- Section: Terima Pembayaran --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Terima Pembayaran
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px] flex items-center gap-1"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Transfer
                                    <flux:button icon="information-circle" iconVariant="outline" variant="ghost"
                                        type="button" wire:click="showNonCashDetails('{{ $todayShiftId }}')" />
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Section: Pendapatan --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan Kotor
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedCash + $receivedNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($refundCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Non Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($refundNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Diskon
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($discountToday, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan Bersih
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedCash + $receivedNonCash - $refundTotal - $discountToday, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Footer: Action Buttons (Fixed/Sticky) --}}
            <div class="px-4 sm:px-[30px] flex items-center justify-end gap-[10px] flex-shrink-0">
                <flux:modal.close>
                    <button type="button"
                        class="bg-[#c4c4c4] border border-[#bababa] rounded-[15px] px-[25px] py-[10px] flex items-center gap-[5px] hover:bg-[#b0b0b0] transition-colors">
                        <flux:icon icon="x-mark" class="size-5 text-[#333333]" />
                        <span class="text-[#333333] font-semibold text-[16px]"
                            style="font-family: 'Montserrat', sans-serif;">
                            Batal
                        </span>
                    </button>
                </flux:modal.close>
                <button type="button" wire:click="closeShift"
                    class="bg-[#3f4e4f] rounded-[15px] px-[25px] py-[10px] flex items-center gap-[5px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] hover:bg-[#2f3e3f] transition-colors">
                    <flux:icon icon="stop" variant="solid" class="size-5 text-[#f8f4e1]" />
                    <span class="text-[#f8f4e1] font-semibold text-[16px]"
                        style="font-family: 'Montserrat', sans-serif;">
                        Tutup Sesi
                    </span>
                </button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Selesai Sesi Penjualan -->
    <flux:modal name="finish-shift-modal" class="w-full max-w-[490px] h-full" wire:model="finishShiftModal"
        @close="$set('initialCash', 0)">
        <div class="bg-[#fafafa] rounded-[15px] flex flex-col">
            <div class="flex flex-col gap-[30px]">
                {{-- Header --}}
                <div class="flex items-center justify-center">
                    <h2 class="text-[#666666] font-bold text-[20px]"
                        style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                        Laporan Sesi
                    </h2>
                </div>

                {{-- Content Area (Scrollable) --}}
                <div class="flex flex-col gap-[30px] h-[500px]">
                    <div class="flex-1 overflow-y-auto px-[30px] py-[40px]">
                        {{-- Info Sesi --}}
                        <div class="flex flex-col gap-[15px]">
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    No. Sesi
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    {{ $todayShiftNumber }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tanggal Buka
                                </span>
                                <div class="flex items-center gap-[10px] text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    <span>{{ \Carbon\Carbon::parse($todayShiftStartTime)->format('d M Y') }}</span>
                                    <span>{{ \Carbon\Carbon::parse($todayShiftStartTime)->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tanggal Tutup
                                </span>
                                <div class="flex items-center gap-[10px] text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    <span>{{ \Carbon\Carbon::parse($todayShiftEndTime)->format('d M Y') }}</span>
                                    <span>{{ \Carbon\Carbon::parse($todayShiftEndTime)->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Kasir
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    {{ $todayShiftOpenedBy }}
                                </span>
                            </div>
                        </div>

                        {{-- Section: Tunai --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tunai
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Awal
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($initialCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($refundCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Diharapkan
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($expectedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Jumlah Sebenarnya
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($finalCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Selisih Jumlah
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($finalCash - $expectedCash, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Section: Terima Pembayaran --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Terima Pembayaran
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] flex items-center gap-2 font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Transfer
                                    <flux:button icon="information-circle" iconVariant="outline" variant="ghost"
                                        type="button" wire:click="showNonCashDetails('{{ $todayShiftId }}')" />
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Section: Pendapatan --}}
                        <div class="flex flex-col gap-[15px] mt-2">
                            <div class="flex items-center gap-[30px]">
                                <span class="text-[#666666] font-semibold text-[16px] mt-2"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan Kotor
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedCash + $receivedNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($refundCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Refund Non Tunai
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($refundNonCash, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Diskon
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($discountToday, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#666666] font-medium text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Pendapatan Bersih
                                </span>
                                <span class="text-[#666666] font-normal text-[16px]"
                                    style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                                    Rp{{ number_format($receivedCash + $receivedNonCash - $refundTotal - $discountToday, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Footer: Action Button (Sticky) --}}
            <div class="px-4 sm:px-[30px] w-full">
                <flux:modal.close class="w-full">
                    <button type="button"
                        class="w-full bg-[#3f4e4f] rounded-[15px] px-[25px] py-[10px] flex items-center justify-center gap-[5px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] hover:bg-[#2f3e3f] transition-colors">
                        <flux:icon icon="check" variant="solid" class="size-5 text-[#f8f4e1]" />
                        <span class="text-[#f8f4e1] font-semibold text-[16px]"
                            style="font-family: 'Montserrat', sans-serif;">
                            Selesai
                        </span>
                    </button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Riwayat Sesi Penjualan -->
    <flux:modal name="history-shift-modal" class="w-full max-w-md max-h-[90vh] flex flex-col"
        wire:model="showHistoryShiftModal">
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Container utama flex -->
            <!-- Header -->
            <div class="p-4">
                <flux:heading size="lg">Riwayat Sesi Penjualan</flux:heading>
            </div>

            <!-- Search Input -->
            <div class="px-4 pb-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="searchHistoryShift"
                        class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari shift...">
                </div>
                <div class="relative mt-2">
                    <input type="date" wire:model.live="searchDate" onclick="this.showPicker()"
                        data-date="{{ $searchDate ? \Carbon\Carbon::parse($searchDate)->format('d/m/Y') : 'dd/mm/yyyy' }}"
                        class="tanggal" placeholder="Cari berdasarkan tanggal...">

                </div>
            </div>
            <!-- Area Scroll (Hanya bagian inilah yang discroll) -->
            <div class="flex-1 overflow-y-auto px-4 pb-2">
                <!-- flex-1 + overflow-auto -->
                @forelse ($historyShifts as $shift)
                    <div class="border-b py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-semibold">{{ $shift->shift_number }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $shift->start_time ? \Carbon\Carbon::parse($shift->start_time)->format('d/m/Y H:i') : 'Belum Dibuka' }}
                                    -
                                    {{ $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('d/m/Y H:i') : 'Belum Ditutup' }}
                                </p>
                            </div>
                            <div>
                                @if ($shift->status === 'open')
                                    <span class="text-green-500 text-xs font-semibold">Terbuka</span>
                                @else
                                    <span class="text-red-500 text-xs font-semibold">Ditutup</span>
                                @endif
                            </div>
                            <div>
                                <flux:button variant="ghost" icon="eye"
                                    wire:click="viewShift('{{ $shift->id }}')" class="text-gray-500" />
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-center text-gray-500">Tidak ada riwayat sesi penjualan.</p>
                @endforelse
            </div>

            <!-- Footer (Tetap di bawah tanpa scroll) -->
            <div class="p-4 border-t mt-auto">
                <flux:modal.close class="w-full">
                    <flux:button type="button" variant="primary" icon="arrow-long-left" class="w-full">
                        Kembali ke Kasir
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Detail Riwayat Sesi Penjualan -->
    <flux:modal name="detail-history-shift-modal" class="w-full max-w-md" wire:model="showDetailHistoryShiftModal">
        <div class="space-y-6">
            @if ($selectedShift)
                <div class="p-4">
                    <flux:heading size="lg">Rincian Sesi</flux:heading>
                    <div class="flex items-center justify-between mt-4 mb-2">
                        <span class="text-sm text-gray-500">No Sesi</span>
                        <span class="text-sm text-gray-500">{{ $selectedShift->shift_number }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Tanggal Buka</span>
                        <span
                            class="text-sm text-gray-500">{{ $selectedShift->start_time
                                ? \Carbon\Carbon::parse($selectedShift->start_time)->format('d/m/Y H:i')
                                : 'Belum Dibuka' }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Tanggal Tutup</span>
                        <span
                            class="text-sm text-gray-500">{{ $selectedShift->end_time
                                ? \Carbon\Carbon::parse($selectedShift->end_time)->format('d/m/Y H:i')
                                : 'Belum Ditutup' }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Dibuka Oleh</span>
                        <span class="text-sm text-gray-500">{{ $selectedShift->openedBy->name }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Ditutup Oleh</span>
                        <span class="text-sm text-gray-500">{{ $selectedShift->closedBy->name ?? '-' }}</span>
                    </div>
                    <h4 class="text-sm font-semibold mt-2">Laci Kasir</h4>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Jumlah awal</span>
                        <span
                            class="text-sm text-gray-500">Rp{{ number_format($selectedShift->initial_cash, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Penerimaan Tunai</span>
                        @php
                            $transactionPenerimaan = \App\Models\Transaction::where(
                                'created_by_shift',
                                $selectedShift->id,
                            )
                                ->whereHas('payments', function ($query) {
                                    $query->where('payment_method', 'tunai');
                                })
                                ->with([
                                    'payments' => function ($query) {
                                        $query->where('payment_method', 'tunai');
                                    },
                                ])
                                ->get()
                                ->sum(function ($transaction) {
                                    return $transaction->payments->sum('paid_amount');
                                });
                        @endphp
                        <span
                            class="text-sm text-gray-500">Rp{{ number_format($transactionPenerimaan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500 flex items-center">Penerimaan Non Tunai
                            <flux:button icon="information-circle" iconVariant="outline" loading="false"
                                variant="ghost" type="button" class="cursor-pointer"
                                wire:click="showNonCashDetails('{{ $selectedShift->id }}')" />
                        </span>
                        @php
                            $transactionPenerimaanNonTunai = \App\Models\Transaction::where(
                                'created_by_shift',
                                $selectedShift->id,
                            )
                                ->whereHas('payments', function ($query) {
                                    $query->where('payment_method', '!=', 'tunai');
                                })
                                ->with([
                                    'payments' => function ($query) {
                                        $query->where('payment_method', '!=', 'tunai');
                                    },
                                ])
                                ->get()
                                ->sum(function ($transaction) {
                                    return $transaction->payments->sum('paid_amount');
                                });
                        @endphp
                        <span
                            class="text-sm text-gray-500">Rp{{ number_format($transactionPenerimaanNonTunai, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Refund</span>
                        @php
                            $transactionRefund = \App\Models\Transaction::where(
                                'refund_by_shift',
                                $selectedShift->id,
                            )->sum('total_refund');
                        @endphp
                        <span
                            class="text-sm text-gray-500">Rp{{ number_format($transactionRefund, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Diskon/Hadian</span>
                        <span class="text-sm text-gray-500">Rp{{ number_format(0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Jumlah Yang Diharapkan</span>
                        <span
                            class="text-sm text-gray-500">Rp{{ number_format($selectedShift->initial_cash + $transactionPenerimaan - $transactionRefund, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Jumlah Sebenarnya</span>
                        <span
                            class="text-sm text-gray-500">Rp{{ number_format($selectedShift->final_cash, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 mb-2">
                        <span class="text-sm text-gray-500">Selisih Jumlah</span>
                        <span
                            class="text-sm text-gray-500">Rp{{ number_format(
                                $selectedShift->final_cash - ($selectedShift->initial_cash + $transactionPenerimaan - $transactionRefund),
                                0,
                                ',',
                                '.',
                            ) }}</span>
                    </div>
                </div>
            @else
                <div class="p-4 flex flex-col items-center">
                    <h2 class="text-lg font-semibold">
                        Tidak ada rincian sesi yang tersedia.
                    </h2>
                </div>
            @endif
        </div>
        <div class="mt-6 space-x-2 w-full">
            <flux:modal.close class="w-full">
                <flux:button type="button" variant="primary" class="w-full">Tutup
                </flux:button>
            </flux:modal.close>
        </div>
    </flux:modal>

    <!-- Modal non cash details -->
    <flux:modal name="non-cash-details-modal" class="w-full max-w-lg max-h-[90vh] flex flex-col"
        wire:model="showNonCashDetailsModal">
        <div class="flex flex-col flex-1 min-h-0">
            <div class="p-4">
                <flux:heading size="lg">Pembayaran Non Tunai</flux:heading>
            </div>
            <div class="flex-1 overflow-y-auto px-4 pb-2">
                @if (count($nonCashDetails) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">
                                        Invoice</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">
                                        Metode</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">
                                        Rekening</th>
                                    <th
                                        class="px-3 py-2 text-right font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($nonCashDetails as $detail)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ $detail->transaction->invoice_number }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap capitalize">
                                            {{ $detail->channel->group ? ucfirst($detail->channel->group) : ucfirst($detail->channel->type) }}
                                            - {{ $detail->channel->bank_name }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ $detail->channel->account_number }} -
                                            {{ $detail->channel->account_name }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-right">
                                            Rp{{ number_format($detail->paid_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-center text-gray-500 py-8">Tidak ada pembayaran non tunai</p>
                @endif
            </div>

            <!-- Footer (Tetap di bawah tanpa scroll) -->
            <div class="p-4 border-t mt-auto">
                <flux:modal.close class="w-full">
                    <flux:button type="button" variant="primary" icon="arrow-long-left" class="w-full">
                        Kembali ke Kasir
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
    {{-- @section('css')
        <style>
            .tanggal {
                position: relative;
                width: 100%;
                height: 2.5rem;
                /* Sesuaikan tinggi input */
                padding: 0.5rem 2.5rem 0.5rem 0.75rem;
                /* Biar ada ruang untuk teks dan ikon */
                color: transparent;
                /* Sembunyikan teks aslinya */
                background-color: #f9fafb;
                /* gray-50 */
                border: 1px solid #d1d5db;
                /* gray-300 */
                border-radius: 0.5rem;
                /* rounded-lg */
                font-size: 0.875rem;
                /* text-sm */
                outline: none;
            }

            .tanggal:before {
                position: absolute;
                top: 50%;
                left: 0.75rem;
                transform: translateY(-50%);
                content: attr(data-date);
                display: inline-block;
                color: #111827;
                /* gray-900 */
                pointer-events: none;
                font-size: 0.875rem;
                /* text-sm */
            }

            .tanggal::-webkit-datetime-edit,
            .tanggal::-webkit-inner-spin-button,
            .tanggal::-webkit-clear-button {
                display: none;
            }

            .tanggal::-webkit-calendar-picker-indicator {
                position: absolute;
                top: 50%;
                right: 0.75rem;
                transform: translateY(-50%);
                opacity: 1;
                color: #6b7280;
                /* gray-500 */
                cursor: pointer;
            }
        </style>
    @endsection --}}
</div>
