<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulan',
        'tahun',
        'total_pemasukan',
        'total_pengeluaran',
        'laba',
        'file_path',
        'user_id',
    ];

    protected $casts = [
        'total_pemasukan' => 'decimal:2',
        'total_pengeluaran' => 'decimal:2',
        'laba' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
