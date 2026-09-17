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

    // "tabler:<nazwa>" — ikony, ktorych Lucide nie ma (np. skrzynia biegow).
    // Oryginalne pliki z Tabler Icons (MIT) w resources/icons/tabler; ta sama
    // konwencja co Lucide (24px, obrys 2, zaokraglone konce), wiec sa spojne.
    $tablerSvg = null;
    if (str_starts_with($name, 'tabler:')) {
        $file = resource_path('icons/tabler/' . basename(substr($name, 7)) . '.svg');
        if (is_file($file)) {
            $inner = preg_replace('~^.*?<svg[^>]*>|</svg>\s*$~s', '', file_get_contents($file));
            $tablerSvg = $inner;
        }
    }
@endphp
@if($tablerSvg !== null)
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="{{ $strokeWidth ?? 2 }}" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true"
     {{ $attributes->merge(['class' => 'cs-icon lucide', 'style' => $style]) }}>{!! $tablerSvg !!}</svg>
@else
<i data-lucide="{{ $name }}"
   aria-hidden="true"
   {{ $attributes->merge(['class' => 'cs-icon', 'style' => $style]) }}></i>
@endif
