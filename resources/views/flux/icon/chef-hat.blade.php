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

    <path d="M10 0C8.13684 0 6.49474 1.25561 5.96842 3.08
         C5.41053 2.82244 4.82105 2.68293 4.21053 2.68293
         C3.09383 2.68293 2.02286 3.13519 1.23323 3.94022
         C0.443608 4.74526 0 5.83712 0 6.97561
         C0.002494 7.92626 0.313678 8.84932 0.884967 9.60066
         C1.45626 10.352 2.25549 10.8893 3.15789 11.1288
         V18.7805H16.8421V11.1288
         C18.6947 10.6351 20 8.92878 20 6.97561
         C20 5.83712 19.5564 4.74526 18.7668 3.94022
         C17.9771 3.13519 16.9062 2.68293 15.7895 2.68293
         C15.1789 2.68293 14.5895 2.82244 14.0316 3.08
         C13.5053 1.25561 11.8632 0 10 0Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
        stroke-linejoin="round" />
    <path d="M9.47 9.66H10.53V17.17H9.47V9.66Z
         M6.32 11.8H7.37V17.17H6.32V11.8Z
         M12.63 11.8H13.68V17.17H12.63V11.8Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
        stroke-linejoin="round" />
    <path d="M3.16 19.85V20.93
         C3.16 21.21 3.27 21.48 3.47 21.69
         C3.66 21.89 3.93 22 4.21 22H15.79
         C16.07 22 16.34 21.89 16.53 21.69
         C16.73 21.48 16.84 21.21 16.84 20.93
         V19.85H3.16Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />

</svg>