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
                <a class="nav-link {{ request()->is('dashboard') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark' }}"
                    href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            @if (auth()->check() && auth()->user()->role === 'admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('transactions*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark' }}"
                        href="{{ route('transactions.index') }}">
                        <i class="bi bi-cash-stack me-2"></i> Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('debtors*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark' }}"
                        href="{{ route('debtors.index') }}">
                        <i class="bi bi-people me-2"></i> Debitur
                    </a>
                </li>
            @endif

            @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'accounting']))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('titipans*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark' }}"
                        href="{{ route('titipans.index') }}">
                        <i class="bi bi-wallet2 me-2"></i> Titipan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('journal*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark' }}"
                        href="{{ route('journal.index') }}">
                        <i class="bi bi-journal-text me-2"></i> Journal
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link dropdown-toggle {{ request()->is('reports*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark' }}"
                        href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-bar-chart me-2"></i>
                        <span>Laporan</span>
                        <i class="ms-auto dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="laporanDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->is('reports/kartu-mutasi*') ? 'active' : '' }}"
                                href="{{ route('reports.kartu-mutasi') }}">
                                <i class="bi bi-card-text me-2"></i> Kartu Mutasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->is('reports/piutang-perbulan*') ? 'active' : '' }}"
                                href="{{ route('reports.piutang-perbulan') }}">
                                <i class="bi bi-calendar-month me-2"></i> Piutang
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->is('reports/pembayaran-perbulan*') ? 'active' : '' }}"
                                href="{{ route('reports.pembayaran-perbulan') }}">
                                <i class="bi bi-cash-coin me-2"></i> Pembayaran
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->is('reports/debit-piutang*') ? 'active' : '' }}"
                                href="{{ route('reports.debit-piutang') }}">
                                <i class="bi bi-journal-text me-2"></i> Debit Piutang
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->check() && auth()->user()->role === 'admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('users*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark' }}"
                        href="{{ route('users.index') }}">
                        <i class="bi bi-people me-2"></i>Pengguna
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <a class="nav-link dropdown-toggle {{ request()->is('settings*') && !request()->is('users*') ? 'active fw-semibold text-primary bg-primary bg-opacity-10 rounded' : 'text-dark' }}"
                    href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear me-2"></i>
                    <span>Pengaturan</span>
                    <i class="ms-auto dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                    <li>
                        <a class="dropdown-item {{ request()->is('settings/profile') ? 'active' : '' }}"
                            href="{{ route('settings.profile') }}">
                            <i class="bi bi-person me-2"></i> Profil Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->is('settings/password') ? 'active' : '' }}"
                            href="{{ route('settings.password') }}">
                            <i class="bi bi-shield-lock me-2"></i> Ubah Password
                        </a>
                    </li>
                    @if (auth()->check() && auth()->user()->role === 'admin')
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->is('settings/application') ? 'active' : '' }}"
                                href="{{ route('settings.application') }}">
                                <i class="bi bi-app me-2"></i> Pengaturan Aplikasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->is('settings/backup') ? 'active' : '' }}"
                                href="{{ route('settings.backup') }}">
                                <i class="bi bi-database me-2"></i> Backup & Restore
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->is('settings/activity-logs') ? 'active' : '' }}"
                                href="{{ route('settings.activity-logs') }}">
                                <i class="bi bi-activity me-2"></i> Log Aktivitas
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer border-top px-3 py-4 mt-auto">
        <a class="nav-link text-dark" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right me-2"></i> Keluar
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <style>
        /* Custom styles for dropdown */
        .dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-right: 0.5rem;
        }

        .dropdown-icon {
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }

        .dropdown-toggle[aria-expanded="true"] .dropdown-icon {
            transform: rotate(180deg);
        }

        /* Custom dropdown menu styling */
        .dropdown-menu {
            position: static !important;
            transform: none !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0.25rem 0 0 0 !important;
            background-color: transparent !important;
            width: 100% !important;
        }

        .dropdown-item {
            padding: 0.5rem 0.75rem !important;
            margin: 0.125rem 0 !important;
            border-radius: 0.25rem !important;
            color: var(--bs-dark) !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
            transform: translateX(-10px);
            opacity: 0;
        }

        .dropdown-menu.show .dropdown-item {
            transform: translateX(0);
            opacity: 1;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .dropdown-menu.show .dropdown-item:nth-child(1) {
            transition-delay: 0.05s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(2) {
            transition-delay: 0.1s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(3) {
            transition-delay: 0.15s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(4) {
            transition-delay: 0.2s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(5) {
            transition-delay: 0.25s;
        }

        .dropdown-menu.show .dropdown-item:nth-child(6) {
            transition-delay: 0.3s;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: rgba(13, 110, 253, 0.05) !important;
            color: var(--bs-primary) !important;
            padding-left: 1rem !important;
        }

        .dropdown-item.active {
            background-color: rgba(13, 110, 253, 0.1) !important;
            color: var(--bs-primary) !important;
            font-weight: 500 !important;
        }

        /* Sidebar footer */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #fff;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dropdown-item {
                padding-left: 1.5rem !important;
            }

            .dropdown-item:hover,
            .dropdown-item:focus {
                padding-left: 1.75rem !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure dropdown is open when page loads if it contains active item
            const laporanDropdown = document.getElementById('laporanDropdown');
            const laporanDropdownMenu = laporanDropdown.nextElementSibling;
            const activeLaporanItem = laporanDropdownMenu.querySelector('.dropdown-item.active');

            if (activeLaporanItem) {
                laporanDropdown.classList.add('active');
                laporanDropdown.setAttribute('aria-expanded', 'true');
                laporanDropdownMenu.classList.add('show');
            }

            // Same for settings dropdown
            const settingsDropdown = document.getElementById('settingsDropdown');
            const settingsDropdownMenu = settingsDropdown.nextElementSibling;
            const activeSettingsItem = settingsDropdownMenu.querySelector('.dropdown-item.active');

            if (activeSettingsItem) {
                settingsDropdown.classList.add('active');
                settingsDropdown.setAttribute('aria-expanded', 'true');
                settingsDropdownMenu.classList.add('show');
            }
        });
    </script>
</aside>
