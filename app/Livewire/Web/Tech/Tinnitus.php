<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

class Tinnitus extends Component
{
    use HasSeo;

    public function render()
    {
        return view('livewire.web.tech.tinnitus')->layout('layouts.web', $this->seo('tech_tinnitus'));
    }
}
