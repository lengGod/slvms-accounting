@extends('layouts.app')

@php($pageTitle = 'Journal')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Journal Transaksi</h1>
                <p class="text-muted">Lihat semua transaksi keuangan dalam format journal.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-primary" onclick="printContent()">
                    <i class="bi bi-printer me-1"></i> Cetak Journal
                </button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('journal.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari ID atau nama debitur..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" placeholder="Dari Tanggal"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" placeholder="Sampai Tanggal"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Semua Tipe</option>
                            <option value="piutang" {{ request('type') == 'piutang' ? 'selected' : '' }}>Piutang</option>
                            <option value="pembayaran" {{ request('type') == 'pembayaran' ? 'selected' : '' }}>Pembayaran
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum
                                Lunas</option>
                            <option value="jatuh_tempo" {{ request('status') == 'jatuh_tempo' ? 'selected' : '' }}>Jatuh
                                Tempo</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-funnel"></i>
                        </button>
                    </div>
                </form>

                <div class="table-responsive" id="print-content">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Tanggal</th>
                                <th>Dibuat Oleh</th>
                                <th>Debitur</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $transaction->formatted_id }}</div>
                                    </td>
                                    <td>{{ $transaction->formatted_date }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="p-2 rounded-circle bg-light me-2">
                                                    <i class="bi bi-person text-secondary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $transaction->user->name }}</div>
                                                <div class="small text-muted">{{ ucfirst($transaction->user->role) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="p-2 rounded-circle bg-light me-2">
                                                    <i class="bi bi-people text-secondary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $transaction->debtor->name }}</div>
                                                <div class="small text-muted">{{ $transaction->debtor->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type == 'piutang' ? 'info' : 'success' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="{{ $transaction->type == 'piutang' ? 'text-danger' : 'text-success' }}">
                                        {{ $transaction->formatted_amount }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->status_color }}">
                                            {{ $transaction->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('journal.show', $transaction->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada data journal</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <div class="d-flex gap-4">
                            <div>
                                Total Piutang: <strong class="text-danger">Rp
                                    {{ number_format($totalPiutang, 0, ',', '.') }}</strong>
                            </div>
                            <div>
                                Total Pembayaran: <strong class="text-success">Rp
                                    {{ number_format($totalPembayaran, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted">
                        Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari
                        {{ $transactions->total() }}
                        data
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
