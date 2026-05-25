<?php $__env->startSection('title', __('Forbidden')); ?>
<?php $__env->startSection('code', '403'); ?>
<?php $__env->startSection('message', __(isset($exception) ? $exception->getMessage() : 'Akses Dilarang')); ?>
<?php $__env->startSection('description', 'Anda tidak memiliki izin untuk mengakses sumber daya ini.'); ?>

<?php echo $__env->make('errors::layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/errors/403.blade.php ENDPATH**/ ?>