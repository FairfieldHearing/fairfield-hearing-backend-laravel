<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Hash;
use Mary\Traits\Toast;
use Livewire\Component;

class Password extends Component
{
use Toast;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        $user = auth()->user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function updateSettings(): void
    {
        $user = auth()->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . ($user?->id ?? 'NULL')],
        ];

        if ($this->password) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $this->validate($rules);

        if ($user) {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
        }

        $this->reset(['password', 'password_confirmation']);
        $this->success('Account settings updated successfully.', position: 'toast-bottom');
    }

    public function render()
    {
        return view('livewire.admin.password')->layout('layouts.app');
    }

}
