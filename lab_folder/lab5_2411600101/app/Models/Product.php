<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'quantity',
        'reorder_level',
        'unit_price',
        'supplier',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'reorder_level' => 'integer',
    ];

   
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }

   
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    
    public function getTotalValueAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

   
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }

    
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }
}
