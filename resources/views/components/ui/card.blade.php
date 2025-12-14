@props([
    'type' => 'default', // default, primary, success, warning, danger, info
    'padding' => 'p-6',
    'hover' => false,
    'href' => null,
])
@php
    $baseClasses = 'card-saas rounded-xl transition-all duration-200';

    $typeClasses = match ($type) {
        'primary' => 'border-siakad-primary/20 bg-siakad-primary/5',
        'success' => 'border-emerald-200 bg-emerald-50/50 dark:bg-emerald-900/10',
        'warning' => 'border-amber-200 bg-amber-50/50 dark:bg-amber-900/10',
        'danger' => 'border-red-200 bg-red-50/50 dark:bg-red-900/10',
        'info' => 'border-blue-200 bg-blue-50/50 dark:bg-blue-900/10',
        default => '',
    };

    $hoverClasses = $hover ? 'hover:shadow-md hover:border-siakad-primary/30 cursor-pointer' : '';

    $classes = "{$baseClasses} {$typeClasses} {$hoverClasses} {$padding}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </div>
@endif
