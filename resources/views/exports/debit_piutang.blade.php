<table>
    <thead>
        <tr>
            <th>Nama Debitur</th>
            <th>Total Piutang</th>
            <th>Total Pembayaran</th>
            <th>Saldo</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($debtors as $debtor)
            <tr>
                <td>{{ $debtor->name }}</td>
                <td>Rp {{ number_format($debtor->total_piutang, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($debtor->total_pembayaran, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($debtor->current_balance, 0, ',', '.') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $debtor->debtor_status)) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>Rp {{ number_format($totalPiutang, 0, ',', '.') }}</strong></td>
            <td><strong>Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</strong></td>
            <td><strong>Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>
