<?php

namespace App\Livewire\Admin\Locations;

use Mary\Traits\Toast;
use Illuminate\Support\Collection;
use Livewire\Component;
use App\Models\Location;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
use Toast;

    public function mount()
    {
        Gate::authorize('manage-content');
    }

    public string $search = '';
    public array $sortBy = ['column' => 'sort_order', 'direction' => 'asc'];

    // Form fields
    public ?Location $location = null;
    public string $name = '';
    public bool $is_main = false;
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = 'United Kingdom';
    public string $availability = '';
    public string $phone = '';
    public string $whatsapp = '';
    public string $google_maps_link = '';
    public string $meta_title = '';
    public string $meta_description = '';
    public string $json_schema = '';

    public bool $drawer = false;

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->location = null;
        $this->reset([
            'name', 'is_main', 'address_line1', 'address_line2', 'city', 'state', 
            'postal_code', 'country', 'availability', 'phone', 'whatsapp', 
            'google_maps_link', 'meta_title', 'meta_description', 'json_schema'
        ]);
        $this->country = 'United Kingdom';
        $this->drawer = true;
    }

    public function showEdit(Location $location): void
    {
        $this->resetValidation();
        $this->location = $location;
        $this->name = $location->name;
        $this->is_main = $location->is_main;
        $this->address_line1 = $location->address_line1;
        $this->address_line2 = $location->address_line2 ?? '';
        $this->city = $location->city;
        $this->state = $location->state;
        $this->postal_code = $location->postal_code;
        $this->country = $location->country;
        $this->availability = $location->availability;
        $this->phone = $location->phone ?? '';
        $this->whatsapp = $location->whatsapp ?? '';
        $this->google_maps_link = $location->google_maps_link ?? '';
        $this->meta_title = $location->meta_title ?? '';
        $this->meta_description = $location->meta_description ?? '';
        $this->json_schema = $location->json_schema ? json_encode($location->json_schema, JSON_PRETTY_PRINT) : '';
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'is_main' => 'required|boolean',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'availability' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'google_maps_link' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'json_schema' => 'nullable|json',
        ];

        $this->validate($rules);

        // If is_main is checked, uncheck it for other locations
        if ($this->is_main) {
            Location::where('is_main', true)->update(['is_main' => false]);
        }

        $decodedSchema = $this->json_schema ? json_decode($this->json_schema, true) : null;

        $data = [
            'name' => $this->name,
            'is_main' => $this->is_main,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'availability' => $this->availability,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'google_maps_link' => $this->google_maps_link,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'json_schema' => $decodedSchema,
        ];

        if ($this->location) {
            $this->location->update($data);
            $this->success('Location updated successfully.', position: 'toast-bottom');
        } else {
            $maxOrder = Location::max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;

            Location::create($data);
            $this->success('Location created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
    }

    public function delete(Location $location): void
    {
        $location->delete();
        $this->success('Location deleted successfully.', position: 'toast-bottom');
    }

    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Location::where('id', $id)->update(['sort_order' => $index]);
        }
        $this->success('Location display order updated.', position: 'toast-bottom');
    }

    public function locations()
    {
        return Location::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('city', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    public function with(): array
    {
        return [
            'rows' => $this->locations(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.locations.index', $this->with())->layout('layouts.app');
    }

}
