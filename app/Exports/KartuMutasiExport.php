<?php

namespace App\Exports;

use App\Models\Debtor;
use App\Models\Transaction;
use App\Models\Titipan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KartuMutasiExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $debtorId;

    public function __construct($debtorId)
    {
        $this->debtorId = $debtorId;
    }

    public function view(): View
    {
        $debtor = Debtor::findOrFail($this->debtorId);

        // Get all transactions and titipans for the debtor
        $transactions = Transaction::where('debtor_id', $this->debtorId)->get();
        $titipans = Titipan::where('debtor_id', $this->debtorId)->get();

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
            ]);
        }

        foreach ($titipans as $titipan) {
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
            ]);
        }

        $sortedEvents = $events->sortBy('date');

        return view('exports.kartu_mutasi', [
            'debtor' => $debtor,
            'sortedEvents' => $sortedEvents
        ]);
    }

    public function title(): string
    {
        return 'Kartu Mutasi';
    }
}
