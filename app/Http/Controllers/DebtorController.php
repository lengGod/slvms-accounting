<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\Transaction;
use App\Models\Titipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DebtorController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Debtor::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $debtors = $query->latest()->paginate(10)->withQueryString();

        return view('debtors.index', compact('debtors'));
    }

    public function create()
    {
        return view('debtors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'balance_type' => 'required|array|min:1',
            'balance_type.*' => 'in:pokok,bagi_hasil',
            'pokok_balance' => 'required_if:balance_type,pokok|nullable|numeric',
            'bagi_hasil_balance' => 'required_if:balance_type,bagi_hasil|nullable|numeric',
            'joined_at' => 'nullable|date',
            'category' => 'nullable|string|max:50',
        ]);

        // Hitung total saldo awal
        $pokokBalance = in_array('pokok', $validated['balance_type']) ? ($validated['pokok_balance'] ?? 0) : 0;
        $bagiHasilBalance = in_array('bagi_hasil', $validated['balance_type']) ? ($validated['bagi_hasil_balance'] ?? 0) : 0;
        $totalBalance = $pokokBalance + $bagiHasilBalance;

        // Gabungkan array jenis saldo menjadi string
        $balanceType = implode(',', $validated['balance_type']);

        // Simpan debitur
        $debtor = Debtor::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'initial_balance' => $totalBalance,
            'initial_pokok_balance' => $pokokBalance,
            'initial_bagi_hasil_balance' => $bagiHasilBalance,
            'initial_balance_type' => $balanceType,
            'joined_at' => $validated['joined_at'] ?? now(),
            'category' => $validated['category'],
        ]);

        // Tangani saldo awal
        $this->handleInitialBalance($debtor, $pokokBalance, $bagiHasilBalance, $balanceType, $validated['joined_at'] ?? now());

        return redirect()->route('debtors.index')->with('success', 'Debitur berhasil ditambahkan');
    }

    public function show(Debtor $debtor)
    {
        $transactions = Transaction::where('debtor_id', $debtor->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('debtors.show', compact('debtor', 'transactions'));
    }

    public function edit(Debtor $debtor)
    {
        return view('debtors.edit', compact('debtor'));
    }

    public function update(Request $request, Debtor $debtor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'balance_type' => 'required|array|min:1',
            'balance_type.*' => 'in:pokok,bagi_hasil',
            'pokok_balance' => 'required_if:balance_type,pokok|nullable|numeric',
            'bagi_hasil_balance' => 'required_if:balance_type,bagi_hasil|nullable|numeric',
            'joined_at' => 'nullable|date',
            'category' => 'nullable|string|max:50',
        ]);

        // Hitung total saldo awal
        $pokokBalance = in_array('pokok', $validated['balance_type']) ? ($validated['pokok_balance'] ?? 0) : 0;
        $bagiHasilBalance = in_array('bagi_hasil', $validated['balance_type']) ? ($validated['bagi_hasil_balance'] ?? 0) : 0;
        $totalBalance = $pokokBalance + $bagiHasilBalance;

        // Gabungkan array jenis saldo menjadi string
        $balanceType = implode(',', $validated['balance_type']);

        // Update data debitur
        $debtor->update([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'initial_balance' => $totalBalance,
            'initial_pokok_balance' => $pokokBalance,
            'initial_bagi_hasil_balance' => $bagiHasilBalance,
            'initial_balance_type' => $balanceType,
            'joined_at' => $validated['joined_at'] ?? $debtor->joined_at,
            'category' => $validated['category'],
        ]);

        // Jika saldo awal berubah, perbarui transaksi/titipan awal
        $this->removeInitialBalanceRecords($debtor);
        $this->handleInitialBalance($debtor, $pokokBalance, $bagiHasilBalance, $balanceType, $validated['joined_at'] ?? now());

        return redirect()->route('debtors.index')->with('success', 'Debitur berhasil diperbarui');
    }

    public function destroy(Debtor $debtor)
    {
        $relations = $debtor->checkRelations();

        if (!empty($relations)) {
            return back()->with('error', 'Debitur tidak dapat dihapus karena masih memiliki relasi: ' . implode(', ', array_keys($relations)));
        }

        $debtor->delete();

        return redirect()->route('debtors.index')->with('success', 'Debitur berhasil dihapus');
    }

    public function checkTitipan($id)
    {
        $debtor = Debtor::find($id);

        if (!$debtor) {
            return response()->json(['success' => false, 'message' => 'Debitur tidak ditemukan']);
        }

        return response()->json([
            'success' => true,
            'has_titipan' => $debtor->hasTitipan(),
            'total_titipan' => $debtor->total_titipan,
            'formatted_total_titipan' => 'Rp ' . number_format($debtor->total_titipan, 0, ',', '.')
        ]);
    }

    public function checkRelations($id)
    {
        $debtor = Debtor::find($id);

        if (!$debtor) {
            return response()->json(['success' => false, 'message' => 'Debitur tidak ditemukan']);
        }

        $relations = $debtor->checkRelations();

        return response()->json([
            'success' => true,
            'has_relations' => !empty($relations),
            'relations' => $relations
        ]);
    }

    private function handleInitialBalance($debtor, $pokokBalance, $bagiHasilBalance, $balanceType, $date)
    {
        $totalAmount = $pokokBalance + $bagiHasilBalance;

        if ($totalAmount == 0) {
            return; // No action needed
        }

        // Create a transaction for every initial balance to ensure an audit trail
        $transactionType = $totalAmount < 0 ? 'piutang' : 'pembayaran';
        $transaction = Transaction::create([
            'debtor_id' => $debtor->id,
            'transaction_date' => $date,
            'type' => $transactionType,
            'amount' => $totalAmount,
            'bagi_pokok' => $pokokBalance,
            'bagi_hasil' => $bagiHasilBalance,
            'description' => 'Saldo Awal',
            'user_id' => Auth::id(),
        ]);

        // If the initial balance is positive, it must also be reflected in the titipans table
        if ($totalAmount > 0) {
            Titipan::create([
                'debtor_id' => $debtor->id,
                'amount' => $totalAmount,
                'bagi_pokok' => $pokokBalance,
                'bagi_hasil' => $bagiHasilBalance,
                'tanggal' => $date,
                'keterangan' => 'Saldo awal dari pembuatan debitur (Ref Transaksi #' . $transaction->id . ')',
                'user_id' => Auth::id(),
            ]);
        }
    }

    /**
     * Remove initial balance records (titipan or transaction)
     */
    private function removeInitialBalanceRecords($debtor)
    {
        $debtor->titipans()->where('keterangan', 'like', '%Saldo awal%')->delete();
        $debtor->transactions()->where('description', 'Saldo Awal')->delete();
    }
}
