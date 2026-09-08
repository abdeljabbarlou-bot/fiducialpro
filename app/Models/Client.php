<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'company_name',
        'trade_name',
        'legal_form',
        'ice',
        'if_number',
        'rc_number',
        'patent_number',
        'cnss_number',
        'share_capital',
        'activity',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'client_since',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'share_capital' => 'decimal:2',
        'client_since' => 'date',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function primaryContact(): HasOne
    {
        return $this->hasOne(ClientContact::class)->where('is_primary', true);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(Declaration::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(Deadline::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'actif');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('company_name', 'like', "%{$term}%")
              ->orWhere('trade_name', 'like', "%{$term}%")
              ->orWhere('ice', 'like', "%{$term}%")
              ->orWhere('if_number', 'like', "%{$term}%")
              ->orWhere('rc_number', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->trade_name ? "{$this->company_name} ({$this->trade_name})" : $this->company_name;
    }
}
