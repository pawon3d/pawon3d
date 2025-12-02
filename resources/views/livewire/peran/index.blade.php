<div>
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold text-[#666666]">Daftar Peran</h1>
        <div class="flex gap-2.5">
            <button type="button" wire:click="cetakInformasi"
                class="bg-[#525252] hover:bg-[#666666] border border-[#666666] px-6 py-2.5 rounded-[15px] font-medium text-sm text-white transition-colors cursor-pointer">
                Cetak Informasi
            </button>
            <button type="button" wire:click="riwayatPembaruan"
                class="bg-[#525252] hover:bg-[#666666] border border-[#666666] px-6 py-2.5 rounded-[15px] font-medium text-sm text-white transition-colors cursor-pointer">
                Riwayat Pembaruan
            </button>
        </div>
    </div>

    <!-- Info Box -->
    <x-alert.info>
        Peran. Peran adalah bentuk perwakilan dari sekumpulan hak dalam menggunakan atau menjalankan sistem. Setiap
        peran dapat memiliki banyak akses dan pekerja.
    </x-alert.info>

    <!-- Main Content Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-8 py-6 mt-5">
        <!-- Search and Add Button -->
        <div class="flex justify-between items-center mb-5">
            <div class="flex items-center gap-4 flex-1">
                <div
                    class="flex items-center bg-white border border-[#666666] rounded-full px-4 py-0 w-full max-w-[545px]">
                    <svg class="w-[30px] h-[30px] text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" placeholder="Cari Peran"
                        class="flex-1 px-2.5 py-2.5 focus:outline-none text-[#959595] text-base font-medium border-none" />
                </div>
                <button class="flex items-center gap-2 text-[#666666] font-medium text-base">
                    <svg class="w-[25px] h-[25px]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                            clip-rule="evenodd" />
                    </svg>
                    Filter
                </button>
            </div>
            <flux:button variant="primary" icon="plus" href="{{ route('role.tambah') }}" wire:navigate>
                Tambah Peran
            </flux:button>
        </div>

        <!-- Table -->
        <x-table.paginated :headers="[
            ['label' => 'Peran', 'sortable' => true, 'sort-by' => 'name'],
            ['label' => 'Akses', 'sortable' => false],
            ['label' => 'Jumlah Pekerja', 'sortable' => true, 'sort-by' => 'users_count', 'align' => 'right'],
        ]" :paginator="$roles" headerBg="#3f4e4f" headerText="#f8f4e1" bodyBg="#fafafa"
            bodyText="#666666" emptyMessage="Tidak ada data peran.">
            @foreach ($roles as $role)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                        <a href="{{ route('role.edit', $role->id) }}" class="hover:underline" wire:navigate>
                            {{ $role->name }}
                        </a>
                    </td>
                    <td class="px-6 py-5 text-[#666666] font-medium text-sm">
                        {{ $this->getReadableAccessLabels($role) }}
                    </td>
                    <td class="px-6 py-5 text-[#666666] font-medium text-sm text-right">
                        {{ $role->users_count }}
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan Peran</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @foreach ($activityLogs as $log)
                    <div class="border-b border-gray-200 py-3">
                        <div class="flex justify-between items-start">
                            <div class="text-sm font-medium text-[#666666]">{{ $log->description }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Oleh: {{ $log->causer->name ?? 'System' }}
                        </div>

                        {{-- Tampilkan detail perubahan untuk event 'updated' --}}
                        @if ($log->event === 'updated' && $log->properties)
                            @php
                                $old = $log->properties['old'] ?? [];
                                $attributes = $log->properties['attributes'] ?? [];
                                $fieldLabels = [
                                    'name' => 'Nama Peran',
                                    'guard_name' => 'Guard',
                                ];
                            @endphp
                            <div class="mt-2 text-xs space-y-1 bg-gray-50 rounded p-2">
                                @foreach ($attributes as $field => $newValue)
                                    @if (isset($fieldLabels[$field]))
                                        <div class="flex flex-wrap gap-1">
                                            <span class="font-medium text-[#666666]">{{ $fieldLabels[$field] }}:</span>
                                            <span class="text-red-500 line-through">{{ $old[$field] ?? '-' }}</span>
                                            <span class="text-gray-400">→</span>
                                            <span class="text-green-600">{{ $newValue ?? '-' }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </flux:modal>
</div>
