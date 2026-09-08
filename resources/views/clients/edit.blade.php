@extends('layouts.app')

@section('title', 'Modifier ' . $client->company_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Top breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('clients.index') }}" class="hover:text-sky-600">Clients</a>
                <span>&bull;</span>
                <a href="{{ route('clients.show', $client) }}" class="hover:text-sky-600">{{ $client->company_name }}</a>
                <span>&bull;</span>
                <span class="text-slate-600 font-medium">Modification</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Modifier la Fiche Client</h1>
        </div>
        <a href="{{ route('clients.show', $client) }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            Annuler
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Section 1 : Informations Générales & Juridiques -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-building text-sky-600 mr-2"></i> Identité de l'Entreprise
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Renseignements légaux et administratifs.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Type de client *</label>
                    <select name="type" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="entreprise" {{ old('type', $client->type) === 'entreprise' ? 'selected' : '' }}>Personne Morale (Société)</option>
                        <option value="particulier" {{ old('type', $client->type) === 'particulier' ? 'selected' : '' }}>Personne Physique / Profession Libérale</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Statut du client *</label>
                    <select name="status" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="actif" {{ old('status', $client->status) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="prospect" {{ old('status', $client->status) === 'prospect' ? 'selected' : '' }}>Prospect</option>
                        <option value="suspendu" {{ old('status', $client->status) === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        <option value="archive" {{ old('status', $client->status) === 'archive' ? 'selected' : '' }}>Archivé</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Raison Sociale *</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom Commercial / Enseigne</label>
                    <input type="text" name="trade_name" value="{{ old('trade_name', $client->trade_name) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Forme Juridique</label>
                    <select name="legal_form" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                        <option value="">Sélectionner</option>
                        <option value="SARL" {{ old('legal_form', $client->legal_form) === 'SARL' ? 'selected' : '' }}>SARL</option>
                        <option value="SARL AU" {{ old('legal_form', $client->legal_form) === 'SARL AU' ? 'selected' : '' }}>SARL AU</option>
                        <option value="SA" {{ old('legal_form', $client->legal_form) === 'SA' ? 'selected' : '' }}>SA</option>
                        <option value="SAS" {{ old('legal_form', $client->legal_form) === 'SAS' ? 'selected' : '' }}>SAS</option>
                        <option value="SNC" {{ old('legal_form', $client->legal_form) === 'SNC' ? 'selected' : '' }}>SNC</option>
                        <option value="Auto-entrepreneur" {{ old('legal_form', $client->legal_form) === 'Auto-entrepreneur' ? 'selected' : '' }}>Auto-entrepreneur</option>
                        <option value="Personne physique" {{ old('legal_form', $client->legal_form) === 'Personne physique' ? 'selected' : '' }}>Personne physique</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">ICE</label>
                    <input type="text" name="ice" value="{{ old('ice', $client->ice) }}" maxlength="20" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Identifiant Fiscal (IF)</label>
                    <input type="text" name="if_number" value="{{ old('if_number', $client->if_number) }}" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Registre du Commerce (RC)</label>
                    <input type="text" name="rc_number" value="{{ old('rc_number', $client->rc_number) }}" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Taxe Professionnelle / Patente</label>
                    <input type="text" name="patent_number" value="{{ old('patent_number', $client->patent_number) }}" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Numéro CNSS</label>
                    <input type="text" name="cnss_number" value="{{ old('cnss_number', $client->cnss_number) }}" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Capital Social (DH)</label>
                    <input type="number" step="0.01" name="share_capital" value="{{ old('share_capital', $client->share_capital) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Date d'entrée au cabinet</label>
                    <input type="date" name="client_since" value="{{ old('client_since', $client->client_since ? $client->client_since->format('Y-m-d') : '') }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Secteur / Activité</label>
                    <input type="text" name="activity" value="{{ old('activity', $client->activity) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 2 : Coordonnées -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-map-location-dot text-emerald-600 mr-2"></i> Coordonnées & Siège Social
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse complète</label>
                    <input type="text" name="address" value="{{ old('address', $client->address) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ville</label>
                    <input type="text" name="city" value="{{ old('city', $client->city) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pays</label>
                    <input type="text" name="country" value="{{ old('country', $client->country) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone fixe</label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email officiel</label>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Site internet</label>
                    <input type="url" name="website" value="{{ old('website', $client->website) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 3 : Représentant Légal -->
        @php
            $contact = $client->primaryContact;
        @endphp
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center">
                    <i class="fa-solid fa-user-tie text-indigo-600 mr-2"></i> Représentant Légal & Gérant
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prénom</label>
                    <input type="text" name="contact_first_name" value="{{ old('contact_first_name', $contact?->first_name) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom</label>
                    <input type="text" name="contact_last_name" value="{{ old('contact_last_name', $contact?->last_name) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">CIN du Représentant</label>
                    <input type="text" name="contact_cin" value="{{ old('contact_cin', $contact?->cin) }}" class="w-full py-2.5 px-3 bg-slate-50 font-mono text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fonction</label>
                    <input type="text" name="contact_position" value="{{ old('contact_position', $contact?->position) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone mobile direct</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $contact?->phone) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email personnel</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $contact?->email) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 4 : Notes -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 flex items-center">
                <i class="fa-regular fa-note-sticky text-amber-500 mr-2"></i> Notes Internes du Cabinet
            </h2>
            <textarea name="notes" rows="3" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">{{ old('notes', $client->notes) }}</textarea>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('clients.show', $client) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Mettre à jour le client
            </button>
        </div>

    </form>

</div>
@endsection
