{{--
    CertiCheck brochure document — browser-rendered via spatie/browsershot.

    Consumes a fully-prepared BrochureData DTO ($b). The view contains NO
    model access, NO sanitization logic, NO label mapping — every value is
    already client-safe by the time it gets here. The only branching done
    here is "do we have anything in this collection? → render the section,
    otherwise hide it entirely".

    All images are base64 data: URIs. Chromium makes zero network requests
    at render time.
--}}
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>{{ $b->title }} — CertiCheck Report</title>
<style>
    @page {
        size: A4;
        margin: 14mm 10mm 16mm 10mm;
        @bottom-center {
            content: "Strona " counter(page) " z " counter(pages);
            font-family: 'Inter', sans-serif;
            font-size: 8.5px;
            color: #94a3b8;
        }
    }
    @page :first { margin: 0; @bottom-center { content: ""; } }

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

    /* ── Brand header on every page after the cover ─────────────── */
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

    /* ── Cover ──────────────────────────────────────────────────── */
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

    /* ── Section headers ────────────────────────────────────────── */
    .sh { font-size: 13px; font-weight: 800; color: #0066ff; margin: 16px 0 6px;
          padding-bottom: 5px; border-bottom: 1.5px solid #e5e7eb; break-after: avoid-page; }
    .sh-warn { color: #b45309; border-bottom-color: #f59e0b; }
    .sh-sub  { font-size: 9.5px; color: #9ca3af; margin: -4px 0 8px; break-after: avoid-page; }

    /* ── Tables ─────────────────────────────────────────────────── */
    table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 10px; }
    td, th { padding: 6px 10px; border-bottom: 1px solid #f0f0f2; text-align: left; vertical-align: top; }
    td.lbl { color: #6b7280; width: 42%; }
    td.val { font-weight: 700; color: #1a1a1a; text-align: right; word-break: break-word; }
    th { background: #f9fafb; font-weight: 700; color: #374151; font-size: 8.5px;
         text-transform: uppercase; letter-spacing: .4px; }

    .cols { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 8px; }

    /* ── Damage cards ───────────────────────────────────────────── */
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

    /* ── Paint ──────────────────────────────────────────────────── */
    .paint-tbl td, .paint-tbl th { text-align: center; }
    .paint-tbl td.lbl { text-align: left; font-weight: 600; color: #1a1a1a; }
    .paint-ok     { color: #16a34a; background: #f0fdf4; }
    .paint-warn   { color: #d97706; background: #fffbeb; }
    .paint-danger { color: #dc2626; background: #fef2f2; }
    .paint-legend { display: flex; gap: 16px; margin: 8px 0 12px; font-size: 8.5px; color: #6b7280; }
    .paint-legend-swatch { display: inline-block; width: 8px; height: 8px; border-radius: 2px;
                            margin-right: 4px; vertical-align: -1px; }

    /* ── Condition cell colours ─────────────────────────────────── */
    .cond-ok   { color: #16a34a; font-weight: 700; }
    .cond-warn { color: #d97706; font-weight: 700; }
    .cond-bad  { color: #dc2626; font-weight: 700; }

    /* ── Tires ──────────────────────────────────────────────────── */
    .tire-tbl td, .tire-tbl th { padding: 8px 10px; border: 1px solid #e5e7eb; text-align: center; }
    .tire-tbl th { background: #f3f4f6; font-size: 8.5px; text-transform: uppercase;
                   letter-spacing: .3px; color: #374151; font-weight: 700; }
    .tire-set-title { font-size: 11px; font-weight: 800; margin: 12px 0 6px; color: #1a1a1a; }

    /* ── Equipment ──────────────────────────────────────────────── */
    .eq-cat-title { font-size: 10px; font-weight: 800; color: #0066ff;
                    margin: 10px 0 4px; text-transform: uppercase; letter-spacing: .3px; break-after: avoid-page; }
    .eq-list { list-style: none; padding: 0; margin: 0; }
    .eq-list li { padding: 3px 0; font-size: 9.5px; color: #374151; line-height: 1.45; }
    .eq-list li::before { content: '• '; color: #0066ff; font-weight: 800; }

    /* ── Photo grid ─────────────────────────────────────────────── */
    .photo-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
    .photo-grid img { width: 100%; height: 130px; object-fit: cover; border-radius: 5px;
                      border: 1px solid #f0f0f2; }

    /* ── Online media cards ─────────────────────────────────────── */
    .media-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
                  padding: 12px 14px; margin-bottom: 8px; break-inside: avoid; }
    .media-card-head { font-size: 11px; font-weight: 800; color: #0a0a0a; margin-bottom: 4px; }
    .media-card-status { display: inline-block; background: #dcfce7; color: #15803d;
                         font-size: 8px; font-weight: 800; padding: 2px 7px; border-radius: 8px;
                         letter-spacing: .3px; text-transform: uppercase; margin-bottom: 6px; }
    .media-card-url { font-size: 9.5px; color: #0066ff; word-break: break-all; }
    .media-card-hint { font-size: 8.5px; color: #9ca3af; margin-top: 4px; }

    .pb { break-before: page; }
    section.section { break-inside: avoid; }
</style>
</head>
<body>

{{-- ── Cover page ─────────────────────────────────────────────── --}}
<div class="cover">
    <div class="cover-brand-row">
        <div>
            <span class="cover-brand">Certi<span>Cars</span></span>
            <span class="cover-brand-badge">CertiCheck Report</span>
        </div>
        <div class="cover-meta-stack">
            <strong>{{ $b->identifier }}</strong>
            <span>Wygenerowano {{ $b->generatedAt }}</span>
        </div>
    </div>

    @if($b->heroImage)
    <div class="cover-hero">
        <img src="{{ $b->heroImage->dataUri }}" alt="{{ $b->title }}">
    </div>
    @endif

    <div class="cover-title">{{ $b->title }}</div>
    <div class="cover-sub">
        @if($b->firstRegistration){{ $b->firstRegistration }}@endif
        @if($b->mileage) · {{ number_format($b->mileage, 0, '', ' ') }} km @endif
        @if($b->fuelType) · {{ $b->fuelType }}@endif
        @if($b->transmission) · {{ $b->transmission }}@endif
    </div>

    <div class="cover-kf">
        @if($b->mileage)<div class="cover-kf-item"><span class="cover-kf-val">{{ number_format($b->mileage, 0, '', ' ') }} km</span><span class="cover-kf-lbl">Przebieg</span></div>@endif
        @if($b->firstRegistration)<div class="cover-kf-item"><span class="cover-kf-val">{{ $b->firstRegistration }}</span><span class="cover-kf-lbl">Rejestracja</span></div>@endif
        @if($b->powerHp)<div class="cover-kf-item"><span class="cover-kf-val">{{ $b->powerHp }} KM</span><span class="cover-kf-lbl">Moc</span></div>@endif
        @if($b->engineCapacity)<div class="cover-kf-item"><span class="cover-kf-val">{{ number_format($b->engineCapacity, 0, '', ' ') }}</span><span class="cover-kf-lbl">cm³</span></div>@endif
        @if($b->doors)<div class="cover-kf-item"><span class="cover-kf-val">{{ $b->doors }}/{{ $b->seats ?? '—' }}</span><span class="cover-kf-lbl">Drzwi / miejsc</span></div>@endif
    </div>

    @if($b->formattedPrice)
    <div class="cover-price">
        <span class="lbl">Cena sprzedaży</span>
        <span class="val">{{ $b->formattedPrice }}</span>
    </div>
    @endif

    <div class="cover-intro">
        Niniejszy raport <strong>CertiCheck</strong> dokumentuje stan techniczny, wizualny i historię pojazdu na dzień inspekcji.
        Zawiera szczegółową specyfikację, ocenę stanu, pomiary lakieru, listę uszkodzeń wraz z dokumentacją fotograficzną
        oraz pełne wyposażenie. Wszystkie informacje pochodzą z naszej weryfikacji.
    </div>
</div>

{{-- ── Dane pojazdu ────────────────────────────────────────────── --}}
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $b->identifier }}</strong> · {{ $b->title }}</div>
</div>

<section class="section">
<div class="sh">Dane pojazdu</div>
<div class="cols">
    <div>
        <table>
            @if($b->brand)<tr><td class="lbl">Marka</td><td class="val">{{ $b->brand }}</td></tr>@endif
            @if($b->model)<tr><td class="lbl">Model</td><td class="val">{{ $b->model }}</td></tr>@endif
            @if($b->firstRegistration)<tr><td class="lbl">Pierwsza rejestracja</td><td class="val">{{ $b->firstRegistration }}</td></tr>@endif
            @if($b->mileage)<tr><td class="lbl">Przebieg</td><td class="val">{{ number_format($b->mileage, 0, '', ' ') }} km</td></tr>@endif
            @if($b->vin)<tr><td class="lbl">VIN</td><td class="val">{{ $b->vin }}</td></tr>@endif
            @if($b->bodyType)<tr><td class="lbl">Typ nadwozia</td><td class="val">{{ $b->bodyType }}</td></tr>@endif
            @if($b->color)<tr><td class="lbl">Kolor</td><td class="val">{{ $b->color }}@if($b->colorCode) ({{ $b->colorCode }})@endif</td></tr>@endif
            @if($b->upholstery)<tr><td class="lbl">Tapicerka</td><td class="val">{{ $b->upholstery }}</td></tr>@endif
        </table>
    </div>
    <div>
        <table>
            @if($b->fuelType)<tr><td class="lbl">Paliwo</td><td class="val">{{ $b->fuelType }}</td></tr>@endif
            @if($b->engineCapacity)<tr><td class="lbl">Pojemność silnika</td><td class="val">{{ number_format($b->engineCapacity, 0, '', ' ') }} cm³</td></tr>@endif
            @if($b->powerHp)<tr><td class="lbl">Moc</td><td class="val">{{ $b->powerHp }} KM @if($b->powerKw)({{ $b->powerKw }} kW)@endif</td></tr>@endif
            @if($b->transmission)<tr><td class="lbl">Skrzynia biegów</td><td class="val">{{ $b->transmission }}</td></tr>@endif
            @if($b->driveType)<tr><td class="lbl">Napęd</td><td class="val">{{ $b->driveType }}</td></tr>@endif
            @if($b->doors)<tr><td class="lbl">Drzwi / Miejsca</td><td class="val">{{ $b->doors }} / {{ $b->seats ?? '—' }}</td></tr>@endif
            @if($b->weight)<tr><td class="lbl">Masa własna</td><td class="val">{{ number_format($b->weight, 0, '', ' ') }} kg</td></tr>@endif
            @if($b->numberOfKeys)<tr><td class="lbl">Liczba kluczyków</td><td class="val">{{ $b->numberOfKeys }}</td></tr>@endif
        </table>
    </div>
</div>
</section>

{{-- ── Historia pojazdu ────────────────────────────────────────── --}}
@if($b->previousOwners !== null || $b->importedFrom || $b->countryRegistration || $b->vehicleHistory)
<section class="section">
<div class="sh">Historia pojazdu</div>
<table>
    @if($b->previousOwners !== null)<tr><td class="lbl">Liczba właścicieli</td><td class="val">{{ $b->previousOwners }}</td></tr>@endif
    @if($b->importedFrom)<tr><td class="lbl">Importowany z</td><td class="val">{{ $b->importedFrom }}</td></tr>@endif
    @if($b->countryRegistration)<tr><td class="lbl">Kraj rejestracji</td><td class="val">{{ $b->countryRegistration }}</td></tr>@endif
</table>
@if($b->vehicleHistory)
<p style="font-size:9.5px;color:#374151;line-height:1.6;margin-top:6px">{{ $b->vehicleHistory }}</p>
@endif
</section>
@endif

{{-- ── Stan techniczny / pomiary lakieru ───────────────────────── --}}
@if(count($b->paintMeasurements) || count($b->technicalConditions))
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $b->identifier }}</strong> · Stan techniczny</div>
</div>

@if(count($b->paintMeasurements))
<section class="section">
<div class="sh">Pomiary grubości lakieru</div>
<div class="sh-sub">Norma fabryczna: 80–150 µm · powyżej 200 µm — możliwa naprawa lakiernicza</div>
<table class="paint-tbl">
    <tr><th>Element</th><th>Grubość (µm)</th><th>Ocena</th></tr>
    @foreach($b->paintMeasurements as $row)
    <tr>
        <td class="lbl">{{ $row['label'] }}</td>
        <td class="{{ $row['class'] }}">{{ $row['value'] }} µm</td>
        <td class="{{ $row['class'] }}">{{ $row['verdict'] }}</td>
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

@if(count($b->technicalConditions))
<section class="section">
<div class="sh">Ocena stanu technicznego</div>
<table>
    <tr><th>Komponent</th><th style="text-align:right">Stan</th></tr>
    @foreach($b->technicalConditions as $row)
    <tr>
        <td class="lbl">{{ $row['label'] }}</td>
        <td class="val {{ $row['class'] }}" style="text-align:right">
            {{ $row['status'] }}@if($row['note']) — {{ $row['note'] }}@endif
        </td>
    </tr>
    @endforeach
</table>
</section>
@endif
@endif

{{-- ── Koła i opony ────────────────────────────────────────────── --}}
@if(count($b->tireSets))
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $b->identifier }}</strong> · Koła i opony</div>
</div>
<div class="sh">Koła i opony</div>
<div class="sh-sub">Pomiar głębokości bieżnika i ocena stanu poszczególnych opon w każdym komplecie.</div>
@foreach($b->tireSets as $set)
<section class="section">
<div class="tire-set-title">{{ $set['title'] }}</div>
<table class="tire-tbl">
    <tr><th>Pozycja</th><th>Bieżnik</th><th>Stan</th></tr>
    @foreach($set['tires'] as $t)
    <tr>
        <td>{{ $t['position'] }}</td>
        <td style="font-weight:700">{{ $t['treadMm'] ?? '—' }}</td>
        <td><span class="{{ $t['class'] }}">{{ $t['label'] }}</span></td>
    </tr>
    @endforeach
</table>
</section>
@endforeach
@endif

{{-- ── Stan wizualny / uszkodzenia ─────────────────────────────── --}}
@if(count($b->damages))
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $b->identifier }}</strong> · Stan wizualny</div>
</div>
<div class="sh sh-warn">Stan wizualny i ślady użytkowania ({{ count($b->damages) }})</div>
<div class="sh-sub">Lista udokumentowanych śladów eksploatacji oraz uszkodzeń wraz ze zdjęciami referencyjnymi.</div>

@foreach($b->damages as $d)
@if(count($d['photos']))
<div class="dmg-card">
    <div class="dmg-card-head">
        <span class="dmg-card-area">{{ $d['area'] }}</span>
        <span class="dmg-card-type">{{ $d['type'] }}@if($d['severity']) · {{ $d['severity'] }}@endif</span>
    </div>
    @if(count($d['tags']))<div class="dmg-card-tags">{{ implode(' · ', $d['tags']) }}</div>@endif
    @if($d['description'])<p class="dmg-card-desc">{{ $d['description'] }}</p>@endif
    <div class="dmg-card-photos">
        @foreach(array_slice($d['photos'], 0, 3) as $photo)
            <img src="{{ $photo->dataUri }}" alt="{{ $d['area'] }}">
        @endforeach
    </div>
</div>
@else
<div class="dmg-text">
    <strong>{{ $d['area'] }}</strong>
    <span style="color:#b45309;font-size:8.5px;text-transform:uppercase;letter-spacing:.3px;margin-left:6px">{{ $d['type'] }}</span>
    @if(count($d['tags'])) — {{ implode(', ', $d['tags']) }}@endif
    @if($d['description'])<p>{{ $d['description'] }}</p>@endif
</div>
@endif
@endforeach
@endif

{{-- ── Wyposażenie ─────────────────────────────────────────────── --}}
@if(count($b->equipment))
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $b->identifier }}</strong> · Wyposażenie</div>
</div>
<div class="sh">Wyposażenie pojazdu</div>
<div class="sh-sub">Pełna lista wyposażenia fabrycznego i opcjonalnego potwierdzona przy inspekcji.</div>
@php $half = (int) ceil(count($b->equipment) / 2); @endphp
<div class="cols">
    <div>
        @foreach(array_slice($b->equipment, 0, $half) as $cat)
            <div class="eq-cat-title">{{ $cat['title'] }}</div>
            <ul class="eq-list">@foreach($cat['items'] as $it)<li>{{ $it }}</li>@endforeach</ul>
        @endforeach
    </div>
    <div>
        @foreach(array_slice($b->equipment, $half) as $cat)
            <div class="eq-cat-title">{{ $cat['title'] }}</div>
            <ul class="eq-list">@foreach($cat['items'] as $it)<li>{{ $it }}</li>@endforeach</ul>
        @endforeach
    </div>
</div>
@endif

{{-- ── Dokumentacja fotograficzna ──────────────────────────────── --}}
@php $galleryChunks = array_chunk($b->galleryImages, 6); @endphp
@foreach($galleryChunks as $chunkIdx => $chunk)
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $b->identifier }}</strong> · Dokumentacja fotograficzna</div>
</div>
@if($chunkIdx === 0)
<div class="sh">Dokumentacja fotograficzna</div>
<div class="sh-sub">{{ count($b->galleryImages) }} zdjęć udokumentowanych podczas inspekcji.</div>
@else
<div class="sh">Dokumentacja fotograficzna (cd.)</div>
@endif
<div class="photo-grid">
    @foreach($chunk as $img)<img src="{{ $img->dataUri }}" alt="">@endforeach
</div>
@endforeach

@php $damageChunks = array_chunk($b->damageImages, 6); @endphp
@foreach($damageChunks as $chunkIdx => $chunk)
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $b->identifier }}</strong> · Zdjęcia uszkodzeń</div>
</div>
@if($chunkIdx === 0)
<div class="sh sh-warn">Zdjęcia uszkodzeń</div>
<div class="sh-sub">Dodatkowa dokumentacja fotograficzna śladów eksploatacji.</div>
@else
<div class="sh sh-warn">Zdjęcia uszkodzeń (cd.)</div>
@endif
<div class="photo-grid">
    @foreach($chunk as $img)<img src="{{ $img->dataUri }}" alt="">@endforeach
</div>
@endforeach

{{-- ── Materiały online ────────────────────────────────────────── --}}
@if($b->engineVideoUrl || $b->exteriorPanoUrl || $b->interiorPanoUrl)
<div class="pb"></div>
<div class="hd">
    <div><span class="hd-brand">Certi<span>Cars</span></span><span class="hd-badge">CertiCheck</span></div>
    <div class="hd-right"><strong>{{ $b->identifier }}</strong> · Materiały dodatkowe</div>
</div>
<div class="sh">Materiały online</div>
<div class="sh-sub">Pełna dokumentacja wideo i panoramy 360° dostępne w wersji online raportu.</div>

@if($b->engineVideoUrl)
<div class="media-card">
    <div class="media-card-head">Nagranie pracy silnika</div>
    <div class="media-card-status">Dostępne online</div>
    <div class="media-card-url">{{ $b->engineVideoUrl }}</div>
    <div class="media-card-hint">Otwórz powyższy adres w przeglądarce, aby odtworzyć nagranie pracy silnika.</div>
</div>
@endif

@if($b->exteriorPanoUrl)
<div class="media-card">
    <div class="media-card-head">Widok zewnętrzny 360°</div>
    <div class="media-card-status">Dostępne online</div>
    <div class="media-card-url">{{ $b->exteriorPanoUrl }}</div>
    <div class="media-card-hint">Interaktywna panorama zewnętrzna dostępna na stronie ogłoszenia.</div>
</div>
@endif

@if($b->interiorPanoUrl)
<div class="media-card">
    <div class="media-card-head">Widok wnętrza 360°</div>
    <div class="media-card-status">Dostępne online</div>
    <div class="media-card-url">{{ $b->interiorPanoUrl }}</div>
    <div class="media-card-hint">Interaktywna panorama wnętrza dostępna na stronie ogłoszenia.</div>
</div>
@endif
@endif

</body>
</html>
