<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'client_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference',
        'bank',
        'comments',
        'receipt_path',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function ($payment) {
            $payment->invoice->recalculatePaymentStatus();
        });

        static::deleted(function ($payment) {
            $payment->invoice->recalculatePaymentStatus();
        });
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'especes' => 'Espèces',
            'carte' => 'Carte bancaire',
            default => ucfirst($this->payment_method),
        };
    }
}
