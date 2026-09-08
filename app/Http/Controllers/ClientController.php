<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('primaryContact')->withCount('dossiers', 'declarations', 'invoices');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('legal_form')) {
            $query->where('legal_form', $request->legal_form);
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        $clients = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $this->authorize('create', Client::class);
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Client::class);

        $validated = $request->validate([
            'type' => 'required|in:entreprise,particulier',
            'company_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'legal_form' => 'nullable|string|max:50',
            'ice' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{15}$/', 'unique:clients,ice'],
            'if_number' => 'nullable|string|max:30',
            'rc_number' => 'nullable|string|max:30',
            'patent_number' => 'nullable|string|max:30',
            'cnss_number' => 'nullable|string|max:30',
            'share_capital' => 'nullable|numeric|min:0',
            'activity' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'client_since' => 'nullable|date',
            'status' => 'required|in:prospect,actif,suspendu,archive',
            'notes' => 'nullable|string',

            // Représentant légal
            'contact_first_name' => 'nullable|string|max:100',
            'contact_last_name' => 'nullable|string|max:100',
            'contact_cin' => 'nullable|string|max:20',
            'contact_position' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $client = DB::transaction(function () use ($validated) {
            $validated['created_by'] = auth()->id();
            $client = Client::create($validated);

            if (!empty($validated['contact_first_name']) || !empty($validated['contact_last_name'])) {
                ClientContact::create([
                    'client_id' => $client->id,
                    'first_name' => $validated['contact_first_name'] ?? 'Contact',
                    'last_name' => $validated['contact_last_name'] ?? '',
                    'cin' => $validated['contact_cin'] ?? null,
                    'position' => $validated['contact_position'] ?? 'Représentant légal',
                    'phone' => $validated['contact_phone'] ?? null,
                    'email' => $validated['contact_email'] ?? null,
                    'is_primary' => true,
                ]);
            }

            ActivityLog::log(
                action: 'creation',
                module: 'clients',
                description: "Création du client '{$client->company_name}' (ICE: " . ($client->ice ?? 'N/A') . ")",
                targetId: $client->id,
                targetLabel: $client->company_name
            );

            return $client;
        });

        return redirect()->route('clients.show', $client)->with('success', "Le client '{$client->company_name}' a été créé avec succès.");
    }

    public function show(Client $client)
    {
        $client->load([
            'contacts',
            'dossiers.responsible',
            'declarations.type',
            'invoices.payments',
            'documents.uploader',
            'deadlines.responsible',
        ]);

        $activities = ActivityLog::where('module', 'clients')
            ->where('target_id', $client->id)
            ->orWhere(function ($q) use ($client) {
                $q->whereIn('module', ['dossiers', 'invoices', 'declarations'])
                  ->where('description', 'like', "%{$client->company_name}%");
            })
            ->orderByDesc('created_at')
            ->take(15)
            ->get();

        return view('clients.show', compact('client', 'activities'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);
        $client->load('primaryContact');
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorize('update', $client);

        $validated = $request->validate([
            'type' => 'required|in:entreprise,particulier',
            'company_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'legal_form' => 'nullable|string|max:50',
            'ice' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{15}$/', 'unique:clients,ice,' . $client->id],
            'if_number' => 'nullable|string|max:30',
            'rc_number' => 'nullable|string|max:30',
            'patent_number' => 'nullable|string|max:30',
            'cnss_number' => 'nullable|string|max:30',
            'share_capital' => 'nullable|numeric|min:0',
            'activity' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'client_since' => 'nullable|date',
            'status' => 'required|in:prospect,actif,suspendu,archive',
            'notes' => 'nullable|string',

            // Représentant
            'contact_first_name' => 'nullable|string|max:100',
            'contact_last_name' => 'nullable|string|max:100',
            'contact_cin' => 'nullable|string|max:20',
            'contact_position' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'contact_email' => 'nullable|email|max:255',
        ]);

        DB::transaction(function () use ($client, $validated) {
            $client->update($validated);

            if (!empty($validated['contact_first_name']) || !empty($validated['contact_last_name'])) {
                $contact = $client->primaryContact ?: new ClientContact(['client_id' => $client->id, 'is_primary' => true]);
                $contact->first_name = $validated['contact_first_name'] ?? 'Contact';
                $contact->last_name = $validated['contact_last_name'] ?? '';
                $contact->cin = $validated['contact_cin'] ?? null;
                $contact->position = $validated['contact_position'] ?? 'Représentant légal';
                $contact->phone = $validated['contact_phone'] ?? null;
                $contact->email = $validated['contact_email'] ?? null;
                $contact->save();
            }

            ActivityLog::log(
                action: 'modification',
                module: 'clients',
                description: "Modification de la fiche client '{$client->company_name}'",
                targetId: $client->id,
                targetLabel: $client->company_name
            );
        });

        return redirect()->route('clients.show', $client)->with('success', "Le client '{$client->company_name}' a été mis à jour.");
    }

    public function archive(Client $client)
    {
        $this->authorize('delete', $client);

        $client->status = 'archive';
        $client->save();

        ActivityLog::log(
            action: 'archivage',
            module: 'clients',
            description: "Archivage du client '{$client->company_name}'",
            targetId: $client->id,
            targetLabel: $client->company_name
        );

        return back()->with('info', "Le client '{$client->company_name}' a été archivé.");
    }

    public function restore(Client $client)
    {
        $this->authorize('delete', $client);

        $client->status = 'actif';
        $client->save();

        ActivityLog::log(
            action: 'modification',
            module: 'clients',
            description: "Restauration / Réactivation du client '{$client->company_name}'",
            targetId: $client->id,
            targetLabel: $client->company_name
        );

        return back()->with('success', "Le client '{$client->company_name}' a été réactivé.");
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        // Un client rattaché à des pièces comptables ne peut pas être supprimé : sa
        // suppression rendrait ses factures, règlements et déclarations orphelins et
        // fausserait les rapports financiers. L'archivage préserve l'intégralité du dossier.
        $rattachements = [
            'facture(s)' => $client->invoices()->count(),
            'règlement(s)' => $client->payments()->count(),
            'déclaration(s) fiscale(s)' => $client->declarations()->count(),
            'dossier(s) de mission' => $client->dossiers()->count(),
        ];

        $bloquants = array_filter($rattachements);

        if (!empty($bloquants)) {
            $detail = implode(', ', array_map(
                fn ($libelle, $nombre) => "{$nombre} {$libelle}",
                array_keys($bloquants),
                $bloquants
            ));

            return back()->with('error', "Impossible de supprimer '{$client->company_name}' : ce client est rattaché à {$detail}. Utilisez plutôt l'archivage pour conserver l'historique comptable.");
        }

        $name = $client->company_name;
        $id = $client->id;
        $client->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'clients',
            description: "Suppression logique du client '{$name}'",
            targetId: $id,
            targetLabel: $name
        );

        return redirect()->route('clients.index')->with('success', "Le client '{$name}' a été supprimé.");
    }
}
