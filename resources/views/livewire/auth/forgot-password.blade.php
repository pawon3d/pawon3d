<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\User;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    use LivewireAlert;

    public function mount(){
         View::share('title', 'Lupa Password');
    }
    

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $user = User::where('email', $this->email)->first();

        if ($user && !$user->is_active) {
            $this->alert('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
            return;
        }

        $status = Password::sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            $this->alert('success', 'Link reset password berhasil dikirim');
        } else {
            $this->alert('error', 'Link reset password gagal dikirim' );
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header title="Forgot password" description="Masukkan email yang terdaftar untuk mengirim link reset password" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email Address')"
            type="email"
            name="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Kirim link reset password') }}</flux:button>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-400">
        <flux:link :href="route('login')" wire:navigate>{{ __('Kembali ke login') }}</flux:link>
    </div>
</div>
