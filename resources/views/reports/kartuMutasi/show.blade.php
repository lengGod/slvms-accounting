@extends('layouts.app')

@php
    $pageTitle = 'Kartu Mutasi: ' . $debtor->name;
@endphp

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Kartu Mutasi</h1>
                <p class="text-muted">Detail transaksi untuk debitur: {{ $debtor->name }}</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('reports.kartu-mutasi') }}" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button class="btn btn-primary" onclick="printContent()">
                    <i class="bi bi-printer me-1"></i> Cetak
                </button>
            </div>
        </div>

        <!-- Debitur Information -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Informasi Debitur</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td style="width: 150px;"><strong>Nama</strong></td>
                                <td>{{ $debtor->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>{{ $debtor->address }}</td>
                            </tr>
                            <tr>
                                <td><strong>No. Telepon</strong></td>
                                <td>{{ $debtor->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Bergabung</strong></td>
                                <td>{{ $debtor->formatted_joined_at }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="card-title">Ringkasan Saldo</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td style="width: 150px;"><strong>Saldo Awal</strong></td>
                                <td>
                                    @if ($debtor->initial_balance_with_type)
                                        {{ $debtor->initial_balance_with_type['formatted'] }}
                                        <span
                                            class="badge bg-{{ $debtor->initial_balance_with_type['is_negative'] ? 'danger' : 'success' }} ms-2">
                                            {{ $debtor->initial_balance_with_type['is_negative'] ? 'Piutang' : 'Titipan' }}
                                        </span>
                                        <span class="badge bg-secondary ms-1">
                                            {{ $debtor->initial_balance_with_type['type_label'] }}
                                        </span>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total Piutang</strong></td>
                                <td class="text-danger">{{ $debtor->formatted_total_piutang }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Pembayaran</strong></td>
                                <td class="text-success">{{ $debtor->formatted_total_pembayaran }}</td>
                            </tr>
                            <tr>
                                <td><strong>Sisa Saldo</strong></td>
                                <td
                                    class="{{ $debtor->current_balance > 0 ? 'text-success' : ($debtor->current_balance < 0 ? 'text-danger' : '') }}">
                                    {{ $debtor->formatted_balance }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>
                                    <span
                                        class="badge bg-{{ $debtor->debtor_status == 'lunas' ? 'success' : ($debtor->debtor_status == 'lebih_bayar' ? 'info' : 'danger') }}">
                                        {{ $debtor->keterangan_piutang }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <!-- Transaction Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Detail Transaksi</h5>
                <div class="table-responsive" id="print-content">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th colspan="2">Piutang</th>
                                <th>Jumlah Piutang</th>
                                <th colspan="2">Pembayaran</th>
                                <th>Jumlah Pembayaran</th>
                                <th colspan="2">Sisa Saldo</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Pokok</th>
                                <th>Bagi Hasil</th>
                                <th></th>
                                <th>Pokok</th>
                                <th>Bagi Hasil</th>
                                <th></th>
                                <th>Pokok</th>
                                <th>Bagi Hasil</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $saldoPokok = 0;
                                $saldoBagiHasil = 0;
                                $saldoTotal = 0;
                            @endphp
                            @if (count($sortedEvents) > 0)
                                @foreach ($sortedEvents as $transaction)
                                    <tr>
                                        <td>{{ $transaction['id'] ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d M Y') ?? '-' }}</td>
                                        <td>{{ $transaction['description'] ?? '-' }}</td>

                                        @php
                                            $bagiPokok = $transaction['pokok'] ?? 0;
                                            $bagiHasil = $transaction['hasil'] ?? 0;
                                            $amount = $transaction['total'] ?? 0;
                                            $type = $transaction['type'] ?? '';
                                        @endphp

                                        @if ($type == 'piutang')
                                            <td class="text-danger">{{ number_format(abs($bagiPokok), 0, ',', '.') }}</td>
                                            <td class="text-danger">{{ number_format(abs($bagiHasil), 0, ',', '.') }}</td>
                                            <td class="text-danger fw-bold">{{ number_format(abs($amount), 0, ',', '.') }}
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-success">{{ number_format(abs($bagiPokok), 0, ',', '.') }}</td>
                                            <td class="text-success">{{ number_format(abs($bagiHasil), 0, ',', '.') }}</td>
                                            <td class="text-success fw-bold">{{ number_format(abs($amount), 0, ',', '.') }}
                                            </td>
                                        @endif

                                        @php
                                            // FIX: Don't add payment from titipan to running total, as it's already accounted for.
                                            if (!str_starts_with($transaction['description'], 'Pembayaran menggunakan titipan')) {
                                                $saldoPokok += $bagiPokok;
                                                $saldoBagiHasil += $bagiHasil;
                                                $saldoTotal += $amount;
                                            }
                                        @endphp

                                        <td class="{{ $saldoPokok >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($saldoPokok, 0, ',', '.') }}</td>
                                        <td class="{{ $saldoBagiHasil >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($saldoBagiHasil, 0, ',', '.') }}</td>
                                        <td class="{{ $saldoTotal >= 0 ? 'text-success' : 'text-danger' }}">
                                            <strong>{{ number_format($saldoTotal, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="12" class="text-center">Tidak ada transaksi pada periode ini.</td>
                                </tr>
                            @endif
                            <tr class="table-active">
                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                <td class="text-danger">
                                    <strong>{{ number_format($sortedEvents->where('type', 'piutang')->sum('pokok') ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-danger">
                                    <strong>{{ number_format($sortedEvents->where('type', 'piutang')->sum('hasil') ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-danger">
                                    <strong>{{ number_format($sortedEvents->where('type', 'piutang')->sum('total') ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-success">
                                    <strong>{{ number_format($sortedEvents->whereIn('type', ['pembayaran', 'titipan_masuk'])->sum('pokok') ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-success">
                                    <strong>{{ number_format($sortedEvents->whereIn('type', ['pembayaran', 'titipan_masuk'])->sum('hasil') ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-success">
                                    <strong>{{ number_format($sortedEvents->whereIn('type', ['pembayaran', 'titipan_masuk'])->sum('total') ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="{{ $saldoPokok >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ number_format($saldoPokok, 0, ',', '.') }}</strong>
                                </td>
                                <td class="{{ $saldoBagiHasil >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ number_format($saldoBagiHasil, 0, ',', '.') }}</strong>
                                </td>
                                <td class="{{ $saldoTotal >= 0 ? 'text-success' : 'text-danger' }}">
                                    <strong>{{ number_format($saldoTotal, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
