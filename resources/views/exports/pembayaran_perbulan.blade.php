<table>
    <thead>
        <tr>
            <th>Bulan</th>
            <th>Total Pembayaran</th>
            <th>Persentase</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($monthlyData as $data)
            <tr>
                <td>{{ $data['month'] }}</td>
                <td>Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                <td>
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
            <td><strong>Rp {{ number_format($totalYear, 0, ',', '.') }}</strong></td>
            <td><strong>100%</strong></td>
        </tr>
    </tfoot>
</table>
