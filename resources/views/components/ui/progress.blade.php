@props([
    'value' => 0,
    'max' => 100,
    'label' => null,
    'sublabel' => null,
    'showPercentage' => true,
    'size' => 'md', // sm, md, lg
    'color' => 'primary', // primary, success, warning, danger
])

@php
$percentage = $max > 0 ? min(round(($value / $max) * 100, 1), 100) : 0;

$heightClasses = match($size) {
    'sm' => 'h-1.5',
    'md' => 'h-2.5',
    'lg' => 'h-4',
    default => 'h-2.5',
};

$colorClasses = match($color) {
    'success' => 'bg-emerald-500',
    'warning' => 'bg-amber-500',
    'danger' => 'bg-red-500',
    default => 'bg-siakad-primary',
};

// Auto color based on percentage
if ($color === 'auto') {
    $colorClasses = match(true) {
        $percentage >= 75 => 'bg-emerald-500',
        $percentage >= 50 => 'bg-siakad-primary',
        $percentage >= 25 => 'bg-amber-500',
        default => 'bg-red-500',
    };
}
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if($label || $showPercentage)
    <div class="flex items-center justify-between mb-1.5">
        @if($label)
        <span class="text-sm text-siakad-secondary">{{ $label }}</span>
        @endif
        
        <div class="flex items-center gap-2">
            @if($sublabel)
            <span class="text-sm font-medium text-siakad-dark dark:text-white">{{ $sublabel }}</span>
            @endif
            @if($showPercentage)
            <span class="text-xs text-siakad-secondary">{{ $percentage }}%</span>
            @endif
        </div>
    </div>
    @endif
    
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full {{ $heightClasses }}">
        <div class="{{ $colorClasses }} {{ $heightClasses }} rounded-full transition-all duration-500 ease-out" 
             style="width: {{ $percentage }}%"
             role="progressbar"
             aria-valuenow="{{ $value }}"
             aria-valuemin="0"
             aria-valuemax="{{ $max }}">
        </div>
    </div>
</div>
