<div>
    <div class="min-h-[calc(100vh-12rem)] bg-gray-100 p-6">
        <!-- Tabs -->
        <div class="tabs-container mb-6">
            <div class="flex justify-center gap-2 bg-white p-1 rounded-lg">
                <button wire:click="switchTab('ready')" class="flex-1 px-4 py-2 text-center rounded-lg transition-colors duration-200 ease-in-out text-gray-700 {{ $activeTab === 'ready' ? 'bg-white text-blue-600 shadow-lg border-b-2 border-blue-600' : 'bg-transparent' }}">
                    Siap Beli
                </button>
                <button wire:click="switchTab('order')" class="flex-1 px-4 py-2 text-center rounded-lg transition-colors duration-200 ease-in-out text-gray-700 {{ $activeTab === 'order' ? 'bg-white text-blue-600 shadow-lg border-b-2 border-blue-600' : 'bg-transparent' }}">
                    Pesanan
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Produk -->
            <div class="flex-1">
                <div class="bg-white shadow rounded-lg p-4 mb-6">
                    <input type="text" wire:model.debounce.300ms="searchQuery" placeholder="Cari produk..." class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div class="mb-6">
                    <select wire:model.live="activeCategory" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    <div class="bg-white shadow rounded-lg p-4 flex flex-col">
                        <div class="flex-grow">
                            <h3 class="text-sm font-semibold mb-2">{{ $product->name }}</h3>
                            @if($product->product_image)
                            <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}" class="w-full max-h-32 object-cover mb-2">
                            @else
                            <img src="{{ asset('img/no-image.jpg') }}" alt="{{ $product->name }}" class="w-full max-h-32 object-cover mb-2">
                            @endif
                        </div>
                        <div class="mt-auto">
                            <p class="text-xs text-gray-500 mb-2">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            @if($activeTab === 'ready')
                            <p class="text-xs text-gray-400 mb-2">
                                Stok: {{ $product->stock }}
                            </p>
                            @endif
                            <button wire:click="addToCart('{{ $product->id }}')" @class([ 'mt-auto w-full px-4 py-2 rounded-lg' , 'bg-blue-500 text-white hover:bg-blue-600'=> ($activeTab === 'ready' && $product->stock > 0) || $activeTab === 'order',
                                'bg-gray-300 cursor-not-allowed' => $activeTab === 'ready' && $product->stock < 1, ]) @disabled($activeTab==='ready' && $product->stock < 1)>
                                        Tambah
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Keranjang -->
            <div class="flex-1">
                <div class="bg-white shadow rounded-lg p-4 sticky top-32">
                    <div class="flex justify-between gap-6 items-center mb-4">
                        <h2 class="text-lg font-semibold">Keranjang</h2>

                    </div>

                    @if(count($cart) > 0)
                    <div class="space-y-4 mb-4">
                        @foreach($cart as $index => $item)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button wire:click="removeFromCart('{{ $item['product_id'] }}')" class="px-2 py-1 border rounded-lg">
                                    -
                                </button>
                                <button wire:click="addToCart('{{ $item['product_id'] }}')" @class([ 'px-2 py-1 border rounded-lg' , 'cursor-not-allowed bg-gray-100'=> $activeTab === 'ready' && $item['quantity'] >= \App\Models\Product::find($item['product_id'])->stock,
                                    ])
                                    @disabled($activeTab === 'ready' && $item['quantity'] >= \App\Models\Product::find($item['product_id'])->stock)
                                    >
                                    +
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($activeTab === 'order')
                    <div class="mb-4">
                        <label class="block mb-2">Jadwal Pengambilan</label>
                        <div x-data x-init="
                                    flatpickr($refs.input, {
                                        dateFormat: 'd-m-Y',
                                        onChange: function(selectedDates, dateStr, instance) {
                                            if (selectedDates.length > 0) {
                                                let formatted = instance.formatDate(selectedDates[0], 'Y-m-d');
                                                @this.set('schedule', formatted);
                                            }
                                        }
                                    });
                                " class="relative">
                            <input x-ref="input" type="text" wire:model="schedule" class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" placeholder="Pilih tanggal" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Metode Pembayaran</label>
                            <select wire:model="paymentMethod" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Pilih</option>
                                <option value="tunai">Tunai</option>
                                <option value="non tunai">Non Tunai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2">Status Pembayaran</label>
                            <select wire:model="paymentStatus" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Pilih</option>
                                <option value="lunas">Lunas</option>
                                <option value="belum lunas">Belum Lunas</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2">DP (Optional)</label>
                        <input type="number" wire:model="dp" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    @else
                    <div class="mb-4">
                        <label class="block mb-2">Metode Pembayaran</label>
                        <select wire:model="paymentMethod" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Pilih</option>
                            <option value="tunai">Tunai</option>
                            <option value="non tunai">Non Tunai</option>
                        </select>
                    </div>
                    @endif

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-semibold">Total:</span>
                            <span class="font-semibold">
                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </span>
                        </div>
                        <button wire:click="processPayment" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600" wire:loading.attr="disabled">
                            Proses Pembayaran
                        </button>
                    </div>
                    @else
                    <p class="text-gray-500 text-center">Keranjang kosong</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
