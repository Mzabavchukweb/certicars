@props([
    // Car slug — used to build the PDF download URL.
    'slug',
    // Size variant. 'md' (default) matches the approved single-car sidebar pill.
    // 'sm' is a slightly compact variant for catalog card overlays. Only the
    // outer dimensions change; colours, icons, separator and layout are
    // identical so the visual language stays uniform.
    'size' => 'md',
])
@php
    $isSmall = $size === 'sm';
@endphp
{{-- Single unified CertiCheck CTA: shield-check + label + thin separator +
     download icon, all inside ONE clickable pill. Clicking downloads the
     PDF report (route('car.pdf', $slug)). Renders only when the caller has
     already gated on $car->has_certicheck so we never produce a fake
     clickable affordance. --}}
<a
    href="{{ route('car.pdf', $slug) }}"
    {{ $attributes->merge([
        'class' => 'cs-certi-cta' . ($isSmall ? ' cs-certi-cta--sm' : ''),
        'download' => true,
        'title' => 'Pobierz raport CertiCheck (PDF)',
        'aria-label' => 'Pobierz raport CertiCheck (PDF)',
    ]) }}
    onclick="event.stopPropagation()"
>
    <x-icon name="badge-check" :size="$isSmall ? 12 : 14" :strokeWidth="2.4" class="cs-certi-cta-leading"/>
    <span>CertiCheck</span>
    <span class="cs-certi-cta-sep" aria-hidden="true"></span>
    <x-icon name="download" :size="$isSmall ? 13 : 15" :strokeWidth="2.4" class="cs-certi-cta-trailing"/>
</a>
{{-- Inline @once style block: emitted to the page exactly once no matter how
     many times the component is rendered. Keeps the visual contract owned by
     the component itself, so there's no hidden coupling to a global CSS file
     that could drift. --}}
@once
@verbatim
<style>
.cs-certi-cta{display:inline-flex;align-items:center;gap:8px;background:#0a0a0a;color:#fff;font-size:12px;font-weight:800;letter-spacing:.2px;padding:8px 14px;border-radius:50px;text-decoration:none;transition:transform .15s,box-shadow .15s,background .15s;line-height:1;flex-shrink:0;min-height:36px;box-sizing:border-box;white-space:nowrap}
.cs-certi-cta:hover{background:#1a1a1a;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.22)}
.cs-certi-cta .cs-certi-cta-leading{stroke:#4ea3ff!important;color:#4ea3ff}
.cs-certi-cta .cs-certi-cta-trailing{stroke:#fff!important;color:#fff}
.cs-certi-cta .cs-certi-cta-sep{display:inline-block;width:1px;height:14px;background:rgba(255,255,255,.22);margin:0 -2px;flex-shrink:0}
.cs-certi-cta--sm{font-size:10.5px;padding:6px 10px;gap:6px;min-height:28px}
.cs-certi-cta--sm .cs-certi-cta-sep{height:12px}
@media(max-width:520px){
    .cs-certi-cta{font-size:12.5px;padding:9px 16px;min-height:40px;align-self:flex-start}
    .cs-certi-cta--sm{font-size:11px;padding:6px 11px;min-height:30px;align-self:auto}
}
</style>
@endverbatim
@endonce
