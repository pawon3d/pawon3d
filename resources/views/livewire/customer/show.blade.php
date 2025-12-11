<div>
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('customer') }}" wire:navigate
                class="bg-[#313131] hover:bg-[#252324] px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold text-base transition-colors"
                wire:navigate>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
            <h1 class="text-xl font-semibold text-[#666666]">Rincian Pelanggan</h1>
        </div>
    </div>

    <!-- Info Box -->
    <x-alert.info>
        Anda dapat mengubah atau menghapus pelanggan. Sesuaikan informasi jika terdapat perubahan, pastikan informasi
        yang dimasukan benar dan tepat. Informasi akan ditampilkan untuk mengetahui data diri pelanggan dan transaksi
        yang dilakukan.
    </x-alert.info>

    <div class="flex flex-col gap-[30px] mt-5">
        <!-- Customer Info Card -->
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-[30px] py-[25px] flex flex-col gap-10">
            <!-- Form Fields -->
            <div class="flex flex-col gap-[30px]">
                <!-- No. Telepon -->
                <div class="flex flex-col gap-2.5">
                    <label class="text-base font-medium text-[#666666]">No. Telepon</label>
                    <div
                        class="w-full px-5 py-2.5 bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] text-base text-[#666666]">
                        {{ $phone }}
                    </div>
                </div>

                <!-- Nama Pelanggan -->
                <div class="flex flex-col gap-2.5">
                    <label class="text-base font-medium text-[#666666]">Nama Pelanggan</label>
                    <input type="text" wire:model="name"
                        class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] placeholder:text-[#959595] focus:outline-none focus:border-[#666666]"
                        placeholder="Nama Pelanggan" />
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Stats Section -->
            <div class="flex flex-col gap-[25px]">
                <div class="flex justify-between items-center">
                    <span class="text-base font-medium text-[#666666]">Transaksi Terbaru</span>
                    <span class="text-base font-medium text-[#666666]">
                        @if ($lastTransaction)
                            {{ \Carbon\Carbon::parse($lastTransaction)->translatedFormat('d M Y') }}
                            {{ \Carbon\Carbon::parse($lastTransaction)->format('H:i') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-base font-medium text-[#666666]">Total Transaksi</span>
                    <span class="text-base font-medium text-[#666666]">{{ $totalTransactions }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-1text-base font-medium text-[#666666]">Total Pembayaran
                        <flux:button icon="information-circle" iconVariant="outline" variant="ghost" type="button"
                            wire:click="showDetailModal()" />
                    </span>
                    <span
                        class="text-base font-medium text-[#666666]">Rp{{ number_format($totalPayment, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-base font-medium text-[#666666]">Saldo Poin</span>
                    <span class="text-base font-medium text-[#666666]">{{ $points }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <button type="button" wire:click="confirmDelete"
                    class="bg-[#eb5757] hover:bg-red-600 px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#f8f4e1] font-semibold text-base transition-colors cursor-pointer">
                    <flux:icon icon="trash" class="size-5" />
                    Hapus Pelanggan
                </button>
                <button type="button" wire:click="update"
                    class="bg-[#3f4e4f] hover:bg-[#2f3e3f] px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold text-base transition-colors cursor-pointer">
                    <flux:icon icon="save" class="size-5" />
                    Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- Points History Card -->
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-[30px] py-[25px]">
            <div class="flex flex-col gap-[15px]">
                <!-- Header -->
                <div class="flex justify-between items-center">
                    <span class="text-base font-medium text-[#666666]">Riwayat Poin</span>
                    <button type="button" wire:click="showModalTambahPoin"
                        class="bg-[#74512d] hover:bg-[#5d4024] px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#fafafa] font-semibold text-base transition-colors cursor-pointer">
                        <flux:icon icon="plus" class="size-4" />
                        Tambah Poin
                    </button>
                </div>

                <!-- Points History Table -->
                <x-table.paginated :paginator="$histories" :headers="[
                    ['label' => 'ID Aksi', 'width' => 'max-w-[180px]'],
                    ['label' => 'Aksi', 'width' => 'max-w-[180px] min-w-[100px]'],
                    ['label' => 'Poin', 'sortable' => true, 'sort-by' => 'points', 'align' => 'right'],
                ]" headerBg="#3F4E4F" headerText="#F8F4E1"
                    emptyMessage="Tidak ada riwayat poin." pageName="historyPage">
                    @foreach ($histories as $history)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $history->action_id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center gap-2.5">
                                    <span>{{ $history->action }}</span>
                                    @if ($history->image)
                                        <a href="{{ asset('storage/' . $history->image) }}" target="_blank"
                                            class="bg-[#666666] rounded-[5px] p-1 hover:bg-gray-700 transition-colors">
                                            <flux:icon icon="eye" class="size-3.5 text-white" />
                                        </a>
                                        <a href="{{ asset('storage/' . $history->image) }}" download
                                            class="bg-[#56c568] rounded-[5px] p-1 hover:bg-green-600 transition-colors">
                                            <flux:icon icon="arrow-down-tray" class="size-3.5 text-white" />
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-right">
                                {{ $history->points > 0 ? '+' . $history->points : $history->points }}
                            </td>
                        </tr>
                    @endforeach
                </x-table.paginated>
            </div>
        </div>

        {{-- Payment Detail Modal --}}
        <flux:modal class="w-full max-w-2xl" name="payments-detail" wire:model="showPaymentModal">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Detail Pembayaran</flux:heading>
                    <p class="text-sm text-gray-500 mt-2">Daftar pembayaran yang dilakukan pelanggan untuk semua
                        transaksi terkait.</p>
                </div>

                <div class="max-h-[60vh] overflow-y-auto">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Bayar:
                                <strong>Rp{{ number_format($totalPaidInModal, 0, ',', '.') }}</strong>
                            </p>
                            <p class="text-sm text-gray-600">Total Refund: <strong
                                    class="text-red-600">Rp{{ number_format($totalRefundInModal, 0, ',', '.') }}</strong>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Netto:
                                <strong>Rp{{ number_format($netPaidInModal, 0, ',', '.') }}</strong>
                            </p>
                        </div>
                    </div>

                    @if (count($payments) > 0)
                        <table class="w-full text-sm bg-white rounded">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">No. Transaksi</th>
                                    <th class="px-4 py-2 text-left">Metode</th>
                                    <th class="px-4 py-2 text-right">Jumlah</th>
                                    <th class="px-4 py-2 text-left">Bank / Channel</th>
                                    <th class="px-4 py-2 text-left">Tanggal</th>
                                    <th class="px-4 py-2">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $payment->transaction->invoice_number ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            {{ ucfirst($payment->payment_method ?? ($payment->payment_group ?? '-')) }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3">{{ $payment->channel?->bank_name ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($payment->image)
                                                <a href="{{ asset('storage/' . $payment->image) }}" target="_blank"
                                                    class="inline-block px-2 py-1 bg-[#666] text-white rounded">Lihat</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-8 text-gray-500">Belum ada data pembayaran.</div>
                    @endif

                    {{-- Refunds --}}
                    <div class="mt-6">
                        <h3 class="text-sm font-medium mb-2">Refunds</h3>
                        @if (count($refunds) > 0)
                            <table class="w-full text-sm bg-white rounded">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left">No. Transaksi</th>
                                        <th class="px-4 py-2 text-left">Metode</th>
                                        <th class="px-4 py-2 text-right">Jumlah</th>
                                        <th class="px-4 py-2 text-left">Bank / Channel</th>
                                        <th class="px-4 py-2 text-left">Tanggal</th>
                                        <th class="px-4 py-2">Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($refunds as $refund)
                                        <tr class="border-t">
                                            <td class="px-4 py-3">{{ $refund->transaction->invoice_number ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3">{{ ucfirst($refund->refund_method ?? '-') }}</td>
                                            <td class="px-4 py-3 text-right text-red-600">
                                                -Rp{{ number_format($refund->total_amount, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3">{{ $refund->channel?->bank_name ?? '-' }}</td>
                                            <td class="px-4 py-3">
                                                {{ \Carbon\Carbon::parse($refund->refunded_at)->format('d M Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if ($refund->proof_image)
                                                    <a href="{{ asset('storage/' . $refund->proof_image) }}"
                                                        target="_blank"
                                                        class="inline-block px-2 py-1 bg-[#666] text-white rounded">Lihat</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-4 text-gray-500">Belum ada data refund.</div>
                        @endif
                    </div>

                    {{-- Cancellations --}}
                    <div class="mt-6">
                        <h3 class="text-sm font-medium mb-2">Pembatalan</h3>
                        @if (count($cancellations) > 0)
                            <table class="w-full text-sm bg-white rounded">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left">No. Transaksi</th>
                                        <th class="px-4 py-2 text-left">Alasan</th>
                                        <th class="px-4 py-2 text-left">Pengembalian Poin</th>
                                        <th class="px-4 py-2 text-left">Waktu</th>
                                        <th class="px-4 py-2">Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cancellations as $c)
                                        <tr class="border-t">
                                            <td class="px-4 py-3">{{ $c->invoice_number }}</td>
                                            <td class="px-4 py-3">{{ $c->cancel_reason ?? '-' }}</td>
                                            <td class="px-4 py-3">
                                                {{ $c->points_used ? '+' . $c->points_used . ' poin' : '-' }}</td>
                                            <td class="px-4 py-3">
                                                {{ \Carbon\Carbon::parse($c->cancelled_at)->format('d M Y H:i') }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @if ($c->cancel_proof_image)
                                                    <a href="{{ asset('storage/' . $c->cancel_proof_image) }}"
                                                        target="_blank"
                                                        class="inline-block px-2 py-1 bg-[#666] text-white rounded">Lihat</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-4 text-gray-500">Belum ada pembatalan.</div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-200 pt-4">
                    <flux:modal.close>
                        <flux:button type="button" icon="x-mark">Tutup</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </flux:modal>

        <!-- Top Products Chart -->
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-[30px] py-[25px]">
            <p class="text-base font-medium text-[#333333] opacity-70 mb-4">10 Produk Pembelian Teratas</p>

            @if ($topProducts->isNotEmpty())
                <div class="bg-[#fafafa] p-4">
                    @php
                        $maxQuantity = $topProducts->max('quantity') ?: 1;
                    @endphp
                    <div class="flex flex-col gap-3">
                        @foreach ($topProducts as $product)
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-xs text-gray-600 w-32 text-right truncate">{{ $product['name'] }}</span>
                                <div class="flex-1 bg-gray-200 rounded h-5 relative">
                                    <div class="bg-[#933c24] h-full rounded opacity-80"
                                        style="width: {{ ($product['quantity'] / $maxQuantity) * 100 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 w-8">{{ $product['quantity'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-center gap-2 mt-4">
                        <div class="size-2 bg-[#933c24] border border-white"></div>
                        <span class="text-xs text-gray-600">Jumlah Produk (pcs)</span>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    Belum ada data pembelian produk.
                </div>
            @endif
        </div>

        <!-- Recent Orders Card -->
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-[30px] py-[25px]">
            <div class="flex flex-col gap-[25px]">
                <!-- Header with Order Type Filter -->
                <div class="flex justify-between items-center">
                    <span class="text-base font-medium text-[#666666]">Pesanan Terbaru</span>
                    <div class="flex gap-2.5">
                        <button type="button" wire:click="setOrderType('pesanan-reguler')"
                            class="{{ $orderType === 'pesanan-reguler' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] border border-[#3f4e4f] text-[#3f4e4f]' }} px-6 py-2.5 rounded-[15px] shadow-sm font-semibold text-base transition-colors cursor-pointer">
                            Pesanan Reguler
                        </button>
                        <button type="button" wire:click="setOrderType('pesanan-kotak')"
                            class="{{ $orderType === 'pesanan-kotak' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] border border-[#3f4e4f] text-[#3f4e4f]' }} px-6 py-2.5 rounded-[15px] shadow-sm font-semibold text-base transition-colors cursor-pointer">
                            Pesanan Kotak
                        </button>
                        <button type="button" wire:click="setOrderType('siap-beli')"
                            class="{{ $orderType === 'siap-beli' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] border border-[#3f4e4f] text-[#3f4e4f]' }} px-6 py-2.5 rounded-[15px] shadow-sm font-semibold text-base transition-colors cursor-pointer">
                            Siap Saji
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div class="flex items-center gap-4">
                    <div
                        class="flex-1 bg-white border border-[#adadad] rounded-full flex items-center px-4 py-0 focus-within:ring-2 focus-within:ring-blue-500">
                        <flux:icon icon="magnifying-glass" class="size-5 text-[#adadad]" />
                        <input type="text" wire:model.live.debounce.300ms="orderSearch"
                            placeholder="Cari Pesanan atau Pembelian"
                            class="flex-1 px-3 py-2 border-0 focus:outline-none focus:ring-0 bg-transparent text-[#666666] placeholder-[#959595] text-base font-medium" />
                    </div>
                </div>

                <!-- Orders Table -->
                <x-table.paginated :paginator="$orders" :headers="array_merge([
                    ['label' => 'ID Transaksi', 'width' => 'w-[170px]'],
                    $orderType === 'siap-beli'
                        ? ['label' => 'Tanggal Beli', 'sortable' => true, 'sort-by' => 'date', 'width' => 'w-[190px]']
                        : ['label' => 'Tanggal Ambil', 'sortable' => true, 'sort-by' => 'date', 'width' => 'w-[190px]'],
                    ['label' => 'Daftar Produk'],
                    ['label' => 'Pembeli'],
                    ['label' => 'Kasir', 'width' => 'max-w-[120px]'],
                ], $orderType !== 'siap-beli' ? [
                    ['label' => 'Status Bayar', 'sortable' => true, 'sort-by' => 'payment_status'],
                    ['label' => 'Status Pesanan', 'sortable' => true, 'sort-by' => 'status'],
                ] : [])" headerBg="#3F4E4F" headerText="#F8F4E1"
                    emptyMessage="Tidak ada pesanan." pageName="ordersPage">
                    @foreach ($orders as $order)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $order->invoice_number }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($order->date)->translatedFormat('d F Y') }}
                                {{ \Carbon\Carbon::parse($order->time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="truncate max-w-[200px]">
                                    {{ $order->details->map(fn($d) => $d->product?->name)->filter()->implode(', ') ?: '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $order->customer_name ?: $order->customer?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $order->user?->name ?? '-' }}
                            </td>
                            @if ($orderType !== 'siap-beli')
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ ucfirst($order->payment_status ?? '-') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ ucfirst($order->status ?? '-') }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </x-table.paginated>
            </div>
        </div>
    </div>

    <!-- Add Points Modal -->
    <flux:modal name="tambah-poin" class="w-full max-w-md" wire:model="addPointsModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Poin</flux:heading>
            </div>
            <div class="space-y-4">
                <div class="mb-5 w-full">
                    <flux:label class="mb-2">Bukti Story Instagram (5 Poin)</flux:label>
                    <div class="flex flex-row items-center gap-4 mt-3">
                        <label
                            class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2) w-1/4 text-xs text-center">
                            Unggah Bukti
                            <input type="file" wire:model.live="ig_image"
                                accept="image/jpeg, image/png, image/jpg" class="hidden" />
                        </label>

                        @if ($ig_image)
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="{{ is_string($ig_image) ? basename($ig_image) : $ig_image->getClientOriginalName() }}"
                                readonly wire:loading.remove wire:target="ig_image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="ig_image">
                        @else
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="File Belum Dipilih" readonly wire:loading.remove wire:target="ig_image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="ig_image">
                        @endif
                    </div>
                </div>
                <flux:error name="ig_image" />
                <div class="mb-5 w-full">
                    <flux:label class="mb-2">Bukti Rating Gmaps (10 Poin)</flux:label>
                    <div class="flex flex-row items-center gap-4 mt-3">
                        <label
                            class="relative items-center cursor-pointer font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg px-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2) w-1/4 text-xs text-center">
                            Unggah Bukti
                            <input type="file" wire:model.live="gmaps_image"
                                accept="image/jpeg, image/png, image/jpg" class="hidden" />
                        </label>

                        @if ($gmaps_image)
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="{{ is_string($gmaps_image) ? basename($gmaps_image) : $gmaps_image->getClientOriginalName() }}"
                                readonly wire:loading.remove wire:target="gmaps_image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="gmaps_image">
                        @else
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="File Belum Dipilih" readonly wire:loading.remove wire:target="gmaps_image">
                            <input type="text"
                                class="w-full px-3 py-2 text-sm text-gray-800 border border-gray-300 rounded-md bg-gray-100"
                                value="Mengupload File..." readonly wire:loading wire:target="gmaps_image">
                        @endif
                    </div>
                </div>
                <flux:error name="gmaps_image" />
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <flux:modal.close>
                    <flux:button type="button" icon="x-mark">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="button" icon="arrow-up-tray" variant="primary" wire:click="addPoints">Unggah
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
