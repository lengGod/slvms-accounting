<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'debtor_id',
        'type',
        'amount',
        'bagi_hasil',
        'bagi_pokok',
        'transaction_date',
        'description',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bagi_hasil' => 'decimal:2',
        'bagi_pokok' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    protected $appends = [
        'formatted_amount',
        'formatted_bagi_hasil',
        'formatted_bagi_pokok',
        'formatted_date',
        'formatted_id',
        'sisa_after_allocation',
        'formatted_sisa_after_allocation',
        'total_alokasi',
        'formatted_total_alokasi'
    ];

    // Relasi ke debitur
    public function debtor()
    {
        return $this->belongsTo(Debtor::class);
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk filter tipe transaksi
    public function scopeTipe($query, $tipe)
    {
        return $query->where('type', $tipe);
    }

    // Scope untuk filter tanggal
    public function scopeTanggal($query, $from = null, $to = null)
    {
        if ($from && $to) {
            return $query->whereBetween('transaction_date', [$from, $to]);
        } elseif ($from) {
            return $query->whereDate('transaction_date', '>=', $from);
        } elseif ($to) {
            return $query->whereDate('transaction_date', '<=', $to);
        }
        return $query;
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('debtor', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        }
        return $query;
    }

    // Format amount dengan currency
    public function getFormattedAmountAttribute()
    {
        // If it's a 'pembayaran' or 'piutang' transaction and its amount is 0, it means the payment/piutang was fully converted to titipan.
        // In this case, we should display the amount from the associated titipan record.
        if (($this->type === 'pembayaran' || $this->type === 'piutang') && $this->amount == 0) {
            $titipanAmount = $this->debtor->titipans()->where('transaction_id', $this->id)->sum('amount');
            return 'Rp ' . number_format(abs($titipanAmount), 0, ',', '.'); // Use abs() as titipan amount might be negative for usage
        }
        return 'Rp ' . number_format(abs($this->amount), 0, ',', '.');
    }

    // Format bagi hasil dengan currency
    public function getFormattedBagiHasilAttribute()
    {
        if (($this->type === 'pembayaran' || $this->type === 'piutang') && $this->amount == 0) {
            $titipanBagiHasil = $this->debtor->titipans()->where('transaction_id', $this->id)->sum('bagi_hasil');
            return 'Rp ' . number_format(abs($titipanBagiHasil ?? 0), 0, ',', '.');
        }
        return 'Rp ' . number_format(abs($this->bagi_hasil ?? 0), 0, ',', '.');
    }

    // Format bagi pokok dengan currency
    public function getFormattedBagiPokokAttribute()
    {
        if (($this->type === 'pembayaran' || $this->type === 'piutang') && $this->amount == 0) {
            $titipanBagiPokok = $this->debtor->titipans()->where('transaction_id', $this->id)->sum('bagi_pokok');
            return 'Rp ' . number_format(abs($titipanBagiPokok ?? 0), 0, ',', '.');
        }
        return 'Rp ' . number_format(abs($this->bagi_pokok ?? 0), 0, ',', '.');
    }

    // Format tanggal
    public function getFormattedDateAttribute()
    {
        return $this->transaction_date->format('d M Y');
    }

    // Format ID transaksi
    public function getFormattedIdAttribute()
    {
        return '#TRX' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    // Method untuk validasi alokasi
    public function isValidAllocation()
    {
        $totalAlokasi = ($this->bagi_hasil ?? 0) + ($this->bagi_pokok ?? 0);
        return $totalAlokasi <= $this->amount;
    }

    // Method untuk mendapatkan sisa setelah alokasi
    public function getSisaAfterAllocationAttribute()
    {
        $totalAlokasi = ($this->bagi_hasil ?? 0) + ($this->bagi_pokok ?? 0);
        return $this->amount - $totalAlokasi;
    }

    // Format sisa alokasi
    public function getFormattedSisaAfterAllocationAttribute()
    {
        return 'Rp ' . number_format($this->sisa_after_allocation, 0, ',', '.');
    }

    // Method untuk mendapatkan total alokasi
    public function getTotalAlokasiAttribute()
    {
        return ($this->bagi_hasil ?? 0) + ($this->bagi_pokok ?? 0);
    }

    // Format total alokasi
    public function getFormattedTotalAlokasiAttribute()
    {
        return 'Rp ' . number_format($this->total_alokasi, 0, ',', '.');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($transaction) {
            // Hapus semua titipan yang terkait dengan transaksi ini
            $transaction->debtor->titipans()->where('transaction_id', $transaction->id)->each(function ($titipan) {
                $titipan->delete();
            });
        });
    }
}
