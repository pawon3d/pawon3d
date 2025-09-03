<div>
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ $_SERVER['HTTP_REFERER'] && $_SERVER['HTTP_REFERER'] != url()->current() ? url()->previous() : route('pengaturan') }}"
                class="mr-2 px-4 py-2 border border-gray-500 rounded-lg bg-gray-800 flex items-center text-white">
                <flux:icon.arrow-left variant="mini" class="mr-2" />
                Kembali
            </a>
            <h1 class="text-2xl hidden md:block">Notifikasi</h1>
        </div>
    </div>
    <div class="w-full flex flex-col gap-6 mt-4 bg-white p-4 rounded-lg shadow-md">
        <div class="flex justify-end">
            <button id="mark-all-read"
                class="text-blue-600 hover:bg-gray-200 cursor-pointer transition-colors duration-200"
                wire:click="markAllAsRead">
                Tandai semua sudah dibaca
            </button>
        </div>
        @php
            $groupedNotifications = auth()
                ->user()
                ->notifications->groupBy(function ($item) {
                    return \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('d F Y');
                });
        @endphp

        @forelse ($groupedNotifications as $date => $notifications)
            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold text-gray-500">{{ $date }}</p>

                @foreach ($notifications as $notification)
                    <div class="flex items-center flex-row gap-3 {{ $notification->is_read ? 'bg-gray-50' : 'bg-blue-100 hover:bg-blue-50 cursor-pointer' }} border border-gray-200 rounded-lg px-4 py-3 shadow-sm"
                        wire:click="markAsRead('{{ $notification->id }}')">
                        <div class="mt-1">
                            <flux:icon icon="cashier" class="text-gray-500 w-5 h-5" />
                        </div>
                        <div class="flex flex-row items-center justify-between w-full">
                            <p class="text-sm text-gray-700">
                                <span class="font-bold">{{ $notification->title }}</span>

                                <span @class([
                                    'text-red-500' => $notification->status == 0,
                                    'text-yellow-500' => $notification->status == 1,
                                    'text-green-500' => $notification->status == 2,
                                    'font-semibold',
                                ])>
                                    {{ $notification->body }}
                                </span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->locale('id')->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <p class="text-gray-500 text-sm">Tidak ada notifikasi.</p>
        @endforelse
    </div>

</div>
