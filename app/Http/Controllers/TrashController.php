<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\Employee;

/**
 * Corbeille : consultation et restauration des fiches supprimées logiquement (RG02).
 *
 * La suppression logique (SoftDeletes) préserve l'historique comptable ; cette
 * corbeille rend cet historique réellement accessible et réversible, au lieu de
 * le laisser inatteignable en base.
 */
class TrashController extends Controller
{
    public function index()
    {
        $clients = Client::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'clients_page');

        // Le client peut lui aussi être en corbeille : on l'affiche quand même
        $dossiers = Dossier::onlyTrashed()
            ->with(['client' => fn ($q) => $q->withTrashed()])
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'dossiers_page');

        $employees = Employee::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'employees_page');

        return view('trash.index', compact('clients', 'dossiers', 'employees'));
    }

    public function restoreClient(Client $client)
    {
        $this->authorize('delete', $client);

        $client->restore();

        ActivityLog::log(
            action: 'restauration',
            module: 'clients',
            description: "Restauration depuis la corbeille du client '{$client->company_name}'",
            targetId: $client->id,
            targetLabel: $client->company_name
        );

        return back()->with('success', "Le client '{$client->company_name}' a été restauré.");
    }

    public function restoreEmployee(Employee $employee)
    {
        $this->authorize('delete', $employee);

        $employee->restore();

        ActivityLog::log(
            action: 'restauration',
            module: 'employees',
            description: "Restauration depuis la corbeille du collaborateur {$employee->full_name}",
            targetId: $employee->id,
            targetLabel: $employee->full_name
        );

        return back()->with('success', "Le collaborateur {$employee->full_name} a été restauré.");
    }

    public function restoreDossier(Dossier $dossier)
    {
        $this->authorize('delete', $dossier);

        $dossier->restore();

        ActivityLog::log(
            action: 'restauration',
            module: 'dossiers',
            description: "Restauration depuis la corbeille du dossier {$dossier->reference}",
            targetId: $dossier->id,
            targetLabel: $dossier->reference
        );

        return back()->with('success', "Le dossier {$dossier->reference} a été restauré.");
    }
}
