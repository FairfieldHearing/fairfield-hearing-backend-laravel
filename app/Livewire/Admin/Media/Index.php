<?php

namespace App\Livewire\Admin\Media;

use App\Models\Media;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use Toast, WithFileUploads, WithPagination;

    // Component parameters
    public bool $selectMode = false;
    public string $selectEvent = 'media-selected';
    public string $targetField = ''; // Optional extra parameter for multiple selector inputs

    // Filters & search
    public string $search = '';
    public string $folderFilter = '';
    public string $viewMode = 'grid';
    public int $perPage = 20;

    // Upload
    public $newImage;

    // Details modal
    public ?Media $viewingMedia = null;
    public array $viewingUsage = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'folderFilter' => ['except' => ''],
        'viewMode' => ['except' => 'grid'],
        'perPage' => ['except' => 20],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFolderFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function mount(bool $selectMode = false, string $selectEvent = 'media-selected', string $targetField = '')
    {
        // Standalone page access check
        if (!$this->selectMode) {
            Gate::authorize('manage-blogs');
        }
        $this->selectMode = $selectMode;
        $this->selectEvent = $selectEvent;
        $this->targetField = $targetField;
    }

    public function updatedNewImage()
    {
        $this->validate([
            'newImage' => 'required|image|max:8192', // up to 8MB
        ]);

        try {
            $folder = $this->folderFilter ?: 'general';
            $media = Media::upload($this->newImage, $folder);
            
            $this->reset('newImage');
            $this->success('Image uploaded and optimized successfully.', position: 'toast-bottom');
            
            if ($this->selectMode) {
                $this->selectMedia($media->id);
            }
        } catch (\Throwable $e) {
            $this->error('Failed to upload/compress image: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function selectMedia($mediaId)
    {
        $media = Media::find($mediaId);
        if ($media) {
            $this->dispatch($this->selectEvent, [
                'filepath' => $media->filepath,
                'url' => $media->url,
                'targetField' => $this->targetField
            ]);
        }
    }

    public function viewDetails($mediaId)
    {
        $this->viewingMedia = Media::find($mediaId);
        if ($this->viewingMedia) {
            $this->viewingUsage = $this->viewingMedia->getUsageInfo();
        }
    }

    public function closeDetails()
    {
        $this->reset(['viewingMedia', 'viewingUsage']);
    }

    public function deleteMedia($mediaId)
    {
        $media = Media::findOrFail($mediaId);
        $usage = $media->getUsageInfo();

        if (count($usage) > 0) {
            $this->error('Cannot delete: Image is currently in use in ' . count($usage) . ' place(s).', position: 'toast-bottom');
            return;
        }

        try {
            if (Storage::disk('public')->exists($media->filepath)) {
                Storage::disk('public')->delete($media->filepath);
            }
            $media->delete();
            $this->closeDetails();
            $this->success('Image deleted successfully.', position: 'toast-bottom');
        } catch (\Throwable $e) {
            $this->error('Error deleting image: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        $query = Media::query()->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('filename', 'LIKE', '%' . $this->search . '%')
                  ->orWhere('original_filename', 'LIKE', '%' . $this->search . '%');
            });
        }

        if ($this->folderFilter) {
            $query->where('folder', $this->folderFilter);
        }

        $mediaItems = $query->paginate($this->perPage);

        $folders = [
            'blog_posts' => 'Blog Posts',
            'categories' => 'Blog Categories',
            'manufacturers' => 'Manufacturers',
            'tinymce' => 'Editor Images',
            'general' => 'General / Other',
        ];

        return view('livewire.admin.media.index', [
            'mediaItems' => $mediaItems,
            'folders' => $folders
        ])->layout($this->selectMode ? 'layouts.empty' : 'layouts.app');
    }
}
