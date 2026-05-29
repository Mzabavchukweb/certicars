<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>{{ $car->title }} — CertiCheck Report</title>
    <style>
        @page { margin: 30px 32px 42px 32px; }
        body{font-family:'DejaVu Sans',sans-serif;color:#1a1a1a;font-size:9.5px;line-height:1.5;margin:0}

        /* ============ BRAND HEADER (every content page) ============ */
        .hd{display:table;width:100%;margin:0 0 14px;border-bottom:2px solid #0066ff;padding-bottom:10px}
        .hd-left{display:table-cell;vertical-align:middle}
        .hd-brand{font-size:15px;font-weight:bold;color:#0066ff;letter-spacing:-0.4px}
        .hd-brand span{color:#1a1a1a}
        .hd-badge{display:inline-block;background:#0066ff;color:#fff;font-size:7.5px;font-weight:bold;padding:2px 8px;border-radius:10px;letter-spacing:0.4px;text-transform:uppercase;margin-left:6px;vertical-align:1px}
        .hd-right{display:table-cell;text-align:right;vertical-align:middle;font-size:7.5px;color:#888}
        .hd-right strong{color:#1a1a1a;font-weight:bold}

        /* ============ COVER PAGE ============ */
        .cover-page{position:relative;text-align:left}
        .cover-brand{margin-bottom:18px;padding-bottom:14px;border-bottom:3px solid #0066ff}
        .cover-brand .cover-brand-name{font-size:22px;font-weight:bold;color:#0066ff;letter-spacing:-0.6px}
        .cover-brand .cover-brand-name span{color:#1a1a1a}
        .cover-brand-badge{display:inline-block;background:#0066ff;color:#fff;font-size:9px;font-weight:bold;padding:3px 10px;border-radius:12px;letter-spacing:0.5px;text-transform:uppercase;margin-left:10px;vertical-align:3px}
        .cover-brand-meta{float:right;text-align:right;font-size:8px;color:#6b7280;padding-top:6px}
        .cover-brand-meta strong{color:#1a1a1a;font-size:9px;display:block;margin-bottom:1px}
        .cover-hero{position:relative;height:330px;background:#f0f0f2;border-radius:10px;overflow:hidden;margin-bottom:18px}
        .cover-hero img{width:100%;height:100%;object-fit:cover;display:block}
        .cover-hero-placeholder{position:absolute;inset:0;display:table;width:100%;height:100%;background:linear-gradient(135deg,#0a0a0a,#1a1a2e)}
        .cover-hero-placeholder span{display:table-cell;vertical-align:middle;text-align:center;color:rgba(255,255,255,.5);font-size:11px;letter-spacing:0.3px}
        .cover-title{font-size:22px;font-weight:bold;color:#0a0a0a;letter-spacing:-0.5px;margin:0 0 4px}
        .cover-meta{font-size:10px;color:#6b7280;margin-bottom:16px}

        /* Key facts row on cover */
        .cover-kf{display:table;width:100%;background:#f5f8ff;border:1px solid #dbeafe;border-radius:8px;padding:12px 0;margin-bottom:14px}
        .cover-kf-item{display:table-cell;text-align:center;padding:4px 6px;border-right:1px solid #e3ecff}
        .cover-kf-item:last-child{border-right:none}
        .cover-kf-val{font-size:13px;font-weight:bold;color:#0a0a0a;display:block;letter-spacing:-0.2px}
        .cover-kf-lbl{font-size:7.5px;color:#6b7280;text-transform:uppercase;letter-spacing:0.35px;margin-top:2px;display:block}

        /* Price block on cover */
        .cover-price{display:table;width:100%;background:#0a0a0a;color:#fff;padding:14px 18px;border-radius:8px;margin-bottom:14px}
        .cover-price .lbl{display:table-cell;font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;vertical-align:middle}
        .cover-price .val{display:table-cell;text-align:right;font-size:22px;font-weight:bold;vertical-align:middle;letter-spacing:-0.4px}

        /* Cover intro / overview */
        .cover-intro{background:#f8fafc;border-left:3px solid #0066ff;padding:10px 14px;font-size:9px;line-height:1.55;color:#374151;margin-bottom:8px;border-radius:0 6px 6px 0}
        .cover-intro strong{color:#0a0a0a}

        /* ============ SECTION HEADERS ============ */
        .sh{font-size:13px;font-weight:bold;color:#0066ff;margin:16px 0 8px;padding-bottom:5px;border-bottom:1.5px solid #e5e7eb;page-break-after:avoid}
        .sh-warn{color:#b45309;border-bottom-color:#f59e0b}
        .sh-sub{font-size:9px;color:#9ca3af;font-weight:normal;margin:-6px 0 8px;page-break-after:avoid}

        /* ============ DATA TABLES ============ */
        table{width:100%;border-collapse:collapse;font-size:9px;margin-bottom:10px}
        td,th{padding:5px 8px;border-bottom:1px solid #f0f0f2;text-align:left;vertical-align:top}
        td.lbl{color:#6b7280;width:42%}
        td.val{font-weight:bold;color:#1a1a1a;text-align:right;word-break:break-word}
        th{background:#f9fafb;font-weight:bold;color:#374151;font-size:8px;text-transform:uppercase;letter-spacing:0.4px}

        /* Two/three column layouts */
        .cols{display:table;width:100%;margin-bottom:8px;table-layout:fixed}
        .col{display:table-cell;width:50%;vertical-align:top;padding-right:10px}
        .col:last-child{padding-right:0;padding-left:10px}

        /* ============ DAMAGE CARD (per-damage with photo) ============ */
        .dmg-card{background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;margin-bottom:8px;page-break-inside:avoid}
        .dmg-card-head{display:table;width:100%;margin-bottom:6px}
        .dmg-card-area{display:table-cell;color:#92400e;font-size:10.5px;font-weight:bold}
        .dmg-card-type{display:table-cell;text-align:right;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;color:#b45309;font-weight:bold;vertical-align:middle}
        .dmg-card-tags{font-size:8px;color:#92400e;margin-bottom:5px}
        .dmg-card-desc{font-size:8.5px;color:#57534e;line-height:1.55;margin:0}
        .dmg-card-photos{margin-top:8px;width:100%}
        .dmg-card-photos img{width:32%;max-height:120px;object-fit:cover;border-radius:4px;margin-right:1%;display:inline-block;border:1px solid #f5e8c5}

        /* Damage list when no photos */
        .dmg-text{background:#fffbeb;border-left:3px solid #f59e0b;padding:7px 12px;margin-bottom:5px;border-radius:0 4px 4px 0;page-break-inside:avoid}
        .dmg-text strong{color:#92400e;font-size:9.5px}
        .dmg-text p{margin:3px 0 0;color:#78716c;font-size:8.5px}

        /* ============ PAINT MEASUREMENTS ============ */
        .paint-tbl td{text-align:center;font-weight:bold}
        .paint-tbl th{text-align:center}
        .paint-ok{color:#16a34a;background:#f0fdf4}
        .paint-warn{color:#d97706;background:#fffbeb}
        .paint-danger{color:#dc2626;background:#fef2f2}
        .paint-legend{display:table;width:100%;margin:6px 0 12px;font-size:7.5px;color:#6b7280}
        .paint-legend-item{display:table-cell;text-align:center}
        .paint-legend-swatch{display:inline-block;width:8px;height:8px;border-radius:2px;margin-right:4px;vertical-align:-1px}

        /* ============ CONDITION CELL COLORS ============ */
        /* cond-bad and cond-fail both used (cond-bad emitted by CarLabels::tireCondition,
           cond-fail by older inline maps). Map both to the same red so legacy view stays
           in lockstep with the browser-rendered version. */
        .cond-ok{color:#16a34a;font-weight:bold}
        .cond-warn{color:#d97706;font-weight:bold}
        .cond-fail,.cond-bad{color:#dc2626;font-weight:bold}

        /* ============ EQUIPMENT LIST ============ */
        .eq-list{list-style:none;padding:0;margin:0}
        .eq-list li{padding:3px 0;font-size:8.5px;color:#374151;line-height:1.4}
        .eq-list li:before{content:'• ';color:#0066ff;font-weight:bold}
        .eq-cat-title{font-size:9px;font-weight:bold;color:#0066ff;margin:10px 0 4px;text-transform:uppercase;letter-spacing:0.3px;page-break-after:avoid}

        /* ============ TIRE TABLE ============ */
        .tire-tbl{width:100%;border-collapse:collapse;font-size:9px;margin-bottom:10px}
        .tire-tbl td,.tire-tbl th{padding:6px 8px;border:1px solid #e5e7eb;text-align:center}
        .tire-tbl th{background:#f3f4f6;font-size:7.5px;text-transform:uppercase;letter-spacing:0.3px;color:#374151}
        .tire-set-title{font-size:9.5px;font-weight:bold;margin:10px 0 6px;color:#1a1a1a}

        /* ============ PHOTO GRID (paginated) ============ */
        .photo-grid{width:100%;margin-bottom:8px}
        .photo-grid-row{width:100%;margin-bottom:6px}
        .photo-grid-row img{width:32.3%;height:130px;object-fit:cover;border-radius:5px;margin-right:1%;display:inline-block;vertical-align:top;border:1px solid #f0f0f2}
        .photo-grid-row img:last-child{margin-right:0}

        /* ============ MEDIA INFO CARDS ============ */
        .media-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 14px;margin-bottom:8px;page-break-inside:avoid}
        .media-card-head{font-size:10px;font-weight:bold;color:#0a0a0a;margin-bottom:4px}
        .media-card-status{display:inline-block;background:#dcfce7;color:#15803d;font-size:7.5px;font-weight:bold;padding:2px 7px;border-radius:8px;letter-spacing:0.3px;text-transform:uppercase;margin-bottom:6px}
        .media-card-url{font-size:8.5px;color:#0066ff;word-break:break-all;font-family:'DejaVu Sans',sans-serif}
        .media-card-hint{font-size:7.5px;color:#9ca3af;margin-top:4px}

        /* ============ EMPTY STATE (small inline) ============ */
        .empty-state{font-size:8.5px;color:#9ca3af;font-style:italic;padding:4px 8px}

        /* ============ FOOTER ============ */
        .foot{position:fixed;bottom:-28px;left:0;right:0;text-align:center;font-size:7px;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:5px}
        .foot strong{color:#0066ff}

        /* ============ PAGE BREAK CONTROL ============ */
        .pb{page-break-before:always}
        .keep{page-break-inside:avoid}
        .no-break-after{page-break-after:avoid}
        table,.dmg-card,.dmg-text,.media-card{page-break-inside:avoid}
    </style>
</head>
<body>

{{-- =============== PAGE 1 — COVER =============== --}}
<div class="cover-page">
    <div class="cover-brand">
        <div style="display:table;width:100%">
            <div style="display:table-cell;vertical-align:bottom">
                <span class="cover-brand-name">Certi<span>Cars</span></span>
                <span class="cover-brand-badge">CertiCheck Report</span>
            </div>
            <div class="cover-brand-meta">
                <strong>{{ $car->identifier }}</strong>
                <span>Wygenerowano {{ now()->format('d.m.Y') }}</span>
            </div>
        </div>
    </div>

    {{-- HERO IMAGE — only renders when a real image was embedded by the
         PdfImageEmbedder. Skipping cleanly is FAR better than rendering a
         330px-tall gray "Brak zdjęcia" block on the cover page. --}}
    @if($car->primaryImage && !empty($car->primaryImage->pdf_src))
    <div class="cover-hero">
        <img src="{{ $car->primaryImage->pdf_src }}" alt="{{ $car->title }}">
    </div>
    @endif

    <div class="cover-title">{{ $car->title }}</div>
    <div class="cover-meta">
        @if($car->first_registration){{ $car->first_registration }}@endif
        @if($car->mileage) · {{ number_format($car->mileage,0,'',' ') }} km @endif
        @if($car->fuel_type) · {{ $car->fuel_type }}@endif
        @if($car->transmission) · {{ $car->transmission_detail ?? $car->transmission }}@endif
    </div>

    <div class="cover-kf">
        @if($car->mileage)<div class="cover-kf-item"><span class="cover-kf-val">{{ number_format($car->mileage,0,'',' ') }} km</span><span class="cover-kf-lbl">Przebieg</span></div>@endif
        @if($car->first_registration)<div class="cover-kf-item"><span class="cover-kf-val">{{ $car->first_registration }}</span><span class="cover-kf-lbl">Rejestracja</span></div>@endif
        @if($car->power_hp)<div class="cover-kf-item"><span class="cover-kf-val">{{ $car->power_hp }} KM</span><span class="cover-kf-lbl">Moc</span></div>@endif
        @if($car->engine_capacity)<div class="cover-kf-item"><span class="cover-kf-val">{{ number_format($car->engine_capacity,0,'',' ') }}</span><span class="cover-kf-lbl">cm³</span></div>@endif
        @if($car->doors)<div class="cover-kf-item"><span class="cover-kf-val">{{ $car->doors }}/{{ $car->seats ?? '—' }}</span><span class="cover-kf-lbl">Drzwi / miejsc</span></div>@endif
    </div>

    @if($car->price)
    <div class="cover-price">
        <span class="lbl">Cena sprzedaży</span>
        <span class="val">{{ $car->formatted_price }}</span>
    </div>
    @endif

    <div class="cover-intro">
        Niniejszy raport <strong>CertiCheck</strong> dokumentuje stan techniczny, wizualny i historię pojazdu na dzień inspekcji.
        Zawiera szczegółową specyfikację, ocenę stanu, pomiary lakieru, listę uszkodzeń wraz z dokumentacją fotograficzną
        oraz pełne wyposażenie. Wszystkie informacje pochodzą z naszej weryfikacji.
    </div>
</div>

{{-- =============== PAGE 2 — DANE POJAZDU =============== --}}
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · {{ $car->title }}</div>
</div>

<div class="sh">Dane pojazdu</div>
<div class="cols">
    <div class="col">
        <table>
            @if($car->brand?->name)<tr><td class="lbl">Marka</td><td class="val">{{ $car->brand->name }}</td></tr>@endif
            @if($car->model)<tr><td class="lbl">Model</td><td class="val">{{ $car->model }}</td></tr>@endif
            @if($car->first_registration)<tr><td class="lbl">Pierwsza rejestracja</td><td class="val">{{ $car->first_registration }}</td></tr>@endif
            @if($car->mileage)<tr><td class="lbl">Przebieg</td><td class="val">{{ number_format($car->mileage,0,'',' ') }} km</td></tr>@endif
            @if($car->vin)<tr><td class="lbl">VIN</td><td class="val">{{ $car->vin }}</td></tr>@endif
            @if($car->body_type)<tr><td class="lbl">Typ nadwozia</td><td class="val">{{ $car->body_type }}</td></tr>@endif
            @if($car->color)<tr><td class="lbl">Kolor</td><td class="val">{{ $car->color }}@if($car->color_code) ({{ $car->color_code }})@endif</td></tr>@endif
            @if($car->upholstery)<tr><td class="lbl">Tapicerka</td><td class="val">{{ $car->upholstery }}</td></tr>@endif
        </table>
    </div>
    <div class="col">
        <table>
            @if($car->fuel_type)<tr><td class="lbl">Paliwo</td><td class="val">{{ $car->fuel_type }}</td></tr>@endif
            @if($car->engine_capacity)<tr><td class="lbl">Pojemność silnika</td><td class="val">{{ number_format($car->engine_capacity,0,'',' ') }} cm³</td></tr>@endif
            @if($car->power_hp)<tr><td class="lbl">Moc</td><td class="val">{{ $car->power_hp }} KM @if($car->power_kw)({{ $car->power_kw }} kW)@endif</td></tr>@endif
            @if($car->transmission)<tr><td class="lbl">Skrzynia biegów</td><td class="val">{{ $car->transmission_detail ?? $car->transmission }}</td></tr>@endif
            @if($car->drive_type)<tr><td class="lbl">Napęd</td><td class="val">{{ $car->drive_type }}</td></tr>@endif
            @if($car->doors)<tr><td class="lbl">Drzwi / Miejsca</td><td class="val">{{ $car->doors }} / {{ $car->seats ?? '—' }}</td></tr>@endif
            @if($car->weight)<tr><td class="lbl">Masa własna</td><td class="val">{{ number_format($car->weight,0,'',' ') }} kg</td></tr>@endif
            @if($car->number_of_keys)<tr><td class="lbl">Liczba kluczyków</td><td class="val">{{ $car->number_of_keys }}</td></tr>@endif
        </table>
    </div>
</div>

{{-- HISTORY & ORIGIN --}}
@if($car->previous_owners !== null || $car->imported_from || $car->country_registration || $car->vehicle_history)
<div class="sh">Historia i pochodzenie</div>
<table>
    @if($car->previous_owners !== null)<tr><td class="lbl">Poprzedni właściciele</td><td class="val">{{ $car->previous_owners == 0 ? 'Pierwszy właściciel' : $car->previous_owners }}</td></tr>@endif
    @if($car->imported_from)<tr><td class="lbl">Importowany z</td><td class="val">{{ $car->imported_from }}</td></tr>@endif
    @if($car->country_registration)<tr><td class="lbl">Kraj rejestracji</td><td class="val">{{ $car->country_registration }}</td></tr>@endif
    @if($car->vehicle_history)<tr><td class="lbl">Historia pojazdu</td><td class="val">{{ $car->vehicle_history }}</td></tr>@endif
</table>
@endif

{{-- SERVICE & DOCUMENTATION --}}
@if($car->service_book || $car->last_service || $car->next_inspection || $car->service_documentation || $car->coc_documents || $car->vehicle_folder || $car->hu_au_report || $car->service_book_status || $car->registration_cert || $car->owners_manual || $car->aso_serviced || $car->service_history)
<div class="sh">Serwis i dokumentacja</div>
<div class="cols">
    <div class="col">
        <table>
            @if($car->service_book)<tr><td class="lbl">Książka serwisowa</td><td class="val">{{ $car->service_book }}</td></tr>@endif
            @if($car->last_service)<tr><td class="lbl">Ostatni serwis</td><td class="val">{{ $car->last_service }}@if($car->last_service_mileage) ({{ number_format($car->last_service_mileage,0,'',' ') }} km)@endif</td></tr>@endif
            @if($car->next_inspection)<tr><td class="lbl">Następny przegląd</td><td class="val">{{ $car->next_inspection }}</td></tr>@endif
            @if($car->aso_serviced)<tr><td class="lbl">Serwis ASO</td><td class="val">{{ $car->aso_serviced }}</td></tr>@endif
            @if($car->service_history)<tr><td class="lbl">Historia serwisowa</td><td class="val">{{ $car->service_history }}</td></tr>@endif
        </table>
    </div>
    <div class="col">
        <table>
            @if($car->coc_documents)<tr><td class="lbl">Dokumenty CoC</td><td class="val">{{ $car->coc_documents }}</td></tr>@endif
            @if($car->vehicle_folder)<tr><td class="lbl">Teczka pojazdu</td><td class="val">{{ $car->vehicle_folder }}</td></tr>@endif
            @if($car->hu_au_report)<tr><td class="lbl">Raport HU/AU</td><td class="val">{{ $car->hu_au_report }}</td></tr>@endif
            @if($car->service_book_status)<tr><td class="lbl">Status książki</td><td class="val">{{ $car->service_book_status }}</td></tr>@endif
            @if($car->registration_cert)<tr><td class="lbl">Dowód rejestracyjny</td><td class="val">{{ $car->registration_cert }}</td></tr>@endif
            @if($car->owners_manual)<tr><td class="lbl">Instrukcja obsługi</td><td class="val">{{ $car->owners_manual }}</td></tr>@endif
        </table>
    </div>
</div>
@endif

{{-- FUEL & EMISSIONS --}}
@php
    $fcRawPdf = $car->fuel_consumption ? trim(str_replace(',', '.', trim(preg_replace('/\s*[lL]\s*\/\s*100\s*km\.?/u', '', (string) $car->fuel_consumption)))) : null;
    $emissionClassPdf = $car->emission_class ? preg_replace('/^(euro)\s*(\d.*)$/i', 'Euro $2', trim((string) $car->emission_class)) : null;
    // Strip any embedded "g/km" the admin may have typed so we don't render "154 g/km g/km".
    $co2RawPdf = $car->co2_emission ? trim(preg_replace('/\s*g\s*\/\s*km\.?/iu', '', (string) $car->co2_emission)) : null;
@endphp
@if($fcRawPdf || $co2RawPdf || $emissionClassPdf || $car->fuel_procedure)
<div class="sh">Zużycie paliwa i emisje</div>
<table>
    @if($fcRawPdf)<tr><td class="lbl">Zużycie paliwa (cykl mieszany)</td><td class="val">{{ $fcRawPdf }} l/100 km</td></tr>@endif
    @if($car->fuel_procedure)<tr><td class="lbl">Procedura pomiaru</td><td class="val">{{ $car->fuel_procedure }}</td></tr>@endif
    @if($co2RawPdf)<tr><td class="lbl">Emisja CO₂</td><td class="val">{{ $co2RawPdf }} g/km</td></tr>@endif
    @if($emissionClassPdf)<tr><td class="lbl">Klasa emisji</td><td class="val">{{ $emissionClassPdf }}</td></tr>@endif
</table>
@endif

{{-- =============== PAGE 3 — STAN TECHNICZNY =============== --}}
@if(($car->paint_measurements && count($car->paint_measurements)) || ($car->technical_conditions && count($car->technical_conditions)))
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Stan techniczny</div>
</div>

{{-- PAINT MEASUREMENTS --}}
@if($car->paint_measurements && count($car->paint_measurements))
<div class="sh">Pomiary grubości lakieru</div>
<div class="sh-sub">Norma fabryczna: 80–150 µm · powyżej 200 µm — możliwa naprawa lakiernicza</div>
<table class="paint-tbl">
    <tr><th>Element</th><th>Grubość (µm)</th><th>Ocena</th></tr>
    @php
        $paintPanelNames = [
            0 => 'Dach', 1 => 'Maska', 2 => 'Błotnik P-L', 3 => 'Błotnik P-P',
            4 => 'Drzwi P-L', 5 => 'Drzwi P-P', 6 => 'Błotnik T-L', 7 => 'Błotnik T-P',
            8 => 'Drzwi T-L', 9 => 'Drzwi T-P', 10 => 'Klapa bagażnika',
            11 => 'Zderzak przód', 12 => 'Zderzak tył', 13 => 'Próg lewy', 14 => 'Próg prawy',
        ];
    @endphp
    @foreach($car->paint_measurements as $panel => $value)
    @php
        $val = is_array($value) ? ($value['value'] ?? $value[0] ?? 0) : $value;
        $numVal = (int) preg_replace('/[^0-9]/', '', (string) $val);
        if ($numVal <= 0) continue;
        $panelLabel = is_array($value) && isset($value['area'])
            ? $value['area']
            : (is_numeric($panel) ? ($paintPanelNames[$panel] ?? 'Panel '.($panel + 1)) : $panel);
        $cls = $numVal > 200 ? 'paint-danger' : ($numVal > 160 ? 'paint-warn' : 'paint-ok');
        $verdict = $numVal > 200 ? 'Naprawa' : ($numVal > 160 ? 'Uwaga' : 'OK');
    @endphp
    <tr>
        <td class="lbl" style="text-align:left">{{ $panelLabel }}</td>
        <td class="{{ $cls }}">{{ $numVal }} µm</td>
        <td class="{{ $cls }}">{{ $verdict }}</td>
    </tr>
    @endforeach
</table>
<div class="paint-legend">
    <div class="paint-legend-item"><span class="paint-legend-swatch" style="background:#16a34a"></span>OK (≤ 160 µm)</div>
    <div class="paint-legend-item"><span class="paint-legend-swatch" style="background:#d97706"></span>Uwaga (161–200 µm)</div>
    <div class="paint-legend-item"><span class="paint-legend-swatch" style="background:#dc2626"></span>Naprawa (> 200 µm)</div>
</div>
@endif

{{-- TECHNICAL CONDITIONS --}}
@if($car->technical_conditions && count($car->technical_conditions))
<div class="sh">Ocena stanu technicznego</div>
<table>
    <tr><th>Komponent</th><th style="text-align:right">Stan</th></tr>
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
    @foreach($car->technical_conditions as $comp => $status)
    @php
        $resolved = \App\Helpers\CarLabels::techStatus($status);
        $compLabel = $techLabels[strtolower($comp)] ?? ucfirst($comp);
        $clsMap = ['ok' => 'cond-ok', 'attention' => 'cond-warn', 'bad' => 'cond-fail'];
        $cls = $clsMap[$resolved['status']] ?? '';
        $cellText = $resolved['label'];
        if (!empty($resolved['note'])) {
            $cellText .= ' — ' . $resolved['note'];
        }
    @endphp
    <tr>
        <td class="lbl" style="text-align:left">{{ $compLabel }}</td>
        <td class="val {{ $cls }}">{{ $cellText }}</td>
    </tr>
    @endforeach
</table>
@endif
@endif

{{-- =============== TIRES =============== --}}
@if($car->tireSets && $car->tireSets->count())
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Koła i opony</div>
</div>
<div class="sh">Koła i opony</div>
<div class="sh-sub">Pomiar głębokości bieżnika i ocena stanu poszczególnych opon w każdym komplecie.</div>
@foreach($car->tireSets as $set)
<div class="tire-set-title">
    Komplet {{ $set->set_number ?? $loop->iteration }}
    @if($set->tire_type) · {{ $set->tire_type }}@endif
    @if($set->rim) · {{ $set->rim }}@endif
    @if($set->is_mounted) (zamontowane){{ ' ' }}@endif
</div>
<table class="tire-tbl">
    <tr>
        <th>Pozycja</th>
        <th>Bieżnik</th>
        <th>Stan</th>
    </tr>
    {{-- Tire position + condition labels are owned by App\Helpers\CarLabels
         so the browser-rendered brochure-browser.blade.php and this DomPDF
         fallback always render the same client-facing strings. Raw enum
         keys (front_left) and slang (zajebiste) never reach the PDF. --}}
    @foreach($set->tires as $tire)
    @php $cond = \App\Helpers\CarLabels::tireCondition($tire->condition); @endphp
    <tr>
        <td>{{ \App\Helpers\CarLabels::tirePosition($tire->position) }}</td>
        <td style="font-weight:bold">{{ $tire->tread_depth !== null ? number_format((float) $tire->tread_depth, 1, ',', ' ') . ' mm' : '—' }}</td>
        <td><span class="{{ $cond['class'] }}">{{ $cond['label'] }}</span></td>
    </tr>
    @endforeach
</table>
@endforeach
@endif

{{-- =============== DAMAGES — per-damage with photos =============== --}}
@if($car->damages->count())
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Stan wizualny</div>
</div>
<div class="sh sh-warn">Stan wizualny i ślady użytkowania ({{ $car->damages->count() }})</div>
<div class="sh-sub">Lista udokumentowanych śladów eksploatacji oraz uszkodzeń wraz ze zdjęciami referencyjnymi.</div>

@foreach($car->damages as $d)
@php
    $dmgPhotos = [];
    if (!empty($d->pdf_image_src)) $dmgPhotos[] = $d->pdf_image_src;
    if ($d->photos) {
        foreach ($d->photos as $dp) {
            if (!empty($dp->pdf_src) && !in_array($dp->pdf_src, $dmgPhotos)) $dmgPhotos[] = $dp->pdf_src;
        }
    }
    $typeLabel = match(strtolower((string) $d->type)) {
        'accident'  => 'Wypadek',
        'repaired'  => 'Naprawione',
        default     => 'Uszkodzenie',
    };
@endphp
@if(count($dmgPhotos))
<div class="dmg-card">
    <div class="dmg-card-head">
        <span class="dmg-card-area">{{ $d->area }}</span>
        <span class="dmg-card-type">{{ $typeLabel }}@if($d->severity) · {{ $d->severity }}@endif</span>
    </div>
    @if($d->tags && count($d->tags))<div class="dmg-card-tags">{{ implode(' · ', $d->tags) }}</div>@endif
    @if($d->description)<p class="dmg-card-desc">{{ $d->description }}</p>@endif
    <div class="dmg-card-photos">
        @foreach(array_slice($dmgPhotos, 0, 3) as $pSrc)
            <img src="{{ $pSrc }}" alt="{{ $d->area }}">
        @endforeach
    </div>
</div>
@else
<div class="dmg-text">
    <strong>{{ $d->area }}</strong>
    <span style="color:#b45309;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;margin-left:6px">{{ $typeLabel }}</span>
    @if($d->tags && count($d->tags)) — {{ implode(', ', $d->tags) }}@endif
    @if($d->description)<p>{{ $d->description }}</p>@endif
</div>
@endif
@endforeach
@endif

{{-- =============== EQUIPMENT =============== --}}
@if($car->equipment && count($car->equipment))
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Wyposażenie</div>
</div>
<div class="sh">Wyposażenie pojazdu</div>
<div class="sh-sub">Pełna lista wyposażenia fabrycznego i opcjonalnego potwierdzona przy inspekcji.</div>
@php
    $equipLabels = [
        'safety' => 'Bezpieczeństwo', 'comfort' => 'Komfort',
        'multimedia' => 'Multimedia', 'exterior' => 'Wygląd zewnętrzny',
        'interior' => 'Wnętrze', 'driving' => 'Wspomaganie jazdy', 'other' => 'Inne',
    ];
@endphp
<div class="cols">
    @php
        // Render category-grouped list, split roughly in half across two columns.
        $categories = [];
        foreach ($car->equipment as $cat => $items) {
            if (is_array($items) && count($items)) {
                $categories[] = ['title' => $equipLabels[$cat] ?? ucfirst((string) $cat), 'items' => $items];
            }
        }
        $halfMark = (int) ceil(count($categories) / 2);
    @endphp
    <div class="col">
        @foreach(array_slice($categories, 0, $halfMark) as $cat)
            <div class="eq-cat-title">{{ $cat['title'] }}</div>
            <ul class="eq-list">@foreach($cat['items'] as $it)<li>{{ $it }}</li>@endforeach</ul>
        @endforeach
    </div>
    <div class="col">
        @foreach(array_slice($categories, $halfMark) as $cat)
            <div class="eq-cat-title">{{ $cat['title'] }}</div>
            <ul class="eq-list">@foreach($cat['items'] as $it)<li>{{ $it }}</li>@endforeach</ul>
        @endforeach
    </div>
</div>
@endif

{{-- =============== PHOTO DOCUMENTATION (multi-page, 6/page) =============== --}}
@php
    // PdfController decorated every CarImage with pdf_src — skip un-embeddable ones
    // so the grid doesn't carry stale empty <img> stubs.
    $galleryEmbeddable = $car->galleryImages?->filter(fn($i) => !empty($i->pdf_src))->values() ?? collect();
    $galleryChunks     = $galleryEmbeddable->chunk(6);
    $damageEmbeddable  = $car->damageImages?->filter(fn($i) => !empty($i->pdf_src))->values() ?? collect();
    $damageChunks      = $damageEmbeddable->chunk(6);
@endphp

@foreach($galleryChunks as $chunkIdx => $chunk)
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Dokumentacja fotograficzna</div>
</div>
@if($chunkIdx === 0)
<div class="sh">Dokumentacja fotograficzna</div>
<div class="sh-sub">{{ $galleryEmbeddable->count() }} zdjęć udokumentowanych podczas inspekcji.</div>
@else
<div class="sh">Dokumentacja fotograficzna (cd.)</div>
@endif
<div class="photo-grid">
    @foreach($chunk->chunk(3) as $row)
    <div class="photo-grid-row">
        @foreach($row as $img)<img src="{{ $img->pdf_src }}">@endforeach
    </div>
    @endforeach
</div>
@endforeach

@foreach($damageChunks as $chunkIdx => $chunk)
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Zdjęcia uszkodzeń</div>
</div>
@if($chunkIdx === 0)
<div class="sh sh-warn">Zdjęcia uszkodzeń</div>
<div class="sh-sub">Dodatkowa dokumentacja fotograficzna śladów eksploatacji.</div>
@else
<div class="sh sh-warn">Zdjęcia uszkodzeń (cd.)</div>
@endif
<div class="photo-grid">
    @foreach($chunk->chunk(3) as $row)
    <div class="photo-grid-row">
        @foreach($row as $img)<img src="{{ $img->pdf_src }}">@endforeach
    </div>
    @endforeach
</div>
@endforeach

{{-- =============== MEDIA & LINKS — engine video + 360 =============== --}}
@php
    $engineVideoUrl = $car->engine_video_url ?: ($car->engine_video_path ? url('/samochody/' . $car->slug) : null);
    $hasPano = $car->pano360Image || $car->exteriorPano360Image;
@endphp
@if($engineVideoUrl || $hasPano)
<div class="pb"></div>
<div class="hd">
    <div class="hd-left"><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Materiały dodatkowe</div>
</div>
<div class="sh">Materiały online</div>
<div class="sh-sub">Pełna dokumentacja wideo i panoramy 360° dostępne w wersji online raportu.</div>

@if($engineVideoUrl)
<div class="media-card">
    <div class="media-card-head">Nagranie pracy silnika</div>
    <div class="media-card-status">Dostępne online</div>
    <div class="media-card-url">{{ $engineVideoUrl }}</div>
    <div class="media-card-hint">Otwórz powyższy adres w przeglądarce, aby odtworzyć nagranie pracy silnika.</div>
</div>
@endif

@if($car->exteriorPano360Image)
<div class="media-card">
    <div class="media-card-head">Widok zewnętrzny 360°</div>
    <div class="media-card-status">Dostępne online</div>
    <div class="media-card-url">{{ url('/samochody/' . $car->slug) }}</div>
    <div class="media-card-hint">Interaktywna panorama zewnętrzna dostępna na stronie ogłoszenia.</div>
</div>
@endif

@if($car->pano360Image)
<div class="media-card">
    <div class="media-card-head">Widok wnętrza 360°</div>
    <div class="media-card-status">Dostępne online</div>
    <div class="media-card-url">{{ url('/samochody/' . $car->slug) }}</div>
    <div class="media-card-hint">Interaktywna panorama wnętrza dostępna na stronie ogłoszenia.</div>
</div>
@endif
@endif

{{-- =============== FOOTER (every page) =============== --}}
<div class="foot"><strong>CertiCars</strong> · certicars.pl · kontakt@certicars.pl · Raport CertiCheck wygenerowany {{ now()->format('d.m.Y H:i') }}</div>

{{-- =============== PAGE NUMBERS (DomPDF callback) =============== --}}
<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_text(498, 818, "Strona {PAGE_NUM} / {PAGE_COUNT}", $fontMetrics->getFont('DejaVu Sans'), 7, [0.6, 0.6, 0.6]);
    }
</script>

</body>
</html>
