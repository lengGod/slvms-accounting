<?php if(session('success') || session('error')): ?>
    <!-- Notification Container -->
    <div id="flashNotification" class="position-fixed top-0 end-0 p-3" style="z-index: 1080; width: 350px;">
        <div class="card shadow-lg border-0 overflow-hidden notification-card <?php echo e(session('success') ? 'success' : 'danger'); ?>" 
             style="opacity: 0; transform: translateY(-20px) scale(0.95); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            
            <!-- Progress Bar -->
            <div class="position-absolute top-0 start-0 w-100" style="height: 3px; background: rgba(0,0,0,0.05);">
                <div id="notificationProgress" class="h-100 <?php echo e(session('success') ? 'bg-success' : 'bg-danger'); ?>" 
                     style="width: 100%; transition: width 5s linear;"></div>
            </div>

            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <?php if(session('success')): ?>
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 animate-bounce">
                                <i class="bi bi-check-lg text-success fs-4"></i>
                            </div>
                        <?php else: ?>
                            <div class="bg-danger bg-opacity-10 rounded-circle p-2 animate-shake">
                                <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 fw-bold <?php echo e(session('success') ? 'text-success' : 'text-danger'); ?>">
                            <?php echo e(session('success') ? 'Berhasil!' : 'Terjadi Kesalahan'); ?>

                        </h6>
                        <div class="small text-muted text-truncate" style="max-width: 220px;">
                            <?php echo e(session('success') ?? session('error')); ?>

                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto small" onclick="closeNotification()" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .notification-card.success { border-left: 5px solid #198754; }
        .notification-card.danger { border-left: 5px solid #dc3545; }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-5px);}
            60% {transform: translateY(-3px);}
        }
        .animate-bounce { animation: bounce 1s infinite; }

        @keyframes shake {
            0%, 100% {transform: translateX(0);}
            25% {transform: translateX(-3px);}
            75% {transform: translateX(3px);}
        }
        .animate-shake { animation: shake 0.5s infinite; }
    </style>

    <script>
        (function() {
            let autoHideTimeout;
            
            function showNotification() {
                const container = document.getElementById('flashNotification');
                if (!container) return;
                
                const card = container.querySelector('.notification-card');
                const progress = document.getElementById('notificationProgress');
                
                if (card) {
                    // Start Animation
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0) scale(1)';
                        
                        // Start Progress Bar
                        if (progress) {
                            setTimeout(() => {
                                progress.style.width = '0%';
                            }, 50);
                        }
                    }, 100);

                    // Auto hide after 5 seconds
                    autoHideTimeout = setTimeout(() => {
                        closeNotification();
                    }, 5000);
                }
            }

            // Run on Load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showNotification);
            } else {
                showNotification();
            }

            // Run on Turbo Navigation
            document.addEventListener('turbo:load', showNotification);
        })();

        function closeNotification() {
            const container = document.getElementById('flashNotification');
            if (container) {
                const card = container.querySelector('.notification-card');
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(-20px) scale(0.95)';
                    
                    setTimeout(() => {
                        if (container.parentNode) {
                            container.remove();
                        }
                    }, 400);
                }
            }
        }
    </script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/partials/alerts.blade.php ENDPATH**/ ?>