{{--
    Browser-rendered CertiCheck brochure.

    This view is consumed by spatie/browsershot (headless Chromium). It assumes
    each photo has been pre-validated by the PdfController; missing images are
    NEVER rendered as empty <img> stubs (no broken-image icons, no gray
    placeholder blocks).

    The companion DomPDF view `pdf.brochure` still exists as a fallback path —
    keep the two visually consistent if you change brand colours / section
    structure.
--}}
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>{{ $car->title }} — CertiCheck Report</title>
<style>
    /* Chromium honours @page properly — page numbers come for free, no PHP
       callback needed. Keep margins close to the DomPDF version so the brand
       footprint matches if a customer downloads both. */
    @page {
        size: A4;
        margin: 14mm 10mm 16mm 10mm;
    }
    @page :first { margin: 0; }

    *, *::before, *::after { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; }
    body {
        font-family: 'Inter', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        color: #1a1a1a;
        font-size: 10.5px;
        line-height: 1.55;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* ============ Brand header (every page after the cover) ============ */
    .hd {
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 2px solid #0066ff; padding-bottom: 8px; margin-bottom: 14px;
    }
    .hd-brand { font-size: 15px; font-weight: 800; color: #0066ff; letter-spacing: -.4px; }
    .hd-brand span { color: #1a1a1a; }
    .hd-badge {
        display: inline-block; background: #0066ff; color: #fff; font-size: 8px;
        font-weight: 800; padding: 2px 8px; border-radius: 10px; letter-spacing: .4px;
        text-transform: uppercase; margin-left: 6px; vertical-align: 2px;
    }
    .hd-right { text-align: right; font-size: 8.5px; color: #6b7280; }
    .hd-right strong { color: #1a1a1a; font-weight: 700; }

    /* ============ Cover (page 1) — full-bleed, no header ============ */
    .cover {
        page-break-after: always;
        min-height: 297mm; padding: 18mm 16mm 14mm; display: flex; flex-direction: column;
        background: #ffffff;
    }
    .cover-brand-row {
        display: flex; align-items: flex-end; justify-content: space-between;
        padding-bottom: 12px; border-bottom: 3px solid #0066ff; margin-bottom: 18px;
    }
    .cover-brand { font-size: 24px; font-weight: 800; color: #0066ff; letter-spacing: -.6px; }
    .cover-brand span { color: #1a1a1a; }
    .cover-brand-badge {
        display: inline-block; background: #0066ff; color: #fff; font-size: 9px;
        font-weight: 800; padding: 3px 10px; border-radius: 12px;
        letter-spacing: .5px; text-transform: uppercase; margin-left: 10px; vertical-align: 4px;
    }
    .cover-meta-stack { text-align: right; font-size: 9px; color: #6b7280; }
    .cover-meta-stack strong { display: block; color: #1a1a1a; font-size: 10px; font-weight: 700; margin-bottom: 2px; }

    .cover-hero {
        width: 100%; height: 110mm; border-radius: 10px; overflow: hidden;
        background: linear-gradient(135deg, #0a0a0a, #1a1a2e); margin-bottom: 16px;
    }
    .cover-hero img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .cover-title { font-size: 24px; font-weight: 800; color: #0a0a0a; letter-spacing: -.5px; margin: 0 0 4px; }
    .cover-sub { font-size: 11px; color: #6b7280; margin-bottom: 14px; }

    .cover-kf {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
        background: #f5f8ff; border: 1px solid #dbeafe; border-radius: 8px;
        padding: 12px 0; margin-bottom: 14px;
    }
    .cover-kf-item { text-align: center; padding: 0 8px; border-right: 1px solid #e3ecff; }
    .cover-kf-item:last-child { border-right: none; }
    .cover-kf-val { display: block; font-size: 13px; font-weight: 800; color: #0a0a0a; letter-spacing: -.2px; }
    .cover-kf-lbl { display: block; font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: .35px; margin-top: 2px; }

    .cover-price {
        display: flex; align-items: center; justify-content: space-between;
        background: #0a0a0a; color: #fff; padding: 14px 18px; border-radius: 8px; margin-bottom: 14px;
    }
    .cover-price .lbl { font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: .5px; }
    .cover-price .val { font-size: 22px; font-weight: 800; letter-spacing: -.4px; }

    .cover-intro {
        background: #f8fafc; border-left: 3px solid #0066ff;
        padding: 10px 14px; font-size: 10px; line-height: 1.6; color: #374151;
        margin-top: auto; border-radius: 0 6px 6px 0;
    }
    .cover-intro strong { color: #0a0a0a; }

    /* ============ Section headers ============ */
    .sh { font-size: 13px; font-weight: 800; color: #0066ff; margin: 16px 0 6px;
          padding-bottom: 5px; border-bottom: 1.5px solid #e5e7eb; break-after: avoid-page; }
    .sh-warn { color: #b45309; border-bottom-color: #f59e0b; }
    .sh-sub  { font-size: 9.5px; color: #9ca3af; margin: -4px 0 8px; break-after: avoid-page; }

    /* ============ Generic data tables ============ */
    table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 10px; }
    td, th { padding: 6px 10px; border-bottom: 1px solid #f0f0f2; text-align: left; vertical-align: top; }
    td.lbl { color: #6b7280; width: 42%; }
    td.val { font-weight: 700; color: #1a1a1a; text-align: right; word-break: break-word; }
    th { background: #f9fafb; font-weight: 700; color: #374151; font-size: 8.5px;
         text-transform: uppercase; letter-spacing: .4px; }

    /* Two-column layout for spec sheets */
    .cols { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 8px; }

    /* ============ Damage cards ============ */
    .dmg-card { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;
                padding: 12px 14px; margin-bottom: 10px; break-inside: avoid; }
    .dmg-card-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px; }
    .dmg-card-area { color: #92400e; font-size: 11.5px; font-weight: 800; }
    .dmg-card-type { font-size: 8.5px; text-transform: uppercase; letter-spacing: .3px;
                     color: #b45309; font-weight: 700; }
    .dmg-card-tags { font-size: 9px; color: #92400e; margin-bottom: 5px; }
    .dmg-card-desc { font-size: 9.5px; color: #57534e; line-height: 1.55; margin: 0; }
    .dmg-card-photos { margin-top: 8px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
    .dmg-card-photos img { width: 100%; height: 120px; object-fit: cover; border-radius: 4px;
                           border: 1px solid #f5e8c5; }

    .dmg-text { background: #fffbeb; border-left: 3px solid #f59e0b;
                padding: 7px 12px; margin-bottom: 6px; border-radius: 0 4px 4px 0; break-inside: avoid; }
    .dmg-text strong { color: #92400e; font-size: 10px; }
    .dmg-text p { margin: 3px 0 0; color: #78716c; font-size: 9.5px; }

    /* ============ Paint measurements ============ */
    .paint-tbl td, .paint-tbl th { text-align: center; }
    .paint-tbl td.lbl { text-align: left; font-weight: 600; color: #1a1a1a; }
    .paint-ok     { color: #16a34a; background: #f0fdf4; }
    .paint-warn   { color: #d97706; background: #fffbeb; }
    .paint-danger { color: #dc2626; background: #fef2f2; }
    .paint-legend { display: flex; gap: 16px; margin: 8px 0 12px; font-size: 8.5px; color: #6b7280; }
    .paint-legend-swatch { display: inline-block; width: 8px; height: 8px; border-radius: 2px;
                            margin-right: 4px; vertical-align: -1px; }

    /* ============ Condition cell colours (shared) ============ */
    .cond-ok   { color: #16a34a; font-weight: 700; }
    .cond-warn { color: #d97706; font-weight: 700; }
    .cond-bad  { color: #dc2626; font-weight: 700; }

    /* ============ Tires table ============ */
    .tire-tbl td, .tire-tbl th { padding: 8px 10px; border: 1px solid #e5e7eb; text-align: center; }
    .tire-tbl th { background: #f3f4f6; font-size: 8.5px; text-transform: uppercase;
                   letter-spacing: .3px; color: #374151; font-weight: 700; }
    .tire-set-title { font-size: 11px; font-weight: 800; margin: 12px 0 6px; color: #1a1a1a; }

    /* ============ Equipment list ============ */
    .eq-cat-title { font-size: 10px; font-weight: 800; color: #0066ff;
                    margin: 10px 0 4px; text-transform: uppercase; letter-spacing: .3px; break-after: avoid-page; }
    .eq-list { list-style: none; padding: 0; margin: 0; }
    .eq-list li { padding: 3px 0; font-size: 9.5px; color: #374151; line-height: 1.45; }
    .eq-list li::before { content: '• '; color: #0066ff; font-weight: 800; }

    /* ============ Photo grid ============ */
    .photo-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
    .photo-grid img { width: 100%; height: 130px; object-fit: cover; border-radius: 5px;
                      border: 1px solid #f0f0f2; }

    /* ============ Media cards (engine video / 360) ============ */
    .media-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
                  padding: 12px 14px; margin-bottom: 8px; break-inside: avoid; }
    .media-card-head { font-size: 11px; font-weight: 800; color: #0a0a0a; margin-bottom: 4px; }
    .media-card-status { display: inline-block; background: #dcfce7; color: #15803d;
                         font-size: 8px; font-weight: 800; padding: 2px 7px; border-radius: 8px;
                         letter-spacing: .3px; text-transform: uppercase; margin-bottom: 6px; }
    .media-card-url { font-size: 9.5px; color: #0066ff; word-break: break-all; }
    .media-card-hint { font-size: 8.5px; color: #9ca3af; margin-top: 4px; }

    /* ============ Page break controls ============ */
    .pb { break-before: page; }
    section.section { break-inside: avoid; }
</style>
</head>
<body>

{{-- ============ Cover page (1) ============ --}}
<div class="cover">
    <div class="cover-brand-row">
        <div>
            <span class="cover-brand">Certi<span>Cars</span></span>
            <span class="cover-brand-badge">CertiCheck Report</span>
        </div>
        <div class="cover-meta-stack">
            <strong>{{ $car->identifier }}</strong>
            <span>Wygenerowano {{ now()->format('d.m.Y') }}</span>
        </div>
    </div>

    {{-- Hero — render only if the controller validated a reachable image. --}}
    @if($car->primaryImage && !empty($car->primaryImage->pdf_src))
    <div class="cover-hero">
        <img src="{{ $car->primaryImage->pdf_src }}" alt="{{ $car->title }}">
    </div>
    @endif

    <div class="cover-title">{{ $car->title }}</div>
    <div class="cover-sub">
        @if($car->first_registration){{ $car->first_registration }}@endif
        @if($car->mileage) · {{ number_format($car->mileage, 0, '', ' ') }} km @endif
        @if($car->fuel_type) · {{ \App\Helpers\CarLabels::fuelType($car->fuel_type) ?? $car->fuel_type }}@endif
        @if($car->transmission) · {{ $car->transmission_detail ?? \App\Helpers\CarLabels::transmission($car->transmission) ?? $car->transmission }}@endif
    </div>

    <div class="cover-kf">
        @if($car->mileage)<div class="cover-kf-item"><span class="cover-kf-val">{{ number_format($car->mileage, 0, '', ' ') }} km</span><span class="cover-kf-lbl">Przebieg</span></div>@endif
        @if($car->first_registration)<div class="cover-kf-item"><span class="cover-kf-val">{{ $car->first_registration }}</span><span class="cover-kf-lbl">Rejestracja</span></div>@endif
        @if($car->power_hp)<div class="cover-kf-item"><span class="cover-kf-val">{{ $car->power_hp }} KM</span><span class="cover-kf-lbl">Moc</span></div>@endif
        @if($car->engine_capacity)<div class="cover-kf-item"><span class="cover-kf-val">{{ number_format($car->engine_capacity, 0, '', ' ') }}</span><span class="cover-kf-lbl">cm³</span></div>@endif
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

{{-- ============ Page 2 — Vehicle data ============ --}}
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · {{ $car->title }}</div>
</div>

<section class="section">
<div class="sh">Dane pojazdu</div>
<div class="cols">
    <div>
        <table>
            @if($car->brand?->name)<tr><td class="lbl">Marka</td><td class="val">{{ $car->brand->name }}</td></tr>@endif
            @if($car->model)<tr><td class="lbl">Model</td><td class="val">{{ $car->model }}</td></tr>@endif
            @if($car->first_registration)<tr><td class="lbl">Pierwsza rejestracja</td><td class="val">{{ $car->first_registration }}</td></tr>@endif
            @if($car->mileage)<tr><td class="lbl">Przebieg</td><td class="val">{{ number_format($car->mileage, 0, '', ' ') }} km</td></tr>@endif
            @if($car->vin)<tr><td class="lbl">VIN</td><td class="val">{{ $car->vin }}</td></tr>@endif
            @if($car->body_type)<tr><td class="lbl">Typ nadwozia</td><td class="val">{{ \App\Helpers\CarLabels::bodyType($car->body_type) ?? $car->body_type }}</td></tr>@endif
            @php
                $colorClean      = \App\Services\BrochureTextScrubber::clean($car->color);
                $colorCodeClean  = \App\Services\BrochureTextScrubber::clean($car->color_code);
                $upholsteryClean = \App\Services\BrochureTextScrubber::clean($car->upholstery);
            @endphp
            @if($colorClean)<tr><td class="lbl">Kolor</td><td class="val">{{ $colorClean }}@if($colorCodeClean) ({{ $colorCodeClean }})@endif</td></tr>@endif
            @if($upholsteryClean)<tr><td class="lbl">Tapicerka</td><td class="val">{{ $upholsteryClean }}</td></tr>@endif
        </table>
    </div>
    <div>
        <table>
            @if($car->fuel_type)<tr><td class="lbl">Paliwo</td><td class="val">{{ \App\Helpers\CarLabels::fuelType($car->fuel_type) ?? $car->fuel_type }}</td></tr>@endif
            @if($car->engine_capacity)<tr><td class="lbl">Pojemność silnika</td><td class="val">{{ number_format($car->engine_capacity, 0, '', ' ') }} cm³</td></tr>@endif
            @if($car->power_hp)<tr><td class="lbl">Moc</td><td class="val">{{ $car->power_hp }} KM @if($car->power_kw)({{ $car->power_kw }} kW)@endif</td></tr>@endif
            @if($car->transmission)
                @php
                    $transmissionLabel = \App\Services\BrochureTextScrubber::clean($car->transmission_detail)
                        ?? \App\Helpers\CarLabels::transmission($car->transmission)
                        ?? $car->transmission;
                @endphp
                <tr><td class="lbl">Skrzynia biegów</td><td class="val">{{ $transmissionLabel }}</td></tr>
            @endif
            @if($car->drive_type)<tr><td class="lbl">Napęd</td><td class="val">{{ \App\Helpers\CarLabels::drive($car->drive_type) ?? $car->drive_type }}</td></tr>@endif
            @if($car->doors)<tr><td class="lbl">Drzwi / Miejsca</td><td class="val">{{ $car->doors }} / {{ $car->seats ?? '—' }}</td></tr>@endif
            @if($car->weight)<tr><td class="lbl">Masa własna</td><td class="val">{{ number_format($car->weight, 0, '', ' ') }} kg</td></tr>@endif
            @if($car->number_of_keys)<tr><td class="lbl">Liczba kluczyków</td><td class="val">{{ $car->number_of_keys }}</td></tr>@endif
        </table>
    </div>
</div>
</section>

{{-- ============ Technical condition + paint ============ --}}
@if(($car->paint_measurements && count($car->paint_measurements)) || ($car->technical_conditions && count($car->technical_conditions)))
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Stan techniczny</div>
</div>

@if($car->paint_measurements && count($car->paint_measurements))
<section class="section">
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
        <td class="lbl">{{ $panelLabel }}</td>
        <td class="{{ $cls }}">{{ $numVal }} µm</td>
        <td class="{{ $cls }}">{{ $verdict }}</td>
    </tr>
    @endforeach
</table>
<div class="paint-legend">
    <div><span class="paint-legend-swatch" style="background:#16a34a"></span>OK (≤ 160 µm)</div>
    <div><span class="paint-legend-swatch" style="background:#d97706"></span>Uwaga (161–200 µm)</div>
    <div><span class="paint-legend-swatch" style="background:#dc2626"></span>Naprawa (&gt; 200 µm)</div>
</div>
</section>
@endif

@if($car->technical_conditions && count($car->technical_conditions))
<section class="section">
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
        $clsMap = ['ok' => 'cond-ok', 'attention' => 'cond-warn', 'bad' => 'cond-bad'];
        $cls = $clsMap[$resolved['status']] ?? '';
        $cellText = $resolved['label'];
        // techStatus note is admin free-text — scrub before appending.
        $noteClean = \App\Services\BrochureTextScrubber::clean($resolved['note'] ?? null);
        if ($noteClean) {
            $cellText .= ' — ' . $noteClean;
        }
    @endphp
    <tr>
        <td class="lbl">{{ $compLabel }}</td>
        <td class="val {{ $cls }}" style="text-align:right">{{ $cellText }}</td>
    </tr>
    @endforeach
</table>
</section>
@endif
@endif

{{-- ============ Tires ============ --}}
@if($car->tireSets && $car->tireSets->count())
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Koła i opony</div>
</div>
<div class="sh">Koła i opony</div>
<div class="sh-sub">Pomiar głębokości bieżnika i ocena stanu poszczególnych opon w każdym komplecie.</div>
@foreach($car->tireSets as $set)
@php
    // tire_type / rim are admin free-text — historically have leaked slang
    // ("zajebiste") and test placeholders into production brochures. Scrub
    // before render; null = hide that segment entirely.
    $tireTypeClean = \App\Services\BrochureTextScrubber::clean($set->tire_type);
    $rimClean      = \App\Services\BrochureTextScrubber::clean($set->rim);
@endphp
<section class="section">
<div class="tire-set-title">
    Komplet {{ $set->set_number ?? $loop->iteration }}
    @if($tireTypeClean) · {{ $tireTypeClean }}@endif
    @if($rimClean) · {{ $rimClean }}@endif
    @if($set->is_mounted) (zamontowane)@endif
</div>
<table class="tire-tbl">
    <tr>
        <th>Pozycja</th>
        <th>Bieżnik</th>
        <th>Stan</th>
    </tr>
    @foreach($set->tires as $tire)
    @php $cond = \App\Helpers\CarLabels::tireCondition($tire->condition); @endphp
    <tr>
        <td>{{ \App\Helpers\CarLabels::tirePosition($tire->position) }}</td>
        <td style="font-weight:700">{{ $tire->tread_depth !== null ? number_format((float) $tire->tread_depth, 1, ',', ' ') . ' mm' : '—' }}</td>
        <td><span class="{{ $cond['class'] }}">{{ $cond['label'] }}</span></td>
    </tr>
    @endforeach
</table>
</section>
@endforeach
@endif

{{-- ============ Damages (per-damage with photos) ============ --}}
@if($car->damages->count())
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
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
    // Damage area / tags / description / severity are admin free-text. Scrub
    // each before render so slang ("zajebiste") and test placeholders never
    // reach the client PDF. Damage location enum keys (front_left) get the
    // CarLabels helper; anything else passes through the scrubber.
    $rawArea = (string) $d->area;
    $areaMapped = \App\Helpers\CarLabels::damageLocation($rawArea);
    $areaCandidate = (str_contains($rawArea, '_') || ctype_lower($rawArea[0] ?? 'X'))
        ? $areaMapped
        : $rawArea;
    $areaLabel    = \App\Services\BrochureTextScrubber::clean($areaCandidate) ?? '—';
    $descClean    = \App\Services\BrochureTextScrubber::clean((string) $d->description);
    $severity     = \App\Services\BrochureTextScrubber::clean((string) $d->severity);
    $tagsClean    = \App\Services\BrochureTextScrubber::cleanArray(is_array($d->tags) ? $d->tags : null);
@endphp
@if(count($dmgPhotos))
<div class="dmg-card">
    <div class="dmg-card-head">
        <span class="dmg-card-area">{{ $areaLabel }}</span>
        <span class="dmg-card-type">{{ $typeLabel }}@if($severity) · {{ $severity }}@endif</span>
    </div>
    @if(count($tagsClean))<div class="dmg-card-tags">{{ implode(' · ', $tagsClean) }}</div>@endif
    @if($descClean)<p class="dmg-card-desc">{{ $descClean }}</p>@endif
    <div class="dmg-card-photos">
        @foreach(array_slice($dmgPhotos, 0, 3) as $pSrc)
            <img src="{{ $pSrc }}" alt="{{ $areaLabel }}">
        @endforeach
    </div>
</div>
@else
<div class="dmg-text">
    <strong>{{ $areaLabel }}</strong>
    <span style="color:#b45309;font-size:8.5px;text-transform:uppercase;letter-spacing:.3px;margin-left:6px">{{ $typeLabel }}</span>
    @if(count($tagsClean)) — {{ implode(', ', $tagsClean) }}@endif
    @if($descClean)<p>{{ $descClean }}</p>@endif
</div>
@endif
@endforeach
@endif

{{-- ============ Equipment ============ --}}
@if($car->equipment && count($car->equipment))
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
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
    $categories = [];
    foreach ($car->equipment as $cat => $items) {
        if (!is_array($items)) continue;
        // Equipment items are free-text typed by admin — scrub each line so
        // slang / test placeholders never reach the client PDF. Drop the
        // whole category if nothing usable is left.
        $cleanItems = \App\Services\BrochureTextScrubber::cleanArray($items);
        if (count($cleanItems) === 0) continue;
        $categories[] = ['title' => $equipLabels[$cat] ?? ucfirst((string) $cat), 'items' => $cleanItems];
    }
    $halfMark = (int) ceil(count($categories) / 2);
@endphp
<div class="cols">
    <div>
        @foreach(array_slice($categories, 0, $halfMark) as $cat)
            <div class="eq-cat-title">{{ $cat['title'] }}</div>
            <ul class="eq-list">@foreach($cat['items'] as $it)<li>{{ $it }}</li>@endforeach</ul>
        @endforeach
    </div>
    <div>
        @foreach(array_slice($categories, $halfMark) as $cat)
            <div class="eq-cat-title">{{ $cat['title'] }}</div>
            <ul class="eq-list">@foreach($cat['items'] as $it)<li>{{ $it }}</li>@endforeach</ul>
        @endforeach
    </div>
</div>
@endif

{{-- ============ Photo documentation (gallery + damage shots) ============ --}}
@php
    $galleryEmbeddable = $car->galleryImages?->filter(fn($i) => !empty($i->pdf_src))->values() ?? collect();
    $galleryChunks     = $galleryEmbeddable->chunk(6);
    $damageEmbeddable  = $car->damageImages?->filter(fn($i) => !empty($i->pdf_src))->values() ?? collect();
    $damageChunks      = $damageEmbeddable->chunk(6);
@endphp

@foreach($galleryChunks as $chunkIdx => $chunk)
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Dokumentacja fotograficzna</div>
</div>
@if($chunkIdx === 0)
<div class="sh">Dokumentacja fotograficzna</div>
<div class="sh-sub">{{ $galleryEmbeddable->count() }} zdjęć udokumentowanych podczas inspekcji.</div>
@else
<div class="sh">Dokumentacja fotograficzna (cd.)</div>
@endif
<div class="photo-grid">
    @foreach($chunk as $img)<img src="{{ $img->pdf_src }}" alt="">@endforeach
</div>
@endforeach

@foreach($damageChunks as $chunkIdx => $chunk)
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $car->identifier }}</strong> · Zdjęcia uszkodzeń</div>
</div>
@if($chunkIdx === 0)
<div class="sh sh-warn">Zdjęcia uszkodzeń</div>
<div class="sh-sub">Dodatkowa dokumentacja fotograficzna śladów eksploatacji.</div>
@else
<div class="sh sh-warn">Zdjęcia uszkodzeń (cd.)</div>
@endif
<div class="photo-grid">
    @foreach($chunk as $img)<img src="{{ $img->pdf_src }}" alt="">@endforeach
</div>
@endforeach

{{-- ============ Online media (engine video / 360) ============ --}}
@php
    $engineVideoUrl = $car->engine_video_url ?: ($car->engine_video_path ? url('/samochody/' . $car->slug) : null);
    $hasPano = $car->pano360Image || $car->exteriorPano360Image;
@endphp
@if($engineVideoUrl || $hasPano)
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
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

</body>
</html>
