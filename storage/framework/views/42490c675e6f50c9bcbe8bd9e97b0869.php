

<?php ($pageTitle = 'Kartu Mutasi: ' . $debtor->name); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Kartu Mutasi</h1>
                <p class="text-muted">Detail transaksi untuk debitur: <?php echo e($debtor->name); ?></p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('reports.kartu-mutasi')); ?>" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button class="btn btn-primary" onclick="printContent()">
                    <i class="bi bi-printer me-1"></i> Cetak
                </button>
            </div>
        </div>

        <!-- Debitur Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Informasi Debitur</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td style="width: 150px;"><strong>Nama</strong></td>
                                <td><?php echo e($debtor->name); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td><?php echo e($debtor->address); ?></td>
                            </tr>
                            <tr>
                                <td><strong>No. Telepon</strong></td>
                                <td><?php echo e($debtor->phone); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Bergabung</strong></td>
                                <td><?php echo e($debtor->formatted_joined_at); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="card-title">Ringkasan Saldo</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td style="width: 150px;"><strong>Saldo Awal</strong></td>
                                <td>
                                    <?php if($debtor->initial_balance_with_type): ?>
                                        <?php echo e($debtor->initial_balance_with_type['formatted']); ?>

                                        <span
                                            class="badge bg-<?php echo e($debtor->initial_balance_with_type['is_negative'] ? 'danger' : 'success'); ?> ms-2">
                                            <?php echo e($debtor->initial_balance_with_type['is_negative'] ? 'Piutang' : 'Titipan'); ?>

                                        </span>
                                        <span class="badge bg-secondary ms-1">
                                            <?php echo e($debtor->initial_balance_with_type['type_label']); ?>

                                        </span>
                                    <?php else: ?>
                                        Rp 0
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total Piutang</strong></td>
                                <td class="text-danger"><?php echo e($debtor->formatted_total_piutang); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Total Pembayaran</strong></td>
                                <td class="text-success"><?php echo e($debtor->formatted_total_pembayaran); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Sisa Saldo</strong></td>
                                <td
                                    class="<?php echo e($debtor->current_balance > 0 ? 'text-success' : ($debtor->current_balance < 0 ? 'text-danger' : '')); ?>">
                                    <?php echo e($debtor->formatted_balance); ?>

                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>
                                    <span
                                        class="badge bg-<?php echo e($debtor->debtor_status == 'lunas' ? 'success' : ($debtor->debtor_status == 'lebih_bayar' ? 'info' : 'danger')); ?>">
                                        <?php echo e($debtor->keterangan_piutang); ?>

                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="<?php echo e(route('reports.kartu-mutasi.show', $debtor->id)); ?>" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo e($startDate); ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo e($endDate); ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transaction Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Detail Transaksi</h5>
                <div class="table-responsive" id="print-content">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th colspan="2">Piutang</th>
                                <th>Jumlah Piutang</th>
                                <th colspan="2">Pembayaran</th>
                                <th>Jumlah Pembayaran</th>
                                <th colspan="2">Sisa Saldo</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Pokok</th>
                                <th>Bagi Hasil</th>
                                <th></th>
                                <th>Pokok</th>
                                <th>Bagi Hasil</th>
                                <th></th>
                                <th>Pokok</th>
                                <th>Bagi Hasil</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // PERBAIKAN: Ambil saldo awal dari initial_balance_with_type
                            $saldoPokok = 0;
                            $saldoBagiHasil = 0;
                            $saldoTotal = 0;
                            
                            if ($debtor->initial_balance_with_type) {
                                $saldoPokok = $debtor->initial_balance_with_type['pokok_amount'] ?? 0;
                                $saldoBagiHasil = $debtor->initial_balance_with_type['bagi_hasil_amount'] ?? 0;
                                $saldoTotal = $debtor->initial_balance_with_type['amount'];
                            }
                            ?>
                            <?php $__currentLoopData = $debtor->transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($transaction->formatted_id); ?></td>
                                    <td><?php echo e($transaction->formatted_date); ?></td>
                                    <td><?php echo e($transaction->description); ?></td>
                                    <?php if($transaction->type == 'piutang'): ?>
                                        <td class="text-danger"><?php echo e(number_format($transaction->bagi_pokok, 0, ',', '.')); ?>

                                        </td>
                                        <td class="text-danger"><?php echo e(number_format($transaction->bagi_hasil, 0, ',', '.')); ?>

                                        </td>
                                        <td class="text-danger fw-bold">
                                            <?php echo e(number_format($transaction->amount, 0, ',', '.')); ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <?php
                                        $saldoPokok -= $transaction->bagi_pokok;
                                        $saldoBagiHasil -= $transaction->bagi_hasil;
                                        $saldoTotal -= $transaction->amount;
                                        ?>
                                    <?php else: ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-success"><?php echo e(number_format($transaction->bagi_pokok, 0, ',', '.')); ?>

                                        </td>
                                        <td class="text-success"><?php echo e(number_format($transaction->bagi_hasil, 0, ',', '.')); ?>

                                        </td>
                                        <td class="text-success fw-bold">
                                            <?php echo e(number_format($transaction->amount, 0, ',', '.')); ?></td>
                                        <?php
                                        $saldoPokok += $transaction->bagi_pokok;
                                        $saldoBagiHasil += $transaction->bagi_hasil;
                                        $saldoTotal += $transaction->amount;
                                        ?>
                                    <?php endif; ?>
                                    <td class="<?php echo e($saldoPokok >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <?php echo e(number_format($saldoPokok, 0, ',', '.')); ?></td>
                                    <td class="<?php echo e($saldoBagiHasil >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <?php echo e(number_format($saldoBagiHasil, 0, ',', '.')); ?></td>
                                    <td class="<?php echo e($saldoTotal >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <?php echo e(number_format($saldoTotal, 0, ',', '.')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr class="table-active">
                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                <td class="text-danger">
                                    <strong><?php echo e(number_format($debtor->transactions->where('type', 'piutang')->sum('bagi_pokok'), 0, ',', '.')); ?></strong>
                                </td>
                                <td class="text-danger">
                                    <strong><?php echo e(number_format($debtor->transactions->where('type', 'piutang')->sum('bagi_hasil'), 0, ',', '.')); ?></strong>
                                </td>
                                <td class="text-danger">
                                    <strong><?php echo e(number_format($debtor->transactions->where('type', 'piutang')->sum('amount'), 0, ',', '.')); ?></strong>
                                </td>
                                <td class="text-success">
                                    <strong><?php echo e(number_format($debtor->transactions->where('type', 'pembayaran')->sum('bagi_pokok'), 0, ',', '.')); ?></strong>
                                </td>
                                <td class="text-success">
                                    <strong><?php echo e(number_format($debtor->transactions->where('type', 'pembayaran')->sum('bagi_hasil'), 0, ',', '.')); ?></strong>
                                </td>
                                <td class="text-success">
                                    <strong><?php echo e(number_format($debtor->transactions->where('type', 'pembayaran')->sum('amount'), 0, ',', '.')); ?></strong>
                                </td>
                                <td class="<?php echo e($saldoPokok >= 0 ? 'text-success' : 'text-danger'); ?>">
                                    <strong><?php echo e(number_format($saldoPokok, 0, ',', '.')); ?></strong>
                                </td>
                                <td class="<?php echo e($saldoBagiHasil >= 0 ? 'text-success' : 'text-danger'); ?>">
                                    <strong><?php echo e(number_format($saldoBagiHasil, 0, ',', '.')); ?></strong>
                                </td>
                                <td class="<?php echo e($saldoTotal >= 0 ? 'text-success' : 'text-danger'); ?>">
                                    <strong><?php echo e(number_format($saldoTotal, 0, ',', '.')); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/reports/kartuMutasi/show.blade.php ENDPATH**/ ?>