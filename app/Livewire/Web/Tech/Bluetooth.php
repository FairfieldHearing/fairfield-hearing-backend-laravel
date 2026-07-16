<?php

namespace App\Livewire\Web\Tech;

use Livewire\Component;
use App\Traits\HasSeo;

use App\Models\DeviceGallery;

class Bluetooth extends Component
{
    use HasSeo;

    public function render()
    {
        $gallery = DeviceGallery::with('media')
            ->where('style_slug', 'bluetooth')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.web.tech.bluetooth', [
            'gallery' => $gallery
        ])->layout('layouts.web', $this->seo('tech_bluetooth'));
    }
}
