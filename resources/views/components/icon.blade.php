@props([
    'name',
    'size' => 'md',
    'strokeWidth' => null,
    'tone' => null,
])
@php
    $sizeMap = ['xs' => 14, 'sm' => 16, 'md' => 20, 'lg' => 24, 'xl' => 32];
    $px = $sizeMap[$size] ?? (is_numeric($size) ? (int) $size : 20);
    $toneMap = [
        'blue'   => '#0066ff',
        'green'  => '#16a34a',
        'orange' => '#f59e0b',
        'red'    => '#dc2626',
        'gray'   => '#6b7280',
        'muted'  => '#9ca3af',
    ];
    $color = $tone ? ($toneMap[$tone] ?? null) : null;
    $style = "width:{$px}px;height:{$px}px";
    if ($color)                    $style .= ";color:{$color}";
    if ($strokeWidth !== null)     $style .= ";stroke-width:{$strokeWidth}";
@endphp
<i data-lucide="{{ $name }}"
   aria-hidden="true"
   {{ $attributes->merge(['class' => 'cs-icon', 'style' => $style]) }}></i>
