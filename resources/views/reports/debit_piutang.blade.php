@extends('layouts.app')

@php($pageTitle = 'Debit Piutang')

@section('content')
    <div id="print-content">
        <div class="container-fluid p-4">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h1 class="display-6 fw-bold">Laporan Debit Piutang</h1>
                    <p class="text-muted">Laporan seluruh debitur dan piutang mereka.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-outline-secondary me-2 no-print" onclick="printContent()">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>
                    <a href="{{ route('reports.export-debit-piutang', ['month' => request('month', \Carbon\Carbon::now()->format('Y-m'))]) }}"
                        class="btn btn-success no-print">
                        <i class="bi bi-download me-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('reports.debit-piutang') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label for="month" class="form-label">Filter per Bulan</label>
                            <input type="month" id="month" name="month" class="form-control"
                                value="{{ request('month', \Carbon\Carbon::now()->format('Y-m')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 bg-warning bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-circle bg-warning bg-opacity-20">
                                        <i class="bi bi-cash-stack text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-1">Total Piutang</h6>
                                    <p class="card-text fs-5 fw-bold mb-0 text-warning">Rp
                                        {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 bg-success bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-circle bg-success bg-opacity-20">
                                        <i class="bi bi-cash-coin text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="card-title text-muted mb-1">Total Pembayaran</h6>
                                    <p class="card-text fs-5 fw-bold mb-0 text-success">Rp
                                        {{ number_format($totalPembayaran, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Debitur</th>
                                    <th class="text-end">Total Piutang</th>
                                    <th class="text-end">Total Pembayaran</th>
                                    <th class="text-end">Saldo</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($debtors as $debtor)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="p-2 rounded-circle bg-light me-2">
                                                        <i class="bi bi-person text-secondary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $debtor->name }}</div>
                                                    <div class="small text-muted">{{ $debtor->phone }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">Rp
                                            {{ number_format(abs($debtor->total_piutang), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end">Rp
                                            {{ number_format(abs($debtor->total_pembayaran), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end {{ $debtor->current_balance < 0 ? 'text-danger' : '' }}">
                                            Rp {{ number_format(abs($debtor->current_balance), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge
                                            @if ($debtor->debtor_status == 'Lunas') bg-success
                                            @elseif ($debtor->debtor_status == 'Belum Lunas') bg-warning
                                            @elseif ($debtor->debtor_status == 'Titipan') bg-info
                                            @else bg-secondary @endif">
                                                {{ $debtor->debtor_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada data debitur yang
                                            memiliki piutang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-primary fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">Rp {{ number_format(abs($totalPiutang), 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format(abs($totalPembayaran), 0, ',', '.') }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
