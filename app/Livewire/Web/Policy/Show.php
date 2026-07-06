<?php

namespace App\Livewire\Web\Policy;

use App\Models\PolicyPage;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.web')]
class Show extends Component
{
    /** @var \App\Models\PolicyPage */
    public $policy;

    public function mount(string $slug): void
    {
        $this->policy = PolicyPage::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        $content = $this->policy->content;
        // Strip the first leading H1 markdown title since it is already rendered in page-hero
        $content = preg_replace('/^#\s+.*?$/m', '', $content, 1);
        
        // Simple Markdown to HTML parser
        $html = $content;
        $html = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^\- (.*?)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/\\*\\*([^\\*]+)\\*\\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/(<li>.*?<\/li>)/s', '<ul>$1</ul>', $html);
        
        $paragraphs = explode("\n\n", $html);
        $parsedParagraphs = [];
        foreach ($paragraphs as $p) {
            $trimmed = trim($p);
            if (!$trimmed) continue;
            if (str_starts_with($trimmed, '<h') || str_starts_with($trimmed, '<ul') || str_starts_with($trimmed, '<li>')) {
                $parsedParagraphs[] = $trimmed;
            } else {
                $parsedParagraphs[] = "<p>" . str_replace("\n", "<br />", $trimmed) . "</p>";
            }
        }
        $policyContent = implode('', $parsedParagraphs);

        $policySchema = [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "name" => $this->policy->title . " | Fairfield Hearing Clinics",
            "description" => $this->policy->meta_description ?: $this->policy->title,
            "url" => "https://fairfieldhearing.in/policies/" . $this->policy->slug
        ];

        return view('livewire.web.policy', [
            'policy' => $this->policy->toArray(),
            'policyContent' => $policyContent,
            'policySchema' => $policySchema
        ]);
    }
}
