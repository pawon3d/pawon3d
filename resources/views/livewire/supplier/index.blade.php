<div>
    <div class="mb-6 flex justify-between items-center">
        <div class="flex gap-4 items-center">
            <a href="{{ route('belanja') }}"
                class="bg-[#313131] hover:bg-[#252324] text-white px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors">
                <flux:icon.arrow-left variant="mini" class="size-4" />
                <span class="font-montserrat font-semibold text-[16px]">Kembali</span>
            </a>
            <h1 class="font-montserrat font-semibold text-[20px] text-[#666666]">Daftar Toko Persediaan</h1>
        </div>
        <div class="flex gap-2.5 items-center">
            <flux:button type="button" wire:click="riwayatPembaruan" variant="filled">
                Riwayat Pembaruan
            </flux:button>
        </div>
    </div>
    <x-alert.info>
        Toko Persediaan digunakan untuk menetapkan asal barang yang dibeli beserta harga yang dikeluarkan. Toko
        persediaan dapat didatangi langsung untuk belanja atau dapat dilakukan lewat telepon atau whatsapp kepada
        toko.
    </x-alert.info>
    <div
        class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] px-8 py-6 flex flex-col gap-5 mt-6">
        <div class="flex justify-between items-center">
            <div class="flex gap-4 items-center w-[545px]">
                <div
                    class="flex-1 bg-white border border-[#666666] rounded-[20px] px-4 py-0 h-[40px] flex items-center">
                    <flux:icon.magnifying-glass class="size-[30px] text-[#666666] shrink-0" />
                    <input wire:model.live="search" placeholder="Cari Toko Persediaan"
                        class="flex-1 px-2.5 py-2.5 font-montserrat font-medium text-[16px] text-[#959595] border-0 focus:outline-none focus:ring-0 bg-transparent" />
                </div>
                <div class="flex items-center gap-1 cursor-pointer">
                    <flux:icon.funnel class="size-[25px] text-[#666666]" />
                    <span class="font-montserrat font-medium text-[16px] text-[#666666] px-1 py-2.5">Filter</span>
                </div>
            </div>
            <flux:button type="button" variant="primary" icon="plus" href="{{ route('supplier.tambah') }}">
                Tambah Toko
            </flux:button>
        </div>

        <x-table.paginated :headers="[
            [
                'label' => 'Nama Toko Persediaan',
                'sortable' => true,
                'sort-by' => 'name',
                'sort-method' => 'sortByColumn',
            ],
            [
                'label' => 'Nama Kontak',
                'sortable' => true,
                'sort-by' => 'contact_name',
                'sort-method' => 'sortByColumn',
            ],
            ['label' => 'No. Telepon', 'sortable' => false],
        ]" :paginator="$suppliers" :sortBy="$sortBy" :sortDirection="$sortDirection" headerBg="#3f4e4f"
            headerText="#f8f4e1" bodyBg="#fafafa" bodyText="#666666" emptyMessage="Tidak ada data."
            wrapperClass="w-full rounded-[15px] overflow-hidden border-0 shadow-none">
            @foreach ($suppliers as $supplier)
                <tr class="border-b border-[#d4d4d4] h-[60px] hover:bg-[#f0f0f0] transition-colors">
                    <td class="px-6 py-0">
                        <a href="{{ route('supplier.edit', $supplier->id) }}" class="block">
                            <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                                {{ $supplier->name }}
                            </span>
                        </a>
                    </td>
                    <td class="px-6 py-0">
                        <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                            {{ $supplier->contact_name }}
                        </span>
                    </td>
                    <td class="px-6 py-0">
                        <span class="font-montserrat font-medium text-[14px] text-[#666666]">
                            {{ $supplier->phone }}
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
                <flux:heading size="lg">Riwayat Pembaruan Toko Persediaan</flux:heading>
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
