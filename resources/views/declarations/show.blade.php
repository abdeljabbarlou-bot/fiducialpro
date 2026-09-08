@extends('layouts.app')

@section('title', 'Déclaration ' . $declaration->type->code . ' - ' . $declaration->period)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('declarations.index') }}" class="hover:text-sky-600">Déclarations</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">{{ $declaration->type->code }} ({{ $declaration->period }})</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $declaration->type->name }}</h1>
                @if($declaration->status === 'deposee' || $declaration->status === 'payee')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Déposée</span>
                @elseif($declaration->status === 'en_retard')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 animate-pulse">En retard</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">{{ $declaration->status_label }}</span>
                @endif
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @can('update', $declaration)
                <a href="{{ route('declarations.edit', $declaration) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-pen"></i>
                    <span>Modifier</span>
                </a>
            @endcan
            <a href="{{ route('declarations.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Retour
            </a>
        </div>
    </div>

    <!-- Alert banner if overdue -->
    @if($declaration->status === 'en_retard')
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg"></i>
                <div>
                    <h4 class="font-bold text-xs">Échéance légale dépassée !</h4>
                    <p class="text-xs text-rose-600">Cette déclaration devait être déposée le {{ $declaration->due_date->format('d/m/Y') }}. Pénalités et majorations fiscales applicables selon le CGI.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Details Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 text-xs">
            
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Client / Entreprise</span>
                <p class="font-bold text-slate-900 text-sm mt-0.5">
                    <a href="{{ route('clients.show', $declaration->client_id) }}" class="hover:text-sky-600">
                        {{ $declaration->client->company_name }}
                    </a>
                </p>
                <span class="text-[11px] text-slate-400 font-mono">ICE : {{ $declaration->client->ice ?? 'N/A' }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Période Fiscale</span>
                <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $declaration->period }}</p>
                <span class="text-[11px] text-slate-400">Périodicité : {{ ucfirst($declaration->type->periodicity) }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Montant de l'obligation</span>
                <p class="font-black text-slate-900 text-base mt-0.5 text-sky-700">
                    {{ number_format($declaration->amount, 2, ',', ' ') }} <span class="text-xs font-normal">DH</span>
                </p>
            </div>

            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Date limite d'échéance</span>
                <p class="font-bold text-slate-800 text-sm mt-0.5 {{ $declaration->status === 'en_retard' ? 'text-rose-600' : '' }}">
                    {{ $declaration->due_date->format('d/m/Y') }}
                </p>
                <span class="text-[11px] text-slate-400">{{ $declaration->due_date->diffForHumans() }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Date effective de dépôt</span>
                <p class="font-bold text-slate-800 text-sm mt-0.5">
                    {{ $declaration->filing_date ? $declaration->filing_date->format('d/m/Y') : 'Non encore déposée' }}
                </p>
            </div>

            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Référence Télé-dépôt SIMPL</span>
                <p class="font-mono font-bold text-slate-800 text-sm mt-0.5">
                    {{ $declaration->filing_reference ?? 'En attente' }}
                </p>
            </div>

            @if($declaration->dossier)
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Dossier Associé</span>
                <p class="font-bold text-slate-800 mt-0.5">
                    <a href="{{ route('dossiers.show', $declaration->dossier_id) }}" class="text-sky-600 hover:underline">
                        {{ $declaration->dossier->reference }} ({{ $declaration->dossier->type_label }})
                    </a>
                </p>
            </div>
            @endif

            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Collaborateur en charge</span>
                <p class="font-bold text-slate-800 mt-0.5">
                    {{ $declaration->responsible?->full_name ?? 'Direction / Cabinet' }}
                </p>
            </div>

        </div>

        @if($declaration->comments)
            <div class="pt-4 border-t border-slate-100 text-xs">
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Commentaires & Observations</span>
                <p class="text-slate-700 mt-1 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    {{ $declaration->comments }}
                </p>
            </div>
        @endif

    </div>

    <!-- Fast Action Form: Mark as filed (if not already filed) -->
    @if($declaration->status !== 'deposee' && $declaration->status !== 'payee')
        <div class="bg-gradient-to-tr from-emerald-50 to-teal-50 p-6 sm:p-8 rounded-3xl border border-emerald-200/80 shadow-sm space-y-4">
            <div>
                <h3 class="font-bold text-emerald-950 text-sm flex items-center">
                    <i class="fa-solid fa-cloud-arrow-up text-emerald-600 mr-2"></i> Valider le Dépôt Télétransmis (SIMPL)
                </h3>
                <p class="text-xs text-emerald-700 mt-0.5">Enregistrez la date et la référence officielle de dépôt de la déclaration.</p>
            </div>

            <form action="{{ route('declarations.mark-filed', $declaration) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-emerald-900 mb-1">Date de dépôt effective *</label>
                    <input type="date" name="filing_date" required value="{{ date('Y-m-d') }}" class="w-full py-2 px-3 bg-white text-xs rounded-xl border border-emerald-200 focus:border-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-emerald-900 mb-1">Référence SIMPL / DGI *</label>
                    <input type="text" name="filing_reference" required placeholder="Ex: SIMPL-TVA-2026-..." class="w-full py-2 px-3 bg-white font-mono text-xs rounded-xl border border-emerald-200 focus:border-emerald-500 outline-none">
                </div>

                <div>
                    <button type="submit" class="w-full py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-500/20 transition-all">
                        <i class="fa-solid fa-check mr-1"></i> Marquer Déposée
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
