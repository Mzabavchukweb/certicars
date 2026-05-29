{{--
    CertiCheck inspection report — rendered via spatie/browsershot (Chromium).

    Consumes a fully-prepared BrochureData DTO ($b). The view contains NO
    model access, NO sanitization logic, NO label mapping. Every value the
    builder pushed in is already client-safe and Polish-localised. The only
    branching here is "is the kv list non-empty? render the section".

    All images are base64 data: URIs (see ImageEmbedder). Chromium makes
    zero network requests at render time, so this template renders the same
    deterministic output in dev and prod.

    Layout: A4, dense COS-Check inspection-report style. The page header
    (brand + contact strip) lives in @page so it appears identically on
    every page without duplicating markup. No marketing cover page.
--}}
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>{{ $b->title }} — CertiCheck Report</title>
<style>
    @page {
        size: A4;
        margin: 22mm 12mm 16mm 12mm;
        @top-left {
            content: "CertiCars  ·  CertiCheck Report";
            font-family: 'Inter', sans-serif;
            font-size: 9px;
            color: #0066ff;
            font-weight: 800;
            letter-spacing: .2px;
        }
        @top-right {
            content: "{{ $b->contactPhone }}  ·  {{ $b->contactEmail }}  ·  {{ $b->contactWebsite }}";
            font-family: 'Inter', sans-serif;
            font-size: 8.5px;
            color: #475569;
            letter-spacing: .1px;
        }
        @bottom-left {
            content: "{{ $b->identifier ?: '' }}";
            font-family: 'Inter', sans-serif;
            font-size: 8px;
            color: #94a3b8;
        }
        @bottom-center {
            content: "Strona " counter(page) " z " counter(pages);
            font-family: 'Inter', sans-serif;
            font-size: 8px;
            color: #94a3b8;
        }
        @bottom-right {
            content: "Wygenerowano {{ $b->generatedAt }}";
            font-family: 'Inter', sans-serif;
            font-size: 8px;
            color: #94a3b8;
        }
    }

    *, *::before, *::after { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; }
    body {
        font-family: 'Inter', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        color: #1a1a1a;
        font-size: 9.5px;
        line-height: 1.5;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Top accent strip under the page header */
    .accent {
        height: 2px;
        background: #0066ff;
        margin: 0 0 12px;
    }

    /* ── Page 1: vehicle summary ──────────────────────────────────── */
    .summary {
        display: table;
        width: 100%;
        margin-bottom: 12px;
    }
    .summary-hero {
        display: table-cell;
        vertical-align: top;
        width: 55%;
        padding-right: 10px;
    }
    .summary-hero img {
        width: 100%;
        height: 62mm;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #e5e7eb;
        display: block;
    }
    .summary-hero-empty {
        width: 100%;
        height: 62mm;
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 9px;
    }
    .summary-meta {
        display: table-cell;
        vertical-align: top;
        width: 45%;
        padding-left: 4px;
    }
    .summary-title {
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -.3px;
        color: #0a0a0a;
        margin: 0 0 2px;
        line-height: 1.15;
    }
    .summary-sub {
        font-size: 9px;
        color: #64748b;
        margin: 0 0 10px;
    }
    .summary-facts {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
        margin-bottom: 10px;
    }
    .summary-facts td {
        padding: 4px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .summary-facts td.lbl {
        color: #64748b;
        width: 50%;
    }
    .summary-facts td.val {
        color: #0a0a0a;
        font-weight: 700;
        text-align: right;
    }
    .summary-price {
        background: #0a0a0a;
        color: #fff;
        padding: 9px 14px;
        border-radius: 4px;
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-top: 6px;
    }
    .summary-price .lbl {
        font-size: 8.5px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
    }
    .summary-price .val {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: -.3px;
    }

    /* ── Section headers (compact, blue accent) ───────────────────── */
    .sh {
        font-size: 11px;
        font-weight: 800;
        color: #0066ff;
        margin: 14px 0 4px;
        padding-bottom: 3px;
        border-bottom: 1.5px solid #cbd5e1;
        letter-spacing: -.1px;
        break-after: avoid-page;
        text-transform: uppercase;
    }
    .sh-warn {
        color: #b45309;
        border-bottom-color: #f59e0b;
    }
    .sh-sub {
        font-size: 8.5px;
        color: #94a3b8;
        margin: -2px 0 6px;
        break-after: avoid-page;
    }

    /* ── KV tables (used by every label/value section) ────────────── */
    table.kv {
        width: 100%;
        border-collapse: collapse;
        font-size: 9.5px;
        margin-bottom: 6px;
    }
    table.kv td {
        padding: 5px 8px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }
    table.kv tr:last-child td { border-bottom: none; }
    table.kv td.lbl {
        color: #64748b;
        width: 42%;
        font-weight: 500;
    }
    table.kv td.val {
        color: #0a0a0a;
        font-weight: 700;
        text-align: right;
        word-break: break-word;
    }
    /* Side-by-side compact tables — used for Documents | Formalities
       row arrangement to avoid huge empty pages on narrow data. */
    .cols2 {
        display: table;
        width: 100%;
        table-layout: fixed;
        border-spacing: 10px 0;
        margin-left: -10px;
        margin-right: -10px;
    }
    .cols2 > .col {
        display: table-cell;
        vertical-align: top;
    }

    /* Free-text note paragraph (history Opis) */
    .note {
        font-size: 9px;
        color: #334155;
        line-height: 1.55;
        margin: 4px 0 8px;
        padding: 8px 10px;
        background: #f8fafc;
        border-left: 3px solid #0066ff;
        border-radius: 0 4px 4px 0;
    }

    /* ── Paint measurement table ──────────────────────────────────── */
    table.paint {
        width: 100%;
        border-collapse: collapse;
        font-size: 9.5px;
        margin-bottom: 6px;
    }
    table.paint th, table.paint td {
        padding: 5px 8px;
        border-bottom: 1px solid #e5e7eb;
        text-align: center;
    }
    table.paint th {
        background: #f9fafb;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #475569;
        font-weight: 700;
    }
    table.paint td.lbl {
        text-align: left;
        font-weight: 600;
        color: #0a0a0a;
        width: 50%;
    }
    .paint-ok     { color: #16a34a; background: #f0fdf4; font-weight: 700; }
    .paint-warn   { color: #b45309; background: #fffbeb; font-weight: 700; }
    .paint-danger { color: #b91c1c; background: #fef2f2; font-weight: 700; }
    .paint-legend {
        display: flex;
        gap: 14px;
        margin: 4px 0 10px;
        font-size: 8px;
        color: #64748b;
    }
    .paint-legend-sw {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 1px;
        margin-right: 4px;
        vertical-align: -1px;
    }

    /* ── Technical-condition rows ─────────────────────────────────── */
    table.tech {
        width: 100%;
        border-collapse: collapse;
        font-size: 9.5px;
        margin-bottom: 6px;
    }
    table.tech th, table.tech td {
        padding: 5px 8px;
        border-bottom: 1px solid #e5e7eb;
    }
    table.tech th {
        background: #f9fafb;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #475569;
        font-weight: 700;
    }
    table.tech td.lbl {
        color: #0a0a0a;
        font-weight: 600;
        width: 45%;
    }
    .cond-ok   { color: #16a34a; font-weight: 700; }
    .cond-warn { color: #b45309; font-weight: 700; }
    .cond-bad  { color: #b91c1c; font-weight: 700; }

    /* ── Tire tables ──────────────────────────────────────────────── */
    table.tire {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
        margin-bottom: 6px;
    }
    table.tire th, table.tire td {
        padding: 5px 8px;
        border: 1px solid #e5e7eb;
        text-align: center;
    }
    table.tire th {
        background: #f3f4f6;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #475569;
        font-weight: 700;
    }
    .tire-set-title {
        font-size: 9.5px;
        font-weight: 800;
        color: #0a0a0a;
        margin: 10px 0 4px;
    }

    /* ── Damage cards ─────────────────────────────────────────────── */
    .dmg {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 4px;
        padding: 8px 10px;
        margin-bottom: 8px;
        break-inside: avoid;
    }
    .dmg-head {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 3px;
    }
    .dmg-area {
        font-size: 10px;
        font-weight: 800;
        color: #92400e;
    }
    .dmg-type {
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: #b45309;
        font-weight: 700;
    }
    .dmg-tags {
        font-size: 8.5px;
        color: #92400e;
        margin-bottom: 3px;
    }
    .dmg-desc {
        font-size: 9px;
        color: #57534e;
        line-height: 1.5;
        margin: 0 0 5px;
    }
    .dmg-photos {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 4px;
    }
    .dmg-photos img {
        width: 100%;
        height: 32mm;
        object-fit: cover;
        border-radius: 3px;
        border: 1px solid #f5e8c5;
    }

    /* ── Equipment lists ──────────────────────────────────────────── */
    .eq-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 16px;
    }
    .eq-cat {
        break-inside: avoid;
    }
    .eq-cat-title {
        font-size: 9px;
        font-weight: 800;
        color: #0066ff;
        margin: 4px 0 2px;
        text-transform: uppercase;
        letter-spacing: .3px;
        border-bottom: 1px solid #dbeafe;
        padding-bottom: 2px;
        break-after: avoid-page;
    }
    .eq-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .eq-list li {
        padding: 1.5px 0;
        font-size: 8.5px;
        color: #334155;
        line-height: 1.4;
    }
    .eq-list li::before {
        content: '✓ ';
        color: #0066ff;
        font-weight: 800;
    }

    /* ── Photo grid ───────────────────────────────────────────────── */
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5px;
    }
    .photo-grid img {
        width: 100%;
        height: 38mm;
        object-fit: cover;
        border-radius: 3px;
        border: 1px solid #e5e7eb;
    }

    /* ── Online media ─────────────────────────────────────────────── */
    table.media {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
        margin-bottom: 4px;
    }
    table.media td {
        padding: 5px 8px;
        border-bottom: 1px solid #f1f5f9;
    }
    table.media td.lbl {
        color: #64748b;
        width: 30%;
        font-weight: 500;
    }
    table.media td.val {
        color: #0066ff;
        word-break: break-all;
    }

    /* Spacing utility */
    .pb { break-before: page; }
    section { break-inside: avoid-page; }
</style>
</head>
<body>

<div class="accent"></div>

{{-- ── 1+2. Vehicle summary (Page 1 top) ─────────────────────────── --}}
<div class="summary">
    <div class="summary-hero">
        @if($b->heroImage)
            <img src="{{ $b->heroImage->dataUri }}" alt="{{ $b->title }}">
        @else
            <div class="summary-hero-empty">Brak zdjęcia tytułowego</div>
        @endif
    </div>
    <div class="summary-meta">
        <h1 class="summary-title">{{ $b->title ?: ($b->brand . ' ' . $b->model) }}</h1>
        <p class="summary-sub">
            @if($b->firstRegistration){{ $b->firstRegistration }}@endif
            @if($b->mileage) · {{ number_format($b->mileage, 0, '', ' ') }} km @endif
            @if($b->fuelType) · {{ $b->fuelType }}@endif
            @if($b->transmission) · {{ $b->transmission }}@endif
        </p>
        <table class="summary-facts">
            @if($b->mileage)<tr><td class="lbl">Przebieg</td><td class="val">{{ number_format($b->mileage, 0, '', ' ') }} km</td></tr>@endif
            @if($b->firstRegistration)<tr><td class="lbl">Rok produkcji</td><td class="val">{{ $b->firstRegistration }}</td></tr>@endif
            @if($b->fuelType)<tr><td class="lbl">Paliwo</td><td class="val">{{ $b->fuelType }}</td></tr>@endif
            @if($b->transmission)<tr><td class="lbl">Skrzynia biegów</td><td class="val">{{ $b->transmission }}</td></tr>@endif
            @if($b->powerHp)<tr><td class="lbl">Moc</td><td class="val">{{ $b->powerHp }} KM @if($b->powerKw)({{ $b->powerKw }} kW)@endif</td></tr>@endif
            @if($b->engineCapacity)<tr><td class="lbl">Pojemność silnika</td><td class="val">{{ number_format($b->engineCapacity, 0, '', ' ') }} cm³</td></tr>@endif
            @if($b->doors || $b->seats)<tr><td class="lbl">Drzwi / Miejsca</td><td class="val">{{ $b->doors ?? '—' }} / {{ $b->seats ?? '—' }}</td></tr>@endif
        </table>
        @if($b->formattedPrice)
        <div class="summary-price">
            <span class="lbl">Cena</span>
            <span class="val">{{ $b->formattedPrice }}</span>
        </div>
        @endif
    </div>
</div>

{{-- ── 3. Main Dane pojazdu table ───────────────────────────────── --}}
@if(count($b->vehicleData))
<section>
<div class="sh">Dane pojazdu</div>
<table class="kv">
    @foreach($b->vehicleData as $row)
        <tr><td class="lbl">{{ $row['label'] }}</td><td class="val">{{ $row['value'] }}</td></tr>
    @endforeach
</table>
</section>
@endif

{{-- ── 4. Historia pojazdu ─────────────────────────────────────── --}}
@if(count($b->historyItems) || $b->vehicleHistoryNote)
<section>
<div class="sh">Historia pojazdu</div>
@if(count($b->historyItems))
<table class="kv">
    @foreach($b->historyItems as $row)
        <tr><td class="lbl">{{ $row['label'] }}</td><td class="val">{{ $row['value'] }}</td></tr>
    @endforeach
</table>
@endif
@if($b->vehicleHistoryNote)
<p class="note"><strong>Opis historii:</strong> {{ $b->vehicleHistoryNote }}</p>
@endif
</section>
@endif

{{-- ── 5+6. Dokumenty + Formalności side-by-side ───────────────── --}}
@if(count($b->documentItems) || count($b->formalItems))
<section>
<div class="cols2">
    @if(count($b->documentItems))
    <div class="col">
        <div class="sh">Dokumenty</div>
        <table class="kv">
            @foreach($b->documentItems as $row)
                <tr><td class="lbl">{{ $row['label'] }}</td><td class="val">{{ $row['value'] }}</td></tr>
            @endforeach
        </table>
    </div>
    @endif
    @if(count($b->formalItems))
    <div class="col">
        <div class="sh">Formalności</div>
        <table class="kv">
            @foreach($b->formalItems as $row)
                <tr><td class="lbl">{{ $row['label'] }}</td><td class="val">{{ $row['value'] }}</td></tr>
            @endforeach
        </table>
    </div>
    @endif
</div>
</section>
@endif

{{-- ── 7+8. Serwis + Zużycie side-by-side ──────────────────────── --}}
@if(count($b->serviceItems) || count($b->fuelItems))
<section>
<div class="cols2">
    @if(count($b->serviceItems))
    <div class="col">
        <div class="sh">Serwis i dokumentacja</div>
        <table class="kv">
            @foreach($b->serviceItems as $row)
                <tr><td class="lbl">{{ $row['label'] }}</td><td class="val">{{ $row['value'] }}</td></tr>
            @endforeach
        </table>
    </div>
    @endif
    @if(count($b->fuelItems))
    <div class="col">
        <div class="sh">Zużycie paliwa i emisje</div>
        <table class="kv">
            @foreach($b->fuelItems as $row)
                <tr><td class="lbl">{{ $row['label'] }}</td><td class="val">{{ $row['value'] }}</td></tr>
            @endforeach
        </table>
    </div>
    @endif
</div>
</section>
@endif

{{-- ── 9. Pomiary grubości lakieru ─────────────────────────────── --}}
@if(count($b->paintMeasurements))
<section>
<div class="sh">Pomiary grubości lakieru</div>
<div class="sh-sub">Norma fabryczna: 80–160 µm · powyżej 200 µm — możliwa naprawa lakiernicza</div>
<table class="paint">
    <tr><th>Element</th><th>Grubość</th><th>Ocena</th></tr>
    @foreach($b->paintMeasurements as $row)
    <tr>
        <td class="lbl">{{ $row['label'] }}</td>
        <td class="{{ $row['class'] }}">{{ $row['value'] }} µm</td>
        <td class="{{ $row['class'] }}">{{ $row['verdict'] }}</td>
    </tr>
    @endforeach
</table>
<div class="paint-legend">
    <div><span class="paint-legend-sw" style="background:#16a34a"></span>Fabryczny (≤ 160 µm)</div>
    <div><span class="paint-legend-sw" style="background:#b45309"></span>Uwaga (161–200 µm)</div>
    <div><span class="paint-legend-sw" style="background:#b91c1c"></span>Naprawa (&gt; 200 µm)</div>
</div>
</section>
@endif

{{-- ── 10. Stan techniczny ─────────────────────────────────────── --}}
@if(count($b->technicalConditions))
<section>
<div class="sh">Stan techniczny</div>
<table class="tech">
    <tr><th>Komponent</th><th style="text-align:right">Stan</th></tr>
    @foreach($b->technicalConditions as $row)
    <tr>
        <td class="lbl">{{ $row['label'] }}</td>
        <td class="{{ $row['class'] }}" style="text-align:right">
            {{ $row['status'] }}@if($row['note']) — {{ $row['note'] }}@endif
        </td>
    </tr>
    @endforeach
</table>
</section>
@endif

{{-- ── 11. Koła i opony ────────────────────────────────────────── --}}
@if(count($b->tireSets))
<section>
<div class="sh">Koła i opony</div>
@foreach($b->tireSets as $set)
<div class="tire-set-title">{{ $set['title'] }}</div>
<table class="tire">
    <tr><th>Pozycja</th><th>Bieżnik</th><th>Stan</th></tr>
    @foreach($set['tires'] as $t)
    <tr>
        <td>{{ $t['position'] }}</td>
        <td style="font-weight:700">{{ $t['treadMm'] ?? '—' }}</td>
        <td><span class="{{ $t['class'] }}">{{ $t['label'] }}</span></td>
    </tr>
    @endforeach
</table>
@endforeach
</section>
@endif

{{-- ── 12. Stan wizualny / uszkodzenia ─────────────────────────── --}}
@if(count($b->damages))
<section>
<div class="sh sh-warn">Stan wizualny i ślady eksploatacji ({{ count($b->damages) }})</div>
<div class="sh-sub">Lista udokumentowanych śladów eksploatacji oraz uszkodzeń wraz ze zdjęciami referencyjnymi.</div>

@foreach($b->damages as $d)
@if(count($d['photos']))
<div class="dmg">
    <div class="dmg-head">
        <span class="dmg-area">{{ $d['area'] }}</span>
        <span class="dmg-type">{{ $d['type'] }}@if($d['severity']) · {{ $d['severity'] }}@endif</span>
    </div>
    @if(count($d['tags']))<div class="dmg-tags">{{ implode(' · ', $d['tags']) }}</div>@endif
    @if($d['description'])<p class="dmg-desc">{{ $d['description'] }}</p>@endif
    <div class="dmg-photos">
        @foreach(array_slice($d['photos'], 0, 3) as $photo)
            <img src="{{ $photo->dataUri }}" alt="{{ $d['area'] }}">
        @endforeach
    </div>
</div>
@else
<div class="dmg">
    <div class="dmg-head">
        <span class="dmg-area">{{ $d['area'] }}</span>
        <span class="dmg-type">{{ $d['type'] }}@if($d['severity']) · {{ $d['severity'] }}@endif</span>
    </div>
    @if(count($d['tags']))<div class="dmg-tags">{{ implode(' · ', $d['tags']) }}</div>@endif
    @if($d['description'])<p class="dmg-desc">{{ $d['description'] }}</p>@endif
</div>
@endif
@endforeach
</section>
@endif

{{-- ── 13. Wyposażenie ─────────────────────────────────────────── --}}
@if(count($b->equipment))
<section>
<div class="sh">Wyposażenie pojazdu</div>
<div class="sh-sub">Pełna lista wyposażenia fabrycznego i opcjonalnego potwierdzona przy inspekcji.</div>
<div class="eq-grid">
    @foreach($b->equipment as $cat)
    <div class="eq-cat">
        <div class="eq-cat-title">{{ $cat['title'] }}</div>
        <ul class="eq-list">@foreach($cat['items'] as $it)<li>{{ $it }}</li>@endforeach</ul>
    </div>
    @endforeach
</div>
</section>
@endif

{{-- ── 14. Dokumentacja fotograficzna ──────────────────────────── --}}
@if(count($b->galleryImages))
@php $galleryChunks = array_chunk($b->galleryImages, 9); @endphp
@foreach($galleryChunks as $chunkIdx => $chunk)
<div class="pb"></div>
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
@endif

{{-- ── 15. Zdjęcia uszkodzeń ───────────────────────────────────── --}}
@if(count($b->damageImages))
@php $damageChunks = array_chunk($b->damageImages, 9); @endphp
@foreach($damageChunks as $chunkIdx => $chunk)
<div class="pb"></div>
@if($chunkIdx === 0)
<div class="sh sh-warn">Zdjęcia uszkodzeń pojazdu</div>
<div class="sh-sub">Dodatkowa dokumentacja fotograficzna śladów eksploatacji.</div>
@else
<div class="sh sh-warn">Zdjęcia uszkodzeń (cd.)</div>
@endif
<div class="photo-grid">
    @foreach($chunk as $img)<img src="{{ $img->dataUri }}" alt="">@endforeach
</div>
@endforeach
@endif

{{-- ── 16. Materiały online ────────────────────────────────────── --}}
@if($b->engineVideoUrl || $b->exteriorPanoUrl || $b->interiorPanoUrl)
<section>
<div class="sh">Materiały online</div>
<div class="sh-sub">Pełna dokumentacja wideo i panoramy 360° dostępne w wersji online raportu.</div>
<table class="media">
    @if($b->engineVideoUrl)<tr><td class="lbl">Nagranie pracy silnika</td><td class="val">{{ $b->engineVideoUrl }}</td></tr>@endif
    @if($b->exteriorPanoUrl)<tr><td class="lbl">Panorama 360° na zewnątrz</td><td class="val">{{ $b->exteriorPanoUrl }}</td></tr>@endif
    @if($b->interiorPanoUrl)<tr><td class="lbl">Panorama 360° wewnątrz</td><td class="val">{{ $b->interiorPanoUrl }}</td></tr>@endif
</table>
</section>
@endif

</body>
</html>
