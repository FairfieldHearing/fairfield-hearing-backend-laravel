<?php

namespace App\Livewire\Web\Team;

use App\Models\TeamMember;
use Livewire\Component;
use App\Traits\HasSeo;

class Show extends Component
{
    use HasSeo;

    public $member;

    public function mount($slug)
    {
        $this->member = TeamMember::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        $m = $this->member;
        $glanceItems = is_string($m->at_a_glance) ? json_decode($m->at_a_glance, true) : $m->at_a_glance ?? [];
        $expertiseItems = is_string($m->areas_of_expertise) ? json_decode($m->areas_of_expertise, true) : $m->areas_of_expertise ?? [];
        $timelineItems = is_string($m->timeline) ? json_decode($m->timeline, true) : $m->timeline ?? [];

        $physicianSchema = [
            "@context" => "https://schema.org",
            "@type" => $m->category === "ent_specialist" ? "Physician" : "Audiologist",
            "name" => $m->name,
            "image" => "https://fairfieldhearing.in/" . $m->photo,
            "medicalSpecialty" => $m->category === "ent_specialist" ? "Otelaryngology" : "Audiology",
            "jobTitle" => $m->role,
            "description" => $m->short_bio,
            "knowsAbout" => $expertiseItems,
            "memberOf" => [
                "@type" => "MedicalOrganization",
                "name" => "Fairfield Hearing Clinics"
            ]
        ];

        return view('livewire.web.team.show', [
            'member' => $m->toArray(),
            'glanceItems' => $glanceItems,
            'expertiseItems' => $expertiseItems,
            'timelineItems' => $timelineItems,
            'physicianSchema' => $physicianSchema
        ])->layout('layouts.web', $this->seoForModel($m, $m->name . ' | Team Profile', $m->short_bio));
    }
}
