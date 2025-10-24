<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Debtor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'initial_balance',
        'joined_at',
        'category',
        'initial_balance_type',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'joined_at' => 'date',
    ];

    protected $appends = [
        'current_balance',
        'total_piutang',
        'total_pembayaran',
        'total_titipan',
        'saldo_akhir',
        'saldo_pokok',
        'saldo_bagi_hasil',
        'formatted_balance',
        'formatted_saldo_pokok',
        'formatted_saldo_bagi_hasil',
        'formatted_initial_balance',
        'formatted_joined_at',
        'debtor_status',
        'keterangan_piutang',
        'initial_balance_with_type'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function titipans()
    {
        return $this->hasMany(Titipan::class);
    }

    /**
     * Search debtors by name or other attributes
     */
    public static function search($keyword)
    {
        return self::where('name', 'like', '%' . $keyword . '%')
            ->orWhere('address', 'like', '%' . $keyword . '%')
            ->orWhere('phone', 'like', '%' . $keyword . '%')
            ->get();
    }

    /**
     * Get the current balance attribute
     * PERBAIKAN: Saldo saat ini = posisi dana sebenarnya
     * Saldo Saat Ini = (Total Pembayaran + Total Titipan) - Total Piutang
     * CATATAN: initial_balance TIDAK dihitung, hanya untuk tampilan/history
     */
    public function getCurrentBalanceAttribute()
    {
        // Hitung saldo dari transaksi saja (tanpa initial_balance)
        $transactionBalance = $this->total_pembayaran - $this->total_piutang;

        // Tambahkan titipan ke saldo
        // Jika ada titipan, akan menambah saldo (positif)
        // Jika piutang > pembayaran+titipan, akan negatif (hutang bersih)
        return $transactionBalance + $this->total_titipan;
    }

    /**
     * Get the formatted balance attribute
     */
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->current_balance, 0, ',', '.');
    }

    /**
     * Get the formatted initial balance attribute
     */
    public function getFormattedInitialBalanceAttribute()
    {
        return 'Rp ' . number_format($this->initial_balance, 0, ',', '.');
    }

    /**
     * Get the formatted joined at attribute
     */
    public function getFormattedJoinedAtAttribute()
    {
        return $this->joined_at ? $this->joined_at->format('d M Y') : '-';
    }

    /**
     * Get the total piutang attribute
     */
    public function getTotalPiutangAttribute()
    {
        return $this->transactions()
            ->where('type', 'piutang')
            ->sum('amount');
    }

    /**
     * Get the total pembayaran attribute
     */
    public function getTotalPembayaranAttribute()
    {
        return $this->transactions()
            ->where('type', 'pembayaran')
            ->sum('amount');
    }

    /**
     * Get the total titipan attribute
     */
    public function getTotalTitipanAttribute()
    {
        return $this->titipans()->sum('amount');
    }

    /**
     * Get the formatted total titipan
     */
    public function getFormattedTotalTitipanAttribute()
    {
        return 'Rp ' . number_format($this->total_titipan, 0, ',', '.');
    }

    /**
     * Get the saldo akhir attribute
     * Saldo akhir = sama dengan current_balance (sudah termasuk titipan)
     * Ini untuk kompatibilitas dengan kode yang sudah ada
     */
    public function getSaldoAkhirAttribute()
    {
        return $this->current_balance;
    }

    /**
     * Get the saldo pokok attribute
     */
    public function getSaldoPokokAttribute()
    {
        $totalPiutangPokok = $this->transactions()
            ->where('type', 'piutang')
            ->sum('bagi_pokok');

        $totalPembayaranPokok = $this->transactions()
            ->where('type', 'pembayaran')
            ->sum('bagi_pokok');

        return $totalPembayaranPokok - $totalPiutangPokok;
    }

    /**
     * Get the formatted saldo pokok attribute
     */
    public function getFormattedSaldoPokokAttribute()
    {
        return 'Rp ' . number_format($this->saldo_pokok, 0, ',', '.');
    }

    /**
     * Get the saldo bagi hasil attribute
     */
    public function getSaldoBagiHasilAttribute()
    {
        $totalPiutangHasil = $this->transactions()
            ->where('type', 'piutang')
            ->sum('bagi_hasil');

        $totalPembayaranHasil = $this->transactions()
            ->where('type', 'pembayaran')
            ->sum('bagi_hasil');

        return $totalPembayaranHasil - $totalPiutangHasil;
    }

    /**
     * Get the formatted saldo bagi hasil attribute
     */
    public function getFormattedSaldoBagiHasilAttribute()
    {
        return 'Rp ' . number_format($this->saldo_bagi_hasil, 0, ',', '.');
    }

    /**
     * Get the debtor status attribute
     */
    public function getDebtorStatusAttribute()
    {
        if ($this->current_balance > 0) {
            return 'lebih_bayar';
        } elseif ($this->current_balance < 0) {
            return 'belum_lunas';
        } else {
            return 'lunas';
        }
    }

    /**
     * Get the keterangan piutang attribute
     */
    public function getKeteranganPiutangAttribute()
    {
        if ($this->current_balance > 0) {
            return 'Lebih bayar';
        } elseif ($this->current_balance < 0) {
            return 'Memiliki piutang';
        } else {
            return 'Lunas';
        }
    }

    /**
     * Get the initial balance with type information
     */
    public function getInitialBalanceWithTypeAttribute()
    {
        if ($this->initial_balance != 0) {
            return [
                'amount' => $this->initial_balance,
                'type' => $this->initial_balance_type,
                'formatted' => 'Rp ' . number_format(abs($this->initial_balance), 0, ',', '.'),
                'is_negative' => $this->initial_balance < 0,
                'is_titipan' => $this->initial_balance > 0
            ];
        }

        return null;
    }

    /**
     * Check if debtor has any titipan
     */
    public function hasTitipan()
    {
        return $this->titipans()->where('amount', '>', 0)->exists();
    }

    /**
     * Gunakan titipan untuk membayar piutang baru
     * Method ini akan otomatis mengurangi titipan yang ada
     */
    public function useTitipanForNewPiutang($piutangAmount)
    {
        if ($this->total_titipan <= 0) {
            return [
                'success' => false,
                'message' => 'Tidak ada titipan yang dapat digunakan',
                'used_titipan' => 0,
                'remaining_piutang' => $piutangAmount
            ];
        }

        $availableTitipan = $this->total_titipan;
        $usedTitipan = 0;
        $remainingPiutang = $piutangAmount;

        // Gunakan titipan untuk membayar piutang
        if ($availableTitipan >= $piutangAmount) {
            // Gunakan titipan sebanyak yang diperlukan
            $this->useTitipanAmount($piutangAmount, 'Digunakan untuk piutang baru');
            $usedTitipan = $piutangAmount;
            $remainingPiutang = 0;
        } else {
            // Gunakan semua titipan yang tersedia
            $this->useAllTitipan('Digunakan untuk piutang baru');
            $usedTitipan = $availableTitipan;
            $remainingPiutang = $piutangAmount - $availableTitipan;
        }

        return [
            'success' => true,
            'message' => 'Berhasil menggunakan titipan sebesar Rp ' . number_format($usedTitipan, 0, ',', '.') . ' untuk piutang baru',
            'used_titipan' => $usedTitipan,
            'remaining_piutang' => $remainingPiutang
        ];
    }

    /**
     * Gunakan jumlah tertentu dari titipan
     */
    private function useTitipanAmount($amount, $keterangan)
    {
        $titipans = $this->titipans()->oldest()->get();
        $remaining = $amount;

        foreach ($titipans as $titipan) {
            if ($remaining <= 0) break;

            if ($titipan->amount >= $remaining) {
                // Kurangi titipan
                $titipan->amount -= $remaining;
                $titipan->keterangan = $keterangan;
                $titipan->save();

                if ($titipan->amount == 0) {
                    $titipan->delete();
                }

                $remaining = 0;
            } else {
                // Gunakan seluruh titipan
                $remaining -= $titipan->amount;
                $titipan->keterangan = $keterangan;
                $titipan->delete();
            }
        }
    }

    /**
     * Gunakan semua titipan yang tersedia
     */
    private function useAllTitipan($keterangan)
    {
        $titipans = $this->titipans()->get();

        foreach ($titipans as $titipan) {
            $titipan->keterangan = $keterangan;
            $titipan->save();
            $titipan->delete();
        }
    }

    /**
     * Check relations before deleting
     */
    public function checkRelations()
    {
        $relations = [];

        if ($this->transactions()->count() > 0) {
            $relations['transactions'] = $this->transactions()->count();
        }

        if ($this->titipans()->count() > 0) {
            $relations['titipans'] = $this->titipans()->count();
        }

        return $relations;
    }

    /**
     * Format currency for display
     */
    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Get the formatted total piutang attribute
     */
    public function getFormattedTotalPiutangAttribute()
    {
        return 'Rp ' . number_format($this->total_piutang, 0, ',', '.');
    }

    /**
     * Get the formatted total pembayaran attribute
     */
    public function getFormattedTotalPembayaranAttribute()
    {
        return 'Rp ' . number_format($this->total_pembayaran, 0, ',', '.');
    }
}
