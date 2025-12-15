<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function mount()
    {
        View::share('title', 'Login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('Salah email atau password'),
                'password' => __('Salah email atau password'),
            ]);
        }

        $user = Auth::user();

        // Cek apakah akun sudah diaktivasi
        if (!$user->activated_at) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Akun Anda belum diaktifkan. Silakan cek email untuk mengaktifkan akun.'),
            ]);
        }

        // Cek apakah akun aktif
        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Akun Anda telah dinonaktifkan. Silakan hubungi pemilik usaha.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('ringkasan-umum', absolute: false), navigate: false);
        // $user = Auth::user();

        // $permissionRoutes = [
        //     'Manajemen Sistem' => 'dashboard',
        //     'Kasir' => 'transaksi',
        //     'Produksi' => 'produksi',
        //     'Inventori' => 'bahan-baku',
        // ];

        // foreach ($permissionRoutes as $permission => $routeName) {
        //     if ($user->hasPermissionTo($permission)) {
        //         $this->redirect(route($routeName), navigate: false);
        //         return;
        //     }
        // }

        // $this->redirect(route('home'), navigate: false);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header title="Login" description="" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <flux:error name="email" />
    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- email -->
        <flux:input wire:model="email" type="email" name="email" required autofocus autocomplete="email"
            placeholder="Email" />

        <!-- password -->
        <div class="relative">
            <flux:input wire:model="password" type="password" name="password" required autocomplete="current-password"
                placeholder="Password" viewable />
        </div>

        <div class="flex justify-between flex-row items-center">
            <!-- Remember Me -->
            <div class="mr-2">
                <label for="remember">
                    <input type="checkbox" id="remember" name="remember" wire:model="remember"
                        class="form-checkbox h-4 w-4 text-black border-gray-500 rounded-md focus:ring-0 focus:ring-offset-0 not-checked:bg-white" />
                    <span class="text-sm ml-4 font-medium select-none text-zinc-800">
                        Ingat Saya
                    </span>
                </label>
            </div>
            <div>
                <flux:link :href="route('password.request')" class="text-sm font-medium text-primary-600"
                    variant="subtle" wire:navigate>
                    Lupa Kata Sandi?
                </flux:link>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="subtle" type="submit"
                class="w-full rounded-2xl py-2 border-black border-2 hover:bg-black hover:text-white bg-white text-black cursor-pointer">
                {{ __('Masuk') }}</flux:button>
        </div>
    </form>

    <div class="mt-6 flex justify-center">
        <flux:text>Belum punya akun? <flux:link wire:navigate href="#" class="text-primary-600 font-semibold"
                variant="ghost"> Hubungi
                Pemilik Usaha</flux:link>
        </flux:text>
    </div>
    {{-- @if (Route::has('register'))
    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        Don't have an account?
        <flux:link :href="route('register')" wire:navigate>Sign up</flux:link>
    </div>
    @endif --}}
</div>
