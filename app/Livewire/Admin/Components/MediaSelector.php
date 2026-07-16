<?php

namespace App\Livewire\Admin\Components;

use Livewire\Component;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;

class MediaSelector extends Component
{
    #[Modelable]
    public ?string $value = null;

    public string $targetField;
    public string $folder = 'general';
    public bool $showModal = false;
    public bool $headless = false;

    public function mount(string $targetField, string $folder = 'general', bool $headless = false)
    {
        $this->targetField = $targetField;
        $this->folder = $folder;
        $this->headless = $headless;
    }

    #[On('media-selected')]
    public function handleMediaSelected($data)
    {
        if (($data['targetField'] ?? '') === $this->targetField) {
            $this->value = $data['filepath'];
            $this->showModal = false;
        }
    }

    public function clearSelection()
    {
        $this->value = null;
    }

    public function render()
    {
        return view('livewire.admin.components.media-selector');
    }
}
