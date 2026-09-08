<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Nombre de tentatives de connexion échouées tolérées avant blocage temporaire */
    protected const MAX_LOGIN_ATTEMPTS = 5;

    /** Durée du blocage en secondes (5 minutes) */
    protected const LOGIN_LOCKOUT_SECONDS = 300;

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = $this->throttleKey($request);

        // Protection contre les attaques par force brute (RG15)
        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = (int) ceil($seconds / 60);

            throw ValidationException::withMessages([
                'email' => "Trop de tentatives de connexion échouées. Veuillez réessayer dans {$minutes} minute(s).",
            ]);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est inactif ou suspendu. Veuillez contacter l’administrateur.']);
            }

            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->save();

            ActivityLog::log(
                action: 'connexion',
                module: 'auth',
                description: "Connexion réussie de {$user->name} ({$user->role?->name})"
            );

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', "Bienvenue, {$user->name} !");
        }

        // Échec : on incrémente le compteur et on trace la tentative (RG14)
        RateLimiter::hit($throttleKey, self::LOGIN_LOCKOUT_SECONDS);

        ActivityLog::log(
            action: 'connexion_echouee',
            module: 'auth',
            description: "Tentative de connexion échouée pour l'adresse '{$credentials['email']}'"
        );

        return back()->withErrors([
            'email' => 'Identifiants invalides. Veuillez vérifier votre adresse email et mot de passe.',
        ])->onlyInput('email');
    }

    /**
     * Clé de limitation : combinaison de l'adresse email visée et de l'adresse IP source,
     * afin de ne pas bloquer un utilisateur légitime à cause d'une autre IP.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::log(
                action: 'deconnexion',
                module: 'auth',
                description: "Déconnexion de {$user->name}"
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Vous avez été déconnecté avec succès.');
    }

    public function showProfile()
    {
        $user = Auth::user()->load('role', 'employee');
        return view('profile.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if (!empty($data['password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            $user->password = Hash::make($data['password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->save();

        ActivityLog::log(
            action: 'modification',
            module: 'auth',
            description: "Mise à jour du profil personnel de {$user->name}"
        );

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
