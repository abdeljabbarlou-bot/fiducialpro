<?php

namespace App\Policies;

use App\Models\Dossier;
use App\Models\User;

/**
 * Matrice RBAC (docs/LIVRABLE_7_DOCUMENTATION_TECHNIQUE.md §4) :
 * Administrateur/Gérant = accès total, Comptable = gestion & affectation,
 * Secrétaire = consultation seule (aucune création/modification/suppression).
 */
class DossierPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Dossier $dossier): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'gerant', 'comptable']);
    }

    public function update(User $user, Dossier $dossier): bool
    {
        return $user->hasRole(['admin', 'gerant', 'comptable']);
    }

    public function delete(User $user, Dossier $dossier): bool
    {
        return $user->hasRole(['admin', 'gerant']);
    }
}
