

<?php ($pageTitle = 'Transaksi'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Daftar Transaksi</h1>
                <p class="text-muted">Kelola semua transaksi Anda di satu tempat.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('transactions.create')); ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi Baru
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo e(route('transactions.index')); ?>" method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari ID transaksi atau nama pelanggan..." value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="start_date" class="form-control" placeholder="Tanggal Mulai"
                            value="<?php echo e(request('start_date')); ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="end_date" class="form-control" placeholder="Tanggal Akhir"
                            value="<?php echo e(request('end_date')); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Jenis</th>
                                <th>Nama Pelanggan</th>
                                <th>Bagi Hasil</th>
                                <th>Pokok</th>
                                <th>Jumlah</th>
                                <th>Tanggal Transaksi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="p-2 rounded-circle bg-light me-2">
                                                    <i class="bi bi-receipt text-secondary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium">#TRX<?php echo e($transaction->id); ?></div>
                                                <div class="small text-muted"><?php echo e($transaction->id); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($transaction->type === 'piutang'): ?>
                                            <span class="badge bg-danger">Piutang</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Pembayaran</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($transaction->debtor->name); ?></td>
                                    <td><?php echo e($transaction->formatted_bagi_hasil); ?></td>
                                    <td><?php echo e($transaction->formatted_bagi_pokok); ?></td>
                                    <td class="fw-semibold"><?php echo e($transaction->formatted_amount); ?></td>
                                    <td><?php echo e($transaction->formatted_date); ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo e(route('transactions.edit', $transaction->id)); ?>"
                                                class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                data-action="<?php echo e(route('transactions.destroy', $transaction->id)); ?>" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada data transaksi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan <?php echo e($transactions->firstItem()); ?> - <?php echo e($transactions->lastItem()); ?> dari
                        <?php echo e($transactions->total()); ?>

                        data
                    </div>
                    <?php echo e($transactions->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/transactions/index.blade.php ENDPATH**/ ?>