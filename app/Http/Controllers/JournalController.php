<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JournalController extends Controller
{
    /**
     * Display a listing of the journal transactions.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['debtor', 'user']);

        // Filter berdasarkan tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Filter berdasarkan tipe
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan pencarian
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                    ->orWhereHas('debtor', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Urutkan berdasarkan tanggal terbaru
        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        // PERBAIKAN: Hitung total dengan query baru yang tidak terpengaruh pagination
        $totalQuery = Transaction::query();

        // Terapkan filter yang sama ke totalQuery
        if ($request->start_date && $request->end_date) {
            $totalQuery->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $totalQuery->whereDate('transaction_date', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $totalQuery->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->type) {
            $totalQuery->where('type', $request->type);
        }

        if ($request->status) {
            $totalQuery->where('status', $request->status);
        }

        if ($request->search) {
            $totalQuery->where(function ($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                    ->orWhereHas('debtor', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Hitung total piutang dan pembayaran
        $totalPiutang = (clone $totalQuery)->where('type', 'piutang')->sum('amount');
        $totalPembayaran = (clone $totalQuery)->where('type', 'pembayaran')->sum('amount');

        return view('journal.index', compact('transactions', 'totalPiutang', 'totalPembayaran'));
    }

    /**
     * Display the specified journal transaction.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['debtor', 'user'])->findOrFail($id);
        return view('journal.show', compact('transaction'));
    }
}
