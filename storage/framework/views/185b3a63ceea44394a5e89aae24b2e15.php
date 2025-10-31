

<?php ($pageTitle = 'Detail Journal'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Detail Journal</h1>
                <p class="text-muted">Detail informasi transaksi #<?php echo e($transaction->formatted_id); ?></p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('journal.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150">ID Transaksi</td>
                                <td><strong><?php echo e($transaction->formatted_id); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td><?php echo e($transaction->formatted_date); ?></td>
                            </tr>
                            <tr>
                                <td>Tipe</td>
                                <td>
                                    <span class="badge bg-<?php echo e($transaction->type == 'piutang' ? 'info' : 'success'); ?>">
                                        <?php echo e(ucfirst($transaction->type)); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Jumlah</td>
                                <td class="<?php echo e($transaction->type == 'piutang' ? 'text-danger' : 'text-success'); ?>">
                                    <strong><?php echo e($transaction->formatted_amount); ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <span class="badge bg-<?php echo e($transaction->status_color); ?>">
                                        <?php echo e($transaction->status_label); ?>

                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150">Dibuat Oleh</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="p-2 rounded-circle bg-light me-2">
                                                <i class="bi bi-person text-secondary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?php echo e($transaction->user->name); ?></div>
                                            <div class="small text-muted"><?php echo e(ucfirst($transaction->user->role)); ?></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Debitur</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="p-2 rounded-circle bg-light me-2">
                                                <i class="bi bi-people text-secondary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?php echo e($transaction->debtor->name); ?></div>
                                            <div class="small text-muted"><?php echo e($transaction->debtor->phone); ?></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Deskripsi</td>
                                <td><?php echo e($transaction->description ?: '-'); ?></td>
                            </tr>
                            <tr>
                                <td>Dibuat Pada</td>
                                <td><?php echo e($transaction->created_at->format('d M Y H:i')); ?></td>
                            </tr>
                            <tr>
                                <td>Diperbarui Pada</td>
                                <td><?php echo e($transaction->updated_at->format('d M Y H:i')); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="mb-3">Riwayat Perubahan</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo e($transaction->created_at->format('d M Y H:i')); ?></td>
                                    <td><?php echo e($transaction->user->name); ?></td>
                                    <td><span class="badge bg-success">Membuat</span></td>
                                    <td>Membuat transaksi <?php echo e($transaction->type); ?></td>
                                </tr>
                                <?php if($transaction->updated_at > $transaction->created_at): ?>
                                    <tr>
                                        <td><?php echo e($transaction->updated_at->format('d M Y H:i')); ?></td>
                                        <td><?php echo e($transaction->user->name); ?></td>
                                        <td><span class="badge bg-warning">Memperbarui</span></td>
                                        <td>Memperbarui data transaksi</td>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/journal/show.blade.php ENDPATH**/ ?>