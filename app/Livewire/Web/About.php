<?php

namespace App\Livewire\Web;

use App\Models\TeamMember;
use Livewire\Component;
use App\Traits\HasSeo;

class About extends Component
{
    use HasSeo;

    public function render()
    {
        $team = TeamMember::orderBy('id')->get()->toArray();
        
        $directors = array_filter($team, fn($m) => $m['category'] === 'director');
        $entSpecialists = array_filter($team, fn($m) => $m['category'] === 'ent_specialist');
        $audiologists = array_filter($team, fn($m) => $m['category'] === 'audiologist');
        $productSpecialists = array_filter($team, fn($m) => $m['category'] === 'product_specialist');

        $aboutSchema = [
            "@context" => "https://schema.org",
            "@type" => "AboutPage",
            "name" => "About Us | Fairfield Hearing Clinics",
            "description" => "Learn about Fairfield Hearing Clinics' mission, values, ENT specialists, and RCI-certified audiologists providing professional, transparent hearing care in Delhi.",
            "mainEntity" => [
                "@type" => "MedicalOrganization",
                "name" => "Fairfield Hearing Clinics",
                "employee" => collect($team)->map(fn($m) => [
                    "@type" => $m['category'] === 'ent_specialist' ? 'Physician' : 'MedicalBusiness',
                    "name" => $m['name'],
                    "jobTitle" => $m['role'],
                    "image" => "https://fairfieldhearing.in/" . $m['photo']
                ])->toArray()
            ]
        ];

        return view('livewire.web.about', [
            'directors' => $directors,
            'entSpecialists' => $entSpecialists,
            'audiologists' => $audiologists,
            'productSpecialists' => $productSpecialists,
            'aboutSchema' => $aboutSchema
        ])->layout('layouts.web', $this->seo('about'));
    }
}
