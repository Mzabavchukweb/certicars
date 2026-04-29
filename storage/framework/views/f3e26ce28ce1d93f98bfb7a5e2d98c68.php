<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('actions'); ?>
<a href="<?php echo e(route('admin.cars.create')); ?>" class="btn btn-blue"><i data-lucide="plus"></i> Dodaj samochód</a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>


<h2 style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);font-weight:700;margin-bottom:12px">Ruch na stronie</h2>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:22px">
    <div class="stat">
        <div class="stat-ico" style="background:var(--blue-bg);color:var(--blue)"><i data-lucide="eye"></i></div>
        <div class="stat-label">Wejścia dziś</div>
        <div class="stat-value"><?php echo e($stats['views_today']); ?></div>
        <div class="stat-sub"><?php echo e($stats['views_7d']); ?> w ciągu 7 dni</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#ecfdf5;color:#047857"><i data-lucide="trending-up"></i></div>
        <div class="stat-label">Wejścia 30 dni</div>
        <div class="stat-value"><?php echo e(number_format($stats['views_30d'], 0, ',', ' ')); ?></div>
        <div class="stat-sub"><?php echo e(number_format($stats['views_total'], 0, ',', ' ')); ?> łącznie</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#fffbeb;color:#b45309"><i data-lucide="car"></i></div>
        <div class="stat-label">Odsłony ofert (7 dni)</div>
        <div class="stat-value"><?php echo e($stats['car_views_7d']); ?></div>
        <div class="stat-sub"><?php echo e(number_format($stats['car_views_total'], 0, ',', ' ')); ?> łącznie</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#eff6ff;color:#1e40af"><i data-lucide="inbox"></i></div>
        <div class="stat-label">Wiadomości</div>
        <div class="stat-value"><?php echo e($stats['total_msgs']); ?></div>
        <div class="stat-sub">
            <?php if($stats['unread_msgs']>0): ?>
                <a href="<?php echo e(route('admin.messages.index',['filter'=>'unread'])); ?>" style="color:var(--blue);font-weight:600"><?php echo e($stats['unread_msgs']); ?> nieprzeczytanych</a>
            <?php else: ?>
                Wszystkie przeczytane
            <?php endif; ?>
        </div>
    </div>
</div>


<h2 style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;color:var(--text-3);font-weight:700;margin-bottom:12px">Stan magazynu</h2>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:22px">
    <div class="stat">
        <div class="stat-ico"><i data-lucide="car"></i></div>
        <div class="stat-label">Wszystkich aut</div>
        <div class="stat-value"><?php echo e($stats['total_cars']); ?></div>
        <div class="stat-sub">+<?php echo e($stats['new_last_30']); ?> w 30 dniach</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#ecfdf5;color:#047857"><i data-lucide="check-circle"></i></div>
        <div class="stat-label">Aktywne</div>
        <div class="stat-value"><?php echo e($stats['active_cars']); ?></div>
        <div class="stat-sub"><?php echo e($stats['draft_cars']); ?> szkiców</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#fffbeb;color:#b45309"><i data-lucide="wallet"></i></div>
        <div class="stat-label">Wartość stocku</div>
        <div class="stat-value" style="font-size:22px"><?php echo e(number_format($stats['stock_value'], 0, ',', ' ')); ?> zł</div>
        <div class="stat-sub">śr. <?php echo e(number_format($stats['avg_price'], 0, ',', ' ')); ?> zł</div>
    </div>
    <div class="stat">
        <div class="stat-ico" style="background:#fef2f2;color:#b91c1c"><i data-lucide="shopping-cart"></i></div>
        <div class="stat-label">Sprzedane</div>
        <div class="stat-value"><?php echo e($stats['sold_cars']); ?></div>
        <div class="stat-sub"><?php echo e($stats['featured_cars']); ?> wyróżnionych</div>
    </div>
</div>


<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0">Aktywność (ostatnie 14 dni)</h2>
        <div style="display:flex;gap:14px;font-size:12px;flex-wrap:wrap">
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:var(--blue);border-radius:2px"></span> Wejścia</span>
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:#f59e0b;border-radius:2px"></span> Odsłony ofert</span>
            <span style="display:flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:#10b981;border-radius:2px"></span> Wiadomości</span>
        </div>
    </div>
    <canvas id="activityChart" height="70"></canvas>
</div>


<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <h2 style="margin:0">🔥 Najpopularniejsze oferty (wszystko)</h2>
            <a href="<?php echo e(route('admin.cars.index',['sort'=>'views','dir'=>'desc'])); ?>" class="btn btn-outline btn-sm">Wszystkie <i data-lucide="arrow-right"></i></a>
        </div>
        <?php if($topCars->count()): ?>
        <table class="data-table">
            <thead><tr><th style="width:40px">#</th><th style="width:80px"></th><th>Auto</th><th>Cena</th><th style="text-align:right">Odsłony</th><th></th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $topCars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="font-weight:700;color:var(--text-3)"><?php echo e($i + 1); ?></td>
                <td><?php if($car->primaryImage): ?><img src="<?php echo e($car->primaryImage->url); ?>" class="thumb" alt=""><?php else: ?><div style="width:60px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center"><i data-lucide="car" style="width:18px;height:18px;color:var(--text-4)"></i></div><?php endif; ?></td>
                <td><strong><?php echo e($car->title); ?></strong><br><span style="font-size:11px;color:var(--text-3)"><?php echo e($car->identifier); ?></span></td>
                <td><strong><?php echo e($car->formatted_price); ?></strong></td>
                <td style="text-align:right"><span class="badge-pill pill-blue"><i data-lucide="eye" style="width:11px;height:11px;vertical-align:-1px"></i> <?php echo e($car->views_count); ?></span></td>
                <td><a href="<?php echo e(route('admin.cars.edit',$car)); ?>" class="btn btn-outline btn-sm"><i data-lucide="edit"></i></a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state" style="padding:30px">
            <div class="ic"><i data-lucide="bar-chart-3"></i></div>
            <p style="font-size:13px">Brak zarejestrowanych odsłon. Statystyki pojawią się gdy użytkownicy zaczną przeglądać oferty.</p>
        </div>
        <?php endif; ?>
    </div>

    <div>
        <div class="card">
            <h2>Źródła ruchu (30 dni)</h2>
            <?php if($topReferers->count()): ?>
                <?php $__currentLoopData = $topReferers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-l);font-size:13px">
                    <span style="display:flex;align-items:center;gap:8px;min-width:0">
                        <i data-lucide="<?php echo e($r->display === 'Bezpośrednio' ? 'arrow-right-circle' : 'globe'); ?>" style="width:14px;height:14px;color:var(--text-3);flex-shrink:0"></i>
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo e($r->display); ?></span>
                    </span>
                    <strong style="flex-shrink:0;margin-left:10px"><?php echo e($r->count); ?></strong>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <p style="color:var(--text-3);font-size:12.5px;text-align:center;padding:18px">Brak danych</p>
            <?php endif; ?>
        </div>
        <div class="card">
            <h2>Top strony (30 dni)</h2>
            <?php if($topPages->count()): ?>
                <?php $__currentLoopData = $topPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border-l);font-size:12.5px">
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-2);font-family:ui-monospace,monospace">/<?php echo e($p->path); ?></span>
                    <strong style="flex-shrink:0;margin-left:10px"><?php echo e($p->count); ?></strong>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <p style="color:var(--text-3);font-size:12.5px;text-align:center;padding:18px">Brak danych</p>
            <?php endif; ?>
        </div>
    </div>
</div>


<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-top:8px">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <h2 style="margin:0">Ostatnie samochody</h2>
            <a href="<?php echo e(route('admin.cars.index')); ?>" class="btn btn-outline btn-sm">Wszystkie <i data-lucide="arrow-right"></i></a>
        </div>
        <?php if($recentCars->count()): ?>
        <table class="data-table">
            <thead><tr><th></th><th>Tytuł</th><th>Cena</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $recentCars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php if($car->primaryImage): ?><img src="<?php echo e($car->primaryImage->url); ?>" class="thumb" alt=""><?php else: ?><div style="width:60px;height:40px;background:var(--bg);border-radius:6px;display:flex;align-items:center;justify-content:center"><i data-lucide="car" style="width:18px;height:18px;color:var(--text-4)"></i></div><?php endif; ?></td>
                <td><strong><?php echo e($car->title); ?></strong><br><span style="font-size:11px;color:var(--text-3)"><?php echo e($car->identifier); ?> · <?php echo e($car->created_at->diffForHumans()); ?></span></td>
                <td><strong><?php echo e($car->formatted_price); ?></strong></td>
                <td>
                    <?php if($car->is_sold): ?><span class="badge-pill pill-red">Sprzedane</span>
                    <?php elseif($car->status==='active'): ?><span class="badge-pill pill-green">Aktywne</span>
                    <?php else: ?><span class="badge-pill pill-gray">Szkic</span><?php endif; ?>
                </td>
                <td><a href="<?php echo e(route('admin.cars.edit',$car)); ?>" class="btn btn-outline btn-sm"><i data-lucide="edit"></i></a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="color:var(--text-3);text-align:center;padding:28px">Brak samochodów. <a href="<?php echo e(route('admin.cars.create')); ?>" style="color:var(--blue);font-weight:600">Dodaj pierwszy</a></p>
        <?php endif; ?>
    </div>

    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <h2 style="margin:0">Ostatnie wiadomości</h2>
            <a href="<?php echo e(route('admin.messages.index')); ?>" class="btn btn-outline btn-sm"><i data-lucide="arrow-right"></i></a>
        </div>
        <?php if($recentMessages->count()): ?>
            <?php $__currentLoopData = $recentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('admin.messages.show',$m)); ?>" style="display:block;padding:12px 0;border-bottom:1px solid var(--border-l)">
                <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:3px">
                    <strong style="font-size:13px;<?php echo e(!$m->is_read ? '' : 'font-weight:500'); ?>">
                        <?php if(!$m->is_read): ?><span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--blue);margin-right:6px;vertical-align:middle"></span><?php endif; ?>
                        <?php echo e($m->name); ?>

                    </strong>
                    <span style="font-size:11px;color:var(--text-3);white-space:nowrap"><?php echo e($m->created_at->diffForHumans(null, true)); ?></span>
                </div>
                <div style="font-size:12px;color:var(--text-2);overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical"><?php echo e($m->message); ?></div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
        <p style="color:var(--text-3);text-align:center;padding:28px;font-size:13px">Brak wiadomości</p>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    const ctx=document.getElementById('activityChart');
    if(!ctx)return;
    const data=<?php echo json_encode($chart); ?>;
    new Chart(ctx,{
        type:'line',
        data:{
            labels:data.map(d=>d.label),
            datasets:[
                {label:'Wejścia',data:data.map(d=>d.views),borderColor:'#0066ff',backgroundColor:'rgba(0,102,255,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:3},
                {label:'Odsłony ofert',data:data.map(d=>d.cars),borderColor:'#f59e0b',backgroundColor:'rgba(245,158,11,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:3},
                {label:'Wiadomości',data:data.map(d=>d.msgs),borderColor:'#10b981',backgroundColor:'rgba(16,185,129,.1)',tension:.3,fill:true,borderWidth:2,pointRadius:3},
            ]
        },
        options:{
            responsive:true,
            plugins:{legend:{display:false},tooltip:{mode:'index',intersect:false}},
            interaction:{mode:'index',intersect:false},
            scales:{
                y:{beginAtZero:true,ticks:{stepSize:1,font:{size:11}},grid:{color:'#eeeef0'}},
                x:{ticks:{font:{size:11}},grid:{display:false}}
            }
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/maksymzabavchuk/Desktop/certicars/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>