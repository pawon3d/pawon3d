<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
</head>

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Route;

@endphp

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <div id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-12 mt-12 bg-white border-r shadow-lg overflow-hidden transition-all duration-300 flex flex-col">

        <flux:navlist variant="outline" class="my-4 overflow-y-scroll overflow-x-hidden h-screen scroll-hide">

            <flux:navlist.group onclick="openSidebar()" expandable :expanded="false"
                current="{{ Str::startsWith(Route::currentRouteName(), 'ringkasan') || Str::startsWith(Route::currentRouteName(), 'laporan-') }}"
                heading="Dashboard" icon="align-end-horizontal">
                <flux:navlist.item :href="route('ringkasan-umum')"
                    :current="Str::startsWith(Route::currentRouteName(), 'ringkasan')" wire:navigate>
                    {{ __('Ringkasan Umum') }}</flux:navlist.item>
                @can('Kasir')
                    <flux:navlist.item :href="route('laporan-kasir')" :current="request()->routeIs('laporan-kasir')"
                        wire:navigate>
                        {{ __('Laporan Kasir') }}</flux:navlist.item>
                @endcan
                @can('Produksi')
                    <flux:navlist.item :href="route('laporan-produksi')" :current="request()->routeIs('laporan-produksi')"
                        wire:navigate>
                        {{ __('Laporan Produksi') }}</flux:navlist.item>
                @endcan
                @can('Inventori')
                    <flux:navlist.item :href="route('laporan-inventori')" :current="request()->routeIs('laporan-inventori')"
                        wire:navigate>
                        {{ __('Laporan Inventori') }}</flux:navlist.item>
                @endcan
            </flux:navlist.group>

            @can('Kasir')
                <flux:navlist.item solo="true" :href="route('transaksi')" icon="cashier"
                    :current="request()->routeIs('transaksi')" wire:navigate>
                    {{ __('Transaksi') }}
                </flux:navlist.item>
            @endcan

            @can('Produksi')
                <flux:navlist.item solo="true" :href="route('produksi')" :current="request()->routeIs('produksi')"
                    icon="chef-hat" wire:navigate>
                    {{ __('Produksi') }}
                </flux:navlist.item>
            @endcan

            @can('Inventori')
                <flux:navlist.group onclick="openSidebar()" heading="Inventori"
                    current="{{ Str::startsWith(Route::currentRouteName(), 'bahan-baku') || Str::startsWith(Route::currentRouteName(), 'supplier') || Str::startsWith(Route::currentRouteName(), 'belanja') || Str::startsWith(Route::currentRouteName(), 'hitung') || Str::startsWith(Route::currentRouteName(), 'kategori') || request()->routeIs('produk') || request()->routeIs('produk.tambah') || request()->routeIs('produk.edit') || Str::startsWith(Route::currentRouteName(), 'satuan-ukur') }}"
                    expandable icon="warehouse" :expanded="false">
                    <flux:navlist.item :href="route('bahan-baku')"
                        :current="Str::startsWith(Route::currentRouteName(), 'bahan-baku')" wire:navigate>
                        {{ __('Bahan Baku') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('supplier')"
                        :current="Str::startsWith(Route::currentRouteName(), 'supplier')" wire:navigate>
                        {{ __('Toko Persediaan') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('belanja')"
                        :current="Str::startsWith(Route::currentRouteName(), 'belanja')" wire:navigate>{{ __('Belanja') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('hitung')"
                        :current="Str::startsWith(Route::currentRouteName(), 'hitung')" wire:navigate>
                        {{ __('Hitung dan Catat') }}</flux:navlist.item>
                    <flux:navlist.item :href="route('produk')" :current="request()->routeIs('produk')" wire:navigate>
                        {{ __('Produk') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            @endcan

            {{-- @canany(['Manajemen Sistem', 'Kasir'])
            <flux:navlist.group heading="Penilaian" expandable :expanded="false" icon="star" iconVariant="solid">
                <flux:navlist.item :href="route('hadiah')" :current="request()->routeIs('hadiah') || request()->routeIs('hadiah.didapat') || request()->routeIs(
                        'hadiah.ditukar')" wire:navigate>
                    {{ __('Hadiah') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('penukaran')" :current="request()->routeIs('penukaran')" wire:navigate>
                {{ __('Penilaian') }}</flux:navlist.item>
            </flux:navlist.group>
            @endcanany --}}

            @can('Manajemen Sistem')
                <flux:navlist.item solo="true" :href="route('customer')" :current="request()->routeIs('customer')"
                    icon="user-group" wire:navigate>
                    {{ __('Pelanggan') }}
                </flux:navlist.item>
                <flux:navlist.group onclick="openSidebar()" heading="Karyawan" expandable icon="id-card"
                    :expanded="false">
                    <flux:navlist.item :href="route('user')"
                        :current="Str::startsWith(Route::currentRouteName(), 'user')" wire:navigate>{{ __('Pekerja') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('role')"
                        :current="Str::startsWith(Route::currentRouteName(), 'role')" wire:navigate>{{ __('Peran') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            @endcan
            <flux:navlist.item solo="true" :href="route('pengaturan')" icon="cog-6-tooth"
                :current="request()->routeIs('pengaturan')" wire:navigate>{{ __('Pengaturan') }}
            </flux:navlist.item>
        </flux:navlist>

        <flux:spacer />
    </div>

    <div class="fixed inset-x-0 top-0 z-50 bg-[#74512D] border-b border-zinc-200 shadow-sm">
        <div class="flex items-center justify-between h-16 pr-4">
            <!-- Tombol toggle -->
            <div class="flex flex-row items-center gap-4 ">
                <flux:button type="button" variant="ghost" onclick="toggleSidebar()">
                    <flux:icon.bars-3 variant="outline" class="text-gray-100" />
                </flux:button>
                @if (!empty($mainTitle))
                    <div class="md:flex flex-row hidden flex-nowrap items-center gap-2 px-6 py-4 whitespace-nowrap">
                        <div class="ml-1 flex flex-row gap-3 items-center flex-1 text-left text-lg">
                            @if (!empty($storeProfile?->logo))
                                <img src="{{ asset('storage/' . $storeProfile->logo) }}" alt="Logo"
                                    class="w-14 h-14 object-cover rounded-full">
                            @endif

                            <span class="mb-0.5 truncate text-white">{{ $mainTitle }}</span>
                        </div>
                    </div>
                @else
                    <flux:navbar.item href="{{ route('dashboard') }}" class="md:flex hidden">
                        <x-app-logo />
                    </flux:navbar.item>
                @endif
            </div>
            <div class="flex items-center gap-4 flex-row">

                <flux:dropdown position="top" align="start">
                    <flux:button variant="ghost">
                        <div class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-full">
                            @if (auth()->user()->image)
                                <img src="{{ asset('storage/' . auth()->user()->image) }}"
                                    alt="{{ auth()->user()->name }}" class="h-full w-full object-cover rounded-full">
                            @else
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-full bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            @endif
                        </div>
                        <span class="font-semibold text-gray-100">{{ auth()->user()->name }}
                            ({{ auth()->user()->getRoleNames()->first() }})</span>
                        <flux:icon.chevron-down variant="outline" class="text-gray-100 size-4" />
                    </flux:button>

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-3 px-1 py-1.5 text-left text-sm">
                                    <div class="grid flex-1 gap-3 text-left text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}
                                            ({{ auth()->user()->getRoleNames()->first() }})</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                        <a class="text-xs text-gray-500 flex items-center gap-2 underline"
                                            href="{{ route('profil-saya', auth()->user()->id) }}" wire:navigate>
                                            Lihat Profil
                                        </a>
                                        <div class="flex justify-end">
                                            <flux:modal.trigger name="logoutModal">
                                                <flux:button type="button" icon="arrow-left-end-on-rectangle"
                                                    variant="primary">
                                                    Keluar
                                                </flux:button>
                                            </flux:modal.trigger>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>

                <flux:dropdown position="bottom" align="start">
                    <flux:button type="button" variant="ghost" class="cursor-pointer">
                        <flux:icon.bell
                            variant="{{ auth()->user()->unreadNotifications->count() > 0 ? 'solid' : 'outline' }}"
                            class="text-gray-100 size-5" />
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </flux:button>
                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal flex flex-row justify-between items-center">
                                <div class="flex items-center gap-3 px-1 py-1.5 text-left text-sm">
                                    <span class="truncate">Semua Notifikasi</span>
                                </div>
                                <div class="flex justify-end px-2 py-1.5">
                                    <button onclick="markAllAsRead()"
                                        class="text-end text-xs text-blue-600 hover:bg-gray-200 cursor-pointer">
                                        Tandai Semua Dibaca
                                    </button>
                                </div>
                            </div>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <flux:menu.radio.group>
                            @forelse (auth()->user()->notifications->take(5) as $notification)
                                <flux:menu.item
                                    class="flex flex-col justify-start items-start gap-1 {{ $notification->is_read ? 'bg-white' : 'bg-gray-100 font-semibold' }} hover:bg-gray-200 max-w-72 cursor-pointer"
                                    onclick="markAsRead('{{ $notification->id }}')">
                                    <p class="text-xs text-left line-clamp-2 break-words w-full">
                                        {{ $notification->title }}
                                        <span @class([
                                            'text-red-500' => $notification->status == 0,
                                            'text-yellow-500' => $notification->status == 1,
                                            'text-green-500' => $notification->status == 2,
                                        ])>
                                            {{ $notification->body }}
                                        </span>
                                    </p>
                                    <span class="text-xs text-right text-gray-500">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->locale('id')->diffForHumans() }}
                                    </span>
                                </flux:menu.item>
                            @empty
                                <flux:menu.item class="text-center text-xs text-gray-500">
                                    Tidak ada notifikasi
                                </flux:menu.item>
                            @endforelse
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <flux:menu.radio.group>
                            <div class="flex justify-end px-2 py-1.5">
                                <a href="{{ route('notifikasi') }}" wire:navigate
                                    class="text-end text-xs text-blue-600 hover:bg-gray-200">
                                    Lihat Semua Notifikasi
                                </a>
                            </div>
                        </flux:menu.radio.group>
                    </flux:menu>
                </flux:dropdown>
            </div>

        </div>
    </div>

    <div id="main-content" class="lg:ml-12 ml-12 mt-16 transition-all duration-300 bg-gray-100 min-h-screen">
        {{ $slot }}
    </div>

    <flux:modal name="logoutModal" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Yakin Keluar Akun?</flux:heading>
            </div>

            <div class="flex gap-2 justify-end w-full">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button type="button" icon="x-mark">Tidak</flux:button>
                </flux:modal.close>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button type="submit" variant="primary" icon="check" wire:loading.attr="disabled">Yakin
                    </flux:button>
                </form>
            </div>
        </div>
    </flux:modal>

    @fluxScripts
    <script src="{{ asset('flowbite/flowbite.min.js') }}"></script>
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <x-livewire-alert::scripts />
    <script>
        function markAsRead(id) {
            $.ajax({
                url: `/read-notification/${id}`,
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1000,
                        toast: true,
                        position: 'top-end',
                        timerProgressBar: true,
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        function markAllAsRead() {
            $.ajax({
                url: `/read-all-notification`,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1000,
                        toast: true,
                        position: 'top-end',
                        timerProgressBar: true,
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    </script>
    @livewireScripts
    <script>
        document.addEventListener('sweet.success', event => {
            Swal.fire({
                icon: 'success',
                text: event.detail.message,
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>
    @if (session('notification'))
        <script>
            Swal.fire({
                icon: 'success',
                text: '{{ session('notification') }}',
                showConfirmButton: false,
                timer: 1000,
                toast: true,
                position: 'top-end',
                timerProgressBar: true,
            });
        </script>
    @endif

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const labels = document.querySelectorAll('.menu-label');
            const content = document.getElementById('main-content');

            // Toggle lebar sidebar
            sidebar.classList.toggle('w-56');
            sidebar.classList.toggle('w-12');

            // Toggle label teks
            labels.forEach(label => {
                label.classList.toggle('hidden');
            });

            // Geser konten utama
            content.classList.toggle('lg:ml-56');
            content.classList.toggle('lg:ml-12');

        }

        function openSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('main-content');

            // Set lebar sidebar ke 56
            sidebar.classList.add('w-56');
            sidebar.classList.remove('w-12');

            // Geser konten utama
            content.classList.add('lg:ml-56');
            content.classList.remove('lg:ml-12');
        }
    </script>
    @yield('scripts')
</body>

</html>
