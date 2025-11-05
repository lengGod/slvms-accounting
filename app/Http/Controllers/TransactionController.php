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

            // Buat deskripsi yang menunjukkan penggunaan titipan
            $description = $validated['description'] ?? '';
            if ($usedTitipan > 0) {
                $description .= ($description ? ' | ' : '') .
                    'Pembayaran menggunakan titipan: Rp ' . number_format($usedTitipan, 0, ',', '.');
            }

            // Only create a piutang transaction if there's a remaining piutang
            if ($remainingPiutang > 0) {
                // Calculate proportional bagi_hasil and bagi_pokok for the remaining piutang
                $totalOriginalAllocation = ($validated['bagi_hasil'] ?? 0) + ($validated['bagi_pokok'] ?? 0);
                $remainingBagiHasil = 0;
                $remainingBagiPokok = 0;

                if ($totalOriginalAllocation > 0) {
                    if ($piutangAmount > 0) {
                        $ratio = $remainingPiutang / $piutangAmount;
                        $remainingBagiHasil = ($validated['bagi_hasil'] ?? 0) * $ratio;
                        $remainingBagiPokok = ($validated['bagi_pokok'] ?? 0) * $ratio;
                    } else {
                        $remainingBagiPokok = $remainingPiutang;
                    }
                } else {
                    // If no original allocation, assume remaining piutang is all pokok or all hasil, for simplicity, let's put it all in pokok if not specified.
                    $remainingBagiPokok = $remainingPiutang;
                }

                $createdTransaction = Transaction::create([
                    'debtor_id' => $validated['debtor_id'],
                    'type' => 'piutang',
                    'amount' => -$remainingPiutang,
                    'bagi_hasil' => -$remainingBagiHasil,
                    'bagi_pokok' => -$remainingBagiPokok,
                    'transaction_date' => $validated['transaction_date'],
                    'description' => $description,
                    'user_id' => auth()->id(),
                ]);
            } else {
                // If piutang is fully covered, create a transaction with amount 0 to record the event.
                $createdTransaction = Transaction::create([
                    'debtor_id' => $validated['debtor_id'],
                    'type' => 'piutang', // Still 'piutang' type, but amount is 0
                    'amount' => -($validated['amount'] ?? 0),
                    'bagi_hasil' => -($validated['bagi_hasil'] ?? 0),
                    'bagi_pokok' => -($validated['bagi_pokok'] ?? 0),
                    'transaction_date' => $validated['transaction_date'],
                    'description' => $description . ' (Lunas dengan titipan)',
                    'user_id' => auth()->id(),
                ]);
            }

            // Kurangi/hapus titipan yang digunakan
            if ($usedTitipan > 0) {
                $debtor->useTitipanForNewPiutang($usedTitipan, $createdTransaction->id);
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
            'amount' => 'required|numeric',
            'bagi_hasil' => 'nullable|numeric',
            'bagi_pokok' => 'nullable|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        // Jika transaksi adalah piutang
        if ($validated['type'] === 'piutang') {
            $validated['amount'] *= -1;
            if (isset($validated['bagi_pokok'])) {
                $validated['bagi_pokok'] *= -1;
            }
            if (isset($validated['bagi_hasil'])) {
                $validated['bagi_hasil'] *= -1;
            }

            $debtor = Debtor::find($validated['debtor_id']);

            // Cek apakah debitur memiliki titipan
            if ($debtor->total_titipan > 0) {
                // Redirect ke halaman konfirmasi penggunaan titipan
                return redirect()->route('transactions.createWithTitipanConfirmation', [
                    'debtor_id' => $validated['debtor_id'],
                    'amount' => abs($validated['amount']),
                    'bagi_hasil' => isset($validated['bagi_hasil']) ? abs($validated['bagi_hasil']) : 0,
                    'bagi_pokok' => isset($validated['bagi_pokok']) ? abs($validated['bagi_pokok']) : 0,
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

            if ($totalAlokasi > $validated['amount']) {
                return back()
                    ->withErrors(['amount' => 'Total alokasi (bagi hasil + bagi pokok) tidak boleh melebihi jumlah pembayaran'])
                    ->withInput();
            }

            $debtor = Debtor::find($validated['debtor_id']);

            // Correctly calculate the real outstanding debt
            $sisaPiutangReal = $debtor->current_balance < 0 ? abs($debtor->current_balance) : 0;

            // Helper function to add/update titipan


            // Case 1: No real debt. The entire payment becomes a titipan.
            if ($sisaPiutangReal <= 0) {
                DB::beginTransaction();
                try {
                    // Create a transaction record, the amount will be fully recorded as titipan
                    $pembayaran = Transaction::create(array_merge($validated, [
                        'amount' => $validated['amount'],
                        'bagi_pokok' => $bagiPokok,
                        'bagi_hasil' => $bagiHasil,
                        'description' => ($validated['description'] ?? '') . ' (Pembayaran menjadi titipan)',
                    ]));

                    $totalAlokasi = $bagiHasil + $bagiPokok;

                    // If allocation is missing or doesn't cover the full amount, default to pokok
                    if ($totalAlokasi < $validated['amount']) {
                        $bagiPokok += $validated['amount'] - $totalAlokasi;
                    }

                    $keterangan = 'Pembayaran menjadi titipan (Transaksi #' . $pembayaran->id . ')';
                    // Record the full payment as a positive titipan adjustment
                    $debtor->recordTitipanAdjustment($validated['amount'], $keterangan, $pembayaran->id, $bagiPokok, $bagiHasil);

                    DB::commit();
                    return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil. Karena piutang sudah lunas, pembayaran disimpan sebagai titipan.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
                }
            }

            // Case 2: Payment is more than the real debt. Pay off the debt, the rest becomes titipan.
            if ($sisaPiutangReal > 0 && $validated['amount'] > $sisaPiutangReal) {
                $kelebihan = $validated['amount'] - $sisaPiutangReal;

                $allocatedHasil = min($bagiHasil, $sisaPiutangReal);
                $allocatedPokok = min($bagiPokok, $sisaPiutangReal - $allocatedHasil);

                $titipanHasil = $bagiHasil - $allocatedHasil;
                $titipanPokok = $bagiPokok - $allocatedPokok;

                $totalAllocatedTitipan = $titipanPokok + $titipanHasil;
                if ($totalAllocatedTitipan < $kelebihan) {
                    $titipanPokok += $kelebihan - $totalAllocatedTitipan;
                }

                DB::beginTransaction();
                try {
                    // Create payment transaction only for the amount of the real debt
                    $pembayaran = Transaction::create(array_merge($validated, [
                        'amount' => $sisaPiutangReal,
                        'description' => $validated['description'] ?? 'Pembayaran pelunasan sisa piutang',
                        'bagi_hasil' => $allocatedHasil,
                        'bagi_pokok' => $allocatedPokok,
                    ]));

                    // Add the overpayment to titipan
                    $keterangan = 'Kelebihan pembayaran (Transaksi #' . $pembayaran->id . ')';
                    $debtor->recordTitipanAdjustment($kelebihan, $keterangan, $pembayaran->id, $titipanPokok, $titipanHasil);

                    DB::commit();
                    return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil. Piutang lunas dan kelebihan pembayaran disimpan sebagai titipan.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
                }
            }

            // If the code reaches here, it's a regular payment that doesn't clear the debt.
            // The final Transaction::create() at the end of the method will handle it.
        }

        // PERBAIKAN: Pastikan type transaksi sesuai dengan yang dipilih
        $transaction = Transaction::create($validated);

        // PERBAIKAN: Tambahkan pesan sukses yang sesuai dengan jenis transaksi
        $message = $validated['type'] === 'piutang' ? 'Piutang berhasil ditambahkan' : 'Pembayaran berhasil ditambahkan';

        return redirect()->route('transactions.index')->with('success', $message);
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
            'amount' => 'required|numeric',
            'bagi_hasil' => 'nullable|numeric',
            'bagi_pokok' => 'nullable|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        if ($validated['type'] === 'piutang') {
            $validated['amount'] *= -1;
            if (isset($validated['bagi_pokok'])) {
                $validated['bagi_pokok'] *= -1;
            }
            if (isset($validated['bagi_hasil'])) {
                $validated['bagi_hasil'] *= -1;
            }
        }

        // Validasi alokasi hanya untuk pembayaran
        if ($validated['type'] === 'pembayaran') {
            $bagiHasil = $validated['bagi_hasil'] ?? 0;
            $bagiPokok = $validated['bagi_pokok'] ?? 0;
            $totalAlokasi = $bagiHasil + $bagiPokok;

            if ($totalAlokasi > $validated['amount']) {
                return back()
                    ->withErrors(['amount' => 'Total alokasi (bagi hasil + bagi pokok) tidak boleh melebihi jumlah pembayaran'])
                    ->withInput();
            }

            $debtor = Debtor::find($validated['debtor_id']);

            // PERBAIKAN: Hitung sisa piutang dengan benar, dengan mempertimbangkan transaksi yang sedang diedit
            $totalPiutang = $debtor->total_piutang;
            $totalPembayaran = $debtor->total_pembayaran;

            // Jika transaksi yang diedit adalah pembayaran, kurangi dari total pembayaran
            if ($transaction->type === 'pembayaran') {
                $totalPembayaran -= $transaction->amount;
            }

            $sisaPiutang = $totalPiutang - $totalPembayaran;

            if ($sisaPiutang <= 0) {
                // Update transaksi pembayaran dengan amount 0, karena seluruhnya menjadi titipan
                $transaction->update(array_merge($validated, [
                    'amount' => $validated['amount'],
                    'bagi_pokok' => $validated['bagi_pokok'] ?? 0,
                    'bagi_hasil' => $validated['bagi_hasil'] ?? 0,
                    'description' => ($validated['description'] ?? '') . ' (Pembayaran menjadi titipan)',
                ]));

                // Refresh debtor untuk mendapatkan data terbaru
                $debtor = $debtor->fresh();

                // Tambahkan seluruh pembayaran sebagai titipan
                $debtor->recordTitipanAdjustment(
                    $validated['amount'],
                    'Penambahan dari pembayaran debitur (transaksi #' . $transaction->id . ')',
                    $transaction->id,
                    $validated['bagi_pokok'] ?? 0,
                    $validated['bagi_hasil'] ?? 0
                );

                return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil diperbarui dan disimpan sebagai titipan');
            }

            if ($sisaPiutang > 0 && $validated['amount'] > $sisaPiutang) {
                // PERBAIKAN: Hitung kelebihan pembayaran dengan benar
                $kelebihan = $validated['amount'] - $sisaPiutang;

                // PERBAIKAN: Update transaksi pembayaran dulu dengan jumlah sesuai sisa piutang
                $transaction->update([
                    'debtor_id' => $validated['debtor_id'],
                    'type' => 'pembayaran',
                    'amount' => $sisaPiutang,
                    'bagi_hasil' => $bagiHasil > $sisaPiutang ? $sisaPiutang : $bagiHasil,
                    'bagi_pokok' => $bagiPokok > 0 ? min($bagiPokok, $sisaPiutang - ($bagiHasil > $sisaPiutang ? $sisaPiutang : $bagiHasil)) : 0,
                    'transaction_date' => $validated['transaction_date'],
                    'description' => $validated['description'] ?? 'Pembayaran pelunasan piutang',
                    'user_id' => auth()->id(),
                ]);

                // Refresh debtor untuk mendapatkan data terbaru
                $debtor = $debtor->fresh();

                // PERBAIKAN: Tambahkan kelebihan ke titipan yang ada
                $existingTitipan = $debtor->titipans()->latest()->first();

                $debtor->recordTitipanAdjustment(
                    $kelebihan,
                    'Penambahan dari kelebihan pembayaran (transaksi #' . $transaction->id . ')',
                    $transaction->id,
                    0,
                    0
                );

                return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil diperbarui. Piutang telah lunas dan kelebihan pembayaran disimpan sebagai titipan sebesar Rp ' . number_format($kelebihan, 0, ',', '.'));
            }
        }

        $transaction->update($validated);

        // PERBAIKAN: Tambahkan pesan sukses yang sesuai dengan jenis transaksi
        $message = $validated['type'] === 'piutang' ? 'Piutang berhasil diperbarui' : 'Pembayaran berhasil diperbarui';

        return redirect()->route('transactions.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            // Delete associated Titipan records first
            Titipan::where('transaction_id', $transaction->id)->delete();

            // Then delete the transaction
            $transaction->delete();

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('transactions.index')->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
