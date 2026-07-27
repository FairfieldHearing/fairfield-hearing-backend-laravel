<?php

namespace App\Livewire\Admin\TeamMembers;

use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\TeamMember;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use Toast, WithFileUploads, WithPagination;

    public string $search = '';
    public array $sortBy = ['column' => 'sort_order', 'direction' => 'asc'];

    // Form fields
    public ?TeamMember $teamMember = null;
    public string $name = '';
    public string $slug = '';
    public string $role = '';
    public string $category = 'audiologist';
    public ?string $eyebrow = null;
    public ?string $photo = null;
    public ?string $short_bio = null;
    public array $at_a_glance = [];
    public array $areas_of_expertise = [];
    public ?string $blockquote = null;
    public ?string $bio = null;
    public array $timeline = [];
    public ?string $meta_title = null;
    public ?string $meta_description = null;
    public int $sort_order = 0;

    public bool $drawer = false;

    public function mount()
    {
        Gate::authorize('manage-content');
    }

    public function updatedName($value): void
    {
        if (!$this->teamMember) {
            $this->slug = str($value)->slug();
        }
    }

    // At a Glance repeaters
    public function addGlanceItem(): void
    {
        $this->at_a_glance[] = ['label' => '', 'value' => ''];
    }

    public function removeGlanceItem(int $index): void
    {
        unset($this->at_a_glance[$index]);
        $this->at_a_glance = array_values($this->at_a_glance);
    }

    // Areas of Expertise repeaters
    public function addExpertiseItem(): void
    {
        $this->areas_of_expertise[] = '';
    }

    public function removeExpertiseItem(int $index): void
    {
        unset($this->areas_of_expertise[$index]);
        $this->areas_of_expertise = array_values($this->areas_of_expertise);
    }

    // Timeline repeaters
    public function addTimelineItem(): void
    {
        $this->timeline[] = ['year' => '', 'text' => ''];
    }

    public function removeTimelineItem(int $index): void
    {
        unset($this->timeline[$index]);
        $this->timeline = array_values($this->timeline);
    }

    public function showCreate(): void
    {
        $this->resetValidation();
        $this->teamMember = null;
        $this->reset([
            'name', 'slug', 'role', 'category', 'eyebrow', 'photo', 
            'short_bio', 'at_a_glance', 'areas_of_expertise', 
            'blockquote', 'bio', 'timeline', 'meta_title', 'meta_description', 'sort_order'
        ]);
        $this->category = 'audiologist';
        $this->at_a_glance = [];
        $this->areas_of_expertise = [];
        $this->timeline = [];
        $this->drawer = true;
    }

    public function showEdit(TeamMember $teamMember): void
    {
        $this->resetValidation();
        $this->teamMember = $teamMember;
        $this->name = $teamMember->name;
        $this->slug = $teamMember->slug;
        $this->role = $teamMember->role;
        $this->category = $teamMember->category;
        $this->eyebrow = $teamMember->eyebrow;
        $this->photo = $teamMember->photo;
        $this->short_bio = $teamMember->short_bio;
        
        $glance = $teamMember->at_a_glance;
        if (is_string($glance)) {
            $glance = json_decode($glance, true) ?: [];
        }
        $formattedGlance = [];
        if (is_array($glance)) {
            foreach ($glance as $gItem) {
                if (is_string($gItem) && str_contains($gItem, ':')) {
                    $parts = explode(':', $gItem, 2);
                    $formattedGlance[] = ['label' => trim($parts[0]), 'value' => trim($parts[1])];
                } elseif (is_array($gItem) && isset($gItem['label'])) {
                    $formattedGlance[] = $gItem;
                } else {
                    $formattedGlance[] = ['label' => '', 'value' => (string) $gItem];
                }
            }
        }
        $this->at_a_glance = $formattedGlance;

        $expertise = $teamMember->areas_of_expertise;
        if (is_string($expertise)) {
            $expertise = json_decode($expertise, true) ?: [];
        }
        $this->areas_of_expertise = is_array($expertise) ? array_values($expertise) : [];

        $this->blockquote = $teamMember->blockquote;
        
        // Convert HTML paragraphs to plain text line breaks for editing
        $rawBio = $teamMember->bio ?? '';
        if ($rawBio) {
            $cleanBio = preg_replace('/<\/p>\s*<p>/i', "\n\n", $rawBio);
            $cleanBio = preg_replace('/<\/?p>/i', '', $cleanBio);
            $this->bio = trim($cleanBio);
        } else {
            $this->bio = '';
        }

        $t = $teamMember->timeline;
        if (is_string($t)) {
            $t = json_decode($t, true) ?: [];
        }
        $this->timeline = is_array($t) ? array_values($t) : [];

        $this->meta_title = $teamMember->meta_title;
        $this->meta_description = $teamMember->meta_description;
        $this->sort_order = $teamMember->sort_order;
        $this->drawer = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:team_members,slug,' . ($this->teamMember?->id ?? 'NULL'),
            'role' => 'required|string|max:255',
            'category' => 'required|string|in:director,ent_specialist,audiologist,product_specialist',
            'eyebrow' => 'nullable|string|max:255',
            'photo' => 'nullable|string|max:255',
            'short_bio' => 'nullable|string',
            'at_a_glance' => 'nullable|array',
            'at_a_glance.*.label' => 'nullable|string',
            'at_a_glance.*.value' => 'nullable|string',
            'areas_of_expertise' => 'nullable|array',
            'areas_of_expertise.*' => 'nullable|string',
            'blockquote' => 'nullable|string',
            'bio' => 'nullable|string',
            'timeline' => 'nullable|array',
            'timeline.*.year' => 'nullable|string',
            'timeline.*.text' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'sort_order' => 'required|integer',
        ];

        $this->validate($rules);

        // Filter out empty rows and format as "Label: Value"
        $cleanGlance = [];
        foreach ($this->at_a_glance as $g) {
            $lbl = trim($g['label'] ?? '');
            $val = trim($g['value'] ?? '');
            if ($lbl !== '' || $val !== '') {
                $cleanGlance[] = $lbl !== '' ? "{$lbl}: {$val}" : $val;
            }
        }
        $cleanExpertise = array_values(array_filter($this->areas_of_expertise, fn($v) => !empty(trim($v))));
        $cleanTimeline = array_values(array_filter($this->timeline, fn($v) => !empty(trim($v['year'] ?? '')) || !empty(trim($v['text'] ?? ''))));

        // Convert line breaks in plain text bio into clean HTML paragraphs
        $formattedBio = null;
        if (!empty(trim($this->bio ?? ''))) {
            $paragraphs = array_filter(array_map('trim', explode("\n\n", str_replace("\r\n", "\n", $this->bio))));
            if (!empty($paragraphs)) {
                $formattedBio = implode('', array_map(fn($p) => '<p>' . nl2br($p) . '</p>', $paragraphs));
            } else {
                $formattedBio = '<p>' . nl2br(trim($this->bio)) . '</p>';
            }
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'role' => $this->role,
            'category' => $this->category,
            'eyebrow' => $this->eyebrow,
            'photo' => $this->photo,
            'short_bio' => $this->short_bio,
            'at_a_glance' => $cleanGlance,
            'areas_of_expertise' => $cleanExpertise,
            'blockquote' => $this->blockquote,
            'bio' => $formattedBio,
            'timeline' => $cleanTimeline,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'sort_order' => $this->sort_order,
        ];

        if ($this->teamMember && $this->teamMember->exists) {
            $this->teamMember->update($data);
            $this->success('Team member updated successfully.', position: 'toast-bottom');
        } else {
            TeamMember::create($data);
            $this->success('Team member created successfully.', position: 'toast-bottom');
        }

        $this->drawer = false;
    }

    public function delete(TeamMember $teamMember): void
    {
        $teamMember->delete();
        $this->success('Team member deleted.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-16'],
            ['key' => 'photo', 'label' => 'Photo', 'class' => 'w-20', 'sortable' => false],
            ['key' => 'name', 'label' => 'Name & Role'],
            ['key' => 'category', 'label' => 'Category'],
            ['key' => 'sort_order', 'label' => 'Sort Order', 'class' => 'w-24'],
        ];
    }

    public function teamMembers()
    {
        return TeamMember::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('role', 'like', "%{$this->search}%"))
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.team-members.index', [
            'members' => $this->teamMembers(),
            'headers' => $this->headers(),
            'categories' => [
                ['id' => 'director', 'name' => 'Director'],
                ['id' => 'ent_specialist', 'name' => 'ENT Specialist'],
                ['id' => 'audiologist', 'name' => 'Audiologist'],
                ['id' => 'product_specialist', 'name' => 'Product Specialist'],
            ]
            ]);
    }
}
