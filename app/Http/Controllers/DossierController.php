<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\DossierEmployee;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DossierController extends Controller
{
    public function index(Request $request)
    {
        $query = Dossier::with(['client', 'responsible', 'employees'])->withCount('declarations', 'documents');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $dossiers = $query->orderByDesc('id')->paginate(10)->withQueryString();
        $clients = Client::orderBy('company_name')->get();
        $employees = Employee::where('status', 'actif')->orderBy('last_name')->get();

        return view('dossiers.index', compact('dossiers', 'clients', 'employees'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Dossier::class);

        $clients = Client::where('status', '!=', 'archive')->orderBy('company_name')->get();
        $employees = Employee::where('status', 'actif')->orderBy('last_name')->get();
        $selectedClientId = $request->query('client_id');

        return view('dossiers.create', compact('clients', 'employees', 'selectedClientId'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Dossier::class);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|in:comptabilite,fiscalite,conseil,formation,social_rh,juridique,creation_entreprise',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:nouveau,en_cours,en_attente,termine,suspendu,archive',
            'priority' => 'required|in:faible,moyenne,haute,urgente',
            'responsible_id' => 'nullable|exists:employees,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_employee_id' => 'nullable|exists:employees,id',
            'role_in_dossier' => 'nullable|string|max:100',
        ]);

        // Deux dossiers ouverts simultanément peuvent viser la même référence : la contrainte
        // d'unicité rejette alors le second, on régénère la référence et on retente.
        $maxAttempts = 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $dossier = $this->persistDossier($validated);
                break;
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === $maxAttempts) {
                    throw $e;
                }
            }
        }

        return redirect()->route('dossiers.show', $dossier)->with('success', "Le dossier {$dossier->reference} a été créé avec succès.");
    }

    protected function persistDossier(array $validated): Dossier
    {
        return DB::transaction(function () use ($validated) {
            // Auto-génération de la référence unique DOS-YYYY-XXXX
            $year = Carbon::now()->year;
            $count = Dossier::withTrashed()->whereYear('created_at', $year)->count() + 1;
            $reference = sprintf('DOS-%d-%04d', $year, $count);

            while (Dossier::withTrashed()->where('reference', $reference)->exists()) {
                $count++;
                $reference = sprintf('DOS-%d-%04d', $year, $count);
            }

            $validated['reference'] = $reference;
            $dossier = Dossier::create($validated);

            // Affectation optionnelle directe du collaborateur
            if (!empty($validated['assigned_employee_id'])) {
                $dossier->employees()->attach($validated['assigned_employee_id'], [
                    'role_in_dossier' => $validated['role_in_dossier'] ?? 'Comptable chargé du dossier',
                    'assigned_date' => $validated['start_date'],
                    'status' => 'actif',
                ]);
            }

            ActivityLog::log(
                action: 'creation',
                module: 'dossiers',
                description: "Création du dossier {$dossier->reference} ({$dossier->type_label}) pour le client #{$dossier->client_id}",
                targetId: $dossier->id,
                targetLabel: $dossier->reference
            );

            return $dossier;
        });
    }

    public function show(Dossier $dossier)
    {
        $dossier->load([
            'client',
            'responsible',
            'employees',
            'declarations.type',
            'documents.uploader',
            'deadlines.responsible',
        ]);

        $availableEmployees = Employee::where('status', 'actif')
            ->whereNotIn('id', $dossier->employees->pluck('id'))
            ->orderBy('last_name')
            ->get();

        return view('dossiers.show', compact('dossier', 'availableEmployees'));
    }

    public function edit(Dossier $dossier)
    {
        $this->authorize('update', $dossier);

        $clients = Client::orderBy('company_name')->get();
        $employees = Employee::where('status', 'actif')->orderBy('last_name')->get();

        return view('dossiers.edit', compact('dossier', 'clients', 'employees'));
    }

    public function update(Request $request, Dossier $dossier)
    {
        $this->authorize('update', $dossier);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type' => 'required|in:comptabilite,fiscalite,conseil,formation,social_rh,juridique,creation_entreprise',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:nouveau,en_cours,en_attente,termine,suspendu,archive',
            'priority' => 'required|in:faible,moyenne,haute,urgente',
            'responsible_id' => 'nullable|exists:employees,id',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $dossier->update($validated);

        ActivityLog::log(
            action: 'modification',
            module: 'dossiers',
            description: "Mise à jour du dossier {$dossier->reference}",
            targetId: $dossier->id,
            targetLabel: $dossier->reference
        );

        return redirect()->route('dossiers.show', $dossier)->with('success', "Le dossier {$dossier->reference} a été mis à jour.");
    }

    public function assignEmployee(Request $request, Dossier $dossier)
    {
        $this->authorize('update', $dossier);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role_in_dossier' => 'required|string|max:100',
            'assigned_date' => 'required|date',
        ]);

        if ($dossier->employees()->where('employees.id', $validated['employee_id'])->exists()) {
            return back()->withErrors(['employee_id' => 'Ce collaborateur est déjà affecté à ce dossier.']);
        }

        $dossier->employees()->attach($validated['employee_id'], [
            'role_in_dossier' => $validated['role_in_dossier'],
            'assigned_date' => $validated['assigned_date'],
            'status' => 'actif',
        ]);

        $employee = Employee::find($validated['employee_id']);

        ActivityLog::log(
            action: 'modification',
            module: 'dossiers',
            description: "Affectation du collaborateur {$employee->full_name} au dossier {$dossier->reference} en tant que '{$validated['role_in_dossier']}'",
            targetId: $dossier->id,
            targetLabel: $dossier->reference
        );

        return back()->with('success', "Le collaborateur {$employee->full_name} a été affecté au dossier.");
    }

    public function removeEmployee(Dossier $dossier, Employee $employee)
    {
        $this->authorize('update', $dossier);

        $dossier->employees()->detach($employee->id);

        ActivityLog::log(
            action: 'modification',
            module: 'dossiers',
            description: "Retrait du collaborateur {$employee->full_name} du dossier {$dossier->reference}",
            targetId: $dossier->id,
            targetLabel: $dossier->reference
        );

        return back()->with('info', "L'affectation a été retirée du dossier.");
    }

    public function destroy(Dossier $dossier)
    {
        $this->authorize('delete', $dossier);

        // Même principe que pour les clients : un dossier portant des obligations
        // fiscales ou des pièces archivées ne peut pas être supprimé.
        $declarations = $dossier->declarations()->count();
        $documents = $dossier->documents()->count();

        if ($declarations > 0 || $documents > 0) {
            return back()->with('error', "Impossible de supprimer le dossier {$dossier->reference} : il porte {$declarations} déclaration(s) et {$documents} document(s). Clôturez ou archivez le dossier pour conserver l'historique.");
        }

        $ref = $dossier->reference;
        $dossier->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'dossiers',
            description: "Suppression du dossier {$ref}",
            targetId: $dossier->id,
            targetLabel: $ref
        );

        return redirect()->route('dossiers.index')->with('success', "Le dossier {$ref} a été supprimé.");
    }
}
