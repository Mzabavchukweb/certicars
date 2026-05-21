@php
$eqCategories = [
    'safety'       => 'Bezpieczeństwo',
    'comfort'      => 'Komfort / multimedia',
    'exterior'     => 'Wyposażenie zewnętrzne',
    'interior'     => 'Wyposażenie wewnętrzne',
    'extra'        => 'Dodatkowe',
];
$techCategories = [
    'engine'       => 'Silnik',
    'transmission' => 'Skrzynia / napęd',
    'suspension'   => 'Zawieszenie / hamulce',
    'air_conditioning' => 'Klimatyzacja',
    'braking'      => 'Układ hamulcowy',
    'electronics'  => 'Elektronika',
    'body'         => 'Nadwozie / lakier',
];
$equipment          = old('equipment', $car?->equipment ?? []);
$technicalConditions = old('technical_conditions', $car?->technical_conditions ?? []);
$paintMeasurements  = old('paint_measurements', $car?->paint_measurements ?? []);
$existingDamages    = $car?->damages ?? collect();
$existingTireSets   = $car?->tireSets ?? collect();
$eqToText = fn($items) => is_array($items) ? implode("\n", $items) : ($items ?? '');
@endphp

<style>
.tabs{display:flex;gap:2px;border-bottom:1px solid var(--border-l);margin-bottom:20px;overflow-x:auto;scrollbar-width:none}
.tabs::-webkit-scrollbar{display:none}
.tabs button{background:none;border:none;padding:11px 16px;font-size:13px;font-weight:600;color:var(--text-3);border-bottom:2px solid transparent;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;transition:all .15s}
.tabs button:hover{color:var(--text-2)}
.tabs button.active{color:var(--blue);border-bottom-color:var(--blue)}
.tabs button i{width:14px;height:14px}
.tabs button .badge-count{background:var(--bg);color:var(--text-3);font-size:10.5px;font-weight:700;padding:1px 7px;border-radius:10px;margin-left:4px}
.tabs button.active .badge-count{background:var(--blue-bg);color:var(--blue)}
.tab-panel{display:none}
.tab-panel.active{display:block;animation:fade-in-p .15s}
@keyframes fade-in-p{from{opacity:0;transform:translateY(3px)}to{opacity:1;transform:translateY(0)}}
.repeater-item{background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:14px;margin-bottom:10px;position:relative}
.repeater-item .rmv{position:absolute;top:10px;right:10px;background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:6px;width:26px;height:26px;display:flex;align-items:center;justify-content:center;cursor:pointer}
.repeater-item .rmv i{width:13px;height:13px}
.repeater-add{width:100%;justify-content:center;padding:11px;background:#fff;border:1px dashed var(--border);color:var(--text-2);font-weight:600;border-radius:10px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-size:13px}
.repeater-add:hover{border-color:var(--blue);color:var(--blue);background:var(--blue-bg)}
.repeater-add i{width:14px;height:14px}
.img-tile{position:relative;border:1px solid var(--border-l);border-radius:10px;overflow:hidden;background:#fff}
.img-tile img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block}
.img-tile .primary-badge{position:absolute;top:6px;left:6px;background:rgba(255,255,255,.95);border-radius:6px;padding:4px 8px;font-size:10px;display:flex;align-items:center;gap:4px;cursor:pointer;font-weight:600}
.img-tile.to-delete{opacity:.4;outline:2px solid var(--red)}
.img-tile .del-toggle{position:absolute;top:6px;right:6px;background:rgba(239,68,68,.95);color:#fff;border-radius:6px;width:26px;height:26px;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none}
.img-tile .del-toggle i{width:13px;height:13px}
.file-drop{border:1.5px dashed var(--border);border-radius:10px;padding:22px;text-align:center;color:var(--text-3);font-size:13px;cursor:pointer;transition:all .15s;background:#fff}
.file-drop:hover,.file-drop.over{border-color:var(--blue);color:var(--blue);background:var(--blue-bg)}
.file-drop input{display:none}
.file-drop i{width:24px;height:24px;margin-bottom:6px;color:var(--text-4)}
.file-drop:hover i,.file-drop.over i{color:var(--blue)}
.file-preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;margin-top:12px}
.file-preview-item{position:relative;border:1px solid var(--border-l);border-radius:8px;overflow:hidden;background:#fff}
.file-preview-item img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block}
.file-preview-item .fp-name{padding:4px 8px;font-size:10px;color:var(--text-3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.file-preview-item .fp-remove{position:absolute;top:4px;right:4px;width:22px;height:22px;background:rgba(239,68,68,.9);color:#fff;border:none;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;line-height:1}
.inline-label{display:flex;align-items:center;gap:8px;text-transform:none;letter-spacing:0;font-size:13px;font-weight:500;color:var(--text);margin:0;cursor:pointer}
.damage-item.highlight{outline:3px solid var(--blue);box-shadow:0 0 0 8px rgba(0,102,255,.12);animation:dmg-pulse 1.2s}
@keyframes dmg-pulse{0%{box-shadow:0 0 0 0 rgba(0,102,255,.4)}100%{box-shadow:0 0 0 10px rgba(0,102,255,0)}}
.view-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;background:#fff;border:1px solid var(--border);border-radius:999px;font-size:12.5px;font-weight:600;color:var(--text-2);cursor:pointer;transition:all .15s}
.view-btn:hover{border-color:var(--blue);color:var(--blue)}
.view-btn.active{background:var(--blue);border-color:var(--blue);color:#fff;box-shadow:0 3px 10px rgba(0,102,255,.25)}
.view-btn .svg-ic{display:inline-flex;color:currentColor}
.view-btn .cnt{background:rgba(0,0,0,.08);color:inherit;font-size:10.5px;font-weight:700;padding:2px 7px;border-radius:10px;min-width:17px;text-align:center;line-height:1.1}
.view-btn.active .cnt{background:rgba(255,255,255,.22)}
.view-btn .cnt:empty,.view-btn .cnt[data-view-count]:not([data-has]){display:none}
.svg-view{display:none}
.svg-view.active{display:block;animation:view-fade .2s}
@keyframes view-fade{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}
.damage-item.dim{opacity:.45}
.damage-item.dim:hover{opacity:.9}
.sort-item{cursor:grab;transition:transform .15s,opacity .15s,box-shadow .15s}
.sort-item:active{cursor:grabbing}
.sort-item.dragging{opacity:.5;transform:scale(.95)}
.sort-item.drag-over{box-shadow:0 0 0 3px var(--blue);transform:scale(1.02)}
.drag-handle{position:absolute;top:6px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.6);color:#fff;border-radius:6px;width:26px;height:22px;display:flex;align-items:center;justify-content:center;cursor:grab;opacity:0;transition:opacity .15s;pointer-events:none}
.sort-item:hover .drag-handle{opacity:1}
.drag-handle i{width:14px;height:14px}
</style>

@if($errors->any())
<div class="card" style="margin-bottom:14px;padding:14px 18px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <i data-lucide="alert-circle" style="width:18px;height:18px;color:#dc2626"></i>
        <strong style="font-size:14px;color:#991b1b">Formularz zawiera błędy:</strong>
    </div>
    <ul style="margin:0;padding-left:20px;font-size:12.5px;color:#991b1b;line-height:1.8">
        @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card" style="padding:0;margin-bottom:18px">
    <div class="tabs" role="tablist">
        <button type="button" class="tab-btn active" data-tab="basic"><i data-lucide="info"></i> Podstawowe</button>
        <button type="button" class="tab-btn" data-tab="engine"><i data-lucide="zap"></i> Silnik i historia</button>
        <button type="button" class="tab-btn" data-tab="vehicle"><i data-lucide="car"></i> Pojazd</button>
        <button type="button" class="tab-btn" data-tab="service"><i data-lucide="wrench"></i> Serwis i emisja</button>
        <button type="button" class="tab-btn" data-tab="seller"><i data-lucide="user"></i> Sprzedawca</button>
        <button type="button" class="tab-btn" data-tab="equipment"><i data-lucide="list-checks"></i> Wyposażenie</button>
        <button type="button" class="tab-btn" data-tab="condition"><i data-lucide="shield-check"></i> Stan i lakier</button>
        <button type="button" class="tab-btn" data-tab="damages"><i data-lucide="alert-triangle"></i> Uszkodzenia <span class="badge-count" id="cntDamages">{{ $existingDamages->count() }}</span></button>
        <button type="button" class="tab-btn" data-tab="tires"><i data-lucide="circle"></i> Opony <span class="badge-count" id="cntTires">{{ $existingTireSets->count() }}</span></button>
        <button type="button" class="tab-btn" data-tab="images"><i data-lucide="image"></i> Zdjęcia <span class="badge-count">{{ $car?->images->count() ?? 0 }}</span></button>
        <button type="button" class="tab-btn" data-tab="seo"><i data-lucide="search"></i> SEO <span class="badge-count" id="seoScoreBadge">—</span></button>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start">
<div>

{{-- ====== TAB: BASIC ====== --}}
<div class="tab-panel active" data-panel="basic">
    <div class="card">
        <h2>Dane podstawowe</h2>
        <div class="field-row">
            <div class="field">
                <label>Marka * <a href="#" id="brandAddToggle" style="float:right;font-size:11px;font-weight:600;color:var(--blue);text-decoration:none;display:inline-flex;align-items:center;gap:4px"><i data-lucide="plus" style="width:12px;height:12px"></i> Dodaj nową</a></label>
                <select name="brand_id" id="brandSelect" required>
                    <option value="">— wybierz —</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ old('brand_id',$car?->brand_id)==$b->id?'selected':'' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
                <div id="brandAddRow" style="display:none;margin-top:8px;gap:6px;align-items:stretch">
                    <input type="text" id="brandAddName" placeholder="Nazwa nowej marki" maxlength="255" style="flex:1;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px">
                    <button type="button" id="brandAddSubmit" class="btn btn-blue btn-sm" style="white-space:nowrap"><i data-lucide="check" style="width:14px;height:14px"></i> Dodaj</button>
                    <button type="button" id="brandAddCancel" class="btn btn-outline btn-sm" style="white-space:nowrap"><i data-lucide="x" style="width:14px;height:14px"></i></button>
                </div>
                <div id="brandAddError" style="display:none;color:#dc2626;font-size:12px;margin-top:6px"></div>
            </div>
            <div class="field">
                <label>Model *</label>
                <input type="text" name="model" value="{{ old('model',$car?->model) }}" required>
            </div>
        </div>
        <div class="field-row-3">
            <div class="field"><label>Kategoria</label><input type="text" name="category" value="{{ old('category',$car?->category) }}" placeholder="Sedan, SUV, Coupé..."></div>
            <div class="field"><label>Nadwozie</label>
                <select name="body_type" id="bodyTypeSelect">
                    <option value="">— wybierz —</option>
                    @foreach(['Sedan','SUV','Coupé','Bus','Kombi','Hatchback','Kabriolet','Pickup'] as $bt)
                    <option value="{{ $bt }}" {{ old('body_type',$car?->body_type)==$bt?'selected':'' }}>{{ $bt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field"><label>Identyfikator</label><input type="text" value="{{ $car?->identifier ?? '— zostanie wygenerowany —' }}" disabled></div>
        </div>
    </div>

    <div class="card">
        <h2>Cena</h2>
        <div class="field-row-3">
            <div class="field"><label>Cena</label><input type="number" name="price" value="{{ old('price',$car?->price) }}" step="0.01" min="0"></div>
            <div class="field"><label>Waluta</label><input type="text" name="currency" value="{{ old('currency',$car?->currency ?? 'PLN') }}" maxlength="5"></div>
            <div class="field"><label>Typ ceny</label><input type="text" name="price_type" value="{{ old('price_type',$car?->price_type) }}" placeholder="VAT marża, netto..."></div>
        </div>
        <div class="field">
            <label>Opodatkowanie (taxation)</label>
            <input type="text" name="taxation" value="{{ old('taxation',$car?->taxation) }}" placeholder="np. Różnicowe opodatkowanie, FV netto">
        </div>
    </div>
</div>

{{-- ====== TAB: ENGINE / HISTORY ====== --}}
<div class="tab-panel" data-panel="engine">
    <div class="card">
        <h2>Historia eksploatacji</h2>
        <div class="field-row-3">
            <div class="field"><label>Pierwsza rejestracja</label><input type="text" name="first_registration" value="{{ old('first_registration',$car?->first_registration) }}" placeholder="11/2018"></div>
            <div class="field"><label>Przebieg (km)</label><input type="number" name="mileage" value="{{ old('mileage',$car?->mileage) }}" min="0"></div>
            <div class="field"><label>Poprzednich właścicieli</label><input type="number" name="previous_owners" value="{{ old('previous_owners',$car?->previous_owners) }}" min="0"></div>
        </div>
        <div class="field-row">
            <div class="field"><label>Liczba kluczy</label><input type="number" name="number_of_keys" value="{{ old('number_of_keys',$car?->number_of_keys) }}" min="0"></div>
            <div class="field"><label>Użytkowanie</label><input type="text" name="business_use" value="{{ old('business_use',$car?->business_use) }}" placeholder="Prywatne, Firma, Leasing..."></div>
        </div>
    </div>

    <div class="card">
        <h2>Jednostka napędowa</h2>
        <div class="field-row-3">
            <div class="field"><label>Paliwo</label><select name="fuel_type"><option value="">Wybierz typ paliwa</option>@foreach(['Benzyna','Diesel','Hybryda','Hybryda plug-in','Elektryczny','LPG','CNG'] as $ft)<option value="{{ $ft }}" {{ old('fuel_type',$car?->fuel_type)==$ft?'selected':'' }}>{{ $ft }}</option>@endforeach</select></div>
            <div class="field"><label>Skrzynia</label><select name="transmission"><option value="">Wybierz skrzynię biegów</option>@foreach(['Automatyczna','Manualna','CVT','Półautomatyczna (DSG/DCT)'] as $tr)<option value="{{ $tr }}" {{ old('transmission',$car?->transmission)==$tr?'selected':'' }}>{{ $tr }}</option>@endforeach</select></div>
            <div class="field"><label>Szczegóły skrzyni</label><input type="text" name="transmission_detail" value="{{ old('transmission_detail',$car?->transmission_detail) }}" placeholder="8-biegowa, DSG..."></div>
        </div>
        <div class="field-row-3">
            <div class="field"><label>Pojemność (ccm)</label><input type="number" name="engine_capacity" value="{{ old('engine_capacity',$car?->engine_capacity) }}" min="0" placeholder="np. 1984" step="1"></div>
            <div class="field"><label>Moc (KM)</label><input type="number" name="power_hp" value="{{ old('power_hp',$car?->power_hp) }}" min="0"></div>
            <div class="field"><label>Moc (kW)</label><input type="number" name="power_kw" value="{{ old('power_kw',$car?->power_kw) }}" min="0"></div>
        </div>
    </div>

    <div class="card">
        <h2>Film z pracy silnika</h2>
        <p style="font-size:12px;color:var(--text-3);margin-bottom:14px">Dodaj link do YouTube/Vimeo <strong>lub</strong> wgraj plik wideo (MP4, WebM, MOV, AVI, MKV — max 100 MB).</p>
        <div style="display:flex;gap:4px;background:var(--bg);padding:4px;border-radius:9px;margin-bottom:14px;width:fit-content">
            <button type="button" id="vidTabUrl" class="btn btn-sm" style="background:{{ $car?->engine_video_url ? '#fff' : 'transparent' }};border:none">🔗 Link URL</button>
            <button type="button" id="vidTabFile" class="btn btn-sm" style="background:{{ $car?->engine_video_path && !$car?->engine_video_url ? '#fff' : 'transparent' }};border:none">📤 Plik na serwerze</button>
        </div>

        <div id="vidUrlPanel">
            <div class="field" style="margin-bottom:10px">
                <label>URL filmu</label>
                <input type="url" name="engine_video_url" id="engineVideoUrl" value="{{ old('engine_video_url',$car?->engine_video_url) }}" placeholder="https://youtu.be/... lub https://vimeo.com/...">
            </div>
            <div id="vidUrlPreview"></div>
        </div>

        <div id="vidFilePanel" style="display:none">
            @if($car?->engine_video_path)
            <div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:10px;margin-bottom:10px;display:flex;gap:12px;align-items:center">
                <video src="{{ $car->engine_video_file_url }}" controls preload="metadata" style="max-width:220px;border-radius:8px;background:#000"></video>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:13px;margin-bottom:4px">Aktualny plik</div>
                    <div style="font-size:11.5px;color:var(--text-3);word-break:break-all">{{ $car->engine_video_path }}</div>
                    <label class="inline-label" style="margin-top:8px;color:#b91c1c"><input type="checkbox" name="remove_engine_video" value="1"> Usuń ten plik przy zapisie</label>
                </div>
            </div>
            @endif
            <label class="file-drop" id="videoDrop">
                <i data-lucide="film"></i>
                <div>{{ $car?->engine_video_path ? 'Kliknij lub przeciągnij, aby podmienić plik' : 'Kliknij lub przeciągnij plik wideo' }}</div>
                <input type="file" name="engine_video_file" id="engineVideoFile" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska">
            </label>
            <div id="videoLocalPreview" style="margin-top:10px"></div>
        </div>
    </div>
</div>

{{-- ====== TAB: VEHICLE ====== --}}
<div class="tab-panel" data-panel="vehicle">
    <div class="card">
        <h2>Nadwozie</h2>
        <div class="field-row-3">
            <div class="field"><label>Drzwi</label><input type="number" name="doors" value="{{ old('doors',$car?->doors) }}" min="1" max="7"></div>
            <div class="field"><label>Siedzenia</label><input type="number" name="seats" value="{{ old('seats',$car?->seats) }}" min="1"></div>
            <div class="field"><label>Masa (kg)</label><input type="number" name="weight" value="{{ old('weight',$car?->weight) }}" min="0"></div>
        </div>
        <div class="field-row-3">
            <div class="field"><label>Kolor</label><input type="text" name="color" value="{{ old('color',$car?->color) }}"></div>
            <div class="field"><label>Kod koloru</label><input type="text" name="color_code" value="{{ old('color_code',$car?->color_code) }}"></div>
            <div class="field"><label>Tapicerka</label><input type="text" name="upholstery" value="{{ old('upholstery',$car?->upholstery) }}"></div>
        </div>
        <div class="field-row">
            <div class="field"><label>VIN</label><input type="text" name="vin" value="{{ old('vin',$car?->vin) }}" maxlength="50"></div>
            <div class="field"><label>Kraj pochodzenia</label><input type="text" name="country_registration" value="{{ old('country_registration',$car?->country_registration) }}" placeholder="Niemcy, Polska, Francja..."></div>
        </div>
        <div class="field">
            <label class="inline-label">
                <input type="hidden" name="is_imported" value="0">
                <input type="checkbox" name="is_imported" value="1" {{ old('is_imported',$car?->is_imported ?? true)?'checked':'' }}> Import (sprowadzony z zagranicy)
            </label>
        </div>
    </div>

</div>

{{-- ====== TAB: SERVICE ====== --}}
<div class="tab-panel" data-panel="service">
    <div class="card">
        <h2>Serwis</h2>
        <div class="field-row-3">
            <div class="field"><label>Ostatni serwis</label><input type="text" name="last_service" value="{{ old('last_service',$car?->last_service) }}" placeholder="03/2025"></div>
            <div class="field"><label>Przy przebiegu</label><input type="text" name="last_service_mileage" value="{{ old('last_service_mileage',$car?->last_service_mileage) }}"></div>
            <div class="field"><label>Następna inspekcja</label><input type="text" name="next_inspection" value="{{ old('next_inspection',$car?->next_inspection) }}" placeholder="09/2026"></div>
        </div>
        <div class="field">
            <label>Dokumentacja serwisowa (opis)</label>
            <input type="text" name="service_documentation" value="{{ old('service_documentation',$car?->service_documentation) }}" placeholder="Pełna, częściowa...">
        </div>
    </div>

    <div class="card">
        <h2>Emisja i zużycie</h2>
        <div class="field-row-3">
            <div class="field"><label>Zużycie paliwa</label><input type="text" name="fuel_consumption" value="{{ old('fuel_consumption',$car?->fuel_consumption) }}"></div>
            <div class="field"><label>CO₂</label><input type="text" name="co2_emission" value="{{ old('co2_emission',$car?->co2_emission) }}"></div>
            <div class="field"><label>Klasa emisji</label><input type="text" name="emission_class" value="{{ old('emission_class',$car?->emission_class) }}" placeholder="Euro 6..."></div>
        </div>
        <div class="field">
            <label>Procedura pomiaru paliwa</label>
            <input type="text" name="fuel_procedure" value="{{ old('fuel_procedure',$car?->fuel_procedure) }}" placeholder="WLTP, NEDC...">
        </div>
    </div>

    <div class="card">
        <h2>Dokumenty</h2>
        <div class="field-row">
            <div class="field"><label>Książka serwisowa</label><input type="text" name="service_book" value="{{ old('service_book',$car?->service_book) }}" placeholder="Tak / Nie / Cyfrowa"></div>
            <div class="field"><label>Dokumenty COC</label><input type="text" name="coc_documents" value="{{ old('coc_documents',$car?->coc_documents) }}" placeholder="Tak / Nie"></div>
        </div>
        <div class="field-row">
            <div class="field"><label>Teczka pojazdu</label><input type="text" name="vehicle_folder" value="{{ old('vehicle_folder',$car?->vehicle_folder) }}" placeholder="Tak / Nie"></div>
            <div class="field"><label>Raport HU/AU</label><input type="text" name="hu_au_report" value="{{ old('hu_au_report',$car?->hu_au_report) }}" placeholder="Tak / Nie"></div>
        </div>
    </div>
</div>

{{-- ====== TAB: SELLER ====== --}}
<div class="tab-panel" data-panel="seller">
    <div class="card">
        <h2>Dane sprzedawcy</h2>
        <div class="field-row">
            <div class="field"><label>Nazwa / imię i nazwisko</label><input type="text" name="seller_name" value="{{ old('seller_name',$car?->seller_name) }}"></div>
            <div class="field"><label>Data przyjęcia pojazdu</label><input type="date" name="reception_date" value="{{ old('reception_date', $car?->reception_date?->format('Y-m-d')) }}"></div>
        </div>
        <div class="field-row">
            <div class="field"><label>Telefon</label><input type="text" name="seller_phone" value="{{ old('seller_phone',$car?->seller_phone) }}"></div>
            <div class="field"><label>E-mail</label><input type="email" name="seller_email" value="{{ old('seller_email',$car?->seller_email) }}"></div>
        </div>
        <div class="field">
            <label>Notatka komisowa (wewnętrzna)</label>
            <textarea name="commission_note" rows="3" placeholder="Prowizja, ustalenia, uwagi dla zespołu...">{{ old('commission_note',$car?->commission_note) }}</textarea>
        </div>
    </div>
</div>

{{-- ====== TAB: EQUIPMENT ====== --}}
<div class="tab-panel" data-panel="equipment">
    <div class="card">
        <h2>Wyposażenie</h2>
        <p style="font-size:12px;color:var(--text-3);margin-bottom:14px">Każda pozycja w osobnej linii. Wpisy zapiszą się jako tablica.</p>
        @foreach($eqCategories as $key => $label)
        <div class="field">
            <label>{{ $label }}</label>
            <textarea name="equipment[{{ $key }}]" rows="4" placeholder="Jedna pozycja w linii">{{ $eqToText($equipment[$key] ?? null) }}</textarea>
        </div>
        @endforeach
    </div>
</div>

{{-- ====== TAB: CONDITION ====== --}}
<div class="tab-panel" data-panel="condition">
    <div class="card">
        <h2>Stan techniczny</h2>
        <p style="font-size:12px;color:var(--text-3);margin-bottom:14px">Każda uwaga w osobnej linii. Lista per kategoria.</p>
        @foreach($techCategories as $key => $label)
        <div class="field">
            <label>{{ $label }}</label>
            <textarea name="technical_conditions[{{ $key }}]" rows="3" placeholder="Jedna pozycja w linii">{{ $eqToText($technicalConditions[$key] ?? null) }}</textarea>
        </div>
        @endforeach
    </div>

    <div class="card">
        <h2>Pomiar grubości lakieru</h2>
        <p style="font-size:12px;color:var(--text-3);margin-bottom:14px">Wpisz wartość µm dla każdego elementu. Pozostaw puste jeśli brak pomiaru. <span style="color:#10b981">●</span> 90–150 µm <span style="color:#f59e0b">●</span> 150–300 µm <span style="color:#ef4444">●</span> >300 µm</p>
        @php
            $panelNames = [
                'Maska', 'Błotnik przedni lewy', 'Błotnik przedni prawy',
                'Drzwi przednie lewe', 'Drzwi przednie prawe',
                'Drzwi tylne lewe', 'Drzwi tylne prawe',
                'Klapa bagażnika', 'Dach',
                'Próg lewy', 'Próg prawy',
                'Zderzak przedni', 'Zderzak tylny',
            ];
            // Build lookup from existing data
            $paintLookup = [];
            foreach(collect($paintMeasurements)->values() as $p) {
                $area = $p['area'] ?? array_key_first((array) $p);
                $val = is_array($p) ? ($p['value'] ?? reset($p)) : $p;
                $paintLookup[mb_strtolower(trim($area))] = preg_replace('/[^0-9]/', '', $val);
            }
        @endphp
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px">
            @foreach($panelNames as $i => $panel)
            @php $existing = $paintLookup[mb_strtolower($panel)] ?? ''; @endphp
            <div style="display:flex;align-items:center;gap:8px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px">
                <input type="hidden" name="paint_measurements[{{ $i }}][area]" value="{{ $panel }}">
                <span style="flex:1;font-size:12.5px;font-weight:600;color:#374151;white-space:nowrap">{{ $panel }}</span>
                <div style="display:flex;align-items:center;gap:4px;width:90px">
                    <input type="number" name="paint_measurements[{{ $i }}][value]" value="{{ $existing }}" placeholder="—" min="0" max="9999" style="width:60px;padding:5px 6px;font-size:13px;font-weight:700;text-align:center;border:1px solid #d1d5db;border-radius:6px">
                    <span style="font-size:11px;font-weight:600;color:#9ca3af">µm</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ====== TAB: DAMAGES ====== --}}
<div class="tab-panel" data-panel="damages">
    <div class="card">
        <h2>Uszkodzenia — klikalny schemat auta</h2>
        <p style="font-size:12px;color:var(--text-3);margin-bottom:14px">Wybierz widok (góra / przód / tył / bok) i kliknij w miejsce uszkodzenia. Każdy marker zostaje przypisany do wybranego widoku.</p>

        <div class="view-switch" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px">
            <button type="button" class="view-btn active" data-view="top"><span class="svg-ic">@include('admin.cars.partials.car-icon-top')</span> Góra <span class="cnt" data-view-count="top">0</span></button>
            <button type="button" class="view-btn" data-view="front"><span class="svg-ic">@include('admin.cars.partials.car-icon-front')</span> Przód <span class="cnt" data-view-count="front">0</span></button>
            <button type="button" class="view-btn" data-view="rear"><span class="svg-ic">@include('admin.cars.partials.car-icon-rear')</span> Tył <span class="cnt" data-view-count="rear">0</span></button>
            <button type="button" class="view-btn" data-view="left"><span class="svg-ic">@include('admin.cars.partials.car-icon-side')</span> Lewy bok <span class="cnt" data-view-count="left">0</span></button>
            <button type="button" class="view-btn" data-view="right"><span class="svg-ic" style="transform:scaleX(-1)">@include('admin.cars.partials.car-icon-side')</span> Prawy bok <span class="cnt" data-view-count="right">0</span></button>
        </div>

        <div style="display:grid;grid-template-columns:1fr;gap:14px">
            <div class="car-diagram-wrap" id="carDiagramWrap" style="position:relative;background:linear-gradient(180deg,#fafafb,#f0f0f2);border:1px solid var(--border-l);border-radius:14px;padding:24px;cursor:crosshair;user-select:none;min-height:360px">
                <div id="svgStage" style="max-width:540px;margin:0 auto;position:relative">
                    <div class="svg-view active" data-view="top">
                        @php
                            $bodyTypeMap = [
                                'sedan' => 'sedan', 'suv' => 'suv', 'coupé' => 'coupe', 'coupe' => 'coupe',
                                'bus' => 'van', 'van' => 'van', 'kombi' => 'kombi', 'hatchback' => 'hatchback',
                                'kabriolet' => 'sedan', 'cabriolet' => 'sedan', 'pickup' => 'suv',
                            ];
                            $btKey = strtolower(old('body_type', $car?->body_type ?? 'sedan'));
                            $topImg = $bodyTypeMap[$btKey] ?? 'sedan';
                        @endphp
                        <img id="damageTopImg" src="/img/body-types-top/{{ $topImg }}.png" alt="Schemat pojazdu" draggable="false" style="width:100%;height:auto;display:block;pointer-events:none">
                    </div>
                    <div class="svg-view" data-view="front">@include('admin.cars.partials.view-front')</div>
                    <div class="svg-view" data-view="rear">@include('admin.cars.partials.view-rear')</div>
                    <div class="svg-view" data-view="left">@include('admin.cars.partials.view-side')</div>
                    <div class="svg-view" data-view="right" style="transform:scaleX(-1)">@include('admin.cars.partials.view-side')</div>
                </div>
                <div id="diagramMarkers" style="position:absolute;inset:24px;pointer-events:none"></div>
                <div style="position:absolute;top:14px;right:14px;background:rgba(10,10,10,.78);color:#fff;font-size:11px;padding:6px 11px;border-radius:999px;pointer-events:none;display:flex;align-items:center;gap:5px;backdrop-filter:blur(6px)"><i data-lucide="mouse-pointer-click" style="width:12px;height:12px"></i> Kliknij aby dodać marker</div>
                <div id="viewLabel" style="position:absolute;top:14px;left:14px;background:rgba(255,255,255,.92);color:var(--text);font-size:11.5px;font-weight:700;padding:5px 12px;border-radius:999px;pointer-events:none;text-transform:uppercase;letter-spacing:.4px;border:1px solid var(--border-l)">WIDOK: GÓRA</div>
            </div>

            <div id="damageRepeater">
                @foreach($existingDamages as $i => $d)
                <div class="repeater-item damage-item" data-index="{{ $i }}">
                    <button type="button" class="rmv" onclick="removeDamage(this)"><i data-lucide="x"></i></button>
                    <input type="hidden" name="damages[{{ $i }}][id]" value="{{ $d->id }}">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                        <div class="damage-num" style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0">{{ $i + 1 }}</div>
                        <input type="text" name="damages[{{ $i }}][area]" value="{{ $d->area }}" placeholder="Nazwa obszaru (np. Maska, Prawe drzwi...)" style="flex:1;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13.5px;font-weight:600">
                    </div>
                    <div class="field-row-3">
                        <div class="field" style="margin:0">
                            <label>Typ</label>
                            <select name="damages[{{ $i }}][type]">
                                <option value="damage" {{ $d->type=='damage'?'selected':'' }}>Aktualne uszkodzenie</option>
                                <option value="repaired" {{ $d->type=='repaired'?'selected':'' }}>Naprawione</option>
                                <option value="accident" {{ $d->type=='accident'?'selected':'' }}>Po wypadku</option>
                            </select>
                        </div>
                        <div class="field" style="margin:0">
                            <label>Istotność</label>
                            <select name="damages[{{ $i }}][severity]">
                                <option value="info" {{ $d->severity=='info'?'selected':'' }}>Info</option>
                                <option value="warning" {{ $d->severity=='warning'?'selected':'' }}>Ostrzeżenie</option>
                                <option value="critical" {{ $d->severity=='critical'?'selected':'' }}>Krytyczne</option>
                            </select>
                        </div>
                        <div class="field" style="margin:0"><label>Tagi (,)</label><input type="text" name="damages[{{ $i }}][tags]" value="{{ is_array($d->tags)?implode(', ', $d->tags):'' }}" placeholder="rysa, kosmetyczne"></div>
                    </div>
                    <input type="hidden" name="damages[{{ $i }}][position_x]" value="{{ $d->position_x }}" class="pos-x">
                    <input type="hidden" name="damages[{{ $i }}][position_y]" value="{{ $d->position_y }}" class="pos-y">
                    <input type="hidden" name="damages[{{ $i }}][position_view]" value="{{ $d->position_view ?? 'top' }}" class="pos-view">
                    <div class="field" style="margin:10px 0 0;display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-3)">
                        <i data-lucide="map-pin" style="width:13px;height:13px"></i>
                        <span>Widok: <strong class="view-name" style="color:var(--text)">{{ ['top'=>'Góra','front'=>'Przód','rear'=>'Tył','left'=>'Lewy bok','right'=>'Prawy bok'][$d->position_view ?? 'top'] ?? 'Góra' }}</strong></span>
                        <span style="color:var(--text-4)">·</span>
                        <button type="button" onclick="goToMarker(this)" style="background:none;border:none;color:var(--blue);font-size:12px;font-weight:600;cursor:pointer;padding:0">Pokaż na schemacie →</button>
                    </div>
                    <div class="field" style="margin:10px 0 0">
                        <label>Opis / notatka</label>
                        <textarea name="damages[{{ $i }}][description]" rows="2" placeholder="Szczegóły uszkodzenia...">{{ $d->description }}</textarea>
                    </div>
                    <div class="field" style="margin:10px 0 0">
                        <label>Zdjęcie uszkodzenia</label>
                        @if($d->image_path)
                        <div class="existing-img" style="display:flex;gap:10px;align-items:flex-start;background:#fff;border:1px solid var(--border-l);border-radius:8px;padding:8px;margin-bottom:6px" data-lightbox="{{ $d->image_url }}" data-gallery="admin-damage-markers" data-caption="{{ $d->area }}">
                            <img src="{{ $d->image_url }}" alt="" style="width:120px;height:80px;object-fit:cover;border-radius:6px">
                            <div style="flex:1;font-size:11.5px;color:var(--text-3);word-break:break-all">{{ $d->image_path }}</div>
                            <label class="inline-label" style="color:#b91c1c;font-size:12px;flex-shrink:0"><input type="checkbox" name="damages[{{ $i }}][remove_image]" value="1"> Usuń</label>
                        </div>
                        @endif
                        <input type="file" name="damages[{{ $i }}][image]" accept="image/*" class="damage-img-input" onchange="previewDamageImg(this)">
                        <div class="damage-img-preview" style="margin-top:8px"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="repeater-add" onclick="addDamageItem()"><i data-lucide="plus"></i> Dodaj uszkodzenie (ręcznie)</button>
        </div>
    </div>
</div>

{{-- ====== TAB: TIRES ====== --}}
<div class="tab-panel" data-panel="tires">
    <div class="card">
        <h2>Zestawy opon</h2>
        <p style="font-size:12px;color:var(--text-3);margin-bottom:14px">Każdy zestaw zawiera 4 koła. Możesz pominąć wypełnianie pojedynczych kół.</p>
        <div id="tireSetRepeater">
            @foreach($existingTireSets as $i => $set)
            @php $tires = $set->tires ?? collect(); @endphp
            <div class="repeater-item tire-set-item">
                <button type="button" class="rmv" onclick="this.closest('.tire-set-item').remove();refreshCounts()"><i data-lucide="x"></i></button>
                <div class="field-row-3">
                    <div class="field" style="margin:0"><label>Numer zestawu</label><input type="number" name="tire_sets[{{ $i }}][set_number]" value="{{ $set->set_number ?? $i+1 }}" min="1"></div>
                    <div class="field" style="margin:0"><label>Typ opon</label><input type="text" name="tire_sets[{{ $i }}][tire_type]" value="{{ $set->tire_type }}" placeholder="Letnie, Zimowe, Całoroczne"></div>
                    <div class="field" style="margin:0"><label>Felgi</label><input type="text" name="tire_sets[{{ $i }}][rim]" value="{{ $set->rim }}" placeholder="Aluminium 18''"></div>
                </div>
                <label class="inline-label" style="margin:8px 0">
                    <input type="hidden" name="tire_sets[{{ $i }}][is_mounted]" value="0">
                    <input type="checkbox" name="tire_sets[{{ $i }}][is_mounted]" value="1" {{ $set->is_mounted?'checked':'' }}> Obecnie zamontowane
                </label>
                <div class="field" style="margin:6px 0"><label>Uwagi</label><input type="text" name="tire_sets[{{ $i }}][notes]" value="{{ $set->notes }}"></div>

                <div style="margin-top:10px">
                    <div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.3px;margin-bottom:6px">Koła w zestawie</div>
                    @foreach(['front_left'=>'Przód L','front_right'=>'Przód P','rear_left'=>'Tył L','rear_right'=>'Tył P'] as $pos => $label)
                        @php $tire = $tires->firstWhere('position', $pos); @endphp
                        <div style="display:grid;grid-template-columns:90px 1fr 2fr;gap:8px;margin-bottom:6px;align-items:center;font-size:12.5px">
                            <strong>{{ $label }}</strong>
                            <input type="hidden" name="tire_sets[{{ $i }}][tires][{{ $pos }}][position]" value="{{ $pos }}">
                            <input type="text" name="tire_sets[{{ $i }}][tires][{{ $pos }}][tread_depth]" value="{{ $tire?->tread_depth }}" placeholder="Bieżnik (mm)">
                            <input type="text" name="tire_sets[{{ $i }}][tires][{{ $pos }}][condition]" value="{{ $tire && is_array($tire->condition) ? implode(', ', $tire->condition) : '' }}" placeholder="stan: dobry, pęknięcie — oddziel przecinkiem">
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" class="repeater-add" onclick="addTireSet()"><i data-lucide="plus"></i> Dodaj zestaw opon</button>
    </div>
</div>

{{-- ====== TAB: IMAGES ====== --}}
<div class="tab-panel" data-panel="images">
    @if(!$car)
    <div class="card" style="background:#eff6ff;border:1px solid #bfdbfe">
        <p style="font-size:13px;color:#1e40af;margin:0;display:flex;gap:8px;align-items:center">
            <i data-lucide="info" style="width:16px;height:16px;flex-shrink:0"></i>
            <span>Wybierz zdjęcia poniżej. Zostaną zapisane razem z autem po kliknięciu <strong>"Zapisz"</strong>.</span>
        </p>
    </div>
    @endif
    <div class="card">
        <h2>Galeria <span style="font-size:12px;font-weight:500;color:var(--text-3);margin-left:8px">— przeciągnij, aby zmienić kolejność</span></h2>
        <p style="font-size:12px;color:var(--text-3);margin:-6px 0 14px">Alt text wypełniany automatycznie z tytułu auta — możesz nadpisać dla lepszego SEO.</p>
        @if($car && $car->galleryImages->count())
        <div id="galleryGrid" data-sortable data-type="gallery" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:14px">
            @foreach($car->galleryImages as $img)
            <div class="sort-item" data-img-id="{{ $img->id }}" draggable="true" style="background:#fff;border:1px solid var(--border-l);border-radius:10px;overflow:hidden">
                <div class="img-tile" data-img-id="{{ $img->id }}" data-lightbox="{{ $img->url }}" data-gallery="admin-gallery" data-caption="{{ $img->alt }}" style="border:none;border-radius:0">
                    <img src="{{ $img->url }}" alt="{{ $img->alt }}">
                    <span class="drag-handle" title="Przeciągnij, aby zmienić kolejność"><i data-lucide="grip-vertical"></i></span>
                    <label class="primary-badge" title="Zaznacz jako główne"><input type="radio" name="primary_image_id" value="{{ $img->id }}" {{ $img->is_primary?'checked':'' }}> Główne</label>
                    <button type="button" class="del-toggle" onclick="toggleDelete(this,{{ $img->id }},'gallery')"><i data-lucide="x"></i></button>
                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" style="display:none">
                    <input type="hidden" name="image_order[]" value="{{ $img->id }}" class="sort-order-input">
                </div>
                <div style="padding:8px 10px">
                    <input type="text" name="image_alt[{{ $img->id }}]" value="{{ $img->alt_text }}" placeholder="{{ $img->alt }}" style="width:100%;padding:6px 9px;border:1px solid var(--border);border-radius:6px;font-size:11.5px" title="Alt text — opis zdjęcia dla SEO i accessibility">
                </div>
            </div>
            @endforeach
        </div>
        @elseif($car)
        <p style="color:var(--text-3);font-size:12.5px;margin-bottom:10px">Brak zdjęć galerii.</p>
        @endif
        <label class="file-drop" id="galleryDrop" data-upload-type="gallery">
            <i data-lucide="image-plus"></i>
            <div>Kliknij lub przeciągnij pliki, aby dodać zdjęcia galerii</div>
            <input type="file" name="gallery_images[]" multiple accept="image/*">
        </label>
        <div id="galleryUploadProgress" style="display:none;margin-top:10px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <div style="flex:1;background:#e5e7eb;border-radius:6px;height:8px;overflow:hidden">
                    <div id="galleryProgressBar" style="width:0%;height:100%;background:#0066ff;border-radius:6px;transition:width .2s"></div>
                </div>
                <span id="galleryProgressText" style="font-size:12px;font-weight:600;color:var(--text-2);white-space:nowrap">0/0</span>
            </div>
        </div>
        <div id="galleryUploadedGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-top:10px"></div>
    </div>

    <div class="card">
        <h2>Zdjęcia uszkodzeń</h2>
        @if($car && $car->damageImages->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:14px">
            @foreach($car->damageImages as $img)
            <div style="background:#fff;border:1px solid var(--border-l);border-radius:10px;overflow:hidden">
                <div class="img-tile" data-img-id="{{ $img->id }}" data-lightbox="{{ $img->url }}" data-gallery="admin-damage" data-caption="{{ $img->alt }}" style="border:none;border-radius:0">
                    <img src="{{ $img->url }}" alt="{{ $img->alt }}">
                    <button type="button" class="del-toggle" onclick="toggleDelete(this,{{ $img->id }},'damage')"><i data-lucide="x"></i></button>
                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" style="display:none">
                </div>
                <div style="padding:8px 10px">
                    <input type="text" name="image_alt[{{ $img->id }}]" value="{{ $img->alt_text }}" placeholder="{{ $img->alt }}" style="width:100%;padding:6px 9px;border:1px solid var(--border);border-radius:6px;font-size:11.5px">
                </div>
            </div>
            @endforeach
        </div>
        @elseif($car)
        <p style="color:var(--text-3);font-size:12.5px;margin-bottom:10px">Brak zdjęć uszkodzeń.</p>
        @endif
        <label class="file-drop" id="damageDrop" data-upload-type="damage">
            <i data-lucide="image-plus"></i>
            <div>Kliknij lub przeciągnij pliki, aby dodać zdjęcia uszkodzeń</div>
            <input type="file" name="damage_images[]" multiple accept="image/*">
        </label>
        <div id="damageUploadProgress" style="display:none;margin-top:10px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <div style="flex:1;background:#e5e7eb;border-radius:6px;height:8px;overflow:hidden">
                    <div id="damageProgressBar" style="width:0%;height:100%;background:#0066ff;border-radius:6px;transition:width .2s"></div>
                </div>
                <span id="damageProgressText" style="font-size:12px;font-weight:600;color:var(--text-2);white-space:nowrap">0/0</span>
            </div>
        </div>
        <div id="damageUploadedGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-top:10px"></div>
    </div>

    {{-- ============= 360° PANORAMA (jedno zdjęcie equirectangular) ============= --}}
    <div class="card">
        <h2 style="display:flex;align-items:center;gap:8px"><i data-lucide="globe" style="width:18px;height:18px;color:var(--blue)"></i> Panorama 360° — wnętrze</h2>
        <div style="font-size:12px;color:var(--text-3);margin:-6px 0 14px;display:flex;flex-direction:column;gap:8px">
            <p style="margin:0">Potrzebne jest <strong>jedno zdjęcie equirectangular</strong> (format 2:1, np. 4096×2048 px) — to standardowy format panoram sferycznych, który przeglądarka Pannellum zamienia w widok 360°.</p>
            <div style="background:#f0f6ff;border:1px solid #c7dcff;border-radius:8px;padding:10px 14px">
                <div style="font-weight:700;margin-bottom:6px;color:#1a1a1a">📱 Bez zewnętrznych aplikacji — natywna kamera telefonu</div>
                <ol style="margin:0;padding-left:18px;line-height:1.8">
                    <li>Otwórz <strong>aparat</strong> telefonu → tryb <strong>Panorama</strong> (iPhone) lub <strong>Photo Sphere / Zdjęcie sferyczne</strong> (Android)</li>
                    <li>Usiądź na środku fotela kierowcy lub pasażera</li>
                    <li>Trzymaj telefon pionowo i obracaj się powoli we wszystkich kierunkach — aparat prowadzi sam</li>
                    <li>Po zakończeniu telefon automatycznie skleja zdjęcie w jeden plik JPG</li>
                    <li>Wyeksportuj/skopiuj plik i wgraj go tutaj</li>
                </ol>
            </div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px">
                <div style="font-weight:700;margin-bottom:6px;color:#1a1a1a">🌐 Alternatywnie — <strong>Google Street View</strong> (bezpłatna aplikacja)</div>
                <ol style="margin:0;padding-left:18px;line-height:1.8">
                    <li>Pobierz <strong>Google Street View</strong> (Android/iPhone) — aplikacja jest bezpłatna</li>
                    <li>W aplikacji wybierz ikonę aparatu → <strong>Utwórz Photo Sphere</strong></li>
                    <li>Postępuj zgodnie z pomarańczowymi kółkami — apka prowadzi przez wszystkie kierunki</li>
                    <li>Po zakończeniu wybierz <strong>Eksportuj</strong> → plik JPG equirectangular jest gotowy</li>
                </ol>
            </div>
        </div>

        @if($car?->pano360Image)
        <div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:12px;margin-bottom:12px;display:flex;gap:14px;align-items:flex-start">
            <img src="{{ $car->pano360Image->url }}" alt="" style="width:280px;height:140px;object-fit:cover;border-radius:8px;background:#000" data-lightbox="{{ $car->pano360Image->url }}" data-caption="Panorama wnętrza">
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:13px;margin-bottom:4px"><i data-lucide="check-circle" style="width:14px;height:14px;color:#10b981;vertical-align:-2px"></i> Panorama aktywna</div>
                <div style="font-size:11.5px;color:var(--text-3);word-break:break-all;margin-bottom:8px">{{ $car->pano360Image->path }}</div>
                <label class="inline-label" style="color:#b91c1c;font-size:12px"><input type="checkbox" name="remove_pano360" value="1"> Usuń panoramę przy zapisie</label>
                <div style="font-size:11.5px;color:var(--text-3);margin-top:6px">Aby podmienić — po prostu wgraj nowy plik niżej.</div>
            </div>
        </div>
        @endif

        <label class="file-drop" id="pano360Drop">
            <i data-lucide="globe"></i>
            <div>Kliknij lub przeciągnij <strong>jedno</strong> zdjęcie panoramiczne (max 15 MB)</div>
            <input type="file" name="pano360_image" accept="image/jpeg,image/png,image/webp">
        </label>
    </div>

    {{-- ============= 360° PANORAMA ZEWNĘTRZNA ============= --}}
    <div class="card">
        <h2 style="display:flex;align-items:center;gap:8px"><i data-lucide="scan" style="width:18px;height:18px;color:var(--blue)"></i> Panorama 360° — zewnętrze</h2>
        <div style="font-size:12px;color:var(--text-3);margin:-6px 0 14px;display:flex;flex-direction:column;gap:8px">
            <p style="margin:0">Potrzebne jest <strong>jedno zdjęcie equirectangular</strong> (format 2:1, np. 4096×2048 px) obejmujące pełny widok zewnętrzny dookoła pojazdu.</p>
            <div style="background:#f0f6ff;border:1px solid #c7dcff;border-radius:8px;padding:10px 14px">
                <div style="font-weight:700;margin-bottom:6px;color:#1a1a1a">📱 Bez zewnętrznych aplikacji — natywna kamera telefonu</div>
                <ol style="margin:0;padding-left:18px;line-height:1.8">
                    <li>Otwórz <strong>aparat</strong> telefonu → tryb <strong>Panorama</strong> (iPhone) lub <strong>Photo Sphere / Zdjęcie sferyczne</strong> (Android)</li>
                    <li>Stań w odległości ok. 3–4 m od boku auta, na środku pojazdu</li>
                    <li>Trzymaj telefon pionowo i obracaj się powoli o 360° w miejscu — aparat prowadzi sam</li>
                    <li>Po zakończeniu telefon skleja zdjęcie w jeden plik JPG — wyeksportuj i wgraj tutaj</li>
                </ol>
            </div>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px">
                <div style="font-weight:700;margin-bottom:6px;color:#1a1a1a">🌐 Alternatywnie — <strong>Google Street View</strong> (bezpłatna aplikacja)</div>
                <ol style="margin:0;padding-left:18px;line-height:1.8">
                    <li>Pobierz <strong>Google Street View</strong> (Android/iPhone) — aplikacja jest bezpłatna</li>
                    <li>W aplikacji wybierz ikonę aparatu → <strong>Utwórz Photo Sphere</strong></li>
                    <li>Stań przy aucie i podążaj za pomarańczowymi kółkami obchodząc pojazd</li>
                    <li>Po zakończeniu wybierz <strong>Eksportuj</strong> → plik JPG equirectangular jest gotowy</li>
                </ol>
            </div>
        </div>

        @if($car?->exteriorPano360Image)
        <div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:12px;margin-bottom:12px;display:flex;gap:14px;align-items:flex-start">
            <img src="{{ $car->exteriorPano360Image->url }}" alt="" style="width:280px;height:140px;object-fit:cover;border-radius:8px;background:#000" data-lightbox="{{ $car->exteriorPano360Image->url }}" data-caption="Panorama zewnętrzna">
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:13px;margin-bottom:4px"><i data-lucide="check-circle" style="width:14px;height:14px;color:#10b981;vertical-align:-2px"></i> Panorama zewnętrzna aktywna</div>
                <div style="font-size:11.5px;color:var(--text-3);word-break:break-all;margin-bottom:8px">{{ $car->exteriorPano360Image->path }}</div>
                <label class="inline-label" style="color:#b91c1c;font-size:12px"><input type="checkbox" name="remove_pano360ext" value="1"> Usuń panoramę przy zapisie</label>
                <div style="font-size:11.5px;color:var(--text-3);margin-top:6px">Aby podmienić — po prostu wgraj nowy plik niżej.</div>
            </div>
        </div>
        @endif

        <label class="file-drop" id="pano360extDrop">
            <i data-lucide="scan"></i>
            <div>Kliknij lub przeciągnij <strong>jedno</strong> zdjęcie panoramiczne zewnętrzne (max 15 MB)</div>
            <input type="file" name="pano360ext_image" accept="image/jpeg,image/png,image/webp">
        </label>
    </div>
</div>

{{-- ====== TAB: SEO ====== --}}
<div class="tab-panel" data-panel="seo">
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:18px 22px;border-bottom:1px solid var(--border-l);display:flex;justify-content:space-between;align-items:center;gap:12px;background:linear-gradient(180deg,#fafafb,#fff)">
            <div>
                <h2 style="margin:0 0 2px">SEO — widok w wyszukiwarce</h2>
                <p style="font-size:12px;color:var(--text-3);margin:0">Ustaw metadane — jeśli puste, wygenerują się z pól auta.</p>
            </div>
            <div id="seoScoreCard" style="display:flex;align-items:center;gap:10px;background:#fff;border:1px solid var(--border-l);padding:8px 14px;border-radius:12px">
                <div id="seoScoreCircle" style="width:36px;height:36px;border-radius:50%;background:conic-gradient(#10b981 0deg, #e5e5e7 0deg);display:flex;align-items:center;justify-content:center">
                    <div style="width:28px;height:28px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800" id="seoScorePct">0</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:.3px;font-weight:700">SEO Score</div>
                    <div id="seoScoreLabel" style="font-size:13px;font-weight:700;color:var(--text-3)">Nieoceniony</div>
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0">
            {{-- LEFT: inputs --}}
            <div style="padding:22px;border-right:1px solid var(--border-l)">
                <div class="field">
                    <label>Focus keyword <span style="color:var(--text-3);font-weight:400;text-transform:none;letter-spacing:0">(fraza pod którą chcesz być znaleziony)</span></label>
                    <input type="text" id="seoFocus" name="focus_keyword" value="{{ old('focus_keyword',$car?->focus_keyword) }}" placeholder="np. Audi A4 quattro 2020">
                </div>

                <div class="field">
                    <label style="display:flex;justify-content:space-between">
                        <span>Meta title</span>
                        <span id="seoTitleLen" style="text-transform:none;letter-spacing:0;font-size:11px;color:var(--text-3);font-weight:700">0 / 60</span>
                    </label>
                    <input type="text" id="seoTitle" name="meta_title" value="{{ old('meta_title',$car?->meta_title) }}" placeholder="Automatyczny: {{ $car?->seo_title ?? '—' }}" maxlength="180">
                    <div id="seoTitleBar" style="height:4px;background:#e5e5e7;border-radius:2px;margin-top:6px;overflow:hidden"><div style="height:100%;width:0;background:#e5e5e7;transition:all .2s" id="seoTitleBarFill"></div></div>
                    <p style="font-size:11.5px;color:var(--text-3);margin-top:5px">Zalecane: 50–60 znaków. Google pokaże więcej, ale utnie długie.</p>
                </div>

                <div class="field">
                    <label style="display:flex;justify-content:space-between">
                        <span>Meta description</span>
                        <span id="seoDescLen" style="text-transform:none;letter-spacing:0;font-size:11px;color:var(--text-3);font-weight:700">0 / 160</span>
                    </label>
                    <textarea id="seoDesc" name="meta_description" rows="3" placeholder="Automatyczny: {{ $car?->seo_description ?? '—' }}" maxlength="320">{{ old('meta_description',$car?->meta_description) }}</textarea>
                    <div id="seoDescBar" style="height:4px;background:#e5e5e7;border-radius:2px;margin-top:6px;overflow:hidden"><div style="height:100%;width:0;background:#e5e5e7;transition:all .2s" id="seoDescBarFill"></div></div>
                    <p style="font-size:11.5px;color:var(--text-3);margin-top:5px">Zalecane: 120–160 znaków. Max 320.</p>
                </div>

                <div class="field">
                    <label>URL (slug)</label>
                    <input type="text" id="seoSlug" value="{{ $car?->slug ?? '— wygeneruje się automatycznie —' }}" disabled>
                    <p style="font-size:11.5px;color:var(--text-3);margin-top:5px">Slug generowany z marki i modelu. Aby go zmienić, skontaktuj się z developerem.</p>
                </div>

                <div class="field">
                    <label class="inline-label" style="color:#b91c1c">
                        <input type="hidden" name="noindex" value="0">
                        <input type="checkbox" name="noindex" value="1" {{ old('noindex',$car?->noindex)?'checked':'' }}>
                        <span><strong>Noindex</strong> — ukryj stronę w wynikach Google (dobre dla szkiców).</span>
                    </label>
                </div>
            </div>

            {{-- RIGHT: previews + analysis --}}
            <div style="padding:22px;background:#fafafb">
                <div style="font-size:11px;color:var(--text-3);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:10px;display:flex;align-items:center;gap:6px"><i data-lucide="search" style="width:13px;height:13px"></i> Podgląd Google</div>

                <div id="serpPreview" style="background:#fff;border:1px solid var(--border-l);border-radius:10px;padding:14px 18px;margin-bottom:8px;font-family:arial,sans-serif">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                        <div style="width:26px;height:26px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700">C</div>
                        <div>
                            <div style="font-size:13px;color:#202124">CertiCars</div>
                            <div id="serpUrl" style="font-size:12px;color:#5f6368">certicars.pl › samochody › <span id="serpSlug">{{ $car?->slug ?? 'slug' }}</span></div>
                        </div>
                    </div>
                    <h3 id="serpTitle" style="color:#1a0dab;font-size:20px;font-weight:400;line-height:1.3;margin:6px 0 4px;cursor:pointer">{{ $car?->seo_title ?? 'Tytuł oferty' }}</h3>
                    <p id="serpDesc" style="color:#4d5156;font-size:14px;line-height:1.5;margin:0">{{ $car?->seo_description ?? 'Tutaj pojawi się opis oferty.' }}</p>
                </div>

                <div style="display:flex;gap:6px;margin-bottom:16px">
                    <button type="button" class="btn btn-outline btn-sm" id="serpModeDesktop" style="flex:1;justify-content:center">Desktop</button>
                    <button type="button" class="btn btn-outline btn-sm" id="serpModeMobile" style="flex:1;justify-content:center">Mobile</button>
                </div>

                <div style="font-size:11px;color:var(--text-3);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:10px;display:flex;align-items:center;gap:6px"><i data-lucide="share-2" style="width:13px;height:13px"></i> Podgląd Facebook / Open Graph</div>

                <div id="ogPreview" style="background:#fff;border:1px solid var(--border-l);border-radius:10px;overflow:hidden;margin-bottom:18px">
                    @if($car?->primaryImage)
                    <img src="{{ $car->primaryImage->url }}" alt="" style="width:100%;height:180px;object-fit:cover;display:block;background:#f0f0f2">
                    @else
                    <div style="width:100%;height:180px;background:linear-gradient(135deg,#e5e5e7,#c7c7cc);display:flex;align-items:center;justify-content:center;color:#9a9a9e"><i data-lucide="image" style="width:28px;height:28px"></i></div>
                    @endif
                    <div style="padding:10px 14px;background:#f0f2f5">
                        <div style="font-size:11px;text-transform:uppercase;color:#606770">certicars.pl</div>
                        <div id="ogTitle" style="font-weight:600;font-size:14.5px;color:#1c1e21;margin-top:2px;line-height:1.3;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">{{ $car?->seo_title ?? 'Tytuł' }}</div>
                        <div id="ogDesc" style="font-size:12.5px;color:#606770;margin-top:3px;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">{{ $car?->seo_description ?? '' }}</div>
                    </div>
                </div>

                <div style="font-size:11px;color:var(--text-3);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:10px;display:flex;align-items:center;gap:6px"><i data-lucide="sparkles" style="width:13px;height:13px"></i> Analiza SEO</div>
                <ul id="seoChecks" style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:6px"></ul>
            </div>
        </div>
    </div>
</div>

</div>

{{-- ====== SIDEBAR: STATUS ====== --}}
<div>
    <input type="hidden" name="active_tab" id="activeTabInput" value="basic">
    <div class="card" style="position:sticky;top:80px">
        <h2>Publikacja</h2>
        <div class="field">
            <label>Status</label>
            <select name="status">
                <option value="draft" {{ old('status',$car?->status ?? 'draft')=='draft'?'selected':'' }}>Szkic</option>
                <option value="active" {{ old('status',$car?->status)=='active'?'selected':'' }}>Aktywne</option>
                <option value="reserved" {{ old('status',$car?->status)=='reserved'?'selected':'' }}>Zarezerwowane</option>
                <option value="sold" {{ old('status',$car?->status)=='sold'?'selected':'' }}>Sprzedane</option>
            </select>
        </div>
        <div class="field">
            <label class="inline-label">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured',$car?->is_featured)?'checked':'' }}> Wyróżnione
            </label>
        </div>
        <div class="field">
            <label class="inline-label">
                <input type="hidden" name="is_sold" value="0">
                <input type="checkbox" name="is_sold" value="1" {{ old('is_sold',$car?->is_sold)?'checked':'' }}> Oznacz jako sprzedane
            </label>
        </div>
        <div class="field" style="background:rgba(0,102,255,.06);border:1px solid rgba(0,102,255,.18);border-radius:10px;padding:12px 14px;margin-bottom:14px">
            <label class="inline-label" style="font-weight:600">
                <input type="hidden" name="has_certicheck" value="0">
                <input type="checkbox" name="has_certicheck" value="1" {{ old('has_certicheck',$car?->has_certicheck)?'checked':'' }}>
                <span style="display:flex;align-items:center;gap:6px"><i data-lucide="shield-check" style="width:14px;height:14px;color:var(--blue)"></i> CertiCheck</span>
            </label>
            <p style="font-size:11.5px;color:var(--text-3);margin:6px 0 0 24px;line-height:1.45">Pokazuje plakietkę "CertiCheck" w katalogu oraz udostępnia broszurę PDF na stronie auta.</p>
        </div>
        <div class="field" style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.18);border-radius:10px;padding:12px 14px;margin-bottom:14px">
            <label class="inline-label" style="font-weight:600">
                <input type="hidden" name="available_now" value="0">
                <input type="checkbox" name="available_now" value="1" {{ old('available_now',$car?->available_now)?'checked':'' }}>
                <span style="display:flex;align-items:center;gap:6px"><i data-lucide="zap" style="width:14px;height:14px;color:#10b981"></i> Dostępny od ręki</span>
            </label>
            <p style="font-size:11.5px;color:var(--text-3);margin:6px 0 0 24px;line-height:1.45">Badge "Dostępny od ręki" na zdjęciu auta.</p>
        </div>
        <div class="field" style="background:rgba(99,102,241,.06);border:1px solid rgba(99,102,241,.18);border-radius:10px;padding:12px 14px;margin-bottom:14px">
            <label class="inline-label" style="font-weight:600">
                <input type="hidden" name="home_delivery" value="0">
                <input type="checkbox" name="home_delivery" value="1" {{ old('home_delivery',$car?->home_delivery)?'checked':'' }}>
                <span style="display:flex;align-items:center;gap:6px"><i data-lucide="truck" style="width:14px;height:14px;color:#6366f1"></i> Dostawa pod dom</span>
            </label>
            <p style="font-size:11.5px;color:var(--text-3);margin:6px 0 0 24px;line-height:1.45">Badge "Dostawa pod dom" na zdjęciu auta.</p>
        </div>
        <div class="field" style="background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.18);border-radius:10px;padding:12px 14px;margin-bottom:14px">
            <label class="inline-label" style="font-weight:600">
                <input type="hidden" name="has_gethelp" value="0">
                <input type="checkbox" name="has_gethelp" value="1" {{ old('has_gethelp',$car?->has_gethelp)?'checked':'' }}>
                <span style="display:flex;align-items:center;gap:6px"><i data-lucide="shield" style="width:14px;height:14px;color:#f59e0b"></i> GetHelp w cenie</span>
            </label>
            <div class="field" style="margin:8px 0 0 24px">
                <select name="gethelp_package" style="padding:7px 10px;font-size:12px;border-radius:8px;border:1px solid var(--border)">
                    <option value="Classic" {{ old('gethelp_package',$car?->gethelp_package)=='Classic'?'selected':'' }}>Classic</option>
                    <option value="Comfort" {{ old('gethelp_package',$car?->gethelp_package)=='Comfort'?'selected':'' }}>Comfort</option>
                    <option value="Grand" {{ old('gethelp_package',$car?->gethelp_package)=='Grand'?'selected':'' }}>Grand</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-blue" id="carFormSubmit" style="width:100%;justify-content:center;padding:13px"><i data-lucide="save"></i> {{ $car ? 'Zapisz zmiany' : 'Zapisz' }}</button>
        <a href="{{ route('admin.cars.index') }}" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:8px">Anuluj</a>
    </div>
</div>
</div>

<div class="sticky-save" id="stickySave">
    <div class="hint"><i data-lucide="alert-circle"></i> Masz niezapisane zmiany</div>
    <a href="{{ route('admin.cars.index') }}" class="btn btn-outline">Anuluj</a>
    <button type="button" class="btn btn-blue" onclick="document.getElementById('carFormSubmit').click()"><i data-lucide="save"></i> {{ $car ? 'Zapisz zmiany' : 'Zapisz' }}</button>
</div>

@push('scripts')
<script>
// ===== Drag-and-drop gallery reorder =====
(function(){
    const grid = document.getElementById('galleryGrid');
    if (!grid) return;

    let draggedEl = null;

    function syncOrder(){
        const items = grid.querySelectorAll('.sort-item');
        items.forEach((it, idx) => {
            const inp = it.querySelector('.sort-order-input');
            if (inp) inp.value = it.dataset.imgId;
            // image_order[] keeps DOM order naturally — but force re-index for clarity
        });
        // Re-collect hidden inputs in DOM order so the form submits the new sequence
        const hidden = grid.querySelectorAll('input[name="image_order[]"]');
        hidden.forEach(h => h.parentNode.appendChild(h));
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
            const after = (e.clientY - rect.top) > rect.height / 2 || (e.clientX - rect.left) > rect.width / 2;
            if (after) item.parentNode.insertBefore(draggedEl, item.nextSibling);
            else item.parentNode.insertBefore(draggedEl, item);
            syncOrder();
        });
    });
})();

// ===== Inline brand creation =====
(function(){
    const toggle = document.getElementById('brandAddToggle');
    const row    = document.getElementById('brandAddRow');
    const input  = document.getElementById('brandAddName');
    const submit = document.getElementById('brandAddSubmit');
    const cancel = document.getElementById('brandAddCancel');
    const errBox = document.getElementById('brandAddError');
    const select = document.getElementById('brandSelect');
    if (!toggle || !row || !select) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
              || document.querySelector('input[name="_token"]')?.value;

    function showError(msg){ errBox.textContent = msg; errBox.style.display = 'block'; }
    function clearError(){ errBox.textContent = ''; errBox.style.display = 'none'; }

    function open(){
        row.style.display = 'flex';
        input.value = '';
        clearError();
        setTimeout(()=>input.focus(), 30);
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
            // Insert sorted by name (case-insensitive)
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
        } catch (e) {
            showError('Błąd sieci — spróbuj ponownie.');
        } finally {
            submit.disabled = false;
            submit.innerHTML = originalText;
            if (window.lucide) lucide.createIcons();
        }
    });
})();

(function(){
    // ===== Tabs =====
    const tabs=document.querySelectorAll('.tab-btn'),panels=document.querySelectorAll('.tab-panel');
    function activateTab(name){
        tabs.forEach(t=>t.classList.toggle('active',t.dataset.tab===name));
        panels.forEach(p=>p.classList.toggle('active',p.dataset.panel===name));
        history.replaceState(null,'',location.pathname+'#'+name);
        const hidden=document.getElementById('activeTabInput');
        if(hidden)hidden.value=name;
        if(window.initLightbox)initLightbox();
    }
    tabs.forEach(t=>t.addEventListener('click',()=>activateTab(t.dataset.tab)));
    if(location.hash){const n=location.hash.slice(1);if(document.querySelector('[data-tab="'+n+'"]'))activateTab(n)}
    // If validation errors — jump to first errored tab
    @if($errors->any())
    (function(){
        const fieldToTab={
            brand_id:'basic',model:'basic',category:'basic',body_type:'basic',price:'basic',currency:'basic',price_type:'basic',taxation:'basic',
            first_registration:'engine',mileage:'engine',previous_owners:'engine',number_of_keys:'engine',business_use:'engine',fuel_type:'engine',transmission:'engine',transmission_detail:'engine',engine_capacity:'engine',power_hp:'engine',power_kw:'engine',engine_video_url:'engine',
            doors:'vehicle',seats:'vehicle',weight:'vehicle',color:'vehicle',color_code:'vehicle',upholstery:'vehicle',vin:'vehicle',is_imported:'vehicle',
            last_service:'service',last_service_mileage:'service',next_inspection:'service',service_documentation:'service',fuel_consumption:'service',co2_emission:'service',emission_class:'service',fuel_procedure:'service',service_book:'service',coc_documents:'service',vehicle_folder:'service',hu_au_report:'service',
            seller_name:'seller',seller_phone:'seller',seller_email:'seller',commission_note:'seller',reception_date:'seller',
            equipment:'equipment',technical_conditions:'condition',paint_measurements:'condition',
            damages:'damages',tire_sets:'tires',
            gallery_images:'images',damage_images:'images',pano360_image:'images',pano360ext_image:'images',
            primary_image_id:'images',delete_images:'images',image_order:'images',image_alt:'images',
        };
        const errors=@json($errors->keys());
        for(const k of errors){const base=k.split('.')[0];if(fieldToTab[base]){activateTab(fieldToTab[base]);break}}
    })();
    @endif

    // ===== Dirty check =====
    const form=document.getElementById('carFormSubmit')?.closest('form');
    const bar=document.getElementById('stickySave');

    // Intercept submit: switch to tab with first invalid field so browser can focus it
    if(form){
        const submitBtn = document.getElementById('carFormSubmit');
        if(submitBtn){
            submitBtn.addEventListener('click', function(e){
                if(form.checkValidity()) return; // all good, let it submit
                // Find first invalid field
                const invalids = form.querySelectorAll(':invalid');
                for(const inv of invalids){
                    const panel = inv.closest('.tab-panel');
                    if(panel && !panel.classList.contains('active')){
                        e.preventDefault();
                        const tabName = panel.dataset.panel;
                        if(tabName) activateTab(tabName);
                        setTimeout(()=>{ inv.focus(); form.reportValidity(); }, 80);
                        return;
                    }
                }
            });
        }
    }

    if(form&&bar){
        let dirty=false;
        const check=()=>{if(!dirty){dirty=true;bar.classList.add('show');window.addEventListener('beforeunload',beforeUnload)}};
        const beforeUnload=e=>{e.preventDefault();e.returnValue=''};
        form.addEventListener('input',check);
        form.addEventListener('change',check);
        form.addEventListener('submit',()=>{dirty=false;window.removeEventListener('beforeunload',beforeUnload)});
    }

    // ===== Counts =====
    window.refreshCounts=()=>{
        document.getElementById('cntDamages').textContent=document.querySelectorAll('.damage-item').length;
        document.getElementById('cntTires').textContent=document.querySelectorAll('.tire-set-item').length;
    };

    // ===== Damage repeater (with multi-view clickable car diagram) =====
    const VIEW_LABELS={top:'Góra',front:'Przód',rear:'Tył',left:'Lewy bok',right:'Prawy bok'};
    let currentView='top';

    function buildDamageHTML(i, posX, posY, posView){
        posView=posView||currentView;
        return `<div class="repeater-item damage-item" data-index="${i}" data-view="${posView}">
            <button type="button" class="rmv" onclick="removeDamage(this)"><i data-lucide="x"></i></button>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div class="damage-num" style="width:28px;height:28px;border-radius:50%;background:var(--blue);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0">${i+1}</div>
                <input type="text" name="damages[${i}][area]" placeholder="Nazwa obszaru (np. Maska, Prawe drzwi...)" style="flex:1;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13.5px;font-weight:600" autofocus>
            </div>
            <div class="field-row-3">
                <div class="field" style="margin:0"><label>Typ</label>
                    <select name="damages[${i}][type]">
                        <option value="damage">Aktualne uszkodzenie</option><option value="repaired">Naprawione</option><option value="accident">Po wypadku</option>
                    </select>
                </div>
                <div class="field" style="margin:0"><label>Istotność</label>
                    <select name="damages[${i}][severity]">
                        <option value="info">Info</option><option value="warning" selected>Ostrzeżenie</option><option value="critical">Krytyczne</option>
                    </select>
                </div>
                <div class="field" style="margin:0"><label>Tagi (,)</label><input type="text" name="damages[${i}][tags]" placeholder="rysa, kosmetyczne"></div>
            </div>
            <input type="hidden" name="damages[${i}][position_x]" value="${posX??''}" class="pos-x">
            <input type="hidden" name="damages[${i}][position_y]" value="${posY??''}" class="pos-y">
            <input type="hidden" name="damages[${i}][position_view]" value="${posView}" class="pos-view">
            <div class="field" style="margin:10px 0 0;display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-3)">
                <i data-lucide="map-pin" style="width:13px;height:13px"></i>
                <span>Widok: <strong class="view-name" style="color:var(--text)">${VIEW_LABELS[posView]||'Góra'}</strong></span>
                <span style="color:var(--text-4)">·</span>
                <button type="button" onclick="goToMarker(this)" style="background:none;border:none;color:var(--blue);font-size:12px;font-weight:600;cursor:pointer;padding:0">Pokaż na schemacie →</button>
            </div>
            <div class="field" style="margin:10px 0 0"><label>Opis / notatka</label><textarea name="damages[${i}][description]" rows="2" placeholder="Szczegóły uszkodzenia..."></textarea></div>
            <div class="field" style="margin:10px 0 0">
                <label>Zdjęcie uszkodzenia</label>
                <input type="file" name="damages[${i}][image]" accept="image/*" class="damage-img-input" onchange="previewDamageImg(this)">
                <div class="damage-img-preview" style="margin-top:8px"></div>
            </div>
        </div>`;
    }
    window.addDamageItem=(posX,posY,posView)=>{
        const wrap=document.getElementById('damageRepeater');
        const i=wrap.querySelectorAll('.damage-item').length;
        wrap.insertAdjacentHTML('beforeend', buildDamageHTML(i, posX, posY, posView));
        lucide.createIcons();
        renderMarkers();
        refreshCounts();
        updateViewCounts();
        applyDamageFilter();
        const el=wrap.querySelector(`.damage-item[data-index="${i}"]`);
        el?.scrollIntoView({behavior:'smooth',block:'center'});
        el?.querySelector('input[name$="[area]"]')?.focus();
    };
    window.removeDamage=(btn)=>{
        btn.closest('.damage-item').remove();
        reindexDamages();
        renderMarkers();
        refreshCounts();
        updateViewCounts();
    };
    window.goToMarker=(btn)=>{
        const card=btn.closest('.damage-item');
        const view=card.dataset.view||card.querySelector('.pos-view')?.value||'top';
        setView(view);
        document.getElementById('carDiagramWrap').scrollIntoView({behavior:'smooth',block:'center'});
    };
    function reindexDamages(){
        document.querySelectorAll('.damage-item').forEach((el,idx)=>{
            el.dataset.index=idx;
            el.querySelector('.damage-num').textContent=idx+1;
            el.querySelectorAll('[name^="damages["]').forEach(inp=>{
                inp.name=inp.name.replace(/damages\[\d+\]/,'damages['+idx+']');
            });
        });
    }
    function renderMarkers(){
        const layer=document.getElementById('diagramMarkers');
        if(!layer)return;
        layer.innerHTML='';
        document.querySelectorAll('.damage-item').forEach((el,idx)=>{
            const view=el.dataset.view||el.querySelector('.pos-view')?.value||'top';
            if(view!==currentView)return;
            const x=parseFloat(el.querySelector('.pos-x')?.value);
            const y=parseFloat(el.querySelector('.pos-y')?.value);
            if(isNaN(x)||isNaN(y))return;
            const sev=el.querySelector('select[name$="[severity]"]').value;
            const color={info:'#3b82f6',warning:'#f59e0b',critical:'#ef4444'}[sev]||'#f59e0b';
            const m=document.createElement('div');
            m.className='diagram-marker';
            m.dataset.index=idx;
            m.style.cssText=`position:absolute;left:${x}%;top:${y}%;transform:translate(-50%,-50%);width:30px;height:30px;border-radius:50%;background:${color};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12.5px;border:3px solid #fff;box-shadow:0 3px 10px rgba(0,0,0,.3);cursor:grab;pointer-events:auto;z-index:5;transition:transform .12s`;
            m.textContent=idx+1;
            m.title='Uszkodzenie #'+(idx+1);
            m.addEventListener('mouseenter',()=>m.style.transform='translate(-50%,-50%) scale(1.15)');
            m.addEventListener('mouseleave',()=>m.style.transform='translate(-50%,-50%) scale(1)');
            m.addEventListener('click',e=>{
                e.stopPropagation();
                const target=document.querySelector(`.damage-item[data-index="${idx}"]`);
                target?.scrollIntoView({behavior:'smooth',block:'center'});
                target?.classList.add('highlight');
                setTimeout(()=>target?.classList.remove('highlight'),1200);
            });
            m.addEventListener('mousedown',startDrag);
            layer.appendChild(m);
        });
    }
    function startDrag(e){
        e.preventDefault();e.stopPropagation();
        const marker=e.currentTarget;
        const idx=+marker.dataset.index;
        const wrap=document.getElementById('carDiagramWrap');
        const diag=wrap.getBoundingClientRect();
        const move=ev=>{
            const px=((ev.clientX-diag.left-24)/(diag.width-48))*100;
            const py=((ev.clientY-diag.top-24)/(diag.height-48))*100;
            const x=Math.max(0,Math.min(100,px));
            const y=Math.max(0,Math.min(100,py));
            const el=document.querySelector(`.damage-item[data-index="${idx}"]`);
            el.querySelector('.pos-x').value=x.toFixed(2);
            el.querySelector('.pos-y').value=y.toFixed(2);
            marker.style.left=x+'%';marker.style.top=y+'%';
        };
        const up=()=>{document.removeEventListener('mousemove',move);document.removeEventListener('mouseup',up)};
        document.addEventListener('mousemove',move);
        document.addEventListener('mouseup',up);
    }
    function setView(view){
        currentView=view;
        document.querySelectorAll('.view-btn').forEach(b=>b.classList.toggle('active',b.dataset.view===view));
        document.querySelectorAll('.svg-view').forEach(v=>v.classList.toggle('active',v.dataset.view===view));
        document.getElementById('viewLabel').textContent='WIDOK: '+VIEW_LABELS[view].toUpperCase();
        renderMarkers();
        applyDamageFilter();
    }
    function applyDamageFilter(){
        document.querySelectorAll('.damage-item').forEach(el=>{
            const view=el.dataset.view||el.querySelector('.pos-view')?.value||'top';
            el.classList.toggle('dim',view!==currentView);
        });
    }
    function updateViewCounts(){
        const counts={top:0,front:0,rear:0,left:0,right:0};
        document.querySelectorAll('.damage-item').forEach(el=>{
            const v=el.dataset.view||el.querySelector('.pos-view')?.value||'top';
            if(counts[v]!==undefined)counts[v]++;
        });
        Object.entries(counts).forEach(([v,c])=>{
            const badge=document.querySelector('[data-view-count="'+v+'"]');
            if(badge){badge.textContent=c;badge.toggleAttribute('data-has',c>0)}
        });
    }
    document.querySelectorAll('.view-btn').forEach(b=>b.addEventListener('click',()=>setView(b.dataset.view)));

    const diagram=document.getElementById('carDiagramWrap');
    diagram?.addEventListener('click',e=>{
        if(e.target.closest('.diagram-marker'))return;
        const rect=diagram.getBoundingClientRect();
        const px=((e.clientX-rect.left-24)/(rect.width-48))*100;
        const py=((e.clientY-rect.top-24)/(rect.height-48))*100;
        const x=Math.max(0,Math.min(100,px)).toFixed(2);
        const y=Math.max(0,Math.min(100,py)).toFixed(2);
        addDamageItem(x,y,currentView);
    });
    document.addEventListener('change',e=>{
        if(e.target.matches('select[name$="[severity]"]'))renderMarkers();
    });
    window.previewDamageImg=(input)=>{
        const f=input.files?.[0];const box=input.nextElementSibling;
        if(!f){box.innerHTML='';return}
        const url=URL.createObjectURL(f);
        box.innerHTML=`<img src="${url}" alt="" style="max-width:200px;max-height:120px;border-radius:8px;border:1px solid var(--border-l)">`;
    };
    renderMarkers();
    updateViewCounts();
    applyDamageFilter();

    // ===== Dynamic body-type image for damage diagram =====
    const btMap = {sedan:'sedan',suv:'suv','coupé':'coupe',coupe:'coupe',bus:'van',van:'van',kombi:'kombi',hatchback:'hatchback',kabriolet:'sedan',cabriolet:'sedan',pickup:'suv'};
    const btSelect = document.getElementById('bodyTypeSelect');
    const dtImg = document.getElementById('damageTopImg');
    if (btSelect && dtImg) {
        btSelect.addEventListener('change', () => {
            const key = (btSelect.value || 'sedan').toLowerCase();
            const img = btMap[key] || 'sedan';
            dtImg.src = '/img/body-types-top/' + img + '.png';
        });
    }

    // ===== Engine video URL/File toggle + preview =====
    const vidUrl=document.getElementById('engineVideoUrl');
    const vidFile=document.getElementById('engineVideoFile');
    const vidUrlPreview=document.getElementById('vidUrlPreview');
    const vidLocalPreview=document.getElementById('videoLocalPreview');
    const vidTabUrl=document.getElementById('vidTabUrl');
    const vidTabFile=document.getElementById('vidTabFile');
    const vidUrlPanel=document.getElementById('vidUrlPanel');
    const vidFilePanel=document.getElementById('vidFilePanel');
    function setVidTab(tab){
        const url=tab==='url';
        vidUrlPanel.style.display=url?'block':'none';
        vidFilePanel.style.display=url?'none':'block';
        vidTabUrl.style.background=url?'#fff':'transparent';
        vidTabFile.style.background=url?'transparent':'#fff';
    }
    vidTabUrl?.addEventListener('click',()=>setVidTab('url'));
    vidTabFile?.addEventListener('click',()=>setVidTab('file'));

    function previewVideoUrl(url){
        if(!url){vidUrlPreview.innerHTML='';return}
        let yt=url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/))([A-Za-z0-9_-]{11})/);
        let vim=url.match(/vimeo\.com\/(\d+)/);
        if(yt){
            vidUrlPreview.innerHTML=`<div style="position:relative;padding-bottom:56.25%;border-radius:10px;overflow:hidden;background:#000"><iframe src="https://www.youtube.com/embed/${yt[1]}" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen></iframe></div>`;
        }else if(vim){
            vidUrlPreview.innerHTML=`<div style="position:relative;padding-bottom:56.25%;border-radius:10px;overflow:hidden;background:#000"><iframe src="https://player.vimeo.com/video/${vim[1]}" style="position:absolute;inset:0;width:100%;height:100%;border:0" allowfullscreen></iframe></div>`;
        }else{
            vidUrlPreview.innerHTML=`<div style="background:#fafafb;border:1px solid var(--border-l);border-radius:8px;padding:10px;font-size:12px;color:var(--text-3)"><i data-lucide="info" style="width:13px;height:13px;vertical-align:-2px"></i> Link zapisany — podgląd dostępny tylko dla YouTube i Vimeo.</div>`;
            if(window.lucide)lucide.createIcons();
        }
    }
    vidUrl?.addEventListener('input',e=>previewVideoUrl(e.target.value.trim()));
    if(vidUrl?.value)previewVideoUrl(vidUrl.value.trim());

    vidFile?.addEventListener('change',e=>{
        const f=e.target.files?.[0];
        if(!f){vidLocalPreview.innerHTML='';return}
        const url=URL.createObjectURL(f);
        const sizeMB=(f.size/(1024*1024)).toFixed(1);
        vidLocalPreview.innerHTML=`<div style="background:#fafafb;border:1px solid var(--border-l);border-radius:10px;padding:10px"><div style="font-size:12px;color:var(--text-3);margin-bottom:6px">Podgląd: <strong style="color:var(--text)">${f.name}</strong> · ${sizeMB} MB</div><video src="${url}" controls preload="metadata" style="max-width:100%;max-height:320px;border-radius:8px;background:#000"></video></div>`;
    });

    @if($car?->engine_video_path && !$car?->engine_video_url)
    setVidTab('file');
    @endif

    // ===== Tire set repeater =====
    window.addTireSet=()=>{
        const wrap=document.getElementById('tireSetRepeater');
        const i=wrap.querySelectorAll('.tire-set-item').length;
        const positions=[['front_left','Przód L'],['front_right','Przód P'],['rear_left','Tył L'],['rear_right','Tył P']];
        const tireRows=positions.map(([pos,lbl])=>`<div style="display:grid;grid-template-columns:90px 1fr 2fr;gap:8px;margin-bottom:6px;align-items:center;font-size:12.5px">
            <strong>${lbl}</strong>
            <input type="hidden" name="tire_sets[${i}][tires][${pos}][position]" value="${pos}">
            <input type="text" name="tire_sets[${i}][tires][${pos}][tread_depth]" placeholder="Bieżnik (mm)">
            <input type="text" name="tire_sets[${i}][tires][${pos}][condition]" placeholder="stan: dobry, pęknięcie">
        </div>`).join('');
        const html=`<div class="repeater-item tire-set-item">
            <button type="button" class="rmv" onclick="this.closest('.tire-set-item').remove();refreshCounts()"><i data-lucide="x"></i></button>
            <div class="field-row-3">
                <div class="field" style="margin:0"><label>Numer zestawu</label><input type="number" name="tire_sets[${i}][set_number]" value="${i+1}" min="1"></div>
                <div class="field" style="margin:0"><label>Typ opon</label><input type="text" name="tire_sets[${i}][tire_type]" placeholder="Letnie, Zimowe, Całoroczne"></div>
                <div class="field" style="margin:0"><label>Felgi</label><input type="text" name="tire_sets[${i}][rim]" placeholder="Aluminium 18''"></div>
            </div>
            <label class="inline-label" style="margin:8px 0"><input type="hidden" name="tire_sets[${i}][is_mounted]" value="0"><input type="checkbox" name="tire_sets[${i}][is_mounted]" value="1"> Obecnie zamontowane</label>
            <div class="field" style="margin:6px 0"><label>Uwagi</label><input type="text" name="tire_sets[${i}][notes]"></div>
            <div style="margin-top:10px"><div style="font-size:11px;font-weight:700;color:var(--text-3);text-transform:uppercase;letter-spacing:.3px;margin-bottom:6px">Koła w zestawie</div>${tireRows}</div>
        </div>`;
        wrap.insertAdjacentHTML('beforeend',html);
        lucide.createIcons();refreshCounts();
    };

    // ===== Paint repeater =====

    // ===== Images delete toggle =====
    window.toggleDelete=(btn,id,type)=>{
        const tile=btn.closest('.img-tile');
        const cb=tile.querySelector('input[type=checkbox]');
        cb.checked=!cb.checked;
        tile.classList.toggle('to-delete',cb.checked);
    };

    // ===== File drop zones =====
    document.querySelectorAll('.file-drop').forEach(drop=>{
        const input=drop.querySelector('input');
        drop.addEventListener('dragover',e=>{e.preventDefault();drop.classList.add('over')});
        drop.addEventListener('dragleave',()=>drop.classList.remove('over'));
        drop.addEventListener('drop',e=>{
            e.preventDefault();drop.classList.remove('over');
            input.files=e.dataTransfer.files;
            updateDropLabel(drop, input);
            form.dispatchEvent(new Event('change'));
        });
        input.addEventListener('change',()=>updateDropLabel(drop, input));
    });
    function updateDropLabel(drop, input){
        const lbl = drop.querySelector('div');
        if(!input.files.length){
            lbl.textContent = drop.dataset.originalText || 'Kliknij lub przeciągnij pliki';
            const pg = drop.parentElement.querySelector('.file-preview-grid');
            if(pg) pg.remove();
            return;
        }
        if(!drop.dataset.originalText) drop.dataset.originalText = lbl.textContent;
        const n = input.files.length;
        lbl.style.color = '';

        const uploadType = drop.dataset.uploadType;
        const carId = @json($car?->id ?? null);

        // AJAX upload for existing cars
        if(carId && (uploadType === 'gallery' || uploadType === 'damage')){
            const files = Array.from(input.files).filter(f => f.type.startsWith('image/'));
            lbl.textContent = `Wgrywanie ${files.length} zdjęć...`;
            // Clear the file input so form doesn't re-send them
            input.value = '';
            ajaxUploadFiles(files, uploadType, carId);
            return;
        }

        // Fallback: show previews for create page (standard form upload)
        lbl.textContent = `${n} plik(ów) gotowych do wgrania`;
        let pg = drop.parentElement.querySelector('.file-preview-grid');
        if(pg) pg.remove();
        pg = document.createElement('div');
        pg.className = 'file-preview-grid';
        drop.parentElement.insertBefore(pg, drop.nextSibling);

        Array.from(input.files).forEach((file, i) => {
            if(!file.type.startsWith('image/')) return;
            const item = document.createElement('div');
            item.className = 'file-preview-item';
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

    // ===== AJAX sequential file upload =====
    async function ajaxUploadFiles(files, type, carId){
        const prefix = type === 'gallery' ? 'gallery' : 'damage';
        const progressWrap = document.getElementById(prefix+'UploadProgress');
        const progressBar = document.getElementById(prefix+'ProgressBar');
        const progressText = document.getElementById(prefix+'ProgressText');
        const grid = document.getElementById(prefix+'UploadedGrid');
        const drop = document.getElementById(prefix+'Drop');
        const lbl = drop?.querySelector('div');

        progressWrap.style.display = '';
        let done = 0;
        const total = files.length;
        progressText.textContent = `0/${total}`;
        progressBar.style.width = '0%';

        for(const file of files){
            const fd = new FormData();
            fd.append('image', file);
            fd.append('type', type);
            fd.append('_token', csrf);

            try {
                const res = await fetch(`/admin/cars/${carId}/upload-image`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd,
                });
                const data = await res.json().catch(()=>({}));
                done++;
                progressBar.style.width = Math.round((done/total)*100)+'%';
                progressText.textContent = `${done}/${total}`;

                if(data.success && data.image){
                    const tile = document.createElement('div');
                    tile.className = 'file-preview-item';
                    tile.style.border = '2px solid #10b981';
                    tile.innerHTML = `<img src="${data.image.url}" alt="${data.image.alt||''}" style="width:100%;aspect-ratio:4/3;object-fit:cover;display:block"><div class="fp-name" style="color:#10b981">✓ ${file.name}</div>`;
                    grid.appendChild(tile);
                } else {
                    const tile = document.createElement('div');
                    tile.className = 'file-preview-item';
                    tile.style.border = '2px solid #ef4444';
                    tile.innerHTML = `<div style="padding:12px;font-size:11px;color:#ef4444">✗ ${file.name}<br>${data.message||'Błąd'}</div>`;
                    grid.appendChild(tile);
                }
            } catch(err){
                done++;
                progressBar.style.width = Math.round((done/total)*100)+'%';
                progressText.textContent = `${done}/${total}`;
                const tile = document.createElement('div');
                tile.className = 'file-preview-item';
                tile.style.border = '2px solid #ef4444';
                tile.innerHTML = `<div style="padding:12px;font-size:11px;color:#ef4444">✗ ${file.name}<br>Błąd sieci</div>`;
                grid.appendChild(tile);
            }
        }

        if(lbl) lbl.textContent = `✓ Wgrano ${done}/${total} zdjęć`;
        progressBar.style.background = '#10b981';
        if(window.toast) toast(`Wgrano ${done} zdjęć ${type==='gallery'?'galerii':'uszkodzeń'}.`, 'success');
    }

    // ===== SEO analyzer (Yoast-style) =====
    const seoFallbackTitle=@json($car?->seo_title ?? '');
    const seoFallbackDesc=@json($car?->seo_description ?? '');
    const seoSlug=@json($car?->slug ?? '');
    const seoCarTitle=@json($car?->title ?? '');
    const seoBrand=@json($car?->brand?->name ?? '');
    const seoModel=@json($car?->model ?? '');

    const elFocus=document.getElementById('seoFocus');
    const elTitle=document.getElementById('seoTitle');
    const elDesc=document.getElementById('seoDesc');
    const elTitleLen=document.getElementById('seoTitleLen');
    const elDescLen=document.getElementById('seoDescLen');
    const elTitleBar=document.getElementById('seoTitleBarFill');
    const elDescBar=document.getElementById('seoDescBarFill');
    const elSerpTitle=document.getElementById('serpTitle');
    const elSerpDesc=document.getElementById('serpDesc');
    const elOgTitle=document.getElementById('ogTitle');
    const elOgDesc=document.getElementById('ogDesc');
    const elChecks=document.getElementById('seoChecks');
    const elScorePct=document.getElementById('seoScorePct');
    const elScoreCircle=document.getElementById('seoScoreCircle');
    const elScoreLabel=document.getElementById('seoScoreLabel');
    const elScoreBadge=document.getElementById('seoScoreBadge');

    function colorFor(len,min,max){
        if(!len)return {c:'#e5e5e7',w:0};
        if(len<min)return {c:'#f59e0b',w:(len/min)*100};
        if(len<=max)return {c:'#10b981',w:100};
        return {c:'#ef4444',w:100};
    }
    function renderBar(el,info){el.style.background=info.c;el.style.width=Math.min(100,info.w)+'%'}
    function updateCounters(){
        const t=(elTitle.value||seoFallbackTitle);
        const d=(elDesc.value||seoFallbackDesc);
        const ti=colorFor(t.length,50,60);
        const di=colorFor(d.length,120,160);
        elTitleLen.textContent=t.length+' / 60'; elTitleLen.style.color=ti.c;
        elDescLen.textContent=d.length+' / 160'; elDescLen.style.color=di.c;
        renderBar(elTitleBar,{c:ti.c,w:Math.min(100,(t.length/60)*100)});
        renderBar(elDescBar,{c:di.c,w:Math.min(100,(d.length/160)*100)});
        elSerpTitle.textContent=t||'Tytuł oferty';
        elSerpDesc.textContent=d||'Tutaj pojawi się opis oferty.';
        elOgTitle.textContent=t||'Tytuł';
        elOgDesc.textContent=d||'';
    }

    function analyzeSeo(){
        const t=(elTitle.value||seoFallbackTitle).toLowerCase();
        const d=(elDesc.value||seoFallbackDesc).toLowerCase();
        const kw=(elFocus.value||'').trim().toLowerCase();
        const slug=seoSlug.toLowerCase();
        const checks=[];

        // Title length
        const tLen=(elTitle.value||seoFallbackTitle).length;
        if(tLen===0)checks.push({ok:false,warn:true,t:'Ustaw meta tytuł (min. 30 znaków).'});
        else if(tLen<30)checks.push({ok:false,t:`Meta tytuł za krótki: ${tLen} znaków (min. 30).`});
        else if(tLen<=60)checks.push({ok:true,t:`Długość meta tytułu idealna (${tLen} znaków).`});
        else checks.push({ok:false,warn:true,t:`Meta tytuł może zostać obcięty w Google: ${tLen}/60 znaków.`});

        // Desc length
        const dLen=(elDesc.value||seoFallbackDesc).length;
        if(dLen===0)checks.push({ok:false,warn:true,t:'Ustaw meta opis (min. 70 znaków).'});
        else if(dLen<70)checks.push({ok:false,t:`Meta opis za krótki: ${dLen} znaków (min. 70).`});
        else if(dLen<=160)checks.push({ok:true,t:`Długość meta opisu idealna (${dLen} znaków).`});
        else checks.push({ok:false,warn:true,t:`Meta opis zostanie obcięty: ${dLen}/160 znaków.`});

        // Focus keyword checks
        if(!kw){checks.push({ok:false,warn:true,t:'Dodaj focus keyword aby sprawdzić jego obecność.'});}
        else{
            checks.push(t.includes(kw)?{ok:true,t:'Focus keyword występuje w meta tytule.'}:{ok:false,t:'Focus keyword nie występuje w meta tytule.'});
            checks.push(d.includes(kw)?{ok:true,t:'Focus keyword występuje w meta opisie.'}:{ok:false,t:'Focus keyword nie występuje w meta opisie.'});
            const kwSlug=kw.replace(/\s+/g,'-');
            checks.push(slug.includes(kwSlug)?{ok:true,t:'Focus keyword występuje w URL (slug).'}:{ok:false,warn:true,t:'Focus keyword nie występuje w slug (może być OK jeśli różni się od marki/modelu).'});
        }

        // Duplicate check
        if(seoBrand && seoModel){
            const containsCar=t.includes(seoBrand.toLowerCase())&&t.includes(seoModel.toLowerCase());
            checks.push(containsCar?{ok:true,t:'Meta tytuł zawiera markę i model auta.'}:{ok:false,warn:true,t:'Rozważ dodanie marki i modelu do meta tytułu.'});
        }

        // Render + score
        const okCount=checks.filter(c=>c.ok).length;
        const total=checks.length;
        const score=total?Math.round((okCount/total)*100):0;
        const color=score>=80?'#10b981':score>=50?'#f59e0b':'#ef4444';
        const label=score>=80?'Świetnie':score>=50?'Do poprawy':'Słabo';
        elScorePct.textContent=score;
        elScoreCircle.style.background=`conic-gradient(${color} ${score*3.6}deg, #e5e5e7 0deg)`;
        elScoreLabel.textContent=label;
        elScoreLabel.style.color=color;
        elScoreBadge.textContent=score;
        elScoreBadge.style.background=color+'22';
        elScoreBadge.style.color=color;

        elChecks.innerHTML=checks.map(c=>{
            const icon=c.ok?'check-circle':(c.warn?'alert-circle':'x-circle');
            const col=c.ok?'#10b981':(c.warn?'#f59e0b':'#ef4444');
            const bg=c.ok?'#ecfdf5':(c.warn?'#fffbeb':'#fef2f2');
            return `<li style="display:flex;gap:8px;padding:7px 10px;background:${bg};border-radius:7px;font-size:12.5px;line-height:1.45;color:var(--text)"><i data-lucide="${icon}" style="width:14px;height:14px;flex-shrink:0;color:${col};margin-top:1px"></i> ${c.t}</li>`;
        }).join('');
        if(window.lucide)lucide.createIcons();
    }

    [elFocus,elTitle,elDesc].forEach(el=>el?.addEventListener('input',()=>{updateCounters();analyzeSeo()}));
    updateCounters();analyzeSeo();

    // SERP mode switch (desktop/mobile)
    const serp=document.getElementById('serpPreview');
    document.getElementById('serpModeDesktop')?.addEventListener('click',()=>{serp.style.maxWidth='';serp.style.fontSize=''});
    document.getElementById('serpModeMobile')?.addEventListener('click',()=>{serp.style.maxWidth='360px';serp.style.fontSize='13px'});
})();
</script>
@endpush
