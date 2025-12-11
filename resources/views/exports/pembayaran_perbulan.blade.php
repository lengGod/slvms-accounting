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
    <h2>Laporan Pembayaran Per Bulan</h2>
</div>
<table>
    <thead>
        <tr>
            <th>Bulan</th>
            <th class="text-end">Total Pembayaran</th>
            <th class="text-end">Persentase</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($monthlyData as $data)
            <tr>
                <td>{{ $data['month'] }}</td>
                <td class="text-end">{{ $data['total'] }}</td>
                <td class="text-end">
                    @if ($totalYear > 0)
                        {{ round(($data['total'] / $totalYear) * 100, 1) }}%
                    @else
                        0%
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total Tahun {{ $year }}</strong></td>
            <td class="text-end"><strong>{{ $totalYear }}</strong></td>
            <td class="text-end"><strong>100%</strong></td>
        </tr>
    </tfoot>
</table>
