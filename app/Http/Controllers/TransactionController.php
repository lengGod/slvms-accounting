<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\Transaction;
use App\Models\Titipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,accounting');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['debtor', 'user']);

        // Pencarian berdasarkan ID atau nama debitur
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('debtor', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter berdasarkan rentang tanggal
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->whereDate('transaction_date', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        $transactions = $query->latest()->paginate(10)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $debtors = Debtor::all();
        return view('transactions.create', compact('debtors'));
    }

    /**
     * Show confirmation page for using titipan
     */
    public function createWithTitipanConfirmation(Request $request)
    {
        $validated = $request->validate([
            'debtor_id' => 'required|exists:debtors,id',
            'amount' => 'required|numeric|min:0',
            'bagi_hasil' => 'nullable|numeric|min:0',
            'bagi_pokok' => 'nullable|numeric|min:0',
            'transaction_date' => 'nullable|date',
            'description' => 'nullable|string|max:255',
        ]);

        $debtor = Debtor::find($validated['debtor_id']);

        // Cek apakah debitur memiliki titipan
        if (!$debtor->hasTitipan()) {
            return redirect()->route('transactions.create')
                ->with('error', 'Debitur tidak memiliki titipan yang dapat digunakan.');
        }

        // Cek apakah jumlah piutang valid
        if ($validated['amount'] <= 0) {
            return redirect()->route('transactions.create')
                ->with('error', 'Jumlah piutang harus lebih dari 0.');
        }

        // Hitung berapa titipan yang dapat digunakan
        $availableTitipan = $debtor->total_titipan;
        $piutangAmount = $validated['amount'];

        return view('transactions.use_titipan_confirmation', [
            'debtor' => $debtor,
            'piutangAmount' => $piutangAmount,
            'piutangAmountFormatted' => 'Rp ' . number_format($piutangAmount, 0, ',', '.'),
            'availableTitipan' => $availableTitipan,
            'usableTitipan' => min($availableTitipan, $piutangAmount),
            'usableTitipanFormatted' => 'Rp ' . number_format(min($availableTitipan, $piutangAmount), 0, ',', '.'),
            'remainingPiutang' => max(0, $piutangAmount - $availableTitipan),
            'remainingPiutangFormatted' => 'Rp ' . number_format(max(0, $piutangAmount - $availableTitipan), 0, ',', '.'),
            'canUseAllTitipan' => $availableTitipan >= $piutangAmount,
            'request' => $request
        ]);
    }

    /**
     * Process using titipan for new piutang
     */
    public function useTitipanForPiutang(Request $request)
    {
        $validated = $request->validate([
            'debtor_id' => 'required|exists:debtors,id',
            'amount' => 'required|numeric|min:0',
            'bagi_hasil' => 'nullable|numeric|min:0',
            'bagi_pokok' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $debtor = Debtor::find($validated['debtor_id']);
            $piutangAmount = $validated['amount'];
            $availableTitipan = $debtor->total_titipan;

            // Hitung berapa titipan yang akan digunakan
            $usedTitipan = min($availableTitipan, $piutangAmount);
            $remainingPiutang = $piutangAmount - $usedTitipan;

            // 1. Buat transaksi PIUTANG untuk jumlah penuh
            Transaction::create([
                'debtor_id' => $validated['debtor_id'],
                'type' => 'piutang',
                'amount' => $piutangAmount,
                'bagi_hasil' => $validated['bagi_hasil'] ?? 0,
                'bagi_pokok' => $validated['bagi_pokok'] ?? 0,
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'] ?? '',
                'user_id' => auth()->id(),
            ]);

            // 2. Buat transaksi PEMBAYARAN sejumlah titipan yang digunakan
            if ($usedTitipan > 0) {
                Transaction::create([
                    'debtor_id' => $validated['debtor_id'],
                    'type' => 'pembayaran',
                    'amount' => $usedTitipan,
                    'bagi_hasil' => 0,
                    'bagi_pokok' => 0,
                    'transaction_date' => $validated['transaction_date'],
                    'description' => 'Pembayaran menggunakan titipan',
                    'user_id' => auth()->id(),
                ]);

                // 3. Kurangi/hapus titipan yang digunakan
                $result = $debtor->useTitipanForNewPiutang($usedTitipan);
            }

            DB::commit();

            $message = 'Piutang berhasil ditambahkan. ';
            if ($usedTitipan > 0) {
                $message .= 'Menggunakan titipan sebesar Rp ' . number_format($usedTitipan, 0, ',', '.') . '. ';
            }
            if ($remainingPiutang > 0) {
                $message .= 'Sisa piutang yang belum dibayar: Rp ' . number_format($remainingPiutang, 0, ',', '.');
            } else {
                $message .= 'Piutang sudah lunas dengan titipan.';
            }

            return redirect()->route('transactions.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('transactions.create')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'debtor_id' => 'required|exists:debtors,id',
            'type' => 'required|in:piutang,pembayaran',
            'amount' => 'required|numeric|min:0',
            'bagi_hasil' => 'nullable|numeric|min:0',
            'bagi_pokok' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        // Jika transaksi adalah piutang
        if ($validated['type'] === 'piutang') {
            $debtor = Debtor::find($validated['debtor_id']);

            // Cek apakah debitur memiliki titipan
            if ($debtor->hasTitipan()) {
                // Redirect ke halaman konfirmasi penggunaan titipan
                return redirect()->route('transactions.createWithTitipanConfirmation', [
                    'debtor_id' => $validated['debtor_id'],
                    'amount' => $validated['amount'],
                    'bagi_hasil' => $validated['bagi_hasil'] ?? 0,
                    'bagi_pokok' => $validated['bagi_pokok'] ?? 0,
                    'transaction_date' => $validated['transaction_date'],
                    'description' => $validated['description'] ?? '',
                ]);
            }
        }

        // Validasi alokasi hanya untuk pembayaran
        if ($validated['type'] === 'pembayaran') {
            $bagiHasil = $validated['bagi_hasil'] ?? 0;
            $bagiPokok = $validated['bagi_pokok'] ?? 0;
            $totalAlokasi = $bagiHasil + $bagiPokok;

            // Pastikan total alokasi tidak melebihi jumlah pembayaran
            if ($totalAlokasi > $validated['amount']) {
                return back()
                    ->withErrors(['amount' => 'Total alokasi (bagi hasil + bagi pokok) tidak boleh melebihi jumlah pembayaran'])
                    ->withInput();
            }

            $debtor = Debtor::find($validated['debtor_id']);
            $currentBalance = $debtor->current_balance;

            // Jika saldo sudah lunas (0) atau positif, semua pembayaran menjadi titipan
            if ($currentBalance >= 0) {
                // Simpan seluruh pembayaran sebagai titipan
                Titipan::create([
                    'debtor_id' => $validated['debtor_id'],
                    'amount' => $validated['amount'],
                    'tanggal' => $validated['transaction_date'],
                    'keterangan' => 'Pembayaran setelah lunas (titipan) dari transaksi #' . time(),
                    'user_id' => auth()->id(),
                ]);

                // Tidak perlu menyimpan transaksi pembayaran karena sudah menjadi titipan
                return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil disimpan sebagai titipan');
            }

            // Jika masih ada piutang (saldo negatif)
            if ($currentBalance < 0 && $validated['amount'] > abs($currentBalance)) {
                $kelebihan = $validated['amount'] - abs($currentBalance);

                // Simpan kelebihan sebagai titipan
                Titipan::create([
                    'debtor_id' => $validated['debtor_id'],
                    'amount' => $kelebihan,
                    'tanggal' => $validated['transaction_date'],
                    'keterangan' => 'Kelebihan pembayaran dari transaksi #' . time(),
                    'user_id' => auth()->id(),
                ]);

                // Ubah jumlah pembayaran menjadi sama dengan saldo piutang
                $validated['amount'] = abs($currentBalance);

                // Sesuaikan alokasi jika perlu
                if ($totalAlokasi > $validated['amount']) {
                    $validated['bagi_hasil'] = min($bagiHasil, $validated['amount']);
                    $validated['bagi_pokok'] = min($bagiPokok, $validated['amount'] - $validated['bagi_hasil']);
                }
            }
        }

        $transaction = Transaction::create($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        $debtors = Debtor::all();
        return view('transactions.edit', compact('transaction', 'debtors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'debtor_id' => 'required|exists:debtors,id',
            'type' => 'required|in:piutang,pembayaran',
            'amount' => 'required|numeric|min:0',
            'bagi_hasil' => 'nullable|numeric|min:0',
            'bagi_pokok' => 'nullable|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        // Validasi alokasi hanya untuk pembayaran
        if ($validated['type'] === 'pembayaran') {
            $bagiHasil = $validated['bagi_hasil'] ?? 0;
            $bagiPokok = $validated['bagi_pokok'] ?? 0;
            $totalAlokasi = $bagiHasil + $bagiPokok;

            // Pastikan total alokasi tidak melebihi jumlah pembayaran
            if ($totalAlokasi > $validated['amount']) {
                return back()
                    ->withErrors(['amount' => 'Total alokasi (bagi hasil + bagi pokok) tidak boleh melebihi jumlah pembayaran'])
                    ->withInput();
            }

            $debtor = Debtor::find($validated['debtor_id']);
            // Hitung saldo tanpa transaksi ini
            $currentBalance = $debtor->current_balance + $transaction->amount;

            // Jika saldo sudah lunas (0) atau positif, semua pembayaran menjadi titipan
            if ($currentBalance >= 0) {
                // Simpan seluruh pembayaran sebagai titipan
                Titipan::create([
                    'debtor_id' => $validated['debtor_id'],
                    'amount' => $validated['amount'],
                    'tanggal' => $validated['transaction_date'],
                    'keterangan' => 'Pembayaran setelah lunas (titipan) dari transaksi #' . $transaction->id,
                    'user_id' => auth()->id(),
                ]);

                // Hapus transaksi pembayaran karena sudah menjadi titipan
                $transaction->delete();

                return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil disimpan sebagai titipan');
            }

            // Jika masih ada piutang (saldo negatif)
            if ($currentBalance < 0 && $validated['amount'] > abs($currentBalance)) {
                $kelebihan = $validated['amount'] - abs($currentBalance);

                // Simpan kelebihan sebagai titipan
                Titipan::create([
                    'debtor_id' => $validated['debtor_id'],
                    'amount' => $kelebihan,
                    'tanggal' => $validated['transaction_date'],
                    'keterangan' => 'Kelebihan pembayaran dari transaksi #' . $transaction->id,
                    'user_id' => auth()->id(),
                ]);

                // Ubah jumlah pembayaran menjadi sama dengan saldo piutang
                $validated['amount'] = abs($currentBalance);

                // Sesuaikan alokasi jika perlu
                if ($totalAlokasi > $validated['amount']) {
                    $validated['bagi_hasil'] = min($bagiHasil, $validated['amount']);
                    $validated['bagi_pokok'] = min($bagiPokok, $validated['amount'] - $validated['bagi_hasil']);
                }
            }
        }

        $transaction->update($validated);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus');
    }
}
