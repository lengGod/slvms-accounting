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
        text-align: center;
    }
    h1, h2 {
        text-align: center;
    }
    .text-end {
        text-align: right;
    }
</style>

<div>
    <h1>SLV Accounting</h1>
    <h2>Laporan Kartu Mutasi: {{ $debtor->name }}</h2>
</div>

<table>
    <thead>
        <tr>
            <th rowspan="2">ID Transaksi</th>
            <th rowspan="2">Tanggal</th>
            <th rowspan="2">Keterangan</th>
            <th colspan="2">Piutang</th>
            <th rowspan="2">Jumlah Piutang</th>
            <th colspan="2">Pembayaran</th>
            <th rowspan="2">Jumlah Pembayaran</th>
            <th colspan="2">Sisa Saldo</th>
            <th rowspan="2">Total</th>
        </tr>
        <tr>
            <th>Pokok</th>
            <th>Bagi Hasil</th>
            <th>Pokok</th>
            <th>Bagi Hasil</th>
            <th>Pokok</th>
            <th>Bagi Hasil</th>
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
                @php
                    $bagiPokok = $transaction['pokok'] ?? 0;
                    $bagiHasil = $transaction['hasil'] ?? 0;
                    $amount = $transaction['total'] ?? 0;
                    $type = $transaction['type'] ?? '';
                @endphp
                <tr>
                    <td>{{ $transaction['id'] ?? '' }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('Y-m-d') }}</td>
                    <td>{{ $transaction['description'] ?? '' }}</td>

                    @if ($type == 'piutang')
                        <td class="text-end">{{ abs($bagiPokok) }}</td>
                        <td class="text-end">{{ abs($bagiHasil) }}</td>
                        <td class="text-end">{{ abs($amount) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end">{{ abs($bagiPokok) }}</td>
                        <td class="text-end">{{ abs($bagiHasil) }}</td>
                        <td class="text-end">{{ abs($amount) }}</td>
                    @endif

                    @php
                        if (!str_starts_with($transaction['description'], 'Pembayaran menggunakan titipan')) {
                            $saldoPokok += $bagiPokok;
                            $saldoBagiHasil += $bagiHasil;
                            $saldoTotal += $amount;
                        }
                    @endphp

                    <td class="text-end">{{ $saldoPokok }}</td>
                    <td class="text-end">{{ $saldoBagiHasil }}</td>
                    <td class="text-end">{{ $saldoTotal }}</td>
                </tr>
            @endforeach
        @endif
        <tr style="font-weight: bold;">
            <td colspan="3" class="text-end"><strong>Total</strong></td>
            <td class="text-end">{{ $sortedEvents->where('type', 'piutang')->sum('pokok') }}</td>
            <td class="text-end">{{ $sortedEvents->where('type', 'piutang')->sum('hasil') }}</td>
            <td class="text-end">{{ $sortedEvents->where('type', 'piutang')->sum('total') }}</td>
            <td class="text-end">{{ $sortedEvents->whereIn('type', ['pembayaran', 'titipan_masuk'])->sum('pokok') }}</td>
            <td class="text-end">{{ $sortedEvents->whereIn('type', ['pembayaran', 'titipan_masuk'])->sum('hasil') }}</td>
            <td class="text-end">{{ $sortedEvents->whereIn('type', ['pembayaran', 'titipan_masuk'])->sum('total') }}</td>
            <td class="text-end">{{ $saldoPokok }}</td>
            <td class="text-end">{{ $saldoBagiHasil }}</td>
            <td class="text-end">{{ $saldoTotal }}</td>
        </tr>
    </tbody>
</table>

