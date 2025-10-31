<!-- Loading Screen Inside Content -->
<div id="loadingScreen" class="content-loading-overlay hidden">
    <div class="loading-content">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="loading-text">
            <h5 class="mb-2">Memuat Data...</h5>
            <p class="text-muted small mb-0">Mohon tunggu sebentar</p>
        </div>
        <div class="loading-progress mt-3">
            <div class="progress" style="height: 4px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                    style="width: 100%">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Loading di dalam content area */
    .content-loading-overlay {
        position: fixed;
        top: 64px;
        /* Tinggi header */
        left: 250px;
        /* Lebar sidebar */
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        opacity: 1;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .content-loading-overlay.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .loading-content {
        text-align: center;
        padding: 2rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        min-width: 300px;
        animation: fadeInUp 0.5s ease;
    }

    .loading-spinner {
        margin-bottom: 1.5rem;
    }

    .loading-spinner .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3rem;
    }

    .loading-text h5 {
        font-weight: 600;
        color: #212529;
        margin: 0;
    }

    .loading-text p {
        font-size: 0.875rem;
        margin: 0;
        color: #6c757d;
    }

    .loading-progress {
        width: 100%;
        max-width: 250px;
        margin: 0 auto;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Variant: Small Loading */
    .content-loading-overlay.loading-small .loading-content {
        padding: 1.5rem;
        min-width: 200px;
    }

    .content-loading-overlay.loading-small .spinner-border {
        width: 2rem;
        height: 2rem;
        border-width: 0.25rem;
    }

    .content-loading-overlay.loading-small .loading-text h5 {
        font-size: 1rem;
    }

    /* Variant: Transparent Background */
    .content-loading-overlay.loading-transparent {
        background: rgba(0, 0, 0, 0.5);
    }

    .content-loading-overlay.loading-transparent .loading-content {
        background: rgba(255, 255, 255, 0.95);
    }

    /* Variant: Dark Mode */
    .content-loading-overlay.loading-dark {
        background: rgba(0, 0, 0, 0.9);
    }

    .content-loading-overlay.loading-dark .loading-content {
        background: #1a1a1a;
        color: white;
    }

    .content-loading-overlay.loading-dark .loading-text h5 {
        color: white;
    }

    .content-loading-overlay.loading-dark .loading-text p {
        color: #adb5bd;
    }

    /* Variant: Minimal (hanya spinner) */
    .content-loading-overlay.loading-minimal .loading-content {
        background: transparent;
        box-shadow: none;
        padding: 1rem;
        min-width: auto;
    }

    .content-loading-overlay.loading-minimal .loading-text,
    .content-loading-overlay.loading-minimal .loading-progress {
        display: none;
    }

    .content-loading-overlay.loading-minimal .spinner-border {
        width: 4rem;
        height: 4rem;
        border-width: 0.4rem;
    }

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
        .content-loading-overlay {
            top: 64px;
            left: 0;
            /* Sidebar tersembunyi di mobile */
        }

        .loading-content {
            min-width: 250px;
            padding: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .loading-content {
            min-width: 200px;
            padding: 1.25rem;
        }

        .loading-spinner .spinner-border {
            width: 2.5rem;
            height: 2.5rem;
        }

        .loading-text h5 {
            font-size: 1rem;
        }
    }
</style>

<script>
    // Content Loading Screen Controller
    const LoadingScreen = {
        element: null,

        init: function() {
            this.element = document.getElementById('loadingScreen');
        },

        // Show loading screen
        show: function(options = {}) {
            if (!this.element) this.init();
            if (!this.element) return;

            // Set custom text if provided
            if (options.text) {
                const textElement = this.element.querySelector('.loading-text h5');
                if (textElement) textElement.textContent = options.text;
            }

            if (options.subtext) {
                const subtextElement = this.element.querySelector('.loading-text p');
                if (subtextElement) subtextElement.textContent = options.subtext;
            }

            // Add variant classes
            if (options.variant) {
                this.element.className = 'content-loading-overlay loading-' + options.variant;
            } else {
                this.element.className = 'content-loading-overlay';
            }

            // Show the loading screen
            this.element.classList.remove('hidden');
        },

        // Hide loading screen
        hide: function(delay = 0) {
            if (!this.element) this.init();
            if (!this.element) return;

            setTimeout(() => {
                this.element.classList.add('hidden');

                // Reset text after hide animation
                setTimeout(() => {
                    const textElement = this.element.querySelector('.loading-text h5');
                    const subtextElement = this.element.querySelector('.loading-text p');
                    if (textElement) textElement.textContent = 'Memuat Data...';
                    if (subtextElement) subtextElement.textContent = 'Mohon tunggu sebentar';
                    this.element.className = 'content-loading-overlay hidden';
                }, 300);
            }, delay);
        },

        // Toggle loading screen
        toggle: function() {
            if (!this.element) this.init();
            if (!this.element) return;

            if (this.element.classList.contains('hidden')) {
                this.show();
            } else {
                this.hide();
            }
        }
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        LoadingScreen.init();
    });

    // Optional: Auto-show/hide untuk form submission
    // Uncomment jika ingin mengaktifkan
    /*
    document.addEventListener('DOMContentLoaded', function() {
        // Show loading saat form submit
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                // Skip jika form punya class 'no-loading'
                if (!form.classList.contains('no-loading')) {
                    LoadingScreen.show({
                        text: 'Menyimpan Data...',
                        subtext: 'Mohon jangan tutup halaman ini'
                    });
                }
            });
        });
    });
    */

    // Optional: Auto-loading untuk AJAX dengan jQuery
    // Uncomment jika menggunakan jQuery AJAX
    /*
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined') {
            $(document).ajaxStart(function() {
                LoadingScreen.show();
            }).ajaxStop(function() {
                LoadingScreen.hide();
            }).ajaxError(function() {
                LoadingScreen.hide();
            });
        }
    });
    */

    // Optional: Auto-loading untuk Fetch API
    // Uncomment jika ingin auto-loading pada fetch
    /*
    document.addEventListener('DOMContentLoaded', function() {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            LoadingScreen.show();
            return originalFetch.apply(this, args)
                .finally(() => {
                    setTimeout(() => LoadingScreen.hide(), 300);
                });
        };
    });
    */
</script>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/partials/loading.blade.php ENDPATH**/ ?>