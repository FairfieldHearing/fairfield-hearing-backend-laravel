<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;
use Livewire\Component;

class Login extends Component
{
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
            $this->success('Welcome back!', redirectTo: route('admin.dashboard'));
            return;
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.admin.login')->layout('layouts.app');
    }

}
