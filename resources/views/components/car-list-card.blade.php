@props(['car'])
{{--
    Single source of truth for the horizontal vehicle list card. Used both
    on the catalog index page and on the homepage "Wyróżnione pojazdy"
    section. Every visual difference between the two pages used to live in
    77 lines of duplicated markup — now they share this one file.

    The outer wrapper is intentionally a <div>, NOT an <a>. The CertiCheck
    pill nested inside is itself an <a download> and HTML5 forbids nested
    anchors — browsers auto-close the outer <a> before the inner one, and
    the card visually breaks apart. The whole-card click target is the
    separately-stacked .lcard-link overlay. The favorite button and the
    "Zobacz szczegóły" CTA both sit above that overlay via z-index so
    they receive their own clicks independently.

    All values are pulled from the live Car model — no hardcoded counts,
    no fake data.
--}}
<div class="lcard">
    <a href="{{ route('catalog.show', $car) }}" class="lcard-link" aria-label="{{ $car->title }}"></a>

    {{-- Image area: hero photo + featured badge + favorite + photo count --}}
    <div class="lcard-img">
        @if($car->primaryImage)
            <img src="{{ $car->primaryImage->url }}" alt="{{ $car->primaryImage->alt }}" loading="lazy"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="lcard-img-placeholder" style="display:none">
        @else
            <div class="lcard-img-placeholder">
        @endif
                <x-icon name="car" size="40"/>
            </div>

        @if($car->is_featured)
            <div class="lcard-badge-top">Wyróżnione</div>
        @endif

        @php $imgCount = $car->images->count(); @endphp
        @if($imgCount > 1)
            <div class="lcard-photo-count">
                <x-icon name="image" size="14"/>
                {{ $imgCount }}
            </div>
        @endif

        <button type="button" class="lcard-fav" data-id="{{ $car->id }}"
                aria-label="Dodaj do ulubionych"
                onclick="toggleFav(event,{{ $car->id }})">
            <x-icon name="heart" size="16"/>
        </button>
    </div>

    {{-- Content area: vertical column so the CertiCheck footer can anchor to
         the bottom under the main info+actions row. --}}
    <div class="lcard-content">
        <div class="lcard-main">
            {{-- Center column: title + single inline metadata row --}}
            <div class="lcard-info">
                <h3 class="lcard-title">{{ $car->title }}</h3>

                <div class="lcard-specs">
                    @if($car->first_registration)
                        <span class="lcard-spec">
                            <x-icon name="calendar" size="14"/>
                            {{ $car->first_registration }}
                        </span>
                    @endif
                    @if($car->mileage)
                        <span class="lcard-spec">
                            <x-icon name="gauge" size="14"/>
                            {{ number_format((float) $car->mileage, 0, '.', ' ') }} km
                        </span>
                    @endif
                    @if($car->fuel_type)
                        <span class="lcard-spec">
                            <x-icon name="fuel" size="14"/>
                            {{ \App\Helpers\CarLabels::fuelType($car->fuel_type) ?? $car->fuel_type }}
                        </span>
                    @endif
                    @if($car->power_hp)
                        <span class="lcard-spec">
                            <x-icon name="zap" size="14"/>
                            {{ $car->power_hp }} KM
                        </span>
                    @endif
                    @if($car->transmission)
                        <span class="lcard-spec">
                            <x-icon name="settings" size="14"/>
                            {{ \App\Helpers\CarLabels::transmission($car->transmission) ?? $car->transmission }}
                        </span>
                    @endif
                </div>

                @if($car->has_certicheck)
                    <div class="lcard-certicheck">
                        <x-certicheck-cta :slug="$car->slug" :ready="$car->brochureIsReady()" size="sm"/>
                    </div>
                @endif
            </div>

            {{-- Right column: price + outline CTA. lcard-actions z-index sits
                 above the .lcard-link overlay so the button is directly
                 clickable; both lead to the same route as the card overlay
                 but the explicit button keeps the action discoverable. --}}
            <div class="lcard-actions">
                <div class="lcard-price">{{ $car->formatted_price }}</div>
                <a href="{{ route('catalog.show', $car) }}" class="lcard-cta">
                    Zobacz szczegóły
                    <x-icon name="arrow-right" size="14"/>
                </a>
            </div>
        </div>
    </div>
</div>
