<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Strawberi extends Model
{
    use HasFactory;

    protected $table = 'strawberis';

    protected $fillable = [
        'batch_number',
        'jenis',
        'jumlah',
        'stok_awal',
        'stok_terjual',
        'stok_rusak',
        'stok_adjustment',
        'harga_beli',
        'harga_jual',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'supplier_id',
        'keterangan',
        'adjustment_notes',
        'last_stock_update',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'stok_awal' => 'decimal:2',
        'stok_terjual' => 'decimal:2',
        'stok_rusak' => 'decimal:2',
        'stok_adjustment' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'last_stock_update' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($strawberi) {
            if (empty($strawberi->stok_awal)) {
                $strawberi->stok_awal = $strawberi->jumlah;
            }
            if (empty($strawberi->stok_terjual)) {
                $strawberi->stok_terjual = 0;
            }
            if (empty($strawberi->batch_number)) {
                $strawberi->batch_number = 'BTH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            }
            $strawberi->last_stock_update = now();
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getStokTersisaAttribute()
    {
        return $this->stok_awal - $this->stok_terjual - $this->stok_rusak + $this->stok_adjustment;
    }

    public function getTotalNilaiBeliAttribute()
    {
        return $this->stok_awal * $this->harga_beli;
    }

    public function getTotalNilaiJualAttribute()
    {
        return $this->stok_terjual * $this->harga_jual;
    }

    public function getLabaAttribute()
    {
        return $this->getTotalNilaiJualAttribute() - ($this->stok_terjual * $this->harga_beli);
    }

    public function isKadaluarsa()
    {
        return now()->isAfter($this->tanggal_kadaluarsa);
    }

    public function isHampirKadaluarsa()
    {
        return now()->isBefore($this->tanggal_kadaluarsa) && 
               now()->addDays(7)->isAfter($this->tanggal_kadaluarsa);
    }

    public function recordStockMovement($type, $quantity, $notes = null)
    {
        $stockBefore = $this->getStokTersisaAttribute();
        
        switch ($type) {
            case 'sale':
                $this->stok_terjual += $quantity;
                break;
            case 'damage':
                $this->stok_rusak += $quantity;
                break;
            case 'adjustment':
                $this->stok_adjustment += $quantity;
                $this->adjustment_notes = $notes;
                break;
        }

        $this->last_stock_update = now();
        $this->save();

        $this->stockMovements()->create([
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->getStokTersisaAttribute(),
            'notes' => $notes
        ]);
    }

    public function getStockStatus()
    {
        if ($this->isKadaluarsa()) {
            return 'Kadaluarsa';
        } elseif ($this->isHampirKadaluarsa()) {
            return 'Hampir Kadaluarsa';
        } else {
            return 'Baik';
        }
    }
}
