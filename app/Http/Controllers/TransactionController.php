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

    public function index(Request $request)
    {
        $query = Transaction::with(['debtor', 'user']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('debtor', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

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

    public function create()
    {
        $debtors = Debtor::all();
        return view('transactions.create', compact('debtors'));
    }

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
        if (!$debtor) {
            return redirect()->route('transactions.create')->with('error', 'Debitur tidak ditemukan.');
        }

        if ($debtor->total_titipan <= 0) {
            return redirect()->route('transactions.create')->with('error', 'Debitur tidak memiliki titipan.');
        }

        if ($validated['amount'] <= 0) {
            return redirect()->route('transactions.create')->with('error', 'Jumlah piutang harus lebih dari 0.');
        }

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
     * FIXED: Apply titipan when creating a new piutang
     * Now records FULL piutang amount and separate payment from titipan
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
            $debtor = Debtor::findOrFail($validated['debtor_id']);
            $piutangAmount = round($validated['amount'], 2);
            $piutangPokok = abs($validated['bagi_pokok'] ?? 0);
            $piutangHasil = abs($validated['bagi_hasil'] ?? 0);

            // If no allocation specified, assume all is pokok
            if ($piutangPokok == 0 && $piutangHasil == 0) {
                $piutangPokok = $piutangAmount;
            }

            // Calculate how much titipan will be used
            $availableTitipan = $debtor->total_titipan;
            $usedTitipan = min($availableTitipan, $piutangAmount);

            // STEP 1: Create FULL piutang transaction (not net amount)
            $piutangDescription = $validated['description'] ?? 'Piutang';
            if ($usedTitipan > 0) {
                $piutangDescription .= ' (Dibayar menggunakan titipan: Rp ' . number_format($usedTitipan, 0, ',', '.') . ')';
            }

            $piutangTx = Transaction::create([
                'debtor_id' => $debtor->id,
                'type' => 'piutang',
                'amount' => -1 * $piutangAmount, // FULL amount, not net!
                'bagi_hasil' => -1 * $piutangHasil,
                'bagi_pokok' => -1 * $piutangPokok,
                'transaction_date' => $validated['transaction_date'],
                'description' => $piutangDescription,
                'user_id' => auth()->id(),
            ]);

            // STEP 2: If titipan is used, create payment transaction and reduce titipan
            if ($usedTitipan > 0) {


                // Calculate proportional allocation for payment
                $paymentPokok = ($piutangPokok / $piutangAmount) * $usedTitipan;
                $paymentHasil = ($piutangHasil / $piutangAmount) * $usedTitipan;

                // Create payment transaction from titipan
                Transaction::create([
                    'debtor_id' => $debtor->id,
                    'type' => 'pembayaran',
                    'amount' => $usedTitipan,
                    'bagi_hasil' => $paymentHasil,
                    'bagi_pokok' => $paymentPokok,
                    'transaction_date' => $validated['transaction_date'],
                    'description' => 'Pembayaran menggunakan titipan untuk piutang #' . $piutangTx->id,
                    'user_id' => auth()->id(),
                ]);

                // Reduce titipan
                $debtor->useTitipanForNewPiutang($piutangAmount, $piutangTx->id, $piutangPokok, $piutangHasil);
            }

            DB::commit();

            $message = 'Piutang berhasil ditambahkan. ';
            if ($usedTitipan > 0) {
                $message .= 'Menggunakan titipan sebesar Rp ' . number_format($usedTitipan, 0, ',', '.') . '. ';
                $remainingPiutang = $piutangAmount - $usedTitipan;
                if ($remainingPiutang > 0) {
                    $message .= 'Sisa piutang yang belum dibayar: Rp ' . number_format($remainingPiutang, 0, ',', '.');
                } else {
                    $message .= 'Piutang sudah lunas dengan titipan.';
                }
            }

            return redirect()->route('transactions.index')->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('transactions.create')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'debtor_id' => 'required|exists:debtors,id',
            'type' => 'required|in:piutang,pembayaran',
            'amount' => 'required|numeric|min:0',
            'bagi_hasil' => 'nullable|numeric',
            'bagi_pokok' => 'nullable|numeric',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();
        $debtor = Debtor::findOrFail($validated['debtor_id']);

        // --- PIUTANG FLOW ---
        if ($validated['type'] === 'piutang') {
            $nominalPiutang = round($validated['amount'], 2);

            // If debtor has titipan, redirect to confirmation
            if ($debtor->total_titipan > 0) {
                return redirect()->route('transactions.createWithTitipanConfirmation', [
                    'debtor_id' => $debtor->id,
                    'amount' => $nominalPiutang,
                    'bagi_hasil' => isset($validated['bagi_hasil']) ? abs($validated['bagi_hasil']) : 0,
                    'bagi_pokok' => isset($validated['bagi_pokok']) ? abs($validated['bagi_pokok']) : 0,
                    'transaction_date' => $validated['transaction_date'],
                    'description' => $validated['description'] ?? '',
                ]);
            }

            // no titipan -> create piutang directly
            $validated['amount'] = -1 * $nominalPiutang;
            if (isset($validated['bagi_pokok'])) {
                $validated['bagi_pokok'] = -1 * round($validated['bagi_pokok'], 2);
            }
            if (isset($validated['bagi_hasil'])) {
                $validated['bagi_hasil'] = -1 * round($validated['bagi_hasil'], 2);
            }

            Transaction::create($validated);
            return redirect()->route('transactions.index')->with('success', 'Piutang berhasil ditambahkan.');
        }

        // --- PEMBAYARAN FLOW ---
        if ($validated['type'] === 'pembayaran') {
            $paymentAmount = round($validated['amount'], 2);
            $bagiHasil = $validated['bagi_hasil'] ?? 0;
            $bagiPokok = $validated['bagi_pokok'] ?? 0;

            if (($bagiHasil + $bagiPokok) > $paymentAmount) {
                return back()->withErrors(['amount' => 'Total alokasi (bagi hasil + bagi pokok) tidak boleh melebihi jumlah pembayaran'])->withInput();
            }

            $outstandingDebt = 0;
            if ($debtor->current_balance < 0) {
                $outstandingDebt = abs($debtor->current_balance);
            }

            DB::beginTransaction();
            try {
                // Case: no outstanding debt -> whole payment becomes titipan
                if ($outstandingDebt <= 0) {
                    $pembayaran = Transaction::create(array_merge($validated, [
                        'amount' => 0,
                        'bagi_pokok' => 0,
                        'bagi_hasil' => 0,
                        'description' => ($validated['description'] ?? '') . ' (Pembayaran menjadi titipan)',
                    ]));

                    $keterangan = 'Pembayaran menjadi titipan (Transaksi #' . $pembayaran->id . ')';
                    $debtor->recordTitipanAdjustment($paymentAmount, $keterangan, $pembayaran->id);

                    DB::commit();
                    return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil; disimpan sebagai titipan karena tidak ada piutang.');
                }

                // Case: payment >= outstandingDebt -> pay off debt, excess becomes titipan
                if ($paymentAmount >= $outstandingDebt) {
                    $allocatedHasil = min($bagiHasil, $outstandingDebt);
                    $allocatedPokok = min($bagiPokok, max(0, $outstandingDebt - $allocatedHasil));

                    $pembayaran = Transaction::create(array_merge($validated, [
                        'amount' => round($outstandingDebt, 2),
                        'description' => $validated['description'] ?? 'Pembayaran pelunasan sisa piutang',
                        'bagi_hasil' => $allocatedHasil,
                        'bagi_pokok' => $allocatedPokok,
                    ]));

                    $excess = round($paymentAmount - $outstandingDebt, 2);
                    if ($excess > 0) {
                        $keterangan = 'Kelebihan pembayaran (Transaksi #' . $pembayaran->id . ')';

                        $totalPaid = $allocatedHasil + $allocatedPokok;

                        if ($totalPaid > 0) {
                            $excessPokok = ($allocatedPokok / $totalPaid) * $excess;
                            $excessHasil = ($allocatedHasil / $totalPaid) * $excess;
                            $debtor->recordTitipanAdjustment($excess, $keterangan, $pembayaran->id, $excessPokok, $excessHasil);
                        } else {
                            $debtor->recordTitipanAdjustment($excess, $keterangan, $pembayaran->id);
                        }
                    }

                    DB::commit();
                    return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil; piutang lunas dan kelebihan disimpan sebagai titipan.');
                }

                // Case: payment < outstandingDebt -> partial payment
                $allocatedHasil = min($bagiHasil, $paymentAmount);
                $allocatedPokok = min($bagiPokok, max(0, $paymentAmount - $allocatedHasil));

                $remainingToAllocate = $paymentAmount - ($allocatedHasil + $allocatedPokok);
                if ($remainingToAllocate > 0) {
                    $allocatedPokok += $remainingToAllocate;
                }

                $pembayaran = Transaction::create(array_merge($validated, [
                    'amount' => round($paymentAmount, 2),
                    'bagi_hasil' => $allocatedHasil,
                    'bagi_pokok' => $allocatedPokok,
                ]));

                DB::commit();
                return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil dicatat; sisa piutang masih ada.');
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
            }
        }

        return redirect()->route('transactions.index')->with('error', 'Tipe transaksi tidak dikenali.');
    }

    public function show(Transaction $transaction)
    {
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $debtors = Debtor::all();
        return view('transactions.edit', compact('transaction', 'debtors'));
    }

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
        $debtor = Debtor::findOrFail($validated['debtor_id']);

        // Normalize amount based on type for comparison and later use
        $normalizedValidatedAmount = ($validated['type'] === 'piutang') ? -1 * abs(round($validated['amount'], 2)) : abs(round($validated['amount'], 2));
        $normalizedOriginalAmount = ($transaction->type === 'piutang') ? -1 * abs(round($transaction->amount, 2)) : abs(round($transaction->amount, 2));


        // If only date or description changed, and amount/type/allocation is the same, just update simple fields
        // Compare with original transaction values to determine if complex re-evaluation is needed
        if ($normalizedValidatedAmount == $normalizedOriginalAmount &&
            $validated['type'] == $transaction->type &&
            ($validated['bagi_pokok'] ?? 0) == ($transaction->bagi_pokok ?? 0) &&
            ($validated['bagi_hasil'] ?? 0) == ($transaction->bagi_hasil ?? 0) &&
            ($validated['transaction_date'] != $transaction->transaction_date ||
            $validated['description'] != $transaction->description))
        {
            $transaction->update([
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'],
                'user_id' => auth()->id(), // Ensure user_id is updated even on simple changes
            ]);
            $message = $validated['type'] === 'piutang' ? 'Piutang berhasil diperbarui' : 'Pembayaran berhasil diperbarui';
            return redirect()->route('transactions.index')->with('success', $message);
        }

        if ($validated['type'] === 'piutang') {
            $validated['amount'] = -1 * abs(round($validated['amount'], 2));
            if (isset($validated['bagi_pokok'])) $validated['bagi_pokok'] = -1 * abs(round($validated['bagi_pokok'], 2));
            if (isset($validated['bagi_hasil'])) $validated['bagi_hasil'] = -1 * abs(round($validated['bagi_hasil'], 2));
        } else {
            $validated['amount'] = abs(round($validated['amount'], 2));
        }

        if ($validated['type'] === 'pembayaran') {
            $bagiHasil = $validated['bagi_hasil'] ?? 0;
            $bagiPokok = $validated['bagi_pokok'] ?? 0;
            if (($bagiHasil + $bagiPokok) > $validated['amount']) {
                return back()->withErrors(['amount' => 'Total alokasi melebihi jumlah pembayaran'])->withInput();
            }

            $totalPiutang = $debtor->total_piutang;
            $totalPembayaran = $debtor->total_pembayaran;
            if ($transaction->type === 'pembayaran') {
                $totalPembayaran -= $transaction->amount;
            }
            $sisaPiutang = $totalPiutang - $totalPembayaran;

            if ($sisaPiutang <= 0) {
                $transaction->update(array_merge($validated, [
                    'amount' => 0,
                    'bagi_pokok' => 0,
                    'bagi_hasil' => 0,
                    'description' => ($validated['description'] ?? '') . ' (Pembayaran menjadi titipan)',
                ]));

                $debtor = $debtor->fresh();
                $debtor->recordTitipanAdjustment(
                    abs($validated['amount']),
                    'Penambahan dari pembayaran debitur (transaksi #' . $transaction->id . ')',
                    $transaction->id
                );

                return redirect()->route('transactions.index')->with('success', 'Pembayaran diperbarui menjadi titipan.');
            }

            if ($sisaPiutang > 0 && $validated['amount'] > $sisaPiutang) {
                $kelebihan = $validated['amount'] - $sisaPiutang;

                $transaction->update([
                    'debtor_id' => $validated['debtor_id'],
                    'type' => 'pembayaran',
                    'amount' => $sisaPiutang,
                    'bagi_hasil' => min($bagiHasil, $sisaPiutang),
                    'bagi_pokok' => min($bagiPokok, max(0, $sisaPiutang - min($bagiHasil, $sisaPiutang))),
                    'transaction_date' => $validated['transaction_date'],
                    'description' => $validated['description'] ?? 'Pembayaran pelunasan piutang',
                    'user_id' => auth()->id(),
                ]);

                $debtor = $debtor->fresh();
                $debtor->recordTitipanAdjustment(
                    $kelebihan,
                    'Penambahan dari kelebihan pembayaran (transaksi #' . $transaction->id . ')',
                    $transaction->id
                );

                return redirect()->route('transactions.index')->with('success', 'Pembayaran diperbarui; piutang lunas dan kelebihan disimpan sebagai titipan.');
            }
        }

        $transaction->update($validated);
        $message = $validated['type'] === 'piutang' ? 'Piutang berhasil diperbarui' : 'Pembayaran berhasil diperbarui';
        return redirect()->route('transactions.index')->with('success', $message);
    }

    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            $debtor = $transaction->debtor;

            $associatedTitipans = Titipan::where('transaction_id', $transaction->id)->get();
            foreach ($associatedTitipans as $titipan) {
                $debtor->recordTitipanAdjustment(
                    -1 * $titipan->amount,
                    'Pengembalian titipan dari transaksi yang dihapus #' . $transaction->id,
                    null,
                    -1 * $titipan->bagi_pokok,
                    -1 * $titipan->bagi_hasil
                );
            }

            Titipan::where('transaction_id', $transaction->id)->delete();
            $transaction->delete();

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus dan nominal dikembalikan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('transactions.index')->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
