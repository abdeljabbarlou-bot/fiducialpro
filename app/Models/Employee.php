<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'matricule',
        'cin',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'position',
        'hire_date',
        'salary',
        'status',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dossiers(): BelongsToMany
    {
        return $this->belongsToMany(Dossier::class, 'dossier_employees')
            ->withPivot('id', 'role_in_dossier', 'assigned_date', 'end_date', 'status', 'notes')
            ->withTimestamps();
    }

    public function responsibleDossiers(): HasMany
    {
        return $this->hasMany(Dossier::class, 'responsible_id');
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(Declaration::class, 'responsible_id');
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(Deadline::class, 'responsible_id');
    }
}
