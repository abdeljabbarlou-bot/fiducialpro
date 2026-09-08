<?php

namespace App\Policies;

use App\Models\Declaration;
use App\Models\User;

/**
 * Matrice RBAC (docs/LIVRABLE_7_DOCUMENTATION_TECHNIQUE.md §4) :
 * Administrateur/Gérant = accès total, Comptable = saisie & télé-dépôt SIMPL,
 * Secrétaire = consultation seule (les obligations fiscales engagent la
 * responsabilité du cabinet : leur saisie reste réservée aux comptables).
 */
class DeclarationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Declaration $declaration): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'gerant', 'comptable']);
    }

    public function update(User $user, Declaration $declaration): bool
    {
        return $user->hasRole(['admin', 'gerant', 'comptable']);
    }

    /**
     * Télétransmission SIMPL / DAMANCOM : acte engageant le cabinet auprès de la DGI.
     */
    public function file(User $user, Declaration $declaration): bool
    {
        return $user->hasRole(['admin', 'gerant', 'comptable']);
    }

    public function delete(User $user, Declaration $declaration): bool
    {
        return $user->hasRole(['admin', 'gerant']);
    }
}
