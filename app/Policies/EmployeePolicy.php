<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

/**
 * Gestion des collaborateurs (données RH sensibles, incluant le salaire) :
 * réservée à l'Administrateur et au Gérant. La consultation reste ouverte
 * (nécessaire pour les listes d'affectation aux dossiers).
 */
class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Employee $employee): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'gerant']);
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->hasRole(['admin', 'gerant']);
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->hasRole(['admin', 'gerant']);
    }
}
