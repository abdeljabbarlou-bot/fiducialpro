@extends('layouts.app')

@section('title', $employee->full_name)

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('employees.index') }}" class="hover:text-sky-600">Collaborateurs</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">{{ $employee->full_name }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $employee->full_name }}</h1>
                @if($employee->status === 'actif')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Actif</span>
                @elseif($employee->status === 'conge')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">En congé</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">Inactif</span>
                @endif
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @can('update', $employee)
            <a href="{{ route('employees.edit', $employee) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-pen"></i>
                <span>Modifier</span>
            </a>
            @endcan
            <a href="{{ route('employees.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Retour
            </a>
        </div>
    </div>

    <!-- Employee Profile Banner -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-800 text-white font-black text-xl flex items-center justify-center shadow-md">
                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $employee->full_name }}</h2>
                <p class="text-xs text-sky-600 font-semibold">{{ $employee->position }}</p>
                <div class="flex items-center space-x-3 text-xs text-slate-400 mt-1">
                    <span>Matricule : <strong class="text-slate-700 font-mono">{{ $employee->matricule }}</strong></span>
                    <span>&bull;</span>
                    <span>CIN : <strong class="text-slate-700 font-mono">{{ $employee->cin }}</strong></span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-6 text-xs border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6">
            <div>
                <span class="text-slate-400 uppercase text-[10px] font-semibold">Contact Direct</span>
                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->phone }}</p>
                <p class="text-slate-500">{{ $employee->email }}</p>
            </div>
            <div>
                <span class="text-slate-400 uppercase text-[10px] font-semibold">Ancienneté</span>
                <p class="font-bold text-slate-800 mt-0.5">{{ $employee->hire_date->format('d/m/Y') }}</p>
                <p class="text-slate-500">{{ $employee->hire_date->diffForHumans(null, true) }}</p>
            </div>
        </div>
    </div>

    <!-- Dossiers Assignés -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Missions & Dossiers Affectés ({{ $employee->dossiers->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Réf Dossier</th>
                        <th class="py-3 px-4">Client</th>
                        <th class="py-3 px-4">Type de Mission</th>
                        <th class="py-3 px-4">Rôle dans le dossier</th>
                        <th class="py-3 px-4">Date Affectation</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employee->dossiers as $dos)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-sky-700">
                                <a href="{{ route('dossiers.show', $dos) }}" class="hover:underline">{{ $dos->reference }}</a>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                <a href="{{ route('clients.show', $dos->client_id) }}" class="hover:text-sky-600">{{ $dos->client->company_name }}</a>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-700">{{ $dos->type_label }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 font-medium text-slate-700">
                                    {{ $dos->pivot->role_in_dossier }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ \Carbon\Carbon::parse($dos->pivot->assigned_date)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                    {{ $dos->status === 'en_cours' ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $dos->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('dossiers.show', $dos) }}" class="text-sky-600 hover:underline font-semibold">Ouvrir &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Aucun dossier actuellement confié à ce collaborateur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
