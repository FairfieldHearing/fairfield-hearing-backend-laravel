<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

use App\Models\DeviceGallery;

class Ric extends Component
{
    use HasSeo;

    public function render()
    {
        $gallery = DeviceGallery::with('media')
            ->where('style_slug', 'ric')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.web.tech.ric', [
            'gallery' => $gallery
        ])->layout('layouts.web', $this->seo('tech_ric'));
    }
}
