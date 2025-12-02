<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-normal text-gray-700">Ringkasan Umum</h1>
    </div>

    {{-- Dropdown Section Selector --}}
    <div class="mb-6">
        <flux:select wire:model.live="selectedSection" class="w-full max-w-full">
            @canany(['kasir.pesanan.kelola', 'kasir.laporan.kelola'])
                <option value="kasir" selected>Kasir</option>
            @endcanany
            @canany(['produksi.rencana.kelola', 'produksi.mulai', 'produksi.laporan.kelola'])
                <option value="produksi">Produksi</option>
            @endcanany
            @canany(['inventori.persediaan.kelola', 'inventori.produk.kelola', 'inventori.belanja.rencana.kelola',
                'inventori.hitung.kelola', 'inventori.satuan.kelola', 'inventori.kategori.kelola',
                'inventori.tipe-biaya.kelola', 'inventori.laporan.kelola'])
                <option value="inventori">Inventori</option>
            @endcanany
        </flux:select>
    </div>

    {{-- Calendar and Today's Orders Section --}}
    <div class="flex flex-col overflow-hidden bg-white rounded-lg shadow p-6 md:flex-row gap-6" style="height: 26rem;">
        <!-- Kalender -->
        <div class="bg-white border border-gray-300 max-h-96 rounded-lg p-6 w-full md:w-1/3">
            <h2 class="text-base font-semibold mb-4">Kalender Kasir Pawon3D</h2>

            <!-- Navigasi Bulan -->
            <div class="flex items-center justify-between mb-4">
                <flux:button type="button" icon="chevron-left" wire:click="previousMonth" variant="ghost"
                    class="text-gray-500 hover:text-black" />
                <div class="font-normal text-sm text-center">
                    {{ \Carbon\Carbon::parse($currentMonth)->translatedFormat('F') }}
                    <br>{{ \Carbon\Carbon::parse($currentMonth)->year }}
                </div>
                <flux:button type="button" icon="chevron-right" wire:click="nextMonth" variant="ghost"
                    class="text-gray-500 hover:text-black" />
            </div>

            <!-- Hari -->
            <div class="grid grid-cols-7 text-center text-xs text-gray-500 font-medium mb-2">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                    <div @class([
                        $day === 'Min' || $day === 'Sab' ? 'text-red-500' : 'text-gray-700',
                    ])>{{ $day }}</div>
                @endforeach
            </div>

            <!-- Tanggal -->
            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                @foreach ($calendar as $date => $info)
                    @php
                        $carbonDate = \Carbon\Carbon::parse($date);
                        $dayOfWeek = $carbonDate->translatedFormat('D');
                        $isWeekend = in_array($dayOfWeek, ['Min', 'Sab']);
                        $hasTransaction = $info['hasTransaction'] ?? false;
                    @endphp
                    <div class="flex flex-col items-center justify-center">
                        <button wire:click="selectDate('{{ $date }}')" @class([
                            'rounded-lg w-8 h-8 flex items-center justify-center transition-colors',
                            'bg-gray-800 text-white' => $selectedDate === $date,
                            'text-red-500' =>
                                $selectedDate !== $date && $isWeekend && $info['isCurrentMonth'],
                            'text-gray-800' =>
                                $selectedDate !== $date && !$isWeekend && $info['isCurrentMonth'],
                            'text-red-300' =>
                                $selectedDate !== $date && $isWeekend && !$info['isCurrentMonth'],
                            'text-gray-400' =>
                                $selectedDate !== $date && !$isWeekend && !$info['isCurrentMonth'],
                            'hover:bg-gray-100' => $selectedDate !== $date,
                        ])>
                            {{ $carbonDate->day }}
                        </button>
                        @if ($hasTransaction)
                            <span class="w-1 h-1 bg-red-500 rounded-full mt-0.5"></span>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>

        <!-- Detail Transaksi -->
        <div class="w-full md:w-2/3 overflow-y-auto scroll-hide">
            <h2 class="text-base font-normal mb-3">
                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F') }}
            </h2>

            <!-- Hari Ini -->
            <div class="space-y-3">
                @forelse($todayTransactions as $trx)
                    <div
                        class="bg-white border border-gray-200 rounded-lg p-4 flex items-start justify-between hover:shadow-sm transition-shadow">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                <span class="inline-block w-2 h-2 rounded-full bg-gray-800"></span>
                                <span>{{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                                    {{ $trx->time }}</span>
                                <span
                                    class="text-red-500">(H-{{ (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($trx->date)->startOfDay(), false) }})</span>
                            </div>
                            <p class="font-semibold text-sm mb-1">
                                {{ strtoupper($trx->invoice_number) }} {{ $trx->name ? $trx->name : '' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ str_replace('-', ' ', ucwords($trx->method)) }}</p>
                        </div>
                        <flux:button icon="chevron-right" type="button" variant="ghost" size="sm"
                            href="{{ route('transaksi.rincian-pesanan', $trx->id) }}"
                            class="text-gray-400 hover:text-gray-800" />
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Tidak ada transaksi pada hari ini.</p>
                @endforelse
            </div>

            <!-- Lainnya -->
            @if ($otherTransactions->count())
                <div class="mt-6">
                    <h3 class="text-sm font-normal mb-3 text-gray-600">Lainnya</h3>
                    <div class="space-y-3">
                        @foreach ($otherTransactions as $trx)
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 flex items-start justify-between hover:shadow-sm transition-shadow">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                        <span class="inline-block w-2 h-2 rounded-full bg-gray-800"></span>
                                        <span>{{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                                            {{ $trx->time }}</span>
                                        <span
                                            class="text-red-500">(H-{{ (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($trx->date)->startOfDay(), false) }})</span>
                                    </div>
                                    <p class="font-semibold text-sm mb-1">
                                        {{ strtoupper($trx->invoice_number) }} {{ $trx->name ? $trx->name : '' }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ str_replace('-', ' ', ucwords($trx->method)) }}
                                    </p>
                                </div>
                                <flux:button icon="chevron-right" type="button" variant="ghost" size="sm"
                                    href="{{ route('transaksi.rincian-pesanan', $trx->id) }}"
                                    class="text-gray-400 hover:text-gray-800" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Pesanan Terdekat --}}
    <div class="bg-white mt-8 rounded-lg shadow p-6">
        <div class="flex flex-row justify-between items-center mb-6">
            <h2 class="text-base font-semibold">Pesanan Terdekat</h2>
        </div>

        {{-- Search and Filter --}}
        <div class="flex items-center justify-between gap-6 mb-6">
            <div class="flex-1 flex items-center gap-4">
                {{-- Search Input --}}
                <div class="flex-1 bg-white border border-gray-300 rounded-lg px-4 py-0 flex items-center gap-2">
                    <flux:icon icon="magnifying-glass" class="size-5 text-gray-400" />
                    <input type="text" wire:model.live="search" placeholder="Cari Pesanan"
                        class="flex-1 border-0 focus:ring-0 text-sm text-gray-700 py-2" />
                </div>

                {{-- Filter Button --}}
                <button class="flex items-center gap-1 text-gray-600 hover:text-gray-800 transition-colors">
                    <flux:icon icon="funnel" class="size-5" />
                    <span class="text-sm font-medium">Filter</span>
                </button>
            </div>
        </div>

        <x-table.paginated :headers="[
            ['label' => 'ID Transaksi', 'sortable' => true, 'sort-by' => 'invoice_number'],
            ['label' => 'Tanggal Ambil', 'sortable' => true, 'sort-by' => 'date'],
            ['label' => 'Daftar Produk', 'sortable' => false],
            ['label' => 'Pembeli', 'sortable' => true, 'sort-by' => 'name'],
            ['label' => 'Kasir', 'sortable' => true, 'sort-by' => 'user_name'],
            ['label' => 'Status Bayar', 'sortable' => true, 'sort-by' => 'payment_status'],
            ['label' => 'Status Pesanan', 'sortable' => true, 'sort-by' => 'status'],
        ]" :paginator="$transactions" emptyMessage="Belum ada pesanan." headerBg="#3f4e4f"
            headerText="#f8f4e1">

            @foreach ($transactions as $transaction)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('transaksi.rincian-pesanan', $transaction->id) }}"
                            class="text-gray-900 hover:text-gray-600">
                            {{ $transaction->invoice_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        @if ($method == 'siap-beli')
                            {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d M Y  H:i') : '-' }}
                            @if ($transaction->start_date)
                                <br>
                                <span
                                    class="text-red-500">(H-{{ (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($transaction->start_date)->startOfDay(), false) }})</span>
                            @endif
                        @else
                            {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d M Y') : '-' }}
                            {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '-' }}
                            @if ($transaction->date)
                                <br>
                                <span
                                    class="text-red-500">(H-{{ (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($transaction->date)->startOfDay(), false) }})</span>
                            @endif
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $transaction->details->count() > 0
                            ? $transaction->details->map(fn($d) => $d->product?->name)->filter()->implode(', ')
                            : 'Tidak ada produk' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $transaction->name ?: '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $transaction->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span @class([
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                            'bg-green-100 text-green-800' => $transaction->payment_status === 'Lunas',
                            'bg-yellow-100 text-yellow-800' =>
                                $transaction->payment_status === 'Belum Lunas',
                            'bg-blue-100 text-blue-800' =>
                                $transaction->payment_status === 'Sedang Diproses',
                            'bg-gray-100 text-gray-800' => !in_array($transaction->payment_status, [
                                'Lunas',
                                'Belum Lunas',
                                'Sedang Diproses',
                            ]),
                        ])>
                            {{ $transaction->payment_status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span @class([
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                            'bg-blue-500 text-white' => $transaction->status === 'Dapat Diambil',
                            'bg-yellow-500 text-white' => $transaction->status === 'Sedang Diproses',
                            'bg-gray-400 text-white' => $transaction->status === 'Belum Diproses',
                        ])>
                            {{ $transaction->status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    {{-- Siap Saji Saat Ini --}}
    <div class="bg-white mt-8 rounded-lg shadow p-6">
        <div class="flex flex-row justify-between items-center mb-6">
            <h2 class="text-base font-semibold">Siap Saji Saat Ini</h2>
        </div>

        {{-- Search and Filter --}}
        <div class="flex items-center justify-between gap-6 mb-6">
            <div class="flex-1 flex items-center gap-4">
                {{-- Search Input --}}
                <div class="flex-1 bg-white border border-gray-300 rounded-lg px-4 py-0 flex items-center gap-2">
                    <flux:icon icon="magnifying-glass" class="size-5 text-gray-400" />
                    <input type="text" placeholder="Cari Produk"
                        class="flex-1 border-0 focus:ring-0 text-sm text-gray-700 py-2" />
                </div>

                {{-- Filter Button --}}
                <button class="flex items-center gap-1 text-gray-600 hover:text-gray-800 transition-colors">
                    <flux:icon icon="funnel" class="size-5" />
                    <span class="text-sm font-medium">Filter</span>
                </button>
            </div>
        </div>

        <x-table.paginated :headers="[
            ['label' => 'Produk', 'sortable' => false],
            ['label' => 'ID Produksi', 'sortable' => false],
            ['label' => 'Produksi', 'sortable' => false, 'align' => 'center'],
            ['label' => 'Terjual', 'sortable' => false, 'align' => 'center'],
            ['label' => 'Tersisa', 'sortable' => false, 'align' => 'center'],
        ]" :paginator="$readyProducts" emptyMessage="Belum ada produk siap saji."
            headerBg="#3f4e4f" headerText="#f8f4e1">

            @foreach ($readyProducts as $product)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $product->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $product->code ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                        {{ $product->production_quantity ?? 0 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                        {{ $product->sold_quantity ?? 0 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                        {{ $product->stock ?? 0 }}
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>
</div>
