<div class="space-y-2" x-data="{}" x-on:open-media-selector-{{ $targetField }}.window="$wire.showModal = true">
    @if(!$headless)
        <div class="flex items-center gap-4">
            <!-- Preview -->
            <div class="w-20 h-20 bg-base-200 border border-base-300 rounded-lg overflow-hidden flex items-center justify-center shrink-0">
                @if($value)
                    @php
                        $url = (str_starts_with($value, 'assets/') || str_starts_with($value, '/assets/')) 
                            ? '/' . ltrim($value, '/') 
                            : \Illuminate\Support\Facades\Storage::url($value);
                    @endphp
                    <img src="{{ $url }}" alt="Selected image preview" class="w-full h-full object-cover" />
                @else
                    <x-icon name="o-photo" class="w-8 h-8 text-base-content/30" />
                @endif
            </div>

            <!-- Buttons -->
            <div class="space-y-1.5">
                <div class="flex gap-2">
                    <button type="button" wire:click="$set('showModal', true)" class="btn btn-outline btn-sm btn-primary">
                        <x-icon name="o-folder-open" class="w-4 h-4" />
                        Browse Media
                    </button>
                    @if($value)
                        <button type="button" wire:click="clearSelection" class="btn btn-outline btn-sm btn-error">
                            <x-icon name="o-x-mark" class="w-4 h-4" />
                            Remove
                        </button>
                    @endif
                </div>
                <p class="text-[10px] text-base-content/60">
                    @if($value)
                        Selected: <span class="font-mono">{{ basename($value) }}</span>
                    @else
                        No image selected. Upload or choose from media library.
                    @endif
                </p>
            </div>
        </div>
    @endif

    <!-- Media Library Selection Modal -->
    <x-modal wire:model="showModal" class="backdrop-blur !z-[999999]" box-class="max-w-5xl w-full h-[85vh] overflow-y-auto !z-[999999]">
        <div class="relative h-full">
            <livewire:admin.media.index 
                :select-mode="true" 
                select-event="media-selected" 
                :target-field="$targetField" 
                :folder-filter="$folder" />
        </div>
    </x-modal>
</div>
