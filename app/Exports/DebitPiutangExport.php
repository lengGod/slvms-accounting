<?php

namespace App\Exports;

use App\Models\Debtor;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DebitPiutangExport implements FromView, WithTitle, ShouldAutoSize
{
    public function view(): View
    {
        $debtors = Debtor::with(['transactions' => function ($query) {
            $query->latest()->take(5);
        }])->get();

        $totalPiutang = Transaction::where('type', 'piutang')->sum('amount');
        $totalPembayaran = Transaction::where('type', 'pembayaran')->sum('amount');
        $saldoAkhir = $totalPiutang - $totalPembayaran;

        return view('exports.debit_piutang', [
            'debtors' => $debtors,
            'totalPiutang' => $totalPiutang,
            'totalPembayaran' => $totalPembayaran,
            'saldoAkhir' => $saldoAkhir
        ]);
    }

    public function title(): string
    {
        return 'Debit Piutang';
    }
}
