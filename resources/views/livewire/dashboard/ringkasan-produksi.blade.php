<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Ringkasan Umum</h1>
        <div class="flex gap-2 items-center">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>
        </div>
    </div>
    <div class="flex items-center mr-4 gap-2 my-8">
        @can('Kasir')
        <div class="cursor-pointer">
            <a href="{{ route('ringkasan-kasir') }}"
                class="text-gray-800 bg-gray-200 rounded-xl py-2 px-4 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                Kasir
            </a>
        </div>
        @endcan
        @can('Produksi')

        <div class="cursor-pointer">
            <a href="{{ route('ringkasan-produksi') }}"
                class="text-gray-100 bg-gray-800 rounded-xl py-2 px-4 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                Produksi
            </a>
        </div>
        @endcan
        @can('Inventori')

        <div class="cursor-pointer">
            <a href="{{ route('ringkasan-inventori') }}"
                class="text-gray-800 bg-gray-200 rounded-xl py-2 px-4 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                Inventori
            </a>
        </div>
        @endcan

    </div>
    <div class="flex flex-col overflow-hidden  bg-white rounded-lg shadow p-4 md:flex-row gap-6" style="height: 26rem;">
        <!-- Kalender -->
        <div class="bg-white border border-gray-500 max-h-96 rounded-lg shadow p-4 w-full md:w-1/3">
            <h2 class="text-lg font-semibold mb-4">Kalender Pawon3D</h2>

            <!-- Navigasi Bulan -->
            <div class="flex items-center justify-between mb-4">
                <flux:button type="button" icon="chevron-left" wire:click="previousMonth"
                    class="text-gray-500 hover:text-black" />
                <div class="font-semibold text-center">
                    {{ \Carbon\Carbon::parse($currentMonth)->translatedFormat('F Y') }}
                </div>
                <flux:button type="button" icon="chevron-right" wire:click="nextMonth"
                    class="text-gray-500 hover:text-black" />
            </div>

            <!-- Hari -->
            <div class="grid grid-cols-7 text-center text-sm text-gray-500 font-semibold mb-2">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                <div @class([ $day==='Min' || $day==='Sab' ? 'text-red-500' : 'text-gray-500' , ])>{{ $day }}</div>
                @endforeach
            </div>

            <!-- Tanggal -->
            <div class="grid grid-cols-7 gap-1 text-center text-sm">
                @foreach ($calendar as $date => $info)
                @php
                $carbonDate = \Carbon\Carbon::parse($date);
                $dayOfWeek = $carbonDate->translatedFormat('D');
                $isWeekend = in_array($dayOfWeek, ['Min', 'Sab']);
                $hasProduction = $info['hasProduction'] ?? false;
                @endphp
                <div class="flex flex-col items-center">
                    <button wire:click="selectDate('{{ $date }}')" class="rounded-lg w-8 h-8
            {{ $selectedDate === $date ? 'bg-black text-white' : ($info['isCurrentMonth'] ? ($isWeekend ? 'text-red-500' : 'text-black') : ($isWeekend ? 'text-red-300' : 'text-gray-400')) }}
            hover:bg-gray-200">
                        {{ $carbonDate->day }}
                    </button>
                    @if ($hasProduction)
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mt-1"></span>
                    @endif
                </div>
                @endforeach
            </div>

        </div>

        <!-- Detail Transaksi -->
        <div class="w-full md:w-2/3 overflow-y-auto scroll-hide">
            <h2 class="text-lg font-semibold mb-2">
                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F') }}
            </h2>

            <!-- Hari Ini -->
            <div class="space-y-3">
                @forelse($todayProductions as $prod)
                <div class="bg-white border rounded-lg shadow p-4 flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-500 flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-black"></span>
                            {{ \Carbon\Carbon::parse($prod->start_date)->format('d M Y') }} {{ $prod->time }}
                        </p>
                        <p class="font-semibold mt-1">
                            {{ strtoupper($prod->production_number) }} ( {{ $prod->workers->count() > 0
                            ? $prod->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ')
                            : 'Tidak ada pekerja' }} )
                        </p>
                        <p class="text-sm text-gray-500 capitalize">{{ str_replace('-', ' ', $prod->method) }}</p>
                    </div>
                    <flux:button icon="chevron-right" type="button" variant="ghost"
                        href="{{ route('produksi.rincian', $prod->id) }}" class="text-gray-500 hover:text-black" />
                </div>
                @empty
                <p class="text-sm text-gray-500">Tidak ada produksi pada hari ini.</p>
                @endforelse
            </div>

            <!-- Lainnya -->
            @if($otherProductions->count())
            <div class="mt-6">
                <h3 class="text-sm font-semibold mb-2 text-gray-600">Lainnya</h3>
                <div class="space-y-3">
                    @foreach ($otherProductions as $prod)
                    <div class="bg-white border rounded-lg shadow p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs text-gray-500 flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full bg-black"></span>
                                {{ \Carbon\Carbon::parse($prod->start_date)->format('d M Y') }} {{ $prod->time }}
                            </p>
                            <p class="font-semibold mt-1">
                                {{ strtoupper($prod->production_number) }} ( {{ $prod->workers->count() > 0
                                ? $prod->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ')
                                : 'Tidak ada pekerja' }} )
                            </p>
                            <p class="text-sm text-gray-500 capitalize">{{ str_replace('-', ' ', $prod->method) }}</p>
                        </div>
                        <flux:button icon="chevron-right" type="button" variant="ghost"
                            href="{{ route('produksi.rincian', $prod->id) }}" class="text-gray-500 hover:text-black" />
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white mt-8 rounded-lg shadow p-4">
        <div class="flex flex-row justify-between items-center">
            <h2 class="text-lg font-normal">Produksi Terdekat</h2>
            <div class="flex items-center mr-4 gap-2">
                <div class="relative">
                    <input type="radio" name="method" id="pesanan-reguler" value="pesanan-reguler"
                        wire:model.live="method" class="absolute opacity-0 w-0 h-0">
                    <label for="pesanan-reguler" class="cursor-pointer">
                        <span
                            class="{{ $method === 'pesanan-reguler' ? 'text-gray-100 bg-gray-800' : 'text-gray-800 bg-white' }} rounded-xl p-2 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                            Pesanan Reguler
                        </span>
                    </label>
                </div>

                <div class="relative">
                    <input type="radio" name="method" id="pesanan-kotak" value="pesanan-kotak" wire:model.live="method"
                        class="absolute opacity-0 w-0 h-0">
                    <label for="pesanan-kotak" class="cursor-pointer">
                        <span
                            class="{{ $method === 'pesanan-kotak' ? 'text-gray-100 bg-gray-800' : 'text-gray-800 bg-white' }} rounded-xl p-2 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                            Pesanan Kotak
                        </span>
                    </label>
                </div>
                <div class="relative">
                    <input type="radio" name="method" id="siap-beli" value="siap-beli" wire:model.live="method"
                        class="absolute opacity-0 w-0 h-0">
                    <label for="siap-beli" class="cursor-pointer">
                        <span
                            class="{{ $method === 'siap-beli' ? 'text-gray-100 bg-gray-800' : 'text-gray-800 bg-white' }} rounded-xl p-2 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                            Siap Saji
                        </span>
                    </label>
                </div>

            </div>
        </div>

        @if ($productions->isEmpty())
        <div class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center mt-8">
            <p class="text-gray-700 font-semibold">Belum ada produksi.</p>
        </div>
        @else
        <div class="bg-white rounded-xl border shadow-sm mt-8">
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="px-6 py-3 font-semibold cursor-pointer" wire:click="sortBy('production_number')">
                                ID Produk
                                <span>{{ $sortDirection === 'asc' && $sortField === 'production_number' ? '↑' : '↓'
                                    }}</span>
                            </th>
                            <th class="px-6 py-3 font-semibold cursor-pointer" wire:click="sortBy('start_date')">
                                Jadwal
                                Produksi
                                <span>{{ $sortDirection === 'asc' && $sortField === 'start_date' ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 font-semibold cursor-pointer" wire:click='sortBy("product_name")'>
                                Daftar Produk
                                <span>{{ $sortDirection === 'asc' && $sortField === 'product_name' ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 font-semibold cursor-pointer" wire:click='sortBy("worker_name")'>
                                Pekerja
                                <span>{{ $sortDirection === 'asc' && $sortField === 'worker_name' ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 font-semibold cursor-pointer" wire:click="sortBy('status')">Status
                                <span>{{ $sortDirection === 'asc' && $sortField === 'status' ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 font-semibold">Kemajuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-900">
                        @foreach ($productions as $production)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- ID Produk -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('produksi.rincian', $production->id) }}">
                                    {{ $production->production_number }}
                                </a>
                            </td>

                            <!-- Jadwal Produksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $production->start_date ?
                                \Carbon\Carbon::parse($production->start_date)->format('d-m-Y') : '-' }}
                            </td>

                            <!-- Daftar Produk -->
                            <td class="px-6 py-4 max-w-xs truncate">
                                {{ $production->details->count() > 0
                                ? $production->details->map(fn($d) => $d->product?->name)->filter()->implode(', ')
                                : 'Tidak ada produk' }}
                            </td>

                            <!-- Pekerja -->
                            <td class="px-6 py-4 max-w-xs truncate">
                                {{ $production->workers->count() > 0
                                ? $production->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ')
                                : 'Tidak ada pekerja' }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $production->status === 'selesai'
                                ? 'bg-green-100 text-green-800'
                                : ($production->status === 'berjalan'
                                    ? 'bg-blue-100 text-blue-800'
                                    : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst($production->status) }}
                                </span>
                            </td>

                            <!-- Kemajuan Produksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                $total_plan = $production->details->sum('quantity_plan');
                                $total_done = $production->details->sum('quantity_get');
                                $progress = $total_plan > 0 ? ($total_done / $total_plan) * 100 : 0;
                                if ($progress > 100) {
                                $progress = 100;
                                }
                                @endphp

                                <div class="flex flex-col gap-1">
                                    <div class="w-full bg-gray-200 h-4 rounded-full overflow-hidden">
                                        <div class="h-4 bg-blue-600 rounded-full transition-all"
                                            style="width: {{ number_format($progress, 0) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ number_format($progress, 0) }}% ({{ $total_done }} dari
                                        {{ $total_plan }})
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $productions->links() }}
            </div>
        </div>
        @endif
    </div>
</div>