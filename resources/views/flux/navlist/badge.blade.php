@props([
    'color' => null,
])

@php
    $class = Flux::classes()
        ->add('text-xs font-medium rounded-sm px-1 py-0.5')
        ->add(
            match ($color) {
                default => 'text-zinc-700  bg-zinc-400/15 ',
                'red' => 'text-red-700  bg-red-400/20 ',
                'orange' => 'text-orange-700  bg-orange-400/20 ',
                'amber' => 'text-amber-700  bg-amber-400/25 ',
                'yellow' => 'text-yellow-800  bg-yellow-400/25 ',
                'lime' => 'text-lime-800  bg-lime-400/25 ',
                'green' => 'text-green-800  bg-green-400/20 ',
                'emerald' => 'text-emerald-800  bg-emerald-400/20 ',
                'teal' => 'text-teal-800  bg-teal-400/20 ',
                'cyan' => 'text-cyan-800  bg-cyan-400/20 ',
                'sky' => 'text-sky-800  bg-sky-400/20 ',
                'blue' => 'text-blue-800  bg-blue-400/20 ',
                'indigo' => 'text-indigo-700  bg-indigo-400/20 ',
                'violet' => 'text-violet-700  bg-violet-400/20 ',
                'purple' => 'text-purple-700  bg-purple-400/20 ',
                'fuchsia' => 'text-fuchsia-700  bg-fuchsia-400/20 ',
                'pink' => 'text-pink-700  bg-pink-400/20 ',
                'rose' => 'text-rose-700  bg-rose-400/20 ',
            },
        );
@endphp

<span {{ $attributes->class($class) }}>{{ $slot }}</span>
