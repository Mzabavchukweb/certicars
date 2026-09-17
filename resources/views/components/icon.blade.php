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

    // Ikony spoza Lucide — oryginalne pliki SVG z bibliotek (MIT) w resources/icons:
    //   tabler:<n>       Tabler outline  (obrys, siatka 24)
    //   tabler-fill:<n>  Tabler filled   (wypelnione, siatka 24)
    //   ph:<n>           Phosphor regular (ksztalty wypelnione, siatka 256)
    //   ph-fill:<n>      Phosphor fill
    // Wszystko bez prefiksu idzie do Lucide (<i data-lucide>, podmieniane w JS).
    $libs = [
        'tabler:'      => ['tabler',           '0 0 24 24',   false],
        'tabler-fill:' => ['tabler/filled',    '0 0 24 24',   true],
        'ph:'          => ['phosphor/regular', '0 0 256 256', true],
        'ph-fill:'     => ['phosphor/fill',    '0 0 256 256', true],
    ];
    $svgInner = null; $viewBox = null; $solid = false;
    foreach ($libs as $prefix => [$dir, $vb, $isSolid]) {
        if (str_starts_with($name, $prefix)) {
            $file = resource_path("icons/{$dir}/" . basename(substr($name, strlen($prefix))) . '.svg');
            if (is_file($file)) {
                $svgInner = preg_replace('~^.*?<svg[^>]*>|</svg>\s*$~s', '', file_get_contents($file));
                $viewBox = $vb; $solid = $isSolid;
            }
            break;
        }
    }
@endphp
@if($svgInner !== null && $solid)
<svg xmlns="http://www.w3.org/2000/svg" viewBox="{{ $viewBox }}" fill="currentColor" stroke="none"
     aria-hidden="true"
     {{ $attributes->merge(['class' => 'cs-icon cs-icon-solid lucide', 'style' => $style]) }}>{!! $svgInner !!}</svg>
@elseif($svgInner !== null)
<svg xmlns="http://www.w3.org/2000/svg" viewBox="{{ $viewBox }}" fill="none" stroke="currentColor"
     stroke-width="{{ $strokeWidth ?? 2 }}" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true"
     {{ $attributes->merge(['class' => 'cs-icon lucide', 'style' => $style]) }}>{!! $svgInner !!}</svg>
@else
<i data-lucide="{{ $name }}"
   aria-hidden="true"
   {{ $attributes->merge(['class' => 'cs-icon', 'style' => $style]) }}></i>
@endif
