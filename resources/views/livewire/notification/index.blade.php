<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-5">
            <a href="{{ route('pengaturan') }}" wire:navigate
                class="flex items-center gap-[5px] px-[25px] py-[10px] bg-[#313131] text-[#f6f6f6] rounded-[15px] shadow-sm hover:bg-[#252324] transition-colors"
                style="font-family: Montserrat, sans-serif;">
                <flux:icon.arrow-left class="size-5" />
                <span class="font-semibold text-[16px]">Kembali</span>
            </a>
            <h1 class="text-[20px] font-semibold text-[#666666] hidden md:block"
                style="font-family: Montserrat, sans-serif;">Notifikasi</h1>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex items-center gap-[10px]">
            <button wire:click="$set('filter', 'kasir')"
                class="px-[25px] py-[10px] rounded-[15px] font-semibold text-[16px] transition-colors shadow-sm {{ $filter === 'kasir' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] text-[#3f4e4f] border border-[#3f4e4f]' }}"
                style="font-family: Montserrat, sans-serif;">
                Kasir
            </button>
            <button wire:click="$set('filter', 'produksi')"
                class="px-[25px] py-[10px] rounded-[15px] font-semibold text-[16px] transition-colors shadow-sm {{ $filter === 'produksi' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] text-[#3f4e4f] border border-[#3f4e4f]' }}"
                style="font-family: Montserrat, sans-serif;">
                Produksi
            </button>
            <button wire:click="$set('filter', 'inventori')"
                class="px-[25px] py-[10px] rounded-[15px] font-semibold text-[16px] transition-colors shadow-sm {{ $filter === 'inventori' ? 'bg-[#3f4e4f] text-[#f8f4e1]' : 'bg-[#fafafa] text-[#3f4e4f] border border-[#3f4e4f]' }}"
                style="font-family: Montserrat, sans-serif;">
                Inventori
            </button>
        </div>
    </div>

    {{-- Notification Container --}}
    <div class="w-full flex flex-col gap-[30px] bg-[#fafafa] px-[30px] py-[25px] rounded-[15px]"
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
            <div class="flex flex-col gap-[25px]">
                {{-- Date Header --}}
                <div class="flex flex-col gap-[19px]">
                    <p class="text-[14px] font-semibold text-[#666666]">{{ $date }}</p>

                    {{-- Notifications for this date --}}
                    <div class="flex flex-col gap-[15px]">
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
                                class="flex items-center justify-between pl-[10px] pr-[20px] py-[8px] border border-[#d4d4d4] rounded-[15px] cursor-pointer transition-colors {{ $notification->is_read ? 'bg-[#fafafa]' : 'bg-blue-50 hover:bg-blue-100' }}">
                                <div class="flex items-center gap-[5px] flex-1 max-w-[800px]">
                                    {{-- Icon --}}
                                    <div class="flex items-center justify-center size-[40px]">
                                        <flux:icon :icon="$icon" class="size-[18px] text-[#666666]" />
                                    </div>
                                    {{-- Message --}}
                                    <p class="text-[14px] font-medium text-[#666666]">
                                        {!! $notification->body !!}
                                    </p>
                                </div>
                                {{-- Time --}}
                                <p class="text-[14px] font-medium text-[#adadad]">
                                    {{ $notification->created_at->locale('id')->diffForHumans() }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center py-10">
                <p class="text-[14px] text-[#adadad]">Tidak ada notifikasi.</p>
            </div>
        @endforelse
    </div>
</div>
