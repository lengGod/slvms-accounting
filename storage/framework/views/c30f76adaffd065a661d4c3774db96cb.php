

<?php ($pageTitle = 'Log Aktivitas'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Log Aktivitas</h1>
                <p class="text-muted">Lihat log aktivitas pengguna.</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo e(route('settings.activity-logs')); ?>" method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <select name="user_id" class="form-select">
                            <option value="">Semua Pengguna</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                    <?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="action" class="form-select">
                            <option value="">Semua Aksi</option>
                            <option value="create" <?php echo e(request('action') == 'create' ? 'selected' : ''); ?>>Membuat</option>
                            <option value="update" <?php echo e(request('action') == 'update' ? 'selected' : ''); ?>>Memperbarui
                            </option>
                            <option value="delete" <?php echo e(request('action') == 'delete' ? 'selected' : ''); ?>>Menghapus</option>
                            <option value="login" <?php echo e(request('action') == 'login' ? 'selected' : ''); ?>>Login</option>
                            <option value="logout" <?php echo e(request('action') == 'logout' ? 'selected' : ''); ?>>Logout</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Target</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($log->created_at->format('d M Y H:i:s')); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="p-2 rounded-circle bg-light me-2">
                                                    <i class="bi bi-person text-secondary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium"><?php echo e($log->user->name); ?></div>
                                                <div class="small text-muted"><?php echo e(ucfirst($log->user->role)); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                        <?php if($log->action == 'create'): ?> bg-success
                                        <?php elseif($log->action == 'update'): ?> bg-warning
                                        <?php elseif($log->action == 'delete'): ?> bg-danger
                                        <?php elseif($log->action == 'login'): ?> bg-info
                                        <?php elseif($log->action == 'logout'): ?> bg-secondary <?php endif; ?>">
                                            <?php echo e(ucfirst($log->action)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($log->target_type && $log->target_id): ?>
                                            <?php echo e($log->target_type); ?> #<?php echo e($log->target_id); ?>

                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($log->description); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada data log aktivitas</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan <?php echo e($logs->firstItem()); ?> - <?php echo e($logs->lastItem()); ?> dari <?php echo e($logs->total()); ?>

                        data
                    </div>
                    <?php echo e($logs->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/settings/activity_logs.blade.php ENDPATH**/ ?>