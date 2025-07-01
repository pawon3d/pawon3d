@props([
'expandable' => false,
'expanded' => true,
'heading' => null,
'icon' => null,
'sub' => null,
'current' => false,
])

<?php if ($expandable && $heading): ?>

<ui-disclosure {{ $attributes->class('group/disclosure') }}
    @if ($expanded === true) open @endif
    data-flux-navlist-group
    >
    <button type="button"
        class="group/disclosure-button mb-[2px] flex h-10 w-full items-center text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800 lg:h-8 dark:text-white/80 dark:hover:bg-white/[7%] dark:hover:text-white {{ $current ? 'bg-zinc-100 border-l-4 border-zinc-800 text-zinc-900 font-semibold' : '' }}">
        @if ($icon)
        <div class="pl-3 pr-4 {{ $current ? '-ml-1' : '' }}">
            <x-dynamic-component :component="'flux::icon.' . $icon" @class([ 'text-zinc-500'
                , 'group-data-open/disclosure-button:text-zinc-800' , 'dark:text-white/80'
                , 'dark:group-data-open/disclosure-button:text-white' , 'size-4!'=> $sub === true,
                'size-6!' => $sub !== true,
                ])
                />
        </div>
        @else
        <div class="pl-3 pr-4">
            <flux:icon.chevron-down class="hidden size-3! group-data-open/disclosure-button:block" />
            <flux:icon.chevron-right class="block size-3! group-data-open/disclosure-button:hidden" />
        </div>
        @endif

        <span class="text-sm font-medium leading-none">{{ $heading }}</span>

        @if ($icon)
        <div class="ml-auto pr-4">
            <flux:icon.chevron-up class="hidden size-3! group-data-open/disclosure-button:block" />
            <flux:icon.chevron-down class="block size-3! group-data-open/disclosure-button:hidden" />
        </div>
        @endif
    </button>

    <div class="relative hidden space-y-[2px] data-open:block" @if ($expanded===true) data-open @endif>
        {{-- <div class="absolute inset-y-[3px] left-0 ml-4 w-px bg-zinc-200 dark:bg-white/30"></div> --}}
        {{ $slot }}
    </div>
</ui-disclosure>

<?php elseif ($heading): ?>

<div {{ $attributes->class('block space-y-[2px]') }}>
    <div class="px-1 py-2">
        <div class="text-xs leading-none text-zinc-400">{{ $heading }}</div>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>

<?php else: ?>

<div {{ $attributes->class('block space-y-[2px]') }}>
    {{ $slot }}
</div>

<?php endif; ?>