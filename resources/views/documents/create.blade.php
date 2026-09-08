@extends('layouts.app')

@section('title', 'Archiver un Document')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('documents.index') }}" class="hover:text-sky-600">Documents GED</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Nouveau dépôt</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Archiver un Document Numérique</h1>
        </div>
        <a href="{{ route('documents.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            
            <!-- File upload input -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Fichier à déposer * (Max: 10 Mo)</label>
                <div class="p-6 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50/50 hover:bg-slate-50 transition-colors text-center cursor-pointer relative">
                    <input type="file" name="file" required accept=".pdf,.docx,.xlsx,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-indigo-500 mb-2"></i>
                    <p class="text-xs font-semibold text-slate-800">Glissez-déposez votre fichier ici ou cliquez pour parcourir</p>
                    <p class="text-[11px] text-slate-400 mt-1">Formats acceptés : PDF, DOCX, XLSX, JPG, JPEG, PNG (Taille max : 10 Mo)</p>
                </div>
            </div>

            <!-- Titre -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Titre / Libellé du document *</label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="Ex: Statuts signés, Modèle J RC 2026..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-500 outline-none">
            </div>

            <!-- Catégorie -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catégorie du document *</label>
                <select name="category" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="statuts">Statuts constitutifs / modificatifs</option>
                    <option value="rc">Modèle J / Registre de Commerce (RC)</option>
                    <option value="cin">Copie CIN des associés / gérants</option>
                    <option value="pv">Procès-Verbal d'Assemblée Générale (PV)</option>
                    <option value="bilan">Bilan comptable annuel / Liasse fiscale</option>
                    <option value="declaration_fiscale">Déclaration fiscale & Reçu de télépaiement</option>
                    <option value="contrat">Contrat commercial / Bail commercial</option>
                    <option value="releve_bancaire">Relevé de compte bancaire</option>
                    <option value="facture">Facture fournisseur / justificatif d'achat</option>
                    <option value="attestation">Attestation administrative ou fiscale</option>
                    <option value="document_comptable">Pièce comptable diverse</option>
                    <option value="autre">Autre document</option>
                </select>
            </div>

            <!-- Rattachement Client ou Dossier -->
            <div class="p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 space-y-3">
                <span class="text-xs font-bold text-indigo-950 flex items-center">
                    <i class="fa-solid fa-link text-indigo-600 mr-2"></i> Rattachement du document
                </span>
                <p class="text-[11px] text-indigo-700">Le document doit être associé à une entreprise cliente ou à une mission précise.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Client associé</label>
                        <select name="client_id" class="w-full py-2 px-2.5 bg-white text-xs rounded-xl border border-slate-200 focus:border-indigo-500 outline-none">
                            <option value="">Sélectionner...</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ (old('client_id', $selectedClientId) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Dossier associé</label>
                        <select name="dossier_id" class="w-full py-2 px-2.5 bg-white text-xs rounded-xl border border-slate-200 focus:border-indigo-500 outline-none">
                            <option value="">Sélectionner...</option>
                            @foreach($dossiers as $dos)
                                <option value="{{ $dos->id }}" {{ (old('dossier_id', $selectedDossierId) == $dos->id) ? 'selected' : '' }}>
                                    {{ $dos->reference }} ({{ $dos->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Notes et observations</label>
                <textarea name="notes" rows="2" placeholder="Informations complémentaires sur le document..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-indigo-500 outline-none">{{ old('notes') }}</textarea>
            </div>

        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('documents.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-lg shadow-indigo-500/25 transition-all">
                Archiver dans la GED
            </button>
        </div>

    </form>

</div>
@endsection
