@extends('layouts.app')

@section('title', 'Corbeille & Restauration')

@section('content')
<div class="space-y-6">

    <!-- Top header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Corbeille & Restauration</h1>
            <p class="text-xs sm:text-sm text-slate-500">
                Fiches supprimées logiquement (RG02) : l'historique comptable est préservé et reste restaurable à tout moment.
            </p>
        </div>
    </div>

    <!-- Clients supprimés -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fa-solid fa-building-circle-xmark text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Clients supprimés</h2>
                    <p class="text-[11px] text-slate-400">{{ $clients->total() }} fiche(s) dans la corbeille</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-slate-400 bg-slate-50/70">
                        <th class="py-3 px-5">Raison Sociale</th>
                        <th class="py-3 px-5">ICE</th>
                        <th class="py-3 px-5">Ville</th>
                        <th class="py-3 px-5">Supprimé le</th>
                        <th class="py-3 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr class="border-t border-slate-100">
                            <td class="py-4 px-5">
                                <p class="font-bold text-slate-800">{{ $client->company_name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $client->legal_form ?? '—' }}</p>
                            </td>
                            <td class="py-4 px-5 font-mono text-xs text-slate-500">{{ $client->ice ?? '—' }}</td>
                            <td class="py-4 px-5 text-xs text-slate-600">{{ $client->city ?? '—' }}</td>
                            <td class="py-4 px-5 text-xs text-slate-500">{{ $client->deleted_at?->format('d/m/Y à H:i') }}</td>
                            <td class="py-4 px-5 text-right">
                                <form action="{{ route('trash.restore-client', $client->id) }}" method="POST"
                                      onsubmit="return confirm('Restaurer définitivement cette fiche client ?');" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition-colors">
                                        <i class="fa-solid fa-rotate-left mr-1"></i> Restaurer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                <i class="fa-regular fa-trash-can text-2xl mb-2 text-slate-300"></i>
                                <p class="text-sm">Aucun client dans la corbeille</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">{{ $clients->links() }}</div>
        @endif
    </div>

    <!-- Dossiers supprimés -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-folder-minus text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Dossiers de mission supprimés</h2>
                    <p class="text-[11px] text-slate-400">{{ $dossiers->total() }} dossier(s) dans la corbeille</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-slate-400 bg-slate-50/70">
                        <th class="py-3 px-5">Référence</th>
                        <th class="py-3 px-5">Client</th>
                        <th class="py-3 px-5">Type de mission</th>
                        <th class="py-3 px-5">Supprimé le</th>
                        <th class="py-3 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dossiers as $dossier)
                        <tr class="border-t border-slate-100">
                            <td class="py-4 px-5 font-mono text-xs font-bold text-slate-800">{{ $dossier->reference }}</td>
                            <td class="py-4 px-5 text-xs text-slate-600">{{ $dossier->client?->company_name ?? '—' }}</td>
                            <td class="py-4 px-5 text-xs text-slate-600">{{ $dossier->type_label }}</td>
                            <td class="py-4 px-5 text-xs text-slate-500">{{ $dossier->deleted_at?->format('d/m/Y à H:i') }}</td>
                            <td class="py-4 px-5 text-right">
                                <form action="{{ route('trash.restore-dossier', $dossier->id) }}" method="POST"
                                      onsubmit="return confirm('Restaurer ce dossier de mission ?');" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition-colors">
                                        <i class="fa-solid fa-rotate-left mr-1"></i> Restaurer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-2xl mb-2 text-slate-300"></i>
                                <p class="text-sm">Aucun dossier dans la corbeille</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dossiers->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">{{ $dossiers->links() }}</div>
        @endif
    </div>

    <!-- Collaborateurs supprimés -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-user-minus text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Collaborateurs supprimés</h2>
                    <p class="text-[11px] text-slate-400">{{ $employees->total() }} collaborateur(s) dans la corbeille</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-slate-400 bg-slate-50/70">
                        <th class="py-3 px-5">Collaborateur</th>
                        <th class="py-3 px-5">Matricule</th>
                        <th class="py-3 px-5">Fonction</th>
                        <th class="py-3 px-5">Supprimé le</th>
                        <th class="py-3 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr class="border-t border-slate-100">
                            <td class="py-4 px-5">
                                <p class="font-bold text-slate-800">{{ $employee->full_name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $employee->email }}</p>
                            </td>
                            <td class="py-4 px-5 font-mono text-xs text-slate-500">{{ $employee->matricule }}</td>
                            <td class="py-4 px-5 text-xs text-slate-600">{{ $employee->position }}</td>
                            <td class="py-4 px-5 text-xs text-slate-500">{{ $employee->deleted_at?->format('d/m/Y à H:i') }}</td>
                            <td class="py-4 px-5 text-right">
                                <form action="{{ route('trash.restore-employee', $employee->id) }}" method="POST"
                                      onsubmit="return confirm('Réintégrer ce collaborateur ?');" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition-colors">
                                        <i class="fa-solid fa-rotate-left mr-1"></i> Restaurer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                <i class="fa-regular fa-user text-2xl mb-2 text-slate-300"></i>
                                <p class="text-sm">Aucun collaborateur dans la corbeille</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">{{ $employees->links() }}</div>
        @endif
    </div>

</div>
@endsection
