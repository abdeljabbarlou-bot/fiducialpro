<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - FiducialPro Cabinet Fiduciaire</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS et JS compilés localement par Vite (aucune dépendance CDN) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-slate-800 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-slate-900 to-black">

    <div class="w-full max-w-md">
        
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-3 p-3 rounded-2xl bg-slate-950/90 border border-slate-800 shadow-2xl shadow-sky-950/50">
                <img src="{{ asset('images/logo.jpg') }}?v=2" alt="FIDUCIALPRO" class="h-16 sm:h-20 w-auto max-w-[300px] object-contain rounded-xl">
            </div>
            <p class="text-sm text-slate-400 mt-2">Système Intégré de Gestion de Cabinet Fiduciaire</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 border border-slate-100">
            
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">Espace Collaborateurs</h2>
                <p class="text-xs text-slate-500 mt-1">Connectez-vous pour accéder à vos dossiers et opérations.</p>
            </div>

            <!-- Error Notification -->
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3.5 rounded-xl bg-sky-50 border border-sky-200 text-sky-700 text-xs font-medium">
                    <i class="fa-solid fa-circle-info mr-1.5 text-sky-500"></i> {{ session('info') }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" id="loginForm" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Adresse Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', 'admin@cabinet.ma') }}" 
                               required 
                               autocomplete="email"
                               placeholder="nom@cabinet.ma" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 focus:bg-white text-sm text-slate-800 rounded-xl border border-slate-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all outline-none">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Mot de passe</label>
                        <span class="text-[11px] text-slate-400">Défaut : password</span>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               value="password"
                               required 
                               placeholder="••••••••" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 focus:bg-white text-sm text-slate-800 rounded-xl border border-slate-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition-all outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center space-x-2 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                        <span>Se souvenir de moi</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 text-white font-semibold text-sm hover:from-sky-700 hover:to-indigo-700 shadow-lg shadow-sky-500/25 transition-all duration-200 flex items-center justify-center space-x-2">
                    <span>Ouvrir la session</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Quick Roles Access -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center mb-3">
                    <i class="fa-solid fa-user-shield text-slate-400 mr-1"></i> Accès Rapides par Rôle
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" onclick="fillAndSubmit('admin@cabinet.ma', 'password')" class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 hover:text-sky-700 border border-slate-200 hover:border-sky-300 text-left transition-all">
                        <span class="font-bold block text-slate-800">Administrateur</span>
                        <span class="text-[10px] text-slate-500">Accès total & logs</span>
                    </button>
                    <button type="button" onclick="fillAndSubmit('gerant@cabinet.ma', 'password')" class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 hover:text-sky-700 border border-slate-200 hover:border-sky-300 text-left transition-all">
                        <span class="font-bold block text-slate-800">Gérant / Associé</span>
                        <span class="text-[10px] text-slate-500">Direction & Rapports</span>
                    </button>
                    <button type="button" onclick="fillAndSubmit('comptable1@cabinet.ma', 'password')" class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 hover:text-sky-700 border border-slate-200 hover:border-sky-300 text-left transition-all">
                        <span class="font-bold block text-slate-800">Sara Alami</span>
                        <span class="text-[10px] text-slate-500">Comptable Senior</span>
                    </button>
                    <button type="button" onclick="fillAndSubmit('secretaire@cabinet.ma', 'password')" class="p-2 rounded-xl bg-slate-50 hover:bg-sky-50 hover:text-sky-700 border border-slate-200 hover:border-sky-300 text-left transition-all">
                        <span class="font-bold block text-slate-800">Secrétaire</span>
                        <span class="text-[10px] text-slate-500">Factures & Paiements</span>
                    </button>
                </div>
            </div>

        </div>

        <p class="text-center text-xs text-slate-500 mt-6">&copy; {{ date('Y') }} FiducialPro — Système Intégré de Gestion de Cabinet Fiduciaire.</p>
    </div>

    <script>
        function fillAndSubmit(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('loginForm').submit();
        }
    </script>
</body>
</html>
