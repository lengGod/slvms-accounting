@extends('layouts.app')

@php($pageTitle = 'Daftar Debitur')

@section('content')
    <div class="container-fluid p-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold">Daftar Debitur</h1>
                <p class="text-muted">Kelola semua Debitur Anda di satu tempat.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('debtors.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Debitur
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('debtors.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama debitur..."
                                value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-funnel me-1"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('debtors.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Saldo Pokok</th>
                                <th>Saldo Bagi Hasil</th>
                                <th>Total Saldo</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Titipan</th>
                                <th>Aksi</th>
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
                                                <div class="small text-muted">{{ $debtor->phone ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $debtor->formatted_saldo_pokok }}</td>
                                    <td>{{ $debtor->formatted_saldo_bagi_hasil }}</td>
                                    <td class="{{ $debtor->current_balance < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $debtor->formatted_balance }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                        @if ($debtor->debtor_status == 'lunas') bg-success
                                        @elseif ($debtor->debtor_status == 'belum_lunas') bg-warning
                                        @else bg-info @endif">
                                            @if ($debtor->debtor_status == 'lunas')
                                                Lunas
                                            @elseif ($debtor->debtor_status == 'belum_lunas')
                                                Belum Lunas
                                            @else
                                                Lebih Bayar
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $debtor->keterangan_piutang }}</td>
                                    <td>
                                        @if ($debtor->hasTitipan())
                                            <span class="text-info fw-medium">
                                                {{ $debtor->formatted_total_titipan }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('debtors.show', $debtor->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('debtors.edit', $debtor->id) }}"
                                                class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('debtors.destroy', $debtor->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada data debitur</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $debtors->firstItem() }} - {{ $debtors->lastItem() }} dari {{ $debtors->total() }}
                        data
                    </div>
                    {{ $debtors->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
