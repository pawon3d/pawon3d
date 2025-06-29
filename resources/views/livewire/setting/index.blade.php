<div class="max-h-96 overflow-y-auto ml-12">
    <div class="flex items-center gap-4 mb-4 flex-row bg-white p-4 rounded-lg shadow-lg">
        <div class="relative flex h-16 w-16 shrink-0 overflow-hidden rounded-full">
            @if (auth()->user()->image)
            <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="{{ auth()->user()->name }}"
                class="h-full w-full object-cover rounded-full">
            @else
            <span class="flex h-full w-full items-center justify-center rounded-full bg-neutral-200 text-black">
                {{ auth()->user()->initials() }}
            </span>
            @endif
        </div>
        <div class="flex flex-col">
            <h1 class="text-2xl font-semibold text-neutral-900">
                {{ auth()->user()->name }} ({{ auth()->user()->getRoleNames()->first() ?? 'User' }})
            </h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                {{ auth()->user()->email }}
            </p>
        </div>
        <div class="ml-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <flux:button type="submit" icon="arrow-left-end-on-rectangle" variant="primary"
                    wire:loading.attr="disabled">
                    Keluar
                </flux:button>
            </form>
        </div>
    </div>
    <div class="flex flex-col gap-4 mt-8">
        <a href="{{ route('profil-saya', auth()->user()->id) }}"
            class="flex flex-row items-center gap-4 hover:bg-gray-100 py-2 px-4 rounded-lg transition ease-in-out duration-150 border border-gray-200 bg-white">
            <flux:icon.user class="w-6 h-6 text-gray-600" variant="solid" />
            <span class="text-gray-800">Profil Saya</span>
            <flux:icon.chevron-right class="ml-auto w-4 h-4 text-gray-400" />
        </a>
        @can('Manajemen Sistem')
        <a href="{{ route('profil-usaha') }}"
            class="flex flex-row items-center gap-4 hover:bg-gray-100 py-2 px-4 rounded-lg transition ease-in-out duration-150 border border-gray-200 bg-white">
            <flux:icon.building-storefront class="w-6 h-6 text-gray-600" variant="solid" />
            <span class="text-gray-800">Profil Usaha</span>
            <flux:icon.chevron-right class="ml-auto w-4 h-4 text-gray-400" />
        </a>
        <a href="{{ route('metode-pembayaran') }}"
            class="flex flex-row items-center gap-4 hover:bg-gray-100 py-2 px-4 rounded-lg transition ease-in-out duration-150 border border-gray-200 bg-white">
            <flux:icon.wallet class="w-6 h-6 text-gray-600" variant="solid" />
            <span class="text-gray-800">Metode Pembayaran</span>
            <flux:icon.chevron-right class="ml-auto w-4 h-4 text-gray-400" />
        </a>
        @endcan
        <a href="{{ route('panduan-pengguna') }}"
            class="flex flex-row items-center gap-4 hover:bg-gray-100 py-2 px-4 rounded-lg transition ease-in-out duration-150 border border-gray-200 bg-white">
            <flux:icon.exclamation-circle class="w-6 h-6 text-gray-600" variant="solid" />
            <span class="text-gray-800">Panduan Pengguna</span>
            <flux:icon.chevron-right class="ml-auto w-4 h-4 text-gray-400" />
        </a>
    </div>
</div>