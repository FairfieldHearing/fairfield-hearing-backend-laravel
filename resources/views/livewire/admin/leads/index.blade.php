<div>
    <!-- HEADER -->
    <x-header title="Leads Pipeline" subtitle="Track prospects from inquiry to hearing aid fitting" separator progress-indicator>
        <x-slot:middle class="!justify-end gap-2">
            <x-select label="" wire:model.live="statusFilter" :options="[
                ['id' => 'all', 'name' => 'All Statuses'],
                ['id' => 'new', 'name' => 'New Lead'],
                ['id' => 'contacted', 'name' => 'Contacted'],
                ['id' => 'in_progress', 'name' => 'Assessment / Fitting In Progress'],
                ['id' => 'won', 'name' => 'Hearing Aid Purchased (Won)'],
                ['id' => 'lost', 'name' => 'Lost / Disengaged']
            ]" option-value="id" option-label="name" class="w-56" />
            <x-input placeholder="Search leads..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy" with-pagination>
            @scope('cell_created_at', $lead)
                {{ $lead->created_at->format('M d, Y') }}
            @endscope

            @scope('cell_assignedUser.name', $lead)
                @if($lead->assignedUser)
                    <span class="font-semibold text-sm">{{ $lead->assignedUser->name }}</span>
                @else
                    <span class="text-base-content/40 text-xs italic">Unassigned</span>
                @endif
            @endscope

            @scope('cell_status', $lead)
                <span class="badge badge-sm badge-outline 
                    {{ $lead->status === 'won' ? 'badge-success' : '' }}
                    {{ $lead->status === 'lost' ? 'badge-error' : '' }}
                    {{ $lead->status === 'in_progress' ? 'badge-warning' : '' }}
                    {{ $lead->status === 'contacted' ? 'badge-info' : '' }}
                    {{ $lead->status === 'new' ? 'badge-primary' : '' }}
                ">
                    {{ str_replace('_', ' ', ucfirst($lead->status)) }}
                </span>
            @endscope

            @scope('actions', $lead)
            <div class="flex gap-2">
                <x-button icon="o-eye" wire:click="showDetails({{ $lead->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $lead->id }})" wire:confirm="Are you sure you want to delete this lead?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- DETAILS DRAWER -->
    <x-drawer wire:model="drawer" title="Lead Management" right separator with-close-button class="lg:w-1/2">
        @if($selectedLead)
            <x-form wire:submit="save">
                <div class="space-y-6">
                    <!-- Client contact info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-base-content/60">Client Name</span>
                            <p class="font-bold text-lg">{{ $selectedLead->full_name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Phone Number</span>
                            <p class="font-semibold">{{ $selectedLead->mobile_number }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-select label="Assign Staff Member" wire:model="assigned_user_id" :options="$staffList" option-value="id" option-label="name" placeholder="Choose staff member" />
                        
                        <x-select label="Pipeline Status" wire:model="status" :options="[
                            ['id' => 'new', 'name' => 'New Lead'],
                            ['id' => 'contacted', 'name' => 'Contacted'],
                            ['id' => 'in_progress', 'name' => 'Assessment / Fitting In Progress'],
                            ['id' => 'won', 'name' => 'Hearing Aid Purchased (Won)'],
                            ['id' => 'lost', 'name' => 'Lost']
                        ]" option-value="id" option-label="name" required />
                    </div>

                    <!-- Client initial inquiry details -->
                    <div class="bg-base-200 p-4 rounded-lg space-y-2">
                        <div>
                            <span class="text-xs text-base-content/60">Inquiry Target / Hearing Issue</span>
                            <p class="font-semibold text-sm">{{ $selectedLead->hearing_problem ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Preferred Clinic Location</span>
                            <p class="font-semibold text-sm">{{ $selectedLead->location->name }}</p>
                        </div>
                        @if($selectedLead->message)
                            <div>
                                <span class="text-xs text-base-content/60">Additional Notes</span>
                                <p class="text-sm text-base-content/80 whitespace-pre-line">{{ $selectedLead->message }}</p>
                            </div>
                        @endif

                        <!-- Exchange Calculator Estimate Details (If Present) -->
                        @if($selectedLead->formSubmission && $selectedLead->formSubmission->exchangeEstimate)
                            <div class="mt-4 border border-warning/30 bg-warning/10 p-4 rounded-lg space-y-3">
                                <div class="text-xs text-base-content/50 uppercase font-bold tracking-wider">Exchange Calculator Summary</div>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-xs text-base-content/60">Selected New Model</span>
                                        <p class="font-bold text-primary">
                                            {{ $selectedLead->formSubmission->exchangeEstimate->hearingAidModel?->manufacturer?->name }} 
                                            {{ $selectedLead->formSubmission->exchangeEstimate->hearingAidModel?->name }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-base-content/60">MRP / Discounted Price</span>
                                        <p class="font-semibold text-base-content/80">
                                            ₹{{ number_format($selectedLead->formSubmission->exchangeEstimate->hearingAidModel?->mrp) }} 
                                            / <span class="text-success font-bold">₹{{ number_format($selectedLead->formSubmission->exchangeEstimate->final_price + $selectedLead->formSubmission->exchangeEstimate->calculated_value) }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                @if($selectedLead->formSubmission->exchangeEstimate->want_exchange)
                                    <div class="border-t border-warning/20 pt-2 grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <span class="text-xs text-base-content/60">Old Hearing Aid</span>
                                            <p class="font-semibold">{{ $selectedLead->formSubmission->exchangeEstimate->old_brand }} ({{ $selectedLead->formSubmission->exchangeEstimate->old_model }})</p>
                                        </div>
                                        <div>
                                            <span class="text-xs text-base-content/60">Estimated Exchange Value</span>
                                            <p class="font-bold text-success">− ₹{{ number_format($selectedLead->formSubmission->exchangeEstimate->calculated_value) }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="border-t border-warning/20 pt-2 text-xs text-base-content/60">
                                        Selected without exchanging old hearing aid.
                                    </div>
                                @endif

                                <div class="border-t border-warning/20 pt-2 flex justify-between items-center text-sm">
                                    <span class="font-bold">Estimated Final Price:</span>
                                    <span class="text-base font-extrabold text-success">₹{{ number_format($selectedLead->formSubmission->exchangeEstimate->final_price) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- History / Updates log -->
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wider text-base-content/50 mb-2">History & Updates Log</h4>
                        
                        <div class="space-y-2 max-h-60 overflow-y-auto mb-4 border border-base-300 p-3 rounded-lg bg-base-100">
                            @forelse($selectedLead->logs ?: [] as $log)
                                <div class="text-xs border-b border-base-200 pb-2 last:border-0">
                                    <div class="flex justify-between text-base-content/50 mb-1">
                                        <span class="font-bold">{{ $log['author'] }}</span>
                                        <span>{{ $log['date'] }}</span>
                                    </div>
                                    <p class="text-base-content/95">{{ $log['message'] }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-base-content/40 italic py-2 text-center">No update logs recorded yet.</p>
                            @endforelse
                        </div>

                        <!-- Add new log note -->
                        <x-textarea label="Add Progress Log Note" wire:model="logMessage" placeholder="Type logs, audiologist reports, fittings updates..." rows="3" />
                    </div>
                </div>

                <x-slot:actions>
                    <x-button label="Cancel" @click="$wire.drawer = false" class="btn-ghost" />
                    <x-button label="Save Changes" type="submit" class="btn-primary" spinner="save" />
                </x-slot:actions>
            </x-form>
        @endif
    </x-drawer>
</div>