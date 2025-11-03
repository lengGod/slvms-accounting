<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
            background-color: #f8f9fa; /* Light gray background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            background-color: #ffffff;
            padding: 3rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            max-width: 500px; /* Limit width for better readability */
            width: 90%; /* Responsive width */
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545; /* Bootstrap danger color */
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 4rem;
            font-weight: bold;
            color: #343a40; /* Dark gray for code */
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.8rem;
            font-weight: 600;
            color: #212529; /* Darker text for message */
            margin-bottom: 1rem;
        }
        .error-description {
            font-size: 1rem;
            color: #6c757d; /* Secondary text for description */
            margin-bottom: 2rem;
        }
        .btn-home {
            background-color: #0d6efd; /* Bootstrap primary color */
            color: #ffffff;
            padding: 0.75rem 2.5rem;
            border-radius: 0.3rem;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
            display: inline-flex; /* For icon alignment */
            align-items: center;
            gap: 0.5rem;
        }
        .btn-home:hover {
            background-color: #0b5ed7; /* Darker primary color on hover */
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle-fill"></i> <!-- Default icon -->
        </div>
        <div class="error-code">
            @yield('code')
        </div>
        <div class="error-message">
            @yield('message')
        </div>
        <div class="error-description">
            @yield('description', 'Maaf, terjadi kesalahan. Silakan coba lagi nanti atau kembali ke halaman utama.')
        </div>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="bi bi-house-fill"></i>
            Kembali ke Halaman Utama
        </a>
    </div>
</body>
</html>
