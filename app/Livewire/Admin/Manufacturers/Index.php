<?php

namespace App\Livewire\Admin\Manufacturers;

use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Helpers\ImageHelper;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use Toast, WithFileUploads;

    public string $search = '';
    public array $sortBy = ['column' => 'sort_order', 'direction' => 'asc'];

    // Form fields
    public ?Manufacturer $manufacturer = null;
    public string $name = '';
    public $logo = null;
    public bool $is_active = true;
    public bool $show_on_homepage = true;

    public bool $drawer = false;

    public function mount()
    {
        Gate::authorize('manage-content');
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->manufacturer = null;
        $this->reset(['name', 'logo', 'is_active', 'show_on_homepage']);
        $this->is_active = true;
        $this->show_on_homepage = true;
        $this->drawer = true;
    }

    public function showEdit(Manufacturer $manufacturer): void
    {
        $this->resetValidation();
        $this->manufacturer = $manufacturer;
        $this->name = $manufacturer->name;
        $this->is_active = $manufacturer->is_active;
        $this->show_on_homepage = (bool)$manufacturer->show_on_homepage;
        $this->logo = $manufacturer->logo_path;
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'show_on_homepage' => 'boolean',
            'logo' => 'required|string|max:255',
        ];

        $this->validate($rules);

        $logoMediaId = null;
        if ($this->logo) {
            $media = \App\Models\Media::where('filepath', $this->logo)->first();
            if ($media) {
                $logoMediaId = $media->id;
            }
        }

        $data = [
            'name' => $this->name,
            'is_active' => $this->is_active,
            'show_on_homepage' => $this->show_on_homepage,
            'logo_path' => $this->logo,
            'logo_media_id' => $logoMediaId,
        ];

        if ($this->manufacturer) {
            $this->manufacturer->update($data);
            $this->success('Manufacturer updated successfully.', position: 'toast-bottom');
        } else {
            $maxOrder = Manufacturer::max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;

            Manufacturer::create($data);
            $this->success('Manufacturer created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
        $this->reset(['logo', 'manufacturer', 'name', 'is_active', 'show_on_homepage']);
    }

    public function delete(Manufacturer $manufacturer): void
    {
        // Delete logo file if it was uploaded
        if ($manufacturer->logo_path && !str_starts_with($manufacturer->logo_path, 'assets/')) {
            Storage::disk('public')->delete($manufacturer->logo_path);
        }
        $manufacturer->delete();
        $this->success('Manufacturer deleted successfully.', position: 'toast-bottom');
    }

    public function toggleActive(int $id): void
    {
        $m = Manufacturer::findOrFail($id);
        $m->update(['is_active' => !$m->is_active]);
        $this->success('Brand status updated.', position: 'toast-bottom');
    }

    public function toggleHomepage(int $id): void
    {
        $m = Manufacturer::findOrFail($id);
        $m->update(['show_on_homepage' => !$m->show_on_homepage]);
        $this->success('Homepage visibility updated.', position: 'toast-bottom');
    }

    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Manufacturer::where('id', $id)->update(['sort_order' => $index]);
        }
        $this->success('Manufacturer display order updated.', position: 'toast-bottom');
    }

    public function manufacturers()
    {
        return Manufacturer::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    public function with(): array
    {
        return [
            'rows' => $this->manufacturers(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.manufacturers.index', $this->with())->layout('layouts.app');
    }
}
