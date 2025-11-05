

<?php ($pageTitle = 'Kartu Mutasi'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Kartu Mutasi</h1>
                <p class="text-muted">Pilih debitur untuk melihat detail kartu mutasi.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-primary" onclick="printContent()">
                    <i class="bi bi-printer me-1"></i> Cetak
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo e(route('reports.kartu-mutasi')); ?>" method="GET" class="row g-3 mb-4">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama debitur..."
                                value="<?php echo e($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" placeholder="Dari Tanggal"
                            value="<?php echo e($startDate); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" placeholder="Sampai Tanggal"
                            value="<?php echo e($endDate); ?>">
                    </div>
                </form>

                <div class="table-responsive" id="print-content">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama Debitur</th>
                                <th>Saldo Awal</th>
                                <th>Saldo Akhir</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $debtors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debtor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="fw-medium"><?php echo e($debtor->name); ?></div>
                                    </td>
                                    <td><?php echo e($debtor->formatted_initial_balance); ?></td>
                                    <td
                                        class="<?php echo e($debtor->current_balance > 0 ? 'text-success' : ($debtor->current_balance < 0 ? 'text-danger' : '')); ?>">
                                        <?php echo e($debtor->formatted_balance); ?>

                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-<?php echo e($debtor->debtor_status == 'lunas' ? 'success' : ($debtor->debtor_status == 'Titipan' ? 'info' : 'danger')); ?>">
                                            <?php echo e($debtor->keterangan_piutang); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo e(route('reports.kartu-mutasi.show', $debtor->id)); ?>"
                                                class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada data debitur</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan <?php echo e($debtors->firstItem()); ?> - <?php echo e($debtors->lastItem()); ?> dari
                        <?php echo e($debtors->total()); ?>

                        data
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    <?php echo e($debtors->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/reports/kartuMutasi/index.blade.php ENDPATH**/ ?>