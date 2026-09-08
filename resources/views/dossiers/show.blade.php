@extends('layouts.app')

@section('title', 'Dossier ' . $dossier->reference)

@section('content')
<div class="space-y-6" x-data="{ assignModal: false }">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('dossiers.index') }}" class="hover:text-sky-600">Dossiers</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">{{ $dossier->reference }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $dossier->reference }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                    {{ $dossier->status === 'en_cours' ? 'bg-sky-100 text-sky-800' : ($dossier->status === 'termine' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                    {{ $dossier->status_label }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $dossier->priority === 'urgente' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-50 text-slate-700 border-slate-200' }}">
                    Priorité {{ ucfirst($dossier->priority) }}
                </span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('declarations.create', ['client_id' => $dossier->client_id, 'dossier_id' => $dossier->id]) }}" class="px-3 py-2 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>Ajouter Déclaration</span>
            </a>
            <a href="{{ route('documents.create', ['client_id' => $dossier->client_id, 'dossier_id' => $dossier->id]) }}" class="px-3 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-upload"></i>
                <span>Déposer Document</span>
            </a>
            @can('update', $dossier)
                <a href="{{ route('dossiers.edit', $dossier) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-pen"></i>
                    <span>Modifier</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Dossier Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main details (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Details Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900">Synthèse de la Mission</h2>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">{{ $dossier->type_label }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Client</span>
                        <p class="font-bold text-slate-900 mt-0.5">
                            <a href="{{ route('clients.show', $dossier->client_id) }}" class="hover:text-sky-600">
                                {{ $dossier->client->company_name }}
                            </a>
                        </p>
                        <span class="text-[11px] text-slate-400">ICE : {{ $dossier->client->ice ?? 'N/A' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Date d'ouverture</span>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $dossier->start_date->format('d/m/Y') }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Date de fin / Clôture</span>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $dossier->end_date ? $dossier->end_date->format('d/m/Y') : 'En cours (non définie)' }}</p>
                    </div>
                </div>

                @if($dossier->description)
                    <div class="pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Description des prestations</span>
                        <p class="text-slate-700 mt-1 leading-relaxed">{{ $dossier->description }}</p>
                    </div>
                @endif

                @if($dossier->notes)
                    <div class="pt-2 text-xs">
                        <span class="text-slate-400 font-semibold uppercase text-[10px]">Notes internes</span>
                        <p class="text-slate-600 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $dossier->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Associated Tax Declarations -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Déclarations Fiscales Liées ({{ $dossier->declarations->count() }})</h3>
                    <a href="{{ route('declarations.create', ['client_id' => $dossier->client_id, 'dossier_id' => $dossier->id]) }}" class="text-xs font-semibold text-sky-600 hover:underline">
                        + Ajouter
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Période</th>
                                <th class="py-3 px-4">Échéance</th>
                                <th class="py-3 px-4">Montant DH</th>
                                <th class="py-3 px-4">Statut</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dossier->declarations as $decl)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-4 font-bold text-slate-800">{{ $decl->type->name }}</td>
                                    <td class="py-3 px-4 text-slate-600">{{ $decl->period }}</td>
                                    <td class="py-3 px-4 {{ $decl->status === 'en_retard' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                        {{ $decl->due_date->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-800">{{ number_format($decl->amount, 2, ',', ' ') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                            {{ $decl->status === 'deposee' ? 'bg-emerald-100 text-emerald-800' : ($decl->status === 'en_retard' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $decl->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('declarations.show', $decl) }}" class="text-sky-600 hover:underline font-semibold">Détails</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-400">Aucune déclaration rattachée à ce dossier</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Associated Documents -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Pièces & Documents Comptables ({{ $dossier->documents->count() }})</h3>
                    <a href="{{ route('documents.create', ['client_id' => $dossier->client_id, 'dossier_id' => $dossier->id]) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                        + Déposer
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-4">Document</th>
                                <th class="py-3 px-4">Catégorie</th>
                                <th class="py-3 px-4">Taille</th>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4 text-right">Télécharger</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dossier->documents as $doc)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-4 font-semibold text-slate-800 flex items-center space-x-2">
                                        <i class="fa-regular fa-file text-slate-400"></i>
                                        <span>{{ $doc->title }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">{{ $doc->category_label }}</td>
                                    <td class="py-3 px-4 font-mono text-slate-500">{{ $doc->formatted_size }}</td>
                                    <td class="py-3 px-4 text-slate-600">{{ $doc->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('documents.download', $doc) }}" class="text-sky-600 hover:underline font-semibold">Télécharger</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Aucun document rattaché</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Sidebar: Affectation des Collaborateurs -->
        <div class="space-y-6">
            
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Équipe Affectée</h3>
                        <p class="text-[11px] text-slate-400">Collaborateurs en charge du dossier</p>
                    </div>
                    @can('update', $dossier)
                        <button @click="assignModal = true" class="px-2.5 py-1.5 bg-sky-50 text-sky-700 hover:bg-sky-100 rounded-lg text-xs font-semibold transition-colors">
                            <i class="fa-solid fa-user-plus mr-1"></i> Affecter
                        </button>
                    @endcan
                </div>

                <!-- Superviseur principal -->
                <div class="p-3.5 rounded-2xl bg-sky-50/60 border border-sky-100 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                        {{ strtoupper(substr($dossier->responsible?->first_name ?? 'C', 0, 1) . substr($dossier->responsible?->last_name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600">Superviseur Référent</span>
                        <p class="text-xs font-bold text-slate-900">{{ $dossier->responsible?->full_name ?? 'Cabinet Fiduciaire' }}</p>
                        <p class="text-[11px] text-slate-500">{{ $dossier->responsible?->position ?? 'Direction' }}</p>
                    </div>
                </div>

                <!-- Liste des affectations N:N -->
                <div class="space-y-2.5">
                    @forelse($dossier->employees as $emp)
                        <div class="p-3 rounded-2xl border border-slate-100 bg-slate-50/70 flex items-center justify-between">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-slate-700 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $emp->full_name }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $emp->pivot->role_in_dossier }} &bull; Depuis {{ \Carbon\Carbon::parse($emp->pivot->assigned_date)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            @can('update', $dossier)
                                <form action="{{ route('dossiers.remove-employee', [$dossier, $emp]) }}" method="POST" onsubmit="return confirm('Retirer ce collaborateur du dossier ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg transition-colors" title="Retirer l'affectation">
                                        <i class="fa-solid fa-user-minus text-xs"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Aucun collaborateur secondaire affecté</p>
                    @endforelse
                </div>

            </div>

        </div>

    </div>

    <!-- Modal d'affectation d'employé -->
    <div x-show="assignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="assignModal = false" class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm">Affecter un collaborateur au dossier</h3>
                <button @click="assignModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('dossiers.assign-employee', $dossier) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Collaborateur *</label>
                    <select name="employee_id" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionnez un employé...</option>
                        @foreach($availableEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->position }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fonction / Rôle dans la mission *</label>
                    <input type="text" name="role_in_dossier" required placeholder="Ex: Saisie & TVA, Rapprochement, Juriste..." value="Comptable chargé du dossier" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'affectation *</label>
                    <input type="date" name="assigned_date" required value="{{ date('Y-m-d') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="assignModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Annuler</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md shadow-sky-500/20">Valider l'affectation</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
