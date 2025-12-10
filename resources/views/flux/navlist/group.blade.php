@props([
    'expandable' => false,
    'expanded' => true,
    'heading' => null,
    'icon' => null,
    'sub' => null,
    'current' => false,
])

<?php if ($expandable && $heading): ?>

<ui-disclosure {{ $attributes->class('group/disclosure') }} @if ($expanded === true) open @endif
    data-flux-navlist-group>
    <button type="button"
        class="redupkan group/disclosure-button flex py-3 w-full items-center hover:text-[#F8F4E1] hover:bg-[#74512d20] dark:text-white/80 dark:hover:bg-white/[7%] dark:hover:text-white {{ $current ? 'bg-[#74512d20] text-[#F8F4E1] font-semibold' : 'text-[#666666]' }}">
        @if ($icon)
            <div class="pl-3 pr-4">
                <x-dynamic-component :component="'flux::icon.' . $icon" @class([
                    'text-[#F8F4E1]' => $current == true,
                    'group-data-open/disclosure-button:text-[#F8F4E1]',
                    'dark:text-white/80',
                    'dark:group-data-open/disclosure-button:text-white',
                    'size-4!' => $sub === true,
                    'size-6!' => $sub !== true,
                ]) />
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

    <div class="relative hidden data-open:block" @if ($expanded === true) data-open @endif>
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
