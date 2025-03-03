<div>
    <div class="flex items-end justify-between mb-7">
        <h1 class="text-3xl font-bold">Pengguna</h1>
        <div class="flex gap-2 items-center">
            <button wire:click="openAddModal" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pengguna
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border">
        <!-- Search Input -->
        <div class="p-4">
            <input wire:model.live="search" placeholder="Cari nama pengguna..." class="w-full max-w-sm px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->username }}</td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $user->role }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button wire:click="openEditModal({{ $user }})" class="px-3 py-1 border rounded-md hover:bg-gray-100">
                                Edit
                            </button>
                            @if($user->role !== 'pemilik')
                            <button wire:click="confirmDelete({{ $user }})" class="px-3 py-1 border rounded-md text-red-600 hover:bg-red-50">
                                Hapus
                            </button>
                            @endif
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
            {{ $users->links() }}
        </div>
    </div>

    <!-- Add Modal -->
    <flux:modal name="tambah-pengguna" class="w-full max-w-lg" wire:model="showModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Pengguna</flux:heading>
            </div>
            <form wire:submit.prevent='store' class="space-y-4">

                <flux:input label="Nama" placeholder="Nama" type="text" wire:model="name" />

                <flux:input label="Username" placeholder="Username" type="text" wire:model="username" />

                <flux:select wire:model="role" placeholder="Pilih role" label="Role">
                    <flux:select.option value="kasir">Kasir</flux:select.option>
                    <flux:select.option value="produksi">Produksi</flux:select.option>
                </flux:select>

                <flux:input label="Password" placeholder="Password" type="password" wire:model="password" />


                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal name="edit-pengguna" class="w-full max-w-lg" wire:model="showEditModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Pengguna</flux:heading>
            </div>
            <form wire:submit.prevent='update' class="space-y-4">

                <flux:input label="Nama" placeholder="Nama" type="text" wire:model="name" />

                <flux:input label="Username" placeholder="Username" type="text" wire:model="username" />

                <flux:select wire:model="role" placeholder="Pilih role" label="Role">
                    @if ($this->role == 'pemilik')
                    <flux:select.option value="pemilik" selected>Pemilik</flux:select.option>
                    @endif
                    <flux:select.option value="kasir">Kasir</flux:select.option>
                    <flux:select.option value="produksi">Produksi</flux:select.option>
                </flux:select>

                <flux:input label="Password" placeholder="Password" type="password" wire:model="password" />


                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
