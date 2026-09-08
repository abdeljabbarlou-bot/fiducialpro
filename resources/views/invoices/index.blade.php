@extends('layouts.app')

@section('title', 'Facturation des Honoraires')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Factures d'Honoraires</h1>
            <p class="text-xs sm:text-sm text-slate-500">Émission, suivi des règlements et facturation des prestations du cabinet.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('payments.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors flex items-center space-x-2">
                <i class="fa-solid fa-hand-holding-dollar text-slate-400"></i>
                <span>Journal des Règlements</span>
            </a>
            @if(in_array(auth()->user()->role->slug, ['admin', 'gerant', 'comptable']))
            <a href="{{ route('invoices.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-plus"></i>
                <span>Émettre une Facture</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('invoices.index') }}" class="p-4 rounded-2xl bg-white border {{ !request('filter') && !request('status') ? 'border-sky-500 ring-2 ring-sky-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-slate-400 font-semibold uppercase">Total Facturé</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ number_format(\App\Models\Invoice::where('status', '!=', 'annulee')->sum('total_ttc'), 2, ',', ' ') }} <span class="text-xs font-normal">DH</span></p>
        </a>
        <a href="{{ route('invoices.index', ['filter' => 'unpaid']) }}" class="p-4 rounded-2xl bg-white border {{ request('filter') === 'unpaid' ? 'border-amber-500 ring-2 ring-amber-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-amber-600 font-semibold uppercase">Reste à Encaisser</span>
            <p class="text-xl font-black text-amber-600 mt-1">{{ number_format(\App\Models\Invoice::whereNotIn('status', ['payee', 'annulee'])->sum('remaining_amount'), 2, ',', ' ') }} <span class="text-xs font-normal">DH</span></p>
        </a>
        <a href="{{ route('invoices.index', ['filter' => 'overdue']) }}" class="p-4 rounded-2xl bg-white border {{ request('filter') === 'overdue' ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-rose-600 font-semibold uppercase">Factures en Retard</span>
            <p class="text-xl font-black text-rose-600 mt-1">{{ \App\Models\Invoice::where('status', 'en_retard')->count() }}</p>
        </a>
        <a href="{{ route('invoices.index', ['status' => 'payee']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'payee' ? 'border-emerald-500 ring-2 ring-emerald-500/10' : 'border-slate-200/80 hover:border-slate-300' }} transition-all">
            <span class="text-xs text-emerald-600 font-semibold uppercase">Factures Réglées</span>
            <p class="text-xl font-black text-slate-800 mt-1">{{ \App\Models\Invoice::where('status', 'payee')->count() }}</p>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('invoices.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Numéro de facture (ex: FAC-2026-0001), client..." 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>

            <div>
                <select name="status" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="brouillon" {{ request('status') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="emise" {{ request('status') === 'emise' ? 'selected' : '' }}>Émise (Non réglée)</option>
                    <option value="partiellement_payee" {{ request('status') === 'partiellement_payee' ? 'selected' : '' }}>Partiellement payée</option>
                    <option value="payee" {{ request('status') === 'payee' ? 'selected' : '' }}>Payée intégralement</option>
                    <option value="en_retard" {{ request('status') === 'en_retard' ? 'selected' : '' }}>En retard</option>
                    <option value="annulee" {{ request('status') === 'annulee' ? 'selected' : '' }}>Annulée</option>
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
                <a href="{{ route('invoices.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Réinitialiser">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Réf Facture</th>
                        <th class="py-3.5 px-5">Client</th>
                        <th class="py-3.5 px-5">Émission</th>
                        <th class="py-3.5 px-5">Échéance</th>
                        <th class="py-3.5 px-5">Total TTC</th>
                        <th class="py-3.5 px-5">Encaissé</th>
                        <th class="py-3.5 px-5">Reste Dû</th>
                        <th class="py-3.5 px-5">Statut</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <a href="{{ route('invoices.show', $inv) }}" class="font-mono font-bold text-sky-600 hover:underline">
                                    {{ $inv->reference }}
                                </a>
                            </td>

                            <td class="py-4 px-5">
                                <a href="{{ route('clients.show', $inv->client_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block">
                                    {{ $inv->client->company_name }}
                                </a>
                                <span class="text-[11px] text-slate-400">ICE: {{ $inv->client->ice ?? 'N/A' }}</span>
                            </td>

                            <td class="py-4 px-5 text-slate-600">{{ $inv->invoice_date->format('d/m/Y') }}</td>
                            
                            <td class="py-4 px-5">
                                <span class="{{ $inv->status === 'en_retard' ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                    {{ $inv->due_date->format('d/m/Y') }}
                                </span>
                            </td>

                            <td class="py-4 px-5 font-black text-slate-900">
                                {{ number_format($inv->total_ttc, 2, ',', ' ') }} DH
                            </td>

                            <td class="py-4 px-5 font-semibold text-emerald-600">
                                {{ number_format($inv->paid_amount, 2, ',', ' ') }} DH
                            </td>

                            <td class="py-4 px-5 font-bold {{ $inv->remaining_amount > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                {{ number_format($inv->remaining_amount, 2, ',', ' ') }} DH
                            </td>

                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold 
                                    {{ $inv->status === 'payee' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'partiellement_payee' ? 'bg-amber-100 text-amber-800' : ($inv->status === 'en_retard' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ $inv->status_label }}
                                </span>
                            </td>

                            <td class="py-4 px-5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('invoices.pdf', $inv) }}" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 inline-block" title="Télécharger PDF">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </a>
                                <a href="{{ route('invoices.print', $inv) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 inline-block" title="Imprimer">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <a href="{{ route('invoices.show', $inv) }}" class="p-1.5 text-slate-400 hover:text-sky-600 rounded-lg hover:bg-slate-100 inline-block" title="Consulter">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Aucune facture enregistrée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
