@extends('layouts.app')

@section('title', $client->company_name)

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'dossiers' }">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('clients.index') }}" class="hover:text-sky-600">Clients</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">{{ $client->company_name }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $client->company_name }}</h1>
                @if($client->status === 'actif')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Actif</span>
                @elseif($client->status === 'prospect')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">Prospect</span>
                @elseif($client->status === 'suspendu')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Suspendu</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">Archivé</span>
                @endif
            </div>
        </div>

        <!-- Quick actions -->
        <div class="flex items-center flex-wrap gap-2">
            <a href="{{ route('dossiers.create', ['client_id' => $client->id]) }}" class="px-3 py-2 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-folder-plus"></i>
                <span>Nouveau Dossier</span>
            </a>
            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-file-invoice"></i>
                <span>Nouvelle Facture</span>
            </a>
            <a href="{{ route('documents.create', ['client_id' => $client->id]) }}" class="px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-upload"></i>
                <span>Archiver Document</span>
            </a>
            @can('update', $client)
                <a href="{{ route('clients.edit', $client) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-pen"></i>
                    <span>Modifier</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Client Header Card (Legal IDs Banner) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 text-xs">
        <div>
            <span class="text-slate-400 font-semibold uppercase text-[10px]">Forme Juridique</span>
            <p class="text-slate-800 font-bold mt-0.5">{{ $client->legal_form ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-slate-400 font-semibold uppercase text-[10px]">Numéro ICE</span>
            <p class="font-mono text-slate-800 font-bold mt-0.5">{{ $client->ice ?? 'Non renseigné' }}</p>
        </div>
        <div>
            <span class="text-slate-400 font-semibold uppercase text-[10px]">Identifiant Fiscal (IF)</span>
            <p class="font-mono text-slate-800 font-bold mt-0.5">{{ $client->if_number ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-slate-400 font-semibold uppercase text-[10px]">Registre Commerce (RC)</span>
            <p class="font-mono text-slate-800 font-bold mt-0.5">{{ $client->rc_number ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-slate-400 font-semibold uppercase text-[10px]">Affiliation CNSS</span>
            <p class="font-mono text-slate-800 font-bold mt-0.5">{{ $client->cnss_number ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-slate-400 font-semibold uppercase text-[10px]">Capital Social</span>
            <p class="text-slate-800 font-bold mt-0.5">{{ $client->share_capital ? number_format($client->share_capital, 2, ',', ' ') . ' DH' : 'N/A' }}</p>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-slate-200 flex items-center space-x-2 overflow-x-auto text-xs font-semibold">
        <button @click="activeTab = 'dossiers'" :class="activeTab === 'dossiers' ? 'border-sky-600 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 transition-colors flex items-center space-x-2 whitespace-nowrap">
            <i class="fa-solid fa-folder-open"></i>
            <span>Dossiers ({{ $client->dossiers->count() }})</span>
        </button>
        <button @click="activeTab = 'declarations'" :class="activeTab === 'declarations' ? 'border-sky-600 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 transition-colors flex items-center space-x-2 whitespace-nowrap">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Déclarations Fiscales ({{ $client->declarations->count() }})</span>
        </button>
        <button @click="activeTab = 'invoices'" :class="activeTab === 'invoices' ? 'border-sky-600 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 transition-colors flex items-center space-x-2 whitespace-nowrap">
            <i class="fa-solid fa-receipt"></i>
            <span>Factures & Règlements ({{ $client->invoices->count() }})</span>
        </button>
        <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-sky-600 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 transition-colors flex items-center space-x-2 whitespace-nowrap">
            <i class="fa-solid fa-box-archive"></i>
            <span>Documents GED ({{ $client->documents->count() }})</span>
        </button>
        <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-sky-600 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 transition-colors flex items-center space-x-2 whitespace-nowrap">
            <i class="fa-solid fa-circle-info"></i>
            <span>Fiche Coordonnées & Gérance</span>
        </button>
        <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-sky-600 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 transition-colors flex items-center space-x-2 whitespace-nowrap">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Historique & Audit</span>
        </button>
    </div>

    <!-- Tab 1: Dossiers -->
    <div x-show="activeTab === 'dossiers'" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Dossiers comptables & juridiques en cours</h3>
            <a href="{{ route('dossiers.create', ['client_id' => $client->id]) }}" class="text-xs font-semibold text-sky-600 hover:underline">
                + Nouveau dossier
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Référence</th>
                        <th class="py-3 px-4">Type de Mission</th>
                        <th class="py-3 px-4">Période</th>
                        <th class="py-3 px-4">Responsable</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($client->dossiers as $dos)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-sky-700">
                                <a href="{{ route('dossiers.show', $dos) }}" class="hover:underline">{{ $dos->reference }}</a>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $dos->type_label }}</td>
                            <td class="py-3 px-4 text-slate-600">
                                Du {{ $dos->start_date->format('d/m/Y') }} {{ $dos->end_date ? 'au ' . $dos->end_date->format('d/m/Y') : '' }}
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                {{ $dos->responsible?->full_name ?? 'Non assigné' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ $dos->status === 'en_cours' ? 'bg-sky-100 text-sky-800' : ($dos->status === 'termine' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $dos->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('dossiers.show', $dos) }}" class="text-sky-600 hover:underline font-semibold">Consulter &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Aucun dossier rattaché à ce client</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 2: Déclarations Fiscales -->
    <div x-show="activeTab === 'declarations'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Déclarations fiscales & sociales</h3>
            <a href="{{ route('declarations.create', ['client_id' => $client->id]) }}" class="text-xs font-semibold text-sky-600 hover:underline">
                + Ajouter une déclaration
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Impôt / Taxe</th>
                        <th class="py-3 px-4">Période</th>
                        <th class="py-3 px-4">Échéance</th>
                        <th class="py-3 px-4">Date Dépôt</th>
                        <th class="py-3 px-4">Montant DH</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($client->declarations as $decl)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $decl->type->name }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-600">{{ $decl->period }}</td>
                            <td class="py-3 px-4 {{ $decl->status === 'en_retard' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                {{ $decl->due_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $decl->filing_date ? $decl->filing_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ number_format($decl->amount, 2, ',', ' ') }}</td>
                            <td class="py-3 px-4">
                                @if($decl->status === 'deposee' || $decl->status === 'payee')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Déposée</span>
                                @elseif($decl->status === 'en_retard')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">En retard</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $decl->status_label }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('declarations.show', $decl) }}" class="text-sky-600 hover:underline font-semibold">Détails</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Aucune déclaration fiscale enregistrée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 3: Factures & Règlements -->
    <div x-show="activeTab === 'invoices'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Factures d'honoraires & paiements</h3>
            <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                + Émettre une facture
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">N° Facture</th>
                        <th class="py-3 px-4">Date Émission</th>
                        <th class="py-3 px-4">Échéance</th>
                        <th class="py-3 px-4">Total TTC</th>
                        <th class="py-3 px-4">Encaissé</th>
                        <th class="py-3 px-4">Reste Dû</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($client->invoices as $inv)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-sky-700">
                                <a href="{{ route('invoices.show', $inv) }}" class="hover:underline">{{ $inv->reference }}</a>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $inv->invoice_date->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $inv->due_date->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ number_format($inv->total_ttc, 2, ',', ' ') }} DH</td>
                            <td class="py-3 px-4 font-semibold text-emerald-600">{{ number_format($inv->paid_amount, 2, ',', ' ') }} DH</td>
                            <td class="py-3 px-4 font-semibold text-amber-600">{{ number_format($inv->remaining_amount, 2, ',', ' ') }} DH</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ $inv->status === 'payee' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'partiellement_payee' ? 'bg-amber-100 text-amber-800' : ($inv->status === 'en_retard' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ $inv->status_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('invoices.pdf', $inv) }}" class="text-slate-400 hover:text-slate-700" title="Télécharger PDF">
                                    <i class="fa-solid fa-file-pdf text-sm"></i>
                                </a>
                                <a href="{{ route('invoices.show', $inv) }}" class="text-sky-600 hover:underline font-semibold">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Aucune facture émise pour ce client</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 4: Documents GED -->
    <div x-show="activeTab === 'documents'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Documents juridiques & comptables archivés</h3>
            <a href="{{ route('documents.create', ['client_id' => $client->id]) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                + Déposer un fichier
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Titre du document</th>
                        <th class="py-3 px-4">Catégorie</th>
                        <th class="py-3 px-4">Taille</th>
                        <th class="py-3 px-4">Date d'archivage</th>
                        <th class="py-3 px-4">Ajouté par</th>
                        <th class="py-3 px-4 text-right">Téléchargement</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($client->documents as $doc)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-semibold text-slate-900 flex items-center space-x-2">
                                <i class="fa-regular fa-file-pdf text-rose-500 text-sm"></i>
                                <span>{{ $doc->title }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $doc->category_label }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $doc->formatted_size }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $doc->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $doc->uploader?->name ?? 'Cabinet' }}</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('documents.download', $doc) }}" class="px-3 py-1 bg-slate-100 hover:bg-sky-50 hover:text-sky-600 text-slate-700 rounded-lg font-semibold transition-colors inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-download text-xs"></i>
                                    <span>Télécharger</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Aucun document archivé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 5: Fiche Coordonnées & Gérance -->
    <div x-show="activeTab === 'info'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Coordonnées Générales -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Siège & Coordonnées</h3>
            <div class="space-y-2.5 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">Adresse</span>
                    <p class="text-slate-800 font-medium">{{ $client->address ?? 'Non renseignée' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Ville</span>
                        <p class="text-slate-800 font-medium">{{ $client->city ?? 'Casablanca' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Pays</span>
                        <p class="text-slate-800 font-medium">{{ $client->country ?? 'Maroc' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Téléphone</span>
                        <p class="text-slate-800 font-medium">{{ $client->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Email</span>
                        <p class="text-slate-800 font-medium">{{ $client->email ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($client->website)
                <div>
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">Site Internet</span>
                    <p><a href="{{ $client->website }}" target="_blank" class="text-sky-600 hover:underline">{{ $client->website }}</a></p>
                </div>
                @endif
                <div>
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">Activité & Objet Social</span>
                    <p class="text-slate-700 leading-relaxed">{{ $client->activity ?? 'Non renseigné' }}</p>
                </div>
            </div>
        </div>

        <!-- Représentant Légal -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Représentant Légal / Interlocuteurs</h3>
            @if($client->primaryContact)
                <div class="space-y-2.5 text-xs">
                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Nom et Prénom</span>
                        <p class="text-slate-900 font-bold text-sm">{{ $client->primaryContact->full_name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-400 font-semibold uppercase text-[10px]">CIN</span>
                            <p class="font-mono text-slate-800 font-bold">{{ $client->primaryContact->cin ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold uppercase text-[10px]">Fonction</span>
                            <p class="text-slate-800 font-medium">{{ $client->primaryContact->position ?? 'Gérant' }}</p>
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Téléphone Direct</span>
                        <p class="text-slate-800 font-medium">{{ $client->primaryContact->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Email Personnel</span>
                        <p class="text-slate-800 font-medium">{{ $client->primaryContact->email ?? 'N/A' }}</p>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400 py-6 text-center">Aucun contact enregistré</p>
            @endif

            @if($client->notes)
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <span class="text-slate-400 font-semibold uppercase text-[10px]">Notes internes du cabinet</span>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $client->notes }}</p>
                </div>
            @endif
        </div>

    </div>

    <!-- Tab 6: Audit Activity -->
    <div x-show="activeTab === 'activity'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 text-sm mb-4">Traçabilité & Historique des Opérations du Client</h3>
        <div class="space-y-3">
            @forelse($activities as $act)
                <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-semibold text-slate-800">{{ $act->description }}</span>
                        <p class="text-[11px] text-slate-400 mt-0.5">Par <strong>{{ $act->user_name }}</strong> &bull; Module : {{ ucfirst($act->module) }}</p>
                    </div>
                    <span class="text-slate-400 text-[11px] font-mono">{{ $act->created_at->format('d/m/Y H:i') }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-6 text-center">Aucun journal pour ce client</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
