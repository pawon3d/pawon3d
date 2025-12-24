<div>
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('pengaturan') }}" wire:navigate
                class="flex items-center gap-[5px] px-5 py-2 sm:px-[25px] sm:py-[10px] bg-[#313131] text-[#f6f6f6] rounded-[15px] shadow-sm hover:bg-[#252324] transition-colors"
                style="font-family: Montserrat, sans-serif;">
                <flux:icon.arrow-left class="size-5" />
                <span class="font-semibold text-sm sm:text-[16px]">Kembali</span>
            </a>
            <h1 class="text-lg sm:text-[20px] font-semibold text-[#666666]"
                style="font-family: Montserrat, sans-serif;">Notifikasi</h1>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex items-center gap-2 sm:gap-[10px] overflow-x-auto pb-2 lg:pb-0 scrollbar-hide">
            @canany(['kasir.pesanan.kelola', 'kasir.laporan.kelola'])
                <button wire:click="$set('filter', 'kasir')"
                    class="whitespace-nowrap px-4 py-2 sm:px-[25px] sm:py-[10px] rounded-[15px] font-semibold text-sm sm:text-[16px] transition-colors shadow-sm {{ $filter === 'kasir' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] text-[#3f4e4f] border border-[#3f4e4f]' }}"
                    style="font-family: Montserrat, sans-serif;">
                    Kasir
                </button>
            @endcanany
            @canany(['produksi.rencana.kelola', 'produksi.laporan.kelola', 'produksi.mulai'])
                <button wire:click="$set('filter', 'produksi')"
                    class="whitespace-nowrap px-4 py-2 sm:px-[25px] sm:py-[10px] rounded-[15px] font-semibold text-sm sm:text-[16px] transition-colors shadow-sm {{ $filter === 'produksi' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] text-[#3f4e4f] border border-[#3f4e4f]' }}"
                    style="font-family: Montserrat, sans-serif;">
                    Produksi
                </button>
            @endcanany
            @canany(['inventori.persediaan.kelola', 'inventori.laporan.kelola', 'inventori.produk.kelola',
                'inventori.belanja.rencana.kelola', 'inventori.toko.kelola', 'inventori.belanja.mulai',
                'inventori.hitung.kelola', 'inventori.alur.lihat'])
                <button wire:click="$set('filter', 'inventori')"
                    class="whitespace-nowrap px-4 py-2 sm:px-[25px] sm:py-[10px] rounded-[15px] font-semibold text-sm sm:text-[16px] transition-colors shadow-sm {{ $filter === 'inventori' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] text-[#3f4e4f] border border-[#3f4e4f]' }}"
                    style="font-family: Montserrat, sans-serif;">
                    Inventori
                </button>
            @endcanany
        </div>
    </div>

    {{-- Notification Container --}}
    <div class="w-full flex flex-col gap-6 sm:gap-[30px] bg-[#fafafa] px-4 sm:px-[30px] py-5 sm:py-[25px] rounded-[15px]"
        style="font-family: Montserrat, sans-serif;">

        @php
            $notifications = auth()
                ->user()
                ->notifications()
                ->when($filter, function ($query) use ($filter) {
                    return $query->where('type', $filter);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $groupedNotifications = $notifications->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y');
            });
        @endphp

        @forelse ($groupedNotifications as $date => $dateNotifications)
            <div class="flex flex-col gap-5 sm:gap-[25px]">
                {{-- Date Header --}}
                <div class="flex flex-col gap-4 sm:gap-[19px]">
                    <p class="text-xs sm:text-[14px] font-semibold text-[#666666]">{{ $date }}</p>

                    {{-- Notifications for this date --}}
                    <div class="flex flex-col gap-3 sm:gap-[15px]">
                        @foreach ($dateNotifications as $notification)
                            @php
                                $icon = match ($notification->type) {
                                    'kasir' => 'shopping-cart',
                                    'produksi' => 'fire',
                                    'inventori' => 'archive-box',
                                    default => 'bell',
                                };
                            @endphp
                            <div wire:click="markAsRead('{{ $notification->id }}')"
                                class="flex flex-col sm:flex-row sm:items-center justify-between pl-[10px] pr-4 sm:pr-[20px] py-3 sm:py-[8px] border border-[#d4d4d4] rounded-[15px] cursor-pointer transition-colors {{ $notification->is_read ? 'bg-[#fafafa]' : 'bg-blue-50 hover:bg-blue-100' }}">
                                <div class="flex items-start sm:items-center gap-[5px] flex-1">
                                    {{-- Icon --}}
                                    <div class="flex items-center justify-center size-[40px] shrink-0">
                                        <flux:icon :icon="$icon" class="size-[18px] text-[#666666]" />
                                    </div>
                                    {{-- Message --}}
                                    <div class="text-sm sm:text-[14px] font-medium text-[#666666] flex-1">
                                        {!! $notification->body !!}
                                    </div>
                                </div>
                                {{-- Time --}}
                                <div class="text-xs sm:text-[14px] font-medium text-[#adadad] self-end sm:self-auto mt-2 sm:mt-0 ml-[45px] sm:ml-0">
                                    {{ $notification->created_at->locale('id')->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center py-10">
                <p class="text-sm text-[#adadad]">Tidak ada notifikasi.</p>
            </div>
        @endforelse
    </div>
</div>
