<div class="p-6">
    <!-- HEADER -->
    <x-header title="Device Page Galleries" subtitle="Manage and reorder hearing aid images displayed on device landing pages" separator progress-indicator />

    <!-- DUMMY/HEADLESS MEDIA SELECTOR -->
    <livewire:admin.components.media-selector target-field="gallery-add" :headless="true" />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- LEFT SIDEBAR: STYLES LIST -->
        <div class="lg:col-span-1 space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-base-content/50 px-3">Device Styles</span>
            <div class="flex flex-col bg-base-100 border border-base-300 rounded-lg overflow-hidden divide-y divide-base-200 shadow-sm">
                @foreach($styles as $slug => $label)
                    <button type="button" 
                            wire:click="setStyleSlug('{{ $slug }}')" 
                            class="flex items-center justify-between px-4 py-3 text-left text-sm transition duration-150 {{ $styleSlug === $slug ? 'bg-primary text-white font-semibold' : 'hover:bg-base-200 text-base-content/80' }}">
                        <span>{{ $label }}</span>
                        <x-icon name="o-chevron-right" class="w-4 h-4 opacity-50" />
                    </button>
                @endforeach
            </div>
        </div>

        <!-- RIGHT SIDE: ACTIVE GALLERY IMAGES -->
        <div class="lg:col-span-3">
            <div class="bg-base-100 border border-base-300 rounded-xl shadow-sm overflow-hidden">
                <!-- PANEL HEADER -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border-b border-base-200 bg-base-200/20 gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-base-content">{{ $styles[$styleSlug] }}</h3>
                        <p class="text-xs text-base-content/60">Images will appear on the <a href="/{{ $styleSlug }}" target="_blank" class="link link-primary font-medium hover:underline">/{{ $styleSlug }}</a> page</p>
                    </div>
                    <button type="button" 
                            @click="$dispatch('open-media-selector-gallery-add')" 
                            class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-sm">
                        <x-icon name="o-plus" class="w-4.5 h-4.5" />
                        Add Image
                    </button>
                </div>

                <!-- GALLERY ITEMS -->
                <div class="p-6">
                    @if($galleryItems->isEmpty())
                        <div class="text-center py-12 border-2 border-dashed border-base-300 rounded-xl max-w-md mx-auto">
                            <x-icon name="o-photo" class="w-12 h-12 mx-auto text-base-content/30 mb-2" />
                            <h4 class="font-bold text-base-content/80">No images added yet</h4>
                            <p class="text-xs text-base-content/50 mt-1 mb-4">Add product photos or hearing aid models for the client to view on the frontend.</p>
                            <button type="button" 
                                    @click="$dispatch('open-media-selector-gallery-add')" 
                                    class="btn btn-outline btn-primary btn-sm flex items-center gap-1 mx-auto">
                                <x-icon name="o-folder-open" class="w-4 h-4" /> Browse Media Library
                            </button>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            @foreach($galleryItems as $index => $item)
                                <div class="group relative flex flex-col bg-base-100 border border-base-300 rounded-xl overflow-hidden shadow-sm hover:shadow transition duration-150">
                                    <!-- Image Preview -->
                                    <div class="aspect-video bg-base-200 overflow-hidden flex items-center justify-center border-b border-base-200 relative">
                                        <img src="{{ $item->media->url }}" alt="{{ $item->media->original_filename }}" class="object-contain w-full h-full max-h-40 p-2" />
                                        
                                        <!-- Featured Overlay Button -->
                                        <button type="button" 
                                                wire:click="makeFeatured({{ $item->id }})" 
                                                class="absolute top-2 right-2 p-1.5 rounded-full shadow-md backdrop-blur transition-all duration-200 {{ $item->is_featured ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-black/35 hover:bg-black/60 text-white/90 hover:text-white' }}"
                                                title="{{ $item->is_featured ? 'Featured Image' : 'Set as Featured Image' }}">
                                            <x-icon name="{{ $item->is_featured ? 's-star' : 'o-star' }}" class="w-4 h-4" />
                                        </button>
                                    </div>

                                    <!-- Meta / Info -->
                                    <div class="p-3 text-xs flex-1 flex flex-col justify-between">
                                        <div>
                                            <p class="font-semibold truncate text-base-content/80" title="{{ $item->media->original_filename }}">
                                                {{ $item->media->original_filename }}
                                            </p>
                                            <div class="flex justify-between items-center text-[10px] text-base-content/50 mt-1">
                                                <span>{{ $item->media->width }}x{{ $item->media->height }} px</span>
                                                <span>{{ number_format($item->media->size / 1024, 1) }} KB</span>
                                            </div>
                                        </div>

                                        <!-- Ordering & Deletion Controls -->
                                        <div class="flex justify-between items-center border-t border-base-100 pt-3 mt-3 gap-2">
                                            <div class="join">
                                                <button type="button" 
                                                        wire:click="moveUp({{ $item->id }})" 
                                                        class="btn btn-ghost btn-xs btn-square border border-base-300 join-item" 
                                                        title="Move Left/Up"
                                                        {{ $index === 0 ? 'disabled' : '' }}>
                                                    <x-icon name="o-chevron-left" class="w-3.5 h-3.5" />
                                                </button>
                                                <button type="button" 
                                                        wire:click="moveDown({{ $item->id }})" 
                                                        class="btn btn-ghost btn-xs btn-square border border-base-300 join-item" 
                                                        title="Move Right/Down"
                                                        {{ $index === count($galleryItems) - 1 ? 'disabled' : '' }}>
                                                    <x-icon name="o-chevron-right" class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                            <span class="text-[10px] font-bold text-neutral-content/60 bg-base-200 px-2 py-0.5 rounded-full">
                                                Order: #{{ $item->sort_order }}
                                            </span>
                                            <button type="button" 
                                                    wire:click="removeImage({{ $item->id }})" 
                                                    wire:confirm="Remove this image from this style gallery? The image will remain in your Media Library."
                                                    class="btn btn-ghost btn-xs btn-square border border-base-300 text-error hover:bg-error/10" 
                                                    title="Remove Image">
                                                <x-icon name="o-trash" class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
