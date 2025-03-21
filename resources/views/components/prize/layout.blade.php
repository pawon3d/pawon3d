<div class="flex items-start max-md:flex-col">
    <div class="mr-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('hadiah')" :current="request()->routeIs('hadiah')" wire:navigate>
                {{ __('Daftar Kode') }}</flux:navlist.item>
            <flux:navlist.item :href="route('hadiah.didapat')" :current="request()->routeIs('hadiah.didapat')"
                wire:navigate>{{ __('Kode Didapat') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('hadiah.ditukar')" :current="request()->routeIs('hadiah.ditukar')"
                wire:navigate>{{ __('Kode Ditukar') }}
            </flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <h1 class="text-3xl font-bold">{{ $heading ?? '' }}</h1>

        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>