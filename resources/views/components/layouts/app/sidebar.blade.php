<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group heading="Platform" class="grid">
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
            <flux:navlist.item icon="newspaper" :href="route('review')" :current="request()->routeIs('review')"
                wire:navigate>{{ __('Ulasan') }}</flux:navlist.item>
        </flux:navlist>

        <flux:spacer />


        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
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
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
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
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

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
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
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
    </flux:header>

    {{ $slot }}

    @fluxScripts
    <script src="{{ asset('flowbite/flowbite.min.js') }}"></script>
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>

    <x-livewire-alert::scripts />
</body>

</html>