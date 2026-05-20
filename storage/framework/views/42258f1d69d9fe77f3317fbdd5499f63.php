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
    <h3>Laporan Per Bulan: <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y')); ?></h3>
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
        <?php $__currentLoopData = $debtorsByCode->flatten(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debtor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($debtor->code ?: 'Tanpa Kode'); ?></td>
                <td><?php echo e($debtor->name); ?></td>
                <td class="text-end"><?php echo e($debtor->total_piutang); ?></td>
                <td class="text-end"><?php echo e($debtor->total_pembayaran); ?></td>
                <td class="text-end"><?php echo e($debtor->current_balance); ?></td>
                <td class="text-center"><?php echo e(ucfirst(str_replace('_', ' ', $debtor->debtor_status))); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><strong>Total</strong></td>
            <td class="text-end"><strong><?php echo e($totalPiutang); ?></strong></td>
            <td class="text-end"><strong><?php echo e($totalPembayaran); ?></strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
<?php /**PATH C:\laragon\www\slv-acounting\resources\views/exports/debit_piutang.blade.php ENDPATH**/ ?>