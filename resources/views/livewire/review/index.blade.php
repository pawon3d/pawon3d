<div>
    <div class="flex items-end justify-between mb-7">
        <h1 class="text-3xl font-bold">Ulasan</h1>
    </div>

    <!-- Table -->
    <div class="rounded-xl border bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Banyak produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"> {{ $transaction->reviews->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <flux:button wire:click="showDetail('{{ $transaction->id }}')"
                                class="text-blue-500 hover:text-blue-700">
                                Lihat Detail
                            </flux:button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button wire:click="deleteReviews('{{ $transaction->id }}')"
                                class="px-3 py-1 border rounded-md text-red-600 hover:bg-red-50">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-center" colspan="4">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    <flux:modal wire:model="showDetailModal" class="w-1/2">
        <flux:heading name="title">
            Detail
        </flux:heading>

        @if($selectedTransaction)
        <div class="space-y-4">
            <!-- Informasi Utama -->
            <div class="flex gap-4">
                <div>
                    <label class="block text-sm font-medium">Kode Transaksi</label>
                    <p class="text-sm">{{ $selectedTransaction->id }}</p>
                </div>
            </div>

            <!-- Tabel Detail -->
            <div class="border-t pt-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komentar
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visibilitas
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedTransaction->reviews as $review)
                            <tr>
                                <td class="px-6 py-4 text-xs ">{{ $review->product->name }}</td>
                                <td class="px-6 py-4 text-xs">
                                    <img src="{{ $review->product->product_image ? asset('storage/'.$review->product->product_image) : '/img/no-img.jpg' }}"
                                        class="w-10 h-10 object-cover rounded">
                                </td>
                                <td class="px-6 py-4 text-xs">{{ $review->rating }}/5</td>
                                <td class="px-6 py-4 text-xs">{{ $review->comment }}</td>
                                <td class="px-6 py-4 text-xs">
                                    <flux:switch
                                        wire:change="updateVisibility('{{ $review->id }}', $event.target.checked)"
                                        :checked="$review->visible ? true : false" />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <x-slot name="footer">
            <button wire:click="$set('showDetailModal', false)" class="px-4 py-2 border rounded-md">
                Tutup
            </button>
        </x-slot>
    </flux:modal>

</div>