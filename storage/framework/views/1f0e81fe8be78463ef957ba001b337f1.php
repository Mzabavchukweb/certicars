<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($car->title); ?> — CertiCars</title>
    <style>
        @page { margin: 40px 40px 50px 40px; }
        body{font-family:'DejaVu Sans',sans-serif;color:#0a0a0a;font-size:10px;line-height:1.5}
        h1{font-size:20px;font-weight:bold;margin-bottom:4px;color:#0a0a0a}
        h2{font-size:13px;font-weight:bold;margin:16px 0 8px;color:#0066ff;border-bottom:1px solid #0066ff;padding-bottom:4px}
        .header{border-bottom:2px solid #0066ff;padding-bottom:12px;margin-bottom:14px}
        .header-top{display:table;width:100%;margin-bottom:8px}
        .brand{display:table-cell;font-weight:bold;font-size:14px;color:#0066ff}
        .ident{display:table-cell;text-align:right;color:#666;font-size:9px}
        .meta{color:#555;font-size:9px;margin-top:4px}
        .price-box{background:#0a0a0a;color:#fff;padding:12px 16px;border-radius:6px;margin:10px 0;display:table;width:100%}
        .price-box .lbl{display:table-cell;font-size:9px;color:#aaa;text-transform:uppercase;vertical-align:middle}
        .price-box .val{display:table-cell;text-align:right;font-size:18px;font-weight:bold;vertical-align:middle}
        table{width:100%;border-collapse:collapse;font-size:9.5px}
        td{padding:5px 8px;border-bottom:1px solid #eee}
        td.lbl{color:#666;width:45%}
        td.val{font-weight:bold;text-align:right}
        .two-col{display:table;width:100%}
        .two-col .col{display:table-cell;width:50%;vertical-align:top;padding-right:10px}
        .two-col .col:last-child{padding-right:0;padding-left:10px}
        .dmg{background:#fffbeb;border-left:3px solid #f59e0b;padding:8px 12px;margin-bottom:6px;font-size:9.5px}
        .dmg strong{color:#b45309}
        .foot{position:fixed;bottom:-30px;left:0;right:0;text-align:center;font-size:8px;color:#888;border-top:1px solid #eee;padding-top:6px}
        .img{max-height:280px;width:auto;max-width:100%}
        .img-wrap{text-align:center;margin-bottom:12px}
        ul{list-style:none;padding:0;margin:0}
        ul li{padding:3px 0;font-size:9px}
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="brand">CertiCars</div>
            <div class="ident"><?php echo e($car->identifier); ?> · <?php echo e(now()->format('d.m.Y')); ?></div>
        </div>
        <h1><?php echo e($car->title); ?></h1>
        <div class="meta">
            <?php if($car->first_registration): ?><?php echo e($car->first_registration); ?> · <?php endif; ?>
            <?php if($car->mileage): ?><?php echo e(number_format($car->mileage,0,'',' ')); ?> km · <?php endif; ?>
            <?php if($car->fuel_type): ?><?php echo e($car->fuel_type); ?> · <?php endif; ?>
            <?php if($car->power_hp): ?><?php echo e($car->power_hp); ?> KM <?php endif; ?>
        </div>
    </div>

    <?php if($car->primaryImage): ?>
    <div class="img-wrap">
        <img src="<?php echo e(public_path('storage/'.$car->primaryImage->path)); ?>" class="img" onerror="this.style.display='none'">
    </div>
    <?php endif; ?>

    <div class="price-box">
        <span class="lbl">Cena sprzedaży</span>
        <span class="val"><?php echo e($car->formatted_price); ?></span>
    </div>

    <h2>Specyfikacja</h2>
    <div class="two-col">
        <div class="col">
            <table>
                <?php if($car->first_registration): ?><tr><td class="lbl">Rejestracja</td><td class="val"><?php echo e($car->first_registration); ?></td></tr><?php endif; ?>
                <?php if($car->mileage): ?><tr><td class="lbl">Przebieg</td><td class="val"><?php echo e(number_format($car->mileage,0,'',' ')); ?> km</td></tr><?php endif; ?>
                <?php if($car->fuel_type): ?><tr><td class="lbl">Paliwo</td><td class="val"><?php echo e($car->fuel_type); ?></td></tr><?php endif; ?>
                <?php if($car->transmission): ?><tr><td class="lbl">Skrzynia</td><td class="val"><?php echo e($car->transmission); ?></td></tr><?php endif; ?>
                <?php if($car->power_hp): ?><tr><td class="lbl">Moc</td><td class="val"><?php echo e($car->power_hp); ?> KM</td></tr><?php endif; ?>
                <?php if($car->engine_capacity): ?><tr><td class="lbl">Pojemność</td><td class="val"><?php echo e($car->engine_capacity); ?> ccm</td></tr><?php endif; ?>
            </table>
        </div>
        <div class="col">
            <table>
                <?php if($car->color): ?><tr><td class="lbl">Kolor</td><td class="val"><?php echo e($car->color); ?></td></tr><?php endif; ?>
                <?php if($car->body_type): ?><tr><td class="lbl">Nadwozie</td><td class="val"><?php echo e($car->body_type); ?></td></tr><?php endif; ?>
                <?php if($car->doors): ?><tr><td class="lbl">Drzwi</td><td class="val"><?php echo e($car->doors); ?></td></tr><?php endif; ?>
                <?php if($car->seats): ?><tr><td class="lbl">Siedzenia</td><td class="val"><?php echo e($car->seats); ?></td></tr><?php endif; ?>
                <?php if($car->vin): ?><tr><td class="lbl">VIN</td><td class="val"><?php echo e($car->vin); ?></td></tr><?php endif; ?>
                <?php if($car->co2_emission): ?><tr><td class="lbl">CO₂</td><td class="val"><?php echo e($car->co2_emission); ?></td></tr><?php endif; ?>
            </table>
        </div>
    </div>

    <?php if($car->damages->count()): ?>
    <h2>Uszkodzenia pojazdu (<?php echo e($car->damages->count()); ?>)</h2>
    <?php $__currentLoopData = $car->damages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="dmg">
        <strong><?php echo e($d->area); ?></strong>
        <?php if($d->tags && count($d->tags)): ?> — <?php echo e(implode(', ', $d->tags)); ?><?php endif; ?>
        <?php if($d->description): ?><br><?php echo e($d->description); ?><?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php if($car->equipment): ?>
    <h2>Wyposażenie</h2>
    <div class="two-col">
        <?php
            $all = [];
            foreach($car->equipment as $items) {
                if(is_array($items)) foreach($items as $it) $all[] = $it;
            }
            $half = ceil(count($all)/2);
        ?>
        <div class="col"><ul><?php $__currentLoopData = array_slice($all,0,$half); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li>✓ <?php echo e($it); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
        <div class="col"><ul><?php $__currentLoopData = array_slice($all,$half); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li>✓ <?php echo e($it); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    </div>
    <?php endif; ?>

    <div class="foot">CertiCars · kontakt@certicars.pl · +48 123 456 789 · certicars.pl</div>
</body>
</html>
<?php /**PATH /Users/maksymzabavchuk/Desktop/certicars/resources/views/pdf/brochure.blade.php ENDPATH**/ ?>