@extends('layouts.app')

@section('title', 'Mon Profil Utilisateur')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Mon Profil & Paramètres</h1>
        <p class="text-xs sm:text-sm text-slate-500">Gérez vos informations personnelles et votre mot de passe de connexion.</p>
    </div>

    <!-- Profile Form -->
    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            <div class="flex items-center space-x-4 border-b border-slate-100 pb-5">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white font-black text-xl flex items-center justify-center shadow-md">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ $user->name }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800">
                        {{ $user->role->name }}
                    </span>
                    <p class="text-[11px] text-slate-400 mt-1">Dernière connexion : {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : 'Première session' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nom complet *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Adresse Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div class="sm:col-span-2 pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-900 mb-1">Changer le mot de passe</h3>
                    <p class="text-[11px] text-slate-400 mb-3">Laissez ces champs vides si vous ne souhaitez pas modifier votre mot de passe.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mot de passe actuel</label>
                    <input type="password" name="current_password" placeholder="••••••••" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nouveau mot de passe</label>
                    <input type="password" name="password" placeholder="Minimum 8 caractères" class="w-full py-2.5 px-3 bg-slate-50 text-xs rounded-xl border border-slate-200 focus:bg-white focus:border-sky-500 outline-none">
                </div>

            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition-all">
                Enregistrer mon profil
            </button>
        </div>

    </form>

</div>
@endsection
