<div class="flex flex-col gap-6">
    @if ($alreadyActivated)
        {{-- Akun sudah diaktivasi --}}
        <div class="text-center">
            <flux:icon.check-circle class="w-16 h-16 text-green-500 mx-auto mb-4" />
            <flux:heading size="xl">Akun Sudah Aktif</flux:heading>
            <flux:text class="mt-2 text-gray-600">
                Akun ini sudah diaktifkan sebelumnya. Silakan login untuk melanjutkan.
            </flux:text>
            <div class="mt-6">
                <flux:button variant="primary" href="{{ route('login') }}">
                    Ke Halaman Login
                </flux:button>
            </div>
        </div>
    @elseif ($tokenExpired)
        {{-- Token sudah expired --}}
        <div class="text-center">
            <flux:icon.clock class="w-16 h-16 text-amber-500 mx-auto mb-4" />
            <flux:heading size="xl">Link Sudah Kadaluarsa</flux:heading>
            <flux:text class="mt-2 text-gray-600">
                Link aktivasi ini sudah tidak berlaku (lebih dari 7 hari).
                Silakan hubungi pemilik usaha untuk mengirim ulang undangan.
            </flux:text>
        </div>
    @elseif (!$tokenValid)
        {{-- Token tidak valid --}}
        <div class="text-center">
            <flux:icon.x-circle class="w-16 h-16 text-red-500 mx-auto mb-4" />
            <flux:heading size="xl">Link Tidak Valid</flux:heading>
            <flux:text class="mt-2 text-gray-600">
                Link aktivasi yang Anda gunakan tidak valid atau sudah tidak berlaku.
            </flux:text>
        </div>
    @else
        {{-- Form aktivasi --}}
        <div class="text-center mb-4">
            <flux:heading size="xl">Aktivasi Akun</flux:heading>
            <flux:text class="mt-2 text-gray-600">
                Halo <strong>{{ $user->name }}</strong>! Silakan buat kata sandi untuk mengaktifkan akun Anda.
            </flux:text>
        </div>

        <form wire:submit="activate" class="flex flex-col gap-4">
            <flux:input type="email" label="Email" value="{{ $user->email }}" disabled readonly />

            <flux:input type="password" wire:model="password" label="Kata Sandi"
                placeholder="Minimal 8 karakter, huruf dan angka" viewable />
            <flux:error name="password" />

            <flux:input type="password" wire:model="password_confirmation" label="Konfirmasi Kata Sandi"
                placeholder="Ulangi kata sandi" viewable />
            <flux:error name="password_confirmation" />

            <flux:callout icon="information-circle" color="blue" class="text-sm">
                <flux:callout.text>
                    Kata sandi harus minimal 8 karakter dan mengandung kombinasi huruf dan angka.
                </flux:callout.text>
            </flux:callout>

            <flux:button type="submit" variant="primary" class="w-full">
                Aktifkan Akun
            </flux:button>
        </form>
    @endif
</div>
