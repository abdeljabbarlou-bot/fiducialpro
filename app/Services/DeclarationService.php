<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Declaration;
use App\Models\Notification;
use Carbon\Carbon;

class DeclarationService
{
    /**
     * Mettre à jour automatiquement les déclarations en retard (RG11)
     */
    public function syncOverdueDeclarations(): int
    {
        // Le type et le client sont utilisés dans le message de notification :
        // on les précharge pour éviter 2 requêtes supplémentaires par déclaration.
        $overdue = Declaration::with(['type', 'client'])
            ->where('due_date', '<', Carbon::today())
            ->whereNotIn('status', ['deposee', 'payee', 'annulee', 'en_retard'])
            ->get();

        $count = 0;
        foreach ($overdue as $declaration) {
            $declaration->status = 'en_retard';
            $declaration->save();
            $count++;

            // Générer notification d'alerte si pas déjà fait
            Notification::firstOrCreate(
                [
                    'title' => 'Déclaration en retard',
                    'link' => route('declarations.show', $declaration->id),
                ],
                [
                    'message' => "La déclaration {$declaration->type->name} ({$declaration->period}) du client {$declaration->client->company_name} est en retard depuis le {$declaration->due_date->format('d/m/Y')}.",
                    'type' => 'danger',
                ]
            );
        }

        return $count;
    }

    /**
     * Marquer une déclaration comme déposée
     */
    public function markAsFiled(Declaration $declaration, string $filingDate, ?string $reference = null, ?string $comments = null): Declaration
    {
        $declaration->status = 'deposee';
        $declaration->filing_date = $filingDate;
        if ($reference) {
            $declaration->filing_reference = $reference;
        }
        if ($comments) {
            $declaration->comments = $comments;
        }
        $declaration->save();

        ActivityLog::log(
            action: 'modification',
            module: 'declarations',
            description: "Déclaration {$declaration->type->name} ({$declaration->period}) marquée comme déposée (Réf: {$declaration->filing_reference})",
            targetId: $declaration->id,
            targetLabel: $declaration->period
        );

        return $declaration;
    }
}
