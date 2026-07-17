<?php

namespace App\Livewire\Admin;

use App\Models\Lead;
use Livewire\Component;
use App\Models\FormSubmission;
use App\Models\BlogPost;
use App\Models\Ticket;

class Dashboard extends Component
{

    public function with(): array
    {
        return [
            'totalPosts' => BlogPost::count(),
            'totalSubmissions' => FormSubmission::count(),
            'totalLeads' => Lead::count(),
            'openTickets' => Ticket::where('status', 'open')->count(),
            'totalCategories' => \App\Models\BlogCategory::count(),
            'totalManufacturers' => \App\Models\Manufacturer::count(),
            'totalModels' => \App\Models\HearingAidModel::count(),
            'totalStaff' => \App\Models\User::count(),
            'recentSubmissions' => FormSubmission::with(['location', 'lead', 'ticket'])->latest()->take(5)->get(),
            'recentLeads' => Lead::with(['location', 'assignedUser'])->latest()->take(5)->get(),
            'recentTickets' => Ticket::with(['customer', 'assignedUser'])->where('status', 'open')->latest()->take(5)->get(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard', $this->with())->layout('layouts.app');
    }
}
