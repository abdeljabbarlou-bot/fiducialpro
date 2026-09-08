@extends('layouts.app')

@section('title', 'Déclarations Fiscales & Sociales')

@section('content')
<div class="space-y-6" x-data="{ filingModal: false, selectedDecl: null }">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Déclarations Fiscales & Sociales</h1>
            <p class="text-xs sm:text-sm text-slate-500">Suivi des obligations fiscales : TVA, IS, IR, CNSS et télédéclarations SIMPL.</p>
        </div>
        @can('create', App\Models\Declaration::class)
            <a href="{{ route('declarations.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Nouvelle Déclaration</span>
            </a>
        @endcan
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('declarations.index') }}" class="p-4 rounded-2xl bg-white border {{ !request('filter') && !request('status') ? 'border-sky-500 ring-2 ring-sky-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-slate-400 font-semibold uppercase">Total Déclarations</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Declaration::count() }}</p>
        </a>
        <a href="{{ route('declarations.index', ['filter' => 'overdue']) }}" class="p-4 rounded-2xl bg-white border {{ request('filter') === 'overdue' ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-rose-600 font-semibold uppercase">En Retard</span>
            <p class="text-xl font-black text-rose-600 mt-1">{{ \App\Models\Declaration::where('status', 'en_retard')->count() }}</p>
        </a>
        <a href="{{ route('declarations.index', ['filter' => 'upcoming']) }}" class="p-4 rounded-2xl bg-white border {{ request('filter') === 'upcoming' ? 'border-amber-500 ring-2 ring-amber-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-amber-600 font-semibold uppercase">Échéance Proche (15j)</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Declaration::upcoming(15)->count() }}</p>
        </a>
        <a href="{{ route('declarations.index', ['status' => 'deposee']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'deposee' ? 'border-emerald-500 ring-2 ring-emerald-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-emerald-600 font-semibold uppercase">Déposées</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Declaration::where('status', 'deposee')->count() }}</p>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('declarations.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            
            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Client, période, réf SIMPL..." 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>

            <div>
                <select name="type_id" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les types d'impôt</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}" {{ request('type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="a_preparer" {{ request('status') === 'a_preparer' ? 'selected' : '' }}>À préparer</option>
                    <option value="en_preparation" {{ request('status') === 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                    <option value="prete" {{ request('status') === 'prete' ? 'selected' : '' }}>Prête</option>
                    <option value="deposee" {{ request('status') === 'deposee' ? 'selected' : '' }}>Déposée</option>
                    <option value="payee" {{ request('status') === 'payee' ? 'selected' : '' }}>Payée</option>
                    <option value="en_retard" {{ request('status') === 'en_retard' ? 'selected' : '' }}>En retard</option>
                </select>
            </div>

            <div>
                <select name="client_id" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
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
                <a href="{{ route('declarations.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Réinitialiser">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Declarations Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Client & Entreprise</th>
                        <th class="py-3.5 px-5">Obligation Fiscale</th>
                        <th class="py-3.5 px-5">Période</th>
                        <th class="py-3.5 px-5">Date d'Échéance</th>
                        <th class="py-3.5 px-5">Montant DH</th>
                        <th class="py-3.5 px-5">Réf Dépôt / SIMPL</th>
                        <th class="py-3.5 px-5">Statut</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($declarations as $decl)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <a href="{{ route('clients.show', $decl->client_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block">
                                    {{ $decl->client->company_name }}
                                </a>
                                <span class="text-[11px] text-slate-400">ICE : {{ $decl->client->ice ?? 'N/A' }}</span>
                            </td>

                            <td class="py-4 px-5">
                                <span class="font-bold text-slate-800 px-2 py-0.5 rounded bg-slate-100">{{ $decl->type->code }}</span>
                                <p class="text-[11px] text-slate-500 mt-1">{{ $decl->type->name }}</p>
                            </td>

                            <td class="py-4 px-5 font-semibold text-slate-700">{{ $decl->period }}</td>

                            <td class="py-4 px-5">
                                <span class="{{ $decl->status === 'en_retard' ? 'text-rose-600 font-bold' : 'text-slate-800' }}">
                                    {{ $decl->due_date->format('d/m/Y') }}
                                </span>
                                @if($decl->status === 'en_retard')
                                    <span class="block text-[10px] text-rose-500 font-semibold">Expirée depuis {{ $decl->due_date->diffForHumans() }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 font-bold text-slate-900">
                                {{ number_format($decl->amount, 2, ',', ' ') }} DH
                            </td>

                            <td class="py-4 px-5">
                                @if($decl->filing_reference)
                                    <span class="font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded text-slate-700 font-semibold">{{ $decl->filing_reference }}</span>
                                    <span class="block text-[10px] text-slate-400 mt-0.5">{{ $decl->filing_date ? $decl->filing_date->format('d/m/Y') : '' }}</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">En attente</span>
                                @endif
                            </td>

                            <td class="py-4 px-5">
                                @if($decl->status === 'deposee' || $decl->status === 'payee')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-check mr-1"></i> Déposée
                                    </span>
                                @elseif($decl->status === 'en_retard')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 animate-pulse">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> En retard
                                    </span>
                                @elseif($decl->status === 'prete')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800">Prête</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">{{ $decl->status_label }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-right space-x-1 whitespace-nowrap">
                                @if($decl->status !== 'deposee' && $decl->status !== 'payee')
                                    @can('file', $decl)
                                        <button type="button"
                                                @click="filingModal = true; selectedDecl = { id: {{ $decl->id }}, period: '{{ $decl->period }}', type: '{{ $decl->type->code }}', client: '{{ addslashes($decl->client->company_name) }}' }"
                                                class="px-2 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold transition-colors">
                                            <i class="fa-solid fa-paper-plane mr-1"></i> Dépôt
                                        </button>
                                    @endcan
                                @endif
                                <a href="{{ route('declarations.show', $decl) }}" class="p-1.5 text-slate-400 hover:text-sky-600 rounded-lg hover:bg-slate-100 inline-block" title="Détail">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                                @can('update', $decl)
                                    <a href="{{ route('declarations.edit', $decl) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100 inline-block" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-file-invoice-dollar text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Aucune déclaration trouvée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($declarations->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $declarations->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Enregistrer le dépôt -->
    <div x-show="filingModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="filingModal = false" class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm">Enregistrer le dépôt de télédéclaration</h3>
                <button @click="filingModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <p class="text-xs text-slate-500">
                Validation du dépôt pour <strong x-text="selectedDecl?.client"></strong> (<span x-text="selectedDecl?.type + ' ' + selectedDecl?.period"></span>).
            </p>

            <form :action="'/declarations/' + selectedDecl?.id + '/mark-filed'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date effective de dépôt *</label>
                    <input type="date" name="filing_date" required value="{{ date('Y-m-d') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Référence du reçu SIMPL / DGI / DAMANCOM</label>
                    <input type="text" name="filing_reference" placeholder="Ex: SIMPL-TVA-2026-89412" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Commentaires de validation</label>
                    <textarea name="comments" rows="2" placeholder="Observations, date du virement bancaire lié..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="filingModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Annuler</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-500/20">Valider comme déposée</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
