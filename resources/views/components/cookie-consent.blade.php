<div
    x-data="{
        consentGiven: localStorage.getItem('ypt_cookie_consent') !== null,
        accept() {
            localStorage.setItem('ypt_cookie_consent', 'granted');
            this.consentGiven = true;
            window.dispatchEvent(new CustomEvent('ga4-consent-granted'));
        },
        decline() {
            localStorage.setItem('ypt_cookie_consent', 'declined');
            this.consentGiven = true;
        }
    }"
    x-show="!consentGiven"
    x-cloak
    class="fixed bottom-0 inset-x-0 z-50 p-4 bg-ink-950/95 backdrop-blur-sm border-t border-ink-800 text-white shadow-2xl"
    role="region"
    aria-label="Cookie consent banner"
>
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded bg-brand-blue/20 text-brand-blue flex items-center justify-center shrink-0 mt-0.5">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div class="text-xs text-ink-300 leading-relaxed">
                <strong class="text-white block font-medium">Privacy and Analytics Consent</strong>
                We use privacy-friendly analytics (Google Analytics 4 with IP anonymisation) solely to measure aggregated usage and understand how stakeholders interact with youth policy data. We never load analytics cookies until you consent.
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button
                type="button"
                @click="decline()"
                class="px-3.5 py-2 text-xs font-medium text-ink-300 hover:text-white hover:bg-ink-800 rounded transition border border-ink-700 touch-target"
            >
                Strictly Necessary Only
            </button>
            <button
                type="button"
                @click="accept()"
                class="px-4 py-2 text-xs font-semibold text-white bg-brand-blue hover:bg-blue-600 rounded shadow transition touch-target"
            >
                Accept Analytics
            </button>
        </div>
    </div>
</div>
