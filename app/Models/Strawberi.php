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
        'grade',
        'jumlah',
        'stok_awal',
        'stok_terjual',
        'stok_rusak',
        'stok_adjustment',
        'stok_terkunci',
        'harga_beli',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'supplier_id',
        'keterangan',
        'adjustment_notes',
        'last_stock_update',
        'is_posted',
        'is_locked',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'stok_awal' => 'decimal:2',
        'stok_terjual' => 'decimal:2',
        'stok_rusak' => 'decimal:2',
        'stok_adjustment' => 'decimal:2',
        'stok_terkunci' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'last_stock_update' => 'datetime',
        'is_posted' => 'boolean',
        'is_locked' => 'boolean',
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
            if (empty($strawberi->stok_terkunci)) {
                $strawberi->stok_terkunci = 0;
            }
            if (empty($strawberi->batch_number)) {
                $strawberi->batch_number = 'BTH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            }

            // Auto calculate expiry date based on type
            if (empty($strawberi->tanggal_kadaluarsa) && !empty($strawberi->tanggal_masuk)) {
                $tanggalMasuk = Carbon::parse($strawberi->tanggal_masuk);
                if ($strawberi->jenis === 'segar') {
                    $strawberi->tanggal_kadaluarsa = $tanggalMasuk->addDays(2);
                } elseif ($strawberi->jenis === 'beku') {
                    $strawberi->tanggal_kadaluarsa = $tanggalMasuk->addMonth(1);
                }
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
        return $this->stok_awal - $this->stok_terjual - $this->stok_rusak + $this->stok_adjustment - $this->stok_terkunci;
    }

    public function getStokTersediaAttribute()
    {
        return $this->stok_awal - $this->stok_terjual - $this->stok_rusak + $this->stok_adjustment;
    }

    public function getTotalNilaiBeliAttribute()
    {
        return $this->stok_awal * $this->harga_beli;
    }

    // Removed getTotalNilaiJualAttribute and getLabaAttribute methods
    // as they depend on harga_jual which is no longer used

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

    /**
     * Get remaining days until expiration
     *
     * @return int
     */
    public function getSisaHariKadaluarsaAttribute()
    {
        return now()->diffInDays($this->tanggal_kadaluarsa, false);
    }

    /**
     * Get formatted expiration text
     *
     * @return string
     */
    public function getTextKadaluarsaAttribute()
    {
        $sisaHari = $this->sisa_hari_kadaluarsa;

        if ($sisaHari < 0) {
            return 'Kadaluarsa ' . abs($sisaHari) . ' hari yang lalu';
        } elseif ($sisaHari == 0) {
            return 'Kadaluarsa hari ini';
        } else {
            return $sisaHari . ' hari lagi';
        }
    }

    /**
     * Get formatted jenis and grade
     *
     * @return string
     */
    public function getJenisGradeAttribute()
    {
        if ($this->grade) {
            return ucfirst($this->jenis) . ' Grade ' . strtoupper($this->grade);
        }
        return ucfirst($this->jenis);
    }

    /**
     * Scope a query to only include strawberi of a given jenis.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $jenis
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope a query to only include strawberi of a given grade.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $grade
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }
}
