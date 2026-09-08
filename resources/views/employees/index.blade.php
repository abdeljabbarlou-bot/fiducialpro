@extends('layouts.app')

@section('title', 'Collaborateurs du Cabinet')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Collaborateurs & Équipe du Cabinet</h1>
            <p class="text-xs sm:text-sm text-slate-500">Comptables, fiscalistes, juristes et assistants du cabinet fiduciaire.</p>
        </div>
        @can('create', App\Models\Employee::class)
            <a href="{{ route('employees.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Nouveau Collaborateur</span>
            </a>
        @endcan
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold uppercase">Effectif Total</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ \App\Models\Employee::count() }} <span class="text-xs font-normal text-slate-400">membres</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-emerald-600 font-semibold uppercase">En Activité</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ \App\Models\Employee::where('status', 'actif')->count() }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-indigo-600 font-semibold uppercase">Missions Assignées</span>
            <p class="text-2xl font-black text-indigo-600 mt-1">{{ \App\Models\Dossier::whereIn('status', ['nouveau', 'en_cours'])->count() }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('employees.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nom, prénom, matricule, CIN, fonction, email..." 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <select name="status" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('status') === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="conge" {{ request('status') === 'conge' ? 'selected' : '' }}>En congé</option>
                    <option value="inactif" {{ request('status') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                </select>
                <button type="submit" class="py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Collaborateur</th>
                        <th class="py-3.5 px-5">CIN & Matricule</th>
                        <th class="py-3.5 px-5">Fonction / Rôle</th>
                        <th class="py-3.5 px-5">Coordonnées</th>
                        <th class="py-3.5 px-5 text-center">Dossiers Gérés</th>
                        <th class="py-3.5 px-5">Statut</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-800 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                        {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('employees.show', $emp) }}" class="font-bold text-slate-900 hover:text-sky-600 block">
                                            {{ $emp->full_name }}
                                        </a>
                                        <span class="text-[11px] text-slate-400">Embauché le {{ $emp->hire_date->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-5 font-mono text-slate-700">
                                <span class="block font-bold">{{ $emp->cin }}</span>
                                <span class="text-[11px] text-slate-400">{{ $emp->matricule }}</span>
                            </td>

                            <td class="py-4 px-5 font-semibold text-slate-800">
                                {{ $emp->position }}
                                @if($emp->user?->role)
                                    <span class="block text-[10px] text-sky-600 font-normal">Compte : {{ $emp->user->role->name }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-slate-600">
                                <p>{{ $emp->phone }}</p>
                                <p class="text-[11px] text-slate-400">{{ $emp->email }}</p>
                            </td>

                            <td class="py-4 px-5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700">
                                    {{ $emp->dossiers_count }}
                                </span>
                            </td>

                            <td class="py-4 px-5">
                                @if($emp->status === 'actif')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Actif</span>
                                @elseif($emp->status === 'conge')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">En congé</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">Inactif</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('employees.show', $emp) }}" class="p-1.5 text-slate-400 hover:text-sky-600 rounded-lg hover:bg-slate-100 inline-block" title="Voir la fiche">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                @can('update', $emp)
                                    <a href="{{ route('employees.edit', $emp) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100 inline-block" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-user-group text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Aucun collaborateur trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
