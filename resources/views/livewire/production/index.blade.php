<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <h1 class="text-[20px] font-semibold text-[#666666] text-center sm:text-left">Daftar Produksi</h1>
    </div>

    <!-- Info Alert -->
    <x-alert.info>
        <div class="text-[#dcd7c9] text-[14px] leading-normal">
            <p class="font-semibold mb-2">Pilih salah satu metode penjualan seperti Pesanan Reguler, Pesanan Kotak, atau
                Siap Saji untuk menampilkan <span class="font-bold">Daftar Produksi</span>.</p>
            <ul class="list-disc pl-6 space-y-1">
                <li><span class="font-bold">Pesanan Reguler : produk pesanan dalam bentuk loyangan atau paketan.</span>
                </li>
                <li><span class="font-bold">Pesanan Kotak : produk dalam bentuk snack box dengan kombinasi banyak jenis
                        dalam kotak.</span></li>
                <li><span class="font-bold">Siap Saji : produk dalam bentuk per potong yang dipajang di etalase
                        toko.</span></li>
            </ul>
        </div>
    </x-alert.info>

    <!-- Method Tabs -->
    <div class="flex flex-col sm:flex-row items-center w-full mb-9 gap-4 sm:gap-0">
        <label wire:click="$set('method', 'pesanan-reguler')"
            class="w-full sm:flex-1 h-auto sm:h-[105px] bg-[#fafafa] rounded-tl-[15px] rounded-tr-[15px] sm:rounded-tr-none rounded-bl-[15px] rounded-br-[15px] sm:rounded-br-none shadow-sm flex flex-col items-center justify-center gap-1 cursor-pointer px-5 py-4 {{ $method === 'pesanan-reguler' ? 'border-b-4 border-[#74512d]' : '' }}">
            <flux:icon icon="cake" class=" size-8" />
            <p
                class="text-[16px] text-center {{ $method === 'pesanan-reguler' ? 'font-bold text-[#74512d]' : 'font-medium text-[#6c7068] opacity-90' }}">
                Pesanan Reguler
            </p>
        </label>
        <label wire:click="$set('method', 'pesanan-kotak')"
            class="w-full sm:flex-1 h-auto sm:h-[105px] bg-[#fafafa] rounded-[15px] sm:rounded-none shadow-sm flex flex-col items-center justify-center gap-1 cursor-pointer px-5 py-4 {{ $method === 'pesanan-kotak' ? 'border-b-4 border-[#74512d]' : '' }}">
            <flux:icon icon="package-open" class="size-8" />
            <p
                class="text-[16px] text-center {{ $method === 'pesanan-kotak' ? 'font-bold text-[#74512d]' : 'font-medium text-[#6c7068] opacity-90' }}">
                Pesanan Kotak
            </p>
        </label>
        <label wire:click="$set('method', 'siap-beli')"
            class="w-full sm:flex-1 h-auto sm:h-[105px] bg-[#fafafa] rounded-tr-[15px] rounded-tl-[15px] sm:rounded-tl-none rounded-br-[15px] rounded-bl-[15px] sm:rounded-bl-none shadow-sm flex flex-col items-center justify-center gap-1 cursor-pointer px-5 py-4 {{ $method === 'siap-beli' ? 'border-b-4 border-[#74512d]' : '' }}">
            <flux:icon icon="dessert" class="size-8" />
            <p
                class="text-[16px] text-center {{ $method === 'siap-beli' ? 'font-bold text-[#74512d]' : 'font-medium text-[#6c7068] opacity-90' }}">
                Siap Saji
            </p>
        </label>
    </div>
    <!-- Content Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-4 sm:px-[30px] sm:py-6">
        <!-- Search and Actions -->
        <div class="flex flex-col lg:flex-row items-center justify-between mb-8 gap-4">
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                <!-- Search Bar -->
                <div class="w-full lg:w-[450px] flex items-center border border-[#666666] rounded-[20px] bg-white px-4 py-2">
                    <svg class="w-[30px] h-[30px] text-[#666666]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live="search" type="text" placeholder="Cari Produksi"
                        class="ml-2 flex-1 border-0 focus:ring-0 text-[16px] font-medium text-[#666666] placeholder:text-[#959595]" />
                </div>

                <!-- Filter -->
                <div class="flex items-center gap-1 text-[#666666] justify-center">
                    <svg class="w-[25px] h-[25px]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                    </svg>
                    <span class="text-[16px] font-medium px-1">Filter</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                @if ($method === 'siap-beli')
                    <flux:button variant="primary" icon="list-bullet" href="{{ route('produksi.antrian-produksi') }}"
                        wire:navigate class="w-full sm:w-auto">
                        Antrian Produksi
                    </flux:button>
                @else
                    <flux:button variant="primary" icon="list-bullet"
                        href="{{ route('produksi.pesanan', ['method' => $method]) }}" wire:navigate class="w-full sm:w-auto">
                        Antrian Pesanan
                    </flux:button>
                @endif

                <flux:button variant="primary" icon="history"
                    href="{{ route('produksi.riwayat', ['method' => $method]) }}" wire:navigate class="w-full sm:w-10" />
            </div>
        </div>

        <!-- Table -->
        @php
            // Buat headers dinamis berdasarkan method
            if ($method === 'siap-beli') {
                // Headers untuk Siap Saji: tanpa ID Pesanan dan Tanggal Ambil
                $headers = [
                    [
                        'label' => 'ID Produksi',
                        'sortable' => true,
                        'sort-by' => 'production_number',
                        'class' => 'w-[180px]',
                    ],
                    [
                        'label' => 'Tanggal Produksi',
                        'sortable' => true,
                        'sort-by' => 'start_date',
                        'class' => 'w-[185px]',
                    ],
                    ['label' => 'Daftar Produk', 'class' => 'flex-1 min-w-[255px]'],
                    ['label' => 'Koki', 'sortable' => true, 'sort-by' => 'worker_name', 'class' => 'w-[140px]'],
                    ['label' => 'Status Produksi', 'sortable' => true, 'sort-by' => 'status', 'class' => 'w-[140px]'],
                    ['label' => 'Kemajuan Produksi', 'class' => 'w-[200px]'],
                ];
            } else {
                // Headers untuk Pesanan Reguler dan Pesanan Kotak
                $headers = [
                    [
                        'label' => 'ID Produksi',
                        'sortable' => true,
                        'sort-by' => 'production_number',
                        'class' => 'w-[180px]',
                    ],
                    [
                        'label' => 'Tanggal Ambil',
                        'sortable' => true,
                        'sort-by' => 'pickup_date',
                        'class' => 'w-[185px]',
                    ],
                    ['label' => 'Daftar Produk', 'class' => 'flex-1 min-w-[255px]'],
                    ['label' => 'Koki', 'sortable' => true, 'sort-by' => 'worker_name', 'class' => 'w-[140px]'],
                    ['label' => 'Status Produksi', 'sortable' => true, 'sort-by' => 'status', 'class' => 'w-[140px]'],
                    ['label' => 'Kemajuan Produksi', 'class' => 'w-[200px]'],
                ];
            }
        @endphp

        <div class="overflow-x-auto">
            <div class="min-w-[1000px]">
                <x-table.paginated :headers="$headers" :paginator="$productions" :empty-message="'Belum ada produksi.'" header-bg="#3f4e4f"
                    header-text="#f8f4e1" body-bg="#fafafa" body-text="#666666">
            @foreach ($productions as $production)
                <tr class="border-b border-[#d4d4d4] hover:bg-gray-50" wire:key="production-{{ $production->id }}">
                    <td class="px-6 py-5 w-[180px]">
                        @if ($method == 'siap-beli')
                            <a href="{{ route('produksi.rincian-siap-beli', ['id' => $production->id]) }}"
                                wire:navigate class="text-[14px] font-medium text-[#666666] hover:underline">
                                {{ $production->production_number }}
                            </a>
                        @else
                            <a href="{{ route('produksi.rincian', $production->id) }}" wire:navigate
                                class="text-[14px] font-medium text-[#666666] hover:underline">
                                {{ $production->production_number }}
                            </a>
                        @endif
                    </td>

                    @if ($method === 'siap-beli')
                        {{-- Tanggal Produksi untuk Siap Saji --}}
                        <td class="px-6 py-5 w-[185px]">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 text-[14px] font-medium text-[#666666]">
                                    <span>
                                        {{ $production->created_at ? $production->created_at->format('d M Y') : '-' }}
                                    </span>
                                    <span>{{ $production->created_at ? $production->created_at->format('H:i') : '' }}</span>
                                </div>
                                @if ($production->created_at)
                                    @php
                                        $daysFromProduction = (int) \Carbon\Carbon::now()->diffInDays(
                                            $production->created_at,
                                            false,
                                        );
                                    @endphp
                                    <span
                                        class="text-[14px] font-medium text-[#666666]">(H-{{ abs($daysFromProduction) }})</span>
                                @endif
                            </div>
                        </td>
                    @else
                        {{-- Tanggal Ambil untuk Pesanan Reguler dan Kotak --}}
                        <td class="px-6 py-5 w-[185px]">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 text-[14px] font-medium text-[#666666]">
                                    <span>
                                        {{ $production->transaction->date ? \Carbon\Carbon::parse($production->transaction->date)->format('d M Y') : '-' }}
                                    </span>
                                    <span>{{ $production->transaction->time ? \Carbon\Carbon::parse($production->transaction->time)->format('H:i') : '' }}</span>
                                </div>
                                @if ($production->transaction->date)
                                    @php
                                        $daysUntil = (int) \Carbon\Carbon::now()->diffInDays(
                                            \Carbon\Carbon::parse($production->transaction->date),
                                            false,
                                        );
                                    @endphp
                                    <span
                                        class="text-[14px] font-medium text-[#eb5757]">(H-{{ abs($daysUntil) }})</span>
                                @endif
                            </div>
                        </td>
                    @endif

                    <td class="px-6 py-5 flex-1 min-w-[255px]">
                        <p class="text-[14px] font-medium text-[#666666] line-clamp-2">
                            {{ $production->details->count() > 0
                                ? $production->details->map(fn($d) => $d->product?->name)->filter()->implode(', ')
                                : 'Tidak ada produk' }}
                        </p>
                    </td>
                    <td class="px-6 py-5 w-[140px]">
                        <p class="text-[14px] font-medium text-[#666666]">
                            {{ $production->workers->count() > 0 ? $production->workers->first()->worker?->name : '-' }}
                        </p>
                    </td>
                    <td class="px-6 py-5 w-[140px]">
                        <div
                            class="bg-[#ffc400] rounded-[15px] px-4 py-2 inline-flex items-center justify-center min-w-[90px]">
                            <p class="text-[12px] font-bold text-[#fafafa] text-center leading-tight">
                                Sedang<br>Diproses
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-5 w-[200px]">
                        @php
                            $total_plan = $production->details->sum('quantity_plan');
                            $total_done = $production->details->sum('quantity_get');
                            $progress = $total_plan > 0 ? ($total_done / $total_plan) * 100 : 0;
                            if ($progress > 100) {
                                $progress = 100;
                            }
                        @endphp
                        <div class="flex flex-col gap-1.5">
                            <div class="w-full bg-[#eaeaea] h-[18px] rounded-[5px] overflow-hidden">
                                <div class="h-full bg-[#74512d] transition-all" style="width: {{ $progress }}%">
                                </div>
                            </div>
                            <p class="text-[12px] font-medium text-[#525252] text-center">
                                {{ number_format($progress, 0) }}% (<span
                                    class="text-[#525252]">{{ $total_done }}</span> <span
                                    class="font-normal">dari</span> <span
                                    class="text-[#525252]">{{ $total_plan }}</span>)
                            </p>
                        </div>
                    </td>
                </tr>
            @endforeach
                </x-table.paginated>
            </div>
        </div>


    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Produksi</flux:heading>
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
