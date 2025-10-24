

<?php ($pageTitle = 'Konfirmasi Penggunaan Titipan'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Konfirmasi Penggunaan Titipan</h1>
                <p class="text-muted">Gunakan titipan untuk menutupi sebagian atau seluruh piutang.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('transactions.create')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>
                        <strong>Informasi:</strong>
                        <p class="mb-0 mt-1">Debitur memiliki titipan sebesar <?php echo e($usableTitipanFormatted); ?> yang dapat
                            digunakan untuk menutupi piutang sebesar <?php echo e($piutangAmountFormatted); ?>.</p>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Informasi Debitur</h5>
                                <p class="card-text"><strong>Nama:</strong> <?php echo e($debtor->name); ?></p>
                                <p class="card-text"><strong>Total Titipan:</strong>
                                    <?php echo e('Rp ' . number_format($debtor->total_titipan, 0, ',', '.')); ?></p>
                                <p class="card-text"><strong>Saldo Saat Ini:</strong> <?php echo e($debtor->formatted_balance); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Rincian Piutang</h5>
                                <p class="card-text"><strong>Jumlah Piutang:</strong> <?php echo e($piutangAmountFormatted); ?></p>
                                <p class="card-text"><strong>Titipan yang Dapat Digunakan:</strong>
                                    <?php echo e($usableTitipanFormatted); ?></p>
                                <p class="card-text"><strong>Sisa Piutang:</strong> <?php echo e($remainingPiutangFormatted); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($canUseAllTitipan): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>
                            <strong>Semua Piutang Dapat Ditutupi:</strong>
                            <p class="mb-0 mt-1">Titipan yang tersedia cukup untuk menutupi seluruh piutang. Tidak akan ada
                                sisa piutang.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            <strong>Ada Sisa Piutang:</strong>
                            <p class="mb-0 mt-1">Titipan yang tersedia tidak cukup untuk menutupi seluruh piutang. Akan ada
                                sisa piutang sebesar <?php echo e($remainingPiutangFormatted); ?>.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- FIXED ROUTE NAME HERE -->
                <form action="<?php echo e(route('transactions.use-titipan-for-piutang')); ?>" method="POST" class="mt-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="debtor_id" value="<?php echo e($request->debtor_id); ?>">
                    <input type="hidden" name="amount" value="<?php echo e($request->amount); ?>">
                    <input type="hidden" name="bagi_hasil" value="<?php echo e($request->bagi_hasil); ?>">
                    <input type="hidden" name="bagi_pokok" value="<?php echo e($request->bagi_pokok); ?>">
                    <input type="hidden" name="transaction_date" value="<?php echo e($request->transaction_date); ?>">
                    <input type="hidden" name="description" value="<?php echo e($request->description); ?>">

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('transactions.create')); ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Gunakan Titipan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/transactions/use_titipan_confirmation.blade.php ENDPATH**/ ?>