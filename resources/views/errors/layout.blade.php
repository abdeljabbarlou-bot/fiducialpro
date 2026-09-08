<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Erreur') - FiducialPro</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-slate-800 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-slate-900 to-black">

    <div class="w-full max-w-md text-center">

        <div class="inline-flex items-center justify-center mb-6 p-3 rounded-2xl bg-slate-950/90 border border-slate-800 shadow-2xl shadow-sky-950/50">
            <img src="{{ asset('images/logo.jpg') }}?v=2" alt="FIDUCIALPRO" class="h-14 w-auto max-w-[260px] object-contain rounded-xl">
        </div>

        <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 border border-slate-100">
            @php
                // Classes écrites en toutes lettres : Tailwind analyse le code source au
                // moment du build et ne détecterait pas des noms de classes concaténés.
                $accentClasses = match ($accent ?? 'rose') {
                    'amber' => 'bg-amber-50 text-amber-600',
                    'sky' => 'bg-sky-50 text-sky-600',
                    default => 'bg-rose-50 text-rose-600',
                };
            @endphp
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl {{ $accentClasses }} flex items-center justify-center text-2xl">
                <i class="fa-solid {{ $icon ?? 'fa-triangle-exclamation' }}"></i>
            </div>

            <div class="text-5xl font-black text-slate-900 tracking-tight mb-1">@yield('code')</div>
            <h1 class="text-lg font-bold text-slate-800 mb-2">@yield('heading')</h1>
            <p class="text-sm text-slate-500 leading-relaxed mb-8">@yield('message')</p>

            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
               class="inline-flex items-center justify-center space-x-2 w-full py-3 px-4 rounded-xl bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-700 hover:to-blue-800 text-white font-semibold text-sm shadow-lg shadow-sky-500/20 transition-all">
                <i class="fa-solid {{ auth()->check() ? 'fa-house' : 'fa-right-to-bracket' }}"></i>
                <span>{{ auth()->check() ? 'Retour au tableau de bord' : 'Retour à la connexion' }}</span>
            </a>
        </div>

        <p class="text-xs text-slate-500 mt-6">FiducialPro &bull; Cabinet d'Expertise Comptable, Audit & Conseil Fiscal</p>
    </div>

</body>
</html>
