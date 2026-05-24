<?php $__env->startSection('title', __('Server Error')); ?>
<?php $__env->startSection('code', '500'); ?>
<?php $__env->startSection('message', __('Kesalahan Server Internal')); ?>
<?php $__env->startSection('description', 'Terjadi kesalahan tak terduga di server. Kami sedang berupaya memperbaikinya.'); ?>

<?php echo $__env->make('errors::layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/errors/500.blade.php ENDPATH**/ ?>