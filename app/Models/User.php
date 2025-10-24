<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }

    // Relasi ke transaksi yang dibuat user
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Relasi ke aktivitas log
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scope untuk role
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Cek apakah user admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Cek apakah user accounting
    public function isAccounting(): bool
    {
        return $this->role === 'accounting';
    }
}
