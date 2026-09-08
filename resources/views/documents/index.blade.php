@extends('layouts.app')

@section('title', 'Gestion Électronique des Documents (GED)')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Archivage Numérique (GED)</h1>
            <p class="text-xs sm:text-sm text-slate-500">Centralisation sécurisée des pièces comptables, statuts, bilans et justificatifs clients.</p>
        </div>
        <a href="{{ route('documents.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-md shadow-indigo-500/20 transition-all flex items-center justify-center space-x-2">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>Archiver un Document</span>
        </a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold uppercase">Total Fichiers</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Document::count() }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-indigo-600 font-semibold uppercase">Statuts & PV</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Document::whereIn('category', ['statuts', 'pv', 'rc'])->count() }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-emerald-600 font-semibold uppercase">Bilans & Liasses</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Document::whereIn('category', ['bilan', 'declaration_fiscale'])->count() }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-sky-600 font-semibold uppercase">Pièces Comptables</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Document::whereIn('category', ['document_comptable', 'facture', 'releve_bancaire'])->count() }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('documents.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Titre du document, nom de fichier..." 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-500 outline-none">
                </div>
            </div>

            <div>
                <select name="category" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="">Toutes les catégories</option>
                    <option value="contrat" {{ request('category') === 'contrat' ? 'selected' : '' }}>Contrat</option>
                    <option value="facture" {{ request('category') === 'facture' ? 'selected' : '' }}>Facture justificative</option>
                    <option value="releve_bancaire" {{ request('category') === 'releve_bancaire' ? 'selected' : '' }}>Relevé bancaire</option>
                    <option value="declaration_fiscale" {{ request('category') === 'declaration_fiscale' ? 'selected' : '' }}>Déclaration fiscale</option>
                    <option value="bilan" {{ request('category') === 'bilan' ? 'selected' : '' }}>Bilan / Liasse</option>
                    <option value="pv" {{ request('category') === 'pv' ? 'selected' : '' }}>Procès-verbal (PV)</option>
                    <option value="rc" {{ request('category') === 'rc' ? 'selected' : '' }}>Modèle J / RC</option>
                    <option value="statuts" {{ request('category') === 'statuts' ? 'selected' : '' }}>Statuts</option>
                    <option value="attestation" {{ request('category') === 'attestation' ? 'selected' : '' }}>Attestation</option>
                    <option value="document_comptable" {{ request('category') === 'document_comptable' ? 'selected' : '' }}>Pièce comptable</option>
                </select>
            </div>

            <div>
                <select name="client_id" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition-colors">
                    Filtrer
                </button>
                <a href="{{ route('documents.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Réinitialiser">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Documents Grid / Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Titre du Fichier</th>
                        <th class="py-3.5 px-5">Client Associé</th>
                        <th class="py-3.5 px-5">Catégorie</th>
                        <th class="py-3.5 px-5">Taille</th>
                        <th class="py-3.5 px-5">Date d'Archivage</th>
                        <th class="py-3.5 px-5">Ajouté par</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div class="truncate max-w-xs">
                                        <p class="font-bold text-slate-900 truncate">{{ $doc->title }}</p>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $doc->file_name }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-5">
                                @if($doc->client)
                                    <a href="{{ route('clients.show', $doc->client_id) }}" class="font-semibold text-slate-900 hover:text-sky-600">
                                        {{ $doc->client->company_name }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Interne cabinet</span>
                                @endif
                            </td>

                            <td class="py-4 px-5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $doc->category_label }}
                                </span>
                            </td>

                            <td class="py-4 px-5 font-mono text-slate-600">
                                {{ $doc->formatted_size }}
                            </td>

                            <td class="py-4 px-5 text-slate-600">
                                {{ $doc->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="py-4 px-5 text-slate-700">
                                {{ $doc->uploader?->name ?? 'Cabinet' }}
                            </td>

                            <td class="py-4 px-5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('documents.download', $doc) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-sky-50 hover:text-sky-600 text-slate-700 rounded-xl font-semibold text-xs transition-colors inline-flex items-center space-x-1.5" title="Télécharger">
                                    <i class="fa-solid fa-download text-xs"></i>
                                    <span>Télécharger</span>
                                </a>
                                @can('delete', $doc)
                                    <form action="{{ route('documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce document ?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-colors" title="Supprimer">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-box-archive text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Aucun document dans la GED</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
