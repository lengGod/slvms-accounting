

<?php ($pageTitle = 'Piutang'); ?>

<?php $__env->startSection('content'); ?>
    <div id="print-content">
        <div class="container-fluid p-4">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold">Laporan Piutang Per Bulan</h1>
                    <p class="text-muted">Laporan piutang per bulan untuk tahun <?php echo e($year); ?>.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-outline-secondary me-2 no-print" onclick="printContent()">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>
                    <a href="<?php echo e(route('reports.export-piutang-perbulan', ['year' => $year])); ?>"
                        class="btn btn-success no-print">
                        <i class="bi bi-download me-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <form action="<?php echo e(route('reports.piutang-perbulan')); ?>" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="year" class="form-label">Tahun</label>
                            <select class="form-select" id="year" name="year">
                                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>>
                                        <?php echo e($y); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="d-grid gap-2 w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th class="text-end">Total Piutang</th>
                                    <th class="text-center">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $monthlyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($data['month']); ?></td>
                                        <td class="text-end">Rp <?php echo e(number_format($data['total'], 0, ',', '.')); ?></td>
                                        <td class="text-center">
                                            <?php if($totalYear > 0): ?>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                        style="width: <?php echo e(($data['total'] / $totalYear) * 100); ?>%;"
                                                        aria-valuenow="<?php echo e(($data['total'] / $totalYear) * 100); ?>"
                                                        aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small><?php echo e(round(($data['total'] / $totalYear) * 100, 1)); ?>%</small>
                                            <?php else: ?>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                        style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <small>0%</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary fw-bold">
                                    <td>Total Tahun <?php echo e($year); ?></td>
                                    <td class="text-end">Rp <?php echo e(number_format($totalYear, 0, ',', '.')); ?></td>
                                    <td class="text-center">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/reports/piutang_perbulan.blade.php ENDPATH**/ ?>