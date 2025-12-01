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
        'supplier_name',
        'bukti_pembayaran',
        'tipe_transaksi',
        'is_pinjaman',
        'is_paid',
        'paid_at',
        'paid_by',
        'is_completed',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'is_pinjaman' => 'boolean',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
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

    // Scope untuk transaksi yang sudah dibayar
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    // Scope untuk transaksi yang belum dibayar
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    // Scope untuk pembelian strawberi yang belum dibayar
    public function scopeUnpaidPurchases($query)
    {
        return $query->where('jenis', 'pengeluaran')
            ->where('kategori', 'Pembelian Strawberi')
            ->where('is_pinjaman', false)
            ->where('is_paid', false);
    }
}
