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
        <flux:icon icon="message-square-warning" class="size-16" />
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
        @if ($transaction->method == 'siap-beli')
        <div class="w-full flex items-center justify-between flex-row">
            <div class="flex flex-col gap-1">
                <flux:heading class="text-lg font-semibold">Tanggal Pembelian</flux:heading>
                <p class="text-sm">
                    {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d-m-Y H:i') :
                    '-' }}
                </p>
            </div>
            <div class="flex flex-col gap-1 items-end">
                <flux:heading class="text-lg font-semibold">Kasir</flux:heading>
                <p class="text-sm">
                    {{ $transaction->user->name ?? '-' }}
                </p>
            </div>
        </div>
        @else
        <div class="w-full">
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
                            {{ $transaction->end_date ? \Carbon\Carbon::parse($transaction->end_date)->format('d-m-Y') :
                            '-' }}
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
                        <p class="text-sm">
                            @if (!empty($transaction->production))
                            {{ $transaction->production->status }}
                            @else
                            Belum Diproses
                            @endif
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
                    {{ number_format($percentage, 0) }}% ({{ $total_quantity_get }} dari
                    {{ $total_quantity_plan }})
                </span>
            </div>

            <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4">
                <flux:heading class="text-lg font-semibold">Catatan Pesanan</flux:heading>
                <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $transaction->note }}</flux:textarea>
            </div>
        </div>
        @endif
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
                        @if ($transaction->method != 'siap-beli')
                        <th class="text-left px-6 py-3">Jumlah Didapatkan</th>
                        <th class="text-left px-6 py-3">Selisih Didapatkan</th>
                        @endif
                        <th class="text-left px-6 py-3">Harga Satuan</th>
                        <th class="text-left px-6 py-3">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $id => $detail)
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
                        @if ($transaction->method != 'siap-beli')
                        @php
                        $productionDetails = $transaction->production?->details ?? collect();
                        $prodDetail = $productionDetails->firstWhere('product_id', $detail['product_id']);
                        $qty_get =
                        $prodDetail?->quantity_get > $detail['quantity']
                        ? $detail['quantity']
                        : $prodDetail?->quantity_get;
                        @endphp
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ $qty_get ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-sm">
                                {{ ($qty_get ?? 0) - $detail['quantity'] }}
                            </span>
                        </td>
                        @endif
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
                                {{ $total_quantity_plan }}
                            </span>
                        </td>
                        @if ($transaction->method != 'siap-beli')
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{ $total_quantity_get }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                {{ $total_quantity_get - $total_quantity_plan }}
                            </span>
                        </td>
                        @endif
                        <td class="px-6 py-3">
                            <span class="text-gray-700">

                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="text-gray-700">
                                Rp{{ number_format(
                                collect($details)->sum(function ($detail) {
                                return $detail['quantity'] * $detail['price'];
                                }),
                                0,
                                ',',
                                '.',
                                ) }}
                            </span>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>

    @if (!empty($transaction->production))
    <div class="flex items-start text-start space-x-2 gap-3 flex-col mt-4">
        <flux:heading class="text-lg font-semibold">Catatan Produksi</flux:heading>
        <flux:textarea rows="4" class="bg-gray-300" disabled>{{ $transaction->production->note }}
        </flux:textarea>
    </div>
    @endif

    <div class="w-full flex items-start text-start space-x-2 gap-3 flex-col mt-8 mb-2 p-1">
        <flux:heading>Pembayaran</flux:heading>
    </div>

    <div class="w-full mb-8 flex flex-col rounded-lg bg-white border border-gray-200 p-1 shadow-sm">
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
                    @if ($transaction->payment_status == 'Lunas')
                    Rp{{ number_format($totalAmount, 0, ',', '.') }}
                    @else
                    Rp{{ !empty($totalPayment) ? number_format($totalPayment, 0, ',', '.') : '0' }}
                    @endif
                </p>
            </div>
            @if ($payments && $payments->count())
            @foreach ($payments as $index => $payment)
            @php
            $paidAt = $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') : '-';
            $method = $payment->payment_method ? ucfirst($payment->payment_method) : '-';
            $bank = $payment->channel->bank_name ?? null;
            $label = $method . ($bank ? ' - ' . ucfirst($bank) : '');
            @endphp

            <div class="grid grid-cols-2 gap-4 w-full py-2 border-b">
                {{-- Kiri: Info Metode + Tanggal + Bukti --}}
                <div class="flex flex-col text-sm text-gray-600 gap-1">
                    <div class="flex items-center gap-2">
                        <span class="px-4 py-2">{{ $paidAt }}</span>
                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">{{ $label }}</span>

                        @if ($payment->payment_method && $payment->payment_method !== 'tunai')
                        <button class="text-gray-500 text-sm underline ml-2"
                            wire:click="showImageModal('{{ $payment->id }}')">
                            Lihat Bukti
                        </button>
                        @endif
                    </div>
                    @if ($payments->count() == 1)
                    @if ($payment->payment_method == 'tunai' && $kembalian > 0)
                    <div class="flex flex-col text-sm text-gray-600 gap-1 mt-1">
                        <div class="flex items-center gap-2">
                            <pre class="px-4 py-2">        </pre>
                            <span class="px-4 py-2">Bayar</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <pre class="px-4 py-2">        </pre>
                            <span class="px-4 py-2">Kembalian</span>
                        </div>
                    </div>
                    @endif
                    @elseif ($payments->count() > 1)
                    @if ($payment->id == $pembayaranKedua->id && $payment->payment_method == 'tunai' && $kembalian > 0)
                    <div class="flex flex-col text-sm text-gray-600 gap-1 mt-1">

                        <div class="flex items-center gap-2">
                            <pre class="px-4 py-2">        </pre>
                            <span class="px-4 py-2">Bayar</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <pre class="px-4 py-2">        </pre>
                            <span class="px-4 py-2">Kembalian</span>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>

                {{-- Kanan: Total bayar (hanya untuk pembayaran pertama yang sekarang index ke-1) --}}
                @if ($payments->count() == 1)
                @if ($payment->payment_method == 'tunai' && $kembalian > 0)
                <div class="flex items-start justify-end text-sm text-gray-600">
                    <div class="flex flex-col px-4 gap-1">
                        <span class="py-2">Rp{{ number_format($totalAmount, 0, ',', '.') }}</span>
                        <span class="py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                        <span class="py-2">Rp{{ number_format($kembalian, 0, ',', '.') }}</span>
                    </div>
                </div>
                @else
                <div class="flex items-start justify-end text-sm text-gray-600">
                    <span class="px-4 py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @elseif ($payments->count() > 1)
                @if ($payment->id == $pembayaranKedua->id && $payment->payment_method == 'tunai' && $kembalian > 0)
                {{-- Tampilan khusus untuk pembayaran kedua tunai dengan kembalian --}}
                <div class="flex items-start justify-end text-sm text-gray-600">
                    <div class="flex flex-col px-4 gap-1">
                        <span class="py-2">Rp{{ number_format($sisaPembayaranPertama, 0, ',', '.') }}</span>
                        <span class="py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                        <span class="py-2">Rp{{ number_format($kembalian, 0, ',', '.') }}</span>
                    </div>
                </div>
                @else
                {{-- Tampilan normal untuk semua pembayaran lainnya --}}
                <div class="flex items-start justify-end text-sm text-gray-600">
                    <span class="px-4 py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @endif
            </div>
            @endforeach
            @endif

            <div class="flex flex-row justify-between w-full">
                <p class="px-4 py-2 text-sm text-red-500 font-bold">Sisa Tagihan</p>
                <p class="px-4 py-2 text-sm text-red-500 font-bold">
                    Rp{{ number_format($remainingAmount, 0, ',', '.') }}

                </p>
            </div>
        </div>
    </div>

    @if ($transactionStatus)
    <div class="w-full flex flex-col gap-4">
        <flux:label>Metode Pembayaran</flux:label>
        <p class="text-sm text-gray-500">
            Pilih Metode Pembayaran (Tunai, Transfer, atau QRIS). Jika Bukan Tunai maka akan diminta bukti
            pembayaran
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
                <flux:select wire:model.live="paymentChannelId" placeholder="Pilih Bank Tujuan">
                    @foreach ($paymentChannels as $channel)
                    <flux:select.option value="{{ $channel->id }}" class="text-gray-700">
                        {{ $channel->bank_name }}
                    </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="paymentChannelId" />
            </div>
            <div class="w-3/4">
                <flux:input wire:model="paymentAccount" placeholder="Masukkan Nomor Rekening" readonly />
                <flux:error name="paymentAccount" />
            </div>
        </div>
        @endif

        <flux:label>Nominal Pembayaran</flux:label>
        <p class="text-sm text-gray-500">
            Masukkan atau pilih nominal pembayaran tagihan. Untuk uang muka dilakukan dengan minimal 50% atau
            setengah
            dari
            Total Tagihan.
        </p>
        <div class="flex flex-row gap-2 w-full">
            <div class="flex flex-col gap-2 w-full">
                @if ($paymentMethod == 'tunai')
                <span class="text-xs text-gray-500">
                    Nominal Uang Yang Diterima
                </span>
                @endif
                <flux:input placeholder="Masukkan Nominal Pembayaran..." wire:model.number.live="paidAmount" />
                <flux:error name="paidAmount" />
            </div>
            @if ($paymentMethod == 'tunai')
            <div class="flex flex-col gap-2 w-full">
                <span class="text-xs text-gray-500">
                    Nominal Uang Kembalian
                </span>
                <flux:input placeholder="Kembalian" value="Rp{{ number_format($changeAmount, 0, ',', '.') }}"
                    readonly />
            </div>
            @endif
        </div>

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
    @endif



    <div class="flex justify-end mt-16 gap-4">
        @if ($transaction->status == 'Draft')
        <flux:button icon="trash" type="button" variant="ghost" loading="false" wire:click.prevent="delete" />
        @endif
        @if (
        ($transaction->status == 'Belum Diproses' || $transaction->status == 'Draft') &&
        $transaction->payment_status != 'Lunas')
        <flux:button icon="pencil-square" type="button" href="{{ route('transaksi.edit', $transaction->id) }}">
            Ubah Daftar Pesanan
        </flux:button>
        @elseif ($transaction->status == 'Belum Diproses' && $transaction->payment_status == 'Lunas')
        <flux:button icon="check-circle" type="button" wire:click.prevent='finish'>
            Selesaikan Pesanan
        </flux:button>
        @elseif ($transaction->status == 'Sedang Diproses' || $transaction->status == 'Dapat Diambil')
        <flux:button icon="check-circle" type="button" wire:click.prevent='finish'>
            Selesaikan Pesanan
        </flux:button>
        @endif
        @if ($transaction->status == 'Gagal' || $transaction->status == 'Selesai')
        <flux:button icon="printer" type="button" variant="primary" wire:click.prevent="$set('showStruk', true)">
            Cetak Kembali Struk Pembayaran
        </flux:button>
        @elseif($transaction->payment_status != 'Lunas')
        <flux:button icon="shopping-cart" type="button" variant="primary" wire:click.prevent="pay">
            Bayar dan
            @if ($transaction->status == 'Draft')
            Buat
            @else
            Ambil
            @endif
            Pesanan
        </flux:button>
        @endif
    </div>

    @if ($showStruk)
    <div class="fixed inset-0 top-0 bottom-0 overflow-y-scroll bg-gray-100/95 z-50" id="struk-loading" wire:ignore.self>
        <div class="w-full px-4">
            <div class="relative min-h-screen pb-32">
                <div class="fixed top-2 right-4 z-50">
                    <flux:button type="button" icon="x-mark" wire:click.prevent="kembali" variant="ghost" />
                </div>
                <div class="mx-auto mt-20 max-w-sm text-center fade-slide-up" id="success-content">
                    <div class="state-container">
                        <span id="state" class="state-span active">
                            <svg id="state-svg" width="120" height="120" viewBox="0 0 120 120">
                                <circle id="extra-outer-circle" cx="60" cy="60" r="0" fill="#B9EBC6" opacity="0" />
                                <circle id="outer-circle" cx="60" cy="60" r="0" fill="#72CF81" opacity="0" />
                                <circle id="mid-circle" cx="60" cy="60" r="0" fill="#48A457" opacity="0" />
                                <circle class="pulse-circle" cx="60" cy="60" r="30" fill="#398345" />
                                <path class="checkmark-path" d="M43 64L55 76L87 38" stroke="white" stroke-width="4"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>


                    <p class="text-lg font-bold">
                        Pembayaran Berhasil
                    </p>
                    <p class="text-lg">
                        {{ \Carbon\Carbon::now()->format('d M Y, H:i:s') }} WIB
                    </p>
                </div>
                <div class="max-w-sm mt-8 mb-4 mx-auto border-gray-300 bg-white text-sm rounded-lg shadow-md p-4 font-sans fade-slide-up"
                    id="receipt">
                    <div class="text-center mb-4">
                        <h1 class="text-2xl font-bold pasifico-regular">Pawon3D</h1>
                        <p>Jl. Jenderal Sudirman Km.3 RT.25 RW.07</p>
                        <p>Kel. Muara Bulian, Kec. Muara Bulian, Kab. Batang Hari, Jambi, 36613</p>
                        <p>081122334455</p>
                        <p class="text-sm">www.pawon3d.my.id</p>
                    </div>

                    <div class="border-t border-dashed pt-2 mb-2"></div>

                    <div class="space-y-1 mb-4">
                        <p><span class="font-medium">Tanggal Pembayaran:</span>
                            {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d-m-Y
                            H:i') : '-' }}
                        </p>
                        <p><span class="font-medium">No. Pesanan:</span> {{ $transaction->invoice_number }}</p>
                        <p><span class="font-medium">Status Pembayaran:</span> <span class="font-semibold">
                                {{ $transaction->payment_status ?? 'Belum Lunas' }}
                            </span>
                        </p>
                        <p><span class="font-medium">Kasir:</span> {{ $transaction->user->name ?? '-' }}</p>
                    </div>

                    <div class="border-t border-dashed pt-2 mb-2"></div>

                    {{-- Daftar Produk --}}
                    <div class="space-y-3 mb-4">
                        @foreach ($transaction->details as $detail)
                        <div>
                            <p class="flex justify-between">
                                <span>{{ $detail->product->name }}</span><span>Rp{{
                                    number_format($detail->product->price * $detail->quantity, 0, ',', '.') }}</span>
                            </p>
                            <p class="text-gray-500">{{ $detail->quantity }} x
                                Rp{{ number_format($detail->product->price, 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-dashed pt-2 mb-2"></div>

                    {{-- Total --}}
                    <div class="space-y-1 mb-4">
                        <div class="w-full flex flex-col border-0">
                            <div class="flex flex-row justify-between w-full">
                                <p class="px-4 py-2 text-sm text-gray-500">Subtotal {{ count($details) }} Produk
                                </p>
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
                    </div>

                    <div class="border-t border-dashed pt-2 mb-2"></div>

                    {{-- Pembayaran --}}
                    <div class="space-y-1 mb-4">
                        <div class="flex flex-row justify-between w-full">
                            <p class="px-4 py-2 text-sm text-gray-500">Pembayaran</p>
                            <p class="px-4 py-2 text-sm text-gray-500">
                                @if ($transaction->payment_status == 'Lunas')
                                Rp{{ number_format($totalAmount, 0, ',', '.') }}
                                @else
                                Rp{{ !empty($totalPayment) ? number_format($totalPayment, 0, ',', '.') : '0' }}
                                @endif
                            </p>
                        </div>
                        @if ($payments && $payments->count())
                        @foreach ($payments as $index => $payment)
                        @php
                        $paidAt = $payment->paid_at
                        ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y')
                        : '-';
                        $method = $payment->payment_method ? ucfirst($payment->payment_method) : '-';
                        $bank = $payment->channel->bank_name ?? null;
                        $label = $method . ($bank ? ' - ' . ucfirst($bank) : '');
                        @endphp

                        <div class="grid grid-cols-2 gap-4 w-full py-2 border-b">
                            {{-- Kiri: Info Metode + Tanggal + Bukti --}}
                            <div class="flex flex-col text-sm text-gray-600 gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-4 py-2">{{ $paidAt }}</span>
                                    <span class="px-2 py-1 text-xs">{{ $label }}</span>
                                </div>
                                @if ($payments->count() == 1)
                                @if ($payment->payment_method == 'tunai' && $kembalian > 0)
                                <div class="flex flex-col text-sm text-gray-600 gap-1 mt-1">
                                    <div class="flex items-center gap-2">
                                        <pre class="px-4 py-2">        </pre>
                                        <span class="px-4 py-2">Bayar</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <pre class="px-4 py-2">        </pre>
                                        <span class="px-4 py-2">Kembalian</span>
                                    </div>
                                </div>
                                @endif
                                @elseif ($payments->count() > 1)
                                @if ($payment->id == $pembayaranKedua->id && $payment->payment_method == 'tunai' &&
                                $kembalian > 0)
                                <div class="flex flex-col text-sm text-gray-600 gap-1 mt-1">

                                    <div class="flex items-center gap-2">
                                        <pre class="px-4 py-2">        </pre>
                                        <span class="px-4 py-2">Bayar</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <pre class="px-4 py-2">        </pre>
                                        <span class="px-4 py-2">Kembalian</span>
                                    </div>
                                </div>
                                @endif
                                @endif
                            </div>

                            {{-- Kanan: Total bayar (hanya untuk pembayaran pertama yang sekarang index ke-1) --}}
                            @if ($payments->count() == 1)
                            @if ($payment->payment_method == 'tunai' && $kembalian > 0)
                            <div class="flex items-start justify-end text-sm text-gray-600">
                                <div class="flex flex-col px-4 gap-1">
                                    <span class="py-2">Rp{{ number_format($totalAmount, 0, ',', '.') }}</span>
                                    <span class="py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                                    <span class="py-2">Rp{{ number_format($kembalian, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @else
                            <div class="flex items-start justify-end text-sm text-gray-600">
                                <span class="px-4 py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @elseif ($payments->count() > 1)
                            @if ($payment->id == $pembayaranKedua->id && $payment->payment_method == 'tunai' &&
                            $kembalian > 0)
                            {{-- Tampilan khusus untuk pembayaran kedua tunai dengan kembalian --}}
                            <div class="flex items-start justify-end text-sm text-gray-600">
                                <div class="flex flex-col px-4 gap-1">
                                    <span class="py-2">Rp{{ number_format($sisaPembayaranPertama, 0, ',', '.') }}</span>
                                    <span class="py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                                    <span class="py-2">Rp{{ number_format($kembalian, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @else
                            {{-- Tampilan normal untuk semua pembayaran lainnya --}}
                            <div class="flex items-start justify-end text-sm text-gray-600">
                                <span class="px-4 py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @endif
                        </div>
                        @endforeach
                        @endif

                        <div class="flex flex-row justify-between w-full">
                            <p class="px-4 py-2 text-sm text-red-500 font-bold">Sisa Tagihan</p>
                            <p class="px-4 py-2 text-sm text-red-500 font-bold">
                                Rp{{ number_format($remainingAmount, 0, ',', '.') }}

                            </p>
                        </div>
                    </div>

                    <div class="mt-6 text-center text-sm">
                        <p>Terima kasih telah berbelanja di tempat kami.</p>
                        <p>Semoga hari Anda dipenuhi kebahagiaan dan berkah.</p>
                        <p>Sampai jumpa lagi!</p>
                    </div>
                </div>
                <div class="fixed bottom-0 left-4 right-4 z-51">
                    <div class="max-w-md mt-8 mb-4 mx-auto border-gray-300 bg-white text-sm rounded-lg shadow-md p-4 font-sans fade-slide-up show text-center flex flex-col gap-4"
                        id="buttons">
                        <input type="text" wire:model="phoneNumber"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="contoh: 08123456789" />
                        <div class="grid grid-cols-2 gap-4">
                            <flux:button type="button" wire:click.prevent="kembali" class="w-full">
                                Kembali ke Kasir
                            </flux:button>
                            <flux:button type="button" variant="primary" wire:click="send" class="w-full">
                                Kirim Struk Belanja
                            </flux:button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .state-container {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
        }

        .pacifico-regular {
            font-family: "Pacifico", cursive;
            font-weight: 500;
            font-style: normal;
        }

        .state-span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition:
                opacity 0.6s cubic-bezier(0.22, 0.61, 0.36, 1),
                transform 0.6s cubic-bezier(0.22, 0.61, 0.36, 1);
            pointer-events: none;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .state-span.active {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
            z-index: 10;
        }

        .state-span.prepare {
            transform: translate(-50%, -50%) scale(0.8);
        }

        /* Animasi untuk centang */
        .checkmark-path {
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
            animation: drawCheck 0.5s forwards 0.3s;
        }

        @keyframes drawCheck {
            to {
                stroke-dashoffset: 0;
            }
        }

        .pulse-circle {
            transform-origin: center;
            animation: pulse 1.5s cubic-bezier(0.22, 0.61, 0.36, 1) infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        #extra-outer-circle,
        #outer-circle,
        #mid-circle {
            transition: r 0.8s ease, opacity 0.8s ease;
        }

        /* Transisi masuk dari bawah */
        .fade-slide-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }

        .fade-slide-up.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @script
    <script>
        // Inisialisasi bersamaan dengan Livewire
                window.addEventListener('livewire:init', () => {
                    // Tangkap perubahan state dari Livewire
                    Livewire.on('showStrukChanged', (show) => {
                        if (show) initStrukAnimations();
                    });
                });

                // Inisialisasi awal jika showStruk true
                if (@js($showStruk)) {
                    setTimeout(initStrukAnimations, 100);
                }

                function initStrukAnimations() {
                    // 1. Hentikan animasi sebelumnya jika ada
                    if (window.strukAnimationInterval) {
                        clearInterval(window.strukAnimationInterval);
                    }

                    // 2. Animasi lingkaran (pastikan elemen ada)
                    const getSafeElement = (id) => document.getElementById(id) || {
                        setAttribute: () => {}
                    };

                    const extraOuter = getSafeElement("extra-outer-circle");
                    const outer = getSafeElement("outer-circle");
                    const mid = getSafeElement("mid-circle");

                    const states = [{
                            r1: 0,
                            r2: 0,
                            r3: 0,
                            o: 0
                        },
                        {
                            r1: 40,
                            r2: 0,
                            r3: 0,
                            o: 0.4
                        },
                        {
                            r1: 50,
                            r2: 40,
                            r3: 0,
                            o: 0.5
                        },
                        {
                            r1: 60,
                            r2: 50,
                            r3: 40,
                            o: 0.6
                        }
                    ];

                    let current = 0;

                    function updateState() {
                        const state = states[current];
                        extraOuter.setAttribute("r", state.r1);
                        extraOuter.setAttribute("opacity", state.o);
                        outer.setAttribute("r", state.r2);
                        outer.setAttribute("opacity", state.o);
                        mid.setAttribute("r", state.r3);
                        mid.setAttribute("opacity", state.o);
                        current = (current + 1) % states.length;
                    }

                    // Reset state
                    updateState(); // Set state awal

                    // Jalankan animasi
                    setTimeout(updateState, 1000);
                    window.strukAnimationInterval = setInterval(updateState, 2000);

                    // 3. Animasi fade-in konten
                    const animateWithDelay = (element, delay) => {
                        if (!element) return;
                        element.classList.remove('show');
                        setTimeout(() => element.classList.add('show'), delay);
                    };

                    animateWithDelay(document.getElementById('success-content'), 400);
                    animateWithDelay(document.getElementById('receipt'), 2000);
                    animateWithDelay(document.getElementById('buttons'), 3000);
                }
    </script>
    @endscript
    @endif


    {{-- <flux:modal class="w-full max-w-xs" wire:model="showPrintModal">
        @if ($transaction)
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
                    @if ($transaction->method == 'pesanan-reguler')
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
                        @foreach ($transaction->details as $detail)
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
        </div>
        <div class="flex justify-end mt-4">
            <flux:button type="button" wire:click="send" class="w-full">
                Kirim Struk via WhatsApp
            </flux:button>
        </div>
        @endif
    </flux:modal> --}}

    <flux:modal class="w-full max-w-xs" wire:model="showImage">
        @if (!empty($paymentImage) && $showImage)
        <div class="text-center mt-4">
            <flux:heading class="text-sm text-gray-500">Bukti Pembayaran</flux:heading>
        </div>
        <div class="flex justify-center">
            <img src="{{ asset('storage/' . $paymentImage) }}" alt="Bukti Pembayaran"
                class="w-full h-auto max-h-96 object-cover rounded-lg">
        </div>
        <div class="flex justify-end gap-2 mt-6">
            <flux:button type="button" wire:click="$set('showImage', false)">
                Tutup
            </flux:button>
        </div>
        @endif
    </flux:modal>
    @script
    <script>
        window.addEventListener('open-wa', event => {
                window.open(event.detail[0].url, '_blank');
            });
    </script>
    @endscript

    @section('css')
    <style>
        .text-color-white {
            color: #ffffff !important;
        }

        .text-position-center {
            text-align: center !important;
        }

        .text-size-xs {
            font-size: 0.75rem !important;
        }

        .text-size-sm {
            font-size: 0.875rem !important;
        }
    </style>
    @endsection

</div>