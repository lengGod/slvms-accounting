<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Debtor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
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

    public function getCurrentBalanceAttribute()
    {
        return $this->saldo_pokok + $this->saldo_bagi_hasil;
    }

    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->current_balance, 0, ',', '.');
    }

    public function getFormattedInitialBalanceAttribute()
    {
        return 'Rp ' . number_format($this->initial_balance, 0, ',', '.');
    }

    public function getFormattedJoinedAtAttribute()
    {
        return $this->joined_at ? $this->joined_at->format('d M Y') : '-';
    }

    public function getTotalPiutangAttribute()
    {
        return $this->transactions()
            ->where('type', 'piutang')
            ->sum('amount');
    }

    public function getTotalPembayaranAttribute()
    {
        return $this->transactions()
            ->where('type', 'pembayaran')
            ->sum('amount');
    }

    public function getTotalTitipanAttribute()
    {
        return $this->titipans()->sum('amount');
    }

    public function getFormattedTotalTitipanAttribute()
    {
        return 'Rp ' . number_format($this->total_titipan, 0, ',', '.');
    }

    public function getSaldoAkhirAttribute()
    {
        return $this->current_balance;
    }

    public function getSaldoPokokAttribute()
    {
        $transactionPokok = $this->transactions()->sum('bagi_pokok');
        $titipanPokok = $this->titipans()->sum('bagi_pokok');
        return $transactionPokok + $titipanPokok;
    }

    public function getFormattedSaldoPokokAttribute()
    {
        return 'Rp ' . number_format($this->saldo_pokok, 0, ',', '.');
    }

    public function getSaldoBagiHasilAttribute()
    {
        $transactionHasil = $this->transactions()->sum('bagi_hasil');
        $titipanHasil = $this->titipans()->sum('bagi_hasil');
        return $transactionHasil + $titipanHasil;
    }

    public function getFormattedSaldoBagiHasilAttribute()
    {
        return 'Rp ' . number_format($this->saldo_bagi_hasil, 0, ',', '.');
    }

    public function getDebtorStatusAttribute()
    {
        $balance = $this->current_balance;

        if ($balance < 0) {
            return 'belum_lunas';
        } elseif ($balance > 0) {
            return 'Titipan';
        } else {
            return 'lunas';
        }
    }

    public function getKeteranganPiutangAttribute()
    {
        $balance = $this->current_balance;

        if ($balance < 0) {
            return 'Memiliki piutang';
        } elseif ($balance > 0) {
            return 'Memiliki Titipan';
        } else {
            return 'Lunas';
        }
    }

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

    public function hasTitipan()
    {
        return $this->titipans()->where('amount', '>', 0)->exists();
    }

    /**
     * FIXED: Gunakan titipan untuk membayar piutang baru
     * Alokasi berdasarkan proporsi PIUTANG BARU, bukan titipan yang ada
     * 
     * @param float $piutangAmount Total piutang baru
     * @param int|null $transactionId Transaction ID
     * @param float $piutangPokok Jumlah pokok dari piutang baru
     * @param float $piutangHasil Jumlah bagi hasil dari piutang baru
     * @return array
     */
    public function useTitipanForNewPiutang($piutangAmount, $transactionId = null, $piutangPokok = 0, $piutangHasil = 0)
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
        $usedTitipan = min($availableTitipan, $piutangAmount);
        $remainingPiutang = $piutangAmount - $usedTitipan;

        if ($usedTitipan > 0) {
            // FIXED: Hitung alokasi berdasarkan proporsi PIUTANG BARU
            $usedPokok = 0;
            $usedHasil = 0;

            $totalPiutangAllocation = $piutangPokok + $piutangHasil;

            if ($totalPiutangAllocation > 0) {
                // Hitung proporsi dari piutang baru
                $pokokRatio = $piutangPokok / $totalPiutangAllocation;
                $hasilRatio = $piutangHasil / $totalPiutangAllocation;

                // Alokasikan titipan sesuai proporsi piutang baru
                $usedPokok = $usedTitipan * $pokokRatio;
                $usedHasil = $usedTitipan * $hasilRatio;
            } else {
                // Jika tidak ada alokasi spesifik, gunakan semua untuk pokok
                $usedPokok = $usedTitipan;
                $usedHasil = 0;
            }

            $this->recordTitipanAdjustment(
                -$usedTitipan,
                'Penggunaan titipan untuk piutang #' . $transactionId,
                $transactionId,
                -$usedPokok,
                -$usedHasil
            );
        }

        return [
            'success' => true,
            'message' => 'Berhasil menggunakan titipan sebesar Rp ' . number_format($usedTitipan, 0, ',', '.') . ' untuk piutang baru',
            'used_titipan' => $usedTitipan,
            'remaining_piutang' => $remainingPiutang,
            'used_pokok' => $usedPokok ?? 0,
            'used_hasil' => $usedHasil ?? 0,
        ];
    }

    /**
     * Record a titipan adjustment with AUTOMATIC ALLOCATION
     * 
     * @param float $amount Total amount of titipan (positive for addition, negative for usage)
     * @param string $keterangan Description
     * @param int|null $transactionId Related transaction ID
     * @param float $bagiPokok Bagi pokok (if manually specified)
     * @param float $bagiHasil Bagi hasil (if manually specified)
     */
    public function recordTitipanAdjustment($amount, $keterangan, $transactionId = null, $bagiPokok = null, $bagiHasil = null)
    {
        // Check if an existing titipan for this transaction already exists
        $existingTitipan = null;
        if ($transactionId) {
            $existingTitipan = Titipan::where('debtor_id', $this->id)
                ->where('transaction_id', $transactionId)
                ->where('keterangan', 'like', 'Kelebihan pembayaran%')
                ->first();
        }

        // AUTOMATIC ALLOCATION LOGIC
        // If bagiPokok and bagiHasil are both null (not manually specified), calculate automatically
        if ($bagiPokok === null && $bagiHasil === null && $amount != 0) {
            $allocation = $this->calculateTitipanAllocation(abs($amount));

            // Apply the sign (positive for addition, negative for usage)
            if ($amount < 0) {
                $bagiPokok = -$allocation['pokok'];
                $bagiHasil = -$allocation['hasil'];
            } else {
                $bagiPokok = $allocation['pokok'];
                $bagiHasil = $allocation['hasil'];
            }
        }

        // Ensure values are not null
        $bagiPokok = $bagiPokok ?? 0;
        $bagiHasil = $bagiHasil ?? 0;

        if ($existingTitipan) {
            // Update existing titipan
            $existingTitipan->amount += round($amount, 2);
            $existingTitipan->bagi_pokok += round($bagiPokok, 2);
            $existingTitipan->bagi_hasil += round($bagiHasil, 2);
            $existingTitipan->tanggal = now();
            $existingTitipan->user_id = auth()->id();
            $existingTitipan->save();
        } else {
            // Create new titipan
            Titipan::create([
                'debtor_id' => $this->id,
                'amount' => round($amount, 2),
                'bagi_pokok' => round($bagiPokok, 2),
                'bagi_hasil' => round($bagiHasil, 2),
                'tanggal' => now(),
                'keterangan' => $keterangan,
                'user_id' => auth()->id(),
                'transaction_id' => $transactionId,
            ]);
        }
    }

    /**
     * Calculate automatic allocation for titipan based on outstanding debt proportion
     * 
     * @param float $amount The amount to allocate
     * @return array ['pokok' => float, 'hasil' => float]
     */
    protected function calculateTitipanAllocation($amount)
    {
        // Get current outstanding debt (negative values)
        $saldoPokok = $this->saldo_pokok;
        $saldoHasil = $this->saldo_bagi_hasil;

        // Outstanding debt is negative, so we use abs() for calculation
        $outstandingPokok = abs($saldoPokok < 0 ? $saldoPokok : 0);
        $outstandingHasil = abs($saldoHasil < 0 ? $saldoHasil : 0);
        $totalOutstanding = $outstandingPokok + $outstandingHasil;

        // If there's no outstanding debt, allocate everything to pokok
        if ($totalOutstanding <= 0) {
            return [
                'pokok' => $amount,
                'hasil' => 0,
            ];
        }

        // Calculate proportional allocation based on outstanding debt
        $pokokRatio = $outstandingPokok / $totalOutstanding;
        $hasilRatio = $outstandingHasil / $totalOutstanding;

        return [
            'pokok' => round($amount * $pokokRatio, 2),
            'hasil' => round($amount * $hasilRatio, 2),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($debtor) {
            $debtor->transactions()->each(function ($transaction) {
                $transaction->delete();
            });

            $debtor->titipans()->each(function ($titipan) {
                $titipan->delete();
            });
        });
    }

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

    public function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function getFormattedTotalPiutangAttribute()
    {
        return 'Rp ' . number_format($this->total_piutang, 0, ',', '.');
    }

    public function getFormattedTotalPembayaranAttribute()
    {
        return 'Rp ' . number_format($this->total_pembayaran, 0, ',', '.');
    }
}
