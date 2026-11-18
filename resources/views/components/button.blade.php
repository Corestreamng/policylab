@props([
    'variant' => 'primary', // primary, secondary, outline, ghost, danger
    'size' => 'md',        // sm, md, lg
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'loadingTarget' => null,
    'disabled' => false,
    'href' => null,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 min-h-[44px] touch-target select-none';

    $variants = [
        'primary' => 'bg-brand-blue hover:bg-blue-700 active:bg-blue-800 text-white border border-transparent focus-visible:ring-brand-blue shadow-xs',
        'secondary' => 'bg-white hover:bg-ink-100 active:bg-ink-200 text-ink-800 border border-ink-300 focus-visible:ring-ink-400 shadow-xs',
        'outline' => 'bg-transparent hover:bg-ink-800 active:bg-ink-900 text-ink-200 border border-ink-700 hover:text-white focus-visible:ring-brand-blue',
        'ghost' => 'bg-transparent hover:bg-ink-100 active:bg-ink-200 text-ink-700 hover:text-ink-900 focus-visible:ring-brand-blue',
        'danger' => 'bg-brand-rose hover:bg-rose-700 active:bg-rose-800 text-white border border-transparent focus-visible:ring-brand-rose shadow-xs',
    ];

    $sizes = [
        'sm' => 'text-xs px-3 py-1.5 gap-1.5',
        'md' => 'text-sm px-4 py-2 gap-2',
        'lg' => 'text-base px-5 py-2.5 gap-2.5',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $disabledClass = $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';

    $classes = "{$baseClasses} {$variantClass} {$sizeClass} {$disabledClass}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled) tabindex="-1" aria-disabled="true" @endif>
        @if($icon && $iconPosition === 'left')
            <i class="fas {{ $icon }}"></i>
        @endif
        
        <span>{{ $slot }}</span>

        @if($icon && $iconPosition === 'right')
            <i class="fas {{ $icon }}"></i>
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) disabled @endif
        @if($loadingTarget) wire:loading.attr="disabled" wire:target="{{ $loadingTarget }}" @endif
    >
        @if($loadingTarget)
            <i class="fas fa-circle-notch fa-spin" wire:loading wire:target="{{ $loadingTarget }}"></i>
        @elseif($loading)
            <i class="fas fa-circle-notch fa-spin"></i>
        @endif

        @if($icon && $iconPosition === 'left')
            <i class="fas {{ $icon }}" @if($loadingTarget) wire:loading.remove wire:target="{{ $loadingTarget }}" @endif></i>
        @endif

        <span>{{ $slot }}</span>

        @if($icon && $iconPosition === 'right')
            <i class="fas {{ $icon }}"></i>
        @endif
    </button>
@endif
