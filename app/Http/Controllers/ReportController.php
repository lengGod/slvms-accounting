<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Debtor;
use App\Models\Titipan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\KartuMutasiExport;
use App\Exports\PiutangPerBulanExport;
use App\Exports\PembayaranPerBulanExport;
use App\Exports\DebitPiutangExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display kartu mutasi report (daftar debitur)
     */
    public function kartuMutasi(Request $request)
    {
        $search = $request->search;
        $startDate = $request->start_date ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?: Carbon::now()->endOfMonth()->format('Y-m-d');

        // Ambil daftar debitur dengan filter pencarian
        $debtors = Debtor::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        })
            ->orderBy('name')
            ->paginate(10);

        return view('reports.kartuMutasi.index', compact('debtors', 'search', 'startDate', 'endDate'));
    }

    /**
     * Display kartu mutasi for specific debtor
     */
    public function showKartuMutasi($id, Request $request)
    {
        $startDate = $request->start_date ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?: Carbon::now()->endOfMonth()->format('Y-m-d');

        $debtor = Debtor::findOrFail($id);

        // Saldo awal sebelum tanggal yang difilter
        $saldoAwalPokok = Transaction::where('debtor_id', $id)
            ->where('transaction_date', '<', $startDate)
            ->sum('bagi_pokok');

        $saldoAwalBagiHasil = Transaction::where('debtor_id', $id)
            ->where('transaction_date', '<', $startDate)
            ->sum('bagi_hasil');

        $saldoAwalTotal = Transaction::where('debtor_id', $id)
            ->where('transaction_date', '<', $startDate)
            ->sum('amount');

        $transactions = Transaction::where('debtor_id', $id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->get();

        return view('reports.kartuMutasi.show', compact(
            'debtor',
            'startDate',
            'endDate',
            'transactions',
            'saldoAwalPokok',
            'saldoAwalBagiHasil',
            'saldoAwalTotal'
        ));
    }
    /**
     * Export kartu mutasi to Excel
     */
    public function exportKartuMutasi(Request $request)
    {
        $startDate = $request->start_date ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?: Carbon::now()->endOfMonth()->format('Y-m-d');
        $debtorId = $request->debtor_id;

        return Excel::download(new KartuMutasiExport($startDate, $endDate, $debtorId), 'kartu_mutasi_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Display piutang perbulan report
     */
    public function piutangPerBulan(Request $request)
    {
        $year = $request->year ?: Carbon::now()->year;

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[] = [
                'month' => Carbon::createFromDate($year, $month, 1)->format('F'),
                'total' => Transaction::whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month)
                    ->where('type', 'piutang')
                    ->sum('amount')
            ];
        }

        $years = range(Carbon::now()->year - 5, Carbon::now()->year + 1);
        $totalYear = collect($monthlyData)->sum('total');

        return view('reports.piutang_perbulan', compact('monthlyData', 'year', 'years', 'totalYear'));
    }

    /**
     * Export piutang perbulan to Excel
     */
    public function exportPiutangPerBulan(Request $request)
    {
        $year = $request->year ?: Carbon::now()->year;

        return Excel::download(new PiutangPerBulanExport($year), 'piutang_perbulan_' . $year . '.xlsx');
    }

    /**
     * Display pembayaran perbulan report
     */
    public function pembayaranPerBulan(Request $request)
    {
        $year = $request->year ?: Carbon::now()->year;

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[] = [
                'month' => Carbon::createFromDate($year, $month, 1)->format('F'),
                'total' => Transaction::whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month)
                    ->where('type', 'pembayaran')
                    ->sum('amount')
            ];
        }

        $years = range(Carbon::now()->year - 5, Carbon::now()->year + 1);
        $totalYear = collect($monthlyData)->sum('total');

        return view('reports.pembayaran_perbulan', compact('monthlyData', 'year', 'years', 'totalYear'));
    }

    /**
     * Export pembayaran perbulan to Excel
     */
    public function exportPembayaranPerBulan(Request $request)
    {
        $year = $request->year ?: Carbon::now()->year;

        return Excel::download(new PembayaranPerBulanExport($year), 'pembayaran_perbulan_' . $year . '.xlsx');
    }

    /**
     * Display debit piutang report
     */
    public function debitPiutang(Request $request)
    {
        $debtors = Debtor::with(['transactions' => function ($query) {
            $query->latest()->take(5);
        }])->get();

        $totalPiutang = Transaction::where('type', 'piutang')->sum('amount');
        $totalPembayaran = Transaction::where('type', 'pembayaran')->sum('amount');
        $totalSaldoTitipan = Titipan::sum('amount');

        return view('reports.debit_piutang', compact('debtors', 'totalPiutang', 'totalPembayaran', 'totalSaldoTitipan'));
    }

    /**
     * Export debit piutang to Excel
     */
    public function exportDebitPiutang(Request $request)
    {
        return Excel::download(new DebitPiutangExport(), 'debit_piutang_' . date('Y-m-d') . '.xlsx');
    }
}
