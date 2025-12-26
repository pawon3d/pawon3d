<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    use LivewireAlert;

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        View::share('title', 'Reset Password');
        $this->token = $token;

        $this->email = request()->string('email');

    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults(), 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/'
            ],
        [
            'token.required' => 'Token reset tidak valid atau sudah kadaluarsa.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Email harus valid.',
            'password.required' => 'Kata sandi harus diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PasswordReset) {
            $this->addError('email', __($status));

            return;
        }

        $this->alert('success', 'Password berhasil direset');

        Session::flash('status', 'Password berhasil direset');
        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header title="Reset password" description="Masukkan password baru" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="resetPassword" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            id="email"
            :label="__('Email')"
            type="email"
            name="email"
            required
            autocomplete="email"
            disabled
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            id="password"
            :label="__('Password')"
            type="password"
            name="password"
            required
            autocomplete="new-password"
            placeholder="Password"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            id="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
            placeholder="Confirm password"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Reset password') }}
            </flux:button>
        </div>
    </form>
</div>
