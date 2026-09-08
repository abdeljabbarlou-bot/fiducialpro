@extends('layouts.app')

@section('title', 'Journal d\'Audit & Traçabilité')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Journal d'Audit & Sécurité</h1>
            <p class="text-xs sm:text-sm text-slate-500">Traçabilité complète des créations, modifications, encaissements et suppressions dans l'application.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('activity-logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Rechercher par description, utilisateur..." 
                       class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
            </div>

            <div>
                <select name="module" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les modules</option>
                    <option value="auth" {{ request('module') === 'auth' ? 'selected' : '' }}>Authentification</option>
                    <option value="clients" {{ request('module') === 'clients' ? 'selected' : '' }}>Clients</option>
                    <option value="dossiers" {{ request('module') === 'dossiers' ? 'selected' : '' }}>Dossiers</option>
                    <option value="declarations" {{ request('module') === 'declarations' ? 'selected' : '' }}>Déclarations</option>
                    <option value="invoices" {{ request('module') === 'invoices' ? 'selected' : '' }}>Factures</option>
                    <option value="payments" {{ request('module') === 'payments' ? 'selected' : '' }}>Paiements</option>
                    <option value="documents" {{ request('module') === 'documents' ? 'selected' : '' }}>Documents</option>
                </select>
            </div>

            <div>
                <select name="action" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Toutes les actions</option>
                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Création</option>
                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Modification</option>
                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Suppression</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Connexion</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition-colors">
                    Filtrer
                </button>
                <a href="{{ route('activity-logs.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Réinitialiser">
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
                        <th class="py-3.5 px-5">Date & Heure</th>
                        <th class="py-3.5 px-5">Opérateur</th>
                        <th class="py-3.5 px-5">Module</th>
                        <th class="py-3.5 px-5">Action</th>
                        <th class="py-3.5 px-5">Description de l'Opération</th>
                        <th class="py-3.5 px-5">Adresse IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5 text-slate-500 font-mono">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>

                            <td class="py-4 px-5 font-bold text-slate-800">
                                {{ $log->user_name }}
                            </td>

                            <td class="py-4 px-5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 capitalize">
                                    {{ $log->module }}
                                </span>
                            </td>

                            <td class="py-4 px-5 font-semibold">
                                @if($log->action === 'create')
                                    <span class="text-emerald-600">Création</span>
                                @elseif($log->action === 'update')
                                    <span class="text-sky-600">Mise à jour</span>
                                @elseif($log->action === 'delete')
                                    <span class="text-rose-600">Suppression</span>
                                @elseif($log->action === 'login')
                                    <span class="text-indigo-600">Connexion</span>
                                @else
                                    <span class="text-slate-600">{{ ucfirst($log->action) }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-slate-700 max-w-md">
                                <span class="font-medium">{{ $log->description }}</span>
                                @if($log->target_label)
                                    <span class="text-[11px] text-slate-400 block font-mono">Cible : {{ $log->target_label }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 font-mono text-slate-400 text-[11px]">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Aucun journal d'activité enregistré</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
