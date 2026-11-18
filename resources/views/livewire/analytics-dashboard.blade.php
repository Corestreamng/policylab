<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-ink-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold bg-brand-orange/10 text-brand-orange-text">
                    Sub-National Intelligence
                </span>
                <span class="text-xs text-ink-500">Live Database Analytics</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 font-heading">
                National Youth Policy Domestication Analytics
            </h1>
            <p class="text-xs sm:text-sm text-ink-600 mt-1">
                Cross-sectional distribution, geopolitical zone progress, and stakeholder investment metrics across {{ config('tracker.country_name', 'Nigeria') }}.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-button href="/api/v1/export/csv" variant="secondary" size="sm" icon="fa-file-csv">
                {{ __('tracker.export_csv') }}
            </x-button>
            <x-button href="/api/v1/states" target="_blank" variant="secondary" size="sm" icon="fa-code">
                {{ __('tracker.export_json') }}
            </x-button>
            <x-button href="/" variant="primary" size="sm" icon="fa-map-location-dot">
                {{ __('tracker.nav_map') }}
            </x-button>
        </div>
    </div>

    <!-- Multi-Colour Top KPI Strip -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <x-kpi-card
            title="Signed Policies"
            :value="$kpis['signed']"
            icon="fa-file-signature"
            borderColor="border-brand-orange"
            iconBg="bg-orange-50 text-brand-orange-text"
            subtitle="Executive enactment"
        />
        <x-kpi-card
            title="Implementing"
            :value="$kpis['implementing']"
            icon="fa-rocket"
            borderColor="border-brand-emerald"
            iconBg="bg-emerald-50 text-brand-emerald-text"
            subtitle="Operational framework"
        />
        <x-kpi-card
            title="Stalled / Expired"
            :value="$kpis['stalled_expired']"
            icon="fa-triangle-exclamation"
            borderColor="border-brand-rose"
            iconBg="bg-rose-50 text-brand-rose-text"
            subtitle="Action required"
        />
        <x-kpi-card
            title="No Verifiable Data"
            :value="$kpis['no_data']"
            icon="fa-circle-question"
            borderColor="border-brand-slate"
            iconBg="bg-slate-100 text-brand-slate-text"
            subtitle="Awaiting evidence"
        />
        <x-kpi-card
            title="Partner CSOs"
            :value="$kpis['active_orgs']"
            icon="fa-building"
            borderColor="border-brand-teal"
            iconBg="bg-teal-50 text-brand-teal-text"
            subtitle="Technical partners"
        />
        <x-kpi-card
            title="Funding Donors"
            :value="$kpis['active_funders']"
            icon="fa-hand-holding-dollar"
            borderColor="border-brand-violet"
            iconBg="bg-purple-50 text-brand-violet-text"
            subtitle="Bilateral & philanthropic"
        />
    </div>

    <!-- Charts Row (Lazy-loaded via Chart.js) -->
    <div
        x-data="{
            initCharts() {
                const stageLabels = @js($stageLabels);
                const stageData = @js($stageDistribution);
                const stageColors = @js($stageColors);

                const zoneLabels = @js(collect($zoneData)->pluck('zone'));
                const zoneActive = @js(collect($zoneData)->pluck('active'));
                const zoneNoData = @js(collect($zoneData)->pluck('no_data'));

                const configs = [
                    {
                        canvasId: 'stageDistributionChart',
                        options: {
                            type: 'doughnut',
                            data: {
                                labels: stageLabels,
                                datasets: [{
                                    data: stageData,
                                    backgroundColor: stageColors,
                                    borderWidth: 2,
                                    borderColor: '#ffffff',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'right',
                                        labels: { boxWidth: 12, font: { size: 10 } }
                                    }
                                }
                            }
                        }
                    },
                    {
                        canvasId: 'zoneBreakdownChart',
                        options: {
                            type: 'bar',
                            data: {
                                labels: zoneLabels,
                                datasets: [
                                    {
                                        label: 'Active Policy Progress',
                                        data: zoneActive,
                                        backgroundColor: '#059669',
                                    },
                                    {
                                        label: 'No Verifiable Data',
                                        data: zoneNoData,
                                        backgroundColor: '#CBD5E1',
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: { stacked: true, grid: { display: false } },
                                    y: { stacked: true, beginAtZero: true, ticks: { stepSize: 2 } }
                                },
                                plugins: {
                                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } }
                                }
                            }
                        }
                    }
                ];

                window.loadTrackerCharts(configs);
            }
        }"
        x-init="initCharts()"
        class="grid grid-cols-1 lg:grid-cols-2 gap-6"
    >
        <!-- Policy Lifecycle Distribution Donut -->
        <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-card flex flex-col justify-between">
            <div>
                <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block mb-1">Taxonomy Breakdown</span>
                <h2 class="text-base font-bold text-ink-900 font-heading">Policy Lifecycle Distribution (37 Regions)</h2>
                <p class="text-xs text-ink-500 mt-0.5">Proportion of states across all 8 domestication stages.</p>
            </div>
            <div class="h-64 mt-4 relative">
                <canvas id="stageDistributionChart"></canvas>
            </div>
        </div>

        <!-- Geopolitical Zone Bar Breakdown -->
        <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-card flex flex-col justify-between">
            <div>
                <span class="text-2xs font-bold uppercase tracking-wider text-ink-500 block mb-1">Regional Variance</span>
                <h2 class="text-base font-bold text-ink-900 font-heading">Progress by Geopolitical Zone</h2>
                <p class="text-xs text-ink-500 mt-0.5">States with documented domestication milestones vs. unverified states.</p>
            </div>
            <div class="h-64 mt-4 relative">
                <canvas id="zoneBreakdownChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Stakeholder Mapping: Top Organisations & Top Funders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Leading / Supporting Organisations -->
        <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-card">
            <div class="mb-4">
                <span class="text-2xs font-bold uppercase tracking-wider text-brand-teal-text block mb-1">Civil Society & MDAs</span>
                <h2 class="text-base font-bold text-ink-900 font-heading">Top Supporting Organisations by State Count</h2>
                <p class="text-xs text-ink-500">Agencies and civil society coalitions driving state-level policy drafting.</p>
            </div>

            <div class="space-y-3">
                @foreach($topOrgs as $org)
                    @php
                        $percentage = round(($org->regions_count / $kpis['total_states']) * 100, 1);
                    @endphp
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1">
                            <span class="font-bold text-ink-800">{{ $org->name }}</span>
                            <span class="font-semibold text-ink-600">{{ $org->regions_count }} states ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-ink-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-brand-teal h-2 rounded-full" style="width: {{ min($percentage * 3, 100) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Funding Partners -->
        <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-card">
            <div class="mb-4">
                <span class="text-2xs font-bold uppercase tracking-wider text-brand-violet-text block mb-1">Grantmaking & Bilateral Donors</span>
                <h2 class="text-base font-bold text-ink-900 font-heading">Top Funding Partners by State Portfolio</h2>
                <p class="text-xs text-ink-500">Philanthropic and international development donors financing policy programs.</p>
            </div>

            <div class="space-y-3">
                @foreach($topFunders as $funder)
                    @php
                        $percentage = round(($funder->regions_count / $kpis['total_states']) * 100, 1);
                    @endphp
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1">
                            <span class="font-bold text-ink-800">{{ $funder->name }}</span>
                            <span class="font-semibold text-ink-600">{{ $funder->regions_count }} states ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-ink-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-brand-violet h-2 rounded-full" style="width: {{ min($percentage * 3, 100) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
