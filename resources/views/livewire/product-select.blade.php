<div x-data="{ open: @entangle('isOpen') }" x-on:click.away="open = false" class="relative">
    <!-- Input Box -->
    <input type="text" placeholder="Produk" id="product"
        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer"
        x-on:click="open = true" readonly wire:model="productName" />

    <!-- Dropdown -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg">
        <input type="text" placeholder="Cari produk..." wire:model.live="search"
            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
        <ul class="py-2 overflow-auto max-h-60">
            @forelse($products as $product)
            <li wire:key="{{ $product['id'] }}" x-on:click="$wire.selectProduct('{{ $product['id'] }}')"
                class="px-4 py-2 cursor-pointer hover:bg-blue-50" id="selected">
                {{ $product['name'] }}
            </li>
            @empty
            <li class="px-4 py-2 text-gray-500">
                Produk tidak ditemukan
            </li>
            @endforelse
        </ul>
    </div>

    <!-- Hidden Input untuk Form -->
    <input type="hidden" name="product_id" value="{{ $productId }}" />

</div>