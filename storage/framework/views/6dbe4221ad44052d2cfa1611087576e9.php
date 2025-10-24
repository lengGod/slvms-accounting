

<?php ($pageTitle = 'Tambah Debitur'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Tambah Debitur Baru</h1>
                <p class="text-muted">Tambahkan debitur baru ke dalam sistem.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('debtors.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo e(route('debtors.store')); ?>" method="POST" id="debtorForm">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?php echo e(old('name')); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="<?php echo e(old('phone')); ?>">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?php echo e(old('address')); ?></textarea>
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-4">
                            <label for="initial_balance" class="form-label">Saldo Awal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="initial_balance" name="initial_balance"
                                    value="<?php echo e(old('initial_balance', 0)); ?>" step="100">
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="balanceHint">Isi dengan nilai negatif jika debitur memiliki piutang, atau positif
                                    jika memiliki titipan</span>
                            </div>
                            <?php $__errorArgs = ['initial_balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Jenis saldo awal -->
                        <div class="col-md-4">
                            <label for="initial_balance_type" class="form-label">Jenis Saldo</label>
                            <select class="form-select" id="initial_balance_type" name="initial_balance_type" required>
                                <option value="">Pilih Jenis</option>
                                <option value="pokok" <?php echo e(old('initial_balance_type') == 'pokok' ? 'selected' : ''); ?>>Pokok
                                </option>
                                <option value="bagi_hasil"
                                    <?php echo e(old('initial_balance_type') == 'bagi_hasil' ? 'selected' : ''); ?>>Bagi Hasil</option>
                            </select>
                            <?php $__errorArgs = ['initial_balance_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-4">
                            <label for="joined_at" class="form-label">Tanggal Bergabung</label>
                            <input type="date" class="form-control" id="joined_at" name="joined_at"
                                value="<?php echo e(old('joined_at', now()->format('Y-m-d'))); ?>" required>
                            <?php $__errorArgs = ['joined_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-4">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="internal" <?php echo e(old('category') == 'internal' ? 'selected' : ''); ?>>Internal
                                </option>
                                <option value="eksternal" <?php echo e(old('category') == 'eksternal' ? 'selected' : ''); ?>>Eksternal
                                </option>
                            </select>
                            <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Preview informasi saldo awal -->
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center" role="alert" id="saldoPreview">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Preview Saldo Awal:</strong>
                                    <div class="mt-1">
                                        <div>Nilai: <span id="previewAmount" class="fw-medium">Rp 0</span></div>
                                        <div>Jenis: <span id="previewType" class="fw-medium">-</span></div>
                                        <div class="mt-2">
                                            <small id="previewInfo" class="text-muted">Silakan isi saldo awal dan
                                                jenisnya</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>
                                    <strong>Informasi Penting:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li><strong>Saldo awal negatif</strong> = Debitur memiliki piutang kepada kita</li>
                                        <li><strong>Saldo awal positif</strong> = Kita memiliki titipan dari debitur</li>
                                        <li><strong>Jenis saldo</strong> hanya sebagai label/informasi (Pokok atau Bagi
                                            Hasil)</li>
                                        <li><strong>Contoh</strong>: Saldo awal -2.000.000 (Pokok) = Debitur memiliki
                                            piutang pokok sebesar 2.000.000</li>
                                        <li><strong>Transaksi piutang</strong> akan menambah piutang (saldo lebih negatif)
                                        </li>
                                        <li><strong>Transaksi pembayaran</strong> akan mengurangi piutang (saldo kurang
                                            negatif atau positif)</li>
                                        <li><strong>Kelebihan pembayaran</strong> setelah lunas akan disimpan sebagai
                                            titipan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('debtors.index')); ?>" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const initialBalanceInput = document.getElementById('initial_balance');
                const initialBalanceTypeSelect = document.getElementById('initial_balance_type');
                const previewAmount = document.getElementById('previewAmount');
                const previewType = document.getElementById('previewType');
                const previewInfo = document.getElementById('previewInfo');
                const balanceHint = document.getElementById('balanceHint');
                const form = document.getElementById('debtorForm');

                // Format currency
                function formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount);
                }

                // Update preview
                function updatePreview() {
                    const amount = parseFloat(initialBalanceInput.value) || 0;
                    const type = initialBalanceTypeSelect.value;

                    previewAmount.textContent = formatCurrency(amount);

                    if (type) {
                        const typeName = type === 'pokok' ? 'Pokok' : 'Bagi Hasil';
                        previewType.textContent = typeName;

                        if (amount < 0) {
                            previewInfo.innerHTML =
                                `<i class="bi bi-arrow-down-circle-fill text-danger me-1"></i> Debitur memiliki piutang ${typeName} sebesar ${formatCurrency(Math.abs(amount))}`;
                        } else if (amount > 0) {
                            previewInfo.innerHTML =
                                `<i class="bi bi-arrow-up-circle-fill text-success me-1"></i> Kita memiliki titipan ${typeName} sebesar ${formatCurrency(amount)}`;
                        } else {
                            previewInfo.innerHTML =
                                '<i class="bi bi-dash-circle-fill text-secondary me-1"></i> Debitur tidak memiliki saldo awal';
                        }
                    } else {
                        previewType.textContent = '-';
                        previewInfo.innerHTML =
                            '<i class="bi bi-info-circle-fill text-info me-1"></i> Silakan pilih jenis saldo awal';
                    }
                }

                // Update balance hint based on amount
                function updateBalanceHint() {
                    const amount = parseFloat(initialBalanceInput.value) || 0;

                    if (amount < 0) {
                        balanceHint.innerHTML =
                            '<i class="bi bi-arrow-down-circle-fill text-danger me-1"></i>Nilai negatif untuk piutang awal';
                    } else if (amount > 0) {
                        balanceHint.innerHTML =
                            '<i class="bi bi-arrow-up-circle-fill text-success me-1"></i>Nilai positif untuk titipan awal';
                    } else {
                        balanceHint.innerHTML =
                            '<i class="bi bi-info-circle me-1"></i>Isi dengan nilai negatif jika debitur memiliki piutang, atau positif jika memiliki titipan';
                    }
                }

                // Form validation
                form.addEventListener('submit', function(e) {
                    const amount = parseFloat(initialBalanceInput.value) || 0;
                    const type = initialBalanceTypeSelect.value;

                    if (amount === 0 && !type) {
                        e.preventDefault();
                        alert(
                            'Jika saldo awal 0, jenis saldo awal tidak diperlukan. Atau jika ingin mengisi jenis, masukkan saldo awal terlebih dahulu.');
                    } else if (amount !== 0 && !type) {
                        e.preventDefault();
                        alert('Silakan pilih jenis saldo awal (Pokok atau Bagi Hasil).');
                    }
                });

                // Add event listeners
                initialBalanceInput.addEventListener('input', function() {
                    updatePreview();
                    updateBalanceHint();
                });
                initialBalanceTypeSelect.addEventListener('change', updatePreview);

                // Initialize preview
                updatePreview();
                updateBalanceHint();
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/debtors/create.blade.php ENDPATH**/ ?>