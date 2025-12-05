<div class="min-h-[60vh] flex items-center justify-center">
    <div class="text-center max-w-lg mx-auto p-8">
        {{-- Icon --}}
        <div class="mb-6">
            <div class="mx-auto w-24 h-24 bg-amber-100 rounded-full flex items-center justify-center">
                <flux:icon icon="clock" class="w-12 h-12 text-amber-600" />
            </div>
        </div>

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-800 mb-3">
            Menunggu Penugasan Peran
        </h1>

        {{-- Description --}}
        <p class="text-gray-600 mb-6">
            Akun Anda telah berhasil diaktifkan! Namun, Anda belum memiliki peran yang ditetapkan.
            Silakan hubungi administrator untuk mendapatkan akses ke sistem.
        </p>

        {{-- Info Card --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <flux:icon icon="information-circle" class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" />
                <div class="text-left text-sm text-blue-800">
                    <p class="font-medium mb-1">Apa yang harus dilakukan?</p>
                    <p>Hubungi administrator atau pemilik usaha untuk meminta peran yang sesuai dengan tugas Anda.</p>
                </div>
            </div>
        </div>

        {{-- User Info --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-500 mb-1">Login sebagai:</p>
            <p class="font-medium text-gray-800">{{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <flux:button href="{{ route('home') }}" variant="ghost">
                <flux:icon icon="home" class="w-4 h-4 mr-2" />
                Kembali ke Beranda
            </flux:button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="primary">
                    <flux:icon icon="arrow-right-start-on-rectangle" class="w-4 h-4 mr-2" />
                    Keluar
                </flux:button>
            </form>
        </div>
    </div>
</div>
