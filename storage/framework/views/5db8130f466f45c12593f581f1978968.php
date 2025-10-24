

<?php ($pageTitle = 'Kartu Mutasi'); ?>

<?php $__env->startSection('content'); ?>
    <div id="print-content">
        <div class="container-fluid p-4">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold">Laporan Kartu Mutasi</h1>
                    <p class="text-muted">Laporan mutasi transaksi per debitur.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-outline-secondary me-2 no-print" onclick="printContent()">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>
                    <a href="<?php echo e(route('reports.export-kartu-mutasi', ['start_date' => $startDate, 'end_date' => $endDate, 'debtor_id' => $debtorId])); ?>"
                        class="btn btn-success no-print">
                        <i class="bi bi-download me-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card shadow-sm mb-4 no-print">
                <div class="card-body">
                    <form action="<?php echo e(route('reports.kartu-mutasi')); ?>" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="<?php echo e($startDate); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="<?php echo e($endDate); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="debtor_id" class="form-label">Debitur</label>
                            <select class="form-select" id="debtor_id" name="debtor_id">
                                <option value="">Semua Debitur</option>
                                <?php $__currentLoopData = $debtors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debtor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($debtor->id); ?>" <?php echo e($debtorId == $debtor->id ? 'selected' : ''); ?>>
                                        <?php echo e($debtor->name); ?>

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
                                    <th>Tanggal</th>
                                    <th>Debitur</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Debit (Piutang)</th>
                                    <th class="text-end">Kredit (Pembayaran)</th>
                                    <th class="text-end">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e(\Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y')); ?>

                                        </td>
                                        <td><?php echo e($transaction->debtor->name); ?></td>
                                        <td><?php echo e($transaction->description); ?></td>
                                        <td class="text-end">
                                            <?php echo e($transaction->type == 'piutang' ? 'Rp ' . number_format($transaction->amount, 0, ',', '.') : '-'); ?>

                                        </td>
                                        <td class="text-end">
                                            <?php echo e($transaction->type == 'pembayaran' ? 'Rp ' . number_format($transaction->amount, 0, ',', '.') : '-'); ?>

                                        </td>
                                        <td
                                            class="text-end fw-bold <?php echo e($transaction->running_balance < 0 ? 'text-danger' : ''); ?>">
                                            Rp <?php echo e(number_format($transaction->running_balance, 0, ',', '.')); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada data transaksi</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/reports/kartu_mutasi.blade.php ENDPATH**/ ?>