<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kartu Mutasi</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            color: #000;
        }
        h3 {
            text-align: center;
            font-size: 16pt;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th {
            background-color: #eee !important;
            font-weight: bold;
            text-align: left;
            border: 1px solid #000;
            padding: 10px;
        }
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body onload="window.print()">
    <h3>Laporan Kartu Mutasi</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nama Debitur</th>
                <th>Saldo Awal</th>
                <th>Saldo Akhir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($debtors as $debtor)
                <tr>
                    <td>{{ $debtor->name }}</td>
                    <td>{{ $debtor->formatted_initial_balance }}</td>
                    <td>{{ $debtor->formatted_balance }}</td>
                    <td>{{ $debtor->keterangan_piutang }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
