@props(['gallery', 'default', 'alt' => 'Hearing aid model'])

<div style="width: 100%; height: auto; position: relative;">
    @if(count($gallery) === 1)
        <!-- Single Image: Shadow directly on the image, matched width, 4:3 aspect ratio, object-fit cover -->
        <img src="{{ $gallery[0]->media->url }}" alt="{{ $alt }}" style="width: 100%; aspect-ratio: 4/3; object-fit: cover; object-position: center; border-radius: var(--radius); box-shadow: 0 15px 35px rgba(0,0,0,0.12);" />
    @elseif(count($gallery) > 1)
        <!-- AlpineJS Slideshow: Shadow on the slideshow container, matched width, 4:3 aspect ratio, object-fit cover -->
        <div x-data="{ active: 0, itemsCount: {{ count($gallery) }} }" class="relative w-full" style="width: 100%; aspect-ratio: 4/3; position: relative; border-radius: var(--radius); box-shadow: 0 15px 35px rgba(0,0,0,0.12); background: transparent;">
            <div class="w-full h-full flex items-center justify-center" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: transparent; overflow: hidden; border-radius: var(--radius);">
                @php
                    // Sort gallery items by is_featured DESC then sort_order ASC
                    $sortedGallery = collect($gallery)->sortByDesc('is_featured')->values()->all();
                @endphp
                @foreach($sortedGallery as $index => $item)
                    <img x-show="active === {{ $index }}" src="{{ $item->media->url }}" alt="{{ $alt }}" style="width: 100%; height: 100%; object-fit: cover; object-position: center; {{ $index === 0 ? '' : 'display: none;' }}" />
                @endforeach
            </div>
            <!-- Slideshow Buttons: Larger icon, padding removed, 85% transparent background (15% opacity) -->
            <button type="button" @click="active = (active === 0 ? itemsCount - 1 : active - 1)" class="absolute left-2 top-1/2 -translate-y-1/2 text-white hover:text-neutral-200 transition" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.15); color: #fff; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: none; padding: 0; cursor: pointer; z-index: 10;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </button>
            <button type="button" @click="active = (active === itemsCount - 1 ? 0 : active + 1)" class="absolute right-2 top-1/2 -translate-y-1/2 text-white hover:text-neutral-200 transition" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.15); color: #fff; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: none; padding: 0; cursor: pointer; z-index: 10;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16" style="transform: rotate(180deg);">
                    <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </button>
        </div>
    @else
        <!-- Fallback Illustration: Wrap in media-frame class to match default layout styling -->
        <div class="media-frame">
            <img src="{{ $default }}" alt="{{ $alt }}" style="width: 100%; border-radius: var(--radius);" >
        </div>
    @endif
</div>
