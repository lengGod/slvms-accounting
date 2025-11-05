

<?php ($pageTitle = 'Manajemen Pengguna'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Manajemen Pengguna</h1>
                <p class="text-muted">Kelola semua pengguna aplikasi.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Pengguna
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>No. Telepon</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="p-2 rounded-circle bg-light me-2">
                                                    <i class="bi bi-person text-secondary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium"><?php echo e($user->name); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($user->role == 'admin' ? 'danger' : 'info'); ?>">
                                            <?php echo e(ucfirst($user->role)); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($user->phone ?: '-'); ?></td>
                                    <td><?php echo e($user->created_at->format('d M Y')); ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo e(route('users.show', $user)); ?>"
                                                class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('users.edit', $user)); ?>"
                                                class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                data-action="<?php echo e(route('users.destroy', $user)); ?>" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada data pengguna</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan <?php echo e($users->firstItem()); ?> - <?php echo e($users->lastItem()); ?> dari <?php echo e($users->total()); ?>

                        data
                    </div>
                    <?php echo e($users->links()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/settings/users/index.blade.php ENDPATH**/ ?>