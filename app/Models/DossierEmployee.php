<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DossierEmployee extends Model
{
    use HasFactory;

    protected $table = 'dossier_employees';

    protected $fillable = [
        'dossier_id',
        'employee_id',
        'role_in_dossier',
        'assigned_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'end_date' => 'date',
    ];

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
