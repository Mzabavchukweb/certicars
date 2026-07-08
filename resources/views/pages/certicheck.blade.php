@extends('layouts.public')
@section('title','CertiCheck — dla wybranych aut')
@section('description','CertiCheck to wewnętrzny standard CertiCars: pomiary lakieru, stan techniczny, ślady użytkowania i pełny raport PDF dla wybranych aut w naszej ofercie.')
@section('styles')
/* ===== CERTICHECK LANDING (marketing page) ===== */
.cc-page-wrap{background:linear-gradient(180deg,#f5f8ff 0%,#eef3fb 100%);min-height:100vh}
.cc-page{max-width:1200px;margin:0 auto;padding:56px 24px 96px;position:relative;box-sizing:border-box}

/* Hero grid — text left, hero image right */
.cc-hero-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:48px;align-items:center;margin-bottom:64px}
.cc-hero-content{min-width:0}
.cc-eyebrow{font-size:11.5px;font-weight:800;letter-spacing:2.2px;text-transform:uppercase;color:#0066ff;margin-bottom:22px;display:inline-flex;align-items:center;gap:12px}
.cc-eyebrow::before{content:'';width:24px;height:2px;background:#0066ff;border-radius:2px}
.cc-hero-title{font-size:52px;font-weight:900;color:#0a0a0a;letter-spacing:-1.4px;line-height:1.05;margin:0 0 24px;max-width:520px}
.cc-hero-title em{font-style:normal;color:#0066ff}
.cc-hero-desc{font-size:16px;color:#475569;line-height:1.75;margin:0 0 32px;max-width:520px}
.cc-hero-actions{display:flex;align-items:center;gap:24px;flex-wrap:wrap;margin-bottom:32px}
.cc-hero-cta{display:inline-flex;align-items:center;gap:10px;background:#0066ff;color:#fff;font-size:14.5px;font-weight:700;padding:14px 26px;border-radius:12px;text-decoration:none;letter-spacing:-.1px;transition:background .15s ease,transform .15s ease,box-shadow .15s ease;box-shadow:0 4px 14px rgba(0,102,255,.25)}
.cc-hero-cta:hover{background:#0055d8;transform:translateY(-1px);box-shadow:0 6px 18px rgba(0,102,255,.32)}
.cc-hero-cta svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.4}
.cc-hero-link{display:inline-flex;align-items:center;gap:6px;font-size:14.5px;font-weight:700;color:#0066ff;text-decoration:none;transition:opacity .15s ease}
.cc-hero-link:hover{opacity:.72}
.cc-hero-link svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}

/* Hero image */
.cc-hero-figure{position:relative;min-height:520px;display:flex;align-items:center;justify-content:center}
.cc-hero-figure img{width:100%;max-width:640px;height:auto;object-fit:contain;display:block}
.cc-hero-figure .cc-hero-desktop{display:block}
.cc-hero-figure .cc-hero-mobile{display:none}

/* Feature grid — 2x2 tiles matching reference */
.cc-features{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;max-width:640px;margin-bottom:28px}
.cc-feature{background:#fff;border:1px solid #e6ebf5;border-radius:16px;padding:22px 22px 20px;position:relative;text-decoration:none;color:inherit;transition:border-color .15s ease,transform .15s ease,box-shadow .15s ease;display:flex;flex-direction:column}
.cc-feature:hover{border-color:#c3d4f5;transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,102,255,.08)}
.cc-feature-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px}
.cc-feature-ico{width:44px;height:44px;border-radius:12px;background:#eef3ff;color:#0066ff;display:flex;align-items:center;justify-content:center}
.cc-feature-ico svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.8}
.cc-feature-arrow{width:32px;height:32px;border-radius:10px;background:#f5f7fb;color:#94a3b8;display:flex;align-items:center;justify-content:center;transition:background .15s ease,color .15s ease}
.cc-feature:hover .cc-feature-arrow{background:#eef3ff;color:#0066ff}
.cc-feature-arrow svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.4}
.cc-feature-title{font-size:16px;font-weight:800;color:#0a0a0a;letter-spacing:-.2px;line-height:1.3;margin:0 0 6px}
.cc-feature-desc{font-size:13px;color:#6b7280;line-height:1.55;margin:0}

/* Info box under feature grid */
.cc-info{max-width:640px;background:rgba(255,255,255,.6);border:1px solid #dfe6f3;border-radius:14px;padding:18px 20px;display:flex;gap:14px;align-items:flex-start}
.cc-info-ico{flex-shrink:0;width:32px;height:32px;border-radius:9px;background:#eef3ff;color:#0066ff;display:flex;align-items:center;justify-content:center;margin-top:1px}
.cc-info-ico svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2}
.cc-info p{font-size:12.5px;color:#475569;line-height:1.65;margin:0}
.cc-info p strong{color:#0a0a0a;font-weight:700}

/* Responsive */
@media(max-width:1024px){
    .cc-hero-grid{grid-template-columns:1fr;gap:32px}
    .cc-hero-title{font-size:44px}
    .cc-hero-figure{min-height:auto;order:-1}
    .cc-hero-figure img{max-width:420px}
    .cc-hero-figure .cc-hero-desktop{display:none}
    .cc-hero-figure .cc-hero-mobile{display:block}
    .cc-features,.cc-info{max-width:none}
}
@media(max-width:640px){
    .cc-page{padding:32px 20px 64px}
    .cc-hero-title{font-size:34px;letter-spacing:-1px}
    .cc-hero-desc{font-size:15px}
    .cc-hero-actions{gap:16px}
    .cc-hero-cta{padding:12px 20px;font-size:14px}
    .cc-features{grid-template-columns:1fr}
    .cc-hero-figure img{max-width:280px}
}
@endsection

@section('content')
<div class="cc-page-wrap">
    <div class="cc-page">
        <div class="cc-hero-grid">
            <div class="cc-hero-content">
                <div class="cc-eyebrow">CertiCheck — dla wybranych aut</div>
                <h1 class="cc-hero-title">Wiesz <em>więcej</em> przed przyjazdem</h1>
                <p class="cc-hero-desc">Przy wybranych autach przygotowujemy rozszerzony opis CertiCheck z dodatkowymi informacjami o stanie pojazdu, lakierze, śladach użytkowania i dokumentach. Dzięki temu łatwiej ocenisz auto jeszcze przed wizytą.</p>
                <div class="cc-hero-actions">
                    <a href="{{ route('catalog', ['certicheck' => 1]) }}" class="cc-hero-cta">
                        Zobacz auta z CertiCheck
                        <x-icon name="arrow-right" size="15"/>
                    </a>
                    <a href="#jak-dziala" class="cc-hero-link">
                        Jak działa CertiCheck?
                        <x-icon name="arrow-right" size="14"/>
                    </a>
                </div>

                <div class="cc-features" id="jak-dziala">
                    <a href="{{ route('catalog', ['certicheck' => 1]) }}" class="cc-feature">
                        <div class="cc-feature-head">
                            <span class="cc-feature-ico"><x-icon name="scan" size="22"/></span>
                            <span class="cc-feature-arrow" aria-hidden="true"><x-icon name="arrow-up-right" size="14"/></span>
                        </div>
                        <h3 class="cc-feature-title">Pomiary lakieru</h3>
                        <p class="cc-feature-desc">Wskazujemy pomiary i ewentualne różnice grubości powłoki w punktach kontrolnych.</p>
                    </a>
                    <a href="{{ route('catalog', ['certicheck' => 1]) }}" class="cc-feature">
                        <div class="cc-feature-head">
                            <span class="cc-feature-ico"><x-icon name="wrench" size="22"/></span>
                            <span class="cc-feature-arrow" aria-hidden="true"><x-icon name="arrow-up-right" size="14"/></span>
                        </div>
                        <h3 class="cc-feature-title">Stan techniczny</h3>
                        <p class="cc-feature-desc">Opisujemy widoczne elementy techniczne i podstawowe obserwacje z oględzin pojazdu.</p>
                    </a>
                    <a href="{{ route('catalog', ['certicheck' => 1]) }}" class="cc-feature">
                        <div class="cc-feature-head">
                            <span class="cc-feature-ico"><x-icon name="search" size="22"/></span>
                            <span class="cc-feature-arrow" aria-hidden="true"><x-icon name="arrow-up-right" size="14"/></span>
                        </div>
                        <h3 class="cc-feature-title">Ślady użytkowania</h3>
                        <p class="cc-feature-desc">Pokazujemy widoczne ślady eksploatacji i ich lokalizację.</p>
                    </a>
                    <a href="{{ route('catalog', ['certicheck' => 1]) }}" class="cc-feature">
                        <div class="cc-feature-head">
                            <span class="cc-feature-ico"><x-icon name="file-text" size="22"/></span>
                            <span class="cc-feature-arrow" aria-hidden="true"><x-icon name="arrow-up-right" size="14"/></span>
                        </div>
                        <h3 class="cc-feature-title">Raport PDF</h3>
                        <p class="cc-feature-desc">Czytelne podsumowanie ze zdjęciami i danymi do pobrania.</p>
                    </a>
                </div>

                <div class="cc-info">
                    <span class="cc-info-ico" aria-hidden="true"><x-icon name="shield" size="16"/></span>
                    <p><strong>CertiCheck</strong> to wewnętrzny standard kontroli jakości CertiCars, a nie opinia rzeczoznawcy. Opis dotyczy stanu pojazdu na dzień oględzin i obejmuje elementy możliwe do oceny bez specjalistycznego demontażu podzespołów.</p>
                </div>
            </div>

            <div class="cc-hero-figure" aria-hidden="true">
                <img class="cc-hero-desktop" src="/images/bohater-desktop.png" alt="Doradca CertiCars prezentujący raport CertiCheck" width="1672" height="941" loading="eager" fetchpriority="high" decoding="async">
                <img class="cc-hero-mobile" src="/images/bohater-mobile.png" alt="Doradca CertiCars prezentujący raport CertiCheck" width="941" height="1672" loading="eager" fetchpriority="high" decoding="async">
            </div>
        </div>
    </div>
</div>
@endsection
