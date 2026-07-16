<?php

namespace App\Livewire\Admin\Media;

use App\Models\DeviceGallery;
use App\Models\Media;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Gate;

class Galleries extends Component
{
    use Toast;

    #[Url]
    public string $styleSlug = 'ric';

    protected $queryString = [
        'styleSlug' => ['except' => 'ric'],
    ];

    public function mount()
    {
        Gate::authorize('manage-blogs');
    }

    public function setStyleSlug(string $slug)
    {
        if (in_array($slug, ['ric', 'bte', 'rechargeable', 'tinnitus', 'bluetooth', 'invisible'])) {
            $this->styleSlug = $slug;
        }
    }

    #[On('media-selected')]
    public function handleMediaSelected($data)
    {
        if (($data['targetField'] ?? '') === 'gallery-add') {
            $media = Media::where('filepath', $data['filepath'])->first();
            if ($media) {
                $exists = DeviceGallery::where('style_slug', $this->styleSlug)
                    ->where('media_id', $media->id)
                    ->exists();

                if (!$exists) {
                    $maxOrder = DeviceGallery::where('style_slug', $this->styleSlug)->max('sort_order') ?? 0;
                    $isFirst = !DeviceGallery::where('style_slug', $this->styleSlug)->exists();
                    DeviceGallery::create([
                        'style_slug' => $this->styleSlug,
                        'media_id' => $media->id,
                        'is_featured' => $isFirst,
                        'sort_order' => $maxOrder + 1,
                    ]);
                    $this->success('Image added to gallery.', position: 'toast-bottom');
                } else {
                    $this->warning('This image is already in the gallery.', position: 'toast-bottom');
                }
            }
        }
    }

    public function removeImage($galleryId)
    {
        $galleryItem = DeviceGallery::where('style_slug', $this->styleSlug)->where('id', $galleryId)->first();
        if ($galleryItem) {
            $wasFeatured = $galleryItem->is_featured;
            $galleryItem->delete();

            if ($wasFeatured) {
                $next = DeviceGallery::where('style_slug', $this->styleSlug)->orderBy('sort_order')->first();
                if ($next) {
                    $next->update(['is_featured' => true]);
                }
            }

            $this->success('Image removed from gallery.', position: 'toast-bottom');
        }
    }

    public function makeFeatured($galleryId)
    {
        DeviceGallery::where('style_slug', $this->styleSlug)->update(['is_featured' => false]);
        $item = DeviceGallery::where('style_slug', $this->styleSlug)->where('id', $galleryId)->first();
        if ($item) {
            $item->update(['is_featured' => true]);
            $this->success('Featured image updated successfully.', position: 'toast-bottom');
        }
    }

    public function moveUp($galleryId)
    {
        $items = DeviceGallery::where('style_slug', $this->styleSlug)->orderBy('sort_order')->get();
        $idx = $items->search(fn($item) => $item->id == $galleryId);
        if ($idx !== false && $idx > 0) {
            // Swap sort_order with previous item
            $current = $items[$idx];
            $prev = $items[$idx - 1];

            $temp = $current->sort_order;
            $current->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $temp]);
            $this->success('Order updated.', position: 'toast-bottom');
        }
    }

    public function moveDown($galleryId)
    {
        $items = DeviceGallery::where('style_slug', $this->styleSlug)->orderBy('sort_order')->get();
        $idx = $items->search(fn($item) => $item->id == $galleryId);
        if ($idx !== false && $idx < count($items) - 1) {
            // Swap sort_order with next item
            $current = $items[$idx];
            $next = $items[$idx + 1];

            $temp = $current->sort_order;
            $current->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $temp]);
            $this->success('Order updated.', position: 'toast-bottom');
        }
    }

    public function render()
    {
        $galleryItems = DeviceGallery::with('media')
            ->where('style_slug', $this->styleSlug)
            ->orderBy('sort_order')
            ->get();

        $styles = [
            'ric' => 'Receiver-in-Canal (RIC)',
            'bte' => 'Behind-the-Ear (BTE)',
            'rechargeable' => 'Rechargeable',
            'tinnitus' => 'Tinnitus Relief',
            'bluetooth' => 'Bluetooth Streaming',
            'invisible' => 'Invisible (IIC/CIC)',
        ];

        return view('livewire.admin.media.galleries', [
            'galleryItems' => $galleryItems,
            'styles' => $styles,
        ])->layout('layouts.app');
    }
}
