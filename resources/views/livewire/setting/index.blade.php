<div class="flex flex-col gap-10">
    <!-- Profile Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-6 sm:px-[30px] py-5 sm:py-[25px] flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <div class="flex gap-4 sm:gap-5 items-center">
            <!-- Avatar -->
            <div class="relative flex h-[53px] w-[53px] shrink-0 overflow-hidden rounded-full">
                @if (auth()->user()->image)
                    <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="{{ auth()->user()->name }}"
                        class="h-full w-full object-cover rounded-full">
                @else
                    <span
                        class="flex h-full w-full items-center justify-center rounded-full bg-[#c4c4c4] text-[#333333] font-medium text-lg">
                        {{ auth()->user()->initials() }}
                    </span>
                @endif
            </div>
            <!-- User Info -->
            <div class="flex flex-col gap-1">
                <div class="flex flex-wrap gap-1 sm:gap-[5px] items-center">
                    <span class="text-lg sm:text-xl font-medium text-[#333333]">{{ auth()->user()->name }}</span>
                    <span
                        class="text-sm sm:text-base font-normal text-[#666666]">({{ auth()->user()->getRoleNames()->first() ?? 'User' }})</span>
                </div>
                <span class="text-sm sm:text-base text-[#333333] break-all">{{ auth()->user()->email }}</span>
            </div>
        </div>
        <!-- Logout Button -->
        <flux:modal.trigger name="logoutModal">
            <flux:button type="button" icon="arrow-right-start-on-rectangle" variant="danger"
                class="!w-full sm:!w-auto !px-[25px] !py-[10px] !rounded-[15px] !text-base !font-semibold">
                Keluar
            </flux:button>
        </flux:modal.trigger>
    </div>

    <!-- Menu List -->
    <div class="flex flex-col gap-5">
        <!-- Profil Anda -->
        <a href="{{ route('profil-saya', auth()->user()->id) }}" wire:navigate
            class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] h-[60px] flex items-center px-5 hover:bg-gray-100 transition-colors">
            <div class="flex-1 flex gap-[10px] items-center">
                <div class="p-[5px]">
                    <flux:icon.user class="size-5 text-[#666666]" />
                </div>
                <span class="text-lg text-[#333333]">Profil Anda</span>
            </div>
            <div class="flex items-center justify-center w-[42px] h-full">
                <flux:icon.chevron-right class="size-5 text-[#666666]" />
            </div>
        </a>

        @can('manajemen.profil_usaha.kelola')
            <!-- Profil Usaha -->
            <a href="{{ route('profil-usaha') }}" wire:navigate
                class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] h-[60px] flex items-center px-5 hover:bg-gray-100 transition-colors">
                <div class="flex-1 flex gap-[10px] items-center">
                    <div class="p-[5px]">
                        <flux:icon.building-storefront class="size-5 text-[#666666]" />
                    </div>
                    <span class="text-lg text-[#333333]">Profil Usaha</span>
                </div>
                <div class="flex items-center justify-center w-[42px] h-full">
                    <flux:icon.chevron-right class="size-5 text-[#666666]" />
                </div>
            </a>
        @endcan

        @can('manajemen.pembayaran.kelola')
            <!-- Metode Pembayaran -->
            <a href="{{ route('metode-pembayaran') }}" wire:navigate
                class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] h-[60px] flex items-center px-5 hover:bg-gray-100 transition-colors">
                <div class="flex-1 flex gap-[10px] items-center">
                    <div class="p-[5px]">
                        <flux:icon.wallet class="size-5 text-[#666666]" />
                    </div>
                    <span class="text-lg text-[#333333]">Metode Pembayaran</span>
                </div>
                <div class="flex items-center justify-center w-[42px] h-full">
                    <flux:icon.chevron-right class="size-5 text-[#666666]" />
                </div>
            </a>
        @endcan

        <!-- Notifikasi -->
        <a href="{{ route('notifikasi') }}" wire:navigate
            class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] h-[60px] flex items-center px-5 hover:bg-gray-100 transition-colors">
            <div class="flex-1 flex gap-[10px] items-center">
                <div class="p-[5px]">
                    <flux:icon.bell class="size-5 text-[#666666]" />
                </div>
                <span class="text-lg text-[#333333]">Notifikasi</span>
            </div>
            <div class="flex items-center justify-center w-[42px] h-full">
                <flux:icon.chevron-right class="size-5 text-[#666666]" />
            </div>
        </a>

        <!-- Panduan Pengguna -->
        {{-- <a href="{{ route('panduan-pengguna') }}" wire:navigate
            class="bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] h-[60px] flex items-center px-5 hover:bg-gray-100 transition-colors">
            <div class="flex-1 flex gap-[10px] items-center">
                <div class="p-[5px]">
                    <flux:icon.question-mark-circle class="size-5 text-[#666666]" />
                </div>
                <span class="text-lg text-[#333333]">Panduan Pengguna</span>
            </div>
            <div class="flex items-center justify-center w-[42px] h-full">
                <flux:icon.chevron-right class="size-5 text-[#666666]" />
            </div>
        </a> --}}
    </div>
</div>
