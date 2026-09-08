@extends('layouts.app')

@section('title', 'Rapports & Exports d\'Activité')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rapports & Synthèses d'Activité</h1>
        <p class="text-xs sm:text-sm text-slate-500">Génération et export de rapports financiers, fiscaux et opérationnels en formats PDF et Excel.</p>
    </div>

    <!-- 4 Report Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Rapport 1: Financier & Facturation -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Rapport Financier & Facturation</h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Chiffre d'affaires HT et TTC facturé, montants encaissés, créances impayées et balance âgée des clients.
                    </p>
                </div>
            </div>

            <form action="{{ route('reports.generate') }}" method="POST" class="space-y-3 pt-4 border-t border-slate-100">
                @csrf
                <input type="hidden" name="report_type" value="financial">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Du</label>
                        <input type="date" name="date_start" value="{{ date('Y-01-01') }}" class="w-full py-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Au</label>
                        <input type="date" name="date_end" value="{{ date('Y-m-d') }}" class="w-full py-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs outline-none">
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <button type="submit" name="format" value="pdf" class="flex-1 py-2.5 px-3 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-colors flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Export PDF</span>
                    </button>
                    <button type="submit" name="format" value="excel" class="flex-1 py-2.5 px-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-semibold text-xs rounded-xl transition-colors flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Export Excel</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Rapport 2: Portefeuille Clients -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Rapport Portefeuille Clients</h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Répertoire des entreprises clientes, formes juridiques, identifiants légaux (ICE, IF, RC, CNSS) et état d'activité.
                    </p>
                </div>
            </div>

            <form action="{{ route('reports.generate') }}" method="POST" class="space-y-3 pt-4 border-t border-slate-100">
                @csrf
                <input type="hidden" name="report_type" value="clients">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Statut</label>
                        <select name="status" class="w-full py-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs outline-none">
                            <option value="">Tous les statuts</option>
                            <option value="actif">Actif</option>
                            <option value="prospect">Prospect</option>
                            <option value="suspendu">Suspendu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Forme Juridique</label>
                        <select name="legal_form" class="w-full py-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs outline-none">
                            <option value="">Toutes</option>
                            <option value="SARL">SARL</option>
                            <option value="SARL AU">SARL AU</option>
                            <option value="SA">SA</option>
                            <option value="Personne physique">Personne physique</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <button type="submit" name="format" value="pdf" class="flex-1 py-2.5 px-3 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-colors flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Export PDF</span>
                    </button>
                    <button type="submit" name="format" value="excel" class="flex-1 py-2.5 px-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-semibold text-xs rounded-xl transition-colors flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Export Excel</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Rapport 3: Déclarations Fiscales & Sociales -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Rapport des Déclarations Fiscales</h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        État d'avancement des obligations SIMPL : TVA, acomptes IS, état 9421 et bordereaux CNSS avec alertes de retards.
                    </p>
                </div>
            </div>

            <form action="{{ route('reports.generate') }}" method="POST" class="space-y-3 pt-4 border-t border-slate-100">
                @csrf
                <input type="hidden" name="report_type" value="declarations">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Impôt / Obligation</label>
                        <select name="type_id" class="w-full py-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs outline-none">
                            <option value="">Tous les types</option>
                            @foreach(\App\Models\DeclarationType::all() as $t)
                                <option value="{{ $t->id }}">{{ $t->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Statut</label>
                        <select name="status" class="w-full py-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs outline-none">
                            <option value="">Tous</option>
                            <option value="deposee">Déposée</option>
                            <option value="en_retard">En retard</option>
                            <option value="en_preparation">En préparation</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <button type="submit" name="format" value="pdf" class="flex-1 py-2.5 px-3 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-colors flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Export PDF</span>
                    </button>
                    <button type="submit" name="format" value="excel" class="flex-1 py-2.5 px-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-semibold text-xs rounded-xl transition-colors flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Export Excel</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Rapport 4: Dossiers & Missions -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-6">
            <div class="space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-folder-tree"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Rapport des Dossiers & Missions</h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Suivi opérationnel des missions du cabinet, répartition des charges de travail par collaborateur et avancement.
                    </p>
                </div>
            </div>

            <form action="{{ route('reports.generate') }}" method="POST" class="space-y-3 pt-4 border-t border-slate-100">
                @csrf
                <input type="hidden" name="report_type" value="dossiers">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Type de Mission</label>
                        <select name="type" class="w-full py-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs outline-none">
                            <option value="">Tous les types</option>
                            <option value="comptabilite">Comptabilité</option>
                            <option value="fiscalite">Fiscalité</option>
                            <option value="conseil">Conseil</option>
                            <option value="creation_entreprise">Création Sté</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Statut</label>
                        <select name="status" class="w-full py-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs outline-none">
                            <option value="">Tous</option>
                            <option value="en_cours">En cours</option>
                            <option value="nouveau">Nouveau</option>
                            <option value="termine">Terminé</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <button type="submit" name="format" value="pdf" class="flex-1 py-2.5 px-3 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-colors flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Export PDF</span>
                    </button>
                    <button type="submit" name="format" value="excel" class="flex-1 py-2.5 px-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-semibold text-xs rounded-xl transition-colors flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Export Excel</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
