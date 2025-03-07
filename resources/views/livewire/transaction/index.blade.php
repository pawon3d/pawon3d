<div>
    <div class="flex items-end justify-between mb-7">
        <h1 class="text-3xl font-bold">Transaksi</h1>
    </div>

    <!-- Filter Section -->
    <div class="flex items-center space-x-4 mb-4">
        <input type="text" wire:model.live="search" placeholder="Cari nama..." class="px-4 py-2 border rounded-lg w-full max-w-sm">

        <flux:select wire:model.live="typeFilter" class="px-4 py-2 border rounded-lg">
            <flux:select.option value="all">Semua Tipe</flux:select.option>
            <flux:select.option value="siap beli">Siap Beli</flux:select.option>
            <flux:select.option value="pesanan">Pesanan</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="paymentStatusFilter" class="px-4 py-2 border rounded-lg">
            <flux:select.option value="all">Semua Status</flux:select.option>
            <flux:select.option value="belum lunas">Belum Lunas</flux:select.option>
            <flux:select.option value="lunas">Lunas</flux:select.option>
        </flux:select>
    </div>

    <!-- Table -->
    <div class="rounded-xl border bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($transaction->total_amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:select wire:change="updatePaymentStatus('{{ $transaction->id }}', $event.target.value)" class="border rounded px-2 py-1">
                                <option value="belum lunas" {{ $transaction->payment_status === 'belum lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                <option value="lunas" {{ $transaction->payment_status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                            </flux:select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:button wire:click="showDetail('{{ $transaction->id }}')" class="text-blue-500 hover:text-blue-700">
                                Lihat Detail
                            </flux:button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button wire:click="printReceipt('{{ $transaction->id }}')" class="px-3 py-1 border rounded-md text-green-600 hover:bg-green-50">
                                Cetak
                            </button>
                            <a href="{{ route('transaksi.edit', $transaction->id) }}" class="px-3 py-1 border rounded-md hover:bg-gray-100">
                                Edit
                            </a>
                            <button wire:click="deleteTransaction('{{ $transaction->id }}')" class="px-3 py-1 border rounded-md text-red-600 hover:bg-red-50">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    <flux:modal wire:model="showDetailModal" max-width="4xl">
        <flux:heading name="title">
            Detail Transaksi - {{ $selectedTransaction->type ?? '' }}
        </flux:heading>

        @if($selectedTransaction)
        <div class="space-y-4">
            <!-- Informasi Utama -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Penanggung Jawab</label>
                    <p class="text-sm">{{ $selectedTransaction->user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">Total</label>
                    <p class="text-sm">Rp {{ number_format($selectedTransaction->total_amount) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium">DP</label>
                    <p class="text-sm">Rp {{ number_format($selectedTransaction->dp) }}</p>
                </div>
                @if($selectedTransaction->type === 'pesanan')
                <div>
                    <label class="block text-sm font-medium">Jadwal</label>
                    <p class="text-sm">{{ $selectedTransaction->schedule }}</p>
                </div>
                @endif
            </div>

            <!-- Tabel Detail -->
            <div class="border-t pt-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                            @if($selectedTransaction->type === 'pesanan')
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Produksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedTransaction->details as $detail)
                        <tr>
                            <td class="px-6 py-4">{{ $detail->product->name }}</td>
                            <td class="px-6 py-4">
                                <img src="{{ $detail->product->product_image ? asset('storage/'.$detail->product->product_image) : '/no-image.jpg' }}" class="w-10 h-10 object-cover rounded">
                            </td>
                            <td class="px-6 py-4">{{ $detail->quantity }}</td>
                            <td class="px-6 py-4">Rp {{ number_format($detail->price) }}</td>
                            @if($selectedTransaction->type === 'pesanan')
                            <td class="px-6 py-4">
                                @forelse($detail->productions as $production)
                                <span class="text-xs capitalize">{{ $production->status }}</span>
                                @empty
                                <span class="text-xs">Belum Diproduksi</span>
                                @endforelse
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <x-slot name="footer">
            <button wire:click="$set('showDetailModal', false)" class="px-4 py-2 border rounded-md">
                Tutup
            </button>
        </x-slot>
    </flux:modal>

    <!-- Modal Print Struk -->
    <flux:modal name="print-struk" class="w-full max-w-xs" wire:model="showPrintModal">
        @if($printTransaction)
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
                <p class="text-xs"><strong>Total:</strong> Rp {{ number_format($printTransaction->total_amount) }}</p>
                <p class="text-xs"><strong>Status Pembayaran:</strong> {{ $printTransaction->payment_status }}</p>
                <p class="text-xs"><strong>Tipe:</strong> {{ $printTransaction->type }}</p>
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
                        @foreach($printTransaction->details as $detail)
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
        <div class="flex justify-end gap-2 mt-6">
            <flux:button type="button" onclick="return cetakStruk('{{ route('transaksi.cetak', $printTransaction->id) }}');" class="px-4 py-2 border rounded-md btn-primary">
                Cetak
            </flux:button>
            <flux:button type="button" wire:click="$set('showPrintModal', false)" class="btn-secondary">
                Tutup
            </flux:button>
        </div>
        @endif
    </flux:modal>
</div>
