<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Debitur</th>
            <th>Keterangan</th>
            <th>Debit (Piutang)</th>
            <th>Kredit (Pembayaran)</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $transaction)
            <tr>
                <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $transaction->debtor->name }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->type == 'piutang' ? 'Rp ' . number_format($transaction->amount, 0, ',', '.') : '-' }}
                </td>
                <td>{{ $transaction->type == 'pembayaran' ? 'Rp ' . number_format($transaction->amount, 0, ',', '.') : '-' }}
                </td>
                <td>Rp {{ number_format($transaction->running_balance, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
