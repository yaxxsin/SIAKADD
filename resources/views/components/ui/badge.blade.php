@props([
    'type' => 'default', // default, success, warning, danger, info, primary
    'size' => 'sm', // xs, sm, md
    'dot' => false, // Show dot indicator instead of text
])
@php
    $sizeClasses = match ($size) {
        'xs' => 'px-1.5 py-0.5 text-[9px]',
        'sm' => 'px-2 py-0.5 text-[10px]',
        'md' => 'px-2.5 py-1 text-xs',
        default => 'px-2 py-0.5 text-[10px]',
    };

    $typeClasses = match ($type) {
        'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'danger' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        'info' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'primary' => 'bg-siakad-primary/10 text-siakad-primary dark:bg-siakad-primary/20',
        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    };

    $classes = "inline-flex items-center font-semibold rounded-full {$sizeClasses} {$typeClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full mr-1.5 
            @if($type === 'success') bg-emerald-500
            @elseif($type === 'warning') bg-amber-500
            @elseif($type === 'danger') bg-red-500
            @elseif($type === 'info') bg-blue-500
            @else bg-gray-500
            @endif
        "></span>
    @endif
    {{ $slot }}
</span>
