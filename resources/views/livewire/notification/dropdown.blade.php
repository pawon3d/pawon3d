<div>
    <flux:dropdown position="bottom" align="start">
        <flux:button type="button" variant="ghost" class="cursor-pointer">
            <flux:icon.bell variant="{{ $this->unreadCount > 0 ? 'solid' : 'outline' }}" class="text-gray-100 size-5" />
            @if ($this->unreadCount > 0)
                <span
                    class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                    {{ $this->unreadCount }}
                </span>
            @endif
        </flux:button>
        <flux:menu>
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal flex flex-row justify-between items-center">
                    <div class="flex items-center gap-3 px-1 py-1.5 text-left text-sm">
                        <span class="truncate">Semua Notifikasi</span>
                    </div>
                    <div class="flex justify-end px-2 py-1.5">
                        <button wire:click="markAllAsRead"
                            class="text-end text-xs text-blue-600 hover:bg-gray-200 cursor-pointer">
                            Tandai Semua Dibaca
                        </button>
                    </div>
                </div>
            </flux:menu.radio.group>
            <flux:menu.separator />
            <flux:menu.radio.group>
                @forelse ($this->notifications as $notification)
                    <flux:menu.item wire:click="markAsRead('{{ $notification->id }}')"
                        class="flex flex-col justify-start items-start gap-1 {{ $notification->is_read ? 'bg-white' : 'bg-gray-100 font-semibold' }} hover:bg-gray-200 max-w-72 cursor-pointer">
                        <p class="text-xs text-left line-clamp-2 break-words w-full">
                            {{ $notification->title }}
                            <span @class([
                                'text-red-500' => $notification->status == 0,
                                'text-yellow-500' => $notification->status == 1,
                                'text-green-500' => $notification->status == 2,
                            ])>
                                {!! $notification->body !!}
                            </span>
                        </p>
                        <span class="text-xs text-right text-gray-500">
                            {{ \Carbon\Carbon::parse($notification->created_at)->locale('id')->diffForHumans() }}
                        </span>
                    </flux:menu.item>
                @empty
                    <flux:menu.item class="text-center text-xs text-gray-500">
                        Tidak ada notifikasi
                    </flux:menu.item>
                @endforelse
            </flux:menu.radio.group>
            <flux:menu.separator />
            <flux:menu.radio.group>
                <div class="flex justify-end px-2 py-1.5">
                    <a href="{{ route('notifikasi') }}" wire:navigate
                        class="text-end text-xs text-blue-600 hover:bg-gray-200">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            </flux:menu.radio.group>
        </flux:menu>
    </flux:dropdown>
</div>
