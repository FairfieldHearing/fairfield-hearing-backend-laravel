<div>
    <!-- HEADER -->
    <x-header title="Form Submissions" subtitle="View and manage leads sent from the frontend" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search leads..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    <!-- TABLE -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$rows" :sort-by="$sortBy" with-pagination>
            @scope('cell_created_at', $sub)
                {{ $sub->created_at->format('M d, Y H:i') }}
            @endscope

            @scope('actions', $sub)
            <div class="flex gap-2">
                <x-button icon="o-eye" wire:click="showDetails({{ $sub->id }})" class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-trash" wire:click="delete({{ $sub->id }})" wire:confirm="Are you sure you want to delete this submission?" class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- DETAILS DRAWER -->
    <x-drawer wire:model="drawer" title="Submission Details" right separator with-close-button class="lg:w-1/3">
        @if($selectedSubmission)
            <div class="space-y-6">
                <!-- Basic Contact -->
                <div>
                    <h3 class="text-sm font-semibold text-base-content/50 uppercase tracking-wider mb-2">Lead Information</h3>
                    <div class="bg-base-200 p-4 rounded-lg space-y-2">
                        <div>
                            <span class="text-xs text-base-content/60">Full Name</span>
                            <p class="font-bold text-base">{{ $selectedSubmission->full_name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Mobile Number</span>
                            <p class="font-semibold">{{ $selectedSubmission->mobile_number }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Email Address</span>
                            <p class="font-semibold text-primary">{{ $selectedSubmission->email ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div>
                    <h3 class="text-sm font-semibold text-base-content/50 uppercase tracking-wider mb-2">Service Request</h3>
                    <div class="bg-base-200 p-4 rounded-lg space-y-2">
                        <div>
                            <span class="text-xs text-base-content/60">Preferred Clinic</span>
                            <p class="font-bold text-success">{{ $selectedSubmission->location->name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Type of Hearing Problem</span>
                            <p class="font-semibold">{{ $selectedSubmission->hearing_problem ?: 'Not specified' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-base-content/60">Preferred Day / Time</span>
                            <p class="font-semibold text-warning">{{ $selectedSubmission->preferred_day_time ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div>
                    <h3 class="text-sm font-semibold text-base-content/50 uppercase tracking-wider mb-2">Anything we should know?</h3>
                    <div class="bg-base-200 p-4 rounded-lg">
                        <p class="whitespace-pre-line text-sm text-base-content/80">{{ $selectedSubmission->message ?: 'No additional notes provided.' }}</p>
                    </div>
                </div>

                <!-- Exchange Calculator Estimate Details (If Present) -->
                @if($selectedSubmission->exchangeEstimate)
                    <div>
                        <h3 class="text-sm font-semibold text-base-content/50 uppercase tracking-wider mb-2">Exchange Calculator Summary</h3>
                        <div class="bg-warning/10 border border-warning/30 p-4 rounded-lg space-y-3">
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-xs text-base-content/60">Selected New Model</span>
                                    <p class="font-bold text-primary">
                                        {{ $selectedSubmission->exchangeEstimate->hearingAidModel?->manufacturer?->name }} 
                                        {{ $selectedSubmission->exchangeEstimate->hearingAidModel?->name }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs text-base-content/60">MRP / Discounted Price</span>
                                    <p class="font-semibold text-base-content/80">
                                        ₹{{ number_format($selectedSubmission->exchangeEstimate->hearingAidModel?->mrp) }} 
                                        / <span class="text-success font-bold">₹{{ number_format($selectedSubmission->exchangeEstimate->final_price + $selectedSubmission->exchangeEstimate->calculated_value) }}</span>
                                    </p>
                                </div>
                            </div>
                            
                            @if($selectedSubmission->exchangeEstimate->want_exchange)
                                <div class="border-t border-warning/20 pt-2 grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-xs text-base-content/60">Old Hearing Aid</span>
                                        <p class="font-semibold">{{ $selectedSubmission->exchangeEstimate->old_brand }} ({{ $selectedSubmission->exchangeEstimate->old_model }})</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-base-content/60">Estimated Exchange Value</span>
                                        <p class="font-bold text-success">− ₹{{ number_format($selectedSubmission->exchangeEstimate->calculated_value) }}</p>
                                    </div>
                                </div>
                                <div class="text-xs text-base-content/60 italic grid grid-cols-3 gap-1">
                                    <div>Age: {{ str_replace('_', ' ', $selectedSubmission->exchangeEstimate->old_age_band) }}</div>
                                    <div>Cond: {{ str_replace('_', ' ', $selectedSubmission->exchangeEstimate->old_condition_band) }}</div>
                                    <div>Original Price: {{ str_replace('_', ' ', $selectedSubmission->exchangeEstimate->old_price_band) }}</div>
                                </div>
                            @else
                                <div class="border-t border-warning/20 pt-2 text-xs text-base-content/60">
                                    Selected without exchanging old hearing aid.
                                </div>
                            @endif

                            <div class="border-t border-warning/20 pt-2 flex justify-between items-center">
                                <span class="text-sm font-bold">Estimated Final Price:</span>
                                <span class="text-lg font-extrabold text-success">₹{{ number_format($selectedSubmission->exchangeEstimate->final_price) }}</span>
                            </div>
                            
                            <div class="text-xs text-right text-base-content/40 font-mono">
                                Hash: {{ $selectedSubmission->exchangeEstimate->unique_hash }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Timestamp -->
                <div class="text-xs text-base-content/50 text-right">
                    Received on: {{ $selectedSubmission->created_at->format('F d, Y \a\t H:i') }}
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Convert to Lead" icon="o-user-plus" wire:click="convertToLead({{ $selectedSubmission->id }})" class="btn-success" />
                <x-button label="Convert to Ticket" icon="o-ticket" wire:click="convertToTicket({{ $selectedSubmission->id }})" class="btn-info" />
                <x-button label="Delete" icon="o-trash" wire:click="delete({{ $selectedSubmission->id }})" wire:confirm="Are you sure you want to delete this submission?" class="btn-error btn-outline mr-auto" />
                <x-button label="Close" @click="$wire.drawer = false" class="btn-ghost" />
            </x-slot:actions>
        @endif
    </x-drawer>
</div>