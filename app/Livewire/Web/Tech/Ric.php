<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

class Ric extends Component
{
    use HasSeo;

    public function render()
    {
        return view('livewire.web.tech.ric')->layout('layouts.web', $this->seo('tech_ric'));
    }
}
