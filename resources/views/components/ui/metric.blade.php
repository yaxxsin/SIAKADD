@props([
    'value',
    'label' => null,
    'sublabel' => null,
    'trend' => null, // 'up', 'down', 'stable'
    'trendValue' => null,
    'size' => 'md', // sm, md, lg
    'icon' => null,
])

@php
$valueClasses = match($size) {
    'sm' => 'text-xl font-bold',
    'md' => 'text-2xl font-bold',
    'lg' => 'text-3xl font-bold',
    default => 'text-2xl font-bold',
};

$trendClasses = match($trend) {
    'up' => 'text-emerald-600',
    'down' => 'text-red-600',
    'stable' => 'text-gray-500',
    default => '',
};

$trendIcon = match($trend) {
    'up' => '↑',
    'down' => '↓',
    'stable' => '→',
    default => '',
};
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3']) }}>
    @if($icon)
    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-siakad-primary/10 flex items-center justify-center">
        {{ $icon }}
    </div>
    @endif
    
    <div class="flex-1">
        @if($label)
        <p class="text-xs text-siakad-secondary uppercase tracking-wide mb-1">{{ $label }}</p>
        @endif
        
        <div class="flex items-baseline gap-2">
            <span class="{{ $valueClasses }} text-siakad-dark dark:text-white">{{ $value }}</span>
            
            @if($trend && $trendValue)
            <span class="text-sm font-medium {{ $trendClasses }}">
                {{ $trendIcon }} {{ $trendValue }}
            </span>
            @endif
        </div>
        
        @if($sublabel)
        <p class="text-xs text-siakad-secondary mt-0.5">{{ $sublabel }}</p>
        @endif
    </div>
</div>
