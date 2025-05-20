<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white">
    <div class="fixed inset-x-0 top-0 z-50 bg-[#333333] border-b border-zinc-200 shadow-sm">
        <div class="flex items-center justify-between h-16 px-4">
            <!-- Tombol toggle -->
            <flux:button type="button" variant="ghost" onclick="toggleSidebar()">
                <flux:icon.bars-3 variant="outline" class="text-gray-100" />
            </flux:button>
            <span class="text-lg font-bold text-blue-600">LOGO</span>
            <flux:dropdown position="top" align="start">
                <flux:button variant="ghost">
                    <div class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-full">
                        <span
                            class="flex h-full w-full items-center justify-center rounded-full bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                            {{ auth()->user()->initials() }}
                        </span>
                    </div>
                    <span class="font-semibold text-gray-100">{{ auth()->user()->name }}</span>
                    <flux:icon.chevron-down variant="outline" class="text-gray-100 size-4" />
                </flux:button>

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-full bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->role }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Settings</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    <!-- Sidebar -->
    <div id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-16 mt-16 bg-white border-r shadow-lg overflow-hidden transition-all duration-300 flex flex-col">
        <ul class="flex-1 mt-4 space-y-2">
            <li class="flex items-center px-4 py-2 hover:bg-gray-100">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M3 12h18M3 6h18M3 18h18" />
                </svg>
                <span class="ml-4 text-sm text-gray-700 font-medium hidden menu-label">Dashboard</span>
            </li>
            <li class="flex items-center px-4 py-2 hover:bg-gray-100">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7" />
                </svg>
                <span class="ml-4 text-sm text-gray-700 font-medium hidden menu-label">Tasks</span>
            </li>
            <!-- Tambahkan item lainnya -->
        </ul>
    </div>

    <!-- Konten utama -->
    <div id="main-content" class="lg:ml-16 ml-16 mt-16 transition-all duration-300">
        <div class="p-6">

            <div>
                <div class="flex items-end justify-between mb-7">
                    <h1 class="text-3xl font-bold">Pengguna</h1>
                    <div class="flex gap-2 items-center">
                        <button wire:click="openAddModal"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Pengguna
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-xl border">
                    <!-- Search Input -->
                    <div class="p-4">
                        <input wire:model.live="search" placeholder="Cari nama pengguna..."
                            class="w-full max-w-sm px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Username</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">ads</td>
                                    <td class="px-6 py-4 whitespace-nowrap">asd</td>
                                    <td class="px-6 py-4 whitespace-nowrap capitalize">asd</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                        asd
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-4">
                        123
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

                                <flux:select.option value="pemilik" selected>Pemilik</flux:select.option>

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


        </div>
    </div>


    @fluxScripts
    <script src="{{ asset('flowbite/flowbite.min.js') }}"></script>
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <x-livewire-alert::scripts />
    @livewireScripts
    <script>
        function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const labels = document.querySelectorAll('.menu-label');
      const content = document.getElementById('main-content');

      // Toggle lebar sidebar
      sidebar.classList.toggle('w-64');
      sidebar.classList.toggle('w-16');

      // Toggle label teks
      labels.forEach(label => {
        label.classList.toggle('hidden');
      });

      // Geser konten utama
      content.classList.toggle('lg:ml-64');
      content.classList.toggle('lg:ml-16');
    }
    </script>
    @yield('scripts')
</body>

</html>