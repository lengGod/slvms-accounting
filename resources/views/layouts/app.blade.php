<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLVMS</title>
    <!-- Urutan yang benar -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
    @include('partials.sidebar')
    @include('partials.header')

    <main>
        @include('partials.alerts')
        @yield('content')
    </main>

    @include('partials.confirm-modal')
    @include('partials.validation-modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Fungsi JavaScript untuk cetak -->
    <script>
        function printContent() {
            // ... kode print tetap sama ...
        }

        // Toggle sidebar untuk mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.querySelector('aside');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            });
        });

        //-----fungsi javascript untuk cetak-----//
        function printContent() {
            // Buat elemen baru untuk konten yang akan dicetak
            const content = document.getElementById('print-content').innerHTML;

            // Buat window baru untuk cetak
            const printWindow = window.open('', '_blank');

            // Buat struktur HTML untuk halaman cetak
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

            // Tunggu hingga konten selesai dimuat
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            var confirmModalForm = document.getElementById('confirmModalForm');

            document.querySelectorAll('.delete-btn').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    var action = this.getAttribute('data-action');
                    confirmModalForm.setAttribute('action', action);
                    confirmModal.show();
                });
            });
        });
    </script>

    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var validationModal = new bootstrap.Modal(document.getElementById('validationModal'));
            validationModal.show();
        });
    </script>
    @endif

    @stack('scripts')
</body>

</html>
