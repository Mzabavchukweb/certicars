@extends('layouts.public')
@section('meta_title_full','O nas: komis samochodowy pod Stargardem | CertiCars')
@section('title','O nas')
@section('description','CertiCars z Lipnika koło Stargardu: auta osobowe i dostawcze. Pomagamy w kredycie i leasingu, rejestracji, ubezpieczeniu, transporcie i zakupie na odległość.')
@section('og_title','O nas: komis samochodowy pod Stargardem | CertiCars')
@section('og_description','CertiCars z Lipnika koło Stargardu: auta osobowe i dostawcze. Pomagamy w kredycie i leasingu, rejestracji, ubezpieczeniu, transporcie i zakupie na odległość.')


@section('styles')
@include('partials.onas-styl')
@endsection

@section('content')
@php
    $sc       = $showcase;
    $hasOut   = $sc && ($sc['exterior'] || $sc['poster']);
    $hasIn    = $sc && $sc['interior'];
    $outSpins = $sc && $sc['exterior'];
    $check    = $sc['check'] ?? null;
    $checkTabs = [];
    if ($check) {
        if ($check['paint']['items']) $checkTabs[] = 'paint';
        if ($check['tires'])          $checkTabs[] = 'tires';
        if ($check['damages']['items']) $checkTabs[] = 'wear';
    }
@endphp
<div class="onas">

<section id="01-hero" class="hero">
 <div class="container hero-inner">
  <div class="hero-copy">
   <div class="eyebrow light">O NAS · CERTICARS</div>
   <h1>Poznaj auto wcześniej.<br><span>Kupuj spokojniej.</span></h1>
   <p>Pokazujemy dostępne informacje o autach<br>
i pomagamy przejść przez zakup — od pierwszych<br>
pytań po formalności i odbiór.</p>
   <div class="actions"><a class="button primary" href="{{ route('catalog') }}">Sprawdź ofertę <x-icon name="arrow-right" size="16"/></a><a href="tel:+48515440623" class="button secondary">Porozmawiajmy</a></div>
   <div class="hero-location"><x-icon name="map-pin" size="14"/><span>Lipnik k. Stargardu</span><span class="dot"></span><span>Auta osobowe i dostawcze</span></div>
  </div>
  <div class="hero-art"><div class="hero-glow"></div><img src="/img/about/doradca-tablet.webp" width="800" height="1422" alt="Doradca CertiCars z tabletem" fetchpriority="high" decoding="async"></div>
 </div>
 <svg class="hero-curve" viewBox="0 0 1440 58" preserveAspectRatio="none" aria-hidden="true"><path d="M0 8C390 58 1050 58 1440 8V58H0Z" fill="white"/></svg>
</section>

<section id="02-podejscie" class="approach human-section">
 <div class="container human-layout">
  <div class="human-title"><div class="eyebrow">PO LUDZKU O SAMOCHODACH</div><h2 class="brand-motto">Najpierw Ty.<br><span>Potem samochód.</span></h2><p>Chcemy pomóc Ci wybrać<br>
auto, które pasuje do Ciebie.</p></div>
  <div class="human-story"><p>Chcemy poznać Ciebie, Twój budżet i potrzeby. Szukasz auta do pracy, dla rodziny czy na codzienne dojazdy? <strong>Od tego zaczynamy rozmowę.</strong></p><p>Nie namawiamy do auta na siłę. Przyjedź, obejrzyj je z bliska, usiądź za kierownicą i przejedź się. Masz czas na pytania i własną ocenę. <strong>To Ty decydujesz, czy ten samochód jest dla Ciebie.</strong></p></div>
 </div>
</section>

<section id="03-zakup" class="purchase purchase-early">
 <div class="container"><div class="eyebrow">KROK PO KROKU</div><h2>Jak wygląda zakup?</h2>
  <div class="steps">
   <article><div class="step-number">01</div><h3>Wybierz auto</h3><p>Sprawdź ofertę i zapytaj<br>
o szczegóły.</p></article>
   <article><div class="step-number">02</div><h3>Poznaj je bliżej</h3><p>Oględziny, jazda próbna<br>
lub prezentacja wideo.</p></article>
   <article><div class="step-number">03</div><h3>Ustal warunki</h3><p>Cena, dokumenty<br>
i wybrane usługi.</p></article>
   <article><div class="step-number">04</div><h3>Odbierz samochód</h3><p>Na miejscu lub<br>
z uzgodnionym transportem.</p></article>
  </div>
 </div>
</section>

@if($hasOut || $hasIn)
<section id="04-prezentacje" class="explore-section">
 <div class="container explore-layout">
  <div class="real-viewer">
   @if($hasOut && $hasIn)
   <div class="viewer-tabs" role="tablist" aria-label="Widok samochodu">
    <button id="view-outside" type="button" role="tab" aria-selected="true" aria-controls="outside-panel"><x-icon name="car" size="16"/>{{ $outSpins ? '360° z zewnątrz' : 'Z zewnątrz' }}</button>
    <button id="view-inside" type="button" role="tab" aria-selected="false" aria-controls="inside-panel"><x-icon name="armchair" size="16"/>360° wnętrza</button>
   </div>
   @endif
   <div class="viewer-stage" aria-label="Interaktywna prezentacja {{ $sc['name'] }}" data-onas-stage>
    @if($hasOut)
    <div id="outside-panel" role="tabpanel" @if($hasIn) aria-labelledby="view-outside" @endif
         data-kind="{{ $outSpins ? 'frames' : 'static' }}"
         @if($outSpins) data-frames='@json($sc['exterior']['urls'])' data-sprites='@json($sc['exterior']['sprites'])' @endif>
     @if($sc['poster'])<img class="viewer-poster" src="{{ $sc['poster'] }}" alt="{{ $sc['name'] }}, zdjęcie z ogłoszenia" draggable="false" loading="lazy" decoding="async">@endif
     @if($outSpins)<canvas role="img" aria-label="{{ $sc['name'] }}: widok 360° nadwozia z ogłoszenia"></canvas>@endif
    </div>
    @endif
    @if($hasIn)
    <div id="inside-panel" role="tabpanel" @if($hasOut) aria-labelledby="view-inside" hidden @endif
         data-kind="{{ $sc['interior']['type'] }}"
         @if($sc['interior']['type'] === 'frames') data-frames='@json($sc['interior']['urls'])' data-sprites='@json($sc['interior']['sprites'])' @endif>
     @if($sc['interior']['type'] === 'frames')
      <canvas role="img" aria-label="{{ $sc['name'] }}: widok 360° wnętrza z ogłoszenia"></canvas>
     @else
      <div class="pano-host" data-pano-src="{{ $sc['interior']['src'] }}" role="img" aria-label="Panorama 360 stopni wnętrza {{ $sc['name'] }}"></div><span class="pano-loading" role="status">Wczytuję wnętrze…</span>
     @endif
    </div>
    @endif
    <span class="viewer-badge">360°</span>
    <div class="viewer-controls"><button type="button" data-rotate="-1" aria-label="Obróć widok w lewo">←</button><span data-viewer-hint>Przeciągnij, aby obrócić auto</span><button type="button" data-rotate="1" aria-label="Obróć widok w prawo">→</button></div>
   </div>
   <div class="viewer-caption"><span>Rzeczywiste zdjęcia z ogłoszenia</span><span>{{ $sc['name'] }}</span></div>
  </div>
  <div class="explore-copy"><div class="eyebrow">PREZENTACJE 360°</div><h2>Obejrzyj auto dookoła.<br>Zajrzyj do środka.</h2><p>Na CertiCars możesz oglądać samochody w 360° — z zewnątrz i we wnętrzu. Przyjrzyj się nadwoziu, rozejrzyj po kabinie i poznaj więcej szczegółów jeszcze przed wizytą.</p><a class="text-link" href="{{ route('catalog') }}">Zobacz auta w ofercie <x-icon name="arrow-right" size="16"/></a></div>
 </div>
</section>
@endif

<section id="05-certicheck" class="certi-section certi-v2">
 <div class="wide certi-panel">
  <div class="certi-main">
   <div class="certi-copy"><div class="eyebrow">CERTICHECK · DODATKOWE MATERIAŁY</div><h2>Więcej niż opis<br>i zdjęcia.</h2><p>Przy wybranych samochodach do ogłoszenia dokładamy pomiary i materiały pokazujące stan auta.</p>
    <div class="certi-features">
     <div><x-icon name="scan-line" size="21"/><div><h3>Pomiary lakieru</h3><p>Grubość powłoki w wybranych punktach.</p></div></div>
     <div><x-icon name="tabler:wheel" size="21"/><div><h3>Pomiary opon</h3><p>Zmierzona głębokość bieżnika.</p></div></div>
     <div><x-icon name="camera" size="21"/><div><h3>Stan wizualny</h3><p>Ujęcia nadwozia, wnętrza i detali.</p></div></div>
     <div><x-icon name="map-pin" size="21"/><div><h3>Ślady eksploatacji</h3><p>Zauważone ślady zaznaczone na mapie auta.</p></div></div>
    </div>
    <a class="text-link" href="{{ route('certicheck.landing') }}">Poznaj CertiCheck <x-icon name="arrow-right" size="16"/></a>
   </div>
   @if($checkTabs)
   <div class="check-viewer">
    <div class="check-header"><strong>Zajrzyj do CertiCheck</strong><span>PRZYKŁADOWE AUTO · {{ $sc['name'] }}</span></div>
    <div class="check-tabs" role="tablist" aria-label="Materiały CertiCheck">
     @if(in_array('paint', $checkTabs))<button id="check-paint" type="button" role="tab" aria-selected="{{ $checkTabs[0] === 'paint' ? 'true' : 'false' }}" aria-controls="paint-panel"><x-icon name="scan-line" size="16"/>Lakier</button>@endif
     @if(in_array('tires', $checkTabs))<button id="check-tires" type="button" role="tab" aria-selected="{{ $checkTabs[0] === 'tires' ? 'true' : 'false' }}" aria-controls="tires-panel"><x-icon name="tabler:wheel" size="16"/>Opony</button>@endif
     @if(in_array('wear', $checkTabs))<button id="check-wear" type="button" role="tab" aria-selected="{{ $checkTabs[0] === 'wear' ? 'true' : 'false' }}" aria-controls="wear-panel"><x-icon name="map-pin" size="16"/>Stan i ślady</button>@endif
    </div>
    <div class="check-panels">
     @if(in_array('paint', $checkTabs))
     <div id="paint-panel" role="tabpanel" aria-labelledby="check-paint" @if($checkTabs[0] !== 'paint') hidden @endif>
      <h3>Pomiary grubości lakieru</h3><p>Wyniki pomiarów w wybranych punktach nadwozia.</p>
      <div class="paint-grid">@foreach($check['paint']['items'] as $r)<div class="paint-reading"><span>{{ $r['label'] }}</span><strong>{{ $r['value'] }} <small>μm</small></strong></div>@endforeach</div><p class="reading-note">Odczyty dotyczą prezentowanego samochodu.@if($check['paint']['total'] > count($check['paint']['items'])) Wszystkie pomiary ({{ $check['paint']['total'] }}) są w raporcie CertiCheck.@endif</p>
     </div>
     @endif
     @if(in_array('tires', $checkTabs))
     <div id="tires-panel" role="tabpanel" aria-labelledby="check-tires" @if($checkTabs[0] !== 'tires') hidden @endif>
      <h3>Koła i opony</h3><p>{{ $check['tires']['mounted'] ? 'Zamontowany komplet' : 'Komplet opon' }} · głębokość bieżnika.</p>
      <div class="tires-grid">@foreach($check['tires']['rows'] as $t)<div class="tire-reading"><div><strong>{{ $t['label'] }}</strong>@if($t['type'])<span>{{ $t['type'] }}</span>@endif</div><b @class([$t['status'] => $t['status'] !== 'ok'])>{{ $t['depth'] }} <small>mm</small></b></div>@endforeach</div>
      <div class="wheel-info">@if($check['tires']['rim'])<span><x-icon name="armchair" size="16"/>{{ $check['tires']['rim'] }}</span>@else<span></span>@endif
       @if($check['tires']['anyIssue'])<strong class="warn"><x-icon name="info" size="15"/>Wymaga uwagi</strong>@else<strong><x-icon name="check" size="15"/>Stan bardzo dobry</strong>@endif</div>
     </div>
     @endif
     @if(in_array('wear', $checkTabs))
     @php $dmg = $check['damages']['items']; $dmgN = count($dmg); @endphp
     <div id="wear-panel" role="tabpanel" aria-labelledby="check-wear" @if($checkTabs[0] !== 'wear') hidden @endif>
      <h3>Stan wizualny i ślady eksploatacji</h3><p>Wybierz punkt, aby zobaczyć zdjęcie z tego miejsca.</p>
      <div class="wear-layout"><div class="wear-map-wrap"><div class="wear-map"><img src="{{ $check['damages']['map'] }}" alt="Poglądowa mapa nadwozia z oznaczonymi śladami ({{ $dmgN }})" loading="lazy" decoding="async">@foreach($dmg as $i => $d)<button type="button" class="wear-marker" data-damage="{{ $i + 1 }}" data-photo="{{ $d['photo'] }}" data-label="{{ $d['label'] }}" data-area="{{ $d['area'] }}" aria-pressed="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Ślad {{ $i + 1 }} — zobacz zdjęcie" style="left:{{ $d['x'] }}%;top:{{ $d['y'] }}%">{{ $i + 1 }}</button>@endforeach</div><span>Poglądowa mapa nadwozia</span></div><div class="wear-photo"><img data-damage-photo src="{{ $dmg[0]['photo'] }}" alt="Ślad 1: {{ mb_strtolower($dmg[0]['label']) }}@if($dmg[0]['area']), {{ $dmg[0]['area'] }}@endif" loading="lazy" decoding="async"><div class="wear-caption"><span data-damage-label>{{ $dmg[0]['label'] }}</span><strong data-damage-counter aria-live="polite">1 / {{ $dmgN }}</strong></div><div class="wear-selectors" aria-label="Wybierz zdjęcie śladu">@foreach($dmg as $i => $d)<button type="button" data-damage="{{ $i + 1 }}" data-photo="{{ $d['photo'] }}" data-label="{{ $d['label'] }}" data-area="{{ $d['area'] }}" aria-label="Zobacz ślad {{ $i + 1 }}" aria-pressed="{{ $i === 0 ? 'true' : 'false' }}">{{ $i + 1 }}</button>@endforeach</div></div></div>
     </div>
     @endif
    </div>
    <a class="check-footer" href="{{ $sc['checkUrl'] }}">Zobacz materiały w ogłoszeniu <x-icon name="arrow-right" size="16"/></a>
   </div>
   @endif
  </div>
  <div class="scope-note"><x-icon name="info" size="15"/><p>CertiCheck jest dostępny przy wybranych autach. Zakres materiałów zależy od samochodu i nie zastępuje jego oględzin.</p></div>
 </div>
</section>

<section id="06-uslugi" class="services">
 <div class="container">
  <div class="section-intro service-intro"><div><div class="eyebrow">NASZE USŁUGI</div><h2>Wsparcie przy zakupie<br>i po odbiorze auta.</h2></div><p>Zakres pomocy dobieramy<br>
do Twoich potrzeb.</p></div>
  <div class="service-grid">
   <article><div class="icon"><x-icon name="wallet" size="24"/></div><div><h3>Kredyt i leasing</h3><p>Pomagamy zorganizować<br>
finansowanie zakupu.</p></div></article>
   <article><div class="icon"><x-icon name="file-text" size="24"/></div><div><h3>Rejestracja auta</h3><p>Możemy zająć się formalnościami<br>
związanymi z rejestracją.</p></div></article>
   <article><div class="icon"><x-icon name="shield-check" size="24"/></div><div><h3>Ubezpieczenie OC / AC</h3><p>Pomagamy dobrać polisę<br>
do samochodu.</p></div></article>
   <article><div class="icon"><x-icon name="truck" size="24"/></div><div><h3>Transport pod dom</h3><p>Ustalamy adres, termin<br>
i koszt dowozu samochodu.</p></div></article>
   <article><div class="icon"><x-icon name="wrench" size="24"/></div><div><h3>Pierwszy serwis</h3><p>Organizujemy uzgodniony<br>
serwis eksploatacyjny.</p></div></article>
   <article><div class="icon"><x-icon name="shield-check" size="24"/></div><div><h3>Gwarancja GetHelp</h3><p>Zapytaj o dostępne pakiety<br>
i warunki ochrony.</p></div></article>
  </div>
  <p class="services-note">Zapytaj również o wymianę opon i usuwanie wgnieceń. Zakres, dostępność i koszt usług ustalamy indywidualnie.</p>
 </div>
</section>

<section id="07-odleglosc" class="remote-section remote-v2">
 <div class="wide remote-panel">
  <div class="remote-copy"><div class="eyebrow">ZAKUP NA ODLEGŁOŚĆ</div><h2>Mieszkasz dalej?<br>Poznaj auto na odległość.</h2><p>Umów prezentację wideo, poproś o dodatkowe ujęcia i omów szczegóły auta. Jeśli zdecydujesz się na zakup, ustalimy formalności oraz odbiór lub transport.</p><a class="text-link" href="tel:+48515440623">Zapytaj o zakup na odległość <x-icon name="arrow-right" size="16"/></a></div>
  <div class="remote-visual">
   <div class="laptop">
    <div class="laptop-screen">
     <div class="call-top"><div><x-icon name="video" size="11"/><span>Prezentacja samochodu</span></div><span class="call-indicator"></span></div>
     <div class="call-main">
      <img class="car-render" src="/img/about/prezentacja-auto.webp" width="640" height="427" alt="Poglądowa ilustracja samochodu na ekranie prezentacji" loading="lazy" decoding="async">
      <div class="call-thumb"><x-icon name="video" size="11"/></div>
     </div>
     <div class="call-controls"><span><x-icon name="mic" size="11"/></span><span class="hangup"><x-icon name="phone" size="11"/></span><span><x-icon name="video" size="11"/></span></div>
    </div>
    <div class="laptop-base"><i></i></div>
   </div>
  </div>
  <div class="remote-list"><div><x-icon name="video" size="17"/><span>Prezentacja wideo</span></div><div><x-icon name="camera" size="17"/><span>Dodatkowe zdjęcia</span></div><div><x-icon name="truck" size="17"/><span>Ustalenie odbioru</span></div></div>
 </div>
</section>

<section id="08-kontakt" class="contact-section">
 <div class="wide contact-panel"><div><h2>Porozmawiajmy<br>o Twoim kolejnym aucie.</h2><p>Powiedz, czego szukasz i jaki masz budżet.<br>
Pomożemy sprawdzić dostępne możliwości.</p></div><div class="contact-actions"><a href="tel:+48515440623" class="button primary"><x-icon name="phone" size="16"/>Zadzwoń 515 440 623</a><a class="text-link light-link" href="{{ route('catalog') }}">Zobacz dostępne auta <x-icon name="arrow-right" size="16"/></a></div></div>
</section>
</div>
@endsection

@push('scripts')
<script>
/* O nas: zakładki, ślady CertiCheck i prezentacja 360°.
   360°: auto obraca się samo w spokojnym tempie, gdy sekcja jest widoczna.
   Przeciągnięcie albo strzałki zatrzymują obrót, po ok. 2,5 s wraca.
   Przy prefers-reduced-motion nie ma samoczynnego obrotu. */
(function(){
 'use strict';
 var root = document.querySelector('.onas');
 if(!root) return;
 var $ = function(sel){ return root.querySelector(sel); };

 function tabs(group, callback){
  if(!group) return;
  var buttons = [].slice.call(group.querySelectorAll('[role=tab]'));
  function activate(button){
   buttons.forEach(function(b){
    var on = b === button;
    var panel = document.getElementById(b.getAttribute('aria-controls'));
    b.setAttribute('aria-selected', String(on));
    b.tabIndex = on ? 0 : -1;
    panel.hidden = !on;
    panel.inert = !on;
   });
   if(callback) callback(button);
  }
  buttons.forEach(function(button, index){
   button.tabIndex = button.getAttribute('aria-selected') === 'true' ? 0 : -1;
   document.getElementById(button.getAttribute('aria-controls')).inert = button.tabIndex !== 0;
   button.addEventListener('click', function(){ activate(button); });
   button.addEventListener('keydown', function(e){
    var next;
    if(e.key === 'ArrowRight') next = (index + 1) % buttons.length;
    else if(e.key === 'ArrowLeft') next = (index + buttons.length - 1) % buttons.length;
    else if(e.key === 'Home') next = 0;
    else if(e.key === 'End') next = buttons.length - 1;
    else return;
    e.preventDefault(); buttons[next].focus(); activate(buttons[next]);
   });
  });
 }

 /* ---------- CertiCheck: zakładki i ślady ---------- */
 tabs($('.check-tabs'));
 var markers = [].slice.call(root.querySelectorAll('[data-damage]'));
 var photo = $('[data-damage-photo]');
 markers.forEach(function(marker){
  marker.addEventListener('click', function(){
   var n = marker.getAttribute('data-damage');
   markers.forEach(function(m){ m.setAttribute('aria-pressed', String(m.getAttribute('data-damage') === n)); });
   var area = marker.getAttribute('data-area');
   photo.src = marker.getAttribute('data-photo');
   photo.alt = 'Ślad ' + n + ': ' + marker.getAttribute('data-label').toLowerCase() + (area ? ', ' + area : '');
   $('[data-damage-label]').textContent = marker.getAttribute('data-label');
   $('[data-damage-counter]').textContent = n + ' / ' + root.querySelectorAll('.wear-marker').length;
  });
 });

 /* ---------- Prezentacja 360° ---------- */
 var stage = $('[data-onas-stage]');
 if(!stage) return;
 var REV_MS = 9000;          // pełny obrót auta z zewnątrz: ok. 9 s
 var PANO_SPEED = -6;        // wnętrze: stopnie na sekundę (pełny obrót ok. 60 s)
 var RESUME_MS = 2500;       // po puszczeniu obrót wraca po 2,5 s
 var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
 var hint = $('[data-viewer-hint]');

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
   if(!im){
    im = new Image();
    im.decoding = 'async';
    im.addEventListener('load', function(){ if(!shownHi) draw(); });
    im.src = url;
    cache[url] = im;
   }
   return im;
  }
  function ready(im){ return !!(im && im.complete && im.naturalWidth > 0); }
  function load(){
   if(started) return;
   started = true;
   if(sp){ img(sp.preview); sp.sheets.forEach(img); }
   else urls.forEach(img);
  }
  // [obraz, sx, sy, sw, sh, pełna jakość]
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
   var sc = Math.max(cw / f[3], ch / f[4]), dw = f[3] * sc, dh = f[4] * sc;
   ctx.imageSmoothingEnabled = true;
   ctx.imageSmoothingQuality = 'high';
   ctx.drawImage(f[0], f[1], f[2], f[3], f[4], (cw - dw) / 2, (ch - dh) / 2, dw, dh);
   shownHi = f[5];
   canvas.dataset.frame = current + 1;
   if(poster) poster.hidden = true;
   return true;
  }
  function set(i){ current = ((Math.round(i) % n) + n) % n; draw(); }
  return {
   type: 'frames', n: n, load: load, draw: draw, set: set,
   get frame(){ return current; },
   canShow: function(i){ return !!frameSource(((i % n) + n) % n); }
  };
 }

 function PanoPlayer(panel){
  var host = panel.querySelector('.pano-host'), status = panel.querySelector('.pano-loading');
  var viewer = null, loaded = false, started = false, want = false, running = false;
  function fail(){ if(status){ status.hidden = false; status.textContent = 'Nie udało się wczytać panoramy wnętrza.'; } }
  function init(){
   viewer = pannellum.viewer(host, {
    type: 'equirectangular', panorama: host.getAttribute('data-pano-src'), autoLoad: true,
    showControls: false, mouseZoom: false, keyboardZoom: false, doubleClickZoom: false,
    compass: false, hfov: 100, minHfov: 100, maxHfov: 100, pitch: -15, yaw: 0
   });
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
   s.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
   s.onload = init; s.onerror = fail;
   document.body.appendChild(s);
  }
  function apply(){
   if(!viewer || !loaded) return;
   if(want && !running){ viewer.startAutoRotate(PANO_SPEED); running = true; }
   else if(!want && running){ viewer.stopAutoRotate(); running = false; }
  }
  return {
   type: 'pano', load: load, draw: function(){},
   run: function(on){ want = on; apply(); },
   turn: function(dir){ if(viewer && loaded) viewer.setYaw(viewer.getYaw() + dir * 20, 500); },
   stopNow: function(){ if(viewer && loaded){ viewer.stopAutoRotate(); running = false; } }
  };
 }

 var players = {}, panels = {};
 ['outside', 'inside'].forEach(function(key){
  var panel = document.getElementById(key + '-panel');
  if(!panel) return;
  panels[key] = panel;
  var kind = panel.getAttribute('data-kind');
  if(kind === 'frames') players[key] = FramePlayer(panel);
  else if(kind === 'pano') players[key] = PanoPlayer(panel);
 });
 var view = panels.outside ? 'outside' : 'inside';
 var visible = false, near = false, held = false, resumeTimer = null;

 function active(){ return players[view] || null; }
 function updateChrome(){
  var p = active();
  stage.classList.toggle('is-static', !p);
  if(hint) hint.textContent = view === 'inside' ? 'Przeciągnij, aby się rozejrzeć' : 'Przeciągnij, aby obrócić auto';
 }

 // Pętla obrotu dla klatek (zewnątrz albo wnętrze z klatek).
 var raf = null, last = 0, acc = 0;
 function tick(ts){
  if(!raf) return;
  var p = active();
  if(!last) last = ts;
  acc += ts - last; last = ts;
  if(p && p.type === 'frames'){
   var step = REV_MS / p.n;
   if(acc > 1000) acc = step; // po uśpieniu karty nie nadrabiaj
   while(acc >= step){
    acc -= step;
    if(p.canShow(p.frame + 1)) p.set(p.frame + 1); else { acc = 0; break; }
   }
  }
  raf = requestAnimationFrame(tick);
 }
 function sync(){
  var run = visible && !held && !reduce && !document.hidden;
  var p = active();
  if(run && p && p.type === 'frames'){ if(!raf){ last = 0; acc = 0; raf = requestAnimationFrame(tick); } }
  else if(raf){ cancelAnimationFrame(raf); raf = null; }
  Object.keys(players).forEach(function(k){
   if(players[k].type === 'pano') players[k].run(run && k === view);
  });
 }
 function hold(){
  held = true;
  clearTimeout(resumeTimer);
  var p = active();
  if(p && p.type === 'pano') p.stopNow();
  sync();
 }
 function release(){
  clearTimeout(resumeTimer);
  resumeTimer = setTimeout(function(){ held = false; sync(); }, RESUME_MS);
 }

 tabs($('.viewer-tabs'), function(button){
  view = button.id === 'view-inside' ? 'inside' : 'outside';
  var p = active();
  if(p){ p.load(); p.draw(); }
  updateChrome();
  sync();
 });
 updateChrome();

 function rotate(dir){
  var p = active();
  if(!p) return;
  hold();
  if(p.type === 'frames') p.set(p.frame + dir * 2);
  else p.turn(dir);
  release();
 }
 [].forEach.call(root.querySelectorAll('[data-rotate]'), function(b){
  b.addEventListener('click', function(){ rotate(Number(b.getAttribute('data-rotate'))); });
 });
 stage.tabIndex = 0;
 stage.addEventListener('keydown', function(e){
  if(e.target !== stage) return;
  if(e.key === 'ArrowLeft' || e.key === 'ArrowRight'){ e.preventDefault(); rotate(e.key === 'ArrowLeft' ? -1 : 1); }
 });

 var drag = null;
 stage.addEventListener('pointerdown', function(e){
  if(e.target.closest('button') || e.button !== 0) return;
  var p = active();
  if(!p) return;
  hold();
  if(p.type === 'frames'){
   drag = { x: e.clientX, frame: p.frame };
   stage.setPointerCapture(e.pointerId);
   stage.classList.add('dragging');
  } else {
   drag = { pano: true };
  }
 });
 stage.addEventListener('pointermove', function(e){
  if(!drag || drag.pano) return;
  var p = active();
  if(p && p.type === 'frames') p.set(drag.frame - (e.clientX - drag.x) / 7);
 });
 function stopDrag(){
  if(!drag) return;
  drag = null;
  stage.classList.remove('dragging');
  release();
 }
 stage.addEventListener('pointerup', stopDrag);
 stage.addEventListener('pointercancel', stopDrag);
 stage.addEventListener('lostpointercapture', stopDrag);
 window.addEventListener('pointerup', stopDrag);

 // Klatki pobieramy dopiero, gdy sekcja zbliża się do ekranu; obrót tylko, gdy jest widoczna.
 if('IntersectionObserver' in window){
  new IntersectionObserver(function(entries){
   if(entries[entries.length - 1].isIntersecting && !near){ near = true; var p = active(); if(p) p.load(); }
  }, { rootMargin: '400px 0px' }).observe(stage);
  new IntersectionObserver(function(entries){
   visible = entries[entries.length - 1].isIntersecting; sync(); // ostatni wpis = aktualny stan
  }, { threshold: 0.2 }).observe(stage);
 } else {
  near = visible = true; var p0 = active(); if(p0) p0.load(); sync();
 }
 document.addEventListener('visibilitychange', sync);
 if(window.ResizeObserver) new ResizeObserver(function(){ var p = active(); if(p) p.draw(); }).observe(stage);
})();
</script>
@endpush
