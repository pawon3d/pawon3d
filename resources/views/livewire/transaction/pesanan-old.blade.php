<div>
    {{-- Header with Back Button and Title --}}
    <div class="flex gap-[32px] items-center mb-8">
        <div class="flex gap-[15px] items-center">
            <a href="{{ route('transaksi') }}"
                class="bg-[#313131] flex items-center justify-center gap-[5px] px-[25px] py-[10px] rounded-[15px] shadow-sm hover:bg-[#252324] transition-colors"
                wire:navigate style="font-family: 'Montserrat', sans-serif;">
                <flux:icon icon="arrow-left" class="size-5 text-[#f8f4e1]" />
                <span class="font-semibold text-[16px] text-[#f8f4e1]">Kembali</span>
            </a>
            <p class="font-semibold text-[20px] text-[#666666]" style="font-family: 'Montserrat', sans-serif;">
                Daftar {{ $methodName }}
            </p>
        </div>
    </div>

    {{-- Content Container --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-[30px] py-[25px]">
        {{-- Search Bar --}}
        <div class="flex justify-between items-center mb-[20px]">
            <div class="flex-1 flex gap-[15px] items-center">
                {{-- Search Input --}}
                <div class="flex-1 bg-white border border-[#666666] rounded-[20px] px-[15px] py-0 flex items-center">
                    <flux:icon icon="magnifying-glass" class="size-[30px] text-[#666666]" />
                    <input wire:model.live="search" placeholder="Cari Pesanan" type="text"
                        class="flex-1 px-[10px] py-[10px] font-medium text-[16px] text-[#959595] border-0 focus:ring-0 focus:outline-none bg-transparent"
                        style="font-family: 'Montserrat', sans-serif;" />
                </div>

                {{-- Filter Button --}}
                <div class="flex items-center gap-[5px] cursor-pointer">
                    <flux:icon icon="funnel" class="size-[25px] text-[#666666]" />
                    <span class="font-medium text-[16px] text-[#666666]"
                        style="font-family: 'Montserrat', sans-serif;">
                        Filter
                    </span>
                </div>
            </div>
        </div>

        {{-- Table --}}
        @if ($transactions->isEmpty())
            <div
                class="col-span-full text-center bg-[#eaeaea] p-8 rounded-[15px] flex flex-col items-center justify-center min-h-[200px]">
                <p class="text-[#666666] font-semibold text-base mb-2" style="font-family: 'Montserrat', sans-serif;">
                    Belum ada transaksi.
                </p>
                <p class="text-[#666666] text-sm" style="font-family: 'Montserrat', sans-serif;">
                    Tambah transaksi di menu utama.
                </p>
            </div>
        @else
            <div class="flex flex-col w-full">
                {{-- Table Header --}}
                <div class="flex items-center w-full rounded-tl-[15px] rounded-tr-[15px] overflow-hidden">
                    <div
                        class="flex-none bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center gap-[5px] w-[180px]">
                        <button wire:click="sortBy('invoice_number')"
                            class="font-bold text-[14px] text-[#f8f4e1] leading-normal flex items-center gap-[5px]"
                            style="font-family: 'Montserrat', sans-serif;">
                            ID Transaksi
                            <span class="text-xs">{{ $sortField === 'invoice_number' ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕' }}</span>
                        </button>
                    </div>
                    <div
                        class="flex-none bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center gap-[5px] w-[185px]">
                        <button wire:click="sortBy('date')"
                            class="font-bold text-[14px] text-[#f8f4e1] leading-normal flex items-center gap-[5px]"
                            style="font-family: 'Montserrat', sans-serif;">
                            @if ($method == 'siap-beli')
                                Tanggal Pembelian
                            @else
                                Tanggal Ambil
                            @endif
                            <span class="text-xs">{{ $sortField === 'date' || $sortField === 'start_date' ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕' }}</span>
                        </button>
                    </div>
                    <div class="flex-1 bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center gap-[5px]">
                        <button wire:click="sortBy('product_name')"
                            class="font-bold text-[14px] text-[#f8f4e1] leading-normal flex items-center gap-[5px]"
                            style="font-family: 'Montserrat', sans-serif;">
                            Daftar Produk
                            <span class="text-xs">{{ $sortField === 'product_name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕' }}</span>
                        </button>
                    </div>
                    @if ($method != 'siap-beli')
                        <div
                            class="flex-none bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center gap-[5px] w-[130px]">
                            <button wire:click="sortBy('name')"
                                class="font-bold text-[14px] text-[#f8f4e1] leading-normal flex items-center gap-[5px]"
                                style="font-family: 'Montserrat', sans-serif;">
                                Pembeli
                                <span class="text-xs">{{ $sortField === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕' }}</span>
                            </button>
                        </div>
                    @endif
                    <div
                        class="flex-none bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center gap-[5px] w-[130px]">
                        <button wire:click="sortBy('user_name')"
                            class="font-bold text-[14px] text-[#f8f4e1] leading-normal flex items-center gap-[5px]"
                            style="font-family: 'Montserrat', sans-serif;">
                            Kasir
                            <span class="text-xs">{{ $sortField === 'user_name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕' }}</span>
                        </button>
                    </div>
                    @if ($method != 'siap-beli')
                        <div
                            class="flex-none bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center gap-[5px] w-[140px]">
                            <button wire:click="sortBy('payment_status')"
                                class="font-bold text-[14px] text-[#f8f4e1] leading-normal flex items-center gap-[5px]"
                                style="font-family: 'Montserrat', sans-serif;">
                                <div class="flex flex-col leading-normal text-left">
                                    <span>Status</span>
                                    <span>Bayar</span>
                                </div>
                                <span class="text-xs">{{ $sortField === 'payment_status' ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕' }}</span>
                            </button>
                        </div>
                        <div
                            class="flex-none bg-[#3f4e4f] px-[25px] py-[21px] h-[60px] flex items-center gap-[5px] w-[140px]">
                            <button wire:click="sortBy('status')"
                                class="font-bold text-[14px] text-[#f8f4e1] leading-normal flex items-center gap-[5px]"
                                style="font-family: 'Montserrat', sans-serif;">
                                <div class="flex flex-col leading-normal text-left">
                                    <span>Status</span>
                                    <span>Pesanan</span>
                                </div>
                                <span class="text-xs">{{ $sortField === 'status' ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕' }}</span>
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Table Body --}}
                <div class="flex flex-col w-full">
                    @foreach ($transactions as $transaction)
                        <a href="{{ route('transaksi.rincian-pesanan', $transaction->id) }}"
                            class="flex items-center w-full hover:bg-[#f0f0f0] transition-colors">
                            {{-- ID Transaksi --}}
                            <div
                                class="flex-none bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] py-0 h-[60px] flex items-center w-[180px]">
                                <p class="font-medium text-[14px] text-[#666666] truncate"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    {{ $transaction->invoice_number }}
                                </p>
                            </div>

                            {{-- Tanggal --}}
                            <div
                                class="flex-none bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] py-0 h-[60px] flex flex-col justify-center gap-[5px] w-[185px]">
                                <div class="flex gap-[10px] items-center">
                                    <p class="font-medium text-[14px] text-[#666666] truncate"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        @if ($method == 'siap-beli')
                                            {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d M Y') : '-' }}
                                        @else
                                            {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d M Y') : '-' }}
                                        @endif
                                    </p>
                                    <p class="font-medium text-[14px] text-[#666666] truncate"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        @if ($method == 'siap-beli')
                                            {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('H:i') : '-' }}
                                        @else
                                            {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('H:i') : '-' }}
                                        @endif
                                    </p>
                                </div>
                                @php
                                    $targetDate = $method == 'siap-beli' ? $transaction->start_date : $transaction->date;
                                    $daysUntil = $targetDate ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($targetDate), false) : null;
                                @endphp
                                @if ($daysUntil !== null)
                                    <p class="font-medium text-[14px] text-center truncate {{ $daysUntil <= 2 ? 'text-[#eb5757]' : 'text-[#3fa2f7]' }}"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        (H{{ $daysUntil >= 0 ? '+' : '' }}{{ $daysUntil }})
                                    </p>
                                @endif
                            </div>

                            {{-- Daftar Produk --}}
                            <div
                                class="flex-1 bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] py-0 h-[60px] flex items-center">
                                <p class="font-medium text-[14px] text-[#666666] truncate"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    {{ $transaction->details->count() > 0 ? $transaction->details->map(fn($d) => $d->product?->name)->filter()->implode(', ') : 'Tidak ada produk' }}
                                </p>
                            </div>

                            {{-- Pembeli (hidden for siap-beli) --}}
                            @if ($method != 'siap-beli')
                                <div
                                    class="flex-none bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] py-0 h-[60px] flex items-center w-[130px]">
                                    <p class="font-medium text-[14px] text-[#666666] truncate"
                                        style="font-family: 'Montserrat', sans-serif;">
                                        {{ $transaction->name ?? '-' }}
                                    </p>
                                </div>
                            @endif

                            {{-- Kasir --}}
                            <div
                                class="flex-none bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] py-0 h-[60px] flex items-center w-[130px]">
                                <p class="font-medium text-[14px] text-[#666666] truncate"
                                    style="font-family: 'Montserrat', sans-serif;">
                                    {{ ucfirst($transaction->user->name) }}
                                </p>
                            </div>

                            {{-- Status Pembayaran (hidden for siap-beli) --}}
                            @if ($method != 'siap-beli')
                                <div
                                    class="flex-none bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] py-0 h-[60px] flex items-center w-[140px]">
                                    @php
                                        $paymentStatus = strtolower($transaction->payment_status);
                                        if ($paymentStatus === 'lunas') {
                                            $bgColor = '#56c568';
                                            $textColor = '#fafafa';
                                            $label = 'Lunas';
                                        } elseif (in_array($paymentStatus, ['belum lunas', 'belum dibayar'])) {
                                            $bgColor = '#ffc400';
                                            $textColor = '#fafafa';
                                            $label = 'Belum Lunas';
                                        } else {
                                            $bgColor = '#fafafa';
                                            $textColor = '#666666';
                                            $label = ucfirst($transaction->payment_status);
                                        }
                                    @endphp
                                    <div class="px-[15px] py-[5px] rounded-[15px] min-w-[90px] flex items-center justify-center"
                                        style="background-color: {{ $bgColor }}; {{ $bgColor === '#fafafa' ? 'border: 1px solid #666666;' : '' }}">
                                        <p class="font-bold text-[12px] text-center"
                                            style="font-family: 'Montserrat', sans-serif; color: {{ $textColor }};">
                                            {{ $label }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Status Pesanan (hidden for siap-beli) --}}
                            @if ($method != 'siap-beli')
                                <div
                                    class="flex-none bg-[#fafafa] border-b border-[#d4d4d4] px-[25px] py-0 h-[60px] flex items-center w-[140px]">
                                    @php
                                        $orderStatus = strtolower($transaction->status);
                                        if (in_array($orderStatus, ['dapat diambil', 'selesai'])) {
                                            $bgColor = '#3fa2f7';
                                            $textColor = '#fafafa';
                                            $label = 'Dapat Diambil';
                                        } elseif (in_array($orderStatus, ['sedang diproses', 'diproses'])) {
                                            $bgColor = '#ffc400';
                                            $textColor = '#fafafa';
                                            $label = 'Sedang Diproses';
                                        } elseif (in_array($orderStatus, ['belum diproses', 'pending'])) {
                                            $bgColor = '#adadad';
                                            $textColor = '#fafafa';
                                            $label = 'Belum Diproses';
                                        } elseif ($orderStatus === 'draft') {
                                            $bgColor = '#f6f6f6';
                                            $textColor = '#666666';
                                            $label = 'Draft';
                                        } else {
                                            $bgColor = '#fafafa';
                                            $textColor = '#666666';
                                            $label = ucfirst($transaction->status);
                                        }
                                    @endphp
                                    <div class="px-[15px] py-[5px] rounded-[15px] min-w-[90px] flex items-center justify-center"
                                        style="background-color: {{ $bgColor }}; {{ in_array($bgColor, ['#fafafa', '#f6f6f6']) ? 'border: 1px solid #666666;' : '' }}">
                                        <p class="font-bold text-[12px] text-center"
                                            style="font-family: 'Montserrat', sans-serif; color: {{ $textColor }};">
                                            {{ $label }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Pagination --}}
            <div class="flex justify-between items-center mt-[20px]">
                <div class="flex gap-[5px] items-center font-medium text-[14px] text-[#666666]"
                    style="font-family: 'Montserrat', sans-serif; opacity: 0.7;">
                    <span>Menampilkan</span>
                    <span>{{ $transactions->firstItem() ?? 0 }}</span>
                    <span>hingga</span>
                    <span>{{ $transactions->lastItem() ?? 0 }}</span>
                    <span>dari</span>
                    <span>{{ $transactions->total() }}</span>
                    <span>baris data</span>
                </div>

                <div class="flex gap-[10px] items-center">
                    {{ $transactions->links() }}
                </div>
            </div>
        @endif
    </div>
</div>