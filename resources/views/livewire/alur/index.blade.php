<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-gray-600">Alur Persediaan</h1>
    </div>

    <x-alert.info>
        Alur Persediaan. Lihat riwayat persediaan seperti penggunaan dalam produksi, belanja persediaan, hitung dan
        catat persediaan, dan juga lain sebagainya.
    </x-alert.info>

    <div class="mt-4 bg-[#fafafa] shadow-md rounded-[15px] p-6 overflow-hidden">
        {{-- Search and Filter --}}
        <div class="flex items-center gap-4 mb-6">
            <div
                class="flex-1 bg-white border border-gray-500 rounded-full flex items-center px-4 py-0 focus-within:ring-2 focus-within:ring-blue-500">
                <flux:icon icon="magnifying-glass" class="size-5 text-gray-500" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Riwayat Persediaan"
                    class="flex-1 px-3 py-2 border-0 focus:outline-none focus:ring-0 bg-transparent text-gray-600 placeholder-gray-400" />
            </div>
            <div class="flex items-center gap-1">
                <flux:icon icon="funnel" class="size-5 text-gray-500" />
                <select wire:model.live="filterAction"
                    class="border-0 bg-transparent text-gray-600 font-medium focus:outline-none focus:ring-0 cursor-pointer">
                    @foreach ($actionOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table --}}
        <x-table.paginated :paginator="$inventoryLogs" :headers="[
            ['label' => 'Tanggal'],
            ['label' => 'Barang'],
            ['label' => 'Batch'],
            ['label' => 'Aksi'],
            ['label' => 'Pekerja'],
            ['label' => 'Perubahan', 'align' => 'right'],
            ['label' => 'Persediaan Akhir', 'align' => 'right'],
        ]" headerBg="#3F4E4F" headerText="#F8F4E1"
            emptyMessage="Tidak ada riwayat persediaan yang tersedia.">
            @foreach ($inventoryLogs as $log)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $log->created_at->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $log->material?->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $log->materialBatch?->batch_number ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $log->action_label }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $log->user?->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 text-right">
                        <span
                            class="{{ $log->quantity_change > 0 ? 'text-green-600' : ($log->quantity_change < 0 ? 'text-red-600' : '') }}">
                            {{ $log->formatted_change }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 text-right">
                        {{ $log->formatted_after }}
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>
</div>
