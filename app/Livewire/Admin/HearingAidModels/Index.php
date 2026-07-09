<?php

namespace App\Livewire\Admin\HearingAidModels;

use Mary\Traits\Toast;
use Livewire\Component;
use App\Models\HearingAidModel;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use Toast;

    public string $search = '';
    public ?string $filterBrand = '';
    public array $sortBy = ['column' => 'sort_order', 'direction' => 'asc'];

    // Form fields
    public ?HearingAidModel $modelInstance = null;
    public ?int $manufacturer_id = null;
    public string $name = '';
    public int $mrp = 0;
    public float $discount_pct = 0;
    public string $tech_level = 'Standard';
    public string $form_factor = 'RIC';
    public int $units = 1;
    public array $key_features = [];
    public string $tags_text = '';
    public bool $is_active = true;

    public bool $drawer = false;

    public function mount()
    {
        Gate::authorize('manage-content');
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->modelInstance = null;
        $this->reset(['manufacturer_id', 'name', 'mrp', 'discount_pct', 'tech_level', 'form_factor', 'units', 'key_features', 'tags_text', 'is_active']);
        $this->is_active = true;
        $this->tech_level = 'Standard';
        $this->form_factor = 'RIC';
        $this->units = 1;
        $this->key_features = ['']; // Start with one empty slot
        $this->drawer = true;
    }

    public function showEdit(HearingAidModel $model): void
    {
        $this->resetValidation();
        $this->modelInstance = $model;
        $this->manufacturer_id = $model->manufacturer_id;
        $this->name = $model->name;
        $this->mrp = $model->mrp;
        $this->discount_pct = $model->discount_pct;
        $this->tech_level = $model->tech_level;
        $this->form_factor = $model->form_factor;
        $this->units = $model->units;
        $this->key_features = $model->key_features ?? [''];
        $this->tags_text = is_array($model->tags) ? implode(', ', $model->tags) : '';
        $this->is_active = $model->is_active;
        $this->drawer = true;
    }

    public function addFeature(): void
    {
        $this->key_features[] = '';
    }

    public function removeFeature(int $index): void
    {
        unset($this->key_features[$index]);
        $this->key_features = array_values($this->key_features);
    }

    public function save(): void
    {
        $this->validate([
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'name' => 'required|string|max:255',
            'mrp' => 'required|integer|min:0',
            'discount_pct' => 'required|numeric|min:0|max:100',
            'tech_level' => 'required|string|max:255',
            'form_factor' => 'required|string|max:255',
            'units' => 'required|integer|in:1,2',
            'key_features' => 'array',
            'key_features.*' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        // Clean up empty features and tag inputs
        $features = array_filter(array_map('trim', $this->key_features));
        $tags = array_filter(array_map('trim', explode(',', $this->tags_text)));

        $data = [
            'manufacturer_id' => $this->manufacturer_id,
            'name' => $this->name,
            'mrp' => $this->mrp,
            'discount_pct' => $this->discount_pct,
            'tech_level' => $this->tech_level,
            'form_factor' => $this->form_factor,
            'units' => $this->units,
            'key_features' => array_values($features),
            'tags' => array_values($tags),
            'is_active' => $this->is_active,
        ];

        if ($this->modelInstance) {
            $this->modelInstance->update($data);
            $this->success('Hearing aid model updated successfully.', position: 'toast-bottom');
        } else {
            $maxOrder = HearingAidModel::max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;

            HearingAidModel::create($data);
            $this->success('Hearing aid model created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
        $this->reset(['manufacturer_id', 'name', 'mrp', 'discount_pct', 'tech_level', 'form_factor', 'units', 'key_features', 'tags_text', 'modelInstance']);
    }

    public function delete(HearingAidModel $model): void
    {
        $model->delete();
        $this->success('Hearing aid model deleted successfully.', position: 'toast-bottom');
    }

    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            HearingAidModel::where('id', $id)->update(['sort_order' => $index]);
        }
        $this->success('Models display order updated.', position: 'toast-bottom');
    }

    public function rows()
    {
        return HearingAidModel::query()
            ->with('manufacturer')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterBrand, function ($query) {
                $query->where('manufacturer_id', $this->filterBrand);
            })
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.hearing-aid-models.index', [
            'rows' => $this->rows(),
            'brands' => Manufacturer::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
