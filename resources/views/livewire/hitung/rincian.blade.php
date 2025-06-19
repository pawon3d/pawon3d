<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <a href="{{ route('hitung') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
                Kembali
            </a>
            <h1 class="text-2xl">Rincian {{ $hitung->action }}</h1>
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
        <flux:icon icon="message-square-warning" class="size-16" />
        <div class="ml-3">
            <p class="mt-1 text-sm text-gray-500">
                Lorem ipsum dolor sit amet consectetur. Sed pharetra netus gravida non curabitur fermentum etiam. Lorem
                orci auctor adipiscing vel blandit. In in integer viverra proin risus eu eleifend.
            </p>
        </div>
    </div>

    <div class="w-full flex flex-col gap-4 mt-4">
        <h1 class="text-3xl font-bold">{{ $hitung->hitung_number }}</h1>
        <p class="text-lg text-gray-500">{{ $status }}</p>
        <div class="flex items-center justify-between gap-4 flex-row">
            <div class="flex items-center gap-16 flex-row">
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Aksi</flux:heading>
                    <p class="text-sm text-start">
                        {{ $hitung->hitung_date ? \Carbon\Carbon::parse($hitung->hitung_date)->format('d/m/Y') : '-' }}
                    </p>
                </div>
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Selesai</flux:heading>
                    <p class="text-sm text-start">
                        {{ $finish_date ? \Carbon\Carbon::parse($finish_date)->format('d/m/Y') : '-' }}
                    </p>

                </div>
            </div>
            <div class="flex items-center gap-16 flex-row">
                <div class="flex items-end gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Jenis Aksi</flux:heading>
                    <p class="text-sm">
                        {{ $hitung->action }}
                    </p>
                </div>
                <div class="flex items-end gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Dilakukan Oleh</flux:heading>
                    <p class="text-sm">{{ $logName }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4">
            <flux:heading class="text-lg font-semibold">Catatan Aksi</flux:heading>
            <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $hitung->note }}</flux:textarea>
        </div>
    </div>


    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Daftar Persediaan</flux:label>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3">Barang Persediaan</th>
                        <th class="text-left px-6 py-3">Jumlah Diharapkan</th>
                        <th class="text-left px-6 py-3">
                            Jumlah
                            @if ($hitung->action == 'Hitung Persediaan')
                                Terhitung
                            @elseif ($hitung->action == 'Catat Persediaan Rusak')
                                Rusak
                            @elseif ($hitung->action == 'Catat Persediaan Hilang')
                                Hilang
                            @endif
                        </th>
                        <th class="text-left px-6 py-3">
                            @if ($hitung->action == 'Hitung Persediaan')
                                Selisih Jumlah
                            @else
                                Jumlah Sebenarnya
                            @endif
                        </th>
                        <th class="text-left px-6 py-3">Satuan Ukur</th>
                        <th class="text-left px-6 py-3">Modal</th>
                        <th class="text-left px-6 py-3">
                            @if ($hitung->action == 'Hitung Persediaan')
                                Selisih Modal
                            @else
                                Kerugian
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hitungDetails as $detail)
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
                                    {{ $detail->quantity_actual ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    @if ($hitung->action == 'Hitung Persediaan')
                                        {{ $detail->quantity_actual - $detail->quantity_expect }}
                                    @else
                                        {{ $detail->quantity_expect - $detail->quantity_actual }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail->materialBatch->unit->alias ?? '' }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    Rp{{ number_format($detail->total, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    Rp{{ number_format($detail->loss_total, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3" colspan="5">
                            <span class="text-gray-700">Total</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                Rp{{ number_format($hitung->grand_total, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                Rp{{ number_format($hitung->loss_grand_total, 0, ',', '.') }}
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
                Selesaikan
                @if ($hitung->action == 'Hitung Persediaan')
                    Hitung
                @else
                    Catat
                @endif
            </flux:button>
            @if ($status != 'Selesai')
                <flux:button icon="pencil-square" type="button" variant="primary"
                    href="{{ route('hitung.mulai', $hitung->id) }}">
                    {{ $hitung->action }}
                </flux:button>
            @endif
        </div>
    @elseif (!$is_start && !$is_finish)
        <div class="flex justify-end mt-16 gap-4">
            <flux:button wire:click="confirmDelete" icon="trash" type="button" variant="danger" />
            <flux:button icon="pencil" type="button" href="{{ route('hitung.edit', $hitung_id) }}">
                Ubah Daftar Persediaan
            </flux:button>
            <flux:button icon="play" variant="solid" type="button" variant="primary" wire:click="start">
                Mulai {{ $hitung->action }}
            </flux:button>
        </div>
    @endif



    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <h1 size="lg">Riwayat Pembaruan {{ $hitung->hitung_number }}</h1>
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
