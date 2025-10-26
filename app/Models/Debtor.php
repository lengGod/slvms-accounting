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
        'initial_pokok_balance',
        'initial_bagi_hasil_balance',
        'joined_at',
        'category',
        'initial_balance_type',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'initial_pokok_balance' => 'decimal:2',
        'initial_bagi_hasil_balance' => 'decimal:2',
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
     * Get the current balance attribute
     * PERBAIKAN: Saldo saat ini = Total Titipan (uang yang benar-benar kita pegang)
     */
    public function getCurrentBalanceAttribute()
    {
        // Hitung total titipan yang masih aktif (belum digunakan)
        return $this->titipans()->sum('amount');
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
     * PERBAIKAN: Hitung total titipan yang masih aktif
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
     */
    public function getSaldoAkhirAttribute()
    {
        return $this->current_balance;
    }

    /**
     * Get the saldo pokok attribute
     * PERBAIKAN: Akumulasi total dari semua riwayat transaksi debitur
     * (piutang pokok - pembayaran pokok)
     */
    public function getSaldoPokokAttribute()
    {
        $totalPiutangPokok = $this->transactions()
            ->where('type', 'piutang')
            ->sum('bagi_pokok');

        $totalPembayaranPokok = $this->transactions()
            ->where('type', 'pembayaran')
            ->sum('bagi_pokok');

        // Akumulasi total: piutang pokok - pembayaran pokok
        // Hasil positif = piutang, hasil negatif = lebih bayar
        return $totalPiutangPokok - $totalPembayaranPokok;
    }

    /**
     * Get the formatted saldo pokok attribute
     */
    public function getFormattedSaldoPokokAttribute()
    {
        $saldo = $this->saldo_pokok;
        $formatted = 'Rp ' . number_format(abs($saldo), 0, ',', '.');

        if ($saldo < 0) {
            return '-' . $formatted;
        }

        return $formatted;
    }

    /**
     * Get the saldo bagi hasil attribute
     * PERBAIKAN: Akumulasi total dari semua riwayat transaksi debitur
     * (piutang bagi hasil - pembayaran bagi hasil)
     */
    public function getSaldoBagiHasilAttribute()
    {
        $totalPiutangHasil = $this->transactions()
            ->where('type', 'piutang')
            ->sum('bagi_hasil');

        $totalPembayaranHasil = $this->transactions()
            ->where('type', 'pembayaran')
            ->sum('bagi_hasil');

        // Akumulasi total: piutang bagi hasil - pembayaran bagi hasil
        // Hasil positif = piutang, hasil negatif = lebih bayar
        return $totalPiutangHasil - $totalPembayaranHasil;
    }

    /**
     * Get the formatted saldo bagi hasil attribute
     */
    public function getFormattedSaldoBagiHasilAttribute()
    {
        $saldo = $this->saldo_bagi_hasil;
        $formatted = 'Rp ' . number_format(abs($saldo), 0, ',', '.');

        if ($saldo < 0) {
            return '-' . $formatted;
        }

        return $formatted;
    }

    /**
     * Get the debtor status attribute
     * PERBAIKAN: Berdasarkan total saldo (transaksi + titipan)
     */
    public function getDebtorStatusAttribute()
    {
        $transactionBalance = $this->total_pembayaran - $this->total_piutang;
        $totalBalance = $transactionBalance + $this->total_titipan;

        if ($totalBalance > 0) {
            return 'lebih_bayar';
        } elseif ($totalBalance < 0) {
            return 'belum_lunas';
        } else {
            return 'lunas';
        }
    }

    /**
     * Get the keterangan piutang attribute
     * PERBAIKAN: Berdasarkan total saldo (transaksi + titipan)
     */
    public function getKeteranganPiutangAttribute()
    {
        $transactionBalance = $this->total_pembayaran - $this->total_piutang;
        $totalBalance = $transactionBalance + $this->total_titipan;

        if ($totalBalance > 0) {
            return 'Lebih bayar';
        } elseif ($totalBalance < 0) {
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
            $types = explode(',', $this->initial_balance_type);
            $typeLabel = implode(' + ', array_map(function ($t) {
                return str_replace('_', ' ', ucfirst(trim($t)));
            }, $types));

            return [
                'amount' => $this->initial_balance,
                'type' => $this->initial_balance_type,
                'type_label' => $typeLabel,
                'formatted' => 'Rp ' . number_format(abs($this->initial_balance), 0, ',', '.'),
                'is_negative' => $this->initial_balance < 0,
                'is_titipan' => $this->initial_balance > 0,
                'pokok_amount' => $this->initial_pokok_balance ?? 0,
                'bagi_hasil_amount' => $this->initial_bagi_hasil_balance ?? 0,
            ];
        }

        return null;
    }

    /**
     * Check if debtor has any titipan
     * PERBAIKAN: Cek apakah ada titipan yang masih aktif
     */
    public function hasTitipan()
    {
        return $this->titipans()->where('amount', '>', 0)->exists();
    }

    /**
     * Gunakan titipan untuk membayar piutang baru
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

        if ($availableTitipan >= $piutangAmount) {
            $this->useTitipanAmount($piutangAmount, 'Digunakan untuk piutang baru');
            $usedTitipan = $piutangAmount;
            $remainingPiutang = 0;
        } else {
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
                $titipan->amount -= $remaining;
                $titipan->keterangan = $keterangan;
                $titipan->save();

                if ($titipan->amount == 0) {
                    $titipan->delete();
                }

                $remaining = 0;
            } else {
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
