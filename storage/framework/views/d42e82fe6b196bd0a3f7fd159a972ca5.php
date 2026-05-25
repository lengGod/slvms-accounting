

<?php ($pageTitle = 'Pengaturan Aplikasi'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Pengaturan Aplikasi</h1>
                <p class="text-muted">Kelola pengaturan aplikasi.</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo e(route('settings.update-application')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="app_name" class="form-label">Nama Aplikasi</label>
                            <input type="text" class="form-control" id="app_name" name="app_name"
                                value="<?php echo e(old('app_name', $settings['app_name'])); ?>" required>
                            <?php $__errorArgs = ['app_name'];
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
                            <label for="currency" class="form-label">Mata Uang</label>
                            <select class="form-select" id="currency" name="currency" required>
                                <option value="IDR" <?php echo e($settings['currency'] == 'IDR' ? 'selected' : ''); ?>>IDR - Rupiah
                                </option>
                                <option value="USD" <?php echo e($settings['currency'] == 'USD' ? 'selected' : ''); ?>>USD - Dollar
                                </option>
                                <option value="EUR" <?php echo e($settings['currency'] == 'EUR' ? 'selected' : ''); ?>>EUR - Euro
                                </option>
                            </select>
                            <?php $__errorArgs = ['currency'];
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
                            <label for="app_description" class="form-label">Deskripsi Aplikasi</label>
                            <textarea class="form-control" id="app_description" name="app_description" rows="2"><?php echo e(old('app_description', $settings['app_description'])); ?></textarea>
                            <?php $__errorArgs = ['app_description'];
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
                            <label for="date_format" class="form-label">Format Tanggal</label>
                            <select class="form-select" id="date_format" name="date_format" required>
                                <option value="d-m-Y" <?php echo e($settings['date_format'] == 'd-m-Y' ? 'selected' : ''); ?>>
                                    DD-MM-YYYY</option>
                                <option value="m/d/Y" <?php echo e($settings['date_format'] == 'm/d/Y' ? 'selected' : ''); ?>>
                                    MM/DD/YYYY</option>
                                <option value="Y-m-d" <?php echo e($settings['date_format'] == 'Y-m-d' ? 'selected' : ''); ?>>
                                    YYYY-MM-DD</option>
                            </select>
                            <?php $__errorArgs = ['date_format'];
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
                            <label for="time_format" class="form-label">Format Waktu</label>
                            <select class="form-select" id="time_format" name="time_format" required>
                                <option value="H:i" <?php echo e($settings['time_format'] == 'H:i' ? 'selected' : ''); ?>>24 Jam
                                    (HH:MM)</option>
                                <option value="h:i A" <?php echo e($settings['time_format'] == 'h:i A' ? 'selected' : ''); ?>>12 Jam
                                    (HH:MM AM/PM)</option>
                            </select>
                            <?php $__errorArgs = ['time_format'];
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
                            <label for="app_logo" class="form-label">Logo Aplikasi</label>
                            <input type="file" class="form-control" id="app_logo" name="app_logo" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, GIF. Maksimal: 2MB</div>
                            <?php if($settings['app_logo']): ?>
                                <div class="mt-2">
                                    <img src="<?php echo e(asset('storage/' . $settings['app_logo'])); ?>" alt="Logo"
                                        height="40">
                                </div>
                            <?php endif; ?>
                            <?php $__errorArgs = ['app_logo'];
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
                            <label for="app_favicon" class="form-label">Favicon</label>
                            <input type="file" class="form-control" id="app_favicon" name="app_favicon" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, GIF, ICO. Maksimal: 1MB</div>
                            <?php $__errorArgs = ['app_favicon'];
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
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/settings/application.blade.php ENDPATH**/ ?>