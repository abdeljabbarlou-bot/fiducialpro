<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Si l'utilisateur est admin, il a accès à tout
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Vérifier si le rôle de l'utilisateur correspond à un des rôles autorisés
        if ($user->role && in_array($user->role->slug, $roles)) {
            return $next($request);
        }

        abort(403, "Accès refusé. Vous n'avez pas les permissions requises pour cette action.");
    }
}
