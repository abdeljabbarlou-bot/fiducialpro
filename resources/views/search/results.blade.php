@extends('layouts.app')

@section('title', 'Résultats de recherche pour "' . $query . '"')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
            <span>Recherche globale</span>
            <span>&bull;</span>
            <span class="text-slate-600 font-medium">« {{ $query }} »</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Résultats de recherche</h1>
        <p class="text-xs sm:text-sm text-slate-500">
            {{ $clients->count() + $dossiers->count() + $invoices->count() + $declarations->count() }} résultat(s) correspondant(s) trouvé(s).
        </p>
    </div>

    <!-- Search input repeat -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm max-w-xl">
        <form action="{{ route('search') }}" method="GET" class="flex items-center space-x-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" 
                       name="q" 
                       value="{{ $query }}" 
                       placeholder="Rechercher à nouveau..." 
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
            </div>
            <button type="submit" class="py-2 px-4 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-xl transition-colors">
                Chercher
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Clients matches -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center justify-between border-b border-slate-100 pb-3">
                <span class="flex items-center"><i class="fa-solid fa-building text-sky-600 mr-2"></i> Clients Entreprises</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs">{{ $clients->count() }}</span>
            </h2>

            <div class="divide-y divide-slate-100">
                @forelse($clients as $c)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <a href="{{ route('clients.show', $c) }}" class="font-bold text-slate-900 hover:text-sky-600 text-xs block">
                                {{ $c->company_name }}
                            </a>
                            <span class="text-[11px] text-slate-400 font-mono">ICE: {{ $c->ice ?? 'N/A' }} &bull; {{ $c->city ?? 'Casablanca' }}</span>
                        </div>
                        <a href="{{ route('clients.show', $c) }}" class="text-xs text-sky-600 font-semibold hover:underline">
                            Consulter &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Aucun client correspondant</p>
                @endforelse
            </div>
        </div>

        <!-- Dossiers matches -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center justify-between border-b border-slate-100 pb-3">
                <span class="flex items-center"><i class="fa-solid fa-folder-open text-indigo-600 mr-2"></i> Dossiers & Missions</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs">{{ $dossiers->count() }}</span>
            </h2>

            <div class="divide-y divide-slate-100">
                @forelse($dossiers as $dos)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <a href="{{ route('dossiers.show', $dos) }}" class="font-mono font-bold text-sky-700 hover:underline text-xs block">
                                {{ $dos->reference }} &bull; {{ $dos->type_label }}
                            </a>
                            <span class="text-[11px] text-slate-500">{{ $dos->client->company_name }}</span>
                        </div>
                        <a href="{{ route('dossiers.show', $dos) }}" class="text-xs text-sky-600 font-semibold hover:underline">
                            Consulter &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Aucun dossier correspondant</p>
                @endforelse
            </div>
        </div>

        <!-- Invoices matches -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center justify-between border-b border-slate-100 pb-3">
                <span class="flex items-center"><i class="fa-solid fa-file-invoice text-emerald-600 mr-2"></i> Factures d'Honoraires</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs">{{ $invoices->count() }}</span>
            </h2>

            <div class="divide-y divide-slate-100">
                @forelse($invoices as $inv)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <a href="{{ route('invoices.show', $inv) }}" class="font-mono font-bold text-sky-700 hover:underline text-xs block">
                                {{ $inv->reference }} &bull; {{ number_format($inv->total_ttc, 2, ',', ' ') }} DH
                            </a>
                            <span class="text-[11px] text-slate-500">{{ $inv->client->company_name }} &bull; {{ $inv->status_label }}</span>
                        </div>
                        <a href="{{ route('invoices.show', $inv) }}" class="text-xs text-sky-600 font-semibold hover:underline">
                            Consulter &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Aucune facture correspondante</p>
                @endforelse
            </div>
        </div>

        <!-- Declarations matches -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-slate-900 flex items-center justify-between border-b border-slate-100 pb-3">
                <span class="flex items-center"><i class="fa-solid fa-file-invoice-dollar text-amber-600 mr-2"></i> Déclarations Fiscales</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs">{{ $declarations->count() }}</span>
            </h2>

            <div class="divide-y divide-slate-100">
                @forelse($declarations as $decl)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <a href="{{ route('declarations.show', $decl) }}" class="font-bold text-slate-900 hover:text-sky-600 text-xs block">
                                {{ $decl->type->code }} &bull; {{ $decl->period }}
                            </a>
                            <span class="text-[11px] text-slate-500">{{ $decl->client->company_name }} &bull; Échéance: {{ $decl->due_date->format('d/m/Y') }}</span>
                        </div>
                        <a href="{{ route('declarations.show', $decl) }}" class="text-xs text-sky-600 font-semibold hover:underline">
                            Consulter &rarr;
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Aucune déclaration correspondante</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
