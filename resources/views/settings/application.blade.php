@extends('layouts.app')

@php($pageTitle = 'Pengaturan Aplikasi')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Pengaturan Aplikasi</h1>
                <p class="text-muted">Kelola pengaturan aplikasi.</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('settings.update-application') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="app_name" class="form-label">Nama Aplikasi</label>
                            <input type="text" class="form-control" id="app_name" name="app_name"
                                value="{{ old('app_name', $settings['app_name']) }}" required>
                            @error('app_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="currency" class="form-label">Mata Uang</label>
                            <select class="form-select" id="currency" name="currency" required>
                                <option value="IDR" {{ $settings['currency'] == 'IDR' ? 'selected' : '' }}>IDR - Rupiah
                                </option>
                                <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD - Dollar
                                </option>
                                <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>EUR - Euro
                                </option>
                            </select>
                            @error('currency')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="app_description" class="form-label">Deskripsi Aplikasi</label>
                            <textarea class="form-control" id="app_description" name="app_description" rows="2">{{ old('app_description', $settings['app_description']) }}</textarea>
                            @error('app_description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="date_format" class="form-label">Format Tanggal</label>
                            <select class="form-select" id="date_format" name="date_format" required>
                                <option value="d-m-Y" {{ $settings['date_format'] == 'd-m-Y' ? 'selected' : '' }}>
                                    DD-MM-YYYY</option>
                                <option value="m/d/Y" {{ $settings['date_format'] == 'm/d/Y' ? 'selected' : '' }}>
                                    MM/DD/YYYY</option>
                                <option value="Y-m-d" {{ $settings['date_format'] == 'Y-m-d' ? 'selected' : '' }}>
                                    YYYY-MM-DD</option>
                            </select>
                            @error('date_format')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="time_format" class="form-label">Format Waktu</label>
                            <select class="form-select" id="time_format" name="time_format" required>
                                <option value="H:i" {{ $settings['time_format'] == 'H:i' ? 'selected' : '' }}>24 Jam
                                    (HH:MM)</option>
                                <option value="h:i A" {{ $settings['time_format'] == 'h:i A' ? 'selected' : '' }}>12 Jam
                                    (HH:MM AM/PM)</option>
                            </select>
                            @error('time_format')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="app_logo" class="form-label">Logo Aplikasi</label>
                            <input type="file" class="form-control" id="app_logo" name="app_logo" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, GIF. Maksimal: 2MB</div>
                            @if ($settings['app_logo'])
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['app_logo']) }}" alt="Logo"
                                        height="40">
                                </div>
                            @endif
                            @error('app_logo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="app_favicon" class="form-label">Favicon</label>
                            <input type="file" class="form-control" id="app_favicon" name="app_favicon" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, GIF, ICO. Maksimal: 1MB</div>
                            @error('app_favicon')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
