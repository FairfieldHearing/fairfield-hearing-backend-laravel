<div>
    <!-- HEADER -->
    <x-header title="Team Members" subtitle="Manage clinic audiologists, ENT specialists, and directors" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search team members..." wire:model.live.debounce.300ms="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Add Team Member" wire:click="showCreate" class="btn-primary" icon="o-plus" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE -->
    <x-card shadow class="bg-base-100">
        <x-table :headers="$headers" :rows="$members" :sort-by="$sortBy" with-pagination>
            @scope('cell_photo', $member)
                @if($member->photo)
                    <img src="{{ str_starts_with($member->photo, 'assets/') ? '/' . $member->photo : Storage::url($member->photo) }}" class="w-12 h-12 rounded-full object-cover border" alt="{{ $member->name }}" />
                @else
                    <div class="w-12 h-12 rounded-full bg-base-300 flex items-center justify-center font-bold text-xs">N/A</div>
                @endif
            @endscope

            @scope('cell_name', $member)
                <div>
                    <div class="font-bold text-base">{{ $member->name }}</div>
                    <div class="text-xs text-base-content/70">{{ $member->role }}</div>
                </div>
            @endscope

            @scope('cell_category', $member)
                <x-badge :value="ucwords(str_replace('_', ' ', $member->category))" class="badge-outline badge-primary" />
            @endscope

            @scope('actions', $member)
                <div class="flex gap-2">
                    <x-button icon="o-pencil" wire:click="showEdit({{ $member->id }})" class="btn-sm btn-ghost text-info" />
                    <x-button icon="o-trash" wire:click="delete({{ $member->id }})" wire:confirm="Are you sure you want to delete {{ $member->name }}?" class="btn-sm btn-ghost text-error" />
                </div>
            @endscope
        </x-table>
    </x-card>

    <!-- DRAWER FORM -->
    <x-drawer wire:model="drawer" title="{{ $teamMember ? 'Edit Team Member' : 'Create Team Member' }}" right separator with-close-button class="w-full max-w-3xl">
        <x-form wire:submit="save" class="space-y-4">
            <x-input label="Name" wire:model.live.debounce.500ms="name" required />
            <x-input label="Slug" wire:model="slug" required />
            <x-input label="Role / Title" wire:model="role" placeholder="e.g. Senior Audiologist & Clinical Lead" required />
            
            <x-select label="Category" wire:model="category" :options="$categories" option-value="id" option-label="name" required />

            <x-input label="Eyebrow" wire:model="eyebrow" placeholder="e.g. Our Audiologists" />
            
            <div class="space-y-2">
                <label class="label"><span class="label-text font-semibold">Photo</span></label>
                <livewire:admin.components.media-selector wire:model="photo" target-field="photo" folder="team_members" />
                @error('photo') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
            </div>

            <x-textarea label="Short Bio" wire:model="short_bio" rows="2" hint="Brief overview shown on cards" />
            <x-textarea label="Full Bio" wire:model="bio" rows="8" hint="Paragraphs separated by double line breaks will automatically display as paragraphs on the website" />
            <x-textarea label="Quote / Blockquote" wire:model="blockquote" rows="2" />

            <!-- At a Glance Repeaters -->
            <div class="space-y-2 border p-3 rounded-lg bg-base-200/50">
                <div class="flex justify-between items-center mb-2">
                    <label class="label-text font-bold">At a Glance Items</label>
                    <x-button label="Add Item" wire:click="addGlanceItem" icon="o-plus" class="btn-xs btn-outline btn-primary" />
                </div>
                @foreach($at_a_glance as $index => $item)
                    <div class="flex gap-2 items-center w-full" key="glance-{{ $index }}">
                        <div class="w-1/3">
                            <x-input wire:model="at_a_glance.{{ $index }}.label" placeholder="Label (e.g. Experience)" class="input-sm w-full" />
                        </div>
                        <div class="flex-1 w-full min-w-0">
                            <x-input wire:model="at_a_glance.{{ $index }}.value" placeholder="Value (e.g. 21+ years)" class="input-sm w-full" />
                        </div>
                        <x-button icon="o-trash" wire:click="removeGlanceItem({{ $index }})" class="btn-xs btn-ghost text-error shrink-0" />
                    </div>
                @endforeach
            </div>

            <!-- Areas of Expertise Repeaters -->
            <div class="space-y-2 border p-3 rounded-lg bg-base-200/50">
                <div class="flex justify-between items-center mb-2">
                    <label class="label-text font-bold">Areas of Expertise</label>
                    <x-button label="Add Skill" wire:click="addExpertiseItem" icon="o-plus" class="btn-xs btn-outline btn-primary" />
                </div>
                @foreach($areas_of_expertise as $index => $item)
                    <div class="flex gap-2 items-center w-full" key="exp-{{ $index }}">
                        <div class="flex-1 w-full">
                            <x-input wire:model="areas_of_expertise.{{ $index }}" placeholder="e.g. Real Ear Measurement (REM)" class="input-sm w-full" />
                        </div>
                        <x-button icon="o-trash" wire:click="removeExpertiseItem({{ $index }})" class="btn-xs btn-ghost text-error shrink-0" />
                    </div>
                @endforeach
            </div>

            <!-- Timeline Repeaters -->
            <div class="space-y-2 border p-3 rounded-lg bg-base-200/50">
                <div class="flex justify-between items-center mb-2">
                    <label class="label-text font-bold">Career & Qualifications Timeline</label>
                    <x-button label="Add Timeline Entry" wire:click="addTimelineItem" icon="o-plus" class="btn-xs btn-outline btn-primary" />
                </div>
                @foreach($timeline as $index => $tItem)
                    <div class="flex gap-2 items-center w-full" key="time-{{ $index }}">
                        <div class="w-28 shrink-0">
                            <x-input wire:model="timeline.{{ $index }}.year" placeholder="Year / Period" class="input-sm w-full" />
                        </div>
                        <div class="flex-1 w-full min-w-0">
                            <x-input wire:model="timeline.{{ $index }}.text" placeholder="Event / Position (e.g. Senior Audiologist)" class="input-sm w-full" />
                        </div>
                        <x-button icon="o-trash" wire:click="removeTimelineItem({{ $index }})" class="btn-xs btn-ghost text-error shrink-0" />
                    </div>
                @endforeach
            </div>

            <x-input label="Sort Order" wire:model="sort_order" type="number" required />

            <x-input label="Meta Title" wire:model="meta_title" />
            <x-textarea label="Meta Description" wire:model="meta_description" rows="2" />

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save Team Member" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
