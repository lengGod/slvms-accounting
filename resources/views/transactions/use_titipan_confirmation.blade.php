@extends('layouts.app')

@php($pageTitle = 'Konfirmasi Penggunaan Titipan')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Konfirmasi Penggunaan Titipan</h1>
                <p class="text-muted">Gunakan titipan untuk menutupi sebagian atau seluruh piutang.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('transactions.create') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>
                        <strong>Informasi:</strong>
                        <p class="mb-0 mt-1">Debitur memiliki titipan sebesar {{ $usableTitipanFormatted }} yang dapat
                            digunakan untuk menutupi piutang sebesar {{ $piutangAmountFormatted }}.</p>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Informasi Debitur</h5>
                                <p class="card-text"><strong>Nama:</strong> {{ $debtor->name }}</p>
                                <p class="card-text"><strong>Total Titipan:</strong>
                                    {{ 'Rp ' . number_format($debtor->total_titipan, 0, ',', '.') }}</p>
                                <p class="card-text"><strong>Saldo Saat Ini:</strong> {{ $debtor->formatted_balance }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Rincian Piutang</h5>
                                <p class="card-text"><strong>Jumlah Piutang:</strong> {{ $piutangAmountFormatted }}</p>
                                <p class="card-text"><strong>Titipan yang Dapat Digunakan:</strong>
                                    {{ $usableTitipanFormatted }}</p>
                                <p class="card-text"><strong>Sisa Piutang:</strong> {{ $remainingPiutangFormatted }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($canUseAllTitipan)
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div>
                            <strong>Semua Piutang Dapat Ditutupi:</strong>
                            <p class="mb-0 mt-1">Titipan yang tersedia cukup untuk menutupi seluruh piutang. Tidak akan ada
                                sisa piutang.</p>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            <strong>Ada Sisa Piutang:</strong>
                            <p class="mb-0 mt-1">Titipan yang tersedia tidak cukup untuk menutupi seluruh piutang. Akan ada
                                sisa piutang sebesar {{ $remainingPiutangFormatted }}.</p>
                        </div>
                    </div>
                @endif

                <!-- FIXED ROUTE NAME HERE -->
                <form action="{{ route('transactions.use-titipan-for-piutang') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="debtor_id" value="{{ $request->debtor_id }}">
                    <input type="hidden" name="amount" value="{{ $request->amount }}">
                    <input type="hidden" name="bagi_hasil" value="{{ $request->bagi_hasil }}">
                    <input type="hidden" name="bagi_pokok" value="{{ $request->bagi_pokok }}">
                    <input type="hidden" name="transaction_date" value="{{ $request->transaction_date }}">
                    <input type="hidden" name="description" value="{{ $request->description }}">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions.create') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Gunakan Titipan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
