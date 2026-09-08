@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Utilisateurs & Contrôle d'Accès (RBAC)</h1>
            <p class="text-xs sm:text-sm text-slate-500">Administration des comptes, des rôles et des autorisations du personnel.</p>
        </div>
        <a href="{{ route('users.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition-all flex items-center justify-center space-x-2">
            <i class="fa-solid fa-user-shield"></i>
            <span>Créer un Utilisateur</span>
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">Utilisateur</th>
                        <th class="py-3.5 px-5">Email & Téléphone</th>
                        <th class="py-3.5 px-5">Rôle Système</th>
                        <th class="py-3.5 px-5">Dernière Connexion</th>
                        <th class="py-3.5 px-5">Statut</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                        @if($user->employee)
                                            <span class="text-[11px] text-slate-400">Collaborateur : {{ $user->employee->matricule }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-5 text-slate-600">
                                <p class="font-semibold text-slate-800">{{ $user->email }}</p>
                                <p class="text-[11px] text-slate-400">{{ $user->phone ?? 'N/A' }}</p>
                            </td>

                            <td class="py-4 px-5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold 
                                    {{ $user->role->slug === 'admin' ? 'bg-rose-100 text-rose-800' : ($user->role->slug === 'gerant' ? 'bg-indigo-100 text-indigo-800' : 'bg-sky-100 text-sky-800') }}">
                                    {{ $user->role->name }}
                                </span>
                            </td>

                            <td class="py-4 px-5 text-slate-600">
                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais connecté' }}
                            </td>

                            <td class="py-4 px-5">
                                @if($user->status === 'actif')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Actif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Inactif</span>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-right space-x-2">
                                <a href="{{ route('users.edit', $user) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-100 inline-block" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-slate-100" title="{{ $user->status === 'actif' ? 'Désactiver le compte' : 'Activer' }}">
                                            <i class="fa-solid fa-power-off"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
