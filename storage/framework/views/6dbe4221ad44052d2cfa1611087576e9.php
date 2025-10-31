

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

                        <div class="col-md-6">
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

                        <div class="col-md-6">
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

                        <!-- Jenis Saldo Awal -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Jenis Saldo Awal</label>
                            <div class="border rounded p-3 bg-light">
                                <div class="form-check">
                                    <input class="form-check-input balance-type-check" type="checkbox" id="type_pokok"
                                        name="balance_type[]" value="pokok"
                                        <?php echo e(in_array('pokok', old('balance_type', [])) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="type_pokok">
                                        <strong>Pokok</strong>
                                        <small class="text-muted d-block">Dana pokok dari debitur</small>
                                    </label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input balance-type-check" type="checkbox" id="type_bagi_hasil"
                                        name="balance_type[]" value="bagi_hasil"
                                        <?php echo e(in_array('bagi_hasil', old('balance_type', [])) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="type_bagi_hasil">
                                        <strong>Bagi Hasil</strong>
                                        <small class="text-muted d-block">Keuntungan/bagi hasil dari debitur</small>
                                    </label>
                                </div>
                            </div>
                            <div id="balanceTypeError" class="text-danger small mt-2" style="display:none;">
                                Pilih minimal satu jenis saldo awal
                            </div>
                            <?php $__errorArgs = ['balance_type'];
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

                        <!-- Input saldo awal per jenis (akan muncul dinamis) -->
                        <div class="col-12" id="balanceInputsContainer">
                            <!-- Input untuk pokok -->
                            <div class="row mb-3" id="pokokInputContainer" style="display: none;">
                                <div class="col-md-6">
                                    <label for="pokok_balance" class="form-label">Saldo Awal Pokok</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="pokok_balance" name="pokok_balance"
                                            value="<?php echo e(old('pokok_balance')); ?>" step="100">
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        <span id="pokokBalanceHint">Nilai negatif untuk piutang, positif untuk
                                            titipan</span>
                                    </div>
                                    <?php $__errorArgs = ['pokok_balance'];
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
                            </div>

                            <!-- Input untuk bagi hasil -->
                            <div class="row mb-3" id="bagiHasilInputContainer" style="display: none;">
                                <div class="col-md-6">
                                    <label for="bagi_hasil_balance" class="form-label">Saldo Awal Bagi Hasil</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="bagi_hasil_balance"
                                            name="bagi_hasil_balance" value="<?php echo e(old('bagi_hasil_balance')); ?>"
                                            step="100">
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        <span id="bagiHasilBalanceHint">Nilai negatif untuk piutang, positif untuk
                                            titipan</span>
                                    </div>
                                    <?php $__errorArgs = ['bagi_hasil_balance'];
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
                            </div>
                        </div>

                        <!-- Input hidden untuk total saldo awal dan jenis saldo -->
                        <input type="hidden" id="initial_balance" name="initial_balance"
                            value="<?php echo e(old('initial_balance', 0)); ?>">
                        <input type="hidden" id="initial_balance_type" name="initial_balance_type"
                            value="<?php echo e(old('initial_balance_type', '')); ?>">

                        <!-- Preview informasi saldo awal -->
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-start" role="alert" id="saldoPreview">
                                <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                                <div class="w-100">
                                    <strong>Preview Saldo Awal:</strong>
                                    <div class="mt-2 ms-3">
                                        <div class="mb-2">Total: <span id="previewAmount"
                                                class="fw-medium badge bg-primary">Rp 0</span></div>
                                        <div class="mb-2">Jenis: <span id="previewType"
                                                class="fw-medium badge bg-secondary">-</span></div>
                                        <div>
                                            <small id="previewInfo" class="text-muted">Silakan pilih jenis saldo dan isi
                                                nominal</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-warning d-flex align-items-start" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                                <div>
                                    <strong>Informasi Penting:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><strong>Saldo awal negatif</strong> = Debitur memiliki piutang kepada kita</li>
                                        <li><strong>Saldo awal positif</strong> = Kita memiliki titipan dari debitur</li>
                                        <li><strong>Pilih jenis saldo</strong>: Bisa Pokok saja, Bagi Hasil saja, atau
                                            keduanya</li>
                                        <li><strong>Contoh 1</strong>: Saldo -1.000.000, Pokok saja = Piutang pokok
                                            1.000.000</li>
                                        <li><strong>Contoh 2</strong>: Saldo -1.000.000, Pokok + Bagi Hasil = Piutang pokok
                                            500.000 + Bagi hasil 500.000</li>
                                        <li><strong>Transaksi piutang</strong> akan menambah piutang sesuai alokasi
                                            pokok/bagi hasil</li>
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
                const balanceTypeChecks = document.querySelectorAll('.balance-type-check');
                const pokokInputContainer = document.getElementById('pokokInputContainer');
                const bagiHasilInputContainer = document.getElementById('bagiHasilInputContainer');
                const pokokBalanceInput = document.getElementById('pokok_balance');
                const bagiHasilBalanceInput = document.getElementById('bagi_hasil_balance');
                const initialBalanceInput = document.getElementById('initial_balance');
                const initialBalanceTypeInput = document.getElementById('initial_balance_type');
                const balanceTypeError = document.getElementById('balanceTypeError');
                const form = document.getElementById('debtorForm');

                // Preview elements
                const previewAmount = document.getElementById('previewAmount');
                const previewType = document.getElementById('previewType');
                const previewInfo = document.getElementById('previewInfo');

                // Fungsi untuk menampilkan/menyembunyikan input berdasarkan checkbox
                function toggleBalanceInputs() {
                    const pokokChecked = document.getElementById('type_pokok').checked;
                    const bagiHasilChecked = document.getElementById('type_bagi_hasil').checked;

                    pokokInputContainer.style.display = pokokChecked ? 'block' : 'none';
                    bagiHasilInputContainer.style.display = bagiHasilChecked ? 'block' : 'none';

                    // Jika tidak ada yang dipilih, sembunyikan error
                    if (!pokokChecked && !bagiHasilChecked) {
                        balanceTypeError.style.display = 'none';
                    }

                    updatePreview();
                }

                // Fungsi untuk mengupdate preview saldo
                function updatePreview() {
                    const pokokChecked = document.getElementById('type_pokok').checked;
                    const bagiHasilChecked = document.getElementById('type_bagi_hasil').checked;

                    const pokokAmount = parseFloat(pokokBalanceInput.value) || 0;
                    const bagiHasilAmount = parseFloat(bagiHasilBalanceInput.value) || 0;

                    const totalAmount = pokokAmount + bagiHasilAmount;
                    const types = [];

                    if (pokokChecked) types.push('pokok');
                    if (bagiHasilChecked) types.push('bagi_hasil');

                    // Update hidden fields
                    initialBalanceInput.value = totalAmount;
                    initialBalanceTypeInput.value = types.join(',');

                    // Update preview
                    previewAmount.textContent = formatCurrency(totalAmount);

                    if (types.length === 0) {
                        previewType.textContent = '-';
                        previewInfo.innerHTML =
                            '<i class="bi bi-dash-circle-fill text-secondary me-1"></i>Silakan pilih jenis saldo awal';
                        return;
                    }

                    const typeNames = types.map(t => t === 'pokok' ? 'Pokok' : 'Bagi Hasil').join(' + ');
                    previewType.textContent = typeNames;

                    if (totalAmount === 0) {
                        previewInfo.innerHTML =
                            '<i class="bi bi-dash-circle-fill text-secondary me-1"></i>Debitur tidak memiliki saldo awal';
                        return;
                    }

                    if (types.length === 2) {
                        if (totalAmount < 0) {
                            previewInfo.innerHTML =
                                `<i class="bi bi-arrow-down-circle-fill text-danger me-1"></i>
                                Piutang: Pokok ${formatCurrency(pokokAmount)} + Bagi Hasil ${formatCurrency(bagiHasilAmount)}`;
                        } else {
                            previewInfo.innerHTML =
                                `<i class="bi bi-arrow-up-circle-fill text-success me-1"></i>
                                Titipan: Pokok ${formatCurrency(pokokAmount)} + Bagi Hasil ${formatCurrency(bagiHasilAmount)}`;
                        }
                    } else {
                        if (totalAmount < 0) {
                            previewInfo.innerHTML =
                            `<i class="bi bi-arrow-down-circle-fill text-danger me-1"></i>
                                Debitur memiliki piutang ${typeNames} sebesar ${formatCurrency(totalAmount)}`;
                        } else {
                            previewInfo.innerHTML = `<i class="bi bi-arrow-up-circle-fill text-success me-1"></i>
                                Kita memiliki titipan ${typeNames} sebesar ${formatCurrency(totalAmount)}`;
                        }
                    }
                }

                // Fungsi untuk format currency
                function formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount);
                }

                // Event listener untuk checkbox jenis saldo
                balanceTypeChecks.forEach(checkbox => {
                    checkbox.addEventListener('change', toggleBalanceInputs);
                });

                // Event listener untuk input saldo
                pokokBalanceInput.addEventListener('input', updatePreview);
                bagiHasilBalanceInput.addEventListener('input', updatePreview);

                // Validasi form saat submit
                form.addEventListener('submit', function(e) {
                    const pokokChecked = document.getElementById('type_pokok').checked;
                    const bagiHasilChecked = document.getElementById('type_bagi_hasil').checked;

                    if (!pokokChecked && !bagiHasilChecked) {
                        e.preventDefault();
                        balanceTypeError.style.display = 'block';
                        alert('Silakan pilih minimal satu jenis saldo awal (Pokok atau Bagi Hasil).');
                        return;
                    }

                    // Validasi input saldo yang muncul
                    if (pokokChecked && !pokokBalanceInput.value) {
                        e.preventDefault();
                        alert('Silakan isi saldo awal untuk Pokok.');
                        return;
                    }

                    if (bagiHasilChecked && !bagiHasilBalanceInput.value) {
                        e.preventDefault();
                        alert('Silakan isi saldo awal untuk Bagi Hasil.');
                        return;
                    }
                });

                // Inisialisasi tampilan
                toggleBalanceInputs();
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/debtors/create.blade.php ENDPATH**/ ?>