<div class="space-y-12 pb-16">
    <!-- HERO SECTION (BudgIT-Inspired Editorial Layout: Single Clear Action, Bright Warm Palette, No AI Tells) -->
    <section class="bg-gradient-to-b from-[#FAF9F5] via-[#F6F8F5] to-[#EEF3EA] text-ink-900 pt-10 pb-12 px-4 sm:px-6 lg:px-8 border-b border-ink-200">
        <div class="max-w-7xl mx-auto">
            <div class="max-w-3xl space-y-4">
                <!-- Institutional Header Badge & Trustee Credit -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold uppercase tracking-wider border border-emerald-200 shadow-2xs">
                        <i class="fas fa-landmark text-brand-emerald"></i> Sub-National Civic Intelligence
                    </span>
                    <span class="text-xs text-ink-600 font-medium">
                        An initiative by <a href="https://glowingmindsinitiatives.org/" target="_blank" rel="noopener noreferrer" class="text-emerald-900 hover:text-emerald-700 font-bold underline underline-offset-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald rounded-xs" aria-label="Glowing Minds Initiatives (GMI) — opens in a new tab">Glowing Minds Initiatives (GMI)</a>
                    </span>
                </div>

                <!-- Single, Plain-Language Core Objective -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-ink-900 tracking-tight leading-tight font-heading">
                    Explore youth policy adoption across Nigeria's 36 states
                </h1>

                <!-- Institutional Context & Scope -->
                <p class="text-base sm:text-lg text-ink-700 leading-relaxed max-w-2xl font-normal">
                    An independent, open-data accountability platform monitoring how the National Youth Policy is domesticated, gazetted, funded, and implemented across all 36 states and the Federal Capital Territory.
                </p>

                <!-- Single Primary CTA & Quick State Jump (No competing buttons above the fold) -->
                <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <a
                        href="#coverage"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-md font-bold text-sm text-white bg-brand-emerald hover:bg-emerald-700 active:scale-[0.99] transition shadow-sm hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald"
                    >
                        <i class="fas fa-map-location-dot mr-2"></i> Explore National Policy Map
                    </a>

                    <!-- Compact Direct State Selector -->
                    <div class="relative w-full sm:w-72">
                        <select
                            wire:change="openDrawer($event.target.value)"
                            class="w-full pl-3 pr-8 py-3 bg-white border border-ink-300 rounded-md text-xs font-semibold text-ink-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-emerald focus:border-brand-emerald transition"
                            aria-label="Find your state"
                        >
                            <option value="">Find your state...</option>
                            @foreach($filteredStates->sortBy('name') as $st)
                                <option value="{{ $st->slug }}">{{ $st->name }} ({{ $st->policyStatus?->stage?->name ?? 'No Data' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Key Data Assurance Notes & Quick Resource Links -->
                <div class="pt-2 flex flex-wrap items-center gap-4 text-xs text-ink-600 font-medium">
                    <a href="#coverage" class="inline-flex items-center gap-1.5 hover:text-emerald-900 transition">
                        <i class="fas fa-check-double text-emerald-700"></i> Evidence-backed records
                    </a>
                    <a href="/documents" class="inline-flex items-center gap-1.5 hover:text-emerald-900 transition">
                        <i class="fas fa-certificate text-brand-teal"></i> Certified True Copies (CTCs)
                    </a>
                    <a href="/api/docs" class="inline-flex items-center gap-1.5 hover:text-emerald-900 transition">
                        <i class="fas fa-code text-brand-violet"></i> Public REST API
                    </a>
                    <a href="/api/v1/export/csv" class="inline-flex items-center gap-1.5 hover:text-emerald-900 transition">
                        <i class="fas fa-file-csv text-brand-emerald"></i> Download CSV
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- IMPACT AT A GLANCE (Stats Band: 6 Real, Verifiable Database-Derived Figures) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10" aria-label="Impact at a Glance">
        <div class="bg-white rounded-xl border border-ink-200 shadow-card p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-ink-100">
                <div>
                    <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block">Nationwide Overview</span>
                    <h2 class="text-sm sm:text-base font-bold text-ink-900 font-heading">Sub-National Domestication Metrics</h2>
                </div>
                <span class="text-xs text-ink-500 font-medium">Updated November 2026</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="p-3 rounded-lg border-l-4 border-brand-orange bg-ink-50/50">
                    <span class="text-2xs font-semibold uppercase tracking-wider text-ink-500 block">Signed / Enacted</span>
                    <span class="text-2xl font-extrabold text-ink-900 tabular-nums block mt-1">{{ $kpis['signed'] }}</span>
                    <span class="text-2xs text-ink-600 mt-0.5 block">State legislative assent</span>
                </div>

                <div class="p-3 rounded-lg border-l-4 border-brand-emerald bg-ink-50/50">
                    <span class="text-2xs font-semibold uppercase tracking-wider text-ink-500 block">Implementing</span>
                    <span class="text-2xl font-extrabold text-ink-900 tabular-nums block mt-1">{{ $kpis['implementing'] }}</span>
                    <span class="text-2xs text-ink-600 mt-0.5 block">Active field execution</span>
                </div>

                <div class="p-3 rounded-lg border-l-4 border-brand-blue bg-ink-50/50">
                    <span class="text-2xs font-semibold uppercase tracking-wider text-ink-500 block">In Formulation</span>
                    <span class="text-2xl font-extrabold text-ink-900 tabular-nums block mt-1">{{ $kpis['in_development_validation'] }}</span>
                    <span class="text-2xs text-ink-600 mt-0.5 block">Drafting &amp; validation</span>
                </div>

                <div class="p-3 rounded-lg border-l-4 border-brand-teal bg-ink-50/50">
                    <span class="text-2xs font-semibold uppercase tracking-wider text-ink-500 block">Partner CSOs</span>
                    <span class="text-2xl font-extrabold text-ink-900 tabular-nums block mt-1">{{ $kpis['active_orgs'] }}</span>
                    <span class="text-2xs text-ink-600 mt-0.5 block">Lead coalitions</span>
                </div>

                <div class="p-3 rounded-lg border-l-4 border-brand-violet bg-ink-50/50">
                    <span class="text-2xs font-semibold uppercase tracking-wider text-ink-500 block">Funding Donors</span>
                    <span class="text-2xl font-extrabold text-ink-900 tabular-nums block mt-1">{{ $kpis['active_funders'] }}</span>
                    <span class="text-2xs text-ink-600 mt-0.5 block">Bilateral &amp; grants</span>
                </div>

                <div class="p-3 rounded-lg border-l-4 border-brand-slate bg-ink-50/50">
                    <span class="text-2xs font-semibold uppercase tracking-wider text-ink-500 block">Jurisdictions</span>
                    <span class="text-2xl font-extrabold text-ink-900 tabular-nums block mt-1">{{ $kpis['total_states'] }}</span>
                    <span class="text-2xs text-ink-600 mt-0.5 block">36 States + FCT</span>
                </div>
            </div>
        </div>
    </section>

    <!-- WHERE WE WORK / COVERAGE SECTION (National Map & Filter Bar) -->
    <section id="coverage" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" aria-label="Geographic Coverage">
        <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-card space-y-6">
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-ink-200">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">National Geographic Coverage</span>
                    <h2 class="text-2xl font-bold text-ink-900 font-heading">
                        Where We Work: Tracking All 36 States + FCT
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-600 max-w-3xl">
                        Explore sub-national progress across all 6 geopolitical zones. Click any state boundary on the map or use the filters below to inspect policy dossiers, implementing ministries, and certified documents.
                    </p>
                </div>

                <!-- Desktop View Toggle Buttons (>= md) -->
                <div class="hidden md:flex items-center gap-2 shrink-0" role="tablist" aria-label="Coverage View Switcher">
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $mapView === 'visual' ? 'true' : 'false' }}"
                        aria-controls="coverage-map-panel"
                        wire:click="toggleMapView('visual')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition border touch-target {{ $mapView === 'visual' ? 'bg-emerald-50 text-emerald-900 border-brand-emerald shadow-2xs font-bold' : 'bg-white text-ink-600 border-ink-200 hover:bg-ink-50' }}"
                        id="desktop-tab-visual"
                    >
                        <i class="fas fa-map mr-1.5 text-brand-emerald"></i> Visual Map
                    </button>
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $mapView === 'accessible_table' ? 'true' : 'false' }}"
                        aria-controls="coverage-table-panel"
                        wire:click="toggleMapView('accessible_table')"
                        class="px-3 py-1.5 rounded-md text-xs font-semibold transition border touch-target {{ $mapView === 'accessible_table' ? 'bg-emerald-50 text-emerald-900 border-brand-emerald shadow-2xs font-bold' : 'bg-white text-ink-600 border-ink-200 hover:bg-ink-50' }}"
                        id="desktop-tab-table"
                    >
                        <i class="fas fa-table mr-1.5 text-brand-blue"></i> Accessible Table View
                    </button>
                </div>
            </div>

            <!-- Mobile Segmented View Control (< md) Prominent Map / Table Switcher -->
            <div class="block md:hidden pb-1" role="tablist" aria-label="Coverage View Switcher">
                <div class="grid grid-cols-2 p-1 bg-ink-100 rounded-lg border border-ink-200/80 text-center">
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $mapView === 'visual' ? 'true' : 'false' }}"
                        aria-controls="coverage-map-panel"
                        wire:click="toggleMapView('visual')"
                        class="py-2.5 px-3 rounded-md text-xs font-bold transition flex items-center justify-center gap-1.5 touch-target {{ $mapView === 'visual' ? 'bg-white text-emerald-900 shadow-sm border border-emerald-200' : 'text-ink-600 hover:text-ink-900' }}"
                        id="mobile-tab-visual"
                    >
                        <i class="fas fa-map text-brand-emerald"></i>
                        <span>Visual Map</span>
                    </button>
                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $mapView === 'accessible_table' ? 'true' : 'false' }}"
                        aria-controls="coverage-table-panel"
                        wire:click="toggleMapView('accessible_table')"
                        class="py-2.5 px-3 rounded-md text-xs font-bold transition flex items-center justify-center gap-1.5 touch-target {{ $mapView === 'accessible_table' ? 'bg-white text-emerald-900 shadow-sm border border-emerald-200' : 'text-ink-600 hover:text-ink-900' }}"
                        id="mobile-tab-table"
                    >
                        <i class="fas fa-table text-brand-blue"></i>
                        <span>Accessible Table</span>
                    </button>
                </div>
            </div>

            <!-- Screen reader live region announcing view switches -->
            <div class="sr-only" aria-live="polite">
                Current coverage view: {{ $mapView === 'visual' ? 'Interactive Visual Map loaded' : 'Accessible Table view loaded' }}
            </div>

            <!-- Filter Bar Component -->
            <div class="bg-ink-50/70 rounded-lg p-4 border border-ink-200 space-y-3">
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
                            class="w-full pl-9 pr-8 py-2 bg-white border border-ink-300 rounded-md text-xs text-ink-900 placeholder-ink-400 focus:outline-none focus:ring-2 focus:ring-brand-emerald focus:border-brand-emerald transition touch-target"
                            aria-label="Search states, organisations, and funders"
                        />
                        <span wire:loading.delay wire:target="search,selectedZone,selectedStage,selectedOrg,selectedFunder,hasCtc" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-brand-emerald">
                            <i class="fas fa-circle-notch fa-spin text-xs"></i>
                        </span>
                        @if(!empty($search))
                            <button
                                type="button"
                                wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-ink-400 hover:text-ink-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald rounded-xs"
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
                            class="w-full px-3 py-2 bg-white border border-ink-300 rounded-md text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-brand-emerald focus:border-brand-emerald transition touch-target"
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
                            class="w-full px-3 py-2 bg-white border border-ink-300 rounded-md text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-brand-emerald focus:border-brand-emerald transition touch-target"
                            aria-label="Filter by Lifecycle Stage"
                        >
                            <option value="">All Lifecycle Stages (8)</option>
                            @foreach($stages as $st)
                                <option value="{{ $st->sequence_order }}">Stage {{ $st->sequence_order }}: {{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Supporting Organization Dropdown -->
                    <div>
                        <select
                            wire:model.live="selectedOrg"
                            class="w-full px-3 py-2 bg-white border border-ink-300 rounded-md text-xs text-ink-800 focus:outline-none focus:ring-2 focus:ring-brand-emerald focus:border-brand-emerald transition touch-target"
                            aria-label="Filter by Lead Organization"
                        >
                            <option value="">Filter by Lead Organisation...</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Secondary Row: Funder, CTC, and Clear Button -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-ink-200/60">
                    <div>
                        <select
                            wire:model.live="selectedFunder"
                            class="w-full px-3 py-2 bg-white border border-ink-300 rounded-md text-xs text-ink-800 focus:outline-none focus:ring-2 focus:ring-brand-emerald focus:border-brand-emerald transition touch-target"
                            aria-label="Filter by Funding Partner"
                        >
                            <option value="">Filter by Funding Partner...</option>
                            @foreach($funders as $funder)
                                <option value="{{ $funder->id }}">{{ $funder->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select
                            wire:model.live="hasCtc"
                            class="w-full px-3 py-2 bg-white border border-ink-300 rounded-md text-xs text-ink-800 focus:outline-none focus:ring-2 focus:ring-brand-emerald focus:border-brand-emerald transition touch-target"
                            aria-label="Filter by Document Availability"
                        >
                            <option value="">Document Status (All)</option>
                            <option value="yes">CTC Document Available</option>
                            <option value="no">CTC Not Yet Uploaded</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-3">
                        @if($hasActiveFilters)
                            <button
                                type="button"
                                wire:click="resetFilters()"
                                class="inline-flex items-center px-3 py-2 text-xs font-semibold text-rose-700 hover:text-white bg-rose-50 hover:bg-rose-600 rounded-md border border-rose-200 transition shrink-0 touch-target focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500"
                            >
                                <i class="fas fa-rotate-left mr-1.5"></i> Clear All Filters
                            </button>
                        @endif
                        <span class="text-xs text-ink-600 font-medium tabular-nums">
                            Showing {{ count($filteredStates) }} of {{ $kpis['total_states'] }} jurisdictions
                        </span>
                    </div>
                </div>

                <!-- Active Filter Chips -->
                @if($hasActiveFilters)
                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-ink-200/60">
                        <span class="text-2xs font-bold uppercase tracking-wider text-ink-500">Active Filters:</span>

                        @if(!empty($search))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ink-100 text-ink-800 text-xs border border-ink-200">
                                Keyword: "{{ $search }}"
                                <button type="button" wire:click="removeFilter('search')" class="text-ink-400 hover:text-rose-600 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if(!empty($selectedZone))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ink-100 text-ink-800 text-xs border border-ink-200">
                                Zone: {{ $selectedZone }}
                                <button type="button" wire:click="removeFilter('zone')" class="text-ink-400 hover:text-rose-600 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if($selectedStage !== '')
                            @php
                                $activeStageObj = $stages->firstWhere('sequence_order', (int)$selectedStage);
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ink-100 text-ink-800 text-xs border border-ink-200">
                                Stage: {{ $activeStageObj->name ?? $selectedStage }}
                                <button type="button" wire:click="removeFilter('stage')" class="text-ink-400 hover:text-rose-600 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if(!empty($selectedOrg))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ink-100 text-ink-800 text-xs border border-ink-200">
                                Org Filter Active
                                <button type="button" wire:click="removeFilter('org')" class="text-ink-400 hover:text-rose-600 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if(!empty($selectedFunder))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ink-100 text-ink-800 text-xs border border-ink-200">
                                Funder Filter Active
                                <button type="button" wire:click="removeFilter('funder')" class="text-ink-400 hover:text-rose-600 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif

                        @if(!empty($hasCtc))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-ink-100 text-ink-800 text-xs border border-ink-200">
                                CTC: {{ $hasCtc === 'yes' ? 'Available' : 'Missing' }}
                                <button type="button" wire:click="removeFilter('ctc')" class="text-ink-400 hover:text-rose-600 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"><i class="fas fa-xmark text-2xs"></i></button>
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Interactive Stage Legend Filter Strip (Global Benchmark Feature) -->
            <div class="bg-ink-50/80 rounded-lg p-3 border border-ink-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider text-ink-600 flex items-center gap-1.5">
                        <i class="fas fa-layer-group text-brand-emerald"></i> Quick Filter Map By Lifecycle Stage:
                    </span>
                    @if($selectedStage !== '')
                        <button
                            type="button"
                            wire:click="$set('selectedStage', '')"
                            class="text-2xs text-rose-600 hover:text-rose-800 font-semibold underline focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-rose-500 rounded-xs"
                        >
                            Reset Stage Highlight
                        </button>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach($stages as $st)
                        <button
                            type="button"
                            wire:click="$set('selectedStage', '{{ (string)$st->sequence_order === (string)$selectedStage ? '' : $st->sequence_order }}')"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-2xs font-semibold transition border focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald {{ (string)$st->sequence_order === (string)$selectedStage ? 'ring-2 ring-brand-emerald shadow-2xs font-extrabold scale-[1.02] bg-white' : 'hover:bg-white hover:shadow-2xs opacity-90 hover:opacity-100' }}"
                            style="background-color: {{ (string)$st->sequence_order === (string)$selectedStage ? '#FFFFFF' : $st->bg_color_hex }}; color: {{ $st->text_color_hex }}; border-color: {{ $st->border_color_hex ?? $st->color_hex }};"
                            aria-label="Filter by stage {{ $st->name }}"
                        >
                            <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $st->color_hex }};"></span>
                            <span>{{ $st->sequence_order }}: {{ $st->name }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- National Map Visual Anchor -->
            @if($mapView === 'visual')
                <div id="coverage-map-panel" role="tabpanel" aria-labelledby="desktop-tab-visual mobile-tab-visual">
                    <x-nigeria-map
                        :states="$filteredStates"
                        :stages="$stages"
                        :selectedSlug="$stateDrawerSlug"
                    />
                </div>
            @else
                <!-- Accessible Tabular Alternative for the Map -->
                <div id="coverage-table-panel" role="tabpanel" aria-labelledby="desktop-tab-table mobile-tab-table" class="bg-white rounded-xl border border-ink-200 shadow-card p-4 sm:p-6 text-ink-900">
                    <div class="flex justify-between items-center mb-4 pb-3 border-b border-ink-100">
                        <div>
                            <h3 class="text-base font-bold text-ink-900 font-heading">Accessible State Policy Summary Table</h3>
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

    <!-- UPDATES & STORIES (Editorial Feed: 3-Card Recent Policy Activity Sourced from Real DB Records) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4" aria-label="Recent Policy Activity">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-ink-200">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Verified Evidence Updates</span>
                <h2 class="text-2xl font-bold text-ink-900 font-heading">Recent Policy Activity</h2>
            </div>
            <a href="/dashboard" class="text-xs font-semibold text-emerald-800 hover:text-emerald-900 underline underline-offset-2 flex items-center gap-1">
                View complete analytics dashboard <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($recentActivities as $activity)
                <div class="bg-white rounded-lg border border-ink-200 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-ink-900">{{ $activity->region->name }} State</span>
                            <span class="text-2xs text-ink-500 font-medium">
                                {{ $activity->data_verified_at ? $activity->data_verified_at->format('d M Y') : 'Verified' }}
                            </span>
                        </div>

                        <div>
                            <x-stage-badge
                                :stage="$activity->stage"
                                :isStalled="$activity->is_stalled"
                                :isExpired="$activity->is_expired"
                                size="sm"
                            />
                        </div>

                        <p class="text-xs text-ink-600 line-clamp-3 leading-relaxed">
                            {{ $activity->objectives_summary ?: ($activity->source_notes ?: 'Policy domestication framework verified with active civil society and ministerial coordination.') }}
                        </p>

                        @if($activity->region->leadingOrganizations->isNotEmpty())
                            <div class="text-2xs text-ink-500">
                                <span class="font-semibold text-ink-700">Lead:</span> {{ $activity->region->leadingOrganizations->first()->name }}
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 mt-4 border-t border-ink-100 flex items-center justify-between">
                        <button
                            type="button"
                            wire:click="openDrawer('{{ $activity->region->slug }}')"
                            class="text-xs font-semibold text-emerald-800 hover:text-emerald-900 underline underline-offset-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald rounded-xs"
                        >
                            Open State Dossier &rarr;
                        </button>
                        <span class="text-2xs font-mono text-ink-400">{{ $activity->region->zone }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white p-6 rounded-lg border border-ink-200 text-center text-xs text-ink-500">
                    All sub-national verified activity milestones are indexed directly in the register below.
                </div>
            @endforelse
        </div>
    </section>

    <!-- INFOGRAPHICS & REPORTS: PRIMARY SOURCE TRANSPARENCY (Certified True Copy Vault Spotlight) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4" aria-label="Primary Source Transparency">
        <div class="bg-white rounded-xl border border-ink-200 p-6 sm:p-8 shadow-card space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-ink-200">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Primary Source Verification</span>
                    <h2 class="text-2xl font-bold text-ink-900 font-heading">
                        Primary Source Transparency: Certified True Copies (CTC) Vault
                    </h2>
                    <p class="text-xs sm:text-sm text-ink-600 max-w-3xl">
                        Every status claim on this tracker is substantiated by primary evidence. We preserve official State Executive Council gazettes, enacted bills, and validation communiques so citizens and researchers can verify every milestone directly.
                    </p>
                </div>

                <a
                    href="/documents"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-md text-xs font-bold text-white bg-brand-emerald hover:bg-emerald-700 active:scale-[0.99] transition shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald shrink-0"
                >
                    <i class="fas fa-box-archive mr-2"></i> Browse Document Vault
                </a>
            </div>

            <!-- 3 Category Transparency Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                <div class="p-4 rounded-lg border border-ink-200 bg-ink-50/50 space-y-2">
                    <div class="w-8 h-8 rounded-md bg-blue-100 text-brand-blue flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3 class="text-sm font-bold text-ink-900 font-heading">Official State Gazettes</h3>
                    <p class="text-ink-600 leading-relaxed">
                        Statutory publications and legislative notices signed by state executive governors establishing formal legal domestication.
                    </p>
                    <a href="/documents?type=gazette" class="inline-block pt-1 font-semibold text-emerald-800 hover:underline">
                        Filter Gazettes &rarr;
                    </a>
                </div>

                <div class="p-4 rounded-lg border border-ink-200 bg-ink-50/50 space-y-2">
                    <div class="w-8 h-8 rounded-md bg-emerald-100 text-brand-emerald flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-file-lines"></i>
                    </div>
                    <h3 class="text-sm font-bold text-ink-900 font-heading">Domesticated Policy Frameworks</h3>
                    <p class="text-ink-600 leading-relaxed">
                        Complete policy documents detailing strategic priorities, youth employment targets, and multi-year implementation timelines.
                    </p>
                    <a href="/documents?type=policy" class="inline-block pt-1 font-semibold text-emerald-800 hover:underline">
                        Filter Frameworks &rarr;
                    </a>
                </div>

                <div class="p-4 rounded-lg border border-ink-200 bg-ink-50/50 space-y-2">
                    <div class="w-8 h-8 rounded-md bg-purple-100 text-brand-violet flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                    <h3 class="text-sm font-bold text-ink-900 font-heading">Validation Communiques</h3>
                    <p class="text-ink-600 leading-relaxed">
                        Stakeholder validation records signed by state youth councils, civil society coalitions, and participating ministries.
                    </p>
                    <a href="/documents?type=communique" class="inline-block pt-1 font-semibold text-emerald-800 hover:underline">
                        Filter Communiques &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOCUS AREAS / WHAT WE TRACK (4 Pillars Linking into Filters/Tabs) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4" aria-label="What We Track">
        <div class="text-left space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Methodological Pillars</span>
            <h2 class="text-2xl font-bold text-ink-900 font-heading">What We Track: Four Pillars of Domestication</h2>
            <p class="text-xs sm:text-sm text-ink-600 max-w-2xl">
                An objective accountability framework assessing the transition from national policy commitments into verified state-level implementation.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div class="bg-white p-5 rounded-lg border border-ink-200 shadow-sm hover:shadow-md transition space-y-2.5">
                <div class="w-8 h-8 rounded-md bg-orange-100 text-brand-orange flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h3 class="text-sm font-bold text-ink-900 font-heading">1. Domestication &amp; Assent</h3>
                <p class="text-ink-600 leading-relaxed">
                    Monitoring formal executive sign-off, legislative passage, and statutory gazette publication across jurisdictions.
                </p>
                <button
                    type="button"
                    wire:click="$set('selectedStage', '6')"
                    class="font-semibold text-emerald-800 hover:underline pt-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"
                >
                    View Enacted Policies (Stage 6) &rarr;
                </button>
            </div>

            <div class="bg-white p-5 rounded-lg border border-ink-200 shadow-sm hover:shadow-md transition space-y-2.5">
                <div class="w-8 h-8 rounded-md bg-emerald-100 text-brand-emerald flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3 class="text-sm font-bold text-ink-900 font-heading">2. Field Implementation</h3>
                <p class="text-ink-600 leading-relaxed">
                    Tracking established coordinating agencies, state budgetary disbursements, and operational youth program rollouts.
                </p>
                <button
                    type="button"
                    wire:click="$set('selectedStage', '7')"
                    class="font-semibold text-emerald-800 hover:underline pt-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"
                >
                    View Active Frameworks (Stage 7) &rarr;
                </button>
            </div>

            <div class="bg-white p-5 rounded-lg border border-ink-200 shadow-sm hover:shadow-md transition space-y-2.5">
                <div class="w-8 h-8 rounded-md bg-blue-100 text-brand-blue flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-people-group"></i>
                </div>
                <h3 class="text-sm font-bold text-ink-900 font-heading">3. Civil Society Co-Creation</h3>
                <p class="text-ink-600 leading-relaxed">
                    Evaluating participatory consultations, youth council leadership, and non-state coalition co-design milestones.
                </p>
                <button
                    type="button"
                    wire:click="$set('selectedStage', '3')"
                    class="font-semibold text-emerald-800 hover:underline pt-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"
                >
                    View Developing Policies (Stage 3) &rarr;
                </button>
            </div>

            <div class="bg-white p-5 rounded-lg border border-ink-200 shadow-sm hover:shadow-md transition space-y-2.5">
                <div class="w-8 h-8 rounded-md bg-purple-100 text-brand-violet flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-database"></i>
                </div>
                <h3 class="text-sm font-bold text-ink-900 font-heading">4. Open Data &amp; Evidence</h3>
                <p class="text-ink-600 leading-relaxed">
                    Providing public REST API access and full CSV downloads for journalists, researchers, and civil society advocates.
                </p>
                <a
                    href="/api/docs"
                    class="inline-block font-semibold text-emerald-800 hover:underline pt-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald rounded-xs"
                >
                    Explore Public REST API &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- METHODOLOGY: 8-STAGE HORIZONTAL PROGRESSION JOURNEY -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Lifecycle Journey">
        <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-card">
            <div class="mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Standardised Methodology</span>
                <h2 class="text-xl font-bold text-ink-900 font-heading">How a State Policy Moves From Idea to Implementation</h2>
                <p class="text-xs text-ink-600 mt-1">
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
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="State Policy Register">
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
                                    <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort text-ink-400 ml-1"></i>
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-4 cursor-pointer hover:text-brand-blue" wire:click="sortBy('zone')">
                                Zone
                                @if($sortField === 'zone')
                                    <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} ml-1"></i>
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-4 cursor-pointer hover:text-brand-blue" wire:click="sortBy('stage')">
                                Domestication Status
                                @if($sortField === 'stage')
                                    <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} ml-1"></i>
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

    <!-- PARTNER AND DATA SOURCES STRIP (Real Partners Only: GMI, Real CSOs, Real Donors; No Fabricated Endorsements) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Partners and Data Sources">
        <div class="bg-ink-100/60 rounded-xl p-8 border border-ink-200 text-center">
            <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block mb-1">Collaborative Open-Data Coalition</span>
            <h2 class="text-lg font-bold text-ink-900 font-heading mb-3">Initiative Leadership &amp; Partner Coalitions</h2>
            <p class="text-xs text-ink-600 max-w-2xl mx-auto mb-6">
                An evidence partnership led by <a href="https://glowingmindsinitiatives.org/" target="_blank" rel="noopener noreferrer" class="font-semibold text-emerald-800 underline underline-offset-2 hover:text-emerald-900">Glowing Minds Initiatives (GMI)</a> in coordination with sub-national youth councils, civil society organisations, and development partners.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3 max-w-4xl mx-auto">
                <span class="px-3.5 py-2 rounded-lg bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-900 shadow-xs flex items-center gap-2">
                    <i class="fas fa-landmark text-brand-emerald"></i> Glowing Minds Initiatives (GMI) &bull; Lead Trustee
                </span>
                @foreach($organizations->take(8) as $org)
                    <span class="px-3 py-1.5 rounded-lg bg-white border border-ink-200 text-xs font-semibold text-ink-800 shadow-xs">
                        <i class="fas fa-building mr-1.5 text-brand-teal-text"></i> {{ $org->name }}
                    </span>
                @endforeach
                @foreach($funders->take(6) as $funder)
                    <span class="px-3 py-1.5 rounded-lg bg-white border border-ink-200 text-xs font-semibold text-ink-800 shadow-xs">
                        <i class="fas fa-hand-holding-dollar mr-1.5 text-brand-violet-text"></i> {{ $funder->name }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ SECTION (Methodology & Public Governance) -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Frequently Asked Questions">
        <div class="text-center mb-8">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 block">Methodology &amp; Verification</span>
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

    <!-- State Drawer Loading Indicator -->
    <div wire:loading.flex wire:target="openDrawer" class="fixed inset-0 z-50 items-center justify-center bg-ink-950/40 backdrop-blur-xs">
        <div class="bg-white rounded-xl p-5 shadow-xl border border-ink-200 flex items-center gap-3">
            <i class="fas fa-circle-notch fa-spin text-brand-emerald text-lg"></i>
            <span class="text-sm font-semibold text-ink-800">Loading State Policy Dossier...</span>
        </div>
    </div>

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
                        <!-- Primary Stage Status Card -->
                        @php
                            $dossierStatus = $selectedRegion->policyStatus;
                            $dossierStage = $dossierStatus?->stage;
                        @endphp
                        <div class="bg-ink-50 rounded-xl p-5 border border-ink-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-2xs font-bold uppercase tracking-wider text-ink-500">Current Domestication Milestone</span>
                                <span class="text-2xs text-ink-500">
                                    Verified: {{ $dossierStatus?->data_verified_at ? $dossierStatus->data_verified_at->format('M d, Y') : 'Pending Verification' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <x-stage-badge
                                    :stage="$dossierStage"
                                    :isStalled="$dossierStatus?->is_stalled"
                                    :isExpired="$dossierStatus?->is_expired"
                                    size="lg"
                                />
                                @if($dossierStatus?->year_commenced)
                                    <span class="text-xs text-ink-600 font-medium">Cycle: {{ $dossierStatus->year_commenced }}</span>
                                @endif
                            </div>

                            @if($dossierStatus?->is_stalled && $dossierStatus?->stalled_reason)
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-900 text-xs">
                                    <strong>Stalled Status Note:</strong> {{ $dossierStatus->stalled_reason }}
                                </div>
                            @endif

                            @if($dossierStatus?->is_expired && $dossierStatus?->expired_reason)
                                <div class="p-3 bg-rose-50 border border-rose-200 rounded-lg text-rose-900 text-xs">
                                    <strong>Expired Status Note:</strong> {{ $dossierStatus->expired_reason }}
                                </div>
                            @endif

                            @if($dossierStatus?->objectives_summary)
                                <div class="text-xs text-ink-700 leading-relaxed pt-2 border-t border-ink-200">
                                    <strong>Summary of Objectives:</strong>
                                    <p class="mt-1">{{ $dossierStatus->objectives_summary }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Focus Areas Tag Cloud -->
                        @if($selectedRegion->focusAreas->isNotEmpty())
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-ink-500 mb-2">Priority Focus Areas</h3>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($selectedRegion->focusAreas as $fa)
                                        <span class="px-2.5 py-1 rounded-full text-2xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            {{ $fa->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Supporting Organisations -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-ink-500 mb-2">Lead Implementing &amp; Supporting Organisations</h3>
                            @if($selectedRegion->organizations->isNotEmpty())
                                <ul class="divide-y divide-ink-100 border border-ink-200 rounded-lg bg-white overflow-hidden">
                                    @foreach($selectedRegion->organizations as $org)
                                        <li class="p-3 flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-ink-900 block">{{ $org->name }}</span>
                                                @if($org->pivot->role)
                                                    <span class="text-2xs text-ink-500 uppercase">{{ $org->pivot->role }} partner</span>
                                                @endif
                                            </div>
                                            <span class="text-2xs font-semibold px-2 py-0.5 rounded bg-ink-100 text-ink-700 uppercase">
                                                {{ $org->type }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-ink-400 italic bg-ink-50 p-3 rounded border border-ink-200">No supporting organisations documented for this jurisdiction yet.</p>
                            @endif
                        </div>

                        <!-- Funding Partners -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-ink-500 mb-2">Funding &amp; Technical Donors</h3>
                            @if($selectedRegion->funders->isNotEmpty())
                                <ul class="divide-y divide-ink-100 border border-ink-200 rounded-lg bg-white overflow-hidden">
                                    @foreach($selectedRegion->funders as $funder)
                                        <li class="p-3 flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-bold text-ink-900 block">{{ $funder->name }}</span>
                                                @if($funder->acronym)
                                                    <span class="text-2xs text-ink-500">({{ $funder->acronym }})</span>
                                                @endif
                                            </div>
                                            <span class="text-2xs font-semibold px-2 py-0.5 rounded bg-purple-50 text-brand-violet uppercase">
                                                {{ $funder->type }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-xs text-ink-400 italic bg-ink-50 p-3 rounded border border-ink-200">No external funding partners documented yet.</p>
                            @endif
                        </div>

                        <!-- Certified True Copies & Official Policy Documents -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-ink-500 mb-2">Certified Documents (CTC Vault)</h3>
                            @if($selectedRegion->publicDocuments->isNotEmpty())
                                <ul class="divide-y divide-ink-100 border border-ink-200 rounded-lg bg-white overflow-hidden">
                                    @foreach($selectedRegion->publicDocuments as $doc)
                                        <li class="p-3 flex items-center justify-between gap-3 text-xs">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <i class="fas fa-file-pdf text-rose-500 text-base shrink-0"></i>
                                                <div class="truncate">
                                                    <span class="font-bold text-ink-900 block truncate">{{ $doc->title }}</span>
                                                    <span class="text-2xs text-ink-500">{{ $doc->document_type }} &bull; {{ $doc->year }} &bull; {{ $doc->formatted_size }}</span>
                                                </div>
                                            </div>
                                            <a
                                                href="/documents/{{ $doc->uuid }}/download"
                                                target="_blank"
                                                class="px-2.5 py-1 text-2xs font-semibold rounded bg-brand-blue text-white hover:bg-blue-700 transition shrink-0"
                                            >
                                                Download CTC
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="bg-amber-50/70 border border-amber-200 rounded-lg p-3 text-xs text-amber-900">
                                    <strong>No Certified True Copy (CTC) Document on File</strong>
                                    <p class="mt-0.5 text-2xs text-amber-800">Ministries and partners may submit verified documents to the secretariat for review and publication.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Transition Timeline History -->
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-ink-500 mb-2">Domestication Timeline &amp; Transition History</h3>
                            @if($selectedRegion->statusHistories->isNotEmpty())
                                <div class="relative border-l-2 border-ink-200 ml-3 pl-4 space-y-4">
                                    @foreach($selectedRegion->statusHistories->sortByDesc('transitioned_date') as $hist)
                                        <div class="relative">
                                            <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-brand-blue ring-4 ring-white"></span>
                                            <div class="text-xs">
                                                <span class="font-bold text-ink-900">{{ $hist->stage?->name }}</span>
                                                <span class="text-2xs text-ink-500 ml-2">{{ $hist->transitioned_date ? $hist->transitioned_date->format('M d, Y') : '' }}</span>
                                                @if($hist->change_notes)
                                                    <p class="text-2xs text-ink-600 mt-0.5">{{ $hist->change_notes }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-ink-400 italic bg-ink-50 p-3 rounded border border-ink-200">No historical transition log available for this record.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Drawer Footer Actions -->
                    <div class="p-4 bg-ink-50 border-t border-ink-200 flex items-center justify-between shrink-0">
                        <button
                            type="button"
                            @click="close()"
                            class="px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-200 rounded transition border border-ink-300 touch-target"
                        >
                            Close Dossier
                        </button>
                        <div class="flex items-center gap-3">
                            <a
                                href="/api/v1/states/{{ $selectedRegion->slug }}?format=json"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center text-xs font-semibold text-brand-blue hover:underline"
                            >
                                <i class="fas fa-code mr-1"></i> View Raw JSON
                            </a>
                            <a
                                href="/api/docs?state={{ $selectedRegion->slug }}"
                                class="inline-flex items-center text-xs font-semibold text-emerald-800 hover:underline"
                            >
                                <i class="fas fa-book-open mr-1"></i> API Docs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MOBILE QUICK-JUMP: BACK TO MAP PILL (< md viewport only) -->
    <div
        x-data="{
            visible: false,
            init() {
                const target = document.getElementById('coverage');
                if (!target || typeof IntersectionObserver === 'undefined') return;
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        // Visible when user has scrolled past the coverage/map section
                        this.visible = !entry.isIntersecting && entry.boundingClientRect.top < 0;
                    });
                }, { threshold: 0.05 });
                observer.observe(target);
            },
            scrollToMap() {
                const target = document.getElementById('coverage');
                if (!target) return;
                const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const y = target.getBoundingClientRect().top + window.pageYOffset;
                window.scrollTo({ top: y, behavior: prefersReduced ? 'auto' : 'smooth' });
                target.scrollIntoView({ behavior: prefersReduced ? 'auto' : 'smooth' });
                const focusable = target.querySelector('button, input, a');
                if (focusable) {
                    focusable.focus({ preventScroll: true });
                }
            }
        }"
        x-show="visible"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="md:hidden fixed bottom-6 right-4 z-40 pb-[env(safe-area-inset-bottom)]"
        aria-live="polite"
    >
        <button
            type="button"
            @click="scrollToMap()"
            class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-full bg-navy-royal text-white text-xs font-bold shadow-xl border border-white/20 active:scale-95 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-royal touch-target"
            aria-label="Back to the policy map"
            id="mobile-back-to-map"
        >
            <i class="fas fa-arrow-up text-xs"></i>
            <span>Back to Map</span>
        </button>
    </div>
</div>
