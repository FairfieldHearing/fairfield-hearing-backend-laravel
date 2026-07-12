<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

class Bte extends Component
{
    use HasSeo;

    public function render()
    {
        return view('livewire.web.tech.bte')->layout('layouts.web', $this->seo('tech_bte'));
    }
}
