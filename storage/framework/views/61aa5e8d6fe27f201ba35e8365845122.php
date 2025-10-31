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
                <td>Rp <?php echo e(number_format($data['total'], 0, ',', '.')); ?></td>
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
            <td><strong>Rp <?php echo e(number_format($totalYear, 0, ',', '.')); ?></strong></td>
            <td><strong>100%</strong></td>
        </tr>
    </tfoot>
</table>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/exports/piutang_perbulan.blade.php ENDPATH**/ ?>