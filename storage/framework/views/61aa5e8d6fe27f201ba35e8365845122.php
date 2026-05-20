<div style="text-align: center;">
    <h1><?php echo e(config('app.name', 'SLV Accounting')); ?></h1>
    <h2>Laporan Piutang Per Bulan</h2>
</div>
<table>
    <thead>
        <tr>
            <th>Bulan</th>
            <th>Total Piutang</th>
            <th>Persentase</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $monthlyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($data['month']); ?></td>
                <td><?php echo e($data['total']); ?></td>
                <td>
                    <?php if($totalYear > 0): ?>
                        <?php echo e(round(($data['total'] / $totalYear) * 100, 1)); ?>%
                    <?php else: ?>
                        0%
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total Tahun <?php echo e($year); ?></strong></td>
            <td><strong><?php echo e($totalYear); ?></strong></td>
            <td><strong>100%</strong></td>
        </tr>
    </tfoot>
</table>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/exports/piutang_perbulan.blade.php ENDPATH**/ ?>