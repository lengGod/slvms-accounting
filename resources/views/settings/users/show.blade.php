@extends('layouts.app')

@php($pageTitle = 'Detail Pengguna')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Detail Pengguna</h1>
                <p class="text-muted">Detail informasi pengguna.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                                style="width: 150px; height: 150px;">
                                <i class="bi bi-person fs-1 text-secondary"></i>
                            </div>
                        </div>
                        <h5>{{ $user->name }}</h5>
                        <p class="text-muted">{{ ucfirst($user->role) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="150">ID</td>
                                <td><strong>{{ $user->id }}</strong></td>
                            </tr>
                            <tr>
                                <td>Nama Lengkap</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td>Role</td>
                                <td>
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'info' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>Dibuat Pada</td>
                                <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>Diperbarui Pada</td>
                                <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>

                        <div class="mt-4">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-1"></i> Edit Pengguna
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
