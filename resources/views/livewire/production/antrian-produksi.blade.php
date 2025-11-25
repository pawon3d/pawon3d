<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8" style="height: 40px;">
        <div class="flex items-center gap-4">
            <!-- Tombol Kembali -->
            <flux:button wire:navigate href="{{ route('produksi', ['method' => 'siap-beli']) }}" icon="arrow-left"
                variant="secondary">
                Kembali
            </flux:button>

            <h1 class="text-[20px] font-semibold text-[#666666]">Rencana Produksi Siap Saji</h1>
        </div>
    </div>

    <!-- Content Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-[30px] py-[25px]">
        <!-- Search and Actions -->
        <div class="flex items-center justify-between mb-[20px]">
            <div class="flex items-center gap-4">
                <!-- Search Bar -->
                <div class="w-[450px] flex items-center border border-[#666666] rounded-[20px] bg-white px-4 py-2">
                    <svg class="w-[30px] h-[30px] text-[#666666]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live="search" type="text" placeholder="Cari Rencana Produksi"
                        class="ml-2 flex-1 border-0 focus:ring-0 text-[16px] font-medium text-[#666666] placeholder:text-[#959595]" />
                </div>

                <!-- Filter -->
                <div class="flex items-center gap-1 text-[#666666]">
                    <svg class="w-[25px] h-[25px]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                    </svg>
                    <span class="text-[16px] font-medium px-1">Filter</span>
                </div>
            </div>

            <!-- Tombol Tambah Produksi -->
            <flux:button variant="primary" icon="plus" href="{{ route('produksi.tambah-siap-beli') }}" wire:navigate>
                Tambah Produksi
            </flux:button>
        </div>

        <!-- Table -->
        @php
            $headers = [
                [
                    'label' => 'ID Produksi',
                    'sortable' => true,
                    'sort-by' => 'production_number',
                    'class' => 'w-[180px]',
                ],
                [
                    'label' => 'Tanggal Produksi',
                    'sortable' => true,
                    'sort-by' => 'created_at',
                    'class' => 'w-[185px]',
                ],
                ['label' => 'Daftar Produk', 'class' => 'flex-1 min-w-[255px]'],
                ['label' => 'Status Produksi', 'sortable' => true, 'sort-by' => 'status', 'class' => 'w-[140px]'],
            ];
        @endphp

        <x-table.paginated :headers="$headers" :paginator="$productions" :empty-message="'Belum ada rencana produksi.'" header-bg="#3f4e4f"
            header-text="#f8f4e1" body-bg="#fafafa" body-text="#666666">
            @foreach ($productions as $production)
                <tr class="border-b border-[#d4d4d4] hover:bg-gray-50" wire:key="production-{{ $production->id }}">
                    <td class="px-6 py-5 w-[180px]">
                        <a href="{{ route('produksi.rincian-siap-beli', ['id' => $production->id]) }}" wire:navigate
                            class="text-[14px] font-medium text-[#666666] hover:text-[#3f4e4f] hover:underline">
                            {{ $production->production_number }}
                        </a>
                    </td>

                    <td class="px-6 py-5 w-[185px]">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2 text-[14px] font-medium text-[#666666]">
                                <span>
                                    {{ $production->start_date ? \Carbon\Carbon::parse($production->start_date)->translatedFormat('d M Y') : '-' }}
                                </span>
                                <span>{{ $production->time ? \Carbon\Carbon::parse($production->time)->format('H:i') : '' }}</span>
                            </div>
                            @if ($production->start_date)
                                @php
                                    $daysFromNow = (int) \Carbon\Carbon::now()->diffInDays(
                                        \Carbon\Carbon::parse($production->start_date),
                                        false,
                                    );
                                    $displayColor = abs($daysFromNow) <= 3 ? '#eb5757' : '#3fa2f7';
                                @endphp
                                <span class="text-[14px] font-medium"
                                    style="color: {{ $displayColor }}">(H-{{ abs($daysFromNow) }})</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-5 flex-1 min-w-[255px]">
                        <p class="text-[14px] font-medium text-[#666666] line-clamp-2">
                            {{ $production->details->count() > 0 ? $production->details->map(fn($d) => $d->product?->name)->filter()->implode(', ') : 'Tidak ada produk' }}
                        </p>
                    </td>

                    <td class="px-6 py-5 w-[140px]">
                        <div
                            class="bg-[#adadad] rounded-[15px] px-4 py-2 inline-flex items-center justify-center min-w-[90px]">
                            <p class="text-[12px] font-bold text-[#fafafa] text-center leading-tight">
                                Belum<br>Diproses
                            </p>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>
</div>
