<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    public const TYPE_STOCK_IN   = 'stock_in';
    public const TYPE_STOCK_OUT  = 'stock_out';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_RETURN     = 'return';
    public const TYPE_DAMAGE     = 'damage';

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeStockIn($query)
    {
        return $query->where('type', self::TYPE_STOCK_IN);
    }

    public function scopeStockOut($query)
    {
        return $query->where('type', self::TYPE_STOCK_OUT);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Helpers
    public function isStockIn(): bool
    {
        return in_array($this->type, [self::TYPE_STOCK_IN, self::TYPE_RETURN]);
    }

    public function isStockOut(): bool
    {
        return in_array($this->type, [self::TYPE_STOCK_OUT, self::TYPE_DAMAGE]);
    }

    public function getSignedQuantityAttribute(): int
    {
        if ($this->isStockIn()) {
            return abs($this->quantity);
        }
        if ($this->isStockOut()) {
            return -abs($this->quantity);
        }
        // adjustment: quantity can be positive or negative
        return $this->quantity;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_STOCK_IN   => 'Stock In',
            self::TYPE_STOCK_OUT  => 'Stock Out',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_RETURN     => 'Return',
            self::TYPE_DAMAGE     => 'Damage / Write-off',
            default               => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_STOCK_IN, self::TYPE_RETURN => 'bg-success',
            self::TYPE_STOCK_OUT                   => 'bg-danger',
            self::TYPE_ADJUSTMENT                  => 'bg-warning text-dark',
            self::TYPE_DAMAGE                      => 'bg-secondary',
            default                                => 'bg-secondary',
        };
    }

    public static function types(): array
    {
        return [
            self::TYPE_STOCK_IN   => 'Stock In',
            self::TYPE_STOCK_OUT  => 'Stock Out',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_RETURN     => 'Return',
            self::TYPE_DAMAGE     => 'Damage / Write-off',
        ];
    }
}
