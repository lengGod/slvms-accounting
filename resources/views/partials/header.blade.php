@if (!empty($pageTitle))
    <header>
        <div class="d-flex align-items-center">
            <button id="sidebar-toggle" class="sidebar-toggle me-3">
                <i class="bi bi-list"></i>
            </button>
            <h2 class="h5 fw-bold mb-0 text-dark d-none d-sm-block">{{ $pageTitle }}</h2>
            <h2 class="h6 fw-bold mb-0 text-dark d-sm-none">{{ $pageTitle }}</h2>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="btn position-relative p-0 border-0 bg-transparent">
                <i class="bi bi-bell fs-5 text-secondary"></i>
                <span
                    class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </button>

            <div class="position-relative">
                <button id="profileDropdown" class="btn d-flex align-items-center gap-2" type="button">
                    <img src="https://www.pngmart.com/files/21/Account-User-PNG-Isolated-HD.png" alt="Avatar"
                        class="avatar">
                    <div class="text-start d-none d-md-block">
                        <div class="fw-semibold small text-dark">{{ auth()->user()->name }}</div>
                        <div class="text-muted small">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                    <i class="bi bi-chevron-down text-muted dropdown-icon"></i>
                </button>
                <div id="profileMenu" class="profile-dropdown-menu">
                    <a href="{{ route('settings.profile') }}" class="profile-dropdown-item">
                        <i class="bi bi-person me-2"></i> Profil Saya
                    </a>
                    <a href="{{ route('settings.password') }}" class="profile-dropdown-item">
                        <i class="bi bi-shield-lock me-2"></i> Ubah Password
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="profile-dropdown-item">
                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </header>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
@endif

<style>
    /* Custom profile dropdown styles */
    .profile-dropdown-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        min-width: 200px;
        background-color: #fff;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
    }

    .profile-dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-dropdown-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        color: #212529;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }

    .profile-dropdown-item:hover {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .dropdown-icon {
        transition: transform 0.3s ease;
    }

    .dropdown-icon.rotate {
        transform: rotate(180deg);
    }

    .dropdown-divider {
        height: 1px;
        margin: 0.5rem 0;
        overflow: hidden;
        border-top: 1px solid #e9ecef;
    }

    .logout-form {
        margin: 0;
        padding: 0;
    }

    .logout-form button {
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        padding: 0;
        font-size: inherit;
        font-family: inherit;
        line-height: inherit;
        color: inherit;
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileDropdown = document.getElementById('profileDropdown');
        const profileMenu = document.getElementById('profileMenu');
        const dropdownIcon = profileDropdown.querySelector('.dropdown-icon');

        // Toggle profile dropdown
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.classList.toggle('show');
            dropdownIcon.classList.toggle('rotate');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('show');
                dropdownIcon.classList.remove('rotate');
            }
        });

        // Prevent dropdown from closing when clicking inside
        profileMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
