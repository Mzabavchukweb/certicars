{{--
|--------------------------------------------------------------------------
| Wizard Form — Steps 1-11 (included from create.blade.php / edit.blade.php)
|--------------------------------------------------------------------------
| $car      — Car model instance (null on create)
| $brands   — Collection of Brand models
|--------------------------------------------------------------------------
--}}

@php
    $existingDamages   = $car?->damages ?? collect();
    $existingTireSets  = $car?->tireSets ?? collect();
    $eqCategories = [
        'safety'       => 'Bezpieczeństwo',
        'comfort'      => 'Komfort / multimedia',
        'exterior'     => 'Wyposażenie zewnętrzne',
        'interior'     => 'Wyposażenie wewnętrzne',
        'extra'        => 'Dodatkowe',
    ];
    // 8 canonical technical inspection items — same keys consumed by the public
    // show.blade.php and PDF brochure. Order matches the inspection report layout.
    $techCategories = [
        'engine'       => 'Silnik',
        'transmission' => 'Skrzynia biegów',
        'suspension'   => 'Zawieszenie',
        'brakes'       => 'Hamulce',
        'steering'     => 'Układ kierowniczy',
        'ac'           => 'Klimatyzacja',
        'electronics'  => 'Elektronika',
        'lights'       => 'Oświetlenie',
    ];
    // Legacy key aliases so old saves (air_conditioning, braking, body) preload
    // into the corresponding new canonical bucket when re-editing.
    $techAliases = [
        'air_conditioning' => 'ac',
        'braking'          => 'brakes',
    ];
    $equipment          = old('equipment', $car?->equipment ?? []);
    $rawTechConditions  = old('technical_conditions', $car?->technical_conditions ?? []);
    $technicalConditions = is_array($rawTechConditions) ? $rawTechConditions : [];
    foreach ($techAliases as $oldKey => $newKey) {
        if (isset($technicalConditions[$oldKey]) && !isset($technicalConditions[$newKey])) {
            $technicalConditions[$newKey] = $technicalConditions[$oldKey];
        }
    }
    $paintMeasurements  = old('paint_measurements', $car?->paint_measurements ?? []);
    $eqToText = fn($items) => is_array($items) ? implode("\n", $items) : ($items ?? '');
    // P1 bugfix: historical data has nested array shapes (e.g. {"engine":{"status":"OK"}})
    // — the public view tolerates them but the wizard echoed them directly into
    // <textarea> via {{ }}, crashing with htmlspecialchars(array given).
    // Reduce any shape to a single scalar string for safe echoing.
    $techToString = function ($v) {
        if (is_array($v)) {
            foreach (['status', 'notes', 'value', 'text', 'description', 0] as $k) {
                if (array_key_exists($k, $v) && is_scalar($v[$k])) return (string) $v[$k];
            }
            // Last-resort fallback: flatten scalar leaves into a single line.
            $flat = array_filter($v, 'is_scalar');
            return $flat ? implode(' · ', array_map('strval', $flat)) : '';
        }
        return $v === null ? '' : (string) $v;
    };
    // Paint measurements have two historical shapes: {area, value} OR a plain scalar µm.
    $pmValue = function ($v) {
        if (is_array($v)) return is_scalar($v['value'] ?? null) ? (string) $v['value'] : '';
        return $v === null ? '' : (string) $v;
    };
    $pmArea = function ($v, $fallback) {
        if (is_array($v) && isset($v['area']) && is_scalar($v['area'])) return (string) $v['area'];
        return $fallback;
    };
@endphp

{{-- ============================================================
     CSS — Wizard layout, steps, toggles, preview card
     ============================================================ --}}
<style>
/* ── Wizard layout ── */
.wz-layout {
    max-width: 900px;
}
@media (max-width: 1100px) {
    .wz-layout { max-width: 100%; }
}

/* ── Step panels ── */
.wz-step { display: none; animation: wzFadeIn .2s ease; }
.wz-step.active { display: block; }
@keyframes wzFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Section card ── */
.wz-section {
    background: #fff;
    border: 1px solid var(--border-l, #e5e7eb);
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 18px;
}
.wz-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}
.wz-section-badge {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--blue, #0066ff);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 14px;
    flex-shrink: 0;
}
.wz-section-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text, #111);
    margin: 0;
}
.wz-section-subtitle {
    font-size: 12px;
    color: var(--text-3, #888);
    margin: 2px 0 0;
}

/* ── 2-column field grid ── */
.wz-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
.wz-grid-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 14px;
}
.wz-grid-4 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 14px;
}
/* Step-1 4-column grid keeps a slightly wider gap so each column reads as
   its own labelled group, matching the reference. */
.wz-cols-4 { column-gap: 22px; }
.wz-col { display: flex; flex-direction: column; gap: 12px; min-width: 0 }
.wz-col .wz-field { margin-bottom: 0 }
.wz-col-label {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--text-3, #6b7280);
    padding-bottom: 8px;
    margin-bottom: 4px;
    border-bottom: 1px solid var(--border-l, #eeeef0);
}
@media (max-width: 1100px) {
    .wz-cols-4 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 700px) {
    .wz-grid-2, .wz-grid-3, .wz-grid-4 { grid-template-columns: 1fr; }
}

/* ── Field styles ── */
.wz-field { margin-bottom: 0; }
.wz-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-2, #444);
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.wz-field input,
.wz-field select,
.wz-field textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border, #d1d5db);
    border-radius: 10px;
    font-size: 13.5px;
    background: #fff;
    color: var(--text, #111);
    transition: border-color .15s, box-shadow .15s;
}
.wz-field input:focus,
.wz-field select:focus,
.wz-field textarea:focus {
    outline: none;
    border-color: var(--blue, #0066ff);
    box-shadow: 0 0 0 3px rgba(0,102,255,.1);
}
.wz-field input:read-only {
    background: var(--bg, #f7f7f8);
    color: var(--text-3, #888);
}
.wz-field .field-hint {
    font-size: 11.5px;
    color: var(--text-3, #888);
    margin-top: 4px;
}
.wz-field-full { grid-column: 1 / -1; }

/* ── Title preview bar ── */
.wz-title-preview {
    background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
    border: 1.5px solid #e0e7ff;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.wz-title-preview .tp-icon {
    width: 36px; height: 36px;
    background: #4f46e5;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.wz-title-preview .tp-icon i { color: #fff; width: 18px; height: 18px; }
.wz-title-preview .tp-text {
    font-size: 15px;
    font-weight: 700;
    color: #1e1b4b;
    line-height: 1.3;
}
.wz-title-preview .tp-sub {
    font-size: 11.5px;
    color: #6366f1;
    margin-top: 2px;
}

/* ── Toggle switch ── */
.wz-toggle {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid var(--border-l, #e5e7eb);
    border-radius: 12px;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    user-select: none;
}
.wz-toggle:hover { border-color: var(--blue, #0066ff); background: #fafbff; }
.wz-toggle.active { border-color: var(--blue, #0066ff); background: rgba(0,102,255,.04); }
.wz-toggle input[type="checkbox"] { display: none; }
.wz-toggle-track {
    width: 42px; height: 24px;
    background: #d1d5db;
    border-radius: 12px;
    position: relative;
    flex-shrink: 0;
    transition: background .2s;
}
.wz-toggle.active .wz-toggle-track { background: var(--blue, #0066ff); }
.wz-toggle-thumb {
    position: absolute;
    top: 3px; left: 3px;
    width: 18px; height: 18px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    transition: transform .2s;
}
.wz-toggle.active .wz-toggle-thumb { transform: translateX(18px); }
.wz-toggle-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text, #111);
    display: flex;
    align-items: center;
    gap: 6px;
}
.wz-toggle-desc {
    font-size: 11.5px;
    color: var(--text-3, #888);
    margin-top: 2px;
    line-height: 1.4;
}

/* ── Placeholder step ── */
.wz-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 260px;
    background: #fff;
    border: 1px solid var(--border-l, #e5e7eb);
    border-radius: 14px;
    color: var(--text-3, #888);
    font-size: 15px;
    font-weight: 600;
}
.wz-placeholder i { width: 28px; height: 28px; margin-bottom: 8px; color: var(--text-4, #bbb); }

/* ── Sidebar preview card ── */
.wz-sidebar {
    position: sticky;
    top: 80px;
}
.wz-preview-card {
    background: #fff;
    border: 1px solid var(--border-l, #e5e7eb);
    border-radius: 14px;
    overflow: hidden;
}
.wz-preview-img {
    width: 100%;
    aspect-ratio: 16/10;
    object-fit: cover;
    display: block;
    background: linear-gradient(135deg, #e5e5e7, #c7c7cc);
}
.wz-preview-body { padding: 16px 18px; }
.wz-preview-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text, #111);
    margin: 0 0 8px;
    line-height: 1.3;
    min-height: 22px;
}
.wz-preview-specs {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 10px;
}
.wz-preview-spec {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: var(--bg, #f7f7f8);
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--text-2, #444);
}
.wz-preview-spec i { width: 12px; height: 12px; }
.wz-preview-price {
    font-size: 22px;
    font-weight: 800;
    color: var(--blue, #0066ff);
    letter-spacing: -.5px;
    margin: 12px 0 10px;
}
.wz-preview-price .currency {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-3, #888);
    margin-left: 4px;
}
.wz-preview-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--border-l, #e5e7eb);
}
.wz-preview-badge {
    display: none;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    gap: 4px;
    align-items: center;
}
.wz-preview-badge.visible { display: inline-flex; }
.wz-preview-badge i { width: 12px; height: 12px; }
.wz-preview-badge.badge-certicheck { background: rgba(0,102,255,.08); color: var(--blue, #0066ff); }
.wz-preview-badge.badge-available  { background: rgba(16,185,129,.08); color: #10b981; }
.wz-preview-badge.badge-delivery   { background: rgba(99,102,241,.08); color: #6366f1; }
.wz-preview-badge.badge-gethelp    { background: rgba(245,158,11,.08); color: #f59e0b; }

/* ── Completion tracker ── */
.wz-completion { padding: 14px 18px; border-top: 1px solid var(--border-l, #e5e7eb); }
.wz-completion-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--text-3, #888);
    margin-bottom: 10px;
}
.wz-completion-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 0;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--text-3, #888);
    cursor: pointer;
    transition: color .12s;
}
.wz-completion-item:hover { color: var(--text, #111); }
.wz-completion-item .ci-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #d1d5db;
    flex-shrink: 0;
    transition: background .2s;
}
.wz-completion-item.done .ci-dot { background: #10b981; }
.wz-completion-item.active { color: var(--blue, #0066ff); font-weight: 700; }
.wz-completion-item.active .ci-dot { background: var(--blue, #0066ff); box-shadow: 0 0 0 3px rgba(0,102,255,.15); }

/* ── Otomoto import card ── */
.wz-otomoto-card {
    border: 1.5px solid #e0e7ff;
    background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
    border-radius: 14px;
    padding: 20px 22px;
    margin-bottom: 20px;
}

/* ── File drop zones (carried from main form) ── */
.wz-file-drop {
    border: 1.5px dashed var(--border, #d1d5db);
    border-radius: 12px;
    padding: 28px 22px;
    text-align: center;
    color: var(--text-3, #888);
    font-size: 13px;
    cursor: pointer;
    transition: all .15s;
    background: #fff;
}
.wz-file-drop:hover, .wz-file-drop.over {
    border-color: var(--blue, #0066ff);
    color: var(--blue, #0066ff);
    background: rgba(0,102,255,.03);
}
.wz-file-drop input[type="file"] { display: none; }
.wz-file-drop i { width: 28px; height: 28px; margin-bottom: 8px; color: var(--text-4, #bbb); }
.wz-file-drop:hover i, .wz-file-drop.over i { color: var(--blue, #0066ff); }
.wz-file-drop .drop-title { font-weight: 600; margin-bottom: 4px; }
.wz-file-drop .drop-hint { font-size: 11.5px; color: var(--text-4, #bbb); }

/* ── Upload progress ── */
.wz-upload-progress {
    display: none;
    margin-top: 12px;
}
.wz-progress-bar-wrap {
    background: #e5e7eb;
    border-radius: 6px;
    height: 8px;
    overflow: hidden;
}
.wz-progress-bar {
    height: 100%;
    background: var(--blue, #0066ff);
    border-radius: 6px;
    width: 0%;
    transition: width .2s;
}

/* ── Image grid ── */
.wz-img-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
    margin-bottom: 14px;
}
.wz-img-tile {
    position: relative;
    border: 1px solid var(--border-l, #e5e7eb);
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    cursor: grab;
    transition: transform .15s, opacity .15s, box-shadow .15s;
}
.wz-img-tile:active { cursor: grabbing; }
.wz-img-tile.dragging { opacity: .5; transform: scale(.95); }
.wz-img-tile.drag-over { box-shadow: 0 0 0 3px var(--blue, #0066ff); transform: scale(1.02); }
.wz-img-tile img {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: cover;
    display: block;
}
.wz-img-tile .tile-actions {
    position: absolute;
    top: 6px; right: 6px;
    display: flex; gap: 4px;
}
.wz-img-tile .tile-del {
    width: 26px; height: 26px;
    background: rgba(239,68,68,.92);
    color: #fff;
    border: none;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
}
.wz-img-tile .tile-del i { width: 13px; height: 13px; }
.wz-img-tile .tile-primary {
    position: absolute;
    top: 6px; left: 6px;
    background: rgba(255,255,255,.95);
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 10px;
    display: flex; align-items: center; gap: 4px;
    cursor: pointer;
    font-weight: 600;
}
.wz-img-tile .tile-drag {
    position: absolute;
    top: 6px; left: 50%; transform: translateX(-50%);
    background: rgba(0,0,0,.6);
    color: #fff;
    border-radius: 6px;
    width: 26px; height: 22px;
    display: flex; align-items: center; justify-content: center;
    cursor: grab;
    opacity: 0;
    transition: opacity .15s;
    pointer-events: none;
}
.wz-img-tile:hover .tile-drag { opacity: 1; }
.wz-img-tile .tile-drag i { width: 14px; height: 14px; }
.wz-img-tile.to-delete { opacity: .4; outline: 2px solid var(--red, #ef4444); }
.wz-img-tile .tile-alt {
    padding: 8px 10px;
}
.wz-img-tile .tile-alt input {
    width: 100%;
    padding: 6px 9px;
    border: 1px solid var(--border, #d1d5db);
    border-radius: 6px;
    font-size: 11.5px;
}

/* ── File preview items ── */
.wz-file-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
    margin-top: 12px;
}
.wz-fp-item {
    position: relative;
    border: 1px solid var(--border-l, #e5e7eb);
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
}
.wz-fp-item img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; }
.wz-fp-item .fp-name {
    padding: 4px 8px;
    font-size: 10px;
    color: var(--text-3, #888);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ── Panorama / video sub-sections ── */
.wz-media-section {
    background: #fff;
    border: 1px solid var(--border-l, #e5e7eb);
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 18px;
}
.wz-media-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
}
.wz-media-header i { width: 20px; height: 20px; color: var(--blue, #0066ff); }
.wz-media-header h3 {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text, #111);
}

/* ── Inline label ── */
.wz-inline-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text, #111);
    cursor: pointer;
}
</style>

{{-- ============================================================
     Error banner
     ============================================================ --}}
@if($errors->any())
<div style="margin-bottom:18px;padding:14px 18px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <strong style="font-size:14px;color:#991b1b">Formularz zawiera błędy:</strong>
    </div>
    <ul style="margin:0;padding-left:20px;font-size:12.5px;color:#991b1b;line-height:1.8">
        @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
<script>
(function(){
    // Map each form field to the wizard step it lives on
    var wzFieldStep = {
        brand_id:1, model:1, category:1, price:1, currency:1, price_type:1,
        taxation:1, color:1, doors:1, seats:1,
        vin:1, body_type:1, first_registration:1, mileage:1,
        fuel_type:1, power_hp:1, power_kw:1, engine_capacity:1,
        transmission:1, transmission_detail:1,
        previous_owners:3, country_registration:3, is_imported:3,
        business_use:3, imported_from:3, vehicle_history:3,
        color_code:3, weight:3, upholstery:3,
        number_of_keys:5,
        last_service:4, last_service_mileage:4, next_inspection:4,
        service_documentation:4, fuel_consumption:4, fuel_procedure:4,
        co2_emission:4, emission_class:4, aso_serviced:4, service_history:4,
        service_book:5, coc_documents:5, vehicle_folder:5, hu_au_report:5,
        service_book_status:5, registration_cert:5, owners_manual:5,
        seller_name:5, seller_phone:5, seller_email:5, commission_note:5,
        reception_date:5,
        location:1, location_distance:1, source:1,
        meta_title:11, meta_description:11, focus_keyword:11, noindex:11,
    };
    var errorFields = @json($errors->keys());
    var firstStep = null;
    var errorSteps = new Set();
    errorFields.forEach(function(f){
        var step = wzFieldStep[f];
        if (step) {
            errorSteps.add(step);
            if (firstStep === null || step < firstStep) firstStep = step;
        }
    });
    document.addEventListener('DOMContentLoaded', function(){
        // Mark sidebar steps with errors
        errorSteps.forEach(function(step){
            var el = document.querySelector('.wiz-step[data-step="'+step+'"]');
            if (el) el.classList.add('has-error');
        });
        // Navigate to first step with errors
        if (firstStep && typeof goToStep === 'function') goToStep(firstStep);
    });
})();
</script>
@endif

{{-- ============================================================
     LAYOUT: Steps + Sidebar
     ============================================================ --}}
<div class="wz-layout">

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 1 — Dane podstawowe                                  ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step active" data-step="1">

    {{-- ⓪ Import z Otomoto --}}
    <div class="wz-section" style="background:linear-gradient(135deg,#eef2ff,#f5f3ff);border:1px solid #c7d2fe;margin-bottom:20px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
            <div style="width:40px;height:40px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i data-lucide="download" style="width:18px;height:18px;color:#fff"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:14px;color:#1e1b4b">Import z Otomoto</div>
                <div style="font-size:12px;color:#6366f1">Wklej link ogłoszenia — dane wypełnią się automatycznie (bez zdjęć)</div>
            </div>
        </div>
        <div style="display:flex;gap:8px">
            <input type="text" id="wzOtomotoUrl" placeholder="https://www.otomoto.pl/osobowe/oferta/..." style="flex:1;padding:10px 14px;border:1px solid #c7d2fe;border-radius:8px;font-size:13px;background:#fff">
            <button type="button" id="wzOtomotoBtn" class="btn" style="white-space:nowrap;padding:10px 20px;background:#6366f1;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:.15s" onclick="wzImportOtomoto()">
                <i data-lucide="download" style="width:14px;height:14px"></i> Importuj
            </button>
        </div>
        <div id="wzOtomotoStatus" style="margin-top:8px;font-size:12px;display:none;font-weight:600"></div>
    </div>

    {{-- ① Tytuł ogłoszenia --}}
    <div class="wz-title-preview" id="wzTitlePreview">
        <div class="tp-icon"><i data-lucide="type"></i></div>
        <div>
            <div class="tp-text" id="wzAutoTitle">{{ $car ? $car->title : 'Tytuł wygeneruje się automatycznie' }}</div>
            <div class="tp-sub">Tytuł buduje się z: Marka + Model + Silnik + Wersja wyposażenia</div>
        </div>
    </div>

    {{-- ② Dane podstawowe — 4-column reference layout ── Auto | Rejestracja
         i przebieg | Silnik i skrzynia | Nadwozie i użytkowość. Each column
         is a vertical stack of 4 inputs. --}}
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">1</div>
            <div>
                <div class="wz-section-title">Dane podstawowe</div>
                <div class="wz-section-subtitle">Podstawowe informacje o aucie</div>
            </div>
        </div>

        <div class="wz-grid-4 wz-cols-4">
            {{-- COLUMN 1 — Auto --}}
            <div class="wz-col">
                <div class="wz-col-label">Auto</div>
                <div class="wz-field">
                    <label>Marka * <a href="#" id="wzBrandAddToggle" style="float:right;font-size:11px;font-weight:600;color:var(--blue);text-decoration:none;display:inline-flex;align-items:center;gap:4px;text-transform:none;letter-spacing:0"><i data-lucide="plus" style="width:12px;height:12px"></i> Dodaj nową</a></label>
                    <select name="brand_id" id="wzBrandSelect" required @error('brand_id') style="border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.12)" @enderror>
                        <option value="">— wybierz —</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ old('brand_id',$car?->brand_id)==$b->id?'selected':'' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')
                    <div style="color:#dc2626;font-size:12px;margin-top:4px;display:flex;align-items:center;gap:4px">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                    @enderror
                    <div id="wzBrandAddRow" style="display:none;margin-top:8px;gap:6px;align-items:stretch">
                        <input type="text" id="wzBrandAddName" placeholder="Nazwa nowej marki" maxlength="255" style="flex:1;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px">
                        <button type="button" id="wzBrandAddSubmit" class="btn btn-blue btn-sm" style="white-space:nowrap"><i data-lucide="check" style="width:14px;height:14px"></i> Dodaj</button>
                        <button type="button" id="wzBrandAddCancel" class="btn btn-outline btn-sm" style="white-space:nowrap"><i data-lucide="x" style="width:14px;height:14px"></i></button>
                    </div>
                    <div id="wzBrandAddError" style="display:none;color:#dc2626;font-size:12px;margin-top:6px"></div>
                </div>
                <div class="wz-field">
                    <label>Model *</label>
                    <input type="text" name="model" value="{{ old('model',$car?->model) }}" required @error('model') style="border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.12)" @enderror>
                    @error('model')
                    <div style="color:#dc2626;font-size:12px;margin-top:4px;display:flex;align-items:center;gap:4px">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="wz-field">
                    <label>Wersja / silnik</label>
                    <input type="text" name="engine_version" value="{{ old('engine_version',$car?->engine_version) }}" maxlength="120" placeholder="np. 1.6 dCi 160 KM EDC">
                </div>
                <div class="wz-field">
                    <label>Rok produkcji</label>
                    <input type="number" name="production_year" value="{{ old('production_year',$car?->production_year) }}" min="1900" max="2100" placeholder="np. 2018">
                </div>
            </div>

            {{-- COLUMN 2 — Rejestracja i przebieg --}}
            <div class="wz-col">
                <div class="wz-col-label">Rejestracja i przebieg</div>
                <div class="wz-field">
                    <label>Pierwsza rejestracja</label>
                    <input type="text" name="first_registration" value="{{ old('first_registration',$car?->first_registration) }}" placeholder="mm/rrrr">
                </div>
                <div class="wz-field">
                    <label>Przebieg (km)</label>
                    <input type="number" name="mileage" value="{{ old('mileage',$car?->mileage) }}" min="0" placeholder="np. 136 000">
                </div>
                <div class="wz-field">
                    <label>Kraj pochodzenia</label>
                    <input type="text" name="country_registration" value="{{ old('country_registration',$car?->country_registration) }}" placeholder="Niemcy, Polska, USA...">
                </div>
                <div class="wz-field">
                    <label>VIN</label>
                    <input type="text" name="vin" value="{{ old('vin',$car?->vin) }}" maxlength="50" placeholder="np. VF1RFC00X54321012">
                </div>
            </div>

            {{-- COLUMN 3 — Silnik i skrzynia --}}
            <div class="wz-col">
                <div class="wz-col-label">Silnik i skrzynia</div>
                <div class="wz-field">
                    <label>Paliwo</label>
                    <select name="fuel_type">
                        <option value="">— wybierz —</option>
                        @foreach(['Benzyna','Diesel','Hybryda','Hybryda plug-in','Elektryczny','LPG','CNG'] as $ft)
                        <option value="{{ $ft }}" {{ old('fuel_type',$car?->fuel_type)==$ft?'selected':'' }}>{{ $ft }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wz-field">
                    <label>Skrzynia biegów</label>
                    <select name="transmission">
                        <option value="">— wybierz —</option>
                        @foreach(['Automatyczna','Manualna','CVT','Półautomatyczna (DSG/DCT)'] as $tr)
                        <option value="{{ $tr }}" {{ old('transmission',$car?->transmission)==$tr?'selected':'' }}>{{ $tr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wz-field">
                    <label>Moc (KM / kW)</label>
                    <div style="display:flex;gap:6px">
                        <input type="number" name="power_hp" value="{{ old('power_hp',$car?->power_hp) }}" min="0" placeholder="np. 160" style="flex:1;min-width:0">
                        <input type="number" name="power_kw" value="{{ old('power_kw',$car?->power_kw) }}" min="0" placeholder="np. 118" style="flex:1;min-width:0">
                    </div>
                </div>
                <div class="wz-field">
                    <label>Pojemność skokowa (cm³)</label>
                    <input type="number" name="engine_capacity" value="{{ old('engine_capacity',$car?->engine_capacity) }}" min="0" step="1" placeholder="np. 1598">
                </div>
            </div>

            {{-- COLUMN 4 — Nadwozie i użytkowość --}}
            <div class="wz-col">
                <div class="wz-col-label">Nadwozie i użytkowość</div>
                <div class="wz-field">
                    <label>Typ nadwozia</label>
                    <select name="body_type" id="wzBodyTypeSelect">
                        <option value="">— wybierz —</option>
                        @foreach(['Sedan','SUV','Coupé','Bus','Kombi','Hatchback','Kabriolet','Pickup'] as $bt)
                        <option value="{{ $bt }}" {{ old('body_type',$car?->body_type)==$bt?'selected':'' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wz-field">
                    <label>Liczba miejsc</label>
                    <input type="number" name="seats" value="{{ old('seats',$car?->seats) }}" min="1" placeholder="np. 5">
                </div>
                <div class="wz-field">
                    <label>Liczba drzwi</label>
                    <input type="number" name="doors" value="{{ old('doors',$car?->doors) }}" min="1" max="7" placeholder="np. 5">
                </div>
                <div class="wz-field">
                    <label>Kolor nadwozia</label>
                    <input type="text" name="color" value="{{ old('color',$car?->color) }}" placeholder="np. Szary metalik">
                </div>
            </div>
        </div>

        {{-- Secondary row — fields not in the reference 4-col grid but still
             needed (title formula uses equipment_version; drivetrain stays
             editable; identifier stays read-only for reference). --}}
        <div class="wz-grid-3" style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border-l)">
            <div class="wz-field">
                <label>Wersja wyposażenia</label>
                <input type="text" name="equipment_version" value="{{ old('equipment_version',$car?->equipment_version) }}" maxlength="120" placeholder="np. Initiale Paris">
                <small style="font-size:11px;color:var(--text-3);margin-top:4px;display:block">Wchodzi w skład tytułu ogłoszenia.</small>
            </div>
            <div class="wz-field">
                <label>Napęd</label>
                <input type="text" name="drivetrain" value="{{ old('drivetrain',$car?->drivetrain) }}" maxlength="80" placeholder="np. Na przednie koła (FWD)">
            </div>
            <div class="wz-field">
                <label>Identyfikator</label>
                <input type="text" value="{{ $car?->identifier ?? '— zostanie wygenerowany —' }}" disabled readonly>
            </div>
        </div>
    </div>

    {{-- ③ Cena i sprzedaż — 2×2 grid per reference --}}
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">2</div>
            <div>
                <div class="wz-section-title">Cena i sprzedaż</div>
                <div class="wz-section-subtitle">Ustal cenę i warunki oferty</div>
            </div>
        </div>

        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field">
                <label>Cena *</label>
                <input type="number" name="price" value="{{ old('price',$car?->price) }}" step="0.01" min="0" placeholder="np. 69 900">
            </div>
            <div class="wz-field">
                <label>Waluta *</label>
                <select name="currency">
                    @foreach(['PLN','EUR','USD','GBP','CHF'] as $cur)
                    <option value="{{ $cur }}" {{ old('currency',$car?->currency ?? 'PLN')==$cur?'selected':'' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="wz-grid-2">
            <div class="wz-field">
                <label>Typ ceny *</label>
                <select name="price_type">
                    <option value="">— wybierz —</option>
                    @foreach(['VAT marża','VAT 23%','Netto','Brutto','Cena do negocjacji'] as $pt)
                    <option value="{{ $pt }}" {{ old('price_type',$car?->price_type)==$pt?'selected':'' }}>{{ $pt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="wz-field">
                <label>Dodatkowe informacje cenowe (opcjonalne)</label>
                <input type="text" name="taxation" value="{{ old('taxation',$car?->taxation) }}" placeholder="np. cena do negocjacji, faktura VAT marża">
            </div>
        </div>
    </div>

    {{-- ④ Status pojazdu --}}
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">3</div>
            <div>
                <div class="wz-section-title">Status pojazdu</div>
                <div class="wz-section-subtitle">Dostępność i programy certyfikacji</div>
            </div>
        </div>

        <div class="wz-grid-2">
            <label class="wz-toggle {{ old('available_now',$car?->available_now) ? 'active' : '' }}" data-toggle>
                <input type="hidden" name="available_now" value="0">
                <input type="checkbox" name="available_now" value="1" {{ old('available_now',$car?->available_now)?'checked':'' }}>
                <div class="wz-toggle-track"><div class="wz-toggle-thumb"></div></div>
                <div>
                    <div class="wz-toggle-label"><i data-lucide="zap" style="width:14px;height:14px;color:#10b981"></i> Dostępny od ręki</div>
                    <div class="wz-toggle-desc">Badge na zdjęciu auta</div>
                </div>
            </label>

            <label class="wz-toggle {{ old('home_delivery',$car?->home_delivery) ? 'active' : '' }}" data-toggle>
                <input type="hidden" name="home_delivery" value="0">
                <input type="checkbox" name="home_delivery" value="1" {{ old('home_delivery',$car?->home_delivery)?'checked':'' }}>
                <div class="wz-toggle-track"><div class="wz-toggle-thumb"></div></div>
                <div>
                    <div class="wz-toggle-label"><i data-lucide="truck" style="width:14px;height:14px;color:#6366f1"></i> Dostawa pod dom</div>
                    <div class="wz-toggle-desc">Badge na zdjęciu auta</div>
                </div>
            </label>

            <label class="wz-toggle {{ old('has_certicheck',$car?->has_certicheck) ? 'active' : '' }}" data-toggle>
                <input type="hidden" name="has_certicheck" value="0">
                <input type="checkbox" name="has_certicheck" value="1" {{ old('has_certicheck',$car?->has_certicheck)?'checked':'' }}>
                <div class="wz-toggle-track"><div class="wz-toggle-thumb"></div></div>
                <div>
                    <div class="wz-toggle-label"><i data-lucide="shield-check" style="width:14px;height:14px;color:var(--blue)"></i> CertiCheck</div>
                    <div class="wz-toggle-desc">Plakietka + broszura PDF</div>
                </div>
            </label>

            <label class="wz-toggle {{ old('has_gethelp',$car?->has_gethelp) ? 'active' : '' }}" data-toggle>
                <input type="hidden" name="has_gethelp" value="0">
                <input type="checkbox" name="has_gethelp" value="1" {{ old('has_gethelp',$car?->has_gethelp)?'checked':'' }}>
                <div class="wz-toggle-track"><div class="wz-toggle-thumb"></div></div>
                <div>
                    <div class="wz-toggle-label"><i data-lucide="shield" style="width:14px;height:14px;color:#f59e0b"></i> GetHelp w cenie</div>
                    <div class="wz-toggle-desc">
                        <select name="gethelp_package" style="padding:5px 8px;font-size:11.5px;border-radius:6px;border:1px solid var(--border);margin-top:4px" onclick="event.stopPropagation()">
                            <option value="Classic" {{ old('gethelp_package',$car?->gethelp_package)=='Classic'?'selected':'' }}>Classic</option>
                            <option value="Comfort" {{ old('gethelp_package',$car?->gethelp_package)=='Comfort'?'selected':'' }}>Comfort</option>
                            <option value="Grand" {{ old('gethelp_package',$car?->gethelp_package)=='Grand'?'selected':'' }}>Grand</option>
                        </select>
                    </div>
                </div>
            </label>
        </div>
    </div>



</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 2 — Zdjęcia i media                                  ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="2">

    {{-- Gallery upload --}}
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">1</div>
            <div>
                <div class="wz-section-title">Galeria zdjęć</div>
                <div class="wz-section-subtitle">Przeciągnij aby zmienić kolejność · alt text auto z tytułu</div>
            </div>
        </div>

        @if(!$car)
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 16px;margin-bottom:14px;font-size:13px;color:#1e40af;display:flex;gap:8px;align-items:flex-start">
            <i data-lucide="info" style="width:16px;height:16px;flex-shrink:0;margin-top:1px"></i>
            <span>Zdjęcia zostaną zapisane razem z autem po kliknięciu <strong>"Zapisz"</strong>. Przy pierwszym zapisie zalecamy max <strong>10–15 zdjęć naraz</strong> (łącznie do ~250 MB). Więcej zdjęć można dodać po zapisaniu auta — każde wgra się osobno bez limitu rozmiaru.</span>
        </div>
        @endif

        @if($car && $car->galleryImages->count())
        <div id="wzGalleryGrid" class="wz-img-grid" data-sortable data-type="gallery">
            @foreach($car->galleryImages as $img)
            <div class="wz-img-tile sort-item" data-img-id="{{ $img->id }}" draggable="true">
                <img src="{{ $img->url }}" alt="{{ $img->alt }}">
                <span class="tile-drag" title="Przeciągnij, aby zmienić kolejność"><i data-lucide="grip-vertical"></i></span>
                <label class="tile-primary" title="Zaznacz jako główne"><input type="radio" name="primary_image_id" value="{{ $img->id }}" {{ $img->is_primary?'checked':'' }}> Główne</label>
                <div class="tile-actions">
                    <button type="button" class="tile-del" onclick="wzToggleDelete(this,{{ $img->id }})"><i data-lucide="x"></i></button>
                </div>
                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" style="display:none">
                <input type="hidden" name="image_order[]" value="{{ $img->id }}" class="sort-order-input">
                <div class="tile-alt">
                    <input type="text" name="image_alt[{{ $img->id }}]" value="{{ $img->alt_text }}" placeholder="{{ $img->alt }}" title="Alt text — opis zdjęcia dla SEO">
                </div>
            </div>
            @endforeach
        </div>
        @elseif($car)
        <p style="color:var(--text-3);font-size:12.5px;margin-bottom:10px">Brak zdjęć galerii.</p>
        @endif

        <label class="wz-file-drop" id="wzGalleryDrop" data-upload-type="gallery">
            <i data-lucide="image-plus"></i>
            <div class="drop-title">Kliknij lub przeciągnij pliki, aby dodać zdjęcia galerii</div>
            <div class="drop-hint">JPEG, PNG, WebP, AVIF — max 20 MB na zdjęcie</div>
            <input type="file" name="gallery_images[]" multiple accept="image/*">
        </label>
        <div class="wz-upload-progress" id="wzGalleryUploadProgress">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <div class="wz-progress-bar-wrap" style="flex:1"><div class="wz-progress-bar" id="wzGalleryProgressBar"></div></div>
                <span id="wzGalleryProgressText" style="font-size:12px;font-weight:600;color:var(--text-2);white-space:nowrap">0/0</span>
            </div>
        </div>
        <div id="wzGalleryUploadedGrid" class="wz-file-preview-grid"></div>
    </div>

    {{-- Damage photos --}}
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">2</div>
            <div>
                <div class="wz-section-title">Zdjęcia stanu wizualnego</div>
                <div class="wz-section-subtitle">Zdjęcia dokumentujące stan wizualny pojazdu</div>
            </div>
        </div>

        @if($car && $car->damageImages->count())
        <div class="wz-img-grid" style="margin-bottom:14px">
            @foreach($car->damageImages as $img)
            <div class="wz-img-tile" data-img-id="{{ $img->id }}">
                <img src="{{ $img->url }}" alt="{{ $img->alt }}">
                <div class="tile-actions">
                    <button type="button" class="tile-del" onclick="wzToggleDelete(this,{{ $img->id }})"><i data-lucide="x"></i></button>
                </div>
                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" style="display:none">
                <div class="tile-alt">
                    <input type="text" name="image_alt[{{ $img->id }}]" value="{{ $img->alt_text }}" placeholder="{{ $img->alt }}">
                </div>
            </div>
            @endforeach
        </div>
        @elseif($car)
        <p style="color:var(--text-3);font-size:12.5px;margin-bottom:10px">Brak zdjęć stanu wizualnego.</p>
        @endif

        <label class="wz-file-drop" id="wzDamageDrop" data-upload-type="damage">
            <i data-lucide="image-plus"></i>
            <div class="drop-title">Kliknij lub przeciągnij pliki, aby dodać zdjęcia stanu wizualnego</div>
            <div class="drop-hint">JPEG, PNG, WebP, AVIF — max 20 MB na zdjęcie</div>
            <input type="file" name="damage_images[]" multiple accept="image/*">
        </label>
        <div class="wz-upload-progress" id="wzDamageUploadProgress">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <div class="wz-progress-bar-wrap" style="flex:1"><div class="wz-progress-bar" id="wzDamageProgressBar"></div></div>
                <span id="wzDamageProgressText" style="font-size:12px;font-weight:600;color:var(--text-2);white-space:nowrap">0/0</span>
            </div>
        </div>
        <div id="wzDamageUploadedGrid" class="wz-file-preview-grid"></div>
    </div>

    {{-- 360° interior — video (preferred, Copart-style) --}}
    <div class="wz-media-section">
        <div class="wz-media-header">
            <i data-lucide="film"></i>
            <h3>360° wnętrza — film</h3>
        </div>
        <p style="font-size:12px;color:var(--text-3);margin:0 0 14px">
            Wgraj krótki obrót kamerą wewnątrz pojazdu (3–6 s, MP4/WebM/MOV, max 200 MB).
            System sam wytnie {{ \App\Services\InteriorFrameExtractor::FRAME_COUNT }} klatek do interaktywnego przeglądania w katalogu.
            Status zmienia się na „gotowe” po przetworzeniu w tle.
        </p>

        @if($car?->interior_video_path)
        @php
            $intStatus = $car->interior_frames_status;
            $intPill = match($intStatus) {
                'ready' => ['#10b981','#ecfdf5','Gotowe — '.$car->interior_frames_count.' klatek'],
                'processing' => ['#0066ff','#eff6ff','Przetwarzanie…'],
                'pending' => ['#6b7280','#f3f4f6','W kolejce'],
                'failed' => ['#b91c1c','#fef2f2','Błąd ekstrakcji'],
                default => ['#6b7280','#f3f4f6','Stan nieznany'],
            };
        @endphp
        <div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:12px;margin-bottom:12px;display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap">
            <div style="flex:1;min-width:240px">
                <div style="font-weight:600;font-size:13px;margin-bottom:6px">
                    <span style="display:inline-block;padding:3px 9px;border-radius:50px;font-size:11.5px;font-weight:700;color:{{ $intPill[0] }};background:{{ $intPill[1] }}">{{ $intPill[2] }}</span>
                </div>
                <div style="font-size:11.5px;color:var(--text-3);word-break:break-all;margin-bottom:8px">{{ $car->interior_video_path }}</div>
                @if($intStatus === 'failed' && $car->interior_frames_error)
                <div style="font-size:11px;color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:6px 8px;margin-bottom:8px">{{ $car->interior_frames_error }}</div>
                @endif
                <label class="wz-inline-label" style="color:#b91c1c;font-size:12px"><input type="checkbox" name="remove_interior_video" value="1"> Usuń film i wszystkie klatki przy zapisie</label>
            </div>
        </div>
        @endif

        <label class="wz-file-drop" id="wzInteriorVidDrop">
            <i data-lucide="film"></i>
            <div class="drop-title">Kliknij lub przeciągnij <strong>film z wnętrza</strong> (MP4, WebM, MOV — max 200 MB)</div>
            <input type="file" name="interior_video_file" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska">
        </label>
    </div>

    {{-- 360° interior panorama (legacy / fallback) --}}
    <div class="wz-media-section">
        <div class="wz-media-header">
            <i data-lucide="globe"></i>
            <h3>Panorama 360° — wnętrze <span style="font-size:11px;color:var(--text-3);font-weight:500">(zapas)</span></h3>
        </div>
        <p style="font-size:12px;color:var(--text-3);margin:0 0 14px">Pojedyncze zdjęcie equirectangular (format 2:1, np. 4096×2048 px). Pokaże się tylko, gdy film z wnętrza nie został wgrany.</p>

        @if($car?->pano360Image)
        <div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:12px;margin-bottom:12px;display:flex;gap:14px;align-items:flex-start">
            <img src="{{ $car->pano360Image->url }}" alt="" style="width:200px;height:100px;object-fit:cover;border-radius:8px;background:#000">
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:13px;margin-bottom:4px"><i data-lucide="check-circle" style="width:14px;height:14px;color:#10b981;vertical-align:-2px"></i> Panorama aktywna</div>
                <div style="font-size:11.5px;color:var(--text-3);word-break:break-all;margin-bottom:8px">{{ $car->pano360Image->path }}</div>
                <label class="wz-inline-label" style="color:#b91c1c;font-size:12px"><input type="checkbox" name="remove_pano360" value="1"> Usuń panoramę przy zapisie</label>
            </div>
        </div>
        @endif

        <label class="wz-file-drop" id="wzPano360Drop">
            <i data-lucide="globe"></i>
            <div class="drop-title">Kliknij lub przeciągnij <strong>jedno</strong> zdjęcie panoramiczne (max 15 MB)</div>
            <input type="file" name="pano360_image" accept="image/jpeg,image/png,image/webp">
        </label>
    </div>

    {{-- 360° exterior — video (preferred, Copart-style walk-around) --}}
    <div class="wz-media-section">
        <div class="wz-media-header">
            <i data-lucide="film"></i>
            <h3>360° zewnętrza — film</h3>
        </div>
        <p style="font-size:12px;color:var(--text-3);margin:0 0 14px">
            Wgraj krótki obrót kamerą wokół pojazdu (3–6 s, MP4/WebM/MOV, max 200 MB).
            System sam wytnie {{ \App\Services\InteriorFrameExtractor::FRAME_COUNT }} klatek do interaktywnego oglądania auta z każdej strony.
            Status zmienia się na „gotowe” po przetworzeniu w tle.
        </p>

        @if($car?->exterior_video_path)
        @php
            $extStatus = $car->exterior_frames_status;
            $extPill = match($extStatus) {
                'ready' => ['#10b981','#ecfdf5','Gotowe — '.$car->exterior_frames_count.' klatek'],
                'processing' => ['#0066ff','#eff6ff','Przetwarzanie…'],
                'pending' => ['#6b7280','#f3f4f6','W kolejce'],
                'failed' => ['#b91c1c','#fef2f2','Błąd ekstrakcji'],
                default => ['#6b7280','#f3f4f6','Stan nieznany'],
            };
        @endphp
        <div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:12px;margin-bottom:12px;display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap">
            <div style="flex:1;min-width:240px">
                <div style="font-weight:600;font-size:13px;margin-bottom:6px">
                    <span style="display:inline-block;padding:3px 9px;border-radius:50px;font-size:11.5px;font-weight:700;color:{{ $extPill[0] }};background:{{ $extPill[1] }}">{{ $extPill[2] }}</span>
                </div>
                <div style="font-size:11.5px;color:var(--text-3);word-break:break-all;margin-bottom:8px">{{ $car->exterior_video_path }}</div>
                @if($extStatus === 'failed' && $car->exterior_frames_error)
                <div style="font-size:11px;color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:6px 8px;margin-bottom:8px">{{ $car->exterior_frames_error }}</div>
                @endif
                <label class="wz-inline-label" style="color:#b91c1c;font-size:12px"><input type="checkbox" name="remove_exterior_video" value="1"> Usuń film i wszystkie klatki przy zapisie</label>
            </div>
        </div>
        @endif

        <label class="wz-file-drop" id="wzExteriorVidDrop">
            <i data-lucide="film"></i>
            <div class="drop-title">Kliknij lub przeciągnij <strong>film dookoła auta</strong> (MP4, WebM, MOV — max 200 MB)</div>
            <input type="file" name="exterior_video_file" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska">
        </label>
    </div>

    {{-- 360° exterior panorama (legacy / fallback) --}}
    <div class="wz-media-section">
        <div class="wz-media-header">
            <i data-lucide="scan"></i>
            <h3>Panorama 360° — zewnętrze <span style="font-size:11px;color:var(--text-3);font-weight:500">(zapas)</span></h3>
        </div>
        <p style="font-size:12px;color:var(--text-3);margin:0 0 14px">Pojedyncze zdjęcie equirectangular obejmujące pełny widok dookoła pojazdu. Pokaże się tylko, gdy film z zewnętrza nie został wgrany.</p>

        @if($car?->exteriorPano360Image)
        <div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:12px;margin-bottom:12px;display:flex;gap:14px;align-items:flex-start">
            <img src="{{ $car->exteriorPano360Image->url }}" alt="" style="width:200px;height:100px;object-fit:cover;border-radius:8px;background:#000">
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:13px;margin-bottom:4px"><i data-lucide="check-circle" style="width:14px;height:14px;color:#10b981;vertical-align:-2px"></i> Panorama zewnętrzna aktywna</div>
                <div style="font-size:11.5px;color:var(--text-3);word-break:break-all;margin-bottom:8px">{{ $car->exteriorPano360Image->path }}</div>
                <label class="wz-inline-label" style="color:#b91c1c;font-size:12px"><input type="checkbox" name="remove_pano360ext" value="1"> Usuń panoramę przy zapisie</label>
            </div>
        </div>
        @endif

        <label class="wz-file-drop" id="wzPano360extDrop">
            <i data-lucide="scan"></i>
            <div class="drop-title">Kliknij lub przeciągnij <strong>jedno</strong> zdjęcie panoramiczne zewnętrzne (max 15 MB)</div>
            <input type="file" name="pano360ext_image" accept="image/jpeg,image/png,image/webp">
        </label>
    </div>

    {{-- Engine video --}}
    <div class="wz-media-section">
        <div class="wz-media-header">
            <i data-lucide="film"></i>
            <h3>Film z pracy silnika</h3>
        </div>
        <p style="font-size:12px;color:var(--text-3);margin:0 0 14px">Link YouTube/Vimeo <strong>lub</strong> plik wideo (MP4, WebM, MOV — max 100 MB).</p>

        <div style="display:flex;gap:4px;background:var(--bg);padding:4px;border-radius:9px;margin-bottom:14px;width:fit-content">
            <button type="button" id="wzVidTabUrl" class="btn btn-sm" style="background:{{ $car?->engine_video_url ? '#fff' : 'transparent' }};border:none">🔗 Link URL</button>
            <button type="button" id="wzVidTabFile" class="btn btn-sm" style="background:{{ $car?->engine_video_path && !$car?->engine_video_url ? '#fff' : 'transparent' }};border:none">📤 Plik na serwerze</button>
        </div>

        <div id="wzVidUrlPanel">
            <div class="wz-field" style="margin-bottom:10px">
                <label>URL filmu</label>
                <input type="url" name="engine_video_url" id="wzEngineVideoUrl" value="{{ old('engine_video_url',$car?->engine_video_url) }}" placeholder="https://youtu.be/... lub https://vimeo.com/...">
            </div>
            <div id="wzVidUrlPreview"></div>
        </div>

        <div id="wzVidFilePanel" style="display:none">
            @if($car?->engine_video_path)
            <div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:10px;margin-bottom:10px;display:flex;gap:12px;align-items:center">
                <video src="{{ $car->engine_video_file_url }}" controls preload="metadata" style="max-width:220px;border-radius:8px;background:#000"></video>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:13px;margin-bottom:4px">Aktualny plik</div>
                    <div style="font-size:11.5px;color:var(--text-3);word-break:break-all">{{ $car->engine_video_path }}</div>
                    <label class="wz-inline-label" style="margin-top:8px;color:#b91c1c"><input type="checkbox" name="remove_engine_video" value="1"> Usuń ten plik przy zapisie</label>
                </div>
            </div>
            @endif
            <label class="wz-file-drop" id="wzVideoDrop">
                <i data-lucide="film"></i>
                <div class="drop-title">{{ $car?->engine_video_path ? 'Kliknij lub przeciągnij, aby podmienić plik' : 'Kliknij lub przeciągnij plik wideo' }}</div>
                <input type="file" name="engine_video_file" id="wzEngineVideoFile" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska">
            </label>
            <div id="wzVideoLocalPreview" style="margin-top:10px"></div>
        </div>
    </div>

</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 3 — Historia pojazdu                                 ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="3">

    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">1</div>
            <div>
                <div class="wz-section-title">Historia pojazdu</div>
                <div class="wz-section-subtitle">Właściciele, pochodzenie, import</div>
            </div>
        </div>

        <div class="wz-field" style="margin-bottom:14px">
            <label>Liczba właścicieli</label>
            <select name="previous_owners">
                <option value="" {{ !old('previous_owners',$car?->previous_owners)?'selected':'' }}>— wybierz —</option>
                <option value="1" {{ old('previous_owners',$car?->previous_owners)==1?'selected':'' }}>1</option>
                <option value="2" {{ old('previous_owners',$car?->previous_owners)==2?'selected':'' }}>2</option>
                <option value="3" {{ old('previous_owners',$car?->previous_owners)==3?'selected':'' }}>3+</option>
                <option value="Brak danych" {{ old('previous_owners',$car?->previous_owners)=='Brak danych'?'selected':'' }}>Brak danych</option>
            </select>
            <small style="font-size:11px;color:var(--text-3);margin-top:4px;display:block">Kraj pochodzenia wpisz w kroku 1 „Dane podstawowe".</small>
        </div>

        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field"><label>Sprowadzony z</label>
                <input type="text" name="imported_from" value="{{ old('imported_from',$car?->imported_from) }}" placeholder="Niemcy, Francja, Holandia...">
            </div>
            <div class="wz-field"><label>Pojazd importowany</label>
                <select name="business_use">
                    <option value="" {{ !old('business_use',$car?->business_use)?'selected':'' }}>— wybierz —</option>
                    <option value="Tak" {{ old('business_use',$car?->business_use)=='Tak'?'selected':'' }}>Tak</option>
                    <option value="Nie" {{ old('business_use',$car?->business_use)=='Nie'?'selected':'' }}>Nie</option>
                </select>
            </div>
        </div>

        <div class="wz-field">
            <label>Historia pojazdu</label>
            <select name="vehicle_history">
                <option value="" {{ !old('vehicle_history',$car?->vehicle_history)?'selected':'' }}>— wybierz —</option>
                <option value="Dostępna" {{ old('vehicle_history',$car?->vehicle_history)=='Dostępna'?'selected':'' }}>Dostępna</option>
                <option value="Częściowo dostępna" {{ old('vehicle_history',$car?->vehicle_history)=='Częściowo dostępna'?'selected':'' }}>Częściowo dostępna</option>
                <option value="Brak pełnej historii" {{ old('vehicle_history',$car?->vehicle_history)=='Brak pełnej historii'?'selected':'' }}>Brak pełnej historii</option>
            </select>
        </div>
    </div>

    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">2</div>
            <div>
                <div class="wz-section-title">Dodatkowe informacje</div>
                <div class="wz-section-subtitle">Nadwozie, masa, kolory, tapicerka</div>
            </div>
        </div>

        <div class="wz-grid-3" style="margin-bottom:14px">
            <div class="wz-field">
                <label>Masa (kg)</label>
                <input type="number" name="weight" value="{{ old('weight',$car?->weight) }}" min="0">
            </div>
            <div class="wz-field">
                <label>Kod koloru</label>
                <input type="text" name="color_code" value="{{ old('color_code',$car?->color_code) }}">
            </div>
            <div class="wz-field">
                <label>Tapicerka</label>
                <input type="text" name="upholstery" value="{{ old('upholstery',$car?->upholstery) }}">
            </div>
        </div>
    </div>

</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 4 — Serwisowanie                                     ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="4">
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">1</div>
            <div><div class="wz-section-title">Serwisowanie</div><div class="wz-section-subtitle">Historia serwisowa pojazdu</div></div>
        </div>
        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field"><label>Serwisowany w ASO</label>
                <select name="aso_serviced">
                    <option value="" {{ !old('aso_serviced',$car?->aso_serviced)?'selected':'' }}>— wybierz —</option>
                    <option value="Tak" {{ old('aso_serviced',$car?->aso_serviced)=='Tak'?'selected':'' }}>Tak</option>
                    <option value="Nie" {{ old('aso_serviced',$car?->aso_serviced)=='Nie'?'selected':'' }}>Nie</option>
                    <option value="Częściowo" {{ old('aso_serviced',$car?->aso_serviced)=='Częściowo'?'selected':'' }}>Częściowo</option>
                    <option value="Brak danych" {{ old('aso_serviced',$car?->aso_serviced)=='Brak danych'?'selected':'' }}>Brak danych</option>
                </select>
            </div>
            <div class="wz-field"><label>Historia serwisowa</label>
                <select name="service_history">
                    <option value="" {{ !old('service_history',$car?->service_history)?'selected':'' }}>— wybierz —</option>
                    <option value="Dostępna" {{ old('service_history',$car?->service_history)=='Dostępna'?'selected':'' }}>Dostępna</option>
                    <option value="Częściowo dostępna" {{ old('service_history',$car?->service_history)=='Częściowo dostępna'?'selected':'' }}>Częściowo dostępna</option>
                    <option value="Brak pełnej historii" {{ old('service_history',$car?->service_history)=='Brak pełnej historii'?'selected':'' }}>Brak pełnej historii</option>
                </select>
            </div>
        </div>
        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field"><label>Ostatni serwis (miesiąc/rok)</label><input type="text" name="last_service" value="{{ old('last_service',$car?->last_service) }}" placeholder="03/2024"></div>
            <div class="wz-field"><label>Przy przebiegu</label><input type="text" name="last_service_mileage" value="{{ old('last_service_mileage',$car?->last_service_mileage) }}" placeholder="108 200 km"></div>
        </div>
        <div class="wz-field">
            <label>Przegląd ważny do (miesiąc/rok)</label>
            <input type="text" name="next_inspection" value="{{ old('next_inspection',$car?->next_inspection) }}" placeholder="03/2025">
        </div>
    </div>
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">2</div>
            <div><div class="wz-section-title">Emisja i zużycie</div><div class="wz-section-subtitle">Parametry ekologiczne</div></div>
        </div>
        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field"><label>Zużycie paliwa</label><input type="text" name="fuel_consumption" value="{{ old('fuel_consumption',$car?->fuel_consumption) }}" placeholder="5.6 l/100km"></div>
            <div class="wz-field"><label>Emisja CO₂</label><input type="text" name="co2_emission" value="{{ old('co2_emission',$car?->co2_emission) }}" placeholder="115 g/km"></div>
        </div>
        <div class="wz-grid-2">
            <div class="wz-field"><label>Klasa emisji</label><input type="text" name="emission_class" value="{{ old('emission_class',$car?->emission_class) }}" placeholder="Euro 6"></div>
            <div class="wz-field"><label>Procedura pomiaru</label><input type="text" name="fuel_procedure" value="{{ old('fuel_procedure',$car?->fuel_procedure) }}" placeholder="WLTP"></div>
        </div>
    </div>
</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 5 — Dokumenty i sprzedawca                           ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="5">
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">1</div>
            <div><div class="wz-section-title">Dokumenty</div><div class="wz-section-subtitle">Komplet dokumentacji pojazdu</div></div>
        </div>
        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field"><label>Książka serwisowa</label>
                <select name="service_book_status">
                    <option value="" {{ !old('service_book_status',$car?->service_book_status)?'selected':'' }}>— wybierz —</option>
                    <option value="Dostępna" {{ old('service_book_status',$car?->service_book_status)=='Dostępna'?'selected':'' }}>Dostępna</option>
                    <option value="Niedostępna" {{ old('service_book_status',$car?->service_book_status)=='Niedostępna'?'selected':'' }}>Niedostępna</option>
                    <option value="Elektroniczna" {{ old('service_book_status',$car?->service_book_status)=='Elektroniczna'?'selected':'' }}>Elektroniczna</option>
                    <option value="Częściowa" {{ old('service_book_status',$car?->service_book_status)=='Częściowa'?'selected':'' }}>Częściowa</option>
                </select>
            </div>
            <div class="wz-field"><label>Dowód rejestracyjny</label>
                <select name="registration_cert">
                    <option value="" {{ !old('registration_cert',$car?->registration_cert)?'selected':'' }}>— wybierz —</option>
                    <option value="Dostępny" {{ old('registration_cert',$car?->registration_cert)=='Dostępny'?'selected':'' }}>Dostępny</option>
                    <option value="Niedostępny" {{ old('registration_cert',$car?->registration_cert)=='Niedostępny'?'selected':'' }}>Niedostępny</option>
                </select>
            </div>
        </div>
        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field"><label>Liczba kluczyków</label>
                <select name="number_of_keys">
                    <option value="" {{ !old('number_of_keys',$car?->number_of_keys)?'selected':'' }}>— wybierz —</option>
                    <option value="1" {{ old('number_of_keys',$car?->number_of_keys)==1?'selected':'' }}>1</option>
                    <option value="2" {{ old('number_of_keys',$car?->number_of_keys)==2?'selected':'' }}>2</option>
                    <option value="3" {{ old('number_of_keys',$car?->number_of_keys)==3?'selected':'' }}>3</option>
                </select>
            </div>
            <div class="wz-field"><label>Instrukcja obsługi</label>
                <select name="owners_manual">
                    <option value="" {{ !old('owners_manual',$car?->owners_manual)?'selected':'' }}>— wybierz —</option>
                    <option value="Dostępna" {{ old('owners_manual',$car?->owners_manual)=='Dostępna'?'selected':'' }}>Dostępna</option>
                    <option value="Niedostępna" {{ old('owners_manual',$car?->owners_manual)=='Niedostępna'?'selected':'' }}>Niedostępna</option>
                </select>
            </div>
        </div>
    </div>
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge">2</div>
            <div><div class="wz-section-title">Sprzedawca</div><div class="wz-section-subtitle">Dane wewnętrzne (nie wyświetlane publicznie)</div></div>
        </div>
        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field"><label>Nazwa / imię i nazwisko</label><input type="text" name="seller_name" value="{{ old('seller_name',$car?->seller_name) }}"></div>
            <div class="wz-field"><label>Data przyjęcia pojazdu</label><input type="date" name="reception_date" value="{{ old('reception_date',$car?->reception_date) }}"></div>
        </div>
        <div class="wz-grid-2" style="margin-bottom:14px">
            <div class="wz-field"><label>Telefon</label><input type="text" name="seller_phone" value="{{ old('seller_phone',$car?->seller_phone) }}"></div>
            <div class="wz-field"><label>E-mail</label><input type="email" name="seller_email" value="{{ old('seller_email',$car?->seller_email) }}"></div>
        </div>
        <div class="wz-field"><label>Notatka komisowa (wewnętrzna)</label><textarea name="commission_note" rows="3" style="min-height:70px">{{ old('commission_note',$car?->commission_note) }}</textarea></div>
    </div>
</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 6 — Wyposażenie                                      ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="6">
    {{-- Highlighted equipment — top 8 icon tiles shown on the public page --}}
    @php
        $highlighted = old('highlighted_equipment', $car?->highlighted_equipment ?? []);
        $highlighted = is_array($highlighted) ? array_pad(array_slice($highlighted, 0, 8), 8, null) : array_fill(0, 8, null);
        $eqGrouped   = \App\Helpers\EquipmentCatalog::optionsGroupedByCategory();
        $eqCatLabels = \App\Helpers\EquipmentCatalog::CATEGORIES;
    @endphp
    <div class="wz-section" style="margin-bottom:18px">
        <div class="wz-section-header">
            <div class="wz-section-badge"><i data-lucide="star" style="width:16px;height:16px"></i></div>
            <div><div class="wz-section-title">Najważniejsze wyposażenie (8 kafelków)</div><div class="wz-section-subtitle">Wybierz do 8 elementów, które wyróżnią się ikonami na górze sekcji Wyposażenie. Możesz zostawić puste sloty.</div></div>
        </div>
        @php
            // Lookup table for the per-slot icon preview. JS reads it via data-eq-icon
            // on the wrapper and swaps the visible Lucide icon when the select changes.
            $iconMap = [];
            foreach (\App\Helpers\EquipmentCatalog::OPTIONS as $key => $opt) {
                $iconMap[$key] = $opt['icon'];
            }
        @endphp
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px" id="wzEqSlots" data-eq-icon-map='@json($iconMap)'>
            @for($i = 0; $i < 8; $i++)
            @php
                $currentKey = $highlighted[$i] ?? null;
                $currentIcon = $currentKey ? ($iconMap[$currentKey] ?? null) : null;
            @endphp
            <div class="wz-field wz-eq-slot" style="margin:0">
                <label>Slot {{ $i + 1 }}</label>
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="wz-eq-slot-preview" style="flex-shrink:0;width:42px;height:42px;border-radius:10px;background:#eff6ff;color:#0066ff;display:flex;align-items:center;justify-content:center;border:1px solid #dbe6ff">
                        <i data-lucide="{{ $currentIcon ?? 'star' }}" data-default="star" style="width:22px;height:22px;{{ $currentIcon ? '' : 'opacity:.35' }}"></i>
                    </div>
                    <select name="highlighted_equipment[]" class="wz-eq-slot-select" style="flex:1;min-width:0">
                        <option value="">— pusty —</option>
                        @foreach($eqGrouped as $catKey => $catOptions)
                            @if(!empty($catOptions))
                            <optgroup label="{{ $eqCatLabels[$catKey]['label'] }}">
                                @foreach($catOptions as $optKey => $opt)
                                <option value="{{ $optKey }}" @selected($currentKey === $optKey)>{{ $opt['label'] }}</option>
                                @endforeach
                            </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            @endfor
        </div>
        <script>
        (function(){
            // Live icon preview: when admin picks an option from the dropdown, swap
            // the Lucide icon in the preview tile next to it. Avoids needing a
            // page reload to see what each slot will look like on the frontend.
            const root = document.getElementById('wzEqSlots');
            if(!root) return;
            const map = JSON.parse(root.dataset.eqIconMap || '{}');
            root.addEventListener('change', function(e){
                const sel = e.target.closest('.wz-eq-slot-select');
                if(!sel) return;
                const slot = sel.closest('.wz-eq-slot');
                const icoHost = slot && slot.querySelector('.wz-eq-slot-preview');
                if(!icoHost) return;
                const iconName = map[sel.value] || icoHost.querySelector('[data-default]')?.dataset.default || 'star';
                icoHost.innerHTML = '<i data-lucide="' + iconName + '" data-default="star" style="width:22px;height:22px;' + (sel.value ? '' : 'opacity:.35') + '"></i>';
                if(window.lucide) window.lucide.createIcons();
            });
        })();
        </script>
    </div>

    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge"><i data-lucide="list-checks" style="width:16px;height:16px"></i></div>
            <div><div class="wz-section-title">Wyposażenie</div><div class="wz-section-subtitle">Wpisz elementy wyposażenia — jeden na linię</div></div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:18px">
            @foreach($eqCategories as $key => $label)
            <span style="display:inline-flex;align-items:center;padding:6px 14px;background:var(--bg);border:1px solid var(--border-l);border-radius:50px;font-size:12px;font-weight:600;color:var(--text-2);cursor:pointer" onclick="document.getElementById('eq_{{$key}}').focus()">{{ $label }}</span>
            @endforeach
        </div>
        @foreach($eqCategories as $key => $label)
        <div class="wz-field" style="margin-bottom:16px">
            <label>{{ $label }}</label>
            <textarea name="equipment[{{ $key }}]" id="eq_{{$key}}" rows="4" style="min-height:80px" placeholder="Wpisz elementy, jeden na linię...">{{ $eqToText($equipment[$key] ?? []) }}</textarea>
        </div>
        @endforeach
    </div>
</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 7 — Stan wizualny i ślady użytkowania                ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="7">
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge"><i data-lucide="scan-eye" style="width:16px;height:16px"></i></div>
            <div><div class="wz-section-title">Stan wizualny i ślady użytkowania</div><div class="wz-section-subtitle">Kliknij na schemacie pojazdu, aby zaznaczyć uszkodzenie</div></div>
        </div>

        {{-- Legend — 3 types like the reference --}}
        <div style="display:flex;flex-wrap:wrap;gap:20px;margin-bottom:18px;font-size:12.5px;font-weight:600">
            <span style="display:flex;align-items:center;gap:7px"><span style="width:24px;height:24px;border-radius:50%;background:#dc2626;display:flex;align-items:center;justify-content:center"><svg width="12" height="12" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M12 2L2 22h20L12 2z"/></svg></span> Szkoda wypadkowa</span>
            <span style="display:flex;align-items:center;gap:7px"><span style="width:24px;height:24px;border-radius:50%;background:#f59e0b;display:flex;align-items:center;justify-content:center"><svg width="12" height="12" viewBox="0 0 24 24" fill="#000" stroke="none"><path d="M12 2L2 22h20L12 2zM12 8v6M12 17h.01"/></svg></span> Uszkodzenia</span>
            <span style="display:flex;align-items:center;gap:7px"><span style="width:24px;height:24px;border-radius:50%;background:#9ca3af;display:flex;align-items:center;justify-content:center"><svg width="12" height="12" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.77 3.77z"/></svg></span> Naprawione szkody</span>
        </div>

        {{-- Active damage type selector --}}
        <div style="display:flex;gap:6px;margin-bottom:14px">
            <button type="button" class="wz-dtype-btn active" data-dtype="damage" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:2px solid #f59e0b;background:#fffbeb;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;transition:.15s;color:#92400e">
                <span style="width:20px;height:20px;border-radius:50%;background:#f59e0b;display:inline-flex;align-items:center;justify-content:center"><svg width="10" height="10" viewBox="0 0 24 24" fill="#000" stroke="none"><path d="M12 2L2 22h20L12 2z"/></svg></span> Uszkodzenie
            </button>
            <button type="button" class="wz-dtype-btn" data-dtype="accident" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:2px solid transparent;background:#fafafa;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;transition:.15s;color:var(--text-3)">
                <span style="width:20px;height:20px;border-radius:50%;background:#dc2626;display:inline-flex;align-items:center;justify-content:center"><svg width="10" height="10" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M12 2L2 22h20L12 2z"/></svg></span> Wypadkowa
            </button>
            <button type="button" class="wz-dtype-btn" data-dtype="repaired" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:2px solid transparent;background:#fafafa;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;transition:.15s;color:var(--text-3)">
                <span style="width:20px;height:20px;border-radius:50%;background:#9ca3af;display:inline-flex;align-items:center;justify-content:center"><svg width="10" height="10" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.77 3.77z"/></svg></span> Naprawiona
            </button>
        </div>

        {{-- View switch buttons --}}
        <div class="wz-view-switch" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px">
            <button type="button" class="wz-view-btn active" data-view="top" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--border-l);background:#fff;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:.15s">
                <span class="svg-ic" style="width:16px;height:16px;display:inline-flex">@include('admin.cars.partials.car-icon-top')</span> Góra
            </button>
            <button type="button" class="wz-view-btn" data-view="front" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--border-l);background:#fff;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:.15s">
                <span class="svg-ic" style="width:16px;height:16px;display:inline-flex">@include('admin.cars.partials.car-icon-front')</span> Przód
            </button>
            <button type="button" class="wz-view-btn" data-view="rear" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--border-l);background:#fff;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:.15s">
                <span class="svg-ic" style="width:16px;height:16px;display:inline-flex">@include('admin.cars.partials.car-icon-rear')</span> Tył
            </button>
            <button type="button" class="wz-view-btn" data-view="left" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--border-l);background:#fff;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:.15s">
                <span class="svg-ic" style="width:16px;height:16px;display:inline-flex">@include('admin.cars.partials.car-icon-side')</span> Lewy bok
            </button>
            <button type="button" class="wz-view-btn" data-view="right" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--border-l);background:#fff;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:.15s">
                <span class="svg-ic" style="width:16px;height:16px;display:inline-flex;transform:scaleX(-1)">@include('admin.cars.partials.car-icon-side')</span> Prawy bok
            </button>
        </div>

        {{-- Car diagram --}}
        <div class="wz-car-diagram" id="wzCarDiagram" style="position:relative;background:linear-gradient(180deg,#fafafb,#f0f0f2);border:1px solid var(--border-l);border-radius:14px;padding:24px;cursor:crosshair;user-select:none;min-height:360px;margin-bottom:18px">
            <div id="wzSvgStage" style="max-width:540px;margin:0 auto;position:relative">
                <div class="wz-svg-view active" data-view="top">
                    @php
                        $bodyTypeMap = [
                            'sedan' => 'sedan', 'suv' => 'suv', 'coupé' => 'coupe', 'coupe' => 'coupe',
                            'bus' => 'van', 'van' => 'van', 'kombi' => 'kombi', 'hatchback' => 'hatchback',
                            'kabriolet' => 'sedan', 'cabriolet' => 'sedan', 'pickup' => 'suv',
                        ];
                        $btKey = strtolower(old('body_type', $car?->body_type ?? 'sedan'));
                        $topImg = $bodyTypeMap[$btKey] ?? 'sedan';
                    @endphp
                    <img id="wzDamageTopImg" src="/img/body-types-top/{{ $topImg }}.png" alt="Schemat pojazdu" draggable="false" style="width:100%;height:auto;display:block;pointer-events:none">
                </div>
                <div class="wz-svg-view" data-view="front" style="display:none">@include('admin.cars.partials.view-front')</div>
                <div class="wz-svg-view" data-view="rear" style="display:none">@include('admin.cars.partials.view-rear')</div>
                <div class="wz-svg-view" data-view="left" style="display:none">@include('admin.cars.partials.view-side')</div>
                <div class="wz-svg-view" data-view="right" style="display:none;transform:scaleX(-1)">@include('admin.cars.partials.view-side')</div>
            </div>
            <div id="wzDiagramMarkers" style="position:absolute;inset:24px;pointer-events:none"></div>
            <div style="position:absolute;top:14px;right:14px;background:rgba(10,10,10,.78);color:#fff;font-size:11px;padding:6px 11px;border-radius:999px;pointer-events:none;display:flex;align-items:center;gap:5px;backdrop-filter:blur(6px)"><i data-lucide="mouse-pointer-click" style="width:12px;height:12px"></i> Kliknij aby dodać marker</div>
            <div id="wzViewLabel" style="position:absolute;top:14px;left:14px;background:rgba(255,255,255,.92);color:var(--text);font-size:11.5px;font-weight:700;padding:5px 12px;border-radius:999px;pointer-events:none;text-transform:uppercase;letter-spacing:.4px;border:1px solid var(--border-l)">WIDOK: GÓRA</div>
        </div>

        {{-- Damage list — type icon + label + photo --}}
        <div id="wzDamageList">
            @foreach($existingDamages as $i => $dmg)
            <div class="wz-damage-item" data-idx="{{ $i }}" data-view="{{ $dmg->position_view ?? 'top' }}" data-dtype="{{ $dmg->type ?? 'damage' }}" style="padding:10px 14px;margin-bottom:6px;background:#fafafb;border:1px solid var(--border-l);border-radius:10px">
                <input type="hidden" name="damages[{{ $i }}][id]" value="{{ $dmg->id }}">
                <input type="hidden" name="damages[{{ $i }}][type]" value="{{ $dmg->type ?? 'damage' }}" class="wz-dtype-input">
                <input type="hidden" name="damages[{{ $i }}][position_x]" value="{{ $dmg->position_x ?? 50 }}" class="wz-pos-x">
                <input type="hidden" name="damages[{{ $i }}][position_y]" value="{{ $dmg->position_y ?? 50 }}" class="wz-pos-y">
                <input type="hidden" name="damages[{{ $i }}][position_view]" value="{{ $dmg->position_view ?? 'top' }}" class="wz-pos-view">
                <div style="display:flex;align-items:center;gap:10px">
                    <button type="button" class="wz-dtype-icon" onclick="wzCycleDamageType(this)" title="Kliknij aby zmienić typ" style="width:28px;height:28px;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $dmg->type=='accident'?'#dc2626':($dmg->type=='repaired'?'#9ca3af':'#f59e0b') }}">
                        @if($dmg->type=='accident')<svg width="12" height="12" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M12 2L2 22h20L12 2z"/></svg>
                        @elseif($dmg->type=='repaired')<svg width="12" height="12" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.77 3.77z"/></svg>
                        @else<svg width="12" height="12" viewBox="0 0 24 24" fill="#000" stroke="none"><path d="M12 2L2 22h20L12 2zM12 8v6M12 17h.01"/></svg>
                        @endif
                    </button>
                    <input type="text" name="damages[{{ $i }}][area]" value="{{ $dmg->area }}" placeholder="Podpis (np. Rysa na masce)" style="flex:1;font-weight:600;font-size:13.5px;border:none;background:transparent;outline:none;padding:0">
                    <label class="wz-dmg-photo-btn" title="Dodaj zdjęcia" style="width:30px;height:30px;border-radius:8px;border:1px dashed var(--border);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:.15s;color:var(--text-3)" onmouseenter="this.style.borderColor='var(--blue)';this.style.color='var(--blue)'" onmouseleave="this.style.borderColor='var(--border)';this.style.color='var(--text-3)'">
                        <i data-lucide="camera" style="width:14px;height:14px"></i>
                        <input type="file" name="damages[{{ $i }}][images][]" accept="image/*" multiple style="display:none" onchange="wzPreviewDmgImages(this)">
                    </label>
                    <button type="button" onclick="wzRemoveDamage(this)" style="background:none;border:none;color:#b91c1c;cursor:pointer;padding:4px;flex-shrink:0;opacity:.5;transition:.15s" onmouseenter="this.style.opacity=1" onmouseleave="this.style.opacity=.5"><i data-lucide="x" style="width:15px;height:15px"></i></button>
                </div>
                {{-- Existing photos --}}
                @php $dmgPhotos = $dmg->photos ?? collect(); @endphp
                @if($dmg->image_path || $dmgPhotos->count())
                <div class="wz-dmg-photos" style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;align-items:flex-start">
                    @if($dmg->image_path)
                    <div class="wz-dmg-thumb-item" style="position:relative">
                        {{-- P1 FIX: R2-aware accessor (CarDamage image_url) — see commit log. --}}
                        <img src="{{ $dmg->image_url ?? asset('images/placeholder-car.svg') }}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid var(--border-l)">
                        <label style="position:absolute;top:-4px;right:-4px;width:18px;height:18px;background:#ef4444;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:10px;line-height:1;border:2px solid #fff" title="Usuń">
                            <input type="checkbox" name="damages[{{ $i }}][remove_image]" value="1" style="display:none" onchange="this.closest('.wz-dmg-thumb-item').style.opacity=this.checked?'.3':'1'">✕
                        </label>
                    </div>
                    @endif
                    @foreach($dmgPhotos as $dp)
                    <div class="wz-dmg-thumb-item" style="position:relative">
                        {{-- P1 FIX: R2-aware accessor (CarImage url) — see commit log. --}}
                        <img src="{{ $dp->url }}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid var(--border-l)">
                        <label style="position:absolute;top:-4px;right:-4px;width:18px;height:18px;background:#ef4444;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:10px;line-height:1;border:2px solid #fff" title="Usuń">
                            <input type="checkbox" name="damages[{{ $i }}][remove_photos][]" value="{{ $dp->id }}" style="display:none" onchange="this.closest('.wz-dmg-thumb-item').style.opacity=this.checked?'.3':'1'">✕
                        </label>
                    </div>
                    @endforeach
                    {{-- New upload previews inserted by JS --}}
                    <div class="wz-dmg-new-previews" style="display:contents"></div>
                </div>
                @else
                <div class="wz-dmg-photos" style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;align-items:flex-start">
                    <div class="wz-dmg-new-previews" style="display:contents"></div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 8 — Stan techniczny                                  ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<style>
/* Status pill group — green / orange / red 3-state selector per item. */
.wz-tech-status{display:inline-flex;gap:6px;flex-wrap:wrap}
.wz-tech-status label{cursor:pointer;display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border:1.5px solid #e5e7eb;border-radius:8px;background:#fff;font-size:12.5px;font-weight:600;color:#6b7280;transition:all .15s;line-height:1.2}
.wz-tech-status input[type=radio]{display:none}
.wz-tech-status .dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.wz-tech-status .dot.ok{background:#16a34a}
.wz-tech-status .dot.attention{background:#f59e0b}
.wz-tech-status .dot.bad{background:#dc2626}
.wz-tech-status label:hover{border-color:#cbd5e1}
.wz-tech-status input[type=radio]:checked + .pill.ok       {background:#dcfce7;border-color:#16a34a;color:#15803d}
.wz-tech-status input[type=radio]:checked + .pill.attention{background:#fef3c7;border-color:#f59e0b;color:#b45309}
.wz-tech-status input[type=radio]:checked + .pill.bad      {background:#fee2e2;border-color:#dc2626;color:#b91c1c}
.wz-tech-row{padding:14px 16px;border:1px solid #eeeef0;border-radius:10px;background:#fff;margin-bottom:10px}
.wz-tech-row-head{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
.wz-tech-row-label{font-size:14px;font-weight:700;color:#1a1a1a}
.wz-tech-row input.wz-tech-note{margin-top:10px;width:100%;padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12.5px;color:#1a1a1a;background:#fff}
.wz-tech-row input.wz-tech-note:focus{outline:none;border-color:#0066ff;box-shadow:0 0 0 3px rgba(0,102,255,.1)}
</style>
<div class="wz-step" data-step="8">
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge"><i data-lucide="wrench" style="width:16px;height:16px"></i></div>
            <div><div class="wz-section-title">Stan techniczny</div><div class="wz-section-subtitle">Wybierz status każdego z elementów. Opcjonalna notatka pojawi się przy żółtym lub czerwonym statusie.</div></div>
        </div>
        @php
            // Pre-resolve every row through the same helper the public view uses
            // so legacy free-text saves (e.g. "Sprawny") preload as the correct status.
            $resolved = [];
            foreach ($techCategories as $tcKey => $tcLabel) {
                $resolved[$tcKey] = \App\Helpers\CarLabels::techStatus($technicalConditions[$tcKey] ?? null);
            }
        @endphp
        @foreach($techCategories as $key => $label)
        @php $row = $resolved[$key]; @endphp
        <div class="wz-tech-row">
            <div class="wz-tech-row-head">
                <span class="wz-tech-row-label">{{ $label }}</span>
                <span class="wz-tech-status">
                    <label>
                        <input type="radio" name="technical_conditions[{{ $key }}][status]" value="ok" {{ $row['status'] === 'ok' ? 'checked' : '' }}>
                        <span class="pill ok"><span class="dot ok"></span>Bez zarzutu</span>
                    </label>
                    <label>
                        <input type="radio" name="technical_conditions[{{ $key }}][status]" value="attention" {{ $row['status'] === 'attention' ? 'checked' : '' }}>
                        <span class="pill attention"><span class="dot attention"></span>Wymaga uwagi</span>
                    </label>
                    <label>
                        <input type="radio" name="technical_conditions[{{ $key }}][status]" value="bad" {{ $row['status'] === 'bad' ? 'checked' : '' }}>
                        <span class="pill bad"><span class="dot bad"></span>Nieprawidłowość</span>
                    </label>
                </span>
            </div>
            <input type="text" class="wz-tech-note" name="technical_conditions[{{ $key }}][note]" value="{{ $row['note'] ?? '' }}" maxlength="500" placeholder="Opcjonalna notatka (np. lekki stuk z lewej strony)">
        </div>
        @endforeach
    </div>
</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 9 — Pomiary lakieru                                  ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="9">
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge"><i data-lucide="paintbrush" style="width:16px;height:16px"></i></div>
            <div><div class="wz-section-title">Pomiary grubości lakieru</div><div class="wz-section-subtitle">Wartości w µm — norma fabryczna: 90–150 µm</div></div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:16px;font-size:11px;font-weight:600">
            <span style="display:flex;align-items:center;gap:6px"><span style="width:12px;height:12px;border-radius:3px;background:#10b981"></span> Fabryczny (90–150 µm)</span>
            <span style="display:flex;align-items:center;gap:6px"><span style="width:12px;height:12px;border-radius:3px;background:#f59e0b"></span> Lakierowany (150–300 µm)</span>
            <span style="display:flex;align-items:center;gap:6px"><span style="width:12px;height:12px;border-radius:3px;background:#ef4444"></span> Naprawa (>300 µm)</span>
        </div>
        @php
        $paintPanels = ['Maska','Błotnik przedni lewy','Błotnik przedni prawy','Drzwi przednie lewe','Drzwi przednie prawe','Drzwi tylne lewe','Drzwi tylne prawe','Klapa bagażnika','Dach','Próg lewy','Próg prawy','Zderzak przedni','Zderzak tylny'];
        @endphp
        <div class="wz-grid-3">
            @foreach($paintPanels as $pi => $panel)
            @php
                $pmRaw = $paintMeasurements[$pi] ?? null;
                $pmVal = $pmValue($pmRaw);                  // safe for nested array OR plain scalar
                $pmAreaVal = $pmArea($pmRaw, $panel);      // prefer stored area label, fall back to panel name
            @endphp
            <div class="wz-field" style="margin-bottom:10px">
                <label style="display:flex;align-items:center;gap:6px">
                    <span style="width:8px;height:8px;border-radius:50%;background:{{ is_numeric($pmVal) ? ((int)$pmVal <= 150 ? '#10b981' : ((int)$pmVal <= 300 ? '#f59e0b' : '#ef4444')) : '#d1d5db' }}"></span>
                    {{ $panel }}
                </label>
                <input type="hidden" name="paint_measurements[{{ $pi }}][area]" value="{{ $pmAreaVal }}">
                <input type="number" name="paint_measurements[{{ $pi }}][value]" value="{{ $pmVal }}" min="0" max="2000" step="1" placeholder="µm" oninput="wzPaintColor(this)">
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 10 — Opony i bieżnik                                 ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="10">
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge"><i data-lucide="circle-dot" style="width:16px;height:16px"></i></div>
            <div><div class="wz-section-title">Opony i bieżnik</div><div class="wz-section-subtitle">Dodaj zestawy opon z informacją o stanie</div></div>
        </div>
        <div id="wzTireSetsList">
            @forelse($existingTireSets as $ti => $ts)
            <div class="repeater-item" data-idx="{{ $ti }}" style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:14px;margin-bottom:12px">
                <div class="wz-grid-3" style="margin-bottom:10px">
                    <div class="wz-field"><label>Numer zestawu</label><input type="number" name="tire_sets[{{ $ti }}][set_number]" value="{{ $ts->set_number ?? $ti+1 }}" min="1"></div>
                    <div class="wz-field"><label>Typ opon</label><input type="text" name="tire_sets[{{ $ti }}][tire_type]" value="{{ $ts->tire_type }}" placeholder="Letnie / Zimowe / Całoroczne"></div>
                    <div class="wz-field"><label>Felgi</label><input type="text" name="tire_sets[{{ $ti }}][rim]" value="{{ $ts->rim }}" placeholder="Aluminiowe 18''"></div>
                </div>
                <div class="wz-grid-2" style="margin-bottom:10px">
                    <div class="wz-field"><label>Uwagi</label><input type="text" name="tire_sets[{{ $ti }}][notes]" value="{{ $ts->notes }}"></div>
                    <div style="display:flex;align-items:center;gap:10px;padding-top:20px">
                        <label class="wz-inline-label"><input type="hidden" name="tire_sets[{{ $ti }}][is_mounted]" value="0"><input type="checkbox" name="tire_sets[{{ $ti }}][is_mounted]" value="1" {{ $ts->is_mounted?'checked':'' }}> Obecnie zamontowane</label>
                    </div>
                </div>
                <div style="font-size:12px;font-weight:700;color:var(--text-2);margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px">Bieżnik poszczególnych kół</div>
                <div class="wz-grid-4">
                    @foreach(['front_left'=>'Przód L','front_right'=>'Przód P','rear_left'=>'Tył L','rear_right'=>'Tył P'] as $pos => $posLabel)
                    @php $tire = $ts->tires->firstWhere('position',$pos); @endphp
                    <div class="wz-field">
                        <label>{{ $posLabel }}</label>
                        <input type="hidden" name="tire_sets[{{ $ti }}][tires][{{ $pos }}][position]" value="{{ $pos }}">
                        <input type="text" name="tire_sets[{{ $ti }}][tires][{{ $pos }}][tread_depth]" value="{{ $tire?->tread_depth }}" placeholder="mm" style="margin-bottom:6px">
                        <input type="text" name="tire_sets[{{ $ti }}][tires][{{ $pos }}][condition]" value="{{ is_array($tire?->condition) ? implode(', ', $tire->condition) : $tire?->condition }}" placeholder="Stan">
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="repeater-item" data-idx="0" style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:14px;margin-bottom:12px">
                <div class="wz-grid-3" style="margin-bottom:10px">
                    <div class="wz-field"><label>Numer zestawu</label><input type="number" name="tire_sets[0][set_number]" value="1" min="1"></div>
                    <div class="wz-field"><label>Typ opon</label><input type="text" name="tire_sets[0][tire_type]" placeholder="Letnie / Zimowe / Całoroczne"></div>
                    <div class="wz-field"><label>Felgi</label><input type="text" name="tire_sets[0][rim]" placeholder="Aluminiowe 18''"></div>
                </div>
                <div class="wz-grid-2" style="margin-bottom:10px">
                    <div class="wz-field"><label>Uwagi</label><input type="text" name="tire_sets[0][notes]"></div>
                    <div style="display:flex;align-items:center;gap:10px;padding-top:20px">
                        <label class="wz-inline-label"><input type="hidden" name="tire_sets[0][is_mounted]" value="0"><input type="checkbox" name="tire_sets[0][is_mounted]" value="1" checked> Obecnie zamontowane</label>
                    </div>
                </div>
                <div style="font-size:12px;font-weight:700;color:var(--text-2);margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px">Bieżnik poszczególnych kół</div>
                <div class="wz-grid-4">
                    @foreach(['front_left'=>'Przód L','front_right'=>'Przód P','rear_left'=>'Tył L','rear_right'=>'Tył P'] as $pos => $posLabel)
                    <div class="wz-field">
                        <label>{{ $posLabel }}</label>
                        <input type="hidden" name="tire_sets[0][tires][{{ $pos }}][position]" value="{{ $pos }}">
                        <input type="text" name="tire_sets[0][tires][{{ $pos }}][tread_depth]" placeholder="mm" style="margin-bottom:6px">
                        <input type="text" name="tire_sets[0][tires][{{ $pos }}][condition]" placeholder="Stan">
                    </div>
                    @endforeach
                </div>
            </div>
            @endforelse
        </div>
        <button type="button" class="btn btn-outline" onclick="wzAddTireSet()" style="margin-top:8px"><i data-lucide="plus" style="width:14px;height:14px"></i> Dodaj zestaw opon</button>
    </div>
</div>

{{-- ╔══════════════════════════════════════════════════════════════╗
     ║  STEP 11 — Podgląd i publikacja (SEO)                      ║
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div class="wz-step" data-step="11">
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge"><i data-lucide="search" style="width:16px;height:16px"></i></div>
            <div><div class="wz-section-title">SEO i optymalizacja</div><div class="wz-section-subtitle">Dostosuj jak ogłoszenie wyświetla się w Google</div></div>
        </div>

        {{-- Focus keyword --}}
        <div class="wz-field" style="margin-bottom:18px">
            <label>Focus keyword</label>
            <input type="text" name="focus_keyword" id="wzFocusKw" value="{{ old('focus_keyword',$car?->focus_keyword) }}" placeholder="np. Audi A4 TFSI Quattro" oninput="wzSeoAnalyze()">
        </div>

        {{-- Meta title --}}
        <div class="wz-field" style="margin-bottom:18px">
            <label>Meta title</label>
            <input type="text" name="meta_title" id="wzMetaTitle" value="{{ old('meta_title',$car?->meta_title) }}" maxlength="70" placeholder="Tytuł strony w wynikach Google (max 70 znaków)" oninput="wzSeoAnalyze()">
            <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
                <div style="flex:1;height:4px;background:#e5e7eb;border-radius:2px;overflow:hidden"><div id="wzMetaTitleBar" style="height:100%;width:0%;border-radius:2px;transition:all .3s"></div></div>
                <span id="wzMetaTitleCount" style="font-size:11px;font-weight:600;color:var(--text-3);white-space:nowrap">0 / 70 znaków</span>
            </div>
        </div>

        {{-- Meta description --}}
        <div class="wz-field" style="margin-bottom:18px">
            <label>Meta description</label>
            <textarea name="meta_description" id="wzMetaDesc" maxlength="160" rows="3" style="min-height:60px" placeholder="Opis strony w wynikach Google (max 160 znaków)" oninput="wzSeoAnalyze()">{{ old('meta_description',$car?->meta_description) }}</textarea>
            <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
                <div style="flex:1;height:4px;background:#e5e7eb;border-radius:2px;overflow:hidden"><div id="wzMetaDescBar" style="height:100%;width:0%;border-radius:2px;transition:all .3s"></div></div>
                <span id="wzMetaDescCount" style="font-size:11px;font-weight:600;color:var(--text-3);white-space:nowrap">0 / 160 znaków</span>
            </div>
        </div>

        {{-- Google SERP Preview --}}
        <div style="margin-bottom:20px">
            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-3);margin-bottom:8px;display:block">Podgląd w Google</label>
            <div style="background:#fff;border:1px solid var(--border-l);border-radius:12px;padding:16px 20px;font-family:Arial,sans-serif">
                <div id="wzSerpTitle" style="font-size:18px;color:#1a0dab;font-weight:400;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ old('meta_title',$car?->meta_title) ?: 'Tytuł strony' }}</div>
                <div id="wzSerpUrl" style="font-size:13px;color:#006621;margin:3px 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">certicars.pl/samochody/{{ $car?->slug ?? 'url-slug' }}</div>
                <div id="wzSerpDesc" style="font-size:13px;color:#545454;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ old('meta_description',$car?->meta_description) ?: 'Opis strony w wynikach wyszukiwania...' }}</div>
            </div>
        </div>

        {{-- SEO Analysis (Yoast-style) --}}
        <div style="margin-bottom:18px">
            <label style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--text-3);margin-bottom:10px;display:flex;align-items:center;gap:6px">
                <span id="wzSeoScoreDot" style="width:10px;height:10px;border-radius:50%;background:#e5e7eb"></span>
                Analiza SEO
            </label>
            <div id="wzSeoChecks" style="display:flex;flex-direction:column;gap:6px">
                {{-- JS fills this --}}
            </div>
        </div>

        {{-- URL + noindex --}}
        <div class="wz-grid-2">
            <div class="wz-field">
                <label>URL (slug)</label>
                <input type="text" value="{{ $car?->slug ?? '— generowany automatycznie —' }}" disabled readonly>
            </div>
            <div style="display:flex;align-items:center;gap:10px;padding-top:20px">
                <label class="wz-inline-label">
                    <input type="hidden" name="noindex" value="0">
                    <input type="checkbox" name="noindex" value="1" {{ old('noindex',$car?->noindex)?'checked':'' }}>
                    Noindex — ukryj w Google
                </label>
            </div>
        </div>
    </div>
    <div class="wz-section">
        <div class="wz-section-header">
            <div class="wz-section-badge" style="background:#10b981"><i data-lucide="rocket" style="width:16px;height:16px"></i></div>
            <div><div class="wz-section-title">Publikacja</div><div class="wz-section-subtitle">Sprawdź dane i opublikuj ogłoszenie</div></div>
        </div>
        <div style="padding:18px 20px;background:var(--bg);border-radius:10px;border:1px solid var(--border-l);text-align:center">
            <i data-lucide="check-circle-2" style="width:40px;height:40px;color:#10b981;margin-bottom:10px"></i>
            <div style="font-size:15px;font-weight:700;margin-bottom:4px">Gotowe do publikacji</div>
            <div style="font-size:13px;color:var(--text-3);margin-bottom:16px">Sprawdź podgląd po prawej stronie i kliknij "Zapisz" lub zmień status na "Aktywne" w panelu obok.</div>
            <button type="submit" class="btn btn-blue" style="padding:13px 28px"><i data-lucide="send" style="width:15px;height:15px"></i> Opublikuj ogłoszenie</button>
        </div>
    </div>
</div>

</div>{{-- /wz-layout --}}



{{-- ============================================================
     JAVASCRIPT
     ============================================================ --}}
@push('scripts')
<script>
(function(){
    'use strict';

    // ===================================================================
    //  STEP NAVIGATION — bridges with layout wizard.blade.php
    // ===================================================================
    let currentStep = 1;
    const totalSteps = 11;

    function showStep(n) {
        n = Math.max(1, Math.min(totalSteps, +n));
        document.querySelectorAll('.wz-step[data-step]').forEach(s => {
            s.classList.toggle('active', +s.dataset.step === n);
        });
        document.querySelectorAll('.wz-completion-item').forEach(ci => {
            ci.classList.toggle('active', +ci.dataset.gotoStep === n);
        });
        currentStep = n;
        document.getElementById('wizCenter')?.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Listen to layout's step-changed event
    window.addEventListener('wizard:step-changed', e => {
        showStep(e.detail.step);
    });

    // Expose for direct calls
    window.wzGoToStep = function(n) {
        if (typeof goToStep === 'function') goToStep(n); // layout's goToStep
        showStep(n);
    };

    // Sidebar completion items click — call layout's goToStep
    document.querySelectorAll('.wz-completion-item[data-goto-step]').forEach(ci => {
        ci.addEventListener('click', () => {
            const n = +ci.dataset.gotoStep;
            if (typeof goToStep === 'function') goToStep(n);
            else showStep(n);
        });
    });


    // Step nav button clicks (from step-nav bar in parent page)
    document.addEventListener('click', e => {
        const navBtn = e.target.closest('[data-wz-nav]');
        if (navBtn) goToStep(navBtn.dataset.wzNav);
    });


    // ===================================================================
    //  TOGGLE SWITCHES
    // ===================================================================
    document.querySelectorAll('[data-toggle]').forEach(toggle => {
        const cb = toggle.querySelector('input[type="checkbox"]');
        if (!cb) return;
        toggle.addEventListener('click', e => {
            // Don't toggle when clicking the select inside GetHelp
            if (e.target.tagName === 'SELECT' || e.target.closest('select')) return;
            cb.checked = !cb.checked;
            toggle.classList.toggle('active', cb.checked);
            updatePreview();
        });
    });


    // ===================================================================
    //  LIVE PREVIEW UPDATES
    // ===================================================================
    const $ = s => document.querySelector(s);
    const $v = s => { const el = $(s); return el ? el.value.trim() : ''; };

    function getBrandName() {
        const sel = $('#wzBrandSelect');
        if (!sel || !sel.value) return '';
        return sel.options[sel.selectedIndex]?.textContent?.trim() || '';
    }

    function formatNumber(n) {
        if (!n) return '';
        return (+n).toLocaleString('pl-PL');
    }

    function updatePreview() {
        const brand = getBrandName();
        const model = $v('[name="model"]');
        const power = $v('[name="power_hp"]');
        const fuel  = $v('[name="fuel_type"]');
        const trans = $v('[name="transmission"]');
        const reg   = $v('[name="first_registration"]');
        const km    = $v('[name="mileage"]');
        const price = $v('[name="price"]');
        const curr  = $v('[name="currency"]') || 'PLN';
        const engineVersion    = $v('[name="engine_version"]');
        const equipmentVersion = $v('[name="equipment_version"]');

        // Auto title: Marka + Model + Silnik (engine_version) + Wersja wyposażenia
        let parts = [brand, model, engineVersion, equipmentVersion];
        const title = parts.filter(Boolean).join(' ');
        const titleEl = $('#wzAutoTitle');
        const prevTitleEl = $('#wzPreviewTitle');
        if (titleEl) titleEl.textContent = title || 'Tytuł wygeneruje się automatycznie';
        if (prevTitleEl) prevTitleEl.textContent = title || 'Tytuł ogłoszenia';

        // Specs
        const setSpec = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.querySelector('span').textContent = val || '—';
        };
        setSpec('wzPrevEngine', power ? power + ' KM' : '');
        setSpec('wzPrevFuel', fuel);
        setSpec('wzPrevTransmission', trans);
        setSpec('wzPrevYear', reg);
        setSpec('wzPrevMileage', km ? formatNumber(km) + ' km' : '');

        // Price
        const priceValEl = $('#wzPrevPriceVal');
        const currEl = $('#wzPrevCurrency');
        if (priceValEl) priceValEl.textContent = price ? formatNumber(price) : '—';
        if (currEl) currEl.textContent = curr;

        // Badges
        const toggleBadge = (id, name) => {
            const el = document.getElementById(id);
            const cb = document.querySelector(`input[name="${name}"][type="checkbox"]`);
            if (el && cb) el.classList.toggle('visible', cb.checked);
        };
        toggleBadge('wzBadgeCerticheck', 'has_certicheck');
        toggleBadge('wzBadgeAvailable', 'available_now');
        toggleBadge('wzBadgeDelivery', 'home_delivery');
        toggleBadge('wzBadgeGethelp', 'has_gethelp');

        // Step completion dots
        checkStepCompletion();
    }

    function checkStepCompletion() {
        const checks = {
            1: ['brand_id', 'model'],
            2: [], // images — check count
            3: ['previous_owners'],
        };
        for (let step = 1; step <= totalSteps; step++) {
            const fields = checks[step] || [];
            let done = false;
            if (fields.length) {
                done = fields.every(n => {
                    const el = document.querySelector(`[name="${n}"]`);
                    return el && el.value && el.value.trim() !== '';
                });
            }
            const ci = document.querySelector(`.wz-completion-item[data-goto-step="${step}"]`);
            if (ci) ci.classList.toggle('done', done);
        }
    }

    // Listen to all inputs for live preview
    document.addEventListener('input', e => {
        if (e.target.closest('.wz-layout')) updatePreview();
    });
    document.addEventListener('change', e => {
        if (e.target.closest('.wz-layout')) updatePreview();
    });

    // Initial update
    setTimeout(updatePreview, 100);


    // ===================================================================
    //  INLINE BRAND CREATION
    // ===================================================================
    (function(){
        const toggle = document.getElementById('wzBrandAddToggle');
        const row    = document.getElementById('wzBrandAddRow');
        const input  = document.getElementById('wzBrandAddName');
        const submit = document.getElementById('wzBrandAddSubmit');
        const cancel = document.getElementById('wzBrandAddCancel');
        const errBox = document.getElementById('wzBrandAddError');
        const select = document.getElementById('wzBrandSelect');
        if (!toggle || !row || !select) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content
                  || document.querySelector('input[name="_token"]')?.value;

        function showError(msg){ errBox.textContent = msg; errBox.style.display = 'block'; }
        function clearError(){ errBox.textContent = ''; errBox.style.display = 'none'; }

        function open(){
            row.style.display = 'flex';
            input.value = '';
            clearError();
            setTimeout(() => input.focus(), 30);
        }
        function close(){
            row.style.display = 'none';
            clearError();
        }

        toggle.addEventListener('click', e => { e.preventDefault(); row.style.display === 'flex' ? close() : open(); });
        cancel.addEventListener('click', close);
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); submit.click(); }
            if (e.key === 'Escape') { e.preventDefault(); close(); }
        });

        submit.addEventListener('click', async () => {
            const name = (input.value || '').trim();
            if (!name) { showError('Podaj nazwę marki.'); input.focus(); return; }

            clearError();
            submit.disabled = true;
            const originalText = submit.innerHTML;
            submit.innerHTML = '...';

            try {
                const res = await fetch('{{ route("admin.brands.store") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ name }),
                });
                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    const msg = data?.errors?.name?.[0] || data?.message || 'Nie udało się dodać marki.';
                    showError(msg);
                    return;
                }

                const opt = document.createElement('option');
                opt.value = data.brand.id;
                opt.textContent = data.brand.name;
                opt.selected = true;
                const opts = Array.from(select.options).filter(o => o.value);
                let inserted = false;
                for (const o of opts) {
                    if (o.textContent.localeCompare(data.brand.name, 'pl', { sensitivity: 'base' }) > 0) {
                        select.insertBefore(opt, o);
                        inserted = true;
                        break;
                    }
                }
                if (!inserted) select.appendChild(opt);
                select.value = data.brand.id;
                close();
                updatePreview();
            } catch (e) {
                showError('Błąd sieci — spróbuj ponownie.');
            } finally {
                submit.disabled = false;
                submit.innerHTML = originalText;
                if (window.lucide) lucide.createIcons();
            }
        });
    })();

    // ===================================================================
    //  FILE DROP ZONES
    // ===================================================================
    document.querySelectorAll('.wz-file-drop').forEach(drop => {
        const input = drop.querySelector('input[type="file"]');
        if (!input) return;

        drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('over'); });
        drop.addEventListener('dragleave', () => drop.classList.remove('over'));
        drop.addEventListener('drop', e => {
            e.preventDefault();
            drop.classList.remove('over');
            input.files = e.dataTransfer.files;
            wzHandleFileDrop(drop, input);
        });
        input.addEventListener('change', () => wzHandleFileDrop(drop, input));
    });

    function wzHandleFileDrop(drop, input) {
        const titleEl = drop.querySelector('.drop-title');
        if (!input.files.length) {
            if (titleEl && drop.dataset.originalText) titleEl.textContent = drop.dataset.originalText;
            return;
        }
        if (titleEl && !drop.dataset.originalText) drop.dataset.originalText = titleEl.textContent;

        const uploadType = drop.dataset.uploadType;
        const carId = @json($car?->id ?? null);

        // AJAX upload for existing cars (gallery/damage)
        if (carId && (uploadType === 'gallery' || uploadType === 'damage')) {
            const files = Array.from(input.files).filter(f => f.type.startsWith('image/'));
            if (titleEl) titleEl.textContent = `Wgrywanie ${files.length} zdjęć...`;
            input.value = '';
            wzAjaxUploadFiles(files, uploadType, carId);
            return;
        }

        // Fallback: show local previews
        const n = input.files.length;
        if (titleEl) titleEl.textContent = `${n} plik(ów) gotowych do wgrania`;

        let pg = drop.parentElement?.querySelector('.wz-file-preview-grid');
        if (pg) pg.remove();
        pg = document.createElement('div');
        pg.className = 'wz-file-preview-grid';
        drop.parentElement?.insertBefore(pg, drop.nextSibling);

        Array.from(input.files).forEach(file => {
            if (!file.type.startsWith('image/')) return;
            const item = document.createElement('div');
            item.className = 'wz-fp-item';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = () => URL.revokeObjectURL(img.src);
            const name = document.createElement('div');
            name.className = 'fp-name';
            name.textContent = file.name;
            item.appendChild(img);
            item.appendChild(name);
            pg.appendChild(item);
        });
    }


    // ===================================================================
    //  AJAX IMAGE UPLOAD (sequential)
    // ===================================================================
    async function wzAjaxUploadFiles(files, type, carId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                       || document.querySelector('input[name="_token"]')?.value;
        const prefix = type === 'gallery' ? 'wzGallery' : 'wzDamage';
        const progressWrap = document.getElementById(prefix + 'UploadProgress');
        const progressBar  = document.getElementById(prefix + 'ProgressBar');
        const progressText = document.getElementById(prefix + 'ProgressText');
        const grid         = document.getElementById(prefix + 'UploadedGrid');
        const drop         = document.getElementById(prefix === 'wzGallery' ? 'wzGalleryDrop' : 'wzDamageDrop');
        const titleEl      = drop?.querySelector('.drop-title');

        if (progressWrap) progressWrap.style.display = '';
        let done = 0;
        const total = files.length;
        if (progressText) progressText.textContent = `0/${total}`;
        if (progressBar) progressBar.style.width = '0%';

        for (const file of files) {
            const fd = new FormData();
            fd.append('image', file);
            fd.append('type', type);
            fd.append('_token', csrfToken);

            try {
                const res = await fetch(`/admin/cars/${carId}/upload-image`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd,
                });
                const data = await res.json().catch(() => ({}));
                done++;
                if (progressBar) progressBar.style.width = Math.round((done / total) * 100) + '%';
                if (progressText) progressText.textContent = `${done}/${total}`;

                if (data.success && data.image) {
                    const tile = document.createElement('div');
                    tile.className = 'wz-fp-item';
                    tile.style.border = '2px solid #10b981';
                    tile.innerHTML = `<img src="${data.image.url}" alt="${data.image.alt || ''}" style="width:100%;aspect-ratio:4/3;object-fit:cover;display:block"><div class="fp-name" style="color:#10b981">✓ ${file.name}</div>`;
                    if (grid) grid.appendChild(tile);
                } else {
                    const tile = document.createElement('div');
                    tile.className = 'wz-fp-item';
                    tile.style.border = '2px solid #ef4444';
                    tile.innerHTML = `<div style="padding:12px;font-size:11px;color:#ef4444">✗ ${file.name}<br>${data.message || 'Błąd'}</div>`;
                    if (grid) grid.appendChild(tile);
                }
            } catch (err) {
                done++;
                if (progressBar) progressBar.style.width = Math.round((done / total) * 100) + '%';
                if (progressText) progressText.textContent = `${done}/${total}`;
                const tile = document.createElement('div');
                tile.className = 'wz-fp-item';
                tile.style.border = '2px solid #ef4444';
                tile.innerHTML = `<div style="padding:12px;font-size:11px;color:#ef4444">✗ ${file.name}<br>Błąd sieci</div>`;
                if (grid) grid.appendChild(tile);
            }
        }

        if (titleEl) titleEl.textContent = `✓ Wgrano ${done}/${total} zdjęć`;
        if (progressBar) progressBar.style.background = '#10b981';
        if (window.toast) toast(`Wgrano ${done} zdjęć ${type === 'gallery' ? 'galerii' : 'uszkodzeń'}.`, 'success');
    }


    // ===================================================================
    //  IMAGE DELETE TOGGLE
    // ===================================================================
    window.wzToggleDelete = function(btn, id) {
        const tile = btn.closest('.wz-img-tile');
        const cb = tile.querySelector('input[type="checkbox"]');
        cb.checked = !cb.checked;
        tile.classList.toggle('to-delete', cb.checked);
    };


    // ===================================================================
    //  GALLERY DRAG-AND-DROP REORDER
    // ===================================================================
    (function(){
        const grid = document.getElementById('wzGalleryGrid');
        if (!grid) return;

        let draggedEl = null;

        function syncOrder(){
            const items = grid.querySelectorAll('.sort-item');
            items.forEach(it => {
                const inp = it.querySelector('.sort-order-input');
                if (inp) inp.value = it.dataset.imgId;
            });
        }

        grid.querySelectorAll('.sort-item').forEach(item => {
            item.addEventListener('dragstart', e => {
                draggedEl = item;
                item.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', item.dataset.imgId);
            });
            item.addEventListener('dragend', () => {
                item.classList.remove('dragging');
                grid.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
                draggedEl = null;
                syncOrder();
            });
            item.addEventListener('dragover', e => {
                e.preventDefault();
                if (!draggedEl || draggedEl === item) return;
                item.classList.add('drag-over');
                e.dataTransfer.dropEffect = 'move';
            });
            item.addEventListener('dragleave', () => item.classList.remove('drag-over'));
            item.addEventListener('drop', e => {
                e.preventDefault();
                item.classList.remove('drag-over');
                if (!draggedEl || draggedEl === item) return;
                const rect = item.getBoundingClientRect();
                const after = (e.clientX - rect.left) > rect.width / 2;
                if (after) item.parentNode.insertBefore(draggedEl, item.nextSibling);
                else item.parentNode.insertBefore(draggedEl, item);
                syncOrder();
            });
        });
    })();


    // ===================================================================
    //  ENGINE VIDEO URL / FILE TOGGLE + PREVIEW
    // ===================================================================
    (function(){
        const vidUrl        = document.getElementById('wzEngineVideoUrl');
        const vidFile       = document.getElementById('wzEngineVideoFile');
        const vidUrlPreview = document.getElementById('wzVidUrlPreview');
        const vidLocalPrev  = document.getElementById('wzVideoLocalPreview');
        const vidTabUrl     = document.getElementById('wzVidTabUrl');
        const vidTabFile    = document.getElementById('wzVidTabFile');
        const vidUrlPanel   = document.getElementById('wzVidUrlPanel');
        const vidFilePanel  = document.getElementById('wzVidFilePanel');

        function setVidTab(tab) {
            const isUrl = tab === 'url';
            if (vidUrlPanel)  vidUrlPanel.style.display  = isUrl ? 'block' : 'none';
            if (vidFilePanel) vidFilePanel.style.display  = isUrl ? 'none' : 'block';
            if (vidTabUrl)  vidTabUrl.style.background  = isUrl ? '#fff' : 'transparent';
            if (vidTabFile) vidTabFile.style.background  = isUrl ? 'transparent' : '#fff';
        }
        vidTabUrl?.addEventListener('click', () => setVidTab('url'));
        vidTabFile?.addEventListener('click', () => setVidTab('file'));

        function previewVideoUrl(url) {
            if (!vidUrlPreview) return;
            if (!url) { vidUrlPreview.innerHTML = ''; return; }
            let yt = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/))([A-Za-z0-9_-]{11})/);
            let vim = url.match(/vimeo\.com\/(\d+)/);
            if (yt) {
                vidUrlPreview.innerHTML = `<div style="position:relative;padding-bottom:56.25%;border-radius:10px;overflow:hidden;background:#000"><iframe src="https://www.youtube.com/embed/${yt[1]}" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen></iframe></div>`;
            } else if (vim) {
                vidUrlPreview.innerHTML = `<div style="position:relative;padding-bottom:56.25%;border-radius:10px;overflow:hidden;background:#000"><iframe src="https://player.vimeo.com/video/${vim[1]}" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen></iframe></div>`;
            } else {
                vidUrlPreview.innerHTML = `<div style="background:#fafafb;border:1px solid var(--border-l);border-radius:8px;padding:10px;font-size:12px;color:var(--text-3)"><i data-lucide="info" style="width:13px;height:13px;vertical-align:-2px"></i> Link zapisany — podgląd dostępny tylko dla YouTube i Vimeo.</div>`;
                if (window.lucide) lucide.createIcons();
            }
        }
        vidUrl?.addEventListener('input', e => previewVideoUrl(e.target.value.trim()));
        if (vidUrl?.value) previewVideoUrl(vidUrl.value.trim());

        vidFile?.addEventListener('change', e => {
            if (!vidLocalPrev) return;
            const f = e.target.files?.[0];
            if (!f) { vidLocalPrev.innerHTML = ''; return; }
            const url = URL.createObjectURL(f);
            const sizeMB = (f.size / (1024 * 1024)).toFixed(1);
            vidLocalPrev.innerHTML = `<div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:10px"><div style="font-size:12px;color:var(--text-3);margin-bottom:6px">Podgląd: <strong style="color:var(--text)">${f.name}</strong> · ${sizeMB} MB</div><video src="${url}" controls preload="metadata" style="max-width:100%;max-height:320px;border-radius:8px;background:#000"></video></div>`;
        });

        @if($car?->engine_video_path && !$car?->engine_video_url)
        setVidTab('file');
        @endif
    })();


    // ===================================================================
    //  DIRTY CHECK + STICKY SAVE BAR
    // ===================================================================
    (function(){
        const form = document.getElementById('wizardForm');
        const bar  = document.getElementById('stickySave');
        if (!form || !bar) return;

        let dirty = false;
        const check = () => {
            if (!dirty) {
                dirty = true;
                bar.classList.add('show');
                window.addEventListener('beforeunload', beforeUnload);
            }
        };
        const beforeUnload = e => { e.preventDefault(); e.returnValue = ''; };
        form.addEventListener('input', check);
        form.addEventListener('change', check);
        form.addEventListener('submit', () => { dirty = false; window.removeEventListener('beforeunload', beforeUnload); });

        // P1 GUARD: engine video size — must match Laravel validation max:102400 KB (=100 MB).
        // Without this, PHP upload_max_filesize used to silently drop oversized
        // uploads → empty form / no car / no error. Now PHP allows up to 120MB
        // (so Laravel can return a clear Polish message) and this client-side
        // check blocks the submit before payload even leaves the browser.
        form.addEventListener('submit', function(e) {
            const ENGINE_VIDEO_MAX_BYTES = 100 * 1024 * 1024;
            const vid = form.querySelector('input[type="file"][name="engine_video_file"]');
            const f = vid && vid.files && vid.files[0];
            if (f && f.size > ENGINE_VIDEO_MAX_BYTES) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const mb = Math.round(f.size / 1024 / 1024);
                alert('Nagranie pracy silnika jest za duże (' + mb + ' MB). Maksymalny rozmiar to 100 MB.\n\nSkompresuj wideo lub wyślij krótszy fragment. Pozostałe dane formularza nie zostały utracone — pozostaną na tej stronie.');
                // Re-enable Save buttons (wizSafeSubmit disabled them on first click)
                if (typeof wizResetSubmitState === 'function') wizResetSubmitState();
                return false;
            }
        }, true);

        // Guard: warn if total file payload would exceed server limit (250 MB soft cap)
        form.addEventListener('submit', function(e) {
            const MAX_BYTES = 250 * 1024 * 1024;
            let total = 0;
            form.querySelectorAll('input[type="file"]').forEach(input => {
                Array.from(input.files || []).forEach(f => { total += f.size; });
            });
            if (total > MAX_BYTES) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const mb = Math.round(total / 1024 / 1024);
                alert('Całkowity rozmiar wybranych plików (' + mb + ' MB) przekracza limit 250 MB.\n\nPodziel zdjęcia na mniejsze partie — zapisz auto z pierwszą częścią zdjęć, a pozostałe dodaj po zapisaniu (każde zdjęcie wgra się osobno bez limitu).');
                if (typeof wizResetSubmitState === 'function') wizResetSubmitState();
            }
        }, true);
    })();

    // ===================================================================
    //  WIZARD — Interactive car diagram + damage system (simplified)
    // ===================================================================
    (function(){
        const WZ_VIEW_LABELS = {top:'Góra',front:'Przód',rear:'Tył',left:'Lewy bok',right:'Prawy bok'};
        const TYPE_COLORS = {damage:'#f59e0b', accident:'#dc2626', repaired:'#9ca3af'};
        const TYPE_ICONS = {
            damage: '<svg width="12" height="12" viewBox="0 0 24 24" fill="#000" stroke="none"><path d="M12 2L2 22h20L12 2zM12 8v6M12 17h.01"/></svg>',
            accident: '<svg width="12" height="12" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M12 2L2 22h20L12 2z"/></svg>',
            repaired: '<svg width="12" height="12" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.77 3.77z"/></svg>'
        };
        const TYPE_ORDER = ['damage','accident','repaired'];
        const TYPE_MARKER_ICONS = {
            damage: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="none"><path d="M12 2L1 21h22L12 2z" fill="#000"/><path d="M12 9v5" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round"/><circle cx="12" cy="17" r="1.2" fill="#f59e0b"/></svg>',
            accident: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="none"><path d="M12 2L1 21h22L12 2z" fill="#fff"/><path d="M12 9v5" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round"/><circle cx="12" cy="17" r="1.2" fill="#dc2626"/></svg>',
            repaired: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.77 3.77z"/></svg>'
        };
        let wzCurrentView = 'top';
        let wzCurrentType = 'damage';

        // Type selector buttons
        document.querySelectorAll('.wz-dtype-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                wzCurrentType = btn.dataset.dtype;
                document.querySelectorAll('.wz-dtype-btn').forEach(b => {
                    const isActive = b.dataset.dtype === wzCurrentType;
                    b.classList.toggle('active', isActive);
                    const col = TYPE_COLORS[b.dataset.dtype];
                    b.style.border = isActive ? `2px solid ${col}` : '2px solid transparent';
                    b.style.background = isActive ? (b.dataset.dtype === 'damage' ? '#fffbeb' : b.dataset.dtype === 'accident' ? '#fef2f2' : '#f5f5f5') : '#fafafa';
                    b.style.color = isActive ? (b.dataset.dtype === 'damage' ? '#92400e' : b.dataset.dtype === 'accident' ? '#991b1b' : '#374151') : 'var(--text-3)';
                });
            });
        });

        // Cycle damage type on icon click
        window.wzCycleDamageType = function(btn) {
            const item = btn.closest('.wz-damage-item');
            const input = item.querySelector('.wz-dtype-input');
            const cur = input.value || 'damage';
            const next = TYPE_ORDER[(TYPE_ORDER.indexOf(cur) + 1) % TYPE_ORDER.length];
            input.value = next;
            item.dataset.dtype = next;
            btn.style.background = TYPE_COLORS[next];
            btn.innerHTML = TYPE_ICONS[next];
            wzRenderMarkers();
        };

        function wzBuildDamageHTML(idx, posX, posY, posView) {
            posView = posView || wzCurrentView;
            const dtype = wzCurrentType;
            const color = TYPE_COLORS[dtype];
            return `<div class="wz-damage-item" data-idx="${idx}" data-view="${posView}" data-dtype="${dtype}" style="padding:10px 14px;margin-bottom:6px;background:#fafafb;border:1px solid var(--border-l);border-radius:10px">
                <input type="hidden" name="damages[${idx}][type]" value="${dtype}" class="wz-dtype-input">
                <input type="hidden" name="damages[${idx}][position_x]" value="${posX??50}" class="wz-pos-x">
                <input type="hidden" name="damages[${idx}][position_y]" value="${posY??50}" class="wz-pos-y">
                <input type="hidden" name="damages[${idx}][position_view]" value="${posView}" class="wz-pos-view">
                <div style="display:flex;align-items:center;gap:10px">
                    <button type="button" class="wz-dtype-icon" onclick="wzCycleDamageType(this)" title="Kliknij aby zmienić typ" style="width:28px;height:28px;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:${color}">${TYPE_ICONS[dtype]}</button>
                    <input type="text" name="damages[${idx}][area]" placeholder="Podpis (np. Rysa na masce)" style="flex:1;font-weight:600;font-size:13.5px;border:none;background:transparent;outline:none;padding:0" autofocus>
                    <label class="wz-dmg-photo-btn" title="Dodaj zdjęcia" style="width:30px;height:30px;border-radius:8px;border:1px dashed var(--border);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:.15s;color:var(--text-3)" onmouseenter="this.style.borderColor='var(--blue)';this.style.color='var(--blue)'" onmouseleave="this.style.borderColor='var(--border)';this.style.color='var(--text-3)'">
                        <i data-lucide="camera" style="width:14px;height:14px"></i>
                        <input type="file" name="damages[${idx}][images][]" accept="image/*" multiple style="display:none" onchange="wzPreviewDmgImages(this)">
                    </label>
                    <button type="button" onclick="wzRemoveDamage(this)" style="background:none;border:none;color:#b91c1c;cursor:pointer;padding:4px;flex-shrink:0;opacity:.5;transition:.15s" onmouseenter="this.style.opacity=1" onmouseleave="this.style.opacity=.5"><i data-lucide="x" style="width:15px;height:15px"></i></button>
                </div>
                <div class="wz-dmg-photos" style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;align-items:flex-start">
                    <div class="wz-dmg-new-previews" style="display:contents"></div>
                </div>
            </div>`;
        }

        function wzAddDamageItem(posX, posY, posView) {
            const list = document.getElementById('wzDamageList');
            const idx = list.querySelectorAll('.wz-damage-item').length;
            list.insertAdjacentHTML('beforeend', wzBuildDamageHTML(idx, posX, posY, posView));
            if (window.lucide) lucide.createIcons();
            wzRenderMarkers();
            const el = list.querySelector(`.wz-damage-item[data-idx="${idx}"]`);
            el?.scrollIntoView({behavior:'smooth', block:'center'});
            el?.querySelector('input[name$="[area]"]')?.focus();
        }

        window.wzAddDamageManual = function() {
            wzAddDamageItem(50, 50, wzCurrentView);
        };

        window.wzRemoveDamage = function(btn) {
            btn.closest('.wz-damage-item').remove();
            wzReindexDamages();
            wzRenderMarkers();
        };

        window.wzPreviewDmgImages = function(input) {
            const item = input.closest('.wz-damage-item');
            let container = item.querySelector('.wz-dmg-new-previews');
            if (!container) {
                let photosDiv = item.querySelector('.wz-dmg-photos');
                if (!photosDiv) {
                    photosDiv = document.createElement('div');
                    photosDiv.className = 'wz-dmg-photos';
                    photosDiv.style.cssText = 'margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;align-items:flex-start';
                    item.appendChild(photosDiv);
                }
                container = document.createElement('div');
                container.className = 'wz-dmg-new-previews';
                container.style.display = 'contents';
                photosDiv.appendChild(container);
            }
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const thumb = document.createElement('div');
                        thumb.style.cssText = 'position:relative';
                        thumb.innerHTML = `<img src="${e.target.result}" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:2px solid #10b981">`;
                        container.appendChild(thumb);
                    };
                    reader.readAsDataURL(file);
                });
                const label = input.closest('.wz-dmg-photo-btn');
                if (label) {
                    label.style.borderColor = '#10b981';
                    label.style.color = '#10b981';
                    label.style.borderStyle = 'solid';
                }
            }
        };

        window.wzGoToMarker = function(btn) {
            const card = btn.closest('.wz-damage-item');
            const view = card.dataset.view || card.querySelector('.wz-pos-view')?.value || 'top';
            wzSetView(view);
            document.getElementById('wzCarDiagram')?.scrollIntoView({behavior:'smooth', block:'center'});
        };

        function wzReindexDamages() {
            document.querySelectorAll('.wz-damage-item').forEach((el, idx) => {
                el.dataset.idx = idx;
                el.querySelectorAll('[name^="damages["]').forEach(inp => {
                    inp.name = inp.name.replace(/damages\[\d+\]/, 'damages[' + idx + ']');
                });
            });
        }

        function wzRenderMarkers() {
            const layer = document.getElementById('wzDiagramMarkers');
            if (!layer) return;
            layer.innerHTML = '';
            document.querySelectorAll('.wz-damage-item').forEach((el, idx) => {
                const view = el.dataset.view || el.querySelector('.wz-pos-view')?.value || 'top';
                if (view !== wzCurrentView) return;
                const x = parseFloat(el.querySelector('.wz-pos-x')?.value);
                const y = parseFloat(el.querySelector('.wz-pos-y')?.value);
                if (isNaN(x) || isNaN(y)) return;
                const dtype = el.dataset.dtype || el.querySelector('.wz-dtype-input')?.value || 'damage';
                const color = TYPE_COLORS[dtype] || '#f59e0b';
                const glowColor = dtype === 'accident' ? 'rgba(220,38,38,.25)' : dtype === 'repaired' ? 'rgba(156,163,175,.25)' : 'rgba(245,158,11,.25)';
                const markerIcon = TYPE_MARKER_ICONS[dtype] || TYPE_MARKER_ICONS.damage;
                const m = document.createElement('div');
                m.className = 'wz-diagram-marker';
                m.dataset.idx = idx;
                m.style.cssText = `position:absolute;left:${x}%;top:${y}%;transform:translate(-50%,-50%);width:38px;height:38px;border-radius:50%;background:${glowColor};display:flex;align-items:center;justify-content:center;cursor:grab;pointer-events:auto;z-index:5;transition:transform .12s`;
                const inner = document.createElement('div');
                inner.style.cssText = `width:28px;height:28px;border-radius:50%;background:${color};display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.25)`;
                inner.innerHTML = markerIcon;
                m.appendChild(inner);
                m.title = el.querySelector('input[name$="[area]"]')?.value || '#' + (idx + 1);
                m.addEventListener('mouseenter', () => m.style.transform = 'translate(-50%,-50%) scale(1.18)');
                m.addEventListener('mouseleave', () => m.style.transform = 'translate(-50%,-50%) scale(1)');
                m.addEventListener('click', e => {
                    e.stopPropagation();
                    const target = document.querySelector(`.wz-damage-item[data-idx="${idx}"]`);
                    target?.scrollIntoView({behavior:'smooth', block:'center'});
                    target?.style.setProperty('outline', '2px solid var(--blue)');
                    setTimeout(() => target?.style.removeProperty('outline'), 1200);
                });
                m.addEventListener('mousedown', wzStartDrag);
                layer.appendChild(m);
            });
        }

        function wzStartDrag(e) {
            e.preventDefault(); e.stopPropagation();
            const marker = e.currentTarget;
            const idx = +marker.dataset.idx;
            const wrap = document.getElementById('wzCarDiagram');
            const diag = wrap.getBoundingClientRect();
            const move = ev => {
                const px = ((ev.clientX - diag.left - 24) / (diag.width - 48)) * 100;
                const py = ((ev.clientY - diag.top - 24) / (diag.height - 48)) * 100;
                const x = Math.max(0, Math.min(100, px));
                const y = Math.max(0, Math.min(100, py));
                const el = document.querySelector(`.wz-damage-item[data-idx="${idx}"]`);
                if (el) {
                    el.querySelector('.wz-pos-x').value = x.toFixed(2);
                    el.querySelector('.wz-pos-y').value = y.toFixed(2);
                }
                marker.style.left = x + '%';
                marker.style.top = y + '%';
            };
            const up = () => { document.removeEventListener('mousemove', move); document.removeEventListener('mouseup', up); };
            document.addEventListener('mousemove', move);
            document.addEventListener('mouseup', up);
        }

        function wzSetView(view) {
            wzCurrentView = view;
            document.querySelectorAll('.wz-view-btn').forEach(b => b.classList.toggle('active', b.dataset.view === view));
            document.querySelectorAll('.wz-svg-view').forEach(v => {
                const isActive = v.dataset.view === view;
                v.classList.toggle('active', isActive);
                v.style.display = isActive ? '' : 'none';
            });
            const label = document.getElementById('wzViewLabel');
            if (label) label.textContent = 'WIDOK: ' + (WZ_VIEW_LABELS[view] || 'GÓRA').toUpperCase();
            wzRenderMarkers();
            wzApplyDamageFilter();
        }

        function wzApplyDamageFilter() {
            document.querySelectorAll('.wz-damage-item').forEach(el => {
                const view = el.dataset.view || el.querySelector('.wz-pos-view')?.value || 'top';
                el.style.opacity = view === wzCurrentView ? '1' : '0.4';
            });
        }

        // View switch buttons
        document.querySelectorAll('.wz-view-btn').forEach(b => b.addEventListener('click', () => wzSetView(b.dataset.view)));

        // Click on diagram to add damage
        const diagram = document.getElementById('wzCarDiagram');
        diagram?.addEventListener('click', e => {
            if (e.target.closest('.wz-diagram-marker')) return;
            const rect = diagram.getBoundingClientRect();
            const px = ((e.clientX - rect.left - 24) / (rect.width - 48)) * 100;
            const py = ((e.clientY - rect.top - 24) / (rect.height - 48)) * 100;
            const x = Math.max(0, Math.min(100, px)).toFixed(2);
            const y = Math.max(0, Math.min(100, py)).toFixed(2);
            wzAddDamageItem(x, y, wzCurrentView);
        });

        // Dynamic body-type image
        const btMap = {sedan:'sedan',suv:'suv','coupé':'coupe',coupe:'coupe',bus:'van',van:'van',kombi:'kombi',hatchback:'hatchback',kabriolet:'sedan',cabriolet:'sedan',pickup:'suv'};
        const btSelect = document.getElementById('bodyTypeSelect');
        const dtImg = document.getElementById('wzDamageTopImg');
        if (btSelect && dtImg) {
            btSelect.addEventListener('change', () => {
                const key = (btSelect.value || 'sedan').toLowerCase();
                const img = btMap[key] || 'sedan';
                dtImg.src = '/img/body-types-top/' + img + '.png';
            });
        }

        // Init
        wzRenderMarkers();
        wzApplyDamageFilter();

        // Active view button styling
        const style = document.createElement('style');
        style.textContent = `.wz-view-btn.active{background:var(--blue)!important;color:#fff!important;border-color:var(--blue)!important} .wz-view-btn:hover:not(.active){background:#f0f0f2!important} .wz-dtype-btn.active{font-weight:800!important}`;
        document.head.appendChild(style);
    })();

    // ===================================================================
    //  WIZARD — Add tire set helper
    // ===================================================================
    window.wzAddTireSet = function() {
        const list = document.getElementById('wzTireSetsList');
        const idx = list.querySelectorAll('.repeater-item').length;
        const positions = {front_left:'Przód L',front_right:'Przód P',rear_left:'Tył L',rear_right:'Tył P'};
        let tiresHtml = '';
        for (const [pos, label] of Object.entries(positions)) {
            tiresHtml += `<div class="wz-field"><label>${label}</label><input type="hidden" name="tire_sets[${idx}][tires][${pos}][position]" value="${pos}"><input type="text" name="tire_sets[${idx}][tires][${pos}][tread_depth]" placeholder="mm" style="margin-bottom:6px"><input type="text" name="tire_sets[${idx}][tires][${pos}][condition]" placeholder="Stan"></div>`;
        }
        const html = `<div class="repeater-item" data-idx="${idx}" style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:14px;margin-bottom:12px">
            <div class="wz-grid-3" style="margin-bottom:10px"><div class="wz-field"><label>Numer zestawu</label><input type="number" name="tire_sets[${idx}][set_number]" value="${idx+1}" min="1"></div><div class="wz-field"><label>Typ opon</label><input type="text" name="tire_sets[${idx}][tire_type]" placeholder="Letnie / Zimowe / Całoroczne"></div><div class="wz-field"><label>Felgi</label><input type="text" name="tire_sets[${idx}][rim]" placeholder="Aluminiowe 18''"></div></div>
            <div class="wz-grid-2" style="margin-bottom:10px"><div class="wz-field"><label>Uwagi</label><input type="text" name="tire_sets[${idx}][notes]"></div><div style="display:flex;align-items:center;gap:10px;padding-top:20px"><label class="wz-inline-label"><input type="hidden" name="tire_sets[${idx}][is_mounted]" value="0"><input type="checkbox" name="tire_sets[${idx}][is_mounted]" value="1"> Obecnie zamontowane</label></div></div>
            <div style="font-size:12px;font-weight:700;color:var(--text-2);margin-bottom:8px;text-transform:uppercase;letter-spacing:.3px">Bieżnik poszczególnych kół</div>
            <div class="wz-grid-4">${tiresHtml}</div>
        </div>`;
        list.insertAdjacentHTML('beforeend', html);
        if (window.lucide) lucide.createIcons();
    };

    // ===================================================================
    //  WIZARD — Paint measurement color indicator
    // ===================================================================
    window.wzPaintColor = function(input) {
        const val = parseFloat(input.value) || 0;
        const dot = input.closest('.wz-field')?.querySelector('label span');
        if (!dot) return;
        if (val === 0) dot.style.background = '#d1d5db';
        else if (val <= 150) dot.style.background = '#10b981';
        else if (val <= 300) dot.style.background = '#f59e0b';
        else dot.style.background = '#ef4444';
    };

    // ===================================================================
    //  WIZARD — SEO character counters
    // ===================================================================
    const metaTitleInput = document.querySelector('input[name="meta_title"]');
    const metaDescInput = document.querySelector('textarea[name="meta_description"]');
    if (metaTitleInput) {
        const counter = document.getElementById('wzMetaTitleCount');
        const update = () => { if(counter) counter.textContent = metaTitleInput.value.length; };
        metaTitleInput.addEventListener('input', update);
        update();
    }
    if (metaDescInput) {
        const counter = document.getElementById('wzMetaDescCount');
        const update = () => { if(counter) counter.textContent = metaDescInput.value.length; };
        metaDescInput.addEventListener('input', update);
        update();
    }

    // ===== OTOMOTO IMPORT =====
    window.wzImportOtomoto = async function() {
        const urlInput = document.getElementById('wzOtomotoUrl');
        const btn = document.getElementById('wzOtomotoBtn');
        if (!urlInput || !btn) return;

        const url = urlInput.value.trim();
        if (!url || !url.includes('otomoto.pl')) {
            wzShowImportStatus('Wklej prawidłowy link z Otomoto.pl', 'error');
            return;
        }

        btn.disabled = true;
        btn.style.opacity = '.6';
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Importuję...';
        wzShowImportStatus('Pobieram dane z Otomoto...', 'info');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch('{{ route("admin.otomoto.import") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ url }),
            });

            const result = await res.json();

            if (!result.success) {
                wzShowImportStatus(result.message || 'Nie udało się zaimportować danych.', 'error');
                return;
            }

            const d = result.data;
            let filled = 0;

            // Helper: fill text/number/textarea field
            function fillField(name, val) {
                if (val === undefined || val === null || val === '') return false;
                const el = document.querySelector(`[name="${name}"]`);
                if (!el) return false;
                el.value = val;
                el.dispatchEvent(new Event('input', {bubbles:true}));
                el.dispatchEvent(new Event('change', {bubbles:true}));
                return true;
            }

            // Helper: match select option by label or value
            function fillSelect(name, val) {
                if (!val) return false;
                const el = document.querySelector(`select[name="${name}"]`);
                if (!el) return false;
                const valLower = val.toString().toLowerCase();
                for (const opt of el.options) {
                    const optText = opt.textContent.toLowerCase().trim();
                    const optVal = opt.value.toLowerCase();
                    if (optText === valLower || optVal === valLower || optText.includes(valLower) || valLower.includes(optText)) {
                        el.value = opt.value;
                        el.dispatchEvent(new Event('change', {bubbles:true}));
                        return true;
                    }
                }
                return false;
            }

            // ═══════ STEP 1: Dane podstawowe ═══════
            // Brand
            let brandWarning = '';
            if (d.brand) {
                if (fillSelect('brand_id', d.brand)) {
                    filled++;
                } else {
                    brandWarning = `Marka "${d.brand}" nie istnieje w systemie — dodaj ją ręcznie lub wybierz z listy.`;
                }
            }

            // Text/number fields
            const step1Fields = {
                'model': d.model,
                'price': d.price,
                'currency': d.currency || 'PLN',
                'first_registration': d.first_registration,
                'mileage': d.mileage,
                'engine_capacity': d.engine_capacity,
                'power_hp': d.power_hp,
                'power_kw': d.power_kw,
                'color': d.color,
                'doors': d.doors,
                'seats': d.seats,
                'vin': d.vin,
                'co2_emission': d.co2_emission,
                'description': d.description,
            };
            for (const [name, val] of Object.entries(step1Fields)) {
                if (fillField(name, val)) filled++;
            }

            // Select fields
            if (fillSelect('fuel_type', d.fuel_type)) filled++;
            if (fillSelect('transmission', d.transmission)) filled++;
            if (fillSelect('body_type', d.body_type)) filled++;

            // ═══════ STEP 3: Historia pojazdu ═══════
            if (fillField('country_registration', d.country_registration)) filled++;
            if (d.imported !== undefined) {
                if (fillSelect('business_use', d.imported ? 'Tak' : 'Nie')) filled++;
            }
            if (fillSelect('previous_owners', d.previous_owners)) filled++;
            if (fillField('weight', d.weight)) filled++;
            if (fillField('upholstery', d.upholstery)) filled++;
            if (fillField('color_code', d.color_type)) filled++;

            // ═══════ STEP 4: Serwisowanie ═══════
            if (fillField('fuel_consumption', d.fuel_consumption)) filled++;
            if (fillField('emission_class', d.emission_class)) filled++;

            // ═══════ STEP 5: Dokumenty i sprzedawca ═══════
            // No direct mapping from Otomoto

            // ═══════ STEP 6: Wyposażenie ═══════
            // Equipment comes as flat array of labels from Otomoto
            // The wizard has textareas: equipment[safety], equipment[comfort], etc.
            if (d.equipment && Array.isArray(d.equipment) && d.equipment.length) {
                // First try checkbox-style equipment
                const checkboxes = document.querySelectorAll('input[name="equipment[]"]');
                if (checkboxes.length) {
                    let eqFilled = 0;
                    checkboxes.forEach(cb => {
                        const label = cb.parentElement?.textContent?.trim().toLowerCase();
                        if (d.equipment.some(e => e.toLowerCase() === label)) {
                            cb.checked = true;
                            eqFilled++;
                        }
                    });
                    if (eqFilled > 0) filled += eqFilled;
                }

                // Also fill textarea-style equipment (categories)
                const eqCategories = {
                    'safety': ['ABS','ESP','ASR','Poduszka','Kurtyny','ISOFIX','Kontrola trakcji','Wspomaganie hamowania','System akustyczny','Poduszki boczne'],
                    'comfort': ['Klimatyzacja','Kierownica','Tapicerka','Czujnik deszczu','Elektryczne szyby','Start-Stop','Wspomaganie kierownicy','Ekran dotykowy','Ogranicznik prędkości','Wielofunkcyjna'],
                    'exterior': ['Alufelgi','LED','Światła','Czujnik'],
                    'interior': ['Apple CarPlay','Android Auto','Bluetooth','Radio','USB','Zestaw głośnomówiący','Nawigacja'],
                    'extra': ['Kabel do ładowania']
                };
                for (const [cat, keywords] of Object.entries(eqCategories)) {
                    const textarea = document.querySelector(`textarea[name="equipment[${cat}]"]`);
                    if (!textarea) continue;
                    const matches = d.equipment.filter(eq =>
                        keywords.some(kw => eq.toLowerCase().includes(kw.toLowerCase()))
                    );
                    if (matches.length) {
                        textarea.value = matches.join('\n');
                        textarea.dispatchEvent(new Event('input', {bubbles:true}));
                        filled++;
                    }
                }
            }

            // ═══════ STEP 11: SEO ═══════
            if (d.meta_title) {
                const mtEl = document.getElementById('wzMetaTitle');
                if (mtEl) { mtEl.value = d.meta_title; mtEl.dispatchEvent(new Event('input')); filled++; }
            }
            if (d.meta_description) {
                const mdEl = document.getElementById('wzMetaDesc');
                if (mdEl) { mdEl.value = d.meta_description; mdEl.dispatchEvent(new Event('input')); filled++; }
            }
            if (d.focus_keyword) {
                const fkEl = document.getElementById('wzFocusKw');
                if (fkEl) { fkEl.value = d.focus_keyword; fkEl.dispatchEvent(new Event('input')); filled++; }
            }

            // Rebuild title & SEO
            if (typeof wzRebuildTitle === 'function') wzRebuildTitle();
            if (typeof wzSeoAnalyze === 'function') setTimeout(wzSeoAnalyze, 200);

            // Re-init icons
            if (window.lucide) lucide.createIcons();

            if (brandWarning) {
                wzShowImportStatus(`✓ Zaimportowano ${filled} pól z Otomoto. ⚠ ${brandWarning}`, 'error');
            } else {
                wzShowImportStatus(`✓ Zaimportowano ${filled} pól z Otomoto. Sprawdź dane we wszystkich krokach i uzupełnij brakujące.`, 'success');
            }

        } catch (err) {
            wzShowImportStatus('Błąd sieci — spróbuj ponownie. ' + err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.style.opacity = '';
            btn.innerHTML = '<i data-lucide="download" style="width:14px;height:14px"></i> Importuj';
            if (window.lucide) lucide.createIcons();
        }
    };

    function wzShowImportStatus(msg, type) {
        const el = document.getElementById('wzOtomotoStatus');
        if (!el) return;
        el.style.display = '';
        el.style.color = type === 'error' ? '#dc2626' : type === 'success' ? '#059669' : '#6366f1';
        el.textContent = msg;
    }

    // ===== SEO ANALYSIS (Yoast-style) =====
    window.wzSeoAnalyze = function() {
        const kw = (document.getElementById('wzFocusKw')?.value || '').trim().toLowerCase();
        const title = (document.getElementById('wzMetaTitle')?.value || '').trim();
        const desc = (document.getElementById('wzMetaDesc')?.value || '').trim();
        const slug = '{{ $car?->slug ?? '' }}';

        // --- Character bars ---
        const titleLen = title.length;
        const descLen = desc.length;

        // Title bar
        const titleBar = document.getElementById('wzMetaTitleBar');
        const titleCount = document.getElementById('wzMetaTitleCount');
        if (titleBar && titleCount) {
            const pct = Math.min(titleLen / 70 * 100, 100);
            const color = titleLen === 0 ? '#e5e7eb' : titleLen <= 30 ? '#ef4444' : titleLen <= 49 ? '#f59e0b' : titleLen <= 60 ? '#10b981' : titleLen <= 70 ? '#f59e0b' : '#ef4444';
            titleBar.style.width = pct + '%';
            titleBar.style.background = color;
            titleCount.textContent = titleLen + ' / 70 znaków';
            titleCount.style.color = color === '#10b981' ? '#10b981' : color === '#f59e0b' ? '#f59e0b' : titleLen === 0 ? 'var(--text-3)' : '#ef4444';
        }

        // Desc bar
        const descBar = document.getElementById('wzMetaDescBar');
        const descCount = document.getElementById('wzMetaDescCount');
        if (descBar && descCount) {
            const pct = Math.min(descLen / 160 * 100, 100);
            const color = descLen === 0 ? '#e5e7eb' : descLen <= 70 ? '#ef4444' : descLen <= 119 ? '#f59e0b' : descLen <= 155 ? '#10b981' : '#f59e0b';
            descBar.style.width = pct + '%';
            descBar.style.background = color;
            descCount.textContent = descLen + ' / 160 znaków';
            descCount.style.color = color === '#10b981' ? '#10b981' : color === '#f59e0b' ? '#f59e0b' : descLen === 0 ? 'var(--text-3)' : '#ef4444';
        }

        // --- SERP preview ---
        const serpTitle = document.getElementById('wzSerpTitle');
        const serpDesc = document.getElementById('wzSerpDesc');
        if (serpTitle) serpTitle.textContent = title || 'Tytuł strony';
        if (serpDesc) serpDesc.textContent = desc || 'Opis strony w wynikach wyszukiwania...';

        // --- SEO Checks ---
        const checks = [];
        const kwLower = kw;

        if (kw) {
            // Keyword in title
            const kwInTitle = title.toLowerCase().includes(kwLower);
            checks.push({ ok: kwInTitle ? 'green' : 'red', text: kwInTitle ? 'Focus keyword znajduje się w tytule' : 'Focus keyword nie występuje w tytule' });

            // Keyword in description
            const kwInDesc = desc.toLowerCase().includes(kwLower);
            checks.push({ ok: kwInDesc ? 'green' : 'red', text: kwInDesc ? 'Focus keyword znajduje się w opisie' : 'Focus keyword nie występuje w opisie' });

            // Keyword in URL
            if (slug) {
                const kwInSlug = slug.toLowerCase().includes(kwLower.replace(/\s+/g, '-'));
                checks.push({ ok: kwInSlug ? 'green' : 'orange', text: kwInSlug ? 'Focus keyword znajduje się w URL' : 'Focus keyword nie występuje w URL' });
            }
        } else {
            checks.push({ ok: 'red', text: 'Nie ustawiono focus keyword' });
        }

        // Title length
        if (titleLen === 0) {
            checks.push({ ok: 'red', text: 'Brak meta title' });
        } else if (titleLen < 30) {
            checks.push({ ok: 'red', text: 'Meta title jest za krótki (' + titleLen + ' znaków — zalecane 50-60)' });
        } else if (titleLen <= 49) {
            checks.push({ ok: 'orange', text: 'Meta title mógłby być dłuższy (' + titleLen + ' znaków — optymalnie 50-60)' });
        } else if (titleLen <= 60) {
            checks.push({ ok: 'green', text: 'Meta title ma optymalną długość (' + titleLen + ' znaków)' });
        } else {
            checks.push({ ok: 'orange', text: 'Meta title jest nieco za długi (' + titleLen + ' znaków — optymalnie 50-60)' });
        }

        // Desc length
        if (descLen === 0) {
            checks.push({ ok: 'red', text: 'Brak meta description' });
        } else if (descLen < 70) {
            checks.push({ ok: 'red', text: 'Meta description jest za krótki (' + descLen + ' znaków — zalecane 120-155)' });
        } else if (descLen <= 119) {
            checks.push({ ok: 'orange', text: 'Meta description mógłby być dłuższy (' + descLen + ' znaków — optymalnie 120-155)' });
        } else if (descLen <= 155) {
            checks.push({ ok: 'green', text: 'Meta description ma optymalną długość (' + descLen + ' znaków)' });
        } else {
            checks.push({ ok: 'orange', text: 'Meta description jest nieco za długi (' + descLen + ' znaków — optymalnie 120-155)' });
        }

        // Render checks
        const container = document.getElementById('wzSeoChecks');
        if (container) {
            const colors = { green: '#10b981', orange: '#f59e0b', red: '#ef4444' };
            container.innerHTML = checks.map(c =>
                `<div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:${c.ok==='green'?'#f0fdf4':c.ok==='orange'?'#fffbeb':'#fef2f2'};border-radius:8px;border:1px solid ${c.ok==='green'?'#bbf7d0':c.ok==='orange'?'#fde68a':'#fecaca'}">
                    <span style="width:8px;height:8px;border-radius:50%;background:${colors[c.ok]};flex-shrink:0"></span>
                    <span style="font-size:12.5px;font-weight:500;color:${c.ok==='green'?'#166534':c.ok==='orange'?'#92400e':'#991b1b'}">${c.text}</span>
                </div>`
            ).join('');
        }

        // Overall score dot
        const dot = document.getElementById('wzSeoScoreDot');
        if (dot) {
            const greens = checks.filter(c => c.ok === 'green').length;
            const reds = checks.filter(c => c.ok === 'red').length;
            const ratio = checks.length ? greens / checks.length : 0;
            dot.style.background = reds > 0 ? (ratio >= 0.5 ? '#f59e0b' : '#ef4444') : '#10b981';
        }
    };

    // Run on load
    setTimeout(wzSeoAnalyze, 200);

})();
</script>
@endpush
