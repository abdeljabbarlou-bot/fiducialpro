<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'client_id',
        'invoice_date',
        'due_date',
        'description',
        'subtotal_ht',
        'tax_amount',
        'total_ttc',
        'paid_amount',
        'remaining_amount',
        'status',
        'payment_conditions',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal_ht' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    /**
     * Le client reste rattaché à la facture même s'il a été mis en corbeille :
     * une pièce comptable ne doit jamais perdre l'identité de son débiteur.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereIn('status', ['emise', 'partiellement_payee', 'en_retard']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'en_retard')
            ->orWhere(function ($q) {
                $q->where('due_date', '<', Carbon::today())
                  ->whereIn('status', ['emise', 'partiellement_payee']);
            });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhereHas('client', function ($cq) use ($term) {
                  $cq->where('company_name', 'like', "%{$term}%");
              });
        });
    }

    // Recalcul des totaux à partir des lignes de facture
    public function recalculateTotals(): void
    {
        $subtotal = 0;
        $tax = 0;

        foreach ($this->items as $item) {
            $subtotal += $item->total_ht;
            $tax += ($item->total_ttc - $item->total_ht);
        }

        $this->subtotal_ht = round($subtotal, 2);
        $this->tax_amount = round($tax, 2);
        $this->total_ttc = round($subtotal + $tax, 2);

        $this->recalculatePaymentStatus();
    }

    // Recalcul du solde et mise à jour du statut selon RG07 et RG08
    public function recalculatePaymentStatus(): void
    {
        $paid = $this->payments()->sum('amount');
        $this->paid_amount = round($paid, 2);
        $this->remaining_amount = max(0, round($this->total_ttc - $this->paid_amount, 2));

        if ($this->status !== 'annulee' && $this->status !== 'brouillon') {
            if ($this->remaining_amount <= 0.001) {
                $this->status = 'payee';
            } elseif ($this->paid_amount > 0) {
                $this->status = 'partiellement_payee';
            } elseif ($this->due_date && $this->due_date->lt(Carbon::today())) {
                $this->status = 'en_retard';
            } else {
                $this->status = 'emise';
            }
        }

        $this->save();
    }

    // Ventilation de la TVA par taux (RG06 : nécessaire dès qu'une facture mêle plusieurs taux - 20/14/10/7/0%)
    public function getTaxBreakdownAttribute()
    {
        return $this->items
            ->groupBy(fn ($item) => (float) $item->tax_rate)
            ->map(function ($items, $rate) {
                $baseHt = $items->sum('total_ht');
                return [
                    'rate' => (float) $rate,
                    'base_ht' => round($baseHt, 2),
                    'tax_amount' => round($items->sum('total_ttc') - $baseHt, 2),
                ];
            })
            ->sortKeysDesc()
            ->values();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'brouillon' => 'Brouillon',
            'emise' => 'Émise',
            'partiellement_payee' => 'Partiellement payée',
            'payee' => 'Payée',
            'en_retard' => 'En retard',
            'annulee' => 'Annulée',
            default => ucfirst($this->status),
        };
    }
}
