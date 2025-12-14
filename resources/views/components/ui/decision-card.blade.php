@props([
    'title',
    'description' => null,
    'action' => null,
    'actionLabel' => 'Lihat',
    'color' => 'primary', // primary, success, warning, danger, info
    'badge' => null,
    'icon' => null,
])

@php
    $borderColors = match ($color) {
        'success' => 'border-l-emerald-500',
        'warning' => 'border-l-amber-500',
        'danger' => 'border-l-red-500',
        'info' => 'border-l-blue-500',
        default => 'border-l-siakad-primary',
    };

    $bgColors = match ($color) {
        'success' => 'bg-emerald-50/50 dark:bg-emerald-900/10',
        'warning' => 'bg-amber-50/50 dark:bg-amber-900/10',
        'danger' => 'bg-red-50/50 dark:bg-red-900/10',
        'info' => 'bg-blue-50/50 dark:bg-blue-900/10',
        default => 'bg-siakad-primary/5',
    };

    $badgeColors = match ($color) {
        'success' => 'bg-emerald-100 text-emerald-700',
        'warning' => 'bg-amber-100 text-amber-700',
        'danger' => 'bg-red-100 text-red-700',
        'info' => 'bg-blue-100 text-blue-700',
        default => 'bg-siakad-primary/10 text-siakad-primary',
    };  
@endphp
    
    @if($action)
        <a href="{{ $action }}" 
           {{ $attributes->merge(['class' => "card-saas p-5 border-l-4 hover:shadow-md transition-shadow {$borderColors} {$bgColors}"]) }}>
    @else
        <div {{ $attributes->merge(['class' => "card-saas p-5 border-l-4 {$borderColors} {$bgColors}"]) }}>
       @endif
    
       <div class="  flex items-start justify-between">
          <div cla  ss="flex items-start gap-3">
             @if($icon)
                   <div  class="flex-shrink-0">
                        {{ $icon }}
                    </div>

               @endif
           <div>
                    <h3 class="font-semibold text-siakad-dark dark:text-white">{{ $title }}</h3>
                    @if($description)
                        <p class="text-sm text-siakad-secondary mt-1">{{ $description }}</p>
                    @endif
                </div>
        </div>
            
            @if($badge)
                    <span class="flex-shrink-0 px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $badgeColors }}">
                        {{ $badge }}
                   </sp an>
             @endif
        </div>
    
    @if($action)
            <div class="mt-3 flex items-center text-sm font-medium text-siakad-primary">
            {{ $actionLabel }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    @endif

    {{ $slot }}

@if($action)
    </a>
@else
    </div>
@endif
