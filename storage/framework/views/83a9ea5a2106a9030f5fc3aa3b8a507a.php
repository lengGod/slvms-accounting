<?php if(session('success') || session('error')): ?>
    <!-- Notification -->
    <div id="flashNotification" class="position-fixed top-0 end-0 p-3"
        style="z-index: 1055; transform: translateX(120%); transition: transform 0.3s ease-in-out;">
        <div
            class="card shadow-lg border-0 <?php echo e(session('success') ? 'border-start border-4 border-success' : 'border-start border-4 border-danger'); ?>">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <?php if(session('success')): ?>
                            <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                <i class="bi bi-check-lg text-success fs-4"></i>
                            </div>
                        <?php else: ?>
                            <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 <?php echo e(session('success') ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e(session('success') ? 'Berhasil' : 'Gagal'); ?>

                        </h6>
                        <div class="small text-muted">
                            <?php echo e(session('success') ?? session('error')); ?>

                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto" onclick="closeNotification()"></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show notification with slide-in animation
            setTimeout(function() {
                const notification = document.getElementById('flashNotification');
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Auto hide after 5 seconds
            setTimeout(function() {
                closeNotification();
            }, 5000);
        });

        function closeNotification() {
            const notification = document.getElementById('flashNotification');
            notification.style.transform = 'translateX(120%)';

            // Remove from DOM after animation completes
            setTimeout(function() {
                notification.remove();
            }, 300);
        }
    </script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/partials/alerts.blade.php ENDPATH**/ ?>