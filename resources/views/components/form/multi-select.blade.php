@props([
    'options' => [],
    'selected' => [],
    'placeholder' => 'Pilih...',
    'label' => null,
    'searchable' => true,
    'name' => 'items',
])

@php
    $componentId = 'multi-select-' . uniqid();
    $selected = is_array($selected) ? $selected : [];
@endphp

<div x-data="{
    open: false,
    search: ''
}" @click.away="open = false" class="relative w-full">

    @if ($label)
        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
    @endif

    <!-- Selected Tags Display -->
    <div @click="open = !open"
        class="min-h-[44px] w-full px-4 py-2 bg-[#FAFAFA] border-[1.5px] border-[#ADADAD] rounded-2xl cursor-pointer hover:border-[#74512D] transition-colors">
        <div class="flex flex-wrap gap-2">
            @foreach ($options as $option)
                @if (in_array($option['id'], $selected))
                    <div
                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#74512D] text-[#F8F4E1] text-sm font-medium rounded-full">
                        <span>{{ $option['name'] }}</span>
                        <button type="button"
                            wire:click.stop="$set('{{ $name }}', {{ json_encode(array_values(array_diff($selected, [$option['id']]))) }})"
                            class="hover:text-red-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                @endif
            @endforeach

            @if (count($selected) === 0)
                <span class="text-[#959595] text-base font-normal">
                    {{ $placeholder }}
                </span>
            @endif
        </div>
    </div>

    <!-- Dropdown -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full mt-2 bg-white border border-[#ADADAD] rounded-2xl shadow-lg max-h-60 overflow-hidden"
        style="display: none;">

        @if ($searchable)
            <!-- Search Input -->
            <div class="p-3 border-b border-[#ADADAD]">
                <input type="text" x-model="search" placeholder="Cari..."
                    class="w-full px-4 py-2 bg-[#FAFAFA] border border-[#ADADAD] rounded-xl text-sm focus:outline-none focus:border-[#74512D] transition-colors"
                    @click.stop />
            </div>
        @endif

        <!-- Options List -->
        <div class="overflow-y-auto max-h-48">
            @foreach ($options as $option)
                <div x-show="search === '' || '{{ strtolower($option['name']) }}'.includes(search.toLowerCase())"
                    wire:click.stop="$set('{{ $name }}', {{ in_array($option['id'], $selected) ? json_encode(array_values(array_diff($selected, [$option['id']]))) : json_encode(array_merge($selected, [$option['id']])) }})"
                    class="px-4 py-3 cursor-pointer hover:bg-gray-50 transition-colors flex items-center justify-between {{ in_array($option['id'], $selected) ? 'bg-[#F8F4E1]' : '' }}">
                    <span class="text-sm text-[#666666]">{{ $option['name'] }}</span>
                    @if (in_array($option['id'], $selected))
                        <svg class="w-5 h-5 text-[#3F4E4F]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
