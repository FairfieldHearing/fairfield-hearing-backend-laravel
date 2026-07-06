<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;

class Invisible extends Component
{
    public function render()
    {
        return view('livewire.web.tech.invisible')->layout('layouts.web');
    }
}
