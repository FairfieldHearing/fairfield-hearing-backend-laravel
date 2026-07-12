<div>
    <!-- HEADER -->
    <x-header title="Hearing Aid Models" subtitle="Manage specific hearing aid models, MRP prices, and discount percentages" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select placeholder="Filter by Brand" wire:model.live="filterBrand" :options="$brands" option-value="id" option-label="name" clearable class="select-sm w-48" />
            <x-input placeholder="Search models..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" class="input-sm w-64" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Model" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow class="overflow-x-auto">
        <div x-data="{
            initSortable() {
                let el = document.getElementById('models-table-body');
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
                        <th>Brand</th>
                        <th>Model Name</th>
                        <th>Specs</th>
                        <th>Units</th>
                        <th class="text-right">MRP</th>
                        <th class="text-right">Discount</th>
                        <th class="text-right">Offer Price</th>
                        <th>Status</th>
                        <th class="w-24 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="models-table-body">
                    @forelse($rows as $m)
                        <tr data-id="{{ $m->id }}" class="hover">
                            <td class="align-middle">
                                <span class="drag-handle cursor-grab active:cursor-grabbing text-base-content/40 hover:text-base-content">
                                    <x-icon name="o-bars-4" class="w-5 h-5" />
                                </span>
                            </td>
                            <td class="align-middle font-bold">{{ $m->manufacturer?->name }}</td>
                            <td class="align-middle font-semibold text-primary text-base">{{ $m->name }}</td>
                            <td class="align-middle text-xs space-y-1">
                                <div><span class="badge badge-outline text-xs">{{ $m->tech_level }} Tech</span></div>
                                <div><span class="badge badge-ghost text-xs">{{ $m->form_factor }}</span></div>
                            </td>
                            <td class="align-middle text-sm font-medium text-base-content/70">
                                {{ $m->units === 2 ? 'Pair' : 'Single' }}
                            </td>
                            <td class="text-right align-middle font-mono font-bold text-base-content/60">
                                ₹{{ number_format($m->mrp) }}
                            </td>
                            <td class="text-right align-middle text-success font-bold text-sm">
                                {{ $m->discount_pct }}% OFF
                            </td>
                            <td class="text-right align-middle font-mono font-extrabold text-success text-base">
                                ₹{{ number_format(round($m->mrp * (100 - $m->discount_pct) / 100)) }}
                            </td>
                            <td class="align-middle">
                                <button type="button" wire:click="toggleActive({{ $m->id }})" class="focus:outline-none" title="Click to toggle status">
                                    @if($m->is_active)
                                        <span class="badge badge-success text-xs cursor-pointer hover:opacity-85 transition-opacity">Active</span>
                                    @else
                                        <span class="badge badge-ghost text-xs text-base-content/40 cursor-pointer hover:opacity-85 transition-opacity">Inactive</span>
                                    @endif
                                </button>
                            </td>
                            <td class="text-right align-middle">
                                <div class="flex justify-end gap-1">
                                    <x-button icon="o-pencil" wire:click="showEdit({{ $m->id }})" class="btn-ghost btn-xs text-primary" />
                                    <x-button icon="o-trash" wire:click="delete({{ $m->id }})" wire:confirm="Are you sure you want to delete this model?" class="btn-ghost btn-xs text-error" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-6 text-base-content/50">No models found. Select a different filter or create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="{{ $modelInstance ? 'Edit Model' : 'Add Model' }}" right separator with-close-button class="lg:w-2/5">
        <x-form wire:submit="save">
            <div class="grid grid-cols-2 gap-4">
                <x-select label="Brand" wire:model="manufacturer_id" :options="$brands" option-value="id" option-label="name" placeholder="— Select Brand —" required />
                <x-input label="Model Name" wire:model="name" placeholder="e.g. Styletto 7IX" required />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-2">
                <x-input label="MRP (INR)" wire:model="mrp" type="number" placeholder="e.g. 185000" required />
                <x-input label="Discount %" wire:model="discount_pct" type="number" step="0.1" placeholder="e.g. 50.0" required />
            </div>

            <div class="grid grid-cols-3 gap-4 mt-2">
                <x-select label="Tech Level" wire:model="tech_level" :options="[
                    ['id' => 'Premium', 'name' => 'Premium'],
                    ['id' => 'Advanced', 'name' => 'Advanced'],
                    ['id' => 'Standard', 'name' => 'Standard'],
                    ['id' => 'Essential', 'name' => 'Essential'],
                    ['id' => 'Basic', 'name' => 'Basic']
                ]" option-value="id" option-label="name" required />

                <x-select label="Form Factor" wire:model="form_factor" :options="[
                    ['id' => 'RIC', 'name' => 'RIC / RIE'],
                    ['id' => 'Slim-RIC', 'name' => 'Slim-RIC'],
                    ['id' => 'BTE', 'name' => 'BTE'],
                    ['id' => 'Custom (ITC/CIC/IIC)', 'name' => 'Custom']
                ]" option-value="id" option-label="name" required />

                <x-select label="Units" wire:model="units" :options="[
                    ['id' => 1, 'name' => 'Single (1 ear)'],
                    ['id' => 2, 'name' => 'Pair (both ears)']
                ]" option-value="id" option-label="name" required />
            </div>

            <x-input label="Tags (comma-separated)" wire:model="tags_text" placeholder="e.g. Rechargeable, Bluetooth, Tinnitus" class="mt-2" />

            <!-- KEY FEATURES -->
            <div class="divider mt-4 font-bold text-sm">Key Features</div>
            <div class="space-y-2">
                @foreach($key_features as $index => $feature)
                    <div class="flex gap-2 items-center">
                        <x-input placeholder="Enter key feature description..." wire:model="key_features.{{ $index }}" class="flex-1" />
                        <x-button icon="o-trash" wire:click="removeFeature({{ $index }})" class="btn-ghost text-error btn-sm" />
                    </div>
                @endforeach
                <x-button label="Add Feature Line" wire:click="addFeature" icon="o-plus" class="btn-outline btn-sm btn-block mt-2" />
            </div>

            <x-checkbox label="Active (Show in exchange model options)" wire:model="is_active" class="mt-4" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save Model" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
