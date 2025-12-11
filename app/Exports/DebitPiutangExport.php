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
        $allDebtors = Debtor::with(['transactions' => function ($query) {
            $query->latest()->take(5);
        }])->get();

        $debtorsByCode = $allDebtors
            ->filter(function ($debtor) {
                return $debtor->debtor_status === 'belum_lunas';
            })
            ->groupBy('code');

        $totalPiutang = Transaction::where('type', 'piutang')->sum('amount');
        $totalPembayaran = Transaction::where('type', 'pembayaran')->sum('amount');

        return view('exports.debit_piutang', [
            'debtorsByCode' => $debtorsByCode,
            'totalPiutang' => $totalPiutang,
            'totalPembayaran' => $totalPembayaran,
        ]);
    }

    public function title(): string
    {
        return 'Debit Piutang';
    }
}
