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
use Illuminate\Support\Facades\DB;
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
        $debtor = Debtor::findOrFail($id);

        // Get all transactions and titipans for the debtor
        $transactions = Transaction::where('debtor_id', $id)->get();
        $titipans = Titipan::where('debtor_id', $id)->get();

        // Merge and sort transactions and titipans into a single event log
        $events = collect([]);

        foreach ($transactions as $transaction) {
            $events->push([
                'id' => $transaction->id,
                'date' => $transaction->transaction_date,
                'description' => $transaction->description,
                'type' => $transaction->type,
                'pokok' => $transaction->bagi_pokok,
                'hasil' => $transaction->bagi_hasil,
                'total' => $transaction->amount,
                'source' => 'transaction',
            ]);
        }

        foreach ($titipans as $titipan) {
            // FIX: Exclude titipan adjustments that are recorded alongside a 'pembayaran' transaction
            // to avoid visual duplication in the report. The 'pembayaran' transaction serves as the single recap.
            if (str_starts_with($titipan->keterangan, 'Penggunaan titipan untuk piutang')) {
                continue;
            }

            $events->push([
                'id' => $titipan->id,
                'date' => $titipan->tanggal,
                'description' => $titipan->keterangan,
                'type' => $titipan->amount > 0 ? 'titipan_masuk' : 'titipan_keluar',
                'pokok' => $titipan->bagi_pokok,
                'hasil' => $titipan->bagi_hasil,
                'total' => $titipan->amount,
                'source' => 'titipan',
            ]);
        }

        $sortedEvents = $events->sortBy('date');

        return view('reports.kartuMutasi.show', compact(
            'debtor',
            'sortedEvents'
        ));
    }
    /**
     * Export kartu mutasi to Excel
     */
    public function exportKartuMutasi(Request $request)
    {
        $debtorId = $request->debtor_id;

        return Excel::download(new KartuMutasiExport($debtorId), 'kartu_mutasi_' . date('Y-m-d') . '.xlsx');
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
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $endDate = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

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

        return view('reports.debit_piutang', [
            'debtorsByCode' => $debtorsByCode,
            'totalPiutang' => $totalPiutang,
            'totalPembayaran' => $totalPembayaran,
        ]);
    }

    /**
     * Export debit piutang to Excel
     */
    public function exportDebitPiutang(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        return Excel::download(new DebitPiutangExport($month), 'debit_piutang_' . $month . '.xlsx');
    }
}
