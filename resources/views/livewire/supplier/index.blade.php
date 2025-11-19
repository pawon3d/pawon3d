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
            <button type="button" wire:click="riwayatPembaruan"
                class="bg-[#525252] border border-[#666666] text-white px-6 py-2.5 rounded-[15px] hover:bg-[#666666] transition-colors">
                <span class="font-montserrat font-medium text-[14px]">Riwayat Pembaruan</span>
            </button>
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
            <a href="{{ route('supplier.tambah') }}"
                class="bg-[#74512d] hover:bg-[#5f4224] text-white px-6 py-2.5 rounded-[15px] shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)] flex items-center gap-1 transition-colors"
                wire:navigate>
                <flux:icon.plus class="size-5" />
                <span class="font-montserrat font-semibold text-[16px]">Tambah Toko</span>
            </a>
        </div>

        <div class="w-full">
            <table class="w-full">
                <thead>
                    <tr class="bg-[#3f4e4f] h-[60px]">
                        <th class="text-left px-6 py-5 rounded-tl-[15px]">
                            <div class="flex items-center gap-1">
                                <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Nama Toko
                                    Persediaan</span>
                                <button class="size-3.5">
                                    <flux:icon.chevron-up-down class="text-[#f8f4e1]" />
                                </button>
                            </div>
                        </th>
                        <th class="text-left px-6 py-5">
                            <div class="flex items-center gap-1">
                                <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">Nama Kontak</span>
                                <button class="size-3.5">
                                    <flux:icon.chevron-up-down class="text-[#f8f4e1]" />
                                </button>
                            </div>
                        </th>
                        <th class="text-left px-6 py-5 rounded-tr-[15px]">
                            <span class="font-montserrat font-bold text-[14px] text-[#f8f4e1]">No. Telepon</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-[#fafafa]">
                    @forelse($suppliers as $supplier)
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
                    @empty
                        <tr class="h-[60px]">
                            <td colspan="3" class="px-6 py-0 text-center">
                                <span class="font-montserrat font-medium text-[14px] text-[#666666]">Tidak ada
                                    data.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex justify-between items-center mt-5">
            <div class="flex gap-1 items-center font-montserrat font-medium text-[14px] text-[#666666] opacity-70">
                <span>Menampilkan</span>
                <span>{{ $suppliers->firstItem() ?? 0 }}</span>
                <span>hingga</span>
                <span>{{ $suppliers->lastItem() ?? 0 }}</span>
                <span>dari</span>
                <span>{{ $suppliers->total() }}</span>
                <span>baris data</span>
            </div>
            <div class="flex gap-2.5 items-center">
                @if ($suppliers->onFirstPage())
                    <button disabled
                        class="bg-[#fafafa] border border-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px] opacity-50 cursor-not-allowed">
                        <flux:icon.chevron-left class="size-[17px] text-[#666666]" />
                    </button>
                @else
                    <button wire:click="previousPage"
                        class="bg-[#fafafa] border border-[#666666] hover:bg-[#f0f0f0] min-w-[30px] px-2.5 py-1 rounded-[5px] transition-colors">
                        <flux:icon.chevron-left class="size-[17px] text-[#666666]" />
                    </button>
                @endif

                <div class="bg-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px]">
                    <span
                        class="font-montserrat font-medium text-[14px] text-[#fafafa] text-center block">{{ $suppliers->currentPage() }}</span>
                </div>

                @if ($suppliers->hasMorePages())
                    <button wire:click="nextPage"
                        class="bg-[#fafafa] border border-[#666666] hover:bg-[#f0f0f0] min-w-[30px] px-2.5 py-1 rounded-[5px] transition-colors">
                        <flux:icon.chevron-right class="size-[17px] text-[#666666]" />
                    </button>
                @else
                    <button disabled
                        class="bg-[#fafafa] border border-[#666666] min-w-[30px] px-2.5 py-1 rounded-[5px] opacity-50 cursor-not-allowed">
                        <flux:icon.chevron-right class="size-[17px] text-[#666666]" />
                    </button>
                @endif
            </div>
        </div>
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
