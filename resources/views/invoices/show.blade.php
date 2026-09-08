@extends('layouts.app')

@section('title', 'Facture ' . $invoice->reference)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ paymentModal: false }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('invoices.index') }}" class="hover:text-sky-600">Facturation</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">{{ $invoice->reference }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Facture {{ $invoice->reference }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold 
                    {{ $invoice->status === 'payee' ? 'bg-emerald-100 text-emerald-800' : ($invoice->status === 'partiellement_payee' ? 'bg-amber-100 text-amber-800' : ($invoice->status === 'en_retard' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700')) }}">
                    {{ $invoice->status_label }}
                </span>
            </div>
        </div>

        <div class="flex items-center flex-wrap gap-2">
            @if(in_array(auth()->user()->role->slug, ['admin', 'gerant', 'comptable']) && $invoice->remaining_amount > 0 && $invoice->status !== 'annulee')
                <button type="button" @click="paymentModal = true" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-md shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Enregistrer Règlement</span>
                </button>
            @endif

            <a href="{{ route('invoices.pdf', $invoice) }}" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-md shadow-rose-500/20 transition-all flex items-center space-x-1.5">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Télécharger PDF</span>
            </a>

            <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Imprimer</span>
            </a>

            @if(in_array(auth()->user()->role->slug, ['admin', 'gerant', 'comptable']) && $invoice->status !== 'payee')
                <a href="{{ route('invoices.edit', $invoice) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-pen"></i>
                    <span>Modifier</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="bg-white p-8 sm:p-12 rounded-3xl border border-slate-200/80 shadow-sm space-y-8">
        
        <!-- Header : Cabinet & Invoice Meta -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 border-b border-slate-100 pb-8">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">FIDUCIAL<span class="text-sky-600">PRO</span></span>
                </div>
                <p class="text-xs text-slate-500">{{ config('cabinet.tagline') }}</p>
                <p class="text-xs text-slate-500">{{ config('cabinet.address') }}, {{ config('cabinet.city') }}</p>
                <p class="text-[11px] text-slate-400 mt-1 font-mono">ICE: {{ config('cabinet.ice') }} | IF: {{ config('cabinet.if') }} | RC: {{ config('cabinet.rc') }}</p>
            </div>

            <div class="sm:text-right">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">FACTURE D'HONORAIRES</span>
                <p class="text-2xl font-black font-mono text-sky-700 mt-1">{{ $invoice->reference }}</p>
                <div class="text-xs text-slate-500 space-y-0.5 mt-2">
                    <p>Date d'émission : <strong class="text-slate-800">{{ $invoice->invoice_date->format('d/m/Y') }}</strong></p>
                    <p>Date d'échéance : <strong class="text-slate-800">{{ $invoice->due_date->format('d/m/Y') }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Client Debiteur Box -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/70 p-6 rounded-2xl border border-slate-100">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Facturé à :</span>
                <h3 class="text-base font-bold text-slate-900 mt-1">{{ $invoice->client->company_name }}</h3>
                <p class="text-xs text-slate-600 mt-1">{{ $invoice->client->address ?? 'Siège social' }}</p>
                <p class="text-xs text-slate-600">{{ $invoice->client->city ?? 'Casablanca' }}, {{ $invoice->client->country ?? 'Maroc' }}</p>
                <p class="text-xs text-slate-500 mt-1">Tél : {{ $invoice->client->phone ?? 'N/A' }}</p>
            </div>

            <div class="sm:text-right space-y-1 text-xs">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Identifiants Client</span>
                <p><span class="text-slate-500">ICE :</span> <strong class="font-mono text-slate-800">{{ $invoice->client->ice ?? 'N/A' }}</strong></p>
                <p><span class="text-slate-500">Identifiant Fiscal :</span> <strong class="font-mono text-slate-800">{{ $invoice->client->if_number ?? 'N/A' }}</strong></p>
                <p><span class="text-slate-500">Registre du Commerce :</span> <strong class="font-mono text-slate-800">{{ $invoice->client->rc_number ?? 'N/A' }}</strong></p>
                <p><span class="text-slate-500">Forme juridique :</span> <strong class="text-slate-800">{{ $invoice->client->legal_form ?? 'SARL' }}</strong></p>
            </div>
        </div>

        @if($invoice->description)
            <div class="text-xs text-slate-600 bg-sky-50/50 p-3.5 rounded-xl border border-sky-100">
                <strong class="text-sky-900">Objet :</strong> {{ $invoice->description }}
            </div>
        @endif

        <!-- Table of items -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b-2 border-slate-200 text-slate-400 uppercase text-[10px] font-bold">
                        <th class="py-3 px-3">Désignation des Prestations</th>
                        <th class="py-3 px-3 text-right">Qté</th>
                        <th class="py-3 px-3 text-right">Prix Unit. HT</th>
                        <th class="py-3 px-3 text-right">Taux TVA</th>
                        <th class="py-3 px-3 text-right">Total HT</th>
                        <th class="py-3 px-3 text-right">Total TTC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="py-3.5 px-3 font-semibold text-slate-800">{{ $item->description }}</td>
                            <td class="py-3.5 px-3 text-right text-slate-600">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                            <td class="py-3.5 px-3 text-right text-slate-600">{{ number_format($item->unit_price, 2, ',', ' ') }} DH</td>
                            <td class="py-3.5 px-3 text-right text-slate-600">{{ number_format($item->tax_rate, 0) }}%</td>
                            <td class="py-3.5 px-3 text-right font-medium text-slate-800">{{ number_format($item->total_ht, 2, ',', ' ') }} DH</td>
                            <td class="py-3.5 px-3 text-right font-bold text-slate-900">{{ number_format($item->total_ttc, 2, ',', ' ') }} DH</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Breakdown -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6 pt-4 border-t border-slate-100">
            
            <!-- Left: Payment Conditions -->
            <div class="text-xs text-slate-500 max-w-sm space-y-1">
                <p><strong class="text-slate-700">Conditions :</strong> {{ $invoice->payment_conditions }}</p>
                @if($invoice->notes)
                    <p class="text-[11px] leading-relaxed mt-2 text-slate-400">{{ $invoice->notes }}</p>
                @endif
            </div>

            <!-- Right: Numbers summary -->
            <div class="w-full sm:w-72 bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Total Net Hors Taxes (HT) :</span>
                    <strong class="text-slate-800">{{ number_format($invoice->subtotal_ht, 2, ',', ' ') }} DH</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>TVA (Taxe Valeur Ajoutée) :</span>
                    <strong class="text-slate-800">{{ number_format($invoice->tax_amount, 2, ',', ' ') }} DH</strong>
                </div>
                <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total TTC :</span>
                    <span class="text-sky-700">{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</span>
                </div>
                <div class="flex justify-between text-xs text-emerald-700 font-semibold pt-1 border-t border-slate-100">
                    <span>Montant Encaissé :</span>
                    <span>{{ number_format($invoice->paid_amount, 2, ',', ' ') }} DH</span>
                </div>
                <div class="flex justify-between text-xs font-bold pt-1 border-t border-slate-100 {{ $invoice->remaining_amount > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                    <span>Solde Restant Dû :</span>
                    <span>{{ number_format($invoice->remaining_amount, 2, ',', ' ') }} DH</span>
                </div>
            </div>

        </div>

    </div>

    <!-- Payments Section -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Historique des Paiements Encaissés</h3>
                <p class="text-xs text-slate-400 mt-0.5">Règlements comptabilisés sur cette facture.</p>
            </div>
            @if($invoice->remaining_amount > 0 && $invoice->status !== 'annulee')
                <button type="button" @click="paymentModal = true" class="px-3.5 py-2 bg-sky-50 text-sky-700 hover:bg-sky-100 rounded-xl text-xs font-semibold transition-colors">
                    + Ajouter un paiement
                </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Date Règlement</th>
                        <th class="py-3 px-4">Mode de Paiement</th>
                        <th class="py-3 px-4">Référence / Chèque / Virement</th>
                        <th class="py-3 px-4">Banque</th>
                        <th class="py-3 px-4">Montant Reçu</th>
                        <th class="py-3 px-4 text-right">Reçu Officiel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoice->payments as $payment)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $payment->method_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">{{ $payment->reference ?? 'Espèces' }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $payment->bank ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-black text-emerald-600">{{ number_format($payment->amount, 2, ',', ' ') }} DH</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-receipt text-xs"></i>
                                    <span>Reçu</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">Aucun paiement enregistré pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Enregistrer un règlement -->
    <div x-show="paymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.outside="paymentModal = false" class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm">Enregistrer un Règlement Client</h3>
                <button @click="paymentModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-3 bg-amber-50 rounded-2xl border border-amber-100 text-xs text-amber-800 flex justify-between items-center">
                <span>Reste à payer sur la facture :</span>
                <strong class="text-sm font-black">{{ number_format($invoice->remaining_amount, 2, ',', ' ') }} DH</strong>
            </div>

            <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Montant encaissé (DH) *</label>
                    <input type="number" 
                           step="0.01" 
                           min="0.01" 
                           max="{{ $invoice->remaining_amount }}" 
                           name="amount" 
                           required 
                           value="{{ $invoice->remaining_amount }}" 
                           class="w-full py-2.5 px-3 bg-slate-50 font-bold text-slate-900 text-sm rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    <p class="text-[10px] text-slate-400 mt-1">Ne peut excéder le solde restant dû.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date du paiement *</label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mode de règlement *</label>
                    <select name="payment_method" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="virement">Virement bancaire</option>
                        <option value="cheque">Chèque bancaire</option>
                        <option value="especes">Espèces (Reçu de caisse)</option>
                        <option value="carte">Carte bancaire (TPE)</option>
                        <option value="autre">Autre moyen</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Banque</label>
                        <input type="text" name="bank" placeholder="Ex: BCP, Attijariwafa" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Référence / N° Chèque</label>
                        <input type="text" name="reference" placeholder="Ex: CHQ-994123" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Commentaires</label>
                    <textarea name="comments" rows="2" placeholder="Observations, libellé du virement..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="paymentModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Annuler</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-md shadow-sky-500/20">Valider l'encaissement</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
