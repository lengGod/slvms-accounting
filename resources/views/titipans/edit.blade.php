@extends('layouts.app')

@php($pageTitle = 'Edit Titipan')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Edit Titipan #{{ $titipan->id }}</h1>
                <p class="text-muted">Edit informasi titipan.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('titipans.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('titipans.update', $titipan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="debtor_id" class="form-label">Debitur</label>
                            <select class="form-select" id="debtor_id" name="debtor_id" required>
                                <option value="">Pilih Debitur</option>
                                @foreach ($debtors as $debtor)
                                    <option value="{{ $debtor->id }}"
                                        {{ old('debtor_id', $titipan->debtor_id) == $debtor->id ? 'selected' : '' }}>
                                        {{ $debtor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('debtor_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Jumlah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount" name="amount"
                                    value="{{ number_format($titipan->amount, 2, '.', '') }}" min="0" step="100"
                                    required>
                            </div>
                            @error('amount')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal"
                                value="{{ old('tanggal', $titipan->tanggal->format('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan"
                                value="{{ old('keterangan', $titipan->keterangan) }}" placeholder="Keterangan (opsional)">
                            @error('keterangan')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('titipans.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
