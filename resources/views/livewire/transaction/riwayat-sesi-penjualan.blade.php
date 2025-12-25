<div class="h-full px-4 sm:px-0">
    {{-- Header dengan Tombol Kembali --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-[15px] mb-[30px]">
        <flux:button type="button" href="{{ route('transaksi') }}" variant="secondary" wire:navigate
            class="w-full sm:w-auto bg-[#313131] rounded-[15px] px-[25px] py-[10px] flex items-center justify-center gap-[5px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] hover:bg-[#252324] transition-colors"
            style="font-family: 'Montserrat', sans-serif;">
            <flux:icon icon="arrow-left" class="size-5 text-[#f8f4e1]" />
            <span class="text-[#f8f4e1] font-semibold text-[16px]"
                style="font-family: 'Montserrat', sans-serif; line-height: 1;">
                Kembali
            </span>
        </flux:button>
        <h1 class="text-[#666666] font-semibold text-[20px] text-center sm:text-left"
            style="font-family: 'Montserrat', sans-serif; line-height: 1;">
            Riwayat Sesi Penjualan
        </h1>
    </div>

    {{-- Content Area --}}
    <div class="bg-[#fafafa] rounded-[15px] p-4 sm:p-[30px] flex flex-col gap-[20px]">
        {{-- Search and Filter --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-[30px]">
            <div class="flex-1 flex flex-col sm:flex-row items-center gap-[15px] w-full">
                {{-- Search Input --}}
                <div
                    class="flex-1 bg-white border border-[#666666] rounded-[20px] px-[15px] py-0 flex items-center gap-0 w-full">
                    <flux:icon icon="magnifying-glass" class="size-[30px] text-[#666666]" />
                    <input type="text" wire:model.live="search" placeholder="Cari Sesi"
                        class="flex-1 w-full border-0 focus:ring-0 text-[16px] text-[#959595] py-[10px]"
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500;" />
                </div>

                {{-- Filter Button --}}
                <button class="flex items-center gap-0 text-[#666666] hover:text-[#3f4e4f] transition-colors justify-center">
                    <flux:icon icon="funnel" class="size-[25px]" />
                    <span class="text-[16px] font-medium px-[5px] py-[10px]"
                        style="font-family: 'Montserrat', sans-serif;">Filter</span>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <div class="min-w-[1000px]">
                <x-table.paginated :headers="[
                    [
                        'label' => 'No. Sesi',
                        'sortable' => true,
                        'sort-by' => 'shift_number',
                        'class' => 'w-[100px]',
                        'align' => 'right',
                    ],
                    ['label' => 'Tanggal Buka', 'sortable' => true, 'sort-by' => 'start_time', 'class' => 'w-[180px]'],
                    ['label' => 'Tanggal Tutup', 'sortable' => true, 'sort-by' => 'end_time', 'class' => 'w-[180px]'],
                    ['label' => 'Kasir', 'sortable' => true, 'sort-by' => 'opened_by', 'class' => 'w-[130px]'],
                    ['label' => 'Jumlah Awal Tunai', 'sortable' => true, 'sort-by' => 'initial_cash'],
                    ['label' => 'Penerimaan', 'sortable' => false],
                    ['label' => 'Jumlah Sebenarnya', 'sortable' => true, 'sort-by' => 'final_cash'],
                ]" :paginator="$shifts" emptyMessage="Belum ada riwayat sesi penjualan."
                    headerBg="#3f4e4f" headerText="#f8f4e1" bodyBg="#fafafa" bodyText="#666666"
                    wrapperClass="rounded-[15px] border border-[#d4d4d4]">
                    @foreach ($shifts as $shift)
                <tr class="border-b border-[#d4d4d4] hover:bg-[#f0f0f0] transition-colors">
                    {{-- No. Sesi --}}
                    <td class="px-6 py-4">
                        <a href="{{ route('transaksi.rincian-sesi', ['id' => $shift->id]) }}"
                            class="font-medium text-[14px] text-[#666666] hover:underline text-right block"
                            style="font-family: 'Montserrat', sans-serif;" wire:navigate>
                            {{ $shift->shift_number }}
                        </a>
                    </td>

                    {{-- Tanggal Buka --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-[10px] items-center">
                            <span class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('d M Y') }}
                            </span>
                            <span class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                            </span>
                        </div>
                    </td>

                    {{-- Tanggal Tutup --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-[10px] items-center">
                            <span class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                {{ $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('d M Y') : '-' }}
                            </span>
                            <span class="font-medium text-[14px] text-[#666666]"
                                style="font-family: 'Montserrat', sans-serif;">
                                {{ $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('H:i') : '' }}
                            </span>
                        </div>
                    </td>

                    {{-- Kasir --}}
                    <td class="px-6 py-4">
                        <span class="font-medium text-[14px] text-[#666666] truncate"
                            style="font-family: 'Montserrat', sans-serif;">
                            {{ $shift->openedBy->name ?? '-' }}
                        </span>
                    </td>

                    {{-- Jumlah Awal Tunai --}}
                    <td class="px-6 py-4">
                        <span class="font-medium text-[14px] text-[#666666]"
                            style="font-family: 'Montserrat', sans-serif;">
                            Rp{{ number_format($shift->initial_cash, 0, ',', '.') }}
                        </span>
                    </td>

                    {{-- Penerimaan --}}
                    <td class="px-6 py-4">
                        <span class="font-medium text-[14px] text-[#666666]"
                            style="font-family: 'Montserrat', sans-serif;">
                            Rp{{ number_format($shift->final_cash - $shift->initial_cash, 0, ',', '.') }}
                        </span>
                    </td>

                    {{-- Jumlah Sebenarnya --}}
                    <td class="px-6 py-4">
                        <span class="font-medium text-[14px] text-[#666666]"
                            style="font-family: 'Montserrat', sans-serif;">
                            Rp{{ number_format($shift->final_cash, 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
            @endforeach
                </x-table.paginated>
            </div>
        </div>
    </div>
</div>
