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
            <?php $__currentLoopData = $debtors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debtor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($debtor->name); ?></td>
                    <td><?php echo e($debtor->formatted_initial_balance); ?></td>
                    <td><?php echo e($debtor->formatted_balance); ?></td>
                    <td><?php echo e($debtor->keterangan_piutang); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/reports/kartuMutasi/print.blade.php ENDPATH**/ ?>