@extends('layouts.guest')

@section('content')
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center bg-light">
        <div class="row w-100 shadow rounded overflow-hidden" style="max-width: 900px;">
            {{-- Kolom Kiri: Judul Aplikasi --}}
            <div class="col-md-6 bg-primary text-white d-flex flex-column justify-content-center align-items-center p-5">
                <h2 class="fw-bold mb-3 text-center">Selamat datang di SLVMS</h2>
                <p class="text-white-50 text-center">Sarana Lampung Ventura</p>
                <i class="bi bi-credit-card" style="font-size: 3rem;"></i>
            </div>

            {{-- Kolom Kanan: Form Login --}}
            <div class="col-md-6 bg-white p-5">
                <h4 class="fw-bold text-primary text-center mb-1">Login</h4>
                <p class="text-muted text-center mb-4">Masuk ke akun Anda</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Ingat Saya</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </button>
                    </div>

                    @if (request()->route()->named('password.request'))
                        <div class="text-center mt-3">
                            <a href="{{ route('password.request') }}" class="text-decoration-none text-muted">
                                Lupa Password?
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
