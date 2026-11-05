@extends('layouts.public')
@section('meta_title_full','CertiCheck: pomiar lakieru, opony i 360° auta | CertiCars')
@section('title','CertiCheck: pomiar lakieru, opony i 360° auta')
@section('description','Sprawdź auto przed zakupem: pomiary grubości lakieru, głębokość bieżnika, zdjęcia śladów użytkowania i widok 360°. CertiCheck przy wybranych autach CertiCars.')
@section('og_title','CertiCheck: pomiar lakieru, opony i 360° auta | CertiCars')
@section('og_description','Sprawdź auto przed zakupem: pomiary grubości lakieru, głębokość bieżnika, zdjęcia śladów użytkowania i widok 360°. CertiCheck przy wybranych autach CertiCars.')

@php
    // Projekt z 4.11.2026 (podgląd interaktywny v3). Wszystkie liczby, zdjęcia i 360° pochodzą
    // z ogłoszenia auta pokazowego (App\Support\AboutShowcase, to samo auto co na „O nas”).
    // Brak danych w danej części = ta część się nie pokazuje, nic nie jest wpisane na sztywno.
    $sc       = $showcase;
    $check    = $sc['check'] ?? null;
    $hasOut   = $sc && ($sc['exterior'] || $sc['poster']);
    $outSpins = $sc && $sc['exterior'];
    $hasIn    = $sc && $sc['interior'];
    $paint    = $check['paint']['items'] ?? [];
    $tires    = $check['tires'] ?? null;
    $dmg      = $check['damages']['items'] ?? [];
    $dmgN     = count($dmg);

    $tabs = [];
    if ($hasOut) $tabs[] = 'outside';
    if ($hasIn)  $tabs[] = 'inside';
    if ($paint)  $tabs[] = 'paint';
    if ($tires)  $tabs[] = 'tires';
    if ($dmg)    $tabs[] = 'wear';
    $first = $tabs[0] ?? null;

    $tabLabel = [
        'outside' => [$outSpins ? '360° nadwozia' : 'Nadwozie', 'car'],
        'inside'  => ['360° wnętrza', 'armchair'],
        'paint'   => ['Lakier', 'scan-line'],
        'tires'   => ['Koła i opony', 'tabler:wheel'],
        'wear'    => ['Stan i ślady', 'map-pin'],
    ];
    $nextLabel = [
        'outside' => $outSpins ? 'Wróć do widoku 360°' : 'Zobacz nadwozie',
        'inside'  => 'Zajrzyj do wnętrza',
        'paint'   => 'Sprawdź pomiary lakieru',
        'tires'   => 'Sprawdź koła i opony',
        'wear'    => 'Zobacz stan i ślady',
    ];
    $nextTab = function (string $t) use ($tabs) {
        return $tabs[(array_search($t, $tabs, true) + 1) % count($tabs)];
    };

    $carName  = $sc['name'] ?? null;
    $tireType = $tires['rows'][0]['type'] ?? null;
    $miejsca  = $dmgN === 1 ? 'Jedno sfotografowane miejsce'
              : ($dmgN % 10 >= 2 && $dmgN % 10 <= 4 && ($dmgN % 100 < 12 || $dmgN % 100 > 14)
                  ? $dmgN . ' sfotografowane miejsca' : $dmgN . ' sfotografowanych miejsc');

    $faqs = [
        ['Czy każde auto w ofercie ma CertiCheck?', 'Nie. CertiCheck jest dostępny przy wybranych samochodach. Jego materiały znajdziesz w ogłoszeniu konkretnego auta.'],
        ['Czy każdy CertiCheck zawiera te same materiały?', 'Zakres może się różnić między autami. W ogłoszeniu zobaczysz, jakie pomiary, zdjęcia i dostępne dokumenty przygotowaliśmy dla danego egzemplarza.'],
        ['Jak odczytywać pomiary lakieru?', 'Odczyt w mikrometrach pokazuje grubość powłoki w miejscu pomiaru. Sam wynik nie przesądza o historii samochodu. Warto zestawić go ze zdjęciami, dokumentami i oględzinami.'],
        ['Czy mogę sprawdzić auto w wybranym serwisie?', 'Tak. Skontaktuj się z nami przed wizytą, żeby ustalić miejsce, termin i szczegóły dodatkowego sprawdzenia.'],
        ['Gdzie znajdę pełne materiały?', 'W ogłoszeniu samochodu objętego CertiCheck. Jeśli potrzebujesz dodatkowego zdjęcia lub wyjaśnienia odczytu, zadzwoń. Porozmawiamy o konkretnym aucie.'],
    ];
@endphp

@section('extra_head')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn ($f) => [
        '@type' => 'Question',
        'name' => $f[0],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
    ], $faqs),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('styles')
@include('partials.onas-styl')
/* ===== CertiCheck (projekt 4.11.2026) w systemie strony „O nas” ===== */
.onas .cc-hero .hero-art img{width:600px;right:-46px;bottom:56px}
.onas .cc-hero .hero-glow{right:40px;top:90px;width:560px}
.onas .cc-hero-note{display:flex;align-items:center;gap:9px;margin-top:22px;font-size:13px;color:rgba(255,255,255,.62)}
.onas .cc-hero-note svg{width:16px;height:16px;color:#8fc3ff}

.onas .cc-define{padding:96px 0 0}
.onas .cc-define .human-layout{padding-bottom:56px;border-bottom:1px solid var(--line)}
.onas .cc-define .human-story{padding-top:34px}

.onas .cc-materials{padding:56px 0 96px}
.onas .cc-materials .section-intro{margin-bottom:40px}
.onas .cc-mat-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.onas .cc-mat{display:grid;grid-template-columns:minmax(0,1fr) 150px;gap:24px;align-items:center;background:#fff;border:1px solid var(--line);border-radius:12px;padding:28px;color:inherit;text-decoration:none}
.onas .cc-mat.no-visual{grid-template-columns:1fr}
.onas .cc-over{display:block;font-size:11px;font-weight:800;letter-spacing:1.6px;text-transform:uppercase;color:var(--blue)}
.onas .cc-mat h3{font-size:18px;margin:10px 0 8px}
.onas .cc-mat p{font-size:14px;line-height:1.65}
.onas .cc-mat-link{display:inline-flex;align-items:center;gap:8px;margin-top:14px;font-size:14px;font-weight:700;color:var(--blue)}
.onas .cc-mat-link svg{width:16px;height:16px}
.onas .cc-mat-vis{position:relative;aspect-ratio:1;border-radius:12px;background:var(--ice);overflow:hidden;display:flex;flex-direction:column;justify-content:center;gap:8px;padding:12px}
.onas .cc-mat-vis img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.onas .cc-mat-vis .chip{position:absolute;left:8px;bottom:8px;display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:999px;background:#fff;font-size:11px;font-weight:700;color:var(--ink)}
.onas .cc-mat-vis .chip i{width:7px;height:7px;border-radius:50%;background:#f59e0b}
.onas .cc-mini{display:flex;justify-content:space-between;align-items:baseline;gap:6px;background:#fff;border:1px solid var(--line);border-radius:9px;padding:9px 10px}
.onas .cc-mini span{font-size:11px;color:var(--muted);min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.onas .cc-mini strong{font-size:19px;font-weight:800;color:var(--ink);white-space:nowrap}
.onas .cc-mini small{font-size:10px;font-weight:600;color:var(--muted)}
.onas .cc-tread{align-items:center;text-align:center}
.onas .cc-tread svg{width:30px;height:30px;color:var(--blue)}
.onas .cc-tread span{font-size:11px;color:var(--muted)}
.onas .cc-tread strong{font-size:26px;font-weight:800;color:var(--ink);line-height:1}
.onas .cc-tread small{font-size:12px;font-weight:600;color:var(--muted)}
.onas .cc-mat-foot{display:flex;justify-content:space-between;gap:20px;margin-top:22px;font-size:13px;color:var(--muted)}
.onas .cc-mat-foot p{display:flex;align-items:center;gap:8px;font-size:13px}
.onas .cc-mat-foot svg{width:15px;height:15px;color:var(--blue)}

.onas .cc-example{padding:96px 0;background:var(--ice)}
.onas .cc-example .section-intro{margin-bottom:40px}
.onas .cc-report{background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 4px 16px rgba(0,0,0,.04);overflow:hidden}
.onas .cc-report-top{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;padding:24px 26px 0}
.onas .cc-report-top h3{font-size:22px;margin-top:6px}
.onas .cc-report-top .tag{font-size:12px;font-weight:600;color:var(--muted);background:var(--ice);border-radius:8px;padding:7px 11px;white-space:nowrap}
.onas .cc-tabs{display:flex;gap:4px;margin:18px 26px 0;padding:4px;background:#f2f6fc;border-radius:10px;overflow-x:auto;scrollbar-width:none}
.onas .cc-tabs::-webkit-scrollbar{display:none}
.onas .cc-tabs button{flex:1 0 auto;display:flex;align-items:center;justify-content:center;gap:8px;min-height:44px;padding:10px 14px;border:0;border-radius:8px;background:transparent;font:inherit;font-size:13px;font-weight:700;color:#637895;white-space:nowrap;cursor:pointer}
.onas .cc-tabs button svg{width:16px;height:16px}
.onas .cc-tabs button[aria-selected=true]{background:#fff;color:var(--blue);box-shadow:0 2px 7px #213f6d14}
.onas .cc-panel{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:22px;padding:22px 26px 26px}
.onas .cc-panel[hidden]{display:none}
.onas .cc-panel .viewer-stage{border-radius:12px;aspect-ratio:16/10}
.onas .viewer-stage>.cc-media{position:absolute;inset:0}
.onas .cc-panel .viewer-badge{left:14px;top:14px}
.onas .cc-pane{border:1px solid var(--line);border-radius:12px;padding:24px;min-width:0}
.onas .cc-pane>h4{font-size:18px;font-weight:800;color:var(--ink)}
.onas .cc-pane>p{font-size:13.5px;margin-top:4px}
.onas .cc-pane .paint-grid{margin-top:18px}
.onas .cc-pane .reading-note{font-size:12px;color:var(--muted);margin-top:14px}
.onas .cc-aside{display:flex;flex-direction:column;background:var(--ice);border-radius:12px;padding:24px}
.onas .cc-aside h3{font-size:22px;line-height:1.2;margin:10px 0 12px}
.onas .cc-aside>p{font-size:14px;line-height:1.7}
.onas .cc-point{display:flex;gap:12px;margin-top:18px}
.onas .cc-point>svg{width:20px;height:20px;color:var(--blue);margin-top:2px}
.onas .cc-point strong{display:block;font-size:14px;color:var(--ink)}
.onas .cc-point span{display:block;font-size:13px;color:var(--muted);margin-top:2px}
.onas .cc-tip{font-size:12.5px;line-height:1.65;color:var(--muted);border-top:1px solid #dfe8f5;margin-top:18px;padding-top:16px}
.onas .cc-next{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:auto;width:100%;min-height:48px;padding:12px 16px;border:1px solid #cdddf7;border-radius:12px;background:#fff;font:inherit;font-size:14px;font-weight:700;color:var(--blue);cursor:pointer;text-align:left}
.onas .cc-aside .cc-tip+.cc-next,.onas .cc-aside .cc-point+.cc-next{margin-top:22px}
.onas .cc-next svg{width:16px;height:16px;flex-shrink:0}
.onas .cc-report-foot{display:flex;justify-content:space-between;align-items:center;gap:16px;border-top:1px solid var(--line);padding:16px 26px;font-size:13px;color:var(--muted)}
.onas .cc-report-foot p{display:flex;align-items:center;gap:8px;font-size:13px}
.onas .cc-report-foot p svg{width:15px;height:15px;color:var(--blue)}
.onas .cc-example-note{text-align:center;font-size:12.5px;margin-top:18px}

.onas .cc-process{padding:96px 0}
.onas .cc-scope{padding:96px 0;background:var(--ice)}
.onas .cc-scope-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.onas .cc-scope-col{display:flex;flex-direction:column;background:#fff;border:1px solid var(--line);border-radius:12px;overflow:hidden}
.onas .cc-scope-head{display:flex;align-items:center;gap:14px;padding:24px 26px;border-bottom:1px solid var(--line)}
.onas .cc-scope-head .ico{display:flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:var(--blue);color:#fff;flex-shrink:0}
.onas .cc-scope-col.no .cc-scope-head .ico{background:#eef2f7;color:#475569}
.onas .cc-scope-head .ico svg{width:20px;height:20px}
.onas .cc-scope-head h3{font-size:20px;margin-top:3px}
.onas .cc-scope-col ul{list-style:none;margin:0;padding:6px 26px 10px;flex:1}
.onas .cc-scope-col li{display:grid;grid-template-columns:20px 1fr;gap:12px;padding:18px 0;border-top:1px solid #eef2f8}
.onas .cc-scope-col li:first-child{border-top:0}
.onas .cc-scope-col li>svg{width:18px;height:18px;margin-top:2px;color:var(--blue)}
.onas .cc-scope-col.no li>svg{color:#64748b}
.onas .cc-scope-col h4{font-size:15px;font-weight:800;color:var(--ink)}
.onas .cc-scope-col li p{font-size:13.5px;line-height:1.65;margin-top:4px}
.onas .cc-scope-docs{display:flex;align-items:flex-start;gap:9px;background:#f8fafd;border-top:1px solid var(--line);padding:16px 26px;font-size:13px;color:var(--muted)}
.onas .cc-scope-docs svg{width:16px;height:16px;color:var(--blue);margin-top:2px}
.onas .cc-scope-bottom{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-top:24px;padding-top:18px;border-top:1px solid #dfe8f5}
.onas .cc-scope-bottom p{display:flex;align-items:center;gap:8px;font-size:13px}
.onas .cc-scope-bottom p svg{width:15px;height:15px;color:var(--blue)}

.onas .cc-faq{padding:96px 0}
.onas .cc-faq-layout{display:grid;grid-template-columns:360px minmax(0,1fr);gap:64px;align-items:start}
.onas .cc-faq-list{background:#fff;border:1px solid var(--line);border-radius:12px;padding:6px 24px}
.onas .cc-faq-list details{border-top:1px solid #eef2f8}
.onas .cc-faq-list details:first-child{border-top:0}
.onas .cc-faq-list summary{display:flex;justify-content:space-between;align-items:center;gap:16px;min-height:44px;padding:18px 0;font-size:15px;font-weight:700;color:var(--ink);cursor:pointer;list-style:none}
.onas .cc-faq-list summary::-webkit-details-marker{display:none}
.onas .cc-faq-list summary svg{width:18px;height:18px;color:var(--blue);flex-shrink:0}
.onas .cc-faq-list details[open] summary{color:var(--blue)}
.onas .cc-faq-list details[open] summary svg{transform:rotate(180deg)}
.onas .cc-faq-list details p{font-size:14.5px;line-height:1.75;padding:0 0 20px}

.onas .cc-contact{padding:0 0 96px}
.onas .cc-contact .contact-actions{display:flex;flex-direction:column;align-items:flex-start;gap:12px}
.onas .cc-contact .cc-place{display:flex;align-items:center;gap:7px;font-size:13px;color:rgba(255,255,255,.62)}
.onas .cc-contact .cc-place svg{width:15px;height:15px}

@media(max-width:1100px){
  .onas .cc-hero .hero-art img{width:480px;right:-40px;bottom:80px}
  .onas .cc-panel{grid-template-columns:minmax(0,1fr) 260px}
}
@media(max-width:900px){
  .onas .cc-define,.onas .cc-example,.onas .cc-process,.onas .cc-scope,.onas .cc-faq{padding-top:64px}
  .onas .cc-materials,.onas .cc-example,.onas .cc-process,.onas .cc-scope,.onas .cc-faq{padding-bottom:64px}
  .onas .cc-define .human-layout{padding-bottom:40px}
  .onas .cc-materials{padding-top:40px}
  .onas .cc-mat-grid,.onas .cc-scope-grid{grid-template-columns:1fr}
  .onas .cc-panel{grid-template-columns:1fr}
  .onas .cc-faq-layout{grid-template-columns:1fr;gap:28px}
  .onas .cc-contact{padding-bottom:64px}
}
@media(max-width:760px){
  .onas .cc-hero .hero-art{height:auto;padding:8px 0 40px}
  .onas .cc-hero .hero-art img{position:relative;right:auto;bottom:auto;width:100%;max-width:440px;margin:0 auto}
  .onas .cc-hero .hero-glow{left:0;right:0;top:20px;width:100%}
  .onas .cc-hero .actions{flex-direction:column;align-items:stretch}
  .onas .cc-hero .button{flex:none;width:100%;height:auto;padding:15px 18px;font-size:15px}
  .onas .cc-mat{grid-template-columns:1fr;gap:16px;padding:20px}
  .onas .cc-mat-vis{aspect-ratio:auto;min-height:150px;flex-direction:row;align-items:stretch}
  .onas .cc-mat-vis .cc-mini{flex:1;flex-direction:column;justify-content:center;gap:4px}
  .onas .cc-tread{flex-direction:row;justify-content:center;align-items:center;gap:12px;min-height:96px}
  .onas .cc-tread strong{font-size:22px}
  .onas .cc-mat-foot,.onas .cc-scope-bottom,.onas .cc-report-foot{flex-direction:column;align-items:flex-start}
  .onas .cc-report-top{padding:20px 16px 0;flex-direction:column;align-items:flex-start}
  .onas .cc-report-top .tag{display:none}
  .onas .cc-tabs{margin:16px 16px 0}
  .onas .cc-panel{padding:16px}
  .onas .cc-pane{padding:18px 16px}
  .onas .cc-aside{padding:20px}
  .onas .cc-report-foot{padding:16px}
  .onas .cc-scope-head,.onas .cc-scope-docs{padding-left:18px;padding-right:18px}
  .onas .cc-scope-col ul{padding-left:18px;padding-right:18px}
  .onas .cc-faq-list{padding:4px 18px}
}
@endsection

@section('content')
<div class="onas">
<section id="hero" class="hero cc-hero">
 <div class="container hero-inner">
  <div class="hero-copy">
   <div class="eyebrow light">POZNAJ CERTICHECK</div>
   <h1>Wiesz więcej.<br><span>Zanim przyjedziesz.</span></h1>
   <p>Pomiary lakieru, stan opon, zdjęcia śladów użytkowania i widoki 360°. Zobacz szczegóły konkretnego samochodu, zanim umówisz oględziny.</p>
   <div class="actions">
    @if($tabs)<a class="button primary" href="#przyklad">Zobacz przykładowy raport <x-icon name="arrow-right" size="16"/></a>@endif
    <a class="button secondary" href="{{ route('catalog') }}">Przejdź do oferty</a>
   </div>
   <div class="cc-hero-note"><x-icon name="info" size="16"/><span>CertiCheck jest dostępny przy wybranych samochodach.</span></div>
  </div>
  <div class="hero-art"><div class="hero-glow"></div><img src="/img/certicheck/doradca-lupa-1200.webp" srcset="/img/certicheck/doradca-lupa-760.webp 760w, /img/certicheck/doradca-lupa-1200.webp 1200w" sizes="(max-width: 760px) 92vw, 600px" width="1200" height="800" alt="Doradca CertiCars z lupą przy białym SUV-ie" fetchpriority="high" decoding="async"></div>
 </div>
 <svg class="hero-curve" viewBox="0 0 1440 58" preserveAspectRatio="none" aria-hidden="true"><path d="M0 8C390 58 1050 58 1440 8V58H0Z" fill="white"/></svg>
</section>

<section id="czym-jest" class="human-section cc-define">
 <div class="container human-layout">
  <div class="human-title"><div class="eyebrow">NASZ STANDARD PREZENTACJI</div><h2 class="brand-motto">Czym jest<br><span>CertiCheck?</span></h2></div>
  <div class="human-story"><p><strong>CertiCheck to przygotowany przez nas raport o samochodzie, dostępny przy wybranych autach z oferty.</strong> Zbieramy w nim pomiary, zdjęcia i zauważone ślady użytkowania, żebyś mógł poznać auto jeszcze przed wizytą.</p></div>
 </div>
</section>

<section id="materialy" class="cc-materials">
 <div class="container">
  <div class="section-intro"><div><div class="eyebrow">SZCZEGÓŁY MAJĄ ZNACZENIE</div><h2>Co znajdziesz<br>w CertiCheck?</h2></div></div>
  @php $card = fn ($t) => in_array($t, $tabs, true) ? '#przyklad' : route('catalog'); @endphp
  <div class="cc-mat-grid">
   <a class="cc-mat {{ count($paint) < 2 ? 'no-visual' : '' }}" href="{{ $card('paint') }}" data-open-tab="paint">
    <div><span class="cc-over">Lakier</span><h3>Pomiary grubości lakieru</h3><p>Sprawdź odczyty przypisane do poszczególnych elementów nadwozia.</p><span class="cc-mat-link">Zobacz pomiary <x-icon name="arrow-right" size="16"/></span></div>
    @if(count($paint) >= 2)
    <div class="cc-mat-vis" aria-hidden="true">@foreach(array_slice($paint, 0, 2) as $r)<div class="cc-mini"><span>{{ $r['label'] }}</span><strong>{{ $r['value'] }} <small>µm</small></strong></div>@endforeach</div>
    @endif
   </a>
   <a class="cc-mat {{ $tires ? '' : 'no-visual' }}" href="{{ $card('tires') }}" data-open-tab="tires">
    <div><span class="cc-over">Koła i opony</span><h3>Stan opon i bieżnika</h3><p>Zobacz pomiar dla każdego koła i informacje o zamontowanym komplecie.</p><span class="cc-mat-link">Sprawdź opony <x-icon name="arrow-right" size="16"/></span></div>
    @if($tires)
    <div class="cc-mat-vis cc-tread" aria-hidden="true"><x-icon name="tabler:wheel" size="30"/><span>Głębokość bieżnika</span><strong>{{ $tires['rows'][0]['depth'] }} <small>mm</small></strong></div>
    @endif
   </a>
   <a class="cc-mat {{ $dmg ? '' : 'no-visual' }}" href="{{ $card('wear') }}" data-open-tab="wear">
    <div><span class="cc-over">Stan i ślady</span><h3>Zdjęcia śladów użytkowania</h3><p>Przyjrzyj się zauważonym rysom, otarciom i innym śladom użytkowania.</p><span class="cc-mat-link">Obejrzyj ślady <x-icon name="arrow-right" size="16"/></span></div>
    @if($dmg)
    <div class="cc-mat-vis" aria-hidden="true"><img src="{{ $dmg[0]['photo'] }}" alt="" loading="lazy" decoding="async"><span class="chip"><i></i>Ślad 1 z {{ $dmgN }}</span></div>
    @endif
   </a>
   <a class="cc-mat {{ $sc && $sc['poster'] ? '' : 'no-visual' }}" href="{{ $card('outside') }}" data-open-tab="outside">
    <div><span class="cc-over">Prezentacje 360°</span><h3>Nadwozie i wnętrze w 360°</h3><p>Obejrzyj auto dookoła i rozejrzyj się po kabinie, obracając widok.</p><span class="cc-mat-link">Uruchom widok 360° <x-icon name="arrow-right" size="16"/></span></div>
    @if($sc && $sc['poster'])
    <div class="cc-mat-vis" aria-hidden="true"><img src="{{ $sc['poster'] }}" alt="" loading="lazy" decoding="async"><span class="chip">360°</span></div>
    @endif
   </a>
  </div>
  <div class="cc-mat-foot"><p><x-icon name="file-text" size="15"/>Dokumenty i historia serwisowa: w ogłoszeniu pokazujemy dostępne materiały.</p>@if($sc)<span>Pokazane przykłady: {{ $sc['name'] }}</span>@endif</div>
 </div>
</section>

@if($tabs)
<section id="przyklad" class="cc-example">
 <div class="container">
  <div class="section-intro"><div><div class="eyebrow">CERTICHECK W PRAKTYCE</div><h2>Zajrzyj do raportu.</h2></div><p>Zdjęcia i pomiary jednego samochodu. Przełącz widok, żeby poznać szczegóły.</p></div>
  <div class="cc-report">
   <div class="cc-report-top"><div><span class="cc-over">Przykładowe materiały</span><h3>{{ $carName }}</h3></div><span class="tag">Poznaj zawartość CertiCheck</span></div>
   <div class="cc-tabs" role="tablist" aria-label="Przykładowe materiały CertiCheck">
    @foreach($tabs as $t)
    <button id="cc-tab-{{ $t }}" type="button" role="tab" aria-selected="{{ $t === $first ? 'true' : 'false' }}" aria-controls="cc-panel-{{ $t }}"><x-icon :name="$tabLabel[$t][1]" size="16"/>{{ $tabLabel[$t][0] }}</button>
    @endforeach
   </div>

   @if(in_array('outside', $tabs, true))
   <div id="cc-panel-outside" class="cc-panel" role="tabpanel" aria-labelledby="cc-tab-outside" @if($first !== 'outside') hidden @endif>
    <div class="viewer-stage" data-cc-stage aria-label="Interaktywna prezentacja {{ $sc['name'] }}">
     <div class="cc-media" data-kind="{{ $outSpins ? 'frames' : 'static' }}" @if($outSpins) data-frames='@json($sc['exterior']['urls'])' data-sprites='@json($sc['exterior']['sprites'])' @endif>
      @if($sc['poster'])<img class="viewer-poster" src="{{ $sc['poster'] }}" alt="{{ $sc['name'] }}, zdjęcie z ogłoszenia" draggable="false" loading="lazy" decoding="async">@endif
      @if($outSpins)<canvas role="img" aria-label="{{ $sc['name'] }}: widok 360° nadwozia z ogłoszenia"></canvas>@endif
     </div>
     @if($outSpins)
     <span class="viewer-badge">360°</span>
     <div class="viewer-controls"><button type="button" data-rotate="-1" aria-label="Obróć widok w lewo">←</button><span>Przeciągnij, aby obrócić auto</span><button type="button" data-rotate="1" aria-label="Obróć widok w prawo">→</button></div>
     @endif
    </div>
    <aside class="cc-aside">
     <span class="cc-over">{{ $outSpins ? '360° nadwozia' : 'Nadwozie' }}</span>
     <h3>Obejrzyj auto<br>z każdej strony.</h3>
     <p>@if($outSpins)Przeciągnij zdjęcie lub użyj strzałek. Zatrzymaj widok tam, gdzie chcesz przyjrzeć się detalom.@else Zdjęcie nadwozia z ogłoszenia tego samochodu.@endif</p>
     <div class="cc-point"><x-icon name="camera" size="20"/><div><strong>Rzeczywiste zdjęcia</strong><span>@if($outSpins)Pełny obrót tego samego samochodu.@else Zdjęcie z ogłoszenia, bez retuszu.@endif</span></div></div>
     @if($dmg)<p class="cc-tip">Zauważone rysy i otarcia pokazujemy osobno w zakładce „Stan i ślady”.</p>@endif
     @if(count($tabs) > 1)<button class="cc-next" type="button" data-open-tab="{{ $nextTab('outside') }}">{{ $nextLabel[$nextTab('outside')] }} <x-icon name="arrow-right" size="16"/></button>@endif
    </aside>
   </div>
   @endif

   @if(in_array('inside', $tabs, true))
   <div id="cc-panel-inside" class="cc-panel" role="tabpanel" aria-labelledby="cc-tab-inside" @if($first !== 'inside') hidden @endif>
    <div class="viewer-stage" data-cc-stage aria-label="Wnętrze {{ $sc['name'] }} w 360°">
     <div class="cc-media" data-kind="{{ $sc['interior']['type'] }}" @if($sc['interior']['type'] === 'frames') data-frames='@json($sc['interior']['urls'])' data-sprites='@json($sc['interior']['sprites'])' @endif>
      @if($sc['interior']['type'] === 'frames')
       <canvas role="img" aria-label="{{ $sc['name'] }}: widok 360° wnętrza z ogłoszenia"></canvas>
      @else
       <div class="pano-host" data-pano-src="{{ $sc['interior']['src'] }}" role="img" aria-label="Panorama 360 stopni wnętrza {{ $sc['name'] }}"></div><span class="pano-loading" role="status">Wczytuję wnętrze…</span>
      @endif
     </div>
     <span class="viewer-badge">360°</span>
     <div class="viewer-controls"><button type="button" data-rotate="-1" aria-label="Obróć widok w lewo">←</button><span>Przeciągnij, aby się rozejrzeć</span><button type="button" data-rotate="1" aria-label="Obróć widok w prawo">→</button></div>
    </div>
    <aside class="cc-aside">
     <span class="cc-over">360° wnętrza</span>
     <h3>Rozejrzyj się<br>po kabinie.</h3>
     <p>Przesuwaj panoramę na boki, w górę i w dół. Obejrzyj deskę rozdzielczą, fotele i przestrzeń dla pasażerów.</p>
     <div class="cc-point"><x-icon name="armchair" size="20"/><div><strong>Ty wybierasz kierunek</strong><span>Zobacz wnętrze z różnych stron.</span></div></div>
     <p class="cc-tip">Chcesz przyjrzeć się konkretnemu detalowi? Zapytaj nas o dodatkowe zdjęcie.</p>
     @if(count($tabs) > 1)<button class="cc-next" type="button" data-open-tab="{{ $nextTab('inside') }}">{{ $nextLabel[$nextTab('inside')] }} <x-icon name="arrow-right" size="16"/></button>@endif
    </aside>
   </div>
   @endif

   @if(in_array('paint', $tabs, true))
   <div id="cc-panel-paint" class="cc-panel" role="tabpanel" aria-labelledby="cc-tab-paint" @if($first !== 'paint') hidden @endif>
    <div class="cc-pane">
     <h4>Pomiary grubości lakieru</h4><p>Odczyty w wybranych punktach nadwozia.</p>
     <div class="paint-grid">@foreach($paint as $r)<div class="paint-reading"><span>{{ $r['label'] }}</span><strong>{{ $r['value'] }} <small>µm</small></strong></div>@endforeach</div>
     <p class="reading-note">µm to mikrometry, jednostka grubości powłoki.</p>
    </div>
    <aside class="cc-aside">
     <span class="cc-over">Pomiary lakieru</span>
     <h3>Zobacz odczyty.<br>Porównaj elementy.</h3>
     <p>Każdy wynik jest przypisany do konkretnego elementu. Możesz wskazać miejsce, o które chcesz dopytać podczas oględzin.</p>
     <div class="cc-point"><x-icon name="scan-line" size="20"/><div><strong>Pomiary punktowe</strong><span>Odczyt dotyczy miejsca, w którym wykonano pomiar.</span></div></div>
     <p class="cc-tip">Sama grubość powłoki nie przesądza o historii auta. Zestaw wynik ze zdjęciami i oględzinami.</p>
     @if(count($tabs) > 1)<button class="cc-next" type="button" data-open-tab="{{ $nextTab('paint') }}">{{ $nextLabel[$nextTab('paint')] }} <x-icon name="arrow-right" size="16"/></button>@endif
    </aside>
   </div>
   @endif

   @if(in_array('tires', $tabs, true))
   <div id="cc-panel-tires" class="cc-panel" role="tabpanel" aria-labelledby="cc-tab-tires" @if($first !== 'tires') hidden @endif>
    <div class="cc-pane">
     <h4>Koła i opony</h4><p>{{ $tires['mounted'] ? 'Zamontowany komplet' : 'Komplet opon' }}, zmierzona głębokość bieżnika.</p>
     <div class="tires-grid">@foreach($tires['rows'] as $t)<div class="tire-reading"><div><strong>{{ $t['label'] }}</strong>@if($t['type'])<span>{{ $t['type'] }}</span>@endif</div><b @class([$t['status'] => $t['status'] !== 'ok'])>{{ $t['depth'] }} <small>mm</small></b></div>@endforeach</div>
     <div class="wheel-info">@if($tires['rim'])<span>{{ $tires['rim'] }}</span>@else<span></span>@endif
      @if($tires['anyIssue'])<strong class="warn"><x-icon name="info" size="15"/>Wymaga uwagi</strong>@else<strong><x-icon name="check" size="15"/>Stan bardzo dobry</strong>@endif</div>
    </div>
    <aside class="cc-aside">
     <span class="cc-over">Koła i opony</span>
     <h3>Każde koło<br>ma swój pomiar.</h3>
     <p>Sprawdź zmierzoną głębokość bieżnika każdej opony oraz informacje o komplecie zamontowanym na aucie.</p>
     <div class="cc-point"><x-icon name="tabler:wheel" size="20"/><div><strong>{{ $tires['mounted'] ? 'Zamontowany komplet' : 'Komplet opon' }}</strong>@if($tireType)<span>W tym przykładzie: {{ mb_strtolower($tireType) }}.</span>@endif</div></div>
     <p class="cc-tip">Głębokość bieżnika to jedna z informacji o kondycji opon. Ich stan oceń także podczas oględzin.</p>
     @if(count($tabs) > 1)<button class="cc-next" type="button" data-open-tab="{{ $nextTab('tires') }}">{{ $nextLabel[$nextTab('tires')] }} <x-icon name="arrow-right" size="16"/></button>@endif
    </aside>
   </div>
   @endif

   @if(in_array('wear', $tabs, true))
   <div id="cc-panel-wear" class="cc-panel" role="tabpanel" aria-labelledby="cc-tab-wear" @if($first !== 'wear') hidden @endif>
    <div class="cc-pane">
     <h4>Stan wizualny i ślady eksploatacji</h4><p>Wybierz oznaczenie, aby zobaczyć zdjęcie tego miejsca.</p>
     <div class="wear-layout"><div class="wear-map-wrap"><div class="wear-map"><img src="{{ $check['damages']['map'] }}" alt="Poglądowa mapa nadwozia z oznaczonymi śladami ({{ $dmgN }})" loading="lazy" decoding="async">@foreach($dmg as $i => $d)<button type="button" class="wear-marker" data-damage="{{ $i + 1 }}" data-photo="{{ $d['photo'] }}" data-label="{{ $d['label'] }}" data-area="{{ $d['area'] }}" aria-pressed="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Ślad {{ $i + 1 }}: zobacz zdjęcie" style="left:{{ $d['x'] }}%;top:{{ $d['y'] }}%">{{ $i + 1 }}</button>@endforeach</div><span>Poglądowa mapa nadwozia</span></div><div class="wear-photo"><img data-damage-photo src="{{ $dmg[0]['photo'] }}" alt="Ślad 1: {{ mb_strtolower($dmg[0]['label']) }}@if($dmg[0]['area']), {{ $dmg[0]['area'] }}@endif" loading="lazy" decoding="async"><div class="wear-caption"><span data-damage-label>{{ $dmg[0]['label'] }}</span><strong data-damage-counter aria-live="polite">1 / {{ $dmgN }}</strong></div><div class="wear-selectors" aria-label="Wybierz zdjęcie śladu">@foreach($dmg as $i => $d)<button type="button" data-damage="{{ $i + 1 }}" data-photo="{{ $d['photo'] }}" data-label="{{ $d['label'] }}" data-area="{{ $d['area'] }}" aria-label="Zobacz ślad {{ $i + 1 }}" aria-pressed="{{ $i === 0 ? 'true' : 'false' }}">{{ $i + 1 }}</button>@endforeach</div></div></div>
    </div>
    <aside class="cc-aside">
     <span class="cc-over">Stan i ślady</span>
     <h3>Tak wyglądają<br>zauważone ślady.</h3>
     <p>Rysy, otarcia i inne zauważone ślady pokazujemy z bliska. Punkt na mapie prowadzi do zdjęcia danego miejsca.</p>
     <div class="cc-point"><x-icon name="map-pin" size="20"/><div><strong>{{ $miejsca }}</strong><span>Wybierz punkt na mapie lub numer pod zdjęciem.</span></div></div>
     <p class="cc-tip">Zdjęcia dokumentują zauważone miejsca. Nie zastępują obejrzenia samochodu z bliska.</p>
     @if(count($tabs) > 1)<button class="cc-next" type="button" data-open-tab="{{ $nextTab('wear') }}">{{ $nextLabel[$nextTab('wear')] }} <x-icon name="arrow-right" size="16"/></button>@endif
    </aside>
   </div>
   @endif

   <div class="cc-report-foot"><p><x-icon name="info" size="15"/>Przykład materiałów {{ $carName }}.</p><a class="text-link" href="{{ $sc['checkUrl'] }}">Otwórz ogłoszenie <x-icon name="arrow-right" size="16"/></a></div>
  </div>
  <p class="cc-example-note">To przykład prezentacji. Dostępność materiałów i aktualne informacje sprawdzisz w ogłoszeniu konkretnego samochodu.</p>
 </div>
</section>
@endif

<section id="proces" class="purchase purchase-early cc-process">
 <div class="container"><div class="eyebrow">KROK PO KROKU</div><h2>Od oględzin do raportu.</h2>
  <div class="steps">
   <article><div class="step-number">01</div><h3>Oglądamy samochód</h3><p>Przyglądamy się nadwoziu i wnętrzu. Zbieramy dostępne informacje.</p></article>
   <article><div class="step-number">02</div><h3>Wykonujemy pomiary</h3><p>Mierzymy grubość powłoki lakierniczej i głębokość bieżnika.</p></article>
   <article><div class="step-number">03</div><h3>Dokumentujemy stan</h3><p>Przygotowujemy zdjęcia, prezentacje 360° i mapę zauważonych śladów.</p></article>
   <article><div class="step-number">04</div><h3>Udostępniamy w ogłoszeniu</h3><p>Dodajemy je do ogłoszenia, żebyś mógł obejrzeć je przed wizytą.</p></article>
  </div>
 </div>
</section>

<section id="zakres" class="cc-scope">
 <div class="container">
  <div class="section-intro service-intro"><div><div class="eyebrow">JASNY ZAKRES</div><h2>Co obejmuje CertiCheck,<br><span style="color:var(--blue)">a czego nie?</span></h2></div><p>Sprawdź, jakie informacje otrzymujesz i co warto dodatkowo zweryfikować przed zakupem.</p></div>
  <div class="cc-scope-grid">
   <article class="cc-scope-col ok">
    <div class="cc-scope-head"><span class="ico"><x-icon name="check" size="20"/></span><div><span class="cc-over">Materiały o samochodzie</span><h3>Co obejmuje</h3></div></div>
    <ul>
     <li><x-icon name="check" size="18"/><div><h4>Pomiary grubości lakieru</h4><p>Odczyty w mikrometrach (µm), przypisane do sprawdzonych elementów nadwozia.</p></div></li>
     <li><x-icon name="check" size="18"/><div><h4>Koła i opony</h4><p>Głębokość bieżnika każdej opony w milimetrach oraz informacje o zamontowanym komplecie i felgach.</p></div></li>
     <li><x-icon name="check" size="18"/><div><h4>Stan wizualny i ślady użytkowania</h4><p>Zdjęcia zauważonych rys, otarć i innych śladów wraz z oznaczeniem ich miejsca na mapie auta.</p></div></li>
     <li><x-icon name="check" size="18"/><div><h4>Prezentacje 360°</h4><p>Widok samochodu dookoła i panorama wnętrza, które możesz samodzielnie obracać.</p></div></li>
    </ul>
    <p class="cc-scope-docs"><x-icon name="file-text" size="16"/><span>Dostępne dokumenty i informacje serwisowe znajdziesz również w ogłoszeniu.</span></p>
   </article>
   <article class="cc-scope-col no">
    <div class="cc-scope-head"><span class="ico"><x-icon name="info" size="20"/></span><div><span class="cc-over">Ograniczenia zakresu</span><h3>Czego nie obejmuje</h3></div></div>
    <ul>
     <li><x-icon name="minus" size="18"/><div><h4>Badania technicznego i pełnej diagnostyki</h4><p>Nie zastępuje badania na stacji kontroli pojazdów ani sprawdzenia samochodu w serwisie.</p></div></li>
     <li><x-icon name="minus" size="18"/><div><h4>Niezależnej ekspertyzy</h4><p>To wewnętrzny standard CertiCars, a nie opinia rzeczoznawcy ani zewnętrzny certyfikat.</p></div></li>
     <li><x-icon name="minus" size="18"/><div><h4>Potwierdzenia bezwypadkowości</h4><p>Pomiary i zdjęcia nie stanowią pełnej dokumentacji historii samochodu.</p></div></li>
     <li><x-icon name="minus" size="18"/><div><h4>Gwarancji braku usterek</h4><p>Oględziny nie obejmują demontażu podzespołów ani nie gwarantują wykrycia wszystkich wad, także ukrytych.</p></div></li>
    </ul>
    <p class="cc-scope-docs"><x-icon name="search" size="16"/><span>Chcesz sprawdzić auto w wybranym serwisie? Ustal z nami szczegóły przed wizytą.</span></p>
   </article>
  </div>
  <div class="cc-scope-bottom"><p><x-icon name="info" size="15"/>Zdjęcia i pomiary pokazują stan z dnia ich wykonania. Dostępność materiałów sprawdzisz w ogłoszeniu konkretnego auta.</p><a class="text-link" href="tel:+48515440623">Ustal oględziny <x-icon name="arrow-right" size="16"/></a></div>
 </div>
</section>

<section id="pytania" class="cc-faq">
 <div class="container cc-faq-layout">
  <div class="human-title"><div class="eyebrow">WARTO WIEDZIEĆ</div><h2>Najczęstsze pytania.</h2><p>O materiałach, pomiarach i sprawdzeniu auta przed zakupem.</p></div>
  <div class="cc-faq-list">
   @foreach($faqs as $i => [$q, $a])
   <details @if($i === 0) open @endif><summary>{{ $q }}<x-icon name="chevron-down" size="18"/></summary><p>{{ $a }}</p></details>
   @endforeach
  </div>
 </div>
</section>

<section id="kontakt" class="contact-section cc-contact">
 <div class="wide contact-panel"><div><div class="eyebrow light">POROZMAWIAJMY O KONKRETNYM AUCIE</div><h2>Masz już auto na oku?</h2><p>Masz pytanie o pomiar, zdjęcie lub stan samochodu? Zadzwoń, omówimy szczegóły i ustalimy oględziny.</p></div><div class="contact-actions"><a class="button primary" href="{{ route('catalog') }}">Sprawdź dostępne auta <x-icon name="arrow-right" size="16"/></a><a class="text-link light-link" href="tel:+48515440623"><x-icon name="phone" size="16"/>515 440 623</a><span class="cc-place"><x-icon name="map-pin" size="15"/>Lipnik k. Stargardu</span></div></div>
</section>
</div>
@endsection

@push('scripts')
<script>
/* CertiCheck: zakładki raportu, ślady i prezentacje 360° (ten sam odtwarzacz co na „O nas”).
   360°: auto obraca się samo, gdy zakładka jest widoczna; przeciągnięcie albo strzałki
   zatrzymują obrót, po ok. 2,5 s wraca. Przy prefers-reduced-motion bez samoczynnego obrotu. */
(function(){
 'use strict';
 var root = document.querySelector('.onas');
 if(!root) return;
 var REV_MS = 9000, PANO_SPEED = -6, RESUME_MS = 2500;
 var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

 /* ---------- Ślady ---------- */
 var markers = [].slice.call(root.querySelectorAll('[data-damage]'));
 var photo = root.querySelector('[data-damage-photo]');
 markers.forEach(function(marker){
  marker.addEventListener('click', function(){
   var n = marker.getAttribute('data-damage');
   markers.forEach(function(m){ m.setAttribute('aria-pressed', String(m.getAttribute('data-damage') === n)); });
   var area = marker.getAttribute('data-area');
   photo.src = marker.getAttribute('data-photo');
   photo.alt = 'Ślad ' + n + ': ' + marker.getAttribute('data-label').toLowerCase() + (area ? ', ' + area : '');
   root.querySelector('[data-damage-label]').textContent = marker.getAttribute('data-label');
   root.querySelector('[data-damage-counter]').textContent = n + ' / ' + root.querySelectorAll('.wear-marker').length;
  });
 });

 /* ---------- Odtwarzacze 360° ---------- */
 function FramePlayer(panel){
  var sp = null, urls = [];
  try { sp = JSON.parse(panel.getAttribute('data-sprites') || 'null'); } catch(_){}
  try { urls = JSON.parse(panel.getAttribute('data-frames') || '[]'); } catch(_){}
  var n = sp ? sp.n : urls.length;
  var canvas = panel.querySelector('canvas'), ctx = canvas.getContext('2d');
  var poster = panel.querySelector('.viewer-poster');
  var cache = {}, current = 0, shownHi = false, started = false;
  function img(url){
   var im = cache[url];
   if(!im){ im = new Image(); im.decoding = 'async'; im.addEventListener('load', function(){ if(!shownHi) draw(); }); im.src = url; cache[url] = im; }
   return im;
  }
  function ready(im){ return !!(im && im.complete && im.naturalWidth > 0); }
  function load(){ if(started) return; started = true; if(sp){ img(sp.preview); sp.sheets.forEach(img); } else urls.forEach(img); }
  function frameSource(i){
   if(!started) return null;
   if(sp){
    var sheet = cache[sp.sheets[Math.floor(i / sp.per)]];
    if(ready(sheet)) return [sheet, 0, (i % sp.per) * sp.h, sp.w, sp.h, true];
    var pv = cache[sp.preview];
    if(ready(pv)) return [pv, (i % sp.pcols) * sp.pw, Math.floor(i / sp.pcols) * sp.ph, sp.pw, sp.ph, false];
    return null;
   }
   var im = cache[urls[i]];
   return ready(im) ? [im, 0, 0, im.naturalWidth, im.naturalHeight, true] : null;
  }
  function draw(){
   var r = panel.getBoundingClientRect();
   if(!r.width) return false;
   var dpr = Math.min(2, window.devicePixelRatio || 1);
   var cw = Math.round(r.width * dpr), ch = Math.round(r.height * dpr);
   if(canvas.width !== cw || canvas.height !== ch){ canvas.width = cw; canvas.height = ch; }
   var f = frameSource(current);
   if(!f) return false;
   var s = Math.max(cw / f[3], ch / f[4]), dw = f[3] * s, dh = f[4] * s;
   ctx.imageSmoothingEnabled = true; ctx.imageSmoothingQuality = 'high';
   ctx.drawImage(f[0], f[1], f[2], f[3], f[4], (cw - dw) / 2, (ch - dh) / 2, dw, dh);
   shownHi = f[5]; canvas.dataset.frame = current + 1;
   if(poster) poster.hidden = true;
   return true;
  }
  function set(i){ current = ((Math.round(i) % n) + n) % n; draw(); }
  return { type: 'frames', n: n, load: load, draw: draw, set: set,
   get frame(){ return current; },
   canShow: function(i){ return !!frameSource(((i % n) + n) % n); } };
 }

 function PanoPlayer(panel){
  var host = panel.querySelector('.pano-host'), status = panel.querySelector('.pano-loading');
  var viewer = null, loaded = false, started = false, want = false, running = false;
  function fail(){ if(status){ status.hidden = false; status.textContent = 'Nie udało się wczytać panoramy wnętrza.'; } }
  function init(){
   viewer = pannellum.viewer(host, { type: 'equirectangular', panorama: host.getAttribute('data-pano-src'), autoLoad: true,
    showControls: false, mouseZoom: false, keyboardZoom: false, doubleClickZoom: false,
    compass: false, hfov: 100, minHfov: 100, maxHfov: 100, pitch: -15, yaw: 0 });
   viewer.on('load', function(){ loaded = true; if(status) status.hidden = true; apply(); });
   viewer.on('error', fail);
  }
  function load(){
   if(started) return;
   started = true;
   if(!document.querySelector('link[data-pannellum]')){
    var css = document.createElement('link');
    css.rel = 'stylesheet'; css.href = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css';
    css.dataset.pannellum = '1'; document.head.appendChild(css);
   }
   if(window.pannellum){ init(); return; }
   var s = document.createElement('script');
   s.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js'; s.onload = init; s.onerror = fail;
   document.body.appendChild(s);
  }
  function apply(){
   if(!viewer || !loaded) return;
   if(want && !running){ viewer.startAutoRotate(PANO_SPEED); running = true; }
   else if(!want && running){ viewer.stopAutoRotate(); running = false; }
  }
  return { type: 'pano', load: load, draw: function(){},
   run: function(on){ want = on; apply(); },
   turn: function(dir){ if(viewer && loaded) viewer.setYaw(viewer.getYaw() + dir * 20, 500); },
   stopNow: function(){ if(viewer && loaded){ viewer.stopAutoRotate(); running = false; } } };
 }

 // Każda scena 360° ma jeden odtwarzacz; obraca się tylko scena z otwartej zakładki, gdy jest na ekranie.
 var stages = [].slice.call(root.querySelectorAll('[data-cc-stage]')).map(function(stage){
  var media = stage.querySelector('.cc-media'), kind = media.getAttribute('data-kind');
  var player = kind === 'frames' ? FramePlayer(media) : kind === 'pano' ? PanoPlayer(media) : null;
  return { el: stage, panel: stage.closest('[role=tabpanel]'), player: player, visible: false, held: false, timer: null };
 });
 function shown(s){ return !s.panel.hidden; }

 var raf = null, last = 0, acc = 0;
 function tick(ts){
  if(!raf) return;
  if(!last) last = ts;
  acc += ts - last; last = ts;
  stages.forEach(function(s){
   var p = s.player;
   if(!p || p.type !== 'frames' || !shown(s) || !s.visible || s.held) return;
   var step = REV_MS / p.n;
   if(acc > 1000) acc = step;
   while(acc >= step){ acc -= step; if(p.canShow(p.frame + 1)) p.set(p.frame + 1); else { acc = 0; break; } }
  });
  raf = requestAnimationFrame(tick);
 }
 function sync(){
  var anyFrames = false;
  stages.forEach(function(s){
   var run = shown(s) && s.visible && !s.held && !reduce && !document.hidden;
   if(!s.player) return;
   if(s.player.type === 'pano') s.player.run(run);
   else if(run) anyFrames = true;
  });
  if(anyFrames && !raf){ last = 0; acc = 0; raf = requestAnimationFrame(tick); }
  else if(!anyFrames && raf){ cancelAnimationFrame(raf); raf = null; }
 }
 function hold(s){ s.held = true; clearTimeout(s.timer); if(s.player && s.player.type === 'pano') s.player.stopNow(); sync(); }
 function release(s){ clearTimeout(s.timer); s.timer = setTimeout(function(){ s.held = false; sync(); }, RESUME_MS); }
 function rotate(s, dir){
  var p = s.player; if(!p) return;
  hold(s);
  if(p.type === 'frames') p.set(p.frame + dir * 2); else p.turn(dir);
  release(s);
 }

 stages.forEach(function(s){
  var p = s.player, stage = s.el;
  if(!p){ stage.classList.add('is-static'); return; }
  [].forEach.call(stage.querySelectorAll('[data-rotate]'), function(b){
   b.addEventListener('click', function(){ rotate(s, Number(b.getAttribute('data-rotate'))); });
  });
  stage.tabIndex = 0;
  stage.addEventListener('keydown', function(e){
   if(e.target !== stage) return;
   if(e.key === 'ArrowLeft' || e.key === 'ArrowRight'){ e.preventDefault(); rotate(s, e.key === 'ArrowLeft' ? -1 : 1); }
  });
  var drag = null;
  stage.addEventListener('pointerdown', function(e){
   if(e.target.closest('button') || e.button !== 0) return;
   hold(s);
   if(p.type === 'frames'){ drag = { x: e.clientX, frame: p.frame }; stage.setPointerCapture(e.pointerId); stage.classList.add('dragging'); }
   else drag = { pano: true };
  });
  stage.addEventListener('pointermove', function(e){ if(drag && !drag.pano) p.set(drag.frame - (e.clientX - drag.x) / 7); });
  function stopDrag(){ if(!drag) return; drag = null; stage.classList.remove('dragging'); release(s); }
  stage.addEventListener('pointerup', stopDrag);
  stage.addEventListener('pointercancel', stopDrag);
  stage.addEventListener('lostpointercapture', stopDrag);
  window.addEventListener('pointerup', stopDrag);
  if('IntersectionObserver' in window){
   new IntersectionObserver(function(en){ if(en[en.length - 1].isIntersecting && shown(s)) p.load(); }, { rootMargin: '400px 0px' }).observe(stage);
   new IntersectionObserver(function(en){ s.visible = en[en.length - 1].isIntersecting; sync(); }, { threshold: 0.2 }).observe(stage);
  } else { s.visible = true; if(shown(s)) p.load(); }
  if(window.ResizeObserver) new ResizeObserver(function(){ p.draw(); }).observe(stage);
 });
 document.addEventListener('visibilitychange', sync);

 /* ---------- Zakładki raportu ---------- */
 var tabs = [].slice.call(root.querySelectorAll('.cc-tabs [role=tab]'));
 function activate(button, focus){
  tabs.forEach(function(b){
   var on = b === button, panel = document.getElementById(b.getAttribute('aria-controls'));
   b.setAttribute('aria-selected', String(on)); b.tabIndex = on ? 0 : -1;
   panel.hidden = !on; panel.inert = !on;
  });
  if(focus) button.focus();
  button.scrollIntoView({ block: 'nearest', inline: 'nearest' });
  stages.forEach(function(s){ if(shown(s) && s.player){ s.player.load(); s.player.draw(); } });
  sync();
 }
 tabs.forEach(function(button, index){
  button.tabIndex = button.getAttribute('aria-selected') === 'true' ? 0 : -1;
  document.getElementById(button.getAttribute('aria-controls')).inert = button.tabIndex !== 0;
  button.addEventListener('click', function(){ activate(button); });
  button.addEventListener('keydown', function(e){
   var next;
   if(e.key === 'ArrowRight') next = (index + 1) % tabs.length;
   else if(e.key === 'ArrowLeft') next = (index + tabs.length - 1) % tabs.length;
   else if(e.key === 'Home') next = 0;
   else if(e.key === 'End') next = tabs.length - 1;
   else return;
   e.preventDefault(); activate(tabs[next], true);
  });
 });
 // „Dalej” w panelu i karty „Co znajdziesz” otwierają wskazaną zakładkę raportu.
 [].forEach.call(root.querySelectorAll('[data-open-tab]'), function(el){
  var b = document.getElementById('cc-tab-' + el.getAttribute('data-open-tab'));
  if(!b) return;
  el.addEventListener('click', function(e){
   activate(b, el.classList.contains('cc-next'));
   if(!el.classList.contains('cc-next')){ e.preventDefault(); document.getElementById('przyklad').scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' }); }
  });
 });
})();
</script>
@endpush
