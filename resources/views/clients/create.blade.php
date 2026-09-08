@extends('layouts.app')

@section('title', 'Nouveau Client')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('clients.index') }}" class="hover:text-sky-600">Clients</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Création de fiche</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ajouter un Client au Portefeuille</h1>
        </div>
        <a href="{{ route('clients.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('clients.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Section 1 : Informations Générales & Juridiques -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-building text-sky-600 mr-2"></i> Identité de l'Entreprise
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Renseignements légaux et administratifs selon les normes marocaines.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type de client *</label>
                    <select name="type" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="entreprise" {{ old('type') === 'entreprise' ? 'selected' : '' }}>Personne Morale (Société / Entreprise)</option>
                        <option value="particulier" {{ old('type') === 'particulier' ? 'selected' : '' }}>Personne Physique / Profession Libérale</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut du client *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="actif" {{ old('status', 'actif') === 'actif' ? 'selected' : '' }}>Actif (Sous contrat)</option>
                        <option value="prospect" {{ old('status') === 'prospect' ? 'selected' : '' }}>Prospect (En négociation)</option>
                        <option value="suspendu" {{ old('status') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        <option value="archive" {{ old('status') === 'archive' ? 'selected' : '' }}>Archivé</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Raison Sociale *</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" required placeholder="Ex: ABC Consulting SARL" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom Commercial / Enseigne</label>
                    <input type="text" name="trade_name" value="{{ old('trade_name') }}" placeholder="Ex: ABC Group" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Forme Juridique</label>
                    <select name="legal_form" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionner</option>
                        <option value="SARL" {{ old('legal_form') === 'SARL' ? 'selected' : '' }}>SARL (Société à Responsabilité Limitée)</option>
                        <option value="SARL AU" {{ old('legal_form') === 'SARL AU' ? 'selected' : '' }}>SARL AU (Associé Unique)</option>
                        <option value="SA" {{ old('legal_form') === 'SA' ? 'selected' : '' }}>SA (Société Anonyme)</option>
                        <option value="SAS" {{ old('legal_form') === 'SAS' ? 'selected' : '' }}>SAS (Société par Actions Simplifiée)</option>
                        <option value="SNC" {{ old('legal_form') === 'SNC' ? 'selected' : '' }}>SNC (Société en Nom Collectif)</option>
                        <option value="Auto-entrepreneur" {{ old('legal_form') === 'Auto-entrepreneur' ? 'selected' : '' }}>Auto-entrepreneur</option>
                        <option value="Personne physique" {{ old('legal_form') === 'Personne physique' ? 'selected' : '' }}>Personne physique</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">ICE (Identifiant Commun de l'Entreprise)</label>
                    <input type="text" name="ice" value="{{ old('ice') }}" maxlength="20" placeholder="001234567890012 (15 chiffres)" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Identifiant Fiscal (IF)</label>
                    <input type="text" name="if_number" value="{{ old('if_number') }}" placeholder="Ex: 45892100" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Registre du Commerce (RC)</label>
                    <input type="text" name="rc_number" value="{{ old('rc_number') }}" placeholder="Ex: 124587 Casablanca" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Taxe Professionnelle / Patente</label>
                    <input type="text" name="patent_number" value="{{ old('patent_number') }}" placeholder="Ex: 34215689" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Numéro d'Affiliation CNSS</label>
                    <input type="text" name="cnss_number" value="{{ old('cnss_number') }}" placeholder="Ex: 7845123" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Capital Social (DH)</label>
                    <input type="number" step="0.01" name="share_capital" value="{{ old('share_capital') }}" placeholder="Ex: 100000.00" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'entrée au cabinet</label>
                    <input type="date" name="client_since" value="{{ old('client_since', date('Y-m-d')) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Secteur / Activité</label>
                    <input type="text" name="activity" value="{{ old('activity') }}" placeholder="Ex: Import-export de matériel électronique..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 2 : Coordonnées du Siège -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-map-location-dot text-emerald-600 mr-2"></i> Coordonnées & Siège Social
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Adresse légale et contacts de l'entreprise.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse complète</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Numéro, rue, quartier, étage..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ville</label>
                    <input type="text" name="city" value="{{ old('city', 'Casablanca') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pays</label>
                    <input type="text" name="country" value="{{ old('country', 'Maroc') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone fixe / standard</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+212 5 22 ..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email officiel</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="contact@entreprise.ma" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Site internet</label>
                    <input type="url" name="website" value="{{ old('website') }}" placeholder="https://www.entreprise.ma" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 3 : Représentant Légal & Contact Principal -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-user-tie text-indigo-600 mr-2"></i> Représentant Légal & Gérant
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Interlocuteur principal pour la signature des liasses et facturation.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prénom</label>
                    <input type="text" name="contact_first_name" value="{{ old('contact_first_name') }}" placeholder="Ex: Amine" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom</label>
                    <input type="text" name="contact_last_name" value="{{ old('contact_last_name') }}" placeholder="Ex: Tazi" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">CIN du Représentant</label>
                    <input type="text" name="contact_cin" value="{{ old('contact_cin') }}" placeholder="Ex: AB123456" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fonction</label>
                    <input type="text" name="contact_position" value="{{ old('contact_position', 'Gérant') }}" placeholder="Ex: Gérant Unique, DAF..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone mobile direct</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" placeholder="+212 6 ..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email personnel / professionnel</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}" placeholder="gerant@entreprise.ma" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 4 : Notes internes -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center">
                <i class="fa-regular fa-note-sticky text-amber-500 mr-2"></i> Notes Internes du Cabinet
            </h2>
            <textarea name="notes" rows="3" placeholder="Informations confidentielles, particularités fiscales ou comptables..." class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('notes') }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('clients.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Enregistrer le client
            </button>
        </div>

    </form>

</div>
@endsection
