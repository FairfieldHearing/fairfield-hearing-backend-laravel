<?php

namespace App\Livewire\Web;

use App\Models\Location;
use Livewire\Component;

class BookTest extends Component
{
    public function render()
    {
        $locations = Location::all()->toArray();
        return view('livewire.web.book-test', [
            'locations' => $locations
        ])->layout('layouts.web');
    }
}
