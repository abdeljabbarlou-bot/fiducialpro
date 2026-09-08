<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Declaration;
use App\Models\DeclarationType;
use App\Models\Dossier;
use App\Models\Employee;
use App\Services\DeclarationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeclarationController extends Controller
{
    public function index(Request $request, DeclarationService $declarationService)
    {
        // Règle RG11 : synchroniser les retards
        $declarationService->syncOverdueDeclarations();

        $query = Declaration::with(['client', 'dossier', 'type', 'responsible']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('type_id')) {
            $query->where('declaration_type_id', $request->type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('filter') && $request->filter === 'overdue') {
            $query->where('status', 'en_retard');
        } elseif ($request->filled('filter') && $request->filter === 'upcoming') {
            $query->upcoming(15);
        }

        $declarations = $query->orderBy('due_date')->paginate(10)->withQueryString();
        $types = DeclarationType::orderBy('name')->get();
        $clients = Client::orderBy('company_name')->get();

        return view('declarations.index', compact('declarations', 'types', 'clients'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Declaration::class);

        $clients = Client::where('status', '!=', 'archive')->orderBy('company_name')->get();
        $types = DeclarationType::orderBy('name')->get();
        $employees = Employee::where('status', 'actif')->orderBy('last_name')->get();
        $dossiers = Dossier::orderBy('reference')->get();
        $selectedClientId = $request->query('client_id');
        $selectedDossierId = $request->query('dossier_id');

        return view('declarations.create', compact('clients', 'types', 'employees', 'dossiers', 'selectedClientId', 'selectedDossierId'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Declaration::class);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'dossier_id' => 'nullable|exists:dossiers,id',
            'declaration_type_id' => 'required|exists:declaration_types,id',
            'period' => 'required|string|max:100',
            'due_date' => 'required|date',
            // Un dépôt SIMPL ne peut pas être daté dans le futur
            'filing_date' => 'nullable|date|before_or_equal:today',
            'status' => 'required|in:a_preparer,en_preparation,prete,deposee,payee,en_retard,annulee',
            'amount' => 'nullable|numeric|min:0',
            'filing_reference' => 'nullable|string|max:100',
            'responsible_id' => 'nullable|exists:employees,id',
            'comments' => 'nullable|string',
        ]);

        $validated['amount'] = $validated['amount'] ?? 0;

        // Auto RG11 check si la date est dépassée
        if (Carbon::parse($validated['due_date'])->lt(Carbon::today()) && !in_array($validated['status'], ['deposee', 'payee', 'annulee'])) {
            $validated['status'] = 'en_retard';
        }

        $declaration = Declaration::create($validated);

        ActivityLog::log(
            action: 'creation',
            module: 'declarations',
            description: "Ajout de la déclaration {$declaration->type->name} ({$declaration->period}) pour le client #{$declaration->client_id}",
            targetId: $declaration->id,
            targetLabel: $declaration->period
        );

        return redirect()->route('declarations.show', $declaration)->with('success', "La déclaration a été enregistrée avec succès.");
    }

    public function show(Declaration $declaration)
    {
        $declaration->load(['client', 'dossier', 'type', 'responsible']);
        return view('declarations.show', compact('declaration'));
    }

    public function edit(Declaration $declaration)
    {
        $this->authorize('update', $declaration);

        $clients = Client::orderBy('company_name')->get();
        $types = DeclarationType::orderBy('name')->get();
        $employees = Employee::where('status', 'actif')->orderBy('last_name')->get();
        $dossiers = Dossier::where('client_id', $declaration->client_id)->orderBy('reference')->get();

        return view('declarations.edit', compact('declaration', 'clients', 'types', 'employees', 'dossiers'));
    }

    public function update(Request $request, Declaration $declaration)
    {
        $this->authorize('update', $declaration);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'dossier_id' => 'nullable|exists:dossiers,id',
            'declaration_type_id' => 'required|exists:declaration_types,id',
            'period' => 'required|string|max:100',
            'due_date' => 'required|date',
            'filing_date' => 'nullable|date|before_or_equal:today',
            'status' => 'required|in:a_preparer,en_preparation,prete,deposee,payee,en_retard,annulee',
            'amount' => 'nullable|numeric|min:0',
            'filing_reference' => 'nullable|string|max:100',
            'responsible_id' => 'nullable|exists:employees,id',
            'comments' => 'nullable|string',
        ]);

        $declaration->update($validated);
        $declaration->checkOverdue();

        ActivityLog::log(
            action: 'modification',
            module: 'declarations',
            description: "Mise à jour de la déclaration {$declaration->type->name} ({$declaration->period})",
            targetId: $declaration->id,
            targetLabel: $declaration->period
        );

        return redirect()->route('declarations.show', $declaration)->with('success', "La déclaration a été mise à jour.");
    }

    public function markAsFiled(Request $request, Declaration $declaration, DeclarationService $declarationService)
    {
        $this->authorize('file', $declaration);

        $validated = $request->validate([
            // La date effective de télétransmission ne peut pas être postdatée (RG10)
            'filing_date' => 'required|date|before_or_equal:today',
            'filing_reference' => 'nullable|string|max:100',
            'comments' => 'nullable|string',
        ], [
            'filing_date.before_or_equal' => "La date de dépôt ne peut pas être postérieure à aujourd'hui.",
        ]);

        $declarationService->markAsFiled(
            $declaration,
            $validated['filing_date'],
            $validated['filing_reference'] ?? null,
            $validated['comments'] ?? null
        );

        return back()->with('success', "La déclaration {$declaration->period} a été marquée comme déposée.");
    }

    public function destroy(Declaration $declaration)
    {
        $this->authorize('delete', $declaration);

        $label = "{$declaration->type->name} ({$declaration->period})";
        $declaration->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'declarations',
            description: "Suppression de la déclaration {$label}",
            targetId: $declaration->id,
            targetLabel: $label
        );

        return redirect()->route('declarations.index')->with('success', "La déclaration a été supprimée.");
    }
}
