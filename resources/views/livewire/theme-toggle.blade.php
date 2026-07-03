<?php

use Livewire\Component;

new class extends Component {
    public string $theme = 'light';

    public function mount()
    {
        $this->theme = auth()->check() ? (auth()->user()->theme ?: 'light') : 'light';
    }

    public function toggle()
    {
        $this->theme = $this->theme === 'light' ? 'dark' : 'light';
        if (auth()->check()) {
            auth()->user()->update(['theme' => $this->theme]);
        }
        $this->dispatch('theme-updated', theme: $this->theme);
    }
}; ?>

<div x-data="{ theme: '{{ $theme }}' }" @theme-updated.window="theme = $event.detail.theme; document.documentElement.setAttribute('data-theme', theme)">
    <x-button :icon="$theme === 'light' ? 'o-moon' : 'o-sun'" class="btn-circle btn-ghost" wire:click="toggle" />
</div>
