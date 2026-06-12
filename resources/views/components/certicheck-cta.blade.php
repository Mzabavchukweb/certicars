@props([
    // Car slug — used to build the PDF download URL when ready.
    'slug',
    // One of: ready / processing / failed / missing — see
    // App\Models\Car::brochureCustomerStatus(). Default keeps callers that
    // pass nothing safe: they get the non-clickable badge.
    'status' => 'missing',
    // Size variant: 'md' (default sidebar pill) or 'sm' (catalog card).
    'size' => 'md',
])
@php
    $isSmall = $size === 'sm';
@endphp
{{-- Customer-facing CertiCheck pill — 4 explicit visual states. The pill
     always has the same outer shape so layout never shifts as the brochure
     transitions ready → processing → failed → ready. Click behaviour:

     • ready      → <a download href> — opens cached PDF instantly
     • processing → <button> — opens "raport jest w przygotowaniu" modal
     • failed     → <button> — same modal (we don't expose admin errors)
     • missing    → <button> — same modal (rare; happens on first publish
                    before observer fires)

     ZERO state ever shows a 404 page or a dead click. --}}
@if ($status === 'ready')
{{-- Klik w pill OTWIERA raport HTML (nie wymusza downloadu) — customer
     widzi pełną broszurę w przeglądarce, a wewnątrz tej strony jest
     wyraźny przycisk „Pobierz w PDF" który dopiero zapisuje plik
     (route catalog.certicheck → blade z download-att linkiem do car.pdf).
     Bez tego rozdziału klik downloadował plik silnie, customer nie miał
     szansy podejrzeć zawartości — feedback klienta: „po otwarciu broszury
     pobierz w pdf i to się zapisywało jako plik". --}}
<a
    href="{{ route('catalog.certicheck', $slug) }}"
    {{ $attributes->merge([
        'class' => 'cs-certi-cta cs-certi-cta--ready' . ($isSmall ? ' cs-certi-cta--sm' : ''),
        'title' => 'Otwórz raport CertiCheck',
        'aria-label' => 'Otwórz raport CertiCheck',
        'target' => '_blank',
        'rel' => 'noopener',
    ]) }}
    onclick="event.stopPropagation()"
>
    <x-icon name="badge-check" :size="$isSmall ? 12 : 14" :strokeWidth="2.4" class="cs-certi-cta-leading"/>
    <span>CertiCheck</span>
    <span class="cs-certi-cta-sep" aria-hidden="true"></span>
    <x-icon name="external-link" :size="$isSmall ? 13 : 15" :strokeWidth="2.4" class="cs-certi-cta-trailing"/>
</a>
@else
<button
    type="button"
    {{ $attributes->merge([
        'class' => 'cs-certi-cta cs-certi-cta--pending' . ($isSmall ? ' cs-certi-cta--sm' : ''),
        'title' => 'Raport CertiCheck w przygotowaniu',
        'aria-label' => 'Raport CertiCheck w przygotowaniu',
    ]) }}
    onclick="event.stopPropagation();window.csOpenCertiCheckPending && window.csOpenCertiCheckPending();return false"
>
    <x-icon name="badge-check" :size="$isSmall ? 12 : 14" :strokeWidth="2.4" class="cs-certi-cta-leading"/>
    <span>CertiCheck</span>
    <span class="cs-certi-cta-sep" aria-hidden="true"></span>
    <x-icon name="clock" :size="$isSmall ? 13 : 15" :strokeWidth="2.4" class="cs-certi-cta-trailing"/>
</button>
@endif
@once
@verbatim
<style>
.cs-certi-cta{display:inline-flex;align-items:center;gap:8px;background:#0a0a0a;color:#fff;font-size:12px;font-weight:800;letter-spacing:.2px;padding:8px 14px;border-radius:50px;text-decoration:none;transition:transform .15s,box-shadow .15s,background .15s;line-height:1;flex-shrink:0;min-height:36px;box-sizing:border-box;white-space:nowrap;border:none;font-family:inherit;cursor:pointer}
.cs-certi-cta:hover{background:#1a1a1a;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.22)}
.cs-certi-cta .cs-certi-cta-leading{stroke:#4ea3ff!important;color:#4ea3ff}
.cs-certi-cta .cs-certi-cta-trailing{stroke:#fff!important;color:#fff}
.cs-certi-cta .cs-certi-cta-sep{display:inline-block;width:1px;height:14px;background:rgba(255,255,255,.22);margin:0 -2px;flex-shrink:0}
.cs-certi-cta .cs-certi-cta-action{font-weight:700;letter-spacing:.2px}
.cs-certi-cta--sm{font-size:10.5px;padding:6px 10px;gap:6px;min-height:28px}
.cs-certi-cta--sm .cs-certi-cta-sep{height:12px}
/* Pending state — same shape so layout never shifts; muted palette + clock
   icon signals "wait", click opens the modal below. */
.cs-certi-cta--pending{background:#22262f;color:rgba(255,255,255,.82)}
.cs-certi-cta--pending:hover{background:#2e333d;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.18)}
.cs-certi-cta--pending .cs-certi-cta-leading{stroke:#7eb3ff!important;color:#7eb3ff;opacity:.85}
.cs-certi-cta--pending .cs-certi-cta-trailing{stroke:#ffffff!important;color:#ffffff;opacity:.7}
@media(max-width:520px){
    .cs-certi-cta{font-size:12.5px;padding:9px 16px;min-height:40px;align-self:flex-start}
    .cs-certi-cta--sm{font-size:11px;padding:6px 11px;min-height:30px;align-self:auto}
}

/* Premium "raport w przygotowaniu" modal — same visual language as the
   inquiry modal so customers recognize the surface immediately. */
.cs-cc-modal{position:fixed;inset:0;background:rgba(8,10,20,.55);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);z-index:10000;display:none;align-items:center;justify-content:center;padding:20px;animation:csCcFadeIn .18s ease-out}
.cs-cc-modal.is-open{display:flex}
.cs-cc-modal-box{background:#fff;border-radius:18px;padding:28px 26px 24px;max-width:420px;width:100%;box-shadow:0 24px 64px rgba(0,0,0,.32);text-align:center;animation:csCcSlideUp .25s cubic-bezier(.16,1,.3,1)}
.cs-cc-modal-ico{width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#0066ff;margin:0 auto 14px;display:flex;align-items:center;justify-content:center}
.cs-cc-modal-ico svg{width:28px;height:28px;stroke:currentColor;fill:none;stroke-width:1.8}
.cs-cc-modal-title{font-size:18px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;margin:0 0 8px;line-height:1.3}
.cs-cc-modal-body{font-size:13.5px;color:#475569;line-height:1.55;margin:0 0 20px}
.cs-cc-modal-cta{display:inline-block;background:#0a0a0a;color:#fff;border:none;padding:11px 22px;border-radius:10px;font-size:13.5px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .15s}
.cs-cc-modal-cta:hover{background:#1a1a1a}
@keyframes csCcFadeIn{from{opacity:0}to{opacity:1}}
@keyframes csCcSlideUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
</style>
<div id="csCcPendingModal" class="cs-cc-modal" role="dialog" aria-modal="true" aria-labelledby="csCcPendingTitle" onclick="if(event.target===this)window.csCloseCertiCheckPending&&window.csCloseCertiCheckPending()">
    <div class="cs-cc-modal-box">
        <div class="cs-cc-modal-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h3 id="csCcPendingTitle" class="cs-cc-modal-title">Raport CertiCheck w przygotowaniu</h3>
        <p class="cs-cc-modal-body">Generujemy aktualną wersję raportu w tle. Zwykle zajmuje to kilka chwil — spróbuj ponownie za moment.</p>
        <button type="button" class="cs-cc-modal-cta" onclick="window.csCloseCertiCheckPending&&window.csCloseCertiCheckPending()">Rozumiem</button>
    </div>
</div>
<script>
(function(){
    var modal = document.getElementById('csCcPendingModal');
    if (!modal) return;
    window.csOpenCertiCheckPending = function(){
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    };
    window.csCloseCertiCheckPending = function(){
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && modal.classList.contains('is-open')) window.csCloseCertiCheckPending();
    });
})();
</script>
@endverbatim
@endonce
