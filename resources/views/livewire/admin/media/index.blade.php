<div class="{{ $selectMode ? '' : 'p-6' }}">
    <!-- HEADER -->
    @if(!$selectMode)
        <x-header title="Media Manager" subtitle="Manage and verify images used across the website" separator progress-indicator>
            <x-slot:actions>
                <div class="flex items-center gap-3">
                    <label class="btn btn-primary btn-sm flex items-center gap-1.5 cursor-pointer">
                        <x-icon name="o-arrow-up-tray" class="w-4 h-4" />
                        Upload Image
                        <input type="file" wire:model="newImage" accept="image/*" class="hidden" />
                    </label>
                </div>
            </x-slot:actions>
        </x-header>
    @else
        <div class="flex justify-between items-center pb-4 mb-4 border-b border-base-300">
            <div>
                <h3 class="text-lg font-bold">Select Media</h3>
                <p class="text-xs text-base-content/60">Choose an image from the library or upload a new one</p>
            </div>
            <label class="btn btn-primary btn-sm flex items-center gap-1.5 cursor-pointer">
                <x-icon name="o-arrow-up-tray" class="w-4 h-4" />
                Upload Image
                <input type="file" wire:model="newImage" accept="image/*" class="hidden" />
            </label>
        </div>
    @endif

    <!-- FILTERS AND SEARCH -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6 items-end">
        <div class="md:col-span-5">
            <x-input label="Search by filename" wire:model.live.debounce.300ms="search" placeholder="Type filename..." icon="o-magnifying-glass" clearable />
        </div>
        <div class="md:col-span-3">
            <x-select label="Filter by Folder" wire:model.live="folderFilter" :options="[
                ['id' => '', 'name' => 'All Folders'],
                ['id' => 'blog_posts', 'name' => 'Blog Posts'],
                ['id' => 'categories', 'name' => 'Blog Categories'],
                ['id' => 'manufacturers', 'name' => 'Manufacturers'],
                ['id' => 'tinymce', 'name' => 'Editor Images'],
                ['id' => 'general', 'name' => 'General / Other']
            ]" option-value="id" option-label="name" />
        </div>
        <div class="md:col-span-2">
            <x-select label="Items per page" wire:model.live="perPage" :options="[
                ['id' => 10, 'name' => '10 Items'],
                ['id' => 20, 'name' => '20 Items'],
                ['id' => 50, 'name' => '50 Items'],
                ['id' => 100, 'name' => '100 Items']
            ]" option-value="id" option-label="name" />
        </div>
        <div class="md:col-span-2 flex flex-col justify-end">
            <span class="label-text font-semibold mb-2 block">View Mode</span>
            <div class="join w-full">
                <button type="button" wire:click="$set('viewMode', 'grid')" class="btn btn-sm join-item flex-1 {{ $viewMode === 'grid' ? 'btn-primary' : 'btn-outline' }}">
                    <x-icon name="o-squares-2x2" class="w-4 h-4" /> Grid
                </button>
                <button type="button" wire:click="$set('viewMode', 'table')" class="btn btn-sm join-item flex-1 {{ $viewMode === 'table' ? 'btn-primary' : 'btn-outline' }}">
                    <x-icon name="o-list-bullet" class="w-4 h-4" /> Table
                </button>
            </div>
        </div>
    </div>

    <!-- UPLOAD INDICATOR -->
    <div wire:loading wire:target="newImage" class="w-full bg-base-200 border border-base-300 rounded-lg p-4 text-center mb-6">
        <span class="loading loading-spinner loading-md align-middle mr-2"></span>
        <span class="text-sm font-semibold align-middle">Optimizing, compressing, and saving image...</span>
    </div>

    <!-- MEDIA CONTENT -->
    @if($mediaItems->isEmpty())
        <div class="text-center py-12 bg-base-100 rounded-xl border border-dashed border-base-300">
            <x-icon name="o-photo" class="w-12 h-12 mx-auto text-base-content/30 mb-2" />
            <p class="text-base-content/60">No images found matching your criteria.</p>
        </div>
    @else
        @if($viewMode === 'grid')
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($mediaItems as $media)
                    <div class="group relative flex flex-col bg-base-100 border border-base-300 rounded-lg overflow-hidden shadow-sm hover:shadow-md hover:border-primary/50 transition duration-200">
                        <!-- Image Preview Area -->
                        <div class="aspect-square bg-base-200 flex items-center justify-center overflow-hidden cursor-pointer relative" 
                             wire:click="{{ $selectMode ? 'selectMedia(' . $media->id . ')' : 'viewDetails(' . $media->id . ')' }}">
                            <img src="{{ $media->url }}" alt="{{ $media->original_filename }}" class="object-cover w-full h-full group-hover:scale-105 transition duration-300" />
                            
                            <!-- Badges/Overlay -->
                            <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 bg-neutral/80 backdrop-blur text-[10px] font-bold text-white rounded uppercase tracking-wider">
                                {{ str_replace('_', ' ', $media->folder) }}
                            </span>
                            
                            @if($selectMode)
                                <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 flex items-center justify-center transition duration-200">
                                    <span class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">Select</span>
                                </div>
                            @endif
                        </div>

                        <!-- Metadata Footer -->
                        <div class="p-2 border-t border-base-200 text-xs flex-1 flex flex-col justify-between">
                            <p class="font-medium truncate text-base-content/80" title="{{ $media->original_filename }}">
                                {{ $media->original_filename }}
                            </p>
                            <div class="flex justify-between items-center text-[10px] text-base-content/50 mt-1">
                                <span>
                                    @if($media->width && $media->height)
                                        {{ $media->width }}x{{ $media->height }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                                <span>{{ number_format($media->size / 1024, 1) }} KB</span>
                            </div>
                        </div>

                        <!-- Actions Button Bar for Non-Select Mode -->
                        @if(!$selectMode)
                            <div class="absolute bottom-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition duration-150 bg-base-100/90 backdrop-blur rounded p-1 shadow border border-base-200">
                                <button wire:click="viewDetails({{ $media->id }})" class="btn btn-ghost btn-xs p-1 text-primary" title="Details / Usage">
                                    <x-icon name="o-eye" class="w-3.5 h-3.5" />
                                </button>
                                <button wire:click="viewDetails({{ $media->id }})" class="btn btn-ghost btn-xs p-1 text-error" title="Delete">
                                    <x-icon name="o-trash" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <!-- Table View -->
            <div class="overflow-x-auto bg-base-100 border border-base-300 rounded-lg shadow-sm">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Original Filename</th>
                            <th>Folder</th>
                            <th>Dimensions</th>
                            <th>File Size</th>
                            <th>Uploaded On</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mediaItems as $media)
                            <tr class="hover:bg-base-200/50 transition">
                                <td>
                                    <div class="w-12 h-12 rounded overflow-hidden bg-base-200 border border-base-300 flex items-center justify-center cursor-pointer"
                                         wire:click="{{ $selectMode ? 'selectMedia(' . $media->id . ')' : 'viewDetails(' . $media->id . ')' }}">
                                        <img src="{{ $media->url }}" alt="{{ $media->original_filename }}" class="object-cover w-full h-full" />
                                    </div>
                                </td>
                                <td class="font-medium truncate max-w-[200px] text-sm" title="{{ $media->original_filename }}">
                                    {{ $media->original_filename }}
                                </td>
                                <td>
                                    <span class="badge badge-neutral uppercase tracking-wider text-[9px] font-bold">
                                        {{ str_replace('_', ' ', $media->folder) }}
                                    </span>
                                </td>
                                <td class="text-sm">
                                    {{ $media->width && $media->height ? $media->width . 'x' . $media->height : 'N/A' }}
                                </td>
                                <td class="text-sm">
                                    {{ number_format($media->size / 1024, 1) }} KB
                                </td>
                                <td class="text-sm">
                                    {{ $media->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="text-right">
                                    @if($selectMode)
                                        <button wire:click="selectMedia({{ $media->id }})" class="btn btn-primary btn-xs">
                                            Select
                                        </button>
                                    @else
                                        <div class="flex justify-end gap-1">
                                            <button wire:click="viewDetails({{ $media->id }})" class="btn btn-ghost btn-xs text-primary" title="Details / Usage">
                                                <x-icon name="o-eye" class="w-4 h-4" />
                                            </button>
                                            <button wire:click="viewDetails({{ $media->id }})" class="btn btn-ghost btn-xs text-error" title="Delete">
                                                <x-icon name="o-trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-8">
            {{ $mediaItems->links() }}
        </div>
    @endif

    <!-- DETAILS & USAGE MODAL -->
    @if($viewingMedia)
        <x-modal wire:model="viewingMedia" class="backdrop-blur" title="Image Properties & Usage" separator>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Preview image & meta -->
                <div class="space-y-4">
                    <div class="aspect-video rounded-xl bg-base-200 overflow-hidden flex items-center justify-center border border-base-300">
                        <img src="{{ $viewingMedia->url }}" alt="{{ $viewingMedia->original_filename }}" class="max-w-full max-h-full object-contain" />
                    </div>
                    <div class="bg-base-200 p-4 rounded-xl space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Filename:</span>
                            <span class="font-medium truncate max-w-[200px]" title="{{ $viewingMedia->original_filename }}">{{ $viewingMedia->original_filename }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Saved Path:</span>
                            <span class="font-mono text-xs select-all">{{ $viewingMedia->filepath }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Mime Type:</span>
                            <span>{{ $viewingMedia->mime_type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Dimensions:</span>
                            <span>{{ $viewingMedia->width }} x {{ $viewingMedia->height }} px</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">File Size:</span>
                            <span>{{ number_format($viewingMedia->size / 1024, 1) }} KB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Uploaded On:</span>
                            <span>{{ $viewingMedia->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Usage tracer & actions -->
                <div class="space-y-4">
                    <h4 class="font-bold text-md border-b pb-2">Where is this image used?</h4>

                    @if(empty($viewingUsage))
                        <div class="alert alert-info bg-info/10 border-info/20 text-info text-xs p-3 rounded-lg flex items-start gap-2">
                            <x-icon name="o-information-circle" class="w-4 h-4 shrink-0 mt-0.5" />
                            <span>This image is orphan (not linked to any blog posts, categories, or manufacturers). It is safe to delete.</span>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-xs w-full">
                                <thead>
                                    <tr>
                                        <th>Entity Type</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($viewingUsage as $use)
                                        <tr>
                                            <td class="font-bold text-[10px] uppercase tracking-wider text-base-content/70">{{ $use['type'] }}</td>
                                            <td class="max-w-[150px] truncate">{{ $use['name'] }}</td>
                                            <td>
                                                <a href="{{ $use['url'] }}" target="_blank" class="btn btn-ghost btn-xs text-primary flex items-center gap-0.5 no-wire-navigate">
                                                    Edit <x-icon name="o-arrow-top-right-on-square" class="w-3 h-3" />
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="border-t border-base-200 pt-4 flex justify-between gap-3">
                        @if(empty($viewingUsage))
                            <button wire:click="deleteMedia({{ $viewingMedia->id }})" 
                                    wire:confirm="Are you sure you want to delete this image? This action cannot be undone."
                                    class="btn btn-error btn-outline btn-sm">
                                <x-icon name="o-trash" class="w-4 h-4" />
                                Delete Image
                            </button>
                        @else
                            <button class="btn btn-disabled btn-sm" disabled title="Cannot delete image while in use">
                                <x-icon name="o-trash" class="w-4 h-4" />
                                In Use (Cannot Delete)
                            </button>
                        @endif
                        <button wire:click="closeDetails" class="btn btn-ghost btn-sm">Close</button>
                    </div>
                </div>
            </div>
        </x-modal>
    @endif
</div>
