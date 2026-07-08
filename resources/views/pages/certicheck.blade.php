@extends('layouts.public')
@section('title','CertiCheck — rozszerzony opis wybranych aut')
@section('description','CertiCheck to wewnętrzny standard opisu wybranych pojazdów w CertiCars. Dowiedz się, co obejmuje, jak wygląda proces i co dostajesz w raporcie.')

@section('styles')
:root{--cc-blue:#0066ff;--cc-blue-d:#0052cc;--cc-bg:#eef2fa;--cc-text:#0a0a0a;--cc-muted:#475569}

/* HERO */
.cclp-hero{position:relative;background:#eef2fa;padding:72px 0 96px;overflow:hidden;isolation:isolate}
.cclp-hero::before{content:'';position:absolute;inset:0;background:linear-gradient(180deg,#f4f6fc 0%,#eef2fa 55%,#e8edf7 100%);z-index:0}
.cclp-hero::after{content:'';position:absolute;top:50%;right:-6%;transform:translateY(-50%);width:50%;height:110%;background:radial-gradient(ellipse 55% 60% at 50% 50%,rgba(202,215,246,.55) 0%,rgba(202,215,246,.28) 40%,rgba(202,215,246,0) 85%);z-index:0;pointer-events:none}
.cclp-hero-in{max-width:1200px;margin:0 auto;padding:0 24px;position:relative;z-index:1;display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr);gap:36px;align-items:center}
.cclp-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:800;color:var(--cc-blue);text-transform:uppercase;letter-spacing:1.6px;margin-bottom:16px}
.cclp-eyebrow::before{content:'';width:22px;height:1.5px;background:var(--cc-blue);border-radius:1px}
.cclp-hero h1{font-size:52px;font-weight:900;color:var(--cc-text);letter-spacing:-1px;line-height:1.05;margin:0 0 18px}
.cclp-hero .lead{font-size:16.5px;color:var(--cc-muted);line-height:1.7;margin:0 0 26px;max-width:560px}
.cclp-hero-ctas{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:22px}
.cclp-cta-primary{display:inline-flex;align-items:center;gap:8px;background:var(--cc-blue);color:#fff;padding:14px 26px;border-radius:50px;font-size:14.5px;font-weight:700;text-decoration:none;box-shadow:0 4px 16px rgba(0,102,255,.28);transition:all .18s}
.cclp-cta-primary:hover{background:var(--cc-blue-d);color:#fff;transform:translateY(-1px);box-shadow:0 6px 22px rgba(0,102,255,.4)}
.cclp-cta-secondary{display:inline-flex;align-items:center;gap:6px;color:var(--cc-blue);padding:14px 20px;border-radius:50px;font-size:14.5px;font-weight:700;text-decoration:none;border:1.5px solid #dbeafe;transition:all .15s}
.cclp-cta-secondary:hover{background:#f0f6ff;color:var(--cc-blue-d)}
.cclp-cta-primary svg,.cclp-cta-secondary svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.2}
.cclp-hero-hero{position:relative;display:flex;align-items:center;justify-content:center}
.cclp-hero-hero img{height:460px;width:auto;max-width:100%;object-fit:contain;display:block;-webkit-mask-image:radial-gradient(ellipse 52% 62% at center,#000 32%,rgba(0,0,0,.9) 48%,rgba(0,0,0,.55) 65%,rgba(0,0,0,.2) 80%,transparent 95%);mask-image:radial-gradient(ellipse 52% 62% at center,#000 32%,rgba(0,0,0,.9) 48%,rgba(0,0,0,.55) 65%,rgba(0,0,0,.2) 80%,transparent 95%)}

/* WHAT IS IT */
.cclp-what{padding:72px 0;background:#fff}
.cclp-in{max-width:1200px;margin:0 auto;padding:0 24px}
.cclp-head{text-align:center;max-width:720px;margin:0 auto 48px}
.cclp-head h2{font-size:38px;font-weight:900;letter-spacing:-.7px;color:var(--cc-text);margin:0 0 14px;line-height:1.1}
.cclp-head p{font-size:16px;color:var(--cc-muted);line-height:1.7;margin:0}
.cclp-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.cclp-card{background:#f8fafc;border:1px solid #e5edfa;border-radius:16px;padding:22px 20px;position:relative;transition:all .2s}
.cclp-card:hover{border-color:#c7d8f5;transform:translateY(-2px);box-shadow:0 10px 28px rgba(15,32,72,.06)}
.cclp-card-ico{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#dbeafe 0%,#eff6ff 100%);color:var(--cc-blue);display:flex;align-items:center;justify-content:center;margin-bottom:16px}
.cclp-card-ico svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:1.9}
.cclp-card h3{font-size:16px;font-weight:800;color:var(--cc-text);margin:0 0 8px;letter-spacing:-.2px}
.cclp-card p{font-size:13.5px;color:var(--cc-muted);line-height:1.55;margin:0}

/* PROCESS */
.cclp-process{padding:72px 0;background:#f8fafc}
.cclp-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:12px}
.cclp-step{position:relative;padding:24px 22px;background:#fff;border-radius:16px;border:1px solid #e5edfa}
.cclp-step-num{position:absolute;top:-14px;left:22px;width:32px;height:32px;border-radius:50%;background:var(--cc-blue);color:#fff;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,102,255,.28)}
.cclp-step h3{font-size:15.5px;font-weight:800;color:var(--cc-text);margin:14px 0 8px;letter-spacing:-.2px}
.cclp-step p{font-size:13.5px;color:var(--cc-muted);line-height:1.55;margin:0}

/* INCLUDES / EXCLUDES */
.cclp-scope{padding:72px 0;background:#fff}
.cclp-scope-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}
.cclp-scope-col{padding:28px 26px;border-radius:20px;border:1px solid #e5edfa}
.cclp-scope-col.includes{background:linear-gradient(180deg,#f0f7ff 0%,#f8fafc 100%);border-color:#c7d8f5}
.cclp-scope-col.excludes{background:#fff}
.cclp-scope-title{display:flex;align-items:center;gap:10px;font-size:18px;font-weight:800;color:var(--cc-text);margin:0 0 16px;letter-spacing:-.2px}
.cclp-scope-title .ico{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center}
.cclp-scope-title.ok .ico{background:#dcfce7;color:#16a34a}
.cclp-scope-title.no .ico{background:#fee2e2;color:#dc2626}
.cclp-scope-title svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.4}
.cclp-scope-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:10px}
.cclp-scope-list li{display:flex;align-items:flex-start;gap:10px;font-size:14.5px;color:#334155;line-height:1.55}
.cclp-scope-list li::before{content:'';flex-shrink:0;width:6px;height:6px;border-radius:50%;background:#94a3b8;margin-top:8px}
.cclp-scope-col.includes .cclp-scope-list li::before{background:#22c55e}

/* FAQ */
.cclp-faq{padding:72px 0;background:#f8fafc}
.cclp-faq-list{max-width:820px;margin:0 auto;display:flex;flex-direction:column;gap:12px}
.cclp-faq-item{background:#fff;border:1px solid #e5edfa;border-radius:14px;overflow:hidden}
.cclp-faq-q{width:100%;text-align:left;background:transparent;border:0;padding:18px 22px;font-size:15.5px;font-weight:700;color:var(--cc-text);cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:16px;letter-spacing:-.15px}
.cclp-faq-q svg{width:18px;height:18px;stroke:var(--cc-blue);fill:none;stroke-width:2.4;flex-shrink:0;transition:transform .18s}
.cclp-faq-item[open] .cclp-faq-q svg{transform:rotate(180deg)}
.cclp-faq-a{padding:0 22px 18px;font-size:14.5px;color:var(--cc-muted);line-height:1.7}

/* DISCLAIMER */
.cclp-disc{padding:56px 0;background:#0a1432;color:rgba(255,255,255,.85)}
.cclp-disc-in{max-width:820px;margin:0 auto;padding:0 24px;display:flex;gap:18px;align-items:flex-start}
.cclp-disc-ico{flex-shrink:0;width:44px;height:44px;border-radius:50%;background:rgba(95,161,255,.12);border:1.5px solid rgba(95,161,255,.4);color:#5fa1ff;display:flex;align-items:center;justify-content:center}
.cclp-disc-ico svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2.2}
.cclp-disc h3{font-size:18px;font-weight:800;color:#fff;margin:0 0 8px;letter-spacing:-.2px}
.cclp-disc p{font-size:14.5px;line-height:1.7;margin:0}

/* CTA STRIP */
.cclp-cta-strip{padding:64px 0;background:#fff}
.cclp-cta-strip-in{max-width:1000px;margin:0 auto;padding:32px 36px;background:linear-gradient(135deg,#0066ff 0%,#0052cc 100%);border-radius:24px;display:flex;justify-content:space-between;align-items:center;gap:24px;flex-wrap:wrap;box-shadow:0 24px 48px rgba(0,102,255,.22)}
.cclp-cta-strip h3{font-size:22px;font-weight:800;color:#fff;margin:0 0 4px;letter-spacing:-.3px}
.cclp-cta-strip p{font-size:14px;color:rgba(255,255,255,.85);margin:0}
.cclp-cta-strip-btn{display:inline-flex;align-items:center;gap:8px;background:#fff;color:var(--cc-blue);padding:14px 24px;border-radius:50px;font-size:14.5px;font-weight:700;text-decoration:none;transition:transform .15s}
.cclp-cta-strip-btn:hover{transform:translateY(-1px);color:var(--cc-blue-d)}
.cclp-cta-strip-btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.4}

/* RESPONSIVE */
@media(max-width:1024px){
    .cclp-hero{padding:56px 0 72px}
    .cclp-hero-in{grid-template-columns:1fr;gap:24px}
    .cclp-hero h1{font-size:40px}
    .cclp-hero-hero{order:-1}
    .cclp-hero-hero img{height:320px}
    .cclp-cards{grid-template-columns:repeat(2,1fr)}
    .cclp-steps{grid-template-columns:repeat(2,1fr);gap:24px}
    .cclp-what,.cclp-process,.cclp-scope,.cclp-faq{padding:56px 0}
    .cclp-head h2{font-size:30px}
    .cclp-scope-grid{grid-template-columns:1fr}
}
@media(max-width:640px){
    .cclp-hero{padding:44px 0 56px}
    .cclp-hero h1{font-size:32px;letter-spacing:-.7px}
    .cclp-hero .lead{font-size:15px}
    .cclp-hero-hero img{height:260px}
    .cclp-hero-ctas{flex-direction:column;align-items:stretch}
    .cclp-cta-primary,.cclp-cta-secondary{justify-content:center}
    .cclp-cards{grid-template-columns:1fr;gap:12px}
    .cclp-steps{grid-template-columns:1fr}
    .cclp-what,.cclp-process,.cclp-scope,.cclp-faq{padding:44px 0}
    .cclp-head{margin-bottom:32px}
    .cclp-head h2{font-size:26px}
    .cclp-head p{font-size:14.5px}
    .cclp-scope-col{padding:22px 20px}
    .cclp-cta-strip-in{padding:24px;flex-direction:column;text-align:center}
    .cclp-cta-strip h3{font-size:20px}
    .cclp-disc-in{flex-direction:column;text-align:left;padding:0 20px}
}
@endsection

@section('content')

{{-- HERO --}}
<section class="cclp-hero">
    <div class="cclp-hero-in">
        <div>
            <div class="cclp-eyebrow">CertiCheck</div>
            <h1>Wiesz więcej<br>przed przyjazdem</h1>
            <p class="lead">CertiCheck to rozszerzony opis wybranych aut w naszej ofercie. Poza standardowym opisem stanu, pochodzenia i wyposażenia dodajemy pomiary lakieru, obserwacje techniczne i zdjęcia detali — tak, by ocena auta była łatwiejsza jeszcze przed przyjazdem na plac.</p>
            <div class="cclp-hero-ctas">
                <a class="cclp-cta-primary" href="{{ route('catalog') }}">
                    <x-icon name="search" size="15" :strokeWidth="2.2"/>
                    Zobacz auta z CertiCheck
                </a>
                <a class="cclp-cta-secondary" href="#jak-to-dziala">
                    Jak to działa?
                    <x-icon name="arrow-down" size="14" :strokeWidth="2.2"/>
                </a>
            </div>
        </div>
        <div class="cclp-hero-hero" aria-hidden="true">
            <img src="/images/bohater-mobile.png" alt="" width="941" height="1672" loading="eager" decoding="async">
        </div>
    </div>
</section>

{{-- WHAT IS INCLUDED --}}
<section class="cclp-what" id="co-obejmuje">
    <div class="cclp-in">
        <div class="cclp-head">
            <h2>Co znajdziesz w CertiCheck</h2>
            <p>Cztery obszary, na które kładziemy szczególny nacisk. Każdy z nich dostajesz razem z ofertą auta — bez dodatkowej opłaty, przed wizytą na placu.</p>
        </div>
        <div class="cclp-cards">
            @php
                $cards = [
                    ['scan-line', 'Pomiary lakieru', 'Wskazujemy pomiary i ewentualne różnice grubości powłoki w punktach kontrolnych. Widać, gdzie lakier jest fabryczny, a gdzie mógł być odnawiany.'],
                    ['wrench', 'Stan techniczny', 'Opisujemy widoczne elementy techniczne i podstawowe obserwacje z oględzin pojazdu — zawieszenie, układ hamulcowy, płyny eksploatacyjne, wycieki.'],
                    ['search', 'Ślady użytkowania', 'Pokazujemy widoczne ślady eksploatacji i ich lokalizację — otarcia, rysy, zużycie wnętrza. Zdjęcia detali zamiast ogólników.'],
                    ['file-text', 'Raport PDF', 'Czytelne podsumowanie ze zdjęciami i danymi do pobrania. Możesz przejrzeć raport spokojnie w domu i podjąć decyzję bez presji.'],
                ];
            @endphp
            @foreach($cards as [$ico, $title, $desc])
                <div class="cclp-card">
                    <div class="cclp-card-ico"><x-icon :name="$ico" size="22" :strokeWidth="1.9"/></div>
                    <h3>{{ $title }}</h3>
                    <p>{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PROCESS --}}
<section class="cclp-process" id="jak-to-dziala">
    <div class="cclp-in">
        <div class="cclp-head">
            <h2>Jak powstaje CertiCheck</h2>
            <p>Proces jest ten sam dla każdego auta objętego CertiCheck. Nic nie zostaje pominięte, nawet jeśli auto wygląda dobrze na pierwszy rzut oka.</p>
        </div>
        <div class="cclp-steps">
            @php
                $steps = [
                    ['1','Wstępna kwalifikacja','Wybieramy auto, którego historia i stan uzasadniają rozszerzony opis. Weryfikujemy VIN, przebieg i dokumenty.'],
                    ['2','Oględziny na miejscu','Nasz zespół sprawdza karoserię, wnętrze, przestrzeń bagażową i komorę silnika. Robimy zdjęcia detali, których nie widać w standardowej sesji.'],
                    ['3','Pomiary i obserwacje','Wykonujemy pomiary grubości lakieru w punktach kontrolnych, notujemy widoczne ślady użytkowania i obserwacje techniczne.'],
                    ['4','Publikacja raportu','Wszystko trafia do raportu w formacie PDF, dostępnego na stronie ogłoszenia. Możesz go pobrać przed przyjazdem.'],
                ];
            @endphp
            @foreach($steps as [$n, $title, $desc])
                <div class="cclp-step">
                    <div class="cclp-step-num">{{ $n }}</div>
                    <h3>{{ $title }}</h3>
                    <p>{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SCOPE — includes vs excludes --}}
<section class="cclp-scope">
    <div class="cclp-in">
        <div class="cclp-head">
            <h2>Co CertiCheck obejmuje, a czego nie</h2>
            <p>Uczciwie mówimy, co jesteśmy w stanie ocenić bez demontażu, a co wymaga wizyty u mechanika lub diagnostyki specjalistycznej.</p>
        </div>
        <div class="cclp-scope-grid">
            <div class="cclp-scope-col includes">
                <div class="cclp-scope-title ok">
                    <span class="ico"><x-icon name="check" size="16" :strokeWidth="2.4"/></span>
                    Obejmuje
                </div>
                <ul class="cclp-scope-list">
                    <li>Pomiary grubości lakieru w wyznaczonych punktach kontrolnych.</li>
                    <li>Opis widocznych śladów użytkowania (rysy, otarcia, wgniecenia).</li>
                    <li>Ocena stanu wnętrza — tapicerka, kokpit, oznaki zużycia.</li>
                    <li>Podstawowe obserwacje techniczne bez demontażu (płyny, wycieki, elementy widoczne).</li>
                    <li>Weryfikacja zgodności VIN i podstawowych dokumentów.</li>
                    <li>Zdjęcia detali i miejsc, które warto zobaczyć z bliska.</li>
                </ul>
            </div>
            <div class="cclp-scope-col excludes">
                <div class="cclp-scope-title no">
                    <span class="ico"><x-icon name="x" size="16" :strokeWidth="2.4"/></span>
                    Nie obejmuje
                </div>
                <ul class="cclp-scope-list">
                    <li>Diagnostyki komputerowej podzespołów (silnik, skrzynia, elektronika).</li>
                    <li>Demontażu elementów w celu oceny stanu wewnętrznego.</li>
                    <li>Prognozy trwałości podzespołów i zapowiedzi przyszłych napraw.</li>
                    <li>Opinii rzeczoznawcy w rozumieniu prawnym.</li>
                    <li>Wyceny wartości pojazdu w oparciu o metodologię ekspercką.</li>
                    <li>Badań specjalistycznych (endoskopia, pomiary geometrii, stanowiskowe).</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="cclp-faq" id="faq">
    <div class="cclp-in">
        <div class="cclp-head">
            <h2>Najczęściej zadawane pytania</h2>
            <p>Kilka rzeczy, o które pytacie najczęściej, zanim zdecydujecie się na oglądanie auta z CertiCheck.</p>
        </div>
        <div class="cclp-faq-list">
            @php
                $faqs = [
                    ['Czy CertiCheck to badanie techniczne?','Nie. CertiCheck to rozszerzony opis stanu widocznego pojazdu — nie zastępuje badania technicznego, opinii rzeczoznawcy ani diagnostyki komputerowej. Skupiamy się na tym, co da się zobaczyć i zmierzyć bez demontażu.'],
                    ['Czy CertiCheck jest dostępny dla każdego auta w ofercie?','Nie. Obejmuje wybrane pojazdy, dla których uzasadnione jest przygotowanie rozszerzonego opisu. Auta objęte CertiCheck mają oznaczenie na karcie ogłoszenia oraz w katalogu.'],
                    ['Czy raport CertiCheck jest płatny?','Nie. Raport jest częścią ogłoszenia i dostępny do pobrania w formacie PDF bezpłatnie — bez logowania ani rejestracji.'],
                    ['Co jeśli po przyjeździe zobaczę coś, czego nie ma w raporcie?','Piszemy o widocznych elementach na dzień oględzin. Jeśli coś się różni od raportu — powiedz nam. Każdy taki przypadek weryfikujemy razem z zespołem, który przygotowywał opis.'],
                    ['Czy CertiCheck zastępuje wizytę u mechanika?','Nie. Zawsze zalecamy, aby przed decyzją zakupową obejrzeć auto osobiście, a przy chęci głębszej weryfikacji — skonsultować się z niezależnym mechanikiem lub stacją diagnostyczną.'],
                ];
            @endphp
            @foreach($faqs as $i => [$q, $a])
                <details class="cclp-faq-item" @if($i===0) open @endif>
                    <summary class="cclp-faq-q">
                        {{ $q }}
                        <x-icon name="chevron-down" size="18" :strokeWidth="2.4"/>
                    </summary>
                    <div class="cclp-faq-a">{{ $a }}</div>
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- DISCLAIMER --}}
<section class="cclp-disc">
    <div class="cclp-disc-in">
        <div class="cclp-disc-ico"><x-icon name="shield-check" size="20" :strokeWidth="2.2"/></div>
        <div>
            <h3>Ważne — o standardzie i jego granicach</h3>
            <p>CertiCheck to wewnętrzny standard kontroli jakości CertiCars, a nie opinia rzeczoznawcy. Opis dotyczy stanu pojazdu na dzień oględzin i obejmuje elementy możliwe do oceny bez specjalistycznego demontażu podzespołów. Raport ma pomóc w podjęciu świadomej decyzji, ale nie zastępuje wizyty u mechanika ani badania technicznego.</p>
        </div>
    </div>
</section>

{{-- CTA STRIP --}}
<section class="cclp-cta-strip">
    <div class="cclp-cta-strip-in">
        <div>
            <h3>Gotowy przejrzeć auta z CertiCheck?</h3>
            <p>Zobacz aktualną ofertę pojazdów objętych rozszerzonym opisem.</p>
        </div>
        <a class="cclp-cta-strip-btn" href="{{ route('catalog') }}">
            Przeglądaj ofertę
            <x-icon name="arrow-right" size="15" :strokeWidth="2.4"/>
        </a>
    </div>
</section>

@endsection
