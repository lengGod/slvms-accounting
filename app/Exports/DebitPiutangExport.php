<?php

namespace App\Exports;

use App\Models\Debtor;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DebitPiutangExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $month;

    public function __construct(string $month)
    {
        $this->month = $month;
    }

    public function view(): View
    {
        $endDate = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();

        // Subquery for total piutang
        $piutangQuery = Transaction::selectRaw('sum(amount)')
            ->where('type', 'piutang')
            ->where('debtor_id', DB::raw('debtors.id'))
            ->where('transaction_date', '<=', $endDate);

        // Subquery for total pembayaran
        $pembayaranQuery = Transaction::selectRaw('sum(amount)')
            ->where('type', 'pembayaran')
            ->where('debtor_id', DB::raw('debtors.id'))
            ->where('transaction_date', '<=', $endDate);

        // Subquery for saldo pokok
        $pokokQuery = Transaction::selectRaw('sum(bagi_pokok)')
            ->where('debtor_id', DB::raw('debtors.id'))
            ->where('transaction_date', '<=', $endDate);

        // Subquery for saldo bagi hasil
        $hasilQuery = Transaction::selectRaw('sum(bagi_hasil)')
            ->where('debtor_id', DB::raw('debtors.id'))
            ->where('transaction_date', '<=', $endDate);

        $debtors = Debtor::select('id', 'name', 'code', 'phone')
            ->selectSub($piutangQuery, 'total_piutang')
            ->selectSub($pembayaranQuery, 'total_pembayaran')
            ->selectSub($pokokQuery, 'saldo_pokok')
            ->selectSub($hasilQuery, 'saldo_bagi_hasil')
            ->get();

        // Calculate balance and status, then filter
        $filteredDebtors = $debtors->map(function ($debtor) {
            $debtor->current_balance = ($debtor->saldo_pokok ?? 0) + ($debtor->saldo_bagi_hasil ?? 0);

            if ($debtor->current_balance < 0) {
                $debtor->debtor_status = 'belum_lunas';
            } elseif ($debtor->current_balance > 0) {
                $debtor->debtor_status = 'Titipan';
            } else {
                $debtor->debtor_status = 'lunas';
            }
            return $debtor;
        })->filter(function ($debtor) {
            return $debtor->debtor_status === 'belum_lunas';
        });

        // Group by code after filtering
        $debtorsByCode = $filteredDebtors->groupBy('code');

        // Calculate totals based on the selected month
        $totalPiutang = Transaction::where('type', 'piutang')->where('transaction_date', '<=', $endDate)->sum('amount');
        $totalPembayaran = Transaction::where('type', 'pembayaran')->where('transaction_date', '<=', $endDate)->sum('amount');

        return view('exports.debit_piutang', [
            'debtorsByCode' => $debtorsByCode,
            'totalPiutang' => $totalPiutang,
            'totalPembayaran' => $totalPembayaran,
            'month' => $this->month,
        ]);
    }

    public function title(): string
    {
        return 'Debit Piutang ' . $this->month;
    }
}
