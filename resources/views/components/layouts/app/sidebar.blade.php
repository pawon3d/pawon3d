<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <flux:navlist variant="outline">
            <flux:navlist.group heading="Menu" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.item icon="user" :href="route('pengguna')" :current="request()->routeIs('pengguna')"
                wire:navigate>{{ __('Pengguna') }}</flux:navlist.item>

            <flux:navlist.group heading="Inventori" expandable
                :expanded="request()->routeIs('bahan-baku') || request()->routeIs('bahan-olahan')">
                <flux:navlist.item icon="inbox" :href="route('bahan-baku')" :current="request()->routeIs('bahan-baku')"
                    wire:navigate>{{ __('Bahan Baku') }}</flux:navlist.item>
                <flux:navlist.item icon="archive-box" :href="route('bahan-olahan')"
                    :current="request()->routeIs('bahan-olahan')" wire:navigate>{{ __('Bahan Baku Olahan') }}
                </flux:navlist.item>
                <flux:navlist.item icon="list-bullet" :href="route('kategori-persediaan')"
                    :current="request()->routeIs('kategori-persediaan') || request()->routeIs('kategori-persediaan.tambah') || request()->routeIs('kategori-persediaan.edit')"
                    wire:navigate>{{ __('Kategori') }}</flux:navlist.item>
            </flux:navlist.group>


            <flux:navlist.group heading="Produk" expandable
                :expanded="request()->routeIs('produk') || request()->routeIs('kategori')">
                <flux:navlist.item icon="list-bullet" :href="route('kategori')"
                    :current="request()->routeIs('kategori')" wire:navigate>{{ __('Kategori') }}</flux:navlist.item>
                <flux:navlist.item icon="inbox-stack" :href="route('produk')" :current="request()->routeIs('produk')"
                    wire:navigate>{{ __('Daftar Produk') }}</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.item icon="cube" :href="route('produksi')" :current="request()->routeIs('produksi')"
                wire:navigate>{{ __('Produksi') }}</flux:navlist.item>

            <flux:navlist.item icon="building-storefront" :href="route('pos')" :current="request()->routeIs('pos')"
                wire:navigate>{{ __('Point of Sale') }}</flux:navlist.item>
            <flux:navlist.item icon="calculator" :href="route('transaksi')" :current="request()->routeIs('transaksi')"
                wire:navigate>{{ __('Transaksi') }}</flux:navlist.item>
            <flux:navlist.item icon="gift" :href="route('hadiah')"
                :current="request()->routeIs('hadiah') || request()->routeIs('hadiah.didapat') || request()->routeIs('hadiah.ditukar')"
                wire:navigate>{{
                __('Hadiah') }}</flux:navlist.item>
            <flux:navlist.item icon="gift-top" :href="route('penukaran')" :current="request()->routeIs('penukaran')"
                wire:navigate>{{
                __('Penukaran Hadiah') }}</flux:navlist.item>
            <flux:navlist.item icon="building-storefront" :href="route('pengaturan')"
                :current="request()->routeIs('pengaturan')" wire:navigate>{{ __('Pengaturan Toko') }}
            </flux:navlist.item>
        </flux:navlist>

        <flux:spacer />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">

        <div class="flex items-center justify-between w-full">
            <flux:navbar class="hidden lg:flex w-full">
                <flux:navbar.item href="{{ route('dashboard') }}">
                    <x-app-logo />
                </flux:navbar.item>
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
    </flux:header>

    {{ $slot }}

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
    @yield('scripts')
</body>

</html>