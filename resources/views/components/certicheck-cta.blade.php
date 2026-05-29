@props([
    // Car slug — used to build the PDF download URL when ready.
    'slug',
    // Whether the cached brochure PDF is actually downloadable RIGHT NOW.
    // When false, the component renders a STATIC badge (no download icon,
    // no <a download>) so the user never gets a fake download attempt
    // that would save a 404 page or a JSON error as `download.json`.
    // Default: false — every caller must opt in by passing
    // :ready="$car->brochureIsReady()".
    'ready' => false,
    // Size variant: 'md' (default sidebar pill) or 'sm' (catalog card).
    'size' => 'md',
])
@php
    $isSmall = $size === 'sm';
@endphp
{{-- Two visual states:
     • ready=true  → black pill, badge-check icon, label, divider, download
       icon. Click downloads the cached PDF immediately.
     • ready=false → static badge with badge-check icon and label only.
       No download icon, no anchor, no clickable affordance. Tooltip
       explains the report is being prepared. --}}
@if ($ready)
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
@else
<span
    {{ $attributes->merge([
        'class' => 'cs-certi-cta cs-certi-cta--pending' . ($isSmall ? ' cs-certi-cta--sm' : ''),
        'title' => 'Raport CertiCheck w przygotowaniu',
        'aria-label' => 'Raport CertiCheck w przygotowaniu',
        'role' => 'status',
    ]) }}
>
    <x-icon name="badge-check" :size="$isSmall ? 12 : 14" :strokeWidth="2.4" class="cs-certi-cta-leading"/>
    <span>CertiCheck</span>
</span>
@endif
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
/* Pending state — same shape as the ready pill so the surrounding layout
   never shifts as the brochure becomes available, but visibly muted and
   non-interactive so the user doesn't expect a download. */
.cs-certi-cta--pending{background:#22262f;color:rgba(255,255,255,.62);cursor:default}
.cs-certi-cta--pending:hover{background:#22262f;transform:none;box-shadow:none}
.cs-certi-cta--pending .cs-certi-cta-leading{stroke:#7eb3ff!important;color:#7eb3ff;opacity:.7}
@media(max-width:520px){
    .cs-certi-cta{font-size:12.5px;padding:9px 16px;min-height:40px;align-self:flex-start}
    .cs-certi-cta--sm{font-size:11px;padding:6px 11px;min-height:30px;align-self:auto}
}
</style>
@endverbatim
@endonce
