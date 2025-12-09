<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-4 items-center">
            <a href="{{ route('hitung') }}"
                class="px-6 py-2.5 bg-[#313131] rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold"
                wire:navigate>
                <flux:icon.arrow-left class="size-5" />
                Kembali
            </a>
            <h1 class="text-xl font-semibold text-[#666666]">Rincian {{ $this->hitung->action }}</h1>
        </div>
        <div class="flex gap-2.5 items-center">
            <flux:button variant="secondary" wire:click="riwayatPembaruan">
                Riwayat Pembaruan
            </flux:button>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-6 mb-8">
        <!-- Nomor & Status -->
        <div class="flex justify-between items-start mb-8">
            <h2 class="text-3xl font-medium text-[#666666]">{{ $this->hitung->hitung_number }}</h2>
            @php
                $statusClass = match ($status) {
                    'Selesai' => 'bg-green-500',
                    'Sedang Diproses' => 'bg-yellow-500',
                    'Dibatalkan' => 'bg-red-500',
                    default => 'bg-[#adadad]',
                };
            @endphp
            <span class="px-5 py-2 {{ $statusClass }} rounded-full text-[#fafafa] font-bold text-base">
                {{ $status }}
            </span>
        </div>

        <!-- Info Details -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex gap-9 items-center">
                <div class="flex flex-col gap-1">
                    <span class="text-base font-medium text-[#666666]">Tanggal Aksi</span>
                    <span class="text-base text-[#666666]">
                        {{ $this->hitung->hitung_date ? \Carbon\Carbon::parse($this->hitung->hitung_date)->format('d M Y') : '-' }}
                    </span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-base font-medium text-[#666666]">Tanggal Selesai</span>
                    <span class="text-base text-[#666666]">
                        {{ $finish_date ? \Carbon\Carbon::parse($finish_date)->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>
            <div class="flex gap-9 items-center">
                <div class="flex flex-col gap-1 items-end">
                    <span class="text-base font-medium text-[#666666]">Jenis Aksi</span>
                    <span class="text-base text-[#666666]">{{ $this->hitung->action }}</span>
                </div>
                <div class="flex flex-col gap-1 items-end">
                    <span class="text-base font-medium text-[#666666]">Inventaris</span>
                    <span class="text-base text-[#666666]">{{ $logName }}</span>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        <div class="flex flex-col gap-5">
            <div class="flex justify-between items-center">
                <span class="text-base font-medium text-[#666666]">
                    @if ($this->hitung->action == 'Hitung Persediaan')
                        Rencana Hitung
                    @elseif ($this->hitung->action == 'Catat Persediaan Rusak')
                        Rencana Catat Rusak
                    @else
                        Rencana Catat Hilang
                    @endif
                </span>
                <button type="button" wire:click="openNoteModal"
                    class="px-6 py-2.5 bg-[#666666] rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold text-base">
                    <flux:icon.pencil class="size-3" />
                    {{ $this->hitung->note ? 'Ubah Catatan' : 'Buat Catatan' }}
                </button>
            </div>
            <div class="bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px] px-5 py-2.5 min-h-[120px]">
                <p class="text-base text-[#666666]">{{ $this->hitung->note ?: 'Tidak ada catatan' }}</p>
            </div>
        </div>
    </div>


    <!-- Daftar Persediaan Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-6 mb-12">
        <p class="text-base font-medium text-[#666666] mb-5">Daftar Persediaan</p>

        <div class="w-full overflow-x-auto">
            <table class="w-full border-collapse">
                <!-- Table Header -->
                <thead>
                    <tr>
                        <th class="bg-[#3F4E4F] px-6 py-5 text-left rounded-tl-[15px]">
                            <span
                                class="text-sm font-bold text-[#F8F4E1] leading-tight block">Barang<br>Persediaan</span>
                        </th>
                        <th class="bg-[#3F4E4F] px-6 py-5 text-left">
                            <span class="text-sm font-bold text-[#F8F4E1]">Batch</span>
                        </th>
                        <th class="bg-[#3F4E4F] px-6 py-5 text-right">
                            <span
                                class="text-sm font-bold text-[#F8F4E1] leading-tight block">Jumlah<br>Diharapkan</span>
                        </th>
                        @if ($this->hitung->action == 'Hitung Persediaan')
                            <th class="bg-[#3F4E4F] px-6 py-5 text-right">
                                <span
                                    class="text-sm font-bold text-[#F8F4E1] leading-tight block">Selisih<br>Hitung</span>
                            </th>
                            <th class="bg-[#3F4E4F] px-6 py-5 text-right">
                                <span
                                    class="text-sm font-bold text-[#F8F4E1] leading-tight block">Jumlah<br>Terhitung</span>
                            </th>
                        @else
                            <th class="bg-[#3F4E4F] px-6 py-5 text-right">
                                <span
                                    class="text-sm font-bold text-[#F8F4E1] leading-tight block">Jumlah<br>Terbaru</span>
                            </th>
                            <th class="bg-[#3F4E4F] px-6 py-5 text-right">
                                <span
                                    class="text-sm font-bold text-[#F8F4E1] leading-tight block">Jumlah<br>{{ $this->hitung->action == 'Catat Persediaan Rusak' ? 'Rusak' : 'Hilang' }}</span>
                            </th>
                        @endif
                        <th class="bg-[#3F4E4F] px-6 py-5 text-right">
                            <span class="text-sm font-bold text-[#F8F4E1]">Modal</span>
                        </th>
                        <th class="bg-[#3F4E4F] px-6 py-5 text-right rounded-tr-[15px]">
                            <span
                                class="text-sm font-bold text-[#F8F4E1]">{{ $this->hitung->action == 'Hitung Persediaan' ? 'Selisih Modal' : 'Kerugian' }}</span>
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody>
                    @foreach ($this->hitungDetails as $detail)
                        <tr class="border-b border-[#d4d4d4]">
                            <td class="bg-[#fafafa] px-6 py-4">
                                <span
                                    class="text-sm font-medium text-[#666666]">{{ $detail->material->name ?? 'Barang Tidak Ditemukan' }}</span>
                            </td>
                            <td class="bg-[#fafafa] px-6 py-4">
                                <span
                                    class="text-sm font-medium text-[#666666]">{{ $detail->materialBatch->batch_number ?? '-' }}</span>
                            </td>
                            <td class="bg-[#fafafa] px-6 py-4 text-right">
                                <span class="text-sm font-medium text-[#666666]">{{ $detail->quantity_expect }}
                                    {{ $detail->materialBatch->unit->alias ?? '' }}</span>
                            </td>
                            @if ($this->hitung->action == 'Hitung Persediaan')
                                @php
                                    $selisih = ($detail->quantity_actual ?? 0) - $detail->quantity_expect;
                                @endphp
                                <td class="bg-[#fafafa] px-6 py-4 text-right">
                                    <span
                                        class="text-sm font-medium text-[#666666]">{{ $selisih > 0 ? '+' : '' }}{{ $selisih }}
                                        {{ $detail->materialBatch->unit->alias ?? '' }}</span>
                                </td>
                                <td class="bg-[#fafafa] px-6 py-4 text-right">
                                    <span
                                        class="text-sm font-medium text-[#666666]">{{ $detail->quantity_actual ?? 0 }}
                                        {{ $detail->materialBatch->unit->alias ?? '' }}</span>
                                </td>
                            @else
                                @php
                                    $jumlahTerbaru = $detail->quantity_expect - ($detail->quantity_actual ?? 0);
                                @endphp
                                <td class="bg-[#fafafa] px-6 py-4 text-right">
                                    <span class="text-sm font-medium text-[#666666]">{{ $jumlahTerbaru }}
                                        {{ $detail->materialBatch->unit->alias ?? '' }}</span>
                                </td>
                                <td class="bg-[#fafafa] px-6 py-4 text-right">
                                    <span
                                        class="text-sm font-medium text-[#666666]">{{ $detail->quantity_actual ?? 0 }}
                                        {{ $detail->materialBatch->unit->alias ?? '' }}</span>
                                </td>
                            @endif
                            <td class="bg-[#fafafa] px-6 py-4 text-right">
                                <span
                                    class="text-sm font-medium text-[#666666]">Rp{{ number_format($detail->total, 0, ',', '.') }}</span>
                            </td>
                            <td class="bg-[#fafafa] px-6 py-4 text-right">
                                @if ($this->hitung->action == 'Hitung Persediaan')
                                    @php
                                        $selisihModal = $detail->loss_total;
                                    @endphp
                                    <span
                                        class="text-sm font-medium text-[#666666]">{{ $selisihModal < 0 ? '-' : '' }}Rp{{ number_format(abs($selisihModal), 0, ',', '.') }}</span>
                                @else
                                    <span
                                        class="text-sm font-medium text-[#666666]">Rp{{ number_format($detail->loss_total, 0, ',', '.') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <!-- Table Footer -->
                <tfoot>
                    <tr>
                        <td colspan="5" class="bg-[#eaeaea] px-6 py-4 rounded-bl-[15px]">
                            <span class="text-sm font-bold text-[#666666]">Total</span>
                        </td>
                        <td class="bg-[#eaeaea] px-6 py-4 text-right">
                            <span
                                class="text-sm font-bold text-[#666666]">Rp{{ number_format($this->hitung->grand_total, 0, ',', '.') }}</span>
                        </td>
                        <td class="bg-[#eaeaea] px-6 py-4 text-right rounded-br-[15px]">
                            @if ($this->hitung->action == 'Hitung Persediaan')
                                @php
                                    $totalSelisih = $this->hitung->loss_grand_total;
                                @endphp
                                <span
                                    class="text-sm font-bold text-[#666666]">{{ $totalSelisih < 0 ? '-' : '' }}Rp{{ number_format(abs($totalSelisih), 0, ',', '.') }}</span>
                            @else
                                <span
                                    class="text-sm font-bold text-[#666666]">Rp{{ number_format($this->hitung->loss_grand_total, 0, ',', '.') }}</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    @if ($is_start && !$is_finish)
        <div class="flex justify-between items-center">
            <div class="flex gap-2.5 items-center">
                <flux:button variant="danger" icon="ban" type="button" wire:click="cancelAction">
                    Batalkan Aksi
                </flux:button>
            </div>
            <div class="flex gap-2.5 items-center">
                <flux:button icon="check-circle" type="button" wire:click="finish"
                    class="!px-6 !py-2.5 !rounded-[15px] !shadow-sm !font-semibold">
                    Selesaikan
                    @if ($this->hitung->action == 'Hitung Persediaan')
                        Hitung
                    @else
                        Catat
                    @endif
                </flux:button>
                @if ($status != 'Selesai')
                    <flux:button icon="pencil-square" type="button" variant="primary"
                        href="{{ route('hitung.mulai', $this->hitung->id) }}"
                        class="!px-6 !py-2.5 !bg-[#3F4E4F] !rounded-[15px] !shadow-sm !font-semibold" wire:navigate>
                        {{ $this->hitung->action }}
                    </flux:button>
                @endif
            </div>
        </div>
    @elseif (!$is_start && !$is_finish)
        <div class="flex justify-between items-center">
            <button wire:click="confirmDelete" type="button"
                class="px-6 py-2.5 bg-[#eb5757] rounded-[15px] shadow-sm flex items-center gap-2 text-[#F8F4E1] font-semibold">
                <flux:icon.trash class="size-5" />
                Hapus Aksi
            </button>
            <div class="flex gap-2.5 items-center">
                <a href="{{ route('hitung.edit', $hitung_id) }}"
                    class="px-6 py-2.5 bg-[#feba17] rounded-[15px] shadow-sm flex items-center gap-2 text-[#F8F4E1] font-bold">
                    <flux:icon.pencil class="size-5" />
                    Ubah Daftar Persediaan
                </a>
                <button wire:click="start" type="button"
                    class="px-6 py-2.5 bg-[#3F4E4F] rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold">
                    <flux:icon.archive-box class="size-5" />
                    Mulai {{ $this->hitung->action }}
                </button>
            </div>
        </div>
    @endif



    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6 p-4">
            <div>
                <h2 class="text-xl font-semibold text-[#666666]">Riwayat Pembaruan {{ $this->hitung->hitung_number }}
                </h2>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse ($activityLogs as $log)
                    <div class="border-b border-[#d4d4d4] py-3">
                        <div class="text-sm font-medium text-[#666666]">{{ $log['description'] }}</div>
                        <div class="text-xs text-[#adadad]">
                            {{ $log['causer_name'] }} - {{ $log['created_at'] }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#666666]">Belum ada riwayat pembaruan.</p>
                @endforelse
            </div>
        </div>
    </flux:modal>

    <!-- Modal Buat/Ubah Catatan -->
    <flux:modal name="catatan-modal" class="w-full max-w-xl" wire:model="showNoteModal">
        <div class="space-y-6 p-4">
            <div>
                <h2 class="text-xl font-semibold text-[#666666]">
                    @if ($this->hitung->action == 'Hitung Persediaan')
                        Rencana Hitung
                    @elseif ($this->hitung->action == 'Catat Persediaan Rusak')
                        Rencana Catat Rusak
                    @else
                        Rencana Catat Hilang
                    @endif
                </h2>
            </div>
            <div>
                <flux:textarea wire:model="editNote" rows="6"
                    placeholder="Tuliskan catatan atau rencana aksi di sini..."
                    class="w-full bg-[#eaeaea] border border-[#d4d4d4] rounded-[15px]" />
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="$set('showNoteModal', false)"
                    class="px-6 py-2.5 bg-[#adadad] rounded-[15px] text-white font-semibold">
                    Batal
                </button>
                <button type="button" wire:click="saveNote"
                    class="px-6 py-2.5 bg-[#3F4E4F] rounded-[15px] text-[#F8F4E1] font-semibold">
                    Simpan Catatan
                </button>
            </div>
        </div>
    </flux:modal>
</div>
