@extends('layouts.app')

@php($pageTitle = 'Log Aktivitas')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Log Aktivitas</h1>
                <p class="text-muted">Lihat log aktivitas pengguna.</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('settings.activity-logs') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <select name="user_id" class="form-select">
                            <option value="">Semua Pengguna</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="action" class="form-select">
                            <option value="">Semua Aksi</option>
                            <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Membuat</option>
                            <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Memperbarui
                            </option>
                            <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Menghapus</option>
                            <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                            <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Target</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="p-2 rounded-circle bg-light me-2">
                                                    <i class="bi bi-person text-secondary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $log->user->name }}</div>
                                                <div class="small text-muted">{{ ucfirst($log->user->role) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                        @if ($log->action == 'create') bg-success
                                        @elseif ($log->action == 'update') bg-warning
                                        @elseif ($log->action == 'delete') bg-danger
                                        @elseif ($log->action == 'login') bg-info
                                        @elseif ($log->action == 'logout') bg-secondary @endif">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($log->target_type && $log->target_id)
                                            {{ $log->target_type }} #{{ $log->target_id }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada data log aktivitas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }}
                        data
                    </div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
