

<?php ($pageTitle = 'Tambah Transaksi'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Tambah Transaksi Baru</h1>
                <p class="text-muted">Tambahkan transaksi baru ke dalam sistem.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo e(route('transactions.store')); ?>" method="POST" id="transactionForm">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="debtor_id" class="form-label">Debitur</label>
                            <select class="form-select" id="debtor_id" name="debtor_id" required>
                                <option value="">Pilih Debitur</option>
                                <?php $__currentLoopData = $debtors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debtor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($debtor->id); ?>" data-balance="<?php echo e($debtor->current_balance); ?>"
                                        data-titipan="<?php echo e($debtor->total_titipan); ?>">
                                        <?php echo e($debtor->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['debtor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div id="debtorInfo" class="mt-2 small text-muted" style="display: none;">
                                <div>Saldo Saat Ini: <span id="currentBalance" class="fw-medium"></span></div>
                                <div>Total Titipan: <span id="totalTitipan" class="fw-medium"></span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">Jenis Transaksi</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Pilih Jenis</option>
                                <option value="piutang">Piutang</option>
                                <option value="pembayaran">Pembayaran</option>
                            </select>
                            <?php $__errorArgs = ['type'];
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
                            <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date"
                                value="<?php echo e(date('Y-m-d')); ?>" required>
                            <?php $__errorArgs = ['transaction_date'];
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
                            <label for="amount" class="form-label">Jumlah <span class="text-muted">(Otomatis
                                    dihitung)</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount" name="amount" placeholder="0"
                                    min="0" step="0.01" readonly>
                            </div>
                            <small class="text-muted">Jumlah akan otomatis dihitung dari Bagi Hasil + Bagi Pokok</small>
                            <?php $__errorArgs = ['amount'];
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
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Informasi Alokasi:</strong>
                                    <p class="mb-0 mt-1">Isi Bagi Hasil dan/atau Bagi Pokok. Jumlah transaksi akan otomatis
                                        dihitung dari penjumlahan keduanya.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="bagi_hasil" class="form-label">Bagi Hasil</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="bagi_hasil" name="bagi_hasil" placeholder="0"
                                    min="0" step="0.01">
                            </div>
                            <?php $__errorArgs = ['bagi_hasil'];
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
                            <label for="bagi_pokok" class="form-label">Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="bagi_pokok" name="bagi_pokok" placeholder="0"
                                    min="0" step="0.01">
                            </div>
                            <?php $__errorArgs = ['bagi_pokok'];
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
                        <div id="titipanSection" class="col-12" style="display: none;">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="bi bi-piggy-bank-fill me-2"></i>
                                <div>
                                    <strong>Titipan Tersedia!</strong>
                                    <p class="mb-0 mt-1">
                                        Debitur memiliki titipan sebesar <span id="availableTitipan"
                                            class="fw-medium"></span>.
                                        <button type="button" id="useTitipanBtn" class="btn btn-sm btn-primary ms-2">
                                            <i class="bi bi-arrow-repeat me-1"></i> Gunakan Titipan
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div id="paymentInfo" class="col-12" style="display: none;">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>
                                    <strong>Peringatan:</strong>
                                    <div class="mt-1">
                                        <div>Kelebihan Pembayaran: <span id="kelebihanBayar"
                                                class="fw-medium text-success"></span></div>
                                        <div class="text-info">Kelebihan pembayaran akan otomatis disimpan sebagai titipan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="description" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="description" name="description"
                                placeholder="Keterangan (opsional)">
                            <?php $__errorArgs = ['description'];
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
                            <div class="d-flex justify-content-end">
                                <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-secondary me-2">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
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
                const debtorSelect = document.getElementById('debtor_id');
                const typeSelect = document.getElementById('type');
                const amountInput = document.getElementById('amount');
                const bagiHasilInput = document.getElementById('bagi_hasil');
                const bagiPokokInput = document.getElementById('bagi_pokok');
                const debtorInfo = document.getElementById('debtorInfo');
                const currentBalanceSpan = document.getElementById('currentBalance');
                const totalTitipanSpan = document.getElementById('totalTitipan');
                const titipanSection = document.getElementById('titipanSection');
                const availableTitipanSpan = document.getElementById('availableTitipan');
                const useTitipanBtn = document.getElementById('useTitipanBtn');
                const paymentInfo = document.getElementById('paymentInfo');
                const kelebihanBayarSpan = document.getElementById('kelebihanBayar');
                const submitBtn = document.getElementById('submitBtn');
                const form = document.getElementById('transactionForm');

                // Format currency
                function formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount);
                }

                // Calculate amount from allocation
                function calculateAmount() {
                    const bagiHasil = parseFloat(bagiHasilInput.value) || 0;
                    const bagiPokok = parseFloat(bagiPokokInput.value) || 0;
                    const total = bagiHasil + bagiPokok;

                    amountInput.value = total.toFixed(2);
                    updatePaymentInfo();
                    checkTitipan();
                }

                // Show debtor info when selected
                debtorSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        const balance = parseFloat(selectedOption.dataset.balance);
                        const titipan = parseFloat(selectedOption.dataset.titipan);

                        currentBalanceSpan.textContent = formatCurrency(balance);
                        currentBalanceSpan.className = balance < 0 ? 'fw-medium text-danger' :
                            'fw-medium text-success';

                        totalTitipanSpan.textContent = formatCurrency(titipan);
                        debtorInfo.style.display = 'block';
                    } else {
                        debtorInfo.style.display = 'none';
                    }
                    updatePaymentInfo();
                    checkTitipan();
                });

                // Update payment info
                function updatePaymentInfo() {
                    if (typeSelect.value !== 'pembayaran' || !debtorSelect.value) {
                        paymentInfo.style.display = 'none';
                        return;
                    }

                    const selectedOption = debtorSelect.options[debtorSelect.selectedIndex];
                    const balance = parseFloat(selectedOption.dataset.balance);
                    const amount = parseFloat(amountInput.value) || 0;

                    if (balance < 0 && amount > Math.abs(balance)) {
                        const kelebihan = amount - Math.abs(balance);
                        kelebihanBayarSpan.textContent = formatCurrency(kelebihan);
                        paymentInfo.style.display = 'block';
                    } else {
                        paymentInfo.style.display = 'none';
                    }
                }

                // Check for available titipan and show option
                function checkTitipan() {
                    const selectedOption = debtorSelect.options[debtorSelect.selectedIndex];
                    if (selectedOption.value && typeSelect.value === 'piutang') {
                        const titipan = parseFloat(selectedOption.dataset.titipan);
                        const amount = parseFloat(amountInput.value) || 0;

                        if (titipan > 0 && amount > 0) {
                            availableTitipanSpan.textContent = formatCurrency(titipan);
                            titipanSection.style.display = 'block';
                        } else {
                            titipanSection.style.display = 'none';
                        }
                    } else {
                        titipanSection.style.display = 'none';
                    }
                }

                // Handle use titipan button click
                useTitipanBtn.addEventListener('click', function(e) {
                    e.preventDefault(); // Mencegah form submit utama

                    const debtorId = debtorSelect.value;
                    const amount = parseFloat(amountInput.value) || 0;
                    const bagiHasil = parseFloat(bagiHasilInput.value) || 0;
                    const bagiPokok = parseFloat(bagiPokokInput.value) || 0;
                    const transactionDate = document.getElementById('transaction_date').value;
                    const description = document.getElementById('description').value;

                    if (debtorId && amount > 0) {
                        // Debug: Cek nilai-nilai
                        console.log('debtorId:', debtorId);
                        console.log('amount:', amount);
                        console.log('bagiHasil:', bagiHasil);
                        console.log('bagiPokok:', bagiPokok);
                        console.log('transactionDate:', transactionDate);
                        console.log('description:', description);

                        // Coba dengan URL langsung dulu untuk debug
                        const url = "<?php echo e(route('transactions.create-with-titipan-confirmation')); ?>";
                        console.log('URL:', url);

                        // Buat parameter query string
                        const params = new URLSearchParams({
                            debtor_id: debtorId,
                            amount: amount,
                            bagi_hasil: bagiHasil,
                            bagi_pokok: bagiPokok,
                            transaction_date: transactionDate,
                            description: description
                        });

                        console.log('Params:', params.toString());

                        // Redirect dengan query string
                        window.location.href = url + '?' + params.toString();
                    } else {
                        alert('Pilih debitur dan masukkan jumlah piutang terlebih dahulu');
                    }
                });

                // Add event listeners for allocation inputs
                bagiHasilInput.addEventListener('input', calculateAmount);
                bagiPokokInput.addEventListener('input', calculateAmount);
                typeSelect.addEventListener('change', updatePaymentInfo);

                // Form submission validation
                form.addEventListener('submit', function(e) {
                    const amount = parseFloat(amountInput.value) || 0;

                    if (amount <= 0) {
                        e.preventDefault();
                        alert(
                            'Jumlah transaksi harus lebih dari 0. Silakan isi Bagi Hasil dan/atau Bagi Pokok.'
                        );
                    }
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/transactions/create.blade.php ENDPATH**/ ?>