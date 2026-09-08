@extends('layouts.app')

@section('title', 'Gestion des Dossiers Clients')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dossiers Clients</h1>
            <p class="text-xs sm:text-sm text-slate-500">Missions comptables, fiscales, juridiques et constitution de sociétés.</p>
        </div>
        @can('create', App\Models\Dossier::class)
            <a href="{{ route('dossiers.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Nouveau Dossier</span>
            </a>
        @endcan
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('dossiers.index') }}" class="p-4 rounded-2xl bg-white border {{ !request('status') ? 'border-sky-500 ring-2 ring-sky-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-slate-400 font-semibold uppercase">Total Missions</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Dossier::count() }}</p>
        </a>
        <a href="{{ route('dossiers.index', ['status' => 'en_cours']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'en_cours' ? 'border-sky-500 ring-2 ring-sky-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-sky-600 font-semibold uppercase">En Cours</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Dossier::where('status', 'en_cours')->count() }}</p>
        </a>
        <a href="{{ route('dossiers.index', ['status' => 'nouveau']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'nouveau' ? 'border-indigo-500 ring-2 ring-indigo-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-indigo-600 font-semibold uppercase">Nouveaux</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Dossier::where('status', 'nouveau')->count() }}</p>
        </a>
        <a href="{{ route('dossiers.index', ['priority' => 'urgente']) }}" class="p-4 rounded-2xl bg-white border {{ request('priority') === 'urgente' ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-rose-600 font-semibold uppercase">Priorité Urgente</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Dossier::where('priority', 'urgente')->count() }}</p>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('dossiers.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            
            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Réf dossier, client, description..." 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>

            <div>
                <select name="type" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les types</option>
                    <option value="comptabilite" {{ request('type') === 'comptabilite' ? 'selected' : '' }}>Comptabilité</option>
                    <option value="fiscalite" {{ request('type') === 'fiscalite' ? 'selected' : '' }}>Fiscalité</option>
                    <option value="conseil" {{ request('type') === 'conseil' ? 'selected' : '' }}>Conseil</option>
                    <option value="formation" {{ request('type') === 'formation' ? 'selected' : '' }}>Formation</option>
                    <option value="social_rh" {{ request('type') === 'social_rh' ? 'selected' : '' }}>Social & RH</option>
                    <option value="juridique" {{ request('type') === 'juridique' ? 'selected' : '' }}>Juridique</option>
                    <option value="creation_entreprise" {{ request('type') === 'creation_entreprise' ? 'selected' : '' }}>Création Sté</option>
                </select>
            </div>

            <div>
                <select name="status" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="nouveau" {{ request('status') === 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                    <option value="en_cours" {{ request('status') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="termine" {{ request('status') === 'termine' ? 'selected' : '' }}>Terminé</option>
                    <option value="suspendu" {{ request('status') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                </select>
            </div>

            <div>
                <select name="priority" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Priorité</option>
                    <option value="faible" {{ request('priority') === 'faible' ? 'selected' : '' }}>Faible</option>
                    <option value="moyenne" {{ request('priority') === 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                    <option value="haute" {{ request('priority') === 'haute' ? 'selected' : '' }}>Haute</option>
                    <option value="urgente" {{ request('priority') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition-colors">
                    Filtrer
                </button>
                <a href="{{ route('dossiers.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Réinitialiser">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Référence</th>
                        <th class="py-3.5 px-5">Client</th>
                        <th class="py-3.5 px-5">Type de Mission</th>
                        <th class="py-3.5 px-5">Équipe Affectée</th>
                        <th class="py-3.5 px-5">Priorité</th>
                        <th class="py-3.5 px-5">Statut</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dossiers as $dossier)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <a href="{{ route('dossiers.show', $dossier) }}" class="font-mono font-bold text-sky-600 hover:underline">
                                    {{ $dossier->reference }}
                                </a>
                                <p class="text-[11px] text-slate-400 mt-0.5">Depuis le {{ $dossier->start_date->format('d/m/Y') }}</p>
                            </td>

                            <td class="py-4 px-5">
                                <a href="{{ route('clients.show', $dossier->client_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block">
                                    {{ $dossier->client->company_name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono">ICE: {{ $dossier->client->ice ?? 'N/A' }}</span>
                            </td>

                            <td class="py-4 px-5">
                                <span class="font-semibold text-slate-800">{{ $dossier->type_label }}</span>
                                <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $dossier->description ?? 'Sans description' }}</p>
                            </td>

                            <td class="py-4 px-5">
                                <div class="flex items-center -space-x-1.5 overflow-hidden">
                                    @forelse($dossier->employees as $emp)
                                        <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white bg-slate-700 text-white text-[10px] font-bold flex items-center justify-center" title="{{ $emp->full_name }} ({{ $emp->pivot->role_in_dossier }})">
                                            {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
                                        </div>
                                    @empty
                                        <span class="text-slate-400 italic text-[11px]">Non affecté</span>
                                    @endforelse
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-1">Resp : {{ $dossier->responsible?->full_name ?? 'Cabinet' }}</span>
                            </td>

                            <td class="py-4 px-5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ $dossier->priority === 'urgente' ? 'bg-red-100 text-red-800' : ($dossier->priority === 'haute' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst($dossier->priority) }}
                                </span>
                            </td>

                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold 
                                    {{ $dossier->status === 'en_cours' ? 'bg-sky-100 text-sky-800' : ($dossier->status === 'termine' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $dossier->status_label }}
                                </span>
                            </td>

                            <td class="py-4 px-5 text-right space-x-1">
                                <a href="{{ route('dossiers.show', $dossier) }}" class="p-1.5 text-slate-400 hover:text-sky-600 rounded-lg hover:bg-slate-100" title="Consulter">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                @can('update', $dossier)
                                    <a href="{{ route('dossiers.edit', $dossier) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Aucun dossier trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dossiers->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $dossiers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
