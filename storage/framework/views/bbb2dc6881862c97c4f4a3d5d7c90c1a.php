<?php $__env->startSection('title','Obserwowane'); ?>
<?php $__env->startSection('description','Twoje obserwowane samochody na CertiCars.'); ?>
<?php $__env->startSection('styles'); ?>
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
.fav-lcard{display:flex;text-decoration:none;border-bottom:1px solid var(--border-l);transition:background .15s;position:relative;overflow:hidden}
.fav-lcard:last-child{border-bottom:none}
.fav-lcard::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--orange);transform:scaleY(0);transform-origin:center;transition:transform .2s}
.fav-lcard:hover{background:#fafafa}
.fav-lcard:hover::before{transform:scaleY(1)}
.fav-lcard-img{width:260px;min-width:260px;height:190px;position:relative;overflow:hidden;flex-shrink:0;background:var(--bg)}
.fav-lcard-img img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.fav-lcard:hover .fav-lcard-img img{transform:scale(1.03)}
.fav-lcard-img-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center}
.fav-lcard-img-placeholder svg{width:48px;height:48px;stroke:var(--text-4);stroke-width:1.2;fill:none}
.fav-lcard-certi{position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,.7);color:#fff;font-size:10px;font-weight:700;padding:4px 8px;border-radius:6px;display:flex;align-items:center;gap:4px;backdrop-filter:blur(4px)}
.fav-lcard-certi svg{width:10px;height:10px;stroke:#4ea3ff;fill:none;stroke-width:2.5}
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="fav-header">
    <div class="fav-header-in">
        <nav class="fav-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo e(route('home')); ?>">Strona główna</a>
            <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            <span>Obserwowane</span>
        </nav>
        <div class="fav-title-row">
            <h1>Obserwowane pojazdy</h1>
            <span class="fav-count" id="favCountBadge"><?php echo e($cars->count()); ?></span>
        </div>
    </div>
</div>

<div class="fav-wrap">
    <?php if($cars->count()): ?>
    <div class="fav-listings" id="favListings">
        <?php $__currentLoopData = $cars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('catalog.show', $car)); ?>" class="fav-lcard" data-car-id="<?php echo e($car->id); ?>">
            <div class="fav-lcard-img">
                <?php if($car->primaryImage): ?>
                    <img src="<?php echo e($car->primaryImage->url); ?>" alt="<?php echo e($car->title); ?>" loading="lazy">
                <?php else: ?>
                    <div class="fav-lcard-img-placeholder">
                        <svg viewBox="0 0 24 24"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                    </div>
                <?php endif; ?>
                <div class="fav-lcard-certi">
                    <svg viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    CertiCheck
                </div>
                <button class="fav-remove" data-id="<?php echo e($car->id); ?>" aria-label="Usuń z obserwowanych" onclick="removeFav(event, <?php echo e($car->id); ?>)">
                    <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </button>
            </div>
            <div class="fav-lcard-content">
                <div class="fav-lcard-info">
                    <div class="fav-lcard-title"><?php echo e($car->title); ?></div>
                    <div class="fav-lcard-sub"><?php echo e(implode(' · ', array_filter([$car->category, $car->transmission])) ?: 'Certyfikowany pojazd'); ?></div>
                    <div class="fav-lcard-specs">
                        <?php if($car->mileage): ?>
                        <div class="fav-lcard-spec">
                            <svg viewBox="0 0 24 24"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                            <?php echo e(number_format($car->mileage, 0, '.', ' ')); ?> km
                        </div>
                        <?php endif; ?>
                        <?php if($car->fuel_type): ?>
                        <div class="fav-lcard-spec">
                            <svg viewBox="0 0 24 24"><line x1="3" x2="15" y1="22" y2="22"/><path d="M14 22V4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v18"/></svg>
                            <?php echo e($car->fuel_type); ?>

                        </div>
                        <?php endif; ?>
                        <?php if($car->power_hp): ?>
                        <div class="fav-lcard-spec">
                            <svg viewBox="0 0 24 24"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
                            <?php echo e($car->power_hp); ?> KM
                        </div>
                        <?php endif; ?>
                        <?php if($car->first_registration): ?>
                        <div class="fav-lcard-spec">
                            <svg viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            <?php echo e($car->first_registration); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="fav-lcard-meta">Dodano do obserwowanych · <?php echo e($car->created_at->diffForHumans()); ?></div>
                </div>
                <div class="fav-lcard-price-col">
                    <div>
                        <div class="fav-lcard-price"><?php echo e($car->formatted_price); ?></div>
                        <div class="fav-lcard-price-lbl"><?php echo e($car->price_type ?? 'brutto'); ?></div>
                    </div>
                    <span class="fav-lcard-btn">
                        Szczegóły
                        <svg viewBox="0 0 24 24"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </span>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php else: ?>
    <div class="fav-empty" id="favEmpty">
        <div class="fav-empty-icon">
            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </div>
        <h2>Brak obserwowanych pojazdów</h2>
        <p>Kliknij serduszko przy dowolnym aucie, aby dodać je do listy obserwowanych. Wróć tu kiedy chcesz — lista czeka.</p>
        <a href="<?php echo e(route('catalog')); ?>" class="btn btn-blue btn-pill">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Przeglądaj ofertę
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Na starcie — redirect do tej samej strony z ids z localStorage
(function () {
    const stored = JSON.parse(localStorage.getItem('certicars_favs') || '[]');
    const url = new URL(window.location.href);
    const urlIds = url.searchParams.getAll('ids[]').map(Number);

    // Jeśli URL nie ma ids ale localStorage ma → redirect
    if (stored.length && !urlIds.length) {
        const params = stored.map(id => `ids[]=${id}`).join('&');
        window.location.replace('/obserwowane?' + params);
        return;
    }

    // Jeśli URL ma ids → zsynchronizuj localStorage (gdyby był wyczyszczony)
    if (urlIds.length) {
        localStorage.setItem('certicars_favs', JSON.stringify(urlIds));
    }

    // Aktualizuj badge NavBar
    updateNavBadge(stored.length);
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
            <div class="fav-empty-icon"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/maksymzabavchuk/Desktop/certicars/resources/views/catalog/favorites.blade.php ENDPATH**/ ?>