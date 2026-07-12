<div>
    <!-- HEADER -->
    <x-header title="Page Settings" subtitle="Configure SEO title, description, keywords, and canonical URLs for static web pages" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search pages..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy">
            @scope('cell_updated_at', $setting)
                {{ $setting->updated_at->format('M d, Y H:i') }}
            @endscope

            @scope('actions', $setting)
            <div class="flex gap-2">
                <x-button icon="o-pencil" wire:click="showEdit({{ $setting->id }})" class="btn-ghost btn-sm text-primary" tooltip="Edit SEO Settings" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FORM DRAWER -->
    <x-drawer wire:model="drawer" title="Edit Page SEO Settings" right separator with-close-button class="lg:w-1/3">
        <x-form wire:submit="save">
            <div class="space-y-4">
                <x-input label="Page Name" wire:model="page_name" disabled />
                <x-input label="Route Key" wire:model="page_key" disabled />
                
                <div class="divider">SEO Tag Overrides</div>

                <x-input label="Meta Title" wire:model="meta_title" placeholder="Leave empty for default" />
                <x-textarea label="Meta Description" wire:model="meta_description" placeholder="Leave empty for default" rows="4" />
                <x-textarea label="Meta Keywords" wire:model="meta_keywords" placeholder="Comma-separated keywords (e.g. key1, key2)" rows="3" />
                <x-input label="Canonical URL" wire:model="canonical_url" placeholder="Automatic: {{ $this->automaticCanonical }}" hint="Left empty, it defaults to: {{ $this->automaticCanonical }}" />
                <x-textarea label="JSON Schema (JSON-LD)" wire:model="json_schema" placeholder='{ "@context": "https://schema.org", ... }' rows="6" />
            </div>

            <x-slot:actions>
                <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                <x-button label="Save Settings" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
