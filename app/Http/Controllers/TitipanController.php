<?php

namespace App\Http\Controllers;

use App\Models\Titipan;
use App\Models\Debtor;
use Illuminate\Http\Request;

class TitipanController extends Controller
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
        $query = Titipan::with(['debtor', 'user']);

        // Pencarian berdasarkan nama debitur
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('debtor', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan rentang tanggal
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->whereDate('tanggal', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        $titipans = $query->latest()->paginate(10)->withQueryString();

        return view('titipans.index', compact('titipans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $debtors = Debtor::all();
        return view('titipans.create', compact('debtors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'debtor_id' => 'required|exists:debtors,id',
            'amount' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        Titipan::create($validated);

        return redirect()->route('titipans.index')->with('success', 'Titipan berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Titipan $titipan)
    {
        $debtors = Debtor::all();
        return view('titipans.edit', compact('titipan', 'debtors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Titipan $titipan)
    {
        $validated = $request->validate([
            'debtor_id' => 'required|exists:debtors,id',
            'amount' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        $titipan->update($validated);

        return redirect()->route('titipans.index')->with('success', 'Titipan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Titipan $titipan)
    {
        $titipan->delete();

        return redirect()->route('titipans.index')->with('success', 'Titipan berhasil dihapus');
    }
}
