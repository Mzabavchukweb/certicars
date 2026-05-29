@extends('layouts.public')
@section('title','Obserwowane')
@section('description','Twoje obserwowane samochody na CertiCars.')
@section('styles')
/* Favorites page */
.fav-header{background:#fff;border-bottom:1px solid var(--border-l);padding:28px 0}
.fav-header-in{max-width:1200px;margin:0 auto;padding:0 24px}
.fav-breadcrumb{display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-3);margin-bottom:12px}
.fav-breadcrumb a{color:var(--text-3);text-decoration:none}
.fav-breadcrumb a:hover{color:var(--blue)}
.fav-breadcrumb svg{width:10px;height:10px;stroke:var(--text-4);fill:none;stroke-width:2}
.fav-title-row{display:flex;align-items:center;gap:14px}
.fav-title-row h1{font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.4px}
.fav-title-row .fav-count{font-size:13px;font-weight:600;background:var(--orange);color:#fff;padding:3px 10px;border-radius:50px}
.fav-wrap{max-width:1200px;margin:0 auto;padding:32px 24px 80px}
/* Cards */
.fav-listings{background:#fff;border:1px solid var(--border-l);border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04)}
/* Favorite-list cards: hover effects intentionally removed. The card stays
   static; clickability is signalled by the cursor on the link inside. */
.fav-lcard{display:flex;text-decoration:none;border-bottom:1px solid var(--border-l);position:relative;overflow:hidden}
.fav-lcard:last-child{border-bottom:none}
.fav-lcard-img{width:260px;min-width:260px;height:190px;position:relative;overflow:hidden;flex-shrink:0;background:var(--bg)}
.fav-lcard-img img{width:100%;height:100%;object-fit:cover}
.fav-lcard-img-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.fav-lcard-img-placeholder svg{width:48px;height:48px;stroke:var(--text-4);stroke-width:1.2;fill:none}
/* CertiCheck pill visuals owned by the shared component. The wrap below
   positions the pill over the card image consistently with other variants. */
.fav-lcard-certi-wrap{position:absolute;bottom:8px;left:8px;z-index:2}
.fav-remove{position:absolute;top:8px;right:8px;width:32px;height:32px;background:rgba(255,255,255,.9);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;box-shadow:0 1px 4px rgba(0,0,0,.15)}
.fav-remove:hover{background:#fff;transform:scale(1.1)}
.fav-remove svg{width:14px;height:14px;stroke:var(--orange);fill:var(--orange);stroke-width:2}
.fav-lcard-content{flex:1;padding:18px 22px;display:flex;gap:16px;min-width:0}
.fav-lcard-info{flex:1;min-width:0;display:flex;flex-direction:column}
.fav-lcard-title{font-size:17px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.fav-lcard-sub{font-size:12px;color:var(--text-3);margin-bottom:14px}
.fav-lcard-specs{display:flex;flex-wrap:wrap;gap:4px 0;margin-bottom:14px}
.fav-lcard-spec{display:flex;align-items:center;gap:5px;font-size:13px;color:var(--text-2);font-weight:500;padding-right:14px;margin-right:10px;border-right:1px solid var(--border-l)}
.fav-lcard-spec:last-child{border-right:none;padding-right:0;margin-right:0}
.fav-lcard-spec svg{width:13px;height:13px;stroke:var(--text-3);fill:none;stroke-width:2;flex-shrink:0}
.fav-lcard-meta{margin-top:auto;padding-top:12px;border-top:1px solid var(--border-l);font-size:11px;color:var(--text-3)}
.fav-lcard-price-col{display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between;min-width:160px;padding-top:2px}
.fav-lcard-price{font-size:24px;font-weight:900;color:#000;letter-spacing:-.5px;line-height:1;white-space:nowrap}
.fav-lcard-price-lbl{font-size:11px;color:var(--text-3);margin-top:3px}
.fav-lcard-btn{display:inline-flex;align-items:center;gap:6px;background:var(--blue);color:#fff;font-size:12px;font-weight:700;padding:10px 20px;border-radius:8px;text-decoration:none;transition:all .18s;white-space:nowrap}
.fav-lcard-btn:hover{background:var(--blue-h);box-shadow:0 4px 14px rgba(0,102,255,.3);transform:translateY(-1px)}
.fav-lcard-btn svg{width:13px;height:13px;stroke:#fff;fill:none;stroke-width:2.4}
/* Empty state */
.fav-empty{background:#fff;border:1.5px dashed var(--border);border-radius:16px;padding:80px 32px;text-align:center}
.fav-empty-icon{width:80px;height:80px;border-radius:50%;background:var(--bg);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;border:1.5px dashed var(--border)}
.fav-empty-icon svg{width:40px;height:40px;stroke:var(--text-4);stroke-width:1.2;fill:none}
.fav-empty h2{font-size:20px;font-weight:800;color:var(--text);margin-bottom:10px}
.fav-empty p{font-size:14px;color:var(--text-3);margin-bottom:28px;max-width:380px;margin-left:auto;margin-right:auto;line-height:1.7}
@media(max-width:768px){
    .fav-lcard{flex-direction:column}
    .fav-lcard-img{width:100%;min-width:0;height:200px}
    .fav-lcard-content{flex-direction:column}
    .fav-lcard-price-col{flex-direction:row;align-items:center;min-width:0;width:100%}
}
@endsection

@section('content')
<div class="fav-header">
    <div class="fav-header-in">
        <nav class="fav-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Strona główna</a>
            <x-icon name="chevron-right" size="14"/>
            <span>Obserwowane</span>
        </nav>
        <div class="fav-title-row">
            <h1>Obserwowane pojazdy</h1>
            <span class="fav-count" id="favCountBadge">{{ $cars->count() }}</span>
        </div>
    </div>
</div>

<div class="fav-wrap">
    @if($cars->count())
    <div class="fav-listings" id="favListings">
        @foreach($cars as $car)
        <a href="{{ route('catalog.show', $car) }}" class="fav-lcard" data-car-id="{{ $car->id }}">
            <div class="fav-lcard-img">
                @if($car->primaryImage)
                    <img src="{{ $car->primaryImage->url }}" alt="{{ $car->title }}" loading="lazy">
                @else
                    <div class="fav-lcard-img-placeholder">
                        <x-icon name="car" size="32"/>
                    </div>
                @endif
                @if($car->has_certicheck)
                <div class="fav-lcard-certi-wrap">
                    <x-certicheck-cta :slug="$car->slug" size="sm"/>
                </div>
                @endif
                <button class="fav-remove" data-id="{{ $car->id }}" aria-label="Usuń z obserwowanych" onclick="removeFav(event, {{ $car->id }})">
                    <x-icon name="heart" size="16"/>
                </button>
            </div>
            <div class="fav-lcard-content">
                <div class="fav-lcard-info">
                    <div class="fav-lcard-title">{{ $car->title }}</div>
                    <div class="fav-lcard-sub">{{ implode(' · ', array_filter([$car->category, $car->transmission])) ?: 'Certyfikowany pojazd' }}</div>
                    <div class="fav-lcard-specs">
                        @if($car->mileage)
                        <div class="fav-lcard-spec">
                            <x-icon name="gauge" size="14"/>
                            {{ number_format($car->mileage, 0, '.', ' ') }} km
                        </div>
                        @endif
                        @if($car->fuel_type)
                        <div class="fav-lcard-spec">
                            <x-icon name="fuel" size="14"/>
                            {{ $car->fuel_type }}
                        </div>
                        @endif
                        @if($car->power_hp)
                        <div class="fav-lcard-spec">
                            <x-icon name="zap" size="14"/>
                            {{ $car->power_hp }} KM
                        </div>
                        @endif
                        @if($car->first_registration)
                        <div class="fav-lcard-spec">
                            <x-icon name="calendar" size="14"/>
                            {{ $car->first_registration }}
                        </div>
                        @endif
                    </div>
                    <div class="fav-lcard-meta">Dodano do obserwowanych · {{ $car->created_at->diffForHumans() }}</div>
                </div>
                <div class="fav-lcard-price-col">
                    <div>
                        <div class="fav-lcard-price">{{ $car->formatted_price }}</div>
                        <div class="fav-lcard-price-lbl">{{ $car->price_type ?? 'brutto' }}</div>
                    </div>
                    <span class="fav-lcard-btn">
                        Szczegóły
                        <x-icon name="arrow-right" size="14"/>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="fav-empty" id="favEmpty">
        <div class="fav-empty-icon">
            <x-icon name="heart" size="16"/>
        </div>
        <h2>Brak obserwowanych pojazdów</h2>
        <p>Kliknij serduszko przy dowolnym aucie, aby dodać je do listy obserwowanych. Wróć tu kiedy chcesz — lista czeka.</p>
        <a href="{{ route('catalog') }}" class="btn btn-blue btn-pill">
            <x-icon name="search" size="14"/>
            Przeglądaj ofertę
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    // IDs confirmed valid by the server (public, active, not sold)
    const serverIds = @json($validIds);
    const url = new URL(window.location.href);
    const stored = JSON.parse(localStorage.getItem('certicars_favs') || '[]');
    const urlIds = url.searchParams.getAll('ids[]').map(Number);

    // No URL IDs but localStorage has some → redirect so server can validate them
    if (stored.length && !urlIds.length) {
        const params = stored.map(id => `ids[]=${id}`).join('&');
        window.location.replace('/obserwowane?' + params);
        return;
    }

    // Determine if any requested IDs were stale (not returned by server)
    const requestedIds = urlIds.length ? urlIds : stored;
    const staleRemoved = requestedIds.length > 0 && serverIds.length < requestedIds.length;

    // Write only server-validated IDs back to localStorage
    localStorage.setItem('certicars_favs', JSON.stringify(serverIds));

    // Badge must reflect only valid cars, not raw stored IDs
    updateNavBadge(serverIds.length);

    // Clean stale IDs out of the URL without triggering a reload
    if (staleRemoved && urlIds.length) {
        const cleanUrl = serverIds.length
            ? '/obserwowane?' + serverIds.map(id => `ids[]=${id}`).join('&')
            : '/obserwowane';
        window.history.replaceState(null, '', cleanUrl);
    }

    // Neutral notice when stale IDs were removed
    if (staleRemoved) {
        const notice = document.createElement('div');
        notice.style.cssText = 'background:#f8f9fb;border:1px solid #e5e7eb;border-radius:10px;padding:10px 16px;font-size:12px;color:#6b7280;margin-bottom:16px;display:flex;align-items:center;gap:8px';
        notice.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg> Niektóre obserwowane auta nie są już dostępne i zostały usunięte z listy.';
        const wrap = document.querySelector('.fav-wrap');
        if (wrap) wrap.insertAdjacentElement('afterbegin', notice);
    }
})();

function removeFav(e, id) {
    e.preventDefault();
    e.stopPropagation();
    let favs = JSON.parse(localStorage.getItem('certicars_favs') || '[]');
    favs = favs.filter(f => f !== id);
    localStorage.setItem('certicars_favs', JSON.stringify(favs));

    // Usuń kartę z DOM
    const card = e.currentTarget.closest('.fav-lcard');
    if (card) {
        card.style.transition = 'opacity .25s, transform .25s';
        card.style.opacity = '0';
        card.style.transform = 'translateX(-12px)';
        setTimeout(() => {
            card.remove();
            updateCount(favs.length);
            if (!favs.length) showEmpty();
        }, 260);
    }
    updateNavBadge(favs.length);
}

function updateCount(count) {
    const badge = document.getElementById('favCountBadge');
    if (badge) badge.textContent = count;
}

function showEmpty() {
    const listings = document.getElementById('favListings');
    const wrap = listings?.parentElement;
    if (listings && wrap) {
        listings.remove();
        wrap.innerHTML = `<div class="fav-empty">
            <div class="fav-empty-icon"><x-icon name="heart" size="16"/></div>
            <h2>Brak obserwowanych pojazdów</h2>
            <p>Usunąłeś wszystkie pojazdy z listy obserwowanych.</p>
            <a href="/samochody" class="btn btn-blue btn-pill">Przeglądaj ofertę</a>
        </div>`;
    }
}

function updateNavBadge(count) {
    const navBadge = document.getElementById('navFavBadge');
    if (navBadge) {
        navBadge.textContent = count;
        navBadge.style.display = count ? 'flex' : 'none';
    }
}
</script>
@endpush
