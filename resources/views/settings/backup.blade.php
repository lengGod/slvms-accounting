@extends('layouts.app')

@php($pageTitle = 'Backup & Restore')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Backup & Restore</h1>
                <p class="text-muted">Kelola backup database aplikasi.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <form action="{{ route('settings.create-backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-database-add me-1"></i> Buat Backup Baru
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (count($backups) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama File</th>
                                    <th>Ukuran</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($backups as $backup)
                                    <tr>
                                        <td>{{ $backup['filename'] }}</td>
                                        <td>{{ $backup['size'] }}</td>
                                        <td>{{ $backup['last_modified'] }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('settings.download-backup', $backup['filename']) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form action="{{ route('settings.delete-backup', $backup['filename']) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus backup ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-database-exclamation fs-1 text-muted"></i>
                        <p class="mt-3 text-muted">Belum ada backup database.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
