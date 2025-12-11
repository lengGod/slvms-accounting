<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }
    thead th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    .text-center {
        text-align: center;
    }
    .text-end {
        text-align: right;
    }
    h1, h2 {
        text-align: center;
    }
</style>

<div>
    <h1>SLV Accounting</h1>
    <h2>Laporan Debit Piutang</h2>
</div>
<table>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Debitur</th>
            <th class="text-end">Total Piutang</th>
            <th class="text-end">Total Pembayaran</th>
            <th class="text-end">Saldo</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($debtorsByCode->flatten() as $debtor)
            <tr>
                <td>{{ $debtor->code ?: 'Tanpa Kode' }}</td>
                <td>{{ $debtor->name }}</td>
                <td class="text-end">{{ $debtor->total_piutang }}</td>
                <td class="text-end">{{ $debtor->total_pembayaran }}</td>
                <td class="text-end">{{ $debtor->current_balance }}</td>
                <td class="text-center">{{ ucfirst(str_replace('_', ' ', $debtor->debtor_status)) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><strong>Total</strong></td>
            <td class="text-end"><strong>{{ $totalPiutang }}</strong></td>
            <td class="text-end"><strong>{{ $totalPembayaran }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
