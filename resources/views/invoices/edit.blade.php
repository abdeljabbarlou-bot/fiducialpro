@extends('layouts.app')

@section('title', 'Modifier ' . $invoice->reference)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="invoiceEditForm()">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('invoices.index') }}" class="hover:text-sky-600">Facturation</a>
                <span>&bull;</span>
                <a href="{{ route('invoices.show', $invoice) }}" class="hover:text-sky-600">{{ $invoice->reference }}</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Modification</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier la Facture {{ $invoice->reference }}</h1>
        </div>
        <a href="{{ route('invoices.show', $invoice) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Card 1 : Entête & Client -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-file-invoice text-sky-600 mr-2"></i> Informations de Facturation
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Client -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Client à facturer *</label>
                    <select name="client_id" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (old('client_id', $invoice->client_id) == $c->id) ? 'selected' : '' }}>
                                {{ $c->company_name }} (ICE: {{ $c->ice ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date facture -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'émission *</label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Date échéance -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'échéance *</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="brouillon" {{ old('status', $invoice->status) === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="emise" {{ old('status', $invoice->status) === 'emise' ? 'selected' : '' }}>Émise</option>
                        <option value="annulee" {{ old('status', $invoice->status) === 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                <!-- Conditions -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Conditions de règlement</label>
                    <input type="text" name="payment_conditions" value="{{ old('payment_conditions', $invoice->payment_conditions) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Objet / Description générale</label>
                    <input type="text" name="description" value="{{ old('description', $invoice->description) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

            </div>
        </div>

        <!-- Card 2 : Lignes de Facture Dynamiques -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center">
                        <i class="fa-solid fa-list-check text-emerald-600 mr-2"></i> Lignes de Prestations
                    </h2>
                </div>
                <button type="button" @click="addItem()" class="px-3 py-1.5 bg-sky-50 text-sky-700 hover:bg-sky-100 rounded-xl text-xs font-semibold transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Ajouter une ligne</span>
                </button>
            </div>

            <!-- Table of Items -->
            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 grid grid-cols-12 gap-3 items-center">
                        
                        <!-- Description -->
                        <div class="col-span-12 sm:col-span-5">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Désignation *</label>
                            <input type="text" 
                                   :name="'items[' + index + '][description]'" 
                                   x-model="item.description" 
                                   required 
                                   class="w-full py-2 px-3 bg-white text-xs rounded-xl border border-slate-200 focus:border-sky-500 outline-none">
                        </div>

                        <!-- Quantité -->
                        <div class="col-span-4 sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Qté *</label>
                            <input type="number" 
                                   step="0.01" 
                                   min="0.01" 
                                   :name="'items[' + index + '][quantity]'" 
                                   x-model.number="item.quantity" 
                                   required 
                                   class="w-full py-2 px-3 bg-white text-xs rounded-xl border border-slate-200 focus:border-sky-500 outline-none text-right">
                        </div>

                        <!-- Prix Unitaire HT -->
                        <div class="col-span-4 sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Prix U. HT *</label>
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   :name="'items[' + index + '][unit_price]'" 
                                   x-model.number="item.unit_price" 
                                   required 
                                   class="w-full py-2 px-3 bg-white text-xs rounded-xl border border-slate-200 focus:border-sky-500 outline-none text-right">
                        </div>

                        <!-- Taux TVA -->
                        <div class="col-span-3 sm:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">TVA (%)</label>
                            <select :name="'items[' + index + '][tax_rate]'" 
                                    x-model.number="item.tax_rate" 
                                    class="w-full py-2 px-2 bg-white text-xs rounded-xl border border-slate-200 focus:border-sky-500 outline-none">
                                <option value="20">20%</option>
                                <option value="14">14%</option>
                                <option value="10">10%</option>
                                <option value="7">7%</option>
                                <option value="0">0%</option>
                            </select>
                        </div>

                        <!-- Remove Button -->
                        <div class="col-span-1 flex justify-center pt-4 sm:pt-4">
                            <button type="button" 
                                    @click="removeItem(index)" 
                                    :disabled="items.length === 1"
                                    class="text-slate-400 hover:text-rose-600 disabled:opacity-30 p-1">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Calculated Totals Box -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-80 bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Total Hors Taxes (HT) :</span>
                        <strong class="font-bold text-slate-800" x-text="formatMoney(calculateSubtotal()) + ' DH'"></strong>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Montant TVA :</span>
                        <strong class="font-bold text-slate-800" x-text="formatMoney(calculateTax()) + ' DH'"></strong>
                    </div>
                    <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>Total TTC :</span>
                        <span class="text-sky-700" x-text="formatMoney(calculateTotal()) + ' DH'"></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Card 3 : Notes -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center">
                <i class="fa-solid fa-building-columns text-slate-600 mr-2"></i> Mentions & Coordonnées Bancaires
            </h2>
            <textarea name="notes" rows="2" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('notes', $invoice->notes) }}</textarea>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('invoices.show', $invoice) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Mettre à jour la facture
            </button>
        </div>

    </form>

</div>

<script>
function invoiceEditForm() {
    return {
        items: {!! json_encode($invoice->items->map(function($i) {
            return [
                'description' => $i->description,
                'quantity' => (float)$i->quantity,
                'unit_price' => (float)$i->unit_price,
                'tax_rate' => (float)$i->tax_rate,
            ];
        })) !!},
        addItem() {
            this.items.push({ description: '', quantity: 1, unit_price: 0, tax_rate: 20 });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        calculateSubtotal() {
            return this.items.reduce((sum, item) => {
                const q = parseFloat(item.quantity) || 0;
                const p = parseFloat(item.unit_price) || 0;
                return sum + (q * p);
            }, 0);
        },
        calculateTax() {
            return this.items.reduce((sum, item) => {
                const q = parseFloat(item.quantity) || 0;
                const p = parseFloat(item.unit_price) || 0;
                const r = parseFloat(item.tax_rate) || 0;
                return sum + (q * p * (r / 100));
            }, 0);
        },
        calculateTotal() {
            return this.calculateSubtotal() + this.calculateTax();
        },
        formatMoney(val) {
            return (Math.round(val * 100) / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    };
}
</script>
@endsection
