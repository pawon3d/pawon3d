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

            <flux:navlist.group expandable :expanded="false"
                current="{{ Str::startsWith(Route::currentRouteName(), 'ringkasan') }}" heading="Dashboard"
                icon="align-end-horizontal">
                <flux:navlist.item :href="route('ringkasan-umum')"
                    :current="Str::startsWith(Route::currentRouteName(), 'ringkasan')" wire:navigate>
                    {{ __('Ringkasan Umum') }}</flux:navlist.item>
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
            <flux:navlist.group heading="Inventori"
                current="{{ Str::startsWith(Route::currentRouteName(), 'bahan-baku') || Str::startsWith(Route::currentRouteName(), 'supplier') || Str::startsWith(Route::currentRouteName(), 'belanja') || Str::startsWith(Route::currentRouteName(), 'hitung') || Str::startsWith(Route::currentRouteName(), 'kategori') || Str::startsWith(Route::currentRouteName(), 'produk') || Str::startsWith(Route::currentRouteName(), 'satuan-ukur') }}"
                expandable icon="warehouse" :expanded="false">
                <flux:navlist.item :href="route('bahan-baku')"
                    :current="Str::startsWith(Route::currentRouteName(), 'bahan-baku')" wire:navigate>
                    {{ __('Bahan
                    Baku') }}</flux:navlist.item>
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

            @canany(['Manajemen Sistem', 'Kasir'])
            <flux:navlist.group heading="Penilaian" expandable :expanded="false" icon="star" iconVariant="solid">
                <flux:navlist.item :href="route('hadiah')" :current="request()->routeIs('hadiah') || request()->routeIs('hadiah.didapat') || request()->routeIs(
                        'hadiah.ditukar')" wire:navigate>
                    {{ __('Hadiah') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('penukaran')" :current="request()->routeIs('penukaran')" wire:navigate>
                    {{ __('Penilaian') }}</flux:navlist.item>
            </flux:navlist.group>
            @endcanany

            @can('Manajemen Sistem')
            <flux:navlist.item solo="true" :href="route('customer')" :current="request()->routeIs('customer')"
                icon="user-group" wire:navigate>
                {{ __('Pelanggan') }}
            </flux:navlist.item>
            <flux:navlist.group heading="Karyawan" expandable icon="id-card" :expanded="false">
                <flux:navlist.item :href="route('user')" :current="Str::startsWith(Route::currentRouteName(), 'user')"
                    wire:navigate>{{ __('Pekerja') }}
                </flux:navlist.item>
                <flux:navlist.item :href="route('role')" :current="Str::startsWith(Route::currentRouteName(), 'role')"
                    wire:navigate>{{ __('Peran') }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endcan
            <flux:navlist.item solo="true" :href="route('pengaturan')" icon="cog-6-tooth"
                :current="request()->routeIs('pengaturan')" wire:navigate>{{ __('Pengaturan') }}
            </flux:navlist.item>
        </flux:navlist>

        <flux:spacer />
    </div>

    <!-- Mobile User Menu -->
    {{-- <flux:header
        class="block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">

        <div class="flex items-center justify-between w-full">
            <flux:navbar class="hidden lg:flex w-full">
                @if (!empty($mainTitle))
                <div class="flex flex-row flex-nowrap items-center gap-2 px-6 py-4 whitespace-nowrap">
                    <div class="ml-1 grid flex-1 text-left text-lg">
                        <span class="mb-0.5 truncate leading-none">{{ $mainTitle }}</span>
                    </div>
                </div>
                @else
                <flux:navbar.item href="{{ route('dashboard') }}">
                    <x-app-logo />
                </flux:navbar.item>
                @endif
            </flux:navbar>
            <flux:navbar class="w-full">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
                <flux:spacer />
                <flux:dropdown class="ml-auto" align="start" position="top">
                    <flux:button class="mr-4" variant="ghost">
                        <flux:icon.bell variant="outline" class="text-green-500" />
                    </flux:button>

                    <flux:menu>
                        @php
                        $notifications = App\Models\Notification::where('user_id',
                        auth()->user()->id)->orderBy('created_at',
                        'desc')->take(5)->get();
                        @endphp
                        @forelse ( $notifications as $notification)
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex justify-between gap-2 items-center">
                                    <div class="gap-2 px-1 py-1.5 text-left text-sm">
                                        <span class="text-sm block">{{ $notification->title }}</span>
                                        <span class="text-sm block">{{ $notification->body }}</span>
                                        <span class="text-xs block">{{ $notification->created_at->diffForHumans()
                                            }}</span>
                                    </div>
                                    @if (!$notification->is_read)
                                    <flux:button class="mr-4" variant="ghost" tooltip="Tandai Sudah Dibaca"
                                        iconVariant="micro" onclick="markAsRead('{{ $notification->id }}');">
                                        <flux:icon.check variant="micro" class="text-green-500" />
                                    </flux:button>
                                    @endif
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />
                        @empty
                        <flux:menu.radio.group>

                            <div class=" p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                    <span>
                                        Tidak ada notifikasi
                                    </span>
                                </div>
                            </div>
                        </flux:menu.radio.group>
                        @endforelse

                    </flux:menu>

                </flux:dropdown>
                <flux:dropdown position="top" align="start">
                    <flux:profile :initials="auth()->user()->initials()" name="{{ auth()->user()->name }}"
                        icon-trailing="chevron-down" />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
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
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                                class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:navbar>
        </div>
    </flux:header> --}}

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
            <flux:dropdown position="top" align="start">
                <flux:button variant="ghost">
                    <div class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-full">
                        @if (auth()->user()->image)
                        <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="{{ auth()->user()->name }}"
                            class="h-full w-full object-cover rounded-full">
                        @else
                        <span
                            class="flex h-full w-full items-center justify-center rounded-full bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                            {{ auth()->user()->initials() }}
                        </span>
                        @endif
                    </div>
                    <span class="font-semibold text-gray-100">{{ auth()->user()->name }} ({{
                        auth()->user()->getRoleNames()->first() }})</span>
                    <flux:icon.chevron-down variant="outline" class="text-gray-100 size-4" />
                </flux:button>

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-3 px-1 py-1.5 text-left text-sm">
                                <div class="grid flex-1 gap-3 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }} ({{
                                        auth()->user()->getRoleNames()->first() }})</span>
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
                    location.reload();
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
    </script>
    @yield('scripts')
</body>

</html>