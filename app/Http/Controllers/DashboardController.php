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

        // Statistik Debitur Berdasarkan Status (Optimized using withSum)
        $debtorStats = Debtor::withSum('transactions as total_bagi_pokok', 'bagi_pokok')
            ->withSum('transactions as total_bagi_hasil', 'bagi_hasil')
            ->withSum('titipans as titipan_bagi_pokok', 'bagi_pokok')
            ->withSum('titipans as titipan_bagi_hasil', 'bagi_hasil')
            ->get();

        $piutangCount = 0;
        $lunasCount = 0;
        $titipanCount = 0;

        foreach ($debtorStats as $debtor) {
            $balance = ($debtor->total_bagi_pokok ?? 0) + ($debtor->total_bagi_hasil ?? 0) + 
                       ($debtor->titipan_bagi_pokok ?? 0) + ($debtor->titipan_bagi_hasil ?? 0);
            
            if ($balance < 0) {
                $piutangCount++;
            } elseif ($balance > 0) {
                $titipanCount++;
            } else {
                $lunasCount++;
            }
        }

        // Aktivitas Terbaru (5 transaksi terakhir)
        $latestActivities = Transaction::with(['debtor', 'user'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
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
            'totalSaldo',
            'piutangCount',
            'lunasCount',
            'titipanCount'
        ));
    }
}
