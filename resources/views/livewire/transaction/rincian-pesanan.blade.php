<div>
    <div class="mb-4 flex justify-between items-center">
        <div class="flex gap-2 items-center">
            <a href="{{ route('transaksi') }}"
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
                Lorem ipsum dolor sit amet consectetur. Viverra erat aenean mauris adipiscing nibh. Nullam adipiscing
                dignissim consequat volutpat augue. Auctor euismod arcu at euismod. Odio cras proin eget facilisis vitae
                at. Non at vitae lorem nec quis urna.
            </p>
        </div>
    </div>

    <div class="w-full flex flex-col gap-4 mt-4">
        <h1 class="text-3xl font-bold">{{ $transaction->invoice_number }}</h1>
        <p class="text-lg text-gray-500">{{ $transaction->status }}</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
            <!-- Kolom Kiri -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Tanggal Pesanan Dibuat</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d-m-Y
                        H:i') : '-' }}
                    </p>
                </div>
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Tanggal Pengambilan Pesanan</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') : '-' }}
                        {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '-' }}
                    </p>
                </div>
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Tanggal Selesai</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->end_date ? \Carbon\Carbon::parse($transaction->end_date)->format('d-m-Y') : '-'
                        }}
                    </p>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="grid grid-cols-1 sm:grid-cols-1 gap-6 text-right">
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Status Pembayaran</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->payment_status ? $transaction->payment_status : '-' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Kolom Kiri -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Pemesan</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->name ?? '-' }}
                    </p>
                </div>
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Nomor Telepon</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->phone ?? '-' }}
                    </p>
                </div>
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Kasir</flux:heading>
                    <p class="text-sm">
                        {{ $transaction->user->name ?? '-' }}
                    </p>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="grid grid-cols-1 sm:grid-cols-1 gap-6 text-right">
                <div class="flex flex-col gap-1">
                    <flux:heading class="text-lg font-semibold">Status Produksi</flux:heading>
                    <p class="text-sm">-</p>
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

        <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4">
            <flux:heading class="text-lg font-semibold">Catatan Pesanan</flux:heading>
            <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $transaction->note }}</flux:textarea>
        </div>
    </div>

    <div class="w-full mt-8 flex items-center flex-col gap-4">
        <div class="w-full flex items-center justify-start gap-4 flex-row">
            <flux:label>Daftar Produk</flux:label>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="text-left px-6 py-3">Produk</th>
                        <th class="text-left px-6 py-3">Jumlah Pesanan</th>
                        <th class="text-left px-6 py-3">Jumlah Didapatkan</th>
                        <th class="text-left px-6 py-3">Selisih Didapatkan</th>
                        <th class="text-left px-6 py-3">Harga Satuan</th>
                        <th class="text-left px-6 py-3">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $id => $detail)
                    <tr>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ $detail['name'] ?? 'Produk Tidak Ditemukan' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ $detail['quantity'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                0
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ 0 - $detail['quantity'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                Rp{{ number_format($detail['price'], 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                Rp{{ number_format($detail['quantity'] * $detail['price'], 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
                <tfoot
                    class="text-xs text-gray-700 capitalize font-bold bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">Total</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{ collect($details)->sum('quantity') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                0
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{ 0 - collect($details)->sum('quantity') }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">

                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                Rp{{ number_format(collect($details)->sum(function ($detail) {
                                return $detail['quantity'] * $detail['price'];
                                }), 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>


    <div class="w-full mt-8 mb-8 flex flex-col rounded-lg bg-white border border-gray-200 p-1 shadow-sm">
        <div class="w-full flex flex-col border-b border-gray-200">
            <div class="flex flex-row justify-between w-full">
                <p class="px-4 py-2 text-sm text-gray-500">Subtotal {{ count($details) }} Produk</p>
                <p class="px-4 py-2 text-sm text-gray-500">
                    Rp{{ number_format($totalAmount, 0, ',', '.') }}
                </p>
            </div>
            <div class="flex flex-row justify-between w-full">
                <p class="px-4 py-2 text-sm text-gray-500 font-bold">Total Tagihan</p>
                <p class="px-4 py-2 text-sm text-gray-500 font-bold">
                    Rp{{ number_format($totalAmount, 0, ',', '.') }}
                </p>
            </div>
        </div>
        <div class="w-full flex flex-col">
            <div class="flex flex-row justify-between w-full">
                <p class="px-4 py-2 text-sm text-gray-500">Pembayaran</p>
                <p class="px-4 py-2 text-sm text-gray-500">
                    Rp{{ number_format($paidAmount, 0, ',', '.') }}
                </p>
            </div>
            <div class="flex flex-row justify-between w-full">
                <p class="px-4 py-2 text-sm text-red-500 font-bold">Sisa Tagihan</p>
                <p class="px-4 py-2 text-sm text-red-500 font-bold">
                    Rp{{ number_format($totalAmount - $paidAmount, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    <div class="w-full flex flex-col gap-4">

        <flux:label>Metode Pembayaran</flux:label>
        <p class="text-sm text-gray-500">
            Pilih Metode Pembayaran (Tunai, Transfer, atau QRIS). Jika Bukan Tunai maka akan diminta bukti pembayaran
            berupa
            gambar (.jpg dan .png)
        </p>
        <flux:select wire:model.live="paymentMethod" class="mt-2" placeholder="Pilih Metode Pembayaran">
            <flux:select.option value="tunai" class="text-gray-700">Tunai</flux:select.option>
            <flux:select.option value="transfer" class="text-gray-700">Transfer</flux:select.option>
            <flux:select.option value="qris" class="text-gray-700">QRIS</flux:select.option>
        </flux:select>
        <flux:error name="paymentMethod" />

        @if ($paymentMethod == 'transfer')
        <div class="mt-2 flex flex-row gap-2 w-full">
            <div class="w-1/4">
                <flux:select wire:model.live="paymentTarget" placeholder="Pilih Bank Tujuan">
                    <flux:select.option value="BRI" class="text-gray-700">
                        BRI
                    </flux:select.option>
                    <flux:select.option value="BCA" class="text-gray-700">
                        BCA
                    </flux:select.option>
                    <flux:select.option value="Mandiri" class="text-gray-700">
                        Mandiri
                    </flux:select.option>
                </flux:select>
                <flux:error name="paymentTarget" />
            </div>
            <div class="w-3/4">
                <flux:input wire:model="paymentAccount" placeholder="Masukkan Nomor Rekening" readonly />
                <flux:error name="paymentAccount" />
            </div>
        </div>
        @endif

        <flux:label>Nomimal Pembayaran</flux:label>
        <p class="text-sm text-gray-500">
            Masukkan atau pilih nominal pembayaran tagihan. Untuk uang muka dilakukan dengan minimal 50% atau setengah
            dari
            Total Tagihan.
        </p>
        <flux:input placeholder="Masukkan Nominal Pembayaran..." wire:model.number.live="paidAmount" />
        <flux:error name="paidAmount" />

        @if ($paymentMethod == 'transfer')
        <div class="mb-5 w-full">
            <div class="flex flex-row items-center gap-4">
                <label
                    class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2)">
                    Pilih Bukti Pembayaran
                    <input type="file" wire:model.live="image" accept="image/jpeg, image/png, image/jpg"
                        class="hidden" />
                </label>

                @if ($image)
                <input type="text"
                    class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                    value="{{ is_string($image) ? basename($image) : $image->getClientOriginalName() }}" readonly
                    wire:loading.remove wire:target="image">
                <input type="text"
                    class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                    value="Mengupload gambar..." readonly wire:loading wire:target="image">
                @else
                <input type="text"
                    class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                    value="Belum Ada Bukti Pembayaran" readonly wire:loading.remove wire:target="image">
                <input type="text"
                    class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                    value="Mengupload gambar..." readonly wire:loading wire:target="image">
                @endif

            </div>
        </div>
        <flux:error name="image" />
        @endif
    </div>



    <div class="flex justify-end mt-16 gap-4">
        <flux:button icon="trash" type="button" variant="ghost" loading="false" wire:click.prevent="delete" />
        <flux:button icon="pencil-square" type="button" href="{{ route('transaksi.edit', $transaction->id) }}">
            Ubah Daftar Pesanan
        </flux:button>
        <flux:button icon="shopping-cart" type="button" variant="primary" wire:click.prevent="pay">
            Bayar dan Buat Pesanan
        </flux:button>
    </div>


    <flux:modal name="print-struk" class="w-full max-w-xs" wire:model="showPrintModal">
        @if($transaction)
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }


                #printArea,
                #printArea * {
                    visibility: visible;
                    word-wrap: break-word;
                    overflow-wrap: break-word;
                }

                #printArea {
                    size: 72mm 100vh;
                    margin: 0;
                    padding: 0;
                    font-size: 10px;
                }
            }
        </style>
        <div id="printArea" class="p-4">

            <div class="text-center">
                <h2 class="text-lg font-bold">Struk Transaksi</h2>
                <p class="text-xs">Tanggal: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
            </div>

            <div class="mt-4">
                <p class="text-xs"><strong>Total:</strong> Rp {{ number_format($transaction->total_amount) }}</p>
                <p class="text-xs"><strong>Status Pembayaran:</strong> {{ $transaction->payment_status }}</p>
                <p class="text-xs"><strong>Tipe:</strong>
                    @if($transaction->method == 'pesanan-reguler')
                    Pesanan Reguler
                    @elseif('pesanan-kotak')
                    Pesanan Kotak
                    @else
                    Siap Saji
                    @endif
                </p>
            </div>

            <div class="mt-4 border-t pt-2">
                <table class="w-full text-xs">
                    <thead>
                        <tr>
                            <th class="text-left">Produk</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name }}</td>
                            <td class="text-right">{{ $detail->quantity }}</td>
                            <td class="text-right">Rp {{ number_format($detail->price) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs">Terima kasih telah berbelanja</p>
            </div>
        </div>
        <div class="flex justify-between gap-2 mt-6">
            <flux:button type="button" wire:click="$set('showPrintModal', false)" class="btn-secondary">
                Tutup
            </flux:button>
            <flux:button type="button" onclick="return cetakStruk('{{ route('transaksi.cetak', $transaction->id) }}')"
                class="px-4 py-2 border rounded-md btn-primary">
                Cetak
            </flux:button>
            <flux:button type="button" wire:click="send">
                Kirim Struk via WhatsApp
            </flux:button>
        </div>
        @endif
    </flux:modal>

</div>