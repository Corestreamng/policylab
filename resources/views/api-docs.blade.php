<x-layouts.app title="Public REST API & Developer Hub | Youth Policy Tracker">
    <div
        x-data="{
            activeTab: '{{ $activeEndpoint }}',
            selectedZone: '{{ request()->query('zone', '') }}',
            selectedStage: '{{ request()->query('stage', '') }}',
            selectedSlug: '{{ $selectedStateSlug }}',
            isLoading: false,
            responseStatus: null,
            responseLatency: null,
            responseHeaders: {},
            responseBody: null,
            copiedNotice: '',
            copiedCurl: false,
            copiedJson: false,

            baseUrl: '{{ $baseUrl }}',

            get currentUrl() {
                if (this.activeTab === 'states') {
                    let params = new URLSearchParams();
                    if (this.selectedZone) params.append('zone', this.selectedZone);
                    if (this.selectedStage) params.append('stage', this.selectedStage);
                    let qs = params.toString();
                    return this.baseUrl + '/states' + (qs ? '?' + qs : '');
                }
                if (this.activeTab === 'states-slug') {
                    return this.baseUrl + '/states/' + (this.selectedSlug || 'lagos');
                }
                if (this.activeTab === 'stages') {
                    return this.baseUrl + '/stages';
                }
                if (this.activeTab === 'kpis') {
                    return this.baseUrl + '/kpis';
                }
                if (this.activeTab === 'csv') {
                    return this.baseUrl + '/export/csv';
                }
                return this.baseUrl + '/states';
            },

            get rawJsonUrl() {
                let url = this.currentUrl;
                if (this.activeTab === 'csv') return url;
                return url + (url.includes('?') ? '&' : '?') + 'format=json';
            },

            get curlCommand() {
                if (this.activeTab === 'csv') {
                    return 'curl -s -O ' + this.currentUrl;
                }
                return 'curl -s -H &quot;Accept: application/json&quot; &quot;' + this.currentUrl + '&quot;';
            },

            async executeRequest() {
                if (this.activeTab === 'csv') {
                    window.location.href = this.currentUrl;
                    return;
                }

                this.isLoading = true;
                this.responseStatus = null;
                this.responseBody = null;
                const startTime = performance.now();

                try {
                    const res = await fetch(this.currentUrl, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const duration = Math.round(performance.now() - startTime);
                    this.responseStatus = res.status;
                    this.responseLatency = duration;

                    const data = await res.json();
                    this.responseBody = JSON.stringify(data, null, 2);
                } catch (err) {
                    this.responseStatus = 'Error';
                    this.responseBody = JSON.stringify({ error: err.message }, null, 2);
                } finally {
                    this.isLoading = false;
                }
            },

            copyToClipboard(text, type) {
                navigator.clipboard.writeText(text).then(() => {
                    if (type === 'curl') {
                        this.copiedCurl = true;
                        setTimeout(() => this.copiedCurl = false, 2000);
                    } else if (type === 'json') {
                        this.copiedJson = true;
                        setTimeout(() => this.copiedJson = false, 2000);
                    }
                });
            },

            init() {
                // Automatically run default query on load so users see immediate live data
                this.executeRequest();
            }
        }"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10"
    >
        <!-- Hero Header -->
        <header class="bg-gradient-to-b from-[#FAF9F5] via-[#F6F8F5] to-[#EEF3EA] border border-ink-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Public REST API v1.0 &bull; Open Data
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-ink-900 tracking-tight font-heading">
                        Public REST API & Developer Hub
                    </h1>
                    <p class="text-sm sm:text-base text-ink-700 leading-relaxed">
                        Standardized programmatic access to sub-national youth policy domestication data across all 36 Nigerian States and the Federal Capital Territory (FCT). Published by <a href="https://glowingmindsinitiatives.org/" target="_blank" rel="noopener noreferrer" class="font-semibold text-emerald-800 underline underline-offset-2 hover:text-emerald-900">Glowing Minds Initiatives (GMI)</a>.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row md:flex-col gap-2 shrink-0">
                    <a
                        href="/api/v1/export/csv"
                        class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-brand-emerald hover:bg-emerald-700 active:scale-[0.99] transition shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald"
                    >
                        <i class="fas fa-file-csv mr-2"></i> Download Full Dataset (CSV)
                    </a>
                    <a
                        href="/dashboard"
                        class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-xs font-bold text-ink-700 bg-white border border-ink-300 hover:bg-ink-50 active:scale-[0.99] transition shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald"
                    >
                        <i class="fas fa-chart-pie mr-2 text-brand-orange"></i> Visual Analytics
                    </a>
                </div>
            </div>

            <!-- Meta Specification Badges Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-8 pt-6 border-t border-ink-200/80">
                <div class="bg-white/80 p-3 rounded-lg border border-ink-200">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-ink-500">Base URL</span>
                    <span class="font-mono text-xs font-semibold text-ink-900 truncate block mt-0.5">{{ $baseUrl }}</span>
                </div>
                <div class="bg-white/80 p-3 rounded-lg border border-ink-200">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-ink-500">Authentication</span>
                    <span class="text-xs font-semibold text-emerald-700 flex items-center gap-1.5 mt-0.5">
                        <i class="fas fa-unlock text-[10px]"></i> None (Open Data)
                    </span>
                </div>
                <div class="bg-white/80 p-3 rounded-lg border border-ink-200">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-ink-500">Rate Limit</span>
                    <span class="text-xs font-semibold text-ink-900 mt-0.5 block">60 req / min (IP-based)</span>
                </div>
                <div class="bg-white/80 p-3 rounded-lg border border-ink-200">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-ink-500">License</span>
                    <span class="text-xs font-semibold text-ink-900 mt-0.5 block">PDDL (Open Access)</span>
                </div>
            </div>
        </header>

        <!-- Main Interactive Explorer Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left Column: Endpoint Navigation Directory -->
            <nav class="lg:col-span-4 bg-white rounded-xl border border-ink-200 p-4 shadow-xs space-y-2 sticky top-6" aria-label="API Endpoints">
                <div class="px-3 py-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-ink-500">Available Endpoints</span>
                    <p class="text-xs text-ink-600 mt-0.5">Select an endpoint to test live parameters and view responses.</p>
                </div>

                <!-- Endpoint 1: States -->
                <button
                    type="button"
                    @click="activeTab = 'states'; executeRequest()"
                    :class="activeTab === 'states' ? 'bg-emerald-50 border-brand-emerald text-ink-950 font-semibold' : 'border-transparent text-ink-700 hover:bg-ink-50'"
                    class="w-full text-left p-3 rounded-lg border transition text-xs space-y-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">GET</span>
                        <span class="font-mono text-[11px] text-ink-500">/api/v1/states</span>
                    </div>
                    <p class="text-xs leading-snug">List all 36 States + FCT with policy stages & partners.</p>
                </button>

                <!-- Endpoint 2: Single State Dossier -->
                <button
                    type="button"
                    @click="activeTab = 'states-slug'; executeRequest()"
                    :class="activeTab === 'states-slug' ? 'bg-emerald-50 border-brand-emerald text-ink-950 font-semibold' : 'border-transparent text-ink-700 hover:bg-ink-50'"
                    class="w-full text-left p-3 rounded-lg border transition text-xs space-y-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">GET</span>
                        <span class="font-mono text-[11px] text-ink-500">/api/v1/states/{slug}</span>
                    </div>
                    <p class="text-xs leading-snug">Detailed state dossier, documents, and reform history.</p>
                </button>

                <!-- Endpoint 3: Lifecycle Stages -->
                <button
                    type="button"
                    @click="activeTab = 'stages'; executeRequest()"
                    :class="activeTab === 'stages' ? 'bg-emerald-50 border-brand-emerald text-ink-950 font-semibold' : 'border-transparent text-ink-700 hover:bg-ink-50'"
                    class="w-full text-left p-3 rounded-lg border transition text-xs space-y-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">GET</span>
                        <span class="font-mono text-[11px] text-ink-500">/api/v1/stages</span>
                    </div>
                    <p class="text-xs leading-snug">8-stage policy lifecycle taxonomy, colors & definitions.</p>
                </button>

                <!-- Endpoint 4: KPIs -->
                <button
                    type="button"
                    @click="activeTab = 'kpis'; executeRequest()"
                    :class="activeTab === 'kpis' ? 'bg-emerald-50 border-brand-emerald text-ink-950 font-semibold' : 'border-transparent text-ink-700 hover:bg-ink-50'"
                    class="w-full text-left p-3 rounded-lg border transition text-xs space-y-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">GET</span>
                        <span class="font-mono text-[11px] text-ink-500">/api/v1/kpis</span>
                    </div>
                    <p class="text-xs leading-snug">Nationwide computed metrics and aggregate totals.</p>
                </button>

                <!-- Endpoint 5: CSV Export -->
                <button
                    type="button"
                    @click="activeTab = 'csv'"
                    :class="activeTab === 'csv' ? 'bg-emerald-50 border-brand-emerald text-ink-950 font-semibold' : 'border-transparent text-ink-700 hover:bg-ink-50'"
                    class="w-full text-left p-3 rounded-lg border transition text-xs space-y-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800">CSV</span>
                        <span class="font-mono text-[11px] text-ink-500">/api/v1/export/csv</span>
                    </div>
                    <p class="text-xs leading-snug">Live streaming tabular CSV export for Excel & research.</p>
                </button>
            </nav>

            <!-- Right Column: Interactive Console & Response Viewer -->
            <main class="lg:col-span-8 space-y-6">
                <!-- Endpoint Details & Parameter Form -->
                <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink-200">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-xs font-extrabold bg-emerald-100 text-emerald-800" x-text="activeTab === 'csv' ? 'CSV' : 'GET'">GET</span>
                                <h2 class="text-lg font-bold text-ink-900 font-mono" x-text="activeTab === 'states' ? '/api/v1/states' : (activeTab === 'states-slug' ? '/api/v1/states/{slug}' : (activeTab === 'stages' ? '/api/v1/stages' : (activeTab === 'kpis' ? '/api/v1/kpis' : '/api/v1/export/csv')))"></h2>
                            </div>
                            <p class="text-xs text-ink-600 mt-1" x-show="activeTab === 'states'">
                                Query all 36 Nigerian states and the FCT. Supports filtering by geopolitical zone and lifecycle stage order.
                            </p>
                            <p class="text-xs text-ink-600 mt-1" x-show="activeTab === 'states-slug'">
                                Retrieve the complete evidence dossier, certified documents, lead ministry, partners, and transition history for a single state.
                            </p>
                            <p class="text-xs text-ink-600 mt-1" x-show="activeTab === 'stages'">
                                Retrieve the 8 standardized lifecycle stages from Agenda Setting (0) to Full Implementation (7).
                            </p>
                            <p class="text-xs text-ink-600 mt-1" x-show="activeTab === 'kpis'">
                                Retrieve real-time nationwide aggregation indicators for dashboards and reports.
                            </p>
                            <p class="text-xs text-ink-600 mt-1" x-show="activeTab === 'csv'">
                                Download the entire sub-national dataset formatted as standard CSV for spreadsheets, R, Python, and Stata.
                            </p>
                        </div>

                        <!-- Direct Raw Link -->
                        <div class="shrink-0" x-show="activeTab !== 'csv'">
                            <a
                                :href="rawJsonUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center text-xs font-semibold text-emerald-700 hover:text-emerald-900 underline underline-offset-2"
                            >
                                <i class="fas fa-arrow-up-right-from-square mr-1 text-[11px]"></i> Open Raw JSON
                            </a>
                        </div>
                    </div>

                    <!-- Interactive Parameters (States endpoint) -->
                    <div x-show="activeTab === 'states'" class="space-y-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-500">Query Parameters</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="filter-zone" class="block text-xs font-semibold text-ink-700 mb-1">Geopolitical Zone</label>
                                <select
                                    id="filter-zone"
                                    x-model="selectedZone"
                                    @change="executeRequest()"
                                    class="w-full text-xs rounded-lg border-ink-300 shadow-xs focus:border-brand-emerald focus:ring-brand-emerald"
                                >
                                    <option value="">All Zones (37 Jurisdictions)</option>
                                    @foreach($zones as $z)
                                        <option value="{{ $z }}">{{ $z }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="filter-stage" class="block text-xs font-semibold text-ink-700 mb-1">Lifecycle Stage</label>
                                <select
                                    id="filter-stage"
                                    x-model="selectedStage"
                                    @change="executeRequest()"
                                    class="w-full text-xs rounded-lg border-ink-300 shadow-xs focus:border-brand-emerald focus:ring-brand-emerald"
                                >
                                    <option value="">All Stages (0 through 7)</option>
                                    @foreach($stages as $st)
                                        <option value="{{ $st->sequence_order }}">{{ $st->sequence_order }} &bull; {{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Parameters (Single State endpoint) -->
                    <div x-show="activeTab === 'states-slug'" class="space-y-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-500">Path Parameter</span>
                        <div>
                            <label for="select-slug" class="block text-xs font-semibold text-ink-700 mb-1">Select Jurisdiction ({slug})</label>
                            <select
                                id="select-slug"
                                x-model="selectedSlug"
                                @change="executeRequest()"
                                class="w-full text-xs rounded-lg border-ink-300 shadow-xs focus:border-brand-emerald focus:ring-brand-emerald"
                            >
                                @foreach($states as $st)
                                    <option value="{{ $st->slug }}">{{ $st->name }} ({{ $st->zone }}) &mdash; slug: {{ $st->slug }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Live Request URL Preview & Trigger -->
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-500">Request URL</span>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="flex-1 bg-ink-100/80 px-3 py-2 rounded-lg font-mono text-xs text-ink-900 border border-ink-200 overflow-x-auto truncate">
                                <span x-text="currentUrl"></span>
                            </div>
                            <button
                                type="button"
                                @click="executeRequest()"
                                :disabled="isLoading"
                                class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-xs font-bold text-white bg-brand-emerald hover:bg-emerald-700 active:scale-[0.99] transition shadow-xs disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald shrink-0"
                            >
                                <i class="fas" :class="isLoading ? 'fa-spinner fa-spin mr-1.5' : (activeTab === 'csv' ? 'fa-download mr-1.5' : 'fa-play mr-1.5')"></i>
                                <span x-text="isLoading ? 'Sending...' : (activeTab === 'csv' ? 'Download CSV' : 'Send Request')"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Response Inspector -->
                <div x-show="activeTab !== 'csv'" class="bg-ink-950 rounded-xl border border-ink-800 shadow-card overflow-hidden">
                    <!-- Response Header Bar -->
                    <div class="bg-ink-900/90 px-4 py-3 border-b border-ink-800 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <span class="text-ink-400 font-semibold uppercase tracking-wider text-[11px]">Response</span>
                            <template x-if="responseStatus">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold"
                                    :class="responseStatus === 200 ? 'bg-emerald-900/80 text-emerald-300 border border-emerald-700' : 'bg-rose-900/80 text-rose-300 border border-rose-700'"
                                    x-text="responseStatus === 200 ? '200 OK' : responseStatus"
                                ></span>
                            </template>
                            <template x-if="responseLatency">
                                <span class="text-ink-400 font-mono text-[11px]" x-text="responseLatency + ' ms'"></span>
                            </template>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="copyToClipboard(responseBody, 'json')"
                                :disabled="!responseBody"
                                class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium bg-ink-800 hover:bg-ink-700 text-ink-200 transition focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-brand-emerald"
                            >
                                <i class="fas mr-1.5" :class="copiedJson ? 'fa-check text-emerald-400' : 'fa-copy text-ink-400'"></i>
                                <span x-text="copiedJson ? 'Copied!' : 'Copy JSON'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Code Box Body -->
                    <div class="p-4 relative">
                        <div x-show="isLoading" class="py-12 flex flex-col items-center justify-center text-ink-400 space-y-2">
                            <i class="fas fa-spinner fa-spin text-xl text-brand-emerald"></i>
                            <span class="text-xs">Fetching live response...</span>
                        </div>

                        <pre
                            x-show="!isLoading && responseBody"
                            class="text-xs font-mono text-emerald-400 max-h-96 overflow-y-auto leading-relaxed whitespace-pre"
                        ><code x-text="responseBody"></code></pre>

                        <div x-show="!isLoading && !responseBody" class="py-8 text-center text-xs text-ink-500">
                            Click &ldquo;Send Request&rdquo; to execute query.
                        </div>
                    </div>
                </div>

                <!-- Code Examples (cURL, JavaScript, Python) -->
                <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-500">Developer Quickstart (cURL)</span>
                        <button
                            type="button"
                            @click="copyToClipboard(curlCommand, 'curl')"
                            class="inline-flex items-center text-xs font-semibold text-emerald-700 hover:text-emerald-900"
                        >
                            <i class="fas mr-1" :class="copiedCurl ? 'fa-check text-emerald-600' : 'fa-copy'"></i>
                            <span x-text="copiedCurl ? 'Copied command!' : 'Copy cURL'"></span>
                        </button>
                    </div>

                    <div class="bg-ink-950 p-3.5 rounded-lg border border-ink-800 font-mono text-xs text-emerald-400 overflow-x-auto">
                        <span x-text="curlCommand"></span>
                    </div>

                    <!-- Additional Language Tabs -->
                    <div class="pt-4 border-t border-ink-200 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="space-y-1.5">
                            <span class="font-bold text-ink-900">JavaScript (Fetch API)</span>
                            <pre class="bg-ink-950 p-3 rounded-lg text-[11px] font-mono text-ink-200 overflow-x-auto"><code>const res = await fetch('{{ $baseUrl }}/states');
const { data, meta } = await res.json();
console.log(data);</code></pre>
                        </div>
                        <div class="space-y-1.5">
                            <span class="font-bold text-ink-900">Python (Requests)</span>
                            <pre class="bg-ink-950 p-3 rounded-lg text-[11px] font-mono text-ink-200 overflow-x-auto"><code>import requests

res = requests.get('{{ $baseUrl }}/states')
states = res.json()['data']
print(f"Total jurisdictions: {len(states)}")</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Data Dictionary / Schema Explanation -->
                <div class="bg-white rounded-xl border border-ink-200 p-6 shadow-xs space-y-4 text-xs leading-relaxed text-ink-700">
                    <h3 class="text-sm font-bold text-ink-900 font-heading">Response Schema Reference</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-ink-200 text-left">
                            <thead class="bg-ink-50 text-ink-900 font-semibold">
                                <tr>
                                    <th class="p-2 border border-ink-200">Field</th>
                                    <th class="p-2 border border-ink-200">Type</th>
                                    <th class="p-2 border border-ink-200">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink-200 font-mono text-[11px]">
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">id</td>
                                    <td class="p-2 text-ink-500">integer</td>
                                    <td class="p-2 font-sans">Internal database primary key for jurisdiction.</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">name</td>
                                    <td class="p-2 text-ink-500">string</td>
                                    <td class="p-2 font-sans">Official jurisdiction name (e.g., &ldquo;Lagos&rdquo;, &ldquo;Kano&rdquo;).</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">slug</td>
                                    <td class="p-2 text-ink-500">string</td>
                                    <td class="p-2 font-sans">URL-friendly slug (e.g., &ldquo;fct&rdquo;, &ldquo;cross-river&rdquo;).</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">iso_code</td>
                                    <td class="p-2 text-ink-500">string</td>
                                    <td class="p-2 font-sans">ISO 3166-2:NG sub-division code (e.g., &ldquo;NG-LA&rdquo;).</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">current_stage</td>
                                    <td class="p-2 text-ink-500">object</td>
                                    <td class="p-2 font-sans">Contains sequence_order (0-7), standardized name, and color_hex.</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">is_stalled</td>
                                    <td class="p-2 text-ink-500">boolean</td>
                                    <td class="p-2 font-sans">Flag indicating whether policy domestication has halted.</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">is_expired</td>
                                    <td class="p-2 text-ink-500">boolean</td>
                                    <td class="p-2 font-sans">Flag indicating whether the enacted policy lifecycle has lapsed.</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">leading_organizations</td>
                                    <td class="p-2 text-ink-500">array[string]</td>
                                    <td class="p-2 font-sans">Civil society organisations leading advocacy or implementation.</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-bold text-ink-900">funding_partners</td>
                                    <td class="p-2 text-ink-500">array[string]</td>
                                    <td class="p-2 font-sans">Donor organisations and technical development partners.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Academic & Policy Citation Card -->
                <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-5 text-xs text-emerald-950 space-y-2">
                    <span class="font-bold uppercase tracking-wider text-emerald-800 block text-[11px]">Academic & Policy Citation</span>
                    <p class="leading-relaxed">
                        If you utilize this API or dataset in academic research, civil society reports, or journalistic analysis, please cite:
                    </p>
                    <div class="bg-white p-3 rounded-lg border border-emerald-200 font-mono text-[11px] text-ink-800 select-all">
                        Glowing Minds Initiatives (GMI). (2026). Nigeria Youth Policy Domestication Tracker [Data set]. Retrieved from {{ url('/api/docs') }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-layouts.app>
