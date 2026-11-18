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
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-brand-emerald"></span>
                <span>{{ __('tracker.footer_initiative') }} <strong class="text-white">{{ config('tracker.client_name') }}</strong></span>
            </div>
            <div class="flex items-center gap-4 text-ink-400">
                <span>{{ __('tracker.footer_built_by') }}</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-ink-900 text-white sticky top-0 z-40 border-b border-ink-800 shadow-sm" x-data="{ mobileMenuOpen: false, langOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-brand-blue rounded p-1">
                        <div class="w-10 h-10 rounded bg-brand-blue flex items-center justify-center text-white shadow font-bold text-lg">
                            <i class="fas fa-landmark-flag"></i>
                        </div>
                        <div>
                            <span class="block text-base font-bold tracking-tight text-white font-heading">{{ __('tracker.app_name') }}</span>
                            <span class="block text-2xs uppercase tracking-wider text-ink-400">{{ __('tracker.tagline') }}</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1" aria-label="Main Navigation">
                    <a href="/" class="px-3 py-2 rounded text-sm font-medium transition {{ request()->is('/') ? 'bg-ink-800 text-white' : 'text-ink-300 hover:text-white hover:bg-ink-800' }}">
                        <i class="fas fa-map-location-dot mr-1.5 text-brand-blue"></i> {{ __('tracker.nav_map') }}
                    </a>
                    <a href="/dashboard" class="px-3 py-2 rounded text-sm font-medium transition {{ request()->is('dashboard*') ? 'bg-ink-800 text-white' : 'text-ink-300 hover:text-white hover:bg-ink-800' }}">
                        <i class="fas fa-chart-pie mr-1.5 text-brand-orange"></i> {{ __('tracker.nav_analytics') }}
                    </a>
                    <a href="/documents" class="px-3 py-2 rounded text-sm font-medium transition {{ request()->is('documents*') ? 'bg-ink-800 text-white' : 'text-ink-300 hover:text-white hover:bg-ink-800' }}">
                        <i class="fas fa-box-archive mr-1.5 text-brand-teal"></i> {{ __('tracker.nav_vault') }}
                    </a>
                    <a href="/api/v1/states" target="_blank" class="px-3 py-2 rounded text-sm font-medium transition text-ink-300 hover:text-white hover:bg-ink-800">
                        <i class="fas fa-code mr-1.5 text-brand-violet"></i> {{ __('tracker.nav_api') }}
                    </a>
                </nav>

                <!-- Actions / Language Switcher & Admin Button -->
                <div class="hidden md:flex items-center gap-3">
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

                    <!-- Admin CMS Portal Button -->
                    <x-button href="/admin" variant="outline" size="sm" icon="fa-lock">
                        {{ __('tracker.nav_admin') }}
                    </x-button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded text-ink-400 hover:text-white hover:bg-ink-800 focus:outline-none focus:ring-2 focus:ring-brand-blue" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-ink-950 border-b border-ink-800 px-4 pt-2 pb-4 space-y-1">
            <a href="/" class="block px-3 py-2 rounded text-base font-medium {{ request()->is('/') ? 'bg-ink-800 text-white' : 'text-ink-300 hover:bg-ink-800' }}">
                <i class="fas fa-map-location-dot mr-2 text-brand-blue"></i> Policy Map
            </a>
            <a href="/dashboard" class="block px-3 py-2 rounded text-base font-medium {{ request()->is('dashboard*') ? 'bg-ink-800 text-white' : 'text-ink-300 hover:bg-ink-800' }}">
                <i class="fas fa-chart-pie mr-2 text-brand-orange"></i> Analytics
            </a>
            <a href="/documents" class="block px-3 py-2 rounded text-base font-medium {{ request()->is('documents*') ? 'bg-ink-800 text-white' : 'text-ink-300 hover:bg-ink-800' }}">
                <i class="fas fa-box-archive mr-2 text-brand-teal"></i> Document Vault
            </a>
            <a href="/api/v1/states" target="_blank" class="block px-3 py-2 rounded text-base font-medium text-ink-300 hover:bg-ink-800">
                <i class="fas fa-code mr-2 text-brand-violet"></i> Open API
            </a>
            <a href="/admin" class="block px-3 py-2 rounded text-base font-medium bg-ink-800 text-ink-200 mt-2">
                <i class="fas fa-lock mr-2"></i> Admin Portal
            </a>
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
                    <span class="text-base font-bold text-white tracking-tight font-heading block">YOUTH POLICY TRACKER</span>
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
                        <li><a href="/api/v1/states" target="_blank" class="hover:text-white transition">Public REST API (v1)</a></li>
                        <li><a href="/api/v1/export/csv" class="hover:text-white transition">Download Dataset (CSV)</a></li>
                    </ul>
                </div>

                <!-- Column 3: Governance & Partners -->
                <div>
                    <h2 class="text-xs font-semibold text-white uppercase tracking-wider mb-3">Governance & Partners</h2>
                    <ul class="space-y-2 text-xs">
                        <li><span class="text-ink-300">Client:</span> Glowing Minds Initiatives (GMI)</li>
                        <li><span class="text-ink-300">Engineering:</span> Corestream Networks and Robotics</li>
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
                    <div class="mt-3">
                        <a href="/admin" class="inline-flex items-center text-xs text-brand-blue hover:text-blue-400 font-medium">
                            <i class="fas fa-arrow-right-to-bracket mr-1.5"></i> Authorized Staff Sign In
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-ink-800 text-xs text-ink-500 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p>&copy; 2026 Glowing Minds Initiatives (GMI) &amp; Corestream Networks and Robotics Limited. All rights reserved.</p>
                <p class="text-2xs text-ink-500">State boundaries: geoBoundaries (www.geoboundaries.org), simplified for display. Zero tracking without explicit consent.</p>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent Banner (GA4 Gate) -->
    <x-cookie-consent />

    @livewireScripts
</body>
</html>
