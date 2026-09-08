<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

/**
 * Matrice RBAC (docs/LIVRABLE_7_DOCUMENTATION_TECHNIQUE.md §4) :
 * Administrateur/Gérant = accès total, Comptable = lecture & modification,
 * Secrétaire = lecture & création uniquement.
 */
class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Client $client): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'gerant', 'secretaire']);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasRole(['admin', 'gerant', 'comptable']);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasRole(['admin', 'gerant']);
    }
}
