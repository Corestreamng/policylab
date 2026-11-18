@props([
    'stage',
    'isStalled' => false,
    'isExpired' => false,
    'size' => 'md', // sm, md, lg
])

@php
    $order = $stage->sequence_order ?? 0;
    $styles = [
        0 => 'bg-slate-100 text-[#334155] border-slate-300',
        1 => 'bg-rose-50 text-[#BE123C] border-rose-200',
        2 => 'bg-amber-50 text-[#92400E] border-amber-200',
        3 => 'bg-purple-50 text-[#6D28D9] border-purple-200',
        4 => 'bg-blue-50 text-[#1D4ED8] border-blue-200',
        5 => 'bg-teal-50 text-[#0F766E] border-teal-200',
        6 => 'bg-orange-50 text-[#C2410C] border-orange-200',
        7 => 'bg-emerald-50 text-[#047857] border-emerald-200',
    ];

    $badgeStyle = $styles[$order] ?? $styles[0];
    $sizeClasses = [
        'sm' => 'text-2xs px-2 py-0.5',
        'md' => 'text-xs px-2.5 py-1',
        'lg' => 'text-sm px-3.5 py-1.5',
    ][$size] ?? 'text-xs px-2.5 py-1';
@endphp

<div class="inline-flex flex-wrap items-center gap-1.5">
    <span class="inline-flex items-center font-semibold rounded-full border {{ $badgeStyle }} {{ $sizeClasses }}">
        <span class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: {{ $stage->color_hex ?? '#64748B' }};"></span>
        <i class="fas {{ $stage->icon_class ?? 'fa-circle-question' }} mr-1 text-2xs opacity-80"></i>
        <span>{{ $stage->name ?? 'No Verifiable Data' }}</span>
    </span>

    @if($isStalled)
        <span class="inline-flex items-center text-2xs font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300">
            <i class="fas fa-pause mr-1"></i> Stalled
        </span>
    @endif

    @if($isExpired)
        <span class="inline-flex items-center text-2xs font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300">
            <i class="fas fa-clock-rotate-left mr-1"></i> Expired
        </span>
    @endif
</div>
