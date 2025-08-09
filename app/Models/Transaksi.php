<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis',
        'jumlah',
        'tanggal',
        'keterangan',
        'user_id',
        'kategori',
        'supplier_id',
        'tipe_transaksi',
        'is_pinjaman',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'is_pinjaman' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Scope untuk transaksi pinjaman
    public function scopePinjaman($query)
    {
        return $query->where('is_pinjaman', true);
    }

    // Scope untuk transaksi pembayaran
    public function scopePembayaran($query)
    {
        return $query->where('tipe_transaksi', 'pembayaran');
    }

    // Scope untuk transaksi pinjaman perusahaan ke supplier
    public function scopePinjamanSupplier($query)
    {
        return $query->where('is_pinjaman', true)
                     ->where('jenis', 'pengeluaran'); // Pengeluaran karena uang keluar dari perusahaan ke supplier
    }

    // Scope untuk transaksi pengembalian pinjaman dari supplier
    public function scopePengembalianPinjaman($query)
    {
        return $query->where('tipe_transaksi', 'pengembalian')
                     ->where('jenis', 'pemasukan'); // Pemasukan karena uang masuk ke perusahaan dari supplier
    }
}
