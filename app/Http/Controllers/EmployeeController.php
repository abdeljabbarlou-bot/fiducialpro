<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user.role'])->withCount('dossiers', 'declarations');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('cin', 'like', "%{$term}%")
                  ->orWhere('matricule', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('position', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('last_name')->paginate(10)->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $this->authorize('create', Employee::class);

        $roles = Role::all();
        return view('employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Employee::class);

        $validated = $request->validate([
            'matricule' => 'required|string|unique:employees,matricule',
            'cin' => 'required|string|unique:employees,cin',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string',
            'position' => 'required|string|max:100',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:actif,inactif,conge',
            'notes' => 'nullable|string',

            // Compte utilisateur optionnel
            'create_user_account' => 'nullable|boolean',
            'role_id' => 'nullable|required_if:create_user_account,1|exists:roles,id',
            'user_password' => 'nullable|required_if:create_user_account,1|min:8',
        ]);

        $employee = DB::transaction(function () use ($validated, $request) {
            $userId = null;

            if ($request->boolean('create_user_account')) {
                $user = User::create([
                    'role_id' => $validated['role_id'],
                    'name' => "{$validated['first_name']} {$validated['last_name']}",
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['user_password']),
                    'phone' => $validated['phone'],
                    'status' => 'actif',
                ]);
                $userId = $user->id;
            }

            $validated['user_id'] = $userId;
            $employee = Employee::create($validated);

            ActivityLog::log(
                action: 'creation',
                module: 'employees',
                description: "Ajout du collaborateur {$employee->full_name} ({$employee->position})",
                targetId: $employee->id,
                targetLabel: $employee->full_name
            );

            return $employee;
        });

        return redirect()->route('employees.show', $employee)->with('success', "Le collaborateur {$employee->full_name} a été enregistré.");
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'user.role',
            'dossiers.client',
            'declarations.client',
            'deadlines.client',
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $this->authorize('update', $employee);

        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'matricule' => 'required|string|unique:employees,matricule,' . $employee->id,
            'cin' => 'required|string|unique:employees,cin,' . $employee->id,
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string',
            'position' => 'required|string|max:100',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:actif,inactif,conge',
            'notes' => 'nullable|string',
        ]);

        $employee->update($validated);

        ActivityLog::log(
            action: 'modification',
            module: 'employees',
            description: "Mise à jour des informations de {$employee->full_name}",
            targetId: $employee->id,
            targetLabel: $employee->full_name
        );

        return redirect()->route('employees.show', $employee)->with('success', "Le collaborateur {$employee->full_name} a été mis à jour.");
    }

    public function destroy(Employee $employee)
    {
        $this->authorize('delete', $employee);

        $name = $employee->full_name;
        $employee->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'employees',
            description: "Suppression du collaborateur {$name}",
            targetId: $employee->id,
            targetLabel: $name
        );

        return redirect()->route('employees.index')->with('success', "Le collaborateur {$name} a été supprimé.");
    }
}
