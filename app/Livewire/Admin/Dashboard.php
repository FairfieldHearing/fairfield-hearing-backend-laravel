<?php

namespace App\Livewire\Admin;

use App\Models\Lead;
use Livewire\Component;
use App\Models\FormSubmission;
use App\Models\BlogPost;
use App\Models\Ticket;

class Dashboard extends Component
{
public array $leadChart = [];

    public function mount()
    {
        $new = Lead::where('status', 'new')->count();
        $contacted = Lead::where('status', 'contacted')->count();
        $inProgress = Lead::where('status', 'in_progress')->count();
        $won = Lead::where('status', 'won')->count();
        $lost = Lead::where('status', 'lost')->count();

        $this->leadChart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['New', 'Contacted', 'In Progress', 'Won (Purchased)', 'Lost'],
                'datasets' => [
                    [
                        'label' => 'Leads Status',
                        'data' => [$new, $contacted, $inProgress, $won, $lost],
                        'backgroundColor' => ['#3b82f6', '#06b6d4', '#f59e0b', '#10b981', '#ef4444'],
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]
        ];
    }

    public function with(): array
    {
        return [
            'totalPosts' => BlogPost::count(),
            'totalSubmissions' => FormSubmission::count(),
            'totalLeads' => Lead::count(),
            'openTickets' => Ticket::where('status', 'open')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard', $this->with())->layout('layouts.app');
    }

}
