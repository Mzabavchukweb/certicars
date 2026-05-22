<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>{{ $car->title }} — CertiCheck Report</title>
    <style>
        @page { margin: 30px 35px 45px 35px; }
        body{font-family:'DejaVu Sans',sans-serif;color:#1a1a1a;font-size:9.5px;line-height:1.5;margin:0}

        /* HEADER */
        .hd{display:table;width:100%;margin-bottom:16px;border-bottom:2px solid #0066ff;padding-bottom:12px}
        .hd-left{display:table-cell;vertical-align:middle}
        .hd-brand{font-size:16px;font-weight:bold;color:#0066ff;letter-spacing:-0.5px}
        .hd-brand span{color:#1a1a1a}
        .hd-badge{display:inline-block;background:#0066ff;color:#fff;font-size:8px;font-weight:bold;padding:2px 8px;border-radius:10px;letter-spacing:0.4px;text-transform:uppercase;margin-left:8px;vertical-align:middle}
        .hd-right{display:table-cell;text-align:right;vertical-align:middle;font-size:8px;color:#888}
        .hd-right div{margin-bottom:1px}

        /* PAGE 1: VEHICLE SUMMARY */
        .car-title{font-size:18px;font-weight:bold;color:#1a1a1a;margin:0 0 4px;letter-spacing:-0.5px}
        .car-meta{font-size:9px;color:#666;margin-bottom:14px}
        .car-img{width:100%;max-height:300px;object-fit:cover;border-radius:6px;margin-bottom:14px}

        /* PRICE BOX */
        .price-box{background:#0a0a0a;color:#fff;padding:12px 18px;border-radius:8px;margin-bottom:16px;display:table;width:100%}
        .price-box .lbl{display:table-cell;font-size:8px;color:#aaa;text-transform:uppercase;letter-spacing:0.5px;vertical-align:middle}
        .price-box .val{display:table-cell;text-align:right;font-size:20px;font-weight:bold;vertical-align:middle}

        /* SECTION HEADERS */
        .sh{font-size:13px;font-weight:bold;color:#0066ff;margin:18px 0 8px;padding-bottom:5px;border-bottom:1.5px solid #e5e7eb}
        .sh-dark{color:#1a1a1a;border-bottom-color:#0066ff}
        .sh-icon{display:inline-block;width:14px;height:14px;vertical-align:middle;margin-right:4px}

        /* DATA TABLES */
        table{width:100%;border-collapse:collapse;font-size:9px;margin-bottom:10px}
        td,th{padding:5px 8px;border-bottom:1px solid #f0f0f2;text-align:left}
        td.lbl{color:#6b7280;width:42%}
        td.val{font-weight:bold;color:#1a1a1a;text-align:right}
        th{background:#f9fafb;font-weight:700;color:#374151;font-size:8px;text-transform:uppercase;letter-spacing:0.4px}

        /* TWO COLUMN LAYOUT */
        .cols{display:table;width:100%;margin-bottom:10px}
        .col{display:table-cell;width:50%;vertical-align:top;padding-right:10px}
        .col:last-child{padding-right:0;padding-left:10px}

        /* THREE COLUMN LAYOUT */
        .cols3{display:table;width:100%;margin-bottom:10px}
        .col3{display:table-cell;width:33.33%;vertical-align:top;padding-right:8px}
        .col3:last-child{padding-right:0}

        /* KEY FACTS STRIP */
        .kf{display:table;width:100%;background:#f0f7ff;border-radius:6px;padding:10px 0;margin-bottom:14px}
        .kf-item{display:table-cell;text-align:center;padding:4px 8px}
        .kf-val{font-size:12px;font-weight:bold;color:#1a1a1a;display:block}
        .kf-lbl{font-size:7px;color:#6b7280;text-transform:uppercase;letter-spacing:0.3px}

        /* DAMAGE CARD */
        .dmg{background:#fffbeb;border-left:3px solid #f59e0b;padding:8px 12px;margin-bottom:6px;border-radius:0 4px 4px 0}
        .dmg strong{color:#92400e;font-size:10px}
        .dmg p{margin:3px 0 0;color:#78716c;font-size:9px}

        /* PAINT TABLE */
        .paint-tbl td{text-align:center;font-weight:bold}
        .paint-ok{color:#16a34a;background:#f0fdf4}
        .paint-warn{color:#d97706;background:#fffbeb}
        .paint-danger{color:#dc2626;background:#fef2f2}

        /* CONDITION ROW */
        .cond-ok{color:#16a34a}
        .cond-warn{color:#d97706}
        .cond-fail{color:#dc2626}

        /* EQUIPMENT LIST */
        .eq-list{list-style:none;padding:0;margin:0}
        .eq-list li{padding:3px 0;font-size:8.5px;color:#374151}
        .eq-list li:before{content:'✓ ';color:#0066ff;font-weight:bold}
        .eq-cat{font-size:9px;font-weight:bold;color:#0066ff;margin:8px 0 4px;text-transform:uppercase;letter-spacing:0.3px}

        /* TIRE TABLE */
        .tire-tbl{width:100%;border-collapse:collapse;font-size:9px}
        .tire-tbl td,.tire-tbl th{padding:6px 8px;border:1px solid #e5e7eb;text-align:center}
        .tire-tbl th{background:#f3f4f6;font-size:8px;text-transform:uppercase;letter-spacing:0.3px}

        /* SERVICE LOG */
        .svc-row{display:table;width:100%;border-bottom:1px solid #f0f0f2;padding:4px 0}
        .svc-lbl{display:table-cell;width:40%;color:#6b7280}
        .svc-val{display:table-cell;text-align:right;font-weight:bold}

        /* PHOTO GRID */
        .photo-grid{width:100%;margin-bottom:10px}
        .photo-grid img{width:48%;max-height:180px;object-fit:cover;border-radius:4px;margin:1%;display:inline-block}

        /* FOOTER */
        .foot{position:fixed;bottom:-28px;left:0;right:0;text-align:center;font-size:7.5px;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:5px}
        .foot strong{color:#0066ff}

        /* PAGE BREAK */
        .pb{page-break-before:always}
    </style>
</head>
<body>

{{-- ===== HEADER (repeats conceptually on page 1) ===== --}}
<div class="hd">
    <div class="hd-left">
        <span class="hd-brand">Certi<span>Cars</span></span>
        <span class="hd-badge">CertiCheck</span>
    </div>
    <div class="hd-right">
        <div>{{ $car->identifier }} · {{ now()->format('d.m.Y') }}</div>
        <div>certicars.pl · kontakt@certicars.pl</div>
    </div>
</div>

{{-- ===== PAGE 1: VEHICLE SUMMARY ===== --}}
<div class="car-title">{{ $car->title }}</div>
<div class="car-meta">
    @if($car->first_registration){{ $car->first_registration }} · @endif
    @if($car->mileage){{ number_format($car->mileage,0,'',' ') }} km · @endif
    @if($car->fuel_type){{ $car->fuel_type }} · @endif
    @if($car->power_hp){{ $car->power_hp }} KM · @endif
    @if($car->transmission){{ $car->transmission }}@endif
</div>

@if($car->primaryImage)
<img src="{{ $car->primaryImage->url }}" class="car-img" onerror="this.style.display='none'">
@endif

{{-- KEY FACTS --}}
<div class="kf">
    @if($car->mileage)<div class="kf-item"><span class="kf-val">{{ number_format($car->mileage,0,'',' ') }} km</span><span class="kf-lbl">Przebieg</span></div>@endif
    @if($car->first_registration)<div class="kf-item"><span class="kf-val">{{ $car->first_registration }}</span><span class="kf-lbl">Rejestracja</span></div>@endif
    @if($car->fuel_type)<div class="kf-item"><span class="kf-val">{{ $car->fuel_type }}</span><span class="kf-lbl">Paliwo</span></div>@endif
    @if($car->power_hp)<div class="kf-item"><span class="kf-val">{{ $car->power_hp }} KM</span><span class="kf-lbl">Moc</span></div>@endif
    @if($car->transmission)<div class="kf-item"><span class="kf-val">{{ $car->transmission }}</span><span class="kf-lbl">Skrzynia</span></div>@endif
</div>

{{-- PRICE --}}
<div class="price-box">
    <span class="lbl">Cena sprzedaży</span>
    <span class="val">{{ $car->formatted_price }}</span>
</div>

{{-- ===== DANE POJAZDU (Vehicle Data) ===== --}}
<div class="sh sh-dark">Dane pojazdu</div>
<div class="cols">
    <div class="col">
        <table>
            @if($car->first_registration)<tr><td class="lbl">Pierwsza rejestracja</td><td class="val">{{ $car->first_registration }}</td></tr>@endif
            @if($car->mileage)<tr><td class="lbl">Przebieg</td><td class="val">{{ number_format($car->mileage,0,'',' ') }} km</td></tr>@endif
            @if($car->fuel_type)<tr><td class="lbl">Paliwo</td><td class="val">{{ $car->fuel_type }}</td></tr>@endif
            @if($car->engine_capacity)<tr><td class="lbl">Pojemność silnika</td><td class="val">{{ $car->engine_capacity }} ccm</td></tr>@endif
            @if($car->power_hp)<tr><td class="lbl">Moc</td><td class="val">{{ $car->power_hp }} KM @if($car->power_kw)({{ $car->power_kw }} kW)@endif</td></tr>@endif
            @if($car->transmission)<tr><td class="lbl">Skrzynia biegów</td><td class="val">{{ $car->transmission_detail ?? $car->transmission }}</td></tr>@endif
            @if($car->drive_type)<tr><td class="lbl">Napęd</td><td class="val">{{ $car->drive_type }}</td></tr>@endif
        </table>
    </div>
    <div class="col">
        <table>
            @if($car->body_type)<tr><td class="lbl">Typ nadwozia</td><td class="val">{{ $car->body_type }}</td></tr>@endif
            @if($car->color)<tr><td class="lbl">Kolor</td><td class="val">{{ $car->color }}@if($car->color_code) ({{ $car->color_code }})@endif</td></tr>@endif
            @if($car->doors)<tr><td class="lbl">Drzwi</td><td class="val">{{ $car->doors }}</td></tr>@endif
            @if($car->seats)<tr><td class="lbl">Miejsca</td><td class="val">{{ $car->seats }}</td></tr>@endif
            @if($car->vin)<tr><td class="lbl">VIN</td><td class="val">{{ $car->vin }}</td></tr>@endif
            @if($car->co2_emission)<tr><td class="lbl">Emisja CO₂</td><td class="val">{{ $car->co2_emission }}</td></tr>@endif
            @if($car->emission_class)<tr><td class="lbl">Klasa emisji</td><td class="val">{{ $car->emission_class }}</td></tr>@endif
        </table>
    </div>
</div>

{{-- ===== PAGE 2: STAN POJAZDU ===== --}}
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><div>{{ $car->identifier }}</div></div>
</div>

{{-- SERVICE & DOCUMENTATION --}}
@if($car->service_book || $car->last_service || $car->next_inspection || $car->service_documentation)
<div class="sh">Serwis i dokumentacja</div>
<table>
    @if($car->service_book)<tr><td class="lbl">Książka serwisowa</td><td class="val">{{ $car->service_book }}</td></tr>@endif
    @if($car->last_service)<tr><td class="lbl">Ostatni serwis</td><td class="val">{{ $car->last_service }}@if($car->last_service_mileage) ({{ number_format($car->last_service_mileage,0,'',' ') }} km)@endif</td></tr>@endif
    @if($car->next_inspection)<tr><td class="lbl">Następny przegląd</td><td class="val">{{ $car->next_inspection }}</td></tr>@endif
    @if($car->service_documentation)<tr><td class="lbl">Dokumentacja</td><td class="val">{{ $car->service_documentation }}</td></tr>@endif
    @if($car->coc_documents)<tr><td class="lbl">Dokumenty CoC</td><td class="val">{{ $car->coc_documents }}</td></tr>@endif
    @if($car->vehicle_folder)<tr><td class="lbl">Teczka pojazdu</td><td class="val">{{ $car->vehicle_folder }}</td></tr>@endif
    @if($car->hu_au_report)<tr><td class="lbl">Raport HU/AU</td><td class="val">{{ $car->hu_au_report }}</td></tr>@endif
    @if($car->service_book_status)<tr><td class="lbl">Status książki serwisowej</td><td class="val">{{ $car->service_book_status }}</td></tr>@endif
    @if($car->registration_cert)<tr><td class="lbl">Dowód rejestracyjny</td><td class="val">{{ $car->registration_cert }}</td></tr>@endif
    @if($car->owners_manual)<tr><td class="lbl">Instrukcja obsługi</td><td class="val">{{ $car->owners_manual }}</td></tr>@endif
    @if($car->aso_serviced)<tr><td class="lbl">Serwis ASO</td><td class="val">{{ $car->aso_serviced }}</td></tr>@endif
    @if($car->service_history)<tr><td class="lbl">Historia serwisowa</td><td class="val">{{ $car->service_history }}</td></tr>@endif
    @if($car->vehicle_history)<tr><td class="lbl">Historia pojazdu</td><td class="val">{{ $car->vehicle_history }}</td></tr>@endif
    @if($car->imported_from)<tr><td class="lbl">Importowany z</td><td class="val">{{ $car->imported_from }}</td></tr>@endif
</table>
@endif

{{-- FUEL CONSUMPTION & EMISSIONS --}}
@if($car->fuel_consumption || $car->co2_emission || $car->emission_class)
<div class="sh">Zużycie paliwa i emisje</div>
<table>
    @if($car->fuel_consumption)<tr><td class="lbl">Zużycie paliwa (mieszany)</td><td class="val">{{ $car->fuel_consumption }} l/100km</td></tr>@endif
    @if($car->fuel_procedure)<tr><td class="lbl">Procedura pomiaru</td><td class="val">{{ $car->fuel_procedure }}</td></tr>@endif
    @if($car->co2_emission)<tr><td class="lbl">Emisja CO₂</td><td class="val">{{ $car->co2_emission }}</td></tr>@endif
    @if($car->emission_class)<tr><td class="lbl">Klasa emisji</td><td class="val">{{ $car->emission_class }}</td></tr>@endif
</table>
@endif

{{-- ===== PAINT MEASUREMENTS ===== --}}
@if($car->paint_measurements && count($car->paint_measurements))
<div class="sh">Pomiary grubości lakieru</div>
<p style="font-size:8px;color:#9ca3af;margin:0 0 6px">Norma fabryczna: 80–150 µm · powyżej 200 µm — możliwa naprawa lakiernicza</p>
<table class="paint-tbl">
    <tr><th>Element</th><th>Grubość (µm)</th><th>Ocena</th></tr>
    @foreach($car->paint_measurements as $panel => $value)
    @php $val = is_array($value) ? ($value['value'] ?? $value[0] ?? 0) : $value; @endphp
    <tr>
        <td class="lbl">{{ $panel }}</td>
        <td class="{{ $val > 200 ? 'paint-danger' : ($val > 160 ? 'paint-warn' : 'paint-ok') }}">{{ $val }} µm</td>
        <td class="{{ $val > 200 ? 'paint-danger' : ($val > 160 ? 'paint-warn' : 'paint-ok') }}">{{ $val > 200 ? 'Naprawa' : ($val > 160 ? 'Uwaga' : 'OK') }}</td>
    </tr>
    @endforeach
</table>
@endif

{{-- ===== TECHNICAL CONDITIONS ===== --}}
@if($car->technical_conditions && count($car->technical_conditions))
<div class="sh">Ocena stanu technicznego</div>
<table>
    <tr><th>Komponent</th><th>Stan</th></tr>
    @foreach($car->technical_conditions as $comp => $status)
    @php $st = is_array($status) ? ($status['status'] ?? $status[0] ?? 'OK') : $status; @endphp
    <tr>
        <td class="lbl">{{ $comp }}</td>
        <td class="val {{ str_contains(strtolower($st),'ok') || str_contains(strtolower($st),'dobr') || str_contains(strtolower($st),'brak') ? 'cond-ok' : (str_contains(strtolower($st),'uwag') ? 'cond-warn' : '') }}">{{ $st }}</td>
    </tr>
    @endforeach
</table>
@endif

{{-- ===== TIRE CONDITION ===== --}}
@if($car->tireSets && $car->tireSets->count())
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><div>{{ $car->identifier }}</div></div>
</div>
<div class="sh">Koła i opony</div>
@foreach($car->tireSets as $set)
<p style="font-size:9px;font-weight:bold;margin:8px 0 4px">
    Komplet {{ $set->set_number }}
    @if($set->tire_type) · {{ $set->tire_type }}@endif
    @if($set->rim) · {{ $set->rim }}@endif
    @if($set->is_mounted) (zamontowane)@endif
</p>
<table class="tire-tbl">
    <tr>
        <th>Pozycja</th>
        <th>Głębokość bieżnika</th>
        <th>Stan</th>
    </tr>
    @foreach($set->tires as $tire)
    <tr>
        <td>{{ $tire->position }}</td>
        <td style="font-weight:bold">{{ $tire->tread_depth ?? '—' }}</td>
        <td>
            @if($tire->condition && count($tire->condition))
            <span class="cond-warn">{{ implode(', ', $tire->condition) }}</span>
            @else
            <span class="cond-ok">Brak nieprawidłowości</span>
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endforeach
@endif

{{-- ===== DAMAGES ===== --}}
@if($car->damages->count())
<div class="sh" style="color:#b45309;border-bottom-color:#f59e0b">Uszkodzenia pojazdu ({{ $car->damages->count() }})</div>
@foreach($car->damages as $d)
<div class="dmg">
    <strong>{{ $d->area }}</strong>
    @if($d->tags && count($d->tags)) — {{ implode(', ', $d->tags) }}@endif
    @if($d->description)<p>{{ $d->description }}</p>@endif
</div>
@endforeach
@endif

{{-- ===== EQUIPMENT ===== --}}
@if($car->equipment && count($car->equipment))
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><div>{{ $car->identifier }}</div></div>
</div>
<div class="sh">Wyposażenie pojazdu</div>
<div class="cols">
    @php
        $allItems = [];
        foreach($car->equipment as $cat => $items) {
            if(is_array($items)) foreach($items as $it) $allItems[] = $it;
        }
        $half = ceil(count($allItems)/2);
    @endphp
    <div class="col">
        <ul class="eq-list">
            @foreach(array_slice($allItems, 0, $half) as $it)
            <li>{{ $it }}</li>
            @endforeach
        </ul>
    </div>
    <div class="col">
        <ul class="eq-list">
            @foreach(array_slice($allItems, $half) as $it)
            <li>{{ $it }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- ===== PHOTO DOCUMENTATION ===== --}}
@if($car->galleryImages && $car->galleryImages->count() > 1)
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><div>{{ $car->identifier }}</div></div>
</div>
<div class="sh">Dokumentacja fotograficzna</div>
<div class="photo-grid">
    @foreach($car->galleryImages->take(8) as $img)
    <img src="{{ $img->url }}" onerror="this.style.display='none'">
    @endforeach
</div>
@endif

{{-- DAMAGE PHOTOS --}}
@if($car->damageImages && $car->damageImages->count())
<div class="sh" style="color:#b45309;border-bottom-color:#f59e0b">Zdjęcia uszkodzeń</div>
<div class="photo-grid">
    @foreach($car->damageImages->take(6) as $img)
    <img src="{{ $img->url }}" onerror="this.style.display='none'">
    @endforeach
</div>
@endif

{{-- FOOTER --}}
<div class="foot"><strong>CertiCars</strong> · certicars.pl · kontakt@certicars.pl · Raport CertiCheck wygenerowany {{ now()->format('d.m.Y H:i') }}</div>

</body>
</html>
