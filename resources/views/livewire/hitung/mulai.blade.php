<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <a href="{{ route('hitung.rincian', $hitung_id) }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
                Kembali
            </a>
            <h1 class="text-2xl">{{ $hitung->action }}</h1>
        </div>
        <div class="flex gap-2 items-center">
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


    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-between gap-4 flex-row">
            <h2 class="text-lg font-semibold">Daftar Persediaan</h2>
            <flux:button type="button" variant="primary" wire:click="markAllReceived">
                Tandai
                @if ($hitung->action === 'Hitung Persediaan')
                    Hitung
                @elseif ($hitung->action === 'Catat Persediaan Rusak')
                    Rusak
                @elseif ($hitung->action === 'Catat Persediaan Hilang')
                    Hilang
                @endif
                Semua
            </flux:button>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3 text-nowrap">Barang Persediaan</th>
                        <th class="text-right px-6 py-3">Jumlah Diharapkan</th>
                        <th class="text-right px-6 py-3">
                            Jumlah
                            @if ($hitung->action === 'Hitung Persediaan')
                                Terhitung
                            @elseif ($hitung->action === 'Catat Persediaan Rusak')
                                Rusak
                            @elseif ($hitung->action === 'Catat Persediaan Hilang')
                                Hilang
                            @endif
                        </th>
                        <th class="text-right px-6 py-3">
                            @if ($hitung->action === 'Hitung Persediaan')
                                Selisih Jumlah
                            @else
                                Jumlah Sebenarnya
                            @endif
                        </th>
                        <th class="text-right px-6 py-3">Satuan Ukur</th>
                        <th class="text-right px-6 py-3">
                            Barang
                            @if ($hitung->action === 'Hitung Persediaan')
                                Terhitung
                            @elseif ($hitung->action === 'Catat Persediaan Rusak')
                                Rusak
                            @elseif ($hitung->action === 'Catat Persediaan Hilang')
                                Hilang
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hitungDetails as $index => $detail)
                        <tr>
                            <td class="px-6 py-3 text-nowrap">
                                <span class="text-sm">
                                    {{ $detail['material_name'] ?? 'Barang Tidak Ditemukan' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-sm">
                                    {{ $detail['quantity_expect'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-sm">
                                    {{ $detail['quantity_actual'] ? $detail['quantity_actual'] : 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-sm">
                                    @if ($hitung->action === 'Hitung Persediaan')
                                        {{ $detail['quantity_actual'] - $detail['quantity_expect'] }}
                                    @else
                                        {{ $detail['quantity_expect'] - $detail['quantity_actual'] }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-sm">
                                    {{ $detail['unit'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <input type="number" placeholder="0"
                                    wire:model.number.live="hitungDetails.{{ $index }}.quantity"
                                    class="w-full border-gray-300
                                {{ isset($errorInputs[$index]) ? 'border-red-500' : 'border-gray-300' }}
                                focus:outline-none focus:ring-0 rounded text-right" />
                                @if (isset($errorInputs[$index]))
                                    <span class="text-xs text-red-500">Nilai melebihi jumlah yang ada</span>
                                @endif

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
    </div>



    <div class="flex justify-end mt-16 gap-4">
        <a href="{{ route('hitung.rincian', $hitung_id) }}"
            class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-50 flex items-center">
            <flux:icon.x-mark class="w-4 h-4 mr-2" />
            Batal
        </a>
        <flux:button icon="save" type="button" variant="primary" wire:click="save">
            Simpan
        </flux:button>
    </div>




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
