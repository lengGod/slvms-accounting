<style>
    .header { font-weight: bold; font-size: 14pt; text-align: center; }
    .table-header { background-color: #d9e1f2; font-weight: bold; text-align: center; border: 1px solid #000; }
    .text-end { text-align: right; }
    .border { border: 1px solid #000; }
</style>

<table>
    <thead>
        <tr>
            <th colspan="12" class="header">LAPORAN KARTU MUTASI: {{ $debtor->name }}</th>
        </tr>
        <tr>
            <th colspan="12" class="header">Periode: {{ \Carbon\Carbon::now()->format('d F Y') }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="2" class="table-header">ID</th>
            <th rowspan="2" class="table-header">Tanggal</th>
            <th rowspan="2" class="table-header">Keterangan</th>
            <th colspan="2" class="table-header">Piutang</th>
            <th rowspan="2" class="table-header">Total Piutang</th>
            <th colspan="2" class="table-header">Pembayaran</th>
            <th rowspan="2" class="table-header">Total Bayar</th>
            <th colspan="2" class="table-header">Sisa Saldo</th>
        </tr>
        <tr>
            <th class="table-header">Pokok</th>
            <th class="table-header">Bagi Hasil</th>
            <th class="table-header">Pokok</th>
            <th class="table-header">Bagi Hasil</th>
            <th class="table-header">Pokok</th>
            <th class="table-header">Bagi Hasil</th>
        </tr>
    </thead>
    <tbody>
        @php
            $saldoPokok = 0;
            $saldoBagiHasil = 0;
        @endphp
        @foreach ($sortedEvents as $event)
            @php
                $pokok = $event['pokok'] ?? 0;
                $hasil = $event['hasil'] ?? 0;
                $total = $event['total'] ?? 0;
                $isPiutang = $event['type'] == 'piutang';

                if (!str_starts_with($event['description'], 'Pembayaran menggunakan titipan')) {
                    $saldoPokok += ($isPiutang ? $pokok : -$pokok);
                    $saldoBagiHasil += ($isPiutang ? $hasil : -$hasil);
                }
            @endphp
            <tr>
                <td class="border">{{ $event['id'] }}</td>
                <td class="border">{{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y') }}</td>
                <td class="border">{{ $event['description'] }}</td>
                <td class="border text-end">{{ $isPiutang ? abs($pokok) : 0 }}</td>
                <td class="border text-end">{{ $isPiutang ? abs($hasil) : 0 }}</td>
                <td class="border text-end">{{ $isPiutang ? abs($total) : 0 }}</td>
                <td class="border text-end">{{ !$isPiutang ? abs($pokok) : 0 }}</td>
                <td class="border text-end">{{ !$isPiutang ? abs($hasil) : 0 }}</td>
                <td class="border text-end">{{ !$isPiutang ? abs($total) : 0 }}</td>
                <td class="border text-end">{{ $saldoPokok }}</td>
                <td class="border text-end">{{ $saldoBagiHasil }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

