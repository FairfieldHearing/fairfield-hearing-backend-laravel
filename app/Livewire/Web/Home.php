<?php

namespace App\Livewire\Web;

use App\Models\Location;
use App\Models\Faq;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $locations = Location::all()->toArray();
        $faqs = Faq::where('type', 'general')->get()->toArray();
        
        $medicalBusinessSchema = [
            "@context" => "https://schema.org",
            "@type" => "MedicalBusiness",
            "name" => "Fairfield Hearing Clinics",
            "image" => "https://fairfieldhearing.in/assets/img/logo.jpeg",
            "telePhone" => "+91-9811418578",
            "email" => "info@fairfieldhearing.in",
            "priceRange" => "$$",
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => "New Friends Colony",
                "addressLocality" => "New Delhi",
                "addressRegion" => "Delhi",
                "postalCode" => "110025",
                "addressCountry" => "IN"
            ],
            "subOrganization" => collect($locations)->map(fn($loc) => [
                "@type" => "MedicalClinic",
                "name" => $loc['name'],
                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => $loc['address_line1'],
                    "addressLocality" => str_contains($loc['name'], 'Kaimganj') ? 'Kaimganj' : 'Delhi',
                    "addressCountry" => "IN"
                ],
                "telephone" => $loc['phone'],
                "url" => "https://fairfieldhearing.in/#clinics"
            ])->toArray()
        ];

        $faqSchema = count($faqs) > 0 ? [
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => collect($faqs)->map(fn($f) => [
                "@type" => "Question",
                "name" => $f['question'],
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => strip_tags($f['answer'])
                ]
            ])->toArray()
        ] : null;

        return view('livewire.web.home', [
            'locations' => $locations,
            'faqs' => $faqs,
            'medicalBusinessSchema' => $medicalBusinessSchema,
            'faqSchema' => $faqSchema
        ])->layout('layouts.web');
    }
}
