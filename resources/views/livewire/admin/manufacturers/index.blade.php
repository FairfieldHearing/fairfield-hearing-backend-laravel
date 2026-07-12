<div>
    <!-- HEADER -->
    <x-header title="Brand Manufacturers" subtitle="Manage hearing aid brand logos shown on the homepage (Drag & Drop to reorder)" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search brands..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Brand" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow class="overflow-x-auto">
        <div x-data="{
            initSortable() {
                let el = document.getElementById('manufacturers-table-body');
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
                        <th class="w-32">Logo</th>
                        <th>Brand Name</th>
                        <th>Status</th>
                        <th class="w-24 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="manufacturers-table-body">
                    @forelse($rows as $m)
                        <tr data-id="{{ $m->id }}" class="hover">
                            <td class="align-middle">
                                <span class="drag-handle cursor-grab active:cursor-grabbing text-base-content/40 hover:text-base-content">
                                    <x-icon name="o-bars-4" class="w-5 h-5" />
                                </span>
                            </td>
                            <td class="font-mono text-xs align-middle">#{{ $m->id }}</td>
                            <td class="align-middle">
                                <div class="bg-base-200 p-2 rounded-md inline-block">
                                    <img src="{{ $m->logo_url }}" alt="{{ $m->name }}" class="h-10 w-auto object-contain" />
                                </div>
                            </td>
                            <td class="align-middle font-semibold text-lg">{{ $m->name }}</td>
                            <td class="align-middle space-x-1">
                                <button type="button" wire:click="toggleActive({{ $m->id }})" class="focus:outline-none" title="Click to toggle status">
                                    @if($m->is_active)
                                        <span class="badge badge-success cursor-pointer hover:opacity-85 transition-opacity">Active</span>
                                    @else
                                        <span class="badge badge-ghost text-base-content/40 cursor-pointer hover:opacity-85 transition-opacity">Inactive</span>
                                    @endif
                                </button>
                                
                                <button type="button" wire:click="toggleHomepage({{ $m->id }})" class="focus:outline-none" title="Click to toggle homepage visibility">
                                    @if($m->show_on_homepage)
                                        <span class="badge badge-info text-white cursor-pointer hover:opacity-85 transition-opacity">Homepage</span>
                                    @else
                                        <span class="badge badge-ghost text-base-content/40 cursor-pointer hover:opacity-85 transition-opacity">Hidden</span>
                                    @endif
                                </button>
                            </td>
                            <td class="text-right align-middle">
                                <div class="flex justify-end gap-1">
                                    <x-button icon="o-pencil" wire:click="showEdit({{ $m->id }})" class="btn-ghost btn-xs text-primary" />
                                    <x-button icon="o-trash" wire:click="delete({{ $m->id }})" wire:confirm="Are you sure you want to delete this brand?" class="btn-ghost btn-xs text-error" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-base-content/50">No brands found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $manufacturer ? 'Edit Brand' : 'Add Brand' }}" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="save">
            <x-input label="Brand Name" wire:model="name" placeholder="e.g. Signia" required />
            
            <div class="space-y-2">
                <label class="label"><span class="label-text font-semibold">Logo Image (PNG preferred)</span></label>
                <input type="file" wire:model="logo" accept="image/*" class="file-input file-input-bordered w-full" />
                @error('logo') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                @if($manufacturer && !$logo)
                    <div class="mt-2 bg-base-200 p-3 rounded-md inline-block">
                        <img src="{{ $manufacturer->logo_url }}" class="h-16 w-auto object-contain" />
                    </div>
                @endif
            </div>

            <x-checkbox label="Active (System status)" wire:model="is_active" />
            <x-checkbox label="Show in homepage brand strip" wire:model="show_on_homepage" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
