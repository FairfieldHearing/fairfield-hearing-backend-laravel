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