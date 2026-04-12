<div class="min-h-screen bg-[#eaeaea] px-4 py-4 sm:px-8 sm:py-8">
    <div class="mx-auto flex w-full max-w-[1400px] flex-col gap-6 sm:gap-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col items-center gap-3 sm:flex-row sm:gap-4">
                <flux:button href="{{ route('produksi', ['method' => 'siap-beli']) }}" icon="arrow-left"
                    variant="secondary" wire:navigate class="w-full justify-center sm:w-auto">
                    Kembali
                </flux:button>
                <h1 class="text-center font-['Montserrat'] text-xl font-semibold text-[#666666] sm:text-left">
                    Rincian Produksi
                </h1>
            </div>

            @if ($status === 'Sedang Diproses')
            <flux:button variant="secondary" wire:click="riwayatPembaruan" class="w-full sm:w-auto">
                Riwayat Pembaruan
            </flux:button>
            @endif
        </div>

        <div class="rounded-2xl bg-[#fafafa] p-4 shadow-[0px_2px_3px_rgba(0,0,0,0.1)] sm:p-7">
            <div class="flex flex-col gap-6">
                @php
                $statusColor = match ($status) {
                'Belum Diproses' => '#adadad',
                'Sedang Diproses' => '#ffc400',
                default => '#56c568',
                };
                @endphp

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2
                        class="break-all text-center font-['Montserrat'] text-2xl font-medium text-[#666666] sm:text-left sm:text-3xl">
                        {{ $production->production_number }}
                    </h2>
                    <span
                        class="inline-flex items-center justify-center rounded-full px-5 py-2 text-sm font-bold text-[#fafafa] sm:text-base"
                        style="background-color: {{ $statusColor }};">
                        {{ $status }}
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-[#d4d4d4] bg-[#f6f6f6] p-4">
                        <p class="mb-1 font-['Montserrat'] text-sm font-semibold text-[#666666]">Rencana Produksi</p>
                        <p class="font-['Montserrat'] text-sm text-[#666666] sm:text-base">
                            {{ \Carbon\Carbon::parse($production->start_date)->translatedFormat('d M Y') }}
                            {{ \Carbon\Carbon::parse($production->time)->format('H:i') }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-[#d4d4d4] bg-[#f6f6f6] p-4">
                        <p class="mb-1 font-['Montserrat'] text-sm font-semibold text-[#666666]">Tanggal Mulai Produksi
                        </p>
                        @if ($production->date)
                        <p class="font-['Montserrat'] text-sm text-[#666666] sm:text-base">
                            {{ \Carbon\Carbon::parse($production->date)->format('d M Y') }}
                            {{ \Carbon\Carbon::parse($production->date)->format('H:i') }}
                        </p>
                        @else
                        <p class="font-['Montserrat'] text-sm text-[#666666] sm:text-base">-</p>
                        @endif
                    </div>

                    <div class="rounded-xl border border-[#d4d4d4] bg-[#f6f6f6] p-4">
                        <p class="mb-1 font-['Montserrat'] text-sm font-semibold text-[#666666]">Tanggal Produksi
                            Selesai</p>
                        <p class="font-['Montserrat'] text-sm text-[#666666] sm:text-base">
                            {{ $production->end_date ? \Carbon\Carbon::parse($production->end_date)->translatedFormat('d
                            M Y H:i') : '-' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-[#d4d4d4] bg-[#f6f6f6] p-4">
                        <p class="mb-1 font-['Montserrat'] text-sm font-semibold text-[#666666]">Koki</p>
                        <p class="font-['Montserrat'] text-sm text-[#666666] sm:text-base">-</p>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <div class="h-4 overflow-hidden rounded-md bg-[#eaeaea] sm:h-5">
                        <div class="h-full rounded-md bg-[#3f4e4f] transition-[width] duration-300"
                            style="width: {{ $percentage }}%;"></div>
                    </div>
                    <p class="text-center font-['Montserrat'] text-xs font-medium text-[#525252] sm:text-sm">
                        {{ number_format($percentage, 0) }}% ({{ $total_quantity_get }} dari {{ $total_quantity_plan }})
                    </p>
                </div>

                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="font-['Montserrat'] text-base font-medium text-[#666666]">Rencana Produksi</p>
                        <button wire:click="buatCatatan"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-[15px] bg-[#666666] px-5 py-2.5 text-sm font-semibold text-[#f6f6f6] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] sm:w-auto sm:text-base">
                            <flux:icon icon="pencil" class="size-4" />
                            Buat Catatan
                        </button>
                    </div>

                    <div class="min-h-[120px] rounded-[15px] border border-[#d4d4d4] bg-[#eaeaea] p-4 sm:p-5">
                        <p class="font-['Montserrat'] text-sm text-[#666666] sm:text-base">{{ $production->note ?:
                            'Tidak ada catatan' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-[#fafafa] p-4 shadow-[0px_2px_3px_rgba(0,0,0,0.1)] sm:p-7">
            <div class="mb-4 flex items-center justify-between gap-3">
                <p class="font-['Montserrat'] text-base font-medium text-[#666666]">Daftar Produk</p>
                <p class="text-xs text-[#8a8a8a] sm:hidden">Geser tabel ke samping</p>
            </div>

            <div class="overflow-x-auto rounded-xl border border-[#d4d4d4]">
                <table class="min-w-[980px] w-full border-collapse text-sm font-['Montserrat']">
                    <thead>
                        <tr class="bg-[#3f4e4f] text-[#f8f4e1]">
                            <th class="px-5 py-4 text-left font-bold">Produk</th>
                            <th class="px-5 py-4 text-right font-bold">Rencana Produksi</th>
                            <th class="px-5 py-4 text-right font-bold">Selisih Didapatkan</th>
                            <th class="px-5 py-4 text-right font-bold">Jumlah Didapatkan</th>
                            <th class="px-5 py-4 text-right font-bold">Ulang</th>
                            <th class="px-5 py-4 text-right font-bold">Pcs Gagal</th>
                            <th class="px-5 py-4 text-right font-bold">Pcs Lebih</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[#fafafa] text-[#666666]">
                        @foreach ($production_details as $detail)
                        <tr class="border-t border-[#d4d4d4]">
                            <td class="max-w-[260px] truncate px-5 py-4 font-medium">{{ $detail->product->name }}</td>
                            <td class="px-5 py-4 text-right">{{ $detail->quantity_plan }}</td>
                            <td class="px-5 py-4 text-right">{{ $detail->quantity_get - $detail->quantity_plan }}</td>
                            <td class="px-5 py-4 text-right">{{ $detail->quantity_get }}</td>
                            <td class="px-5 py-4 text-right">{{ $detail->cycle }}</td>
                            <td class="px-5 py-4 text-right">{{ $detail->quantity_fail }}</td>
                            <td class="px-5 py-4 text-right">{{ max(0, $detail->quantity_get - $detail->quantity_plan)
                                }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-[#d4d4d4] bg-[#eaeaea] text-[#666666]">
                            <td class="px-5 py-4 font-bold">Total</td>
                            <td class="px-5 py-4 text-right font-bold">{{ $total_quantity_plan }}</td>
                            <td class="px-5 py-4 text-right font-bold">{{ $total_selisih }}</td>
                            <td class="px-5 py-4 text-right font-bold">{{ $total_quantity_get }}</td>
                            <td class="px-5 py-4 text-right font-bold">{{ $total_cycle }}</td>
                            <td class="px-5 py-4 text-right font-bold">{{ $total_pcs_gagal }}</td>
                            <td class="px-5 py-4 text-right font-bold">{{ $total_pcs_lebih }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            @if ($status === 'Belum Diproses')
            <button wire:click="confirmDelete"
                class="inline-flex w-full items-center justify-center gap-2 rounded-[15px] bg-[#eb5757] px-6 py-2.5 text-sm font-semibold text-[#f8f4e1] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] sm:w-auto sm:text-base">
                <flux:icon icon="trash" class="size-5" />
                Hapus Rencana Produksi
            </button>

            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                <button wire:click="ubahProduksi"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-[15px] bg-[#feba17] px-6 py-2.5 text-sm font-bold text-[#f8f4e1] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] sm:w-auto sm:text-base"
                    wire:navigate>
                    <flux:icon icon="pencil-square" class="size-5" />
                    Ubah Produksi
                </button>

                <flux:button variant="secondary" icon="chef-hat" wire:click="start" class="w-full sm:w-auto">
                    <span class="font-['Montserrat'] text-sm font-medium sm:text-base">Mulai Produksi</span>
                </flux:button>
            </div>
            @else
            <div class="hidden sm:block"></div>

            <div class="ml-auto flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                <flux:button icon="check-circle" wire:click="selesaikanProduksi" class="w-full sm:w-auto">
                    <span class="font-['Montserrat'] text-sm font-semibold sm:text-base">Selesaikan Produksi</span>
                </flux:button>

                <flux:button wire:click="dapatkanProduk" icon="clipboard-document-list" variant="secondary"
                    class="w-full sm:w-auto">
                    <span class="font-['Montserrat'] text-sm font-semibold sm:text-base">Dapatkan Produk</span>
                </flux:button>
            </div>
            @endif
        </div>
    </div>

    @if ($showNoteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-2xl rounded-[15px] bg-[#fafafa] p-5 shadow-xl sm:p-7">
            <h3 class="mb-4 font-['Montserrat'] text-xl font-semibold text-[#666666]">Catatan Produksi</h3>
            <textarea wire:model="noteInput" rows="6"
                class="w-full resize-y rounded-xl border border-[#d4d4d4] px-4 py-3 font-['Montserrat'] text-base text-[#666666] focus:border-[#3f4e4f] focus:outline-none"
                placeholder="Tulis catatan..."></textarea>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button wire:click="$set('showNoteModal', false)"
                    class="w-full rounded-[15px] bg-[#c4c4c4] px-5 py-2.5 font-['Montserrat'] text-sm font-semibold text-white sm:w-auto sm:text-base">
                    Batal
                </button>
                <button wire:click="simpanCatatan"
                    class="w-full rounded-[15px] bg-[#3f4e4f] px-5 py-2.5 font-['Montserrat'] text-sm font-semibold text-white sm:w-auto sm:text-base">
                    Simpan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('redirect', (event) => {
            window.location.href = event.url;
        });
    });
</script>