<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'employee']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $employees = Employee::where('status', 'actif')->orderBy('last_name')->get();
        return view('users.create', compact('roles', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:30',
            'status' => 'required|in:actif,inactif',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        ActivityLog::log(
            action: 'creation',
            module: 'users',
            description: "Création de l'utilisateur {$user->name} ({$user->email}) avec le rôle {$user->role?->name}",
            targetId: $user->id,
            targetLabel: $user->name
        );

        return redirect()->route('users.index')->with('success', "L'utilisateur {$user->name} a été créé avec succès.");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $employees = Employee::where('status', 'actif')->orderBy('last_name')->get();
        return view('users.edit', compact('user', 'roles', 'employees'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:30',
            'status' => 'required|in:actif,inactif',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Un administrateur ne peut pas se retirer lui-même ses propres droits :
        // sans ce garde-fou, il pourrait se rétrograder ou se désactiver et
        // verrouiller définitivement l'administration du cabinet.
        if ($user->id === auth()->id()) {
            if ((int) $validated['role_id'] !== (int) $user->role_id) {
                return back()->withInput()->withErrors([
                    'role_id' => "Vous ne pouvez pas modifier votre propre rôle. Demandez à un autre administrateur de le faire.",
                ]);
            }

            if ($validated['status'] !== 'actif') {
                return back()->withInput()->withErrors([
                    'status' => "Vous ne pouvez pas désactiver votre propre compte.",
                ]);
            }
        }

        // Filet de sécurité : le cabinet doit conserver au moins un administrateur actif
        if ($user->isAdmin() && $this->wouldRemoveLastActiveAdmin($user, (int) $validated['role_id'], $validated['status'])) {
            return back()->withInput()->withErrors([
                'role_id' => "Opération refusée : le système doit conserver au moins un administrateur actif.",
            ]);
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        ActivityLog::log(
            action: 'modification',
            module: 'users',
            description: "Modification du compte utilisateur {$user->name}",
            targetId: $user->id,
            targetLabel: $user->name
        );

        return redirect()->route('users.index')->with('success', "L'utilisateur {$user->name} a été mis à jour.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', "Vous ne pouvez pas désactiver votre propre compte administrateur.");
        }

        // Désactiver le dernier administrateur actif verrouillerait l'administration
        if ($user->isAdmin() && $user->status === 'actif' && $this->wouldRemoveLastActiveAdmin($user, 0, 'inactif')) {
            return back()->with('error', "Impossible de désactiver le dernier administrateur actif du système.");
        }

        $user->status = ($user->status === 'actif') ? 'inactif' : 'actif';
        $user->save();

        ActivityLog::log(
            action: 'modification',
            module: 'users',
            description: "Changement de statut du compte {$user->name} -> {$user->status}",
            targetId: $user->id,
            targetLabel: $user->name
        );

        return back()->with('success', "Le statut de {$user->name} est désormais '{$user->status}'.");
    }

    /**
     * Détermine si la modification envisagée priverait le système de son dernier
     * administrateur actif (changement de rôle ou désactivation).
     */
    protected function wouldRemoveLastActiveAdmin(User $user, int $newRoleId, string $newStatus): bool
    {
        $adminRoleId = Role::where('slug', 'admin')->value('id');

        $resteAdminActif = $newRoleId === (int) $adminRoleId && $newStatus === 'actif';

        if ($resteAdminActif) {
            return false;
        }

        $autresAdminsActifs = User::where('id', '!=', $user->id)
            ->where('role_id', $adminRoleId)
            ->where('status', 'actif')
            ->count();

        return $autresAdminsActifs === 0;
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', "Vous ne pouvez pas supprimer votre propre compte administrateur.");
        }

        // Le dernier administrateur actif ne peut pas être supprimé
        if ($user->isAdmin() && $this->wouldRemoveLastActiveAdmin($user, 0, 'inactif')) {
            return back()->with('error', "Impossible de supprimer le dernier administrateur actif du système.");
        }

        // Dissocier l'employé lié si présent
        if ($user->employee) {
            $user->employee->update(['user_id' => null]);
        }

        $name = $user->name;
        $id = $user->id;
        $user->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'users',
            description: "Suppression du compte utilisateur {$name}",
            targetId: $id,
            targetLabel: $name
        );

        return redirect()->route('users.index')->with('success', "L'utilisateur {$name} a été supprimé avec succès.");
    }
}
