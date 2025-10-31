

<?php ($pageTitle = 'Debit Piutang'); ?>

<?php $__env->startSection('content'); ?>
    <div id="print-content">
        <div class="container-fluid p-4">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold">Laporan Debit Piutang</h1>
                    <p class="text-muted">Laporan seluruh debitur dan piutang mereka.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-outline-secondary me-2 no-print" onclick="printContent()">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>
                    <a href="<?php echo e(route('reports.export-debit-piutang')); ?>" class="btn btn-success no-print">
                        <i class="bi bi-download me-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 bg-warning bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-circle bg-warning bg-opacity-20">
                                        <i class="bi bi-cash-stack text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-1">Total Piutang</h6>
                                    <p class="card-text fs-5 fw-bold mb-0 text-warning">Rp
                                        <?php echo e(number_format($totalPiutang, 0, ',', '.')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 bg-success bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-circle bg-success bg-opacity-20">
                                        <i class="bi bi-cash-coin text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-1">Total Pembayaran</h6>
                                    <p class="card-text fs-5 fw-bold mb-0 text-success">Rp
                                        <?php echo e(number_format($totalPembayaran, 0, ',', '.')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div
                        class="card shadow-sm border-0 <?php echo e($saldoAkhir >= 0 ? 'bg-info bg-opacity-10' : 'bg-danger bg-opacity-10'); ?> h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="p-3 rounded-circle <?php echo e($saldoAkhir >= 0 ? 'bg-info bg-opacity-20' : 'bg-danger bg-opacity-20'); ?>">
                                        <i
                                            class="bi bi-wallet2 <?php echo e($saldoAkhir >= 0 ? 'text-info' : 'text-danger'); ?> fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-1">Saldo Akhir</h6>
                                    <p
                                        class="card-text fs-5 fw-bold mb-0 <?php echo e($saldoAkhir >= 0 ? 'text-info' : 'text-danger'); ?>">
                                        Rp <?php echo e(number_format($saldoAkhir, 0, ',', '.')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Debitur</th>
                                    <th class="text-end">Total Piutang</th>
                                    <th class="text-end">Total Pembayaran</th>
                                    <th class="text-end">Saldo</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $debtors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debtor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="p-2 rounded-circle bg-light me-2">
                                                        <i class="bi bi-person text-secondary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-medium"><?php echo e($debtor->name); ?></div>
                                                    <div class="small text-muted"><?php echo e($debtor->phone); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">Rp <?php echo e(number_format($debtor->total_piutang, 0, ',', '.')); ?>

                                        </td>
                                        <td class="text-end">Rp <?php echo e(number_format($debtor->total_pembayaran, 0, ',', '.')); ?>

                                        </td>
                                        <td class="text-end <?php echo e($debtor->current_balance < 0 ? 'text-danger' : ''); ?>">
                                            Rp <?php echo e(number_format($debtor->current_balance, 0, ',', '.')); ?>

                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge 
                                            <?php if($debtor->debtor_status == 'lunas'): ?> bg-success
                                            <?php elseif($debtor->debtor_status == 'belum_lunas'): ?> bg-warning
                                            <?php else: ?> bg-danger <?php endif; ?>">
                                                <?php echo e(ucfirst(str_replace('_', ' ', $debtor->debtor_status))); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada data debitur</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">Rp <?php echo e(number_format($totalPiutang, 0, ',', '.')); ?></td>
                                    <td class="text-end">Rp <?php echo e(number_format($totalPembayaran, 0, ',', '.')); ?></td>
                                    <td class="text-end">Rp <?php echo e(number_format($saldoAkhir, 0, ',', '.')); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/reports/debit_piutang.blade.php ENDPATH**/ ?>