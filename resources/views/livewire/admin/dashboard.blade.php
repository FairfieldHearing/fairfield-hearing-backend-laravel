<div>
    <x-header title="Admin Dashboard" subtitle="Welcome to Fairfield Hearing Administration Panel" separator />

    <!-- QUICK ACTIONS -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        @can('manage-leads')
            <x-button label="View Submissions" icon="o-inbox" link="{{ route('admin.submissions') }}" no-wire-navigate class="btn-primary btn-sm btn-outline shadow-sm bg-base-100 w-full" />
            <x-button label="Manage Leads Pipeline" icon="o-user-group" link="{{ route('admin.leads') }}" no-wire-navigate class="btn-success btn-sm btn-outline shadow-sm bg-base-100 w-full" />
        @endcan

        @can('manage-tickets')
            <x-button label="Support Helpdesk" icon="o-ticket" link="{{ route('admin.tickets') }}" no-wire-navigate class="btn-warning btn-sm btn-outline shadow-sm bg-base-100 w-full" />
        @endcan

        @can('manage-blogs')
            <x-button label="Write Blog Post" icon="o-document-text" link="{{ route('admin.posts') }}" no-wire-navigate class="btn-info btn-sm btn-outline shadow-sm bg-base-100 w-full" />
        @endcan

        @can('superadmin-only')
            <x-button label="Manage Staff" icon="o-users" link="{{ route('admin.staff') }}" no-wire-navigate class="btn-ghost btn-sm btn-outline border-base-300 shadow-sm bg-base-100 w-full text-primary" />
        @endcan
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 mb-8">
        @can('manage-leads')
            <x-stat title="Submissions" value="{{ $totalSubmissions }}" icon="o-inbox" class="shadow-sm bg-base-100 border-l-4 border-primary !p-3" />
            <x-stat title="Active Leads" value="{{ $totalLeads }}" icon="o-user-group" class="shadow-sm bg-base-100 border-l-4 border-success !p-3" />
        @endcan
        @can('manage-tickets')
            <x-stat title="Open Tickets" value="{{ $openTickets }}" icon="o-ticket" class="shadow-sm bg-base-100 border-l-4 border-warning text-warning !p-3" />
        @endcan
        @can('manage-blogs')
            <x-stat title="Articles" value="{{ $totalPosts }}" icon="o-document-text" class="shadow-sm bg-base-100 border-l-4 border-info !p-3" />
            <x-stat title="Categories" value="{{ $totalCategories }}" icon="o-folder" class="shadow-sm bg-base-100 border-l-4 border-accent !p-3" />
        @endcan
        <x-stat title="Brands" value="{{ $totalManufacturers }}" icon="o-building-office" class="shadow-sm bg-base-100 border-l-4 border-secondary !p-3" />
        <x-stat title="Devices" value="{{ $totalModels }}" icon="o-sparkles" class="shadow-sm bg-base-100 border-l-4 border-warning !p-3" />
        @can('superadmin-only')
            <x-stat title="Staff Members" value="{{ $totalStaff }}" icon="o-users" class="shadow-sm bg-base-100 border-l-4 border-primary !p-3" />
        @endcan
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- LEFT: SUBMISSIONS & TICKETS -->
        <div class="xl:col-span-2 space-y-6">
            
            @can('manage-leads')
                <!-- Recent Submissions -->
                <x-card title="Recent Form Submissions" subtitle="Latest inquiries received from the website" shadow separator>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra table-sm w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Preferred Clinic</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSubmissions as $sub)
                                    <tr>
                                        <td class="font-bold">{{ $sub->full_name }}</td>
                                        <td>{{ $sub->location->name }}</td>
                                        <td>
                                            @if($sub->lead)
                                                <span class="badge badge-success badge-sm text-[10px] font-semibold py-1 px-2">Lead</span>
                                            @elseif($sub->ticket)
                                                <span class="badge badge-info badge-sm text-[10px] font-semibold py-1 px-2">Ticket</span>
                                            @else
                                                <span class="badge badge-ghost badge-sm text-[10px] text-base-content/60 py-1 px-2">New</span>
                                            @endif
                                        </td>
                                        <td>{{ $sub->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('admin.submissions') }}" class="btn btn-ghost btn-xs text-primary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-base-content/50">No recent submissions.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            @endcan

            @can('manage-tickets')
                <!-- Recent Open Tickets -->
                <x-card title="Open Support Tickets Queue" subtitle="Pending helpdesk requests requiring action" shadow separator>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra table-sm w-full text-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Subject</th>
                                    <th>Assigned To</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTickets as $ticket)
                                    <tr>
                                        <td class="font-mono text-xs font-bold text-primary">{{ $ticket->ticket_number }}</td>
                                        <td>{{ $ticket->customer->name }}</td>
                                        <td>{{ Str::limit($ticket->subject, 30) }}</td>
                                        <td>{{ $ticket->assignedUser?->name ?: 'Unassigned' }}</td>
                                        <td>
                                            <a href="{{ route('admin.tickets') }}" class="btn btn-ghost btn-xs text-primary">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-base-content/50">No open tickets.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            @endcan

        </div>

        <!-- RIGHT: CHART & QUICK ACTIONS -->
        <div class="space-y-6">

            @can('manage-leads')
                <!-- Recent Leads activity list -->
                <x-card title="Recent Leads Activity" shadow separator>
                    <div class="space-y-3">
                        @forelse($recentLeads as $lead)
                            <div class="flex justify-between items-center text-sm border-b border-base-200 pb-2 last:border-0 last:pb-0">
                                <div>
                                    <p class="font-bold">{{ $lead->full_name }}</p>
                                    <p class="text-xs text-base-content/50">Clinic: {{ $lead->location?->name ?: 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-sm uppercase text-[9px] font-bold
                                        @if($lead->status === 'new') badge-primary
                                        @elseif($lead->status === 'contacted') badge-accent
                                        @elseif($lead->status === 'in_progress') badge-warning
                                        @elseif($lead->status === 'won') badge-success
                                        @else badge-error
                                        @endif
                                    ">
                                        {{ str_replace('_', ' ', $lead->status) }}
                                    </span>
                                    <p class="text-[9px] text-base-content/40 mt-1">{{ $lead->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-base-content/50 text-center py-2">No leads available.</p>
                        @endforelse
                    </div>
                </x-card>
            @endcan

        </div>
    </div>
</div>