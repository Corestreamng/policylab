<div class="space-y-8 pb-16">
    <!-- HERO SECTION (Map is the Hero Visual Above the Fold) -->
    <section class="bg-gradient-to-b from-ink-900 to-ink-950 text-white pt-8 pb-12 px-4 sm:px-6 lg:px-8 border-b border-ink-800">
        <div class="max-w-7xl mx-auto">
            <!-- Headline & Purpose -->
            <div class="max-w-3xl mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-blue/20 text-blue-300 text-xs font-semibold uppercase tracking-wider mb-3 border border-brand-blue/30">
                    <i class="fas fa-satellite-dish"></i> Live National Policy Tracking
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight font-heading">
                    Tracking Sub-National Youth Policy Domestication in Nigeria
                </h1>
                <p class="mt-3 text-sm sm:text-base text-ink-300 leading-relaxed">
                    An evidence-led civic intelligence platform monitoring how the National Youth Policy is domesticated, gazetted, funded, and implemented across all 36 States and the Federal Capital Territory.
                </p>
            </div>

            <!-- Quick Find & Quick Filter Bar -->
            <div class="bg-ink-800/80 backdrop-blur-md rounded-xl p-4 border border-ink-700 mb-8 shadow-lg">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <!-- Quick Search Input -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-400">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search state, partner, funder..."
                            class="w-full pl-9 pr-4 py-2 bg-ink-900 border border-ink-600 rounded text-xs text-white placeholder-ink-400 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent touch-target"
                            aria-label="Search states, organisations, and funders"
                        />
                        @if(!empty($search))
                            <button
                                type="button"
                                wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-ink-400 hover:text-white"
                                aria-label="Clear search"
                            >
                                <i class="fas fa-xmark"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Geopolitical Zone Filter -->
                    <div>
                        <select
                            wire:model.live="selectedZone"
                            class="w-full px-3 py-2 bg-ink-900 border border-ink-600 rounded text-xs text-white focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                            aria-label="Filter by Geopolitical Zone"
                        >
                            <option value="">All Geopolitical Zones (6)</option>
                            @foreach($zones as $z)
                                <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lifecycle Stage Filter -->
                    <div>
                        <select
                            wire:model.live="selectedStage"
                            class="w-full px-3 py-2 bg-ink-900 border border-ink-600 rounded text-xs text-white focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                            aria-label="Filter by Lifecycle Stage"
                        >
                            <option value="">All Lifecycle Stages (8)</option>
                            @foreach($stages as $st)
                                <option value="{{ $st->sequence_order }}">Stage {{ $st->sequence_order }}: {{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quick Jump to State Drawer -->
                    <div>
                        <select
                            wire:change="openDrawer($event.target.value)"
                            class="w-full px-3 py-2 bg-ink-900 border border-ink-600 rounded text-xs text-brand-blue font-semibold focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                            aria-label="Quick Select State"
                        >
                            <option value="">Jump to State Dossier...</option>
                            @foreach($filteredStates->sortBy('name') as $st)
                                <option value="{{ $st->slug }}">{{ $st->name }} ({{ $st->policyStatus?->stage?->name ?? 'No Data' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Secondary Filters: Lead Org & Funder & CTC -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-3 pt-3 border-t border-ink-700/60">
                    <div>
                        <select
                            wire:model.live="selectedOrg"
                            class="w-full px-3 py-2 bg-ink-900 border border-ink-600 rounded text-xs text-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                            aria-label="Filter by Lead Organization"
                        >
                            <option value="">Filter by Lead Organisation...</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select
                            wire:model.live="selectedFunder"
                            class="w-full px-3 py-2 bg-ink-900 border border-ink-600 rounded text-xs text-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                            aria-label="Filter by Funding Partner"
                        >
                            <option value="">Filter by Funding Partner...</option>
                            @foreach($funders as $funder)
                                <option value="{{ $funder->id }}">{{ $funder->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <select
                            wire:model.live="hasCtc"
                            class="w-full px-3 py-2 bg-ink-900 border border-ink-600 rounded text-xs text-ink-200 focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                            aria-label="Filter by Document Availability"
                        >
                            <option value="">Document Status (All)</option>
                            <option value="yes">CTC Document Available</option>
                            <option value="no">CTC Not Yet Uploaded</option>
                        </select>

                        @if($hasActiveFilters)
                            <button
                                type="button"
                                wire:click="resetFilters()"
                                class="inline-flex items-center px-3 py-2 text-xs font-semibold text-rose-300 hover:text-white bg-rose-950/80 hover:bg-rose-900 rounded border border-rose-800 transition shrink-0 touch-target"
                            >
                                <i class="fas fa-rotate-left mr-1.5"></i> Clear All
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Active Filter Chips -->
                @if($hasActiveFilters)
                    <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-ink-700/60">
                        <span class="text-2xs font-bold uppercase tracking-wider text-ink-400">Active Filters:</span>

                        @if(!empty($search))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-ink-700 text-white text-xs border border-ink-600">
                                Keyword: "{{ $search }}"
                                <button type="button" wire:click="removeFilter('search')" class="hover:text-rose-300"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if(!empty($selectedZone))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-ink-700 text-white text-xs border border-ink-600">
                                Zone: {{ $selectedZone }}
                                <button type="button" wire:click="removeFilter('zone')" class="hover:text-rose-300"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if($selectedStage !== '')
                            @php
                                $activeStageObj = $stages->firstWhere('sequence_order', (int)$selectedStage);
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-ink-700 text-white text-xs border border-ink-600">
                                Stage: {{ $activeStageObj->name ?? $selectedStage }}
                                <button type="button" wire:click="removeFilter('stage')" class="hover:text-rose-300"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if(!empty($selectedOrg))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-ink-700 text-white text-xs border border-ink-600">
                                Org Filter Active
                                <button type="button" wire:click="removeFilter('org')" class="hover:text-rose-300"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if(!empty($selectedFunder))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-ink-700 text-white text-xs border border-ink-600">
                                Funder Filter Active
                                <button type="button" wire:click="removeFilter('funder')" class="hover:text-rose-300"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if(!empty($hasCtc))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-ink-700 text-white text-xs border border-ink-600">
                                CTC: {{ $hasCtc === 'yes' ? 'Available' : 'Missing' }}
                                <button type="button" wire:click="removeFilter('ctc')" class="hover:text-rose-300"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        <span class="text-xs text-ink-300 ml-auto font-medium">
                            Showing {{ count($filteredStates) }} of {{ $kpis['total_states'] }} states
                        </span>
                    </div>
                @endif
            </div>

            <!-- National Map Visual Above the Fold -->
            @if($mapView === 'visual')
                <x-nigeria-map
                    :states="$filteredStates"
                    :stages="$stages"
                    :selectedSlug="$stateDrawerSlug"
                />
            @else
                <!-- Accessible Tabular Alternative for the Map -->
                <div class="bg-white rounded-xl border border-ink-200 shadow-card p-4 sm:p-6 text-ink-900">
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-ink-100">
                        <div>
                            <h2 class="text-base font-bold text-ink-900 font-heading">Accessible State Policy Summary Table</h2>
                            <p class="text-xs text-ink-500">Accessible tabular view providing full keyboard and screen reader access to state policy domestication data.</p>
                        </div>
                        <button
                            type="button"
                            wire:click="toggleMapView('visual')"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded text-brand-blue bg-blue-50 hover:bg-blue-100 border border-blue-200 transition touch-target"
                        >
                            <i class="fas fa-map mr-1.5"></i> Return to Visual Map
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-ink-200 text-left text-xs" aria-label="Accessible State Policy Table">
                            <thead class="bg-ink-100 font-semibold text-ink-700">
                                <tr>
                                    <th scope="col" class="py-2.5 px-3">State</th>
                                    <th scope="col" class="py-2.5 px-3">Zone</th>
                                    <th scope="col" class="py-2.5 px-3">Current Stage</th>
                                    <th scope="col" class="py-2.5 px-3">Lead Organisation</th>
                                    <th scope="col" class="py-2.5 px-3">Funding Partner</th>
                                    <th scope="col" class="py-2.5 px-3">Cycle</th>
                                    <th scope="col" class="py-2.5 px-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink-100 bg-white">
                                @foreach($filteredStates as $state)
                                    @php
                                        $stage = $state->policyStatus?->stage;
                                    @endphp
                                    <tr class="hover:bg-ink-50">
                                        <td class="py-2.5 px-3 font-bold text-ink-900">{{ $state->name }}</td>
                                        <td class="py-2.5 px-3 text-ink-600">{{ $state->zone }}</td>
                                        <td class="py-2.5 px-3">
                                            <x-stage-badge
                                                :stage="$stage"
                                                :isStalled="$state->policyStatus?->is_stalled"
                                                :isExpired="$state->policyStatus?->is_expired"
                                                size="sm"
                                            />
                                        </td>
                                        <td class="py-2.5 px-3 text-ink-700">
                                            {{ $state->leadingOrganizations->pluck('name')->join(', ') ?: 'Not yet documented' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-ink-700">
                                            {{ $state->funders->pluck('name')->join(', ') ?: 'Not yet documented' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-ink-600">
                                            {{ $state->policyStatus?->year_commenced ?: 'Not yet documented' }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <button
                                                type="button"
                                                wire:click="openDrawer('{{ $state->slug }}')"
                                                class="text-xs font-semibold text-brand-blue hover:underline"
                                            >
                                                Inspect Dossier
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- LIVE KPI METRIC STRIP (Top of Dashboard, Animated Count-up, Distinct Accent Colors) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <x-kpi-card
                title="Signed / Enacted"
                :value="$kpis['signed']"
                icon="fa-file-signature"
                borderColor="border-brand-orange"
                iconBg="bg-orange-50 text-brand-orange-text"
                subtitle="Stage 6 milestone"
            />
            <x-kpi-card
                title="Implementing"
                :value="$kpis['implementing']"
                icon="fa-rocket"
                borderColor="border-brand-emerald"
                iconBg="bg-emerald-50 text-brand-emerald-text"
                subtitle="Active frameworks"
            />
            <x-kpi-card
                title="In Co-Creation"
                :value="$kpis['in_development_validation']"
                icon="fa-users-gear"
                borderColor="border-brand-blue"
                iconBg="bg-blue-50 text-brand-blue-text"
                subtitle="Stages 3, 4 and 5"
            />
            <x-kpi-card
                title="Partner CSOs"
                :value="$kpis['active_orgs']"
                icon="fa-building"
                borderColor="border-brand-teal"
                iconBg="bg-teal-50 text-brand-teal-text"
                subtitle="Lead & technical CSOs"
            />
            <x-kpi-card
                title="Donor Partners"
                :value="$kpis['active_funders']"
                icon="fa-hand-holding-dollar"
                borderColor="border-brand-violet"
                iconBg="bg-purple-50 text-brand-violet-text"
                subtitle="Bilateral & grants"
            />
            <x-kpi-card
                title="No Verified Data"
                :value="$kpis['no_data']"
                icon="fa-circle-question"
                borderColor="border-brand-slate"
                iconBg="bg-slate-100 text-brand-slate-text"
                subtitle="Awaiting evidence"
            />
        </div>
    </section>

    <!-- HOW THE POLICY MOVES: 8-STAGE HORIZONTAL PROGRESSION JOURNEY -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-card">
            <div class="mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-blue block">Standardised Methodology</span>
                <h2 class="text-xl font-bold text-ink-900 font-heading">How a State Policy Moves From Idea to Implementation</h2>
                <p class="text-xs text-ink-500 mt-1">
                    Every state policy is categorized against an 8-stage objective progression scale based on verifiable documentation.
                </p>
            </div>

            <!-- Horizontal Journey Stepper -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 pt-2">
                @foreach($stages as $stage)
                    <div class="p-3 rounded-lg border border-ink-200 bg-ink-50/50 hover:bg-white transition relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-2xs font-extrabold uppercase px-1.5 py-0.5 rounded" style="background-color: {{ $stage->bg_color_hex }}; color: {{ $stage->text_color_hex }};">
                                Stage {{ $stage->sequence_order }}
                            </span>
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs shadow-xs" style="background-color: {{ $stage->color_hex }}; color: #ffffff;">
                                <i class="fas {{ $stage->icon_class }}"></i>
                            </span>
                        </div>
                        <h3 class="text-xs font-bold text-ink-900 leading-tight mb-1">{{ $stage->name }}</h3>
                        <p class="text-2xs text-ink-600 leading-relaxed">{{ $stage->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- STATE POLICY REGISTER (Sortable Data Table with Pagination) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-ink-200 shadow-card overflow-hidden">
            <div class="p-5 border-b border-ink-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-lg font-bold text-ink-900 font-heading">State Youth Policy Register</h2>
                    <p class="text-xs text-ink-500">Comprehensive inventory of sub-national policy domestication, partner attribution, and certified documents.</p>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <a href="/api/v1/export/csv" class="inline-flex items-center px-3 py-1.5 rounded bg-ink-100 hover:bg-ink-200 text-ink-800 font-medium border border-ink-200 transition touch-target">
                        <i class="fas fa-file-csv mr-1.5 text-brand-emerald-text"></i> Export CSV
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-ink-200 text-left text-xs" aria-label="State Policy Register Table">
                    <thead class="bg-ink-50 font-semibold text-ink-700 uppercase tracking-wider text-2xs">
                        <tr>
                            <th scope="col" class="py-3 px-4 cursor-pointer hover:text-brand-blue" wire:click="sortBy('name')">
                                State
                                @if($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort text-ink-400 ml-1"></i>
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-4 cursor-pointer hover:text-brand-blue" wire:click="sortBy('zone')">
                                Zone
                                @if($sortField === 'zone')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-4 cursor-pointer hover:text-brand-blue" wire:click="sortBy('stage')">
                                Domestication Status
                                @if($sortField === 'stage')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-4">Lead Organisation(s)</th>
                            <th scope="col" class="py-3 px-4">Funding Partner(s)</th>
                            <th scope="col" class="py-3 px-4">Cycle</th>
                            <th scope="col" class="py-3 px-4 text-center">CTC File</th>
                            <th scope="col" class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100 bg-white">
                        @forelse($paginatedStates as $state)
                            @php
                                $stage = $state->policyStatus?->stage;
                                $docs = $state->publicDocuments;
                            @endphp
                            <tr class="hover:bg-ink-50/70 transition">
                                <td class="py-3.5 px-4 font-bold text-ink-900">
                                    <button
                                        type="button"
                                        wire:click="openDrawer('{{ $state->slug }}')"
                                        class="hover:text-brand-blue text-left focus:outline-none focus:underline"
                                    >
                                        {{ $state->name }}
                                    </button>
                                </td>
                                <td class="py-3.5 px-4 text-ink-600 font-medium">{{ $state->zone }}</td>
                                <td class="py-3.5 px-4">
                                    <x-stage-badge
                                        :stage="$stage"
                                        :isStalled="$state->policyStatus?->is_stalled"
                                        :isExpired="$state->policyStatus?->is_expired"
                                        size="sm"
                                    />
                                </td>
                                <td class="py-3.5 px-4 text-ink-700 max-w-xs truncate" title="{{ $state->leadingOrganizations->pluck('name')->join('; ') }}">
                                    @if($state->leadingOrganizations->isNotEmpty())
                                        {{ $state->leadingOrganizations->pluck('name')->join('; ') }}
                                    @else
                                        <span class="text-ink-400 italic">Not yet documented</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-ink-700 max-w-xs truncate" title="{{ $state->funders->pluck('name')->join('; ') }}">
                                    @if($state->funders->isNotEmpty())
                                        {{ $state->funders->pluck('name')->join('; ') }}
                                    @else
                                        <span class="text-ink-400 italic">Not yet documented</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-ink-600">
                                    {{ $state->policyStatus?->year_commenced ?: 'Not yet documented' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($docs->isNotEmpty())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold bg-emerald-100 text-emerald-800" title="Certified True Copy Available">
                                            <i class="fas fa-check mr-1"></i> Available
                                        </span>
                                    @else
                                        <span class="text-2xs text-ink-400">Not uploaded</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button
                                        type="button"
                                        wire:click="openDrawer('{{ $state->slug }}')"
                                        class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-brand-blue hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded transition border border-blue-200"
                                    >
                                        Dossier <i class="fas fa-chevron-right ml-1 text-2xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-ink-500">
                                    <div class="w-12 h-12 rounded-full bg-ink-100 text-ink-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                        <i class="fas fa-filter-circle-xmark"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-ink-800">No states match the specified filter criteria</h3>
                                    <p class="text-xs text-ink-500 mt-1">Try adjusting your search keywords, zone, or lifecycle stage filters.</p>
                                    <button
                                        type="button"
                                        wire:click="resetFilters()"
                                        class="mt-3 px-3 py-1.5 text-xs font-semibold text-brand-blue hover:underline"
                                    >
                                        Reset All Filters
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="p-4 border-t border-ink-100">
                {{ $paginatedStates->links() }}
            </div>
        </div>
    </section>

    <!-- PARTNER AND FUNDER WALL -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-ink-100/60 rounded-xl p-8 border border-ink-200 text-center">
            <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block mb-1">Collaborative Open-Data Coalition</span>
            <h2 class="text-lg font-bold text-ink-900 font-heading mb-6">Partner Organisations, Technical Agencies & Donors</h2>

            <div class="flex flex-wrap items-center justify-center gap-3 max-w-4xl mx-auto">
                @foreach($organizations->take(12) as $org)
                    <span class="px-3 py-1.5 rounded-lg bg-white border border-ink-200 text-xs font-semibold text-ink-800 shadow-xs">
                        <i class="fas fa-building mr-1.5 text-brand-teal-text"></i> {{ $org->name }}
                    </span>
                @endforeach
                @foreach($funders->take(8) as $funder)
                    <span class="px-3 py-1.5 rounded-lg bg-white border border-ink-200 text-xs font-semibold text-ink-800 shadow-xs">
                        <i class="fas fa-hand-holding-dollar mr-1.5 text-brand-violet-text"></i> {{ $funder->name }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FREQUENTLY ASKED QUESTIONS -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <span class="text-xs font-bold uppercase tracking-wider text-brand-blue block">Methodology & Verification</span>
            <h2 class="text-2xl font-bold text-ink-900 font-heading">Frequently Asked Questions</h2>
        </div>

        <div class="space-y-4" x-data="{ activeFaq: null }">
            <div class="bg-white rounded-lg border border-ink-200 p-4">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 1 ? null : 1)"
                    class="w-full flex justify-between items-center text-left text-sm font-bold text-ink-900"
                >
                    <span>How is a state's policy status verified?</span>
                    <i class="fas" :class="activeFaq === 1 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="activeFaq === 1" x-cloak class="mt-2 text-xs text-ink-600 leading-relaxed border-t border-ink-100 pt-2">
                    Policy milestones are recorded exclusively when substantiated by official documentation, including published State Executive Council gazettes, formal validation workshop communiques, or authorized ministerial announcements. Where evidence is absent, the state is marked as "No Verifiable Data".
                </div>
            </div>

            <div class="bg-white rounded-lg border border-ink-200 p-4">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 2 ? null : 2)"
                    class="w-full flex justify-between items-center text-left text-sm font-bold text-ink-900"
                >
                    <span>What does a Certified True Copy (CTC) represent?</span>
                    <i class="fas" :class="activeFaq === 2 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="activeFaq === 2" x-cloak class="mt-2 text-xs text-ink-600 leading-relaxed border-t border-ink-100 pt-2">
                    A Certified True Copy is an authentic replica of the official state policy document endorsed by the Ministry of Justice, Ministry of Youth, or authorized government printing office. All CTC files uploaded to this tracker are preserved on private storage and served via signed URLs.
                </div>
            </div>

            <div class="bg-white rounded-lg border border-ink-200 p-4">
                <button
                    type="button"
                    @click="activeFaq = (activeFaq === 3 ? null : 3)"
                    class="w-full flex justify-between items-center text-left text-sm font-bold text-ink-900"
                >
                    <span>Can organisations submit updated state policy records?</span>
                    <i class="fas" :class="activeFaq === 3 ? 'fa-minus' : 'fa-plus'"></i>
                </button>
                <div x-show="activeFaq === 3" x-cloak class="mt-2 text-xs text-ink-600 leading-relaxed border-t border-ink-100 pt-2">
                    Yes. Civil society organisations and government ministries can submit verified policy drafts, gazettes, or stakeholder resolutions to Glowing Minds Initiatives for technical review and entry into the administrative portal.
                </div>
            </div>
        </div>
    </section>

    <!-- STATE DETAIL DRAWER (Slide-Out Deep-Linkable Panel) -->
    @if($selectedRegion)
        <div
            x-data="{
                open: true,
                close() {
                    this.open = false;
                    setTimeout(() => @this.closeDrawer(), 250);
                }
            }"
            @keydown.window.escape="close()"
            class="relative z-50"
            role="dialog"
            aria-modal="true"
            aria-labelledby="drawer-title"
        >
            <!-- Backdrop -->
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="close()"
                class="fixed inset-0 bg-ink-950/60 backdrop-blur-xs transition-opacity"
            ></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-2xl bg-white shadow-2xl flex flex-col h-full border-l border-ink-200"
                >
                    <!-- Drawer Header -->
                    <div class="p-6 bg-ink-900 text-white flex items-center justify-between border-b border-ink-800 shrink-0">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-2xs font-extrabold px-2 py-0.5 rounded bg-brand-blue text-white uppercase tracking-wider">
                                    {{ $selectedRegion->iso_code ?? 'NG' }}
                                </span>
                                <span class="text-xs text-ink-300">{{ $selectedRegion->zone }} Geopolitical Zone</span>
                            </div>
                            <h2 id="drawer-title" class="text-2xl font-bold font-heading text-white mt-1">
                                {{ $selectedRegion->name }} State Policy Dossier
                            </h2>
                        </div>
                        <button
                            type="button"
                            @click="close()"
                            class="p-2 text-ink-400 hover:text-white rounded hover:bg-ink-800 transition focus:outline-none focus:ring-2 focus:ring-brand-blue"
                            aria-label="Close state dossier"
                        >
                            <i class="fas fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Drawer Scrollable Body -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6 text-xs text-ink-800">
                        <!-- Current Status Block -->
                        <div class="p-4 rounded-xl border border-ink-200 bg-ink-50 space-y-3">
                            <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block">Current Domestication Status</span>
                            <div class="flex items-center justify-between">
                                <x-stage-badge
                                    :stage="$selectedRegion->policyStatus?->stage"
                                    :isStalled="$selectedRegion->policyStatus?->is_stalled"
                                    :isExpired="$selectedRegion->policyStatus?->is_expired"
                                    size="lg"
                                />
                                <span class="text-xs font-semibold text-ink-700">
                                    Cycle: {{ $selectedRegion->policyStatus?->year_commenced ?: 'Not yet documented' }}
                                </span>
                            </div>

                            @if($selectedRegion->policyStatus?->is_stalled)
                                <div class="p-3 rounded bg-rose-50 border border-rose-200 text-rose-900">
                                    <strong class="block font-bold mb-0.5"><i class="fas fa-triangle-exclamation mr-1 text-rose-600"></i> Stalled Milestone Flag</strong>
                                    {{ $selectedRegion->policyStatus?->stalled_reason ?: 'Domestication process paused pending government reconstitution or funding review.' }}
                                </div>
                            @endif

                            @if($selectedRegion->policyStatus?->is_expired)
                                <div class="p-3 rounded bg-amber-50 border border-amber-200 text-amber-900">
                                    <strong class="block font-bold mb-0.5"><i class="fas fa-clock-rotate-left mr-1 text-amber-600"></i> Expired Policy Framework</strong>
                                    {{ $selectedRegion->policyStatus?->expired_reason ?: 'Implementation cycle elapsed. Renewal and legislative review required.' }}
                                </div>
                            @endif
                        </div>

                        <!-- Visual Progress Stepper (0 to 7) -->
                        <div class="space-y-3">
                            <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block">Lifecycle Progression Audit</span>
                            <div class="space-y-2 border-l-2 border-ink-200 pl-4 ml-2">
                                @php
                                    $currentOrder = $selectedRegion->policyStatus?->stage?->sequence_order ?? 0;
                                @endphp
                                @foreach($stages as $stage)
                                    @php
                                        $isPastOrCurrent = $stage->sequence_order <= $currentOrder && $currentOrder > 0;
                                        $isCurrent = $stage->sequence_order === $currentOrder;
                                    @endphp
                                    <div class="relative flex items-start gap-3 py-1">
                                        <div class="absolute -left-[25px] top-1.5 w-4 h-4 rounded-full flex items-center justify-center text-2xs
                                                    {{ $isCurrent ? 'ring-4 ring-blue-100 bg-brand-blue text-white' : ($isPastOrCurrent ? 'bg-emerald-600 text-white' : 'bg-ink-200 text-ink-500') }}">
                                            @if($isPastOrCurrent && !$isCurrent)
                                                <i class="fas fa-check text-3xs"></i>
                                            @else
                                                <span class="text-3xs">{{ $stage->sequence_order }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-bold {{ $isCurrent ? 'text-brand-blue-text text-sm' : ($isPastOrCurrent ? 'text-ink-900' : 'text-ink-400') }}">
                                                {{ $stage->name }}
                                            </span>
                                            <p class="text-2xs text-ink-500">{{ $stage->description }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Stakeholder Landscape -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Lead Orgs -->
                            <div class="p-4 rounded-lg border border-ink-200 bg-white">
                                <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block mb-2">
                                    <i class="fas fa-building mr-1 text-brand-teal-text"></i> Leading & Supporting Organisations
                                </span>
                                @if($selectedRegion->organizations->isNotEmpty())
                                    <ul class="space-y-1.5">
                                        @foreach($selectedRegion->organizations as $org)
                                            <li class="font-semibold text-ink-900 flex items-start gap-1.5">
                                                <i class="fas fa-circle-check text-brand-teal-text mt-1 text-2xs"></i>
                                                <div>
                                                    <span>{{ $org->name }}</span>
                                                    @if($org->pivot->role)
                                                        <span class="block text-2xs text-ink-500 font-normal capitalize">Role: {{ $org->pivot->role }}</span>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-ink-400 italic">Not yet documented</span>
                                @endif
                            </div>

                            <!-- Funders -->
                            <div class="p-4 rounded-lg border border-ink-200 bg-white">
                                <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block mb-2">
                                    <i class="fas fa-hand-holding-dollar mr-1 text-brand-violet-text"></i> Funding Partners & Donors
                                </span>
                                @if($selectedRegion->funders->isNotEmpty())
                                    <ul class="space-y-1.5">
                                        @foreach($selectedRegion->funders as $funder)
                                            <li class="font-semibold text-ink-900 flex items-start gap-1.5">
                                                <i class="fas fa-coins text-brand-violet-text mt-1 text-2xs"></i>
                                                <span>{{ $funder->name }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-ink-400 italic">Not yet documented</span>
                                @endif
                            </div>
                        </div>

                        <!-- Policy Objectives & Focus Areas -->
                        <div class="space-y-2">
                            <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block">Focus Areas & Policy Objectives</span>
                            @if($selectedRegion->focusAreas->isNotEmpty())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedRegion->focusAreas as $area)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-ink-100 text-ink-800 text-2xs font-semibold">
                                            <i class="fas {{ $area->icon_class }} mr-1 text-brand-blue-text"></i> {{ $area->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-ink-400 italic">Not yet documented</p>
                            @endif

                            <div class="mt-2 text-ink-600 bg-ink-50 p-3 rounded border border-ink-200">
                                <strong class="block text-ink-800 font-semibold mb-1">Objectives Summary:</strong>
                                {{ $selectedRegion->policyStatus?->objectives_summary ?: 'Not yet documented' }}
                            </div>
                        </div>

                        <!-- Certified True Copy (CTC) Documents Section -->
                        <div class="space-y-3">
                            <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block">
                                <i class="fas fa-stamp mr-1 text-brand-emerald-text"></i> Certified True Copies & Policy Files
                            </span>

                            @if($selectedRegion->publicDocuments->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($selectedRegion->publicDocuments as $doc)
                                        @php
                                            $downloadUrl = URL::temporarySignedRoute('documents.download', now()->addMinutes(30), ['document' => $doc->uuid]);
                                            $streamUrl = URL::temporarySignedRoute('documents.stream', now()->addMinutes(30), ['document' => $doc->uuid]);
                                        @endphp
                                        <div class="p-4 rounded-lg border border-ink-200 bg-white space-y-3 shadow-xs">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <h3 class="font-bold text-sm text-ink-900 leading-snug">{{ $doc->title }}</h3>
                                                    <div class="flex items-center gap-2 mt-1 text-2xs text-ink-500">
                                                        <span>Size: {{ $doc->formatted_size }}</span>
                                                        <span>&bull;</span>
                                                        <span>Downloads: {{ $doc->download_count }}</span>
                                                        @if($doc->year)
                                                            <span>&bull;</span>
                                                            <span>Year: {{ $doc->year }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <a
                                                    href="{{ $downloadUrl }}"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-brand-emerald hover:bg-emerald-700 rounded transition shrink-0 shadow-xs touch-target"
                                                >
                                                    <i class="fas fa-download mr-1.5"></i> Download PDF
                                                </a>
                                            </div>

                                            <!-- PDF.js Embedded Preview Container -->
                                            <div
                                                x-data="{
                                                    previewOpen: false,
                                                    loadViewer() {
                                                        this.previewOpen = true;
                                                        this.$nextTick(() => {
                                                            window.loadPdfViewer('pdf-container-{{ $doc->id }}', '{{ $streamUrl }}');
                                                        });
                                                    }
                                                }"
                                                class="border-t border-ink-100 pt-3"
                                            >
                                                <button
                                                    type="button"
                                                    @click="loadViewer()"
                                                    x-show="!previewOpen"
                                                    class="inline-flex items-center text-xs font-semibold text-brand-blue hover:underline"
                                                >
                                                    <i class="fas fa-eye mr-1.5"></i> Preview Document (pdf.js)
                                                </button>

                                                <div x-show="previewOpen" x-cloak class="mt-2">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <span class="text-2xs font-semibold text-ink-500">Live PDF Preview</span>
                                                        <button type="button" @click="previewOpen = false" class="text-2xs text-rose-600 hover:underline">
                                                            Hide Preview
                                                        </button>
                                                    </div>
                                                    <div id="pdf-container-{{ $doc->id }}" class="max-h-[350px] overflow-y-auto bg-ink-100 p-2 rounded border border-ink-200">
                                                        <!-- PDF canvases rendered dynamically here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-lg border border-dashed border-ink-300 text-center text-ink-500 bg-ink-50/50">
                                    <i class="fas fa-file-circle-xmark text-lg text-ink-400 mb-1"></i>
                                    <p class="font-medium">No Certified True Copy currently uploaded for {{ $selectedRegion->name }}.</p>
                                    <p class="text-2xs text-ink-400 mt-0.5">Official gazettes and validated frameworks can be uploaded via the administrative portal.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Data Integrity & Verification Note -->
                        <div class="p-3 rounded bg-ink-100 text-ink-600 text-2xs space-y-1">
                            <div><strong>Data Verification Date:</strong> {{ $selectedRegion->policyStatus?->data_verified_at?->format('d M Y') ?: 'Not yet documented' }}</div>
                            <div><strong>Source Reference:</strong> {{ $selectedRegion->policyStatus?->source_notes ?: 'Not yet documented' }}</div>
                        </div>
                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 bg-ink-50 border-t border-ink-200 flex justify-between items-center shrink-0">
                        <button
                            type="button"
                            @click="close()"
                            class="px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-200 rounded transition border border-ink-300 touch-target"
                        >
                            Close Dossier
                        </button>
                        <a
                            href="/api/v1/states/{{ $selectedRegion->slug }}"
                            target="_blank"
                            class="inline-flex items-center text-xs font-semibold text-brand-blue hover:underline"
                        >
                            <i class="fas fa-code mr-1"></i> View JSON API Record
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
