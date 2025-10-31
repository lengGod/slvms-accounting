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

            $usedTitipan = min($availableTitipan, $piutangAmount);
            $remainingPiutang = $piutangAmount - $usedTitipan;

            // Kurangi titipan yang digunakan
            if ($usedTitipan > 0) {
                $debtor->useTitipanAmount($usedTitipan, 'Digunakan untuk piutang baru: ' . ($validated['description'] ?? 'Piutang baru'));
            }

            // Jika ada sisa piutang, buat transaksi piutang untuk sisanya
            if ($remainingPiutang > 0) {
                $pokokAmount = $validated['bagi_pokok'] ?? 0;
                $hasilAmount = $validated['bagi_hasil'] ?? 0;

                // Prorate sisa alokasi
                $remainingPokok = $pokokAmount * ($remainingPiutang / $piutangAmount);
                $remainingHasil = $hasilAmount * ($remainingPiutang / $piutangAmount);

                Transaction::create([
                    'debtor_id' => $validated['debtor_id'],
                    'type' => 'piutang',
                    'amount' => -$remainingPiutang,
                    'bagi_pokok' => -$remainingPokok,
                    'bagi_hasil' => -$remainingHasil,
                    'transaction_date' => $validated['transaction_date'],
                    'description' => 'Sisa piutang setelah menggunakan titipan: ' . ($validated['description'] ?? 'Piutang baru'),
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();

            $message = 'Proses piutang dengan titipan berhasil. ';
            $message .= 'Titipan digunakan: Rp ' . number_format($usedTitipan, 0, ',', '.') . '. ';
            if ($remainingPiutang > 0) {
                $message .= 'Sisa piutang: Rp ' . number_format($remainingPiutang, 0, ',', '.');
            } else {
                $message .= 'Seluruh piutang telah ditutupi oleh titipan.';
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
            if ($debtor->hasTitipan()) {
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

            // LOGIKA BARU: Hitung sisa piutang nyata dengan memperhitungkan titipan
            $sisaPiutangTransactional = $debtor->total_piutang - $debtor->total_pembayaran;
            $sisaPiutangReal = max(0, $sisaPiutangTransactional - $debtor->total_titipan);

            // Fungsi bantuan untuk menambah/memperbarui titipan
            $add_to_titipan = function ($amount, $keterangan, $bagiPokok, $bagiHasil) use ($debtor, $validated) {
                $existingTitipan = $debtor->titipans()->latest()->first();
                if ($existingTitipan) {
                    $existingTitipan->update([
                        'amount' => $existingTitipan->amount + $amount,
                        'bagi_pokok' => $existingTitipan->bagi_pokok + $bagiPokok,
                        'bagi_hasil' => $existingTitipan->bagi_hasil + $bagiHasil,
                        'keterangan' => $keterangan,
                        'tanggal' => $validated['transaction_date'],
                    ]);
                } else {
                    Titipan::create([
                        'debtor_id' => $debtor->id,
                        'amount' => $amount,
                        'bagi_pokok' => $bagiPokok,
                        'bagi_hasil' => $bagiHasil,
                        'tanggal' => $validated['transaction_date'],
                        'keterangan' => $keterangan,
                        'user_id' => auth()->id(),
                    ]);
                }
            };

            // Kasus 1: Tidak ada sisa piutang nyata. Seluruh pembayaran menjadi titipan.
            if ($sisaPiutangReal <= 0) {
                DB::beginTransaction();
                try {
                    // Langsung tambahkan ke titipan tanpa membuat transaksi pembayaran
                    $keterangan = 'Pembayaran langsung menjadi titipan';
                    $add_to_titipan($validated['amount'], $keterangan, $bagiPokok, $bagiHasil);

                    DB::commit();
                    return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil. Karena piutang sudah lunas, pembayaran disimpan sebagai titipan.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
                }
            }

            // Kasus 2: Pembayaran lebih besar dari sisa piutang nyata.
            // Bayar lunas sisa piutang, dan sisanya menjadi titipan.
            if ($sisaPiutangReal > 0 && $validated['amount'] > $sisaPiutangReal) {
                $kelebihan = $validated['amount'] - $sisaPiutangReal;

                $allocatedHasil = min($bagiHasil, $sisaPiutangReal);
                $allocatedPokok = min($bagiPokok, $sisaPiutangReal - $allocatedHasil);

                $titipanHasil = $bagiHasil - $allocatedHasil;
                $titipanPokok = $bagiPokok - $allocatedPokok;

                DB::beginTransaction();
                try {
                    // Buat transaksi pembayaran hanya sebesar sisa piutang nyata
                    $pembayaran = Transaction::create(array_merge($validated, [
                        'amount' => $sisaPiutangReal,
                        'description' => $validated['description'] ?? 'Pembayaran pelunasan sisa piutang',
                        'bagi_hasil' => $allocatedHasil,
                        'bagi_pokok' => $allocatedPokok,
                    ]));

                    // Tambahkan kelebihan pembayaran ke titipan
                    $keterangan = 'Kelebihan pembayaran (Transaksi #' . $pembayaran->id . ')';
                    $add_to_titipan($kelebihan, $keterangan, $titipanPokok, $titipanHasil);

                    DB::commit();
                    return redirect()->route('transactions.index')->with('success', 'Pembayaran berhasil. Piutang lunas dan kelebihan pembayaran disimpan sebagai titipan.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
                }
            }
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
                // PERBAIKAN: Update transaksi pembayaran dulu
                $transaction->update($validated);

                // Refresh debtor untuk mendapatkan data terbaru
                $debtor = $debtor->fresh();

                // PERBAIKAN: Tambahkan ke titipan yang ada, bukan buat yang baru
                $existingTitipan = $debtor->titipans()->latest()->first();

                if ($existingTitipan) {
                    // Update titipan yang ada dengan menambah amount
                    $existingTitipan->update([
                        'amount' => $existingTitipan->amount + $validated['amount'],
                        'keterangan' => 'Penambahan dari pembayaran debitur (transaksi #' . $transaction->id . ')',
                        'tanggal' => $validated['transaction_date'],
                    ]);
                } else {
                    // Jika tidak ada titipan sebelumnya, buat titipan baru
                    Titipan::create([
                        'debtor_id' => $validated['debtor_id'],
                        'amount' => $validated['amount'],
                        'tanggal' => $validated['transaction_date'],
                        'keterangan' => 'Pembayaran setelah lunas (titipan) dari transaksi #' . $transaction->id,
                        'user_id' => auth()->id(),
                    ]);
                }

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

                if ($existingTitipan) {
                    // Update titipan yang ada dengan menambah amount
                    $existingTitipan->update([
                        'amount' => $existingTitipan->amount + $kelebihan,
                        'keterangan' => 'Penambahan dari kelebihan pembayaran (transaksi #' . $transaction->id . ')',
                        'tanggal' => $validated['transaction_date'],
                    ]);
                } else {
                    // Jika tidak ada titipan sebelumnya, buat titipan baru
                    Titipan::create([
                        'debtor_id' => $validated['debtor_id'],
                        'amount' => $kelebihan,
                        'tanggal' => $validated['transaction_date'],
                        'keterangan' => 'Kelebihan pembayaran dari transaksi #' . $transaction->id,
                        'user_id' => auth()->id(),
                    ]);
                }

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
        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus');
    }
}
