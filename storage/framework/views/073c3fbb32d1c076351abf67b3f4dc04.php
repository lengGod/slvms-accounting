

<?php ($pageTitle = 'Detail Debitur'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Detail Debitur: <?php echo e($debtor->name); ?></h1>
                <p class="text-muted">Informasi lengkap dan riwayat transaksi debitur.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('debtors.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Informasi Debitur Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Informasi Debitur</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nama Lengkap</label>
                                <p class="fw-bold"><?php echo e($debtor->name); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">No. Telepon</label>
                                <p><?php echo e($debtor->phone ?: '-'); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kategori</label>
                                <p>
                                    <span class="badge bg-<?php echo e($debtor->category == 'internal' ? 'info' : 'warning'); ?>">
                                        <?php echo e(ucfirst($debtor->category)); ?>

                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Bergabung</label>
                                <p><?php echo e($debtor->formatted_joined_at); ?></p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Alamat</label>
                                <p><?php echo e($debtor->address ?: '-'); ?></p>
                            </div>
                            <!-- Informasi saldo awal -->
                            <div class="col-12 mb-3">
                                <label class="form-label text-muted">Saldo Awal</label>
                                <?php if($debtor->initial_balance_with_type): ?>
                                    <div class="d-flex align-items-center">
                                        <p class="fw-medium mb-0"><?php echo e($debtor->initial_balance_with_type['formatted']); ?></p>
                                        <span
                                            class="badge bg-<?php echo e($debtor->initial_balance_with_type['is_negative'] ? 'danger' : 'success'); ?> ms-2">
                                            <?php echo e($debtor->initial_balance_with_type['is_negative'] ? 'Piutang' : 'Titipan'); ?>

                                        </span>
                                        <span class="badge bg-secondary ms-1">
                                            <?php echo e($debtor->initial_balance_with_type['type_label']); ?>

                                        </span>
                                    </div>
                                <?php else: ?>
                                    <p class="fw-medium">Rp 0</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Transaksi Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Riwayat Transaksi</h5>
                        <a href="<?php echo e(route('transactions.create', ['debtor_id' => $debtor->id])); ?>"
                            class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-end">Bagi Pokok</th>
                                        <th class="text-end">Bagi Hasil</th>
                                        <th class="text-center">Tipe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e(\Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y')); ?>

                                            </td>
                                            <td>
                                                <small>
                                                    <?php if($transaction->description): ?>
                                                        <?php echo e($transaction->description); ?>

                                                        <?php if(strpos($transaction->description, 'Pembayaran menggunakan titipan') !== false): ?>
                                                            <br>
                                                            <span class="badge bg-info">Menggunakan Titipan</span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td
                                                class="text-end fw-medium <?php echo e($transaction->type == 'piutang' ? 'text-danger' : 'text-success'); ?>">
                                                <?php echo e($transaction->formatted_amount); ?>

                                            </td>
                                            <td class="text-end"><?php echo e($transaction->formatted_bagi_pokok); ?></td>
                                            <td class="text-end"><?php echo e($transaction->formatted_bagi_hasil); ?></td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-<?php echo e($transaction->type == 'piutang' ? 'info' : 'success'); ?>">
                                                    <?php echo e(ucfirst($transaction->type)); ?>

                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if($transactions->count() > 0): ?>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Menampilkan <?php echo e($transactions->firstItem()); ?> - <?php echo e($transactions->lastItem()); ?> dari
                                <?php echo e($transactions->total()); ?> data
                            </div>
                            <?php echo e($transactions->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>

                <!-- Riwayat Titipan Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Riwayat Titipan</h5>
                        <a href="<?php echo e(route('titipans.create', ['debtor_id' => $debtor->id])); ?>"
                            class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Titipan
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $debtor->titipans()->latest()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $titipan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e(\Carbon\Carbon::parse($titipan->tanggal)->format('d M Y')); ?></td>
                                            <td><?php echo e($titipan->keterangan ?: '-'); ?></td>
                                            <td class="text-end fw-medium text-success">
                                                <?php echo e($titipan->formatted_amount); ?>

                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="<?php echo e(route('titipans.edit', $titipan)); ?>"
                                                        class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('titipans.destroy', $titipan)); ?>" method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">Belum ada titipan</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Informasi Saldo Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Informasi Saldo</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Saldo Awal</label>
                            <?php if($debtor->initial_balance_with_type): ?>
                                <div class="d-flex align-items-center">
                                    <p class="fs-5 mb-0"><?php echo e($debtor->initial_balance_with_type['formatted']); ?></p>
                                    <span
                                        class="badge bg-<?php echo e($debtor->initial_balance_with_type['is_negative'] ? 'danger' : 'success'); ?> ms-2">
                                        <?php echo e($debtor->initial_balance_with_type['is_negative'] ? 'Piutang' : 'Titipan'); ?>

                                    </span>
                                    <span class="badge bg-secondary ms-1">
                                        <?php echo e($debtor->initial_balance_with_type['type_label']); ?>

                                    </span>
                                </div>
                            <?php else: ?>
                                <p class="fs-5">Rp 0</p>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Piutang</label>
                            <p class="fs-5 text-danger">Rp <?php echo e(number_format($debtor->total_piutang, 0, ',', '.')); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Pembayaran</label>
                            <p class="fs-5 text-success">Rp <?php echo e(number_format($debtor->total_pembayaran, 0, ',', '.')); ?>

                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Saldo Pokok</label>
                            <p class="fs-5 <?php echo e($debtor->saldo_pokok < 0 ? 'text-danger' : 'text-success'); ?>">
                                <?php echo e($debtor->formatted_saldo_pokok); ?>

                            </p>
                            <small class="text-muted">
                                <?php if($debtor->saldo_pokok < 0): ?>
                                    (Piutang Pokok)
                                <?php else: ?>
                                    (Saldo Positif)
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Saldo Bagi Hasil</label>
                            <p class="fs-5 <?php echo e($debtor->saldo_bagi_hasil < 0 ? 'text-danger' : 'text-success'); ?>">
                                <?php echo e($debtor->formatted_saldo_bagi_hasil); ?>

                            </p>
                            <small class="text-muted">
                                <?php if($debtor->saldo_bagi_hasil < 0): ?>
                                    (Piutang Bagi Hasil)
                                <?php else: ?>
                                    (Saldo Positif)
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Titipan</label>
                            <p class="fs-5 text-info"><?php echo e($debtor->formatted_total_titipan); ?></p>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label text-muted">Saldo Saat Ini</label>
                            <p
                                class="fs-4 fw-bold <?php echo e($debtor->current_balance < 0 ? 'text-danger' : ($debtor->current_balance > 0 ? 'text-info' : 'text-success')); ?>">
                                <?php echo e($debtor->formatted_balance); ?>

                            </p>
                            <small class="text-muted d-block">
                                <?php if($debtor->current_balance < 0): ?>
                                    = Total Piutang
                                <?php elseif($debtor->current_balance > 0): ?>
                                    = Total Titipan
                                <?php else: ?>
                                    = Lunas
                                <?php endif; ?>
                            </small>
                        </div>
                        <div>
                            <label class="form-label text-muted">Status</label>
                            <p>
                                <span
                                    class="badge bg-<?php echo e($debtor->debtor_status == 'lunas' ? 'success' : ($debtor->debtor_status == 'belum_lunas' ? 'warning' : 'info')); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $debtor->debtor_status))); ?>

                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="form-label text-muted">Keterangan</label>
                            <p>
                                <?php echo e($debtor->keterangan_piutang); ?>

                                <?php if($debtor->initial_balance_with_type && $debtor->initial_balance_with_type['amount'] > 0): ?>
                                    <br><small class="text-muted">Saldo awal:
                                        <?php echo e($debtor->initial_balance_with_type['formatted']); ?>

                                        (<?php echo e($debtor->initial_balance_with_type['is_negative'] ? 'Piutang' : 'Titipan'); ?>)</small>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Aksi Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Aksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('debtors.edit', $debtor)); ?>" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i> Edit Data
                            </a>
                            <a href="<?php echo e(route('transactions.create', ['debtor_id' => $debtor->id])); ?>"
                                class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Transaksi
                            </a>
                            <a href="<?php echo e(route('titipans.create', ['debtor_id' => $debtor->id])); ?>" class="btn btn-info">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Titipan
                            </a>
                            <form action="<?php echo e(route('debtors.destroy', $debtor)); ?>" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash me-1"></i> Hapus Debitur
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/debtors/show.blade.php ENDPATH**/ ?>