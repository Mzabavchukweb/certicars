<?php if($paginator->hasPages()): ?>
<nav role="navigation" aria-label="Paginacja" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:center">
    <?php if($paginator->onFirstPage()): ?>
        <span aria-disabled="true" style="display:inline-flex;align-items:center;gap:4px;padding:10px 14px;border:1px solid var(--border-l);border-radius:10px;font-size:13px;color:var(--text-4);background:#fff;cursor:not-allowed">
            <i data-lucide="chevron-left" style="width:14px;height:14px" aria-hidden="true"></i> Poprzednia
        </span>
    <?php else: ?>
        <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" aria-label="Poprzednia strona" style="display:inline-flex;align-items:center;gap:4px;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text);background:#fff;font-weight:500;transition:all .15s">
            <i data-lucide="chevron-left" style="width:14px;height:14px" aria-hidden="true"></i> Poprzednia
        </a>
    <?php endif; ?>

    <?php $__currentLoopData = $elements ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(is_string($element)): ?>
            <span style="padding:10px 6px;color:var(--text-4);font-size:13px"><?php echo e($element); ?></span>
        <?php endif; ?>
        <?php if(is_array($element)): ?>
            <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page == $paginator->currentPage()): ?>
                    <span aria-current="page" style="display:inline-flex;align-items:center;justify-content:center;min-width:40px;padding:10px 12px;border-radius:10px;font-size:13px;font-weight:600;background:var(--blue);color:#fff;border:1px solid var(--blue)"><?php echo e($page); ?></span>
                <?php else: ?>
                    <a href="<?php echo e($url); ?>" aria-label="Strona <?php echo e($page); ?>" style="display:inline-flex;align-items:center;justify-content:center;min-width:40px;padding:10px 12px;border:1px solid var(--border-l);border-radius:10px;font-size:13px;color:var(--text-2);background:#fff;font-weight:500;transition:all .15s"><?php echo e($page); ?></a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($paginator->hasMorePages()): ?>
        <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" aria-label="Następna strona" style="display:inline-flex;align-items:center;gap:4px;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text);background:#fff;font-weight:500;transition:all .15s">
            Następna <i data-lucide="chevron-right" style="width:14px;height:14px" aria-hidden="true"></i>
        </a>
    <?php else: ?>
        <span aria-disabled="true" style="display:inline-flex;align-items:center;gap:4px;padding:10px 14px;border:1px solid var(--border-l);border-radius:10px;font-size:13px;color:var(--text-4);background:#fff;cursor:not-allowed">
            Następna <i data-lucide="chevron-right" style="width:14px;height:14px" aria-hidden="true"></i>
        </span>
    <?php endif; ?>
</nav>
<?php endif; ?>
<?php /**PATH /Users/maksymzabavchuk/Desktop/certicars/resources/views/pagination/custom.blade.php ENDPATH**/ ?>