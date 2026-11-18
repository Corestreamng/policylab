<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-ink-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold bg-brand-teal/10 text-brand-teal-text">
                    Official Repository
                </span>
                <span class="text-xs text-ink-500">Certified True Copies (CTC)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 font-heading">
                Certified Youth Policy Document Vault
            </h1>
            <p class="text-xs sm:text-sm text-ink-600 mt-1">
                Secure public hub for official state gazettes, validated frameworks, and consultative drafts served via signed expiring URLs.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-ink-700 bg-white border border-ink-200 px-3 py-1.5 rounded shadow-xs">
                Total Files: {{ $totalDocs }}
            </span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-xl border border-ink-200 p-4 shadow-card">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-ink-400">
                    <i class="fas fa-magnifying-glass"></i>
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search document title or state..."
                    class="w-full pl-9 pr-8 py-2 bg-ink-50 border border-ink-200 rounded text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-brand-emerald focus:border-brand-emerald touch-target"
                />
                <span wire:loading.delay wire:target="search,selectedRegion,selectedType,selectedYear" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-brand-emerald">
                    <i class="fas fa-circle-notch fa-spin text-xs"></i>
                </span>
            </div>

            <!-- State Filter -->
            <div>
                <select
                    wire:model.live="selectedRegion"
                    class="w-full px-3 py-2 bg-ink-50 border border-ink-200 rounded text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                >
                    <option value="">All States (37)</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Document Type -->
            <div>
                <select
                    wire:model.live="selectedType"
                    class="w-full px-3 py-2 bg-ink-50 border border-ink-200 rounded text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                >
                    <option value="">All Document Types</option>
                    <option value="ctc_enacted">Certified True Copy (Enacted)</option>
                    <option value="policy_draft">Validated Draft Framework</option>
                    <option value="gazette">State Official Gazette</option>
                    <option value="communique">Validation Communique</option>
                    <option value="evaluation">Evaluation Report</option>
                </select>
            </div>

            <!-- Year Filter -->
            <div>
                <select
                    wire:model.live="selectedYear"
                    class="w-full px-3 py-2 bg-ink-50 border border-ink-200 rounded text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-brand-blue touch-target"
                >
                    <option value="">All Years</option>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Loading Skeleton Placeholder -->
    <div wire:loading.grid wire:target="search,selectedRegion,selectedType,selectedYear" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @for($i = 0; $i < 6; $i++)
            <div class="bg-white rounded-xl border border-ink-200 p-5 shadow-xs space-y-4">
                <div class="flex justify-between items-center">
                    <div class="h-4 w-20 rounded-md skeleton-shimmer"></div>
                    <div class="h-3 w-12 rounded-md skeleton-shimmer"></div>
                </div>
                <div class="h-5 w-3/4 rounded-md skeleton-shimmer"></div>
                <div class="space-y-2">
                    <div class="h-3 w-full rounded-md skeleton-shimmer"></div>
                    <div class="h-3 w-2/3 rounded-md skeleton-shimmer"></div>
                </div>
                <div class="pt-3 border-t border-ink-100 flex justify-between">
                    <div class="h-8 w-24 rounded-md skeleton-shimmer"></div>
                    <div class="h-8 w-28 rounded-md skeleton-shimmer"></div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Document Grid / List -->
    <div wire:loading.remove wire:target="search,selectedRegion,selectedType,selectedYear">
    @if($documents->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($documents as $doc)
                @php
                    $downloadUrl = $this->getSignedDownloadUrl($doc);
                @endphp
                <div class="bg-white rounded-xl border border-ink-200 p-5 shadow-card hover:shadow-card-hover transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-2xs font-extrabold uppercase px-2 py-0.5 rounded bg-brand-teal/10 text-brand-teal-text">
                                {{ $doc->region->name }} State
                            </span>
                            @if($doc->year)
                                <span class="text-2xs font-semibold text-ink-500">Year: {{ $doc->year }}</span>
                            @endif
                        </div>

                        <h3 class="text-sm font-bold text-ink-900 leading-snug mb-2 font-heading">
                            {{ $doc->title }}
                        </h3>

                        <div class="space-y-1 text-2xs text-ink-600 mb-4">
                            <div><strong>Type:</strong> <span class="capitalize">{{ str_replace('_', ' ', $doc->document_type) }}</span></div>
                            <div><strong>Supporting Org:</strong> {{ $doc->supportingOrg?->name ?? 'Government Direct' }}</div>
                            <div><strong>File Size:</strong> {{ $doc->formatted_size }} &bull; <strong>Downloads:</strong> {{ $doc->download_count }}</div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-ink-100 flex items-center justify-between gap-2">
                        <button
                            type="button"
                            wire:click="openPreview('{{ $doc->uuid }}')"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-brand-blue hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded transition border border-blue-200 touch-target"
                        >
                            <i class="fas fa-eye mr-1.5"></i> Quick View
                        </button>

                        <a
                            href="{{ $downloadUrl }}"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-brand-emerald hover:bg-emerald-700 rounded transition shadow-xs touch-target"
                        >
                            <i class="fas fa-download mr-1.5"></i> Download PDF
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4 bg-white rounded-lg border border-ink-200">
            {{ $documents->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-ink-200 p-12 text-center text-ink-600 shadow-card">
            <div class="w-16 h-16 rounded-full bg-ink-100 text-ink-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-box-open"></i>
            </div>
            <h2 class="text-lg font-bold text-ink-900 font-heading">No Certified True Copies Found</h2>
            <p class="text-xs text-ink-500 max-w-md mx-auto mt-1 leading-relaxed">
                No policy documents match the active filter criteria. Certified policy copies are actively uploaded as state governments complete validation.
            </p>
        </div>
    @endif
    </div>

    <!-- Fast Preview Modal -->
    @if($previewDocUuid && $previewStreamUrl)
        <div
            x-data="{
                init() {
                    this.$nextTick(() => {
                        window.loadPdfViewer('modal-pdf-container', '{{ $previewStreamUrl }}');
                    });
                }
            }"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/70 backdrop-blur-xs"
            role="dialog"
            aria-modal="true"
        >
            <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-ink-200 overflow-hidden">
                <div class="p-4 bg-ink-900 text-white flex justify-between items-center shrink-0">
                    <h3 class="text-sm font-bold truncate pr-4 font-heading">{{ $previewTitle }}</h3>
                    <button
                        type="button"
                        wire:click="closePreview()"
                        class="p-1.5 text-ink-400 hover:text-white rounded hover:bg-ink-800"
                        aria-label="Close preview modal"
                    >
                        <i class="fas fa-xmark text-base"></i>
                    </button>
                </div>
                <div id="modal-pdf-container" class="flex-1 overflow-y-auto p-4 bg-ink-100 min-h-[400px]">
                    <!-- pdf.js renders canvases here -->
                </div>
                <div class="p-3 bg-ink-50 border-t border-ink-200 flex justify-end shrink-0">
                    <button
                        type="button"
                        wire:click="closePreview()"
                        class="px-4 py-1.5 text-xs font-semibold text-ink-700 bg-white border border-ink-300 rounded hover:bg-ink-100"
                    >
                        Close Preview
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
