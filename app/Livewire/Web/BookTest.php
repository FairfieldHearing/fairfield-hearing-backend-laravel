<?php

namespace App\Livewire\Web;

use App\Models\Location;
use Livewire\Component;
use App\Traits\HasSeo;

class BookTest extends Component
{
    use HasSeo;

    public function render()
    {
        $locations = Location::all()->toArray();
        return view('livewire.web.book-test', [
            'locations' => $locations
        ])->layout('layouts.web', $this->seo('book_test'));
    }
}
