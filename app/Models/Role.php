<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * Le contrôle d'accès repose sur le `slug` du rôle, exploité par le middleware
     * CheckRole et par les Policies (app/Policies) : il n'existe volontairement pas
     * de table de permissions séparée, la matrice des droits étant centralisée
     * dans les Policies.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
