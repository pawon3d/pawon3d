<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
        <flux:button variant="secondary" href="{{ route('hitung') }}" wire:navigate icon="arrow-left" class="w-full sm:w-auto">
            Kembali
        </flux:button>
        <h1 class="text-xl font-semibold text-[#666666] text-center sm:text-left">Riwayat Hitung dan Catat Persediaan</h1>
    </div>

    {{-- Table Card --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-6">
        {{-- Search & Filter --}}
        <div class="flex flex-col sm:flex-row gap-4 items-center mb-5">
            <div class="flex-1 flex items-center bg-white border border-[#666666] rounded-full px-4 w-full">
                <flux:icon.magnifying-glass class="size-5 text-[#666666] shrink-0" />
                <input type="text" wire:model.live="search" placeholder="Cari Aksi..."
                    class="flex-1 px-3 py-2.5 border-0 bg-transparent text-[#666666] placeholder-[#959595] focus:outline-none focus:ring-0" />
            </div>
            <button type="button" class="flex items-center gap-1 text-[#666666] justify-center w-full sm:w-auto">
                <flux:icon.funnel class="size-5" />
                <span class="font-medium">Filter</span>
            </button>
        </div>

        {{-- Table --}}
        <div class="w-full overflow-x-auto rounded-[15px] shadow-sm mb-5">
            <x-table.paginated :headers="[
                ['label' => 'ID Aksi', 'sortable' => true, 'sort-by' => 'hitung_number', 'class' => 'min-w-[150px]'],
                ['label' => 'Selesai', 'sortable' => true, 'sort-by' => 'hitung_date_finish'],
                ['label' => 'Aksi', 'sortable' => true, 'sort-by' => 'action'],
                ['label' => 'Persediaan', 'class' => 'min-w-[200px]'],
                ['label' => 'Inventaris', 'sortable' => true, 'sort-by' => 'user_id'],
                ['label' => 'Status'],
            ]" :paginator="$hitungs" headerBg="#3F4E4F" headerText="#F8F4E1"
                wrapperClass="mb-0">
            @foreach ($hitungs as $hitung)
                <tr class="border-b border-[#d4d4d4] hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-[#666666] font-medium">
                        <a href="{{ route('hitung.rincian', $hitung->id) }}" class="hover:underline" wire:navigate>
                            {{ $hitung->hitung_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-[#666666] font-medium">
                        {{ $hitung->hitung_date_finish ? \Carbon\Carbon::parse($hitung->hitung_date_finish)->translatedFormat('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-[#666666] font-medium">
                        {{ $hitung->action ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-[#666666] font-medium">
                        @if ($hitung->details->isNotEmpty())
                            {{ $hitung->details->take(2)->pluck('material.name')->join(', ') }}
                            @if ($hitung->details->count() > 2)
                                <span class="text-xs text-gray-400">+{{ $hitung->details->count() - 2 }} lainnya</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-[#666666] font-medium">
                        {{ $hitung->user->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'Selesai' => 'bg-[#56c568]',
                                'Dibatalkan' => 'bg-[#eb5757]',
                                'Sedang Diproses' => 'bg-[#f2c94c]',
                            ];
                            $bgColor = $statusColors[$hitung->status] ?? 'bg-gray-400';
                        @endphp
                        <span
                            class="{{ $bgColor }} text-white text-xs font-bold px-4 py-1.5 rounded-full min-w-[90px] inline-block text-center">
                            {{ $hitung->status ?? '-' }}
                        </span>
                    </td>
                </tr>
            @endforeach
            </x-table.paginated>
        </div>
    </div>

    {{-- Cetak Button --}}
    {{-- <div class="mt-4 flex justify-end">
        <flux:button variant="secondary" wire:click="cetakInformasi" icon="printer">
            Cetak Informasi
        </flux:button>
    </div> --}}
</div>
