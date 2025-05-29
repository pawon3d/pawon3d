{{-- Credit: Lucide (https://lucide.dev) --}}

@props([
'variant' => 'outline',
])

@php
if ($variant === 'solid') {
throw new \Exception('The "solid" variant is not supported in Lucide.');
}

$classes = Flux::classes('shrink-0')
->add(match($variant) {
'outline' => '[:where(&)]:size-6',
'solid' => '[:where(&)]:size-6',
'mini' => '[:where(&)]:size-5',
'micro' => '[:where(&)]:size-4',
});

$strokeWidth = match ($variant) {
'outline' => 2,
'mini' => 2.25,
'micro' => 2.5,
};
@endphp

<svg {{ $attributes->class($classes) }}
    data-flux-icon
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    data-slot="icon"
    >

    <!-- Pegangan -->
    <rect x="7" y="2" width="10" height="6" rx="2" />

    <!-- Segitiga penghubung -->
    <path d="M10 8L12 12L14 8" />

    <!-- Alas trapesium -->
    <path d="M4 20L6 12H18L20 20Z" />

    <!-- Garis horizontal dalam alas -->
    <line x1="8" y1="16" x2="16" y2="16" />


</svg>