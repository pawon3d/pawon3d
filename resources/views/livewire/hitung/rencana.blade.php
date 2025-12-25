<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
        <flux:button variant="secondary" href="{{ route('hitung') }}" wire:navigate icon="arrow-left" class="w-full sm:w-auto">
            Kembali
        </flux:button>
        <h1 class="text-xl font-semibold text-[#666666] text-center sm:text-left">Rencana Hitung dan Catat Persediaan</h1>
    </div>

    {{-- Info Box --}}
    <x-alert.info>
        Rencana Hitung dan Catat Persedian. Pilih dan mulai rencana aksi atau tambah aksi jika diperlukan.
        Hitung untuk menghitung jumlah persediaan dan catat untuk mencatat jumlah hilang atau rusak.
    </x-alert.info>

    {{-- Table Card --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-6">
        {{-- Search & Actions --}}
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-5">
            {{-- Search & Filter --}}
            <div class="flex flex-col sm:flex-row items-center gap-4 flex-1 w-full">
                <div class="flex-1 flex items-center bg-white border border-[#666666] rounded-full px-4 w-full">
                    <flux:icon.magnifying-glass class="size-5 text-[#666666] shrink-0" />
                    <input type="text" wire:model.live="search" placeholder="Cari Rencana..."
                        class="flex-1 px-3 py-2.5 border-0 bg-transparent text-[#666666] placeholder-[#959595] focus:outline-none focus:ring-0" />
                </div>
                <button type="button" class="flex items-center gap-1 text-[#666666] justify-center w-full sm:w-auto">
                    <flux:icon.funnel class="size-5" />
                    <span class="font-medium py-2.5">Filter</span>
                </button>
            </div>

            {{-- Tambah Aksi Button --}}
            <flux:button variant="primary" href="{{ route('hitung.tambah') }}" wire:navigate icon="plus" class="w-full lg:w-auto">
                Tambah Aksi
            </flux:button>
        </div>

        {{-- Table --}}
        <div class="w-full overflow-x-auto rounded-[15px] shadow-sm mb-5">
            <x-table.paginated :headers="[
                ['label' => 'ID Aksi', 'sortable' => true, 'sort-by' => 'hitung_number', 'class' => 'min-w-[150px]'],
                ['label' => 'Tanggal', 'sortable' => true, 'sort-by' => 'hitung_date'],
                ['label' => 'Aksi', 'sortable' => true, 'sort-by' => 'action'],
                ['label' => 'Persediaan', 'class' => 'min-w-[200px]'],
                ['label' => 'Status'],
            ]" :paginator="$hitungs" headerBg="#3F4E4F" headerText="#F8F4E1"
                emptyMessage="Belum Ada Rencana Aksi. Tekan tombol 'Tambah Aksi' untuk menambahkan aksi."
                wrapperClass="mb-0">
            @foreach ($hitungs as $hitung)
                <tr class="border-b border-[#d4d4d4] hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-[#666666] font-medium">
                        <a href="{{ route('hitung.rincian', $hitung->id) }}" class="hover:underline" wire:navigate>
                            {{ $hitung->hitung_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-[#666666] font-medium">
                        {{ $hitung->hitung_date ? \Carbon\Carbon::parse($hitung->hitung_date)->translatedFormat('d F Y') : '-' }}
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
                    <td class="px-6 py-4">
                        <span
                            class="bg-[#adadad] text-white text-xs font-bold px-4 py-1.5 rounded-full min-w-[90px] inline-block text-center leading-tight">
                            Belum<br>Diproses
                        </span>
                    </td>
                </tr>
            @endforeach
            </x-table.paginated>
        </div>
    </div>
</div>
