<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strawberi extends Model
{
    use HasFactory;

    protected $table = 'strawberis';

    protected $fillable = [
        'jenis',
        'jumlah',
        'harga_beli',
        'harga_jual',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'supplier_id',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
