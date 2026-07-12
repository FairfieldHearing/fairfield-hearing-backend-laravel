<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

class Rechargeable extends Component
{
    use HasSeo;

    public function render()
    {
        return view('livewire.web.tech.rechargeable')->layout('layouts.web', $this->seo('tech_rechargeable'));
    }
}
