@extends('layouts.app')

@php($pageTitle = 'Edit Transaksi')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Edit Transaksi #TRX{{ $transaction->id }}</h1>
                <p class="text-muted">Edit informasi transaksi.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" id="transactionForm">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="debtor_id" class="form-label">Debitur</label>
                            <select class="form-select" id="debtor_id" name="debtor_id" required>
                                <option value="">Pilih Debitur</option>
                                @foreach ($debtors as $debtor)
                                    <option value="{{ $debtor->id }}"
                                        {{ $transaction->debtor_id == $debtor->id ? 'selected' : '' }}
                                        data-balance="{{ $debtor->current_balance }}"
                                        data-titipan="{{ $debtor->total_titipan }}">
                                        {{ $debtor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('debtor_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <div id="debtorInfo" class="mt-2 small text-muted">
                                <div>Saldo Saat Ini: <span id="currentBalance" class="fw-medium"></span></div>
                                <div>Total Titipan: <span id="totalTitipan" class="fw-medium"></span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">Jenis Transaksi</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Pilih Jenis</option>
                                <option value="piutang" {{ $transaction->type == 'piutang' ? 'selected' : '' }}>Piutang
                                </option>
                                <option value="pembayaran" {{ $transaction->type == 'pembayaran' ? 'selected' : '' }}>
                                    Pembayaran</option>
                            </select>
                            @error('type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date"
                                value="{{ $transaction->transaction_date->format('Y-m-d') }}" required>
                            @error('transaction_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Jumlah <span class="text-muted">(Otomatis
                                    dihitung)</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="amount" name="amount"
                                    value="{{ number_format($transaction->amount, 2, '.', '') }}" min="0"
                                    step="0.01" readonly>
                            </div>
                            <small class="text-muted">Jumlah otomatis dihitung dari Bagi Hasil + Bagi Pokok</small>
                            @error('amount')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info untuk transaksi piutang -->
                        <div id="piutangInfo" class="col-12"
                            style="{{ $transaction->type == 'piutang' ? '' : 'display: none;' }}">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Informasi Piutang:</strong>
                                    <p class="mb-0 mt-1">Isi Bagi Hasil dan/atau Bagi Pokok. Jumlah transaksi akan otomatis
                                        dihitung dari penjumlahan keduanya.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Info untuk transaksi pembayaran -->
                        <div id="pembayaranInfo" class="col-12"
                            style="{{ $transaction->type == 'pembayaran' ? '' : 'display: none;' }}">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Informasi Pembayaran:</strong>
                                    <p class="mb-0 mt-1">Isi Bagi Hasil dan/atau Bagi Pokok. Jumlah transaksi akan otomatis
                                        dihitung dari penjumlahan keduanya.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="bagi_hasil" class="form-label">Bagi Hasil</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="bagi_hasil" name="bagi_hasil"
                                    value="{{ number_format($transaction->bagi_hasil ?? 0, 2, '.', '') }}" min="0"
                                    step="0.01">
                            </div>
                            @error('bagi_hasil')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="bagi_pokok" class="form-label">Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="bagi_pokok" name="bagi_pokok"
                                    value="{{ number_format($transaction->bagi_pokok ?? 0, 2, '.', '') }}" min="0"
                                    step="0.01">
                            </div>
                            @error('bagi_pokok')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info untuk pembayaran lebih -->
                        <div id="paymentInfo" class="col-12" style="display: none;">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>
                                    <strong>Peringatan:</strong>
                                    <div class="mt-1">
                                        <div>Kelebihan Pembayaran: <span id="kelebihanBayar"
                                                class="fw-medium text-success"></span></div>
                                        <div class="text-info">Kelebihan pembayaran akan otomatis disimpan sebagai titipan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info untuk piutang dengan titipan -->
                        <div id="titipanInfo" class="col-12" style="display: none;">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Informasi Titipan:</strong>
                                    <div class="mt-1">
                                        <div>Titipan Tersedia: <span id="availableTitipan"
                                                class="fw-medium text-info"></span></div>
                                        <div class="text-info">Titipan akan otomatis digunakan untuk membayar piutang</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="description" name="description"
                                value="{{ $transaction->description ?? '' }}" placeholder="Keterangan (opsional)">
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('transactions.index') }}" class="btn btn-secondary me-2">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bi bi-save me-1"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const debtorSelect = document.getElementById('debtor_id');
                const typeSelect = document.getElementById('type');
                const amountInput = document.getElementById('amount');
                const bagiHasilInput = document.getElementById('bagi_hasil');
                const bagiPokokInput = document.getElementById('bagi_pokok');
                const debtorInfo = document.getElementById('debtorInfo');
                const currentBalanceSpan = document.getElementById('currentBalance');
                const totalTitipanSpan = document.getElementById('totalTitipan');
                const paymentInfo = document.getElementById('paymentInfo');
                const kelebihanBayarSpan = document.getElementById('kelebihanBayar');
                const piutangInfo = document.getElementById('piutangInfo');
                const pembayaranInfo = document.getElementById('pembayaranInfo');
                const titipanInfo = document.getElementById('titipanInfo');
                const availableTitipanSpan = document.getElementById('availableTitipan');
                const submitBtn = document.getElementById('submitBtn');
                const form = document.getElementById('transactionForm');

                // Format currency
                function formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount);
                }

                // Calculate amount from allocation
                function calculateAmount() {
                    const bagiHasil = parseFloat(bagiHasilInput.value) || 0;
                    const bagiPokok = parseFloat(bagiPokokInput.value) || 0;
                    const total = bagiHasil + bagiPokok;

                    amountInput.value = total.toFixed(2);
                    updateInfo();
                }

                // Initialize debtor info
                function initDebtorInfo() {
                    const selectedOption = debtorSelect.options[debtorSelect.selectedIndex];
                    if (selectedOption.value) {
                        const balance = parseFloat(selectedOption.dataset.balance);
                        const titipan = parseFloat(selectedOption.dataset.titipan);

                        currentBalanceSpan.textContent = formatCurrency(balance);
                        currentBalanceSpan.className = balance < 0 ? 'fw-medium text-danger' : 'fw-medium text-success';

                        totalTitipanSpan.textContent = formatCurrency(titipan);
                    }
                    updateInfo();
                }

                // Update info based on transaction type
                function updateInfo() {
                    // Show/hide info based on transaction type
                    if (typeSelect.value === 'piutang') {
                        piutangInfo.style.display = 'block';
                        pembayaranInfo.style.display = 'none';

                        // Check if debtor has titipan
                        const selectedOption = debtorSelect.options[debtorSelect.selectedIndex];
                        if (selectedOption.value) {
                            const titipan = parseFloat(selectedOption.dataset.titipan);
                            const amount = parseFloat(amountInput.value) || 0;

                            if (titipan > 0 && amount > 0) {
                                availableTitipanSpan.textContent = formatCurrency(titipan);
                                titipanInfo.style.display = 'block';
                            } else {
                                titipanInfo.style.display = 'none';
                            }
                        } else {
                            titipanInfo.style.display = 'none';
                        }

                        paymentInfo.style.display = 'none';
                    } else if (typeSelect.value === 'pembayaran') {
                        piutangInfo.style.display = 'none';
                        pembayaranInfo.style.display = 'block';
                        titipanInfo.style.display = 'none';

                        // Check for overpayment
                        if (debtorSelect.value) {
                            const selectedOption = debtorSelect.options[debtorSelect.selectedIndex];
                            const balance = parseFloat(selectedOption.dataset.balance);
                            const amount = parseFloat(amountInput.value) || 0;

                            if (balance < 0 && amount > Math.abs(balance)) {
                                const kelebihan = amount - Math.abs(balance);
                                kelebihanBayarSpan.textContent = formatCurrency(kelebihan);
                                paymentInfo.style.display = 'block';
                            } else {
                                paymentInfo.style.display = 'none';
                            }
                        } else {
                            paymentInfo.style.display = 'none';
                        }
                    } else {
                        piutangInfo.style.display = 'none';
                        pembayaranInfo.style.display = 'none';
                        titipanInfo.style.display = 'none';
                        paymentInfo.style.display = 'none';
                    }
                }

                // Add event listeners
                debtorSelect.addEventListener('change', function() {
                    initDebtorInfo();
                });

                typeSelect.addEventListener('change', updateInfo);

                bagiHasilInput.addEventListener('input', calculateAmount);
                bagiPokokInput.addEventListener('input', calculateAmount);

                // Form submission validation
                form.addEventListener('submit', function(e) {
                    const amount = parseFloat(amountInput.value) || 0;

                    if (amount <= 0) {
                        e.preventDefault();
                        alert(
                            'Jumlah transaksi harus lebih dari 0. Silakan isi Bagi Hasil dan/atau Bagi Pokok.'
                            );
                    }
                });

                // Initialize on load
                initDebtorInfo();
            });
        </script>
    @endpush
@endsection
