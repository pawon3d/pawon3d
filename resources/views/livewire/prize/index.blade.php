<section class="w-full">
    <x-prize.layout :heading="__('Kode Hadiah')">

        <div class="flex items-end justify-between mb-7">
            <div class="flex gap-2 items-center">
                <button wire:click="openAddModal"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kode Hadiah
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border">
            <!-- Search Input -->
            <div class="p-4">
                <input wire:model.live="search_code" placeholder="Cari..."
                    class="w-full max-w-sm px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode Hadiah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produk Hadiah</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($prizes as $prize)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $prize->code }}
                                <span class="text-xs text-green-500">
                                    @if ($prize->is_redeem)
                                    (ditukar)
                                    @elseif ($prize->is_get)
                                    (didapat)
                                    @endif

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $prize->product->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <button wire:click="openEditModal({{ $prize }})"
                                    class="px-3 py-1 border rounded-md hover:bg-gray-100">
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $prize }})"
                                    class="px-3 py-1 border rounded-md text-red-600 hover:bg-red-50">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $prizes->links() }}
            </div>
        </div>

        <!-- Add Modal -->
        <flux:modal name="tambah-kode" class="w-1/2 relative" wire:model="showModal">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Tambah Kode Hadiah</flux:heading>
                </div>
                <form wire:submit.prevent='store' class="space-y-4">

                    <livewire:product-select />

                    <div class="flex">
                        <flux:spacer />

                        <flux:button type="submit" variant="primary">Simpan</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        <!-- Edit Modal -->
        <flux:modal name="edit-kode" class="w-full max-w-lg" wire:model="showEditModal">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Kategori</flux:heading>
                </div>
                <form wire:submit.prevent='update' class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode Hadiah</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $code }}
                        </p>
                    </div>

                    <livewire:product-select />

                    <div class="flex">
                        <flux:spacer />

                        <flux:button type="submit" variant="primary">Simpan</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </x-prize.layout>
</section>