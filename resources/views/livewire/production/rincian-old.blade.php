<div>
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-[30px]">
        <div class="flex items-center gap-[15px]">
            <a href="{{ route('produksi') }}"
                class="bg-[#313131] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-[5px] px-[25px] py-[10px] no-underline">
                <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 16L6 10L12 4" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; color: #f6f6f6; white-space: nowrap;">Kembali</span>
            </a>
            <h1 style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; white-space: nowrap;">
                Rincian Produksi</h1>
        </div>
        <button wire:click="riwayatPembaruan"
            class="bg-[#525252] border border-[#666666] rounded-[15px] px-[25px] py-[10px] h-[40px] flex items-center">
            <span
                style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; color: #ffffff; white-space: nowrap;">Riwayat
                Pembaruan</span>
        </button>
    </div>

    <div class="w-full flex flex-col gap-4 mt-4">
        <h1 class="text-3xl font-bold">{{ $production->production_number }}</h1>
        <p class="text-lg text-gray-500">{{ $status }}</p>
        <div class="flex items-center justify-between gap-4 flex-row">
            <div class="flex items-center gap-16 flex-row">
                @if ($production->method == 'siap-beli')
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Pembuatan Rencana</flux:heading>
                    <p class="text-sm text-start">
                        {{ $date ? \Carbon\Carbon::parse($date)->format('d / m / Y H:i') : '-' }}
                    </p>
                </div>
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Pelaksanaan Produksi</flux:heading>
                    <p class="text-sm text-start">
                        {{ $production->start_date ? \Carbon\Carbon::parse($production->start_date)->format('d / m / Y')
                        : '-' }}
                        {{ $production->time ? \Carbon\Carbon::parse($production->time)->format('H:i') : '-' }}
                    </p>
                </div>
                @else
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Pengaktifan Produksi</flux:heading>
                    <p class="text-sm text-start">
                        {{ $production->start_date ? \Carbon\Carbon::parse($production->start_date)->format('d / m / Y')
                        : '-' }}
                        {{ $production->time ? \Carbon\Carbon::parse($production->time)->format('H:i') : '-' }}
                    </p>
                </div>
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Pengambilan Pesanan</flux:heading>
                    <p class="text-sm text-start">
                        {{ $production->transaction->date
                        ? \Carbon\Carbon::parse($production->transaction->date)->format('d / m / Y')
                        : '-' }}
                        {{ $production->transaction->time ?
                        \Carbon\Carbon::parse($production->transaction->time)->format('H:i') : '-' }}
                    </p>
                </div>
                @endif

                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Produksi Selesai</flux:heading>
                    <p class="text-sm text-start">
                        {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d / m / Y H:i') : '-' }}
                    </p>

                </div>
            </div>
            <div class="flex items-center gap-16 flex-row">
                <div class="flex items-end gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Dikerjakan Oleh</flux:heading>
                    <p class="text-sm">
                        {{ $production->workers->count() > 0
                        ? $production->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ')
                        : '-' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-y-4 my-4 flex-col">
            <div class="w-full h-4 mb-4 bg-gray-200 rounded-full dark:bg-gray-700">
                <div class="h-4 bg-blue-600 rounded-full dark:bg-blue-500"
                    style="width: {{ number_format($percentage, 0) }}%">
                </div>
            </div>
            <span class="text-xs text-gray-500">
                {{ number_format($percentage, 0) }}% ({{ $total_quantity_get }} dari {{ $total_quantity_plan }})
            </span>
        </div>
        @if ($production->method != 'siap-beli')
        <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4">
            <flux:heading class="text-lg font-semibold">Catatan Pesanan</flux:heading>
            <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $production->transaction->note }}
            </flux:textarea>
        </div>
        @endif
    </div>


    <div class="w-full mt-8 flex flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Daftar Produk</flux:label>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3">Produk</th>
                        <th class="text-left px-6 py-3">Rencana Produksi</th>
                        <th class="text-left px-6 py-3">Selisih Didapatkan</th>
                        <th class="text-left px-6 py-3">Jumlah Didapatkan</th>
                        <th class="text-left px-6 py-3">Pengulangan</th>
                        <th class="text-left px-6 py-3">Gagal</th>
                        <th class="text-left px-6 py-3">Kelebihan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($production_details as $detail)
                    <tr>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ $detail->product->name ?? 'Produk Tidak Ditemukan' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ $detail->quantity_plan }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                @if ($detail->quantity_get > $detail->quantity_plan)
                                +{{ $detail->quantity_get - $detail->quantity_plan }}
                                @else
                                {{ $detail->quantity_get - $detail->quantity_plan }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ $detail->quantity_get }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ $detail->cycle }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ $detail->quantity_fail }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                @if ($detail->quantity_get > $detail->quantity_plan)
                                {{ $detail->quantity_get - $detail->quantity_plan }}
                                @else
                                0
                                @endif
                            </span>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
                <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">Total</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{ $production_details->sum('quantity_plan') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                @if ($production_details->sum(fn($d) => $d->quantity_get - $d->quantity_plan) > 0)
                                +{{ $production_details->sum(fn($d) => $d->quantity_get - $d->quantity_plan) }}
                                @else
                                {{ $production_details->sum(fn($d) => $d->quantity_get - $d->quantity_plan) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{ $production_details->sum('quantity_get') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{ $production_details->sum('cycle') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{ $production_details->sum('quantity_fail') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{-- Check if quantity_get is greater than quantity_plan --}}
                                @if ($production_details->sum('quantity_get') >
                                $production_details->sum('quantity_plan'))
                                {{ $production_details->sum(fn($d) => $d->quantity_get - $d->quantity_plan) }}
                                @else
                                0
                                @endif
                            </span>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>

        <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4">
            <flux:heading class="text-lg font-semibold">Catatan Produksi</flux:heading>
            <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $production->note }}</flux:textarea>
        </div>
    </div>

    @if ($is_start && !$is_finish)
    <div class="flex justify-between mt-16 gap-4">
        <div class="flex gap-4 items-center">
            @if ($production->method != 'siap-beli' && $total_quantity_get <= 0) <flux:button icon="pencil"
                type="button" href="{{ route('produksi.edit-produksi-pesanan', $production->id) }}">
                Ubah Rencana Produksi
                </flux:button>
                @endif
        </div>
        <div class="flex gap-4 items-center">
            <flux:button icon="check-circle" type="button" wire:click="finish">
                Selesaikan Produksi
            </flux:button>
            @if ($total_quantity_get < $total_quantity_plan) <flux:button icon="pencil-square" type="button"
                variant="primary" href="{{ route('produksi.mulai', $production->id) }}">
                Dapatkan Hasil
                </flux:button>
                @endif
        </div>
    </div>
    @elseif (!$is_start && !$is_finish)
    <div class="flex justify-end mt-16 gap-4">
        <flux:button wire:click="confirmDelete" icon="trash" type="button" variant="danger" />
        <flux:button icon="pencil" type="button" variant="primary" href="{{ route('produksi.edit', $production->id) }}">
            Ubah Daftar Produk
        </flux:button>
        <flux:button icon="dessert" type="button" variant="primary" wire:click="start">
            Mulai Produksi
        </flux:button>
    </div>
    @endif



    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <h1 size="lg">Riwayat Pembaruan {{ $production->production_number }}</h1>
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