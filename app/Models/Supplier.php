<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'email',
        'total_pinjaman',
        'total_pembayaran',
        'keterangan',
        'status',
        'foto',
    ];

    protected $casts = [
        'total_pinjaman' => 'decimal:2',
        'total_pembayaran' => 'decimal:2',
    ];

    public function strawberis()
    {
        return $this->hasMany(Strawberi::class);
    }

    public function getSisaPinjamanAttribute()
    {
        return $this->total_pinjaman - $this->total_pembayaran;
    }

    public function isAktif()
    {
        return $this->status === 'aktif';
    }
}
