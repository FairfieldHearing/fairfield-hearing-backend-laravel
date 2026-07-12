<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

class Bluetooth extends Component
{
    use HasSeo;

    public function render()
    {
        return view('livewire.web.tech.bluetooth')->layout('layouts.web', $this->seo('tech_bluetooth'));
    }
}
