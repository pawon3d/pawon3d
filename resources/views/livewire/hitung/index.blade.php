<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-[#666666]">Hitung dan catat Persediaan</h1>
        <flux:button variant="secondary" wire:click="riwayatPembaruan">
            Riwayat Pembaruan
        </flux:button>
    </div>

    <!-- Info Box -->
    <x-alert.info>
        Hitung dan Catat Persediaan. Penting untuk melakukan pengecekan terhadap jumlah dan kondisi barang secara
        berkala, sehingga catatan fisik dan sistem selalu akurat dan dapat dipertanggungjawabkan. Hitung untuk
        menghitung jumlah persediaan dan catat untuk mencatat persediaan yang rusak atau hilang.
    </x-alert.info>

    <!-- Table Card -->
    <div class="bg-[#fafafa] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] rounded-[15px] p-6">
        <!-- Search & Actions Row -->
        <div class="flex justify-between items-center mb-5">
            <!-- Search & Filter -->
            <div class="flex items-center gap-4 flex-1">
                <div class="flex-1 max-w-md bg-white border border-[#666666] rounded-[20px] flex items-center px-4 py-0">
                    <flux:icon.magnifying-glass class="size-[20px] text-[#666666]" />
                    <input wire:model.live="search" placeholder="Cari Rencana Hitung atau Catat"
                        class="w-full px-3 py-2.5 text-base text-[#959595] bg-transparent border-none focus:outline-none focus:ring-0 placeholder:text-[#959595]" />
                </div>
                <button type="button" class="flex items-center gap-1 text-[#666666]">
                    <flux:icon.funnel class="size-[20px]" />
                    <span class="text-base font-medium">Filter</span>
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-4">
                <flux:button variant="primary" href="{{ route('hitung.rencana') }}" wire:navigate>
                    <flux:icon.list-bullet class="size-5 mr-2" />
                    Rencana Aksi
                </flux:button>
                <flux:button variant="primary" href="{{ route('hitung.riwayat') }}" wire:navigate>
                    <flux:icon.clock class="size-5" />
                </flux:button>
            </div>
        </div>

        <!-- Table -->
        <x-table.paginated :headers="[
            ['label' => 'ID Aksi', 'sortable' => true, 'sort-by' => 'hitung_number'],
            ['label' => 'Tanggal Aksi', 'sortable' => true, 'sort-by' => 'hitung_date'],
            ['label' => 'Aksi', 'sortable' => true, 'sort-by' => 'action'],
            ['label' => 'Persediaan'],
            ['label' => 'Inventaris', 'sortable' => true, 'sort-by' => 'user_id'],
            ['label' => 'Status'],
        ]" :paginator="$hitungs" headerBg="#3F4E4F" headerText="#F8F4E1"
            emptyMessage="Belum Ada Aksi. Tekan tombol 'Rencana Aksi' untuk menambahkan aksi.">
            @foreach ($hitungs as $hitung)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-[#666666] border-b border-[#d4d4d4]">
                        <a href="{{ route('hitung.rincian', $hitung->id) }}" class="hover:underline" wire:navigate>
                            {{ $hitung->hitung_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-[#666666] border-b border-[#d4d4d4]">
                        {{ $hitung->hitung_date ? \Carbon\Carbon::parse($hitung->hitung_date)->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-[#666666] border-b border-[#d4d4d4]">
                        {{ $hitung->action ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-[#666666] border-b border-[#d4d4d4]">
                        @if ($hitung->details && $hitung->details->count() > 0)
                            {{ $hitung->details->pluck('material.name')->filter()->implode(', ') ?: '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-[#666666] border-b border-[#d4d4d4]">
                        {{ $hitung->user->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 border-b border-[#d4d4d4]">
                        @php
                            $statusColors = [
                                'Sedang Diproses' => 'bg-[#FFC400] text-white',
                                'Selesai' => 'bg-green-500 text-white',
                                'Dibatalkan' => 'bg-red-500 text-white',
                            ];
                            $statusClass = $statusColors[$hitung->status] ?? 'bg-gray-400 text-white';
                        @endphp
                        <span
                            class="inline-flex items-center justify-center px-4 py-1.5 text-xs font-bold rounded-[15px] min-w-[90px] {{ $statusClass }}">
                            {{ $hitung->status ?? '-' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Hitung dan Catat Persediaan</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse ($activityLogs as $log)
                    <div class="border-b py-2">
                        <div class="text-sm font-medium">{{ $log->description }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $log->causer->name ?? 'System' }} -
                            {{ $log->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Tidak ada riwayat pembaruan.</p>
                @endforelse
            </div>
        </div>
    </flux:modal>
</div>
