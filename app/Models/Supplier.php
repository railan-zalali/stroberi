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
        'keterangan',
        'status',
        'foto',
    ];

    public function strawberis()
    {
        return $this->hasMany(Strawberi::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    // Mendapatkan total pinjaman dari transaksi (perusahaan ke supplier)
    public function getTotalPinjamanAttribute()
    {
        return $this->transaksis()
            ->where('is_pinjaman', true)
            ->where('jenis', 'pengeluaran') // Pengeluaran karena uang keluar dari perusahaan ke supplier
            ->sum('jumlah');
    }

    // Mendapatkan total pengembalian pinjaman dari supplier
    public function getTotalPengembalianAttribute()
    {
        return $this->transaksis()
            ->where('tipe_transaksi', 'pengembalian')
            ->where('jenis', 'pemasukan') // Pemasukan karena uang masuk ke perusahaan dari supplier
            ->sum('jumlah');
    }

    // Menghitung sisa pinjaman yang belum dikembalikan oleh supplier
    public function getSisaPinjamanAttribute()
    {
        return $this->total_pinjaman - $this->total_pengembalian;
    }

    public function isAktif()
    {
        return $this->status === 'aktif';
    }
}
