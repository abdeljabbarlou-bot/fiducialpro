@extends('layouts.app')

@section('title', 'Centre de Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Centre de Notifications</h1>
            <p class="text-xs sm:text-sm text-slate-500">Alertes d'échéances fiscales, nouveaux paiements et jalons des dossiers.</p>
        </div>
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-check-double text-slate-400"></i>
                <span>Tout marquer comme lu</span>
            </button>
        </form>
    </div>

    <!-- Notification list -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm divide-y divide-slate-100 overflow-hidden">
        @forelse($notifications as $notif)
            <div class="p-5 flex items-start justify-between gap-4 {{ $notif->is_read ? 'opacity-60 bg-white' : 'bg-sky-50/20' }} hover:bg-slate-50 transition-colors">
                <div class="flex items-start space-x-3.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 
                        {{ $notif->type === 'danger' ? 'bg-rose-100 text-rose-600' : ($notif->type === 'warning' ? 'bg-amber-100 text-amber-600' : ($notif->type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-sky-100 text-sky-600')) }}">
                        @if($notif->type === 'danger')
                            <i class="fa-solid fa-circle-exclamation text-sm"></i>
                        @elseif($notif->type === 'warning')
                            <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                        @elseif($notif->type === 'success')
                            <i class="fa-solid fa-circle-check text-sm"></i>
                        @else
                            <i class="fa-solid fa-bell text-sm"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">{{ $notif->title }}</h3>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ $notif->message }}</p>
                        <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    @if($notif->link)
                        <a href="{{ $notif->link }}" class="px-3 py-1 bg-slate-100 hover:bg-sky-50 hover:text-sky-600 text-slate-700 rounded-lg text-xs font-semibold transition-colors">
                            Voir &rarr;
                        </a>
                    @endif
                    @if(!$notif->is_read)
                        <form action="{{ route('notifications.read', $notif) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-sky-600 rounded-lg hover:bg-slate-100" title="Marquer comme lu">
                                <i class="fa-solid fa-check text-xs"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-slate-400">
                <i class="fa-regular fa-bell-slash text-3xl mb-2 text-slate-300"></i>
                <p class="text-sm font-medium">Aucune notification pour le moment</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection
