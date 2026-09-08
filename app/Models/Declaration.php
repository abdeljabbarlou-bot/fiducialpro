<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Declaration extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'dossier_id',
        'declaration_type_id',
        'period',
        'due_date',
        'filing_date',
        'status',
        'amount',
        'filing_reference',
        'responsible_id',
        'comments',
        'document_path',
    ];

    protected $casts = [
        'due_date' => 'date',
        'filing_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class)->withTrashed();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(DeclarationType::class, 'declaration_type_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_id')->withTrashed();
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['a_preparer', 'en_preparation', 'prete']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'en_retard')
            ->orWhere(function ($q) {
                $q->where('due_date', '<', Carbon::today())
                  ->whereNotIn('status', ['deposee', 'payee', 'annulee']);
            });
    }

    public function scopeUpcoming(Builder $query, int $days = 15): Builder
    {
        return $query->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays($days)])
            ->whereNotIn('status', ['deposee', 'payee', 'annulee', 'en_retard']);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('period', 'like', "%{$term}%")
              ->orWhere('filing_reference', 'like', "%{$term}%")
              ->orWhereHas('client', function ($cq) use ($term) {
                  $cq->where('company_name', 'like', "%{$term}%");
              })
              ->orWhereHas('type', function ($tq) use ($term) {
                  $tq->where('name', 'like', "%{$term}%")
                     ->orWhere('code', 'like', "%{$term}%");
              });
        });
    }

    // Règle RG11 : vérification automatique du retard
    public function checkOverdue(): bool
    {
        if ($this->due_date->lt(Carbon::today()) && !in_array($this->status, ['deposee', 'payee', 'annulee', 'en_retard'])) {
            $this->status = 'en_retard';
            $this->save();
            return true;
        }
        return false;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'a_preparer' => 'À préparer',
            'en_preparation' => 'En préparation',
            'prete' => 'Prête',
            'deposee' => 'Déposée',
            'payee' => 'Payée',
            'en_retard' => 'En retard',
            'annulee' => 'Annulée',
            default => ucfirst($this->status),
        };
    }
}
