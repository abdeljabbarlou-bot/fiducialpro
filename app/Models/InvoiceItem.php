<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'total_ht',
        'total_ttc',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_ht = round($item->quantity * $item->unit_price, 2);
            $taxMultiplier = 1 + ($item->tax_rate / 100);
            $item->total_ttc = round($item->total_ht * $taxMultiplier, 2);
        });
    }
}
