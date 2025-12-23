<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ActivateAccount extends Component
{
    use LivewireAlert;

    public string $token;

    public ?User $user = null;

    public string $password = '';

    public string $password_confirmation = '';

    public bool $tokenValid = false;

    public bool $alreadyActivated = false;

    public bool $tokenExpired = false;

    public function mount(string $token): void
    {
        View::share('title', 'Aktivasi Akun');

        $this->token = $token;

        $this->user = User::where('invitation_token', $token)->first();

        if (! $this->user) {
            $this->tokenValid = false;

            return;
        }

        if ($this->user->isActivated()) {
            $this->alreadyActivated = true;

            return;
        }

        if (! $this->user->hasValidInvitationToken()) {
            $this->tokenExpired = true;

            return;
        }

        $this->tokenValid = true;
    }

    public function activate(): void
    {
        $this->validate([
            'password' => 'required|string|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/|confirmed',
        ], [
            'password.required' => 'Kata sandi harus diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $this->user->activateWithPassword($this->password);

        // Auto login setelah aktivasi
        Auth::login($this->user);

        session()->flash('success', 'Akun Anda berhasil diaktifkan! Selamat datang, '.$this->user->name.'.');

        $this->redirect(route('dashboard'));
    }

    #[Layout('components.layouts.auth')]
    public function render()
    {
        return view('livewire.activate-account');
    }
}
