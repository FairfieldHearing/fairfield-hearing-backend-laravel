<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

use App\Models\DeviceGallery;

class Bte extends Component
{
    use HasSeo;

    public function render()
    {
        $gallery = DeviceGallery::with('media')
            ->where('style_slug', 'bte')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.web.tech.bte', [
            'gallery' => $gallery
        ])->layout('layouts.web', $this->seo('tech_bte'));
    }
}
