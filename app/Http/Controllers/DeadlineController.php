<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Deadline;
use App\Models\Dossier;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeadlineController extends Controller
{
    public function index(Request $request)
    {
        $viewMode = $request->query('view', 'calendar'); // 'calendar' ou 'list'

        $query = Deadline::with(['client', 'dossier', 'responsible']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $deadlines = $query->orderBy('due_date')->get();
        $clients = Client::orderBy('company_name')->get();
        $employees = Employee::where('status', 'actif')->orderBy('last_name')->get();
        $dossiers = Dossier::orderBy('reference')->get();

        // Pour la vue calendrier
        $currentMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $monthDate = Carbon::createFromFormat('Y-m', $currentMonth)->startOfMonth();

        return view('deadlines.index', compact(
            'deadlines',
            'clients',
            'employees',
            'dossiers',
            'viewMode',
            'currentMonth',
            'monthDate'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:fiscal,comptable,paiement,juridique,social,autre',
            'client_id' => 'nullable|exists:clients,id',
            'dossier_id' => 'nullable|exists:dossiers,id',
            'due_date' => 'required|date',
            'priority' => 'required|in:faible,moyenne,haute,urgente',
            'responsible_id' => 'nullable|exists:employees,id',
            'description' => 'nullable|string',
        ]);

        $validated['status'] = 'en_attente';
        $deadline = Deadline::create($validated);

        ActivityLog::log(
            action: 'creation',
            module: 'deadlines',
            description: "Ajout de l'échéance '{$deadline->title}' pour le {$deadline->due_date->format('d/m/Y')}",
            targetId: $deadline->id,
            targetLabel: $deadline->title
        );

        return back()->with('success', "L'échéance a été planifiée avec succès.");
    }

    public function toggleStatus(Deadline $deadline)
    {
        $deadline->status = ($deadline->status === 'terminee') ? 'en_cours' : 'terminee';
        $deadline->save();

        ActivityLog::log(
            action: 'modification',
            module: 'deadlines',
            description: "Changement de statut de l'échéance '{$deadline->title}' -> {$deadline->status}",
            targetId: $deadline->id,
            targetLabel: $deadline->title
        );

        return back()->with('success', "Statut de l'échéance mis à jour.");
    }

    public function destroy(Deadline $deadline)
    {
        $title = $deadline->title;
        $deadline->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'deadlines',
            description: "Suppression de l'échéance '{$title}'",
            targetId: $deadline->id,
            targetLabel: $title
        );

        return back()->with('info', "L'échéance a été supprimée.");
    }
}
