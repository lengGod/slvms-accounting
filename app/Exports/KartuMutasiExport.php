<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KartuMutasiExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $debtorId;

    public function __construct($startDate, $endDate, $debtorId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->debtorId = $debtorId;
    }

    public function view(): View
    {
        $query = Transaction::with('debtor')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate]);

        if ($this->debtorId) {
            $query->where('debtor_id', $this->debtorId);
        }

        $transactions = $query->orderBy('transaction_date')->get();

        // Hitung saldo berjalan
        $runningBalance = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->type == 'piutang') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }
            $transaction->running_balance = $runningBalance;
        }

        return view('exports.kartu_mutasi', [
            'transactions' => $transactions,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }

    public function title(): string
    {
        return 'Kartu Mutasi';
    }
}
