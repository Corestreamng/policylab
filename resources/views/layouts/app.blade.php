<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full bg-ink-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'National Youth Policy Tracker | Glowing Minds Initiatives' }}</title>
    <meta name="description" content="A comprehensive open-data platform tracking, visualising, and documenting how the National Youth Policy is domesticated across sub-national jurisdictions.">

    <!-- Open Graph / Twitter Card Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? 'National Youth Policy Tracker' }}">
    <meta property="og:description" content="Tracking and visualising sub-national youth policy domestication across jurisdictions.">
    <meta property="og:site_name" content="Youth Policy Tracker">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Favicon & Site Icons Set (Placeholder until client official brand vector is uploaded) -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0F172A">

    <!-- Self-hosted Font Awesome Free (No External CDNs) -->
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex flex-col font-sans text-ink-900 bg-ink-50 selection:bg-brand-blue selection:text-white">
    <!-- Skip to main content for screen readers -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 bg-brand-blue text-white px-4 py-2 rounded shadow font-semibold">
        Skip to main content
    </a>

    <!-- Top Institutional Banner -->
    <div class="bg-ink-950 text-ink-300 text-xs py-2 px-4 border-b border-ink-800">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-brand-emerald"></span>
                <span>{{ __('tracker.footer_initiative') }} <a href="https://glowingmindsinitiatives.org/" target="_blank" rel="noopener noreferrer" class="text-white hover:text-emerald-400 font-bold underline underline-offset-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald rounded-xs" aria-label="Glowing Minds Initiatives (GMI) — opens in a new tab">{{ config('tracker.client_name') }}</a></span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header (Mature Royal Blue Option A) -->
    <header class="bg-navy-royal text-white sticky top-0 z-40 border-b border-white/10 shadow-sm" x-data="{ mobileMenuOpen: false, langOpen: false, megaOpen: false, mobileZonesOpen: false, mobileStagesOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-white rounded p-1">
                        <div class="w-11 h-11 rounded-xl bg-white p-1.5 flex items-center justify-center shadow-md ring-1 ring-black/10 shrink-0">
                            <img src="{{ asset('img/Policy_Lab_logo.png') }}" alt="Policy Lab Logo" class="w-full h-full object-contain" />
                        </div>
                        <div>
                            <span class="block text-base font-bold tracking-tight text-white font-heading">{{ __('tracker.app_name') }}</span>
                            <span class="block text-2xs uppercase tracking-wider text-blue-100/90">{{ __('tracker.tagline') }}</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1" aria-label="Main Navigation">
                    <a href="/" class="px-3 py-2 rounded text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-royal {{ request()->is('/') && !request()->has('view') ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-2xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-map-location-dot mr-1.5 text-blue-200"></i> {{ __('tracker.nav_map') }}
                    </a>
                    <a href="/dashboard" class="px-3 py-2 rounded text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-royal {{ request()->is('dashboard*') ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-2xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-chart-pie mr-1.5 text-orange-300"></i> {{ __('tracker.nav_analytics') }}
                    </a>
                    <a href="/documents" class="px-3 py-2 rounded text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-royal {{ request()->is('documents*') ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-2xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-box-archive mr-1.5 text-teal-300"></i> {{ __('tracker.nav_vault') }}
                    </a>
                    <a href="/api/docs" class="px-3 py-2 rounded text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-royal {{ request()->is('api*') ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-2xs' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                        <i class="fas fa-code mr-1.5 text-violet-300"></i> {{ __('tracker.nav_api') }}
                    </a>

                    <!-- Mega Menu Dropdown Trigger -->
                    <div class="relative" @keydown.escape="megaOpen = false">
                        <button
                            type="button"
                            @click="megaOpen = !megaOpen"
                            @click.outside="megaOpen = false"
                            class="px-3 py-2 rounded text-sm font-semibold transition flex items-center gap-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-royal"
                            :class="megaOpen ? 'bg-white text-ink-950 shadow-md ring-1 ring-white/30' : 'text-blue-100 hover:text-white hover:bg-white/10'"
                            :aria-expanded="megaOpen.toString()"
                            aria-haspopup="true"
                            aria-label="Explore Tracker Mega Menu"
                        >
                            <i class="fas fa-bars-staggered" :class="megaOpen ? 'text-brand-emerald' : 'text-emerald-300'"></i>
                            <span>Explore Tracker</span>
                            <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="megaOpen ? 'rotate-180 text-brand-emerald' : 'text-blue-200'"></i>
                        </button>

                        <!-- Mega Menu Panel (Bright Scheme) -->
                        <div
                            x-show="megaOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="transform opacity-0 -translate-y-2"
                            x-transition:enter-end="transform opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="transform opacity-100 translate-y-0"
                            x-transition:leave-end="transform opacity-0 -translate-y-2"
                            class="absolute right-0 mt-3 w-[880px] max-w-[94vw] bg-white border border-ink-200 border-t-4 border-t-brand-emerald rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 z-50 text-xs text-ink-700"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <!-- Column 1: Geopolitical Zones -->
                                <div class="space-y-3">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-ink-900 flex items-center gap-1.5 pb-2 border-b border-ink-100">
                                        <i class="fas fa-map-location-dot text-brand-blue"></i> Geopolitical Zones
                                    </span>
                                    <ul class="space-y-1 font-medium">
                                        <li>
                                            <a href="/?zone=North+Central#coverage" @click="megaOpen = false" class="block p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="font-bold text-ink-900 block group-hover:text-brand-blue transition-colors">North Central</span>
                                                <span class="text-[10px] text-ink-500">Benue, FCT, Kogi, Kwara, Niger...</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?zone=North+East#coverage" @click="megaOpen = false" class="block p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="font-bold text-ink-900 block group-hover:text-brand-blue transition-colors">North East</span>
                                                <span class="text-[10px] text-ink-500">Adamawa, Bauchi, Borno, Gombe...</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?zone=North+West#coverage" @click="megaOpen = false" class="block p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="font-bold text-ink-900 block group-hover:text-brand-blue transition-colors">North West</span>
                                                <span class="text-[10px] text-ink-500">Jigawa, Kaduna, Kano, Sokoto...</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?zone=South+East#coverage" @click="megaOpen = false" class="block p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="font-bold text-ink-900 block group-hover:text-brand-blue transition-colors">South East</span>
                                                <span class="text-[10px] text-ink-500">Abia, Anambra, Ebonyi, Enugu, Imo</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?zone=South+South#coverage" @click="megaOpen = false" class="block p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="font-bold text-ink-900 block group-hover:text-brand-blue transition-colors">South South</span>
                                                <span class="text-[10px] text-ink-500">Akwa Ibom, Delta, Edo, Rivers...</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?zone=South+West#coverage" @click="megaOpen = false" class="block p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="font-bold text-ink-900 block group-hover:text-brand-blue transition-colors">South West</span>
                                                <span class="text-[10px] text-ink-500">Ekiti, Lagos, Ogun, Osun, Oyo...</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Column 2: Policy Lifecycle Stages -->
                                <div class="space-y-3">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-ink-900 flex items-center gap-1.5 pb-2 border-b border-ink-100">
                                        <i class="fas fa-stairs text-brand-emerald"></i> Lifecycle Stages
                                    </span>
                                    <ul class="space-y-1">
                                        <li>
                                            <a href="/?stage=6#coverage" @click="megaOpen = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="w-2.5 h-2.5 rounded-full bg-brand-orange shrink-0"></span>
                                                <div>
                                                    <span class="font-bold text-ink-900 block group-hover:text-brand-orange transition-colors">Stage 6: Signed</span>
                                                    <span class="text-[10px] text-ink-500">Legislative assent</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?stage=7#coverage" @click="megaOpen = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="w-2.5 h-2.5 rounded-full bg-brand-emerald shrink-0"></span>
                                                <div>
                                                    <span class="font-bold text-ink-900 block group-hover:text-brand-emerald transition-colors">Stage 7: Implementing</span>
                                                    <span class="text-[10px] text-ink-500">Active programs</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?stage=5#coverage" @click="megaOpen = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="w-2.5 h-2.5 rounded-full bg-brand-violet shrink-0"></span>
                                                <div>
                                                    <span class="font-bold text-ink-900 block group-hover:text-brand-violet transition-colors">Stage 5: Validated</span>
                                                    <span class="text-[10px] text-ink-500">Stakeholder approval</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?stage=4#coverage" @click="megaOpen = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="w-2.5 h-2.5 rounded-full bg-brand-teal shrink-0"></span>
                                                <div>
                                                    <span class="font-bold text-ink-900 block group-hover:text-brand-teal transition-colors">Stage 4: Validating</span>
                                                    <span class="text-[10px] text-ink-500">Review sessions</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?stage=3#coverage" @click="megaOpen = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="w-2.5 h-2.5 rounded-full bg-brand-blue shrink-0"></span>
                                                <div>
                                                    <span class="font-bold text-ink-900 block group-hover:text-brand-blue transition-colors">Stage 3: Developing</span>
                                                    <span class="text-[10px] text-ink-500">Technical drafting</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?stage=2#coverage" @click="megaOpen = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="w-2.5 h-2.5 rounded-full bg-brand-sunflower shrink-0"></span>
                                                <div>
                                                    <span class="font-bold text-ink-900 block group-hover:text-brand-sunflower transition-colors">Stage 2: Consultation</span>
                                                    <span class="text-[10px] text-ink-500">Youth dialogues</span>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/?stage=0#coverage" @click="megaOpen = false" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-50 transition group">
                                                <span class="w-2.5 h-2.5 rounded-full bg-brand-slate shrink-0"></span>
                                                <div>
                                                    <span class="font-bold text-ink-900 block group-hover:text-brand-slate transition-colors">Stage 0: No Data</span>
                                                    <span class="text-[10px] text-ink-500">Awaiting evidence</span>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Column 3: Data & Evidence Hubs -->
                                <div class="space-y-3">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-ink-900 flex items-center gap-1.5 pb-2 border-b border-ink-100">
                                        <i class="fas fa-database text-brand-orange"></i> Data &amp; Research Hub
                                    </span>
                                    <ul class="space-y-2 font-medium">
                                        <li>
                                            <a href="/dashboard" @click="megaOpen = false" class="block p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 hover:bg-emerald-50/50 hover:border-brand-emerald hover:shadow-sm transition group">
                                                <span class="font-bold text-ink-900 group-hover:text-brand-emerald flex items-center gap-1.5 transition-colors">
                                                    <i class="fas fa-chart-pie text-brand-orange text-xs"></i> Analytics Dashboard
                                                </span>
                                                <span class="text-[10px] text-ink-500 block mt-0.5">Macro distributions, zones, and stakeholder charts.</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/documents" @click="megaOpen = false" class="block p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 hover:bg-emerald-50/50 hover:border-brand-emerald hover:shadow-sm transition group">
                                                <span class="font-bold text-ink-900 group-hover:text-brand-emerald flex items-center gap-1.5 transition-colors">
                                                    <i class="fas fa-box-archive text-brand-teal text-xs"></i> Document Vault (CTCs)
                                                </span>
                                                <span class="text-[10px] text-ink-500 block mt-0.5">Certified True Copies of state gazettes and policy drafts.</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/api/docs" @click="megaOpen = false" class="block p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 hover:bg-emerald-50/50 hover:border-brand-emerald hover:shadow-sm transition group">
                                                <span class="font-bold text-ink-900 group-hover:text-brand-emerald flex items-center gap-1.5 transition-colors">
                                                    <i class="fas fa-code text-brand-violet text-xs"></i> Public REST API (v1)
                                                </span>
                                                <span class="text-[10px] text-ink-500 block mt-0.5">Interactive query console, endpoints &amp; cURL snippets.</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="/api/v1/export/csv" class="block p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 hover:bg-emerald-50/50 hover:border-brand-emerald hover:shadow-sm transition group">
                                                <span class="font-bold text-ink-900 group-hover:text-brand-emerald flex items-center gap-1.5 transition-colors">
                                                    <i class="fas fa-file-csv text-brand-emerald text-xs"></i> Download Dataset (CSV)
                                                </span>
                                                <span class="text-[10px] text-ink-500 block mt-0.5">Full streaming tabular export for Excel and Stata.</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Column 4: Institutional Trust & Governance -->
                                <div class="space-y-3">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-ink-900 flex items-center gap-1.5 pb-2 border-b border-ink-100">
                                        <i class="fas fa-shield-halved text-brand-teal"></i> Institutional Trust
                                    </span>
                                    <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/90 space-y-2.5">
                                        <span class="text-[10px] font-bold uppercase text-brand-emerald block tracking-wider">Civic Data Trust</span>
                                        <p class="text-[11px] text-ink-700 leading-relaxed">
                                            Operated by <a href="https://glowingmindsinitiatives.org/" target="_blank" rel="noopener noreferrer" class="text-ink-950 underline font-semibold hover:text-brand-emerald">Glowing Minds Initiatives</a> to provide independent, verifiable monitoring of youth policy commitments.
                                        </p>
                                        <div class="pt-2 border-t border-emerald-200/70 space-y-1.5 font-medium">
                                            <a href="/privacy-policy" @click="megaOpen = false" class="block text-ink-700 hover:text-brand-emerald transition">
                                                &bull; Privacy &amp; Data Governance
                                            </a>
                                            <a href="/api/docs" @click="megaOpen = false" class="block text-ink-700 hover:text-brand-emerald transition">
                                                &bull; Citation &amp; License (PDDL)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Actions / Language Switcher & Admin Button -->
                <div class="hidden md:flex items-center gap-3">
                    @if(count(config('tracker.supported_locales', [])) > 1)
                    <!-- Language Switcher Dropdown -->
                    <div class="relative" x-data="{ langOpen: false }">
                        <button
                            type="button"
                            @click="langOpen = !langOpen"
                            @click.outside="langOpen = false"
                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded bg-ink-800 hover:bg-ink-700 text-ink-200 border border-ink-700 transition focus:outline-none focus:ring-2 focus:ring-brand-blue"
                            aria-haspopup="true"
                            :aria-expanded="langOpen.toString()"
                        >
                            <i class="fas fa-language mr-1.5 text-brand-blue"></i>
                            <span class="uppercase font-bold">{{ app()->getLocale() }}</span>
                            <i class="fas fa-chevron-down ml-1 text-2xs opacity-70"></i>
                        </button>

                        <div
                            x-show="langOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-36 rounded-md shadow-lg bg-ink-900 border border-ink-700 py-1 ring-1 ring-black ring-opacity-5 z-50 text-xs"
                        >
                            @foreach(config('tracker.supported_locales', []) as $code => $loc)
                                <a
                                    href="{{ route('locale.switch', $code) }}"
                                    class="flex items-center justify-between px-3 py-2 text-ink-300 hover:bg-ink-800 hover:text-white transition {{ app()->getLocale() === $code ? 'bg-ink-800 text-brand-blue font-bold' : '' }}"
                                >
                                    <span>{{ $loc['native'] }}</span>
                                    <span class="text-2xs uppercase text-ink-500 font-mono">{{ $code }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded text-blue-100 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white" aria-expanded="false" aria-label="Toggle navigation menu">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown (Royal Blue Theme) -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-navy-royal border-b border-white/10 px-4 pt-2 pb-4 space-y-2">
            <a href="/" class="block px-3 py-2 rounded text-base font-semibold transition {{ request()->is('/') ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-2xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-map-location-dot mr-2 text-blue-200"></i> Policy Map
            </a>
            <a href="/dashboard" class="block px-3 py-2 rounded text-base font-semibold transition {{ request()->is('dashboard*') ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-2xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-chart-pie mr-2 text-orange-300"></i> Analytics
            </a>
            <a href="/documents" class="block px-3 py-2 rounded text-base font-semibold transition {{ request()->is('documents*') ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-2xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-box-archive mr-2 text-teal-300"></i> Document Vault
            </a>
            <a href="/api/docs" class="block px-3 py-2 rounded text-base font-semibold transition {{ request()->is('api*') ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-2xs' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                <i class="fas fa-code mr-2 text-violet-300"></i> Open API
            </a>

            <!-- Mobile Geopolitical Zones Accordion -->
            <div class="border-t border-white/10 pt-2">
                <button
                    type="button"
                    @click="mobileZonesOpen = !mobileZonesOpen"
                    class="w-full flex items-center justify-between px-3 py-2 rounded text-sm font-semibold text-blue-100 hover:bg-white/10 hover:text-white"
                >
                    <span class="flex items-center gap-2">
                        <i class="fas fa-map-location-dot text-blue-200"></i> Browse by Zone (6)
                    </span>
                    <i class="fas text-xs" :class="mobileZonesOpen ? 'fa-chevron-up text-emerald-300' : 'fa-chevron-down text-blue-200'"></i>
                </button>
                <div x-show="mobileZonesOpen" x-cloak class="pl-6 pr-2 py-1 space-y-1 text-xs">
                    <a href="/?zone=North+Central#coverage" class="block py-1 text-blue-100/90 hover:text-white">North Central</a>
                    <a href="/?zone=North+East#coverage" class="block py-1 text-blue-100/90 hover:text-white">North East</a>
                    <a href="/?zone=North+West#coverage" class="block py-1 text-blue-100/90 hover:text-white">North West</a>
                    <a href="/?zone=South+East#coverage" class="block py-1 text-blue-100/90 hover:text-white">South East</a>
                    <a href="/?zone=South+South#coverage" class="block py-1 text-blue-100/90 hover:text-white">South South</a>
                    <a href="/?zone=South+West#coverage" class="block py-1 text-blue-100/90 hover:text-white">South West</a>
                </div>
            </div>

            <!-- Mobile Stages Accordion -->
            <div class="border-t border-white/10 pt-1">
                <button
                    type="button"
                    @click="mobileStagesOpen = !mobileStagesOpen"
                    class="w-full flex items-center justify-between px-3 py-2 rounded text-sm font-semibold text-blue-100 hover:bg-white/10 hover:text-white"
                >
                    <span class="flex items-center gap-2">
                        <i class="fas fa-stairs text-emerald-300"></i> Lifecycle Stages (8)
                    </span>
                    <i class="fas text-xs" :class="mobileStagesOpen ? 'fa-chevron-up text-emerald-300' : 'fa-chevron-down text-blue-200'"></i>
                </button>
                <div x-show="mobileStagesOpen" x-cloak class="pl-6 pr-2 py-1 space-y-1 text-xs">
                    <a href="/?stage=6#coverage" class="flex items-center gap-2 py-1 text-blue-100/90 hover:text-white"><span class="w-2 h-2 rounded-full bg-brand-orange"></span> Stage 6: Signed</a>
                    <a href="/?stage=7#coverage" class="flex items-center gap-2 py-1 text-blue-100/90 hover:text-white"><span class="w-2 h-2 rounded-full bg-brand-emerald"></span> Stage 7: Implementing</a>
                    <a href="/?stage=5#coverage" class="flex items-center gap-2 py-1 text-blue-100/90 hover:text-white"><span class="w-2 h-2 rounded-full bg-brand-violet"></span> Stage 5: Validated</a>
                    <a href="/?stage=4#coverage" class="flex items-center gap-2 py-1 text-blue-100/90 hover:text-white"><span class="w-2 h-2 rounded-full bg-brand-teal"></span> Stage 4: Validating</a>
                    <a href="/?stage=3#coverage" class="flex items-center gap-2 py-1 text-blue-100/90 hover:text-white"><span class="w-2 h-2 rounded-full bg-brand-blue"></span> Stage 3: Developing</a>
                    <a href="/?stage=2#coverage" class="flex items-center gap-2 py-1 text-blue-100/90 hover:text-white"><span class="w-2 h-2 rounded-full bg-brand-sunflower"></span> Stage 2: Consultation</a>
                    <a href="/?stage=0#coverage" class="flex items-center gap-2 py-1 text-blue-100/90 hover:text-white"><span class="w-2 h-2 rounded-full bg-brand-slate"></span> Stage 0: No Data</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main id="main-content" class="flex-1 focus:outline-none">
        {{ $slot }}
    </main>

    <!-- Institutional Footer -->
    <footer class="bg-ink-950 text-ink-400 text-sm border-t border-ink-800 pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Column 1: Initiative Overview -->
                <div class="md:col-span-1 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-white p-1.5 flex items-center justify-center shadow-md ring-1 ring-black/10 shrink-0">
                            <img src="{{ asset('img/Policy_Lab_logo.png') }}" alt="Policy Lab Logo" class="w-full h-full object-contain" />
                        </div>
                        <span class="text-base font-bold text-white tracking-tight font-heading block">YOUTH POLICY TRACKER</span>
                    </div>
                    <p class="text-xs text-ink-400 leading-relaxed">
                        A rigorous, open-access intelligence system monitoring the domestication, gazetting, and implementation of youth policies across sub-national governments.
                    </p>
                    <div class="pt-2 text-2xs text-ink-500">
                        @php
                            $verifiedDate = \App\Models\PolicyStatus::whereNotNull('data_verified_at')->max('data_verified_at');
                        @endphp
                        <span>{{ __('tracker.footer_last_updated') }} <strong>{{ $verifiedDate ? \Carbon\Carbon::parse($verifiedDate)->format('d F Y') : __('tracker.not_yet_verified') }}</strong></span>
                    </div>
                </div>

                <!-- Column 2: Navigation -->
                <div>
                    <h2 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Platform Navigation</h2>
                    <ul class="space-y-2 text-xs">
                        <li><a href="/" class="hover:text-white transition">Interactive National Map</a></li>
                        <li><a href="/dashboard" class="hover:text-white transition">Analytics & KPI Dashboard</a></li>
                        <li><a href="/documents" class="hover:text-white transition">Certified True Copies (CTC) Vault</a></li>
                        <li><a href="/api/docs" class="hover:text-white transition">Public REST API (v1 Docs &amp; Explorer)</a></li>
                        <li><a href="/api/v1/states?format=json" target="_blank" rel="noopener noreferrer" class="hover:text-white transition text-ink-400">Raw JSON Feed (v1)</a></li>
                        <li><a href="/api/v1/export/csv" class="hover:text-white transition">Download Dataset (CSV)</a></li>
                    </ul>
                </div>

                <!-- Column 3: Governance & Partners -->
                <div>
                    <h2 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Governance & Partners</h2>
                    <ul class="space-y-2.5 text-xs">
                        <li class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white p-1 flex items-center justify-center shadow-sm ring-1 ring-black/10 shrink-0">
                                <img src="{{ asset('img/GMI_logo.png') }}" alt="Glowing Minds Initiatives Logo" class="w-full h-full object-contain" />
                            </div>
                            <div>
                                <span class="text-ink-300 block text-2xs uppercase tracking-wider">Convening Body:</span>
                                <a href="https://glowingmindsinitiatives.org/" target="_blank" rel="noopener noreferrer" class="hover:text-white underline underline-offset-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald rounded-xs font-medium">Glowing Minds Initiatives (GMI)</a>
                            </div>
                        </li>
                        <li><span class="text-ink-300">Scope:</span> 36 States + Federal Capital Territory</li>
                        <li><a href="/privacy-policy" class="hover:text-white transition">Privacy & Data Governance Policy</a></li>
                    </ul>
                </div>

                <!-- Column 4: Verification Standard -->
                <div>
                    <h2 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Data Integrity Standards</h2>
                    <p class="text-xs text-ink-400 leading-relaxed">
                        All policy milestones reflect formal evidence from official state gazettes, verified consultative drafts, or authorized stakeholder communiques. Missing data is explicitly marked as "Not yet documented".
                    </p>
                </div>
            </div>

            <div class="pt-8 border-t border-ink-800 text-xs text-ink-400 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p>&copy; 2026 Glowing Minds Initiatives (GMI). All rights reserved.</p>
                <p class="text-xs text-ink-400" aria-label="Powered by Corestream">
                    Powered by <a href="https://www.corestream.ng" target="_blank" rel="noopener noreferrer" class="text-ink-200 hover:text-white underline underline-offset-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-emerald rounded-xs">Corestream</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent Banner (GA4 Gate) -->
    <x-cookie-consent />

    @livewireScripts
</body>
</html>
