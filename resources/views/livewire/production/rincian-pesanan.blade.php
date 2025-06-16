<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <a href="{{ route('produksi.pesanan', ['method' => $transaction->method]) }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" wire:navigate />
                Kembali
            </a>
            <h1 class="text-2xl">Rincian Pesanan</h1>
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
        <h1 class="text-3xl font-bold">{{ $transaction->invoice_number }}</h1>
        <p class="text-lg text-gray-500">{{ $transaction->status }}</p>
        <div class="flex items-center justify-between gap-4 flex-row">
            <div class="flex items-center gap-16 flex-row">
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Pesanan Masuk</flux:heading>
                    <p class="text-sm text-start">
                        {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d / m / Y H:i') : '-' }}
                    </p>
                </div>
                <div class="flex items-start gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Tanggal Pengambilan Pesanan</flux:heading>
                    <p class="text-sm text-start">
                        {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d / m / Y') : '-' }}
                        {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '-' }}
                    </p>
                </div>

            </div>
            <div class="flex items-center gap-16 flex-row">
                <div class="flex items-end gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Pemesan</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->name ?? '-' }}
                    </p>
                </div>
                <div class="flex items-end gap-4 flex-col">
                    <flux:heading class="text-lg font-semibold">Kasir</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->user->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4">
            <flux:heading class="text-lg font-semibold">Catatan Pesanan</flux:heading>
            <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $transaction->note }}
            </flux:textarea>
        </div>

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
                        <th class="text-right px-6 py-3">Jumlah Pesanan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $detail)
                        <tr>
                            <td class="px-6 py-3">
                                <span class="text-sm">
                                    {{ $detail->product->name ?? 'Produk Tidak Ditemukan' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="text-sm ">
                                    {{ $detail->quantity ?? 0 }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3">
                            <span class="text-gray-700 font-bold">Total</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <span class="text-gray-700">
                                {{ $details->sum('quantity') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>

    </div>

    <div class="flex justify-end mt-16 gap-4">
        @if (empty($transaction->production))
            <flux:button icon="chef-hat" type="button" variant="primary" wire:click="start">
                Rencanakan Produksi
            </flux:button>
        @endif
    </div>


    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <h1 size="lg">Riwayat Pembaruan {{ $transaction->invoice_number }}</h1>
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
