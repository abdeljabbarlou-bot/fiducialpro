<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dossier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'client_id',
        'type',
        'start_date',
        'end_date',
        'status',
        'priority',
        'responsible_id',
        'description',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_id')->withTrashed();
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'dossier_employees')
            ->withPivot('id', 'role_in_dossier', 'assigned_date', 'end_date', 'status', 'notes')
            ->withTimestamps();
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(Declaration::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(Deadline::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['nouveau', 'en_cours', 'en_attente']);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('reference', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhereHas('client', function ($clientQuery) use ($term) {
                  $clientQuery->where('company_name', 'like', "%{$term}%");
              });
        });
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'comptabilite' => 'Comptabilité',
            'fiscalite' => 'Fiscalité',
            'conseil' => 'Conseil & Audit',
            'formation' => 'Formation',
            'social_rh' => 'Social & RH',
            'juridique' => 'Juridique',
            'creation_entreprise' => "Création d'entreprise",
            default => ucfirst($this->type),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'nouveau' => 'Nouveau',
            'en_cours' => 'En cours',
            'en_attente' => 'En attente',
            'termine' => 'Terminé',
            'suspendu' => 'Suspendu',
            'archive' => 'Archivé',
            default => ucfirst($this->status),
        };
    }
}
