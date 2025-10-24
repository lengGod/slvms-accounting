<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDebtors = Debtor::count();
        $totalPiutang = Transaction::tipe('piutang')->sum('amount');
        $totalPembayaran = Transaction::tipe('pembayaran')->sum('amount');

        // Hitung saldo dinamis dari accessor
        $totalSaldo = Debtor::with('transactions')->get()->sum(function ($debtor) {
            return $debtor->current_balance;
        });

        $latestActivities = Transaction::with(['debtor', 'user'])
            ->orderByDesc('transaction_date')
            ->take(5)
            ->get();

        $year = Carbon::now()->year;

        $piutangPerBulan = Transaction::tipe('piutang')
            ->whereYear('transaction_date', $year)
            ->selectRaw('MONTH(transaction_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $pembayaranPerBulan = Transaction::tipe('pembayaran')
            ->whereYear('transaction_date', $year)
            ->selectRaw('MONTH(transaction_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return view('dashboard', compact(
            'totalDebtors',
            'totalPiutang',
            'totalPembayaran',
            'totalSaldo',
            'latestActivities',
            'piutangPerBulan',
            'pembayaranPerBulan'
        ));
    }
}
