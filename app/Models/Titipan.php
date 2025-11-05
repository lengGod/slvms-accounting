<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Titipan extends Model
{
    use HasFactory;

    protected $fillable = [
        'debtor_id',
        'amount',
        'tanggal',
        'keterangan',
        'user_id',
        'bagi_pokok',
        'bagi_hasil',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tanggal' => 'date',
    ];

    protected $appends = ['formatted_amount', 'formatted_tanggal'];

    public function debtor()
    {
        return $this->belongsTo(Debtor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formatted amount attribute
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get the formatted tanggal attribute
     */
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal->format('d F Y');
    }
}
