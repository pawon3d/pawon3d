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

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- email -->
        <flux:input wire:model="email" :label="__('Email')" type="email" name="email" required autofocus
            autocomplete="email" placeholder="Email" />

        <!-- password -->
        <div class="relative">
            <flux:input wire:model="password" :label="__('Password')" type="password" name="password" required
                autocomplete="current-password" placeholder="Password" viewable />
        </div>

        <!-- Remember Me -->
        <label for="remember">
            <input type="checkbox" id="remember" name="remember" wire:model="remember"
                class="form-checkbox h-4 w-4 text-black border-gray-500 rounded-full focus:ring-0 focus:ring-offset-0 not-checked:bg-white" />
            <span class="text-sm ml-4 font-medium select-none text-zinc-800">
                Ingat Saya
            </span>
        </label>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Masuk') }}</flux:button>
        </div>
    </form>

    {{-- @if (Route::has('register'))
    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        Don't have an account?
        <flux:link :href="route('register')" wire:navigate>Sign up</flux:link>
    </div>
    @endif --}}
</div>