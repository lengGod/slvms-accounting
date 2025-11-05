<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\Transaction;
use App\Models\Titipan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Debitur
        $totalDebtors = Debtor::count();

        // Total Piutang (semua transaksi tipe piutang)
        $totalPiutang = Transaction::where('type', 'piutang')->sum('amount');

        // Total Pembayaran (semua transaksi tipe pembayaran)
        $totalPembayaran = Transaction::where('type', 'pembayaran')->sum('amount');

        // Total Saldo Titipan
        $totalSaldoTitipan = Titipan::sum('amount');

        // Aktivitas Terbaru (5 transaksi terakhir)
        $latestActivities = Transaction::with(['debtor', 'user'])
            ->orderByDesc('transaction_date')
            ->take(5)
            ->get();

        // Total Saldo
        $totalSaldo = $totalSaldoTitipan;

        return view('dashboard', compact(
            'totalDebtors',
            'totalPiutang',
            'totalPembayaran',
            'totalSaldoTitipan',
            'latestActivities',
            'totalSaldo'
        ));
    }
}
