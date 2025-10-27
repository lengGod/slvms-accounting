<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\Transaction;
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

        // Total Saldo (menghitung saldo dinamis dari accessor di model Debtor)
        $totalSaldo = Debtor::with('transactions', 'titipans')->get()->sum(function ($debtor) {
            return $debtor->current_balance;
        });

        // Aktivitas Terbaru (5 transaksi terakhir)
        $latestActivities = Transaction::with(['debtor', 'user'])
            ->orderByDesc('transaction_date')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalDebtors',
            'totalPiutang',
            'totalPembayaran',
            'totalSaldo',
            'latestActivities'
        ));
    }
}
