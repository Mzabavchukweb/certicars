@extends('layouts.public')

@section('title', 'CertiCheck — ' . $car->title)
@section('description', 'Pełny raport inspekcji CertiCheck dla ' . $car->title . '. Historia lakieru, stan techniczny, wyposażenie i dokumentacja pojazdu.')
@section('og_title', 'Raport CertiCheck — ' . $car->title)
@section('og_description', 'Pełny raport inspekcji CertiCheck dla ' . $car->title . '. Historia lakieru, stan techniczny, wyposażenie i dokumentacja pojazdu.')
@section('og_type', 'article')
@if($car->primaryImage)
@section('og_image', $car->primaryImage->url)
@endif

@section('extra_head')
    @if($car->noindex || $car->is_sold)<meta name="robots" content="noindex,nofollow">@endif
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Strona główna', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Oferta samochodów', 'item' => url('/samochody')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $car->title, 'item' => url('/samochody/'.$car->slug)],
            ['@type' => 'ListItem', 'position' => 4, 'name' => 'CertiCheck', 'item' => url('/samochody/'.$car->slug.'/certicheck')],
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('styles')
@media print{.no-print{display:none!important}.cc-page{box-shadow:none!important;margin:0!important;padding:20px!important}}
.cc-page{max-width:900px;margin:40px auto;background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);overflow:hidden;font-family:'Inter',system-ui,sans-serif}
.cc-header{background:linear-gradient(135deg,#0a0a0a 0%,#1a1a2e 100%);color:#fff;padding:40px 48px;position:relative;overflow:hidden}
.cc-header::after{content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;background:rgba(0,102,255,.12);border-radius:50%}
.cc-brand{display:flex;align-items:center;gap:12px;margin-bottom:24px}
.cc-brand-name{font-size:22px;font-weight:800;letter-spacing:-.5px}
.cc-brand-name span{color:#0066ff}
.cc-badge{background:rgba(0,102,255,.15);color:#6db3ff;font-size:10px;font-weight:700;padding:4px 10px;border-radius:50px;letter-spacing:.5px;text-transform:uppercase}
.cc-car-title{font-size:28px;font-weight:900;letter-spacing:-.8px;margin-bottom:6px}
.cc-car-meta{font-size:13px;color:rgba(255,255,255,.5);font-weight:500}
.cc-car-id{font-size:11px;color:rgba(255,255,255,.3);margin-top:8px}
.cc-body{padding:36px 48px 48px}
.cc-section{margin-bottom:32px}
.cc-section:last-child{margin-bottom:0}
.cc-section-title{font-size:16px;font-weight:800;color:#0a0a0a;letter-spacing:-.3px;margin-bottom:16px;display:flex;align-items:center;gap:10px;padding-bottom:10px;border-bottom:2px solid #0066ff}
.cc-section-title svg{width:20px;height:20px;color:#0066ff;flex-shrink:0}
.cc-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:0}
.cc-row{display:flex;justify-content:space-between;align-items:baseline;padding:10px 16px;font-size:13px;border-bottom:1px solid #f0f0f2}
.cc-row:nth-child(odd){background:#fafafa}
.cc-row .lbl{color:#6b7280;font-weight:400}
.cc-row .val{font-weight:700;color:#1a1a1a;text-align:right}
.cc-paint-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1px;background:#eee;border:1px solid #eee;border-radius:10px;overflow:hidden}
.cc-paint-item{background:#fff;padding:12px 16px;display:flex;justify-content:space-between;align-items:center;font-size:12.5px}
.cc-paint-item .panel{color:#6b7280}
.cc-paint-item .um{font-weight:800;color:#1a1a1a;font-size:14px}
.cc-paint-item .um.warn{color:#f59e0b}
.cc-paint-item .um.danger{color:#ef4444}
.cc-tech-grid{display:grid;grid-template-columns:1fr;gap:0}
.cc-tech-row{display:flex;gap:16px;padding:12px 16px;font-size:13px;border-bottom:1px solid #f0f0f2;align-items:flex-start}
.cc-tech-row:nth-child(odd){background:#fafafa}
.cc-tech-row .comp{font-weight:700;color:#1a1a1a;min-width:160px;flex-shrink:0}
.cc-tech-row .status{color:#6b7280;line-height:1.5}
.cc-tech-row .status.ok{color:#16a34a}
.cc-eq-grid{display:grid;grid-template-columns:1fr 1fr;gap:0 32px}
.cc-eq-cat{margin-bottom:20px}
.cc-eq-cat-title{font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px}
.cc-eq-item{display:flex;align-items:center;gap:8px;padding:5px 0;font-size:12.5px;color:#374151}
.cc-eq-item svg{width:14px;height:14px;color:#16a34a;flex-shrink:0}
.cc-dmg{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;margin-bottom:8px}
.cc-dmg strong{color:#b45309;font-size:13px}
.cc-dmg p{font-size:12px;color:#78716c;margin:4px 0 0;line-height:1.5}
.cc-footer{background:#f5f5f7;padding:24px 48px;text-align:center;font-size:11px;color:#9ca3af;border-top:1px solid #e5e5e7}
.cc-footer a{color:#0066ff;text-decoration:none;font-weight:600}
.cc-actions{display:flex;gap:12px;justify-content:center;padding:24px 48px;background:#f5f5f7;border-top:1px solid #e5e5e7}
.cc-actions .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:all .15s;text-decoration:none;border:none}
.cc-actions .btn-primary{background:#0066ff;color:#fff}
.cc-actions .btn-primary:hover{background:#0052cc}
.cc-actions .btn-secondary{background:#fff;color:#1a1a1a;border:1px solid #e5e5e7}
.cc-actions .btn-secondary:hover{border-color:#0066ff;color:#0066ff}
.cc-actions .btn svg{width:16px;height:16px}
@media(max-width:768px){.cc-body{padding:24px 20px}.cc-header{padding:28px 20px}.cc-grid-2,.cc-eq-grid{grid-template-columns:1fr}.cc-paint-grid{grid-template-columns:1fr 1fr}.cc-actions{padding:16px 20px}.cc-footer{padding:16px 20px}}
@endsection

@section('content')
<div style="background:#f5f5f7;min-height:100vh;padding:24px 0 60px">
<div class="container no-print" style="max-width:900px;margin-bottom:16px">
    <a href="{{ route('catalog.show', $car->slug) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#6b7280;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Powrót do oferty
    </a>
</div>
<div class="cc-page">
    {{-- HEADER --}}
    <div class="cc-header">
        <div class="cc-brand">
            <div class="cc-brand-name">Certi<span>Cars</span></div>
            <span class="cc-badge">CertiCheck</span>
        </div>
        <div class="cc-car-title">{{ $car->title }}</div>
        <div class="cc-car-meta">
            @if($car->first_registration){{ $car->first_registration }} · @endif
            @if($car->mileage){{ number_format($car->mileage,0,'',' ') }} km · @endif
            @if($car->fuel_type){{ $car->fuel_type }} · @endif
            @if($car->power_hp){{ $car->power_hp }} KM @if($car->power_kw)({{ $car->power_kw }} kW)@endif @endif
        </div>
        <div class="cc-car-id">{{ $car->identifier }} · Raport wygenerowany {{ now()->format('d.m.Y') }}</div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="cc-actions no-print">
        <a href="{{ route('car.pdf', $car->slug) }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
            Pobierz PDF
        </a>
        <button onclick="window.print()" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            Drukuj raport
        </button>
    </div>

    <div class="cc-body">
        {{-- SPECYFIKACJA --}}
        <div class="cc-section">
            <div class="cc-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                Specyfikacja techniczna
            </div>
            <div class="cc-grid-2">
                <div>
                    @if($car->first_registration)<div class="cc-row"><span class="lbl">Pierwsza rejestracja</span><span class="val">{{ $car->first_registration }}</span></div>@endif
                    @if($car->mileage)<div class="cc-row"><span class="lbl">Przebieg</span><span class="val">{{ number_format($car->mileage,0,'',' ') }} km</span></div>@endif
                    @if($car->fuel_type)<div class="cc-row"><span class="lbl">Paliwo</span><span class="val">{{ $car->fuel_type }}</span></div>@endif
                    @if($car->engine_capacity)<div class="cc-row"><span class="lbl">Pojemność silnika</span><span class="val">{{ $car->engine_capacity }} ccm</span></div>@endif
                    @if($car->power_hp)<div class="cc-row"><span class="lbl">Moc</span><span class="val">{{ $car->power_hp }} KM @if($car->power_kw)({{ $car->power_kw }} kW)@endif</span></div>@endif
                    @if($car->transmission)<div class="cc-row"><span class="lbl">Skrzynia biegów</span><span class="val">{{ $car->transmission_detail ?? $car->transmission }}</span></div>@endif
                    @if($car->body_type)<div class="cc-row"><span class="lbl">Nadwozie</span><span class="val">{{ $car->body_type }}</span></div>@endif
                </div>
                <div>
                    @if($car->color)<div class="cc-row"><span class="lbl">Kolor</span><span class="val">{{ $car->color }}@if($car->color_code) ({{ $car->color_code }})@endif</span></div>@endif
                    @if($car->doors)<div class="cc-row"><span class="lbl">Drzwi / Miejsca</span><span class="val">{{ $car->doors }} / {{ $car->seats ?? '—' }}</span></div>@endif
                    @if($car->vin)<div class="cc-row"><span class="lbl">VIN</span><span class="val">{{ $car->vin }}</span></div>@endif
                    @if($car->upholstery)<div class="cc-row"><span class="lbl">Tapicerka</span><span class="val">{{ $car->upholstery }}</span></div>@endif
                    @if($car->weight)<div class="cc-row"><span class="lbl">Masa własna</span><span class="val">{{ number_format($car->weight,0,'',' ') }} kg</span></div>@endif
                    @if($car->previous_owners !== null)<div class="cc-row"><span class="lbl">Poprzedni właściciele</span><span class="val">{{ $car->previous_owners }}</span></div>@endif
                    @if($car->number_of_keys)<div class="cc-row"><span class="lbl">Liczba kluczyków</span><span class="val">{{ $car->number_of_keys }}</span></div>@endif
                    @if($car->imported_from)<div class="cc-row"><span class="lbl">Importowany z</span><span class="val">{{ $car->imported_from }}</span></div>@endif
                    @if($car->vehicle_history)<div class="cc-row"><span class="lbl">Historia pojazdu</span><span class="val">{{ $car->vehicle_history }}</span></div>@endif
                </div>
            </div>
        </div>

        {{-- SERWIS I DOKUMENTACJA --}}
        @if($car->service_book || $car->last_service || $car->next_inspection || $car->coc_documents || $car->service_documentation || $car->vehicle_folder || $car->hu_au_report || $car->service_book_status || $car->registration_cert || $car->owners_manual || $car->aso_serviced || $car->service_history)
        <div class="cc-section">
            <div class="cc-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                Serwis i dokumentacja
            </div>
            <div class="cc-grid-2">
                <div>
                    @if($car->service_book)<div class="cc-row"><span class="lbl">Książka serwisowa</span><span class="val">{{ $car->service_book }}</span></div>@endif
                    @if($car->last_service)<div class="cc-row"><span class="lbl">Ostatni serwis</span><span class="val">{{ $car->last_service }}@if($car->last_service_mileage) ({{ number_format($car->last_service_mileage,0,'',' ') }} km)@endif</span></div>@endif
                    @if($car->next_inspection)<div class="cc-row"><span class="lbl">Następny przegląd</span><span class="val">{{ $car->next_inspection }}</span></div>@endif
                    @if($car->service_documentation)<div class="cc-row"><span class="lbl">Dokumentacja</span><span class="val">{{ $car->service_documentation }}</span></div>@endif
                </div>
                <div>
                    @if($car->coc_documents)<div class="cc-row"><span class="lbl">Dokumenty CoC</span><span class="val">{{ $car->coc_documents }}</span></div>@endif
                    @if($car->vehicle_folder)<div class="cc-row"><span class="lbl">Teczka pojazdu</span><span class="val">{{ $car->vehicle_folder }}</span></div>@endif
                    @if($car->hu_au_report)<div class="cc-row"><span class="lbl">Raport HU/AU</span><span class="val">{{ $car->hu_au_report }}</span></div>@endif
                    @if($car->country_registration)<div class="cc-row"><span class="lbl">Kraj rejestracji</span><span class="val">{{ $car->country_registration }}</span></div>@endif
                    @if($car->service_book_status)<div class="cc-row"><span class="lbl">Status książki serwisowej</span><span class="val">{{ $car->service_book_status }}</span></div>@endif
                    @if($car->registration_cert)<div class="cc-row"><span class="lbl">Dowód rejestracyjny</span><span class="val">{{ $car->registration_cert }}</span></div>@endif
                    @if($car->owners_manual)<div class="cc-row"><span class="lbl">Instrukcja obsługi</span><span class="val">{{ $car->owners_manual }}</span></div>@endif
                    @if($car->aso_serviced)<div class="cc-row"><span class="lbl">Serwis ASO</span><span class="val">{{ $car->aso_serviced }}</span></div>@endif
                    @if($car->service_history)<div class="cc-row"><span class="lbl">Historia serwisowa</span><span class="val">{{ $car->service_history }}</span></div>@endif
                </div>
            </div>
        </div>
        @endif

        {{-- EMISJE I ZUŻYCIE --}}
        @if($car->co2_emission || $car->emission_class || $car->fuel_consumption)
        <div class="cc-section">
            <div class="cc-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 22V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v17"/><path d="M15 11h2a2 2 0 0 1 2 2v2a2 2 0 0 0 2 2h0a2 2 0 0 0 2-2V9.83a2 2 0 0 0-.59-1.42L18 4"/></svg>
                Emisje i zużycie paliwa
            </div>
            <div class="cc-grid-2">
                <div>
                    @if($car->fuel_consumption)<div class="cc-row"><span class="lbl">Zużycie paliwa (cykl mieszany)</span><span class="val">{{ $car->fuel_consumption }} l/100km</span></div>@endif
                    @if($car->fuel_procedure)<div class="cc-row"><span class="lbl">Procedura pomiaru</span><span class="val">{{ $car->fuel_procedure }}</span></div>@endif
                </div>
                <div>
                    @if($car->co2_emission)<div class="cc-row"><span class="lbl">Emisja CO₂</span><span class="val">{{ $car->co2_emission }} g/km</span></div>@endif
                    @if($car->emission_class)<div class="cc-row"><span class="lbl">Klasa emisji</span><span class="val">{{ $car->emission_class }}</span></div>@endif
                </div>
            </div>
        </div>
        @endif

        {{-- POMIARY LAKIERU --}}
        @if($car->paint_measurements && count($car->paint_measurements))
        <div class="cc-section">
            <div class="cc-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="2.5"/><path d="M17.78 10.22a2.5 2.5 0 0 1 0 3.56L12 19.56a2.5 2.5 0 0 1-3.56 0l-5.78-5.78a2.5 2.5 0 0 1 0-3.56L8.44 4.44a2.5 2.5 0 0 1 3.56 0z"/></svg>
                Pomiary grubości lakieru
            </div>
            <p style="font-size:12px;color:#9ca3af;margin:-8px 0 14px">Norma fabryczna: 80–150 µm. Wartości powyżej 200 µm mogą wskazywać na naprawę lakierniczą.</p>
            @php
                $paintPanelNames = [
                    0 => 'Dach', 1 => 'Maska', 2 => 'Błotnik P-L', 3 => 'Błotnik P-P',
                    4 => 'Drzwi P-L', 5 => 'Drzwi P-P', 6 => 'Błotnik T-L', 7 => 'Błotnik T-P',
                    8 => 'Drzwi T-L', 9 => 'Drzwi T-P', 10 => 'Klapa bagażnika',
                    11 => 'Zderzak przód', 12 => 'Zderzak tył', 13 => 'Próg lewy', 14 => 'Próg prawy',
                ];
            @endphp
            <div class="cc-paint-grid">
                @foreach($car->paint_measurements as $panel => $value)
                @php
                    $val = is_array($value) ? ($value['value'] ?? $value[0] ?? '') : $value;
                    $numVal = (int) preg_replace('/[^0-9]/', '', (string) $val);
                    if ($numVal <= 0) continue;
                    $panelLabel = is_array($value) && isset($value['area'])
                        ? $value['area']
                        : (is_numeric($panel) ? ($paintPanelNames[$panel] ?? 'Panel '.($panel + 1)) : $panel);
                @endphp
                <div class="cc-paint-item">
                    <span class="panel">{{ $panelLabel }}</span>
                    <span class="um {{ $numVal > 200 ? 'danger' : ($numVal > 160 ? 'warn' : '') }}">{{ $val }} µm</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- STAN TECHNICZNY --}}
        @if($car->technical_conditions && count($car->technical_conditions))
        <div class="cc-section">
            <div class="cc-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                Ocena stanu technicznego
            </div>
            @php
                $techLabels = [
                    'engine' => 'Silnik', 'transmission' => 'Skrzynia biegów',
                    'suspension' => 'Zawieszenie', 'electronics' => 'Elektronika',
                    'body' => 'Nadwozie', 'brakes' => 'Hamulce',
                    'steering' => 'Układ kierowniczy', 'exhaust' => 'Układ wydechowy',
                    'ac' => 'Klimatyzacja', 'air_conditioning' => 'Klimatyzacja',
                    'braking' => 'Układ hamulcowy', 'tires' => 'Opony', 'lights' => 'Oświetlenie',
                    'interior' => 'Wnętrze', 'underbody' => 'Podwozie',
                ];
            @endphp
            <div class="cc-tech-grid">
                @foreach($car->technical_conditions as $comp => $status)
                @php
                    $st = is_array($status) ? ($status['status'] ?? $status[0] ?? 'OK') : $status;
                    $compLabel = $techLabels[strtolower($comp)] ?? ucfirst($comp);
                @endphp
                <div class="cc-tech-row">
                    <span class="comp">{{ $compLabel }}</span>
                    <span class="status {{ str_contains(strtolower($st), 'sprawn') ? 'ok' : '' }}">{{ $st }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- USZKODZENIA --}}
        @if($car->damages->count())
        <div class="cc-section">
            <div class="cc-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                Uszkodzenia pojazdu ({{ $car->damages->count() }})
            </div>
            @foreach($car->damages as $d)
            <div class="cc-dmg">
                <strong>{{ $d->area }}</strong>
                @if($d->tags && count($d->tags)) <span style="font-size:11px;color:#92400e">— {{ implode(', ', $d->tags) }}</span>@endif
                @if($d->description)<p>{{ $d->description }}</p>@endif
            </div>
            @endforeach
        </div>
        @endif

        {{-- WYPOSAŻENIE --}}
        @if($car->equipment && count($car->equipment))
        <div class="cc-section">
            <div class="cc-section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Wyposażenie pojazdu
            </div>
            @php
                $equipLabels = [
                    'safety' => 'Bezpieczeństwo', 'comfort' => 'Komfort',
                    'multimedia' => 'Multimedia', 'exterior' => 'Wygląd zewnętrzny',
                    'interior' => 'Wnętrze', 'driving' => 'Jazda', 'other' => 'Inne',
                ];
            @endphp
            <div class="cc-eq-grid">
                @foreach($car->equipment as $cat => $items)
                @if(is_array($items) && count($items))
                <div class="cc-eq-cat">
                    <div class="cc-eq-cat-title">{{ $equipLabels[$cat] ?? ucfirst($cat) }}</div>
                    @foreach($items as $item)
                    <div class="cc-eq-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ $item }}
                    </div>
                    @endforeach
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="cc-footer">
        CertiCars · <a href="mailto:kontakt@certicars.pl">kontakt@certicars.pl</a> · +48 123 456 789 · <a href="{{ url('/') }}">certicars.pl</a>
        <br>Raport CertiCheck wygenerowany {{ now()->format('d.m.Y H:i') }}
    </div>
</div>
</div>
@endsection
