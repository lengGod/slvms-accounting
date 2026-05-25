<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <title>SLVMS</title>
    <!-- Vite for CSS and JS (Turbo included in app.js) -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        body {
            margin: 0;
            overflow-x: hidden;
        }

        aside {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1000;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            transition: transform 0.3s ease;
        }

        header {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 64px;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            transition: left 0.3s ease;
        }

        .brand-header {
            height: 64px;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        main {
            margin-left: 250px;
            margin-top: 64px;
            min-height: calc(100vh - 64px);
            overflow-y: auto;
            padding: 1rem 2rem;
            transition: margin-left 0.3s ease;
        }

        .avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        /* CSS untuk media print */
        @media print {
            /* ... kode print tetap sama ... */
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            aside {
                transform: translateX(-100%);
            }

            aside.show {
                transform: translateX(0);
            }

            header {
                left: 0;
            }

            main {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        @media (max-width: 576px) {
            main {
                padding: 1rem;
            }

            header {
                padding: 0 1rem;
            }
        }

        /* Toggle button styles */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #495057;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main>
        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.confirm-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.validation-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Fungsi JavaScript Global -->
    <script>
        // Global initialization to handle Turbo and Bootstrap Modals
        (function() {
            if (window.appScriptsInitialized) return;

            // Sidebar Toggle & Close Logic (Delegated)
            document.addEventListener('click', function(event) {
                const sidebarToggle = event.target.closest('#sidebar-toggle');
                const sidebarClose = event.target.closest('.sidebar-close');
                const sidebar = document.querySelector('aside');
                
                if (sidebarToggle && sidebar) {
                    sidebar.classList.toggle('show');
                    return;
                }

                if (sidebarClose && sidebar) {
                    sidebar.classList.remove('show');
                    return;
                }

                if (sidebar && sidebar.classList.contains('show')) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnToggle = event.target.closest('#sidebar-toggle');

                    if (!isClickInsideSidebar && !isClickOnToggle) {
                        sidebar.classList.remove('show');
                    }
                }
            });

            // Profile Dropdown Logic (Delegated)
            document.addEventListener('click', function(event) {
                const profileDropdown = event.target.closest('#profileDropdown');
                const profileMenu = document.getElementById('profileMenu');
                const dropdownIcon = profileDropdown ? profileDropdown.querySelector('.dropdown-icon') : null;

                if (profileDropdown && profileMenu) {
                    event.stopPropagation();
                    profileMenu.classList.toggle('show');
                    if (dropdownIcon) dropdownIcon.classList.toggle('rotate');
                    return;
                }

                // Close profile dropdown when clicking outside
                const activeProfileMenu = document.querySelector('.profile-dropdown-menu.show');
                if (activeProfileMenu && !event.target.closest('.profile-dropdown-menu')) {
                    activeProfileMenu.classList.remove('show');
                    const activeIcon = document.querySelector('.dropdown-icon.rotate');
                    if (activeIcon) activeIcon.classList.remove('rotate');
                }
            });

            // Delete Button Delegation (Confirm Modal)
            document.addEventListener('click', function(event) {
                const button = event.target.closest('.delete-btn');
                if (!button) return;

                event.preventDefault();
                const modalElement = document.getElementById('confirmModal');
                const confirmModalForm = document.getElementById('confirmModalForm');
                
                if (modalElement && confirmModalForm && window.bootstrap) {
                    const action = button.getAttribute('data-action');
                    confirmModalForm.setAttribute('action', action);
                    
                    const confirmModal = bootstrap.Modal.getOrCreateInstance(modalElement);
                    confirmModal.show();
                }
            });

            // Validation Modal Auto-show & Sidebar Dropdown Sync
            document.addEventListener('turbo:load', function() {
                // Auto-show validation modal
                const valModalElement = document.getElementById('validationModal');
                if (valModalElement && window.bootstrap) {
                    const validationModal = bootstrap.Modal.getOrCreateInstance(valModalElement);
                    validationModal.show();
                }

                // Auto-expand Sidebar Dropdowns if they contain active items
                document.querySelectorAll('.nav-item .dropdown-menu').forEach(menu => {
                    if (menu.querySelector('.dropdown-item.active')) {
                        const toggle = menu.previousElementSibling;
                        if (toggle && toggle.classList.contains('dropdown-toggle')) {
                            toggle.setAttribute('aria-expanded', 'true');
                            menu.classList.add('show');
                        }
                    }
                });
            });

            // Turbo Cleanup (Before Cache)
            document.addEventListener('turbo:before-cache', function() {
                // Hide any open modals before caching
                document.querySelectorAll('.modal.show').forEach(modalEl => {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                });

                // Ensure backdrops are removed
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });

            // Extra safety for backdrop cleanup
            document.addEventListener('hidden.bs.modal', function() {
                if (document.querySelectorAll('.modal.show').length === 0) {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }
            });

            window.appScriptsInitialized = true;
        })();

        // Function for printing content
        function printContent() {
            const content = document.getElementById('print-content').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Laporan Cetak</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 20px; 
                            color: black;
                            background: white;
                        }
                        .table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin-bottom: 20px; 
                        }
                        .table th, .table td { 
                            border: 1px solid #ddd; 
                            padding: 8px; 
                            text-align: left; 
                        }
                        .table th { 
                            background-color: #f2f2f2; 
                            font-weight: bold;
                        }
                        .text-end { text-align: right; }
                        .text-center { text-align: center; }
                        .fw-bold { font-weight: bold; }
                        .mb-4 { margin-bottom: 20px; }
                        .card { 
                            margin-bottom: 20px; 
                            border: 1px solid #ddd; 
                        }
                        .card-header { 
                            background-color: #f2f2f2; 
                            padding: 10px; 
                            font-weight: bold; 
                            border-bottom: 1px solid #ddd;
                        }
                        .card-body { padding: 15px; }
                        .progress { 
                            background-color: #e9ecef; 
                            height: 10px; 
                            margin-bottom: 5px; 
                        }
                        .progress-bar { 
                            background-color: #0d6efd; 
                            height: 100%; 
                        }
                        .badge { 
                            border: 1px solid #ddd; 
                            padding: 3px 8px; 
                            border-radius: 10px; 
                        }
                        .text-danger { color: #dc3545 !important; }
                        .text-muted { color: #6c757d !important; }
                        .small { font-size: 0.875em; }
                        .container-fluid { padding: 0; }
                        .d-flex { display: flex; }
                        .align-items-center { align-items: center; }
                        .gap-2 { gap: 0.5rem; }
                        .rounded-circle { border-radius: 50%; }
                        .bg-light { background-color: #f8f9fa; }
                        .p-2 { padding: 0.5rem; }
                        .me-2 { margin-right: 0.5rem; }
                        .bi { font-family: bootstrap-icons !important; }
                        .flex-shrink-0 { flex-shrink: 0; }
                        .fw-medium { font-weight: 500; }
                    </style>
                </head>
                <body>
                    <div class="print-area">
                        ${content}
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
        }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/layouts/app.blade.php ENDPATH**/ ?>