<div>
    <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-4 items-center">
            <a href="@if ($status == 'Draft') {{ route('belanja.rencana') }}
                 @else
                 {{ route('belanja') }} @endif"
                class="bg-[#313131] hover:bg-[#252324] text-white px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
                <flux:icon.arrow-left variant="mini" class="size-4" wire:navigate />
                <span class="font-montserrat font-semibold text-[16px]">Kembali</span>
            </a>
            <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Rincian Belanja Persediaan</h1>
        </div>
        <div class="flex gap-2.5 items-center">
            <flux:button type="button" wire:click="cetakInformasi" variant="filled">
                Cetak Informasi
            </flux:button>
            <flux:button type="button" wire:click="riwayatPembaruan" variant="filled">
                Riwayat Pembaruan
            </flux:button>
        </div>
    </div>

    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-8 py-6 flex flex-col gap-8 mt-6">
        <!-- Expense Number and Status -->
        <div class="flex items-center justify-between">
            <h1 class="font-montserrat font-medium text-[30px] text-[#666666]">{{ $expense->expense_number }}</h1>
            @if ($status == 'Draft')
                <div class="bg-[#adadad] px-5 py-2 rounded-[30px]">
                    <span class="font-montserrat font-bold text-[16px] text-[#fafafa]">Belum Diproses</span>
                </div>
            @elseif($status == 'Dimulai')
                <div class="bg-[#ffc400] px-5 py-2 rounded-[30px]">
                    <span class="font-montserrat font-bold text-[16px] text-[#fafafa]">Sedang Diproses</span>
                </div>
            @elseif($status == 'Selesai')
                <div class="bg-[#56c568] px-5 py-2 rounded-[30px]">
                    <span class="font-montserrat font-bold text-[16px] text-[#fafafa]">Selesai</span>
                </div>
            @endif
        </div>

        <!-- Info Section -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-9">
                <div class="flex flex-col gap-1">
                    <p class="font-montserrat font-medium text-[16px] text-[#666666]">Tanggal Belanja</p>
                    <p class="font-montserrat font-normal text-[16px] text-[#666666]">
                        {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '-' }}
                    </p>
                </div>
                <div class="flex flex-col gap-1">
                    <p class="font-montserrat font-medium text-[16px] text-[#666666]">Tanggal Selesai</p>
                    <p class="font-montserrat font-normal text-[16px] text-[#666666]">
                        {{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-9">
                <div class="flex flex-col gap-1 items-end">
                    <p class="font-montserrat font-medium text-[16px] text-[#666666]">Toko Persediaan</p>
                    <p class="font-montserrat font-normal text-[16px] text-[#666666]">{{ $expense->supplier->name }}</p>
                </div>
                <div class="flex flex-col gap-1 items-end">
                    <p class="font-montserrat font-medium text-[16px] text-[#666666]">Kontak Toko</p>
                    <p class="font-montserrat font-normal text-[16px] text-[#666666]">
                        {{ $expense->supplier->phone ?? '-' }}</p>
                </div>
                <div class="flex flex-col gap-1 items-end">
                    <p class="font-montserrat font-medium text-[16px] text-[#666666]">Inventaris</p>
                    <p class="font-montserrat font-normal text-[16px] text-[#666666]">{{ $logName }}</p>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="flex flex-col gap-1">
            <div class="w-full h-[30px] bg-[#d4d4d4] rounded-[5px] overflow-hidden">
                <div class="h-full bg-[#56c568] transition-all duration-300"
                    style="width: {{ number_format($percentage, 0) }}%">
                </div>
            </div>
            <div class="flex justify-center">
                <span class="font-montserrat font-medium text-[14px] text-[#525252]">
                    {{ number_format($percentage, 0) }}% ({{ $completed_count ?? 0 }} dari {{ $total_count ?? 0 }})
                </span>
            </div>
        </div>

        <!-- Rencana Belanja Section -->
        <div class="flex flex-col gap-5">
            <div class="flex items-center justify-between">
                <p class="font-montserrat font-medium text-[18px] text-[#666666]">Rencana Belanja</p>
                @if (!$is_start)
                    <flux:button type="button" wire:click="editRencanaBelanja" variant="filled" icon="pencil">
                        Buat Catatan
                    </flux:button>
                @endif
            </div>
            <div class="bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] px-5 py-2.5 min-h-[120px]">
                <p class="font-montserrat font-normal text-[16px] text-[#666666] text-justify">
                    {{ $expense->note ?? 'Tidak ada catatan' }}
                </p>
            </div>
        </div>
    </div>


    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-8 py-6 flex flex-col gap-5 mt-12">
        <p class="font-montserrat font-medium text-[18px] text-[#666666]">Daftar Belanja Persediaan</p>
        <x-table.form :headers="[
            ['label' => 'Barang Persediaan', 'class' => 'text-left px-6 py-5'],
            ['label' => 'Rencana Belanja', 'class' => 'text-right px-6 py-5'],
            ['label' => 'Jumlah Didapatkan', 'class' => 'text-right px-6 py-5'],
            ['label' => 'Satuan Ukur Belanja', 'class' => 'text-left px-6 py-5'],
            ['label' => 'Harga Satuan', 'class' => 'text-right px-6 py-5'],
            ['label' => 'Total Harga', 'class' => 'text-right px-6 py-5'],
            ['label' => 'Total Harga (Sebenarnya)', 'class' => 'text-right px-6 py-5'],
        ]" header-bg="bg-[#3f4e4f]" header-text="text-[#f8f4e1]" body-bg="bg-[#fafafa]"
            body-text="text-[#666666]" footer-bg="bg-[#eaeaea]" footer-text="text-[#666666]"
            empty-message="Belum ada data belanja.">
            <x-slot:rows>
                @foreach ($expenseDetails as $detail)
                    <tr class="border-b border-[#d4d4d4] h-[60px]">
                        <td class="px-6 py-0">
                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                {{ $detail->material->name ?? 'Barang Tidak Ditemukan' }}
                            </span>
                        </td>
                        <td class="px-6 py-0 text-right">
                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                {{ $detail->quantity_expect }}
                            </span>
                        </td>
                        <td class="px-6 py-0 text-right">
                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                {{ $detail->quantity_get }}
                            </span>
                        </td>
                        <td class="px-6 py-0">
                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                {{ $detail->unit->name ?? '' }} ({{ $detail->unit->alias ?? '' }})
                            </span>
                        </td>
                        <td class="px-6 py-0 text-right">
                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                Rp{{ number_format($detail->price_expect, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-0 text-right">
                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                Rp{{ number_format($detail->total_expect, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-0 text-right">
                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                Rp{{ number_format($detail->total_actual, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </x-slot:rows>

            <x-slot:footer>
                <tr class="h-[60px]">
                    <td class="px-6 py-0 rounded-bl-[15px]" colspan="5">
                        <span class="font-montserrat font-bold text-[14px] text-[#666666]">Total</span>
                    </td>
                    <td class="px-6 py-0 text-right">
                        <span class="font-montserrat font-bold text-[14px] text-[#666666]">
                            Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-6 py-0 text-right rounded-br-[15px]">
                        <span class="font-montserrat font-bold text-[14px] text-[#666666]">
                            Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
            </x-slot:footer>
        </x-table.form>
    </div>

    @if ($is_start && !$is_finish)
        <div class="flex justify-end mt-16 gap-2.5">
            <flux:button icon="check-circle" type="button" variant="primary" wire:click="finish">
                Selesaikan Belanja
            </flux:button>
            @if ($status != 'Lengkap')
                <flux:button icon="shopping-cart" type="button" variant="primary"
                    href="{{ route('belanja.dapatkan-belanja', $expense->id) }}">
                    Dapatkan Belanja
                </flux:button>
            @endif
        </div>
    @elseif (!$is_start && !$is_finish)
        <div class="flex justify-between mt-16">
            <button type="button" wire:click="confirmDelete"
                class="bg-[#eb5757] hover:bg-[#d84545] text-[#f8f4e1] px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
                <flux:icon.trash variant="mini" class="size-5" />
                <span class="font-montserrat font-semibold text-[16px]">Hapus Belanja</span>
            </button>
            <div class="flex gap-2.5">
                <button type="button" onclick="window.location.href='{{ route('belanja.edit', $expense->id) }}'"
                    class="bg-[#feba17] hover:bg-[#e5a615] text-[#f8f4e1] px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
                    <flux:icon.pencil variant="mini" class="size-5" />
                    <span class="font-montserrat font-bold text-[16px]">Ubah Daftar Belanja</span>
                </button>
                <button type="button" wire:click="start"
                    class="bg-[#3f4e4f] hover:bg-[#2f3e3f] text-white px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
                    <flux:icon.shopping-cart variant="mini" class="size-5" />
                    <span class="font-montserrat font-semibold text-[16px]">Mulai Belanja</span>
                </button>
            </div>
        </div>
    @endif



    <!-- Modal Catatan Rencana Belanja -->
    <flux:modal name="catatan-belanja" class="w-full max-w-xl" wire:model="showNoteModal">
        <div class="space-y-4">
            <div>
                <h1 size="lg" class="font-montserrat font-semibold text-[18px] text-[#666666]">
                    Catatan Rencana Belanja
                </h1>
                <p class="font-montserrat text-[14px] text-[#666666] mt-1">Tambahkan atau perbarui catatan untuk
                    membantu tim saat proses belanja berlangsung.</p>
            </div>
            <flux:textarea wire:model.defer="noteInput" rows="6" placeholder="Masukkan catatan"
                class="!bg-[#fafafa] !border-[#d4d4d4] !rounded-[15px] !px-5 !py-4 !font-montserrat !text-[16px] !text-[#666666]" />
            @error('noteInput')
                <p class="text-sm text-red-500 font-montserrat">{{ $message }}</p>
            @enderror
            <div class="flex justify-end gap-2.5">
                <flux:button type="button" wire:click="$set('showNoteModal', false)" variant="subtle">
                    Batal
                </flux:button>
                <flux:button icon="save" type="button" variant="primary" wire:click="saveNote">
                    Simpan Catatan
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <h1 size="lg">Riwayat Pembaruan Daftar Belanja</h1>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach ($activityLogs as $log)
                    <div class="border-b py-2">
                        <div class="text-sm font-medium">{{ $log->description }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $log->causer->name ?? 'System' }} -
                            {{ $log->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </flux:modal>
</div>
