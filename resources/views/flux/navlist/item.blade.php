@aware(['variant'])

@props([
'iconVariant' => 'outline',
'iconTrailing' => null,
'badgeColor' => null,
'variant' => null,
'iconDot' => null,
'accent' => true,
'badge' => null,
'icon' => null,
'solo' => false,
])

@php
// Button should be a square if it has no text contents...
$square ??= $slot->isEmpty();

// Size-up icons in square/icon-only buttons...
$iconClasses = Flux::classes($square ? 'size-6!' : 'size-6!');

$classes = Flux::classes()
->add($solo ? '' : 'pl-10')
->add('relative flex items-center gap-3 overflow-hidden')
->add($square ? 'px-2.5!' : '')
->add('py-3 text-left w-full ps-[calc(0.75rem-1px)] pe-3')
->add('text-zinc-800 dark:text-white/80')
->add([
'data-current:bg-[#74512d20]',
'data-current:border-zinc-800',
'data-current:text-[#74512D] data-current:font-semibold',
'hover:bg-[#74512d20] hover:text-[#74512D] hover:font-semibold',
]);
@endphp

<flux:button-or-link :attributes="$attributes->class($classes)" data-flux-navlist-item>
    <?php if ($icon): ?>
    <!-- Perubahan di sini: gunakan kelas CSS khusus -->
    <div class="relative">
        <?php if (is_string($icon) && $icon !== ''): ?>
        <flux:icon :$icon :variant="$iconVariant" class="{!! $iconClasses !!}" />
        <?php else: ?>
        {{ $icon }}
        <?php endif; ?>

        <?php if ($iconDot): ?>
        <div class="absolute top-[-2px] right-[-2px]">
            <div class="size-[6px] rounded-full bg-zinc-500 dark:bg-zinc-400"></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($slot->isNotEmpty()): ?>
    <span
        class="{{ $solo ? 'ml-1' : 'ml-3'  }} flex-1 text-sm font-medium leading-none whitespace-nowrap [[data-nav-footer]_&]:hidden [[data-nav-sidebar]_[data-nav-footer]_&]:block"
        data-content>{{ $slot }}</span>
    <?php endif; ?>

    <?php if (is_string($iconTrailing) && $iconTrailing !== ''): ?>
    <flux:icon :icon="$iconTrailing" :variant="$iconVariant" class="size-6!" />
    <?php elseif ($iconTrailing): ?>
    {{ $iconTrailing }}
    <?php endif; ?>

    <?php if ($badge): ?>
    <flux:navlist.badge :color="$badgeColor">{{ $badge }}</flux:navlist.badge>
    <?php endif; ?>
</flux:button-or-link>