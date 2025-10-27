@php($pageTitle = 'Dashboard')
@extends('layouts.app')

@section('content')
    <!-- Header selamat datang-->
    <div class="mb-4">
        <h2 class="fw-bold">
            Selamat Datang, <span class="text-primary">{{ auth()->user()->name }}</span>
        </h2>
        <p class="text-muted">Berikut adalah ringkasan keuangan Anda hari ini.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white h-100">
                <div class="card-body d-flex flex-column justify-content-start align-items-start text-center">
                    <h6 class="card-title mb-2">Total Debitur</h6>
                    <p class="card-text fs-5 fw-bold mb-0">{{ $totalDebtors }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-white h-100">
                <div class="card-body d-flex flex-column justify-content-start align-items-start text-center">
                    <h6 class="card-title mb-2">Total Piutang</h6>
                    <p class="card-text fs-5 fw-bold mb-0">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100">
                <div class="card-body d-flex flex-column justify-content-start align-items-start text-center">
                    <h6 class="card-title mb-2">Total Pembayaran</h6>
                    <p class="card-text fs-5 fw-bold mb-0">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white h-100">
                <div class="card-body d-flex flex-column justify-content-start align-items-start text-center">
                    <h6 class="card-title mb-2">Total Saldo Titipan</h6>
                    <p class="card-text fs-5 fw-bold mb-0">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Aktivitas Terbaru --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Aktivitas Transaksi Terbaru</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Debitur</th>
                            <th>Tanggal</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestActivities as $activity)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="p-2 rounded-circle bg-light me-2">
                                                <i class="bi bi-person text-secondary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            {{ $activity->debtor->name ?? 'Tidak diketahui' }}
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($activity->transaction_date)->translatedFormat('d F Y') }}</td>
                                <td class="text-end fw-medium">
                                    Rp {{ number_format($activity->amount, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill 
                                        {{ $activity->type === 'piutang' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }}">
                                        {{ ucfirst($activity->type) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada aktivitas terbaru</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
