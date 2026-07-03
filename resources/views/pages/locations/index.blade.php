<?php

use App\Models\Location;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
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
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Office Locations" subtitle="Manage office locations and service centers (Drag & Drop to reorder)" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Location" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow class="overflow-x-auto">
        <div x-data="{
            initSortable() {
                let el = document.getElementById('locations-table-body');
                if (!el) return;
                Sortable.create(el, {
                    animation: 150,
                    handle: '.drag-handle',
                    onEnd: () => {
                        let ids = Array.from(el.querySelectorAll('tr')).map(tr => tr.getAttribute('data-id'));
                        $wire.updateOrder(ids);
                    }
                });
            }
        }" x-init="initSortable()">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th class="w-10"></th>
                        <th class="w-16">#</th>
                        <th>Name</th>
                        <th>Main Office</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th class="w-24 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="locations-table-body">
                    @forelse($rows as $loc)
                        <tr data-id="{{ $loc->id }}" class="hover">
                            <td class="align-middle">
                                <span class="drag-handle cursor-grab active:cursor-grabbing text-base-content/40 hover:text-base-content">
                                    <x-icon name="o-bars-4" class="w-5 h-5" />
                                </span>
                            </td>
                            <td class="font-mono text-xs align-middle">#{{ $loc->id }}</td>
                            <td class="align-middle font-semibold">{{ $loc->name }}</td>
                            <td class="align-middle">
                                @if($loc->is_main)
                                    <span class="badge badge-primary">Primary Location</span>
                                @else
                                    <span class="text-base-content/40">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-sm">
                                {{ $loc->address_line1 }}, {{ $loc->address_line2 ? $loc->address_line2 . ', ' : '' }}{{ $loc->city }}, {{ $loc->postal_code }}
                            </td>
                            <td class="align-middle text-sm">{{ $loc->phone ?? '-' }}</td>
                            <td class="text-right align-middle">
                                <div class="flex justify-end gap-1">
                                    <x-button icon="o-pencil" wire:click="showEdit({{ $loc->id }})" class="btn-ghost btn-xs text-primary" />
                                    <x-button icon="o-trash" wire:click="delete({{ $loc->id }})" wire:confirm="Are you sure you want to delete this location?" class="btn-ghost btn-xs text-error" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-base-content/50">No locations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $location ? 'Edit Location' : 'Add Location' }}" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Location Name" wire:model="name" placeholder="e.g. London Office" required />
            
            <x-checkbox label="Set as Main Location" wire:model="is_main" />

            <div class="divider">Address Breakdown</div>
            <x-input label="Address Line 1" wire:model="address_line1" required />
            <x-input label="Address Line 2 (Optional)" wire:model="address_line2" />
            <div class="grid grid-cols-2 gap-4">
                <x-input label="City" wire:model="city" required />
                <x-input label="State / Region" wire:model="state" required />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Postal Code" wire:model="postal_code" required />
                <x-input label="Country" wire:model="country" required />
            </div>

            <div class="divider">Contact & Hours</div>
            <x-input label="Availability / Hours" wire:model="availability" placeholder="e.g. Mon-Fri 10:00 - 18:00" required />
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Phone Number" wire:model="phone" />
                <x-input label="WhatsApp Number" wire:model="whatsapp" />
            </div>
            <x-input label="Google Maps Share Link" wire:model="google_maps_link" />

            <div class="divider">SEO Metadata</div>
            <x-input label="Meta Title" wire:model="meta_title" />
            <x-textarea label="Meta Description" wire:model="meta_description" rows="2" />
            <x-textarea label="JSON Schema (JSON-LD LocalBusiness)" wire:model="json_schema" placeholder='{ "@context": "https://schema.org", ... }' rows="5" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
