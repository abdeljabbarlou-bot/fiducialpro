@extends('layouts.app')

@section('title', 'Journal des Règlements')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Journal des Paiements & Règlements</h1>
            <p class="text-xs sm:text-sm text-slate-500">Traçabilité des encaissements par virement bancaire, chèque et espèces.</p>
        </div>
        @if(in_array(auth()->user()->role->slug, ['admin', 'gerant', 'comptable']))
        <a href="{{ route('payments.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center justify-center space-x-2">
            <i class="fa-solid fa-plus"></i>
            <span>Enregistrer un Règlement</span>
        </a>
        @endif
    </div>

    <!-- Stats Banner -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold uppercase">Total Encaissé</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($totalCollected, 2, ',', ' ') }} <span class="text-xs font-normal text-slate-400">DH</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold uppercase">Nombre d'Encaissements</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ \App\Models\Payment::count() }} <span class="text-xs font-normal text-slate-400">opérations</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold uppercase">Règlements par Virement</span>
            <p class="text-2xl font-black text-sky-600 mt-1">{{ \App\Models\Payment::where('payment_method', 'virement')->count() }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('payments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <div class="lg:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Réf virement, chèque, banque, client..." 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>

            <div>
                <select name="payment_method" class="w-full py-2 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <option value="">Tous les modes</option>
                    <option value="virement" {{ request('payment_method') === 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                    <option value="cheque" {{ request('payment_method') === 'cheque' ? 'selected' : '' }}>Chèque</option>
                    <option value="especes" {{ request('payment_method') === 'especes' ? 'selected' : '' }}>Espèces</option>
                    <option value="carte" {{ request('payment_method') === 'carte' ? 'selected' : '' }}>Carte bancaire</option>
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
                <a href="{{ route('payments.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" title="Réinitialiser">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Date</th>
                        <th class="py-3.5 px-5">Facture Liée</th>
                        <th class="py-3.5 px-5">Client</th>
                        <th class="py-3.5 px-5">Mode de Paiement</th>
                        <th class="py-3.5 px-5">Référence / Banque</th>
                        <th class="py-3.5 px-5">Montant Encaissé</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5 font-semibold text-slate-800">
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>

                            <td class="py-4 px-5">
                                <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="font-mono font-bold text-sky-600 hover:underline">
                                    {{ $payment->invoice->reference }}
                                </a>
                            </td>

                            <td class="py-4 px-5">
                                <a href="{{ route('clients.show', $payment->client_id) }}" class="font-bold text-slate-900 hover:text-sky-600 block">
                                    {{ $payment->client->company_name }}
                                </a>
                            </td>

                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $payment->method_label }}
                                </span>
                            </td>

                            <td class="py-4 px-5 text-slate-600">
                                <span class="font-mono">{{ $payment->reference ?? '-' }}</span>
                                @if($payment->bank)
                                    <span class="block text-[10px] text-slate-400">{{ $payment->bank }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 font-black text-emerald-600 text-sm">
                                {{ number_format($payment->amount, 2, ',', ' ') }} DH
                            </td>

                            <td class="py-4 px-5 text-right space-x-2">
                                <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-receipt text-xs"></i>
                                    <span>Reçu</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-hand-holding-dollar text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Aucun paiement enregistré</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
