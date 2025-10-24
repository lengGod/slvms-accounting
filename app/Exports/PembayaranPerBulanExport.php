<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PembayaranPerBulanExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function view(): View
    {
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[] = [
                'month' => \Carbon\Carbon::createFromDate($this->year, $month, 1)->format('F'),
                'total' => Transaction::whereYear('transaction_date', $this->year)
                    ->whereMonth('transaction_date', $month)
                    ->where('type', 'pembayaran')
                    ->sum('amount')
            ];
        }

        $totalYear = collect($monthlyData)->sum('total');

        return view('exports.pembayaran_perbulan', [
            'monthlyData' => $monthlyData,
            'year' => $this->year,
            'totalYear' => $totalYear
        ]);
    }

    public function title(): string
    {
        return 'Pembayaran Per Bulan';
    }
}
