<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

class Invisible extends Component
{
    use HasSeo;

    public function render()
    {
        return view('livewire.web.tech.invisible')->layout('layouts.web', $this->seo('tech_invisible'));
    }
}
