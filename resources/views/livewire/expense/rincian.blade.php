<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <a href="{{ route('belanja') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
                Kembali
            </a>
            <h1 class="text-2xl">Rincian Belanja Persediaan</h1>
        </div>
        <div class="flex gap-2 items-center">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>

            <!-- Tombol Riwayat Pembaruan -->
            <button type="button" wire:click="riwayatPembaruan"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Riwayat Pembaruan
            </button>
        </div>
    </div>
    <div class="flex items-center border border-gray-500 rounded-lg p-4">
        <flux:icon icon="exclamation-triangle" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Sed pharetra netus gravida non curabitur fermentum etiam. Lorem
                orci auctor adipiscing vel blandit. In in integer viverra proin risus eu eleifend.
            </p>
        </div>
    </div>

    <div class="w-full flex flex-col gap-4 mt-4">
        <h1 class="text-3xl font-bold">{{ $expense->expense_number }}</h1>
        <p class="text-lg text-gray-500">{{ $status }}</p>
        <div class="flex items-center justify-between gap-4 flex-row">

            <div class="flex items-center gap-16 flex-row">
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Belanja</flux:heading>
                    <p class="text-sm text-start">
                        {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') : '-' }}
                    </p>
                </div>

                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Selesai</flux:heading>
                    <p class="text-sm text-start">
                        {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d/m/Y') : '-' }}
                    </p>
                </div>

            </div>
            <div class="flex items-center gap-16 flex-row">
                <div class="flex items-end gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Toko Persediaan</flux:heading>
                    <p class="text-sm text-end">{{ $expense->supplier->name }}</p>
                </div>

                <div class="flex items-end gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Dibelanja Oleh</flux:heading>
                    <p class="text-sm">{{ $logName }}</p>
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
                {{ number_format($percentage, 0) }}%
            </span>
        </div>

        <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4">
            <flux:heading class="text-lg font-semibold">Catatan Belanja</flux:heading>
            <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $expense->note }}</flux:textarea>
        </div>
    </div>


    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Daftar Belanja Persediaan</flux:label>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3">Barang Persediaan</th>
                        <th class="text-left px-6 py-3">Jumlah Diharapkan</th>
                        <th class="text-left px-6 py-3">Jumlah Didapatkan</th>
                        <th class="text-left px-6 py-3">Satuan Ukur Belanja</th>
                        <th class="text-left px-6 py-3">Harga / Satuan</th>
                        <th class="text-left px-6 py-3">Total Harga</th>
                        <th class="text-left px-6 py-3">Total Harga (Harus Dibayar)</th>
                        {{-- <th class="text-left px-6 py-3">exp</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenseDetails as $detail)
                        <tr>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail->material->name ?? 'Barang Tidak Ditemukan' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail->quantity_expect }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail->quantity_get }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail->unit->name ?? '' }} ({{ $detail->unit->alias ?? '' }})
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    Rp{{ number_format($detail->price_expect, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    Rp{{ number_format($detail->total_expect, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    Rp{{ number_format($detail->total_actual, 0, ',', '.') }}
                                </span>
                            </td>
                            {{-- <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail->expiry_date ? \Carbon\Carbon::parse($detail->expiry_date)->format('d/m/Y') : '-' }}
                                </span>
                            </td> --}}
                        </tr>
                    @endforeach

                </tbody>
                <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3" colspan="6">
                            <span class="text-gray-700">Total Harga Keseluruhan (Sebenarnya)</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
                <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3" colspan="6">
                            <span class="text-gray-700">Total Harga Keseluruhan (Perkiraan)</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>

    @if ($is_start && !$is_finish)
        <div class="flex justify-end mt-16 gap-4">
            <flux:button icon="check-circle" type="button" variant="primary" wire:click="finish">
                Selesaikan Belanja
            </flux:button>
            @if ($status != 'Lengkap')
                <flux:button icon="shopping-cart" type="button" variant="primary"
                    href="{{ route('belanja.dapatkan-belanja', $expense->id) }}">
                    Dapatkan Belanja
                </flux:button>
            @endif
        </div>
    @elseif (!$is_start && !$is_finish)
        <div class="flex justify-end mt-16 gap-4">
            <flux:button wire:click="confirmDelete" icon="trash" type="button" variant="danger" />
            <flux:button icon="pencil" type="button" variant="primary"
                href="{{ route('belanja.edit', $expense->id) }}">
                Ubah Daftar Belanja
            </flux:button>
            <flux:button icon="shopping-cart" type="button" variant="primary" wire:click="start">
                Mulai Belanja
            </flux:button>
        </div>
    @endif



    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <h1 size="lg">Riwayat Pembaruan Daftar Belanja</h1>
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
