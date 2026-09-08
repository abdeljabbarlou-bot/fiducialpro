<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

/**
 * Matrice RBAC (docs/LIVRABLE_7_DOCUMENTATION_TECHNIQUE.md §4) :
 * "Archivage GED" — Administrateur/Gérant = total ; Comptable et Secrétaire =
 * dépôt & téléchargement. La suppression d'une pièce justificative (statuts, PV,
 * bilan) est irréversible côté GED : elle reste réservée à la direction.
 */
class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Document $document): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function download(User $user, Document $document): bool
    {
        return true;
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasRole(['admin', 'gerant']);
    }
}
