<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'client_id',
        'dossier_id',
        'due_date',
        'priority',
        'responsible_id',
        'status',
        'description',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class)->withTrashed();
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_id')->withTrashed();
    }

    public function scopeUpcoming(Builder $query, int $days = 15): Builder
    {
        return $query->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays($days)])
            ->whereIn('status', ['en_attente', 'en_cours']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', 'en_retard')
              ->orWhere(function ($sub) {
                  $sub->where('due_date', '<', Carbon::today())
                      ->whereIn('status', ['en_attente', 'en_cours']);
              });
        });
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'urgente' => 'bg-red-100 text-red-800 border-red-200',
            'haute' => 'bg-amber-100 text-amber-800 border-amber-200',
            'moyenne' => 'bg-blue-100 text-blue-800 border-blue-200',
            'faible' => 'bg-slate-100 text-slate-700 border-slate-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
