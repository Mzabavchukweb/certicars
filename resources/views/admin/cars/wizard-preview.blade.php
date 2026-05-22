{{-- Preview card for wizard right sidebar --}}
<div class="wz-preview-card" id="wzPreviewCard">
    {{-- Car image --}}
    @if($car && $car->primaryImage)
    <img class="wz-preview-img" id="wzPreviewImg" src="{{ $car->primaryImage->url }}" alt="">
    @else
    <div class="wz-preview-img" id="wzPreviewImgPlaceholder" style="display:flex;align-items:center;justify-content:center;color:#9a9a9e">
        <i data-lucide="image" style="width:32px;height:32px"></i>
    </div>
    @endif

    <div class="wz-preview-body">
        <div class="wz-preview-title" id="wzPreviewTitle">{{ $car?->title ?? 'Tytuł ogłoszenia' }}</div>

        <div class="wz-preview-specs">
            <span class="wz-preview-spec" id="wzPrevEngine"><i data-lucide="zap"></i> <span>—</span></span>
            <span class="wz-preview-spec" id="wzPrevFuel"><i data-lucide="fuel"></i> <span>—</span></span>
            <span class="wz-preview-spec" id="wzPrevTransmission"><i data-lucide="settings-2"></i> <span>—</span></span>
        </div>

        <div class="wz-preview-specs">
            <span class="wz-preview-spec" id="wzPrevYear"><i data-lucide="calendar"></i> <span>—</span></span>
            <span class="wz-preview-spec" id="wzPrevMileage"><i data-lucide="gauge"></i> <span>—</span></span>
        </div>

        <div class="wz-preview-price" id="wzPreviewPrice">
            <span id="wzPrevPriceVal">—</span>
            <span class="currency" id="wzPrevCurrency">PLN</span>
        </div>

        <div class="wz-preview-badges">
            <span class="wz-preview-badge badge-certicheck" id="wzBadgeCerticheck"><i data-lucide="shield-check"></i> CertiCheck</span>
            <span class="wz-preview-badge badge-available" id="wzBadgeAvailable"><i data-lucide="zap"></i> Dostępny od ręki</span>
            <span class="wz-preview-badge badge-delivery" id="wzBadgeDelivery"><i data-lucide="truck"></i> Dostawa pod dom</span>
            <span class="wz-preview-badge badge-gethelp" id="wzBadgeGethelp"><i data-lucide="shield"></i> GetHelp</span>
        </div>
    </div>

    {{-- Step completion indicators --}}
    <div class="wz-completion">
        <div class="wz-completion-title">Postęp wypełniania</div>
        <div class="wz-completion-item active" data-goto-step="1"><div class="ci-dot"></div> 1. Dane podstawowe</div>
        <div class="wz-completion-item" data-goto-step="2"><div class="ci-dot"></div> 2. Zdjęcia i media</div>
        <div class="wz-completion-item" data-goto-step="3"><div class="ci-dot"></div> 3. Historia pojazdu</div>
        <div class="wz-completion-item" data-goto-step="4"><div class="ci-dot"></div> 4. Serwisowanie</div>
        <div class="wz-completion-item" data-goto-step="5"><div class="ci-dot"></div> 5. Dokumenty</div>
        <div class="wz-completion-item" data-goto-step="6"><div class="ci-dot"></div> 6. Wyposażenie</div>
        <div class="wz-completion-item" data-goto-step="7"><div class="ci-dot"></div> 7. Stan wizualny</div>
        <div class="wz-completion-item" data-goto-step="8"><div class="ci-dot"></div> 8. Stan techniczny</div>
        <div class="wz-completion-item" data-goto-step="9"><div class="ci-dot"></div> 9. Pomiary lakieru</div>
        <div class="wz-completion-item" data-goto-step="10"><div class="ci-dot"></div> 10. Opony i bieżnik</div>
        <div class="wz-completion-item" data-goto-step="11"><div class="ci-dot"></div> 11. Podgląd i publikacja</div>
    </div>
</div>

{{-- Publication controls --}}
<div style="margin-top:16px">
    <div class="wz-section" style="padding:18px">
        <div class="wz-field" style="margin-bottom:14px">
            <label>Status</label>
            <select name="status" form="wizardForm">
                <option value="draft" {{ old('status',$car?->status ?? 'draft')=='draft'?'selected':'' }}>Szkic</option>
                <option value="active" {{ old('status',$car?->status)=='active'?'selected':'' }}>Aktywne</option>
                <option value="reserved" {{ old('status',$car?->status)=='reserved'?'selected':'' }}>Zarezerwowane</option>
                <option value="sold" {{ old('status',$car?->status)=='sold'?'selected':'' }}>Sprzedane</option>
            </select>
        </div>
        <div style="margin-bottom:12px">
            <label class="wz-inline-label">
                <input type="hidden" name="is_featured" value="0" form="wizardForm">
                <input type="checkbox" name="is_featured" value="1" form="wizardForm" {{ old('is_featured',$car?->is_featured)?'checked':'' }}> Wyróżnione
            </label>
        </div>
        <div style="margin-bottom:14px">
            <label class="wz-inline-label">
                <input type="hidden" name="is_sold" value="0" form="wizardForm">
                <input type="checkbox" name="is_sold" value="1" form="wizardForm" {{ old('is_sold',$car?->is_sold)?'checked':'' }}> Oznacz jako sprzedane
            </label>
        </div>
        <button type="submit" form="wizardForm" class="btn btn-blue" id="wzFormSubmit" style="width:100%;justify-content:center;padding:13px"><i data-lucide="save"></i> {{ $car ? 'Zapisz zmiany' : 'Zapisz' }}</button>
        <a href="{{ route('admin.cars.index') }}" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:8px">Anuluj</a>
    </div>
</div>
