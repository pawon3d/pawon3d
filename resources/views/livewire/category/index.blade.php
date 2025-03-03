<div>
    <div class="flex items-end justify-between mb-7">
        <h1 class="text-3xl font-bold">Kategori</h1>
        <div class="flex gap-2 items-center">
            <button wire:click="openAddModal" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kategori
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border">
        <!-- Search Input -->
        <div class="p-4">
            <input wire:model.live="search" placeholder="Cari..." class="w-full max-w-sm px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button wire:click="openEditModal({{ $category }})" class="px-3 py-1 border rounded-md hover:bg-gray-100">
                                Edit
                            </button>
                            <button wire:click="confirmDelete({{ $category }})" class="px-3 py-1 border rounded-md text-red-600 hover:bg-red-50">
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
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Add Modal -->
    <flux:modal name="tambah-kategori" class="w-full max-w-lg" wire:model="showModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Kategori</flux:heading>
            </div>
            <form wire:submit.prevent='store' class="space-y-4">

                <flux:input label="Nama Kategori" placeholder="Nama Kategori" type="text" wire:model="name" />

                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal name="edit-kategori" class="w-full max-w-lg" wire:model="showEditModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Kategori</flux:heading>
            </div>
            <form wire:submit.prevent='update' class="space-y-4">

                <flux:input label="Nama Kategori" placeholder="Nama Kategori" type="text" wire:model="name" />

                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
