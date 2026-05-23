<?php if($errors->any()): ?>
    <!-- Modal -->
    <div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="validationModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Kesalahan Validasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold text-muted">Mohon perbaiki kesalahan berikut:</p>
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="text-danger"><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modalElement = document.getElementById('validationModal');

            function cleanupBackdrop() {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }

            function handleValidationModal() {
                if (modalElement && window.bootstrap) {
                    const existingModal = bootstrap.Modal.getInstance(modalElement);
                    const validationModal = existingModal || new bootstrap.Modal(modalElement, {
                        backdrop: true,
                        keyboard: true
                    });
                    
                    validationModal.show();
                }
            }

            // Cleanup ketika modal benar-benar tertutup
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', cleanupBackdrop);
            }

            // Jalankan saat turbo load
            document.addEventListener('turbo:load', handleValidationModal);
            
            // Bersihkan backdrop SAAT AKAN berpindah halaman
            document.addEventListener('turbo:before-cache', function() {
                const instance = window.bootstrap ? bootstrap.Modal.getInstance(modalElement) : null;
                if (instance) instance.hide();
                cleanupBackdrop();
            });

            // Jalankan jika sudah ready
            if (document.readyState !== 'loading') {
                handleValidationModal();
            }
        })();
    </script>
<?php endif; ?><?php /**PATH C:\laragon\www\slv-acounting\resources\views/partials/validation-modal.blade.php ENDPATH**/ ?>