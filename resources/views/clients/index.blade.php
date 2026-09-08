@extends('layouts.app')

@section('title', 'Gestion des Clients')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Portefeuille Clients</h1>
            <p class="text-xs sm:text-sm text-slate-500">Entreprises, filiales et personnes physiques gérées par le cabinet.</p>
        </div>
        @can('create', App\Models\Client::class)
            <a href="{{ route('clients.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Nouveau Client</span>
            </a>
        @endcan
    </div>

    <!-- Quick Filter Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('clients.index') }}" class="p-4 rounded-2xl bg-white border {{ !request('status') ? 'border-sky-500 ring-2 ring-sky-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-slate-400 font-semibold uppercase">Total Clients</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Client::count() }}</p>
        </a>
        <a href="{{ route('clients.index', ['status' => 'actif']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'actif' ? 'border-emerald-500 ring-2 ring-emerald-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-emerald-600 font-semibold uppercase">Clients Actifs</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Client::where('status', 'actif')->count() }}</p>
        </a>
        <a href="{{ route('clients.index', ['status' => 'prospect']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'prospect' ? 'border-indigo-500 ring-2 ring-indigo-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-indigo-600 font-semibold uppercase">Prospects</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Client::where('status', 'prospect')->count() }}</p>
        </a>
        <a href="{{ route('clients.index', ['status' => 'archive']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'archive' ? 'border-amber-500 ring-2 ring-amber-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-amber-600 font-semibold uppercase">Archivés</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Client::where('status', 'archive')->count() }}</p>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('clients.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Recherche par Raison sociale, ICE, RC, IF, Tél..." 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/10 outline-none">
                </div>
            </div>

            <div>
                <select name="status" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('status') === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="prospect" {{ request('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                    <option value="suspendu" {{ request('status') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                    <option value="archive" {{ request('status') === 'archive' ? 'selected' : '' }}>Archivé</option>
                </select>
            </div>

            <div>
                <select name="legal_form" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Forme juridique</option>
                    <option value="SARL" {{ request('legal_form') === 'SARL' ? 'selected' : '' }}>SARL</option>
                    <option value="SARL AU" {{ request('legal_form') === 'SARL AU' ? 'selected' : '' }}>SARL AU</option>
                    <option value="SA" {{ request('legal_form') === 'SA' ? 'selected' : '' }}>SA</option>
                    <option value="SNC" {{ request('legal_form') === 'SNC' ? 'selected' : '' }}>SNC</option>
                    <option value="Auto-entrepreneur" {{ request('legal_form') === 'Auto-entrepreneur' ? 'selected' : '' }}>Auto-entrepreneur</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition-colors">
                    Filtrer
                </button>
                <a href="{{ route('clients.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Réinitialiser">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Raison Sociale</th>
                        <th class="py-3.5 px-5">Identifiants Fiscaux</th>
                        <th class="py-3.5 px-5">Représentant</th>
                        <th class="py-3.5 px-5">Localisation</th>
                        <th class="py-3.5 px-5 text-center">Dossiers</th>
                        <th class="py-3.5 px-5">Statut</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <a href="{{ route('clients.show', $client) }}" class="font-bold text-sm text-slate-900 hover:text-sky-600 block">
                                    {{ $client->company_name }}
                                </a>
                                <div class="flex items-center space-x-2 text-[11px] text-slate-400 mt-0.5">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 font-semibold text-slate-600">{{ $client->legal_form ?? 'Ste' }}</span>
                                    @if($client->trade_name)
                                        <span>&bull; {{ $client->trade_name }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4 px-5">
                                <div class="text-[11px] space-y-0.5">
                                    <p><strong class="text-slate-500">ICE :</strong> <span class="font-mono text-slate-700">{{ $client->ice ?? 'N/A' }}</span></p>
                                    <p><strong class="text-slate-500">IF :</strong> <span class="font-mono text-slate-700">{{ $client->if_number ?? 'N/A' }}</span> | <strong class="text-slate-500">RC :</strong> <span class="font-mono text-slate-700">{{ $client->rc_number ?? 'N/A' }}</span></p>
                                </div>
                            </td>

                            <td class="py-4 px-5">
                                @if($client->primaryContact)
                                    <p class="font-semibold text-slate-800">{{ $client->primaryContact->full_name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $client->primaryContact->position ?? 'Représentant' }} &bull; {{ $client->primaryContact->phone }}</p>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Non renseigné</span>
                                @endif
                            </td>

                            <td class="py-4 px-5">
                                <p class="text-slate-700 font-medium">{{ $client->city ?? 'Casablanca' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $client->phone }}</p>
                            </td>

                            <td class="py-4 px-5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700">
                                    {{ $client->dossiers_count }}
                                </span>
                            </td>

                            <td class="py-4 px-5">
                                @if($client->status === 'actif')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Actif</span>
                                @elseif($client->status === 'prospect')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800">Prospect</span>
                                @elseif($client->status === 'suspendu')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Suspendu</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">Archivé</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <a href="{{ route('clients.show', $client) }}" class="p-1.5 text-slate-400 hover:text-sky-600 rounded-lg hover:bg-slate-100" title="Consulter la fiche">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    @can('update', $client)
                                        <a href="{{ route('clients.edit', $client) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100" title="Modifier">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $client)
                                        @if($client->status !== 'archive')
                                            <form action="{{ route('clients.archive', $client) }}" method="POST" onsubmit="return confirm('Archiver ce client ?');" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100" title="Archiver">
                                                    <i class="fa-solid fa-box-archive"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('clients.restore', $client) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-emerald-600 rounded-lg hover:bg-slate-100" title="Restaurer">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-users text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Aucun client trouvé selon vos critères</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
