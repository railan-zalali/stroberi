<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'strawberi_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];

    public function strawberi()
    {
        return $this->belongsTo(Strawberi::class);
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'sale' => 'Penjualan',
            'damage' => 'Kerusakan',
            'adjustment' => 'Penyesuaian',
            default => ucfirst($this->type)
        };
    }
} 