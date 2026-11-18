@props([
    'states',
    'stages',
    'selectedSlug' => null,
])

@php
    $customSvgPath = public_path('maps/states.svg');
    $hasCustomSvg = file_exists($customSvgPath);
@endphp

<div
    x-data="{
        hoveredState: null,
        tooltipX: 0,
        tooltipY: 0,
        setHover(state, event) {
            this.hoveredState = state;
            const rect = this.$el.getBoundingClientRect();
            this.tooltipX = event.clientX - rect.left;
            this.tooltipY = event.clientY - rect.top;
        },
        clearHover() {
            this.hoveredState = null;
        }
    }"
    class="relative bg-white rounded-xl border border-ink-200 shadow-card p-4 sm:p-6"
>
    <!-- Map Header with Accessible View Toggle -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 mb-4 border-b border-ink-100">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-ink-900 font-heading">Interactive National Map</h2>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold bg-brand-blue/10 text-brand-blue-text">
                    {{ count($states) }} States Displayed
                </span>
            </div>
            <p class="text-xs text-ink-500 mt-0.5">
                Click any state or navigate with keyboard to inspect policy milestones and certified documentation.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                wire:click="toggleMapView('accessible_table')"
                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded text-ink-700 bg-ink-100 hover:bg-ink-200 border border-ink-200 transition touch-target"
            >
                <i class="fas fa-table mr-1.5 text-ink-500"></i> Accessible Table View
            </button>
        </div>
    </div>

    @if($hasCustomSvg)
        <!-- Custom States SVG (When supplied) -->
        <div class="w-full overflow-hidden flex items-center justify-center min-h-[380px]">
            {!! file_get_contents($customSvgPath) !!}
        </div>
    @else
        <!-- Temporary Geopolitical Tile-Grid Map -->
        <div class="space-y-3">
            <div class="flex items-center justify-between bg-amber-50 border border-amber-200 rounded p-2.5 text-xs text-amber-900">
                <div class="flex items-center gap-2">
                    <i class="fas fa-circle-info text-brand-sunflower-text shrink-0"></i>
                    <span><strong>TEMPORARY TILE-GRID MAP:</strong> Displaying standardized geopolitical cartogram awaiting final <code class="bg-amber-100 px-1 py-0.5 rounded text-2xs font-mono">public/maps/states.svg</code> boundaries.</span>
                </div>
            </div>

            <!-- Cartogram Tile Matrix (6 columns x 7 rows) -->
            <div class="w-full overflow-x-auto pb-2">
                <div class="grid grid-cols-6 gap-2 min-w-[580px] max-w-3xl mx-auto p-4 bg-ink-50/70 rounded-lg border border-dashed border-ink-300">
                    @foreach($states as $state)
                        @php
                            $stage = $state->policyStatus?->stage;
                            $order = $stage->sequence_order ?? 0;
                            $col = $state->grid_col ?? 0;
                            $row = $state->grid_row ?? 0;
                            $color = $stage->color_hex ?? '#64748B';
                            $textColor = $stage->text_color_hex ?? '#334155';
                            $bgColor = $stage->bg_color_hex ?? '#F1F5F9';
                            $isSelected = $selectedSlug === $state->slug;

                            $orgCount = $state->leadingOrganizations->count();
                            $funderCount = $state->funders->count();
                            $docCount = $state->publicDocuments->count();
                        @endphp

                        <button
                            type="button"
                            wire:click="openDrawer('{{ $state->slug }}')"
                            @mouseenter="setHover({
                                name: '{{ addslashes($state->name) }}',
                                zone: '{{ addslashes($state->zone) }}',
                                stage: '{{ addslashes($stage->name ?? 'No Verifiable Data') }}',
                                order: {{ $order }},
                                color: '{{ $color }}',
                                stalled: {{ $state->policyStatus?->is_stalled ? 'true' : 'false' }},
                                expired: {{ $state->policyStatus?->is_expired ? 'true' : 'false' }},
                                orgs: '{{ addslashes($state->leadingOrganizations->pluck('name')->join(', ') ?: 'Not yet documented') }}',
                                funders: '{{ addslashes($state->funders->pluck('name')->join(', ') ?: 'Not yet documented') }}',
                                year: '{{ $state->policyStatus?->year_commenced ?: 'Not yet documented' }}'
                            }, $event)"
                            @mouseleave="clearHover()"
                            @focus="setHover({
                                name: '{{ addslashes($state->name) }}',
                                zone: '{{ addslashes($state->zone) }}',
                                stage: '{{ addslashes($stage->name ?? 'No Verifiable Data') }}',
                                order: {{ $order }},
                                color: '{{ $color }}',
                                stalled: {{ $state->policyStatus?->is_stalled ? 'true' : 'false' }},
                                expired: {{ $state->policyStatus?->is_expired ? 'true' : 'false' }},
                                orgs: '{{ addslashes($state->leadingOrganizations->pluck('name')->join(', ') ?: 'Not yet documented') }}',
                                funders: '{{ addslashes($state->funders->pluck('name')->join(', ') ?: 'Not yet documented') }}',
                                year: '{{ $state->policyStatus?->year_commenced ?: 'Not yet documented' }}'
                            }, $event)"
                            @blur="clearHover()"
                            class="relative p-2.5 rounded-lg border text-left transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue group
                                   {{ $isSelected ? 'ring-2 ring-ink-900 shadow-md scale-105 z-10' : 'hover:shadow-md hover:scale-102 hover:z-10' }}"
                            style="background-color: {{ $bgColor }}; border-color: {{ $color }};"
                            aria-label="{{ $state->name }}: {{ $stage->name ?? 'No Verifiable Data' }}"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-2xs font-extrabold uppercase tracking-wider" style="color: {{ $textColor }};">
                                    {{ $state->iso_code ? str_replace('NG-', '', $state->iso_code) : substr($state->name, 0, 3) }}
                                </span>
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 shadow-xs" style="background-color: {{ $color }};"></span>
                            </div>

                            <span class="block text-xs font-bold truncate leading-tight text-ink-900 group-hover:text-black">
                                {{ $state->name }}
                            </span>

                            <div class="flex items-center gap-1.5 mt-1.5 text-2xs text-ink-500">
                                @if($docCount > 0)
                                    <span title="Certified True Copy available"><i class="fas fa-file-pdf text-brand-emerald-text"></i></span>
                                @endif
                                @if($state->policyStatus?->is_stalled)
                                    <span title="Stalled status" class="text-rose-600 font-bold"><i class="fas fa-pause"></i></span>
                                @endif
                                @if($state->policyStatus?->is_expired)
                                    <span title="Expired status" class="text-amber-600 font-bold"><i class="fas fa-clock-rotate-left"></i></span>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Dynamic Hover Tooltip -->
    <div
        x-show="hoveredState"
        x-cloak
        class="absolute z-30 pointer-events-none bg-ink-950 text-white rounded-lg p-3.5 shadow-xl text-xs max-w-xs transition-opacity duration-150 border border-ink-800"
        :style="`top: ${tooltipY + 15}px; left: ${Math.min(tooltipX + 15, 420)}px;`"
    >
        <template x-if="hoveredState">
            <div class="space-y-1.5">
                <div class="flex items-center justify-between gap-2 border-b border-ink-800 pb-1.5">
                    <span class="font-bold text-sm text-white font-heading" x-text="hoveredState.name"></span>
                    <span class="text-2xs px-2 py-0.5 rounded-full font-semibold uppercase"
                          :style="`background-color: ${hoveredState.color}; color: #ffffff;`"
                          x-text="hoveredState.stage"></span>
                </div>
                <div class="text-ink-400 text-2xs">
                    Zone: <span class="text-ink-200" x-text="hoveredState.zone"></span> | Cycle: <span class="text-ink-200" x-text="hoveredState.year"></span>
                </div>
                <div class="text-2xs text-ink-300">
                    <strong class="text-ink-400 block">Leading Organisation:</strong>
                    <span x-text="hoveredState.orgs"></span>
                </div>
                <div class="text-2xs text-ink-300">
                    <strong class="text-ink-400 block">Funding Partner:</strong>
                    <span x-text="hoveredState.funders"></span>
                </div>
                <template x-if="hoveredState.stalled">
                    <div class="text-2xs font-semibold text-rose-300 bg-rose-950/60 p-1 rounded border border-rose-800">
                        <i class="fas fa-triangle-exclamation mr-1"></i> Policy formulation process is currently stalled.
                    </div>
                </template>
                <template x-if="hoveredState.expired">
                    <div class="text-2xs font-semibold text-amber-300 bg-amber-950/60 p-1 rounded border border-amber-800">
                        <i class="fas fa-clock-rotate-left mr-1"></i> Policy implementation cycle has expired.
                    </div>
                </template>
                <div class="text-2xs text-brand-blue pt-1 text-right font-medium">
                    Click to view full state dossier <i class="fas fa-arrow-right ml-0.5"></i>
                </div>
            </div>
        </template>
    </div>

    <!-- 8-Stage Legend Bar -->
    <div class="mt-6 pt-4 border-t border-ink-100">
        <span class="block text-2xs font-bold uppercase tracking-wider text-ink-500 mb-2">Policy Lifecycle Taxonomy</span>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-2">
            @foreach($stages as $st)
                <button
                    type="button"
                    wire:click="$set('selectedStage', '{{ $st->sequence_order }}')"
                    class="flex items-center gap-1.5 p-1.5 rounded text-left transition hover:bg-ink-100 focus:outline-none focus:ring-1 focus:ring-brand-blue group"
                >
                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $st->color_hex }};"></span>
                    <span class="text-2xs font-medium text-ink-700 truncate group-hover:text-ink-950">
                        {{ $st->sequence_order }}. {{ $st->name }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>
</div>
