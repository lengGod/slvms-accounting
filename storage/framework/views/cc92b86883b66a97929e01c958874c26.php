<aside>
    <div class="brand-header">
        <div class="bg-primary text-white p-2 rounded">
            <i class="bi bi-credit-card"></i>
        </div>
        <h1 class="h5 mb-0 text-dark">SLVMS</h1>
        <button class="sidebar-close btn d-md-none ms-auto">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="px-3 py-4">
        <ul class="nav flex-column gap-2">
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->is('dashboard') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark'); ?>"
                    href="<?php echo e(route('dashboard')); ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <?php if(auth()->check() && auth()->user()->role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('transactions*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark'); ?>"
                        href="<?php echo e(route('transactions.index')); ?>">
                        <i class="bi bi-cash-stack me-2"></i> Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('debtors*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark'); ?>"
                        href="<?php echo e(route('debtors.index')); ?>">
                        <i class="bi bi-people me-2"></i> Debitur
                    </a>
                </li>
            <?php endif; ?>

            <?php if(auth()->check() && in_array(auth()->user()->role, ['admin', 'accounting'])): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('titipans*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark'); ?>"
                        href="<?php echo e(route('titipans.index')); ?>">
                        <i class="bi bi-wallet2 me-2"></i> Titipan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('journal*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark'); ?>"
                        href="<?php echo e(route('journal.index')); ?>">
                        <i class="bi bi-journal-text me-2"></i> Journal
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo e(request()->is('reports*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark'); ?>"
                        href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-bar-chart me-2"></i>
                        <span>Laporan</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="laporanDropdown">
                        <li>
                            <a class="dropdown-item <?php echo e(request()->is('reports/kartu-mutasi*') ? 'active' : ''); ?>"
                                href="<?php echo e(route('reports.kartu-mutasi')); ?>">
                                <i class="bi bi-card-text me-2"></i> Kartu Mutasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo e(request()->is('reports/piutang-perbulan*') ? 'active' : ''); ?>"
                                href="<?php echo e(route('reports.piutang-perbulan')); ?>">
                                <i class="bi bi-calendar-month me-2"></i> Piutang
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo e(request()->is('reports/pembayaran-perbulan*') ? 'active' : ''); ?>"
                                href="<?php echo e(route('reports.pembayaran-perbulan')); ?>">
                                <i class="bi bi-cash-coin me-2"></i> Pembayaran
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo e(request()->is('reports/debit-piutang*') ? 'active' : ''); ?>"
                                href="<?php echo e(route('reports.debit-piutang')); ?>">
                                <i class="bi bi-journal-text me-2"></i> Debit Piutang
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if(auth()->check() && auth()->user()->role === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->is('users*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark'); ?>"
                        href="<?php echo e(route('users.index')); ?>">
                        <i class="bi bi-people me-2"></i> Pengguna
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link dropdown-toggle <?php echo e(request()->is('settings*') && !request()->is('users*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark'); ?>"
                    href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear me-2"></i>
                    <span>Pengaturan</span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                    <li>
                        <a class="dropdown-item <?php echo e(request()->is('settings/profile') ? 'active' : ''); ?>"
                            href="<?php echo e(route('settings.profile')); ?>">
                            <i class="bi bi-person me-2"></i> Profil Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?php echo e(request()->is('settings/password') ? 'active' : ''); ?>"
                            href="<?php echo e(route('settings.password')); ?>">
                            <i class="bi bi-shield-lock me-2"></i> Ubah Password
                        </a>
                    </li>
                    <?php if(auth()->check() && auth()->user()->role === 'admin'): ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo e(request()->is('settings/application') ? 'active' : ''); ?>"
                                href="<?php echo e(route('settings.application')); ?>">
                                <i class="bi bi-app me-2"></i> Pengaturan Aplikasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo e(request()->is('settings/backup') ? 'active' : ''); ?>"
                                href="<?php echo e(route('settings.backup')); ?>">
                                <i class="bi bi-database me-2"></i> Backup & Restore
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo e(request()->is('settings/activity-logs') ? 'active' : ''); ?>"
                                href="<?php echo e(route('settings.activity-logs')); ?>">
                                <i class="bi bi-activity me-2"></i> Log Aktivitas
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer border-top px-3 py-4 mt-auto">
        <a class="nav-link text-dark logout-link" href="<?php echo e(route('logout')); ?>"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right me-2"></i> Keluar
        </a>
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
            <?php echo csrf_field(); ?>
        </form>
    </div>

    <style>
        /* Sidebar structure */
        aside {
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #fff;
            z-index: 1000;
        }

        aside nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom scrollbar */
        aside nav::-webkit-scrollbar {
            width: 6px;
        }

        aside nav::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        aside nav::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        aside nav::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Nav link styles */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.625rem 0.75rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            text-decoration: none;
            color: var(--bs-dark);
        }

        .nav-link:hover {
            background-color: rgba(13, 110, 253, 0.05);
            color: var(--bs-primary);
        }

        .nav-link.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--bs-primary);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Dropdown styles */
        .dropdown-toggle {
            display: flex;
            align-items: center;
            position: relative;
            padding-right: 2rem;
        }

        .dropdown-toggle::after {
            content: '\F282';
            font-family: 'bootstrap-icons';
            border: none;
            position: absolute;
            right: 0.75rem;
            transition: transform 0.3s ease;
            font-size: 0.75rem;
        }

        .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        /* Dropdown menu */
        .dropdown-menu {
            position: static !important;
            transform: none !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0.25rem 0 0 0 !important;
            margin: 0 !important;
            background-color: transparent !important;
            width: 100% !important;
        }

        .dropdown-item {
            padding: 0.5rem 0.75rem 0.5rem 2.5rem !important;
            margin: 0.125rem 0 !important;
            border-radius: 0.375rem !important;
            color: var(--bs-dark) !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
            background-color: transparent !important;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: rgba(13, 110, 253, 0.05) !important;
            color: var(--bs-primary) !important;
            padding-left: 2.75rem !important;
        }

        .dropdown-item.active {
            background-color: rgba(13, 110, 253, 0.1) !important;
            color: var(--bs-primary) !important;
            font-weight: 500 !important;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            opacity: 0.1;
        }

        /* Dropdown animation */
        .dropdown-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-menu.show .dropdown-item {
            animation: slideIn 0.3s ease forwards;
            opacity: 0;
        }

        .dropdown-menu.show .dropdown-item:nth-child(1) {
            animation-delay: 0.05s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(2) {
            animation-delay: 0.1s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(3) {
            animation-delay: 0.15s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(4) {
            animation-delay: 0.2s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(5) {
            animation-delay: 0.25s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(6) {
            animation-delay: 0.3s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(7) {
            animation-delay: 0.35s;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Sidebar footer */
        .sidebar-footer {
            background-color: #fff;
            margin-top: auto;
        }

        .logout-link:hover {
            color: #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.05);
        }

        /* Brand header */
        .brand-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            aside {
                position: fixed;
                left: -100%;
                top: 0;
                z-index: 1050;
                width: 280px;
                background: white;
                transition: left 0.3s ease;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }

            aside.show {
                left: 0;
            }

            .sidebar-close {
                display: block !important;
            }
        }

        @media (min-width: 769px) {
            .sidebar-close {
                display: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap dropdowns
            const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');

            // Check and open dropdown if it contains active item
            const laporanDropdown = document.getElementById('laporanDropdown');
            if (laporanDropdown) {
                const laporanMenu = laporanDropdown.nextElementSibling;
                const hasActiveItem = laporanMenu.querySelector('.dropdown-item.active');

                if (hasActiveItem) {
                    laporanDropdown.setAttribute('aria-expanded', 'true');
                    laporanMenu.classList.add('show');
                }
            }

            const settingsDropdown = document.getElementById('settingsDropdown');
            if (settingsDropdown) {
                const settingsMenu = settingsDropdown.nextElementSibling;
                const hasActiveItem = settingsMenu.querySelector('.dropdown-item.active');

                if (hasActiveItem) {
                    settingsDropdown.setAttribute('aria-expanded', 'true');
                    settingsMenu.classList.add('show');
                }
            }

            // Mobile sidebar toggle
            const sidebarClose = document.querySelector('.sidebar-close');
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    document.querySelector('aside').classList.remove('show');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const sidebar = document.querySelector('aside');
                const sidebarToggle = document.querySelector('[data-sidebar-toggle]');

                if (window.innerWidth <= 768 &&
                    sidebar &&
                    !sidebar.contains(event.target) &&
                    sidebarToggle &&
                    !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
</aside>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>