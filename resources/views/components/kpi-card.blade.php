@props([
    'title',
    'value',
    'icon',
    'borderColor' => 'border-brand-blue',
    'iconBg' => 'bg-blue-50 text-brand-blue-text',
    'subtitle' => null,
])

<div
    x-data="{
        currentVal: 0,
        targetVal: {{ (int) $value }},
        animate() {
            let start = 0;
            const duration = 900;
            const startTime = performance.now();
            const step = (now) => {
                const progress = Math.min((now - startTime) / duration, 1);
                this.currentVal = Math.floor(progress * this.targetVal);
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    this.currentVal = this.targetVal;
                }
            };
            requestAnimationFrame(step);
        }
    }"
    x-init="animate()"
    class="bg-white rounded-lg p-5 border-t-4 {{ $borderColor }} border border-ink-200 shadow-card hover:shadow-card-hover transition-all"
>
    <div class="flex items-center justify-between">
        <div>
            <span class="block text-xs font-semibold uppercase tracking-wider text-ink-500 mb-1">{{ $title }}</span>
            <div class="text-3xl font-extrabold text-ink-900 font-heading">
                <span x-text="currentVal">{{ $value }}</span>
            </div>
            @if($subtitle)
                <span class="block text-2xs text-ink-500 mt-1">{{ $subtitle }}</span>
            @endif
        </div>
        <div class="w-12 h-12 rounded-lg {{ $iconBg }} flex items-center justify-center text-xl shrink-0">
            <i class="fas {{ $icon }}"></i>
        </div>
    </div>
</div>
