<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            $this->success('Welcome back!', redirectTo: '/');
            return;
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-base-200">
    <div class="w-full max-w-md p-6">
        <x-card shadow class="bg-base-100">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold">Admin Login</h1>
                <p class="text-base-content/60 text-sm">Please log in to manage your website contents</p>
            </div>

            <x-form wire:submit="login">
                <x-input label="E-mail" wire:model="email" icon="o-envelope" required />
                <x-input label="Password" type="password" wire:model="password" icon="o-lock-closed" required />
                <x-checkbox label="Remember me" wire:model="remember" />

                <x-slot:actions>
                    <x-button label="Log In" type="submit" class="btn-primary w-full" spinner="login" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</div>
