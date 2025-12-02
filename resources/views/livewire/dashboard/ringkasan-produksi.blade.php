<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-normal text-gray-700">Ringkasan Umum</h1>
    </div>

    {{-- Dropdown Section Selector --}}
    <div class="mb-6">
        <flux:select wire:model.live="selectedSection" class="w-full max-w-full">
            @canany(['kasir.pesanan.kelola', 'kasir.laporan.kelola'])
                <option value="kasir">Kasir</option>
            @endcanany
            @canany(['produksi.rencana.kelola', 'produksi.mulai', 'produksi.laporan.kelola'])
                <option value="produksi" selected>Produksi</option>
            @endcanany
            @canany(['inventori.persediaan.kelola', 'inventori.produk.kelola', 'inventori.belanja.rencana.kelola',
                'inventori.hitung.kelola', 'inventori.satuan.kelola', 'inventori.kategori.kelola',
                'inventori.tipe-biaya.kelola', 'inventori.laporan.kelola'])
                <option value="inventori">Inventori</option>
            @endcanany
        </flux:select>
    </div>

    {{-- Calendar and Today's Productions Section --}}
    <div class="flex flex-col overflow-hidden bg-white rounded-lg shadow p-6 md:flex-row gap-6" style="height: 26rem;">
        <!-- Kalender -->
        <div class="bg-white border border-gray-300 rounded-lg max-h-96 p-6 w-full md:w-1/3">
            <h2 class="text-base font-semibold mb-4">Kalender Produksi Pawon3D</h2>

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
                        $hasProduction = $info['hasProduction'] ?? false;
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
                        @if ($hasProduction)
                            <span class="w-1 h-1 bg-red-500 rounded-full mt-0.5"></span>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>

        <!-- Detail Produksi -->
        <div class="w-full md:w-2/3 overflow-y-auto scroll-hide">
            <h2 class="text-base font-normal mb-3">
                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F') }}
            </h2>

            <!-- Hari Ini -->
            <div class="space-y-3">
                @forelse($todayProductions as $prod)
                    <div
                        class="bg-white border border-gray-200 rounded-lg p-4 flex items-start justify-between hover:shadow-sm transition-shadow">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                <span class="inline-block w-2 h-2 rounded-full bg-gray-800"></span>
                                <span>{{ \Carbon\Carbon::parse($prod->start_date)->format('d M Y') }}
                                    {{ $prod->start_time ?? '' }}</span>
                                @php
                                    $diff = (int) now()
                                        ->startOfDay()
                                        ->diffInDays(\Carbon\Carbon::parse($prod->start_date)->startOfDay(), false);
                                    $label = $diff > 0 ? "H-{$diff}" : ($diff < 0 ? 'H' . '+' . abs($diff) : 'H-0');
                                @endphp
                                <span class="text-red-500">({{ $label }})</span>
                            </div>
                            <p class="font-semibold text-sm mb-1">
                                {{ strtoupper($prod->code) }}
                                @if ($prod->method === 'siap-beli')
                                    {{ $prod->production_number }} (siap saji)
                                @else
                                    {{ $prod->transaction?->invoice_number ?? 'Tanpa Transaksi' }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">{{ str_replace('-', ' ', ucwords($prod->method)) }}</p>
                        </div>
                        <flux:button icon="chevron-right" type="button" variant="ghost" size="sm"
                            href="{{ route('produksi.rincian', $prod->id) }}"
                            class="text-gray-400 hover:text-gray-800" />
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Tidak ada produksi pada hari ini.</p>
                @endforelse
            </div>

            <!-- Lainnya -->
            @if ($otherProductions->count())
                <div class="mt-6">
                    <h3 class="text-sm font-normal mb-3 text-gray-600">Lainnya</h3>
                    <div class="space-y-3">
                        @foreach ($otherProductions as $prod)
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 flex items-start justify-between hover:shadow-sm transition-shadow">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                        <span class="inline-block w-2 h-2 rounded-full bg-gray-800"></span>
                                        <span>{{ \Carbon\Carbon::parse($prod->start_date)->format('d M Y') }}
                                            {{ $prod->start_time ?? '' }}</span>
                                        @php
                                            $diff = (int) now()
                                                ->startOfDay()
                                                ->diffInDays(
                                                    \Carbon\Carbon::parse($prod->start_date)->startOfDay(),
                                                    false,
                                                );
                                            $label =
                                                $diff > 0 ? "H-{$diff}" : ($diff < 0 ? 'H' . '+' . abs($diff) : 'H-0');
                                        @endphp
                                        <span class="text-red-500">({{ $label }})</span>
                                    </div>
                                    <p class="font-semibold text-sm mb-1">
                                        {{ strtoupper($prod->code) }}
                                        @if ($prod->method === 'siap-beli')
                                            {{ $prod->production_number }} (siap saji)
                                        @else
                                            {{ $prod->transaction?->invoice_number ?? 'Tanpa Transaksi' }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ str_replace('-', ' ', ucwords($prod->method)) }}</p>
                                </div>
                                <flux:button icon="chevron-right" type="button" variant="ghost" size="sm"
                                    href="{{ route('produksi.rincian', $prod->id) }}"
                                    class="text-gray-400 hover:text-gray-800" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Pesanan dan Rencana Siap Saji Terdekat --}}
    <div class="bg-white mt-8 rounded-lg shadow p-6">
        <div class="flex flex-row justify-between items-center mb-6">
            <h2 class="text-base font-semibold">Pesanan dan Rencana Siap Saji Terdekat</h2>
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
            [
                'label' => 'ID Transaksi',
                'sortable' => true,
                'sort-by' => 'code',
                'align' => 'left',
                'class' => 'w-[140px]',
            ],
            [
                'label' => 'Tanggal Ambil',
                'sortable' => true,
                'sort-by' => 'start_date',
                'align' => 'left',
                'class' => 'w-[150px]',
            ],
            ['label' => 'Daftar Produk', 'sortable' => false, 'class' => 'w-auto'],
            ['label' => 'Pembeli', 'sortable' => false, 'align' => 'left', 'class' => 'w-[120px]'],
            ['label' => 'Kasir', 'sortable' => false, 'align' => 'left', 'class' => 'w-[100px]'],
            [
                'label' => 'Status Pesanan',
                'sortable' => true,
                'sort-by' => 'status',
                'align' => 'left',
                'class' => 'w-[140px]',
            ],
        ]" :paginator="$nearestProductions" emptyMessage="Belum ada produksi terdekat."
            headerBg="#3f4e4f" headerText="#f8f4e1">

            @foreach ($nearestProductions as $production)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-5 whitespace-nowrap text-sm align-top">
                        <a href="{{ route('produksi.rincian', $production->id) }}"
                            class="text-gray-900 hover:text-gray-600">
                            {{ $production->production_number }}
                        </a>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        {{ $production->start_date ? \Carbon\Carbon::parse($production->start_date)->format('d M Y  H:i') : '-' }}
                        @if ($production->start_date)
                            <br>
                            @php
                                $diff = (int) now()
                                    ->startOfDay()
                                    ->diffInDays(\Carbon\Carbon::parse($production->start_date)->startOfDay(), false);
                                $label = $diff > 0 ? "H-{$diff}" : ($diff < 0 ? 'H' . '+' . abs($diff) : 'H-0');
                            @endphp
                            <span class="text-red-500">({{ $label }})</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-sm text-gray-700 align-top">
                        <div class="max-w-xs truncate">
                            @php
                                $productItems = $production->details
                                    ->map(function ($d) {
                                        $name = $d->product?->name ?? ($d->product_name ?? '—');
                                        $plan = $d->quantity_plan ?? 0;
                                        $done = $d->quantity_get ?? 0;
                                        return "{$name} ({$plan}→{$done})";
                                    })
                                    ->filter()
                                    ->values();

                                $fullList = $productItems->implode(', ');
                                $baseDisplay =
                                    $productItems->count() > 3
                                        ? $productItems->take(3)->implode(', ') .
                                            ' dan ' .
                                            ($productItems->count() - 3) .
                                            ' lainnya'
                                        : $productItems->implode(', ');

                                $display = \Illuminate\Support\Str::limit($baseDisplay, 60);
                            @endphp
                            <span title="{{ $fullList }}">{{ $display ?: 'Tidak ada produk' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        {{ $production->transaction?->name ?? '-' }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        {{ $production->transaction?->user->name ?? '-' }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm align-top">
                        <span @class([
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                            'bg-gray-400 text-white' => $production->status === 'Belum Diproses',
                            'bg-yellow-500 text-white' => $production->status === 'Sedang Diproses',
                        ])>
                            {{ $production->status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    {{-- Produksi Berlangsung --}}
    <div class="bg-white mt-8 rounded-lg shadow p-6">
        <div class="flex flex-row justify-between items-center mb-6">
            <h2 class="text-base font-semibold">Produksi Berlangsung</h2>
        </div>

        {{-- Search and Filter --}}
        <div class="flex items-center justify-between gap-6 mb-6">
            <div class="flex-1 flex items-center gap-4">
                {{-- Search Input --}}
                <div class="flex-1 bg-white border border-gray-300 rounded-lg px-4 py-0 flex items-center gap-2">
                    <flux:icon icon="magnifying-glass" class="size-5 text-gray-400" />
                    <input type="text" placeholder="Cari Produksi"
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
            ['label' => 'ID Produksi', 'sortable' => false, 'align' => 'left', 'class' => 'w-[140px]'],
            ['label' => 'Tanggal Ambil', 'sortable' => false, 'align' => 'left', 'class' => 'w-[150px]'],
            ['label' => 'Daftar Produk', 'sortable' => false, 'align' => 'left', 'class' => 'w-auto'],
            ['label' => 'Pekerja', 'sortable' => false, 'align' => 'left', 'class' => 'w-[120px]'],
            ['label' => 'Status Produksi', 'sortable' => false, 'align' => 'left', 'class' => 'w-[140px]'],
            ['label' => 'Kemajuan Produksi', 'sortable' => false, 'align' => 'left', 'class' => 'w-[200px]'],
        ]" :paginator="$ongoingProductions"
            emptyMessage="Tidak ada produksi yang sedang berlangsung." headerBg="#3f4e4f" headerText="#f8f4e1">

            @foreach ($ongoingProductions as $production)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-5 whitespace-nowrap text-sm align-top">
                        <a href="{{ route('produksi.rincian', $production->id) }}"
                            class="text-gray-900 hover:text-gray-600">
                            {{ $production->production_number }}
                        </a>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        {{ $production->start_date ? \Carbon\Carbon::parse($production->start_date)->format('d M Y  H:i') : '-' }}
                        @if ($production->start_date)
                            <br>
                            @php
                                $diff = (int) now()
                                    ->startOfDay()
                                    ->diffInDays(\Carbon\Carbon::parse($production->start_date)->startOfDay(), false);
                                $label = $diff > 0 ? "H-{$diff}" : ($diff < 0 ? 'H' . '+' . abs($diff) : 'H-0');
                            @endphp
                            <span class="text-red-500">({{ $label }})</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-sm text-gray-700 align-top">
                        <div class="max-w-xs truncate">
                            @php
                                $productItems = $production->details
                                    ->map(function ($d) {
                                        $name = $d->product?->name ?? ($d->product_name ?? '—');
                                        $plan = $d->quantity_plan ?? 0;
                                        $done = $d->quantity_get ?? 0;
                                        return "{$name} ({$plan}→{$done})";
                                    })
                                    ->filter()
                                    ->values();

                                $fullList = $productItems->implode(', ');
                                $baseDisplay =
                                    $productItems->count() > 3
                                        ? $productItems->take(3)->implode(', ') .
                                            ' dan ' .
                                            ($productItems->count() - 3) .
                                            ' lainnya'
                                        : $productItems->implode(', ');

                                $display = \Illuminate\Support\Str::limit($baseDisplay, 60);
                            @endphp
                            <span title="{{ $fullList }}">{{ $display ?: 'Tidak ada produk' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        {{ $production->workers->count() > 0
                            ? $production->workers->map(fn($w) => $w->name)->filter()->implode(', ')
                            : '-' }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm align-top">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white">
                            Sedang Diproses
                        </span>
                    </td>
                    <td class="px-6 py-5 text-sm text-gray-700 align-top">
                        @php
                            $total_plan = $production->details->sum('quantity_plan');
                            $total_done = $production->details->sum('quantity_get');
                            $progress = $total_plan > 0 ? ($total_done / $total_plan) * 100 : 0;
                            $progress = min($progress, 100);
                        @endphp
                        <span class="text-xs whitespace-nowrap">{{ number_format($progress, 0) }}%
                            ({{ $total_done }} dari {{ $total_plan }})
                        </span>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>
</div>
