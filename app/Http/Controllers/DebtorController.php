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

        // Pencarian berdasarkan nama
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
            'initial_balance' => 'required|numeric',
            'initial_balance_type' => 'required|in:pokok,bagi_hasil',
            'joined_at' => 'nullable|date',
            'category' => 'nullable|string|max:50',
        ]);

        // Simpan debitur dengan saldo awal
        $debtor = Debtor::create($validated);

        // Tangani saldo awal
        $this->handleInitialBalance($debtor, $validated['initial_balance'], $validated['initial_balance_type'], $validated['joined_at'] ?? now());

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
            'initial_balance' => 'required|numeric',
            'initial_balance_type' => 'required|in:pokok,bagi_hasil',
            'joined_at' => 'nullable|date',
            'category' => 'nullable|string|max:50',
        ]);

        $oldInitialBalance = $debtor->initial_balance;
        $newInitialBalance = $validated['initial_balance'];

        // Update data debitur
        $debtor->update($validated);

        // Jika saldo awal berubah, perbarui transaksi/titipan awal
        if ($oldInitialBalance != $newInitialBalance) {
            // Hapus titipan/transaksi awal yang lama
            $this->removeInitialBalanceRecords($debtor);

            // Buat titipan/transaksi awal yang baru
            $this->handleInitialBalance($debtor, $newInitialBalance, $validated['initial_balance_type'], $validated['joined_at'] ?? now());
        }

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

    /**
     * Handle initial balance (positive = titipan, negative = piutang)
     */
    private function handleInitialBalance($debtor, $initialBalance, $initialBalanceType, $date)
    {
        if ($initialBalance > 0) {
            // Saldo awal positif = titipan
            Titipan::create([
                'debtor_id' => $debtor->id,
                'amount' => $initialBalance,
                'tanggal' => $date,
                'keterangan' => 'Saldo awal (Titipan ' . ucfirst($initialBalanceType) . ')',
                'user_id' => Auth::id(),
            ]);
        } elseif ($initialBalance < 0) {
            // Saldo awal negatif = piutang
            // Pastikan nilai positif untuk transaksi
            $amount = abs($initialBalance);

            // Hitung pembagian antara pokok dan bagi hasil
            $bagiPokok = $initialBalanceType == 'pokok' ? $amount : 0;
            $bagiHasil = $initialBalanceType == 'bagi_hasil' ? $amount : 0;

            // Jika perlu membagi rata (jika tidak ditentukan jenisnya)
            if ($initialBalanceType != 'pokok' && $initialBalanceType != 'bagi_hasil') {
                $bagiPokok = $amount / 2;
                $bagiHasil = $amount / 2;
            }

            Transaction::create([
                'debtor_id' => $debtor->id,
                'transaction_date' => $date,
                'type' => 'piutang',
                'amount' => $amount,
                'bagi_pokok' => $bagiPokok,
                'bagi_hasil' => $bagiHasil,
                'description' => 'Piutang awal (Saldo awal negatif)',
                'user_id' => Auth::id(),
            ]);
        }
        // Jika 0, tidak perlu buat apa-apa
    }

    /**
     * Remove initial balance records (titipan or transaction)
     */
    private function removeInitialBalanceRecords($debtor)
    {
        // Hapus titipan awal
        $debtor->titipans()->where('keterangan', 'like', '%Saldo awal%')->delete();

        // Hapus transaksi piutang awal
        $debtor->transactions()->where('description', 'like', '%Piutang awal%')->delete();
    }
}
