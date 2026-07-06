<div>
    <x-header title="Admin Dashboard" subtitle="Welcome to Fairfield Hearing Administration Panel" separator />

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @can('manage-leads')
            <x-stat title="Form Submissions" value="{{ $totalSubmissions }}" icon="o-inbox" class="shadow-sm bg-base-100" />
            <x-stat title="Active Leads" value="{{ $totalLeads }}" icon="o-user-group" class="shadow-sm bg-base-100" />
        @endcan
        @can('manage-tickets')
            <x-stat title="Open Support Tickets" value="{{ $openTickets }}" icon="o-ticket" class="shadow-sm bg-base-100 text-warning" />
        @endcan
        @can('manage-blogs')
            <x-stat title="Blog Articles" value="{{ $totalPosts }}" icon="o-document-text" class="shadow-sm bg-base-100" />
        @endcan
    </div>

    <!-- CHARTS & NAVIGATION GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- QUICK NAVIGATION -->
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Quick Navigation" shadow>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @can('manage-leads')
                        <x-button label="View Submissions" icon="o-inbox" link="{{ route('admin.submissions') }}" class="btn-outline w-full" />
                        <x-button label="Manage Leads Pipeline" icon="o-user-group" link="{{ route('admin.leads') }}" class="btn-outline w-full" />
                    @endcan

                    @can('manage-tickets')
                        <x-button label="Support Helpdesk Queue" icon="o-ticket" link="{{ route('admin.tickets') }}" class="btn-outline w-full" />
                    @endcan

                    @can('manage-blogs')
                        <x-button label="Write New Blog Post" icon="o-document-text" link="{{ route('admin.posts') }}" class="btn-outline w-full" />
                    @endcan

                    @can('superadmin-only')
                        <x-button label="Manage Staff Members" icon="o-users" link="{{ route('admin.staff') }}" class="btn-outline w-full text-primary" />
                    @endcan
                </div>
            </x-card>

            <x-card title="Operational Overview" shadow>
                <p class="text-sm text-base-content/70">
                    Use the sidebar or quick links to navigate modules based on assigned staff permissions. High-priority inquiries can be converted into support tickets or tracked directly as leads until hearing aid purchase completion.
                </p>
            </x-card>
        </div>

        <!-- ANALYTICS CHART -->
        @can('manage-leads')
            <x-card title="Leads Conversion Pipeline" shadow>
                <div class="h-64 relative">
                    <x-chart wire:model="leadChart" />
                </div>
            </x-card>
        @endcan
    </div>
</div>